<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Mrmarchone\LaravelAutoCrud\Helpers\MediaHelper;
use Throwable;

final class ClinicService
{
    /**
     * Update clinic (tenant) data and its primary image in a transaction.
     *
     * @param  array<string, mixed>  $payload
     *
     * @throws Throwable
     */
    public function update(array $payload, Tenant $tenant): Tenant
    {
        return DB::transaction(static function () use ($payload, $tenant) {
            /** @var UploadedFile|null $primaryImage */
            $primaryImage = $payload['primaryImage'] ?? null;

            $tenant->update(self::mapPayloadToTenantAttributes($payload));
            MediaHelper::updateMedia($primaryImage, $tenant, 'primary-image');

            return $tenant;
        });
    }

    /**
     * Convert request payload keys (camelCase) to tenant model attributes.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private static function mapPayloadToTenantAttributes(array $payload): array
    {
        $mapped = [
            'clinicName' => 'name',
            'phoneNumber' => 'phone_number',
            'phoneNumber2' => 'phone_number2',
            'specialties' => 'specialties',
            'mapPin' => 'map_pin',
            'city' => 'city',
            'address' => 'address',
            'instagram' => 'instagram',
            'facebook' => 'facebook',
            'startWorkingDay' => 'start_working_day',
            'endWorkingDay' => 'end_working_day',
            'startWorkingTime' => 'start_working_time',
            'endWorkingTime' => 'end_working_time',
        ];

        $attributes = [];
        foreach ($mapped as $inputKey => $column) {
            if (array_key_exists($inputKey, $payload)) {
                $attributes[$column] = $payload[$inputKey];
            }
        }

        return $attributes;
    }
}
