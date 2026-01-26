<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesSeeder::class,
            Permissions\UserPermissionsSeeder::class,
            Permissions\PatientPermissionsSeeder::class,
            Permissions\ChronicDiseasesPermissionsSeeder::class,
            Permissions\ChronicMedicationsPermissionsSeeder::class,
        ]);
    }
}
