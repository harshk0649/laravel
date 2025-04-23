<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Import Hash facade
use App\Models\User;
use App\Models\File; // Import the File model
use Illuminate\Support\Facades\Storage; // Import Storage facade
use Illuminate\Http\Response; // Import Response facade
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Support\Facades\Auth; // Import Auth facade

use App\Models\Brand;

class AdminController extends Controller
{

    public function dashboard() {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized access'); // Block access if not Admin
        }
    
        $users = User::where('id', '!=', Auth::id()) 
                     ->where('role', '!=', 'super_admin') 
                     ->get(); 
    
        $adminName = Auth::user()->name; 
    
        return view('dashboard.admin', compact('users', 'adminName'));
    }
    
    
    
    public function listUsers()
    {
        if(Auth::user()->role == 'admin') {
            $users = User::where('role', '!=', 'super_admin')->where('id', '!=', Auth::id())->get(); // Fetch all users except super_admin and the authenticated admin
            $adminName = Auth::user()->name; // Get the authenticated admin's name
            $files = File::all(); // Fetch all files
            return view('dashboard.admin', compact('users', 'adminName', 'files')); // Return the admin view with users, admin name, and files
        } else {
            return redirect()->route('user.dashboard')->with('error', 'Unauthorized Access');
        }
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('auth.edit', compact('user')); // Return the edit view with user data
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.dashboard')->with('success', 'User deleted successfully.');
    }


    public function search(Request $request)
{
    $search = $request->get('search');
    $adminName = Auth::user()->name; // Get the authenticated admin's name

    $query = User::where('role', '!=', 'super_admin')->where('id', '!=', Auth::id());

    if (!empty($search)) {
        $query->where('name', 'like', "%$search%");
    }

    $users = $query->get(); // Fetch users based on search condition

    return view('dashboard.admin', compact('users', 'adminName'));
}

}
