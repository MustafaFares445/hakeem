<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\DentalLabFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class DentalLab extends Model
{
    use BelongsToTenant, DentalLabFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'phone',
        'address',
        'color',
    ];

    public function medicalRecordTreatments(): HasMany
    {
        return $this->hasMany(MedicalRecordTreatment::class);
    }
}
