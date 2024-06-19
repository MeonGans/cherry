<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Team;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function show()
    {
        // Вибираємо 5 випадкові запитання
        $questions = Question::inRandomOrder()->take(5)->get();

        // Отримуємо ID активної сесії
        $activeSessionId = Session::where('active', true)->first()->id;

        // Список користувачів, які без команди і які в активній сесії
        $usersWithoutTeam = User::whereNull('team_id')
            ->where('session_id', $activeSessionId)
            ->get();


        return view('test.show', compact('questions', 'usersWithoutTeam'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'required|array',
            'answers.*' => 'required|exists:answers,id',
        ]);

        $teamScores = [];

        foreach ($data['answers'] as $answerId) {
            $answer = Answer::find($answerId);
            if ($answer) {
                if (!isset($teamScores[$answer->team_id])) {
                    $teamScores[$answer->team_id] = 0;
                }
                $teamScores[$answer->team_id]++;
            }
        }

        // Сортування команд за балами у зменшуваному порядку
        arsort($teamScores);

        // Вибір команди з урахуванням обмеження на кількість користувачів
        $selectedTeamId = null;
        foreach ($teamScores as $teamId => $score) {
            $team = Team::find($teamId);
            $teamCount = $team->users()->count();
            if (($teamId == 1 && $teamCount < 6) || ($teamId != 1 && $teamCount < 5)) {
                $selectedTeamId = $teamId;
                break;
            }
        }

        if ($selectedTeamId === null) {
            // Розподіл користувача рандомно серед інших вільних команд
            $availableTeams = Team::all()->filter(function($team) {
                if ($team->id == 10) {
                    return false; // Пропустити команду з ID 10
                }
                $teamCount = $team->users()->count();
                return ($team->id == 1 && $teamCount < 6) || ($team->id != 1 && $teamCount < 5);
            });

            if ($availableTeams->isEmpty()) {
                return redirect()->route('test.show')->withErrors(['message' => 'Всі команди заповнені']);
            }

            $selectedTeamId = $availableTeams->random()->id;
        }

        $team = Team::find($selectedTeamId);

        // Оновлення команди користувача
        $user = User::find($data['user_id']);
        $user->team_id = $team->id;
        $user->save();

        return redirect()->route('test.result', ['team' => $team]);
    }

    public function result(Team $team)
    {
        return view('test.result', compact('team'));
    }
}
