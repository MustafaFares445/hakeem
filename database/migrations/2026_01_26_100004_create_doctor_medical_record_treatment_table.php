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
        Schema::create('doctor_medical_record_treatment', static function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('medical_record_treatment_id')->constrained(
                table: 'medical_record_treatments',
                indexName: 'doctor_mrt_fk'
            );
            $table->foreignUuid('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_medical_record_treatment');
    }
};
