<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Repayment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendNextDayRepaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repayments:remind-next-day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send repayment reminders for next day due loans';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $repayments = Repayment::whereDate('due_date', $tomorrow)
            ->where('status', '!=', 'paid')
            ->with(['loan.member', 'loan.branch'])
            ->get();

        foreach ($repayments as $repayment) {
            $member = $repayment->loan->member;
            $branch = $repayment->loan->branch;

            // Find manager or branch user
            $managers = $branch->users()->where('role', 'manager')->get();

            $message = "Dear {$member->name}, your EMI of ₹{$repayment->amount} is due tomorrow ({$repayment->due_date->format('d M Y')}).";

            // === Send Email ===
            if ($member->email) {
                Mail::raw($message, function ($mail) use ($member) {
                    $mail->to($member->email)->subject('Loan Repayment Reminder');
                });
            }

            // === Send SMS (if integrated with API like Twilio / MSG91) ===
            if ($member->mobile) {
                // integrate SMS API call here
                // SmsService::send($member->mobile, $message);
            }

            // === Create System Notifications ===
            Notification::create([
                'branch_id' => $branch->id,
                'user_id' => null,
                'repayment_id' => $repayment->id,
                'type' => 'system',
                'title' => 'Upcoming Repayment Due',
                'message' => $message,
            ]);

            // Notify Branch Managers
            foreach ($managers as $manager) {
                Notification::create([
                    'branch_id' => $branch->id,
                    'user_id' => $manager->id,
                    'repayment_id' => $repayment->id,
                    'type' => 'system',
                    'title' => 'Branch Repayment Reminder',
                    'message' => "Member {$member->name} has a repayment of ₹{$repayment->amount} due tomorrow.",
                ]);
            }
        }

        $this->info('Next-day repayment reminders sent successfully!');
    }
}
