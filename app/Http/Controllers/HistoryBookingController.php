<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HistoryBookingController extends Controller
{
    private function exportLaporanPdf(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->route('login')->with('error', 'Silakan login dulu!');
        }

        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($response->failed()) {
                return redirect()->route('admin.laporan')->with('error', 'Gagal ambil data dari API');
            }

            $result = $response->json();
            $bookings = collect($result['data'] ?? []);

            // approved/confirmed/booked AND payment=paid
            $reportFilteredBookings = $bookings->filter(function ($b) {
                $status = $b['status'] ?? null;
                $payment = $b['status_pembayaran'] ?? null;

                $isApprovedStatus = in_array($status, ['booked', 'confirmed', 'approved'], true);
                $isPaidPayment = in_array($payment, ['paid'], true);

                return $isApprovedStatus && $isPaidPayment;
            });

            if ($tanggalAwal) {
                $reportFilteredBookings = $reportFilteredBookings->filter(function ($b) use ($tanggalAwal) {
                    $tanggal = $b['tanggal'] ?? null;
                    if (!$tanggal) return false;
                    return \Carbon\Carbon::parse($tanggal)->toDateString() >= \Carbon\Carbon::parse($tanggalAwal)->toDateString();
                });
            }

            if ($tanggalAkhir) {
                $reportFilteredBookings = $reportFilteredBookings->filter(function ($b) use ($tanggalAkhir) {
                    $tanggal = $b['tanggal'] ?? null;
                    if (!$tanggal) return false;
                    return \Carbon\Carbon::parse($tanggal)->toDateString() <= \Carbon\Carbon::parse($tanggalAkhir)->toDateString();
                });
            }

            // newest first
            $sortedBookings = $reportFilteredBookings->sortByDesc(function ($b) {
                $tanggal = $b['tanggal'] ?? null;
                return $tanggal ? \Carbon\Carbon::parse($tanggal)->timestamp : 0;
            })->values();

            $revenueToday = $sortedBookings->sum('total_harga');
            $totalBooking = $sortedBookings->count();

            // Use dompdf wrapper
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.pages.laporan-pdf', [
                'bookings' => $sortedBookings,
                'revenueToday' => $revenueToday,
                'totalBooking' => $totalBooking,
                'tanggalAwal' => $tanggalAwal,
                'tanggalAkhir' => $tanggalAkhir,
                'exportDate' => \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d M Y H:i'),
            ])->setPaper('a4', 'portrait');

            $fileName = 'laporan-booking-' . (\Carbon\Carbon::now('Asia/Jakarta')->format('Y-m-d_His')) . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return redirect()->route('admin.laporan')->with('error', $e->getMessage());
        }
    }

    // =========================
    // HALAMAN BOOKING ADMIN
    // =========================
    public function index(Request $request)
    {
        $token = session('token');

        // Cek token login
        if (!$token) {
            return redirect()->route('login')
                ->with('error', 'Silakan login dulu!');
        }

        try {

            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'status_pembayaran' => $request->status,
                    'tanggal' => $request->tanggal,
                    'id_lapangan' => $request->lapangan,
                ]);

            //  Kalau API gagal
            if ($response->failed()) {

                return view('admin.pages.booking', [
                    'bookings' => [],
                    'error' => 'Gagal ambil data dari API'
                ]);
            }

            $result = $response->json();

            $bookings = $result['data'] ?? [];

        } catch (\Exception $e) {

            return view('admin.pages.booking', [
                'bookings' => [],
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.pages.booking', compact('bookings'));
    }

    // =========================
    // HALAMAN LAPORAN ADMIN
    // =========================
    public function laporan(Request $request)
    {
        // If this request is for PDF export, generate PDF (all filtered rows).
        if ($request->filled('export') && $request->input('export') === 'pdf') {
            return $this->exportLaporanPdf($request);
        }

        $token = session('token');


        // Cek token login
        if (!$token) {
            return redirect()->route('login')
                ->with('error', 'Silakan login dulu!');
        }

        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            //  Kalau API gagal
            if ($response->failed()) {
                return view('admin.pages.laporan', [
                    'bookings' => collect(),
                    'paginator' => null,
                    'revenueToday' => 0,
                    'totalBooking' => 0,
                    'tanggalAwal' => $tanggalAwal,
                    'tanggalAkhir' => $tanggalAkhir,
                    'error' => 'Gagal ambil data dari API'
                ]);
            }

            $result = $response->json();
            $bookings = collect($result['data'] ?? []);

            // =========================
            // VALID DATA FILTER (for table + stats + export)
            // =========================
            // approved/confirmed/booked AND payment=paid
            $reportFilteredBookings = $bookings->filter(function ($b) {
                $status = $b['status'] ?? null;
                $payment = $b['status_pembayaran'] ?? null;

                $isApprovedStatus = in_array($status, ['booked', 'confirmed', 'approved'], true);
                $isPaidPayment = in_array($payment, ['paid'], true);

                return $isApprovedStatus && $isPaidPayment;
            });

            // =========================
            // DATE FILTER (keeps pagination + export consistent)
            // =========================
            if ($tanggalAwal) {
                $reportFilteredBookings = $reportFilteredBookings->filter(function ($b) use ($tanggalAwal) {
                    $tanggal = $b['tanggal'] ?? null;
                    if (!$tanggal) return false;
                    return \Carbon\Carbon::parse($tanggal)->toDateString() >= \Carbon\Carbon::parse($tanggalAwal)->toDateString();
                });
            }

            if ($tanggalAkhir) {
                $reportFilteredBookings = $reportFilteredBookings->filter(function ($b) use ($tanggalAkhir) {
                    $tanggal = $b['tanggal'] ?? null;
                    if (!$tanggal) return false;
                    return \Carbon\Carbon::parse($tanggal)->toDateString() <= \Carbon\Carbon::parse($tanggalAkhir)->toDateString();
                });
            }


            // =========================
            // SORT: newest first
            // =========================
            $sortedBookings = $reportFilteredBookings->sortByDesc(function ($b) {
                $tanggal = $b['tanggal'] ?? null;
                return $tanggal ? \Carbon\Carbon::parse($tanggal)->timestamp : 0;
            })->values();

            // =========================
            // STATS (from same filtered dataset)
            // =========================
            $revenueToday = $sortedBookings->sum('total_harga');
            $totalBooking = $sortedBookings->count();

            // =========================
            // PAGINATION (15 per page)
            // =========================
            $perPage = 15;
            $currentPage = (int) ($request->input('page') ?: 1);

            $total = $sortedBookings->count();
            $offset = ($currentPage - 1) * $perPage;

            $pageItems = $sortedBookings->slice($offset, $perPage)->values();

            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $pageItems,
                $total,
                $perPage,
                $currentPage,
                [
                    'path' => \Illuminate\Support\Facades\URL::current(),
                    'query' => $request->except('page'),
                ]
            );

        } catch (\Exception $e) {
            return view('admin.pages.laporan', [
                'bookings' => collect(),
                'paginator' => null,
                'revenueToday' => 0,
                'totalBooking' => 0,
                'tanggalAwal' => $tanggalAwal,
                'tanggalAkhir' => $tanggalAkhir,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.pages.laporan', [
            'bookings' => $sortedBookings ?? collect(),
            'paginator' => $paginator ?? null,
            'revenueToday' => $revenueToday ?? 0,
            'totalBooking' => $totalBooking ?? 0,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
        ]);
    }
}