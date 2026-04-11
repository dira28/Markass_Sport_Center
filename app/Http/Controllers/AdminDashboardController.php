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

        // 🔥 TAMBAHAN (BIAR GA ERROR)
        $totalRevenue = 0;
        $totalUser = 0;
        $averagePerDay = 0;

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

                // 🔥 latest booking
                $latestBookings = array_slice($bookings, 0, 5);

                $monthly = [];
                $totalRevenueTemp = 0;
                $days = [];

                foreach ($bookings as $b) {
                    if (($b['status_pembayaran'] ?? '') !== 'paid') continue;

                    $tanggal = Carbon::parse($b['tanggal']);
                    $month = $tanggal->format('M');

                    // total revenue
                    $totalRevenueTemp += $b['total_harga'];

                    // simpan unique day
                    $days[$tanggal->format('Y-m-d')] = true;

                    // chart
                    if (!isset($monthly[$month])) {
                        $monthly[$month] = 0;
                    }

                    $monthly[$month] += $b['total_harga'];
                }

                $totalRevenue = $totalRevenueTemp;

                // average per day
                $totalDays = count($days);
                $averagePerDay = $totalDays > 0 ? $totalRevenue / $totalDays : 0;

                $chartLabels = array_keys($monthly);
                $chartValues = array_values($monthly);
            }

            // 🔥 GET TOTAL USER (optional, kalau ada API)
            try {
                $resUser = Http::withToken($token)
                    ->acceptJson()
                    ->get(env('API_URL') . '/api/user');

                if ($resUser->successful()) {
                    $users = $resUser->json()['data'] ?? [];
                    $totalUser = count($users);
                }
            } catch (\Exception $e) {}

        } catch (\Exception $e) {
            // fallback aman
        }

        return view('admin.pages.dashboard', compact(
            'revenueToday',
            'totalBooking',
            'latestBookings',
            'chartLabels',
            'chartValues',
            'totalRevenue',
            'totalUser',
            'averagePerDay'
        ));
    }
}