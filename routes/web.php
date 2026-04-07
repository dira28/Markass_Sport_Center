<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

// =====================
// HALAMAN USER
// =====================
Route::get('/', function () {
    return view('user.pages.dashboard');
});

Route::get('/dashboard', function () {
    return view('user.pages.dashboard');
});

Route::get('/booking', function () {
    return view('user.pages.booking');
});

Route::get('/harga', function () {
    return view('user.pages.harga'); // pastikan file ada
});

// =====================
// AUTH
// =====================
Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', function () {
    return view('auth.register');
});

Route::post('/register', [RegisterController::class, 'register']);

// =====================
// ADMIN
// =====================
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {

        if (!session('token') || session('role') !== 'admin') {
            return redirect('/login');
        }

        return view('admin.pages.dashboard');
    })->name('admin.dashboard');

});