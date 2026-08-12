<div class="card-box modern-booking-card">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="booking-title mb-1">
                Latest Bookings
            </h4>
            <p class="booking-subtitle mb-0">
                Booking terbaru pengguna hari ini
            </p>
        </div>

        @if(isset($showButton) && $showButton)
            <a href="{{ $url ?? '#' }}" class="btn-modern">
                <span>Lihat Semua</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        @endif
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table modern-table align-middle mb-0">

            {{-- HEADER --}}
            <thead>
                <tr>
                    <th>User</th>
                    <th>Lapangan</th>
                    <th>Jadwal</th>
                    <th>Durasi</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>

            {{-- BODY --}}
            <tbody>
                @forelse($data as $item)
                    @php
                        // 1. Hitung Durasi
                        $jamMulai = data_get($item, 'jam_mulai');
                        $jamSelesai = data_get($item, 'jam_selesai');
                        $durasi = 0;
                        if ($jamMulai && $jamSelesai) {
                            $start = \Carbon\Carbon::parse($jamMulai);
                            $end = \Carbon\Carbon::parse($jamSelesai);
                            $durasi = $start->diffInHours($end);
                        }

                        // 2. Format ID Booking (Cegah Double #)
                        $rawId = data_get($item, 'id_booking', data_get($item, 'id', '-'));
                        $idBooking = str_starts_with($rawId, '#') ? $rawId : '#' . $rawId;

                        // 3. Normalisasi Status
                        $rawStatus = data_get($item, 'status_pembayaran', data_get($item, 'status', data_get($item, 'payment_status', 'pending')));
                        $st = str_replace([' ', '-'], '_', strtolower(trim((string) $rawStatus)));

                        // Kategori Status
                        $paidStatuses = ['paid', 'confirmed', 'approve', 'approved', 'lunas', 'berhasil', 'success', 'settlement'];
                        $waitingStatuses = ['waiting_confirmation', 'waiting', 'menunggu_verifikasi', 'menunggu_konfirmasi'];
                        $pendingStatuses = ['pending', 'unpaid', 'menunggu'];
                        $expiredStatuses = ['expired', 'expire'];

                        if (in_array($st, $paidStatuses, true)) {
                            $badgeClass = 'success-status';
                            $statusText = 'Paid';
                        } elseif (in_array($st, $waitingStatuses, true)) {
                            $badgeClass = 'verify-status';
                            $statusText = 'Verifikasi';
                        } elseif (in_array($st, $pendingStatuses, true)) {
                            $badgeClass = 'pending-status';
                            $statusText = 'Pending';
                        } elseif (in_array($st, $expiredStatuses, true)) {
                            $badgeClass = 'expired-status';
                            $statusText = 'Expired';
                        } else {
                            $badgeClass = 'cancel-status';
                            $statusText = 'Dibatalkan';
                        }
                    @endphp

                    <tr>
                        {{-- USER --}}
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr(data_get($item, 'user.nama', data_get($item, 'user.name', 'U')), 0, 1)) }}
                                </div>
                                <div>
                                    <div class="user-name">
                                        {{ data_get($item, 'user.nama', data_get($item, 'user.name', data_get($item, 'nama_user', '-'))) }}
                                    </div>
                                    <span class="user-id">
                                        Booking {{ strtoupper($idBooking) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- LAPANGAN --}}
                        <td>
                            <div class="lapangan-wrapper">
                                <div class="lapangan-icon">
                                    <i class="fas fa-futbol"></i>
                                </div>
                                <div class="lapangan-name">
                                    {{ data_get($item, 'lapangan.nama_lapangan', data_get($item, 'nama_lapangan', '-')) }}
                                </div>
                            </div>
                        </td>

                        {{-- JADWAL --}}
                        <td>
                            <div class="schedule-box">
                                <div class="schedule-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ data_get($item, 'tanggal') ? \Carbon\Carbon::parse(data_get($item, 'tanggal'))->translatedFormat('d M Y') : '-' }}
                                </div>
                                <div class="schedule-time">
                                    <i class="fas fa-clock"></i>
                                    {{ $jamMulai ? substr($jamMulai, 0, 5) : '00:00' }} -
                                    {{ $jamSelesai ? substr($jamSelesai, 0, 5) : '00:00' }}
                                </div>
                            </div>
                        </td>

                        {{-- DURASI --}}
                        <td>
                            <span class="duration-badge">
                                <i class="fas fa-hourglass-half"></i>
                                {{ $durasi }} Jam
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td class="text-center">
                            <span class="status-modern {{ $badgeClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5 class="mt-2">Belum Ada Booking</h5>
                                <p class="mb-0">Booking terbaru pengguna akan tampil di sini</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>