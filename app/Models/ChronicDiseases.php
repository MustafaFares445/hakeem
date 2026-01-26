<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\ChronicDiseasesFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ChronicDiseases extends Model
{
    use ChronicDiseasesFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'patient_id',
        'title',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }
}
