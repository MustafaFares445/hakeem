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
        Schema::create('billings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('type'); // incoming, outgoing
            $table->date('date');
            $table->foreignUuid('patient_id')->nullable()->constrained('patients')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('medical_record_id')->nullable()->constrained('medical_records')->cascadeOnDelete();
            $table->string('case_name')->nullable();
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('item_name')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('outgoing_type')->nullable(); // medicine, equipment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
