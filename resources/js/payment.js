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

    // 30min countdown
    let timeLeft = 30 * 60; // seconds
    countdownInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(countdownInterval);
            timerEl.textContent = '00:00';
            timerEl.classList.add('expired');
            statusBadge.textContent = 'Expired';
            statusBadge.className = 'badge bg-danger';
            paymentForm.style.display = 'none';
            showStatus('Waktu Habis', 'Booking expired. Silakan booking ulang.', 'danger');
            return;
        }

        const mins = Math.floor(timeLeft / 60);
        const secs = timeLeft % 60;
        timerEl.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        timeLeft--;
    }, 1000);

    // Status check
    const currentStatus = statusBadge.textContent.toLowerCase();
    if (currentStatus.includes('menunggu') || currentStatus.includes('confirmed') || currentStatus.includes('expired')) {
        uploadBtn.disabled = true;
        uploadBox.style.opacity = '0.5';
        if (currentStatus.includes('menunggu')) {
            showStatus('Menunggu Verifikasi', 'Bukti pembayaran Anda sedang diverifikasi admin.', 'info');
        } else if (currentStatus.includes('confirmed')) {
            showStatus('Berhasil', 'Pembayaran sudah dikonfirmasi.', 'success');
        }
    }

    function showStatus(title, text, type) {
        paymentForm.style.display = 'none';
        messageTitle.textContent = title;
        messageText.textContent = text;
        statusMessage.className = `mt-4 text-center alert alert-${type}`;
        statusMessage.style.display = 'block';
    }

    // Upload preview
    proofFile.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 2*1024*1024) {
            alert('File max 2MB');
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            uploadPreview.style.display = 'block';
            uploadBtn.disabled = false;
            uploadBtn.textContent = 'Upload & Bayar';
        };
        reader.readAsDataURL(file);
    });

    uploadBox.addEventListener('click', () => proofFile.click());
    uploadBox.addEventListener('dragover', e => {
        e.preventDefault();
        uploadBox.classList.add('dragover');
    });
    uploadBox.addEventListener('dragleave', () => uploadBox.classList.remove('dragover'));
    uploadBox.addEventListener('drop', e => {
        e.preventDefault();
        uploadBox.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        proofFile.files = e.dataTransfer.files;
        proofFile.dispatchEvent(new Event('change'));
    });

    uploadBtn.addEventListener('click', async () => {
        const formData = new FormData();
        formData.append('proof', proofFile.files[0]);

        try {
            const res = await fetch(`/booking/payment/${bookingId}/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await res.json();
            if (result.success) {
                showStatus('Uploaded', 'Bukti berhasil diupload. Menunggu verifikasi admin.', 'info');
            } else {
                alert('Upload gagal: ' + (result.message || 'Unknown error'));
            }
        } catch (err) {
            alert('Error: ' + err.message);
        }
    });
});
