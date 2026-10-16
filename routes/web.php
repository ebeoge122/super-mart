<?php

use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Models\User;


use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ImportProductController;

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');



Route::get('/allcategories', [CategoryController::class, 'display'])->name('allcategories');

Route::get('/categories', function () {
    return redirect()->route('home');
})->name('categories.index');

use App\Http\Controllers\CartController;
use App\Http\Controllers\Auth\LoginController;

Route::post('/cart/add/{item}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::delete('/cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

require __DIR__ . '/auth.php';

Route::get('/account', function () {
    $userId = session('user_id');

    if ($userId) {
        $userName = session('user_name');
        $userEmail = session('user_email');

        if ($userName) {
            $user = (object) [
                'id' => $userId,
                'name' => $userName,
                'email' => $userEmail ?? '',
                'created_at' => now(),
            ];

            return view('account-info', ['user' => $user]);
        }

        $user = User::find($userId);

        if ($user) {
            return view('account-info', ['user' => $user]);
        }
    }

    return view('login');
})->name('account');


Route::get('/admin', [ImportProductController::class, 'admin'])->name('admin');
Route::post('/admin', [ImportProductController::class, 'uploadExcel'])->name('admin.upload');

