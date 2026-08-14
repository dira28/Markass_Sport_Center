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

    // Process manual login and request OTP
    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        try {
            $apiUrl = env('API_URL');

            // Send request to Backend API
            $response = Http::post($apiUrl . '/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password
            ]);

            $result = $response->json();

            if ($response->successful()) {
                // 1. Check if backend requires OTP verification
                if (isset($result['require_otp']) && $result['require_otp']) {
                    session(['otp_email' => $result['email']]);
                    return redirect()->route('otp.view');
                }

                // 2. Direct login without OTP
                $token = $result['data']['token'] ?? $result['token'] ?? null;
                $user = $result['data'] ?? $result['user'] ?? null;

                if ($token) {
                    $role = $user['role'] ?? 'user';

                    // Save token, user, and role to session
                    session([
                        'token' => $token,
                        'role' => $role,
                        'user' => $user
                    ]);

                    // Redirect based on role
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

    // Show OTP verification form
    public function showOtpForm()
    {
        if (!session('otp_email')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', [
            'title' => 'Verifikasi OTP'
        ]);
    }

    // Process OTP verification
    public function processVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        try {
            $apiUrl = env('API_URL');

            $response = Http::post($apiUrl . '/api/auth/verify-otp', [
                'email' => $request->email,
                'otp' => $request->otp
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['token'])) {
                session()->forget('otp_email');

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

    // Google OAuth methods
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $apiUrl = env('API_URL');

            // Send Google user data to Backend API
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