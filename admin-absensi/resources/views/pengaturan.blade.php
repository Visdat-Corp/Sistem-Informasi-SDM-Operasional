<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pengaturan</title>
    {{-- Memuat Tailwind CSS melalui CDN untuk kemudahan --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Memuat Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    {{-- Vite directive, jika Anda menggunakan build tools Laravel --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

            <a href="/laporan" class="flex items-center space-x-3 hover:bg-blue-800 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                </svg>
                <span>Laporan</span>
            </a>

            <a href="/pengaturan" class="flex items-center space-x-3 bg-blue-950 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
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
                <h1 class="text-3xl font-bold text-gray-800">Pengaturan</h1>
                <p class="text-gray-500"></p>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">Kamis, 14 September 2023</div>
                <div class="text-xs text-gray-500">10:30 WITA</div>
            </div>
        </header>
        <!-- end of Header -->

        <div class="grid md:grid-cols-1 gap-10">
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Atur jam kerja</h2>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jam masuk</label>
                        <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan Jam Masuk">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jam keluar</label>
                        <input type="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan Jam Keluar">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Toleransi keterlambatan</label>
                        <input type="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan Toleransi Keterlambatan (dalam menit)">
                    </div>
                </form>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors mt-4">
                    Simpan
                </button>
            </div>

            {{-- <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Lokasi Kerja</h2>
                <form class="space-y-4">
                    <div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lokasi</label>
                            <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan nama lokasi">
                        </div>
                        <label class="block text-sm font-medium text-gray-700 mt-1">Metode absensi</label>
                        <select class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            <option>GPS</option>
                            <option>WiFi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jarak maksimum (meter)</label>
                        <input type="number" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan jarak maksimum">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Lokasi Kerja</label>
                        <input type="text" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan lokasi kantor">
                    </div>
                </form>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors mt-4">
                    Simpan
                </button>
            </div> --}}
        </div>
    </main>
    <!-- End of Main Content -->

</body>
</html>