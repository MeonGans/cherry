<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'session_id' => 1, // або інший ідентифікатор заїзду
            'phone_number' => $this->faker->phoneNumber,
            'date_of_birth' => $this->faker->date,
            'liceum_id' => 1, // завжди 1
            'team_id' => 1, // або інший ідентифікатор команди
            'gender' => $this->faker->randomElement(['male', 'female']),
            'pin_code' => $this->generateUniquePinCode(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function generateUniquePinCode()
    {
        do {
            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('pin_code', $pin)->exists());

        return $pin;
    }
}
