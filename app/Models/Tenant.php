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
