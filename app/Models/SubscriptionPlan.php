<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionDurationUnitEnum;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class SubscriptionPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'tenant_type_id',
        'name',
        'description',
        'original_price',
        'price',
        'currency_code',
        'duration_value',
        'duration_unit',
        'is_lifetime',
        'is_active',
        'sort_order',
    ];

    /**
     * @return BelongsTo<TenantType, $this>
     */
    public function tenantType(): BelongsTo
    {
        return $this->belongsTo(TenantType::class);
    }

    /**
     * @return HasMany<SubscriptionOrder, $this>
     */
    public function subscriptionOrders(): HasMany
    {
        return $this->hasMany(SubscriptionOrder::class);
    }

    protected function casts(): array
    {
        return [
            'original_price' => 'decimal:2',
            'price' => 'decimal:2',
            'duration_value' => 'integer',
            'duration_unit' => SubscriptionDurationUnitEnum::class,
            'is_lifetime' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
