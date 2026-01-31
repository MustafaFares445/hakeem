<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use App\Traits\FilterQueries\BillingFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class Billing extends Model
{
    use BelongsToTenant, BillingFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'type',
        'date',
        'patient_id',
        'user_id',
        'medical_record_id',
        'case_name',
        'paid_amount',
        'total_cost',
        'item_name',
        'quantity',
        'amount',
        'outgoing_type',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    protected function casts(): array
    {
        return [
            'type' => BillingTypeEnum::class,
            'date' => 'date',
            'paid_amount' => 'decimal:2',
            'total_cost' => 'decimal:2',
            'amount' => 'decimal:2',
            'outgoing_type' => BillingOutgoingTypeEnum::class,
        ];
    }
}
