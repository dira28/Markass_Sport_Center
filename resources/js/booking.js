document.addEventListener("DOMContentLoaded", function () {

    // ===== STATE MANAGEMENT =====
    let lapanganAktif = null;
    let hargaPagi = 0;
    let hargaMalam = 0;
    let hargaPerJam = 0;

    let jamAktif = null;
    let durasi = 1;

    let controller = null;
    let blockedSlots = [];
    let fpInstance = null;

    // ===== ELEMENT SELECTION =====
    const summaryNama = document.getElementById("summary-nama");
    const summaryTanggal = document.getElementById("summary-tanggal");
    const summaryJam = document.getElementById("summary-jam");
    const totalHarga = document.getElementById("total");

    const durasiEl = document.getElementById("durasi");
    const summaryDurasi = document.getElementById("summary-durasi");
    const tanggalInput = document.getElementById("tanggal");

    // ===== FORMAT TANGGAL INDONESIA =====
    function formatDateIndo(str) {
        if (!str) return "-";
        const d = new Date(str);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    // ===== INISIALISASI FLATPICKR =====
    function initDatePicker(disabledDates = []) {
        if (fpInstance) fpInstance.destroy();

        if (document.getElementById("tanggal")) {
            fpInstance = flatpickr("#tanggal", {
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: disabledDates,
                locale: { firstDayOfWeek: 1 },
                onChange: function (selectedDates, dateStr) {
                    if (summaryTanggal) summaryTanggal.textContent = dateStr ? formatDateIndo(dateStr) : "-";

                    resetSelection();
                    blockedSlots = [];

                    if (dateStr && lapanganAktif) {
                        loadAvailability(dateStr);
                    }
                }
            });
        }
    }

    initDatePicker();

    // ===== FORMAT JAM RANGE =====
    function formatJamRange(start, durasi) {
        if (!start) return "-";
        let s = parseInt(start.split(":")[0]);
        let e = s + durasi;
        return `${String(s).padStart(2, "0")}:00 - ${String(e).padStart(2, "0")}:00`;
    }

    // ===== VALIDASI BENTROK JAM =====
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

    // ===== HIGHLIGHT JAM DISOROT =====
    function highlightRange() {
        document.querySelectorAll(".jam-btn").forEach(btn => btn.classList.remove("active-range"));

        if (!jamAktif) return;

        if (!isRangeValid(jamAktif, durasi)) {
            durasi = 1;
            updateDurasi();

            Swal.fire({
                icon: 'warning',
                title: 'Durasi Melebihi Slot Kosong',
                text: 'Durasi yang kamu pilih melewati jam yang sudah dibooking orang lain.',
                confirmButtonColor: '#dc3545'
            });
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

    // ===== UPDATE TOTAL DAN RINGKASAN UI =====
    function updateUI() {
        if (!totalHarga || !summaryJam) return;

        if (!jamAktif || !hargaPerJam) {
            totalHarga.textContent = "Rp0";
            summaryJam.textContent = "-";
            return;
        }

        let total = hargaPerJam * durasi;
        totalHarga.textContent = "Rp" + total.toLocaleString("id-ID");
        summaryJam.textContent = formatJamRange(jamAktif, durasi);
    }

    // ===== RESET PILIHAN JAM =====
    function resetSelection() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active", "active-range");
        });

        jamAktif = null;
        hargaPerJam = 0;
        updateUI();
    }

    // ===== FETCH AVAILABILITY JAM DARI SERVER (FIXED & ENHANCED) =====
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

            // Saring dan kumpulkan semua jam yang sudah terisi
            let bookedArray = [];

            statusData.forEach(item => {
                const s = String(item.status_pembayaran ?? item.status ?? '').toLowerCase();

                // Abaikan jika status batal/expired/ditolak
                if (s.includes('expired') || s.includes('cancelled') || s.includes('batal') || s.includes('failed') || s.includes('reject')) {
                    return;
                }

                // Ambil jam mulai & jam selesai
                let jamMulai = item.jam_mulai ?? item.jam ?? item.waktu_mulai;
                let jamSelesai = item.jam_selesai ?? item.waktu_selesai;

                if (jamMulai) {
                    let startHour = parseInt(String(jamMulai).split(":")[0]);
                    let endHour = jamSelesai ? parseInt(String(jamSelesai).split(":")[0]) : startHour + 1;

                    // Expand semua slot jam (misal 07:00 s/d 09:00 -> masukan 07:00 dan 08:00)
                    for (let h = startHour; h < endHour; h++) {
                        let formattedSlot = String(h).padStart(2, "0") + ":00";
                        if (!bookedArray.includes(formattedSlot)) {
                            bookedArray.push(formattedSlot);
                        }
                    }
                }
            });

            blockedSlots = bookedArray;

            resetSelection();

            // Terapkan perubahan pada setiap tombol jam di UI
            document.querySelectorAll(".jam-btn").forEach(btn => {
                const jam = btn.dataset.jam; // Format di HTML contoh: "07:00"
                const blocked = blockedSlots.includes(jam);

                if (blocked) {
                    btn.disabled = true;
                    btn.classList.add("jam-booked");
                    btn.classList.remove("active", "active-range");
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

    // ===== EVENT LISTENERS: PILIH LAPANGAN =====
    document.querySelectorAll(".card-lapangan").forEach(el => {
        el.addEventListener("click", async function () {
            const isAlreadyActive = this.classList.contains("active");

            document.querySelectorAll(".card-lapangan").forEach(c => {
                c.classList.remove("active");
                const detail = c.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = null;
            });

            if (!isAlreadyActive) {
                this.classList.add("active");

                const detail = this.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = detail.scrollHeight + "px";

                lapanganAktif = this.dataset.id;
                hargaPagi = parseInt(this.dataset.hargaPagi || 0);
                hargaMalam = parseInt(this.dataset.hargaMalam || 0);

                if (summaryNama) summaryNama.textContent = this.dataset.nama;

                jamAktif = null;
                hargaPerJam = 0;
                blockedSlots = [];

                document.querySelectorAll(".jam-btn").forEach(btn => {
                    btn.classList.remove("active", "active-range");
                });

                updateUI();

                // Load ketersediaan jam untuk tanggal yang terpilih saat ini
                if (tanggalInput && tanggalInput.value) {
                    loadAvailability(tanggalInput.value);
                }

                try {
                    let res = await fetch(`/booking/fully-booked-dates?lapangan_id=${lapanganAktif}`);
                    let fullyBookedDates = await res.json();
                    initDatePicker(fullyBookedDates);
                } catch (err) {
                    console.error("Gagal load fully booked dates:", err);
                }

            } else {
                lapanganAktif = null;
                hargaPagi = 0;
                hargaMalam = 0;
                if (summaryNama) summaryNama.textContent = "-";

                jamAktif = null;
                hargaPerJam = 0;
                updateUI();

                // Reset status tombol jam jika lapangan dibatalkan pilihannya
                document.querySelectorAll(".jam-btn").forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove("jam-booked", "active", "active-range");
                    if (btn.dataset.jam) {
                        btn.innerHTML = `<span class="jam-text">${btn.dataset.jam}</span>`;
                    }
                });

                initDatePicker([]);
            }
        });
    });

    // ===== EVENT LISTENERS: PILIH JAM =====
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            if (this.disabled || this.classList.contains("jam-booked")) return;

            const start = this.dataset.jam;
            if (!start) return;

            if (!isRangeValid(start, durasi)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jadwal Bentrok',
                    text: `Durasi ${durasi} jam pilihanmu menabrak jadwal lain yang sudah di-booking.`,
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            document.querySelectorAll(".jam-btn").forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            jamAktif = start;

            let jam = parseInt(jamAktif.split(":")[0]);
            hargaPerJam = (jam >= 6 && jam < 16) ? hargaPagi : hargaMalam;

            updateUI();
            highlightRange();
        });
    });

    // ===== DURASI KONTROL =====
    const plusBtn = document.getElementById("plus");
    const minusBtn = document.getElementById("minus");

    if (plusBtn) {
        plusBtn.onclick = () => {
            durasi++;
            updateDurasi();
        };
    }

    if (minusBtn) {
        minusBtn.onclick = () => {
            if (durasi > 1) durasi--;
            updateDurasi();
        };
    }

    function updateDurasi() {
        if (durasiEl) durasiEl.textContent = durasi;
        if (summaryDurasi) summaryDurasi.textContent = durasi;
        updateUI();
        highlightRange();
    }

    // ===== SUBMIT BOOKING =====
    const btnBooking = document.getElementById("btnBooking");
    if (btnBooking) {
        btnBooking.addEventListener("click", async function () {

            // 1. Validasi Login
            if (!window.isLoggedIn) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perlu Login',
                    text: 'Silakan login terlebih dahulu untuk melakukan booking.',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Login Sekarang'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "/login";
                    }
                });
                return;
            }

            let tanggal = tanggalInput ? tanggalInput.value : null;

            // 2. Validasi Form
            if (!lapanganAktif) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Pilih Lapangan',
                    text: 'Silakan klik salah satu lapangan terlebih dahulu.',
                    confirmButtonColor: '#dc3545'
                });
            }
            if (!tanggal) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Pilih Tanggal',
                    text: 'Silakan tentukan tanggal main.',
                    confirmButtonColor: '#dc3545'
                });
            }
            if (!jamAktif) {
                return Swal.fire({
                    icon: 'info',
                    title: 'Pilih Jam',
                    text: 'Silakan tentukan jam mulai bermain.',
                    confirmButtonColor: '#dc3545'
                });
            }

            if (!isRangeValid(jamAktif, durasi)) {
                return Swal.fire({
                    icon: 'error',
                    title: 'Jadwal Bentrok',
                    text: 'Waktu yang dipilih sudah di-booking oleh orang lain.',
                    confirmButtonColor: '#dc3545'
                });
            }

            let startHour = parseInt(jamAktif.split(":")[0]);
            let endHour = startHour + durasi;
            let jamSelesai = String(endHour).padStart(2, "0") + ":00";
            let namaLap = summaryNama ? summaryNama.textContent : "Lapangan";
            let totalBayar = totalHarga ? totalHarga.textContent : "Rp0";

            // 3. Pop-up Konfirmasi
            const confirmResult = await Swal.fire({
                title: 'Konfirmasi Pesanan',
                html: `
                    <div style="text-align: left; background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 14px; color: #212529;">
                        <p class="mb-1"><strong>Lapangan:</strong> ${namaLap}</p>
                        <p class="mb-1"><strong>Tanggal:</strong> ${formatDateIndo(tanggal)}</p>
                        <p class="mb-1"><strong>Waktu:</strong> ${jamAktif} - ${jamSelesai} (${durasi} Jam)</p>
                        <hr class="my-2">
                        <p class="mb-0 text-danger fw-bold" style="font-size: 16px;">Total Bayar: ${totalBayar}</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<span style="color: #ffffff; font-weight: bold;">Ya, Lanjut Pembayaran</span>',
                cancelButtonText: '<span style="color: #ffffff; font-weight: bold;">Cek Kembali</span>'
            });

            if (!confirmResult.isConfirmed) return;

            // 4. Loading State
            Swal.fire({
                title: 'Memproses Pesanan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // 5. Kirim Request
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
                        Swal.fire({
                            icon: 'warning',
                            title: 'Booking Berhasil',
                            text: 'Booking berhasil, namun ID booking tidak terdeteksi.',
                            confirmButtonColor: '#dc3545'
                        });
                        return;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Berhasil!',
                        text: 'Mengarahkan ke halaman pembayaran...',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = `/booking/payment/${bookingId}`;
                    });

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Booking',
                        text: result.message || "Gagal membuat pesanan.",
                        confirmButtonColor: '#dc3545'
                    });
                }

            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Sistem',
                    text: 'Terjadi kesalahan pada server. Silakan coba lagi.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

});