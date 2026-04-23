<div class="harga-wrapper">
    <h5 class="fw-bold mb-3">Pilih Lapangan</h5>

    @foreach($lapangan ?? [] as $item)
        <div class="card-lapangan" data-id="{{ $item['id_lapangan'] }}" data-nama="{{ $item['nama_lapangan'] }}"
            data-harga="{{ $item['harga_per_jam'] }}">

            <div class="flex-grow-1">
                <h6 class="mb-1">{{ $item['nama_lapangan'] }}</h6>
                <small class="text-muted">{{ $item['deskripsi'] }}</small>
            </div>

            <div class="harga">
                Rp{{ number_format($item['harga_per_jam'], 0, ',', '.') }}
            </div>
        </div>
    @endforeach
</div>