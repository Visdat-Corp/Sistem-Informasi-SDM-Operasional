<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. VISDAT TEKNIK UTAMA</title>
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
            <!-- Dashboard - Active -->
            <a href="/dashboard" class="nav-item flex items-center space-x-3 bg-blue-600 rounded-lg px-3 py-2 text-white">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke-width="2"/>
                </svg>
                <span class="nav-text whitespace-nowrap">Dashboard</span>
            </a>

            <!-- Kelola Karyawan -->
            <a href="/kelola-karyawan" class="nav-item flex items-center justify-between hover:bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Kelola Karyawan</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>
                </svg>

            <!-- Data Absensi -->
            <a href="/data-absensi" class="nav-item flex items-center justify-between hover:bg-blue-600 rounded-lg px-3 py-2 text-white group">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" stroke-width="2"/>
                    </svg>
                    <span class="nav-text whitespace-nowrap">Data Absensi</span>
                </div>
                <svg class="nav-arrow w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 18l6-6-6-6" stroke-width="2"/>
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
    <main class="flex-1 bg-white p-6">
        <!-- Header -->
        <header class="flex items-center justify-between border-b border-gray-200 pb-4 mb-8">
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" stroke-width="2"/>
                </svg>
                <h1 class="font-bold text-2xl text-gray-900">Dashboard</h1>
            </div>
        </header>

        <!-- Statistics Cards -->
        <div class="flex space-x-6">
            <!-- Total Karyawan -->
            <div class="flex items-center space-x-4 bg-green-50 p-6 rounded-xl flex-1 border border-green-100">
                <div class="p-3 bg-green-200 rounded-full">
                    <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Karyawan</p>
                    <p class="font-bold text-2xl text-gray-900">50 orang</p>
                </div>
            </div>

            <!-- Hadir Hari Ini -->
            <div class="flex items-center space-x-4 bg-blue-50 p-6 rounded-xl flex-1 border border-blue-100">
                <div class="p-3 bg-blue-200 rounded-full">
                    <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Hadir Hari Ini</p>
                    <p class="font-bold text-2xl text-gray-900">40 orang</p>
                </div>
            </div>

            <!-- Total Lembur Hari Ini -->
            <div class="flex items-center space-x-4 bg-pink-50 p-6 rounded-xl flex-1 border border-pink-100">
                <div class="p-3 bg-pink-200 rounded-full">
                    <svg class="w-8 h-8 text-pink-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Lembur Hari Ini</p>
                    <p class="font-bold text-2xl text-gray-900">3 orang</p>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
