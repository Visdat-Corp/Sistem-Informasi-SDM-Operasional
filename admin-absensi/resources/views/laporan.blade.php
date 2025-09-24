<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- Memuat Tailwind CSS melalui CDN untuk kemudahan --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Memuat Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Vite directive, jika Anda menggunakan build tools Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Laporan</title>
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

            <a href="/data-absensi" class="flex items-center space-x-3 hover:bg-blue-950 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                <span>Data Absensi</span>
            </a>

            <a href="/laporan" class="flex items-center space-x-3 bg-blue-950 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
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
    <main class="flex-1 bg-white p-6">
        <!-- Header Konten -->
        <header class="flex items-center justify-between pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Laporan</h1>
                <p class="text-gray-500">Laporan Absensi Harian karyawan</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">Kamis, 14 September 2023</div>
                <div class="text-xs text-gray-500">10:30 WITA</div>
            </div>
        </header>
        <!-- end of Header -->

        <!-- Laporan Absensi Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">Laporan Absensi</h2>
                        
            <div class="grid md:grid-cols-2 gap-10">
                <!-- Filter Form -->
                <div>
                    <h3 class="text-sm font-medium mb-3">Filter Laporan</h3>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Periode</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan periode">
                        </div>
                        <div>
                        <label class="block text-sm text-gray-700 mb-1">Karyawan</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Pilih karyawan">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Generate Laporan
                        </button>
                    </form>
                </div>

                <!-- Statistics Cards -->
                <div>
                    <h3 class="text-sm font-medium mb-3">Ringkasan Absensi Bulan Ini</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-green-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-green-700">67%</div>
                            <div class="text-xs text-green-600">Tingkat Kehadiran</div>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-yellow-700">8%</div>
                            <div class="text-xs text-yellow-600">Terlambat & Tidak Informasi</div>
                        </div>
                        <div class="bg-red-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-red-700">3%</div>
                            <div class="text-xs text-red-600">Sakit</div>
                        </div>
                        <div class="bg-blue-100 p-4 rounded-lg text-center">
                            <div class="text-2xl font-bold text-blue-700">8.2%</div>
                            <div class="text-xs text-blue-600">Izin Cuti dan Yang</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Laporan Detail Section -->
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Laporan Detail</h2>
                <button class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Download Excel
                </button>
            </div>
                        
            <!-- Chart Placeholder -->
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <p class="text-gray-500 text-lg">Pilih filter dan klik "Generate" untuk melihat chart</p>
            </div>
        </div>
    </main>     
</body>
</html>