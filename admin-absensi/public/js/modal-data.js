document.addEventListener('DOMContentLoaded', function () {
            const detailModal = document.getElementById('detailModal');
            const detailBtns = document.querySelectorAll('.detail-btn');
            const closeBtns = document.querySelectorAll('.close-modal-btn');

            // Fungsi untuk membuka modal
            const openModal = (modal) => {
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            // Fungsi untuk menutup modal
            const closeModal = (modal) => {
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            // Event listener untuk tombol detail
            detailBtns.forEach(btn => {
                btn.addEventListener('click', () => openModal(detailModal));
            });

            // Event listener untuk tombol close
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    closeModal(detailModal);
                });
            });

            // Tutup modal saat klik di luar area modal
            window.addEventListener('click', function(event) {
                if (event.target === detailModal) {
                    closeModal(detailModal);
                }
            });
        });