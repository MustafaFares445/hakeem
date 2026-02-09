<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class CreateTenantTypesTableAndMigrateTenants extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_types', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $smallClinicId = Str::uuid()->toString();
        DB::table('tenant_types')->insert([
            'id' => $smallClinicId,
            'key' => 'small_clinic',
            'name' => 'Small Clinic',
            'description' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Schema::table('tenants', static function (Blueprint $table): void {
            $table->foreignUuid('tenant_type_id')
                ->nullable()
                ->after('type')
                ->constrained('tenant_types')
                ->nullOnDelete();
            $table->timestamp('trial_starts_at')->nullable()->after('end_working_time');
            $table->timestamp('trial_ends_at')->nullable()->after('trial_starts_at');
        });

        $tenantTypesByKey = DB::table('tenant_types')->pluck('id', 'key')->all();

        $tenants = DB::table('tenants')
            ->select('id', 'type', 'created_at')
            ->get();

        foreach ($tenants as $tenant) {
            $tenantTypeKey = $tenant->type ?? 'small_clinic';

            if (! isset($tenantTypesByKey[$tenantTypeKey])) {
                $tenantTypesByKey[$tenantTypeKey] = Str::uuid()->toString();

                DB::table('tenant_types')->insert([
                    'id' => $tenantTypesByKey[$tenantTypeKey],
                    'key' => $tenantTypeKey,
                    'name' => Str::headline(str_replace('_', ' ', $tenantTypeKey)),
                    'description' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $trialStartsAt = $tenant->created_at !== null ? Carbon::parse((string) $tenant->created_at) : now();
            $trialEndsAt = $trialStartsAt->copy()->addMonth();

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update([
                    'tenant_type_id' => $tenantTypesByKey[$tenantTypeKey],
                    'trial_starts_at' => $trialStartsAt,
                    'trial_ends_at' => $trialEndsAt,
                ]);
        }

        Schema::table('tenants', static function (Blueprint $table): void {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', static function (Blueprint $table): void {
            $table->enum('type', ['small_clinic'])->default('small_clinic')->after('name');
        });

        $tenantTypeKeys = DB::table('tenant_types')
            ->pluck('key', 'id')
            ->all();

        $tenants = DB::table('tenants')
            ->select('id', 'tenant_type_id')
            ->get();

        foreach ($tenants as $tenant) {
            $legacyType = $tenant->tenant_type_id !== null && isset($tenantTypeKeys[$tenant->tenant_type_id])
                ? $tenantTypeKeys[$tenant->tenant_type_id]
                : 'small_clinic';

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['type' => $legacyType]);
        }

        Schema::table('tenants', static function (Blueprint $table): void {
            $table->dropConstrainedForeignId('tenant_type_id');
            $table->dropColumn(['trial_starts_at', 'trial_ends_at']);
        });

        Schema::dropIfExists('tenant_types');
    }
}
