<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Activity;
use App\Models\ActivityParticipant;
use App\Models\AnnualArchive;
use App\Models\AnnualRollover;
use App\Models\Center;
use App\Models\CenterExpense;
use App\Models\CircleAttendance;
use App\Models\CircleSession;
use App\Models\Commitment;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Complaint;
use App\Models\FoodAttendanceReport;
use App\Models\Fund;
use App\Models\FoodBudget;
use App\Models\FoodDistribution;
use App\Models\FoodMonthlySettlement;
use App\Models\FoodPurchaseInvoice;
use App\Models\FoodSubscription;
use App\Models\FoodVoucher;
use App\Models\Leave;
use App\Models\MealDistribution;
use App\Models\MealSubscription;
use App\Models\MonthlyBudget;
use App\Models\MonthlySettlement;
use App\Models\News;
use App\Models\Penalty;
use App\Models\RoomAssignment;
use App\Models\Student;
use App\Models\StudentAchievement;
use App\Models\StudentGrade;
use App\Models\VehicleViolation;
use App\Models\Violation;
use App\Models\Voucher;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AnnualRolloverController extends Controller
{
    private array $fileColumnsMap = [
        'violation'    => ['attachments'],
        'commitment'   => ['image_path'],
        'activity'     => ['attachment_pdf'],
        'news'         => ['cover_image', 'video_path', 'gallery'],
        'voucher'      => ['attachment'],
        'expense'      => ['receipt'],
        'grade'        => ['file_path'],
        'achievement'  => ['certificate_file'],
        'club'         => ['logo'],
        'complaint'    => ['attachment'],
        'food_invoice' => ['attachment'],
        'food_voucher' => ['attachment'],
    ];

    private function collectFilesFromRecord($record, string $subType): array
    {
        $paths = [];
        $columns = $this->fileColumnsMap[$subType] ?? [];

        foreach ($columns as $col) {
            $value = $record->{$col} ?? null;
            if (!$value) continue;

            if (is_array($value)) {
                foreach ($value as $v) {
                    if (is_string($v) && Storage::disk('public')->exists($v)) {
                        $paths[] = $v;
                    }
                }
            } elseif (is_string($value) && Storage::disk('public')->exists($value)) {
                $paths[] = $value;
            }
        }

        return $paths;
    }

    private function copyFilesToArchive(array $filePaths, string $year, int $archiveId): array
    {
        $archivedPaths = [];
        $archiveDir = "archive_files/{$year}";

        foreach ($filePaths as $originalPath) {
            $filename = basename($originalPath);
            $uniqueName = $archiveId . '_' . time() . '_' . $filename;
            $destination = "{$archiveDir}/{$uniqueName}";

            if (Storage::disk('public')->copy($originalPath, $destination)) {
                $archivedPaths[$originalPath] = $destination;
            }
        }

        return $archivedPaths;
    }

    private function restoreFilesFromArchive(array $archivedFiles): void
    {
        foreach ($archivedFiles as $originalPath => $archivedPath) {
            if (Storage::disk('public')->exists($archivedPath)) {
                Storage::disk('public')->copy($archivedPath, $originalPath);
            }
        }
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;

        $currentCounts = [
            'administrative' => [
                'violations' => Violation::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'penalties' => Penalty::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'commitments' => Commitment::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'leaves' => Leave::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'absences' => Absence::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'activities' => [
                'activities' => Activity::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'news' => News::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'financial' => [
                'vouchers' => Voucher::when($centerId, fn($q) => $q->where('center_id', $centerId))->where('status', 'approved')->count(),
                'budgets' => MonthlyBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'settlements' => MonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'expenses' => CenterExpense::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'nutrition' => [
                'distributions' => FoodDistribution::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'subscriptions' => FoodSubscription::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'invoices' => FoodPurchaseInvoice::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'vouchers' => FoodVoucher::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'budgets' => FoodBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
                'settlements' => FoodMonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
            'quran' => [
                'sessions' => CircleSession::whereHas('circle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'academic' => [
                'grades' => StudentGrade::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
                'achievements' => StudentAchievement::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'rooms' => [
                'assignments' => RoomAssignment::whereNull('released_at')
                    ->whereHas('room', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'vehicles' => [
                'violations' => VehicleViolation::whereHas('vehicle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->count(),
            ],
            'complaints' => [
                'complaints' => Complaint::when($centerId, fn($q) => $q->where(fn($sq) => $sq->where('sender_center_id', $centerId)->orWhere('receiver_center_id', $centerId)->orWhereHas('sender', fn($ssq) => $ssq->where('center_id', $centerId))))->count(),
            ],
            'graduates' => [
                'graduates' => Student::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where(function ($q) {
                        $q->where('is_graduate', true)
                          ->orWhere('status', 'graduated');
                    })->count(),
            ],
            'funds' => [
                'funds' => Fund::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('is_system', false)
                    ->count(),
            ],
            'clubs' => [
                'clubs' => Club::when($centerId, fn($q) => $q->where('center_id', $centerId))->count(),
            ],
        ];

        $archivesQuery = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['rollover', 'center', 'student']);

        if ($request->filled('archived_year')) {
            $archivesQuery->where('year', $request->archived_year);
        }
        if ($request->filled('module')) {
            $archivesQuery->where('module', $request->module);
        }
        if ($request->filled('date_from')) {
            $archivesQuery->whereDate('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $archivesQuery->whereDate('record_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $archivesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $archives = $archivesQuery->latest()->paginate(20)->withQueryString();

        $rollovers = AnnualRollover::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['user', 'center'])
            ->latest()
            ->get();

        $availableYears = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->select('year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $centers = Center::where('is_active', true)->get();

        return view('annual_rollover.index', compact(
            'currentCounts',
            'archives',
            'rollovers',
            'availableYears',
            'centers',
            'centerId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|string|max:50',
            'modules' => 'required|array|min:1',
            'modules.*' => 'in:administrative,activities,financial,nutrition,quran,academic,rooms,vehicles,complaints,graduates,funds,clubs',
            'cutoff_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'year.required' => 'يرجى تحديد السنة للترحيل',
            'modules.required' => 'يرجى اختيار قسم واحد على الأقل للترحيل',
        ]);

        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;
        $year = $request->year;
        $cutoffDate = $request->cutoff_date;
        $selectedModules = $request->modules;

        DB::beginTransaction();

        try {
            $rollover = AnnualRollover::create([
                'center_id' => $centerId,
                'year' => $year,
                'from_date' => null,
                'to_date' => $cutoffDate,
                'performed_by' => $user->id,
                'modules' => $selectedModules,
                'summary' => [],
                'notes' => $request->notes,
            ]);

            $summary = [];

            if (in_array('administrative', $selectedModules)) {
                $count = 0;

                $violations = Violation::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($violations as $item) {
                    $files = $this->collectFilesFromRecord($item, 'violation');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'violation',
                        'title' => 'مخالفة: ' . ($item->title ?? $item->type ?? 'مخالفة سلوكية'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $penalties = Penalty::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($penalties as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'penalty',
                        'title' => 'جزاء: ' . ($item->penalty_type ?? 'جزاء إداري'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $commitments = Commitment::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($commitments as $item) {
                    $files = $this->collectFilesFromRecord($item, 'commitment');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'commitment',
                        'title' => 'تعهد: ' . ($item->subject ?? 'تعهد خطي'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $leaves = Leave::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($leaves as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'leave',
                        'title' => 'استئذان: ' . ($item->reason ?? 'طلب مغادرة'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $absences = Absence::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($absences as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'administrative',
                        'sub_type' => 'absence',
                        'title' => 'غياب: ' . ($item->notes ?? 'تسجيل غياب'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['administrative'] = $count;
            }

            if (in_array('activities', $selectedModules)) {
                $count = 0;
                $activities = Activity::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($activities as $item) {
                    $files = $this->collectFilesFromRecord($item, 'activity');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'activities',
                        'sub_type' => 'activity',
                        'title' => 'نشاط: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->cost ?? 0,
                        'data' => $item->load('participants')->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    ActivityParticipant::where('activity_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }

                $newsList = News::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($newsList as $item) {
                    $files = $this->collectFilesFromRecord($item, 'news');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'activities',
                        'sub_type' => 'news',
                        'title' => 'خبر: ' . $item->title,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $summary['activities'] = $count;
            }

            if (in_array('financial', $selectedModules)) {
                $count = 0;

                $vouchers = Voucher::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('status', 'approved')
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($vouchers as $item) {
                    $files = $this->collectFilesFromRecord($item, 'voucher');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'voucher',
                        'title' => 'سند: ' . ($item->voucher_type == 'receipt' ? 'قبض' : 'صرف') . ' - ' . $item->description,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'student_id' => $item->student_id,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $budgets = MonthlyBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($budgets as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'budget',
                        'title' => 'موازنة شهر: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'data' => $item->load('items')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $settlements = MonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($settlements as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'settlement',
                        'title' => 'تصفية مالية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('details')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $expenses = CenterExpense::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($expenses as $item) {
                    $files = $this->collectFilesFromRecord($item, 'expense');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'financial',
                        'sub_type' => 'expense',
                        'title' => 'مصروف مركز: ' . $item->title,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $summary['financial'] = $count;
            }

            if (in_array('nutrition', $selectedModules)) {
                $count = 0;

                $distributions = FoodDistribution::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($distributions as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_distribution',
                        'title' => 'توزيع وجبة: ' . ($item->meal_type ?? 'وجبة'),
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $subscriptions = FoodSubscription::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($subscriptions as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_subscription',
                        'title' => 'اشتراك تغذية للطالب: ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $invoices = FoodPurchaseInvoice::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($invoices as $item) {
                    $files = $this->collectFilesFromRecord($item, 'food_invoice');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_invoice',
                        'title' => 'فاتورة مشتريات تغذية: ' . $item->invoice_number,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->grand_total ?? $item->total_amount ?? 0,
                        'data' => $item->load('items')->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $foodVouchers = FoodVoucher::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodVouchers as $item) {
                    $files = $this->collectFilesFromRecord($item, 'food_voucher');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_voucher',
                        'title' => 'سند تغذية: ' . $item->voucher_number,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->amount,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $foodBudgets = FoodBudget::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodBudgets as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_budget',
                        'title' => 'ميزانية تغذية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->total_amount ?? 0,
                        'data' => $item->load('lines')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $foodSettlements = FoodMonthlySettlement::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();
                foreach ($foodSettlements as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'nutrition',
                        'sub_type' => 'food_settlement',
                        'title' => 'تصفية تغذية: ' . $item->month_year,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('details')->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }

                $summary['nutrition'] = $count;
            }

            if (in_array('quran', $selectedModules)) {
                $count = 0;
                $sessions = CircleSession::whereHas('circle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($sessions as $item) {
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->circle)->center_id,
                        'year' => $year,
                        'module' => 'quran',
                        'sub_type' => 'circle_session',
                        'title' => 'جلسة تحفيظ: ' . optional($item->circle)->name . ' بتاريخ ' . $item->session_date,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('attendance')->toArray(),
                    ]);
                    CircleAttendance::where('session_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }
                $summary['quran'] = $count;
            }

            if (in_array('academic', $selectedModules)) {
                $count = 0;
                $grades = StudentGrade::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($grades as $item) {
                    $files = $this->collectFilesFromRecord($item, 'grade');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'academic',
                        'sub_type' => 'student_grade',
                        'title' => 'درجة أكاديمية: ' . $item->subject . ' للطالب ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }

                $achievements = StudentAchievement::whereHas('student', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($achievements as $item) {
                    $files = $this->collectFilesFromRecord($item, 'achievement');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->student)->center_id,
                        'year' => $year,
                        'module' => 'academic',
                        'sub_type' => 'student_achievement',
                        'title' => 'إنجاز طالب: ' . $item->title . ' - ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }
                $summary['academic'] = $count;
            }

            if (in_array('rooms', $selectedModules)) {
                $count = 0;
                $assignments = RoomAssignment::whereNull('released_at')
                    ->whereHas('room', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))->get();

                foreach ($assignments as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->room)->center_id,
                        'year' => $year,
                        'module' => 'rooms',
                        'sub_type' => 'room_assignment',
                        'title' => 'تسكين غرفة ' . optional($item->room)->room_number . ' للطالب ' . optional($item->student)->name_ar,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'student_id' => $item->student_id,
                        'student_name' => optional($item->student)->name_ar,
                        'data' => $item->toArray(),
                    ]);
                    $item->update(['released_at' => now(), 'release_reason' => 'الترحيل السنوي']);
                    $count++;
                }
                $summary['rooms'] = $count;
            }

            if (in_array('vehicles', $selectedModules)) {
                $count = 0;
                $vViolations = VehicleViolation::whereHas('vehicle', fn($q) => $q->when($centerId, fn($sq) => $sq->where('center_id', $centerId)))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($vViolations as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => optional($item->vehicle)->center_id,
                        'year' => $year,
                        'module' => 'vehicles',
                        'sub_type' => 'vehicle_violation',
                        'title' => 'مخالفة مركبة: ' . $item->violation_type,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['vehicles'] = $count;
            }

            if (in_array('complaints', $selectedModules)) {
                $count = 0;
                $complaints = Complaint::when($centerId, fn($q) => $q->where(fn($sq) => $sq->where('sender_center_id', $centerId)->orWhere('receiver_center_id', $centerId)->orWhereHas('sender', fn($ssq) => $ssq->where('center_id', $centerId))))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))->get();

                foreach ($complaints as $item) {
                    $files = $this->collectFilesFromRecord($item, 'complaint');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->sender_center_id ?? $item->receiver_center_id,
                        'year' => $year,
                        'module' => 'complaints',
                        'sub_type' => 'complaint',
                        'title' => 'إشعار/شكوى: ' . $item->subject,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    $item->delete();
                    $count++;
                }
                $summary['complaints'] = $count;
            }

            if (in_array('graduates', $selectedModules)) {
                $count = 0;
                $graduates = Student::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where(function ($q) {
                        $q->where('is_graduate', true)
                          ->orWhere('status', 'graduated');
                    })
                    ->when($cutoffDate, fn($q) => $q->whereDate('updated_at', '<=', $cutoffDate))
                    ->with(['center', 'activeRoomAssignment.room', 'program'])
                    ->get();

                foreach ($graduates as $student) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $student->center_id,
                        'year' => $year,
                        'module' => 'graduates',
                        'sub_type' => 'student_graduate',
                        'title' => 'أرشفة طالب خريج: ' . $student->name_ar,
                        'record_id' => $student->id,
                        'record_date' => $student->updated_at ?? $student->created_at,
                        'student_id' => $student->id,
                        'student_name' => $student->name_ar,
                        'data' => array_merge($student->toArray(), [
                            'room_number' => optional(optional($student->activeRoomAssignment)->room)->room_number,
                            'major' => $student->major,
                            'program_name' => optional($student->program)->name,
                        ]),
                    ]);

                    if ($student->activeRoomAssignment) {
                        $student->activeRoomAssignment->update([
                            'released_at' => now(),
                            'release_reason' => 'الترحيل السنوي للأرشيف (تخرج)'
                        ]);
                    }

                    $student->delete();
                    $count++;
                }
                $summary['الطلاب الخريجون'] = $count;
            }

            if (in_array('funds', $selectedModules)) {
                $count = 0;
                $funds = Fund::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->where('is_system', false)
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))
                    ->get();

                foreach ($funds as $item) {
                    AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'funds',
                        'sub_type' => 'fund',
                        'title' => 'صندوق: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'amount' => $item->balance ?? 0,
                        'data' => $item->toArray(),
                    ]);
                    $item->delete();
                    $count++;
                }
                $summary['funds'] = $count;
            }

            if (in_array('clubs', $selectedModules)) {
                $count = 0;
                $clubs = Club::when($centerId, fn($q) => $q->where('center_id', $centerId))
                    ->when($cutoffDate, fn($q) => $q->whereDate('created_at', '<=', $cutoffDate))
                    ->get();

                foreach ($clubs as $item) {
                    $files = $this->collectFilesFromRecord($item, 'club');
                    $arc = AnnualArchive::create([
                        'rollover_id' => $rollover->id,
                        'center_id' => $item->center_id,
                        'year' => $year,
                        'module' => 'clubs',
                        'sub_type' => 'club',
                        'title' => 'نادي: ' . $item->name,
                        'record_id' => $item->id,
                        'record_date' => $item->created_at,
                        'data' => $item->load('members')->toArray(),
                    ]);
                    if ($files) {
                        $archived = $this->copyFilesToArchive($files, $year, $arc->id);
                        $arc->update(['archived_files' => $archived]);
                    }
                    ClubMember::where('club_id', $item->id)->delete();
                    $item->delete();
                    $count++;
                }
                $summary['clubs'] = $count;
            }

            $rollover->update(['summary' => $summary]);

            DB::commit();

            return redirect()->route('annual-rollover.index')
                ->with('success', "تم تنفيذ الترحيل السنوي لعام ({$year}) بنجاح، وتمت أرشفة البيانات والملفات المحددة وحفظها في أرشيف السنين.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء تنفيذ الترحيل السنوي: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Undo a rollover: restore all archived records to their original tables and restore files.
     */
    public function undoRollover(AnnualRollover $rollover)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $rollover->center_id && $rollover->center_id != $user->center_id) {
            abort(403, 'غير مصرح بإلغاء هذا الترحيل');
        }

        $archives = $rollover->archives()->get();
        if ($archives->isEmpty()) {
            return redirect()->back()->with('error', 'لا توجد سجلات مؤرشفة لهذا الترحيل.');
        }

        DB::beginTransaction();

        try {
            $restoredCount = 0;
            $filesRestored = 0;

            foreach ($archives as $archive) {
                $data = $archive->data ?? [];

                if ($archive->archived_files && is_array($archive->archived_files)) {
                    $this->restoreFilesFromArchive($archive->archived_files);
                    $filesRestored += count($archive->archived_files);
                }

                switch ($archive->module) {
                    case 'administrative':
                        $restoredCount += $this->restoreAdminRecord($archive, $data);
                        break;
                    case 'activities':
                        $restoredCount += $this->restoreActivityRecord($archive, $data);
                        break;
                    case 'financial':
                        $restoredCount += $this->restoreFinancialRecord($archive, $data);
                        break;
                    case 'nutrition':
                        $restoredCount += $this->restoreNutritionRecord($archive, $data);
                        break;
                    case 'quran':
                        $restoredCount += $this->restoreQuranRecord($archive, $data);
                        break;
                    case 'academic':
                        $restoredCount += $this->restoreAcademicRecord($archive, $data);
                        break;
                    case 'vehicles':
                        $restoredCount += $this->restoreVehicleRecord($archive, $data);
                        break;
                    case 'complaints':
                        $restoredCount += $this->restoreComplaintRecord($archive, $data);
                        break;
                    case 'graduates':
                        $restoredCount += $this->restoreGraduateRecord($archive, $data);
                        break;
                    case 'funds':
                        $restoredCount += $this->restoreFundRecord($archive, $data);
                        break;
                    case 'clubs':
                        $restoredCount += $this->restoreClubRecord($archive, $data);
                        break;
                }

                $archive->delete();
            }

            $rollover->update(['summary' => array_merge($rollover->summary ?? [], ['restored_count' => $restoredCount, 'files_restored' => $filesRestored])]);

            DB::commit();

            return redirect()->route('annual-rollover.index')
                ->with('success', "تم إلغاء الترحيل السنوي بنجاح. تم استعادة {$restoredCount} سجل و {$filesRestored} ملف.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الترحيل: ' . $e->getMessage());
        }
    }

    private function tryRestoreOrCreate(string $modelClass, array &$data): bool
    {
        $originalId = $data['id'] ?? null;
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);

        if ($originalId) {
            $existing = $modelClass::withoutGlobalScopes()->find($originalId);
            if ($existing) {
                $existing->restore();
                return false;
            }
        }

        $modelClass::withoutGlobalScopes()->create($data);
        return true;
    }

    private function restoreAdminRecord(AnnualArchive $archive, array $data): int
    {
        $modelClass = match ($archive->sub_type) {
            'violation' => Violation::class,
            'penalty' => Penalty::class,
            'commitment' => Commitment::class,
            'leave' => Leave::class,
            'absence' => Absence::class,
            default => null,
        };

        if (!$modelClass) return 0;

        $this->tryRestoreOrCreate($modelClass, $data);
        return 1;
    }

    private function restoreActivityRecord(AnnualArchive $archive, array $data): int
    {
        if ($archive->sub_type === 'activity') {
            $originalId = $data['id'] ?? null;
            $participants = $data['participants'] ?? [];
            unset($data['participants']);

            if ($originalId) {
                $existing = Activity::withoutGlobalScopes()->find($originalId);
                if ($existing) {
                    $existing->restore();
                    return 1;
                }
            }

            unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
            $activity = Activity::withoutGlobalScopes()->create($data);
            foreach ($participants as $p) {
                unset($p['id'], $p['activity_id']);
                $activity->participants()->create($p);
            }
            return 1;
        }

        if ($archive->sub_type === 'news') {
            $this->tryRestoreOrCreate(News::class, $data);
            return 1;
        }

        return 0;
    }

    private function restoreFinancialRecord(AnnualArchive $archive, array $data): int
    {
        $modelClass = match ($archive->sub_type) {
            'voucher' => Voucher::class,
            'budget' => MonthlyBudget::class,
            'settlement' => MonthlySettlement::class,
            'expense' => CenterExpense::class,
            default => null,
        };

        if (!$modelClass) return 0;

        if ($archive->sub_type === 'budget') {
            unset($data['items']);
        }
        if ($archive->sub_type === 'settlement') {
            unset($data['details']);
        }

        $this->tryRestoreOrCreate($modelClass, $data);
        return 1;
    }

    private function restoreNutritionRecord(AnnualArchive $archive, array $data): int
    {
        $modelClass = match ($archive->sub_type) {
            'food_distribution' => FoodDistribution::class,
            'food_subscription' => FoodSubscription::class,
            'food_invoice' => FoodPurchaseInvoice::class,
            'food_voucher' => FoodVoucher::class,
            'food_budget' => FoodBudget::class,
            'food_settlement' => FoodMonthlySettlement::class,
            default => null,
        };

        if (!$modelClass) return 0;

        if ($archive->sub_type === 'food_invoice') {
            unset($data['items']);
        }
        if ($archive->sub_type === 'food_budget') {
            unset($data['lines']);
        }
        if ($archive->sub_type === 'food_settlement') {
            unset($data['details']);
        }

        $this->tryRestoreOrCreate($modelClass, $data);
        return 1;
    }

    private function restoreQuranRecord(AnnualArchive $archive, array $data): int
    {
        if ($archive->sub_type === 'circle_session') {
            $originalId = $data['id'] ?? null;
            $attendance = $data['attendance'] ?? [];
            unset($data['attendance']);

            if ($originalId) {
                $existing = CircleSession::withoutGlobalScopes()->find($originalId);
                if ($existing) {
                    $existing->restore();
                    return 1;
                }
            }

            unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
            $session = CircleSession::withoutGlobalScopes()->create($data);
            foreach ($attendance as $a) {
                unset($a['id'], $a['session_id']);
                CircleAttendance::withoutGlobalScopes()->create(array_merge($a, ['session_id' => $session->id]));
            }
            return 1;
        }
        return 0;
    }

    private function restoreAcademicRecord(AnnualArchive $archive, array $data): int
    {
        $modelClass = match ($archive->sub_type) {
            'student_grade' => StudentGrade::class,
            'student_achievement' => StudentAchievement::class,
            default => null,
        };

        if (!$modelClass) return 0;

        $this->tryRestoreOrCreate($modelClass, $data);
        return 1;
    }

    private function restoreVehicleRecord(AnnualArchive $archive, array $data): int
    {
        $this->tryRestoreOrCreate(VehicleViolation::class, $data);
        return 1;
    }

    private function restoreComplaintRecord(AnnualArchive $archive, array $data): int
    {
        $this->tryRestoreOrCreate(Complaint::class, $data);
        return 1;
    }

    private function restoreGraduateRecord(AnnualArchive $archive, array $data): int
    {
        unset($data['room_number'], $data['major'], $data['program_name']);

        $originalId = $data['id'] ?? null;
        if ($originalId) {
            $existing = Student::withoutGlobalScopes()->find($originalId);
            if ($existing) {
                $existing->restore();
                return 1;
            }
        }

        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
        Student::withoutGlobalScopes()->create($data);
        return 1;
    }

    private function restoreFundRecord(AnnualArchive $archive, array $data): int
    {
        $this->tryRestoreOrCreate(Fund::class, $data);
        return 1;
    }

    private function restoreClubRecord(AnnualArchive $archive, array $data): int
    {
        $originalId = $data['id'] ?? null;
        $members = $data['members'] ?? [];
        unset($data['members']);

        if ($originalId) {
            $existing = Club::withoutGlobalScopes()->find($originalId);
            if ($existing) {
                $existing->restore();
                return 1;
            }
        }

        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
        $club = Club::withoutGlobalScopes()->create($data);
        foreach ($members as $m) {
            unset($m['id'], $m['club_id']);
            ClubMember::withoutGlobalScopes()->create(array_merge($m, ['club_id' => $club->id]));
        }
        return 1;
    }

    private function resolveArchiveView(AnnualArchive $archive, array $data, $originalId): ?\Illuminate\View\View
    {
        $module = $archive->module;
        $subType = $archive->sub_type;

        $viewModelMap = [
            'administrative' => [
                'violation' => [Violation::class, ['student', 'recordedBy', 'penalty', 'center'], 'violations.show', 'violation'],
                'commitment' => [Commitment::class, ['student', 'violation'], 'administrative.commitments', 'commitments'],
                'penalty' => [Penalty::class, ['student', 'appliedBy'], 'administrative.penalties', 'penalties'],
                'leave' => [Leave::class, ['student', 'approvedBy'], 'administrative.leaves', 'leaves'],
                'absence' => [Absence::class, ['student'], 'administrative.absences', 'absences'],
            ],
            'financial' => [
                'voucher' => [Voucher::class, ['fund', 'targetFund', 'creator', 'approver', 'center'], 'vouchers.show', 'voucher'],
                'budget' => [MonthlyBudget::class, ['items.fund', 'submitter', 'approver', 'center'], 'budgets.show', 'budget'],
                'settlement' => [MonthlySettlement::class, ['details.fund', 'submitter', 'approver', 'center'], 'settlements.show', 'settlement'],
            ],
            'clubs' => [
                'club' => [Club::class, ['members.student', 'center'], 'social.clubs.show', 'club'],
            ],
            'activities' => [
                'activity' => [Activity::class, ['club', 'participants.student', 'creator', 'targetedStudents'], 'social.activities.show', 'activity'],
                'news' => [News::class, [], 'social.news.show', 'news'],
            ],
            'complaints' => [
                'complaint' => [Complaint::class, ['sender', 'receiver'], 'complaints.show', 'complaint'],
            ],
            'quran' => [
                'circle_session' => [CircleSession::class, ['attendance.student'], 'circle-sessions.show', 'session'],
            ],
            'nutrition' => [
                'food_invoice' => [FoodPurchaseInvoice::class, ['supplier', 'items', 'creator'], 'nutrition.invoices.show', 'invoice'],
                'food_voucher' => [FoodVoucher::class, ['supplier', 'student', 'creator'], 'nutrition.vouchers.show', 'voucher'],
                'food_budget' => [FoodBudget::class, ['lines', 'creator', 'approver'], 'nutrition.budgets.show', 'budget'],
                'food_subscription' => [FoodSubscription::class, ['student', 'distributions.distributor', 'budget'], 'nutrition.subscriptions.show', 'subscription'],
                'food_supplier' => [FoodSupplier::class, ['invoices.items', 'vouchers'], 'nutrition.suppliers.show', 'supplier'],
            ],
        ];

        if (!isset($viewModelMap[$module][$subType])) {
            return null;
        }

        [$modelClass, $relations, $viewName, $varName] = $viewModelMap[$module][$subType];

        $model = null;
        if ($originalId) {
            $model = $modelClass::withoutGlobalScopes()->with($relations)->find($originalId);
        }
        if (!$model) {
            $cleanData = $data;
            unset($cleanData['id'], $cleanData['created_at'], $cleanData['updated_at'], $cleanData['deleted_at']);
            $model = $modelClass::withoutGlobalScopes()->create($cleanData);
            foreach ($relations as $relation) {
                try { $model->loadMissing($relation); } catch (\Exception $e) {}
            }
        }

        $viewData = [
    $varName => $model,
    'preview' => true,
    'previewArchive' => $archive,
];

        if ($subType === 'settlement' && $module === 'financial') {
            $viewData['vouchers'] = Voucher::with(['creator', 'targetFund', 'student'])
                ->where('center_id', $model->center_id)
                ->where('status', 'approved')
                ->whereMonth('created_at', $model->month)
                ->whereYear('created_at', $model->year)
                ->get();
        }

        return view($viewName, $viewData);
    }

    public function showArchive(Request $request, AnnualArchive $archive)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $archive->center_id && $archive->center_id != $user->center_id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'غير مصرح'], 403);
            }
            abort(403, 'غير مصرح بالوصول إلى هذا السجل المؤرشف');
        }

        $archive->load(['rollover.user', 'center', 'student']);

        $fileStatuses = [];
        if ($archive->archived_files) {
            foreach ($archive->archived_files as $original => $archived) {
                $fileStatuses[$original] = [
                    'archived_path' => $archived,
                    'archived_exists' => Storage::disk('public')->exists($archived),
                    'original_exists' => Storage::disk('public')->exists($original),
                ];
            }
        }

        $data = (array) ($archive->data ?? []);
        $originalId = $data['id'] ?? $archive->record_id ?? null;
        $idMap = [];

        if (!empty($data['center_id'])) {
            $c = Center::withoutGlobalScopes()->find($data['center_id']);
            if ($c) $idMap['center_id'] = $c->name;
        }

        if (!empty($data['student_id'])) {
            $s = Student::withoutGlobalScopes()->find($data['student_id']);
            if ($s) $idMap['student_id'] = $s->name_ar;
        }

        if (!empty($data['fund_id'])) {
            $f = Fund::withoutGlobalScopes()->find($data['fund_id']);
            if ($f) $idMap['fund_id'] = 'صندوق: ' . $f->name;
        }

        if (!empty($data['target_fund_id'])) {
            $f = Fund::withoutGlobalScopes()->find($data['target_fund_id']);
            if ($f) $idMap['target_fund_id'] = 'صندوق: ' . $f->name;
        }

        if (!empty($data['vehicle_id'])) {
            $v = \App\Models\Vehicle::withoutGlobalScopes()->find($data['vehicle_id']);
            if ($v) $idMap['vehicle_id'] = $v->plate_number ?? ('مركبة #' . $v->id);
        }

        if (!empty($data['room_id'])) {
            $r = \App\Models\Room::withoutGlobalScopes()->find($data['room_id']);
            if ($r) $idMap['room_id'] = 'غرفة ' . $r->room_number;
        }

        if (!empty($data['program_id'])) {
            $p = \App\Models\Program::withoutGlobalScopes()->find($data['program_id']);
            if ($p) $idMap['program_id'] = $p->name;
        }

        if (!empty($data['club_id'])) {
            $cl = Club::withoutGlobalScopes()->find($data['club_id']);
            if ($cl) $idMap['club_id'] = $cl->name;
        }

        if (!empty($data['circle_id'])) {
            $ci = \App\Models\QuranCircle::withoutGlobalScopes()->find($data['circle_id']);
            if ($ci) $idMap['circle_id'] = $ci->name;
        }

        if (!empty($data['session_id'])) {
            $ss = \App\Models\CircleSession::withoutGlobalScopes()->find($data['session_id']);
            if ($ss) $idMap['session_id'] = 'جلسة #' . $ss->id;
        }

        if (!empty($data['activity_id'])) {
            $a = Activity::withoutGlobalScopes()->find($data['activity_id']);
            if ($a) $idMap['activity_id'] = $a->name;
        }

        if (!empty($data['violation_id'])) {
            $v = Violation::withoutGlobalScopes()->find($data['violation_id']);
            if ($v) $idMap['violation_id'] = 'مخالفة #' . $v->id;
        }

        if (!empty($data['subscription_id'])) {
            $sub = FoodSubscription::withoutGlobalScopes()->find($data['subscription_id']);
            if ($sub) $idMap['subscription_id'] = 'اشتراك #' . $sub->id;
        }

        if (!empty($data['budget_id'])) {
            $b = FoodBudget::withoutGlobalScopes()->find($data['budget_id']);
            if ($b) $idMap['budget_id'] = 'موازنة: ' . ($b->month_year ?? '#' . $b->id);
        }

        $userFieldMap = [
            'recorded_by' => 'المسجّل',
            'created_by' => 'أنشئ بواسطة',
            'approved_by' => 'اعتمد بواسطة',
            'submitted_by' => 'قدّم بواسطة',
            'applied_by' => 'طبّق بواسطة',
            'distributed_by' => 'وزّع بواسطة',
            'performed_by' => 'نفّذ بواسطة',
            'user_id' => 'المستخدم',
        ];

        foreach ($userFieldMap as $field => $label) {
            if (!empty($data[$field])) {
                $u = \App\Models\User::find($data[$field]);
                if ($u) $idMap[$field] = $u->name;
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'archive' => $archive,
                'data' => $archive->data,
                'file_statuses' => $fileStatuses,
                'id_map' => $idMap,
            ]);
        }

        $originalView = $this->resolveArchiveView($archive, $data, $originalId);
        if ($originalView) {
            return $originalView;
        }

        return view('annual_rollover.show', compact('archive', 'fileStatuses', 'idMap'));
    }

    public function previewGraduate(AnnualArchive $archive)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $archive->center_id && $archive->center_id != $user->center_id) {
            abort(403, 'غير مصرح بالوصول إلى هذا السجل المؤرشف');
        }

        if ($archive->module !== 'graduates') {
            abort(400, 'هذا السجل ليس طالباً خريجاً');
        }

        $data = (array) $archive->data;

        $student = new \App\Models\Student();
        foreach ($data as $key => $value) {
            if ($student->isFillable($key)) {
                $student->{$key} = $value;
            }
        }
        $student->id = $archive->record_id ?? $data['id'] ?? 0;
        $student->exists = true;

        $student->setRelation('center', $archive->center);
        if (!empty($data['program_id'])) {
            $student->setRelation('program', \App\Models\Program::withoutGlobalScopes()->find($data['program_id']));
        }
        $student->setRelation('roomAssignments', collect());
        $student->setRelation('violations', collect());
        $student->setRelation('penalties', collect());
        $student->setRelation('leaves', collect());
        $student->setRelation('mealSubscription', null);
        $student->setRelation('user', null);

        return view('students.show', [
            'student' => $student,
            'preview' => true,
            'previewArchive' => $archive,
        ]);
    }

    public function exportArchivePdf(AnnualArchive $archive, PdfService $pdfService)
    {
        $user = auth()->user();
        if (!$user->hasRole('super-admin') && $archive->center_id && $archive->center_id != $user->center_id) {
            abort(403, 'غير مصرح بالوصول إلى هذا السجل المؤرشف');
        }

        $archive->load(['rollover.user', 'center', 'student']);

        $data = (array) $archive->data;
        $originalId = $data['id'] ?? $archive->record_id ?? null;

        $result = $this->resolveArchiveExport($archive, $data, $originalId, $pdfService);
        if ($result) {
            return $result;
        }

        $scalarData = [];
        $complexData = [];
        foreach ($data as $key => $val) {
            if (is_array($val)) {
                $complexData[$key] = $val;
            } else {
                $scalarData[$key] = $val;
            }
        }

        $moduleNames = [
            'administrative' => 'الإجراءات الإدارية والجزاءات',
            'activities' => 'الأنشطة والأخبار والفعاليات',
            'financial' => 'النظام المالي والسندات',
            'nutrition' => 'نظام التغذية والوجبات',
            'quran' => 'الحلقات القرآنية والحضور',
            'academic' => 'الأكاديمي والدرجات والإنجازات',
            'rooms' => 'تسكين وإخلاء الغرف',
            'vehicles' => 'مركبات الطلاب ومخالفاتها',
            'complaints' => 'الشكاوى والاقتراحات',
            'graduates' => 'الطلاب الخريجون',
            'funds' => 'الصناديق المالية',
            'clubs' => 'الأندية الطلابية',
        ];

        $reportTitle = 'تفاصيل السجل المؤرشف';
        $filename = 'archive_record_' . $archive->id . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdfService->stream(
            'pdf.annual_rollover.show',
            [
                'archive' => $archive,
                'data' => $data,
                'scalarData' => $scalarData,
                'complexData' => $complexData,
                'moduleName' => $moduleNames[$archive->module] ?? $archive->module,
            ],
            $reportTitle,
            $filename,
            'portrait',
            [
                'المركز' => optional($archive->center)->name ?? 'المركز العام',
                'السنة المؤرشفة' => $archive->year,
            ],
            [
                'رقم الأرشيف' => 'ARC-' . str_pad($archive->id, 6, '0', STR_PAD_LEFT),
                'تاريخ السجل الأصلي' => $archive->record_date ? $archive->record_date->format('Y/m/d') : '-',
            ]
        );
    }

    private function resolveArchiveExport(AnnualArchive $archive, array $data, $originalId, PdfService $pdfService): ?\Symfony\Component\HttpFoundation\Response
    {
        $module = $archive->module;
        $subType = $archive->sub_type;

        $modelMap = [
            'administrative' => [
                'violation' => [Violation::class, ['student', 'recordedBy', 'penalty', 'center'], 'pdf.violations.show', 'violation', 'تقرير تفصيلي للمخالفة'],
                'commitment' => [Commitment::class, ['student', 'violation'], 'pdf.commitments.show', 'commitment', 'تعهد طلابي'],
            ],
            'financial' => [
                'voucher' => [Voucher::class, ['fund', 'targetFund', 'creator', 'approver', 'center'], 'pdf.vouchers.show', 'voucher', 'سند مالي'],
                'budget' => [MonthlyBudget::class, ['items.fund', 'submitter', 'approver', 'center'], 'pdf.budgets.show', 'budget', 'موازنة شهرية'],
                'settlement' => [MonthlySettlement::class, ['details.fund', 'submitter', 'approver', 'center'], 'pdf.settlements.show', 'settlement', 'تصفيه مالية شهرية'],
            ],
            'clubs' => [
                'club' => [Club::class, ['members.student', 'center'], 'pdf.social.clubs.show-pdf', 'club', 'تقرير النادي'],
            ],
            'nutrition' => [
                'food_invoice' => [FoodPurchaseInvoice::class, ['supplier', 'items', 'creator'], 'pdf.nutrition.invoices.show', 'invoice', 'فاتورة مشتريات'],
                'food_voucher' => [FoodVoucher::class, ['supplier', 'student', 'creator'], 'pdf.nutrition.vouchers.show', 'voucher', 'سند تغذية'],
                'food_budget' => [FoodBudget::class, ['lines', 'creator', 'approver'], 'pdf.nutrition.budgets.show', 'budget', 'موازنة تغذية'],
                'food_supplier' => [FoodSupplier::class, ['invoices.items', 'vouchers'], 'pdf.nutrition.suppliers.show', 'supplier', 'كشف حساب مورد'],
            ],
        ];

        if ($module === 'graduates') {
            return $this->exportGraduateProfilePdf($archive, $pdfService);
        }

        if (isset($modelMap[$module][$subType])) {
            [$modelClass, $relations, $blade, $varName, $title] = $modelMap[$module][$subType];
            return $this->exportArchivedModelPdf($originalId, $modelClass, $relations, $blade, $varName, $archive, $pdfService, $title);
        }

        return null;
    }

    private function exportArchivedModelPdf(
        $originalId,
        string $modelClass,
        array $relations,
        string $bladeTemplate,
        string $variableName,
        AnnualArchive $archive,
        PdfService $pdfService,
        string $reportTitle
    ): \Symfony\Component\HttpFoundation\Response {
        $model = null;

        if ($originalId) {
            $model = $modelClass::withoutGlobalScopes()->with($relations)->find($originalId);
        }

        if (!$model) {
            $data = (array) $archive->data;
            unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);
            $model = $modelClass::withoutGlobalScopes()->create($data);
            foreach ($relations as $relation) {
                try { $model->loadMissing($relation); } catch (\Exception $e) {}
            }
        }

        $filename = class_basename($modelClass) . '_' . ($originalId ?? $archive->id) . '.pdf';
        return $pdfService->stream($bladeTemplate, [
            $variableName => $model,
        ], $reportTitle, $filename, 'portrait');
    }

    private function exportGraduateProfilePdf(AnnualArchive $archive, PdfService $pdfService)
    {
        $data = (array) $archive->data;

        $student = new Student();
        foreach ($data as $key => $value) {
            if ($student->isFillable($key)) {
                $student->{$key} = $value;
            }
        }
        $student->id = $archive->record_id ?? $data['id'] ?? 0;
        $student->exists = true;

        $student->setRelation('center', $archive->center);
        if (!empty($data['program_id'])) {
            $student->setRelation('program', \App\Models\Program::withoutGlobalScopes()->find($data['program_id']));
        }
        $student->setRelation('roomAssignments', collect());
        $student->setRelation('activeRoomAssignment', null);
        $student->setRelation('violations', collect());
        $student->setRelation('penalties', collect());
        $student->setRelation('leaves', collect());
        $student->setRelation('absences', collect());
        $student->setRelation('grades', collect());
        $student->setRelation('achievements', collect());
        $student->setRelation('quranCircles', collect());
        $student->setRelation('circleAttendances', collect());
        $student->setRelation('mealSubscription', null);
        $student->setRelation('foodSubscriptions', collect());
        $student->setRelation('user', null);
        $student->setRelation('vouchers', collect());

        $photoBase64 = $this->imageToBase64($student->photo);
        $barcodeBase64 = null;
        if ($student->barcode) {
            try {
                $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                    ->size(150)
                    ->generate($student->barcode);
                $barcodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
            } catch (\Exception $e) {
                $barcodeBase64 = null;
            }
        }

        $totalPaid = 0;
        $remainingFees = max(0, (float) $student->annual_fees - $totalPaid);

        $filename = 'graduate_profile_' . ($student->student_number ?? $archive->id) . '.pdf';

        return $pdfService->stream('pdf.reports.student-profile', [
            'student' => $student,
            'photoBase64' => $photoBase64,
            'idCardFileBase64' => null,
            'certificateFileBase64' => null,
            'universityCardFileBase64' => null,
            'barcodeBase64' => $barcodeBase64,
            'totalPaid' => $totalPaid,
            'remainingFees' => $remainingFees,
        ], 'ملف الطالب الخريج', $filename, 'portrait');
    }

    private function imageToBase64(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return null;
        }

        $mimeType = mime_content_type($fullPath);
        $data = file_get_contents($fullPath);

        return 'data:' . $mimeType . ';base64,' . base64_encode($data);
    }

    public function exportPdf(Request $request, PdfService $pdfService)
    {
        $user = auth()->user();
        $centerId = $user->hasRole('super-admin') ? ($request->get('center_id') ?: $user->center_id) : $user->center_id;

        $archivesQuery = AnnualArchive::query()
            ->when($centerId, fn($q) => $q->where('center_id', $centerId))
            ->with(['rollover', 'center', 'student']);

        if ($request->filled('archived_year')) {
            $archivesQuery->where('year', $request->archived_year);
        }
        if ($request->filled('module')) {
            $archivesQuery->where('module', $request->module);
        }
        if ($request->filled('date_from')) {
            $archivesQuery->whereDate('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $archivesQuery->whereDate('record_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $archivesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('year', 'like', "%{$search}%");
            });
        }

        $archives = $archivesQuery->latest()->get();
        $center = $centerId ? Center::find($centerId) : null;
        $selectedYear = $request->get('archived_year', 'جميع السنوات');

        $moduleNames = [
            'administrative' => 'الإجراءات الإدارية والجزاءات',
            'activities' => 'الأنشطة والأخبار والفعاليات',
            'financial' => 'النظام المالي والسندات',
            'nutrition' => 'نظام التغذية والوجبات',
            'quran' => 'الحلقات القرآنية والحضور',
            'academic' => 'الأكاديمي والدرجات والإنجازات',
            'rooms' => 'تسكين وإخلاء الغرف',
            'vehicles' => 'مركبات الطلاب ومخالفاتها',
            'complaints' => 'الشكاوى والاقتراحات',
            'graduates' => 'الطلاب الخريجون',
            'funds' => 'الصناديق المالية',
            'clubs' => 'الأندية الطلابية',
        ];

        $filters = [
            'المركز' => $center ? $center->name : 'جميع المراكز',
            'السنة المؤرشفة' => $request->filled('archived_year') ? $request->archived_year : 'جميع السنوات',
        ];

        if ($request->filled('module')) {
            $filters['القسم / القطاع'] = $moduleNames[$request->module] ?? $request->module;
        }
        if ($request->filled('date_from')) {
            $filters['من تاريخ'] = $request->date_from;
        }
        if ($request->filled('date_to')) {
            $filters['إلى تاريخ'] = $request->date_to;
        }

        $stats = [
            'إجمالي السجلات' => count($archives) . ' سجل',
        ];

        $totalAmount = $archives->sum('amount');
        if ($totalAmount > 0) {
            $stats['إجمالي المبالغ المالية'] = number_format($totalAmount, 2) . ' ريال';
        }

        $reportTitle = 'تقرير أرشيف السنوات السابقة (' . $selectedYear . ')';
        $filename = 'annual_archive_' . ($request->archived_year ?: 'all') . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdfService->stream(
            'annual_rollover.pdf',
            [
                'data' => $archives,
                'archives' => $archives,
                'center' => $center,
                'selectedYear' => $selectedYear,
                'stats' => $stats,
            ],
            $reportTitle,
            $filename,
            'landscape',
            $filters,
            $stats
        );
    }
}
