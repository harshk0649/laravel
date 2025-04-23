<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductController; 
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UpdateUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('dummy');
});
Route::get('/home', [HomeController::class, 'index']);


// ✅ Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ✅ Protected Routes (Requires Authentication)
Route::middleware(['auth'])->group(function () {
    // ✅ User Profile Update
    Route::get('/user/update', [UpdateUserController::class, 'showUpdateForm'])->name('user.update.form');
    Route::post('/user/update', [UpdateUserController::class, 'update'])->name('user.update');

    // ✅ Dashboard Routes
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/super_admin/dashboard', [SuperAdminController::class, 'dashboard'])->name('super_admin.dashboard');
    Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');

    // ✅ Admin User Management
    Route::prefix('admin/user')->name('admin.user.')->group(function () {
        Route::get('{id}/edit', [AdminController::class, 'editUser'])->name('edit');
        Route::put('{id}', [AdminController::class, 'updateUser'])->name('update');
        Route::delete('{id}', [AdminController::class, 'deleteUser'])->name('delete');
        Route::get('{id}', [AdminController::class, 'getUserDetails'])->name('details');
    });

    // ✅ Super Admin User Management
    Route::prefix('superadmin/user')->name('superadmin.user.')->group(function () {
        Route::get('{id}/edit', [SuperAdminController::class, 'edit'])->name('edit');
        Route::put('{id}', [SuperAdminController::class, 'update'])->name('update');
        Route::delete('{id}', [SuperAdminController::class, 'delete'])->name('delete');
        Route::get('{id}', [SuperAdminController::class, 'getUserDetails'])->name('details');
    });

    // ✅ Search Routes
    Route::get('/admin/search', [AdminController::class, 'search'])->name('admin.search');
    Route::get('/superadmin/search', [SuperAdminController::class, 'search'])->name('superadmin.search');

    // ✅ User & Brand Management Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
        Route::get('/brands/new', [BrandController::class, 'newBrand'])->name('brands.new');
        Route::post('/brands/store', [BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand_id}/edit', [BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand_id}', [BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand_id}', [BrandController::class, 'destroy'])->name('brands.delete');
    });


    Route::middleware(['auth'])->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('items.category');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category_id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });

    Route::middleware(['auth'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/dashboard/products', [ProductController::class, 'index'])->name('dashboard.products');
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');

    }); 
     // ✅ Super Admin product Management
Route::prefix('superadmin/product')->name('superadmin.product.')->group(function () {
    Route::post('user/{id}/add', [SuperAdminController::class, 'addFile'])->name('add');
    Route::get('{id}/edit', [SuperAdminController::class, 'editFile'])->name('edit');
    Route::delete('{id}', [SuperAdminController::class, 'deleteFile'])->name('delete');
    Route::put('{id}', [SuperAdminController::class, 'updateProduct'])->name('update');
});
Route::middleware('auth')->group(function () {
    Route::get('/search', [HomeController::class, 'search'])->name('search');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
});
});