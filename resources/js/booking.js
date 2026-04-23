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

    // FORMAT RANGE JAM
    function formatJamRange(start, durasi) {
        if (!start) return "-";

        let startHour = parseInt(start.split(":")[0]);
        let endHour = startHour + durasi;

        let startText = (startHour < 10 ? "0" : "") + startHour + ":00";
        let endText = (endHour < 10 ? "0" : "") + endHour + ":00";

        return startText + " - " + endText;
    }

    function updateUI() {
        if (!jamAktif || !hargaPerJam) {
            totalHarga.textContent = "Rp0";
            summaryJam.textContent = "-";
            return;
        }

        totalHarga.textContent =
            "Rp" + (hargaPerJam * durasi).toLocaleString("id-ID");

        // UPDATE RANGE JAM
        summaryJam.textContent = formatJamRange(jamAktif, durasi);
    }

    function resetJam() {
        jamAktif = null;
        summaryJam.textContent = "-";

        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active");
        });
    }

    async function loadAvailability(tanggal) {
        try {
            let res = await fetch(`/booking/slots?tanggal=${tanggal}&lapangan_id=${lapanganAktif}`);
            let blocked = await res.json();

            document.querySelectorAll(".jam-btn").forEach(btn => {
                btn.classList.remove("jam-booked", "active");
                btn.classList.add("btn-light");
                btn.disabled = false;
            });

            resetJam();

            document.querySelectorAll(".jam-btn").forEach(btn => {
                if (blocked.includes(btn.dataset.jam)) {
                    btn.classList.add("jam-booked");
                    btn.disabled = true;

                    if (jamAktif === btn.dataset.jam) {
                        jamAktif = null;
                        summaryJam.textContent = "-";
                    }
                }
            });

        } catch (err) {
            console.error("ERROR SLOT:", err);
        }
    }

    // PILIH LAPANGAN
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

    // PILIH JAM
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            if (this.classList.contains("jam-booked")) return;

            document.querySelectorAll(".jam-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = this.dataset.jam;

            updateUI();
        });
    });

    // PILIH TANGGAL
    document.getElementById("tanggal").addEventListener("change", function () {
        let tanggal = this.value;

        summaryTanggal.textContent = tanggal;

        if (tanggal) {
            loadAvailability(tanggal);
        }

        updateUI();
    });

    // DURASI
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

    // BOOKING
    document.getElementById("btnBooking").addEventListener("click", async function () {

        let tanggal = document.getElementById("tanggal").value;

        if (!tanggal) return alert("Pilih tanggal dulu");
        if (!jamAktif) return alert("Pilih jam dulu");

        let startHour = parseInt(jamAktif);
        let endHour = startHour + durasi;

        let jamSelesai = (endHour < 10 ? "0" : "") + endHour + ":00";

        try {
            let res = await fetch("/booking", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
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
                alert("Booking berhasil");
                loadAvailability(tanggal);
            } else {
                alert(result.message);
            }

        } catch (err) {
            alert("Server error");
        }

    });

});