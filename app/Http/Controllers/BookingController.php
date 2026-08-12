<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingController extends Controller
{
    // ==========================================
    // METHOD ADMIN BOOKING (DENGAN FILTER & PAGINATION)
    // ==========================================
    public function laporanIndex(Request $request)
    {
        $token = session('token');
        $error = null;

        try {
            $res = Http::withToken($token)->get(env('API_URL') . '/api/booking');

            if ($res->successful()) {
                $allBookings = $res->json()['data'] ?? [];

                // 1. FILTER SEMUA DATA DULU
                $filteredCollection = collect($allBookings)->filter(function ($item) use ($request) {
                    $match = true;

                    // A. Filter Search (ID Booking, Nama User, Nama Lapangan)
                    if ($request->filled('search')) {
                        $search = strtolower($request->search);
                        $idBooking = strtolower((string) ($item['id_booking'] ?? $item['id'] ?? ''));
                        $userName = strtolower((string) ($item['user']['name'] ?? $item['user']['nama'] ?? $item['nama_user'] ?? ''));
                        $lapangan = strtolower((string) ($item['lapangan']['nama_lapangan'] ?? $item['nama_lapangan'] ?? ''));

                        if (
                            strpos($idBooking, $search) === false &&
                            strpos($userName, $search) === false &&
                            strpos($lapangan, $search) === false
                        ) {
                            $match = false;
                        }
                    }

                    // B. Filter Single Tanggal (pencarian dari input type date tunggal)
                    if ($request->filled('tanggal')) {
                        $bookingDate = isset($item['created_at'])
                            ? Carbon::parse($item['created_at'])->timezone('Asia/Jakarta')->format('Y-m-d')
                            : ($item['tanggal'] ?? null);

                        if ($bookingDate !== $request->tanggal) {
                            $match = false;
                        }
                    }

                    // C. Filter Rentang Tanggal (dari_tanggal & sampai_tanggal)
                    if ($request->filled('dari_tanggal') && $request->filled('sampai_tanggal')) {
                        $createdDate = isset($item['created_at'])
                            ? Carbon::parse($item['created_at'])->timezone('Asia/Jakarta')->format('Y-m-d')
                            : null;

                        if ($createdDate < $request->dari_tanggal || $createdDate > $request->sampai_tanggal) {
                            $match = false;
                        }
                    }

                    // D. Filter Status Pembayaran
                    if ($request->filled('status')) {
                        $itemStatus = strtolower((string) ($item['status_pembayaran'] ?? $item['status'] ?? ''));
                        $reqStatus = strtolower($request->status);

                        // Normalisasi aliasing status agar kompatibel
                        if ($reqStatus === 'waiting_confirmation' || $reqStatus === 'menunggu_verifikasi') {
                            if (!in_array($itemStatus, ['waiting_confirmation', 'menunggu_verifikasi'], true)) {
                                $match = false;
                            }
                        } elseif ($reqStatus === 'confirmed' || $reqStatus === 'paid' || $reqStatus === 'approve') {
                            if (!in_array($itemStatus, ['confirmed', 'paid', 'approve'], true)) {
                                $match = false;
                            }
                        } else {
                            if ($itemStatus !== $reqStatus) {
                                $match = false;
                            }
                        }
                    }

                    return $match;
                });

                // 2. HITUNG STATISTIK DARI SELURUH DATA HASIL FILTER (SEBELUM PAGINASI)
                $totalBookingCount = $filteredCollection->count();

                $totalRevenue = $filteredCollection->whereIn('status_pembayaran', ['confirmed', 'paid', 'approve'])
                    ->sum('total_harga');

                $statusPaidCount = $filteredCollection->whereIn('status_pembayaran', ['confirmed', 'paid', 'approve'])
                    ->count();

                $statusPendingCount = $filteredCollection->whereIn('status_pembayaran', ['pending', 'waiting_confirmation', 'menunggu_verifikasi'])
                    ->count();

                // 3. BARU POTONG DATA KHUSUS UNTUK TABEL (PAGINASI)
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();
                $currentPageItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

                $bookings = new LengthAwarePaginator(
                    $currentPageItems,
                    $totalBookingCount,
                    $perPage,
                    $currentPage,
                    [
                        'path' => LengthAwarePaginator::resolveCurrentPath(),
                        'query' => $request->query(),
                    ]
                );

                return view('admin.pages.laporan', compact(
                    'bookings',
                    'totalBookingCount',
                    'totalRevenue',
                    'statusPaidCount',
                    'statusPendingCount',
                    'error'
                ));
            }
        } catch (\Exception $e) {
            $error = 'Server error: ' . $e->getMessage();
        }

        return view('admin.pages.laporan', compact('error'));
    }

    public function index()
    {
        $lapangan = [];

        try {
            $res = Http::get(env('API_URL') . '/api/lapangan');
            if ($res->successful()) {
                $lapangan = $res->json()['data'] ?? [];
            }
        } catch (\Exception $e) {
            // Keep empty on error
        }

        return view('user.pages.booking', compact('lapangan'));
    }

    public function store(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Silakan login dulu'
            ], 401);
        }

        try {
            $res = Http::withToken($token)
                ->acceptJson()
                ->post(env('API_URL') . '/api/booking', [
                    'id_lapangan' => $request->id_lapangan,
                    'tanggal' => $request->tanggal,
                    'jam_mulai' => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                ]);

            if ($res->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $res->json()
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $res->json()['message'] ?? 'Booking gagal'
            ], $res->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMyBookings(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json([]);
        }

        try {
            $res = Http::withToken($token)
                ->get(env('API_URL') . '/api/booking/user/my-bookings');

            return response()->json($res->successful() ? ($res->json()['data'] ?? []) : []);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getStatusJam(Request $request)
    {
        $token = session('token');
        $lapangan_id = $request->lapangan_id;
        $tanggal = $request->tanggal;

        if (!$lapangan_id || !$tanggal) {
            return response()->json([]);
        }

        try {
            if ($token) {
                Http::withToken($token)->acceptJson()->get(env('API_URL') . '/api/booking', ['status_pembayaran' => 'pending']);
                Http::withToken($token)->patch(env('API_URL') . '/api/booking/expire-pending');
            }

            $res = Http::get(env('API_URL') . '/api/lapangan/' . $lapangan_id . '/status-jam', [
                'tanggal' => $tanggal
            ]);

            return response()->json($res->successful() ? ($res->json()['data'] ?? []) : []);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function payment($id)
    {
        $token = session('token');

        if (!$token) {
            return redirect('/login');
        }

        try {
            $res = Http::withToken($token)->get(env('API_URL') . '/api/booking/' . $id);

            if ($res->successful()) {
                $booking = $res->json()['data'] ?? null;

                if ($booking) {
                    $paymentStatus = strtolower($booking['status_pembayaran'] ?? 'pending');

                    if ($paymentStatus === 'menunggu_verifikasi') {
                        $paymentStatus = 'waiting_confirmation';
                    } elseif (in_array($paymentStatus, ['approve', 'paid'], true)) {
                        $paymentStatus = 'confirmed';
                    }

                    $statusClass = match ($paymentStatus) {
                        'pending' => 'bg-warning',
                        'waiting_confirmation' => 'bg-primary',
                        'confirmed' => 'bg-success',
                        'expired' => 'bg-danger',
                        'cancelled' => 'bg-secondary',
                        default => 'bg-secondary'
                    };

                    $statusText = match ($paymentStatus) {
                        'pending' => 'Pending',
                        'waiting_confirmation' => 'Menunggu Verifikasi',
                        'confirmed' => 'Sudah Dibayar',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        default => 'Pending'
                    };

                    $booking['status_pembayaran'] = $paymentStatus;

                    return view('user.pages.payment', compact('booking', 'statusClass', 'statusText'));
                }
            }

            return redirect('/booking')->with('error', 'Booking not found');
        } catch (\Exception $e) {
            return redirect('/booking')->with('error', 'Error loading booking');
        }
    }

    public function uploadProof($id, Request $request)
    {
        $request->validate([
            'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $token = session('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Login required'], 401);
        }

        try {
            $file = $request->file('bukti');
            $path = $file->store('bukti', 'public');

            $res = Http::withToken($token)
                ->attach('bukti', file_get_contents($file->getRealPath()), basename($path), [
                    'Content-Type' => $file->getClientMimeType()
                ])
                ->post(env('API_URL') . '/api/booking/' . $id . '/upload-bukti');

            if ($res->successful()) {
                $payload = $res->json();
                $data = $payload['data'] ?? $payload;

                $latestBukti = $data['bukti_pembayaran'] ?? $data['bukti'] ?? null;
                $statusPembayaran = $data['status_pembayaran'] ?? $data['status'] ?? 'waiting_confirmation';

                $normalizedStatus = strtolower(trim((string) $statusPembayaran));
                if ($normalizedStatus === 'menunggu_verifikasi') {
                    $normalizedStatus = 'waiting_confirmation';
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Menunggu konfirmasi admin',
                    'data' => [
                        'id_booking' => $data['id_booking'] ?? $id,
                        'bukti_pembayaran' => $latestBukti,
                        'status_pembayaran' => $normalizedStatus,
                    ]
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => $res->json()['message'] ?? 'Upload failed'
            ], $res->status());

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Upload error: ' . $e->getMessage()], 500);
        }
    }

    public function confirmPayment($id_booking)
    {
        $token = session('token');

        if (!$token) {
            return redirect()->back()->with('error', 'Login required');
        }

        try {
            $res = Http::withToken($token)
                ->patch(env('API_URL') . '/api/booking/' . $id_booking . '/confirm-payment');

            if ($res->successful()) {
                return redirect()->back()->with('success', 'Pembayaran berhasil di-approve');
            }

            return redirect()->back()->with('error', $res->json()['message'] ?? 'Confirm payment failed');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function rejectPayment($id_booking)
    {
        return response()->json([
            'success' => false,
            'message' => 'Reject payment not supported'
        ], 400);
    }

    public function getBookedSlots(Request $request)
    {
        $token = session('token');
        $lapangan_id = $request->lapangan_id;

        if (!$token || !$lapangan_id) {
            return response()->json([]);
        }

        try {
            $res = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal' => $request->tanggal,
                    'id_lapangan' => $lapangan_id,
                ]);

            if ($res->failed()) {
                return response()->json([]);
            }

            $data = $res->json()['data'] ?? [];
            $blocked = [];

            foreach ($data as $booking) {
                if ($booking['id_lapangan'] != $lapangan_id) {
                    continue;
                }

                $paymentStatus = strtolower((string) ($booking['status_pembayaran'] ?? 'pending'));

                if (in_array($paymentStatus, ['approve', 'paid'], true)) {
                    $paymentStatus = 'confirmed';
                } elseif ($paymentStatus === 'menunggu_verifikasi') {
                    $paymentStatus = 'waiting_confirmation';
                }

                if ($paymentStatus === 'pending') {
                    $createdAt = Carbon::parse($booking['created_at']);
                    $now = Carbon::now('Asia/Jakarta');

                    if ($createdAt->diffInMinutes($now) >= 30) {
                        Http::withToken($token)->patch(env('API_URL') . '/api/booking/' . $booking['id_booking'], [
                            'status_pembayaran' => 'expired'
                        ]);
                        continue;
                    }
                }

                if (in_array($paymentStatus, ['expired', 'cancelled'], true)) {
                    continue;
                }

                $start = (int) substr($booking['jam_mulai'], 0, 2);
                $end = (int) substr($booking['jam_selesai'], 0, 2);

                for ($i = $start; $i < $end; $i++) {
                    $blocked[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ":00";
                }
            }

            return response()->json(array_values(array_unique($blocked)));
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getFullyBookedDates(Request $request)
    {
        $lapangan_id = $request->lapangan_id;
        if (!$lapangan_id) {
            return response()->json([]);
        }

        try {
            $res = Http::get(env('API_URL') . '/api/booking', [
                'id_lapangan' => $lapangan_id,
            ]);

            if ($res->failed()) {
                return response()->json([]);
            }

            $data = $res->json()['data'] ?? [];
            $dateSlotCount = [];
            $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');

            foreach ($data as $booking) {
                if (($booking['tanggal'] ?? '') < $today) {
                    continue;
                }

                $status = strtolower((string) ($booking['status_pembayaran'] ?? 'pending'));

                if (in_array($status, ['expired', 'cancelled'], true)) {
                    continue;
                }

                $start = (int) substr($booking['jam_mulai'], 0, 2);
                $end = (int) substr($booking['jam_selesai'], 0, 2);
                $duration = max(1, $end - $start);

                $tgl = $booking['tanggal'];
                $dateSlotCount[$tgl] = ($dateSlotCount[$tgl] ?? 0) + $duration;
            }

            $fullyBookedDates = [];
            foreach ($dateSlotCount as $tgl => $totalJam) {
                if ($totalJam >= 18) {
                    $fullyBookedDates[] = $tgl;
                }
            }

            return response()->json(array_values($fullyBookedDates));
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    // ==========================================
    // METHOD CANCEL BOOKING (USER)
    // ==========================================
    public function cancel($id)
    {
        $token = session('token');

        if (!$token) {
            return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        try {
            $response = Http::withToken($token)
                ->post(env('API_URL') . "/api/booking/{$id}/cancel");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Booking berhasil dibatalkan.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $response->json()['message'] ?? 'Gagal membatalkan booking.'
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}