<?php

namespace App\Http\Controllers;

use App\Models\Session as CampSession;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SortingTwoController extends Controller
{
    private const QUESTION_COUNT = 4;

    public function show()
    {
        $activeSession = CampSession::where('active', true)->first();

        if (!$activeSession) {
            return redirect()->back()->with('error', 'Немає активної сесії.');
        }

        $users = User::whereNull('team_id')
            ->whereNotNull('desired_team_id')
            ->where('session_id', $activeSession->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        $questions = collect($this->questionDeck())
            ->shuffle()
            ->take(self::QUESTION_COUNT)
            ->values()
            ->all();

        return view('sorting2.show', compact('activeSession', 'questions', 'users'));
    }

    public function handle(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'answers' => ['required', 'array', 'size:' . self::QUESTION_COUNT],
            'answers.*' => ['required', 'string', 'max:80'],
        ]);

        $activeSession = CampSession::where('active', true)->first();

        if (!$activeSession) {
            return redirect()->back()->with('error', 'Немає активної сесії.');
        }

        $user = User::where('id', $request->input('user_id'))
            ->where('session_id', $activeSession->id)
            ->whereNull('team_id')
            ->firstOrFail();

        if (!$user->desired_team_id) {
            return redirect()->back()->with('error', 'Для цього учня не вказана бажана команда.');
        }

        DB::transaction(function () use ($user): void {
            $user->team_id = $user->desired_team_id;
            $user->save();
        });

        return redirect()->route('sorting2.result', ['team' => $user->team_id]);
    }

    public function result($teamId)
    {
        $team = Team::findOrFail($teamId);
        $imageId = $this->imageIdForTeam($team->name);
        $accent = $this->accentForTeam($team->name);

        return view('sorting2.result', compact('team', 'imageId', 'accent'));
    }

    private function imageIdForTeam(string $teamName): int
    {
        return match ($teamName) {
            'Вогонь' => 1,
            'Повітря' => 2,
            'Вода' => 3,
            'Земля' => 4,
            'Метал' => 5,
            default => 1,
        };
    }

    private function accentForTeam(string $teamName): string
    {
        return match ($teamName) {
            'Вогонь' => '#f97316',
            'Повітря' => '#38bdf8',
            'Вода' => '#0ea5e9',
            'Земля' => '#22c55e',
            'Метал' => '#94a3b8',
            default => '#6366f1',
        };
    }

    private function questionDeck(): array
    {
        return [
            [
                'prompt' => 'Команда заходить у нову локацію. Що хочеться зробити першим?',
                'options' => ['Озирнутися і знайти опорні точки', 'Піти вперед і перевірити маршрут', 'Почути ідеї всіх поруч', 'Знайти тихе місце для плану'],
            ],
            [
                'prompt' => 'На майстерці є кілька завдань. Яке забираєш собі?',
                'options' => ['Зібрати деталі в систему', 'Придумати несподіваний хід', 'Підтримати темп групи', 'Перевірити якість фіналу'],
            ],
            [
                'prompt' => 'У грі раптом змінюються правила. Твоя перша реакція?',
                'options' => ['Швидко адаптуватися', 'Поставити уточнююче питання', 'Підбадьорити інших', 'Знайти нову стратегію'],
            ],
            [
                'prompt' => 'Команді треба придумати назву. Що тобі ближче?',
                'options' => ['Коротко і сильно', 'Весело і незвично', 'Зі змістом для всіх', 'Так, щоб легко запамʼятати'],
            ],
            [
                'prompt' => 'Попереду квест. Яку роль береш на старті?',
                'options' => ['Шукати підказки', 'Вести групу вперед', 'Помічати деталі', 'Тримати всіх разом'],
            ],
            [
                'prompt' => 'Якщо у команди мало часу, що допоможе найбільше?',
                'options' => ['Чітко поділити задачі', 'Зробити перший сміливий крок', 'Не втрачати спокій', 'Підкинути нову ідею'],
            ],
            [
                'prompt' => 'Треба обрати місце для фото. Що обираєш?',
                'options' => ['Де видно всю команду', 'Де багато руху', 'Де є гарне світло', 'Де кадр виглядає незвично'],
            ],
            [
                'prompt' => 'У розмові з новою людиною тобі легше почати з...',
                'options' => ['Спільної теми', 'Жарту', 'Питання про інтереси', 'Короткої історії'],
            ],
            [
                'prompt' => 'На столі лежать різні предмети для завдання. Що береш першим?',
                'options' => ['Те, що виглядає надійно', 'Те, що може здивувати', 'Те, що допоможе всім', 'Те, що відкриває новий варіант'],
            ],
            [
                'prompt' => 'Командний плакат майже готовий. Чого йому бракує?',
                'options' => ['Ясного центру', 'Енергійного акценту', 'Легкості', 'Охайного фінального штриха'],
            ],
            [
                'prompt' => 'Під час репетиції хтось забув слова. Що робиш?',
                'options' => ['Підказую непомітно', 'Імпровізую поруч', 'Переводжу увагу на дію', 'Заспокоюю і повертаю ритм'],
            ],
            [
                'prompt' => 'Уяви маршрут на цілий день. Що має бути в ньому обовʼязково?',
                'options' => ['Місце для відкриттів', 'Час на відпочинок', 'Виклик для сміливості', 'Точка, де всі збираються разом'],
            ],
            [
                'prompt' => 'Який сигнал у команді тобі найприємніший?',
                'options' => ['Коли всі діють злагоджено', 'Коли є драйв', 'Коли хтось помічає деталі', 'Коли звучить нова ідея'],
            ],
            [
                'prompt' => 'Перед важливою грою ти більше довіряєш...',
                'options' => ['Підготовці', 'Відчуттю моменту', 'Підтримці друзів', 'Швидкому рішенню'],
            ],
            [
                'prompt' => 'Команді дали загадку без очевидної відповіді. Що спрацьовує?',
                'options' => ['Розкласти її на частини', 'Спробувати нестандартно', 'Поговорити вголос', 'Почекати, поки зʼявиться звʼязок'],
            ],
            [
                'prompt' => 'Як виглядає ідеальний фініш дня?',
                'options' => ['Є відчуття зробленої справи', 'Є історія, яку хочеться згадувати', 'Усі сміються разом', 'У голові вже новий план'],
            ],
            [
                'prompt' => 'Потрібно швидко вибрати символ команди. Що важливіше?',
                'options' => ['Сила форми', 'Рух', 'Баланс', 'Блиск деталей'],
            ],
            [
                'prompt' => 'Коли хтось пропонує дивну ідею, ти...',
                'options' => ['Шукаю, як її покращити', 'Пробую уявити результат', 'Питаю, кому це допоможе', 'Додаю до неї свій поворот'],
            ],
            [
                'prompt' => 'На старті великої справи тобі найкраще допомагає...',
                'options' => ['План на перші кроки', 'Віра в команду', 'Відчуття пригоди', 'Чітка мета'],
            ],
            [
                'prompt' => 'Якщо день стає надто шумним, що повертає сили?',
                'options' => ['Кілька хвилин тиші', 'Рух і зміна місця', 'Розмова з другом', 'Маленьке завершене завдання'],
            ],
        ];
    }
}
