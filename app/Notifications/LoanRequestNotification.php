<?php

namespace App\Notifications;

use App\Models\LoanRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanRequestNotification extends Notification
{
    use Queueable;

    public $loan;
    public $manager;

    /**
     * Create a new notification instance.
     */
    public function __construct(LoanRequest $loan)
    {
        $this->loan = $loan;

        // manager name from branch → manager relation
        $this->manager = $loan->branch->manager->name ?? 'Unknown Manager';
    }

    /**
     * Delivery channels for the notification.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Store data in notifications table.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New Loan Request',
            'message' => 'A new loan request requires approval',
            'loan_id' => $this->loan->id,
            'member_id' => $this->loan->member_id,
            'manager' => $this->manager,
            'branch' => $this->loan->branch->name ?? null,
        ];
    }

    /**
     * Optional mail channel
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Loan Request Pending Approval')
            ->greeting('Hello Admin,')
            ->line('A new loan request requires your approval.')
            ->action('View Loan Request', url('/admin/loan-requests/' . $this->loan->id))
            ->line('Requested by: ' . $this->manager);
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
