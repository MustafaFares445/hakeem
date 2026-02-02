<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id CHAR(36) NOT NULL');
        }

        if ($driver === 'sqlite') {
            Schema::create('personal_access_tokens_new', function (Blueprint $table) {
                $table->id();
                $table->string('tokenable_type');
                $table->string('tokenable_id', 36);
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
                $table->index(['tokenable_type', 'tokenable_id']);
            });
            DB::statement('INSERT INTO personal_access_tokens_new (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) SELECT id, tokenable_type, CAST(tokenable_id AS TEXT), name, token, abilities, last_used_at, expires_at, created_at, updated_at FROM personal_access_tokens');
            Schema::drop('personal_access_tokens');
            Schema::rename('personal_access_tokens_new', 'personal_access_tokens');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE personal_access_tokens MODIFY tokenable_id BIGINT UNSIGNED NOT NULL');
        }

        if ($driver === 'sqlite') {
            Schema::create('personal_access_tokens_new', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->text('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();
            });
            DB::statement('INSERT INTO personal_access_tokens_new (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) SELECT id, tokenable_type, CAST(tokenable_id AS INTEGER), name, token, abilities, last_used_at, expires_at, created_at, updated_at FROM personal_access_tokens');
            Schema::drop('personal_access_tokens');
            Schema::rename('personal_access_tokens_new', 'personal_access_tokens');
        }
    }
};
