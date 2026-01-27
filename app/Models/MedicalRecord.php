<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\MedicalRecordFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mrmarchone\LaravelAutoCrud\Traits\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class MedicalRecord extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, HasMediaConversions, HasUuids, MedicalRecordFilterQuery;

    protected $fillable = [
        'patient_id',
        'record_date',
        'record_type',
        'case_name',
        'description',
        'total_cost',
        'remaining_amount',
    ];

    protected $casts = [
        'record_date' => 'date',
        'total_cost' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(MedicalRecordTreatment::class);
    }
}
