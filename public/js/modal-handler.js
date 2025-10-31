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
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const nama = this.getAttribute('data-nama');
                    const username = this.getAttribute('data-username');
                    const email = this.getAttribute('data-email');
                    const posisi = this.getAttribute('data-posisi');
                    const departemen = this.getAttribute('data-departemen');
                    const lokasi = this.getAttribute('data-lokasi');
                    const status = this.getAttribute('data-status');

                    // Populate the edit form
                    const editNama = document.getElementById('editNama');
                    const editUsername = document.getElementById('editUsername');
                    const editEmail = document.getElementById('editEmail');
                    const editPosisi = document.getElementById('editPosisi');
                    const editDepartemen = document.getElementById('editDepartemen');
                    const editLokasi = document.getElementById('editLokasi');
                    const editStatus = document.getElementById('editStatus');

                    if (editNama) editNama.value = nama;
                    if (editUsername) editUsername.value = username || '';
                    if (editEmail) editEmail.value = email || '';
                    if (editPosisi) editPosisi.value = posisi || '';
                    if (editDepartemen) editDepartemen.value = departemen || '';
                    if (editLokasi) editLokasi.value = lokasi || '';
                    if (editStatus) editStatus.value = status || '';

                    // Set the form action based on current page
                    const editForm = document.getElementById('editForm');
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('kelola-karyawan')) {
                        editForm.action = `/kelola-karyawan/${id}`;
                    } else if (currentPath.includes('kelola-departemen')) {
                        editForm.action = `/kelola-departemen/${id}`;
                    }

                    openModal(editModal);
                });
            });

            // Tambahkan event listener ke setiap tombol "Delete"
            deleteBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');

                    // Set the form action based on current page
                    const deleteForm = document.getElementById('deleteForm');
                    const currentPath = window.location.pathname;
                    if (currentPath.includes('kelola-karyawan')) {
                        deleteForm.action = `/kelola-karyawan/${id}`;
                    } else if (currentPath.includes('kelola-departemen')) {
                        deleteForm.action = `/kelola-departemen/${id}`;
                    }

                    openModal(hapusModal);
                });
            });

            // Tambahkan event listener ke semua tombol "Close"
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    allModals.forEach(closeModal);
                });
            });
        });
