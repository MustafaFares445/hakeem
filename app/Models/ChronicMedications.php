<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\ChronicMedicationsFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ChronicMedications extends Model
{
    use ChronicMedicationsFilterQuery, HasFactory, HasUuids;

    protected $fillable = [
        'patient_id',
        'title',
    ];

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? $this->getRouteKeyName(), $value)->firstOrFail();
    }
}
