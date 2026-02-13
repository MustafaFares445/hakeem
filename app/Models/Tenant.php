<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\MainTenantScope;
use Carbon\Carbon;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mrmarchone\LaravelAutoCrud\Traits\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

#[ScopedBy(MainTenantScope::class)]
final class Tenant extends BaseTenant implements HasMedia, TenantWithDatabase
{
    /** @use HasFactory<TenantFactory> */
    use HasDatabase, HasDomains, HasFactory , HasMediaConversions;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'tenant_type_id',
        'tenant_id',
        'data',
        'domain_name',
        'phone_number',
        'phone_number2',
        'specialties',
        'number_of_doctors',
        'number_of_secretaries',
        'map_pin',
        'city',
        'address',
        'instagram',
        'facebook',
        'start_working_day',
        'end_working_day',
        'start_working_time',
        'end_working_time',
        'trial_starts_at',
        'trial_ends_at',
        'is_suspended',
        'suspended_at',
        'suspension_reason',
        'suspended_by_user_id',
    ];

    /**
     * Casts for tenant attributes
     *
     * @var array<string,string>
     */
    protected $casts = [
        'data' => 'array',
        'specialties' => 'array',
        'map_pin' => 'array',
        'number_of_doctors' => 'integer',
        'number_of_secretaries' => 'integer',
        'start_working_time' => 'datetime:H:i',
        'end_working_time' => 'datetime:H:i',
        'trial_starts_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'is_suspended' => 'boolean',
        'suspended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create subdomain automatically when Tenant is created
     */
    public static function boot(): void
    {
        parent::boot();

        self::creating(static function (Tenant $tenant): void {
            if ($tenant->trial_starts_at === null) {
                $tenant->trial_starts_at = now();
            }

            if ($tenant->trial_ends_at === null && $tenant->trial_starts_at !== null) {
                $trialStart = $tenant->trial_starts_at instanceof Carbon
                    ? $tenant->trial_starts_at
                    : Carbon::parse((string) $tenant->trial_starts_at);

                $tenant->trial_ends_at = $trialStart->copy()->addMonth();
            }
        });

        self::created(static function (Tenant $tenant) {
            $subdomain = mb_strtolower(str_replace(' ', '-', $tenant->domain_name));

            $tenant->domains()->create([
                'domain' => $subdomain.'.'.config('tenancy.default_domain'),
            ]);
        });
    }

    /**
     * @return BelongsTo<self>
     */
    public function parentTenant(): BelongsTo
    {
        return $this->belongsTo(self::class);
    }

    /**
     * @return HasMany<self>
     */
    public function subTenant(): HasMany
    {
        return $this->hasMany(self::class);
    }

    /**
     * @return BelongsTo<TenantType, $this>
     */
    public function tenantType(): BelongsTo
    {
        return $this->belongsTo(TenantType::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function suspendedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'suspended_by_user_id');
    }

    /**
     * @return HasMany<SubscriptionOrder, $this>
     */
    public function subscriptionOrders(): HasMany
    {
        return $this->hasMany(SubscriptionOrder::class);
    }
}
