<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function index()
    {
        $lapangan = [];

        try {
            $res = Http::get(env('API_URL') . '/api/lapangan');

            if ($res->successful()) {
                $lapangan = $res->json()['data'];
            }
        } catch (\Exception $e) {
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

            // 🔥 HANDLE RESPONSE
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

            if ($res->successful()) {
                return response()->json($res->json()['data'] ?? []);
            }

            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getStatusJam(Request $request)
    {
        $token = session('token');
        $lapangan_id = $request->lapangan_id;
        $tanggal = $request->tanggal;

        if (!$token || !$lapangan_id || !$tanggal) {
            return response()->json([]);
        }

        try {
            // Force-expire before returning availability so expired bookings reopen immediately
            Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'status_pembayaran' => 'pending'
                ]);

            // Run expire job locally (Laravel) by calling the API expiration command.
            // If API implements it, it will update status_pembayaran => expired.
            Http::withToken($token)
                ->patch(env('API_URL') . '/api/booking/expire-pending');

            $res = Http::withToken($token)
                ->get(env('API_URL') . '/api/lapangan/' . $lapangan_id . '/status-jam', [
                    'tanggal' => $tanggal
                ]);

            if ($res->successful()) {
                return response()->json($res->json()['data'] ?? []);
            }

            return response()->json([]);
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
            $res = Http::withToken($token)
                ->get(env('API_URL') . '/api/booking/' . $id);

            if ($res->successful()) {
                $booking = $res->json()['data'] ?? null;
                if ($booking) {
                    $statusClass = match ($booking['status'] ?? '') {
                        'pending' => 'bg-warning',
                        'menunggu_verifikasi' => 'bg-orange',
                        'confirmed' => 'bg-success',
                        'expired' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                    $statusText = ucfirst(str_replace('_', ' ', $booking['status'] ?? 'unknown'));
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
        // Requirement: form-data key `bukti`, accept only .jpg/.jpeg/.png
        $request->validate([
            'bukti' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);


        $token = session('token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Login required'
            ], 401);
        }

        try {
            $file = $request->file('bukti');

            // Store uploaded file (Laravel disk: public)
            $path = $file->store('bukti', 'public');

            // IMPORTANT: API contract expects form-data key `bukti`.
            // Our current backend persists file first, then sends expected payload.
            // If your API expects the actual binary file instead of a path, adjust accordingly.
            $res = Http::withToken($token)
                ->attach('bukti', file_get_contents($file->getRealPath()), basename($path), [
                    'Content-Type' => $file->getClientMimeType()
                ])
                ->post(env('API_URL') . '/api/booking/' . $id . '/upload-bukti');

            if ($res->successful()) {
                $payload = $res->json();
                $data = $payload['data'] ?? $payload;

                // Forward backend contract so frontend/admin can render latest bukti_pembayaran
                $latestBukti = $data['bukti_pembayaran'] ?? $data['bukti'] ?? null;
                $statusPembayaran = $data['status_pembayaran'] ?? $data['status'] ?? 'waiting_confirmation';

                return response()->json([
                    'success' => true,
                    'message' => 'Menunggu konfirmasi admin',
                    'data' => [
                        'id_booking' => $data['id_booking'] ?? $id,
                        'bukti_pembayaran' => $latestBukti,
                        'status_pembayaran' => $statusPembayaran,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $res->json()['message'] ?? 'Upload failed'
            ], $res->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmPayment($id_booking)
    {
        $token = session('token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Login required'], 401);
        }

        try {
            $res = Http::withToken($token)
                ->patch(env('API_URL') . '/api/booking/' . $id_booking . '/confirm-payment');

            if ($res->successful()) {
                return response()->json(['success' => true, 'message' => 'Pembayaran approved']);
            }

            return response()->json([
                'success' => false,
                'message' => $res->json()['message'] ?? 'Confirm payment failed'
            ], $res->status() ?: 500);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    // reject/payment cancellation not supported by backend currently
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

}
