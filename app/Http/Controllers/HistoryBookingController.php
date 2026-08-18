<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoryBookingController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

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

            if ($response->failed()) {
                return view('user.pages.booking-history', [
                    'bookings' => new LengthAwarePaginator([], 0, 10),
                    'error' => 'Gagal ambil data dari API'
                ]);
            }

            $result = $response->json();
            $data = $result['data'] ?? [];

            // Wrap array into LengthAwarePaginator instance
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $itemCollection = collect($data);
            $perPage = 10;

            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            $bookings = new LengthAwarePaginator(
                $currentPageItems,
                $itemCollection->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

        } catch (\Exception $e) {
            return view('user.pages.booking-history', [
                'bookings' => new LengthAwarePaginator([], 0, 10),
                'error' => $e->getMessage()
            ]);
        }

        return view('user.pages.booking-history', compact('bookings'));
    }
}