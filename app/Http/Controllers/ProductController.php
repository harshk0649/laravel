<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['brands', 'categories']) // Eager load brands & categories
                           ->where('user_id', Auth::id())
                           ->latest()
                           ->paginate(9); 
    
        $user = Auth::user();
        return view('dashboard.user', compact('products', 'user'));
    }
    
    

    public function create()
    {                                                                                      
        $brands = Brand::orderBy('name')->get();
        $categories = Category::orderBy('category_name')->get();
        return view('products.create', compact('brands', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,brand_id', // Single brand ID
            'category_id' => 'required|exists:categories,category_id', // Single category ID
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,unavailable',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
        
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'status' => $request->status,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'images' => json_encode($imagePaths),
            'user_id' => Auth::id(), // Use Auth facade to get the authenticated user's ID
        ]);


        DB::table('product_category')->insert([
            'product_id' => $product->product_id,
            'category_id' => $request->category_id,
        ]);
        DB::table('product_brand')->insert([
            'product_id' => $product->product_id,
            'brand_id' => $request->brand_id,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Product added successfully!');
    }

    public function show(Product $product)
{
    if ($product->user_id !== Auth::id()) {
        abort(403, 'Unauthorized access'); // Block unauthorized access
    }
    return view('products.show', compact('product'));
}


public function edit($id)
{
    $product = Product::with(['brands', 'categories'])->findOrFail($id);
    $brands = Brand::all();
    $categories = Category::all();

    $selectedBrandId = $product->brands->first()?->brand_id ?? null;
    $selectedCategoryId = $product->categories->first()?->category_id ?? null;
    

    return view('products.edit', compact('product', 'brands', 'categories', 'selectedBrandId', 'selectedCategoryId'));
}


public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric',
        'sale_price' => 'nullable|numeric',
        'status' => 'required|string',
        'quantity' => 'required|integer',
        'description' => 'required|string',
        'brand_id' => 'required|integer',
        'category_id' => 'required|integer',
        'images.*' => 'image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $product->update([
        'name' => $request->name,
        'price' => $request->price,
        'sale_price' => $request->sale_price,
        'status' => $request->status,
        'quantity' => $request->quantity,
        'description' => $request->description,
        
    ]);

    $product->brands()->sync([$request->brand_id]);
    $product->categories()->sync([$request->category_id]);

    if ($request->hasFile('images')) {
        // Delete old images if they exist
        if (!empty($product->images)) {
            $oldImages = is_array($product->images) ? $product->images : json_decode($product->images, true);
            if (is_array($oldImages)) {
                foreach ($oldImages as $oldImage) {
                    Storage::delete('public/' . $oldImage);
                }
            }
        }
        $imagePaths = [];
        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $imagePaths[] = $path;
        }

        $product->update(['images' => json_encode($imagePaths)]);
    }

    return redirect()->route('dashboard.products')->with('success', 'Product updated successfully!');
}


public function destroy(Product $product)
{
    if ($product->user_id !== Auth::id()) {
        abort(403, 'Unauthorized Access');
    }

    $product->delete();
    return redirect()->route('user.dashboard')->with('success', 'Product deleted successfully!');
}





}
