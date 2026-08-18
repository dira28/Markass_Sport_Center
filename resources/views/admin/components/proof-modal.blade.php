{{-- Admin Proof Modal (used by admin/pages/booking.blade.php) --}}

<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header" style="border-bottom: 1px solid rgba(0,0,0,0.06)">
                <h5 class="modal-title" id="proofModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="text-center">
                    <div id="proofModalLoading" class="text-muted" style="display:none;">Loading...</div>
                    <img id="proofModalImg" alt="Bukti Pembayaran" class="img-fluid rounded"
                        style="display:none; max-height: 70vh; object-fit: contain; background: rgba(255,255,255,0.7); padding: 12px; box-shadow: 0 10px 30px rgba(15,23,42,0.18);" />
                    <div id="proofModalEmpty" class="text-muted" style="display:none;">Tidak ada bukti pembayaran.</div>
                </div>
            </div>

            <div class="modal-footer">
                <a id="proofModalOpenLink" href="#" target="_blank" class="btn btn-outline-primary"
                    style="display:none;">
                    <i class="fas fa-external-link-alt me-1"></i> Buka Gambar
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    window.openProofModal = function (bookingId) {
        const modalEl = document.getElementById('proofModal');
        if (!modalEl) return;

        const loadingEl = document.getElementById('proofModalLoading');
        const imgEl = document.getElementById('proofModalImg');
        const emptyEl = document.getElementById('proofModalEmpty');
        const linkEl = document.getElementById('proofModalOpenLink');

        // Reset state modal
        if (loadingEl) loadingEl.style.display = 'block';
        if (imgEl) {
            imgEl.style.display = 'none';
            imgEl.src = '';
        }
        if (emptyEl) emptyEl.style.display = 'none';
        if (linkEl) linkEl.style.display = 'none';

        // Buka Bootstrap Modal
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Cari elemen gambar thumbnail berdasarkan bookingId
        const rowImg = document.querySelector(`#bookingProofThumb-${bookingId}`);

        if (rowImg && rowImg.dataset && rowImg.dataset.proofUrl) {
            const url = rowImg.dataset.proofUrl.trim();

            if (!url) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }

            // Set sumber gambar dan link tombol "Buka Gambar"
            if (imgEl) {
                imgEl.src = url;
                imgEl.onload = function () {
                    if (loadingEl) loadingEl.style.display = 'none';
                    imgEl.style.display = 'block';
                };
                imgEl.onerror = function () {
                    if (loadingEl) loadingEl.style.display = 'none';
                    if (emptyEl) {
                        emptyEl.innerText = 'Gambar tidak dapat dimuat (File hilang/broken).';
                        emptyEl.style.display = 'block';
                    }
                };
            }

            if (linkEl) {
                linkEl.href = url;
                linkEl.style.display = 'inline-flex';
            }
            return;
        }

        // Jika data-proof-url tidak ditemukan
        if (loadingEl) loadingEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
    };
</script>