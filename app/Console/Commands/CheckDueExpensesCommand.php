<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CenterExpense;
use App\Models\User;
use App\Models\Complaint;
use Carbon\Carbon;

class CheckDueExpensesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:check-due';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for due or overdue center expenses and notify the General Manager.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for due expenses...');

        // Get super admins to notify
        $superAdmins = User::role('super-admin')->where('is_active', true)->get();
        if ($superAdmins->isEmpty()) {
            $this->warn('No super admin found to notify.');
            return;
        }

        // 1. Overdue expenses
        $overdueExpenses = CenterExpense::with('center')
            ->where('status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->get();

        foreach ($overdueExpenses as $expense) {
            foreach ($superAdmins as $admin) {
                $this->sendNotification(
                    $admin,
                    "تأخر سداد: {$expense->type_label} لمركز {$expense->center->name}",
                    "يُرجى الانتباه أن {$expense->type_label} لمركز ({$expense->center->name}) لشهر {$expense->month}/{$expense->year} قد تأخر عن موعد استحقاقه في {$expense->due_date->format('Y-m-d')}.\nالمبلغ: " . number_format($expense->amount, 2) . " ريال يمني.",
                    'urgent'
                );
            }
        }

        // 2. Soon to be due expenses (in exactly 3 days)
        $upcomingExpenses = CenterExpense::with('center')
            ->where('status', 'pending')
            ->where('due_date', '=', Carbon::today()->addDays(3))
            ->get();

        foreach ($upcomingExpenses as $expense) {
            foreach ($superAdmins as $admin) {
                $this->sendNotification(
                    $admin,
                    "اقتراب موعد سداد: {$expense->type_label} لمركز {$expense->center->name}",
                    "تذكير: يتبقى 3 أيام على موعد استحقاق {$expense->type_label} لمركز ({$expense->center->name}) لشهر {$expense->month}/{$expense->year}.\nتاريخ الاستحقاق: {$expense->due_date->format('Y-m-d')}\nالمبلغ: " . number_format($expense->amount, 2) . " ريال يمني.",
                    'normal'
                );
            }
        }

        $this->info('Done checking expenses.');
    }

    private function sendNotification(User $receiver, string $subject, string $body, string $priority)
    {
        // Check if a similar notification was sent recently to avoid spam
        $recentNotice = Complaint::where('receiver_id', $receiver->id)
            ->where('subject', $subject)
            ->where('created_at', '>=', Carbon::now()->subDays(2))
            ->exists();

        if (!$recentNotice) {
            Complaint::create([
                'sender_id' => $receiver->id, // System notice, we can make sender = receiver, or find a system user if one exists.
                'receiver_id' => $receiver->id,
                'subject' => $subject,
                'body' => $body,
                'status' => 'unread',
                'priority' => $priority,
            ]);
            $this->info("Notification sent: {$subject}");
        }
    }
}
