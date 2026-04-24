<div class="harga-wrapper">
    <h5 class="fw-bold mb-3">Pilih Lapangan</h5>

    @forelse($lapangan ?? [] as $item)
        <div class="card-lapangan" data-id="{{ $item['id_lapangan'] }}" data-nama="{{ $item['nama_lapangan'] }}"
            data-harga-pagi="{{ $item['harga_pagi'] }}" data-harga-malam="{{ $item['harga_malam'] }}">

            <!-- INFO -->
            <div class="lapangan-info">
                <h6>{{ $item['nama_lapangan'] }}</h6>

                <small class="desc-lapangan">
                    {{ $item['deskripsi'] ?? 'Lapangan berkualitas dengan fasilitas lengkap, cocok untuk latihan maupun pertandingan bersama tim.' }}
                </small>

                <!-- JAM -->
                <div class="info-jam">
                    <span class="jam-pagi">Pagi (07:00 - 16:00)</span>
                    <span class="jam-malam">Malam (16:00 - 24:00)</span>
                </div>
            </div>

            <!-- HARGA -->
            <div class="harga">
                <div class="harga-box">
                    <small>Pagi</small>
                    <b>Rp{{ number_format($item['harga_pagi'] ?? 0, 0, ',', '.') }}</b>
                </div>

                <div class="harga-box">
                    <small>Malam</small>
                    <b>Rp{{ number_format($item['harga_malam'] ?? 0, 0, ',', '.') }}</b>
                </div>
            </div>

        </div>
    @empty
        <div class="empty-state">
            Lapangan belum tersedia
        </div>
    @endforelse
</div>