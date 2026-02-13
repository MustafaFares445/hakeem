<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', static function (Blueprint $table): void {
            $table->boolean('is_suspended')->default(false)->after('trial_ends_at');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->text('suspension_reason')->nullable()->after('suspended_at');
            $table->foreignUuid('suspended_by_user_id')->nullable()->after('suspension_reason')->constrained('users')->nullOnDelete();

            $table->index(['is_suspended', 'suspended_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tenants', static function (Blueprint $table): void {
            $table->dropConstrainedForeignId('suspended_by_user_id');
            $table->dropIndex('tenants_is_suspended_suspended_at_index');
            $table->dropColumn(['is_suspended', 'suspended_at', 'suspension_reason']);
        });
    }
};
