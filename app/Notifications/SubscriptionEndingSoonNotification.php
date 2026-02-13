<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\SubscriptionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SubscriptionEndingSoonNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly SubscriptionOrder $order,
        private readonly int $daysRemaining,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->order->tenant?->name ?? 'Unknown tenant';

        return (new MailMessage)
            ->subject('Subscription Ending Soon')
            ->line("{$tenantName} subscription ends in {$this->daysRemaining} day(s).")
            ->line("Plan: {$this->order->plan_name}")
            ->action('View Subscription Orders', url('/admin/subscription-orders'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_ending_soon',
            'orderId' => $this->order->id,
            'tenantId' => $this->order->tenant_id,
            'tenantName' => $this->order->tenant?->name,
            'planName' => $this->order->plan_name,
            'endsAt' => $this->order->ends_at?->toIso8601String(),
            'daysRemaining' => $this->daysRemaining,
        ];
    }
}
