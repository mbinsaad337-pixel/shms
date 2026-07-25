<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Center;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    /**
     * Get list of users the current user can send a complaint/notice to.
     */
    private function getAvailableRecipients(): \Illuminate\Support\Collection
    {
        $me = auth()->user();

        // super-admin can send to all center managers
        if ($me->hasRole('super-admin')) {
            return User::role('center-manager')
                ->where('is_active', true)
                ->with('center')
                ->get();
        }

        // center-manager can send to super-admin + other center managers
        if ($me->hasRole('center-manager')) {
            $superAdmins = User::role('super-admin')->where('is_active', true)->get();
            $otherManagers = User::role('center-manager')
                ->where('is_active', true)
                ->where('id', '!=', $me->id)
                ->with('center')
                ->get();
            return $superAdmins->merge($otherManagers);
        }

        // executive-manager same as super-admin
        if ($me->hasRole('executive-manager')) {
            return User::role(['center-manager', 'super-admin'])
                ->where('is_active', true)
                ->where('id', '!=', $me->id)
                ->with('center')
                ->get();
        }

        return collect();
    }

    public function inbox()
    {
        $complaints = Complaint::where('receiver_id', auth()->id())
            ->whereNull('parent_id') // top-level only
            ->with(['sender', 'sender.center', 'replies'])
            ->latest()
            ->paginate(15);

        $unreadCount = Complaint::where('receiver_id', auth()->id())
            ->where('status', 'unread')
            ->count();

        return view('complaints.inbox', compact('complaints', 'unreadCount'));
    }

    public function sent()
    {
        $complaints = Complaint::where('sender_id', auth()->id())
            ->whereNull('parent_id')
            ->with(['receiver', 'receiver.center', 'replies'])
            ->latest()
            ->paginate(15);

        return view('complaints.sent', compact('complaints'));
    }

    public function create()
    {
        $recipients = $this->getAvailableRecipients();
        if ($recipients->isEmpty()) {
            return redirect()->back()->with('error', 'لا يوجد مستلمون متاحون.');
        }
        return view('complaints.create', compact('recipients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receiver_id'  => 'required|exists:users,id',
            'subject'      => 'required|string|max:255',
            'body'         => 'required|string',
            'priority'     => 'required|in:normal,urgent',
            'attachment'   => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
        ], [
            'receiver_id.required' => 'يجب اختيار المستلم.',
            'subject.required'     => 'عنوان الشكوى مطلوب.',
            'body.required'        => 'نص الشكوى مطلوب.',
            'attachment.mimes'     => 'يُسمح فقط بملفات: صور (JPG, PNG) أو PDF.',
            'attachment.max'       => 'الحد الأقصى لحجم المرفق 10 ميجابايت.',
        ]);

        $me = auth()->user();
        $receiver = User::findOrFail($request->receiver_id);

        $attachmentPath = null;
        $attachmentType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('complaints', 'public');
            $attachmentType = in_array($file->getClientOriginalExtension(), ['pdf']) ? 'pdf' : 'image';
        }

        Complaint::create([
            'sender_id'          => $me->id,
            'receiver_id'        => $receiver->id,
            'sender_center_id'   => $me->center_id,
            'receiver_center_id' => $receiver->center_id,
            'subject'            => $request->subject,
            'body'               => $request->body,
            'attachment'         => $attachmentPath,
            'attachment_type'    => $attachmentType,
            'priority'           => $request->priority,
            'status'             => 'unread',
        ]);

        return redirect()->route('complaints.sent')->with('success', 'تم إرسال الشكوى / الإشعار بنجاح.');
    }

    public function show(Complaint $complaint)
    {
        $me = auth()->user();

        // Only sender or receiver can view
        if ($complaint->sender_id !== $me->id && $complaint->receiver_id !== $me->id) {
            abort(403);
        }

        // If receiver is viewing, mark as read
        if ($complaint->receiver_id === $me->id) {
            $complaint->markAsRead();
        }

        // Load thread: parent + all replies
        $thread = $complaint->parent_id
            ? collect([$complaint->parent()->with(['sender', 'receiver'])->first()])
                ->merge($complaint->parent->replies()->with(['sender', 'receiver'])->get())
            : collect([$complaint])->merge($complaint->replies()->with(['sender', 'receiver'])->get());

        $recipients = $this->getAvailableRecipients();

        return view('complaints.show', compact('complaint', 'thread', 'recipients'));
    }

    public function reply(Request $request, Complaint $complaint)
    {
        $me = auth()->user();

        if ($complaint->sender_id !== $me->id && $complaint->receiver_id !== $me->id) {
            abort(403);
        }

        $request->validate([
            'body'       => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:10240',
        ], [
            'body.required' => 'نص الرد مطلوب.',
        ]);

        // Determine receiver for this reply
        $receiverId = ($complaint->sender_id === $me->id) ? $complaint->receiver_id : $complaint->sender_id;
        $receiver   = User::findOrFail($receiverId);

        $attachmentPath = null;
        $attachmentType = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('complaints', 'public');
            $attachmentType = in_array($file->getClientOriginalExtension(), ['pdf']) ? 'pdf' : 'image';
        }

        // Root complaint is the top-level (in case we reply to a reply)
        $rootId = $complaint->parent_id ?? $complaint->id;

        Complaint::create([
            'sender_id'          => $me->id,
            'receiver_id'        => $receiverId,
            'sender_center_id'   => $me->center_id,
            'receiver_center_id' => $receiver->center_id,
            'subject'            => 'رد على: ' . ($complaint->parent->subject ?? $complaint->subject),
            'body'               => $request->body,
            'attachment'         => $attachmentPath,
            'attachment_type'    => $attachmentType,
            'priority'           => $complaint->priority,
            'status'             => 'unread',
            'parent_id'          => $rootId,
        ]);

        // Mark original as replied
        $rootComplaint = Complaint::find($rootId);
        $rootComplaint?->update(['status' => 'replied']);

        return redirect()->route('complaints.show', $complaint)->with('success', 'تم إرسال ردك بنجاح.');
    }

    public function destroy(Complaint $complaint)
    {
        $me = auth()->user();

        if ($complaint->sender_id !== $me->id && $complaint->receiver_id !== $me->id) {
            abort(403);
        }

        if ($complaint->attachment) {
            Storage::disk('public')->delete($complaint->attachment);
        }

        $complaint->delete();

        return redirect()->route('complaints.inbox')->with('success', 'تم حذف الرسالة.');
    }

    /**
     * API endpoint for the notification bell (returns JSON count + latest)
     */
    public function bellData()
    {
        $unread = Complaint::where('receiver_id', auth()->id())
            ->where('status', 'unread')
            ->with('sender')
            ->latest()
            ->take(5)
            ->get();

        $count = Complaint::where('receiver_id', auth()->id())
            ->where('status', 'unread')
            ->count();

        return response()->json([
            'count'   => $count,
            'notices' => $unread->map(fn($c) => [
                'id'      => $c->id,
                'subject' => $c->subject,
                'from'    => $c->sender->name,
                'time'    => $c->created_at->diffForHumans(),
                'urgent'  => $c->priority === 'urgent',
            ]),
        ]);
    }
}
