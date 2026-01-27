<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\PatientFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mrmarchone\LaravelAutoCrud\Traits\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class Patient extends Model implements HasMedia
{
    use BelongsToTenant, HasFactory, HasMediaConversions, HasUuids , PatientFilterQuery;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'birthday',
        'gender',
        'city',
        'street_address',
        'registration_date',
        'notes',
    ];

    public function chronicDiseases(): HasMany
    {
        return $this->hasMany(ChronicDiseases::class);
    }

    public function chronicMedications(): HasMany
    {
        return $this->hasMany(ChronicMedications::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
