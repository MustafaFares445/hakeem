<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AppointmentTypeEnum;
use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use App\Enums\PatientGenderEnum;
use App\Enums\RecordTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\TenantTypes;
use App\Models\Billing;
use App\Models\Booking;
use App\Models\ChronicDiseases;
use App\Models\ChronicMedications;
use App\Models\DentalLab;
use App\Models\FillerMaterial;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordTreatment;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\Treatment;
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

        /******************************************************* */
        $systemAdmin = User::create([
            'name' => 'مدير النظام',
            'username' => 'system_admin',
            'email' => 'systemAdmin@hakeem.sy',
            'phone_number' => '0501234567',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $systemAdmin->assignRole($systemAdminRole);

        /******************************************************* */
        $doctor1 = User::create([
            'name' => 'دكتور العيادة',
            'username' => 'doctor_clinic',
            'email' => 'doctor@hakeem.sy',
            'phone_number' => '0502345678',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor1->assignRole($doctorRole);

        $doctor2 = User::create([
            'name' => 'د. خالد سعد الدوسري',
            'username' => 'khalid_doctor',
            'email' => 'khalid@hakeem.sy',
            'phone_number' => '0503456789',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor2->assignRole($doctorRole);

        /******************************************************* */
        $secretary1 = User::create([
            'name' => 'سكريتاري العيادة',
            'username' => 'secretary_clinic',
            'email' => 'secretary@hakeem.sy',
            'phone_number' => '0504567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary1->assignRole($secretariatRole);

        $secretary2 = User::create([
            'name' => 'نورا محمد الحربي',
            'username' => 'nora_secretary',
            'email' => 'nora@hakeem.sy',
            'phone_number' => '0505678901',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary2->assignRole($secretariatRole);

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

        $fillerMaterialModels = FillerMaterial::where('tenant_id', $tenant->id)->get();
        $dentalLabModels = DentalLab::where('tenant_id', $tenant->id)->get();
        $treatmentModels = Treatment::where('tenant_id', $tenant->id)->get();

        $patientsForRecords = Patient::where('tenant_id', $tenant->id)->take(5)->get();

        foreach ($patientsForRecords as $patient) {
            $medicalRecord = MedicalRecord::create([
                'patient_id' => $patient->id,
                'record_date' => now()->subDays(rand(1, 10))->toDateString(),
                'record_type' => RecordTypeEnum::InClinic->value,
                'case_name' => 'ملف علاجي للأسنان',
                'description' => 'متابعة علاجية لحالة تسوس وآلام الأسنان.',
                'tenant_id' => $tenant->id,
            ]);

            $treatmentCount = rand(1, 3);

            for ($i = 0; $i < $treatmentCount; $i++) {
                $treatmentModel = $treatmentModels->random();
                $fillerMaterialModel = $fillerMaterialModels->random();
                $dentalLabModel = $dentalLabModels->random();

                $recordTreatment = MedicalRecordTreatment::create([
                    'medical_record_id' => $medicalRecord->id,
                    'treatment_id' => $treatmentModel->id,
                    'treatment_date' => now()->subDays(rand(0, 5))->toDateString(),
                    'treatment_cost' => $treatmentModel->default_cost,
                    'treatment_description' => 'جلسة علاجية ضمن خطة العلاج.',
                    'tooth_position' => fake()->randomElement(['11', '12', '13', '14', '15', '16', '17', '18']),
                    'filler_material_id' => $fillerMaterialModel->id,
                    'dental_lab_id' => $dentalLabModel->id,
                    'session_number' => $i + 1,
                    'tenant_id' => $tenant->id,
                ]);

                $assignedDoctor = rand(0, 1) === 0 ? $doctor1 : $doctor2;

                $recordTreatment->doctors()->sync([$assignedDoctor->id]);
            }

            Billing::create([
                'tenant_id' => $tenant->id,
                'type' => BillingTypeEnum::Incoming,
                'date' => $medicalRecord->record_date,
                'patient_id' => $patient->id,
                'user_id' => $doctor1->id,
                'medical_record_id' => $medicalRecord->id,
                'case_name' => $medicalRecord->case_name,
                'paid_amount' => (float) fake()->randomElement([100, 200, 300, 500, 750]),
                'total_cost' => (float) fake()->randomElement([500, 800, 1000, 1500, 2000]),
            ]);
        }

        $outgoingItems = [
            ['name' => 'إيبوبروفين 600', 'type' => BillingOutgoingTypeEnum::Medicine],
            ['name' => 'منظم ضربات القلب', 'type' => BillingOutgoingTypeEnum::Equipment],
            ['name' => 'حشوة مؤقتة', 'type' => BillingOutgoingTypeEnum::Medicine],
            ['name' => 'معدات تعقيم', 'type' => BillingOutgoingTypeEnum::Equipment],
        ];

        foreach (range(1, 8) as $i) {
            $item = fake()->randomElement($outgoingItems);
            Billing::create([
                'tenant_id' => $tenant->id,
                'type' => BillingTypeEnum::Outgoing,
                'date' => now()->subDays(rand(1, 30))->toDateString(),
                'item_name' => $item['name'],
                'quantity' => fake()->numberBetween(1, 5),
                'amount' => (float) fake()->randomElement([20, 50, 100, 200, 500]),
                'outgoing_type' => $item['type']->value,
            ]);
        }

        $allPatients = Patient::where('tenant_id', $tenant->id)->get();
        $appointmentTypes = AppointmentTypeEnum::cases();
        $timeSlots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00'];

        foreach ($allPatients as $patient) {
            $bookingCount = rand(2, 5);

            for ($i = 0; $i < $bookingCount; $i++) {
                $daysAgo = rand(-30, 60);
                $appointmentDate = now()->addDays($daysAgo)->toDateString();
                $appointmentTime = fake()->randomElement($timeSlots);
                $appointmentType = fake()->randomElement($appointmentTypes);
                $assignedDoctor = rand(0, 1) === 0 ? $doctor1 : $doctor2;

                Booking::create([
                    'patient_id' => $patient->id,
                    'tenant_id' => $tenant->id,
                    'date' => $appointmentDate,
                    'time' => $appointmentTime,
                    'appointment_type' => $appointmentType->value,
                    'user_id' => $assignedDoctor->id,
                ]);
            }
        }
    }
}
