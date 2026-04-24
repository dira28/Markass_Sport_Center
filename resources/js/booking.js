document.addEventListener("DOMContentLoaded", function () {

    // ===== STATE =====
    let lapanganAktif = null;
    let hargaPagi = 0;
    let hargaMalam = 0;
    let hargaPerJam = 0;

    let jamAktif = null;
    let durasi = 1;

    let controller = null; // 🔥 anti tabrakan request

    // ===== ELEMENT =====
    const summaryNama = document.getElementById("summary-nama");
    const summaryTanggal = document.getElementById("summary-tanggal");
    const summaryJam = document.getElementById("summary-jam");
    const totalHarga = document.getElementById("total");

    const durasiEl = document.getElementById("durasi");
    const summaryDurasi = document.getElementById("summary-durasi");

    const tanggalInput = document.getElementById("tanggal");

    // ===== FORMAT JAM =====
    function formatJamRange(start, durasi) {
        if (!start) return "-";

        let s = parseInt(start.split(":")[0]);
        let e = s + durasi;

        return `${String(s).padStart(2, "0")}:00 - ${String(e).padStart(2, "0")}:00`;
    }

    // ===== VALIDASI RANGE =====
    function isRangeValid(start, durasi) {
        let s = parseInt(start.split(":")[0]);
        let e = s + durasi;

        let valid = true;

        document.querySelectorAll(".jam-btn").forEach(btn => {
            let jam = parseInt(btn.dataset.jam.split(":")[0]);

            if (jam >= s && jam < e) {
                if (btn.classList.contains("jam-booked")) {
                    valid = false;
                }
            }
        });

        return valid;
    }

    // ===== HIGHLIGHT RANGE =====
    function highlightRange() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active-range");
        });

        if (!jamAktif) return;

        if (!isRangeValid(jamAktif, durasi)) {
            durasi = 1;
            updateDurasi();
            alert("Durasi melewati jam yang sudah dibooking");
            return;
        }

        let s = parseInt(jamAktif.split(":")[0]);
        let e = s + durasi;

        document.querySelectorAll(".jam-btn").forEach(btn => {
            let jam = parseInt(btn.dataset.jam.split(":")[0]);

            if (jam >= s && jam < e && !btn.classList.contains("jam-booked")) {
                btn.classList.add("active-range");
            }
        });
    }

    // ===== UPDATE UI =====
    function updateUI() {
        if (!jamAktif || !hargaPerJam) {
            totalHarga.textContent = "Rp0";
            summaryJam.textContent = "-";
            return;
        }

        let total = hargaPerJam * durasi;
        totalHarga.textContent = "Rp" + total.toLocaleString("id-ID");
        summaryJam.textContent = formatJamRange(jamAktif, durasi);
    }

    // ===== RESET SLOT =====
    function resetAllSlots() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("jam-booked", "active", "active-range");
            btn.disabled = false;
        });

        jamAktif = null;
        hargaPerJam = 0;
        updateUI();
    }

    // ===== LOAD SLOT =====
    async function loadAvailability(tanggal) {
        if (!lapanganAktif) return;

        // 🔥 CANCEL REQUEST LAMA
        if (controller) controller.abort();
        controller = new AbortController();

        try {
            console.log("FETCH:", tanggal, lapanganAktif);

            let res = await fetch(`/booking/slots?tanggal=${tanggal}&lapangan_id=${lapanganAktif}`, {
                credentials: "same-origin",
                signal: controller.signal
            });

            let blocked = await res.json();
            console.log("BLOCKED:", blocked);

            resetAllSlots();

            document.querySelectorAll(".jam-btn").forEach(btn => {
                if (blocked.includes(btn.dataset.jam)) {
                    btn.classList.add("jam-booked");
                    btn.disabled = true;
                }
            });

        } catch (err) {
            if (err.name === "AbortError") return;
            console.error("ERROR SLOT:", err);
        }
    }

    // ===== PILIH LAPANGAN =====
    document.querySelectorAll(".card-lapangan").forEach(el => {
        el.addEventListener("click", function () {

            lapanganAktif = this.dataset.id;
            hargaPagi = parseInt(this.dataset.hargaPagi || 0);
            hargaMalam = parseInt(this.dataset.hargaMalam || 0);

            summaryNama.textContent = this.dataset.nama;

            document.querySelectorAll(".card-lapangan").forEach(c => c.classList.remove("active"));
            this.classList.add("active");

            resetAllSlots();

            if (tanggalInput.value) {
                loadAvailability(tanggalInput.value);
            }
        });
    });

    // ===== PILIH JAM =====
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {

            if (this.classList.contains("jam-booked")) return;

            document.querySelectorAll(".jam-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = this.dataset.jam;

            let jam = parseInt(jamAktif.split(":")[0]);
            hargaPerJam = (jam >= 6 && jam < 16) ? hargaPagi : hargaMalam;

            updateUI();
            highlightRange();
        });
    });

    // ===== PILIH TANGGAL =====
    tanggalInput.addEventListener("change", function () {
        let tgl = this.value;

        summaryTanggal.textContent = tgl || "-";

        resetAllSlots();

        if (tgl && lapanganAktif) {
            loadAvailability(tgl);
        }
    });

    // ===== DURASI =====
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
        highlightRange();
    }

    // ===== BOOKING =====
    document.getElementById("btnBooking").addEventListener("click", async function () {

        if (!window.isLoggedIn) {
            alert("Login dulu ya");
            window.location.href = "/login";
            return;
        }

        let tanggal = tanggalInput.value;

        if (!lapanganAktif) return alert("Pilih lapangan dulu");
        if (!tanggal) return alert("Pilih tanggal dulu");
        if (!jamAktif) return alert("Pilih jam dulu");

        if (!isRangeValid(jamAktif, durasi)) {
            return alert("Waktu yang dipilih bentrok dengan booking lain");
        }

        let startHour = parseInt(jamAktif.split(":")[0]);
        let endHour = startHour + durasi;

        let jamSelesai = String(endHour).padStart(2, "0") + ":00";

        try {
            let res = await fetch("/booking", {
                method: "POST",
                credentials: "same-origin",
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
                alert(result.message || "Gagal booking");
            }

        } catch (err) {
            console.error(err);
            alert("Server error");
        }

    });

});