<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PatientGenderEnum;
use App\Enums\RoleEnum;
use App\Enums\TenantTypes;
use App\Models\ChronicDiseases;
use App\Models\ChronicMedications;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JsonException;
use Spatie\Permission\Models\Role as SpatieRole;

final class ArabicDataSeeder extends Seeder
{
    /**
     * @throws JsonException
     */
    public function run(): void
    {
        DB::table('tenants')->insert([
            'id' => Str::uuid()->toString(),
            'name' => 'عيادة الحكيم الطبية',
            'type' => TenantTypes::SMALL_CLINIC->value,
            'domain_name' => 'alhakeem-clinic',
            'data' => json_encode([
                'description' => 'عيادة طبية متخصصة في الرعاية الصحية الشاملة',
                'address' => 'الرياض، المملكة العربية السعودية',
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);

        $tenant = Tenant::first();

        foreach (RoleEnum::cases() as $role) {
            SpatieRole::firstOrCreate(
                ['name' => $role->value, 'guard_name' => 'web']
            );
        }

        $systemAdminRole = SpatieRole::where('name', RoleEnum::SystemAdmin->value)->first();
        $doctorRole = SpatieRole::where('name', RoleEnum::Doctor->value)->first();
        $secretariatRole = SpatieRole::where('name', RoleEnum::Secretariat->value)->first();

        $systemAdmin = User::create([
            'name' => 'أحمد محمد العلي',
            'username' => 'ahmed_admin',
            'email' => 'ahmed.admin@alhakeem.com',
            'phone_number' => '0501234567',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $systemAdmin->assignRole($systemAdminRole);

        $doctor1 = User::create([
            'name' => 'د. فاطمة عبدالله السالم',
            'username' => 'fatima_doctor',
            'email' => 'fatima.doctor@alhakeem.com',
            'phone_number' => '0502345678',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor1->assignRole($doctorRole);

        $doctor2 = User::create([
            'name' => 'د. خالد سعد الدوسري',
            'username' => 'khalid_doctor',
            'email' => 'khalid.doctor@alhakeem.com',
            'phone_number' => '0503456789',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor2->assignRole($doctorRole);

        $secretary1 = User::create([
            'name' => 'سارة علي القحطاني',
            'username' => 'sara_secretary',
            'email' => 'sara.secretary@alhakeem.com',
            'phone_number' => '0504567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary1->assignRole($secretariatRole);

        $secretary2 = User::create([
            'name' => 'نورا محمد الحربي',
            'username' => 'nora_secretary',
            'email' => 'nora.secretary@alhakeem.com',
            'phone_number' => '0505678901',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary2->assignRole($secretariatRole);

        $patients = [
            [
                'name' => 'محمد عبدالرحمن الشمري',
                'email' => 'mohammed.patient1@example.com',
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
                'email' => 'aisha.patient2@example.com',
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
                'email' => 'abdullah.patient3@example.com',
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
                'email' => 'mariam.patient4@example.com',
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
                'email' => 'saad.patient5@example.com',
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
                'email' => 'lina.patient6@example.com',
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
                'email' => 'youssef.patient7@example.com',
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
                'email' => 'hind.patient8@example.com',
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
                'email' => 'tariq.patient9@example.com',
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
                'email' => 'reem.patient10@example.com',
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
