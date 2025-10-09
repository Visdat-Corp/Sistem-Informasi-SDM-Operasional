<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PT. VISDAT TEKNIK UTAMA</title>
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
    @include('sidebar')

    <!-- Konten Utama -->
    <main class="flex-1 p-8 overflow-y-auto">
        @include('partials.header', ['title' => 'Dashboard', 'description' => 'Selamat datang kembali, ' . Auth::guard('admin')->user()->nama_admin . '!'])

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            <!-- Kartu: Total Karyawan -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyawan</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalKaryawan }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-xl">
                        <svg class="w-7 h-7 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m-7.5-2.962a3.75 3.75 0 015.962 0L14.25 6h3.362c.078 0 .15.01.22.029l.06.012a3 3 0 012.924 3.424l-2.846 6.83a3.75 3.75 0 01-5.962 0L9.75 12h-3.362c-.078 0-.15-.01-.22-.029L6.11 11.96a3 3 0 01-2.924-3.424l2.846-6.83a3.75 3.75 0 015.962 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kartu: Hadir Hari Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Hadir Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $hadirHariIni }} <span class="text-lg font-medium text-gray-400">/ {{ $totalKaryawan }}</span></p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-xl">
                        <svg class="w-7 h-7 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Kartu: Total Karyawan Lembur Hari Ini -->
            <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyawan Lembur Hari Ini</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalKaryawanLembur }}</p>
                    </div>
                    <div class="p-3 bg-slate-100 rounded-xl">
                         <svg class="w-7 h-7 text-slate-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Anda bisa menambahkan elemen UI lainnya di sini -->

    </main>

    <script src="{{ asset('js/update-clock.js') }}"></script>

</body>
</html>
