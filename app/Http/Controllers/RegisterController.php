<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $apiUrl = config('services.api.url', env('API_URL'));

        try {
            $response = Http::acceptJson()->post($apiUrl . '/api/auth/register', [
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            $result = $response->json();

            if (!empty($result['success'])) {
                return redirect('/login')->with(
                    'success',
                    'Registration successful. Please login.'
                );
            }

            return back()->with(
                'error',
                $result['message'] ?? 'Registration failed.'
            );
        } catch (\Exception $e) {
            return back()->with('error', 'Server error.');
        }
    }
}