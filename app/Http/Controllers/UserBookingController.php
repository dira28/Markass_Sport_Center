<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserBookingController extends Controller
{
    public function index()
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Login dulu!');
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking/user/my-bookings');

            if ($response->failed()) {
                return view('user.pages.booking-history', [
                    'bookings' => [],
                    'error' => 'Gagal ambil data booking'
                ]);
            }

            $result = $response->json();
            $bookings = $result['data'] ?? [];

        } catch (\Exception $e) {
            return view('user.pages.booking-history', [
                'bookings' => [],
                'error' => $e->getMessage()
            ]);
        }

        return view('user.pages.booking-history', compact('bookings'));
    }
}