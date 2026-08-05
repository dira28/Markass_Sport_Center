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

        {{-- FORM FILTER & SEARCH --}}
        <form method="GET" action="{{ url()->current() }}" class="row g-2 mb-4">
            {{-- Input Search Text (ID / User / Lapangan) --}}
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="Cari ID, User, atau Lapangan..." 
                       value="{{ request('search') }}">
            </div>

            {{-- Input Tanggal Booking (created_at) --}}
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control" 
                       value="{{ request('tanggal') }}"
                       title="Filter berdasarkan tanggal transaksi/booking dibuat">
            </div>

            {{-- Input Status Pembayaran --}}
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="waiting_confirmation" {{ request('status') == 'waiting_confirmation' ? 'selected' : '' }}>Waiting Confirmation</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Tombol Submit & Reset --}}
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'tanggal', 'status']))
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fas fa-undo"></i>
                    </a>
                @endif
            </div>
        </form>

        {{-- ERROR ALERT --}}
        @if(isset($error))
            <div class="alert alert-danger">
                {{ $error }}
            </div>
        @endif

        {{-- Proof modal (View Proof) --}}
        @include('admin.components.proof-modal')

        <div class="table-responsive">
            <table class="table align-middle admin-booking-table payment-admin-table">
                <thead>
                    <tr>
                        <th>Tanggal Booking</th>
                        <th>ID</th>
                        <th>Pengguna</th>
                        <th>Lapangan</th>
                        <th>Jam</th>
                        <th>Status Pembayaran</th>
                        <th>Harga</th>
                        <th>Bukti Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bookings as $item)

                        @php
                            // Menampilkan tanggal dibuatnya booking (created_at)
                            $tglBooking = isset($item['created_at']) 
                                ? \Carbon\Carbon::parse($item['created_at'])->timezone('Asia/Jakarta')
                                : \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');

                            // Normalisasi status pembayaran
                            $paymentStatus = strtolower(trim((string)($item['status_pembayaran'] ?? 'pending')));

                            if ($paymentStatus === 'menunggu_verifikasi') {
                                $paymentStatus = 'waiting_confirmation';
                            } elseif (in_array($paymentStatus, ['approve', 'paid'], true)) {
                                $paymentStatus = 'confirmed';
                            }

                            if (!in_array($paymentStatus, ['pending', 'waiting_confirmation', 'confirmed', 'expired', 'cancelled'], true)) {
                                $paymentStatus = 'pending';
                            }

                            $statusMap = [
                                'pending'              => ['Pending', 'warning'],
                                'waiting_confirmation' => ['Waiting confirmation', 'primary'],
                                'confirmed'            => ['Confirmed', 'success'],
                                'expired'              => ['Expired', 'secondary'],
                                'cancelled'            => ['Cancelled', 'dark'],
                            ];

                            $statusText = $statusMap[$paymentStatus][0] ?? ucfirst(str_replace('_', ' ', $paymentStatus));
                            $badge      = $statusMap[$paymentStatus][1] ?? 'secondary';
                        @endphp

                        <tr>
                            <td>
                                {{ $tglBooking->translatedFormat('d M Y') }}
                            </td>

                            <td>
                                #{{ strtoupper(substr($item['id_booking'], 0, 6)) }}
                            </td>

                            <td>{{ $item['user']['nama'] ?? $item['user']['name'] ?? '-' }}</td>

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

                            {{-- HARGA --}}
                            <td>
                                Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                            </td>

                            {{-- BUKTI --}}
                            <td>
                                @if(isset($item['bukti_pembayaran']) && trim((string) ($item['bukti_pembayaran'] ?? '')) !== '')
                                    <img id="bookingProofThumb-{{ $item['id_booking'] }}" class="d-none"
                                        data-proof-url="{{ $item['bukti_pembayaran'] }}" alt="Proof" />
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        onclick="openProofModal('{{ $item['id_booking'] }}')">
                                        <i class="fas fa-eye"></i> View Proof
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <form method="POST" action="/booking/{{ $item['id_booking'] }}/confirm-payment" style="display:inline;">
                                    @csrf
                                    @method('PATCH')

                                    @php
                                        $isWaiting   = $paymentStatus === 'waiting_confirmation';
                                        $isConfirmed = $paymentStatus === 'confirmed';
                                    @endphp

                                    <button
                                        type="submit"
                                        class="btn btn-success btn-sm"
                                        {{ (!$isWaiting || $isConfirmed) ? 'disabled' : '' }}
                                        onclick="return confirm('Approve Payment?')">
                                        @if($isConfirmed)
                                            <i class="fas fa-check-double"></i> Approved
                                        @else
                                            <i class="fas fa-check"></i> Approve
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                Tidak ada data booking
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- LINK PAGINASI --}}
        @if(method_exists($bookings, 'links'))
            <div class="d-flex justify-content-end mt-3">
                {{ $bookings->links() }}
            </div>
        @endif

    </div>

@endsection