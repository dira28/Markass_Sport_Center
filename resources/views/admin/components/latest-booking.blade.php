<div class="card-box">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5>{{ $title ?? 'Latest Bookings' }}</h5>

        @if(isset($showButton) && $showButton)
            <a href="{{ $url ?? '#' }}" class="btn-show">Lihat Semua</a>
        @endif
    </div>

    <table class="table">
        @forelse($data as $item)
            <tr>
                <td>{{ $item['id_booking'] }}</td>
                <td>{{ $item['user']['nama'] ?? '-' }}</td>
                <td>{{ $item['lapangan']['nama_lapangan'] ?? '-' }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($item['tanggal'])->format('d M') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="fas fa-box-open mb-2"></i><br>
                    Belum ada booking hari ini
                </td>
            </tr>
        @endforelse
    </table>

</div>