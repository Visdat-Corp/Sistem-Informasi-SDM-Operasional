<!-- Header Konten -->
<header class="flex items-center justify-between pb-6 border-b border-gray-200">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">{{ $title }}</h1>
        <p class="text-gray-500">{{ $description ?? '' }}</p>
    </div>
    <div class="flex items-center space-x-4">
        <div class="text-right">
            <div class="text-sm font-semibold text-gray-800" id="current-date">{{ now()->format('l, d F Y') }}</div>
            <div class="text-xs text-gray-500" id="current-time">{{ now()->format('H:i:s T') }}</div>
        </div>
        <form method="POST" action="/logout" class="inline">
            @csrf
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors duration-200">
                Logout
            </button>
        </form>
    </div>
</header>
