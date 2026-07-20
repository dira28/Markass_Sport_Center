<div class="booking-summary">

    <h6 class="fw-bold mb-3">Atur Jadwal Booking</h6>

    <!-- TANGGAL -->
    <input type="date" class="form-control mb-3" id="tanggal">

    <!-- JAM -->
    <div class="jadwal mb-3">
        @for ($i = 9; $i <= 23; $i++)
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

    <!-- BUTTON -->
    <button id="btnBooking" class="btn btn-red w-100 mt-3">
        Booking Sekarang →
    </button>

</div>