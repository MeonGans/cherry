<?php

namespace App\Http\Controllers;

use App\Models\CherryBalance;
use App\Models\Session as CampSession;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CherryController extends Controller
{
    public function index()
    {
        $session = $this->activeSession();

        if (!$session) {
            return view('cherries.index', [
                'session' => null,
                'users' => collect(),
                'teamTotals' => collect(),
                'grandTotal' => 0,
            ]);
        }

        [$users, $teamTotals, $grandTotal] = $this->scoreboard($session);

        return view('cherries.index', compact('session', 'users', 'teamTotals', 'grandTotal'));
    }

    public function adjust(Request $request, User $user)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'direction' => ['required', 'in:add,subtract'],
        ]);

        $session = $this->activeSessionOrFail();
        abort_unless((int) $user->session_id === (int) $session->id, 404);

        $delta = $data['direction'] === 'add' ? (int) $data['amount'] : -(int) $data['amount'];

        DB::transaction(function () use ($session, $user, $delta) {
            $balance = CherryBalance::query()
                ->where('session_id', $session->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                $balance = CherryBalance::create([
                    'session_id' => $session->id,
                    'user_id' => $user->id,
                    'amount' => 0,
                ]);
            }

            $balance->update(['amount' => max(0, $balance->amount + $delta)]);
        });

        return back()->with('success', "Кількість Черіків для {$user->name} оновлено.");
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:0', 'max:1000000000'],
        ]);

        $session = $this->activeSessionOrFail();
        abort_unless((int) $user->session_id === (int) $session->id, 404);

        CherryBalance::updateOrCreate(
            ['session_id' => $session->id, 'user_id' => $user->id],
            ['amount' => (int) $data['amount']]
        );

        return back()->with('success', "Кількість Черіків для {$user->name} збережено.");
    }

    public function result()
    {
        $session = $this->activeSessionOrFail();
        [$users, $teamTotals, $grandTotal] = $this->scoreboard($session);

        $bestPlayer = $users->first(fn (User $user) => $user->cherries > 0);
        $bestTeam = $teamTotals->first(fn (array $team) => $team['total'] > 0);

        return view('cherries.result', compact(
            'session',
            'bestPlayer',
            'bestTeam',
            'grandTotal'
        ));
    }

    private function activeSession(): ?CampSession
    {
        return CampSession::query()
            ->where('active', true)
            ->orderByDesc('start_date')
            ->first();
    }

    private function activeSessionOrFail(): CampSession
    {
        return $this->activeSession() ?? abort(404, 'Немає активного заїзду.');
    }

    private function scoreboard(CampSession $session): array
    {
        $balances = CherryBalance::query()
            ->where('session_id', $session->id)
            ->pluck('amount', 'user_id');

        $users = $session->users()
            ->with('team.element')
            ->get()
            ->each(function (User $user) use ($balances) {
                $user->cherries = (int) ($balances[$user->id] ?? 0);
            })
            ->sortBy([
                ['cherries', 'desc'],
                ['name', 'asc'],
            ])
            ->values();

        $teamTotals = $users
            ->filter(fn (User $user) => $user->team_id && $user->team)
            ->groupBy('team_id')
            ->map(function ($members) {
                $team = $members->first()->team;

                return [
                    'team' => $team,
                    'total' => $members->sum('cherries'),
                    'members_count' => $members->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return [$users, $teamTotals, $users->sum('cherries')];
    }
}
