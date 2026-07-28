<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getFilteredBookings(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return null;
        }

        // Ambil input tanggal, default ke awal & akhir bulan ini
        $fromDateRaw = $request->get('from_date');
        $toDateRaw = $request->get('to_date');

        $fromDate = $fromDateRaw ? Carbon::parse($fromDateRaw)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $toDate = $toDateRaw ? Carbon::parse($toDateRaw)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();

        $bookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal_gte' => $fromDate->format('Y-m-d H:i:s'),
                    'tanggal_lte' => $toDate->format('Y-m-d H:i:s'),
                ]);

            if ($response->successful()) {
                $bookings = collect($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            $bookings = collect();
        }

        // Filter ketat berdasarkan rentang tanggal
        $bookings = $bookings->filter(function ($item) use ($fromDate, $toDate) {
            $tanggal = Carbon::parse($item['tanggal'] ?? $item['created_at']);
            return $tanggal->between($fromDate, $toDate);
        });

        // Hitung statistik
        $totalBooking = $bookings->count();

        // Hanya hitung revenue dari booking yang lunas/dikonfirmasi
        $totalRevenue = $bookings->filter(function ($item) {
            $st = strtolower($item['status_pembayaran'] ?? '');
            return in_array($st, ['paid', 'confirmed', 'lunas', 'berhasil']);
        })->sum('total_harga');

        return [
            'bookings' => $bookings,
            'totalBooking' => $totalBooking,
            'totalRevenue' => $totalRevenue,
            'fromDate' => $fromDate->format('Y-m-d'),
            'toDate' => $toDate->format('Y-m-d'),
        ];
    }

    public function index(Request $request)
    {
        $data = $this->getFilteredBookings($request);

        if ($data === null) {
            return redirect('/login');
        }

        return view('admin.pages.laporan', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredBookings($request);

        if ($data === null) {
            return redirect('/login');
        }

        $pdf = Pdf::loadView('admin.reports.booking-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}