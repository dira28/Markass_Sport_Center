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
            'password' => 'required|min:6'
        ]);

        try {
            $response = Http::post('http://localhost:5000/api/auth/register', [
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password
            ]);

            $result = $response->json();

            if ($result['success']) {
                return redirect('/login')->with('success', 'Register berhasil, silakan login');
            }

            return back()->with('error', $result['message'] ?? 'Register gagal');

        } catch (\Exception $e) {
            return back()->with('error', 'Server error!');
        }
    }
}