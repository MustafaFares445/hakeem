<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Scopes\MainTenantScope;
use Database\Factories\TenantFactory;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

#[ScopedBy(MainTenantScope::class)]
final class Tenant extends BaseTenant implements TenantWithDatabase
{
    /** @use HasFactory<TenantFactory> */
    use HasDatabase, HasDomains, HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'type',
        'tenant_id',
        'data',
        'domain_name',
        // explicit clinic fields
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
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create subdomain automatically when Tenant is created
     */
    public static function boot(): void
    {
        parent::boot();

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
}
