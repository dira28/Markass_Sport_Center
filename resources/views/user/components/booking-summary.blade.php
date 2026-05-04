<div class="booking-summary">

    <h6 class="fw-bold mb-3">Atur Jadwal Booking</h6>

    <!-- TANGGAL -->
    <input type="date" class="form-control mb-3" id="tanggal">

    <!-- JAM -->
    <div class="jadwal mb-3">
        @for ($i = 6; $i <= 23; $i++)
            <button class="btn btn-light btn-sm jam-btn" data-jam="{{ sprintf('%02d:00', $i) }}">
                {{ sprintf('%02d:00', $i) }}
            </button>
        @endfor
    </div>

    <!-- DURASI -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <button class="btn btn-light" id="minus">-</button>
        <b><span id="durasi">1</span> Jam</b>
        <button class="btn btn-light" id="plus">+</button>
    </div>

    <!-- HARGA AKTIF -->
    <div class="d-flex justify-content-between mb-2">
        <small>Harga / Jam</small>
        <small id="harga-perjam">Rp0</small>
    </div>

    <hr>

    <!-- SUMMARY -->
    <div class="d-flex justify-content-between">
        <small>Lapangan</small>
        <small id="summary-nama">-</small>
    </div>

    <div class="d-flex justify-content-between">
        <small>Tanggal</small>
        <small id="summary-tanggal">-</small>
    </div>

    <div class="d-flex justify-content-between">
        <small>Jam</small>
        <small id="summary-jam">-</small>
    </div>

    <div class="d-flex justify-content-between">
        <small>Durasi</small>
        <small><span id="summary-durasi">1</span> jam</small>
    </div>

    <hr>

    <!-- TOTAL -->
    <div class="d-flex justify-content-between fw-bold">
        <span>Total Harga</span>
        <span id="total">Rp0</span>
    </div>

    <button id="btnBooking" class="btn btn-red w-100 mt-3">
        Booking Sekarang →
    </button>

    <!-- PAYMENT CARD -->
    <div id="paymentCard" class="payment-card" style="display: none;">
        <h5>Complete Payment</h5>
        <p id="paymentSubtitle">Scan QR DANA di bawah dan upload bukti pembayaran</p>
        
        <div class="qr-container">
            <img src="/images/qr-dana.jpg" alt="QR DANA" />
        </div>

        <div class="total-display">
            <div>Total Pembayaran</div>
            <div class="amount" id="paymentTotal">Rp 0</div>
        </div>

        <div class="upload-box" id="uploadBox">
            <i class="fas fa-cloud-upload-alt text-muted" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
            <div>
                <strong>Klik atau drag gambar bukti pembayaran</strong>
                <p class="text-muted small mb-0">JPG, PNG max 2MB</p>
            </div>
            <input type="file" id="proofFile" accept="image/*" class="d-none">
            <button class="payment-btn" id="uploadBtn" disabled>Upload Bukti</button>
        </div>

        <div id="uploadPreview" style="display: none;">
            <img class="upload-preview" id="previewImg">
            <div class="payment-badge menunggu-verifikasi mt-2 d-inline-block">
                Menunggu Verifikasi Admin
            </div>
        </div>

        <div class="payment-badge pending mt-3 d-inline-block" id="statusBadge">
            Pending
        </div>
    </div>

</div>
