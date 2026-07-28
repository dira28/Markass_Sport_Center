<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login', [
            'title' => 'Login'
        ]);
    }

    // ==========================================
    // PROSES LOGIN MANUAL (PROSES MINTA OTP)
    // ==========================================
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $apiUrl = env('API_URL', 'http://127.0.0.1:5000');

            // Tembak ke API Backend Node.js
            $response = Http::post($apiUrl . '/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password
            ]);

            $result = $response->json();

            if ($response->successful()) {
                // 1. Jika Backend minta verifikasi OTP
                if (isset($result['require_otp']) && $result['require_otp']) {
                    session(['otp_email' => $result['email']]);
                    return redirect()->route('otp.view');
                }

                // 2. Jika tanpa OTP (Langsung dapet token)
                $token = $result['data']['token'] ?? $result['token'] ?? null;
                $user = $result['data'] ?? $result['user'] ?? null;

                if ($token) {
                    $role = $user['role'] ?? 'user';

                    // SIMPAN TOKEN, USER, & ROLE KE SESSION
                    session([
                        'token' => $token,
                        'role' => $role,
                        'user' => $user
                    ]);

                    // REDIRECT SESUAI ROLE
                    if ($role === 'admin') {
                        return redirect()->route('admin.dashboard');
                    }
                    return redirect()->route('user.dashboard');
                }
            }

            return back()->with('error', $result['message'] ?? 'Email atau password salah');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke server backend!');
        }
    }

    // ==========================================
    // HALAMAN & PROSES VERIFIKASI OTP
    // ==========================================

    // Tampilkan View Form Input OTP
    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', [
            'title' => 'Verifikasi OTP'
        ]);
    }

    // Process Kirim Kode OTP ke Backend
    public function processVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        try {
            $apiUrl = env('API_URL', 'http://127.0.0.1:5000');

            $response = Http::post($apiUrl . '/api/auth/verify-otp', [
                'email' => $request->email,
                'otp' => $request->otp
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['token'])) {
                session()->forget('otp_email'); // Hapus session temp email

                $user = $result['user'] ?? null;
                $role = $user['role'] ?? 'user'; // Ambil role dari response backend

                // 🔴 KUNCI PERBAIKAN: Simpan 'role' juga ke Session Laravel!
                session([
                    'token' => $result['token'],
                    'role' => $role,
                    'user' => $user
                ]);

                // 🔴 KUNCI PERBAIKAN: Cek role untuk menentukan dashboard
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('user.dashboard');
            }

            return back()->with('error', $result['message'] ?? 'Kode OTP salah atau telah kadaluarsa!');

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal terhubung ke server backend!');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['token', 'role', 'user', 'otp_email']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ==========================================
    // GOOGLE OAUTH METHODS
    // ==========================================

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $apiUrl = env('API_URL', 'http://127.0.0.1:5000');

            // Kirim data Google ke API Node.js Backend
            $response = Http::post($apiUrl . '/api/auth/google', [
                'nama' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['token'])) {
                $user = $result['user'] ?? null;
                $role = $user['role'] ?? 'user';

                session([
                    'token' => $result['token'],
                    'role' => $role,
                    'user' => $user
                ]);

                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                return redirect()->route('user.dashboard');
            }

            return redirect('/login')->with('error', $result['message'] ?? 'Gagal otentikasi via Google dari Backend');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error API: ' . $e->getMessage());
        }
    }
}