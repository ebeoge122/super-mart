<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withInput()->with('error', 'Invalid credentials');
        }

        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('user_email', $user->email);

        return redirect()->route('account')->with('success', 'Welcome back, ' . $user->name);
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        $request->session()->forget('user_name');
        $request->session()->forget('user_email');
        return redirect()->route('account');
    }
}

