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
    @include('sidebar')

    <!-- Main Content -->
    <main class="flex-1 bg-white p-6">
        @include('partials.header', ['title' => 'Pengaturan'])
        <!-- end of Header -->

        <div class="grid md:grid-cols-1 gap-10">
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h2 class="text-lg font-semibold mb-4">Atur jam kerja</h2>
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form class="space-y-4" method="POST" action="{{ route('update-pengaturan') }}">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jam masuk</label>
                        <input type="time" name="jam_masuk_normal" value="{{ old('jam_masuk_normal', $jamKerja->jam_masuk_normal ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jam keluar</label>
                        <input type="time" name="jam_keluar_normal" value="{{ old('jam_keluar_normal', $jamKerja->jam_keluar_normal ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Toleransi keterlambatan</label>
                        <input type="number" name="toleransi_keterlambatan" value="{{ old('toleransi_keterlambatan', $jamKerja->toleransi_keterlambatan ?? '') }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" placeholder="Masukkan Toleransi Keterlambatan (dalam menit)" min="0" required>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors mt-4">
                        Simpan
                    </button>
                </form>
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

    <script src="{{ asset('js/update-clock.js') }}"></script>

</body>
</html>
