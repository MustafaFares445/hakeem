<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\SubscriptionOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class NewPendingSubscriptionOrderNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly SubscriptionOrder $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $tenantName = $this->order->tenant?->name ?? 'Unknown tenant';

        return (new MailMessage)
            ->subject('New Pending Subscription Order')
            ->line("A new subscription order is awaiting moderation for {$tenantName}.")
            ->line("Plan: {$this->order->plan_name}")
            ->action('Review Order', url('/admin/subscription-orders'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pending_subscription_order',
            'orderId' => $this->order->id,
            'tenantId' => $this->order->tenant_id,
            'tenantName' => $this->order->tenant?->name,
            'planName' => $this->order->plan_name,
        ];
    }
}
