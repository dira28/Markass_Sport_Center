<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\BookingController;
use App\Http\Controllers\HistoryBookingController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserBookingController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LaporanController;

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
Route::get('/booking/payment/{id}', [BookingController::class, 'payment'])->name('booking.payment');
Route::post('/booking/payment/{id}/upload-bukti', [BookingController::class, 'uploadProof'])
    ->name('booking.upload-bukti');
Route::post('/booking/{id}/expire', [BookingController::class, 'expire'])->name('booking.expire');
Route::post('/booking/{id}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
Route::post('/api/booking/{id}/cancel', [BookingController::class, 'cancel']);

Route::get('/booking/fully-booked-dates', [BookingController::class, 'getFullyBookedDates']);
Route::patch('/booking/{id_booking}/confirm-payment', [BookingController::class, 'confirmPayment'])->name('booking.confirm-payment');
Route::patch('/booking/{id_booking}/reject-payment', [BookingController::class, 'rejectPayment'])->name('booking.reject-payment');
Route::get('/booking/my-bookings', [BookingController::class, 'getMyBookings'])->name('booking.my-bookings');
Route::get('/booking/status-jam', [BookingController::class, 'getStatusJam'])->name('booking.status-jam');
Route::get('/booking/slots', [BookingController::class, 'getBookedSlots'])->name('booking.slots');

Route::get('/tentang', function () {
    return view('user.pages.tentang');
})->name('user.tentang');

Route::get('/my-bookings', [UserBookingController::class, 'index'])
    ->name('user.bookings');

Route::get('/check-expired', function () {
    if (!session('token'))
        return response()->json(['status' => 'no token']);

    Http::withHeaders([
        'Authorization' => 'Bearer ' . session('token')
    ])->post('http://localhost:5000/api/booking/check-expired');

    return response()->json(['status' => 'checked']);
});

// =====================
// AUTH & GOOGLE LOGIN
// =====================
Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('otp.view');
Route::post('/verify-otp', [AuthController::class, 'processVerifyOtp'])->name('otp.verify');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'processLogin']);

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [RegisterController::class, 'register']);

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/profile', [ProfileController::class, 'profile'])->name('profile');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================
// ADMIN
// =====================
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');

    // FIX 1: Ubah controller ke BookingController dan panggil method bookingIndex
    Route::get('/booking', [BookingController::class, 'bookingIndex'])
        ->name('admin.booking');

    // FIX 2: Route laporan diarahkan ke method laporanIndex di BookingController
    Route::get('/laporan', [BookingController::class, 'laporanIndex'])
        ->name('admin.laporan');

    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])
        ->name('admin.laporan.export');

    Route::get('/profile', function () {
        return view('admin.pages.profile');
    })->name('admin.profile');

    Route::post('/logout', function () {
        session()->flush();
        return redirect('/login');
    })->name('admin.logout');

    Route::get('/lapangan', [LapanganController::class, 'index'])->name('lapangan.index');
    Route::post('/lapangan', [LapanganController::class, 'store'])->name('lapangan.store');
    Route::post('/lapangan/update/{id}', [LapanganController::class, 'update'])->name('lapangan.update');
    Route::get('/lapangan/delete/{id}', [LapanganController::class, 'destroy'])->name('lapangan.delete');

});