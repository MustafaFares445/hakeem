<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\ChronicDiseasesFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

final class ChronicDiseases extends Model
{
    use BelongsToTenant, ChronicDiseasesFilterQuery, HasFactory , HasUuids;

    protected $fillable = [
        'patient_id',
        'title',
        'tenant_id',
    ];
}
