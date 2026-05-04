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
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Http;

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
Route::get('/booking/my-bookings', [BookingController::class, 'getMyBookings'])->name('booking.my-bookings');
Route::get('/booking/status-jam', [BookingController::class, 'getStatusJam'])->name('booking.status-jam');
Route::get('/booking/slots', [BookingController::class, 'getBookedSlots'])
    ->name('booking.slots');

Route::get('/tentang', function () {
    return view('user.pages.tentang');
})->name('user.tentang');

Route::get('/my-bookings', [UserBookingController::class, 'index'])
    ->name('user.bookings');

Route::get('/check-expired', function () {
    if (!session('token')) return response()->json(['status' => 'no token']);

    Http::withHeaders([
        'Authorization' => 'Bearer ' . session('token')
    ])->post('http://localhost:5000/api/booking/check-expired');

    return response()->json(['status' => 'checked']);
});

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

    // DASHBOARD
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // BOOKING
    Route::get('/booking', [HistoryBookingController::class, 'index'])
        ->name('admin.booking');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('admin.laporan.export');

    Route::get('/admin/profile', function () {
        return view('admin.pages.profile');
    })->name('admin.profile');

    Route::post('/logout', function () {
        session()->flush();
        return redirect('/login');
    })->name('logout');

    //LAPANGAN CRUD
    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');
    Route::post('/lapangan/update/{id}', [LapanganController::class, 'update'])->name('lapangan.update');
    Route::get('/lapangan/delete/{id}', [LapanganController::class, 'destroy'])->name('lapangan.delete');

});