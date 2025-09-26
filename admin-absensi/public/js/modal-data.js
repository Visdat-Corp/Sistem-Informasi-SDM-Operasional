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

    // Fungsi untuk mengisi data modal
    const populateModal = (absenData) => {
        // Informasi Karyawan
        const namaKaryawan = absenData.karyawan ? absenData.karyawan.nama_karyawan : 'N/A';
        const posisi = absenData.karyawan && absenData.karyawan.posisi ? absenData.karyawan.posisi.nama_posisi : 'N/A';
        const idKaryawan = absenData.id_karyawan;

        // Avatar
        const avatarText = namaKaryawan.substring(0, 2).toUpperCase();

        // Tanggal
        const tanggal = new Date(absenData.tanggal_absen).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        // Jam
        const jamMasuk = absenData.jam_masuk || '-';
        const jamKeluar = absenData.jam_keluar || '-';

        // Total Jam
        let totalJam = '0 jam';
        if (absenData.jam_masuk && absenData.jam_keluar) {
            const masuk = new Date(`1970-01-01T${absenData.jam_masuk}`);
            const keluar = new Date(`1970-01-01T${absenData.jam_keluar}`);
            const diffMs = keluar - masuk;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
            totalJam = diffHours + ' jam';
            if (diffMinutes > 0) {
                totalJam += ' ' + diffMinutes + ' menit';
            }
        }

        // Status
        let statusText = 'Tidak Hadir';
        let statusClass = 'bg-red-100 text-red-800';
        if (absenData.keterangan === 'izin') {
            statusText = 'Izin';
            statusClass = 'bg-blue-100 text-blue-800';
        } else if (absenData.jam_masuk) {
            if (absenData.jamKerja) {
                const jamMasukNormal = absenData.jamKerja.jam_masuk_normal;
                if (absenData.jam_masuk <= jamMasukNormal) {
                    statusText = 'Hadir';
                    statusClass = 'bg-green-100 text-green-800';
                } else {
                    statusText = 'Terlambat';
                    statusClass = 'bg-yellow-100 text-yellow-800';
                }
            } else {
                statusText = 'Hadir';
                statusClass = 'bg-green-100 text-green-800';
            }
        }

        // Lokasi
        const lokasiMasuk = absenData.lokasi_absen_masuk || 'N/A';
        const lokasiKeluar = absenData.lokasi_absen_keluar || 'N/A';

        // Koordinat (assuming they are stored as strings)
        const koordinatMasuk = absenData.lokasi_absen_masuk || 'N/A'; // Adjust if stored differently
        const koordinatKeluar = absenData.lokasi_absen_keluar || 'N/A';

        // Foto
        const fotoMasuk = absenData.foto_masuk ? `/storage/${absenData.foto_masuk}` : null;
        const fotoKeluar = absenData.foto_keluar ? `/storage/${absenData.foto_keluar}` : null;

        // Update modal content
        document.querySelector('.modal-avatar').textContent = avatarText;
        document.querySelector('.modal-nama').textContent = namaKaryawan;
        document.querySelector('.modal-posisi').textContent = posisi;
        document.querySelector('.modal-id').textContent = `ID: ${idKaryawan}`;

        document.querySelector('.modal-tanggal').textContent = tanggal;
        document.querySelector('.modal-jam-masuk').textContent = jamMasuk;
        document.querySelector('.modal-jam-keluar').textContent = jamKeluar;
        document.querySelector('.modal-total-jam').textContent = totalJam;
        document.querySelector('.modal-status').textContent = statusText;
        document.querySelector('.modal-status').className = `px-2 py-1 rounded-full text-sm font-medium ${statusClass}`;

        document.querySelector('.modal-lokasi-masuk').textContent = lokasiMasuk;
        document.querySelector('.modal-lokasi-keluar').textContent = lokasiKeluar;
        document.querySelector('.modal-koordinat-masuk').textContent = koordinatMasuk;
        document.querySelector('.modal-koordinat-keluar').textContent = koordinatKeluar;

        // Handle foto
        const fotoMasukContainer = document.querySelector('.foto-masuk-container');
        const fotoKeluarContainer = document.querySelector('.foto-keluar-container');

        if (fotoMasuk) {
            fotoMasukContainer.innerHTML = `
                <img src="${fotoMasuk}" alt="Foto Masuk" class="w-full h-32 object-cover rounded-lg">
                <button class="mt-2 text-blue-600 text-sm hover:underline" onclick="window.open('${fotoMasuk}', '_blank')">Lihat Foto</button>
            `;
        } else {
            fotoMasukContainer.innerHTML = `
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-500">Foto Masuk Tidak Tersedia</p>
            `;
        }

        if (fotoKeluar) {
            fotoKeluarContainer.innerHTML = `
                <img src="${fotoKeluar}" alt="Foto Keluar" class="w-full h-32 object-cover rounded-lg">
                <button class="mt-2 text-blue-600 text-sm hover:underline" onclick="window.open('${fotoKeluar}', '_blank')">Lihat Foto</button>
            `;
        } else {
            fotoKeluarContainer.innerHTML = `
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm text-gray-500">Foto Keluar Tidak Tersedia</p>
            `;
        }
    };

    // Event listener untuk tombol detail
    detailBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const absenData = JSON.parse(btn.getAttribute('data-absen'));
            populateModal(absenData);
            openModal(detailModal);
        });
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
