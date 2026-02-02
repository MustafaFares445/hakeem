<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FillerMaterialColorEnum;
use App\Traits\FilterQueries\FillerMaterialFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class FillerMaterial extends Model
{
    use BelongsToTenant, FillerMaterialFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'color',
        'dental_lab_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'color' => FillerMaterialColorEnum::class,
        'is_active' => 'boolean',
    ];

    public function dentalLab(): BelongsTo
    {
        return $this->belongsTo(DentalLab::class);
    }

    public function medicalRecordTreatments(): HasMany
    {
        return $this->hasMany(MedicalRecordTreatment::class);
    }
}
