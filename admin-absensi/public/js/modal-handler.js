document.addEventListener('DOMContentLoaded', function () {
            // Definisi semua elemen modal
            const tambahModal = document.getElementById('tambahModal');
            const editModal = document.getElementById('editModal');
            const hapusModal = document.getElementById('hapusModal');
            const allModals = [tambahModal, editModal, hapusModal];

            // Definisi semua tombol pemicu
            const tambahBtn = document.getElementById('tambahBtn');
            const editBtns = document.querySelectorAll('.edit-btn');
            const deleteBtns = document.querySelectorAll('.delete-btn');
            
            // Semua tombol yang berfungsi untuk menutup modal
            const closeBtns = document.querySelectorAll('.close-modal-btn');

            // Fungsi untuk membuka modal
            const openModal = (modal) => {
                // Hanya buka jika modalnya ada (ditemukan)
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            // Fungsi untuk menutup modal
            const closeModal = (modal) => {
                // Hanya tutup jika modalnya ada (ditemukan)
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            // Tambahkan event listener ke tombol "Tambah" jika ada
            if (tambahBtn) {
                tambahBtn.addEventListener('click', () => openModal(tambahModal));
            }

            // Tambahkan event listener ke setiap tombol "Edit"
            editBtns.forEach(btn => {
                btn.addEventListener('click', () => openModal(editModal));
            });

            // Tambahkan event listener ke setiap tombol "Delete"
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', () => openModal(hapusModal));
            });

            // Tambahkan event listener ke semua tombol "Close"
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    allModals.forEach(closeModal);
                });
            });

            // Tambahkan event listener untuk menutup modal saat mengklik di luar area modal (overlay)
            window.addEventListener('click', function(event) {
                if (event.target === tambahModal) closeModal(tambahModal);
                if (event.target === editModal) closeModal(editModal);
                if (event.target === hapusModal) closeModal(hapusModal);
            });
        });