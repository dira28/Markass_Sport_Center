<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        // 🔐 Cek token login
        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login dulu!');
        }

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'status_pembayaran' => $request->status,
                    'tanggal' => $request->tanggal,
                    'id_lapangan' => $request->lapangan,
                ]);

            // ❌ Kalau API gagal
            if ($response->failed()) {
                return view('admin.pages.booking', [
                    'bookings' => [],
                    'error' => 'Gagal ambil data dari API'
                ]);
            }

            $result = $response->json();

            // 🧠 Ambil data aman
            $bookings = $result['data'] ?? [];

        } catch (\Exception $e) {
            return view('admin.pages.booking', [
                'bookings' => [],
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.pages.booking', compact('bookings'));
    }
}