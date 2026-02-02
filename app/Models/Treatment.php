<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\TreatmentFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class Treatment extends Model
{
    use BelongsToTenant, HasFactory, HasUuids, TreatmentFilterQuery;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'default_cost',
    ];

    protected $casts = [
        'default_cost' => 'decimal:2',
    ];

    public function medicalRecordTreatments(): HasMany
    {
        return $this->hasMany(MedicalRecordTreatment::class);
    }
}
