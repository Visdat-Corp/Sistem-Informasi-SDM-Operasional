<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Absensi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#a96c6c] flex h-screen">
    <!-- Sidebar Utama -->
    <div id="mainSidebar" class="w-60 bg-blue-800 text-white flex flex-col overflow-hidden">
        <!-- Logo Section -->
        <div class="bg-red-600 px-2 flex justify-between items-center" style="padding-top: 4px; padding-bottom: 4px;">
            
            <div class="logo">
                <img src="/image/logo.jpeg" alt="Logo" class="h-16 w-40 object-contain rounded-lg">
            </div>
            
        </div>
        
        <!-- Navigation -->
        <div class="p-4 flex flex-col space-y-2 flex-grow">
            <!-- Dashboard -->
            <a href="/dashboard" class="nav-item flex items-center space-x-3 hover:bg-blue-600 rounded-lg px-3 py-2 text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/>
                </svg>
                <span class="nav-text whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Kelola karyawan -->
            <a href="/kelola-karyawan" class="nav-item flex items-center justify-between hover:bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Kelola Karyawan</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>
                </svg>
            </a>

            <!-- Data Absensi - Active -->
            <a href="/data-absensi" class="nav-item flex items-center justify-between bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Data Absensi</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>/
                </svg>
            </a>

            <!-- Laporan -->
            <a href="#" class="nav-item flex items-center justify-between hover:bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Laporan</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>
                </svg>
            </a>

            <!-- Pengaturan -->
            <a href="#" class="nav-item flex items-center justify-between hover:bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" stroke-width="2"/>
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Pengaturan</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-1 bg-gray-50 p-6 flex flex-col items-start">
       <!-- Header -->
    <header class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
        <div class="flex items-center space-x-3">
            <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" stroke-width="2"/>
            </svg>
            <h1 class="font-bold text-2xl text-gray-900">Data Absensi</h1>
        </div>
    </header>
        <!-- end of Header -->
        <div class="flex items-center justify-between w-full mb-4">
            <!-- Label -->
            <h2 class="font-semibold text-black">Team Member</h2>
            <!-- Input Tanggal + Button -->
            <div class="flex items-center space-x-2">
                <!-- Input Tanggal -->
                <div class="flex items-center border rounded-lg bg-gray-100 px-3 py-2">
                    <input type="date" class="bg-gray-100 outline-none" value="2025-09-18">
                    <i class="fa fa-calendar ml-2 text-gray-500"></i>
                </div>
                <!-- Button Export -->
                <button class="flex items-center bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600 transition">
                    <i class="fa fa-download mr-2"></i> Export
                </button>
            </div>
        </div>
        <!-- Table -->
        <div class="overflow-x-auto w-full rounded-lg shadow bg-white">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-6 text-left border-b border-gray-200">Id</th>
                        <th class="py-3 px-6 text-left border-b border-gray-200">Nama</th>
                        <th class="py-3 px-6 text-left border-b border-gray-200">Tanggal</th>
                        <th class="py-3 px-6 text-left border-b border-gray-200">Jam Masuk</th>
                        <th class="py-3 px-6 text-left border-b border-gray-200">Jam Keluar</th>
                        <th class="py-3 px-6 text-left border-b border-gray-200">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-4 px-6 border-b border-gray-200">1</td>
                        <td class="py-4 px-6 border-b border-gray-200">John Doe</td>
                        <td class="py-4 px-6 border-b border-gray-200">2024-06-01</td>
                        <td class="py-4 px-6 border-b border-gray-200">08:00 AM</td>
                        <td class="py-4 px-6 border-b border-gray-200">05:00 PM</td>
                        <td class="py-4 px-6 border-b border-gray-200">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">Hadir</span>
                        </td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="py-4 px-6 border-b border-gray-200">2</td>
                        <td class="py-4 px-6 border-b border-gray-200">Jane Smith</td>
                        <td class="py-4 px-6 border-b border-gray-200">2024-06-01</td>
                        <td class="py-4 px-6 border-b border-gray-200">08:15 AM</td>
                        <td class="py-4 px-6 border-b border-gray-200">05:00 PM</td>
                        <td class="py-4 px-6 border-b border-gray-200">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Terlambat</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
    <!-- end of Main Content -->
</body>
</html>