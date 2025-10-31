<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Karyawan - PT. VISDAT TEKNIK UTAMA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex h-screen antialiased">
    @include('sidebar')

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        @include('partials.header', [
            'title' => 'Kelola Karyawan',
            'description' => 'Selamat datang kembali, Admin!',
        ])

        @if (session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bar Aksi (Tombol, Cari, Filter) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Tombol Tambah Karyawan - FIXED: Tambahkan ID -->
            <button id="tambahBtn"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>Tambah Karyawan</span>
            </button>

            <!-- Pencarian & Filter -->
            <form method="GET" action="{{ route('kelola-karyawan') }}" class="flex items-center space-x-4">
                <div class="relative">
                    <label for="searchInput" class="sr-only">Cari karyawan</label>
                    <input type="text" id="searchInput" name="search" placeholder="Cari karyawan..." value="{{ request('search') }}"
                        class="w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>
                <label for="departemenFilter" class="sr-only">Filter Departemen</label>
                <select id="departemenFilter" name="departemen" onchange="this.form.submit()"
                    class="py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">Semua Departemen</option>
                    @foreach ($departemens as $departemen)
                        <option value="{{ $departemen->id_departemen }}" {{ request('departemen') == $departemen->id_departemen ? 'selected' : '' }}>
                            {{ $departemen->nama_departemen }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                    Cari
                </button>
            </form>
        </div>

        <!-- Tabel Data Karyawan -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Username</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Departemen</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Posisi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lokasi Kerja</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($karyawans as $karyawan)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $karyawan->id_karyawan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $karyawan->nama_karyawan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->username_karyawan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->email_karyawan }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->departemen->nama_departemen ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->posisi->nama_posisi ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $karyawan->lokasiKerja->lokasi_kerja ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $karyawan->status == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($karyawan->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <button
                                    class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                    data-id="{{ $karyawan->id_karyawan }}" data-nama="{{ $karyawan->nama_karyawan }}"
                                    data-username="{{ $karyawan->username_karyawan }}"
                                    data-email="{{ $karyawan->email_karyawan }}"
                                    data-departemen="{{ $karyawan->id_departemen }}"
                                    data-posisi="{{ $karyawan->id_posisi }}" 
                                    data-lokasi="{{ $karyawan->id_lokasi_kerja }}"
                                    data-status="{{ $karyawan->status }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                    </svg>
                                </button>
                                <button
                                    class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200"
                                    data-id="{{ $karyawan->id_karyawan }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.09-2.134H8.09a2.09 2.09 0 00-2.09 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">Tidak ada data karyawan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
        {{ $karyawans->links() }}
        <!-- Modal Tambah Karyawan -->
        <div id="tambahModal"
            class="fixed inset-0 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Tambah Karyawan Baru</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="p-6" method="POST" action="{{ route('store-karyawan') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="namaTambah" class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" name="nama_karyawan" id="namaTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="usernameTambah" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username_karyawan" id="usernameTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="emailTambah" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email_karyawan" id="emailTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="passwordTambah" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" name="password_karyawan" id="passwordTambah"
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                    required>
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('passwordTambah')">
                                    <svg class="h-5 w-5 text-gray-400 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-5 w-5 text-gray-400 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="departemenTambah" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                            <select name="id_departemen" id="departemenTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id_departemen }}">
                                        {{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="posisiSelect" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select name="id_posisi" id="posisiSelect"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 hidden">
                                <option value="">Pilih Posisi</option>
                            </select>
                        </div>
                        <div>
                            <label for="lokasiTambah" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kerja</label>
                            <select name="id_lokasi_kerja" id="lokasiTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Lokasi Kerja</option>
                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id_lokasi }}">
                                        {{ $lokasi->lokasi_kerja }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="statusTambah" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="statusTambah"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button"
                            class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Karyawan -->
        <div id="editModal"
            class="fixed inset-0 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Edit Data Karyawan</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="p-6" method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label for="editNama" class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                            <input type="text" name="nama_karyawan" id="editNama"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="editUsername" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username_karyawan" id="editUsername"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="editEmail" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email_karyawan" id="editEmail"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div>
                            <label for="passwordEdit" class="block text-sm font-medium text-gray-700 mb-1">Password (Kosongkan jika tidak
                                ingin mengubah)</label>
                            <div class="relative">
                                <input type="password" name="password_karyawan" id="passwordEdit"
                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('passwordEdit')">
                                    <svg class="h-5 w-5 text-gray-400 eye-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg class="h-5 w-5 text-gray-400 eye-slash-icon hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="editDepartemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                            <select name="id_departemen" id="editDepartemen"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Pilih Departemen</option>
                                @foreach ($departemens as $departemen)
                                    <option value="{{ $departemen->id_departemen }}">
                                        {{ $departemen->nama_departemen }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="editPosisi" class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                            <select name="id_posisi" id="editPosisi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 hidden"
                                required>
                                <option value="">Pilih Posisi</option>
                            </select>
                        </div>
                        <div>
                            <label for="editLokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Kerja</label>
                            <select name="id_lokasi_kerja" id="editLokasi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Lokasi Kerja</option>
                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id_lokasi }}">
                                        {{ $lokasi->lokasi_kerja }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" id="editStatus"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button"
                            class="close-modal-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Update</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Hapus Karyawan -->
        <div id="hapusModal"
            class="fixed inset-0 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4">
                <div class="p-8 text-center">
                    <svg class="w-16 h-16 text-red-500 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" />
                    </svg>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Hapus</h3>
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data karyawan ini? Tindakan ini
                        tidak dapat dibatalkan.</p>
                    <form method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center space-x-4">
                            <button type="button"
                                class="close-modal-btn px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                            <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="{{ asset('js/modal-handler.js') }}"></script>
    <script src="{{ asset('js/update-clock.js') }}"></script>

    <script>
        // Function to load positions based on department
        function loadPositions(departemenId, posisiSelect, selectedPosisi = null) {
            if (!departemenId) {
                posisiSelect.innerHTML = '<option value="">Pilih Posisi</option>';
                posisiSelect.classList.add('hidden');
                return;
            }

            fetch(`/get-posisi/${departemenId}`)
                .then(response => response.json())
                .then(data => {
                    posisiSelect.innerHTML = '<option value="">Pilih Posisi</option>';
                    data.forEach(posisi => {
                        const option = document.createElement('option');
                        option.value = posisi.id_posisi;
                        option.textContent = posisi.nama_posisi;
                        if (selectedPosisi && posisi.id_posisi == selectedPosisi) {
                            option.selected = true;
                        }
                        posisiSelect.appendChild(option);
                    });
                    posisiSelect.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error loading positions:', error);
                    posisiSelect.innerHTML = '<option value="">Error loading positions</option>';
                    posisiSelect.classList.remove('hidden');
                });
        }

        // Add event listener for add modal department select
        document.querySelector('#tambahModal select[name="id_departemen"]').addEventListener('change', function() {
            const departemenId = this.value;
            const posisiSelect = document.getElementById('posisiSelect');
            loadPositions(departemenId, posisiSelect);
        });

        // Add event listener for edit modal department select
        document.querySelector('#editModal select[name="id_departemen"]').addEventListener('change', function() {
            const departemenId = this.value;
            const posisiSelect = document.getElementById('editPosisi');
            loadPositions(departemenId, posisiSelect);
        });

        // Override the edit button click to load positions when opening edit modal
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const departemen = this.getAttribute('data-departemen');
                const posisi = this.getAttribute('data-posisi');
                const lokasi = this.getAttribute('data-lokasi');
                const posisiSelect = document.getElementById('editPosisi');
                const lokasiSelect = document.getElementById('editLokasi');
                
                if (departemen) {
                    loadPositions(departemen, posisiSelect, posisi);
                }
                
                if (lokasiSelect && lokasi) {
                    lokasiSelect.value = lokasi;
                }
            });
        });

        // Function to toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const eyeIcon = button.querySelector('.eye-icon');
            const eyeSlashIcon = button.querySelector('.eye-slash-icon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>

</body>

</html>
