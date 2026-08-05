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
    public function adminIndex(Request $request)
    {
        $token = session('token');
        $bookings = [];
        $error = null;

        try {
            $res = Http::withToken($token)->get(env('API_URL') . '/api/booking');

            if ($res->successful()) {
                $allBookings = $res->json()['data'] ?? [];

                // 1. LOGIKA FILTER & SEARCH
                $collection = collect($allBookings)->filter(function ($item) use ($request) {
                    $match = true;

                    // Filter Search Text (Cari ID Booking, Nama User, atau Nama Lapangan)
                    if ($request->filled('search')) {
                        $search = strtolower($request->search);
                        $idBooking = strtolower($item['id_booking'] ?? '');
                        $userName = strtolower($item['user']['nama'] ?? '');
                        $lapanganName = strtolower($item['lapangan']['nama_lapangan'] ?? '');

                        $searchMatch = str_contains($idBooking, $search) ||
                            str_contains($userName, $search) ||
                            str_contains($lapanganName, $search);

                        if (!$searchMatch) {
                            $match = false;
                        }
                    }

                    // Filter Tanggal
                    if ($request->filled('tanggal')) {
                        if (($item['tanggal'] ?? '') !== $request->tanggal) {
                            $match = false;
                        }
                    }

                    // Filter Status Pembayaran
                    if ($request->filled('status')) {
                        $status = strtolower($item['status_pembayaran'] ?? 'pending');

                        // Normalisasi status internal
                        if ($status === 'menunggu_verifikasi')
                            $status = 'waiting_confirmation';
                        if (in_array($status, ['approve', 'paid'], true))
                            $status = 'confirmed';

                        if ($status !== strtolower($request->status)) {
                            $match = false;
                        }
                    }

                    return $match;
                });

                // 2. SETTING PAGINATION: 10 Data Per Halaman
                $perPage = 10;
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                // Potong data hasil filter sesuai halaman aktif
                $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

                // Buat paginator objek agar Blade bisa render tombol geser/page links
                $bookings = new LengthAwarePaginator(
                    $currentPageItems,
                    $collection->count(),
                    $perPage,
                    $currentPage,
                    ['path' => LengthAwarePaginator::resolveCurrentPath()]
                );
            } else {
                $error = 'Gagal mengambil data dari API';
            }
        } catch (\Exception $e) {
            $error = 'Server error: ' . $e->getMessage();
        }

        // Return ke view admin kamu
        return view('admin.pages.booking', compact('bookings', 'error'));
    }

    // ==========================================
    // FUNGSI LAIN DI BAWAH INI SAMA SEKALI TIDAK DIUBAH
    // ==========================================

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
}