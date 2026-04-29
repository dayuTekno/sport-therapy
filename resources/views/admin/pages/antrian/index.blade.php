<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiosk Antrian Admisi - Sistem Informasi Klinik</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white w-full max-w-xl rounded-2xl shadow-xl p-10 text-center">

    <!-- Icon -->
    <div class="flex justify-center mb-6">
        <div class="bg-blue-100 p-4 rounded-full">
            <svg class="h-10 w-10 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 7h16v4a2 2 0 010 4v4H4v-4a2 2 0 010-4V7z
                       M9 11h6
                       M9 15h4"/>
            </svg>
        </div>
    </div>

    <!-- Title -->
    <h2 class="text-3xl font-bold text-gray-800">
        Klinik Sehat Selalu
    </h2>

    <p class="mt-3 text-gray-600 text-lg">
        Silakan ambil nomor antrian Anda untuk mendaftar di meja admisi.
    </p>

    <!-- Steps -->
    <div class="mt-8 text-left bg-gray-50 rounded-xl p-5 text-gray-700 space-y-2 border border-gray-200">
        <p class="font-semibold mb-2">Langkah Mudah:</p>
        <p>1️⃣ Klik tombol <span class="font-semibold text-blue-600">Ambil Antrian</span></p>
        <p>2️⃣ Bawa atau simpan tiket antrian Anda</p>
        <p>3️⃣ Tunggu hingga nomor Anda dipanggil oleh petugas</p>
    </div>

    <!-- Button -->
    <div class="mt-8">
        <form action="{{ route('antrian.generate') }}" method="POST">
            @csrf
            <button type="submit"
               class="w-full py-5 text-xl font-bold bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 active:scale-95 transition cursor-pointer">
                🎟 Ambil Antrian Sekarang
            </button>
        </form>
    </div>

</div>

</body>
</html>