<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;

class HomeController extends Controller 
{
    public function index()
    {
        $products = Product::with('brands', 'categories')->latest()->get(); // Eager load the related data
        $brands = Brand::all(); // You can still load all brands separately if needed
        $categories = Category::all(); // If needed for category-related operations

        // Fetch random products for the carousel
        $carouselProducts = Product::inRandomOrder()->limit(5)->get()->map(function ($product) {
            return (object)[
                'image' => is_array($product->images) ? $product->images[0] : json_decode($product->images)[0]
            ];
        });

        // Pass the products, brands, and carousel products to the view
        return view('ecom.home', compact('products', 'brands', 'carouselProducts', 'categories'));
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
    
        $products = Product::where('name', 'like', "%$query%")
                    ->orWhere('description', 'like', "%$query%")
                    ->orWhereHas('brands', fn($q) => $q->where('name', 'like', "%$query%"))
                    ->orWhereHas('categories', fn($q) => $q->where('category_name', 'like', "%$query%"))
                    ->with('brands', 'categories')
                    ->get();    
    
        $brands = Brand::all();
        $categories = Category::all();
        
        $carouselProducts = Product::inRandomOrder()->limit(5)->get()->map(function ($product) {
            return (object)[
                'image' => is_array($product->images) ? $product->images[0] : json_decode($product->images)[0]
            ];
        });
    
        return view('ecom.home', compact('products', 'brands', 'categories', 'carouselProducts'));
    }
    
}
