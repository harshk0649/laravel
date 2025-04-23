<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Brand;
use App\Models\File;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SuperAdminController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'admin')->where('id', '!=', Auth::id())->get();
        $brands = Brand::all();
        $categories = Category::all();
        $products = Product::with(['brands', 'categories'])->get();
    
        return view('dashboard.super_admin', compact('users', 'brands', 'categories', 'products'));
    }
    
    

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('super_admin.dashboard')->with('success', 'User deleted successfully.');
    }

    public function search(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
        ]);

        $searchTerm = $request->input('search');

        $users = User::where('name', 'LIKE', '%' . $searchTerm . '%')
                     ->where('role', '!=', 'super_admin')
                     ->where('id', '!=', Auth::id())
                     ->get();

        $adminName = Auth::user()->name;
        $brands = Brand::all();

        return view('dashboard.super_admin', compact('users', 'adminName', 'brands'));
    }

    public function dashboard()
    {
        if (Auth::user()->role !== 'super_admin') {
            abort(403, 'Unauthorized access');
        }

        $users = User::where('id', '!=', Auth::id())->get();
        $adminName = Auth::user()->name;
        $brands = Brand::all();

        return view('dashboard.super_admin', compact('users', 'adminName', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:super_admin,admin,user',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('super_admin.dashboard')->with('success', 'User updated successfully.');
    }

    

public function edit($id)
{
    $user = User::findOrFail($id);
    $brands = Brand::all();
    $categories = Category::all();
    $products = Product::where('user_id', $user->id)->get(); // assuming products are linked via user_id

    return view('superedit', compact('user', 'brands', 'categories', 'products'));
}

public function addFile(Request $request, $userId)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'brand_id' => 'required|exists:brands,brand_id',
        'category_id' => 'required|exists:categories,category_id',
        'price' => 'required|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0|lt:price',
        'status' => 'required|in:available,unavailable',
        'quantity' => 'required|integer|min:1',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'description' => 'required|string|max:500',
    ]);

    $imagePaths = [];

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $imagePaths[] = $path;
        }
    }

    // Step 1: Create the product
    $product = Product::create([
        'user_id' => $userId,
        'name' => $request->name,
        'price' => $request->price,
        'sale_price' => $request->sale_price,
        'status' => $request->status,
        'quantity' => $request->quantity,
        'description' => $request->description,
        'images' => json_encode($imagePaths),
    ]);

    // Step 2: Attach to pivot Call to a member function getClientOriginalExtension() on arraytables
    $product->brands()->attach($request->brand_id);
    $product->categories()->attach($request->category_id);

    return redirect()->route('superadmin.user.edit', $userId)->with('success', 'Product added successfully.');
}


public function updateProduct(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'price' => 'required|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0',
        'quantity' => 'required|integer|min:0',
        'status' => 'required|in:available,unavailable',
    ]);

    $product = Product::findOrFail($id);
    $product->name = $request->name;
    $product->description = $request->description;
    $product->price = $request->price;
    $product->sale_price = $request->sale_price;
    $product->quantity = $request->quantity;
    $product->status = $request->status;

    // Handle image uploads
    if ($request->hasFile('images')) {
        $imagePaths = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $imagePaths[] = $path;
        }

        // Delete old images (handle string or array safely)
        if ($product->images) {
            $oldImages = is_array($product->images) ? $product->images : json_decode($product->images, true);

            foreach ($oldImages as $oldImage) {
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
        }

        $product->images = json_encode($imagePaths);
    }

    $product->save();

    return redirect()->back()->with('success', 'Product updated successfully.');
}


public function deleteFile($id)
{
    $product = Product::findOrFail($id);

    // Optionally delete images from storage
    if ($product->images && is_array($product->images)) {
        foreach ($product->images as $image) {
            Storage::delete('public/' . $image);
        }
    }

    $product->delete();

    return back()->with('success', 'Product deleted successfully!');
}


    
}
