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
                <i class="fas fa-arrow-right"></i>
                Lihat Semua
            </a>
        @endif

    </div>

    {{-- TABLE --}}
    <div class="table-responsive">

        <table class="table modern-table align-middle">

            {{-- HEADER --}}
            <thead>
                <tr>
                    <th>User</th>
                    <th>Lapangan</th>
                    <th>Jadwal</th>
                    <th>Durasi</th>
                    <th>Status</th>
                </tr>
            </thead>

            {{-- BODY --}}
            <tbody>

                @forelse($data as $item)

                    @php

                        $start = \Carbon\Carbon::parse($item['jam_mulai']);
                        $end = \Carbon\Carbon::parse($item['jam_selesai']);

                        $durasi = $start->diffInHours($end);

                        $status = strtolower($item['status_pembayaran'] ?? 'pending');

                        if ($status == 'confirmed') {

                            $badgeClass = 'success-status';
                            $statusText = 'Sudah Dibayar';

                        } elseif ($status == 'waiting_confirmation') {

                            $badgeClass = 'verify-status';
                            $statusText = 'Verifikasi';

                        } elseif ($status == 'pending') {

                            $badgeClass = 'pending-status';
                            $statusText = 'Pending';

                        } elseif ($status == 'expired') {

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
                                    {{ strtoupper(substr($item['user']['nama'] ?? 'U', 0, 1)) }}
                                </div>

                                <div>

                                    <div class="user-name">
                                        {{ $item['user']['nama'] ?? '-' }}
                                    </div>

                                    <small class="user-id">
                                        Booking #{{ $item['id_booking'] }}
                                    </small>

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
                                    {{ $item['lapangan']['nama_lapangan'] ?? '-' }}
                                </div>

                            </div>

                        </td>

                        {{-- JADWAL --}}
                        <td>

                            <div class="schedule-box">

                                <div class="schedule-date">
                                    <i class="fas fa-calendar-alt"></i>

                                    {{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d M Y') }}
                                </div>

                                <div class="schedule-time">
                                    <i class="fas fa-clock"></i>

                                    {{ substr($item['jam_mulai'], 0, 5) }}
                                    -
                                    {{ substr($item['jam_selesai'], 0, 5) }}
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
                        <td>

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

                                <h5 class="mt-3">
                                    Belum Ada Booking
                                </h5>

                                <p class="mb-0">
                                    Booking terbaru pengguna akan tampil di sini
                                </p>

                            </div>

                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>