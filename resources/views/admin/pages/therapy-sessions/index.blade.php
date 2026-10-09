@extends('admin.layouts.app')

@section('title', 'Sesi Terapi Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Sesi Terapi Pasien', 'url' => ''],
]" />

{{-- HEADER & ACTION BUTTONS --}}
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
            <span>🏃‍♂️</span> Sesi Terapi Pasien
        </h1>
        <p class="text-xs text-gray-500 mt-1">
            Pengelolaan alur terapi berjenjang (Tahap A, B, C) dengan dukungan pencatatan lebih dari 1 sesi terapi dalam satu hari.
        </p>
    </div>

    <div class="flex items-center gap-2.5">
        <a href="{{ route('therapy-sessions.create') }}" 
           class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-200 transition flex items-center gap-2">
            <span class="text-sm leading-none">+</span>
            <span>Tambah Sesi Terapi Baru</span>
        </a>
    </div>
</div>

@include('admin.partials.alert')

{{-- 4 STAT CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block">Total Sesi Hari Ini</span>
            <span class="text-2xl font-black text-gray-900 mt-0.5 block">{{ $todayTotal }}</span>
            <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::today()->format('d M Y') }}</span>
        </div>
        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl font-bold">
            📅
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-[11px] font-bold text-amber-500 uppercase tracking-wider block">Terjadwal / Menunggu</span>
            <span class="text-2xl font-black text-amber-600 mt-0.5 block">{{ $todayScheduled }}</span>
            <span class="text-[10px] text-gray-400">Siap dilaksanakan</span>
        </div>
        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold">
            ⏳
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider block">Selesai Hari Ini</span>
            <span class="text-2xl font-black text-emerald-600 mt-0.5 block">{{ $todayCompleted }}</span>
            <span class="text-[10px] text-gray-400">Evaluasi tercatat</span>
        </div>
        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl font-bold">
            ✓
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-purple-100 flex items-center justify-between">
        <div>
            <span class="text-[11px] font-bold text-purple-600 uppercase tracking-wider block">&gt;1 Terapi Hari Ini</span>
            <span class="text-2xl font-black text-purple-700 mt-0.5 block">{{ $multiSessionCount }} Pasien</span>
            <span class="text-[10px] text-purple-500 font-medium">Terapi multi-sesi harian</span>
        </div>
        <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl font-bold">
            ⚡
        </div>
    </div>
</div>

{{-- FILTER & SEARCH TOOLBAR --}}
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" action="{{ route('therapy-sessions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
        {{-- Tanggal Filter --}}
        <div class="lg:col-span-3">
            <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Pilih Tanggal</label>
            <div class="flex items-center gap-1.5">
                <input type="date" name="date" value="{{ $selectedDate === 'all' ? '' : $selectedDate }}" 
                       class="w-full text-xs border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white">
                <a href="{{ route('therapy-sessions.index', ['date' => \Carbon\Carbon::today()->toDateString()]) }}" 
                   title="Kembali ke Hari Ini"
                   class="px-2.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-xl font-semibold whitespace-nowrap">
                    Hari Ini
                </a>
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="lg:col-span-3">
            <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Status Sesi</label>
            <select name="status" class="w-full text-xs border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white">
                <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua Status</option>
                <option value="scheduled" {{ $statusFilter == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                <option value="in_progress" {{ $statusFilter == 'in_progress' ? 'selected' : '' }}>Sedang Berjalan</option>
                <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        {{-- Pencarian Pasien --}}
        <div class="lg:col-span-4">
            <label class="block text-[11px] font-bold uppercase text-gray-500 mb-1">Cari Pasien / No HP</label>
            <input type="text" name="q" value="{{ $search }}" placeholder="Nama pasien atau nomor HP..." 
                   class="w-full text-xs border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 bg-white">
        </div>

        {{-- Tombol Filter --}}
        <div class="lg:col-span-2 flex items-center gap-2">
            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl transition">
                Terapkan
            </button>
            <a href="{{ route('therapy-sessions.index', ['date' => 'all']) }}" 
               class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-xl font-medium whitespace-nowrap"
               title="Tampilkan Semua Tanggal">
                Semua
            </a>
        </div>
    </form>
</div>

{{-- DAFTAR SESI TERAPI TABLE --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase font-semibold">
                    <th class="p-4">Waktu Sesi</th>
                    <th class="p-4">Sesi Harian</th>
                    <th class="p-4">Data Pasien</th>
                    <th class="p-4">Jenjang / Tahapan Terapi</th>
                    <th class="p-4">Terapis Penanggung Jawab</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Evaluasi / Tindakan</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($sessions as $session)
                    @php
                        $isMulti = in_array($session->patient_id, $multiSessionPatientIds);
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition {{ $isMulti ? 'bg-purple-50/20' : '' }}">
                        {{-- Waktu Sesi --}}
                        <td class="p-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900 text-sm">
                                🕒 {{ $session->scheduled_at ? $session->scheduled_at->format('H:i') . ' WIB' : '-' }}
                            </div>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                📅 {{ $session->scheduled_at ? $session->scheduled_at->format('d M Y') : '-' }}
                            </div>
                        </td>

                        {{-- Sesi Harian (>1 Terapi per Hari) --}}
                        <td class="p-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold 
                                {{ ($session->daily_session_order ?? 1) > 1 ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-50 text-blue-800 border border-blue-100' }}">
                                <span>⚡</span> Sesi Ke-{{ $session->daily_session_order ?? 1 }}
                            </span>
                            @if($isMulti)
                                <span class="block text-[10px] text-purple-600 font-semibold mt-1">Multi-Sesi Hari Ini</span>
                            @endif
                        </td>

                        {{-- Pasien --}}
                        <td class="p-4">
                            <div class="font-bold text-gray-900 text-sm">
                                {{ $session->patient?->full_name ?? '-' }}
                            </div>
                            <div class="text-[11px] text-gray-500 font-mono mt-0.5">
                                📱 {{ $session->patient?->phone_number ?? '-' }}
                                @if($session->patient?->age)
                                    • {{ $session->patient->age }} thn
                                @endif
                            </div>
                        </td>

                        {{-- Jenjang / Tahapan Terapi --}}
                        <td class="p-4">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-800 font-bold text-xs">
                                <span>Tahap {{ $session->stage_number }} ({{ chr(64 + $session->stage_number) }})</span>
                            </div>
                            <div class="text-[11px] text-gray-600 font-medium mt-1">
                                {{ $session->stage_name }}
                            </div>
                        </td>

                        {{-- Terapis --}}
                        <td class="p-4 whitespace-nowrap">
                            <div class="font-bold text-gray-800">
                                {{ $session->therapist?->full_name ?? 'Ditentukan Klinik' }}
                            </div>
                            @if($session->therapist)
                                <div class="text-[10px] text-gray-400">{{ $session->therapist->specialization }}</div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="p-4 whitespace-nowrap">
                            @if($session->status == 'completed')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Selesai
                                </span>
                            @elseif($session->status == 'in_progress')
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 border border-blue-200 animate-pulse">
                                    Sedang Berjalan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    Terjadwal
                                </span>
                            @endif
                        </td>

                        {{-- Evaluasi / Tindakan --}}
                        <td class="p-4 max-w-xs truncate text-gray-600">
                            @if($session->evaluation_notes)
                                <div class="text-[11px] text-gray-800 font-medium truncate" title="{{ $session->evaluation_notes }}">
                                    📝 {{ $session->evaluation_notes }}
                                </div>
                            @elseif($session->actions_taken)
                                <div class="text-[11px] text-gray-600 truncate" title="{{ $session->actions_taken }}">
                                    🔧 {{ $session->actions_taken }}
                                </div>
                            @else
                                <span class="text-gray-400 text-[11px] italic">Belum ada evaluasi</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="p-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('therapy-sessions.show', $session->id) }}" 
                                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition flex items-center gap-1">
                                    <span>Lembar Kerja</span>
                                    <span>→</span>
                                </a>

                                {{-- Shortcut Tambah Sesi Lagi Hari Ini untuk Pasien Ini --}}
                                <a href="{{ route('therapy-sessions.create', ['patient_id' => $session->patient_id, 'date' => $session->scheduled_at ? $session->scheduled_at->toDateString() : '']) }}" 
                                   title="Tambah Sesi Lagi untuk Pasien Ini di Hari yang Sama"
                                   class="px-2 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs rounded-xl border border-purple-200 transition">
                                    + Sesi
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-gray-400 text-xs">
                            <div class="text-3xl mb-2">📋</div>
                            <p class="font-bold text-gray-600 text-sm">Tidak ada sesi terapi ditemukan untuk kriteria ini.</p>
                            <p class="mt-1 text-gray-400">Silakan pilih tanggal lain atau buat sesi terapi baru untuk pasien.</p>
                            <div class="mt-4">
                                <a href="{{ route('therapy-sessions.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl inline-block">
                                    + Tambah Sesi Terapi Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@endsection
