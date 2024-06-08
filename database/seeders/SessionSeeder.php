<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Session;

class SessionSeeder extends Seeder
{
    public function run()
    {
        Session::create([
            'start_date' => '2024-06-10',
            'end_date' => '2024-06-17',
            'active' => true,
        ]);

        Session::create([
            'start_date' => '2024-06-20',
            'end_date' => '2024-06-27',
            'active' => false,
        ]);

        Session::create([
            'start_date' => '2024-06-30',
            'end_date' => '2024-07-07',
            'active' => false,
        ]);
    }
}
