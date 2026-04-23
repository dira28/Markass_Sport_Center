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

        $res = Http::withToken($token)
            ->post(env('API_URL') . '/api/booking', $request->all());

        if ($res->successful()) {
            return response()->json([
                'status' => 'success',
                'data' => $res->json()
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Booking gagal'
        ], 400);
    }

}