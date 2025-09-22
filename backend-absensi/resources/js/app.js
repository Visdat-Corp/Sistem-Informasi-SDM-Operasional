import './bootstrap';

// Dashboard Sidebar Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.getElementById('toggleSidebar');
    const mainSidebar = document.getElementById('mainSidebar');
    const logoText = document.getElementById('logoText');
    const navTexts = document.querySelectorAll('.nav-text');
    const navArrows = document.querySelectorAll('.nav-arrow');
    
    // Check if elements exist (only run on dashboard page)
    if (toggleButton && mainSidebar) {
        let isMinimized = false;

        toggleButton.addEventListener('click', function() {
            isMinimized = !isMinimized;
            
            if (isMinimized) {
                // Minimize sidebar - langsung tanpa animasi
                mainSidebar.classList.remove('w-60');
                mainSidebar.classList.add('w-16');
                
                // Hide text elements langsung
                if (logoText) logoText.style.display = 'none';
                navTexts.forEach(text => text.style.display = 'none');
                navArrows.forEach(arrow => arrow.style.display = 'none');
                
            } else {
                // Expand sidebar - langsung tanpa animasi
                mainSidebar.classList.remove('w-16');
                mainSidebar.classList.add('w-60');
                
                // Show text elements langsung
                if (logoText) logoText.style.display = '';
                navTexts.forEach(text => text.style.display = '');
                navArrows.forEach(arrow => arrow.style.display = '');
            }
        });
    }

    // Kelola Karyawan Modal Functionality
    const tambahKaryawanBtn = document.getElementById('tambahKaryawanBtn');
    const tambahKaryawanModal = document.getElementById('tambahKaryawanModal');
    const closeTambahModal = document.getElementById('closeTambahModal');
    const cancelTambah = document.getElementById('cancelTambah');
    const tambahKaryawanForm = document.getElementById('tambahKaryawanForm');

    const editKaryawanModal = document.getElementById('editKaryawanModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEdit = document.getElementById('cancelEdit');
    const editKaryawanForm = document.getElementById('editKaryawanForm');
    const editButtons = document.querySelectorAll('.edit-btn');

    

    // Tambah Karyawan Modal
    if (tambahKaryawanBtn && tambahKaryawanModal) {
        tambahKaryawanBtn.addEventListener('click', function() {
            tambahKaryawanModal.classList.remove('hidden');
        });

        closeTambahModal.addEventListener('click', function() {
            tambahKaryawanModal.classList.add('hidden');
        });

        cancelTambah.addEventListener('click', function() {
            tambahKaryawanModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        tambahKaryawanModal.addEventListener('click', function(e) {
            if (e.target === tambahKaryawanModal) {
                tambahKaryawanModal.classList.add('hidden');
            }
        });

        // Handle form submission
        tambahKaryawanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would normally send data to server
            alert('Karyawan berhasil ditambahkan!');
            tambahKaryawanModal.classList.add('hidden');
            tambahKaryawanForm.reset();
        });
    }

    // Edit Karyawan Modal
    if (editKaryawanModal) {
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const jabatan = this.getAttribute('data-jabatan');
                const email = this.getAttribute('data-email');
                const telepon = this.getAttribute('data-telepon');
                const alamat = this.getAttribute('data-alamat');
                const tanggal = this.getAttribute('data-tanggal');

                // Fill form with data
                document.getElementById('editId').value = id;
                document.getElementById('editNama').value = nama;
                document.getElementById('editJabatan').value = jabatan;
                document.getElementById('editEmail').value = email;
                document.getElementById('editTelepon').value = telepon;
                document.getElementById('editAlamat').value = alamat;
                document.getElementById('editTanggal').value = tanggal;

                editKaryawanModal.classList.remove('hidden');
            });
        });

        closeEditModal.addEventListener('click', function() {
            editKaryawanModal.classList.add('hidden');
        });

        cancelEdit.addEventListener('click', function() {
            editKaryawanModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        editKaryawanModal.addEventListener('click', function(e) {
            if (e.target === editKaryawanModal) {
                editKaryawanModal.classList.add('hidden');
            }
        });

        // Handle form submission
        editKaryawanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Here you would normally send data to server
            alert('Data karyawan berhasil diupdate!');
            editKaryawanModal.classList.add('hidden');
        });
    }

    // Delete Karyawan Functionality
    const deleteButtons = document.querySelectorAll('.delete-btn');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const nama = this.getAttribute('data-nama');
            const id = this.getAttribute('data-id');
            
            if (confirm(`Apakah Anda yakin ingin menghapus karyawan "${nama}"?\n\nTindakan ini tidak dapat dibatalkan.`)) {
                // Here you would normally send delete request to server
                // For demo purposes, we'll add a fade effect before removing
                const row = this.closest('tr');
                row.style.opacity = '0.5';
                
                setTimeout(() => {
                    row.remove();
                    alert(`Karyawan "${nama}" berhasil dihapus!`);
                }, 300);
            }
        });
    });
});
