<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'System Admin',
            'username' => 'system_admin',
            'email' => 'systemAdmin@hakeem.sy',
            'password' => Hash::make('password123'),
        ]);

        $user->assignRole('system admin');
    }
}
