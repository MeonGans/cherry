<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(SessionSeeder::class);
        $this->call(ElementSeeder::class);
        $this->call(TeamSeeder::class);
        $this->call(LiceumSeeder::class);
    }
}
