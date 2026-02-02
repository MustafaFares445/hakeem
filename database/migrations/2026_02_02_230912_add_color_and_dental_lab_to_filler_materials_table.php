<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('filler_materials', static function (Blueprint $table): void {
            $table->string('color', 20)->nullable()->after('name');
            $table->uuid('dental_lab_id')->nullable()->after('color');

            $table->foreign('dental_lab_id')
                ->references('id')
                ->on('dental_labs')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('filler_materials', static function (Blueprint $table): void {
            $table->dropForeign(['dental_lab_id']);
            $table->dropColumn(['color', 'dental_lab_id']);
        });
    }
};
