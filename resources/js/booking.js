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

            blockedSlots = statusData.filter(item => item.status === 'terbooking').map(item => item.jam);

            // Reset selection
            resetSelection();

            // Apply status to buttons
            document.querySelectorAll(".jam-btn").forEach(btn => {
                let jam = btn.dataset.jam;
                let status = statusData.find(item => item.jam === jam);
                let isBooked = status && status.status === 'terbooking';
                
                if (isBooked) {
                    btn.classList.add("jam-booked");
                    btn.disabled = true;
                } else {
                    btn.classList.remove("jam-booked");
                    btn.disabled = false;
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

    // ===== PAYMENT UPLOAD PREVIEW =====
    const proofFile = document.getElementById('proofFile');
    const uploadBox = document.getElementById('uploadBox');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImg = document.getElementById('previewImg');
    const statusBadge = document.getElementById('statusBadge');

    // File select
    proofFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file && file.size > 2*1024*1024) {
            alert('File terlalu besar (max 2MB)');
            return;
        }
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                uploadPreview.style.display = 'block';
                statusBadge.textContent = 'Menunggu Verifikasi';
                statusBadge.className = 'payment-badge menunggu-verifikasi mt-3 d-inline-block';
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });

    // Drag & drop
    uploadBox.addEventListener('dragover', e => {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });
    uploadBox.addEventListener('dragleave', () => {
        uploadBox.classList.remove('dragover');
    });
    uploadBox.addEventListener('drop', e => {
        e.preventDefault();
        uploadBox.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        proofFile.files = e.dataTransfer.files;
        proofFile.dispatchEvent(new Event('change', {bubbles: true}));
    });
    uploadBox.addEventListener('click', () => proofFile.click());

    // Upload click (temp)
    uploadBtn.addEventListener('click', function() {
        alert('Upload berhasil! Menunggu verifikasi admin.');
        uploadBox.style.display = 'none';
        uploadPreview.style.display = 'block';
        statusBadge.innerHTML = '<i class="fas fa-clock"></i> Menunggu Verifikasi Admin';
    });

    // Admin view proof modal function
    function viewProof(id) {
        alert('Proof for booking #' + id + ' (demo full image modal)');
    }

    // ===== LOAD MY BOOKINGS (PERSISTENCE) =====
    async function loadMyBookings() {
        try {
            const res = await fetch('/booking/my-bookings', {
                credentials: "same-origin"
            });
            const bookings = await res.json();

            const paymentCard = document.getElementById('paymentCard');
            const paymentTotal = document.getElementById('paymentTotal');
            const statusBadge = document.getElementById('statusBadge');
            const uploadBox = document.getElementById('uploadBox');
            const uploadPreview = document.getElementById('uploadPreview');

            // Find active pending/menunggu_verifikasi today
            const today = new Date().toDateString();
            const activeBooking = bookings.find(b => 
                ['pending', 'menunggu_verifikasi'].includes(b.status) && 
                new Date(b.tanggal).toDateString() === today
            );

            if (activeBooking) {
                paymentCard.style.display = 'block';
                paymentTotal.textContent = 'Rp ' + parseInt(activeBooking.total_harga || 0).toLocaleString('id-ID');
                const statusText = activeBooking.status.replace('_', ' ').toUpperCase();
                statusBadge.textContent = statusText;
                statusBadge.className = `payment-badge ${activeBooking.status} mt-3 d-inline-block`;
                
                if (activeBooking.status === 'menunggu_verifikasi') {
                    uploadBox.style.display = 'none';
                    uploadPreview.style.display = 'block';
                    document.getElementById('paymentSubtitle').textContent = 'Bukti sudah diupload, menunggu verifikasi admin';
                } else {
                    uploadBox.style.display = 'block';
                    uploadPreview.style.display = 'none';
                    document.getElementById('paymentSubtitle').textContent = 'Scan QR DANA di bawah dan upload bukti pembayaran';
                }
            } else {
                paymentCard.style.display = 'none';
            }
        } catch (err) {
            console.error('Error loading my bookings:', err);
        }
    }

    // Auto refresh every 60s
    setInterval(() => {
        if (tanggalInput.value && lapanganAktif) loadAvailability(tanggalInput.value);
        loadMyBookings();
    }, 60000);

    // Initial loads
    loadMyBookings();

});


