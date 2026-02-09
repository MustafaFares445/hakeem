<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SubscriptionDurationUnitEnum;
use App\Enums\SubscriptionOrderStatusEnum;
use App\Traits\FilterQueries\SubscriptionOrderFilterQuery;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mrmarchone\LaravelAutoCrud\Traits\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class SubscriptionOrder extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, HasMediaConversions, HasUuids, SubscriptionOrderFilterQuery;

    protected $fillable = [
        'tenant_id',
        'subscription_plan_id',
        'created_by_user_id',
        'status',
        'plan_name',
        'plan_description',
        'original_price',
        'price',
        'currency_code',
        'duration_value',
        'duration_unit',
        'is_lifetime',
        'starts_at',
        'ends_at',
        'confirmed_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * @return BelongsTo<SubscriptionPlan, $this>
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForTenant(Builder $query, Tenant $tenant): Builder
    {
        return $query->where('tenant_id', $tenant->id);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', SubscriptionOrderStatusEnum::Confirmed);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', SubscriptionOrderStatusEnum::Pending);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActiveLifetime(Builder $query, CarbonImmutable $now): Builder
    {
        return $query
            ->where('is_lifetime', true)
            ->where(function (Builder $query) use ($now): void {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', $now);
            });
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActiveTimed(Builder $query, CarbonImmutable $now): Builder
    {
        return $query
            ->where('is_lifetime', false)
            ->whereNotNull(['starts_at', 'ends_at'])
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

    protected function casts(): array
    {
        return [
            'status' => SubscriptionOrderStatusEnum::class,
            'original_price' => 'decimal:2',
            'price' => 'decimal:2',
            'duration_value' => 'integer',
            'duration_unit' => SubscriptionDurationUnitEnum::class,
            'is_lifetime' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }
}
