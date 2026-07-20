@extends('layouts.admin')

@section('content')

    <div class="content-wrapper">

        {{-- HEADER --}}
        <div class="dashboard-header-v2 mb-4">
            <div>
                <h3>Laporan</h3>
                <p>Ringkasan pendapatan & booking</p>
            </div>
        </div>

        {{-- FILTER --}}
            <div class="card-box mb-4">
                <form method="GET" action="{{ route('admin.laporan') }}">
                <div class="row g-3">

                    <div class="col-12 col-md-4">
                        <label>Dari Tanggal</label>
                        <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                    </div>

                    <div class="col-12 col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn-filter w-100">Filter</button>
                    </div>

                </div>

                <div class="row g-3 mt-3">
                    <div class="col-12 d-flex">
                        <a
                            style="height: 40px; padding: 0 12px; display:flex; align-items:center; justify-content:center; font-size: 14px; width: 100%;"
                            href="{{ route('admin.laporan.export.pdf') }}?tanggal_awal={{ request('tanggal_awal') }}&tanggal_akhir={{ request('tanggal_akhir') }}&export=pdf"
                        >
                            Export PDF
                        </a>
                    </div>
                </div>



                @if(request()->hasAny(['tanggal_awal','tanggal_akhir']))
                    <input type="hidden" name="keep" value="1">
                @endif
            </form>
        </div>


        {{-- STATISTIK --}}
        <div class="mb-4">
            <h5 class="mb-3">Statistik</h5>

            @include('admin.components.revenue-cards', [
                'revenueToday' => $revenueToday ?? 0,
                'totalBooking' => $totalBooking ?? 0
            ])
        </div>

        {{-- TABLE BOOKING --}}
        <div class="card-box">

            <h5 class="mb-3">Data Booking</h5>

            <div class="table-responsive-admin">
                <table class="table align-middle">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>ID</th>
                            <th>Pengguna</th>
                            <th>Lapangan</th>
                            <th>Status</th>
                            <th>Harga</th>
                        </tr>
                    </thead>

                    <tbody>


               @forelse ($paginator?->items() ?? $bookings as $item)

                   @php
                    $tanggal = \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');

                    $status = $item['status'] ?? '-';
                    $pembayaran = $item['status_pembayaran'] ?? '-';

                    if ($status == 'booked') {
                        $statusText = 'Berlangsung';
                        $badge = 'success';
                    } elseif ($status == 'available') {
                        $statusText = 'Tersedia';
                        $badge = 'secondary';
                    } else {
                        $statusText = ucfirst($status);
                        $badge = 'dark';
                    }

                    if ($pembayaran == 'pending') {
                        $statusText .= ' (Menunggu Bayar)';
                        $badge = 'warning';
                    } elseif ($pembayaran == 'expired') {
                        $statusText = 'Expired';
                        $badge = 'danger';
                    } elseif ($pembayaran == 'paid') {
                        $statusText .= ' (Lunas)';
                    }
                @endphp

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $tanggal->translatedFormat('d M Y') }}
                        </td>

                        <td>
                            @php
                                $rawNumericId = $item['id'] ?? (isset($item['id_booking']) ? preg_replace('/\D/', '', (string)$item['id_booking']) : null);
                                $displayBookingId = 'BK-' . str_pad((int) ($rawNumericId ?: 0), 6, '0', STR_PAD_LEFT);
                            @endphp
                            {{ $displayBookingId }}
                        </td>

                        <td>
                            {{ $item['user']['nama'] ?? '-' }}
                        </td>

                        <td>
                            {{ $item['lapangan']['nama_lapangan'] ?? '-' }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ $statusText }}
                            </span>
                        </td>

                        <td>
                            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                        </td>

                    </tr>

            @empty

                    <tr>
                        <td colspan="6" class="text-center">
                            Tidak ada data booking
                            </td>
                    </tr>

                @endforelse

                </tbody>

                </table>
            </div>

            @if($paginator)

                <div class="mt-3">
                    {{ $paginator->links() }}
                </div>
            @endif

        </div>


    </div>

@endsection