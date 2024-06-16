<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Place;

class QuestController extends Controller
{
    private $validPasswords = [
        'ynoujtx',
        'arptydo',
        'ndsgxys',
        'xzbcmps',
    ];

    public function show()
    {
        return view('quest');
    }

    public function handle(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        $password = $request->input('password');

        if (!in_array($password, $this->validPasswords)) {
            return back()->withErrors(['password' => 'Невірний пароль']);
        }

        if (Place::where('password', $password)->exists()) {
            return back()->withErrors(['password' => 'Цей пароль вже використано']);
        }

        $position = Place::max('position') + 1;

        Place::create([
            'password' => $password,
            'position' => $position,
        ]);

        return redirect()->route('quest.result', ['position' => $position]);
    }

    public function result($position)
    {
        $messages = [
            1 => 'Переможний червоний сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 5 4 7 3',
            2 => 'Переможний сірий сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 4 1 3 7',
            3 => 'Переможний чорний сейф ваш. Залишилось ввести пін-код. Точної комбінації ніхто не знає, але відомо, що використовуються цифри 8 5 2 4',
            4 => 'Нажаль сейфів не залишилось, проте ви дійшли до кінця, тому отримуєте 100 ЧЕРІКІВ!',
        ];

        $message = $messages[$position] ?? 'Всі місця зайняті';

        return view('quest-result', ['message' => $message]);
    }
}
