<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::with(['team.element'])
            ->whereHas('session', function($query) {
            $query->where('active', true);
        })->orderBy('team_id')->get();

        return view('list', compact('users'));
    }

    public function random()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::with(['team.element'])
            ->whereHas('session', function($query) {
            $query->where('active', true);
        })->inRandomOrder()->get();

        return view('random_list', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'session_id' => 'required|exists:sessions,id',
            'phone_number' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'liceum_id' => 'required|exists:liceums,id',
            'team_id' => 'exists:teams,id',
            'gender' => 'required|in:male,female',
            'pin_code' => 'nullable|string|max:255|unique:users'
        ]);

        User::create($request->all());
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'teams' => Team::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'session_id' => 'sometimes|required|exists:sessions,id',
            'phone_number' => 'sometimes|required|string|max:15',
            'date_of_birth' => 'sometimes|required|date',
            'liceum_id' => 'sometimes|required|exists:liceums,id',
            'team_id' => 'sometimes|exists:teams,id',
            'desired_team_id' => 'nullable|integer|exists:teams,id',
            'gender' => 'sometimes|required|in:male,female',
            'pin_code' => 'nullable|string|max:255|unique:users,pin_code,' . $user->id,
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        unset($validated['image']);

        $user->update($validated);
        $this->storeImage($request, $user);

        return redirect()->route('list')->with('success', 'Учня оновлено.');
    }

    public function destroy(User $user)
    {
        $this->deleteImage($user->image_path);

        $user->delete();
        return redirect()->route('list')->with('success', 'Учня видалено.');
    }

    private function storeImage(Request $request, User $user): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $directory = public_path('images/users');
        File::ensureDirectoryExists($directory);

        $oldPath = $user->image_path;
        $file = $request->file('image');
        $filename = $user->id . '-' . Str::random(12) . '.' . $file->extension();

        $file->move($directory, $filename);

        $user->update([
            'image_path' => 'images/users/' . $filename,
        ]);

        $this->deleteImage($oldPath);
    }

    private function deleteImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $path = str_replace('\\', '/', $path);

        if (!str_starts_with($path, 'images/users/')) {
            return;
        }

        $fullPath = public_path($path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }
}
