<div class="card-box">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>{{ $title ?? 'Latest Bookings' }}</h5>

        @if(isset($showButton) && $showButton)
            <a href="{{ $url ?? '#' }}" class="btn-show">Lihat Semua</a>
        @endif
    </div>

    <div class="table-responsive-admin">
        <table class="table">

            <!-- header -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Lapangan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>

            <!-- body -->
            <tbody>

            @forelse($data as $item)
                <tr>
                    @php
                        $rawNumericId = $item['id'] ?? (isset($item['id_booking']) ? preg_replace('/\D/', '', (string)$item['id_booking']) : null);
                        $displayBookingId = 'BK-' . str_pad((int) ($rawNumericId ?: 0), 6, '0', STR_PAD_LEFT);
                    @endphp
                    <td>{{ $displayBookingId }}</td>
                    <td>{{ $item['user']['nama'] ?? '-' }}</td>
                    <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M') }}
                    </td>

                    <!-- status -->
                    <td>
                        @if($item['status_pembayaran'] == 'confirmed')
                            <span class="badge bg-success px-3 py-2">Lunas</span>
                        @elseif($item['status_pembayaran'] == 'pending')
                            <span class="badge bg-warning px-3 py-2">Pending</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Expired</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-box-open mb-2"></i><br>
                        Belum ada booking hari ini
                    </td>
                </tr>
            @endforelse
        </tbody>

        </table>
    </div>

</div>
