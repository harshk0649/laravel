<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',   
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            Log::info('Attempting login for email: ' . $request->email); // Log the email being used for login
            Log::info('User role after login: ' . ($user->role ?? 'No role assigned')); // Log user role after successful login
            return redirect()->route($user->role === 'super_admin' ? 'super_admin.dashboard' : ($user->role === 'admin' ? 'admin.dashboard' : 'user.dashboard'));
        }

        return back()
    ->withErrors([
        'password' => 'The provided credentials do not match our records.',
    ])
    ->withInput($request->only('email'));

       
    }

    public function logout(Request $request)
    {
        session()->forget('role');
        Auth::logout();
        return redirect('/');
    }

}
