<?php

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition()
    {
        return [
            'question_id' => Question::factory(),
            'answer' => $this->faker->word,
            'team_id' => $this->faker->numberBetween(1, 5), // Наприклад, від 1 до 5
        ];
    }
}
