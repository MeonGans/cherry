<?php

namespace App\Http\Controllers;

use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = Session::withCount('users')
            ->orderByDesc('active')
            ->orderByDesc('start_date')
            ->get();

        return view('sessions.index', compact('sessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'nullable|boolean',
        ]);

        $validated['active'] = (bool) ($validated['active'] ?? false);

        DB::transaction(function () use ($validated) {
            if ($validated['active']) {
                Session::where('active', true)->update(['active' => false]);
            }

            Session::create($validated);
        });

        return redirect()->route('sessions.index')->with('success', 'Сесію створено.');
    }

    public function show(Session $session)
    {
        return $this->users($session);
    }

    public function edit(Session $session)
    {
        return view('sessions.edit', compact('session'));
    }

    public function update(Request $request, Session $session)
    {
        $validated = $request->validate([
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'active' => 'sometimes|required|boolean',
        ]);

        DB::transaction(function () use ($session, $validated) {
            if (($validated['active'] ?? false) === true) {
                Session::where('id', '!=', $session->id)->update(['active' => false]);
            }

            $session->update($validated);
        });

        return redirect()->route('sessions.index')->with('success', 'Сесію оновлено.');
    }

    public function destroy(Session $session)
    {
        $session->delete();
        return redirect()->route('sessions.index')->with('success', 'Сесію видалено.');
    }

    public function activate(Session $session)
    {
        DB::transaction(function () use ($session) {
            Session::where('active', true)->update(['active' => false]);
            $session->update(['active' => true]);
        });

        return redirect()->route('sessions.index')->with('success', 'Сесію активовано.');
    }

    public function deactivate(Session $session)
    {
        $session->update(['active' => false]);

        return redirect()->route('sessions.index')->with('success', 'Сесію деактивовано.');
    }

    public function users(Session $session)
    {
        $session->load(['users.team.element']);
        $users = $session->users
            ->sortBy('team_id')
            ->values();

        return view('sessions.users', compact('session', 'users'));
    }
}
