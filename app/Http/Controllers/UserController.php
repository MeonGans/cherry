<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::whereHas('session', function($query) {
            $query->where('active', true);
        })->orderBy('team_id')->get();

        return view('list', compact('users'));
    }

    public function random()
    {
        // Отримати користувачів, у яких сесія активна
        $users = User::whereHas('session', function($query) {
            $query->where('active', true);
        })->inRandomOrder()->get();

        return view('list', compact('users'));
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
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'session_id' => 'sometimes|required|exists:sessions,id',
            'phone_number' => 'sometimes|required|string|max:15',
            'date_of_birth' => 'sometimes|required|date',
            'liceum_id' => 'sometimes|required|exists:liceums,id',
            'team_id' => 'sometimes|exists:teams,id',
            'gender' => 'sometimes|required|in:male,female',
            'pin_code' => 'nullable|string|max:255|unique:users,pin_code,' . $user->id
        ]);

        $user->update($request->all());
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
