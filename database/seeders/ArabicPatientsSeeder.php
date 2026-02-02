<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PatientGenderEnum;
use App\Models\ChronicDiseases;
use App\Models\ChronicMedications;
use App\Models\Patient;
use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

final class ArabicPatientsSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrFail();

        $patients = [
            [
                'name' => 'محمد عبدالرحمن الشمري',
                'email' => 'mohammed@hakeem.sy',
                'phone_number' => '0511111111',
                'birthday' => '1985-03-15',
                'gender' => PatientGenderEnum::Male->value,
                'city' => 'الرياض',
                'street_address' => 'حي النرجس، شارع الملك فهد',
                'registration_date' => '2024-01-10',
                'notes' => 'مريض يعاني من ارتفاع ضغط الدم المزمن',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'عائشة سالم العتيبي',
                'email' => 'aisha@hakeem.sy',
                'phone_number' => '0512222222',
                'birthday' => '1990-07-22',
                'gender' => PatientGenderEnum::Female->value,
                'city' => 'جدة',
                'street_address' => 'حي الزهراء، شارع التحلية',
                'registration_date' => '2024-02-05',
                'notes' => 'مريضة تحتاج متابعة دورية لمرض السكري',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'عبدالله يوسف الغامدي',
                'email' => 'abdullah@hakeem.sy',
                'phone_number' => '0513333333',
                'birthday' => '1978-11-08',
                'gender' => PatientGenderEnum::Male->value,
                'city' => 'الدمام',
                'street_address' => 'حي الفيصلية، طريق الكورنيش',
                'registration_date' => '2024-01-20',
                'notes' => 'مريض يعاني من الربو التحسسي',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'مريم حمد المطيري',
                'email' => 'mariam@hakeem.sy',
                'phone_number' => '0514444444',
                'birthday' => '1992-05-14',
                'gender' => PatientGenderEnum::Female->value,
                'city' => 'الرياض',
                'street_address' => 'حي العليا، شارع العروبة',
                'registration_date' => '2024-03-01',
                'notes' => 'مريضة حامل تحتاج متابعة طبية مستمرة',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'سعد ناصر القحطاني',
                'email' => 'saad@hakeem.sy',
                'phone_number' => '0515555555',
                'birthday' => '1988-09-30',
                'gender' => PatientGenderEnum::Male->value,
                'city' => 'الرياض',
                'street_address' => 'حي الملقا، شارع الأمير سلطان',
                'registration_date' => '2024-02-15',
                'notes' => 'مريض يعاني من آلام الظهر المزمنة',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'لينا فهد الدوسري',
                'email' => 'lina@hakeem.sy',
                'phone_number' => '0516666666',
                'birthday' => '1995-12-03',
                'gender' => PatientGenderEnum::Female->value,
                'city' => 'الخبر',
                'street_address' => 'حي الراكة، شارع الأمير فيصل',
                'registration_date' => '2024-03-10',
                'notes' => 'مريضة تعاني من الصداع النصفي المتكرر',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'يوسف عبدالعزيز العلي',
                'email' => 'youssef@hakeem.sy',
                'phone_number' => '0517777777',
                'birthday' => '1982-04-18',
                'gender' => PatientGenderEnum::Male->value,
                'city' => 'الرياض',
                'street_address' => 'حي الياسمين، شارع العليا العام',
                'registration_date' => '2024-01-25',
                'notes' => 'مريض يعاني من التهاب المفاصل الروماتويدي',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'هند محمد الزهراني',
                'email' => 'hind@hakeem.sy',
                'phone_number' => '0518888888',
                'birthday' => '1987-08-25',
                'gender' => PatientGenderEnum::Female->value,
                'city' => 'جدة',
                'street_address' => 'حي الصفا، شارع التحلية',
                'registration_date' => '2024-02-20',
                'notes' => 'مريضة تعاني من قصور الغدة الدرقية',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'طارق إبراهيم الشهراني',
                'email' => 'tariq@hakeem.sy',
                'phone_number' => '0519999999',
                'birthday' => '1993-06-12',
                'gender' => PatientGenderEnum::Male->value,
                'city' => 'الرياض',
                'street_address' => 'حي النرجس، شارع العليا',
                'registration_date' => '2024-03-05',
                'notes' => 'مريض يعاني من حساسية الجلد المزمنة',
                'tenant_id' => $tenant->id,
            ],
            [
                'name' => 'ريم عبدالله العسيري',
                'email' => 'reem@hakeem.sy',
                'phone_number' => '0510000000',
                'birthday' => '1991-10-07',
                'gender' => PatientGenderEnum::Female->value,
                'city' => 'الرياض',
                'street_address' => 'حي العليا، شارع التحلية',
                'registration_date' => '2024-02-28',
                'notes' => 'مريضة تحتاج متابعة لارتفاع الكوليسترول',
                'tenant_id' => $tenant->id,
            ],
        ];

        $chronicDiseases = [
            'ارتفاع ضغط الدم',
            'داء السكري من النوع الثاني',
            'الربو التحسسي',
            'التهاب المفاصل الروماتويدي',
            'قصور الغدة الدرقية',
            'ارتفاع الكوليسترول',
            'الأنيميا المزمنة',
            'التهاب القولون التقرحي',
        ];

        $chronicMedications = [
            'أدوية خفض ضغط الدم',
            'الإنسولين',
            'أدوية موسعات الشعب الهوائية',
            'مضادات الالتهاب غير الستيرويدية',
            'هرمونات الغدة الدرقية',
            'أدوية خفض الكوليسترول',
            'مكملات الحديد',
            'أدوية علاج التهاب القولون',
        ];

        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);
            $patient->addMedia(UploadedFile::fake()->image('patient.jpg', 100, 100))->toMediaCollection('primary-image');

            $diseaseCount = rand(1, 3);
            for ($i = 0; $i < $diseaseCount; $i++) {
                ChronicDiseases::create([
                    'patient_id' => $patient->id,
                    'title' => fake()->randomElement($chronicDiseases),
                    'tenant_id' => $tenant->id,
                ]);
            }

            $medicationCount = rand(1, 2);
            for ($i = 0; $i < $medicationCount; $i++) {
                ChronicMedications::create([
                    'patient_id' => $patient->id,
                    'title' => fake()->randomElement($chronicMedications),
                    'tenant_id' => $tenant->id,
                ]);
            }
        }
    }
}
