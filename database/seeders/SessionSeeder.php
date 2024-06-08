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
            'is_active' => true,
        ]);

        Session::create([
            'start_date' => '2024-06-20',
            'end_date' => '2024-06-27',
            'is_active' => false,
        ]);

        Session::create([
            'start_date' => '2024-06-30',
            'end_date' => '2024-07-07',
            'is_active' => false,
        ]);
    }
}
