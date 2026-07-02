<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminAuthController extends Controller
{
    protected $adminPin = '1168';

    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'pin' => 'required'
        ]);

        if ($request->input('pin') === $this->adminPin) {
            Session::put('admin_logged_in', true);
            return redirect()->route('admin.dashboard')->with('success', 'Ви увійшли в адмін-панель.');
        }

        return back()->withErrors(['pin' => 'Неправильний PIN-код.']);
    }

    public function logout()
    {
        Session::forget('admin_logged_in');
        return redirect()->route('admin.login')->with('success', 'Ви вийшли з адмін-панелі.');
    }
}
