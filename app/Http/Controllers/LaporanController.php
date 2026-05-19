<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect('/login');
        }

        // =========================
        // FILTER DATE (AMAN + FULL DAY)
        // =========================
        $fromDate = Carbon::parse($request->get('from_date', Carbon::now()->startOfMonth()))
            ->startOfDay()
            ->format('Y-m-d H:i:s');

        $toDate = Carbon::parse($request->get('to_date', Carbon::now()->endOfMonth()))
            ->endOfDay()
            ->format('Y-m-d H:i:s');

        $bookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal_gte' => $fromDate,
                    'tanggal_lte' => $toDate,
                ]);

            if ($response->successful()) {
                $bookings = collect($response->json()['data'] ?? []);
            }

        } catch (\Exception $e) {
            $bookings = collect();
        }
        
        $bookings = $bookings->filter(function ($item) use ($fromDate, $toDate) {
            $tanggal = Carbon::parse($item['tanggal'] ?? $item['created_at']);

            return $tanggal->between(
                Carbon::parse($fromDate),
                Carbon::parse($toDate)
            );
        });

        // =========================
        // TOTAL CALCULATION
        // =========================
        $totalBooking = $bookings->count();

        $totalRevenue = $bookings->sum(function ($item) {
            return $item['total_harga'] ?? 0;
        });

        return view('admin.pages.laporan', compact(
            'bookings',
            'totalBooking',
            'totalRevenue',
            'fromDate',
            'toDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $token = session('token');

        $fromDate = $request->get('from_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $bookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal_gte' => $fromDate,
                    'tanggal_lte' => $toDate,
                ]);

            if ($response->successful()) {
                $bookings = collect($response->json()['data'] ?? []);
            }

        } catch (\Exception $e) {
            $bookings = collect();
        }

        $totalRevenue = $bookings->sum('total_harga');

        $pdf = Pdf::loadView('admin.reports.booking-pdf', compact(
            'bookings',
            'fromDate',
            'toDate',
            'totalRevenue'
        ));

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}