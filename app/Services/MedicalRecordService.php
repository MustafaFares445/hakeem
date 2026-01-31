<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\MedicalRecordData;
use App\Enums\BillingTypeEnum;
use App\Models\Billing;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;
use Throwable;

final class MedicalRecordService
{
    /**
     * @throws Throwable
     */
    public function store(MedicalRecordData $data, ?float $totalCost = null, ?float $paidAmount = null): MedicalRecord
    {
        return DB::transaction(function () use ($data, $totalCost, $paidAmount) {
            $medicalRecord = MedicalRecord::create($data->onlyModelAttributes());

            $this->createBillingForRecord($medicalRecord, $totalCost, $paidAmount);

            return $medicalRecord;
        });
    }

    /**
     * @throws Throwable
     */
    public function update(MedicalRecordData $data, MedicalRecord $medicalRecord, ?float $totalCost = null, ?float $paidAmount = null): MedicalRecord
    {
        return DB::transaction(function () use ($data, $medicalRecord, $totalCost, $paidAmount) {
            tap($medicalRecord)->update($data->onlyModelAttributes());

            if ($totalCost !== null || $paidAmount !== null) {
                $this->createBillingForRecord($medicalRecord, $totalCost, $paidAmount);
            }

            return $medicalRecord;
        });
    }

    private function createBillingForRecord(MedicalRecord $medicalRecord, ?float $totalCost, ?float $paidAmount): void
    {
        if ($totalCost === null && $paidAmount === null) {
            return;
        }

        Billing::create([
            'tenant_id' => $medicalRecord->tenant_id ?? null,
            'type' => BillingTypeEnum::Incoming,
            'date' => $medicalRecord->record_date ?? now(),
            'patient_id' => $medicalRecord->patient_id,
            'user_id' => null,
            'medical_record_id' => $medicalRecord->id,
            'case_name' => $medicalRecord->case_name,
            'paid_amount' => $paidAmount,
            'total_cost' => $totalCost,
        ]);
    }
}
