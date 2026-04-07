<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

// =====================
// HALAMAN USER
// =====================
Route::get('/', function () {
    return view('user.pages.dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return view('user.pages.dashboard');
})->name('user.dashboard');

Route::get('/booking', function () {
    return view('user.pages.booking');
})->name('user.booking');

Route::get('/harga', function () {
    return view('user.pages.harga');
})->name('user.harga');

// =====================
// AUTH
// =====================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register']);

// =====================
// ADMIN
// =====================
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {

        if (!session('token') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        return view('admin.pages.dashboard');

    })->name('admin.dashboard');

Route::get('/booking', [BookingController::class, 'index'])
    ->name('admin.booking');
});
