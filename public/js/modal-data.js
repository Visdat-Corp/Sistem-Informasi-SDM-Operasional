document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailModal');

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
        const namaKaryawan = absenData.nama_karyawan || 'N/A';
        const posisi = absenData.posisi || 'N/A';
        const departemen = absenData.departemen || 'N/A';
        const idKaryawan = absenData.id_karyawan;

        // Avatar
        const avatarText = namaKaryawan.substring(0, 2).toUpperCase();

        // Tanggal
        const tanggal = new Date(absenData.tanggal).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });

        // Jam
        const jamMasuk = absenData.jam_masuk || '-';
        const jamKeluar = absenData.jam_keluar || '-';

        // Total Jam
        const totalJam = absenData.total_jam || '0 jam';

        // Status
        const statusText = absenData.status || 'Tidak Hadir';
        let statusClass = 'bg-red-100 text-red-800';
        if (statusText === 'Hadir') statusClass = 'bg-green-100 text-green-800';
        else if (statusText === 'Terlambat') statusClass = 'bg-yellow-100 text-yellow-800';
        else if (statusText === 'Izin') statusClass = 'bg-blue-100 text-blue-800';

        // Lokasi
        const lokasiMasuk = absenData.lokasi_masuk || 'N/A';
        const lokasiKeluar = absenData.lokasi_keluar || 'N/A';

        // Koordinat (assuming they are stored as strings like "lat,lng")
        const koordinatMasuk = absenData.lokasi_masuk || 'N/A';
        const koordinatKeluar = absenData.lokasi_keluar || 'N/A';

        // Foto
        const fotoMasuk = absenData.absen && absenData.absen.foto_masuk ? `/storage/${absenData.absen.foto_masuk}` : null;
        const fotoKeluar = absenData.absen && absenData.absen.foto_keluar ? `/storage/${absenData.absen.foto_keluar}` : null;

        // Update modal content
        const avatarEl = document.querySelector('.modal-avatar');
        if (avatarEl) avatarEl.textContent = avatarText;
        const namaEl = document.querySelector('.modal-nama');
        if (namaEl) namaEl.textContent = namaKaryawan;
        const posisiEl = document.querySelector('.modal-posisi');
        if (posisiEl) posisiEl.textContent = posisi;
        const departemenEl = document.querySelector('.modal-departemen');
        if (departemenEl) departemenEl.textContent = departemen;
        const idEl = document.querySelector('.modal-id');
        if (idEl) idEl.textContent = `ID: ${idKaryawan}`;

        const tanggalEl = document.querySelector('.modal-tanggal');
        if (tanggalEl) tanggalEl.textContent = tanggal;
        const jamMasukEl = document.querySelector('.modal-jam-masuk');
        if (jamMasukEl) jamMasukEl.textContent = jamMasuk;
        const jamKeluarEl = document.querySelector('.modal-jam-keluar');
        if (jamKeluarEl) jamKeluarEl.textContent = jamKeluar;
        const totalJamEl = document.querySelector('.modal-total-jam');
        if (totalJamEl) totalJamEl.textContent = totalJam;
        const statusEl = document.querySelector('.modal-status');
        if (statusEl) {
            statusEl.textContent = statusText;
            statusEl.className = `px-2 py-1 rounded-full text-sm font-medium ${statusClass}`;
        }

        // Handle maps
        const mapMasukContainer = document.querySelector('.modal-map-masuk');
        const mapKeluarContainer = document.querySelector('.modal-map-keluar');

        if (koordinatMasuk && koordinatMasuk !== 'N/A') {
            const coords = koordinatMasuk.split(',');
            if (coords.length === 2) {
                const lat = coords[0].trim();
                const lng = coords[1].trim();
                mapMasukContainer.innerHTML = `<iframe width="100%" height="200" frameborder="0" style="border:0" src="https://maps.google.com/maps?q=${lat},${lng}&output=embed" allowfullscreen></iframe>`;
            } else {
                mapMasukContainer.innerHTML = `<p class="text-sm text-gray-500">Koordinat tidak valid</p>`;
            }
        } else {
            mapMasukContainer.innerHTML = `<p class="text-sm text-gray-500">Lokasi Masuk Tidak Tersedia</p>`;
        }

        if (koordinatKeluar && koordinatKeluar !== 'N/A') {
            const coords = koordinatKeluar.split(',');
            if (coords.length === 2) {
                const lat = coords[0].trim();
                const lng = coords[1].trim();
                mapKeluarContainer.innerHTML = `<iframe width="100%" height="200" frameborder="0" style="border:0" src="https://maps.google.com/maps?q=${lat},${lng}&output=embed" allowfullscreen></iframe>`;
            } else {
                mapKeluarContainer.innerHTML = `<p class="text-sm text-gray-500">Koordinat tidak valid</p>`;
            }
        } else {
            mapKeluarContainer.innerHTML = `<p class="text-sm text-gray-500">Lokasi Keluar Tidak Tersedia</p>`;
        }

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

    // Event listener untuk tombol detail menggunakan event delegation
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('detail-btn')) {
            const absenData = JSON.parse(e.target.getAttribute('data-absen'));
            // HAPUS BARIS INI: closeModal(detailModal);
            populateModal(absenData);
            openModal(detailModal);
        }
    });

    // Event listener untuk tombol close menggunakan event delegation
    document.addEventListener('click', function(e) {
        if (e.target.closest('.close-modal-btn')) {
            closeModal(detailModal);
        }
    });

    // Tutup modal saat klik di luar area modal
    window.addEventListener('click', function(event) {
        if (event.target === detailModal) {
            closeModal(detailModal);
        }
    });
});
