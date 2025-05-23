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
        $questions = Question::inRandomOrder()->take(4)->get();

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

        $user->team_id = $user->desired_team_id;
        // Записуємо бажану команду
//        $user->desired_team_id = $request->input('desired_team_id');
        $user->save();

        return redirect()->route('test.result', ['team' => $user->team_id]);
    }

    public function showTestResult($teamId)
    {
        $team = Team::findOrFail($teamId);
        $message = "Вітаємо! Вас розподілено в команду {$team->name}.";

        return view('test.result', compact('message', 'team'));
    }
}
