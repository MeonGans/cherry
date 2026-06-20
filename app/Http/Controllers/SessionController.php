<?php

namespace App\Http\Controllers;

use App\Models\Liceum;
use App\Models\Session;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    private const DEFAULT_PHONE_NUMBER = '';
    private const DEFAULT_DATE_OF_BIRTH = '2010-01-01';
    private const DEFAULT_GENDER = 'female';

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
        return view('sessions.create', [
            'teams' => $this->availableTeams(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'nullable|boolean',
            'students' => 'nullable|string',
        ]);

        $validated['active'] = (bool) ($validated['active'] ?? false);
        $students = $this->parseStudents($validated['students'] ?? null);
        unset($validated['students']);

        $session = DB::transaction(function () use ($validated, $students) {
            if ($validated['active']) {
                Session::where('active', true)->update(['active' => false]);
            }

            $session = Session::create($validated);

            if ($students->isNotEmpty()) {
                $this->createStudents($session, $students);
            }

            return $session;
        });

        return redirect()
            ->route('sessions.users', $session)
            ->with('success', 'Сесію створено. Додано учнів: ' . $students->count() . '.');
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
        $session->load(['users.team.element', 'users.desiredTeam']);
        $users = $session->users
            ->sortBy('team_id')
            ->values();

        return view('sessions.users', [
            'session' => $session,
            'users' => $users,
            'teams' => $this->availableTeams($session),
        ]);
    }

    public function addUser(Request $request, Session $session)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'desired_team_id' => 'nullable|integer|exists:teams,id',
        ]);

        $this->createStudent($session, $validated['name'], $validated['desired_team_id'] ?? null);

        return redirect()
            ->route('sessions.users', $session)
            ->with('success', 'Учня додано до сесії.');
    }

    private function createStudents(Session $session, Collection $students): void
    {
        foreach ($students as $student) {
            $this->createStudent($session, $student['name'], $student['desired_team_id']);
        }
    }

    private function createStudent(Session $session, string $name, ?int $desiredTeamId): User
    {
        $liceum = Liceum::orderBy('id')->first();

        if (!$liceum) {
            throw ValidationException::withMessages([
                'students' => 'Спочатку додайте хоча б один ліцей у систему.',
            ]);
        }

        return User::create([
            'name' => trim($name),
            'session_id' => $session->id,
            'phone_number' => self::DEFAULT_PHONE_NUMBER,
            'date_of_birth' => self::DEFAULT_DATE_OF_BIRTH,
            'liceum_id' => $liceum->id,
            'team_id' => null,
            'desired_team_id' => $desiredTeamId,
            'gender' => self::DEFAULT_GENDER,
            'pin_code' => $this->generateUniquePinCode(),
        ]);
    }

    private function parseStudents(?string $rawStudents): Collection
    {
        $lines = collect(preg_split('/\R/u', (string) $rawStudents))
            ->map(fn (?string $line) => trim((string) $line))
            ->filter();

        return $lines->map(function (string $line, int $index) {
            if (!preg_match('/^(.+?)\s*[-–—]\s*(\d+)$/u', $line, $matches)) {
                throw ValidationException::withMessages([
                    'students' => 'Рядок ' . ($index + 1) . ' має бути у форматі: Іванов Іван - 5',
                ]);
            }

            $name = trim($matches[1]);
            $desiredTeamId = (int) $matches[2];

            if ($name === '') {
                throw ValidationException::withMessages([
                    'students' => 'У рядку ' . ($index + 1) . ' не вказано імʼя учня.',
                ]);
            }

            if (!Team::whereKey($desiredTeamId)->exists()) {
                throw ValidationException::withMessages([
                    'students' => 'У рядку ' . ($index + 1) . ' команда з ID ' . $desiredTeamId . ' не знайдена.',
                ]);
            }

            return [
                'name' => $name,
                'desired_team_id' => $desiredTeamId,
            ];
        })->values();
    }

    private function generateUniquePinCode(): string
    {
        do {
            $pinCode = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('pin_code', $pinCode)->exists());

        return $pinCode;
    }

    private function availableTeams(?Session $session = null): Collection
    {
        $query = Team::orderBy('id');

        if ($session && DB::getSchemaBuilder()->hasColumn('teams', 'session_id')) {
            $query->where(function ($query) use ($session) {
                $query->where('session_id', $session->id)
                    ->orWhereNull('session_id');
            });
        }

        return $query->get();
    }
}
