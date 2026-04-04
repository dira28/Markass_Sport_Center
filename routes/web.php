<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
});

Route::get('/dashboard', function () {
    return view('pages.dashboard');
});

Route::get('/booking', function () {
    return view('pages.booking');
});
Route::get('/harga', function () {
    return view('pages.harga');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// dashboard admin (sementara)
Route::get('/admin-dashboard', function () {
    return "INI DASHBOARD ADMIN (BELUM DIBUAT)";
});