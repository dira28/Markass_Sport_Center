@extends('layouts.admin')

@section('content')

    <div class="booking-header">
        <div>
            <h4>Manajemen Booking</h4>
            <small class="text-muted">Admin / Booking Pending & Verifikasi</small>
        </div>
    </div>

    <div class="card-box">

        <h5 class="fw-bold mb-4 text-dark">Permintaan Booking (Menunggu Tindakan)</h5>

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
                        <th>Waktu Transaksi</th>
                        <th>ID Booking</th>
                        <th>Pengguna</th>
                        <th>Lapangan</th>
                        <th>Jadwal Main</th>
                        <th>Status</th>
                        <th>Harga</th>
                        <th>Bukti Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($bookings as $item)

                        @php
                            // Ambil tanggal transaksi pembuatan booking (created_at)
                            $createdDate = $item['created_at'] ?? $item['tanggal'] ?? now();
                            $tglTransaksi = \Carbon\Carbon::parse($createdDate)->timezone('Asia/Jakarta');

                            // Tanggal Main
                            $tglMain = \Carbon\Carbon::parse($item['tanggal'])->timezone('Asia/Jakarta');

                            $paymentStatus = strtolower(trim((string) ($item['status_pembayaran'] ?? 'pending')));

                            if (in_array($paymentStatus, ['waiting_confirmation', 'menunggu_verifikasi'], true)) {
                                $statusText = 'Menunggu Verifikasi';
                                $badgeClass = 'status-bg-primary';
                            } else {
                                $statusText = 'Pending (Belum Bayar)';
                                $badgeClass = 'status-bg-warning';
                            }

                            // Format Gambar Bukti Pembayaran
                            $rawProof = $item['bukti_pembayaran'] ?? '';
                            $proofUrl = null;

                            if (trim((string) $rawProof) !== '') {
                                $cleanPath = ltrim((string) $rawProof, '/');

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
                            <!-- Waktu User Melakukan Booking -->
                            <td class="fw-semibold">
                                <div>{{ $tglTransaksi->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">{{ $tglTransaksi->format('H:i') }} WIB</small>
                            </td>

                            <td class="fw-bold text-muted">
                                #{{ strtoupper(substr($item['id_booking'], 0, 6)) }}
                            </td>

                            <td>{{ $item['user']['nama'] ?? $item['user']['name'] ?? '-' }}</td>

                            <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>

                            <!-- Tanggal & Jam Main -->
                            <td>
                                <div>{{ $tglMain->translatedFormat('d M Y') }}</div>
                                <small class="text-muted">{{ $item['jam_mulai'] }} - {{ $item['jam_selesai'] }}</small>
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
                                <div class="d-flex align-items-center gap-1">
                                    <!-- FORM APPROVE -->
                                    <form id="approve-form-{{ $item['id_booking'] }}" method="POST"
                                        action="/booking/{{ $item['id_booking'] }}/confirm-payment" style="display:inline;">
                                        @csrf
                                        @method('PATCH')

                                        <button type="button" class="btn btn-success btn-sm-action btn-approve shadow-sm"
                                            data-id="{{ $item['id_booking'] }}">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    </form>

                                    <!-- TOMBOL REJECT -->
                                    <button type="button" class="btn btn-danger btn-sm-action btn-cancel-admin shadow-sm"
                                        data-id="{{ $item['id_booking'] }}">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-2x mb-3 text-success d-block"></i>
                                Tidak ada antrean booking baru. Semua transaksi sudah diproses!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(is_object($bookings) && method_exists($bookings, 'links') && $bookings->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    Menampilkan <b>{{ $bookings->firstItem() }}</b> - <b>{{ $bookings->lastItem() }}</b> dari
                    <b>{{ $bookings->total() }}</b> data
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

            // 1. CONFIRM APPROVE
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

            // 2. REJECT / CANCEL VIA FETCH API
            document.querySelectorAll('.btn-cancel-admin').forEach(button => {
                button.addEventListener('click', async function (e) {
                    e.preventDefault();

                    const btn = this;
                    const bookingId = btn.getAttribute('data-id');

                    const result = await Swal.fire({
                        title: 'Tolak & Batalkan Booking?',
                        text: "Apakah Anda yakin ingin membatalkan booking ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Batalkan!',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    });

                    if (!result.isConfirmed) return;

                    const token = window.authToken || localStorage.getItem('token');

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                        || document.querySelector('input[name="_token"]')?.value;

                    try {
                        btn.disabled = true;
                        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';

                        const response = await fetch(`/api/booking/${bookingId}/cancel`, {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });

                        const data = await response.json();

                        if (response.ok && (data.success || data.status === 'success')) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dibatalkan!',
                                text: 'Booking berhasil dibatalkan.',
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    popup: 'rounded-4 shadow'
                                }
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Gagal membatalkan booking.',
                                confirmButtonColor: '#0d6efd'
                            });
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-times me-1"></i> Reject';
                        }
                    } catch (err) {
                        console.error("Error cancelling booking:", err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem saat membatalkan booking.',
                            confirmButtonColor: '#0d6efd'
                        });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-times me-1"></i> Reject';
                    }
                });
            });

        });
    </script>

@endsection