@extends('admin.layouts.app')

@section('title', 'Laporan Rekam Terapi Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Laporan'],
    ['label' => 'Rekam Terapi Pasien'],
]" />

{{-- Header --}}
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
            <span>📋</span> Laporan & Rekap Rekam Terapi
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Rekapitulasi riwayat penanganan fisioterapi berjenjang, perkembangan klinis pasien, dan penugasan terapis.
        </p>
    </div>
</div>

{{-- Quick Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
            🏃‍♂️
        </div>
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Sesi Terapi</span>
            <span class="text-2xl font-black text-gray-900">{{ number_format($totalSessions) }}</span>
            <span class="text-[11px] text-gray-400 block">Sesi pada rentang waktu ini</span>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            ✓
        </div>
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Sesi Selesai (Completed)</span>
            <span class="text-2xl font-black text-emerald-600">{{ number_format($completedSessions) }}</span>
            <span class="text-[11px] text-gray-400 block">Evaluasi klinis telah tercatat</span>
        </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
            ⏳
        </div>
        <div>
            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Terjadwal / Sedang Berjalan</span>
            <span class="text-2xl font-black text-amber-600">{{ number_format($inProgressSessions) }}</span>
            <span class="text-[11px] text-gray-400 block">Menunggu / sedang ditangani</span>
        </div>
    </div>
</div>

{{-- Filter Box --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" action="{{ route('reports.medical_records.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Terapis</label>
                <select name="therapist_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Terapis</option>
                    @foreach($therapists as $t)
                        <option value="{{ $t->id }}" {{ request('therapist_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->full_name }} ({{ $t->specialization ?: 'Terapis' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Jenjang Terapi</label>
                <select name="therapy_type_id" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua Jenjang</option>
                    @foreach($therapyTypes as $tt)
                        <option value="{{ $tt->id }}" {{ request('therapy_type_id') == $tt->id ? 'selected' : '' }}>
                            {{ $tt->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Status Sesi</label>
                <select name="status" class="w-full rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✓ Selesai</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Terapi</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 pt-2 border-t border-gray-100 items-center justify-between">
            <div class="w-full sm:flex-1">
                <div class="relative">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari Nama Pasien, No. RM, No. WhatsApp, atau Nama Terapis..." 
                           class="w-full pl-10 rounded-xl border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-initial px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    Terapkan Filter
                </button>
                <a href="{{ route('reports.medical_records.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-semibold text-sm flex items-center justify-center transition">
                    Reset
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/75 border-b border-gray-100 text-[11px] text-gray-500 uppercase tracking-wider font-bold">
                    <th class="p-4 text-center w-12">No</th>
                    <th class="p-4">Jadwal & Sesi</th>
                    <th class="p-4">Pasien</th>
                    <th class="p-4">Terapis Penanggung Jawab</th>
                    <th class="p-4">Tahapan & Jenjang Terapi</th>
                    <th class="p-4">Evaluasi / Hasil Terapi</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($records as $index => $s)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="p-4 text-center text-xs text-gray-400 font-medium">
                            {{ $records->firstItem() + $index }}
                        </td>

                        {{-- Jadwal & Sesi --}}
                        <td class="p-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900">
                                {{ $s->scheduled_at ? $s->scheduled_at->format('d M Y') : '-' }}
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                <span>⏰ {{ $s->scheduled_at ? $s->scheduled_at->format('H:i') : '--:--' }} WIB</span>
                                <span class="px-1.5 py-0.2 rounded bg-purple-50 text-purple-700 font-bold text-[10px]">
                                    Sesi {{ $s->daily_session_order ?? 1 }}
                                </span>
                            </div>
                        </td>

                        {{-- Pasien --}}
                        <td class="p-4">
                            <div class="font-bold text-gray-800">
                                {{ $s->patient?->full_name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                <span>RM: <strong class="font-mono text-gray-700">{{ $s->patient?->patient_code ? substr($s->patient->patient_code, 0, 8) : '-' }}</strong></span>
                                @if($s->patient?->phone_number)
                                    <span>• 📱 {{ $s->patient->phone_number }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- Terapis --}}
                        <td class="p-4">
                            <div class="font-semibold text-gray-900 flex items-center gap-1.5">
                                <span>👨‍⚕️</span>
                                <span>{{ $s->therapist?->full_name ?? 'Belum Ditentukan' }}</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $s->therapist?->specialization ?: 'Fisioterapis Olahraga' }}
                            </div>
                        </td>

                        {{-- Tahapan & Jenjang Terapi --}}
                        <td class="p-4">
                            <div class="font-medium text-gray-800">
                                {{ $s->stage_name ?: ($s->therapyType?->name ?? 'Sesi Terapi') }}
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded text-[10px] font-bold">
                                    Tahap {{ $s->stage_number }} ({{ chr(64 + ($s->stage_number ?: 1)) }})
                                </span>
                                @if($s->therapyType?->duration_minutes)
                                    <span class="text-xs text-gray-400">⏱️ {{ $s->therapyType->duration_minutes }} mnt</span>
                                @endif
                            </div>
                        </td>

                        {{-- Evaluasi Klinis --}}
                        <td class="p-4 max-w-xs">
                            @if(!empty($s->evaluation_notes))
                                <p class="text-xs text-gray-700 line-clamp-2 italic" title="{{ $s->evaluation_notes }}">
                                    "{{ $s->evaluation_notes }}"
                                </p>
                                @if($s->recommended_next_stage)
                                    <div class="text-[10px] text-blue-600 font-medium mt-1">
                                        👉 {{ $s->recommended_next_stage }}
                                    </div>
                                @endif
                            @else
                                <span class="text-xs text-gray-400 italic">Belum ada evaluasi klinis.</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="p-4 text-center whitespace-nowrap">
                            @if($s->status == 'completed')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                    ✓ Selesai
                                </span>
                            @elseif($s->status == 'in_progress')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    Sedang Terapi
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                    Terjadwal
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="p-4 text-right whitespace-nowrap">
                            <a href="{{ route('reports.medical_records.show', $s->id) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-bold transition"
                               title="Lihat Lembar Rekam Terapi">
                                <span>Lihat Detail</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center text-3xl mb-3">
                                    🏃‍♂️
                                </div>
                                <h3 class="font-bold text-gray-700 text-base">Tidak Ada Data Rekam Terapi</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-sm">
                                    Tidak ditemukan sesi terapi pada rentang filter tanggal atau kriteria pencarian yang Anda pilih.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $records->links() }}
</div>

@endsection
