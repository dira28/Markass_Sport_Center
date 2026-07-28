<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hasBooked = false;

        // 1. Cek apakah user memiliki Token Login di Session
        if (session('token')) {
            $userId = session('user.id') ?? session('user_id');

            if ($userId) {
                $hasBooked = DB::table('lapangan_booking')
                    ->where('user_id', $userId)
                    ->exists();
            }
        }

        return view('dashboard', compact('hasBooked'));
    }
}