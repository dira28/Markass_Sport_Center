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
            // 🔥 Ditambahkan withoutVerifying() agar halaman awal tidak crash saat memuat list lapangan
            $res = Http::withoutVerifying()->get(env('API_URL') . '/api/lapangan');

            if ($res->successful()) {
                $lapangan = $res->json()['data'];
            }
        } catch (\Exception $e) {
            // Biarkan kosong atau log error jika diperlukan
        }

        return view('user.pages.booking', compact('lapangan'));
    }

    /**
     * 🔥 SINKRON DENGAN ROUTE: getBookedSlots
     * Mengambil slot jadwal kosong dari Node.js tanpa terblokir SSL
     */
    public function getBookedSlots(Request $request)
    {
        $token = session('token');

        try {
            $res = Http::withoutVerifying()
                ->withToken($token)
                ->get(env('API_URL') . '/api/booking/slots', [
                    'tanggal' => $request->tanggal,
                    'lapangan' => $request->lapangan // 🔥 Diubah dari 'id_lapangan' menjadi 'lapangan' agar sesuai Node.js
                ]);

            // Jika Node.js sukses memberikan data array slots
            if ($res->successful()) {
                return response()->json($res->json());
            }

            // Jika Node.js melempar eror/gagal, balikan array kosong [] agar JS frontend tidak crash
            return response()->json([]);

        } catch (\Exception $e) {
            // Jika Laravel-nya yang crash, balikan array kosong juga sebagai fallback aman
            return response()->json([]);
        }
    }

    public function store(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sesi login habis. Silakan login kembali.'
            ], 401);
        }

        // 1. Ambil detail data lapangan dari API Node.js untuk mengecek harga aslinya
        try {
            $lapanganRes = Http::withoutVerifying()->get(env('API_URL') . '/api/lapangan/' . $request->id_lapangan);

            if (!$lapanganRes->successful()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data lapangan tidak ditemukan di server.'
                ], 404);
            }

            $dataLapangan = $lapanganRes->json()['data'];
            $hargaPagi = intval($dataLapangan['harga_pagi']);
            $hargaMalam = intval($dataLapangan['harga_malam']);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal terhubung ke server database lapangan.'
            ], 500);
        }

        // 2. Hitung total harga real-time berdasarkan jam sewa di Controller
        $startHour = intval(explode(':', $request->jam_mulai)[0]);
        $endHour = intval(explode(':', $request->jam_selesai)[0]);
        $durasi = $endHour - $startHour;

        $totalHargaReal = 0;
        for ($i = 0; $i < $durasi; $i++) {
            $currentHour = $startHour + $i;
            if ($currentHour <= 15) {
                $totalHargaReal += $hargaPagi;
            } else {
                $totalHargaReal += $hargaMalam;
            }
        }

        // 3. Susun payload final yang siap dikirim (DISINKRONKAN KEY-NYA KE NODE.JS)
        $payload = [
            'id_lapangan' => $request->id_lapangan,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'durasi' => $durasi,
            'total_harga' => $totalHargaReal
        ];

        // 4. Kirim data booking ke API Node.js
        try {
            $res = Http::withoutVerifying()->withToken($token)
                ->post(env('API_URL') . '/api/booking', $payload);

            if ($res->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $res->json()
                ]);
            }

            $errorBody = $res->json();
            return response()->json([
                'status' => 'error',
                'message' => $errorBody['message'] ?? 'Proses booking ditolak oleh server pusat.'
            ], $res->status());

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}