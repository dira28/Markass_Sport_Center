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

            //REVENUE HARI INI
            $resRevenue = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/booking/revenue/daily');

            if ($resRevenue->successful()) {
                $data = $resRevenue->json()['data'];

                $revenueToday = $data['total_revenue'] ?? 0;
                $totalBooking = $data['total_bookings'] ?? 0;
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