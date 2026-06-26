<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ZootopiaSafeController extends Controller
{
    private array $validPasswords = [
        'хynoujtxx',
        'хarptydox',
        'хndsgxysx',
        'xzbcmpsxх',
        'хmpsxjtxx',
    ];

    public function show()
    {
        return view('zootopia-quest.safe');
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

        return redirect()->route('zootopia.safe.result', ['position' => $position]);
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

        $position = (int) $position;

        return view('zootopia-quest.safe-result', [
            'message' => $messages[$position] ?? 'Всі місця зайняті',
            'result' => $this->resultPresentation($position),
            'position' => $position,
        ]);
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

    private function resultPresentation(int $position): array
    {
        return match ($position) {
            1 => [
                'variant' => 'red',
                'label' => 'Червоний бокс',
                'eyebrow' => 'Перший доказ закріплено',
                'rank' => 'Головна перемога',
            ],
            2 => [
                'variant' => 'silver',
                'label' => 'Сірий бокс',
                'eyebrow' => 'Другий доказ закріплено',
                'rank' => 'Велика перемога',
            ],
            3 => [
                'variant' => 'black',
                'label' => 'Чорний бокс',
                'eyebrow' => 'Третій доказ закріплено',
                'rank' => 'Темна перемога',
            ],
            4 => [
                'variant' => 'bonus-strong',
                'label' => '200 черіків',
                'eyebrow' => 'Бокси вже забрали',
                'rank' => 'Компенсаційна перемога',
            ],
            5 => [
                'variant' => 'bonus',
                'label' => '150 черіків',
                'eyebrow' => 'Фінал пройдено',
                'rank' => 'Мала перемога',
            ],
            default => [
                'variant' => 'closed',
                'label' => 'Фінал закрито',
                'eyebrow' => 'Усі місця зайняті',
                'rank' => 'Квест завершено',
            ],
        };
    }
}
