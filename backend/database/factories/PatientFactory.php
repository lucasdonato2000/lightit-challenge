<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->firstName() . ' ' . fake()->lastName(),
            'email' => fake()->unique()->userName() . '@gmail.com',
            'phone_country_code' => fake()->randomElement(['+1', '+54', '+598', '+52']),
            'phone_number' => fake()->numerify('##########'),
            'document_photo_path' => 'documents/' . fake()->uuid() . '.jpg.encrypted',
        ];
    }
}
