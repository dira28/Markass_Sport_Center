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

        $fromDate = $request->get('from_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::now()->format('Y-m-d'));

        $bookings = [];
        $totalBooking = 0;
        $totalRevenue = 0;

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal_gte' => $fromDate,
                    'tanggal_lte' => $toDate,
                ]);

            if ($response->successful()) {
                $bookings = $response->json()['data'] ?? [];
                $totalBooking = count($bookings);
                $totalRevenue = collect($bookings)->sum('total_harga');
            }
        } catch (\Exception $e) {
            // Handle error
        }

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
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        $response = Http::withToken($token)
            ->acceptJson()
            ->get(env('API_URL') . '/api/booking', [
                'tanggal_gte' => $fromDate,
                'tanggal_lte' => $toDate,
            ]);

        $bookings = $response->successful() ? $response->json()['data'] ?? [] : [];
        $totalRevenue = collect($bookings)->sum('total_harga');

        $pdf = Pdf::loadView('admin.reports.booking-pdf', compact('bookings', 'fromDate', 'toDate', 'totalRevenue'));
        
        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}

