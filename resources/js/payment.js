document.addEventListener('DOMContentLoaded', function() {
    const bookingId = document.getElementById('bookingId')?.textContent || new URLSearchParams(window.location.search).get('id');
    const statusBadge = document.getElementById('statusBadge');
    const paymentForm = document.getElementById('paymentForm');
    const statusMessage = document.getElementById('statusMessage');
    const messageTitle = document.getElementById('messageTitle');
    const messageText = document.getElementById('messageText');
    const proofFile = document.getElementById('proofFile');
    const uploadBox = document.getElementById('uploadBox');
    const uploadPreview = document.getElementById('uploadPreview');
    const uploadBtn = document.getElementById('uploadBtn'); // legacy (may not exist)

    // UI required by current task
    const pickBtn = document.getElementById('pickBtn');
    const submitPaymentBtn = document.getElementById('submitPaymentBtn');

    // NOTE: existing HTML uses #pickBtn (Choose Payment Proof) and #submitPaymentBtn (Kirim Pembayaran)




    const previewImg = document.getElementById('previewImg');
    const timerEl = document.getElementById('timer');

    let countdownInterval;

    const deadlineValue = document.getElementById('paymentDeadline')?.value;
    const deadlineMs = deadlineValue ? new Date(deadlineValue).getTime() : NaN;

    function applyExpiredUI() {
        clearInterval(countdownInterval);
        timerEl.textContent = '00:00';
        timerEl.classList.add('expired');
        if (statusBadge) {
            statusBadge.textContent = 'Expired';
            statusBadge.className = 'badge bg-danger';
        }
        if (uploadBtn) uploadBtn.disabled = true;
        if (uploadBox) uploadBox.style.opacity = '0.5';
        if (paymentForm) paymentForm.style.display = 'block';
        showStatus('Waktu Habis', 'Payment deadline has expired.', 'danger');
    }

    function updateCountdown() {
        if (!Number.isFinite(deadlineMs)) {
            timerEl.textContent = '—';
            return;
        }

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

    function normalizePaymentStatus(status) {
        if (!status) return 'pending';
        const s = String(status).trim().toLowerCase();

        // Normalize legacy backend value if it still exists
        if (s === 'menunggu_verifikasi' || s.includes('menunggu')) return 'waiting_confirmation';
        if (s === 'waiting_confirmation') return 'waiting_confirmation';
        if (s === 'confirmed') return 'confirmed';
        if (s === 'expired') return 'expired';
        if (s === 'cancelled' || s === 'dibatalkan') return 'cancelled';
        if (s === 'pending') return 'pending';

        return s;
    }

    // Initial status gating (DO NOT force waiting state during interaction)
    if (statusBadge) {
        const normalized = normalizePaymentStatus(statusBadge.dataset.paymentStatus || statusBadge.textContent);
        statusBadge.dataset.paymentStatus = normalized;

        const gated = ['waiting_confirmation', 'confirmed', 'expired', 'cancelled'];
        if (gated.includes(normalized)) {
            if (sendPaymentBtn) sendPaymentBtn.disabled = true;
            if (uploadBox) uploadBox.style.opacity = '0.5';


            if (normalized === 'confirmed') {
                showStatus('Berhasil', 'Pembayaran sudah dikonfirmasi.', 'success');
            } else if (normalized === 'expired') {
                showStatus('Kadaluarsa', 'Waktu pembayaran sudah berakhir.', 'danger');
            } else if (normalized === 'cancelled') {
                showStatus('Dibatalkan', 'Booking dibatalkan.', 'secondary');
            }
            // IMPORTANT: do not auto-show waiting_confirmation UI here; it should only appear after upload success.
        }
    }



    function showStatus(title, text, type) {
        if (paymentForm) paymentForm.style.display = 'none';
        if (messageTitle) messageTitle.textContent = title;
        if (messageText) messageText.textContent = text;
        if (statusMessage) {
            statusMessage.className = `mt-4 text-center alert alert-${type}`;
            statusMessage.style.display = 'block';
        }
    }

    // Upload preview + client validation (jpg/jpeg/png only)
    proofFile?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = (file.name.split('.').pop() || '').toLowerCase();
        const allowedExt = ['jpg', 'jpeg', 'png'];
        if (!allowedExt.includes(ext)) {
            alert('Hanya menerima .jpg .jpeg .png');
            proofFile.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('File max 2MB');
            proofFile.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = (ev) => {
            if (previewImg) previewImg.src = ev.target.result;
            if (uploadPreview) uploadPreview.style.display = 'block';

            // Preview only: do NOT switch badge/state yet
            const previewStatusBadge = document.getElementById('previewStatusBadge');
            if (previewStatusBadge) previewStatusBadge.style.display = 'none';

            // Enable Kirim Pembayaran button only after preview is ready
            if (submitPaymentBtn) {
                submitPaymentBtn.disabled = false;
            }

        };
        reader.readAsDataURL(file);
    });

    // BUTTON 1: Choose Payment Proof (open picker only)
    pickBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        proofFile?.click();
    });

    // Clicking anywhere in the upload box (except the submit button) also opens the picker.
    // This must NOT auto-submit or upload.
    uploadBox?.addEventListener('click', (e) => {
        const target = e?.target;
        // If click was on the submit button, do nothing (submit has its own handler)
        if (target && target.id === 'submitPaymentBtn') return;
        proofFile?.click();
    });



    uploadBox?.addEventListener('dragover', e => {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });

    uploadBox?.addEventListener('dragleave', () => uploadBox.classList.remove('dragover'));

    uploadBox?.addEventListener('drop', e => {
        e.preventDefault();
        uploadBox.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (!file) return;

        // FileList is read-only; reassign via DataTransfer
        const dt = new DataTransfer();
        dt.items.add(file);
        proofFile.files = dt.files;
        proofFile.dispatchEvent(new Event('change'));
    });

    // BUTTON 2: Kirim Pembayaran (upload proof + submit) - manual only
    const sendPaymentBtn = document.getElementById('sendPaymentBtn');
    sendPaymentBtn?.addEventListener('click', async () => {

        // Require file selected

        if (!proofFile.files || !proofFile.files[0]) {
            alert('Silakan pilih gambar bukti pembayaran dulu.');
            return;
        }


        const file = proofFile.files[0];
        const ext = (file.name.split('.').pop() || '').toLowerCase();
        const allowedExt = ['jpg', 'jpeg', 'png'];
        if (!allowedExt.includes(ext)) {
            alert('Hanya menerima .jpg .jpeg .png');
            return;
        }

            // Prevent duplicate upload after success
            if (sendPaymentBtn?.dataset.uploaded === 'true') return;


        const formData = new FormData();
        formData.append('bukti', file);

        if (submitPaymentBtn) submitPaymentBtn.disabled = true;
        if (uploadBox) uploadBox.style.opacity = '0.6';
        if (submitPaymentBtn) submitPaymentBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';


        try {
            const token = document.querySelector('meta[name="token"]')?.content || window.sessionStorage?.getItem('token');
            if (!token) {
                if (submitPaymentBtn) {
                    submitPaymentBtn.disabled = false;
                    submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';
                    submitPaymentBtn.dataset.uploaded = 'false';
                }
                if (uploadBox) uploadBox.style.opacity = '1';
                alert('Login required (token tidak ditemukan).');
                return;
            }





            const res = await fetch(`/booking/payment/${bookingId}/upload-bukti`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });


            const result = await res.json();
            if (result.success) {
                if (submitPaymentBtn) {
                    submitPaymentBtn.dataset.uploaded = 'true';
                }
                if (uploadBox) uploadBox.style.opacity = '0.5';

                if (uploadPreview) uploadPreview.style.display = 'block';

                if (statusBadge) {
                    statusBadge.dataset.paymentStatus = 'waiting_confirmation';
                    statusBadge.textContent = 'MENUNGGU VERIFIKASI ADMIN';
                    statusBadge.className = 'badge bg-primary';
                }

                if (sendPaymentBtn) {
                    sendPaymentBtn.dataset.uploaded = 'true';
                    sendPaymentBtn.disabled = true;
                }



                // Update badge ONLY after successful upload
                if (statusBadge) {
                    statusBadge.dataset.paymentStatus = 'waiting_confirmation';
                    statusBadge.textContent = 'Waiting for admin verification';
                    statusBadge.className = 'badge bg-primary';
                }

                showStatus('Berhasil', 'Payment proof submitted successfully', 'success');
                if (statusBadge) {
                    statusBadge.textContent = 'MENUNGGU VERIFIKASI ADMIN';
                    statusBadge.className = 'badge bg-primary';
                }

            } else {
                if (submitPaymentBtn) {
                    submitPaymentBtn.disabled = false;
                    submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';
                }
                if (uploadBox) uploadBox.style.opacity = '1';
                alert('Upload gagal: ' + (result.message || 'Unknown error'));
            }

        } catch (err) {
            if (submitPaymentBtn) {
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';
                submitPaymentBtn.dataset.uploaded = 'false';
            }
            if (uploadBox) uploadBox.style.opacity = '1';
            alert('Error: ' + err.message);
        }

    });
});

