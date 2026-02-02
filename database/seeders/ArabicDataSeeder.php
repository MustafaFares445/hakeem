<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class ArabicDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantWithUsersSeeder::class,
            ArabicReferenceDataSeeder::class,
            ArabicPatientsSeeder::class,
            ArabicMedicalDataSeeder::class,
        ]);
    }
}
