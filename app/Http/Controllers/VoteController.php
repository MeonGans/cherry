<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\User;
use App\Models\Team;
use App\Models\UserVote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VoteController extends Controller
{
    public function index()
    {
        $votes = Vote::all();
        return view('votes.index', compact('votes'));
    }

    public function create()
    {
        return view('votes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $voteUrl = Str::random(10);
        Vote::create([
            'name' => $data['name'],
            'vote_url' => $voteUrl,
        ]);

        return redirect()->route('votes.show', $voteUrl);
    }

    public function show($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        return view('votes.show', compact('vote'));
    }

    public function authenticate(Request $request, $voteUrl)
    {
        $data = $request->validate([
            'pin_code' => 'required|string|max:255',
        ]);

        $user = User::where('pin_code', $data['pin_code'])->first();

        if (!$user) {
            return redirect()->route('votes.show', $voteUrl)->withErrors(['message' => 'Invalid PIN code.']);
        }

        return redirect()->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $user->id]);
    }

    public function vote($voteUrl, $userId)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $user = User::findOrFail($userId);
        $teams = Team::where('id', '!=', $user->team_id)
            ->where('id', '!=', 10)
            ->get();

        return view('votes.vote', compact('vote', 'user', 'teams'));
    }

    public function submitVote(Request $request, $voteUrl, $userId)
    {
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        $user = User::findOrFail($userId);

        // Перевірка, чи голосував користувач вже
        $existingVote = UserVote::where('vote_id', Vote::where('vote_url', $voteUrl)->first()->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            return redirect()->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $userId])->withErrors(['message' => 'You have already voted.']);
        }

        if ($user->team_id == $data['team_id']) {
            return redirect()->route('votes.vote', ['voteUrl' => $voteUrl, 'userId' => $userId])->withErrors(['message' => 'You cannot vote for your own team.']);
        }

        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        UserVote::create([
            'vote_id' => $vote->id,
            'team_id' => $data['team_id'],
            'user_id' => $user->id,
            'points' => 1, // Голос звичайного користувача рахується як 1 бал
        ]);

        return redirect()->route('votes.success');
    }


    public function success()
    {
        return view('votes.success');
    }

    public function result($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $results = UserVote::where('vote_id', $vote->id)
            ->with('team.element')
            ->get()
            ->groupBy('team_id')
            ->map(function($votes) {
                return $votes->sum('points');
            });

        $maxVotes = $results->max();

        $teams = Team::whereIn('id', $results->keys())->with('element')->get()->mapWithKeys(function ($team) use ($results) {
            return [$team->name => ['count' => $results[$team->id], 'color' => $team->element->color]];
        });

        return view('votes.result', compact('vote', 'teams', 'maxVotes'));
    }


    public function addPointsForm($voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $teams = Team::with('element')->get();

        return view('votes.add-points', compact('vote', 'teams'));
    }

    public function addPoints(Request $request, $voteUrl)
    {
        $vote = Vote::where('vote_url', $voteUrl)->firstOrFail();
        $data = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'points' => 'required|integer|min:1'
        ]);

        $team = Team::findOrFail($data['team_id']);

        // Додавання балів до команди
        UserVote::create([
            'vote_id' => $vote->id,
            'team_id' => $team->id,
            'user_id' => auth()->id() ?? null, // Використовуйте ідентифікатор адміністратора або NULL
            'points' => $data['points'],
        ]);

        return redirect()->route('votes.result', $voteUrl);
    }



}
