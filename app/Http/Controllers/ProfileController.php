<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    public function profile()
    {
        $token = session('token');
        if (!$token) {
            return redirect()->route('login');
        }

        try {
            $apiUrl = env('API_URL', 'http://127.0.0.1:5000');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get($apiUrl . '/api/auth/profile');

            $result = $response->json();

            if (isset($result['success']) && $result['success'] === true) {
                session(['user' => $result['data']]);
                return view('user.pages.profile');
            }

            return redirect()->route('login')->with('error', $result['message'] ?? 'Failed to get profile');

        } catch (\Exception $e) {
            \Log::error('Profile API Error', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Server error!');
        }
    }

    public function logout(Request $request)
    {
        session()->forget(['token', 'user', 'role']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}