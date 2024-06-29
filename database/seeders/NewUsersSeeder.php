<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class NewUsersSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Жайворонок Софія', 'date_of_birth' => '2007-12-21', 'phone_number' => '0954429042', 'gender' => 'female'],
            ['name' => 'Мотрущенко Артем', 'date_of_birth' => '2008-07-06', 'phone_number' => '380994822948', 'gender' => 'male'],
            ['name' => 'Шейкіна Анна', 'date_of_birth' => '2010-02-12', 'phone_number' => '0681744565', 'gender' => 'female'],
            ['name' => 'Зінькова Дар\'я', 'date_of_birth' => '2011-01-21', 'phone_number' => '0684082875', 'gender' => 'female'],
            ['name' => 'Березній Єлизавета Миколаївна', 'date_of_birth' => '2010-06-06', 'phone_number' => '0631028776', 'gender' => 'female'],
            ['name' => 'Зелена Вероніка', 'date_of_birth' => '2009-06-17', 'phone_number' => '0996314474', 'gender' => 'female'],
            ['name' => 'Короленко Арсеній', 'date_of_birth' => '2009-04-26', 'phone_number' => '0979107480', 'gender' => 'male'],
            ['name' => 'Добруцька Тетяна', 'date_of_birth' => '2009-08-11', 'phone_number' => '0969112238', 'gender' => 'female'],
            ['name' => 'Никоненко Іван', 'date_of_birth' => '2010-10-23', 'phone_number' => '0955057510', 'gender' => 'male'],
            ['name' => 'Ткаченко Дмитро Володимирович', 'date_of_birth' => '2009-02-13', 'phone_number' => '+380632564056', 'gender' => 'male'],
            ['name' => 'Фурман Анна', 'date_of_birth' => '2007-11-26', 'phone_number' => '0977607370', 'gender' => 'female'],
            ['name' => 'Музика Анастасія', 'date_of_birth' => '2010-03-26', 'phone_number' => '0974159320', 'gender' => 'female'],
            ['name' => 'Лісовська Валерія', 'date_of_birth' => '2008-12-12', 'phone_number' => '0689939207', 'gender' => 'female'],
            ['name' => 'Могильда Дарина', 'date_of_birth' => '2010-09-28', 'phone_number' => '965079465', 'gender' => 'female'],
            ['name' => 'Камілавкіна Маргарита', 'date_of_birth' => '2010-04-01', 'phone_number' => '@girlpearll', 'gender' => 'female'],
            ['name' => 'Удод Михайло', 'date_of_birth' => '2010-12-17', 'phone_number' => '0636904030', 'gender' => 'male'],
            ['name' => 'Павленко Дарʼя', 'date_of_birth' => '2008-12-21', 'phone_number' => '+380981764929', 'gender' => 'female'],
            ['name' => 'Третьякова Даша', 'date_of_birth' => '2007-03-29', 'phone_number' => '0978194551', 'gender' => 'female'],
            ['name' => 'Мартиненко Софія', 'date_of_birth' => '2010-03-17', 'phone_number' => '0987267650', 'gender' => 'female'],
            ['name' => 'Бевз Софія', 'date_of_birth' => '2010-07-17', 'phone_number' => '0974178757', 'gender' => 'female'],
            ['name' => 'Ворона Крістіна', 'date_of_birth' => '2008-08-25', 'phone_number' => '507028160', 'gender' => 'female'],
            ['name' => 'Тіхонова Ілона', 'date_of_birth' => '2010-11-30', 'phone_number' => '+380980016141', 'gender' => 'female'],
            ['name' => 'Шенфельд Дарʼя', 'date_of_birth' => '2007-05-03', 'phone_number' => '0992058251', 'gender' => 'female'],
            ['name' => 'Півень Артем', 'date_of_birth' => '2009-08-28', 'phone_number' => '380971976171', 'gender' => 'male'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'session_id' => 3, // Припускаємо, що всі користувачі відносяться до другого заїзду
                'phone_number' => $user['phone_number'],
                'date_of_birth' => $user['date_of_birth'],
                'liceum_id' => 1, // або інший ідентифікатор ліцею
                'team_id' => null, // нові користувачі ще не розподілені по командах
                'gender' => $user['gender'],
                'pin_code' => $this->generateUniquePinCode(),
            ]);
        }
    }

    private function generateUniquePinCode()
    {
        do {
            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('pin_code', $pin)->exists());

        return $pin;
    }
}
