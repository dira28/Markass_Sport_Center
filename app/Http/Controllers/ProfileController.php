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

        $apiUrl = config('services.api.url', env('API_URL'));

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get($apiUrl . '/api/auth/profile');

            $result = $response->json();

            if (!empty($result['success'])) {
                session(['user' => $result['data']]);

                return view('user.pages.profile');
            }

            return redirect()
                ->route('login')
                ->with('error', $result['message'] ?? 'Failed to get profile.');
        } catch (\Exception $e) {
            // Optional: \Log::error($e->getMessage());

            return redirect()
                ->route('login')
                ->with('error', 'Server error.');
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