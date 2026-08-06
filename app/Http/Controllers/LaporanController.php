<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Fetch filtered booking collection & summary metrics.
     */
    private function getFilteredData(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return null;
        }

        $fromDateRaw = $request->get('from_date');
        $toDateRaw = $request->get('to_date');

        $fromDate = $fromDateRaw ? Carbon::parse($fromDateRaw)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $toDateRaw ? Carbon::parse($toDateRaw)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $allBookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($response->successful()) {
                $allBookings = collect($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            $allBookings = collect();
        }

        // Strict date filtering based on created_at / booking date
        $filteredCollection = $allBookings->filter(function ($item) use ($fromDate, $toDate) {
            $date = Carbon::parse($item['created_at'] ?? $item['tanggal'] ?? now());
            return $date->between($fromDate, $toDate);
        });

        // Summary metrics calculation (unpaginated total)
        $totalBooking = $filteredCollection->count();

        $paidBooking = $filteredCollection->filter(function ($item) {
            $st = strtolower(trim((string) ($item['status_pembayaran'] ?? '')));
            return in_array($st, ['paid', 'confirmed', 'approve', 'lunas', 'berhasil'], true);
        })->count();

        $pendingBooking = $filteredCollection->filter(function ($item) {
            $st = strtolower(trim((string) ($item['status_pembayaran'] ?? '')));
            return in_array($st, ['pending', 'waiting_confirmation', 'menunggu_verifikasi'], true);
        })->count();

        $totalRevenue = $filteredCollection->filter(function ($item) {
            $st = strtolower(trim((string) ($item['status_pembayaran'] ?? '')));
            return in_array($st, ['paid', 'confirmed', 'approve', 'lunas', 'berhasil'], true);
        })->sum('total_harga');

        return [
            'rawCollection' => $filteredCollection,
            'totalBooking' => $totalBooking,
            'totalRevenue' => $totalRevenue,
            'paidBooking' => $paidBooking,
            'pendingBooking' => $pendingBooking,
            'fromDate' => $fromDate->format('Y-m-d'),
            'toDate' => $toDate->format('Y-m-d'),
        ];
    }

    /**
     * Display report page with pagination.
     */
    public function index(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        $collection = $data['rawCollection'];

        // Table-only pagination setup (10 items per page)
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

    /**
     * Export all filtered records to PDF.
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        // Send unpaginated data for PDF report
        $data['bookings'] = $data['rawCollection'];
        unset($data['rawCollection']);

        $pdf = Pdf::loadView('admin.reports.booking-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}