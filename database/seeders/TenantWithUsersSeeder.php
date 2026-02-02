<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\TenantTypes;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JsonException;
use Spatie\Permission\Models\Role as SpatieRole;

final class TenantWithUsersSeeder extends Seeder
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
            'updated_at' => now(),
        ]);

        /** @var Tenant $tenant */
        $tenant = Tenant::firstOrFail();

        $tenant->domains()->create([
            'domain' => 'alhakeem-clinic.'.config('tenancy.default_domain'),
        ]);

        $systemAdminRole = SpatieRole::where('name', RoleEnum::SystemAdmin->value)->first();
        $doctorRole = SpatieRole::where('name', RoleEnum::Doctor->value)->first();
        $secretariatRole = SpatieRole::where('name', RoleEnum::Secretariat->value)->first();

        $systemAdmin = User::create([
            'name' => 'مدير النظام',
            'username' => 'system_admin',
            'email' => 'systemAdmin@hakeem.sy',
            'phone_number' => '0501234567',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
        $systemAdmin->assignRole($systemAdminRole);

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
    }
}
