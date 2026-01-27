<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
final class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->username(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->numerify('05#########'),
            'email_verified_at' => fake()->dateTime(),
            'password' => Hash::make('secret'),
        ];
    }

    /**
     * Indicate that the user should have Arabic data.
     */
    public function arabic(): static
    {
        $arabicNames = [
            'أحمد محمد العلي',
            'فاطمة عبدالله السالم',
            'خالد سعد الدوسري',
            'سارة علي القحطاني',
            'نورا محمد الحربي',
            'محمد عبدالرحمن الشمري',
            'عائشة سالم العتيبي',
            'عبدالله يوسف الغامدي',
            'مريم حمد المطيري',
            'سعد ناصر القحطاني',
        ];

        return $this->state(fn (array $attributes) => [
            'name' => fake()->randomElement($arabicNames),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->numerify('05#########'),
        ]);
    }
}
