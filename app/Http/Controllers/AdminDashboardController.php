<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!session('token') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $token = session('token');
        $includeAll = config('app.kpi_include_all', true);

        // Inisialisasi Data
        $revenueToday = 0;
        $totalBookingToday = 0;
        $totalBooking = 0;
        $latestBookings = [];
        $totalRevenue = 0;
        $totalUser = 0;
        $averagePerDay = 0;

        $daily = [];
        $monthly = [];
        $yearly = [];

        try {
            // 1. GET MAIN BOOKINGS DATA
            $resBooking = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($resBooking->successful()) {
                $bookings = collect($resBooking->json()['data'] ?? []);

                // Filter status pembayaran
                $filteredBookings = $includeAll
                    ? $bookings
                    : $bookings->filter(fn($b) => ($b['status_pembayaran'] ?? '') === 'confirmed');

                // Latest 5 Bookings
                $latestBookings = $filteredBookings->take(5)->values()->toArray();

                // KPI TOTAL KESELURUHAN
                $totalBooking = $filteredBookings->count();
                $totalUser = $filteredBookings->unique('id_user')->count();
                $totalRevenue = $filteredBookings->sum('total_harga');

                // AVERAGE PER DAY
                $days = $filteredBookings->groupBy(fn($b) => Carbon::parse($b['tanggal'])->format('Y-m-d'))->keys()->count();
                $averagePerDay = $days > 0 ? $totalRevenue / $days : 0;

                // --- CARI TANGGAL TRANSAKSI TERAKHIR (LATEST DATE) ---
                $latestDate = $filteredBookings->max('tanggal'); // Mengambil tanggal terbaru di database

                if ($latestDate) {
                    $todayBookings = $filteredBookings->filter(fn($b) => $b['tanggal'] === $latestDate);
                    $revenueToday = $todayBookings->sum('total_harga');
                    $totalBookingToday = $todayBookings->count();
                } else {
                    $revenueToday = 0;
                    $totalBookingToday = 0;
                }

                // OLAH DATA UNTUK GRAFIK
                $filteredBookings->each(function ($b) use (&$daily, &$monthly, &$yearly) {
                    $tanggal = Carbon::parse($b['tanggal']);
                    $day = $tanggal->format('d M');
                    $month = $tanggal->format('M Y');
                    $year = $tanggal->format('Y');

                    $daily[$day] = ($daily[$day] ?? 0) + $b['total_harga'];
                    $monthly[$month] = ($monthly[$month] ?? 0) + $b['total_harga'];
                    $yearly[$year] = ($yearly[$year] ?? 0) + $b['total_harga'];
                });
            }

            // SUSUN CHART DATA
            $chartData = [
                'day' => [
                    'labels' => array_values(array_keys($daily)),
                    'data' => array_values($daily)
                ],
                'month' => [
                    'labels' => array_values(array_keys($monthly)),
                    'data' => array_values($monthly)
                ],
                'year' => [
                    'labels' => array_values(array_keys($yearly)),
                    'data' => array_values($yearly)
                ]
            ];

        } catch (\Exception $e) {
            $chartData = [
                'day' => ['labels' => [], 'data' => []],
                'month' => ['labels' => [], 'data' => []],
                'year' => ['labels' => [], 'data' => []]
            ];
        }

        return view('admin.pages.dashboard', compact(
            'revenueToday',
            'totalBookingToday',
            'totalBooking',
            'latestBookings',
            'chartData',
            'totalRevenue',
            'totalUser',
            'averagePerDay'
        ));
    }
}