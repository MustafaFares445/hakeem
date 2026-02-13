<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Tenant;
use App\Models\TenantType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
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
        $tenantType = TenantType::query()->firstOrCreate(
            ['key' => 'small_clinic'],
            [
                'name' => 'Small Clinic',
                'description' => null,
                'is_active' => true,
            ]
        );

        $createdAt = now();
        $tenantId = Str::uuid()->toString();

        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => 'Al Hakeem Clinic',
            'tenant_type_id' => $tenantType->id,
            'domain_name' => 'alhakeem-clinic',
            'data' => json_encode([
                'description' => 'General medical clinic',
                'address' => 'Riyadh, Saudi Arabia',
            ], JSON_THROW_ON_ERROR),
            'trial_starts_at' => $createdAt,
            'trial_ends_at' => $createdAt->copy()->addMonth(),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        /** @var Tenant $tenant */
        $tenant = Tenant::query()->findOrFail($tenantId);

        $tenant->domains()->firstOrCreate(
            ['domain' => 'alhakeem-clinic.'.config('tenancy.default_domain')],
            ['domain' => 'alhakeem-clinic.'.config('tenancy.default_domain')]
        );

        $systemAdminRole = SpatieRole::where('name', RoleEnum::SystemAdmin->value)->first();
        $doctorRole = SpatieRole::where('name', RoleEnum::Doctor->value)->first();
        $secretariatRole = SpatieRole::where('name', RoleEnum::Secretariat->value)->first();

        $systemAdmin = User::firstOrCreate(
            ['username' => 'system_admin'],
            [
                'name' => 'System Admin',
                'email' => 'systemAdmin@hakeem.sy',
                'phone_number' => '0501234567',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );
        if (! $systemAdmin->hasRole($systemAdminRole)) {
            $systemAdmin->assignRole($systemAdminRole);
        }
        if ($systemAdmin->wasRecentlyCreated) {
            $systemAdmin->addMedia(UploadedFile::fake()->image('avatar.jpg', 100, 100))->toMediaCollection('primary-image');
        }

        $doctor1 = User::create([
            'name' => 'Clinic Doctor',
            'username' => 'doctor_clinic',
            'email' => 'doctor@hakeem.sy',
            'phone_number' => '0502345678',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor1->assignRole($doctorRole);
        $doctor1->addMedia(UploadedFile::fake()->image('avatar.jpg', 100, 100))->toMediaCollection('primary-image');

        $doctor2 = User::create([
            'name' => 'Khalid Al Dossary',
            'username' => 'khalid_doctor',
            'email' => 'khalid@hakeem.sy',
            'phone_number' => '0503456789',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $doctor2->assignRole($doctorRole);
        $doctor2->addMedia(UploadedFile::fake()->image('avatar.jpg', 100, 100))->toMediaCollection('primary-image');

        $secretary1 = User::create([
            'name' => 'Clinic Secretary',
            'username' => 'secretary_clinic',
            'email' => 'secretary@hakeem.sy',
            'phone_number' => '0504567890',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary1->assignRole($secretariatRole);
        $secretary1->addMedia(UploadedFile::fake()->image('avatar.jpg', 100, 100))->toMediaCollection('primary-image');

        $secretary2 = User::create([
            'name' => 'Nora Al Harbi',
            'username' => 'nora_secretary',
            'email' => 'nora@hakeem.sy',
            'phone_number' => '0505678901',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'tenant_id' => $tenant->id,
        ]);
        $secretary2->assignRole($secretariatRole);
        $secretary2->addMedia(UploadedFile::fake()->image('avatar.jpg', 100, 100))->toMediaCollection('primary-image');
    }
}
