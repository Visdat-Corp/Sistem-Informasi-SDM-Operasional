<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lokasi Kerja - PT. VISDAT TEKNIK UTAMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        #mapTambah, #mapEdit {
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen antialiased">
    @include('sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        @include('partials.header', ['title' => 'Kelola Lokasi Kerja', 'description' => 'Selamat datang kembali, Admin!'])

        @if(session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bar Aksi (Tombol Tambah) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Tombol Tambah Lokasi Kerja -->
            <button id="tambahBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Tambah Lokasi Kerja</span>
            </button>
        </div>

        <!-- Tabel Data Lokasi Kerja -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lokasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Latitude</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Longitude</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Radius (m)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($lokasis as $lokasi)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lokasi->id_lokasi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $lokasi->lokasi_kerja }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lokasi->latitude ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lokasi->longitude ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $lokasi->radius }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                            <button class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200" data-id="{{ $lokasi->id_lokasi }}" data-nama="{{ $lokasi->lokasi_kerja }}" data-lat="{{ $lokasi->latitude }}" data-lng="{{ $lokasi->longitude }}" data-radius="{{ $lokasi->radius }}">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                </svg>
                            </button>
                            <button class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200" data-id="{{ $lokasi->id_lokasi }}">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.134H8.09a2.09 2.09 0 00-2.09 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data lokasi kerja.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Lokasi Kerja -->
        <div id="tambahModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Tambah Lokasi Kerja Baru</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form class="p-6" method="POST" action="{{ route('store-lokasi') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi Kerja</label>
                            <input type="text" name="lokasi_kerja" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" step="any" name="latitude" id="tambahLat" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Contoh: -6.2088">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" step="any" name="longitude" id="tambahLng" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 106.8456">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                            <input type="number" name="radius" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" value="100" min="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Lokasi di Peta</label>
                            <div id="mapTambah" class="border border-gray-300 rounded-md"></div>
                        </div>
                        <div>
                            <button type="button" id="getLocationBtn" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                Dapatkan Lokasi Saat Ini (GPS)
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Lokasi Kerja -->
        <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Edit Lokasi Kerja</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form class="p-6" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lokasi Kerja</label>
                            <input type="text" name="lokasi_kerja" id="editNama" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                                <input type="number" step="any" name="latitude" id="editLat" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Contoh: -6.2088">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                                <input type="number" step="any" name="longitude" id="editLng" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 106.8456">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                            <input type="number" name="radius" id="editRadius" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" min="1" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Lokasi di Peta</label>
                            <div id="mapEdit" class="border border-gray-300 rounded-md"></div>
                        </div>
                        <div>
                            <button type="button" id="editGetLocationBtn" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                                Dapatkan Lokasi Saat Ini (GPS)
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Hapus Lokasi Kerja -->
        <div id="hapusModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data lokasi kerja ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <form method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center space-x-4">
                            <button type="button" class="close-modal-btn px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/modal-handler.js') }}"></script>
    <script src="{{ asset('js/update-clock.js') }}"></script>

    <script>
        let mapTambah, markerTambah;
        let mapEdit, markerEdit;

        // Function to initialize map for add modal
        function initMapTambah(lat = -6.2088, lng = 106.8456) {
            if (mapTambah) return;
            mapTambah = L.map('mapTambah').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapTambah);

            markerTambah = L.marker([lat, lng], {draggable: true}).addTo(mapTambah);
            markerTambah.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('tambahLat').value = pos.lat;
                document.getElementById('tambahLng').value = pos.lng;
            });

            mapTambah.on('click', function(e) {
                markerTambah.setLatLng(e.latlng);
                document.getElementById('tambahLat').value = e.latlng.lat;
                document.getElementById('tambahLng').value = e.latlng.lng;
            });
        }

        // Function to initialize map for edit modal
        function initMapEdit(lat = -6.2088, lng = 106.8456) {
            if (mapEdit) return;
            mapEdit = L.map('mapEdit').setView([lat, lng], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(mapEdit);

            markerEdit = L.marker([lat, lng], {draggable: true}).addTo(mapEdit);
            markerEdit.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                document.getElementById('editLat').value = pos.lat;
                document.getElementById('editLng').value = pos.lng;
            });

            mapEdit.on('click', function(e) {
                markerEdit.setLatLng(e.latlng);
                document.getElementById('editLat').value = e.latlng.lat;
                document.getElementById('editLng').value = e.latlng.lng;
            });
        }

        // Function to get current location
        function getCurrentLocation(latInput, lngInput, marker) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    latInput.value = position.coords.latitude;
                    lngInput.value = position.coords.longitude;
                    if (marker) {
                        marker.setLatLng([position.coords.latitude, position.coords.longitude]);
                    }
                }, function(error) {
                    alert('Error getting location: ' + error.message);
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        }

        // Event listeners for GPS buttons
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            const latInput = document.getElementById('tambahLat');
            const lngInput = document.getElementById('tambahLng');
            getCurrentLocation(latInput, lngInput, markerTambah);
        });

        document.getElementById('editGetLocationBtn').addEventListener('click', function() {
            const latInput = document.getElementById('editLat');
            const lngInput = document.getElementById('editLng');
            getCurrentLocation(latInput, lngInput, markerEdit);
        });

        // Edit button click handler
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const lat = this.getAttribute('data-lat') || -6.2088;
                const lng = this.getAttribute('data-lng') || 106.8456;
                const radius = this.getAttribute('data-radius');

                document.getElementById('editForm').action = `/kelola-lokasi/${id}`;
                document.getElementById('editNama').value = nama;
                document.getElementById('editLat').value = lat;
                document.getElementById('editLng').value = lng;
                document.getElementById('editRadius').value = radius;

                // Initialize map for edit
                setTimeout(() => initMapEdit(lat, lng), 100);

                document.getElementById('editModal').classList.remove('hidden');
            });
        });

        // Delete button click handler
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                document.getElementById('deleteForm').action = `/kelola-lokasi/${id}`;
                document.getElementById('hapusModal').classList.remove('hidden');
            });
        });

        // Modal close handlers
        document.querySelectorAll('.close-modal-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.fixed').classList.add('hidden');
            });
        });

        // Show add modal
        document.getElementById('tambahBtn').addEventListener('click', function() {
            // Initialize map for add
            setTimeout(() => initMapTambah(), 100);
            document.getElementById('tambahModal').classList.remove('hidden');
        });
    </script>

</body>
</html>
