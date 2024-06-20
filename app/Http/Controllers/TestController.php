<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Team;
use App\Models\Session;

class TestController extends Controller
{
    public function showTestForm()
    {
        // Вибираємо 3 випадкові запитання
        $questions = Question::inRandomOrder()->take(3)->get();

        // Отримуємо ID активної сесії
        $activeSession = Session::where('active', true)->first();

        // Перевіряємо, чи є активна сесія
        if (!$activeSession) {
            return redirect()->back()->with('error', 'Немає активної сесії.');
        }

        // Список користувачів, які без команди і які в активній сесії
        $users = User::whereNull('team_id')
            ->where('session_id', $activeSession->id)
            ->get();

        return view('test.show', compact('questions', 'users'));
    }

    public function handleTestSubmission(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'required|array',
            'answers.*' => 'exists:answers,id',
        ]);

        $user = User::find($request->input('user_id'));

        $answers = Answer::whereIn('id', $request->input('answers'))->get();

        // Підрахунок балів для кожної команди
        $teamPoints = [];
        foreach ($answers as $answer) {
            if (!isset($teamPoints[$answer->team_id])) {
                $teamPoints[$answer->team_id] = 0;
            }
            $teamPoints[$answer->team_id]++;
        }

        // Визначення команди з найбільшою кількістю балів
        arsort($teamPoints);

        // Перевірка наявності місць у команді
        $maxMembers = 5;

        $teams = Team::withCount('users')->get()->keyBy('id');

        $selectedTeamId = null;
        foreach ($teamPoints as $teamId => $points) {
            $team = $teams[$teamId];

            if ($team->users_count < $maxMembers) {
                $selectedTeamId = $teamId;
                break;
            }
        }

        // Якщо всі команди заповнені або немає команди, яка відповідає умовам
        if (is_null($selectedTeamId)) {
            $availableTeams = Team::whereDoesntHave('users', function ($query) use ($maxMembers) {
                $query->havingRaw('COUNT(*) < ?', [$maxMembers]);
            })->pluck('id')->toArray();

            if (!empty($availableTeams)) {
                $selectedTeamId = $availableTeams[array_rand($availableTeams)];
            } else {
                return redirect()->back()->with('error', 'Немає доступних команд з місцями.');
            }
        }

        $user->team_id = $selectedTeamId;
        $user->save();

        return redirect()->route('test.result', ['team' => $selectedTeamId]);
    }

    public function showTestResult($teamId)
    {
        $team = Team::findOrFail($teamId);
        $message = "Вітаємо! Ви потрапили в команду {$team->name}.";

        return view('test.result', compact('message', 'team'));
    }
}
