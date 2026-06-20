<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Str;

class QuestController extends Controller
{
    private $validPasswords = [
        'хynoujtxx',
        'хarptydox',
        'хndsgxysx',
        'xzbcmpsxх',
        'хmpsxjtxx',
    ];

    public function show()
    {
        return view('quest');
    }

    public function handle(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string', 'size:9', 'regex:/^[\p{L}]+$/u'],
        ], [
            'password.required' => 'Введіть пароль з 9 букв',
            'password.size' => 'Пароль має містити рівно 9 букв',
            'password.regex' => 'Пароль має містити тільки букви',
        ]);

        $password = $this->normalizePassword($request->input('password'));
        $validPasswords = array_map(
            fn (string $validPassword) => $this->normalizePassword($validPassword),
            $this->validPasswords
        );

        if (!in_array($password, $validPasswords, true)) {
            return back()->withErrors(['password' => 'Невірний пароль']);
        }

        $passwordAlreadyUsed = Place::all()
            ->contains(fn (Place $place) => $this->normalizePassword($place->password) === $password);

        if ($passwordAlreadyUsed) {
            return back()->withErrors(['password' => 'Цей пароль вже використано']);
        }

        $position = Place::max('position') + 1;

        Place::create([
            'password' => $password,
            'position' => $position,
        ]);

        return redirect()->route('quest.result', ['position' => $position]);
    }

    private function normalizePassword(string $password): string
    {
        $password = Str::lower(trim($password));

        return strtr($password, [
            'а' => 'a',
            'е' => 'e',
            'о' => 'o',
            'р' => 'p',
            'с' => 'c',
            'у' => 'y',
            'х' => 'x',
        ]);
    }

    public function result($position)
    {
        $messages = [
            1 => 'Переможний червоний сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 5 4 7 3. Також відомо, що остання цифра коду точно НЕ 5, а 3 та 7 стоять не поруч.',
            2 => 'Переможний сірий сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 4 1 3 7. Також відомо, що остання цифра коду точно НЕ 4, а 1 та 3 стоять не поруч.',
            3 => 'Переможний чорний сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 8 5 2 4. Також відомо, що остання цифра коду точно НЕ 8, а 2 та 5 стоять не поруч.',
            4 => 'Нажаль сейфів не залишилось, проте ви дійшли до кінця, тому отримуєте 200 ЧЕРІКІВ!',
            5 => 'Нажаль сейфів не залишилось, проте ви дійшли до кінця, тому отримуєте 150 ЧЕРІКІВ!',
        ];

        $message = $messages[$position] ?? 'Всі місця зайняті';

        return view('quest-result', ['message' => $message]);
    }
}
