@extends('admin.layouts.app')

@section('title', 'Antrian')

@section('content')

<div class="min-h-[80vh] flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 p-6">

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
            Ambil Nomor Antrian
        </h2>

        <p class="mt-3 text-gray-600">
            Klik tombol di bawah untuk mendapatkan nomor antrian Anda.
        </p>

        <!-- Steps -->
        <div class="mt-6 text-left bg-gray-50 rounded-xl p-5 text-sm text-gray-700 space-y-2">
            <p class="font-semibold mb-2">Langkah Mudah:</p>
            <p>1️⃣ Klik tombol <span class="font-semibold text-blue-600">Ambil Antrian</span></p>
            <p>2️⃣ Simpan atau ingat nomor antrian Anda</p>
            <p>3️⃣ Tunggu hingga nomor Anda dipanggil</p>
        </div>

        <!-- Button -->
        <div class="mt-8">
            <a href="{{ route("antrian-update") }}"
               class="inline-block w-full py-4 text-lg font-semibold bg-blue-600 text-white rounded-xl shadow-md hover:bg-blue-700 active:scale-95 transition">
                🎟 Ambil Antrian Sekarang
            </a>
        </div>

        <!-- Optional: Nomor Antrian Preview -->
        {{-- 
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <p class="text-sm text-gray-500">Nomor Antrian Anda</p>
            <p class="text-4xl font-bold text-blue-600 mt-2">A-012</p>
        </div>
        --}}

    </div>

</div>

@endsection