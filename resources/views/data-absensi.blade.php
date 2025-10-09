<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <form method="GET" action="{{ route('data-absensi') }}"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative">
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                        class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5A2.25 2.25 0 015.25 5.25h13.5A2.25 2.25 0 0121 7.5v11.25M3 18.75A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75M3 18.75v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                </div>

                <select name="status"
                    class="py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition bg-white">
                    <option value="">Semua Status</option>
                    <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat
                    </option>
                    <option value="Lembur" {{ request('status') == 'Lembur' ? 'selected' : '' }}>Lembur</option>
                    <option value="Tidak Hadir" {{ request('status') == 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir
                    </option>
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Filter</button>
                    <a href="{{ route('data-absensi') }}"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
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
                                } elseif ($row['status'] === 'Tidak Hadir') {
                                    $badgeClass = 'badge-tidak-hadir';
                                }
                                $absen = $row['absen'] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $row['nama_karyawan'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['posisi'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['tanggal'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['jam_masuk'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['jam_keluar'] ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $row['total_jam'] }}</td>
                                <td class="px-4 py-3">
                                    <span class="badge {{ $badgeClass }}">
                                        {{ $row['status'] }}
                                        @if ($absen && $absen->menit_keterlambatan > 0 && $row['status'] == 'Terlambat')
                                            ({{ $absen->menit_keterlambatan }} menit)
                                        @endif
                                    </span>
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
                                <td class="px-4 py-3 text-sm"><button
                                        class="detail-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded"
                                        data-absen='{{ json_encode($row) }}'>Detail</button></td>
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
        {{ $absensis->links() }}
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

    <script src="{{ asset('js/modal-data.js') }}"></script>
    <script src="{{ asset('js/update-clock.js') }}"></script>
</body>

</html>
