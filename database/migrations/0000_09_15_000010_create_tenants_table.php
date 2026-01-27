<?php

declare(strict_types=1);

use App\Enums\TenantTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', static function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->json('data')->nullable();
            $table->enum('type', array_values(TenantTypes::cases()))->default(TenantTypes::SMALL_CLINIC->value);
            $table->foreignUuid('tenant_id')->nullable()->constrained();
            $table->string('domain_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
