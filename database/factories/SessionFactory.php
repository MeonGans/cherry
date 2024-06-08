<?php

namespace Database\Factories;

use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        return [
            'start_date' => now(),
            'end_date' => now()->addWeek(),
            'active' => false,
        ];
    }
}
