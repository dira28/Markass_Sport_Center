<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class UserBookingController extends Controller
{
    public function index(Request $request)
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
                    'bookings' => new LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url()]),
                    'error' => 'Gagal ambil data booking'
                ]);
            }

            $result = $response->json();
            $data = $result['data'] ?? [];

            // Setup Manual Pagination
            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $itemCollection = collect($data);
            $perPage = 10; // 10 data per halaman

            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            $bookings = new LengthAwarePaginator(
                $currentPageItems,
                $itemCollection->count(),
                $perPage,
                $currentPage,
                [
                    'path' => $request->url(),
                    'query' => $request->query()
                ]
            );

        } catch (\Exception $e) {
            return view('user.pages.booking-history', [
                'bookings' => new LengthAwarePaginator([], 0, 10, 1, ['path' => $request->url()]),
                'error' => $e->getMessage()
            ]);
        }

        return view('user.pages.booking-history', compact('bookings'));
    }
}