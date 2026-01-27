<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\MedicalRecordTreatmentFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class MedicalRecordTreatment extends Model
{
    use BelongsToTenant, HasFactory, HasUuids, MedicalRecordTreatmentFilterQuery;

    protected $table = 'medical_record_treatments';

    protected $fillable = [
        'medical_record_id',
        'treatment_id',
        'treatment_date',
        'treatment_cost',
        'treatment_description',
        'tooth_position',
        'filler_material_id',
        'dental_lab_id',
        'session_number',
    ];

    protected $casts = [
        'treatment_date' => 'date',
        'treatment_cost' => 'decimal:2',
    ];

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class)->whereNull('deleted_at');
    }

    public function dentalLab(): BelongsTo
    {
        return $this->belongsTo(DentalLab::class);
    }

    public function fillerMaterial(): BelongsTo
    {
        return $this->belongsTo(FillerMaterial::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'doctor_medical_record_treatment',
            'medical_record_treatment_id',
            'user_id'
        )->withTimestamps();
    }
}
