<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function newBrand()
    {
        $userRole = Auth::user()->role;
if ($userRole !== 'super_admin' && $userRole !== 'admin') {
    abort(403, 'Unauthorized access');
}

        return view('brands.create');
    }
    public function index()
    {

        $userRole = Auth::user()->role;
        if ($userRole !== 'super_admin' && $userRole !== 'admin') {
            abort(403, 'Unauthorized access');
        }
        $brands = Brand::all();
        return view('brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate logo file type and size
            'contact_mail' => 'required|email',
            'brand_web' => 'nullable|url',
        ]);
    
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }
    
        $brand = Brand::create([
            'name' => $request->name,
            'logo' => $logoPath,  // Store the correct logo path
            'contact_mail' => $request->contact_mail,
            'brand_web' => $request->brand_web,
            'joindate' => now(),  // Use the current date and time for joindate
        ]);
            return redirect()->route('brands.index')->with('success', 'Brand added successfully!');
    }

    public function edit($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);
        return view('brands.edit', compact('brand'));
    }
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_mail' => 'required|email',
            'brand_web' => 'nullable|url',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',  // Optional logo update
        ]);
        if ($request->hasFile('logo')) {
            if ($brand->logo && file_exists(public_path('storage/' . $brand->logo))) {
                unlink(public_path('storage/' . $brand->logo));  // Delete the old logo
            }
            $brand_logo = $request->file('logo')->store('logos', 'public');
        } else {
            $brand_logo = $brand->logo;
        }
        $brand->update([
            'name' => $request->name,
            'contact_mail' => $request->contact_mail,
            'brand_web' => $request->brand_web,
            'logo' => $brand_logo, 
        ]);
        return redirect()->route('brands.index')->with('success', 'Brand updated successfully!');
    }
    public function destroy($brand_id)
    {
        $brand = Brand::findOrFail($brand_id);

        if ($brand->logo && file_exists(public_path('storage/' . $brand->logo))) {
            unlink(public_path('storage/' . $brand->logo));  // Delete the logo file
        }

        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Brand deleted successfully');
    }
}
