<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\SubscriptionOrder;
use Illuminate\Http\UploadedFile;
use Mrmarchone\LaravelAutoCrud\Traits\HasModelAttributes;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

final class SubscriptionOrderData extends Data
{
    use HasModelAttributes;

    /** @var class-string<SubscriptionOrder> */
    protected static string $model = SubscriptionOrder::class;

    public function __construct(
        #[Max(36)]
        public string $subscriptionPlanId,
        public UploadedFile $transactionImage,
    ) {}
}
