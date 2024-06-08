<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['name' => 'Дяченко Марина', 'date_of_birth' => '2013-04-06', 'phone_number' => '0977087939'],
            ['name' => 'Остапенко Денис', 'date_of_birth' => '2012-02-07', 'phone_number' => '0507814758'],
            ['name' => 'Геращенко Вікторія Вадимівна', 'date_of_birth' => '2012-01-20', 'phone_number' => '380936417290'],
            ['name' => 'Конопольська Маша', 'date_of_birth' => '2012-07-28', 'phone_number' => '0977279232'],
            ['name' => 'Полупан Діана', 'date_of_birth' => '2012-03-19', 'phone_number' => '+38 (066) 144 77 23'],
            ['name' => 'Сушко Катерина Олександрівна', 'date_of_birth' => '2013-12-11', 'phone_number' => '+380993946494'],
            ['name' => 'Дробінська Марта Андріївна', 'date_of_birth' => '2012-07-02', 'phone_number' => '+380 68 166 9355'],
            ['name' => 'Комендантов Назар', 'date_of_birth' => '2011-09-27', 'phone_number' => '0958427003'],
            ['name' => 'Ширіпа Варвара', 'date_of_birth' => '2013-01-23', 'phone_number' => '0960461154'],
            ['name' => 'Сухорукова Анна', 'date_of_birth' => '2012-07-09', 'phone_number' => '0506878643'],
            ['name' => 'Качалов Марк', 'date_of_birth' => '2012-06-26', 'phone_number' => '0933881310'],
            ['name' => 'Дяченко Ліана Володимирівна', 'date_of_birth' => '2012-07-07', 'phone_number' => '0507770533'],
            ['name' => 'Височанський Макарій', 'date_of_birth' => '2011-08-11', 'phone_number' => '+380632429940'],
            ['name' => 'Гулько Віктор', 'date_of_birth' => '2013-07-26', 'phone_number' => '0960055327'],
            ['name' => 'Калюжна Владислава', 'date_of_birth' => '2013-01-24', 'phone_number' => '0505744342'],
            ['name' => 'Питлак Максим', 'date_of_birth' => '2011-08-22', 'phone_number' => '0970464030'],
            ['name' => 'Карлащук Мар\'яна', 'date_of_birth' => '2013-07-26', 'phone_number' => '+380959132978'],
            ['name' => 'Марчук Софія', 'date_of_birth' => '2013-08-20', 'phone_number' => '+380 (98) 224 57 68'],
            ['name' => 'Бойко Олександр', 'date_of_birth' => '2013-05-21', 'phone_number' => '0959401803'],
            ['name' => 'Бойко Марія', 'date_of_birth' => '2013-05-21', 'phone_number' => '0959401813'],
            ['name' => 'Тимур Ярцев', 'date_of_birth' => '2011-07-19', 'phone_number' => '0671777069'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'session_id' => 1, // або інший ідентифікатор заїзду
                'phone_number' => $user['phone_number'],
                'date_of_birth' => $user['date_of_birth'],
                'liceum_id' => 1, // завжди 1
                'team_id' => 1, // або інший ідентифікатор команди
                'gender' => 'female', // або визначте відповідний гендер
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
