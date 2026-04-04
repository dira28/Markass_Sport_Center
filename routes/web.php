<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/booking', function () {
    return view('booking');
});


Route::get('/login', function () {
    return view('auth.login');
});

Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', function () {
    return view('auth.register');
});

Route::get('/register', function () {
    return view('auth.register');
});
Route::post('/register', [RegisterController::class, 'register']);

// dashboard admin (sementara)
Route::get('/admin-dashboard', function () {
    return "INI DASHBOARD ADMIN (BELUM DIBUAT)";
});