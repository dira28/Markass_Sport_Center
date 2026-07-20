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
            // Use EXACT same endpoint + JSON structure as HistoryBookingController
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($response->failed()) {
                $bookings = [];
            } else {
                $result = $response->json();
                $bookings = $result['data'] ?? [];
            }

            // Latest 5 bookings from the same dataset used by Booking Management page
            $latestBookings = array_slice($bookings, 0, 5);

            // KPI + Chart calculations based on real API bookings.
            // Use consistent revenue basis: only count paid bookings if that field exists.
            $filteredBookings = array_values(array_filter($bookings, function ($b) {
                $paidStatus = $b['status_pembayaran'] ?? null;
                // If the API provides status_pembayaran, require it to be paid/confirmed.
                if ($paidStatus !== null) {
                    return in_array($paidStatus, ['paid', 'confirmed'], true);
                }
                return true;
            }));

            $totalRevenueTemp = 0;
            $days = [];
            $users = [];

            $todayYmd = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $todayBookingCount = 0;
            $todayRevenue = 0;

            foreach ($filteredBookings as $b) {
                // user unik (support both id_user and nested user.id depending on API shape)
                $userId = $b['id_user'] ?? ($b['user']['id'] ?? null);
                if ($userId !== null) {
                    $users[] = $userId;
                }

                $tanggal = isset($b['tanggal']) ? Carbon::parse($b['tanggal'])->timezone('Asia/Jakarta') : null;
                if (!$tanggal) {
                    continue;
                }

                $day = $tanggal->format('d M');
                $month = $tanggal->format('M Y');
                $year = $tanggal->format('Y');

                $harga = $b['total_harga'] ?? 0;

                $totalRevenueTemp += $harga;

                $ymd = $tanggal->format('Y-m-d');
                $days[$ymd] = true;

                // DAILY
                $daily[$day] = ($daily[$day] ?? 0) + $harga;

                // MONTHLY
                $monthly[$month] = ($monthly[$month] ?? 0) + $harga;

                // YEARLY
                $yearly[$year] = ($yearly[$year] ?? 0) + $harga;

                // TODAY KPI (for revenue-summary box)
                if ($ymd === $todayYmd) {
                    $todayBookingCount++;
                    $todayRevenue += $harga;
                }
            }

            $totalRevenue = $totalRevenueTemp;
            $totalUser = count(array_unique($users));

            $totalDays = count($days);
            $averagePerDay = $totalDays > 0 ? $totalRevenue / $totalDays : 0;

            // Keep existing revenue-summary and insight widgets working
            $revenueToday = $todayRevenue;
            $totalBooking = $todayBookingCount;

            // CHART DATA (preserve existing structure expected by profit-overview)
            $chartData = [
                'day' => [
                    'labels' => array_keys($daily),
                    'data' => array_values($daily),
                ],
                'month' => [
                    'labels' => array_keys($monthly),
                    'data' => array_values($monthly),
                ],
                'year' => [
                    'labels' => array_keys($yearly),
                    'data' => array_values($yearly),
                ],
            ];
        } catch (\Exception $e) {
            // Fail closed: empty datasets so Blade renders without breaking.
            $chartData = [
                'day' => ['labels' => [], 'data' => []],
                'month' => ['labels' => [], 'data' => []],
                'year' => ['labels' => [], 'data' => []],
            ];
            $latestBookings = [];
            $revenueToday = 0;
            $totalBooking = 0;
            $totalRevenue = 0;
            $totalUser = 0;
            $averagePerDay = 0;
        }

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
