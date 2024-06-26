<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\User;
use App\Models\Team;

class OneTestController extends Controller
{
    public function showTestForm()
    {
        // Вибираємо 3 випадкові запитання
        $questions = Question::inRandomOrder()->take(3)->get();
        // Список користувачів, які ще не мають команди
        $users = User::whereNull('team_id')->get();

        return view('test', compact('questions', 'users'));
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
        $selectedTeamId = key($teamPoints);

        // Перевірка наявності місць у команді та кількості хлопців
        $maxMembers = 5;
        $maxMembersForUnassignedTeam = 6;

        $teams = Team::withCount(['users as males_count' => function ($query) {
            $query->where('gender', 'male');
        }])->withCount('users')->get()->keyBy('id');

        foreach ($teamPoints as $teamId => $points) {
            $team = $teams[$teamId];
            $isUnassignedTeam = $teamId == 10;
            $teamMaxMembers = $isUnassignedTeam ? $maxMembersForUnassignedTeam : $maxMembers;

            if ($team->users_count < $teamMaxMembers && ($team->males_count < 2 || $user->gender != 'male')) {
                $selectedTeamId = $teamId;
                break;
            }
        }

        // Якщо всі команди заповнені, вибір команди випадково серед тих, де є місця і мінімум 2 хлопці
        if (($teams[$selectedTeamId]->users_count >= $maxMembers && $teams[$selectedTeamId]->id != 10) ||
            ($teams[$selectedTeamId]->users_count >= $maxMembersForUnassignedTeam && $teams[$selectedTeamId]->id == 10) ||
            ($teams[$selectedTeamId]->males_count >= 2 && $user->gender == 'male')) {
            $availableTeams = Team::whereDoesntHave('users', function ($query) use ($maxMembers, $maxMembersForUnassignedTeam) {
                $query->havingRaw('COUNT(*) < ?', [($query->getBindings()[0] == 10) ? $maxMembersForUnassignedTeam : $maxMembers]);
            })->whereHas('users', function ($query) {
                $query->where('gender', 'male');
            }, '>=', 2)->pluck('id')->toArray();

            if (!empty($availableTeams)) {
                $selectedTeamId = $availableTeams[array_rand($availableTeams)];
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

        return view('test-result', compact('message'));
    }
}
