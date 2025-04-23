<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\File; // Import the File model
use App\Models\Product; // Import the Product model
use Illuminate\Support\Facades\Storage; // Import Storage facade

class UserController extends Controller
{
    public function index()
    {
        // Fetch all users from the database


        $users = User::all();
        return view('users.index', compact('users')); // Return the view with users data
    }
    public function showLoginForm()
    {
        return view('auth.login'); // Updated to reflect the new path for the login view
    }

    public function showRegistrationForm()
    {
        return view('auth.register'); // Updated to reflect the new path for the registration view
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $request->validate([
            'role' => 'required|string|max:255',
        ]);

        User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]); // Properly close the create method

        return redirect()->route($request->role === 'super-admin' ? 'admin.dashboard' : ($request->role === 'admin' ? 'admin.dashboard' : 'user.dashboard'))->with('success', 'Registration successful. Please log in.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/'); // Redirect to the authentication page after logout
    }

    

    public function dashboard(Request $request) {
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }
    
        $user = Auth::user();
    
        // Check user role and redirect accordingly
        if ($user->role === 'super_admin') {
            return redirect()->route('super_admin.dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            $files = File::where('user_id', $user->id)->get();
            $products = Product::all(); // Fetch all products from the database
            return view('dashboard.user', compact('files', 'user', 'products')); // Load user dashboard
        }
    }
    

};
