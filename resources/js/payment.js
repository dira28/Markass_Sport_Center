document.addEventListener('DOMContentLoaded', function () {

    const bookingId =
        document.getElementById('bookingId')?.textContent?.trim() ||
        window.bookingData?.id ||
        new URLSearchParams(window.location.search).get('id');

    const statusBadge = document.getElementById('statusBadge');
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
    let isExpiredHandled = false;

    // Helper Toast / Alert
    const showToast = (title, icon = 'success') => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            alert(title);
        }
    };

    const qrCardTrigger = document.getElementById('qrCardTrigger');
    if (qrCardTrigger) {
        qrCardTrigger.addEventListener('click', function () {
            if (window.bootstrap && window.bootstrap.Modal) {
                const qrModal = new window.bootstrap.Modal(document.getElementById('qrModal'));
                qrModal.show();
            } else {
                const imgUrl = document.getElementById('qrImage')?.src;
                if (imgUrl) window.open(imgUrl, '_blank');
            }
        });
    }

    const deadlineValue = document.getElementById('paymentDeadline')?.value;

    function parseDeadline(val) {
        const storageKey = `booking_deadline_${bookingId}`;

        if (val && val !== 'null' && val !== 'undefined' && val !== '') {
            const parsed = new Date(val).getTime();
            if (!Number.isNaN(parsed)) {
                localStorage.setItem(storageKey, parsed);
                return parsed;
            }
        }

        const savedDeadline = localStorage.getItem(storageKey);
        if (savedDeadline) {
            return parseInt(savedDeadline, 10);
        }

        const newDeadline = Date.now() + 15 * 60 * 1000;
        if (bookingId) {
            localStorage.setItem(storageKey, newDeadline);
        }
        return newDeadline;
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

    async function applyExpiredUI() {
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

        if (!isExpiredHandled && bookingId) {
            isExpiredHandled = true;
            try {
                const token = window.authToken;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                await fetch(`/booking/${bookingId}/expire`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (err) {
                console.error("Failed to sync expired status:", err);
            }
        }
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

    proofFile?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const ext = (file.name.split('.').pop() || '').toLowerCase();
        if (!['jpg', 'jpeg', 'png'].includes(ext)) {
            showToast('Hanya menerima file JPG / JPEG / PNG', 'warning');
            proofFile.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            showToast('Ukuran file maksimal 2MB', 'warning');
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

    submitPaymentBtn?.addEventListener('click', async () => {
        try {
            if (!proofFile.files || !proofFile.files[0]) {
                showToast('Silakan upload bukti pembayaran terlebih dahulu.', 'warning');
                return;
            }

            const token = window.authToken;
            if (!token) {
                showToast('Login required. Token tidak ditemukan.', 'error');
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
                showToast('Server tidak mengembalikan response JSON valid.', 'error');
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
                return;
            }

            if (!response.ok) {
                showToast(result.message || 'Upload gagal.', 'error');
                submitPaymentBtn.disabled = false;
                submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
                return;
            }

            submitPaymentBtn.disabled = true;
            showStatus('Berhasil!', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.', 'success');

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Bukti pembayaran berhasil terkirim.',
                confirmButtonColor: '#0d6efd'
            });

        } catch (err) {
            console.error(err);
            showToast('Terjadi kesalahan: ' + err.message, 'error');
            submitPaymentBtn.disabled = false;
            submitPaymentBtn.innerHTML = '<i class="fas fa-paper-plane me-1"></i> Kirim Bukti Pembayaran';
        }
    });

    // ==========================================
    // LOGIKA MODAL CANCEL BOOKING DENGAN SWEETALERT2
    // ==========================================
    const cancelBookingBtn = document.getElementById('cancelBookingBtn');

    cancelBookingBtn?.addEventListener('click', async () => {
        // Tampilkan Modal Konfirmasi SweetAlert2
        const confirmResult = await Swal.fire({
            title: 'Batalkan Booking?',
            text: "Apakah Anda yakin ingin membatalkan booking ini? Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-4 shadow',
                confirmButton: 'btn btn-danger px-4 me-2',
                cancelButton: 'btn btn-secondary px-4'
            },
            buttonsStyling: false
        });

        // Jika user tidak menekan tombol konfirmasi "Ya, Batalkan!"
        if (!confirmResult.isConfirmed) {
            return;
        }

        const token = window.authToken;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        try {
            cancelBookingBtn.disabled = true;
            cancelBookingBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Membatalkan...';

            const response = await fetch(`/booking/${bookingId}/cancel`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();

            if (response.ok) {
                clearInterval(countdownInterval);
                if (timerEl) timerEl.textContent = '00:00';
                if (statusBadge) {
                    statusBadge.textContent = 'Cancelled';
                    statusBadge.className = 'badge bg-secondary status-badge';
                }
                if (submitPaymentBtn) submitPaymentBtn.disabled = true;
                cancelBookingBtn.disabled = true;
                if (uploadBox) uploadBox.style.opacity = '0.5';

                showStatus('Dibatalkan', 'Booking berhasil dibatalkan.', 'secondary');

                Swal.fire({
                    icon: 'success',
                    title: 'Dibatalkan!',
                    text: 'Booking Anda telah berhasil dibatalkan.',
                    confirmButtonColor: '#0d6efd',
                    customClass: {
                        popup: 'rounded-4 shadow',
                        confirmButton: 'btn btn-primary px-4'
                    },
                    buttonsStyling: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: result.message || 'Gagal membatalkan booking.',
                    confirmButtonColor: '#0d6efd'
                });
                cancelBookingBtn.disabled = false;
                cancelBookingBtn.innerHTML = '<i class="fas fa-times-circle me-1"></i> Batalkan Booking';
            }
        } catch (err) {
            console.error("Error cancelling booking:", err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan sistem saat membatalkan booking.',
                confirmButtonColor: '#0d6efd'
            });
            cancelBookingBtn.disabled = false;
            cancelBookingBtn.innerHTML = '<i class="fas fa-times-circle me-1"></i> Batalkan Booking';
        }
    });

});