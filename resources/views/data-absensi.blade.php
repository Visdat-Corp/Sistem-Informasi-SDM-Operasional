<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Data Absensi - PT. VISDAT TEKNIK UTAMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-hadir {
            background-color: #dcfce7;
            color: #166534;
        }

        .badge-terlambat {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-lembur {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-libur {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-tidak-hadir {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06), 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .table th {
            white-space: nowrap;
        }

        .table td,
        .table th {
            border-bottom: 1px solid #e5e7eb;
        }
    </style>
</head>

<body class="bg-gray-100 flex min-h-screen antialiased">
    @include('sidebar')

    <main class="flex-1 p-6 md:p-8 overflow-y-auto">
        @include('partials.header', [
            'title' => 'Data Absensi',
            'description' => 'Monitor kehadiran karyawan harian',
        ])

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mt-6 mb-6 gap-3">
            <form method="GET" action="{{ route('data-absensi') }}" id="filterForm"
                class="w-full grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                
                <!-- Filter Tipe Periode -->
                <select name="type" id="filterType" onchange="toggleDateFields()"
                    class="py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                    <option value="harian" {{ request('type', 'harian') == 'harian' ? 'selected' : '' }}>Harian</option>
                    <option value="mingguan" {{ request('type') == 'mingguan' ? 'selected' : '' }}>Mingguan</option>
                    <option value="bulanan" {{ request('type') == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                </select>

                <!-- Filter Harian -->
                <div id="harian-field" class="relative" style="{{ request('type', 'harian') == 'harian' ? '' : 'display: none;' }}">
                    <input type="date" name="tanggal" value="{{ request('tanggal', \Carbon\Carbon::today()->toDateString()) }}"
                        class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v11.25M3 18.75A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75M3 18.75v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                </div>

                <!-- Filter Mingguan -->
                <div id="mingguan-field" style="{{ request('type') == 'mingguan' ? '' : 'display: none;' }}">
                    <input type="week" name="minggu" value="{{ request('minggu', \Carbon\Carbon::now()->format('Y-\WW')) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                </div>

                <!-- Filter Bulanan -->
                <div id="bulanan-fields" class="flex gap-2" style="{{ request('type') == 'bulanan' ? '' : 'display: none;' }}">
                    <select name="bulan" class="py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', date('n')) == $i ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::createFromFormat('m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                        @for($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Filter Status -->
                <select name="status"
                    class="py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Lembur" {{ request('status') == 'Lembur' ? 'selected' : '' }}>Lembur</option>
                    <option value="Libur" {{ request('status') == 'Libur' ? 'selected' : '' }}>Libur</option>
                    <option value="Tidak Hadir" {{ request('status') == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                </select>

                <!-- Tombol Filter dan Reset -->
                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition whitespace-nowrap">Filter</button>
                    <a href="{{ route('data-absensi') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition whitespace-nowrap">Reset</a>
                </div>
            </form>
        </div>

        <script>
            function toggleDateFields() {
                const type = document.getElementById('filterType').value;
                
                // Hide all fields
                document.getElementById('harian-field').style.display = 'none';
                document.getElementById('mingguan-field').style.display = 'none';
                document.getElementById('bulanan-fields').style.display = 'none';
                
                // Show selected field
                if (type === 'harian') {
                    document.getElementById('harian-field').style.display = 'block';
                } else if (type === 'mingguan') {
                    document.getElementById('mingguan-field').style.display = 'block';
                } else if (type === 'bulanan') {
                    document.getElementById('bulanan-fields').style.display = 'flex';
                }
            }
        </script>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <div class="card p-4">
                <div class="text-sm text-gray-500">Hadir</div>
                <div class="text-2xl font-semibold mt-1">{{ $stats['hadir'] ?? 0 }}</div>
            </div>
            <div class="card p-4">
                <div class="text-sm text-gray-500">Terlambat</div>
                <div class="text-2xl font-semibold mt-1">{{ $stats['terlambat'] ?? 0 }}</div>
            </div>
            <div class="card p-4">
                <div class="text-sm text-gray-500">Lembur</div>
                <div class="text-2xl font-semibold mt-1">{{ $stats['lembur'] ?? 0 }}</div>
            </div>
            <div class="card p-4">
                <div class="text-sm text-gray-500">Libur</div>
                <div class="text-2xl font-semibold mt-1 text-blue-600">{{ $stats['libur'] ?? 0 }}</div>
            </div>
            <div class="card p-4">
                <div class="text-sm text-gray-500">Tidak Hadir</div>
                <div class="text-2xl font-semibold mt-1">{{ $stats['tidak_hadir'] ?? 0 }}</div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Karyawan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Posisi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Masuk</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jam Keluar</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Jam</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Foto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse (($processedAbsensis ?? []) as $row)
                            @php
                                $badgeClass = 'badge-hadir';
                                if ($row['status'] === 'Terlambat') {
                                    $badgeClass = 'badge-terlambat';
                                } elseif ($row['status'] === 'Lembur') {
                                    $badgeClass = 'badge-lembur';
                                } elseif ($row['status'] === 'Libur') {
                                    $badgeClass = 'badge-libur';
                                } elseif ($row['status'] === 'Tidak Hadir') {
                                    $badgeClass = 'badge-tidak-hadir';
                                }
                                $absen = $row['absen'] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 {{ $absen && $absen->override_request && $absen->override_status === 'pending' ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    {{ $row['nama_karyawan'] }}
                                    @if ($absen && $absen->override_request && $absen->override_status === 'pending')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            Override Request
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['posisi'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['tanggal'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['jam_masuk'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['jam_keluar'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['total_jam'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $row['status'] }}
                                        @if ($absen && $absen->menit_keterlambatan > 0 && $row['status'] == 'Terlambat')
                                            ({{ number_format($absen->menit_keterlambatan, 2, '.', '') }} menit)
                                        @endif
                                    </span>
                                    @if ($absen && $absen->override_request && $absen->override_status === 'approved')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                            ✓ Override Approved
                                        </span>
                                    @endif
                                    @if ($absen && $absen->override_request && $absen->override_status === 'rejected')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                            ✗ Override Rejected
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex gap-2 items-center">
                                        @if ($absen && $absen->foto_masuk)
                                            <a href="{{ Storage::url($absen->foto_masuk) }}" target="_blank"
                                                class="text-blue-600 hover:underline">Foto Masuk</a>
                                        @else
                                            <span class="text-gray-400">Foto Masuk -</span>
                                        @endif
                                        @if ($absen && $absen->foto_keluar)
                                            <a href="{{ Storage::url($absen->foto_keluar) }}" target="_blank"
                                                class="text-blue-600 hover:underline">Foto Keluar</a>
                                        @else
                                            <span class="text-gray-400">Foto Keluar -</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-col gap-2">
                                        <button class="detail-btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-150"
                                            data-absen='{{ json_encode($row) }}'>
                                            📋 Detail
                                        </button>
                                        
                                        {{-- Tombol Review Override jika ada permintaan pending --}}
                                        @if ($absen && $absen->override_request && $absen->override_status === 'pending')
                                            <button onclick="showOverrideModal({{ $absen->id_absensi }}, '{{ addslashes($row['nama_karyawan']) }}', '{{ addslashes($absen->override_reason ?? '') }}')"
                                                class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1 px-3 rounded text-xs transition duration-150">
                                                ⚠️ Review Override
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-gray-500">Belum ada data absensi
                                    untuk filter yang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
        {{ $absensis->appends(request()->query())->links() }}
        <p class="text-xs text-gray-400 mt-4">Catatan: Pastikan penyimpanan publik ter-link (php artisan storage:link)
            agar foto dapat diakses.</p>
    </main>

    <!-- Modal Detail Absensi -->
    <div id="detailModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Detail Absensi</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <!-- Avatar and basic info -->
                    <div class="flex items-center mb-4">
                        <div
                            class="modal-avatar w-12 h-12 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                        </div>
                        <div>
                            <h4 class="modal-nama text-xl font-semibold"></h4>
                            <p class="modal-posisi text-gray-600"></p>
                            <p class="modal-id text-gray-500 text-sm"></p>
                        </div>
                    </div>
                    <!-- Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Departemen:</strong> <span class="modal-departemen"></span></p>
                            <p><strong>Tanggal:</strong> <span class="modal-tanggal"></span></p>
                            <p><strong>Jam Masuk:</strong> <span class="modal-jam-masuk"></span></p>
                            <p><strong>Jam Keluar:</strong> <span class="modal-jam-keluar"></span></p>
                            <p><strong>Total Jam:</strong> <span class="modal-total-jam"></span></p>
                            <p><strong>Status:</strong> <span class="modal-status"></span></p>
                        </div>
                        <div>
                            <p><strong>Lokasi Masuk:</strong></p>
                            <div class="modal-map-masuk mb-4"></div>
                            <p><strong>Lokasi Keluar:</strong></p>
                            <div class="modal-map-keluar"></div>
                        </div>
                    </div>
                    <!-- Photos -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h5 class="font-semibold mb-2">Foto Masuk</h5>
                            <div class="foto-masuk-container"></div>
                        </div>
                        <div>
                            <h5 class="font-semibold mb-2">Foto Keluar</h5>
                            <div class="foto-keluar-container"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Override Request -->
    <div id="overrideModal" class="fixed inset-0 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Review Permintaan Override</h3>
                    <button onclick="closeOverrideModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Permintaan Override dari Karyawan</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p><strong>Nama:</strong> <span id="override-karyawan-name"></span></p>
                                    <p class="mt-1"><strong>Alasan:</strong></p>
                                    <p class="mt-1 italic" id="override-reason"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="overrideResponseForm">
                        <input type="hidden" id="override-absensi-id">
                        
                        <!-- Section: Edit Waktu Absensi -->
                        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <h4 class="text-sm font-bold text-blue-800 mb-3">📝 Ubah Waktu Absensi (Opsional)</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Jam Check-In
                                    </label>
                                    <input type="time" id="override-jam-masuk"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">
                                        Jam Check-Out
                                    </label>
                                    <input type="time" id="override-jam-keluar"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah</p>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Ubah Status -->
                        <div class="mb-6 bg-purple-50 border-l-4 border-purple-400 p-4 rounded">
                            <h4 class="text-sm font-bold text-purple-800 mb-3">📊 Ubah Status Absensi (Opsional)</h4>
                            <div>
                                <label class="block text-gray-700 text-sm font-medium mb-2">
                                    Status Baru
                                </label>
                                <select id="override-status"
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500">
                                    <option value="">-- Tidak Ubah Status --</option>
                                    <option value="hadir">Hadir</option>
                                    <option value="terlambat">Terlambat</option>
                                    <option value="pulang cepat">Pulang Cepat</option>
                                    <option value="tidak konsisten">Tidak Konsisten</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="cuti">Cuti</option>
                                    <option value="dinas luar">Dinas Luar</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Status akan tetap seperti semula jika tidak diubah</p>
                            </div>
                        </div>
                        
                        <!-- Catatan Admin -->
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">💬 Catatan Admin (Opsional)</label>
                            <textarea id="override-response-note" rows="3" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                placeholder="Tambahkan catatan Anda..."></textarea>
                        </div>

                        <div class="flex gap-3 justify-end">
                            <button type="button" onclick="closeOverrideModal()"
                                class="bg-gray-400 hover:bg-gray-500 text-white font-bold py-2 px-5 rounded-lg transition duration-150 shadow-sm">
                                ✕ Batal
                            </button>
                            <button type="button" onclick="rejectOverride()"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-5 rounded-lg transition duration-150 shadow-sm">
                                ✗ Tolak
                            </button>
                            <button type="button" onclick="approveOverride()"
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-5 rounded-lg transition duration-150 shadow-sm">
                                ✓ Setujui & Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/modal-data.js') }}"></script>
    <script src="{{ asset('js/update-clock.js') }}"></script>
    
    <script>
        function showOverrideModal(absensiId, karyawanName, reason) {
            document.getElementById('override-absensi-id').value = absensiId;
            document.getElementById('override-karyawan-name').textContent = karyawanName;
            document.getElementById('override-reason').textContent = reason;
            document.getElementById('override-response-note').value = '';
            // Reset fields
            document.getElementById('override-jam-masuk').value = '';
            document.getElementById('override-jam-keluar').value = '';
            document.getElementById('override-status').value = '';
            document.getElementById('overrideModal').classList.remove('hidden');
        }

        function closeOverrideModal() {
            document.getElementById('overrideModal').classList.add('hidden');
        }

        async function approveOverride() {
            const absensiId = document.getElementById('override-absensi-id').value;
            const responseNote = document.getElementById('override-response-note').value;
            const jamMasuk = document.getElementById('override-jam-masuk').value;
            const jamKeluar = document.getElementById('override-jam-keluar').value;
            const status = document.getElementById('override-status').value;

            if (!absensiId) {
                alert('ID Absensi tidak ditemukan!');
                return;
            }

            // Confirmation message dengan info perubahan
            let confirmMsg = 'Apakah Anda yakin ingin menyetujui permintaan override ini?';
            const changes = [];
            if (jamMasuk) changes.push(`Jam Check-In → ${jamMasuk}`);
            if (jamKeluar) changes.push(`Jam Check-Out → ${jamKeluar}`);
            if (status) changes.push(`Status → ${status}`);
            
            if (changes.length > 0) {
                confirmMsg += '\n\nPerubahan yang akan diterapkan:\n' + changes.join('\n');
            }

            if (!confirm(confirmMsg)) {
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    alert('CSRF Token tidak ditemukan! Silakan refresh halaman.');
                    return;
                }

                const payload = { 
                    response_note: responseNote || 'Disetujui oleh Manager SDM'
                };

                // Tambahkan data perubahan jika ada
                if (jamMasuk) payload.jam_masuk = jamMasuk;
                if (jamKeluar) payload.jam_keluar = jamKeluar;
                if (status) payload.status = status;

                console.log('Sending payload:', payload);

                const response = await fetch(`/data-absensi/${absensiId}/approve-override`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message || 'Permintaan override berhasil disetujui!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Gagal menyetujui override'));
                }
            } catch (error) {
                console.error('Error detail:', error);
                alert('Terjadi kesalahan saat memproses permintaan: ' + error.message);
            }
        }

        async function rejectOverride() {
            const absensiId = document.getElementById('override-absensi-id').value;
            const responseNote = document.getElementById('override-response-note').value;

            if (!absensiId) {
                alert('ID Absensi tidak ditemukan!');
                return;
            }

            if (!responseNote.trim()) {
                alert('Silakan berikan alasan penolakan pada kolom catatan.');
                return;
            }

            if (!confirm('Apakah Anda yakin ingin menolak permintaan override ini?')) {
                return;
            }

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    alert('CSRF Token tidak ditemukan! Silakan refresh halaman.');
                    return;
                }

                const response = await fetch(`/data-absensi/${absensiId}/reject-override`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ response_note: responseNote })
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server response:', errorText);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message || 'Permintaan override berhasil ditolak!');
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Gagal menolak override'));
                }
            } catch (error) {
                console.error('Error detail:', error);
                alert('Terjadi kesalahan saat memproses permintaan: ' + error.message);
            }
        }
    </script>
</body>

</html>
