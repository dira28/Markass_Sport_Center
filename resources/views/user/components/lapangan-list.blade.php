<div class="harga-wrapper">
    <h5 class="fw-bold mb-3">Pilih Lapangan</h5>

    @foreach($lapangan ?? [] as $item)
        <div class="card-lapangan" data-id="{{ $item['id_lapangan'] }}" data-nama="{{ $item['nama_lapangan'] }}"
            data-harga-pagi="{{ $item['harga_pagi'] }}" data-harga-malam="{{ $item['harga_malam'] }}">

            <div class="flex-grow-1">
                <h6 class="mb-1">{{ $item['nama_lapangan'] }}</h6>
                <small class="text-muted">
                    {{ $item['deskripsi'] ?? '-' }}
                </small>
            </div>

            <div class="harga text-end">
                <div>
                    <small class="text-muted">Pagi</small><br>
                    <b>Rp{{ number_format($item['harga_pagi'] ?? 0, 0, ',', '.') }}</b>
                </div>

                <div class="mt-1">
                    <small class="text-muted">Malam</small><br>
                    <b>Rp{{ number_format($item['harga_malam'] ?? 0, 0, ',', '.') }}</b>
                </div>
            </div>

        </div>
    @endforeach
</div>