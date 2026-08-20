@extends('layouts.admin')

@section('title', 'Laporan Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/pages/report.css') }}">
    @vite('resources/css/admin/pages/report.css')
@endpush

@section('content')

    @php
        // Menyiapkan variabel fallback agar tidak throw "Undefined Variable"
        $fromDateVal = $fromDate ?? request('from_date');
        $toDateVal = $toDate ?? request('to_date');
    @endphp

    <div class="report-page">

        {{-- Header --}}
        <div class="page-title-box">
            <div>
                <h3 class="page-title">Laporan Booking</h3>
                <small class="text-muted">Kelola dan tinjau seluruh riwayat transaksi booking fasilitas.</small>
            </div>
            <div>
                {{-- Export PDF Button --}}
                <a href="{{ route('admin.laporan.export', ['from_date' => $fromDateVal, 'to_date' => $toDateVal, 'status' => request('status', 'all')]) }}"
                    class="btn-export">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                        <path
                            d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- Date & Status Filter Form --}}
        <div class="filter-card">
            <form method="GET" action="{{ route('admin.laporan') }}">
                <div class="row g-3 align-items-end">

                    <div class="col-md-3">
                        <label class="filter-label">Dari Tanggal</label>
                        <input type="date" name="from_date" value="{{ request('from_date', $fromDateVal) }}"
                            class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label">Sampai Tanggal</label>
                        <input type="date" name="to_date" value="{{ request('to_date', $toDateVal) }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="filter-label">Status Pembayaran</label>
                        <select name="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid / Lunas</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending / Menunggu
                            </option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired / Batal
                            </option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn-filter w-50">Filter</button>
                        <a href="{{ route('admin.laporan') }}" class="btn-reset w-50 text-center">Reset</a>
                    </div>

                </div>
            </form>
        </div>

        {{-- KPI Summary Cards --}}
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-label">Total Booking</div>
                <div class="kpi-number kpi-total">{{ number_format($totalBooking ?? 0) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-number kpi-revenue">Rp{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Status Paid</div>
                <div class="kpi-number kpi-paid">{{ number_format($paidBooking ?? 0) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">Status Pending</div>
                <div class="kpi-number kpi-pending">{{ number_format($pendingBooking ?? 0) }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-label">EXPIRED / BATAL</div>
                <div class="kpi-number text-danger">{{ number_format($expiredBooking ?? 0) }}</div>
            </div>
        </div>

        {{-- Bookings Table --}}
        <div class="table-card">

            <div class="table-header d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">{{ number_format($bookings->total()) }} Data Bookings</h5>
                <small class="text-muted">
                    Periode:
                    {{ $fromDateVal ? \Carbon\Carbon::parse($fromDateVal)->format('d M Y') : '-' }} s/d
                    {{ $toDateVal ? \Carbon\Carbon::parse($toDateVal)->format('d M Y') : '-' }}
                </small>
            </div>

            <div class="table-responsive-container">
                <table class="laporan-table">
                    <thead>
                        <tr>
                            <th style="width: 120px; min-width: 120px;">ID</th>
                            <th style="width: 250px; min-width: 250px;">Lapangan</th>
                            <th style="width: 160px; min-width: 160px;">Tgl Dibuat</th>
                            <th style="width: 200px; min-width: 200px;">Jadwal Main</th>
                            <th style="width: 120px; min-width: 120px;">Durasi</th>
                            <th style="width: 180px; min-width: 180px;">User</th>
                            <th style="width: 150px; min-width: 150px;">Status</th>
                            <th style="width: 160px; min-width: 160px;" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $b = (array) $booking;

                                $rawStatus = $b['status_pembayaran'] ?? $b['status'] ?? $b['payment_status'] ?? 'pending';
                                $st = str_replace([' ', '-'], '_', strtolower(trim((string) $rawStatus)));

                                $jamMulai = $b['jam_mulai'] ?? null;
                                $jamSelesai = $b['jam_selesai'] ?? null;

                                $durasi = 0;
                                if ($jamMulai && $jamSelesai) {
                                    try {
                                        $start = \Carbon\Carbon::parse($jamMulai);
                                        $end = \Carbon\Carbon::parse($jamSelesai);
                                        $durasi = $start->diffInHours($end);
                                    } catch (\Exception $e) {
                                        $durasi = 0;
                                    }
                                }

                                $createdAt = $b['created_at'] ?? $b['created_date'] ?? null;
                                $tanggalMain = $b['tanggal'] ?? null;

                                $rawId = $b['id_booking'] ?? $b['id'] ?? '-';
                                $idBooking = (str_starts_with((string) $rawId, '#')) ? $rawId : '#' . $rawId;

                                // Nama Lapangan
                                $namaLapangan = '-';
                                if (isset($b['lapangan']['nama_lapangan'])) {
                                    $namaLapangan = $b['lapangan']['nama_lapangan'];
                                } elseif (isset($b['nama_lapangan'])) {
                                    $namaLapangan = $b['nama_lapangan'];
                                }

                                // Nama User
                                $namaUser = '-';
                                if (isset($b['nama_user'])) {
                                    $namaUser = $b['nama_user'];
                                } elseif (isset($b['user']['nama'])) {
                                    $namaUser = $b['user']['nama'];
                                } elseif (isset($b['user']['name'])) {
                                    $namaUser = $b['user']['name'];
                                }

                                // Status Display
                                $paidStatuses = ['paid', 'confirmed', 'approve', 'approved', 'lunas', 'berhasil', 'success', 'settlement'];
                                $pendingStatuses = ['pending', 'waiting_confirmation', 'waiting', 'menunggu_verifikasi', 'menunggu_konfirmasi', 'unpaid', 'menunggu'];

                                if (in_array($st, $paidStatuses)) {
                                    $displayStatus = 'Paid';
                                    $badgeClass = 'status-paid';
                                } elseif (in_array($st, $pendingStatuses)) {
                                    $displayStatus = 'Pending';
                                    $badgeClass = 'status-pending';
                                } else {
                                    $displayStatus = 'Cancelled / Expired';
                                    $badgeClass = 'status-expired';
                                }
                            @endphp

                            <tr>
                                <td><strong>{{ strtoupper($idBooking) }}</strong></td>
                                <td><strong>{{ $namaLapangan }}</strong></td>
                                <td>{{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d M Y') : '-' }}</td>
                                <td>
                                    <div>
                                        <strong>{{ $tanggalMain ? \Carbon\Carbon::parse($tanggalMain)->format('d M Y') : '-' }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        {{ $jamMulai ? substr($jamMulai, 0, 5) : '00:00' }} -
                                        {{ $jamSelesai ? substr($jamSelesai, 0, 5) : '00:00' }}
                                    </small>
                                </td>
                                <td><span class="badge-durasi">{{ $durasi }} Jam</span></td>
                                <td>{{ $namaUser }}</td>
                                <td>
                                    <span class="status-badge {{ $badgeClass }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <strong>Rp{{ number_format((float) ($b['total_harga'] ?? $b['total'] ?? 0), 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Tidak ada data booking pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            @if($bookings->hasPages())
                <div class="table-footer-pagination p-3 border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan {{ $bookings->firstItem() }} - {{ $bookings->lastItem() }} dari {{ $bookings->total() }}
                        data
                    </div>
                    <div>
                        {{ $bookings->links() }}
                    </div>
                </div>
            @endif

        </div>

    </div>

@endsection