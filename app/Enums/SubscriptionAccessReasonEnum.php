<?php

declare(strict_types=1);

namespace App\Enums;

enum SubscriptionAccessReasonEnum: string
{
    case TrialActive = 'trial_active';
    case SubscriptionActive = 'subscription_active';
    case LifetimeActive = 'lifetime_active';
    case PendingConfirmation = 'pending_confirmation';
    case RenewalRequired = 'renewal_required';
}
