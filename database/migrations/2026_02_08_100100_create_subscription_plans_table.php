<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_type_id')->constrained('tenant_types')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('original_price', 10, 2);
            $table->decimal('price', 10, 2);
            $table->char('currency_code', 3);
            $table->unsignedInteger('duration_value')->nullable();
            $table->enum('duration_unit', ['day', 'month', 'year'])->nullable();
            $table->boolean('is_lifetime')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['tenant_type_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
