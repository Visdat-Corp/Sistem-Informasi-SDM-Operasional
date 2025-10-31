<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Pengecualian - PT. VISDAT TEKNIK UTAMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen antialiased">
    @include('sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        @include('partials.header', [
            'title' => 'Kelola Jadwal Pengecualian',
            'description' => 'Kelola hari libur nasional dan cuti bersama',
        ])

        @if (session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bar Aksi (Tombol, Cari) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Tombol Tambah -->
            <button id="tambahBtn"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>Tambah Hari Libur</span>
            </button>

            <!-- Pencarian -->
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('kelola-jadwal-pengecualian') }}" class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama hari libur..."
                            class="w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('kelola-jadwal-pengecualian') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Hari Libur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jadwalPengecualians as $index => $jadwal)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwalPengecualians->firstItem() + $index }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d-m-Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $jadwal->nama_hari_libur }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($jadwal->jenis == 'libur_nasional')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Libur Nasional</span>
                                @elseif($jadwal->jenis == 'cuti_bersama')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Cuti Bersama</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Lainnya</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $jadwal->keterangan ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button onclick="editJadwal({{ $jadwal->id_jadwal_pengecualian }}, '{{ $jadwal->tanggal }}', '{{ $jadwal->nama_hari_libur }}', '{{ $jadwal->jenis }}', '{{ $jadwal->keterangan }}')"
                                    class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                <form action="{{ route('destroy-jadwal-pengecualian', $jadwal->id_jadwal_pengecualian) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Yakin ingin menghapus jadwal ini?')"
                                        class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="px-6 py-4 bg-gray-50">
                {{ $jadwalPengecualians->links() }}
            </div>
        </div>
    </main>

    <!-- Modal Tambah -->
    <div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-500 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Tambah Hari Libur</h2>
            </div>
            <form action="{{ route('store-jadwal-pengecualian') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Hari Libur</label>
                    <input type="text" name="nama_hari_libur" required placeholder="Contoh: Hari Kemerdekaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                    <select name="jenis" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="libur_nasional">Libur Nasional</option>
                        <option value="cuti_bersama">Cuti Bersama</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="3" placeholder="Keterangan tambahan..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Simpan</button>
                    <button type="button" id="closeModalTambah" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">Edit Hari Libur</h2>
            </div>
            <form id="formEdit" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Hari Libur</label>
                    <input type="text" name="nama_hari_libur" id="editNamaHariLibur" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis</label>
                    <select name="jenis" id="editJenis" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="libur_nasional">Libur Nasional</option>
                        <option value="cuti_bersama">Cuti Bersama</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan (Opsional)</label>
                    <textarea name="keterangan" id="editKeterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">Perbarui</button>
                    <button type="button" id="closeModalEdit" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg transition">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Tambah
        const tambahBtn = document.getElementById('tambahBtn');
        const modalTambah = document.getElementById('modalTambah');
        const closeModalTambah = document.getElementById('closeModalTambah');

        tambahBtn.addEventListener('click', () => {
            modalTambah.classList.remove('hidden');
            modalTambah.classList.add('flex');
        });

        closeModalTambah.addEventListener('click', () => {
            modalTambah.classList.add('hidden');
            modalTambah.classList.remove('flex');
        });

        // Modal Edit
        const modalEdit = document.getElementById('modalEdit');
        const closeModalEdit = document.getElementById('closeModalEdit');
        const formEdit = document.getElementById('formEdit');

        function editJadwal(id, tanggal, nama, jenis, keterangan) {
            document.getElementById('editTanggal').value = tanggal;
            document.getElementById('editNamaHariLibur').value = nama;
            document.getElementById('editJenis').value = jenis;
            document.getElementById('editKeterangan').value = keterangan || '';
            formEdit.action = `/kelola-jadwal-pengecualian/${id}`;
            modalEdit.classList.remove('hidden');
            modalEdit.classList.add('flex');
        }

        closeModalEdit.addEventListener('click', () => {
            modalEdit.classList.add('hidden');
            modalEdit.classList.remove('flex');
        });

        // Close modal on outside click
        modalTambah.addEventListener('click', (e) => {
            if (e.target === modalTambah) {
                modalTambah.classList.add('hidden');
                modalTambah.classList.remove('flex');
            }
        });

        modalEdit.addEventListener('click', (e) => {
            if (e.target === modalEdit) {
                modalEdit.classList.add('hidden');
                modalEdit.classList.remove('flex');
            }
        });
    </script>
</body>

</html>
