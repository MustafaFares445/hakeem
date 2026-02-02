<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            Permissions\UserPermissionsSeeder::class,
            Permissions\PatientPermissionsSeeder::class,
            Permissions\ChronicDiseasesPermissionsSeeder::class,
            Permissions\ChronicMedicationsPermissionsSeeder::class,
            Permissions\DentalLabPermissionsSeeder::class,
            Permissions\TreatmentPermissionsSeeder::class,
            Permissions\MedicalRecordPermissionsSeeder::class,
            Permissions\MedicalRecordTreatmentPermissionsSeeder::class,
            Permissions\FillerMaterialPermissionsSeeder::class,
            Permissions\BillingPermissionsSeeder::class,
            Permissions\MediaPermissionsSeeder::class,
            Permissions\BookingPermissionsSeeder::class,
            RolesSeeder::class,
        ]);
    }
}
