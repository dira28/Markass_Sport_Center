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
    // PAYMENT DEADLINE
    // =========================

    const deadlineValue = document.getElementById('paymentDeadline')?.value;
    const deadlineMs = deadlineValue ? new Date(deadlineValue).getTime() : NaN;

    function showStatus(title, text, type = 'info') {
        if (messageTitle) {
            messageTitle.textContent = title;
        }

        if (messageText) {
            messageText.textContent = text;
        }

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
            statusBadge.className = 'badge bg-danger';
        }

        if (submitPaymentBtn) {
            submitPaymentBtn.disabled = true;
        }

        if (uploadBox) {
            uploadBox.style.opacity = '0.5';
        }

        showStatus(
            'Waktu Habis',
            'Payment deadline has expired.',
            'danger'
        );
    }

    function updateCountdown() {

        if (!timerEl) return;

        if (!Number.isFinite(deadlineMs)) {
            timerEl.textContent = '--:--';
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

        timerEl.textContent =
            `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
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

        // legacy backend/payment tokens
        if (s === 'approve') return 'confirmed';
        if (s === 'paid') return 'confirmed';

        if (s === 'confirmed') return 'confirmed';
        if (s === 'expired') return 'expired';
        if (s === 'cancelled') return 'cancelled';
        if (s === 'pending') return 'pending';

        return s;
    }


    // =========================
    // INITIAL STATUS
    // =========================

    if (statusBadge) {

        const normalized =
            normalizePaymentStatus(
                window.paymentStatus ||
                statusBadge.dataset.paymentStatus ||
                statusBadge.textContent
            );

        statusBadge.dataset.paymentStatus = normalized;

        const blockedStatuses = [
            'waiting_confirmation',
            'confirmed',
            'expired',
            'cancelled'
        ];

        if (blockedStatuses.includes(normalized)) {

            if (submitPaymentBtn) {
                submitPaymentBtn.disabled = true;
            }

            if (uploadBox) {
                uploadBox.style.opacity = '0.5';
            }

            if (normalized === 'confirmed') {
                showStatus(
                    'Berhasil',
                    'Pembayaran sudah dikonfirmasi.',
                    'success'
                );
            }

            if (normalized === 'expired') {
                showStatus(
                    'Kadaluarsa',
                    'Waktu pembayaran sudah berakhir.',
                    'danger'
                );
            }

            if (normalized === 'cancelled') {
                showStatus(
                    'Dibatalkan',
                    'Booking dibatalkan.',
                    'secondary'
                );
            }
        }
    }

    // =========================
    // FILE VALIDATION + PREVIEW
    // =========================

    proofFile?.addEventListener('change', function (e) {

        const file = e.target.files[0];

        if (!file) return;

        const ext = (file.name.split('.').pop() || '').toLowerCase();

        const allowedExt = ['jpg', 'jpeg', 'png'];

        if (!allowedExt.includes(ext)) {
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

            if (previewImg) {
                previewImg.src = ev.target.result;
            }

            if (uploadPreview) {
                uploadPreview.style.display = 'block';
            }

            if (submitPaymentBtn) {
                submitPaymentBtn.disabled = false;
            }
        };

        reader.readAsDataURL(file);
    });

    // =========================
    // OPEN FILE PICKER
    // =========================

    pickBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        proofFile?.click();
    });

    uploadBox?.addEventListener('click', (e) => {

        const target = e.target;

        if (target?.id === 'submitPaymentBtn') return;

        proofFile?.click();
    });

    // =========================
    // DRAG & DROP
    // =========================

    uploadBox?.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });

    uploadBox?.addEventListener('dragleave', () => {
        uploadBox.classList.remove('dragover');
    });

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

    // =========================
    // INITIAL BUTTON STATE
    // =========================

    if (submitPaymentBtn) {
        submitPaymentBtn.disabled = true;
    }

    // =========================
    // SUBMIT PAYMENT
    // =========================

    submitPaymentBtn?.addEventListener('click', async () => {

        try {

            if (!proofFile.files || !proofFile.files[0]) {
                alert('Silakan upload bukti pembayaran terlebih dahulu.');
                return;
            }

            if (submitPaymentBtn.dataset.uploaded === 'true') {
                return;
            }

            const token = window.authToken;

            console.log('TOKEN:', token);

            if (!token) {
                alert('Login required. Token tidak ditemukan.');
                return;
            }

            const file = proofFile.files[0];

            const formData = new FormData();
            formData.append('bukti', file);

            submitPaymentBtn.disabled = true;

            submitPaymentBtn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

            if (uploadBox) {
                uploadBox.style.opacity = '0.6';
            }

            const response = await fetch(
                `/booking/payment/${bookingId}/upload-bukti`,
                {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',

                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    }
                }
            );

            // DEBUG RESPONSE
            const rawText = await response.text();

            console.log('RAW RESPONSE:', rawText);

            let result;

            try {
                result = JSON.parse(rawText);
            } catch (jsonError) {

                console.error('NOT JSON RESPONSE');

                alert(
                    'Server tidak mengembalikan JSON.\n\n' +
                    'Kemungkinan:\n' +
                    '- Route salah\n' +
                    '- Backend error\n' +
                    '- Redirect login\n' +
                    '- API return HTML'
                );

                submitPaymentBtn.disabled = false;

                submitPaymentBtn.innerHTML =
                    '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';

                if (uploadBox) {
                    uploadBox.style.opacity = '1';
                }

                return;
            }

            if (!response.ok) {

                alert(result.message || 'Upload gagal.');

                submitPaymentBtn.disabled = false;

                submitPaymentBtn.innerHTML =
                    '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';

                if (uploadBox) {
                    uploadBox.style.opacity = '1';
                }

                return;
            }

            // SUCCESS

            submitPaymentBtn.dataset.uploaded = 'true';

            submitPaymentBtn.disabled = true;

            if (uploadBox) {
                uploadBox.style.opacity = '0.5';
            }

            const data = result.data || {};

            // IMPORTANT: frontend MUST render from status_pembayaran only
            const backendStatus = data.status_pembayaran || 'pending';
            const normalizedStatus = normalizePaymentStatus(backendStatus);

            if (statusBadge) {
                statusBadge.dataset.paymentStatus = normalizedStatus;

                if (normalizedStatus === 'waiting_confirmation') {
                    statusBadge.className = 'badge bg-primary';
                    statusBadge.textContent = 'WAITING FOR ADMIN VERIFICATION';
                    showStatus(
                        'Success',
                        'Payment proof uploaded successfully. Waiting for admin confirmation.',
                        'success'
                    );
                } else if (normalizedStatus === 'confirmed') {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = 'PAYMENT CONFIRMED';
                    showStatus(
                        'Success',
                        'Your payment has been confirmed by admin.',
                        'success'
                    );
                } else if (normalizedStatus === 'expired') {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = 'Expired';
                    showStatus(
                        'Kadaluarsa',
                        'Payment has expired.',
                        'danger'
                    );
                } else if (normalizedStatus === 'cancelled') {
                    statusBadge.className = 'badge bg-secondary';
                    statusBadge.textContent = 'Cancelled';
                    showStatus(
                        'Dibatalkan',
                        'Payment has been cancelled.',
                        'secondary'
                    );
                } else {
                    statusBadge.className = 'badge bg-warning';
                    statusBadge.textContent = 'Pending';
                }
            }

            // UX rules: never hide the send button; just disable it permanently
            if (submitPaymentBtn) submitPaymentBtn.disabled = true;
            if (uploadBox) uploadBox.style.opacity = '0.5';


        } catch (err) {

            console.error(err);

            if (submitPaymentBtn) {

                submitPaymentBtn.disabled = false;

                submitPaymentBtn.innerHTML =
                    '<i class="fas fa-paper-plane"></i> Kirim Pembayaran';

                submitPaymentBtn.dataset.uploaded = 'false';
            }

            if (uploadBox) {
                uploadBox.style.opacity = '1';
            }

            alert('Error: ' + err.message);
        }
    });

});