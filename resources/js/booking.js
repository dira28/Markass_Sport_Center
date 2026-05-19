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

        // Check against blockedSlots array (more reliable)
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

    // ===== RESET SELECTION ONLY (keep booked states) =====
    function resetSelection() {
        document.querySelectorAll(".jam-btn").forEach(btn => {
            btn.classList.remove("active", "active-range");
            // Keep "jam-booked" states - will be set by loadAvailability
        });

        jamAktif = null;
        hargaPerJam = 0;
        updateUI();
    }

// ===== LOAD STATUS JAM (NEW ENDPOINT) =====
    async function loadAvailability(tanggal) {
        if (!lapanganAktif) return;

        if (controller) controller.abort();
        controller = new AbortController();

        try {
            console.log("FETCH STATUS JAM:", tanggal, lapanganAktif);

            let res = await fetch(`/booking/status-jam?tanggal=${tanggal}&lapangan_id=${lapanganAktif}`, {
                credentials: "same-origin",
                signal: controller.signal
            });

            let statusData = await res.json();
            console.log("STATUS:", statusData);

            blockedSlots = statusData
                .filter(item => {
                    // treat expired/cancelled as available again (ignore ONLY these)
                    const s = (item.status_pembayaran ?? item.status ?? '').toLowerCase();
                    if (s.includes('expired') || s.includes('cancelled')) return false;

                    // block: pending/waiting_confirmation/confirmed (and legacy tokens)
                    if (
                        s === 'pending' ||
                        s === 'waiting_confirmation' ||
                        s === 'confirmed' ||
                        s === 'paid' ||
                        s === 'menunggu_verifikasi' ||
                        s === 'approve'
                    ) {
                        return true;
                    }

                    // backward compat: API might return `terbooking`
                    if (s === 'terbooking') return true;

                    return false;
                })
                .map(item => item.jam);


            // Reset selection
            resetSelection();

            // Apply status to buttons
            const now = new Date();

            // Helper: parse jam range objects coming from backend.
            // API may return fields named jam_mulai/jam_selesai or start_time/end_time.
            function normalizeStatus(raw) {
                return (raw ?? '').toString().toLowerCase().trim();
            }

            function parseDateTimeFromBooking(booking, field) {
                // Prefer explicit ISO timestamps
                const v = booking?.[field] ?? booking?.[`${field}_at`] ?? booking?.[field.replace('end_time', 'jam_selesai')] ?? booking?.[field.replace('start_time', 'jam_mulai')];
                if (!v) return null;
                const d = new Date(v);
                if (Number.isNaN(d.getTime())) return null;
                return d;
            }

            function buildTodayDateFromJamString(jamStr) {
                // jamStr like "08:00"; assume same day in Asia/Jakarta
                if (!jamStr) return null;
                const [hh, mm] = jamStr.split(':').map(x => parseInt(x, 10));
                if (Number.isNaN(hh) || Number.isNaN(mm)) return null;

                // Construct local Date object (browser local). Backend already provides correct date server-side.
                const d = new Date(now);
                d.setHours(hh, mm, 0, 0);
                return d;
            }

            function isBlockedSlotForButton(btnJam, bookingObj) {
                if (!bookingObj) return false;

                const status = normalizeStatus(bookingObj.status_pembayaran ?? bookingObj.status ?? bookingObj.status_booking);

                // Auto-expire rule: pending + created_at > 30 minutes => expired
                const createdAt = parseDateTimeFromBooking(bookingObj, 'created_at');
                const isPending = status === 'pending';
                if (isPending && createdAt) {
                    const diffMin = (now.getTime() - createdAt.getTime()) / 60000;
                    if (diffMin > 30) {
                        bookingObj.__effectiveStatus = 'expired';
                    }
                }

                const effectiveStatus = normalizeStatus(bookingObj.__effectiveStatus ?? status);

                const isExpired = effectiveStatus === 'expired' || effectiveStatus === 'completed';
                if (isExpired) return false;

                // Determine end_time / jam_selesai
                const endTime =
                    parseDateTimeFromBooking(bookingObj, 'end_time') ||
                    parseDateTimeFromBooking(bookingObj, 'jam_selesai') ||
                    buildTodayDateFromJamString(bookingObj.jam_selesai ?? bookingObj.end_time);

                // If current time already passed, lock should be released (auto-completed / time passed)
                if (endTime && now.getTime() > endTime.getTime()) {
                    bookingObj.__effectiveStatus = 'completed';
                    return false;
                }

                // Core locking rules
                // Locked if status in pending/waiting_confirmation/confirmed AND current time < end_time
                const lockedStatuses = ['pending', 'waiting_confirmation', 'confirmed', 'paid', 'approve', 'menunggu_verifikasi'];
                const isLockedStatus = lockedStatuses.includes(effectiveStatus);

                if (!isLockedStatus) return false;

                if (!endTime) {
                    // If we cannot determine end time, safest: keep blocked for these statuses
                    return true;
                }

                return now.getTime() < endTime.getTime();
            }

            document.querySelectorAll(".jam-btn").forEach(btn => {
                const jam = btn.dataset.jam;
                const bookingForJam = statusData.find(item => item.jam === jam);

                // compute blocked using core logic
                const blocked = isBlockedSlotForButton(jam, bookingForJam);

                // Ensure disabled attribute is the single source of clickability
                if (blocked) {
                    btn.disabled = true;
                    btn.classList.add("jam-booked");
                    btn.classList.add("booked-slot");

                    // Label "BOOKED"
                    btn.dataset.booked = '1';
                    btn.innerHTML = `
                        <span class="jam-text">${jam}</span>
                        <span class="jam-booked-label">BOOKED</span>
                    `;
                } else {
                    btn.disabled = false;
                    btn.classList.remove("jam-booked");
                    btn.classList.remove("booked-slot");
                    btn.dataset.booked = '0';
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
        el.addEventListener("click", function () {

            lapanganAktif = this.dataset.id;
            hargaPagi = parseInt(this.dataset.hargaPagi || 0);
            hargaMalam = parseInt(this.dataset.hargaMalam || 0);

            summaryNama.textContent = this.dataset.nama;

            document.querySelectorAll(".card-lapangan").forEach(c => c.classList.remove("active"));
            this.classList.add("active");

            resetSelection();
            blockedSlots = []; // Clear old blocked

            if (tanggalInput.value) {
                loadAvailability(tanggalInput.value);
            }
        });
    });

    // ===== PILIH JAM =====
    document.querySelectorAll(".jam-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            // Must never act when disabled
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


    // ===== PILIH TANGGAL =====
    tanggalInput.addEventListener("change", function () {
        let tgl = this.value;

        summaryTanggal.textContent = tgl || "-";

        resetSelection();
        blockedSlots = []; // Clear old blocked

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
                console.log('BOOKING RESULT:', result);
                const bookingId = result?.data?.data?.id_booking || result?.data?.id_booking;
                if (!bookingId) {
                    console.error('Missing bookingId in booking response', result);
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

// Availability selection + booking creation only.
// Payment upload/proof logic must live on the dedicated payment page (`/booking/payment/{id_booking}`)
// and use resources/js/payment.js.

});











