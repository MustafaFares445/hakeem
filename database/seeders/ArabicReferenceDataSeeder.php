<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DentalLab;
use App\Models\FillerMaterial;
use App\Models\Tenant;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

final class ArabicReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrFail();

        $fillerMaterials = [
            [
                'name' => 'حشوة مؤقتة (IRM)',
                'description' => 'حشوة مؤقتة لحماية السن لفترة قصيرة باستخدام مادة IRM.',
            ],
            [
                'name' => 'حشوة كومبوزيت',
                'description' => 'حشوة تجميلية بلون السن من مادة الكومبوزيت.',
            ],
            [
                'name' => 'حشوة أملغم',
                'description' => 'حشوة معدنية تقليدية (أملغم) للأسنان الخلفية.',
            ],
            [
                'name' => 'حشوة جلاس أيونومر',
                'description' => 'حشوة تطلق الفلورايد ومناسبة للأطفال والأسنان الحساسة.',
            ],
            [
                'name' => 'حشوة سيراميك',
                'description' => 'حشوة مصنوعة من السيراميك لمظهر جمالي ومتانة عالية.',
            ],
            [
                'name' => 'حشوة ذهبية',
                'description' => 'حشوة من الذهب تتميز بالقوة وطول العمر.',
            ],
        ];

        foreach ($fillerMaterials as $material) {
            FillerMaterial::create([
                'name' => $material['name'],
                'description' => $material['description'],
                'is_active' => true,
                'tenant_id' => $tenant->id,
            ]);
        }

        $dentalLabs = [
            [
                'name' => 'مختبر الأسنان المتكامل',
                'phone' => '0112345678',
                'address' => 'الرياض، حي العليا، شارع الملك فهد',
            ],
            [
                'name' => 'مختبر ابتسامة الرياض',
                'phone' => '0113456789',
                'address' => 'الرياض، حي النرجس، شارع أنس بن مالك',
            ],
        ];

        foreach ($dentalLabs as $lab) {
            DentalLab::create([
                'name' => $lab['name'],
                'phone' => $lab['phone'],
                'address' => $lab['address'],
                'tenant_id' => $tenant->id,
            ]);
        }

        $treatments = [
            [
                'name' => 'حشوة سن أمامي',
                'description' => 'علاج تسوس في الأسنان الأمامية باستخدام حشوة تجميلية.',
                'default_cost' => 300.00,
            ],
            [
                'name' => 'حشوة سن خلفي',
                'description' => 'حشوة للأسنان الخلفية لعلاج التسوس العميق.',
                'default_cost' => 400.00,
            ],
            [
                'name' => 'علاج عصب',
                'description' => 'تنظيف وعلاج عصب السن مع حشوة نهائية.',
                'default_cost' => 800.00,
            ],
            [
                'name' => 'تنظيف وتلميع الأسنان',
                'description' => 'تنظيف عميق للأسنان وإزالة الجير والتصبغات.',
                'default_cost' => 250.00,
            ],
        ];

        foreach ($treatments as $treatment) {
            Treatment::create([
                'name' => $treatment['name'],
                'description' => $treatment['description'],
                'default_cost' => $treatment['default_cost'],
                'tenant_id' => $tenant->id,
            ]);
        }
    }
}
