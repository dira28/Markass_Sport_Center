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
                'message' => 'Token tidak ditemukan, silakan login ulang'
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

            dd($res->status(), $res->body());

            return response()->json([
                'status' => 'success',
                'data' => $res->json()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBookedSlots(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return response()->json([]);
        }

        try {
            $res = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking', [
                    'tanggal' => $request->tanggal,
                    'id_lapangan' => $request->lapangan_id,
                ]);

            if ($res->failed()) {
                return response()->json([]);
            }

            $data = $res->json()['data'] ?? [];

            $blocked = [];

            foreach ($data as $booking) {
                $start = (int) substr($booking['jam_mulai'], 0, 2);
                $end = (int) substr($booking['jam_selesai'], 0, 2);

                for ($i = $start; $i < $end; $i++) {
                    $blocked[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ":00";
                }
            }

            return response()->json($blocked);

        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

}