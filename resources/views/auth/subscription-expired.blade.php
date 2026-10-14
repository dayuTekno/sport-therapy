@extends('layouts.auth')

@section('title', 'Masa Langganan Berakhir - Sistem Rumah Terapi')

@section('content')
<div class="flex items-center justify-center min-h-screen px-4 py-8">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700/60 p-6 sm:p-10 text-center">
        
        {{-- Warning Icon Badge --}}
        <div class="w-20 h-20 mx-auto mb-6 rounded-3xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800/60 flex items-center justify-center text-4xl shadow-inner">
            ⏳
        </div>

        {{-- Badges --}}
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-400 border border-rose-200 dark:border-rose-800 mb-3">
            <span>⚠️</span> Akses Menu Terkunci
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-2">
            Masa Langganan Berakhir
        </h1>
        <p class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-6">
            Sistem Rumah Terapi
        </p>

        {{-- Clinic Info Box --}}
        <div class="bg-gray-50 dark:bg-gray-900/60 rounded-2xl p-4 sm:p-5 border border-gray-100 dark:border-gray-700/50 text-left mb-6 space-y-2 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">Nama Klinik:</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ $clinic?->name ?? 'Klinik Anda' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">Kode Klinik:</span>
                <span class="font-mono font-bold text-gray-700 dark:text-gray-300">{{ $clinic?->clinic_code ?? '-' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400">Status Langganan:</span>
                <span class="font-bold text-rose-600 dark:text-rose-400 uppercase">Expired / Habis</span>
            </div>
            @if($clinic?->subscription_ends_at)
                <div class="flex items-center justify-between pt-1 border-t border-gray-200 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Tanggal Berakhir (End Date):</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400">
                        {{ $clinic->subscription_ends_at->format('d F Y, H:i') }}
                    </span>
                </div>
            @elseif($clinic?->trial_ends_at)
                <div class="flex items-center justify-between pt-1 border-t border-gray-200 dark:border-gray-800">
                    <span class="text-gray-500 dark:text-gray-400">Masa Trial Habis:</span>
                    <span class="font-bold text-rose-600 dark:text-rose-400">
                        {{ $clinic->trial_ends_at->format('d F Y, H:i') }}
                    </span>
                </div>
            @endif
        </div>

        <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
            Seluruh menu operasional (Pasien, Reservasi, Terapi, Kasir, Laporan, dll) tidak dapat dibuka sementara karena masa aktif langganan klinik Anda telah habis. Silakan hubungi Administrator SaaS untuk memperpanjang waktu subscribe agar dapat mengakses menu kembali.
        </p>

        {{-- Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center items-center">
            @php
                $waText = urlencode("Halo Admin SaaS, saya ingin memperpanjang masa aktif langganan subscribe untuk klinik {$clinic?->name} ({$clinic?->clinic_code}). Mohon bantuannya.");
                $waNumber = "6285183036722";
            @endphp
            <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition">
                <span>💬</span> Hubungi Admin via WhatsApp
            </a>

            <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                @csrf
                <button type="submit" 
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-bold transition">
                    <span>🚪</span> Keluar (Logout)
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
