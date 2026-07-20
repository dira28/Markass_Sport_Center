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
            return redirect()->route('login')->with('error', 'Token tidak ditemukan');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('https://markasssportcenter.rplrus.com/api/auth/profile');

        $result = $response->json();

        \Log::info('PROFILE RESPONSE', $result);

        if (!empty($result['success']) && $result['success'] === true) {
            $user = $result['data'];
            return view('user.pages.profile', compact('user'));
        }

        return redirect()->route('login')->with('error', 'User tidak ditemukan / token invalid');
    }
    
    public function logout()
    {
        session()->forget(['token', 'role']);
        return redirect()->route('home');
    }
}