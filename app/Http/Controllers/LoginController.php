<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $apiUrl = config('services.api.url', env('API_URL'));

        try {
            $response = Http::acceptJson()->post($apiUrl . '/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $result = $response->json();

            if (!empty($result['success'])) {
                $user = $result['data'];

                session([
                    'token' => $user['token'],
                    'role' => $user['role'],
                    'user' => $user,
                ]);

                return $user['role'] === 'admin'
                    ? redirect()->route('admin.dashboard')
                    : redirect('/dashboard');
            }

            return back()->with(
                'error',
                $result['message'] ?? 'Login failed.'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Server error.');
        }
    }
}