@extends('admin.layouts.app')

@section('title', 'Master Data Referal')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Referal'],
]" />

{{-- Header & Action Button --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Master Data Referal</h1>
        <p class="text-xs text-gray-500 mt-1">Kelola data mitra/perujuk, pantau jumlah pasien terafiliasi, serta frekuensi sesi terapi untuk perhitungan komisi.</p>
    </div>

    <div class="flex items-center gap-2.5">
        <a href="{{ route('patients.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs font-semibold rounded-xl border border-gray-200 shadow-xs transition">
            👥 Master Pasien
        </a>
        <a href="{{ route('referrals.create') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Referal
        </a>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Total Referal --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
            🤝
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Total Mitra Referal</span>
            <div class="text-xl font-extrabold text-gray-900 mt-0.5">{{ number_format($totalReferrals ?? 0) }}</div>
        </div>
    </div>

    {{-- Total Pasien Terafiliasi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            👥
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Pasien Terafiliasi</span>
            <div class="text-xl font-extrabold text-emerald-700 mt-0.5">{{ number_format($totalAffiliatedPatients ?? 0) }} <span class="text-xs font-normal text-gray-400">Orang</span></div>
        </div>
    </div>

    {{-- Total Sesi Terapi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
            🏃
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Total Sesi Terapi</span>
            <div class="text-xl font-extrabold text-purple-700 mt-0.5">
                {{ number_format($totalCompletedSessions ?? 0) }} 
                <span class="text-xs font-normal text-gray-400">selesai / {{ number_format($totalTherapySessions ?? 0) }} sesi</span>
            </div>
        </div>
    </div>

    {{-- Estimasi Komisi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
            💰
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Estimasi Total Komisi</span>
            <div class="text-base font-extrabold text-amber-700 mt-0.5 font-mono">
                Rp {{ number_format($totalEstimatedCommission ?? 0, 0, ',', '.') }}
            </div>
        </div>
    </div>
</div>

{{-- Alert Messages --}}
@if (session('success'))
    <div class="mb-4 p-4 text-xs font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
        <span>✓</span> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 text-xs font-medium text-red-800 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
        <span>⚠️</span> {{ session('error') }}
    </div>
@endif

{{-- Main Table Container --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Search Form --}}
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('referrals.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Nama Referal / Kode / Instansi / No. HP..."
                   class="w-full px-3.5 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs">
            <button type="submit" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 text-xs font-semibold shadow-xs">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('referrals.index') }}" class="px-3 py-2 text-xs text-gray-500 hover:text-gray-700 flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
            <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5">Kode</th>
                    <th class="px-5 py-3.5">Nama Referal / Perujuk</th>
                    <th class="px-5 py-3.5">Instansi / Asal</th>
                    <th class="px-5 py-3.5">Kontak</th>
                    <th class="px-5 py-3.5 text-center">Pasien Terafiliasi</th>
                    <th class="px-5 py-3.5 text-center">Frekuensi Sesi Terapi</th>
                    <th class="px-5 py-3.5">Tarif Komisi / Sesi</th>
                    <th class="px-5 py-3.5">Akumulasi Komisi</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($referrals as $ref)
                    @php
                        $accumulatedCommission = ($ref->commission_amount ?? 0) * ($ref->completed_sessions_count ?? 0);
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition">
                        {{-- Kode --}}
                        <td class="px-5 py-3.5 font-mono text-blue-600 font-semibold whitespace-nowrap">
                            {{ $ref->code ?: 'REF-' . $ref->id }}
                        </td>

                        {{-- Nama Referal --}}
                        <td class="px-5 py-3.5 font-semibold text-gray-900">
                            <div>{{ $ref->name }}</div>
                            @if($ref->notes)
                                <div class="text-[10px] text-gray-400 font-normal max-w-xs truncate" title="{{ $ref->notes }}">
                                    {{ $ref->notes }}
                                </div>
                            @endif
                        </td>

                        {{-- Instansi / Asal --}}
                        <td class="px-5 py-3.5 text-gray-600">
                            <span class="inline-flex items-center gap-1">
                                <span>🏛️</span>
                                <span>{{ $ref->agency ?: '-' }}</span>
                            </span>
                        </td>

                        {{-- Kontak --}}
                        <td class="px-5 py-3.5 text-gray-700 font-mono">
                            @if($ref->phone)
                                <div class="flex items-center gap-1">
                                    <span>📱</span>
                                    <span>{{ $ref->phone }}</span>
                                </div>
                            @endif
                            @if($ref->email)
                                <div class="text-[10px] text-gray-400 font-sans">
                                    {{ $ref->email }}
                                </div>
                            @endif
                            @if(!$ref->phone && !$ref->email)
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>

                        {{-- Jumlah Pasien Terafiliasi --}}
                        <td class="px-5 py-3.5 text-center">
                            <a href="{{ route('referrals.show', $ref->id) }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 transition shadow-2xs"
                               title="Klik untuk melihat daftar pasien terafiliasi">
                                <span>👥</span>
                                <span>{{ number_format($ref->patients_count ?? 0) }} Pasien</span>
                            </a>
                        </td>

                        {{-- Frekuensi Terapi --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-md text-[11px]">
                                    {{ number_format($ref->completed_sessions_count ?? 0) }} Selesai
                                </span>
                                <span class="text-[10px] text-gray-400 mt-0.5">
                                    dari total {{ number_format($ref->therapy_sessions_count ?? 0) }} sesi
                                </span>
                            </div>
                        </td>

                        {{-- Tarif Komisi per Sesi --}}
                        <td class="px-5 py-3.5 font-mono text-gray-800 whitespace-nowrap">
                            @if(($ref->commission_amount ?? 0) > 0)
                                <span class="font-semibold text-gray-900">Rp {{ number_format($ref->commission_amount, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-400 block">/ sesi selesai</span>
                            @else
                                <span class="text-gray-400 italic text-[11px]">Belum diatur</span>
                            @endif
                        </td>

                        {{-- Akumulasi Komisi --}}
                        <td class="px-5 py-3.5 font-mono whitespace-nowrap">
                            @if($accumulatedCommission > 0)
                                <span class="font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg text-xs inline-block">
                                    Rp {{ number_format($accumulatedCommission, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Rp 0</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-3.5 text-center whitespace-nowrap">
                            @if($ref->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ● Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">
                                    Non-Aktif
                                </span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Detail Pasien --}}
                                <a href="{{ route('referrals.show', $ref->id) }}"
                                   title="Lihat Detail Pasien & Komisi"
                                   class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('referrals.edit', $ref->id) }}"
                                   title="Edit Data Referal"
                                   class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>

                                {{-- Hapus --}}
                                <form action="{{ route('referrals.destroy', $ref->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data referal {{ addslashes($ref->name) }}? Pasien yang terafiliasi tidak akan terhapus.')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Hapus Referal"
                                            class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-5 py-12 text-center text-gray-500">
                            <div class="text-3xl mb-2">🤝</div>
                            <p class="font-bold text-gray-800 text-sm">Belum Ada Data Referal</p>
                            <p class="text-xs text-gray-400 mt-1">Daftarkan mitra atau pihak perujuk pertama Anda untuk mulai mengelola afiliasi pasien.</p>
                            <a href="{{ route('referrals.create') }}" class="inline-flex items-center gap-1.5 mt-3 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                                + Tambah Data Referal
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($referrals->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $referrals->links() }}
        </div>
    @endif
</div>

@endsection
