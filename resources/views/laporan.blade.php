<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Memuat Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Vite directive, jika Anda menggunakan build tools Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Laporan</title>
</head>
<body class="bg-gray-100 flex min-h-screen antialiased">
    @include('sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-white p-6">
        @include('partials.header', ['title' => 'Laporan', 'description' => 'Laporan Absensi Karyawan'])
        <!-- end of Header -->

        <!-- Laporan Absensi Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Laporan Absensi Karyawan</h2>

            <div class="grid md:grid-cols-2 gap-10">
                <!-- Filter Form -->
                <div>
                    <h3 class="text-sm font-medium mb-3">Filter Laporan</h3>
                    <form class="space-y-4" method="GET" action="{{ route('laporan') }}">
                        <div>
                            <label for="type" class="block text-sm text-gray-700 mb-1">Tipe Laporan</label>
                            <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="bulanan" {{ $type == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                                <option value="mingguan" {{ $type == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                                <option value="harian" {{ $type == 'harian' ? 'selected' : '' }}>Harian</option>
                            </select>
                        </div>
                        <div id="bulanan-fields" class="space-y-4" style="{{ $type == 'bulanan' || !$type ? '' : 'display: none;' }}">
                            <div>
                                <label for="bulan" class="block text-sm text-gray-700 mb-1">Bulan</label>
                                <select id="bulan" name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label for="tahun" class="block text-sm text-gray-700 mb-1">Tahun</label>
                                <select id="tahun" name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div id="mingguan-fields" class="space-y-4" style="{{ $type == 'mingguan' ? '' : 'display: none;' }}">
                            <div>
                                <label for="minggu" class="block text-sm text-gray-700 mb-1">Minggu</label>
                                <input type="week" id="minggu" name="minggu" value="{{ $minggu ?? \Carbon\Carbon::now()->format('Y-\WW') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div id="harian-fields" class="space-y-4" style="{{ $type == 'harian' ? '' : 'display: none;' }}">
                            <div>
                                <label for="tanggal" class="block text-sm text-gray-700 mb-1">Tanggal</label>
                                <input type="date" id="tanggal" name="tanggal" value="{{ $tanggal ?? \Carbon\Carbon::today()->toDateString() }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label for="search" class="block text-sm text-gray-700 mb-1">Cari Nama Karyawan</label>
                            <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Masukkan nama karyawan" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Terapkan Filter
                        </button>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div>
                    <h3 class="text-sm font-medium mb-3">Ringkasan Absensi</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-green-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-700">{{ $stats['hadir_bulan_ini'] }}</div>
                            <div class="text-xs text-green-600">Total Karyawan Hadir Bulan Ini</div>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-700">{{ $stats['terlambat_bulan_ini'] }}</div>
                            <div class="text-xs text-yellow-600">Total Terlambat Bulan Ini</div>
                        </div>
                        <div class="bg-purple-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-purple-700">{{ $stats['tingkat_kehadiran'] }}%</div>
                            <div class="text-xs text-purple-600">Tingkat Kehadiran Rata-rata</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Laporan -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Detail Laporan Kehadiran</h2>
                <a href="{{ route('laporan.export', ['type' => $type, 'bulan' => $bulan, 'tahun' => $tahun, 'minggu' => $minggu, 'tanggal' => $tanggal, 'search' => $search]) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2 inline" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Ekspor Excel
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_absen', 'sort_direction' => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                    No
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"/>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'nama_karyawan', 'sort_direction' => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                    Nama Karyawan
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"/>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'tanggal_absen', 'sort_direction' => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                    Tanggal
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"/>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jam_masuk', 'sort_direction' => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                    Jam Masuk
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"/>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'jam_keluar', 'sort_direction' => $sortDirection == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                                    Jam Pulang
                                    <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 4a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1V5a1 1 0 00-1-1H5z"/>
                                    </svg>
                                </a>
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Jam Kerja
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($processedAbsensis as $absen)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['no'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['nama_karyawan'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['tanggal'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['jam_masuk'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['jam_keluar'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['total_jam_kerja'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                @if($absen['status'] == 'Hadir')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                                @elseif($absen['status'] == 'Terlambat')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>
                                @elseif($absen['status'] == 'Pulang Cepat')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Pulang Cepat</span>
                                @elseif($absen['status'] == 'Tidak Konsisten')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Tidak Konsisten</span>
                                @elseif($absen['status'] == 'Lembur')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Lembur</span>
                                @elseif($absen['status'] == 'Tidak Hadir')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak Hadir</span>
                                @elseif($absen['status'] == 'Izin')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Izin</span>
                                @elseif($absen['status'] == 'Sakit')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-pink-100 text-pink-800">Sakit</span>
                                @elseif($absen['status'] == 'Cuti')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800">Cuti</span>
                                @elseif($absen['status'] == 'Dinas Luar')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-cyan-100 text-cyan-800">Dinas Luar</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $absen['status'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                <button onclick="editStatus({{ $absen['id_karyawan'] }}, '{{ $absen['tanggal'] }}', '{{ $absen['status'] }}')" class="bg-blue-500 text-white px-2 py-1 rounded text-xs hover:bg-blue-600">Edit</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $absensis->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Tabel Laporan Lembur -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Laporan Lembur</h2>
                <button type="button" class="btn btn-success" onclick="exportLaporanLembur()">Export Excel Lembur</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posisi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam Kerja</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($processedOvertime as $index => $absen)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $overtimeAbsensis->firstItem() + $index }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['nama_karyawan'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['departemen'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['posisi'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($absen['tanggal_absen'])->format('d-m-Y') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['jam_masuk'] ?: '-' }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['jam_keluar'] ?: '-' }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $absen['total_jam_kerja'] }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $absen['status'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">Tidak ada data lembur untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Overtime -->
            <div class="mt-4">
                {{ $overtimeAbsensis->appends(request()->query())->links() }}
            </div>
        </div>

        <!-- Export Form for Overtime -->
        <form id="exportLemburForm" method="GET" action="{{ route('laporan.export-lembur') }}" style="display: none;">
            <input type="hidden" name="type" value="{{ $type }}">
            <input type="hidden" name="bulan" value="{{ $bulan }}">
            <input type="hidden" name="tahun" value="{{ $tahun }}">
            <input type="hidden" name="minggu" value="{{ $minggu }}">
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="search" value="{{ $search }}">
        </form>

    </main>

    <!-- Modal Edit Status -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="my-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Edit Status Absensi</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="editForm">
                        <input type="hidden" id="editIdKaryawan" name="id_karyawan">
                        <input type="hidden" id="editTanggal" name="tanggal">
                        <div class="mb-4">
                            <label for="editStatus" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="editStatus" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="tidak hadir">Tidak Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="cuti">Cuti</option>
                                <option value="dinas luar">Dinas Luar</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="flex items-center px-4 py-3">
                    <button id="saveBtn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        Simpan
                    </button>
                    <button id="cancelBtn" class="ml-3 px-4 py-2 bg-gray-300 text-gray-900 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editStatus(idKaryawan, tanggal, currentStatus) {
            document.getElementById('editIdKaryawan').value = idKaryawan;
            document.getElementById('editTanggal').value = tanggal;
            document.getElementById('editStatus').value = currentStatus.toLowerCase();
            document.getElementById('editModal').classList.remove('hidden');
        }

        function exportLaporanLembur() {
            document.getElementById('exportLemburForm').submit();
        }

        document.getElementById('type').addEventListener('change', function() {
            const type = this.value;
            document.getElementById('bulanan-fields').style.display = type === 'bulanan' ? '' : 'none';
            document.getElementById('mingguan-fields').style.display = type === 'mingguan' ? '' : 'none';
            document.getElementById('harian-fields').style.display = type === 'harian' ? '' : 'none';
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            document.getElementById('editModal').classList.add('hidden');
        });

        document.getElementById('saveBtn').addEventListener('click', function() {
            const formData = new FormData(document.getElementById('editForm'));
            const data = {
                id_karyawan: formData.get('id_karyawan'),
                tanggal: formData.get('tanggal'),
                status: formData.get('status')
            };

            fetch('/admin/laporan/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status berhasil diperbarui');
                    location.reload();
                } else {
                    alert('Gagal memperbarui status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
        });
    </script>

    <script src="{{ asset('js/update-clock.js') }}"></script>
</body>
</html>
