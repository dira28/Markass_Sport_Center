document.addEventListener("DOMContentLoaded", function () {

    let lapanganAktif = null;
    let hargaPagi = 0;
    let hargaMalam = 0;

    let jamAktif = null;
    let durasi = 1;

    let controller = null;
    let blockedSlots = []; // Jam yang TERBOOKING / PENDING
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
        if (isNaN(d.getTime())) return str;
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function initDatePicker(disabledDates = []) {
        if (fpInstance) fpInstance.destroy();

        if (tanggalInput) {
            fpInstance = flatpickr("#tanggal", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d-m-Y",
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
                text: 'Durasi pilihanmu menabrak jam yang sudah terbooking/pending.',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        let s = parseInt(jamAktif.split(":")[0]);
        let e = s + durasi;

        document.querySelectorAll(".jam-btn").forEach(btn => {
            let jam = parseInt(btn.dataset.jam.split(":")[0]);
            if (jam >= s && jam < e && !btn.disabled) {
                btn.classList.add("active-range");
            }
        });
    }

    function hitungTotalHarga(start, durasi) {
        if (!start || (!hargaPagi && !hargaMalam)) return 0;

        let total = 0;
        let startHour = parseInt(start.split(":")[0]);

        for (let i = 0; i < durasi; i++) {
            let currentHour = startHour + i;
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

    // FUNGSI LOAD AVAILABILITY
    async function loadAvailability(tanggal) {
        if (!lapanganAktif || !tanggal) return;

        if (controller) controller.abort();
        controller = new AbortController();

        try {
            console.log(`[DEBUG] Fetching status jam untuk Lapangan ID: ${lapanganAktif}, Tanggal: ${tanggal}`);

            let res = await fetch(`/booking/status-jam?tanggal=${tanggal}&lapangan_id=${lapanganAktif}`, {
                credentials: "same-origin",
                signal: controller.signal
            });

            if (!res.ok) throw new Error(`HTTP Error ${res.status}`);

            let responseData = await res.json();
            console.log("[DEBUG] Response Backend:", responseData);

            let statusJamList = [];
            if (responseData.data && Array.isArray(responseData.data.status_jam)) {
                statusJamList = responseData.data.status_jam;
            } else if (responseData.status_jam && Array.isArray(responseData.status_jam)) {
                statusJamList = responseData.status_jam;
            } else if (responseData.data && Array.isArray(responseData.data)) {
                statusJamList = responseData.data;
            } else if (Array.isArray(responseData)) {
                statusJamList = responseData;
            }

            let blockedArray = [];

            document.querySelectorAll(".jam-btn").forEach(btn => {
                const jamBtn = btn.dataset.jam;
                if (!jamBtn) return;

                const matched = statusJamList.find(item => {
                    if (!item) return false;
                    let jamVal = item.jam || item.jam_mulai || item.waktu;
                    if (jamVal && jamVal.length > 5) jamVal = jamVal.substring(0, 5);
                    return jamVal === jamBtn;
                });

                const status = matched ? String(matched.status).toUpperCase() : 'FREE';

                // Reset status tombol dasar
                btn.disabled = false;
                btn.classList.remove("jam-booked", "jam-pending", "active", "active-range");
                btn.removeAttribute("title");

                // WARNA ABU-ABU (TERBOOKING / LUNAS)
                if (['TERBOOKING', 'BOOKED', 'CONFIRMED', 'PAID', 'LUNAS', 'SUCCESS'].includes(status)) {
                    btn.disabled = true;
                    btn.classList.add("jam-booked");
                    btn.title = `Jam ${jamBtn} - Sudah Terbooking`;
                    btn.innerHTML = `<span class="jam-text">${jamBtn}</span><span class="jam-status-badge">BOOKED</span>`;
                    blockedArray.push(jamBtn);

                    // WARNA KUNING (PENDING / MENUNGGU BAYAR)
                } else if (['PENDING', 'WAITING', 'WAITING_PAYMENT', 'MENUNGGU'].includes(status)) {
                    btn.disabled = true;
                    btn.classList.add("jam-pending");
                    btn.title = `Jam ${jamBtn} - Menunggu Pembayaran`;
                    btn.innerHTML = `<span class="jam-text">${jamBtn}</span><span class="jam-status-badge">PENDING</span>`;
                    blockedArray.push(jamBtn);

                    // NORMAL / TERSEDIA
                } else {
                    btn.title = `Jam ${jamBtn} - Tersedia`;
                    btn.innerHTML = `<span class="jam-text">${jamBtn}</span>`;
                }
            });

            blockedSlots = blockedArray;

        } catch (err) {
            if (err.name === "AbortError") return;
            console.error("Gagal memuat status jam:", err);
        }
    }

    // EVENT KLIK KARTU LAPANGAN (Cukup 1 Listener Saja)
    document.querySelectorAll(".card-lapangan").forEach(el => {
        el.addEventListener("click", async function () {
            const isAlreadyActive = this.classList.contains("active");

            // Reset semua pilihan kartu terlebih dahulu
            document.querySelectorAll(".card-lapangan").forEach(c => {
                c.classList.remove("active");
                const detail = c.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = null;
            });

            // Jika kartu belum aktif, aktifkan kartu yang diklik
            if (!isAlreadyActive) {
                this.classList.add("active");

                const detail = this.querySelector(".lapangan-detail");
                if (detail) detail.style.maxHeight = detail.scrollHeight + "px";

                lapanganAktif = this.dataset.id;
                hargaPagi = parseInt(this.dataset.hargaPagi || 0);
                hargaMalam = parseInt(this.dataset.hargaMalam || 0);

                if (summaryNama) summaryNama.textContent = this.dataset.nama;

                resetSelection();

                // Cek ketersediaan jam jika tanggal sudah dipilih sebelumnya
                const tanggalTerpilih = tanggalInput ? tanggalInput.value : null;
                if (tanggalTerpilih) {
                    loadAvailability(tanggalTerpilih);
                }

                try {
                    let res = await fetch(`/booking/fully-booked-dates?lapangan_id=${lapanganAktif}`);
                    let fullyBookedDates = await res.json();
                    initDatePicker(Array.isArray(fullyBookedDates) ? fullyBookedDates : []);
                } catch (err) {
                    console.error("Gagal load fully booked dates:", err);
                }

                // Jika kartu diklik lagi (unselect / batalkan pilihan)
            } else {
                lapanganAktif = null;
                hargaPagi = 0;
                hargaMalam = 0;
                if (summaryNama) summaryNama.textContent = "-";

                resetSelection();

                document.querySelectorAll(".jam-btn").forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove("jam-booked", "jam-pending", "active", "active-range");
                    btn.removeAttribute("title");
                    if (btn.dataset.jam) {
                        btn.innerHTML = `<span class="jam-text">${btn.dataset.jam}</span>`;
                    }
                });

                initDatePicker([]);
            }
        });
    });

    // Klik Slot Jam
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            if (this.disabled) return;

            const start = this.dataset.jam;
            if (!start) return;

            if (!isRangeValid(start, durasi)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jadwal Bentrok',
                    text: `Durasi ${durasi} jam pilihanmu menabrak jadwal lain yang terbooking/pending.`,
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

    // Tombol Durasi
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

    // Submit Booking
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
                    text: 'Waktu yang dipilih sudah di-booking atau pending oleh orang lain.',
                    confirmButtonColor: '#dc3545'
                });
            }

            let startHour = parseInt(jamAktif.split(":")[0]);
            let endHour = startHour + durasi;
            let jamSelesai = String(endHour).padStart(2, "0") + ":00";
            let namaLap = summaryNama ? summaryNama.textContent : "Lapangan";
            let totalBayar = totalHarga ? totalHarga.textContent : "Rp0";

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

            Swal.fire({
                title: 'Memproses Pesanan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

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