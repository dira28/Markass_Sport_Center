document.addEventListener("DOMContentLoaded", function () {

    // ===== STATE =====
    let lapanganAktif = null;
    let hargaPagi = 0;
    let hargaMalam = 0;
    let hargaPerJam = 0;

    let jamAktif = null;
    let durasi = 1;

    let controller = null;
    let blockedSlots = [];
    let fpInstance = null; // Storing instance Flatpickr

    // ===== ELEMENT =====
    const summaryNama = document.getElementById("summary-nama");
    const summaryTanggal = document.getElementById("summary-tanggal");
    const summaryJam = document.getElementById("summary-jam");
    const totalHarga = document.getElementById("total");

    const durasiEl = document.getElementById("durasi");
    const summaryDurasi = document.getElementById("summary-durasi");
    const tanggalInput = document.getElementById("tanggal");

    // ===== FORMAT INDO TANGGAL =====
    function formatDateIndo(str) {
        if (!str) return "-";
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    // ===== INISIALISASI KALENDER FLATPICKR =====
    function initDatePicker(disabledDates = []) {
        if (fpInstance) fpInstance.destroy(); // Hancurkan instance lama jika ada update

        fpInstance = flatpickr("#tanggal", {
            dateFormat: "Y-m-d",
            minDate: "today", // Tanggal kemarin otomatis abu-abu
            disable: disabledDates, // Array tanggal fully booked ["2026-07-25", "2026-07-28"]
            locale: {
                firstDayOfWeek: 1
            },
            onChange: function (selectedDates, dateStr) {
                summaryTanggal.textContent = dateStr ? formatDateIndo(dateStr) : "-";

                resetSelection();
                blockedSlots = [];

                if (dateStr && lapanganAktif) {
                    loadAvailability(dateStr);
                }
            }
        });
    }

    // Jalankan pertama kali saat halaman di-load
    initDatePicker();

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

        for (let i = s; i < e; i++) {
            let jamStr = String(i).padStart(2, "0") + ":00";
            if (blockedSlots.includes(jamStr)) {
                return false;
            }
        }
        return true;
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

    // ===== RESET SELECTION =====
    function resetSelection() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active", "active-range");
        });

        jamAktif = null;
        hargaPerJam = 0;
        updateUI();
    }

    // ===== LOAD STATUS JAM =====
    async function loadAvailability(tanggal) {
        if (!lapanganAktif) return;

        if (controller) controller.abort();
        controller = new AbortController();

        try {
            let res = await fetch(`/booking/status-jam?tanggal=${tanggal}&lapangan_id=${lapanganAktif}`, {
                credentials: "same-origin",
                signal: controller.signal
            });

            let statusData = await res.json();

            blockedSlots = statusData
                .filter(item => {
                    const s = (item.status_pembayaran ?? item.status ?? '').toLowerCase();
                    if (s.includes('expired') || s.includes('cancelled')) return false;

                    return ['pending', 'waiting_confirmation', 'confirmed', 'paid', 'menunggu_verifikasi', 'approve', 'terbooking'].includes(s);
                })
                .map(item => item.jam);

            resetSelection();

            document.querySelectorAll(".jam-btn").forEach(btn => {
                const jam = btn.dataset.jam;
                const blocked = blockedSlots.includes(jam);

                if (blocked) {
                    btn.disabled = true;
                    btn.classList.add("jam-booked");
                    btn.innerHTML = `<span class="jam-text">${jam}</span><span class="jam-booked-label">BOOKED</span>`;
                } else {
                    btn.disabled = false;
                    btn.classList.remove("jam-booked");
                    btn.innerHTML = `<span class="jam-text">${jam}</span>`;
                }
            });

        } catch (err) {
            if (err.name === "AbortError") return;
            console.error("ERROR STATUS JAM:", err);
            blockedSlots = [];
        }
    }

    // ===== PILIH LAPANGAN =====
    document.querySelectorAll(".card-lapangan").forEach(el => {
        el.addEventListener("click", async function () {

            const isAlreadyActive = this.classList.contains("active");

            // 1. Reset visual card & accordion
            document.querySelectorAll(".card-lapangan").forEach(c => {
                c.classList.remove("active");
                const detail = c.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = null;
            });

            if (!isAlreadyActive) {
                this.classList.add("active");

                const detail = this.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = detail.scrollHeight + "px";

                // 2. Set data lapangan baru
                lapanganAktif = this.dataset.id;
                hargaPagi = parseInt(this.dataset.hargaPagi || 0);
                hargaMalam = parseInt(this.dataset.hargaMalam || 0);

                summaryNama.textContent = this.dataset.nama;

                // 3. RESET PILIHAN JAM (Jam terlepas, TAPI angka durasi TIDAK di-reset)
                jamAktif = null;
                hargaPerJam = 0;
                blockedSlots = [];

                // Lepas highlight/active dari semua tombol jam
                document.querySelectorAll(".jam-btn").forEach(btn => {
                    btn.classList.remove("active", "active-range");
                });

                // Update teks ringkasan (Total Rp0 & Jam jadi "-")
                updateUI();

                // 4. FETCH TANGGAL FULLY BOOKED UNTUK LAPANGAN BARU
                try {
                    let res = await fetch(`/booking/fully-booked-dates?lapangan_id=${lapanganAktif}`);
                    let fullyBookedDates = await res.json();

                    // Update Kalender Flatpickr
                    initDatePicker(fullyBookedDates);
                } catch (err) {
                    console.error("Gagal load fully booked dates:", err);
                }

                // 5. Jika tanggal sudah terpilih sebelumnya, reload availability jam untuk lapangan baru ini
                if (tanggalInput.value) {
                    loadAvailability(tanggalInput.value);
                }

            } else {
                // Jika lapangan yang sama diklik lagi (Deselect / tutup accordion)
                lapanganAktif = null;
                hargaPagi = 0;
                hargaMalam = 0;
                summaryNama.textContent = "-";

                jamAktif = null;
                hargaPerJam = 0;
                updateUI();

                initDatePicker([]); // Reset kalender kembali normal
            }
        });
    });

    // ===== PILIH JAM =====
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            if (this.disabled) return;

            const start = this.dataset.jam;
            if (!start) return;

            if (!isRangeValid(start, durasi)) return;

            document.querySelectorAll(".jam-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = start;

            let jam = parseInt(jamAktif.split(":")[0]);
            hargaPerJam = (jam >= 6 && jam < 16) ? hargaPagi : hargaMalam;

            updateUI();
            highlightRange();
        });
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

    // ===== SUBMIT BOOKING =====
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
                const bookingId = result?.data?.data?.id_booking || result?.data?.id_booking;
                if (!bookingId) {
                    alert('Booking berhasil, tapi ID booking tidak ditemukan.');
                    return;
                }
                window.location.href = `/booking/payment/${bookingId}`;
            } else {
                alert(result.message || "Gagal booking");
            }

        } catch (err) {
            console.error(err);
            alert("Server error");
        }
    });

});