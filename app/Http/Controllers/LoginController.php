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
            'password' => 'required'
        ]);

        try {
            $response = Http::post('https://markasssportcenter.rplrus.com/api/auth/login', [
                'email' => $request->email,
                'password' => $request->password
            ]);

            $result = $response->json();

            if ($result['success']) {
                $user = $result['data'];

                session([
                    'token' => $user['token'],
                    'role' => $user['role']
                ]);

                if ($user['role'] === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else {
                    return redirect('/dashboard');
                }
            }

            return back()->with('error', $result['message'] ?? 'Login gagal');

        } catch (\Exception $e) {
            return back()->with('error', 'Server error!');
        }
    }
}