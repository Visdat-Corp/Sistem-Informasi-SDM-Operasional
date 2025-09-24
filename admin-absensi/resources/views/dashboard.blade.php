<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. VISDAT TEKNIK UTAMA</title>
    {{-- Memuat Tailwind CSS melalui CDN untuk kemudahan --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Memuat Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Vite directive, jika Anda menggunakan build tools Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Menggunakan font Inter sebagai default */
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
            <!-- Tautan Dashboard (Aktif) -->
            <a href="/" class="flex items-center space-x-3 bg-blue-950 text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <!-- Tautan Lainnya -->
            <a href="/kelola-karyawan" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0110 13v-2.26a2 2 0 10-4 0V13a5 5 0 01-1.43 3.67A6.97 6.97 0 004 16c0 .34.024.673.07 1h8.86z" />
                </svg>
                <span>Kelola Karyawan</span>
            </a>

            <a href="/data-absensi" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
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

    <!-- Konten Utama -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Header Konten -->
        <header class="flex items-center justify-between pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500">Selamat datang kembali, Admin!</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">Kamis, 14 September 2023</div>
                <div class="text-xs text-gray-500">10:30 WITA</div>
            </div>
        </header>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <!-- Kartu: Total Karyawan -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-900">50</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-7 h-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a3.75 3.75 0 015.962 0L14.25 6h3.362c.078 0 .15.01.22.029l.06.012a3 3 0 012.924 3.424l-2.846 6.83a3.75 3.75 0 01-5.962 0L9.75 12h-3.362c-.078 0-.15-.01-.22-.029L6.11 11.96a3 3 0 01-2.924-3.424l2.846-6.83a3.75 3.75 0 015.962 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-4">+2 orang sejak bulan lalu</p>
            </div>

            <!-- Kartu: Hadir Hari Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Hadir Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">40 <span class="text-lg font-medium text-gray-400">/ 50</span></p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-xl">
                        <svg class="w-7 h-7 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                 <p class="text-xs text-gray-400 mt-4">Tingkat kehadiran 80%</p>
            </div>

            <!-- Kartu: Total Lembur Hari Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Lembur Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">3</p>
                    </div>
                    <div class="p-3 bg-slate-100 rounded-xl">
                         <svg class="w-7 h-7 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                 <p class="text-xs text-gray-400 mt-4">Total 5 jam lembur</p>
            </div>
        </div>
        
        <!-- Anda bisa menambahkan elemen UI lainnya di sini -->

    </main>

</body>
</html>