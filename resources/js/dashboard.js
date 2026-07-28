document.addEventListener("DOMContentLoaded", function () {

    // ===== MODAL KOMENTAR / TESTIMONI =====
    window.openCommentModal = function () {
        const modal = document.getElementById('commentModal');
        if (modal) modal.style.display = 'flex';
    };

    window.closeCommentModal = function () {
        const modal = document.getElementById('commentModal');
        if (modal) modal.style.display = 'none';
    };

    window.addComment = function (event) {
        event.preventDefault();

        const namaEl = document.getElementById('namaKomentar');
        const komentarEl = document.getElementById('isiKomentar');

        if (!namaEl || !komentarEl) return;

        const nama = namaEl.value.trim();
        const komentar = komentarEl.value.trim();

        if (!nama || !komentar) return;

        const container = document.querySelector('.testimonial-row');

        if (container) {
            container.insertAdjacentHTML('beforeend', `
                <div class="col-md-4">
                    <div class="testimonial-card-modern">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/lego/1.jpg" class="rounded-circle me-3" width="50" height="50" alt="Avatar">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">${nama}</h6>
                                <span class="text-warning small">★★★★★</span>
                            </div>
                        </div>
                        <p class="text-muted small fst-italic mb-0">"${komentar}"</p>
                    </div>
                </div>
            `);
        }

        namaEl.value = '';
        komentarEl.value = '';
        closeCommentModal();
    };

    // Close modal saat area luar diklik
    window.addEventListener("click", function (event) {
        const modal = document.getElementById('commentModal');
        if (event.target === modal) {
            closeCommentModal();
        }
    });

});