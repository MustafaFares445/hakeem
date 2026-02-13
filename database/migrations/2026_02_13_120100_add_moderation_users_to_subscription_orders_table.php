<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_orders', static function (Blueprint $table): void {
            $table->foreignUuid('confirmed_by_user_id')->nullable()->after('confirmed_at')->constrained('users')->nullOnDelete();
            $table->foreignUuid('cancelled_by_user_id')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subscription_orders', static function (Blueprint $table): void {
            $table->dropConstrainedForeignId('confirmed_by_user_id');
            $table->dropConstrainedForeignId('cancelled_by_user_id');
        });
    }
};
