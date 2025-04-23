<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = Category::when($search, function ($query, $search) {
            return $query->where('category_name', 'like', "%$search%");
        })->orderBy('created_at', 'desc')->get();

        return view('items.category', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|unique:categories,category_name|max:255',
        ]);

        Category::create(['category_name' => $request->category_name]);

        return redirect()->route('items.category')->with('success', 'Category added successfully!');
    }


        public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return redirect()->route('items.category')->with('error', 'Category not found.');
        }

        $category->delete();
        return redirect()->route('items.category')->with('success', 'Category deleted successfully!');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }




    public function update(Request $request, $id)
{
    $request->validate([
        'category_name' => 'required|string|max:255',
    ]);

    $category = Category::findOrFail($id);
    $category->category_name = $request->category_name;
    $category->save();

    return redirect()->route('items.category')->with('success', 'Category updated successfully!');
}







}
