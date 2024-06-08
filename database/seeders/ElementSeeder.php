<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Element;

class ElementSeeder extends Seeder
{
    public function run()
    {
        Element::create(['name' => 'Не в команді', 'color' => '#c0c0c0']);
        Element::create(['name' => 'Вогонь', 'color' => '#FF914D']);
        Element::create(['name' => 'Земля', 'color' => '#7ED957']);
        Element::create(['name' => 'Повітря', 'color' => '#C1FDFF']);
        Element::create(['name' => 'Вода', 'color' => '#0097B2']);
    }
}
