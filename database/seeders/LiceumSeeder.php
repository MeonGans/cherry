<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Liceum;

class LiceumSeeder extends Seeder
{
    public function run()
    {
        Liceum::create(['name' => 'Основа']);
        Liceum::create(['name' => 'Ідеал']);
    }
}
