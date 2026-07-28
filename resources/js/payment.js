document.addEventListener('DOMContentLoaded', function () {

    const bookingId =
        document.getElementById('bookingId')?.textContent?.trim() ||
        new URLSearchParams(window.location.search).get('id');

    const statusBadge = document.getElementById('statusBadge');
    const paymentForm = document.getElementById('paymentForm');
    const statusMessage = document.getElementById('statusMessage');
    const messageTitle = document.getElementById('messageTitle');
    const messageText = document.getElementById('messageText');

    const proofFile = document.getElementById('proofFile');
    const uploadBox = document.getElementById('uploadBox');
    const uploadPreview = document.getElementById('uploadPreview');
    const previewImg = document.getElementById('previewImg');

    const pickBtn = document.getElementById('pickBtn');
    const submitPaymentBtn = document.getElementById('submitPaymentBtn');
    const timerEl = document.getElementById('timer');

    let countdownInterval;

    // =========================
    // ZOOM QR MODAL TRIGGER
    // =========================
    const qrCardTrigger = document.getElementById('qrCardTrigger');
    if (qrCardTrigger) {
        qrCardTrigger.addEventListener('click', function () {
            if (window.bootstrap && window.bootstrap.Modal) {
                const qrModal = new window.bootstrap.Modal(document.getElementById('qrModal'));
                qrModal.show();
            } else {
                // Fallback jika bootstrap JS belum terload
                const imgUrl = document.getElementById('qrImage')?.src;
                if (imgUrl) window.open(imgUrl, '_blank');
            }
        });
    }

    // =========================
    // PAYMENT DEADLINE & TIMER FIX
    // =========================
    const deadlineValue = document.getElementById('paymentDeadline')?.value;

    // Parser tanggal fleksibel untuk ISO String / MySQL Datetime
    function parseDeadline(val) {
        if (!val || val === 'null' || val === 'undefined') {
            // Default fallback 15 menit dari sekarang jika deadline dari backend kosong
            return Date.now() + 15 * 60 * 1000;
        }
        const parsed = new Date(val).getTime();
        return Number.isNaN(parsed) ? Date.now() + 15 * 60 * 1000 : parsed;
    }

    const deadlineMs = parseDeadline(deadlineValue);

    function showStatus(title, text, type = 'info') {
        if (messageTitle) messageTitle.textContent = title;
        if (messageText) messageText.textContent = text;
        if (statusMessage) {
            statusMessage.className = `mt-4 text-center alert alert-${type}`;
            statusMessage.style.display = 'block';
        }
    }

    function applyExpiredUI() {
        clearInterval(countdownInterval);

        if (timerEl) {
            timerEl.textContent = '00:00';
            timerEl.classList.add('expired');
        }

        if (statusBadge) {
            statusBadge.textContent = 'Expired';
            statusBadge.className = 'badge bg-danger status-badge';
        }

        if (submitPaymentBtn) submitPaymentBtn.disabled = true;
        if (uploadBox) uploadBox.style.opacity = '0.5';

        showStatus('Waktu Habis', 'Batas waktu pembayaran telah berakhir.', 'danger');
    }

    function updateCountdown() {
        if (!timerEl) return;

        const diffMs = deadlineMs - Date.now();

        if (diffMs <= 0) {
            applyExpiredUI();
            return;
        }

        const totalSeconds = Math.floor(diffMs / 1000);
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;

        timerEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);

    // =========================
    // STATUS NORMALIZER
    // =========================
    function normalizePaymentStatus(status) {
        if (!status) return 'pending';
        const s = String(status).trim().toLowerCase();
        if (s.includes('menunggu')) return 'waiting_confirmation';
        if (s === 'waiting_confirmation') return 'waiting_confirmation';
        if (s === 'approve' || s === 'paid' || s === 'confirmed') return 'confirmed';
        if (s === 'expired') return 'expired';
        if (s === 'cancelled') return 'cancelled';
        return 'pending';
    }

    // INITIAL STATUS CHECK
    if (statusBadge) {
        const normalized = normalizePaymentStatus(
            window.paymentStatus || statusBadge.dataset.paymentStatus || statusBadge.textContent
        );
        statusBadge.dataset.paymentStatus = normalized;

        const blockedStatuses = ['waiting_confirmation', 'confirmed', 'expired', 'cancelled'];

        if (blockedStatuses.includes(normalized)) {
            if (submitPaymentBtn) submitPaymentBtn.disabled = true;
            if (uploadBox) uploadBox.style.opacity = '0.5';

            if (normalized === 'confirmed') {
                showStatus('Berhasil', 'Pembayaran sudah dikonfirmasi.', 'success');
            } else if (normalized === 'expired') {
                showStatus('Kadaluarsa', 'Waktu pembayaran sudah berakhir.', 'danger');
            } else if (normalized === 'cancelled') {
                showStatus('Dibatalkan', 'Booking dibatalkan.', 'secondary');
            }
        }
    }

    // =========================
    // UPLOAD & FILE PICKER
    // =========================
    proofFile?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = (file.name.split('.').pop() || '').toLowerCase();
        if (!['jpg', 'jpeg', 'png'].includes(ext)) {
            alert('Hanya menerima file JPG / JPEG / PNG');
            proofFile.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file maksimal 2MB');
            proofFile.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            if (previewImg) previewImg.src = ev.target.result;
            if (uploadPreview) uploadPreview.style.display = 'block';
            if (submitPaymentBtn) submitPaymentBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    });

    pickBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        proofFile?.click();
    });

    uploadBox?.addEventListener('click', (e) => {
        if (e.target?.id === 'submitPaymentBtn') return;
        proofFile?.click();
    });

    // DRAG & DROP
    uploadBox?.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });

    uploadBox?.addEventListener('dragleave', () => uploadBox.classList.remove('dragover'));

    uploadBox?.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadBox.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (!file) return;

        const dt = new DataTransfer();
        dt.items.add(file);
        proofFile.files = dt.files;
        proofFile.dispatchEvent(new Event('change'));
    });

    // SUBMIT PAYMENT
    submitPaymentBtn?.addEventListener('click', async () => {
        try {
            if (!proofFile.files || !proofFile.files[0]) {
                alert('Silakan upload bukti pembayaran terlebih dahulu.');
                return;
            }

            const token = window.authToken;
            if (!token) {
                alert('Login required. Token tidak ditemukan.');
                return;
            }

            const formData = new FormData();
            formData.append('bukti', proofFile.files[0]);

            submitPaymentBtn.disabled = true;
            submitPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengirim...';
            if (uploadBox) uploadBox.style.opacity = '0.6';

            const response = await fetch(`/booking/payment/${bookingId}/upload-bukti`, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            });

            const rawText = await response.text();
            let result;
            try {
                result = JSON.parse(rawText);
            } catch (err) {
                alert('Server tidak mengembalikan response JSON valid.');
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
                return;
            }

            if (!response.ok) {
                alert(result.message || 'Upload gagal.');
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
                return;
            }

            // SUCCESS
            submitPaymentBtn.disabled = true;
            showStatus('Berhasil!', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.', 'success');

        } catch (err) {
            console.error(err);
            alert('Terjadi kesalahan: ' + err.message);
            submitPaymentBtn.disabled = false;
            submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
        }
    });

});