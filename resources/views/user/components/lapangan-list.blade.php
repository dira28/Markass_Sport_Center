<div class="harga-wrapper">
    <h5 class="fw-bold mb-3">Pilih Lapangan</h5>

    @foreach($lapangan ?? [] as $item)
        <div class="card-lapangan" data-id="{{ $item['id_lapangan'] }}" data-nama="{{ $item['nama_lapangan'] }}"
            data-harga-pagi="{{ $item['harga_pagi'] }}" data-harga-malam="{{ $item['harga_malam'] }}">

            <div class="flex-grow-1">
                <h6 class="mb-1 fw-bold" style="font-size: 16px;">{{ $item['nama_lapangan'] }}</h6>
                <div class="mb-2">
                    <small class="text-muted d-block mb-2" style="font-size: 13px;">
                        {{ $item['deskripsi'] ?? '-' }}
                    </small>

                    <!-- JAM OPERASIONAL (VERSI LEBIH GEDE & JELAS) -->
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        <span class="badge bg-light text-dark border"
                            style="font-size: 13px; padding: 6px 10px; font-weight: 600;">
                            ☀️ Pagi: 09:00 - 15:00
                        </span>
                        <span class="badge bg-dark text-white"
                            style="font-size: 13px; padding: 6px 10px; font-weight: 600;">
                            🌙 Malam: 16:00 - 23:00
                        </span>
                    </div>
                </div>
            </div>

            <div class="harga text-end">
                <div>
                    <small class="text-muted" style="font-size: 12px;">Pagi</small><br>
                    <b style="font-size: 15px;">Rp{{ number_format($item['harga_pagi'] ?? 0, 0, ',', '.') }}</b>
                </div>

                <div class="mt-2">
                    <small class="text-muted" style="font-size: 12px;">Malam</small><br>
                    <b style="font-size: 15px;">Rp{{ number_format($item['harga_malam'] ?? 0, 0, ',', '.') }}</b>
                </div>
            </div>

        </div>
    @endforeach
</div>