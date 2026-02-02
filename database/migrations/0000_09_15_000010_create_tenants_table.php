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

            $table->string('phone_number')->nullable();
            $table->string('phone_number2')->nullable();
            $table->json('specialties')->nullable();
            $table->unsignedInteger('number_of_doctors')->default(0);
            $table->unsignedInteger('number_of_secretaries')->default(0);
            $table->json('map_pin')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('start_working_day')->nullable();
            $table->string('end_working_day')->nullable();
            $table->time('start_working_time')->nullable();
            $table->time('end_working_time')->nullable();

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
