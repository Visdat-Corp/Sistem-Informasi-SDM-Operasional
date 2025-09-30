<!-- Sidebar Utama -->
<aside class="w-64 bg-blue-900 text-blue-100 flex flex-col">
    <!-- Bagian Logo -->
    <div class="flex items-center justify-center bg-red-800 p-4 border-b border-blue-800">
        <img src="/image/logo.jpeg" alt="Logo Perusahaan" class="h-16 object-contain">
    </div>

    <!-- Navigasi -->
    <nav class="flex-1 px-4 py-4 space-y-2">
        <!-- Tautan Dashboard -->
        <a href="/" class="flex items-center space-x-3 {{ request()->is('/') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Tautan Kelola Karyawan -->
        <a href="/kelola-karyawan" class="flex items-center space-x-3 {{ request()->is('kelola-karyawan*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0110 13v-2.26a2 2 0 10-4 0V13a5 5 0 01-1.43 3.67A6.97 6.97 0 004 16c0 .34.024.673.07 1h8.86z" />
            </svg>
            <span>Kelola Karyawan</span>
        </a>

        <!-- Tautan Kelola Departemen -->
        <a href="/kelola-departemen" class="flex items-center space-x-3 {{ request()->is('kelola-departemen*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            <span>Kelola Departemen</span>
        </a>

        <!-- Tautan Kelola Posisi -->
        <a href="/kelola-posisi" class="flex items-center space-x-3 {{ request()->is('kelola-posisi*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd" />
                <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z" />
            </svg>
            <span>Kelola Posisi</span>
        </a>

        <!-- Tautan Kelola Lokasi -->
        <a href="/kelola-lokasi" class="flex items-center space-x-3 {{ request()->is('kelola-lokasi*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            <span>Kelola Lokasi</span>
        </a>

        <!-- Tautan Data Absensi -->
        <a href="/data-absensi" class="flex items-center space-x-3 {{ request()->is('data-absensi*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            <span>Data Absensi</span>
        </a>

        <!-- Tautan Laporan -->
        <a href="/laporan" class="flex items-center space-x-3 {{ request()->is('laporan*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
            <span>Laporan</span>
        </a>

        <!-- Tautan Pengaturan -->
        <a href="/pengaturan" class="flex items-center space-x-3 {{ request()->is('pengaturan*') ? 'bg-blue-950 text-white' : 'hover:bg-blue-800 hover:text-white' }} rounded-lg px-3 py-2.5 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.96.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.532 1.532 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.532 1.532 0 01-.948-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01.948-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.286-.948zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
            </svg>
            <span>Pengaturan</span>
        </a>
    </nav>
</aside>
