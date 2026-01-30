<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AppointmentTypeEnum;
use App\Traits\FilterQueries\BookingFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class Booking extends Model
{
    use BelongsToTenant, BookingFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'patient_id',
        'tenant_id',
        'date',
        'time',
        'appointment_type',
        'user_id',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'appointment_type' => AppointmentTypeEnum::class,
        ];
    }
}
