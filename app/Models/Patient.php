<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\FilterQueries\PatientFilterQuery;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mrmarchone\LaravelAutoCrud\Traits\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia;

final class Patient extends Model implements HasMedia
{
    use HasFactory, HasMediaConversions, HasUuids, PatientFilterQuery;

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
}
