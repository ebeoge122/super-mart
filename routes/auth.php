<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register.get');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.get');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

