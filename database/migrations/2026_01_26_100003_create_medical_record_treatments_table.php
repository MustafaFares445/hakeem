<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medical_record_treatments', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->unsignedBigInteger('session_number')->default(1);
            $table->foreignUuid('medical_record_id')->constrained('medical_records');
            $table->foreignUuid('treatment_id')->nullable()->constrained('treatments');
            $table->date('treatment_date');
            $table->decimal('treatment_cost', 10, 2)->default(0);
            $table->text('treatment_description')->nullable();
            $table->string('tooth_position')->nullable(); // FDI notation: 11-18, 21-28, etc.
            $table->foreignUuid('filler_material_id')->constrained('filler_materials');
            $table->foreignUuid('dental_lab_id')->nullable()->constrained('dental_labs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_treatments');
    }
};
