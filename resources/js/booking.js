document.addEventListener("DOMContentLoaded", function () {

    let lapanganAktif = null;
    let hargaPagi = 0;
    let hargaMalam = 0;

    let jamAktif = null;
    let durasi = 1;

    let controller = null;
    let blockedSlots = [];
    let fpInstance = null;

    const summaryNama = document.getElementById("summary-nama");
    const summaryTanggal = document.getElementById("summary-tanggal");
    const summaryJam = document.getElementById("summary-jam");
    const totalHarga = document.getElementById("total");

    const durasiEl = document.getElementById("durasi");
    const summaryDurasi = document.getElementById("summary-durasi");
    const tanggalInput = document.getElementById("tanggal");

    function formatDateIndo(str) {
        if (!str) return "-";
        const d = new Date(str);
        if (isNaN(d.getTime())) return str; // Fallback jika string format kustom
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function initDatePicker(disabledDates = []) {
        if (fpInstance) fpInstance.destroy();

        if (tanggalInput) {
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

            // Set initial value jika input tanggal sudah berisi data saat load
            if (tanggalInput.value && summaryTanggal) {
                summaryTanggal.textContent = formatDateIndo(tanggalInput.value);
            }
        }
    }

    initDatePicker();

    function formatJamRange(start, durasi) {
        if (!start) return "-";
        let s = parseInt(start.split(":")[0]);
        let e = s + durasi;
        return `${String(s).padStart(2, "0")}:00 - ${String(e).padStart(2, "0")}:00`;
    }

    function isRangeValid(start, durasi) {
        let s = parseInt(start.split(":")[0]);
        let e = s + durasi;

        // Misal batas maksimum jam operasional 24:00
        if (e > 24) return false;

        for (let i = s; i < e; i++) {
            let jamStr = String(i).padStart(2, "0") + ":00";
            if (blockedSlots.includes(jamStr)) {
                return false;
            }
        }
        return true;
    }

    function highlightRange() {
        document.querySelectorAll(".jam-btn").forEach(btn => btn.classList.remove("active-range"));

        if (!jamAktif) return;

        if (!isRangeValid(jamAktif, durasi)) {
            durasi = 1;
            updateDurasi();

            Swal.fire({
                icon: 'warning',
                title: 'Durasi Melebihi Slot Kosong',
                text: 'Durasi yang kamu pilih melewati jam yang sudah dibooking atau melebihi batas waktu.',
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

    // HITUNG AKURAT HARGA LINTAS WAKTU (PAGI & MALAM)
    function hitungTotalHarga(start, durasi) {
        if (!start || (!hargaPagi && !hargaMalam)) return 0;

        let total = 0;
        let startHour = parseInt(start.split(":")[0]);

        for (let i = 0; i < durasi; i++) {
            let currentHour = startHour + i;
            // Pagi: 06:00 - 15:59, Malam: Sisanya
            if (currentHour >= 6 && currentHour < 16) {
                total += hargaPagi;
            } else {
                total += hargaMalam;
            }
        }
        return total;
    }

    function updateUI() {
        if (!totalHarga || !summaryJam) return;

        if (!jamAktif) {
            totalHarga.textContent = "Rp0";
            summaryJam.textContent = "-";
            return;
        }

        let total = hitungTotalHarga(jamAktif, durasi);
        totalHarga.textContent = "Rp" + total.toLocaleString("id-ID");
        summaryJam.textContent = formatJamRange(jamAktif, durasi);
    }

    function resetSelection() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active", "active-range");
        });

        jamAktif = null;
        updateUI();
    }

    // Fetch slot availability from server
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
            let bookedArray = [];

            let dataJam = Array.isArray(statusData) ? statusData : (statusData.data || []);

            if (Array.isArray(dataJam)) {
                dataJam.forEach(item => {
                    const s = String(item.status_pembayaran ?? item.status ?? '').toLowerCase();

                    if (s.includes('expired') || s.includes('cancelled') || s.includes('batal') || s.includes('failed') || s.includes('reject')) {
                        return;
                    }

                    let jamMulai = item.jam_mulai ?? item.jam ?? item.waktu_mulai;
                    let jamSelesai = item.jam_selesai ?? item.waktu_selesai;

                    if (jamMulai) {
                        let startHour = parseInt(String(jamMulai).split(":")[0]);
                        let endHour = jamSelesai ? parseInt(String(jamSelesai).split(":")[0]) : startHour + 1;

                        for (let h = startHour; h < endHour; h++) {
                            let formattedSlot = String(h).padStart(2, "0") + ":00";
                            if (!bookedArray.includes(formattedSlot)) {
                                bookedArray.push(formattedSlot);
                            }
                        }
                    }
                });
            }

            blockedSlots = bookedArray;
            resetSelection();

            document.querySelectorAll(".jam-btn").forEach(btn => {
                const jam = btn.dataset.jam;
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

    // Court select events
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
                blockedSlots = [];

                document.querySelectorAll(".jam-btn").forEach(btn => {
                    btn.classList.remove("active", "active-range");
                });

                updateUI();

                if (tanggalInput && tanggalInput.value) {
                    loadAvailability(tanggalInput.value);
                }

                try {
                    let res = await fetch(`/booking/fully-booked-dates?lapangan_id=${lapanganAktif}`);
                    let fullyBookedDates = await res.json();
                    initDatePicker(Array.isArray(fullyBookedDates) ? fullyBookedDates : []);
                } catch (err) {
                    console.error("Gagal load fully booked dates:", err);
                }

            } else {
                lapanganAktif = null;
                hargaPagi = 0;
                hargaMalam = 0;
                if (summaryNama) summaryNama.textContent = "-";

                jamAktif = null;
                updateUI();

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

    // Time slot select events
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

            updateUI();
            highlightRange();
        });
    });

    // Duration controls
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

    // Submit booking action
    const btnBooking = document.getElementById("btnBooking");
    if (btnBooking) {
        btnBooking.addEventListener("click", async function () {

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

            // Confirmation popup
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
                confirmButtonText: 'Ya, Lanjut Pembayaran',
                cancelButtonText: 'Cek Kembali'
            });

            if (!confirmResult.isConfirmed) return;

            // Loading popup
            Swal.fire({
                title: 'Memproses Pesanan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Safe CSRF token retrieval
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.content : '';

            try {
                let res = await fetch("/booking", {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken
                    },
                    body: JSON.stringify({
                        id_lapangan: lapanganAktif,
                        tanggal: tanggal,
                        jam_mulai: jamAktif,
                        jam_selesai: jamSelesai
                    })
                });

                let result = await res.json();

                if (res.ok && result.status === "success") {
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
                    let errorMessage = result.message || "Gagal membuat pesanan.";

                    if (errorMessage.toLowerCase().includes("database") || res.status === 409) {
                        errorMessage = "Jadwal pada jam ini sudah di-booking oleh orang lain. Silakan pilih jam lain.";
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Booking',
                        text: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });

                    if (tanggal) loadAvailability(tanggal);
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