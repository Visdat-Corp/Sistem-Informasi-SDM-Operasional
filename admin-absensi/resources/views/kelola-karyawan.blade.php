<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Karyawan - PT. VISDAT TEKNIK UTAMA</title>
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
            <img src="/image/logo.jpeg" alt="Logo Perusahaan" class="h-16 object-contain rounded-lg">
        </div>
        
        <!-- Navigasi -->
        <nav class="flex-1 px-4 py-4 space-y-2">
            <a href="/" class="flex items-center space-x-3 hover:bg-blue-800 text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span>Dashboard</span>
            </a>

            <a href="/kelola-karyawan" class="flex items-center space-x-3 bg-blue-950 hover:text-white rounded-lg px-3 py-2.5 transition-colors duration-200">
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

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Header Konten -->
        <header class="flex items-center justify-between pb-6 border-b border-gray-200">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Kelola Karyawan</h1>
                <p class="text-gray-500">Selamat datang kembali, Admin!</p>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">Kamis, 14 September 2023</div>
                <div class="text-xs text-gray-500">10:30 WITA</div>
            </div>
        </header>

        <!-- Bar Aksi (Tombol, Cari, Filter) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Tombol Tambah Karyawan - FIXED: Tambahkan ID -->
            <button id="tambahBtn" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Tambah Karyawan</span>
            </button>

            <!-- Pencarian & Filter -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <input type="text" placeholder="Cari karyawan..." class="w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>
                <select class="py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Departemen</option>
                    <option value="IT">IT</option>
                    <option value="HRD">HRD</option>
                    <option value="Finance">Finance</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>
        </div>

        <!-- Tabel Data Karyawan -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Contoh Baris Data 1 - FIXED: Tambahkan class pada tombol -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">1</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Software Engineer</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">john@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                            <button class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                </svg>
                            </button>
                            <button class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.134H8.09a2.09 2.09 0 00-2.09 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Contoh Baris Data 2 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">2</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">UI/UX Designer</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">jane@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                            <button class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                </svg>
                            </button>
                            <button class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.134H8.09a2.09 2.09 0 00-2.09 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                    <!-- Contoh Baris Data 3 -->
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">3</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Mike Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Project Manager</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">mike@example.com</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Tidak Aktif</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                            <button class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                </svg>
                            </button>
                            <button class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200">
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.134H8.09a2.09 2.09 0 00-2.09 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah Karyawan -->
        <div id="tambahModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Tambah Karyawan Baru</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Karyawan -->
        <div id="editModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Edit Data Karyawan</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" value="John Doe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" value="john@example.com" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <input type="text" value="Software Engineer" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Hapus Karyawan -->
        <div id="hapusModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data karyawan ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-center space-x-4">
                        <button class="close-modal-btn px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/modal-handler.js') }}"></script>

    
</body>
</html>