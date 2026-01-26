<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PatientGenderEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
final class PatientFactory extends Factory
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
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'birthday' => fake()->date(),
            'gender' => fake()->randomElement(array_map(fn ($case) => $case->value, PatientGenderEnum::cases())),
            'city' => fake()->city(),
            'street_address' => fake()->address(),
            'registration_date' => fake()->date(),
            'notes' => fake()->paragraph(),
        ];
    }

    /**
     * Indicate that the patient should have Arabic data.
     */
    public function arabic(): static
    {
        $arabicMaleNames = [
            'محمد عبدالرحمن الشمري',
            'عبدالله يوسف الغامدي',
            'سعد ناصر القحطاني',
            'يوسف عبدالعزيز العلي',
            'طارق إبراهيم الشهراني',
            'أحمد خالد الدوسري',
            'عمر فهد المطيري',
            'نواف سعد العتيبي',
        ];

        $arabicFemaleNames = [
            'عائشة سالم العتيبي',
            'مريم حمد المطيري',
            'لينا فهد الدوسري',
            'هند محمد الزهراني',
            'ريم عبدالله العسيري',
            'فاطمة علي القحطاني',
            'سارة أحمد الشمري',
            'نورا خالد الحربي',
        ];

        $arabicCities = [
            'الرياض',
            'جدة',
            'الدمام',
            'الخبر',
            'المدينة المنورة',
            'مكة المكرمة',
            'الطائف',
            'بريدة',
        ];

        $arabicStreets = [
            'حي النرجس، شارع الملك فهد',
            'حي الزهراء، شارع التحلية',
            'حي الفيصلية، طريق الكورنيش',
            'حي العليا، شارع العروبة',
            'حي الملقا، شارع الأمير سلطان',
            'حي الراكة، شارع الأمير فيصل',
            'حي الياسمين، شارع العليا العام',
            'حي الصفا، شارع التحلية',
        ];

        $arabicNotes = [
            'مريض يعاني من ارتفاع ضغط الدم المزمن',
            'مريضة تحتاج متابعة دورية لمرض السكري',
            'مريض يعاني من الربو التحسسي',
            'مريضة حامل تحتاج متابعة طبية مستمرة',
            'مريض يعاني من آلام الظهر المزمنة',
            'مريضة تعاني من الصداع النصفي المتكرر',
            'مريض يعاني من التهاب المفاصل الروماتويدي',
            'مريضة تعاني من قصور الغدة الدرقية',
            'مريض يعاني من حساسية الجلد المزمنة',
            'مريضة تحتاج متابعة لارتفاع الكوليسترول',
        ];

        return $this->state(function (array $attributes) use ($arabicMaleNames, $arabicFemaleNames, $arabicCities, $arabicStreets, $arabicNotes) {
            $gender = $attributes['gender'] ?? fake()->randomElement([PatientGenderEnum::Male->value, PatientGenderEnum::Female->value]);
            $name = $gender === PatientGenderEnum::Male->value
                ? fake()->randomElement($arabicMaleNames)
                : fake()->randomElement($arabicFemaleNames);

            return [
                'name' => $name,
                'email' => fake()->unique()->safeEmail(),
                'phone_number' => fake()->numerify('05#########'),
                'birthday' => fake()->date('Y-m-d', '1990-01-01'),
                'gender' => $gender,
                'city' => fake()->randomElement($arabicCities),
                'street_address' => fake()->randomElement($arabicStreets),
                'registration_date' => fake()->date('Y-m-d', '-1 year'),
                'notes' => fake()->randomElement($arabicNotes),
            ];
        });
    }
}
