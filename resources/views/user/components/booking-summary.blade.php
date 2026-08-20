<div class="booking-summary-card">
    <div class="summary-header">
        <span class="step-badge">Langkah 1</span>
        <h6 class="fw-bold m-0">Atur Waktu Booking</h6>
    </div>

    <!-- TANGGAL (Ubah type jadi text & tambah placeholder) -->
    <div class="form-group mb-3">
        <label for="tanggal" class="form-label-custom">Pilih Tanggal</label>
        <input type="text" class="form-control-custom" id="tanggal" placeholder="DD-MM-YYYY" readonly>
    </div>

    <!-- JAM -->
    <div class="form-group mb-3">
        <label class="form-label-custom">Pilih Jam Mulai</label>
        <div class="jadwal-grid">
            @for ($i = 6; $i <= 23; $i++)
                <button type="button" class="btn jam-btn" data-jam="{{ sprintf('%02d:00', $i) }}">
                    <span class="jam-text">{{ sprintf('%02d:00', $i) }}</span>
                </button>
            @endfor
        </div>
    </div>

    <!-- DURASI -->
    <div class="form-group mb-4">
        <label class="form-label-custom">Durasi Main</label>
        <div class="counter-box">
            <button type="button" class="btn-counter" id="minus">-</button>
            <span class="counter-value"><b id="durasi">1</b> Jam</span>
            <button type="button" class="btn-counter" id="plus">+</button>
        </div>
    </div>

    <!-- SUMMARY DETAIL -->
    <div class="summary-details-box">
        <div class="summary-row">
            <span>Lapangan Selected:</span>
            <strong id="summary-nama" class="text-end">-</strong>
        </div>
        <div class="summary-row">
            <span>Tanggal:</span>
            <strong id="summary-tanggal">-</strong>
        </div>
        <div class="summary-row">
            <span>Jam:</span>
            <strong id="summary-jam">-</strong>
        </div>
        <div class="summary-row">
            <span>Durasi:</span>
            <strong><span id="summary-durasi">1</span> Jam</strong>
        </div>

        <hr class="summary-divider">

        <div class="summary-row total-row">
            <span>Total Bayar</span>
            <strong id="total" class="text-danger">Rp0</strong>
        </div>
    </div>

    <button id="btnBooking" class="btn btn-booking-submit w-100 mt-4">
        Lanjut Pembayaran →
    </button>
</div>