@extends('layouts.admin')

@section('content')

    <div class="booking-header">
        <div>
            <h4>Manajemen Booking</h4>
            <small class="text-muted">Admin / Booking</small>
        </div>
    </div>

    <div class="card-box">

        <h5 class="fw-bold mb-4 text-dark">Data Booking</h5>

        @if(session('success'))
            <div class="alert alert-success border-0 rounded-3 fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error') || isset($error))
            <div class="alert alert-danger border-0 rounded-3 fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') ?? $error }}
            </div>
        @endif

        @include('admin.components.proof-modal')

        <div class="table-responsive">
            <table class="table align-middle admin-booking-table">
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
                            $dateString = $item['tanggal'] ?? $item['created_at'] ?? now();
                            $tglBooking = \Carbon\Carbon::parse($dateString)->timezone('Asia/Jakarta');

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
                                'pending'              => ['Pending', 'status-bg-warning'],
                                'waiting_confirmation' => ['Waiting confirmation', 'status-bg-primary'],
                                'confirmed'            => ['Confirmed', 'status-bg-success'],
                                'expired'              => ['Expired', 'status-bg-secondary'],
                                'cancelled'            => ['Cancelled', 'status-bg-dark'],
                            ];

                            $statusText = $statusMap[$paymentStatus][0] ?? ucfirst(str_replace('_', ' ', $paymentStatus));
                            $badgeClass = $statusMap[$paymentStatus][1] ?? 'status-bg-dark';

                            // Format proof image path to public/uploads/
                            $rawProof = $item['bukti_pembayaran'] ?? '';
                            $proofUrl = null;

                            if (trim((string) $rawProof) !== '') {
                                $cleanPath = ltrim((string)$rawProof, '/');

                                if (filter_var($rawProof, FILTER_VALIDATE_URL)) {
                                    $cleanPath = ltrim((string) parse_url($rawProof, PHP_URL_PATH), '/');
                                }

                                if (str_starts_with($cleanPath, 'storage/')) {
                                    $cleanPath = substr($cleanPath, 8);
                                }
                                if (str_starts_with($cleanPath, 'uploads/')) {
                                    $cleanPath = substr($cleanPath, 8);
                                }

                                $proofUrl = asset('uploads/' . $cleanPath);
                            }
                        @endphp

                        <tr>
                            <td class="fw-semibold">
                                {{ $tglBooking->translatedFormat('d M Y') }}
                            </td>

                            <td class="fw-bold text-muted">
                                #{{ strtoupper(substr($item['id_booking'], 0, 6)) }}
                            </td>

                            <td>{{ $item['user']['nama'] ?? $item['user']['name'] ?? '-' }}</td>

                            <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>

                            <td>
                                {{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}
                            </td>

                            <td>
                                <span class="badge-status-modern {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>

                            <td class="fw-bold text-dark">
                                Rp {{ number_format($item['total_harga'], 0, ',', '.') }}
                            </td>

                            <td>
                                @if($proofUrl)
                                    <img id="bookingProofThumb-{{ $item['id_booking'] }}" class="d-none"
                                        data-proof-url="{{ $proofUrl }}" alt="Proof" />
                                    <button type="button" class="btn btn-outline-primary btn-sm-action"
                                        onclick="openProofModal('{{ $item['id_booking'] }}')">
                                        <i class="fas fa-eye me-1"></i> View Proof
                                    </button>
                                @else
                                    <span class="text-muted fw-bold">-</span>
                                @endif
                            </td>

                            <td>
                                <form id="approve-form-{{ $item['id_booking'] }}" method="POST" action="/booking/{{ $item['id_booking'] }}/confirm-payment" style="display:inline;">
                                    @csrf
                                    @method('PATCH')

                                    @php
                                        $isWaiting   = $paymentStatus === 'waiting_confirmation';
                                        $isConfirmed = $paymentStatus === 'confirmed';
                                    @endphp

                                    <button
                                        type="button"
                                        class="btn btn-success btn-sm-action btn-approve shadow-sm"
                                        data-id="{{ $item['id_booking'] }}"
                                        {{ (!$isWaiting || $isConfirmed) ? 'disabled' : '' }}>
                                        @if($isConfirmed)
                                            <i class="fas fa-check-double me-1"></i> Approved
                                        @else
                                            <i class="fas fa-check me-1"></i> Approve
                                        @endif
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                Tidak ada data booking
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(is_object($bookings) && method_exists($bookings, 'links') && $bookings->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    Menampilkan <b>{{ $bookings->firstItem() }}</b> - <b>{{ $bookings->lastItem() }}</b> dari <b>{{ $bookings->total() }}</b> data
                </small>
                <div>
                    {{ $bookings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-approve').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const bookingId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Konfirmasi Pembayaran?',
                        text: "Apakah Anda yakin ingin menyetujui pembayaran untuk transaksi ini?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Approve!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById(`approve-form-${bookingId}`).submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection