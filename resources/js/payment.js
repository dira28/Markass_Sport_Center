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
    const uploadBtn = document.getElementById('uploadBtn');
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

    // Initial status gating
    if (statusBadge) {
        const currentStatus = (statusBadge.dataset.paymentStatus || statusBadge.textContent).toLowerCase();
        if (currentStatus.includes('menunggu') || currentStatus.includes('waiting_confirmation') || currentStatus.includes('confirmed') || currentStatus.includes('expired')) {
            if (uploadBtn) uploadBtn.disabled = true;
            if (uploadBox) uploadBox.style.opacity = '0.5';

            if (currentStatus.includes('menunggu')) {
                showStatus('Menunggu Verifikasi', 'Bukti pembayaran Anda sedang diverifikasi admin.', 'info');
            } else if (currentStatus.includes('confirmed')) {
                showStatus('Berhasil', 'Pembayaran sudah dikonfirmasi.', 'success');
            }
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
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload & Bayar';
            }
        };
        reader.readAsDataURL(file);
    });

    uploadBox?.addEventListener('click', () => proofFile.click());

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

    uploadBtn?.addEventListener('click', async () => {
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
        if (uploadBtn.dataset.uploaded === 'true') return;

        const formData = new FormData();
        formData.append('bukti', file);

        uploadBtn.disabled = true;
        uploadBox.style.opacity = '0.6';
        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Upload...';

        try {
            const token = document.querySelector('meta[name="token"]')?.content || window.sessionStorage?.getItem('token');
            if (!token) {
                uploadBtn.disabled = false;
                uploadBox.style.opacity = '1';
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload & Bayar';
                alert('Login required (token tidak ditemukan).');
                return;
            }

            const res = await fetch(`/booking/payment/${bookingId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const result = await res.json();
            if (result.success) {
                uploadBtn.dataset.uploaded = 'true';
                uploadBox.style.opacity = '0.5';
                uploadBtn.disabled = true;

                if (statusBadge) {
                    statusBadge.textContent = 'Waiting confirmation';
                    statusBadge.className = 'badge bg-primary';
                }

                if (uploadPreview) uploadPreview.style.display = 'block';
                showStatus('Berhasil', 'Payment proof uploaded successfully. Waiting for admin confirmation.', 'info');
            } else {
                uploadBtn.disabled = false;
                uploadBox.style.opacity = '1';
                uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload & Bayar';
                alert('Upload gagal: ' + (result.message || 'Unknown error'));
            }
        } catch (err) {
            uploadBtn.disabled = false;
            uploadBox.style.opacity = '1';
            uploadBtn.innerHTML = '<i class="fas fa-upload"></i> Upload & Bayar';
            alert('Error: ' + err.message);
        }
    });
});

