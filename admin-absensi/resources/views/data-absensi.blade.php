<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Absensi - PT. VISDAT TEKNIK UTAMA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex h-screen antialiased">
    <!-- Sidebar Utama -->
    <aside class="w-64 bg-blue-900 text-blue-100 flex flex-col">
        <!-- Bagian Logo -->
        <div class="flex items-center justify-center bg-red-800 p-4 border-b border-blue-800">
            <img src="/image/logo.jpeg" alt="Logo Perusahaan" class="h-16 object-contain">
        </div>
        
        <!-- Navigasi -->
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="/" class="flex items-center space-x-3 hover:bg-blue-800 text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="/kelola-karyawan" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0110 13v-2.26a2 2 0 10-4 0V13a5 5 0 01-1.43 3.67A6.97 6.97 0 004 16c0 .34.024.673.07 1h8.86z" />
                </svg>
                <span>Kelola Karyawan</span>
            </a>

            <a href="/data-absensi" class="flex items-center space-x-3 bg-blue-950 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                <span>Data Absensi</span>
            </a>

            <a href="/laporan" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
                <span>Laporan</span>
            </a>

            <a href="/pengaturan" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.96.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.532 1.532 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.532 1.532 0 01-.948-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01.948-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.286-.948zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Header Konten -->
        <header class="flex items-center justify-between pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Data Absensi</h1>
                <p class="text-gray-500">Monitor kehadiran karyawan harian</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">Kamis, 14 September 2023</div>
                <div class="text-xs text-gray-500">10:30 WITA</div>
            </div>
        </header>

        <!-- Bar Aksi (Filter dan Export) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Filter Tanggal & Karyawan -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="date" value="2023-09-14" class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 005.25 9h13.5a2.25 2.25 0 002.25 2.25v7.5" />
                        </svg>
                    </div>
                </div>
                <select class="py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Hadir">Hadir</option>
                    <option value="Terlambat">Terlambat</option>
                    <option value="Tidak Hadir">Tidak Hadir</option>
                    <option value="Izin">Izin</option>
                </select>
            </div>

            <!-- Tombol Export -->
            <button class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                <span>Export</span>
            </button>
        </div>

        <!-- Tabel Data Absensi -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Masuk</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam Keluar</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Jam</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Baris Data 1 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">001</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                        JD
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-sm text-gray-500">Software Engineer</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14 Sep 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">08:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">17:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">9 jam</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="detail-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </td>
                        
                    </tr>

                    <!-- Baris Data 2 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">002</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-pink-500 flex items-center justify-center text-white font-semibold">
                                        JS
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Jane Smith</div>
                                    <div class="text-sm text-gray-500">UI/UX Designer</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14 Sep 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">08:15</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">17:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8,75 jam</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="detail-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Baris Data 3 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">003</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-purple-500 flex items-center justify-center text-white font-semibold">
                                        MJ
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Mike Johnson</div>
                                    <div class="text-sm text-gray-500">Project Manager</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14 Sep 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">08:00</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">16:30</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8,5 jam</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hadir</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="detail-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Baris Data 4 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">004</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-semibold">
                                        SB
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">Sarah Brown</div>
                                    <div class="text-sm text-gray-500">Marketing Specialist</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14 Sep 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">0 jam</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Izin</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="detail-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>

                    <!-- Baris Data 5 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">005</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-emerald-500 flex items-center justify-center text-white font-semibold">
                                        DW
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">David Wilson</div>
                                    <div class="text-sm text-gray-500">Backend Developer</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">14 Sep 2023</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">-</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">0 jam</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak Hadir</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button class="detail-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-xs font-medium transition-colors duration-200 flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Statistik Singkat -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Hadir</dt>
                            <dd class="text-lg font-medium text-gray-900">3 Orang</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Terlambat</dt>
                            <dd class="text-lg font-medium text-gray-900">1 Orang</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Izin</dt>
                            <dd class="text-lg font-medium text-gray-900">1 Orang</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Tidak Hadir</dt>
                            <dd class="text-lg font-medium text-gray-900">1 Orang</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Detail Absensi -->
    <div id="detailModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Detail Absensi Karyawan</h3>
                <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="p-6">
                <!-- Informasi Karyawan -->
                <div class="flex items-center space-x-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold text-xl">
                        JD
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900">John Doe</h4>
                        <p class="text-gray-500">Software Engineer</p>
                        <p class="text-sm text-gray-400">ID: 001</p>
                    </div>
                </div>

                <!-- Detail Absensi -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <h5 class="font-semibold text-gray-800 border-b pb-2">Informasi Waktu</h5>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="font-medium">14 September 2023</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jam Masuk:</span>
                                <span class="font-medium text-green-600">08:00 WIB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jam Keluar:</span>
                                <span class="font-medium text-red-600">17:00 WIB</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Jam Kerja:</span>
                                <span class="font-medium">9 jam</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">Hadir</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h5 class="font-semibold text-gray-800 border-b pb-2">Informasi Lokasi</h5>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lokasi Masuk:</span>
                                <span class="font-medium">Kantor Pusat</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lokasi Keluar:</span>
                                <span class="font-medium">Kantor Pusat</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Koordinat Masuk:</span>
                                <span class="font-medium text-sm">-6.200, 106.816</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Koordinat Keluar:</span>
                                <span class="font-medium text-sm">-6.200, 106.816</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Foto Bukti Absensi -->
                <div class="mb-6">
                    <h5 class="font-semibold text-gray-800 border-b pb-2 mb-4">Bukti Foto Absensi</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Foto Masuk -->
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-600">Foto Masuk - 08:00 WIB</p>
                            <div class="bg-gray-200 rounded-lg p-8 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-gray-500">Foto Masuk Tersedia</p>
                                <button class="mt-2 text-blue-600 text-sm hover:underline">Lihat Foto</button>
                            </div>
                        </div>
                        
                        <!-- Foto Keluar -->
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-600">Foto Keluar - 17:00 WIB</p>
                            <div class="bg-gray-200 rounded-lg p-8 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-gray-500">Foto Keluar Tersedia</p>
                                <button class="mt-2 text-blue-600 text-sm hover:underline">Lihat Foto</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Tambahan -->
                <div class="mb-6">
                    <h5 class="font-semibold text-gray-800 border-b pb-2 mb-4">Catatan</h5>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-600 text-sm">Karyawan hadir tepat waktu dan bekerja sesuai jadwal yang telah ditentukan.</p>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-4">
                    <button class="close-modal-btn px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Tutup
                    </button>
                    <button class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Export Detail
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/modal-data.js') }}"></script>
    
</body>
</html>