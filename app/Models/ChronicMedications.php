<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\ChronicMedicationsFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class ChronicMedications extends Model
{
    use BelongsToTenant, ChronicMedicationsFilterQuery, HasFactory , HasUuids;

    protected $fillable = [
        'patient_id',
        'tenant_id',
        'title',
    ];
}
