<div class="harga-wrapper">
    <h5 class="fw-bold mb-3 text-dark fs-6">Pilih Lapangan</h5>

    @forelse($lapangan ?? [] as $item)
        <div class="card-lapangan" data-id="{{ $item['id_lapangan'] }}" data-nama="{{ $item['nama_lapangan'] }}"
            data-harga-pagi="{{ $item['harga_pagi'] }}" data-harga-malam="{{ $item['harga_malam'] }}">

            <!-- 1. HEADER (JUDUL & PANAH) - ATAS -->
            <div class="lapangan-header">
                <div class="header-text">
                    <h6 class="title-lapangan">{{ $item['nama_lapangan'] }}</h6>
                    <small class="sub-title">Klik untuk lihat foto & detail harga</small>
                </div>
                <div class="chevron-icon">▼</div>
            </div>

            <!-- 2. DETAIL (FOTO & HARGA) - BAWAH -->
            <div class="lapangan-detail">
                <div class="lapangan-detail-inner">

                    <!-- KIRI: FOTO LAPANGAN -->
                    <div class="lapangan-img-wrapper">
                        <img src="{{ !empty($item['foto']) ? asset('storage/' . $item['foto']) : 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=600&auto=format&fit=crop' }}"
                            alt="{{ $item['nama_lapangan'] }}" class="lapangan-img">
                    </div>

                    <!-- KANAN: DESKRIPSI & HARGA -->
                    <div class="lapangan-info-wrapper">
                        <p class="desc-lapangan">
                            {{ $item['deskripsi'] ?? 'Lapangan berkualitas dengan fasilitas lengkap, cocok untuk latihan maupun pertandingan.' }}
                        </p>

                        <div class="harga">
                            <div class="harga-box">
                                <span class="label-jam">Pagi (07:00 - 16:00)</span>
                                <b>Rp{{ number_format($item['harga_pagi'] ?? 0, 0, ',', '.') }}<small>/jam</small></b>
                            </div>

                            <div class="harga-box">
                                <span class="label-jam">Malam (16:00 - 24:00)</span>
                                <b>Rp{{ number_format($item['harga_malam'] ?? 0, 0, ',', '.') }}<small>/jam</small></b>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    @empty
        <div class="text-center py-3 text-muted small">
            Lapangan belum tersedia
        </div>
    @endforelse
</div>