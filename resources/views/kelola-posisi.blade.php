<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Posisi - PT. VISDAT TEKNIK UTAMA</title>
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
            'title' => 'Kelola Posisi',
            'description' => 'Selamat datang kembali, Admin!',
        ])

        @if (session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Bar Aksi (Tombol, Cari, Filter) -->
        <div class="flex items-center justify-between mt-8 mb-6">
            <!-- Tombol Tambah Posisi -->
            <button id="tambahBtn"
                class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center space-x-2 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                        clip-rule="evenodd" />
                </svg>
                <span>Tambah Posisi</span>
            </button>

            <!-- Pencarian & Filter -->
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('kelola-posisi') }}" class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi..."
                            class="w-72 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                    </div>
                    <select name="departemen" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Departemen</option>
                        @foreach($departemens as $departemen)
                            <option value="{{ $departemen->id_departemen }}" {{ request('departemen') == $departemen->id_departemen ? 'selected' : '' }}>
                                {{ $departemen->nama_departemen }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">Cari</button>
                    @if(request('search') || request('departemen'))
                        <a href="{{ route('kelola-posisi') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tabel Data Posisi -->
        <div class="bg-white rounded-2xl shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Posisi</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Departemen</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($posisis as $posisi)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $posisi->id_posisi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $posisi->nama_posisi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $posisi->departemen->nama_departemen }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <button
                                    class="edit-btn text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                    data-id="{{ $posisi->id_posisi }}" data-nama="{{ $posisi->nama_posisi }}"
                                    data-departemen="{{ $posisi->id_departemen }}">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L7.83 16.036H5.25V13.458l9.612-9.612z" />
                                    </svg>
                                </button>
                                <button
                                    class="delete-btn text-red-600 hover:text-red-900 transition-colors duration-200"
                                    data-id="{{ $posisi->id_posisi }}">
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
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">Tidak ada data posisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
        {{ $posisis->links() }}
        <!-- Modal Tambah Posisi -->
        <div id="tambahModal"
            class="fixed inset-0 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Tambah Posisi Baru</h3>
                    <button class="close-modal-btn text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form class="p-6" method="POST" action="{{ route('store-posisi') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="id_departemen" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                            <select id="id_departemen" name="id_departemen"
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
                            <label for="nama_posisi" class="block text-sm font-medium text-gray-700 mb-1">Nama Posisi</label>
                            <input type="text" id="nama_posisi" name="nama_posisi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
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

        <!-- Modal Edit Posisi -->
        <div id="editModal"
            class="fixed inset-0 hidden flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">Edit Data Posisi</h3>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Posisi</label>
                            <input type="text" name="nama_posisi" id="editPosisi"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                                required>
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

        <!-- Modal Hapus Posisi -->
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
                    <p class="text-gray-500 mb-6">Apakah Anda yakin ingin menghapus data posisi ini? Tindakan ini tidak
                        dapat dibatalkan.</p>
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

</body>

</html>
