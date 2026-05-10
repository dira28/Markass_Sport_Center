@extends('layouts.admin')

@section('content')

    <div class="booking-header">
        <div>
            <h4>Manajemen Booking</h4>
            <small>Admin / Booking</small>
        </div>
    </div>

    <div class="card-box mt-4">

        <h5 class="mb-3">Data Booking</h5>

        {{-- ERROR ALERT --}}
        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

<table class="table align-middle admin-booking-table payment-admin-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>ID</th>
                    <th>Pengguna</th>
                    <th>Lapangan</th>
                    <th>Status</th>
                    <th>Harga</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($bookings as $item)

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

                        $status = $item['status'] ?? 'pending';
                        $pembayaran = $item['status_pembayaran'] ?? $status;
                        
                        $statusMap = [
                            'pending' => ['Pending', 'warning'],
                            'menunggu_verifikasi' => ['Menunggu Verifikasi', 'info'],
                            'confirmed' => ['Confirmed', 'success'],
                            'expired' => ['Expired', 'danger']
                        ];
                        
                        $statusKey = strtolower(str_replace(' ', '_', $status));
                        if (isset($statusMap[$statusKey])) {
                            [$statusText, $badge] = $statusMap[$statusKey];
                        } else {
                            $statusText = ucfirst($status);
                            $badge = 'dark';
                        }
                    @endphp

                    <tr>
                        <td>
                            {{ $tanggal->translatedFormat('d M Y') }}
                        </td>

                        <td>
                            #{{ strtoupper(substr($item['id_booking'], 0, 6)) }}
                        </td>

                        <td>{{ $item['user']['nama'] ?? '-' }}</td>

                        <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>

                        {{-- JAM --}}
                        <td>
                            {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}
                        </td>

                        <td>
                            <span class="badge bg-{{ $badge }}">
                                {{ $statusText }}
                            </span>
                        </td>

                        <td>
                            Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                        </td>
                        <td>
                            @if(isset($item['bukti_path']))
                                <img src="{{ $item['bukti_path'] }}" style="width:40px;height:40px;border-radius:6px;cursor:pointer;" onclick="viewProof('{{ $item['id_booking'] }}')" title="View Proof" />
                            @endif
                            @if($statusKey === 'menunggu_verifikasi')
                                <form method="POST" action="/booking/{{ $item['id_booking'] }}/acc" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm mt-1" onclick="return confirm('ACC pembayaran?')">
                                        <i class="fas fa-check"></i> ACC
                                    </button>
                                </form>
                            @endif
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

@endsection