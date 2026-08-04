<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Exception;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Session & Role Validation
        if (!session('token') || session('role') !== 'admin') {
            return redirect()->route('login');
        }

        $token = session('token');
        $includeAll = config('app.kpi_include_all', true);
        $apiUrl = config('services.api.url', env('API_URL'));

        // Initialize Default Values
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
        $categoryData = [];

        try {
            // 2. Fetch Data from API
            $resBooking = Http::withToken($token)
                ->acceptJson()
                ->get($apiUrl . '/api/booking');

            if ($resBooking->successful()) {
                $bookings = collect($resBooking->json()['data'] ?? []);

                // Filter by payment status
                $filteredBookings = $includeAll
                    ? $bookings
                    : $bookings->filter(fn($b) => ($b['status_pembayaran'] ?? '') === 'confirmed');

                // Sort bookings chronologically
                $sortedBookings = $filteredBookings->sortBy('tanggal');

                // Get 5 latest bookings
                $latestBookings = $filteredBookings->sortByDesc('tanggal')->take(5)->values()->toArray();

                // Calculate Total KPIs
                $totalBooking = $filteredBookings->count();
                $totalUser = $filteredBookings->unique('id_user')->count();
                $totalRevenue = $filteredBookings->sum('total_harga');

                // Calculate daily average revenue
                $daysCount = $filteredBookings->groupBy(fn($b) => Carbon::parse($b['tanggal'])->format('Y-m-d'))->count();
                $averagePerDay = $daysCount > 0 ? $totalRevenue / $daysCount : 0;

                // --- Calculate Real-time Data for Today ---
                $todayDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');

                $todayBookings = $filteredBookings->filter(function ($b) use ($todayDate) {
                    if (empty($b['tanggal']))
                        return false;
                    return Carbon::parse($b['tanggal'])->setTimezone('Asia/Jakarta')->format('Y-m-d') === $todayDate;
                });

                $revenueToday = $todayBookings->sum('total_harga');
                $totalBookingToday = $todayBookings->count();

                // --- Grouping Data Category (Futsal, Badminton, Basket, dll) ---
                $categoryGroup = $filteredBookings->groupBy(function ($b) {
                    // Pengecekan multi-key agar dapat membaca struktur JSON dari API secara fleksibel
                    $cat = $b['kategori']
                        ?? $b['kategori_lapangan']
                        ?? $b['nama_kategori']
                        ?? $b['jenis_lapangan']
                        ?? $b['jenis']
                        ?? $b['lapangan']['kategori']
                        ?? $b['lapangan']['kategori_lapangan']
                        ?? $b['lapangan']['nama_kategori']
                        ?? $b['lapangan']['jenis']
                        ?? null;

                    return !empty($cat) ? ucfirst(strtolower($cat)) : 'Lainnya';
                });

                $categoryData = [
                    'labels' => [],
                    'data' => []
                ];

                foreach ($categoryGroup as $catName => $items) {
                    $categoryData['labels'][] = $catName;
                    $categoryData['data'][] = $items->count();
                }

                // Process Chart Datasets (Line Chart)
                $sortedBookings->each(function ($b) use (&$daily, &$monthly, &$yearly) {
                    if (empty($b['tanggal']))
                        return;

                    $tanggal = Carbon::parse($b['tanggal']);
                    $day = $tanggal->format('d M');
                    $month = $tanggal->format('M Y');
                    $year = $tanggal->format('Y');

                    $price = $b['total_harga'] ?? 0;

                    $daily[$day] = ($daily[$day] ?? 0) + $price;
                    $monthly[$month] = ($monthly[$month] ?? 0) + $price;
                    $yearly[$year] = ($yearly[$year] ?? 0) + $price;
                });
            }
        } catch (Exception $e) {
            // Optional: \Log::error($e->getMessage());
        }

        // 3. Prepare Chart Data Structure
        $chartData = [
            'day' => [
                'labels' => array_keys($daily),
                'data' => array_values($daily)
            ],
            'month' => [
                'labels' => array_keys($monthly),
                'data' => array_values($monthly)
            ],
            'year' => [
                'labels' => array_keys($yearly),
                'data' => array_values($yearly)
            ]
        ];

        // 4. Render Dashboard View
        return view('admin.pages.dashboard', compact(
            'revenueToday',
            'totalBookingToday',
            'totalBooking',
            'latestBookings',
            'chartData',
            'totalRevenue',
            'totalUser',
            'averagePerDay',
            'categoryData'
        ));
    }
}