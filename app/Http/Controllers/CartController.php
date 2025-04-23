<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = DB::table('carts')
            ->join('products', 'carts.product_id', '=', 'products.product_id') // or 'products.id' if you're using default IDs
            ->where('carts.user_id', Auth::id()) // filter by user_id
            ->select(
                'carts.id as cart_id',
                'products.name',
                'products.price',
                'products.images',
                'carts.quantity'
            )
            ->get();
    
        return view('cart.index', compact('cartItems'));
    }
    
    public function add(Request $request, $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $cartItem = Cart::where('user_id', Auth::id())->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
                'quantity' => $request->quantity,
            ]);
        }

        // Update session cart count
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');
        session(['cart_count' => $cartCount]);

        return back()->with('success', 'Product added to cart!');
    }

    public function remove(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please login to continue.');
        }

        $request->validate([
            'cart_id' => 'required|exists:carts,id',
        ]);

        $cartItem = Cart::where('id', $request->cart_id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $cartItem->delete();

        session(['cart_count' => Cart::where('user_id', Auth::id())->sum('quantity')]);

        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please login to continue.');
        }

        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('id', $request->cart_id)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        // Update cart count in session
        session(['cart_count' => Cart::where('user_id', Auth::id())->sum('quantity')]);

        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

}
