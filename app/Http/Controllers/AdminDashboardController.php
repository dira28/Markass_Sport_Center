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
        $includeAll = config('app.kpi_include_all', true); // Dev=true, Prod=false

        $revenueToday = 0;
        $totalBooking = 0;
        $latestBookings = [];
        $totalRevenue = 0;
        $totalUser = 0;
        $averagePerDay = 0;
        $daily = [];
        $monthly = [];
        $yearly = [];

        try {
            // Keep existing revenue API (today KPIs)
            $resRevenue = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/booking/revenue/daily');

            if ($resRevenue->successful()) {
                $data = $resRevenue->json()['data'];
                $revenueToday = $data['total_revenue'] ?? 0;
                $totalBookingToday = $data['total_bookings'] ?? 0;
            }

            // Main booking data for KPIs
            $resBooking = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($resBooking->successful()) {
                $bookings = collect($resBooking->json()['data'] ?? []);

                // FLEXIBLE FILTER - ONE PLACE
                $filteredBookings = $includeAll 
                    ? $bookings 
                    : $bookings->filter(fn($b) => ($b['status_pembayaran'] ?? '') === 'confirmed');

                $latestBookings = $filteredBookings->take(5)->values()->toArray();

                // TOTAL BOOKING (flexible)
                $totalBooking = $filteredBookings->count();

                // TOTAL USERS (unique id_user)
                $totalUser = $filteredBookings->unique('id_user')->count();

                // TOTAL REVENUE
                $totalRevenue = $filteredBookings->sum('total_harga');

                // AVERAGE PER DAY
                $days = $filteredBookings->groupBy(fn($b) => Carbon::parse($b['tanggal'])->format('Y-m-d'))->keys()->count();
                $averagePerDay = $days > 0 ? $totalRevenue / $days : 0;

                // CHART DATA (daily/monthly/yearly revenue)
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

            //BOOKING
            $resBooking = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/booking');

            if ($resBooking->successful()) {
                $bookings = $resBooking->json()['data'] ?? [];

                $latestBookings = array_slice($bookings, 0, 5);

                $totalRevenueTemp = 0;
                $days = [];

                foreach ($bookings as $b) {

                    //STATUS
                    if (($b['status_pembayaran'] ?? '') !== 'confirmed')
                        continue;

                    $tanggal = Carbon::parse($b['tanggal']);

                    $day = $tanggal->format('d M');
                    $month = $tanggal->format('M Y');
                    $year = $tanggal->format('Y');

                    $totalRevenueTemp += $b['total_harga'];
                    $days[$tanggal->format('Y-m-d')] = true;

                    // DAILY
                    $daily[$day] = ($daily[$day] ?? 0) + $b['total_harga'];

                    // MONTHLY
                    $monthly[$month] = ($monthly[$month] ?? 0) + $b['total_harga'];

                    // YEARLY
                    $yearly[$year] = ($yearly[$year] ?? 0) + $b['total_harga'];
                }

                $totalRevenue = $totalRevenueTemp;

                $totalDays = count($days);
                $averagePerDay = $totalDays > 0 ? $totalRevenue / $totalDays : 0;
            }

            // FINAL CHART DATA
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

        $chartData = [
            'day' => [
                'labels' => ['01 Jul', '02 Jul', '03 Jul'],
                'data' => [100000, 200000, 150000]
            ],
            'month' => [
                'labels' => ['Jan', 'Feb', 'Mar'],
                'data' => [1000000, 1500000, 1200000]
            ],
            'year' => [
                'labels' => ['2023', '2024', '2025'],
                'data' => [10000000, 15000000, 20000000]
            ]
        ];

        return view('admin.pages.dashboard', compact(
            'revenueToday',
            'totalBooking',
            'latestBookings',
            'chartData',
            'totalRevenue',
            'totalUser',
            'averagePerDay'
        ));
    }
}