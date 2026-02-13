<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class TrialEndingSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Tenant $tenant,
        private readonly int $daysRemaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tenant Trial Ending Soon')
            ->line("{$this->tenant->name} trial ends in {$this->daysRemaining} day(s).")
            ->line('Please review and follow up if needed.')
            ->action('View Tenant', url('/admin/tenants'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'trial_ending_soon',
            'tenantId' => $this->tenant->id,
            'tenantName' => $this->tenant->name,
            'trialEndsAt' => $this->tenant->trial_ends_at?->toIso8601String(),
            'daysRemaining' => $this->daysRemaining,
        ];
    }
}
