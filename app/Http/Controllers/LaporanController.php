<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    private function getFilteredData(Request $request)
    {
        $token = session('token');

        if (!$token) {
            return null;
        }

        $fromDateRaw = $request->get('from_date');
        $toDateRaw = $request->get('to_date');
        $statusFilter = $request->get('status', 'all');

        // Parse tanggal HANYA JIKA input dari/sampai tanggal diisi oleh user
        $fromDate = $fromDateRaw ? Carbon::parse($fromDateRaw)->startOfDay() : null;
        $toDate = $toDateRaw ? Carbon::parse($toDateRaw)->endOfDay() : null;

        $allBookings = collect();

        try {
            $response = Http::withToken($token)
                ->acceptJson()
                ->get(env('API_URL') . '/api/booking');

            if ($response->successful()) {
                $allBookings = collect($response->json()['data'] ?? []);
            }
        } catch (\Exception $e) {
            $allBookings = collect();
        }

        // 1. FILTER TANGGAL (Hanya aktif jika user mengisi input tanggal)
        $filteredByDate = $allBookings->filter(function ($item) use ($fromDate, $toDate) {
            $rawDate = $item['created_at'] ?? $item['tanggal'] ?? null;
            if (!$rawDate)
                return true;

            $date = Carbon::parse($rawDate);

            // Jika kedua tanggal diisi
            if ($fromDate && $toDate) {
                return $date->between($fromDate, $toDate);
            }
            // Jika hanya "Dari Tanggal" yang diisi
            if ($fromDate) {
                return $date->greaterThanOrEqualTo($fromDate);
            }
            // Jika hanya "Sampai Tanggal" yang diisi
            if ($toDate) {
                return $date->lessThanOrEqualTo($toDate);
            }

            // Jika tidak ada filter tanggal (Reset / Default), TAMPILKAN SEMUA DATA
            return true;
        });

        // Helper ekstraksi status
        $getStatus = function ($item) {
            $raw = $item['status_pembayaran']
                ?? $item['status']
                ?? $item['payment_status']
                ?? '';

            return str_replace([' ', '-'], '_', strtolower(trim((string) $raw)));
        };

        $paidStatuses = ['paid', 'confirmed', 'approve', 'approved', 'lunas', 'berhasil', 'success', 'settlement'];
        $pendingStatuses = ['pending', 'waiting_confirmation', 'waiting', 'menunggu_verifikasi', 'menunggu_konfirmasi', 'unpaid', 'menunggu'];
        $expiredStatuses = ['expired', 'expire', 'cancelled', 'canceled', 'failed', 'batal'];

        // 2. HITUNG KPI CARD
        $totalBooking = $filteredByDate->count();

        $paidBooking = $filteredByDate->filter(function ($item) use ($getStatus, $paidStatuses) {
            return in_array($getStatus($item), $paidStatuses, true);
        })->count();

        $pendingBooking = $filteredByDate->filter(function ($item) use ($getStatus, $pendingStatuses) {
            return in_array($getStatus($item), $pendingStatuses, true);
        })->count();

        $expiredBooking = $filteredByDate->filter(function ($item) use ($getStatus, $expiredStatuses) {
            return in_array($getStatus($item), $expiredStatuses, true);
        })->count();

        $totalRevenue = $filteredByDate->filter(function ($item) use ($getStatus, $paidStatuses) {
            return in_array($getStatus($item), $paidStatuses, true);
        })->sum(function ($item) {
            return (float) ($item['total_harga'] ?? $item['total'] ?? 0);
        });

        // 3. FILTER TABEL SESUAI DROPDOWN STATUS
        $tableCollection = $filteredByDate;
        if ($statusFilter && $statusFilter !== 'all') {
            $tableCollection = $filteredByDate->filter(function ($item) use ($getStatus, $statusFilter, $paidStatuses, $pendingStatuses, $expiredStatuses) {
                $st = $getStatus($item);
                if ($statusFilter === 'paid')
                    return in_array($st, $paidStatuses, true);
                if ($statusFilter === 'pending')
                    return in_array($st, $pendingStatuses, true);
                if ($statusFilter === 'expired')
                    return in_array($st, $expiredStatuses, true);
                return true;
            });
        }

        return [
            'rawCollection' => $tableCollection,
            'totalBooking' => $totalBooking,
            'totalRevenue' => $totalRevenue,
            'paidBooking' => $paidBooking,
            'pendingBooking' => $pendingBooking,
            'expiredBooking' => $expiredBooking,
            'selectedStatus' => $statusFilter,
            'fromDate' => $fromDate ? $fromDate->format('Y-m-d') : null,
            'toDate' => $toDate ? $toDate->format('Y-m-d') : null,
        ];
    }
    public function index(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        $collection = $data['rawCollection'];

        $perPage = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $pageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedBookings = new LengthAwarePaginator(
            $pageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        $data['bookings'] = $paginatedBookings;
        unset($data['rawCollection']);

        return view('admin.pages.laporan', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getFilteredData($request);

        if ($data === null) {
            return redirect('/login');
        }

        $data['bookings'] = $data['rawCollection'];
        unset($data['rawCollection']);

        $pdf = Pdf::loadView('admin.reports.booking-pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-booking-' . date('Y-m-d') . '.pdf');
    }
}