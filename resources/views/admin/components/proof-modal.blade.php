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
                    <img id="proofModalImg" alt="Bukti Pembayaran" class="img-fluid rounded" style="display:none; max-height: 70vh; object-fit: contain; background: rgba(255,255,255,0.7); padding: 12px; box-shadow: 0 10px 30px rgba(15,23,42,0.18);" />
                    <div id="proofModalEmpty" class="text-muted" style="display:none;">Tidak ada bukti.</div>
                </div>
            </div>

            <div class="modal-footer">
                <a id="proofModalOpenLink" href="#" target="_blank" class="btn btn-outline-primary" style="display:none;">
                    <i class="fas fa-up-right-from-square"></i> Open Image
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Expose function used by booking table
    window.openProofModal = function (bookingId) {
        const modalEl = document.getElementById('proofModal');
        if (!modalEl) return;

        const loadingEl = document.getElementById('proofModalLoading');
        const imgEl = document.getElementById('proofModalImg');
        const emptyEl = document.getElementById('proofModalEmpty');
        const linkEl = document.getElementById('proofModalOpenLink');

        if (loadingEl) loadingEl.style.display = 'block';
        if (imgEl) imgEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'none';
        if (linkEl) linkEl.style.display = 'none';

        // Open Bootstrap modal immediately
        const modal = new bootstrap.Modal(modalEl);
        modal.show();

        // If you already embed the full proof URL in data attributes later, you can set it directly.
        // Currently we try to discover the image url from the row thumbnail.
        const rowImg = document.querySelector(`#bookingProofThumb-${bookingId}`);
        if (rowImg && rowImg.dataset && rowImg.dataset.proofUrl) {
            const raw = rowImg.dataset.proofUrl;
            if (typeof raw !== 'string' || !raw.trim()) {
                if (loadingEl) loadingEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'block';
                return;
            }
            const rawTrim = raw.trim();
            const url = /^(https?:\/\/|\/)/i.test(rawTrim)
                ? rawTrim
                : `http://localhost:5000/uploads/${rawTrim}`;

            if (imgEl) {
                imgEl.src = url;
                imgEl.style.display = 'block';
            }
            if (linkEl) {
                linkEl.href = url;
                linkEl.style.display = 'inline-flex';
            }
            if (loadingEl) loadingEl.style.display = 'none';
            return;
        }

        // Fallback: try to find an image that contains bookingId in onclick (legacy)
        // If not found, show empty state.
        if (loadingEl) loadingEl.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
    };
</script>

