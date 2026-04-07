@extends('layouts.app')

@section('title', 'Booking')

@push('styles')
@vite('resources/css/user/pages/booking.css')
@endpush

@section('content')

<section class="hero-booking">
    <div class="container">
        <h2 class="fw-bold">Harga Sewa Lapangan<br>Olahraga Kudus!</h2>
        <p>Lihat harga sewa lapangan favoritmu di Markass Sport Center Kudus</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row row-equal">

            <!-- KIRI -->
            <div class="col-md-8 col-left">
                <div class="harga-wrapper">
                    <h5 class="fw-bold mb-3">Pilih Lapangan</h5>

                    @foreach($lapangan ?? [] as $i => $item)
                    <div class="card-lapangan {{ $i==0 ? 'active' : '' }}"
                        data-id="{{ $item['id_lapangan'] }}"
                        data-nama="{{ $item['nama_lapangan'] }}"
                        data-harga="{{ $item['harga_per_jam'] }}">


                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $item['nama_lapangan'] }}</h6>
                            <small class="text-muted">{{ $item['deskripsi'] }}</small>
                        </div>

                        <div class="harga">
                            Rp{{ number_format($item['harga_per_jam'],0,',','.') }}
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>

            <!-- KANAN -->
            <div class="col-md-4 col-right">
                <div class="booking-summary shadow-sm">

                    <h6 class="fw-bold mb-3">Ringkasan Booking</h6>

                    <div class="d-flex mb-3">
                        @foreach($lapangan ?? [] as $i => $item)
                        <button class="sport btn {{ $i==0 ? 'btn-outline-danger active' : 'btn-outline-secondary' }} w-50 me-2"
                            data-id="{{ $item['id_lapangan'] }}"
                            data-nama="{{ $item['nama_lapangan'] }}"
                            data-harga="{{ $item['harga_per_jam'] }}">
                            {{ $item['nama_lapangan'] }}
                        </button>
                        @endforeach
                    </div>

                    <input type="date" class="form-control mb-3" id="tanggal">

                    <!-- JAM -->
                    <div class="jadwal mb-3">
                        @for ($i = 9; $i <= 23; $i++)
                            <button class="btn btn-light btn-sm jam-btn" data-jam="{{ sprintf('%02d:00',$i) }}">
                                {{ sprintf('%02d:00',$i) }}
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

                    <div class="d-flex justify-content-between">
                        <small>Olahraga</small>
                        <small id="summary-nama">{{ $lapangan[0]['nama_lapangan'] ?? '-' }}</small>
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

                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total Harga</span>
                        <span id="total">Rp0</span>
                    </div>

                    <button id="btnBooking" class="btn btn-red w-100 mt-3">
                        Booking Sekarang →
                    </button>

                </div>
            </div>

        </div>
    </div>
</section>

<style>
.btn-danger {
    background-color: #ff4d4d !important;
    color: white;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    let activeCard = document.querySelector(".card-lapangan.active");

    let lapanganAktif = activeCard?.dataset.id;
    let hargaPerJam = parseInt(activeCard?.dataset.harga || 0);

    let jamAktif = null;
    let durasi = 1;

    let summaryNama = document.getElementById("summary-nama");
    let summaryTanggal = document.getElementById("summary-tanggal");
    let summaryJam = document.getElementById("summary-jam");
    let totalHarga = document.getElementById("total");

    let durasiEl = document.getElementById("durasi");
    let summaryDurasi = document.getElementById("summary-durasi");

    function updateUI() {
        if (!jamAktif || !hargaPerJam) {
            totalHarga.textContent = "Rp0";
            return;
        }

        totalHarga.textContent =
            "Rp" + (hargaPerJam * durasi).toLocaleString("id-ID");
    }

    // 🔥 RESET JAM
    function resetJam() {
        jamAktif = null;
        summaryJam.textContent = "-";

        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active");
        });
    }

    // 🔥 LOAD AVAILABILITY
    async function loadAvailability(tanggal) {
        try {
            let res = await fetch(`/api/lapangan/availability/check?tanggal=${tanggal}`);
            let result = await res.json();

            if (result.status !== "success") return;

            // 🔥 RESET SEMUA JAM
            document.querySelectorAll(".jam-btn").forEach(btn => {
                btn.classList.remove("jam-booked", "active");
                btn.classList.add("btn-light");

                // balikin biar bisa diklik lagi
                btn.style.pointerEvents = "auto";
            });

            resetJam();

            let lapangan = result.data.find(l => l.id_lapangan === lapanganAktif);
            if (!lapangan) return;

            let booked = [];

            // 🔥 CONVERT SLOT
            lapangan.booked_slots.forEach(slot => {
                let [start, end] = slot.split("-");

                let s = parseInt(start.split(":")[0]);
                let e = parseInt(end.split(":")[0]);

                for (let i = s; i < e; i++) {
                    let jam = (i < 10 ? "0" : "") + i + ":00";
                    booked.push(jam);
                }
            });

            // 🔥 APPLY BOOKED STYLE (PINK + DEAD CLICK)
            document.querySelectorAll(".jam-btn").forEach(btn => {
                if (booked.includes(btn.dataset.jam)) {

                    btn.classList.add("jam-booked");

                    // ❌ MATI TOTAL CLICK
                    btn.style.pointerEvents = "none";

                    // kalau sebelumnya kepilih → reset
                    if (jamAktif === btn.dataset.jam) {
                        jamAktif = null;
                        summaryJam.textContent = "-";
                    }
                }
            });

        } catch (err) {
            console.error("ERROR AVAILABILITY:", err);
        }
    }

    // 🔥 PILIH LAPANGAN
    document.querySelectorAll(".sport, .card-lapangan").forEach(el => {
        el.addEventListener("click", function () {

            lapanganAktif = this.dataset.id;
            hargaPerJam = parseInt(this.dataset.harga);
            let nama = this.dataset.nama;

            document.querySelectorAll(".sport").forEach(b => b.classList.remove("active"));
            document.querySelectorAll(".card-lapangan").forEach(c => c.classList.remove("active"));

            document.querySelectorAll(`[data-id="${lapanganAktif}"]`)
                .forEach(x => x.classList.add("active"));

            summaryNama.textContent = nama;

            let tgl = document.getElementById("tanggal").value;
            if (tgl) loadAvailability(tgl);

            updateUI();
        });
    });

    // 🔥 PILIH JAM
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            // kalau udah ke-lock → stop
            if (this.classList.contains("jam-booked")) return;

            document.querySelectorAll(".jam-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = this.dataset.jam;
            summaryJam.textContent = jamAktif;

            updateUI();
        });
    });

    // 🔥 PILIH TANGGAL
    document.getElementById("tanggal").addEventListener("change", function(){
        let tanggal = this.value;

        summaryTanggal.textContent = tanggal;

        if (tanggal) {
            loadAvailability(tanggal);
        }

        updateUI();
    });

    // 🔥 DURASI
    document.getElementById("plus").onclick = () => {
        durasi++;
        updateDurasi();
    };

    document.getElementById("minus").onclick = () => {
        if (durasi > 1) durasi--;
        updateDurasi();
    };

    function updateDurasi() {
        durasiEl.textContent = durasi;
        summaryDurasi.textContent = durasi;
        updateUI();
    }

    // 🔥 BOOKING
    document.getElementById("btnBooking").addEventListener("click", async function () {

        let tanggal = document.getElementById("tanggal").value;

        if (!tanggal) return alert("Pilih tanggal dulu");
        if (!jamAktif) return alert("Pilih jam dulu");

        let startHour = parseInt(jamAktif);
        let endHour = startHour + durasi;

        let jamSelesai = (endHour < 10 ? "0" : "") + endHour + ":00";

        try {
            let res = await fetch("{{ route('booking.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id_lapangan: lapanganAktif,
                    tanggal: tanggal,
                    jam_mulai: jamAktif,
                    jam_selesai: jamSelesai
                })
            });

            let result = await res.json();

            if (result.status === "success") {
                alert("✅ Booking berhasil!");

                // 🔥 reload availability biar langsung ke-lock
                loadAvailability(tanggal);

            } else {
                alert("❌ " + result.message);
            }

        } catch (err) {
            alert("Server error");
        }

    });

});
</script>
@endsection