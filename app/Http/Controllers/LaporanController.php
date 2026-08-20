<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getFilteredData(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return null;
        }

        $fromDateRaw = $request->get('from_date');
        $toDateRaw = $request->get('to_date');
        // Ambil input status & paksa lowercase
        $statusFilter = strtolower(trim((string) $request->get('status', 'all')));

        $fromDate = $fromDateRaw ? Carbon::parse($fromDateRaw)->startOfDay() : null;
        $toDate = $toDateRaw ? Carbon::parse($toDateRaw)->endOfDay() : null;

        $allBookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($response->successful()) {
                $resData = $response->json();
                $rawList = $resData['data']['data'] ?? $resData['data'] ?? $resData ?? [];
                $allBookings = collect($rawList)->map(fn($item) => (array) $item);
            }
        } catch (\Exception $e) {
            $allBookings = collect();
        }

        // Helper Ekstraksi Status
        $getStatus = function ($item) {
            $raw = $item['status_pembayaran'] ?? $item['status'] ?? $item['payment_status'] ?? '';
            return str_replace([' ', '-'], '_', strtolower(trim((string) $raw)));
        };

        $paidStatuses = ['paid', 'confirmed', 'approve', 'approved', 'lunas', 'berhasil', 'success', 'settlement'];
        $pendingStatuses = ['pending', 'waiting_confirmation', 'waiting', 'menunggu_verifikasi', 'menunggu_konfirmasi', 'unpaid', 'menunggu'];
        $expiredStatuses = ['expired', 'expire', 'cancelled', 'canceled', 'failed', 'batal'];

        // 1. FILTER TANGGAL
        $filteredByDate = $allBookings->filter(function ($item) use ($fromDate, $toDate) {
            if (!$fromDate && !$toDate) {
                return true;
            }

            $rawDate = $item['created_at'] ?? $item['created_date'] ?? $item['tanggal'] ?? null;
            if (!$rawDate)
                return true;

            try {
                $d = Carbon::parse($rawDate);
                if ($fromDate && $toDate)
                    return $d->between($fromDate, $toDate);
                if ($fromDate)
                    return $d->greaterThanOrEqualTo($fromDate);
                if ($toDate)
                    return $d->lessThanOrEqualTo($toDate);
            } catch (\Exception $e) {
                return true;
            }

            return true;
        });

        // 2. HITUNG KPI (DARI HASIL FILTER TANGGAL)
        $totalBooking = $filteredByDate->count();
        $paidBooking = $filteredByDate->filter(fn($item) => in_array($getStatus($item), $paidStatuses, true))->count();
        $pendingBooking = $filteredByDate->filter(fn($item) => in_array($getStatus($item), $pendingStatuses, true))->count();
        $expiredBooking = $filteredByDate->filter(fn($item) => in_array($getStatus($item), $expiredStatuses, true))->count();

        $totalRevenue = $filteredByDate->filter(fn($item) => in_array($getStatus($item), $paidStatuses, true))
            ->sum(function ($item) {
                return (float) ($item['total_harga'] ?? $item['total'] ?? $item['harga'] ?? 0);
            });

        // 3. FILTER STATUS TABEL (FIXED!)
        $tableCollection = $filteredByDate->filter(function ($item) use ($getStatus, $statusFilter, $paidStatuses, $pendingStatuses, $expiredStatuses) {
            // Jika ALL / Kosong / Semua, langsung LOLOSKAN SEMUA DATA
            if (in_array($statusFilter, ['all', '', 'semua'], true)) {
                return true;
            }

            $st = $getStatus($item);

            if ($statusFilter === 'paid')
                return in_array($st, $paidStatuses, true);
            if ($statusFilter === 'pending')
                return in_array($st, $pendingStatuses, true);
            if ($statusFilter === 'expired')
                return in_array($st, $expiredStatuses, true);

            return true;
        });

        // 4. SORTING DATA TERBARU
        $sortedTableCollection = $tableCollection->sortByDesc(function ($item) {
            $rawCreated = $item['created_at'] ?? $item['created_date'] ?? $item['tanggal'] ?? null;
            if (!$rawCreated)
                return 0;
            try {
                return Carbon::parse($rawCreated)->timestamp;
            } catch (\Exception $e) {
                return 0;
            }
        })->values();

        return [
            'rawCollection' => $sortedTableCollection,
            'totalBooking' => $totalBooking,
            'totalRevenue' => $totalRevenue,
            'paidBooking' => $paidBooking,
            'pendingBooking' => $pendingBooking,
            'expiredBooking' => $expiredBooking,
            'fromDate' => $fromDateRaw,
            'toDate' => $toDateRaw,
        ];
    }

    public function index(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        $collection = $data['rawCollection'];

        // Paginasi Manual
        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedBookings = new LengthAwarePaginator(
            $pageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $data['bookings'] = $paginatedBookings;
        unset($data['rawCollection']);

        return view('admin.pages.laporan', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        $data['bookings'] = $data['rawCollection'];
        unset($data['rawCollection']);

        $pdf = Pdf::loadView('admin.reports.booking-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}