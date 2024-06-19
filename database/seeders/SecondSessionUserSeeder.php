<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class SecondSessionUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Дмитренко Ілля', 'date_of_birth' => '2010-09-30', 'phone_number' => '+380977398538', 'gender' => 'male'],
            ['name' => 'Юзькова Аріана', 'date_of_birth' => '2012-04-24', 'phone_number' => '0991288734', 'gender' => 'female'],
            ['name' => 'Козаков Іван', 'date_of_birth' => '2011-06-02', 'phone_number' => '0982894709', 'gender' => 'male'],
            ['name' => 'Іванов Іван', 'date_of_birth' => '2011-07-18', 'phone_number' => '+380664538816', 'gender' => 'male'],
            ['name' => 'Пустовалова Ангеліна', 'date_of_birth' => '2010-11-03', 'phone_number' => '+380972059458', 'gender' => 'female'],
            ['name' => 'Лазутіна Вікторія', 'date_of_birth' => '2012-07-30', 'phone_number' => '0957603556', 'gender' => 'female'],
            ['name' => 'Діденко Давід', 'date_of_birth' => '2011-08-23', 'phone_number' => '0975433413', 'gender' => 'male'],
            ['name' => 'Бурлака Марія', 'date_of_birth' => '2010-08-04', 'phone_number' => '0688406582', 'gender' => 'female'],
            ['name' => 'Чантурідзе Ліліана', 'date_of_birth' => '2011-01-05', 'phone_number' => '0930097519', 'gender' => 'female'],
            ['name' => 'Любивий Назар', 'date_of_birth' => '2011-11-18', 'phone_number' => '0961809550', 'gender' => 'male'],
            ['name' => 'Тепленко Ігор', 'date_of_birth' => '2012-04-20', 'phone_number' => '+380957562475', 'gender' => 'male'],
            ['name' => 'Безкровний Максим', 'date_of_birth' => '2011-09-04', 'phone_number' => '+380957082402', 'gender' => 'male'],
            ['name' => 'Охріменко Поліна', 'date_of_birth' => '2011-02-24', 'phone_number' => '+380984912703', 'gender' => 'female'],
            ['name' => 'Колодійчик Ярослав', 'date_of_birth' => '2012-06-18', 'phone_number' => '+380952292108', 'gender' => 'male'],
            ['name' => 'Карпович Анна', 'date_of_birth' => '2011-11-09', 'phone_number' => '0502991202', 'gender' => 'female'],
            ['name' => 'Петренко Аліна', 'date_of_birth' => '2011-05-25', 'phone_number' => '+380974493270', 'gender' => 'female'],
            ['name' => 'Запорожець Ксенія', 'date_of_birth' => '2011-02-01', 'phone_number' => '+380502510341', 'gender' => 'female'],
            ['name' => 'Денисенко Діана', 'date_of_birth' => '2011-03-02', 'phone_number' => '0931451156', 'gender' => 'female'],
            ['name' => 'Павловський Роман', 'date_of_birth' => '2011-10-02', 'phone_number' => '0936545486', 'gender' => 'male'],
            ['name' => 'Новохацький Станіслав', 'date_of_birth' => '2012-05-11', 'phone_number' => '+380969530924', 'gender' => 'male'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'session_id' => 2, // Другий заїзд
                'phone_number' => $user['phone_number'],
                'date_of_birth' => $user['date_of_birth'],
                'liceum_id' => 1, // або інший ідентифікатор ліцею
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
