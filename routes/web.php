<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HistoryBookingController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\AdminDashboardController;

// =====================
// HALAMAN USER
// =====================
Route::get('/', function () {
    return view('user.pages.dashboard');
})->name('home');

Route::get('/dashboard', function () {
    return view('user.pages.dashboard');
})->name('user.dashboard');

Route::get('/booking', [BookingController::class, 'index']);
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

Route::get('/tentang', function () {
    return view('user.pages.tentang');
})->name('user.tentang');

Route::get('/my-bookings', [UserBookingController::class, 'index'])
    ->name('user.bookings');

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

Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Route::get('/logout', [ProfileController::class, 'logout'])->name('logout');

// =====================
// ADMIN
// =====================
Route::prefix('admin')->group(function () {

    // 🔥 DASHBOARD (PAKE CONTROLLER)
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // 🔥 BOOKING
    Route::get('/booking', [HistoryBookingController::class, 'index'])
        ->name('admin.booking');

    // 🔥 LAPANGAN CRUD
    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');
    Route::post('/lapangan/update/{id}', [LapanganController::class, 'update'])->name('lapangan.update');
    Route::get('/lapangan/delete/{id}', [LapanganController::class, 'destroy'])->name('lapangan.delete');

});