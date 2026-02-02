<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AppointmentTypeEnum;
use App\Enums\BillingOutgoingTypeEnum;
use App\Enums\BillingTypeEnum;
use App\Enums\RecordTypeEnum;
use App\Enums\RoleEnum;
use App\Models\Billing;
use App\Models\Booking;
use App\Models\DentalLab;
use App\Models\FillerMaterial;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordTreatment;
use App\Models\Patient;
use App\Models\Tenant;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Database\Seeder;

final class ArabicMedicalDataSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::firstOrFail();

        $doctors = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn ($q) => $q->where('name', RoleEnum::Doctor->value))
            ->get();
        $doctor1 = $doctors->get(0);
        $doctor2 = $doctors->get(1);

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
