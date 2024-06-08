<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    public function run()
    {
        Team::create(['name' => 'Вогонь', 'element_id' => 2, 'session_id' => 1]);
        Team::create(['name' => 'Повітря', 'element_id' => 4, 'session_id' => 1]);
        Team::create(['name' => 'Вода', 'element_id' => 5, 'session_id' => 1]);
        Team::create(['name' => 'Земля', 'element_id' => 3, 'session_id' => 1]);
        Team::create(['name' => 'Нерозподілені', 'element_id' => 1, 'session_id' => 1]);
    }
}
