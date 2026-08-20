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
                $rawBookings = collect($resBooking->json()['data'] ?? [])->map(fn($item) => (array) $item);

                // Status sah dianggap lunas/bayar
                $paidStatuses = ['paid', 'confirmed', 'approve', 'approved', 'lunas', 'berhasil', 'success', 'settlement'];

                // Helper Normalisasi Status
                $getStatus = function ($item) {
                    $raw = $item['status_pembayaran'] ?? $item['status'] ?? $item['payment_status'] ?? '';
                    return str_replace([' ', '-'], '_', strtolower(trim((string) $raw)));
                };

                // Filter Khusus Transaksi Lunas untuk Revenue
                $paidBookings = $rawBookings->filter(function ($b) use ($getStatus, $paidStatuses) {
                    return in_array($getStatus($b), $paidStatuses, true);
                });

                // Get 5 latest bookings (Menampilkan semua transaksi terbaru untuk list)
                $latestBookings = $rawBookings->sortByDesc(function ($b) {
                    return $b['tanggal'] ?? $b['created_at'] ?? '';
                })->take(5)->values()->toArray();

                // Calculate Total KPIs
                $totalBooking = $rawBookings->count(); // Total seluruh order masuk
                $totalUser = $rawBookings->unique('id_user')->count();

                // HITUNG REVENUE HANYA DARI YANG PAID/LUNAS
                $totalRevenue = $paidBookings->sum('total_harga');

                // Calculate daily average revenue (Dari transaksi lunas)
                $daysCount = $paidBookings->groupBy(function ($b) {
                    $rawDate = $b['tanggal'] ?? $b['created_at'] ?? null;
                    return $rawDate ? Carbon::parse($rawDate)->format('Y-m-d') : null;
                })->filter()->count();

                $averagePerDay = $daysCount > 0 ? $totalRevenue / $daysCount : 0;

                // --- Calculate Real-time Data for Today ---
                $todayDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');

                $todayBookings = $paidBookings->filter(function ($b) use ($todayDate) {
                    $rawDate = $b['tanggal'] ?? $b['created_at'] ?? null;
                    if (empty($rawDate))
                        return false;
                    return Carbon::parse($rawDate)->setTimezone('Asia/Jakarta')->format('Y-m-d') === $todayDate;
                });

                $revenueToday = $todayBookings->sum('total_harga');
                $totalBookingToday = $todayBookings->count();

                // --- Grouping Data Category ---
                $categoryGroup = $rawBookings->groupBy(function ($b) {
                    $cat = $b['kategori']
                        ?? $b['kategori_lapangan']
                        ?? $b['nama_kategori']
                        ?? $b['jenis_lapangan']
                        ?? $b['jenis']
                        ?? $b['lapangan']['kategori']['nama'] ?? null;

                    if (is_array($cat)) {
                        $cat = $cat['nama'] ?? $cat['nama_kategori'] ?? null;
                    }

                    if (empty($cat)) {
                        $namaLapangan = strtolower(
                            $b['lapangan']['nama_lapangan']
                            ?? $b['nama_lapangan']
                            ?? ''
                        );

                        if (str_contains($namaLapangan, 'futsal')) {
                            $cat = 'Futsal';
                        } elseif (str_contains($namaLapangan, 'badminton') || str_contains($namaLapangan, 'bulutangkis')) {
                            $cat = 'Badminton';
                        } elseif (str_contains($namaLapangan, 'basket')) {
                            $cat = 'Basketball';
                        }
                    }

                    return !empty($cat) ? ucfirst(strtolower(trim($cat))) : 'Lainnya';
                });

                $categoryData = ['labels' => [], 'data' => []];
                foreach ($categoryGroup as $catName => $items) {
                    $categoryData['labels'][] = $catName;
                    $categoryData['data'][] = $items->count();
                }

                // Process Chart Datasets (Line Chart) HANYA DARI TRANSAKSI LUNAS
                $paidBookings->sortBy('tanggal')->each(function ($b) use (&$daily, &$monthly, &$yearly) {
                    $rawDate = $b['tanggal'] ?? $b['created_at'] ?? null;
                    if (empty($rawDate))
                        return;

                    $tanggal = Carbon::parse($rawDate);
                    $day = $tanggal->format('d M');
                    $month = $tanggal->format('M Y');
                    $year = $tanggal->format('Y');

                    $price = (float) ($b['total_harga'] ?? 0);

                    $daily[$day] = ($daily[$day] ?? 0) + $price;
                    $monthly[$month] = ($monthly[$month] ?? 0) + $price;
                    $yearly[$year] = ($yearly[$year] ?? 0) + $price;
                });
            }
        } catch (Exception $e) {
            // Log Error if needed
        }

        // 3. Prepare Chart Data Structure
        $chartData = [
            'day' => ['labels' => array_keys($daily), 'data' => array_values($daily)],
            'month' => ['labels' => array_keys($monthly), 'data' => array_values($monthly)],
            'year' => ['labels' => array_keys($yearly), 'data' => array_values($yearly)]
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