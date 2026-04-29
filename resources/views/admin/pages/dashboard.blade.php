@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Utama</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau performa klinik dan statistik antrian secara real-time.</p>
    </div>
    <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Hari Ini</p>
            <p class="text-sm font-bold text-gray-700">{{ now()->format('d F Y') }}</p>
        </div>
    </div>
</div>

{{-- Top Metrics --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Total Antrian --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <span class="text-xs font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">Hari Ini</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Total Antrian</h3>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($totalQueues) }}</p>
    </div>

    {{-- Selesai Diperiksa --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">Proses Medis</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Selesai Diperiksa</h3>
        <p class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($examinedQueues) }}</p>
    </div>

    {{-- Pendapatan --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            </div>
            <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">Cash Flow</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium">Pendapatan Real</h3>
        <p class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Eselon Stats --}}
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            Pasien per Kategori (Hari Ini)
        </h3>
        <div class="space-y-4">
            @foreach($eselonStats as $stat)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        <span class="text-sm font-semibold text-gray-700">{{ $stat->name }}</span>
                    </div>
                    <span class="px-3 py-1 bg-white rounded-lg shadow-sm text-sm font-bold text-blue-600">{{ $stat->queues_count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Quick Shortcuts --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Akses Cepat Modul</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('registrasi.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Pendaftaran</p>
                <p class="text-[10px] text-gray-400">Input pasien baru</p>
            </a>

            <a href="{{ route('anamnesis.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Amnesa</p>
                <p class="text-[10px] text-gray-400">Pemeriksaan perawat</p>
            </a>

            <a href="{{ route('doctor-exam.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Pemeriksaan</p>
                <p class="text-[10px] text-gray-400">Diagnosis dokter</p>
            </a>

            <a href="{{ route('cashier.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Kasir</p>
                <p class="text-[10px] text-gray-400">Pembayaran & Bill</p>
            </a>

            <a href="{{ route('pharmacy.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Farmasi</p>
                <p class="text-[10px] text-gray-400">Pengambilan obat</p>
            </a>

            <a href="{{ route('reports.medical_records.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50 transition-all group">
                <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Laporan</p>
                <p class="text-[10px] text-gray-400">Histori rekam medis</p>
            </a>
        </div>
    </div>
</div>

@endsection
