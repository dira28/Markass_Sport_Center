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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token
            ])->get('http://localhost:5000/api/auth/profile');

            $result = $response->json();

            if (isset($result['success']) && $result['success'] === true) {
                session(['user' => $result['data']]);
                return view('user.pages.profile');
            }

            // If API fails, redirect to login or show error
            return redirect()->route('login')->with('error', $result['message'] ?? 'Failed to get profile');

        } catch (\Exception $e) {
            \Log::error('Profile API Error', ['error' => $e->getMessage()]);
            return redirect()->route('login')->with('error', 'Server error!');
        }
    }

    public function logout()
    {
        session()->forget(['token', 'role']);
        return redirect()->route('home');
    }
}