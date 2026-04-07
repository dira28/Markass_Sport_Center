<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 🔐 cek admin
        if (!session('token') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $token = session('token');

        $revenueToday = 0;
        $totalBooking = 0;
        $latestBookings = [];
        $chartLabels = [];
        $chartValues = [];

        try {
            // 🔥 REVENUE HARI INI
            $resRevenue = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking/revenue/daily');

            if ($resRevenue->successful()) {
                $data = $resRevenue->json()['data'];

                $revenueToday = $data['total_revenue'] ?? 0;
                $totalBooking = $data['total_bookings'] ?? 0;
            }

            // 🔥 GET ALL BOOKINGS
            $resBooking = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($resBooking->successful()) {
                $bookings = $resBooking->json()['data'] ?? [];

                // 🔥 latest booking (ambil 5)
                $latestBookings = array_slice($bookings, 0, 5);

                // 🔥 chart monthly revenue
                $monthly = [];

                foreach ($bookings as $b) {
                    if (($b['status_pembayaran'] ?? '') !== 'paid') continue;

                    $month = Carbon::parse($b['tanggal'])->format('M');

                    if (!isset($monthly[$month])) {
                        $monthly[$month] = 0;
                    }

                    $monthly[$month] += $b['total_harga'];
                }

                $chartLabels = array_keys($monthly);
                $chartValues = array_values($monthly);
            }

        } catch (\Exception $e) {
            // fallback aman
        }

        return view('admin.pages.dashboard', compact(
            'revenueToday',
            'totalBooking',
            'latestBookings',
            'chartLabels',
            'chartValues'
        ));
    }
}