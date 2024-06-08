<?php

namespace Database\Factories;

use App\Models\Liceum;
use Illuminate\Database\Eloquent\Factories\Factory;

class LiceumFactory extends Factory
{
    protected $model = Liceum::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
