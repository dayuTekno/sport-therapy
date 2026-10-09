@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- Header Banner --}}
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Dashboard Utama</h1>
        <p class="text-sm text-gray-500 mt-1">Sistem Manajemen Klinik Sport Physiotherapy & Rehabilitasi Cedera.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="px-4 py-2.5 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tanggal Hari Ini</p>
                <p class="text-sm font-bold text-gray-700">{{ now()->format('d F Y') }}</p>
            </div>
        </div>
        <div class="hidden md:flex items-center gap-2 px-3.5 py-2.5 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 text-xs font-semibold">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Layanan Aktif
        </div>
    </div>
</div>

{{-- Top Metrics Grid (4 Kolom Rapi & Seimbang) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- Total Antrian & Reservasi --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg">Hari Ini</span>
        </div>
        <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Antrian & Reservasi</h3>
        <p class="text-2xl md:text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalQueues) }}</p>
        <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
            @if($pendingReservations > 0)
                <span class="text-amber-600 font-semibold">{{ $pendingReservations }} reservasi baru</span>
            @else
                <span class="text-gray-500">Kunjungan terjadwal</span>
            @endif
        </p>
    </div>

    {{-- Selesai Diperiksa / Ditangani --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-lg">Proses Medis</span>
        </div>
        <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Sesi Terapi Selesai</h3>
        <p class="text-2xl md:text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalCompleted) }}</p>
        <p class="text-xs text-gray-400 mt-2">Evaluasi terapi tuntas</p>
    </div>

    {{-- Total Pasien Terdaftar --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-lg">Database</span>
        </div>
        <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Total Pasien</h3>
        <p class="text-2xl md:text-3xl font-extrabold text-gray-800 mt-1">{{ number_format($totalPatients) }}</p>
        <p class="text-xs text-gray-400 mt-2">Data rekam pasien aktif</p>
    </div>

    {{-- Pendapatan Kasir --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">Cash Flow</span>
        </div>
        <h3 class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Pendapatan Real</h3>
        <p class="text-xl md:text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($revenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-2">Penerimaan kasir valid</p>
    </div>
</div>

{{-- Main Grid: Akses Cepat Modul & Ringkasan Alur Layanan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Akses Cepat Modul Pelayanan (6 Modul Utama Klinik) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-7">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Akses Cepat Modul Klinik</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pintasan navigasi untuk operasional pelayanan terapis harian.</p>
            </div>
            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                6 Modul Layanan
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            {{-- 1. Reservasi Terapi --}}
            <a href="{{ route('reservations.index') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-blue-500 hover:bg-blue-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Reservasi Terapi</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Booking jadwal & keluhan cedera pasien.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-blue-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            {{-- 2. Riwayat Terapi --}}
            <a href="{{ route('reservations.history') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-purple-500 hover:bg-purple-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-purple-600 transition-colors">Riwayat Terapi</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Arsip reservasi selesai & dibatalkan.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-purple-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            {{-- 3. Sesi Terapi Pasien --}}
            <a href="{{ route('therapy-sessions.index') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-rose-500 hover:bg-rose-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-rose-600 transition-colors">Sesi Terapi Pasien</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Lembar kerja terapi berjenjang Tahap A - D.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-rose-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            {{-- 4. Kasir & Pembayaran --}}
            <a href="{{ route('cashier.index') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-amber-500 hover:bg-amber-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-amber-600 transition-colors">Kasir & Billing</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Pembayaran tindakan & kwitansi kasir.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-amber-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            {{-- 5. Peralatan Terapi --}}
            <a href="{{ route('therapy-equipments.index') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-emerald-500 hover:bg-emerald-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-emerald-600 transition-colors">Peralatan Terapi</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Manajemen stok alat modalitas & gym.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>

            {{-- 6. Rekap Rekam Medis --}}
            <a href="{{ route('reports.medical_records.index') }}" 
               class="p-4 rounded-xl border border-gray-100 hover:border-slate-500 hover:bg-slate-50/50 hover:shadow-sm transition-all group flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-slate-600 transition-colors">Rekap Rekam Medis</p>
                    <p class="text-[11px] text-gray-400 mt-1 leading-relaxed">Histori perkembangan pemulihan pasien.</p>
                </div>
                <div class="mt-3 flex items-center gap-1 text-[11px] font-semibold text-slate-600">
                    <span>Buka Modul</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </a>
        </div>
    </div>

    {{-- Kolom Kanan: Ringkasan Operasional & Aksi Cepat --}}
    <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Ringkasan Klinik
                </h3>
                <span class="text-[10px] font-bold uppercase text-gray-400 tracking-wider">Sport Clinic</span>
            </div>

            {{-- Highlight Status Alur Terapi --}}
            <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50/50 rounded-xl border border-blue-100/60 mb-5">
                <p class="text-xs font-bold text-blue-900 mb-1">Alur Terapi Berjenjang</p>
                <p class="text-[11px] text-blue-700/80 leading-relaxed">
                    Mendukung multi-sesi harian (>1 terapi per hari) dengan monitoring skala nyeri (VAS) dan range of motion (ROM).
                </p>
            </div>

            {{-- Stat List Ringkas --}}
            <div class="space-y-3 mb-6">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-xs font-semibold text-gray-700">Terapis Aktif</span>
                    </div>
                    <span class="px-2.5 py-0.5 bg-white rounded-lg shadow-xs text-xs font-bold text-blue-600">
                        {{ $totalTherapists }} Terapis
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span class="text-xs font-semibold text-gray-700">Jenjang Terapi</span>
                    </div>
                    <span class="px-2.5 py-0.5 bg-white rounded-lg shadow-xs text-xs font-bold text-purple-600">
                        {{ $totalTherapyTypes }} Tahap (A - D)
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-xs font-semibold text-gray-700">Peralatan Terapi</span>
                    </div>
                    <span class="px-2.5 py-0.5 bg-white rounded-lg shadow-xs text-xs font-bold text-emerald-600">
                        {{ $totalEquipments }} Alat Terdaftar
                    </span>
                </div>
            </div>
        </div>

        {{-- Tombol Tindakan Cepat --}}
        <div class="space-y-2 pt-2 border-t border-gray-100">
            <a href="{{ route('reservations.create') }}" 
               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Reservasi Baru
            </a>
            <a href="{{ route('therapy-sessions.create') }}" 
               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-xs font-bold rounded-xl transition-colors">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Buka Sesi Terapi Baru
            </a>
        </div>
    </div>

</div>

@endsection
