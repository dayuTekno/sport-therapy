@extends('admin.layouts.app')

@section('title', 'Riwayat Terapi & Reservasi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Riwayat Terapi'],
]" />

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>📜</span> Riwayat Terapi & Reservasi
        </h1>
        <p class="text-xs text-gray-500 mt-1">Daftar arsip reservasi dan sesi terapi pasien yang telah diselesaikan atau dibatalkan.</p>
    </div>
</div>

@include('admin.partials.alert')

{{-- STATUS TABS: SEMUA RIWAYAT, TERAPI SELESAI, DIBATALKAN --}}
<div class="border-b border-gray-200 mb-6">
    <nav class="flex space-x-6" aria-label="Tabs">
        <a href="{{ route('reservations.history', array_merge(request()->except('status'), ['status' => ''])) }}"
           class="{{ empty(request('status')) ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>📚 Semua Riwayat</span>
            <span class="{{ empty(request('status')) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $totalHistoryCount }}
            </span>
        </a>
        <a href="{{ route('reservations.history', array_merge(request()->except('status'), ['status' => 'completed'])) }}"
           class="{{ request('status') == 'completed' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>✓ Terapi Selesai</span>
            <span class="{{ request('status') == 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $completedCount }}
            </span>
        </a>
        <a href="{{ route('reservations.history', array_merge(request()->except('status'), ['status' => 'cancelled'])) }}"
           class="{{ request('status') == 'cancelled' ? 'border-rose-600 text-rose-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>✕ Reservasi Dibatalkan</span>
            <span class="{{ request('status') == 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $cancelledCount }}
            </span>
        </a>
    </nav>
</div>

{{-- METRIC SUMMARY CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600 font-bold shrink-0">
            📚
        </div>
        <div>
            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider block">Total Riwayat Arsip</span>
            <span class="text-xl font-black text-gray-800">{{ $totalHistoryCount }}</span>
            <span class="text-[11px] text-gray-500 block">Selesai & Dibatalkan</span>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shrink-0">
            ✓
        </div>
        <div>
            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider block">Terapi Selesai</span>
            <span class="text-xl font-black text-emerald-700">{{ $completedCount }}</span>
            <span class="text-[11px] text-emerald-600 font-medium block">Selesai Ditangani</span>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3.5">
        <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold shrink-0">
            ✕
        </div>
        <div>
            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider block">Reservasi Dibatalkan</span>
            <span class="text-xl font-black text-rose-700">{{ $cancelledCount }}</span>
            <span class="text-[11px] text-rose-600 font-medium block">Batal / No-Show</span>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" action="{{ route('reservations.history') }}" class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari kode atau nama/no HP pasien..."
                       class="px-3 py-2 pl-9 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <select name="status" class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status Riwayat</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✓ Terapi Selesai</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>✕ Dibatalkan</option>
            </select>

            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span>Tanggal:</span>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <span>s/d</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                Filter
            </button>
            @if(request('search') || request('status') || request('date_from') || request('date_to'))
                <a href="{{ route('reservations.history') }}" class="text-xs text-gray-500 hover:text-gray-700 underline font-medium">Reset</a>
            @endif
        </div>

        <div class="text-xs text-gray-500">
            Menampilkan <span class="font-bold text-gray-800">{{ $reservations->total() }}</span> data riwayat
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kode Reservasi</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Pasien & Kontak</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Keluhan Utama</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Jadwal / Waktu Arsip</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Terapis</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Keterangan / Alasan</th>
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($reservations as $res)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                        <a href="{{ route('reservations.show', $res->id) }}" class="hover:underline">
                            {{ $res->reservation_code }}
                        </a>
                        <div class="text-[10px] text-gray-400 font-sans mt-0.5">
                            Dibuat: {{ $res->created_at->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $res->patient?->full_name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 font-mono mt-0.5">📱 {{ $res->patient?->phone_number ?? '-' }}</div>
                        @if($res->patient?->age)
                            <div class="text-[11px] text-gray-400">Usia: {{ $res->patient->age }} thn</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900 max-w-xs truncate font-medium text-xs">{{ $res->chief_complaint }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Durasi: {{ $res->complaint_duration }}</div>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-700 whitespace-nowrap">
                        <div class="font-medium">{{ $res->preferred_schedule }}</div>
                        @if($res->status == 'cancelled' && $res->cancelled_at)
                            <div class="text-[11px] text-rose-600 font-semibold mt-0.5">
                                Batal pada: {{ $res->cancelled_at->format('d M Y, H:i') }}
                            </div>
                        @elseif($res->status == 'completed' && $res->completed_at)
                            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">
                                Selesai pada: {{ $res->completed_at->format('d M Y, H:i') }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <div class="font-medium text-gray-900">{{ $res->therapist?->full_name ?? 'Ditentukan Klinik' }}</div>
                        @if($res->therapist?->specialization)
                            <div class="text-[10px] text-gray-500">{{ $res->therapist->specialization }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($res->status == 'completed')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span>✓</span> Selesai
                            </span>
                            <div class="mt-1">
                                @if($res->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                        <span>✓</span> Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">
                                        <span>⏳</span> Belum Lunas
                                    </span>
                                @endif
                            </div>
                        @elseif($res->status == 'cancelled')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                <span>✕</span> Dibatalkan
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                {{ ucfirst($res->status) }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs max-w-xs">
                        @if($res->status == 'cancelled')
                            <div class="text-rose-700 bg-rose-50/70 p-2 rounded-lg border border-rose-100 text-[11px] leading-relaxed">
                                <span class="font-bold block">Alasan Batal:</span>
                                {{ $res->cancellation_reason ?: ($res->notes ?: 'Tidak ada alasan khusus') }}
                            </div>
                        @else
                            <div class="text-gray-600 text-[11px]">
                                {{ $res->notes ?: 'Sesi terapi telah berhasil diselesaikan' }}
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($res->payment_status === 'paid')
                                <a href="{{ route('cashier.receipt', $res->id) }}" target="_blank"
                                   title="Cetak Kwitansi Pembayaran"
                                   class="px-2 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-blue-200">
                                    <span>🖨️</span> Kwitansi
                                </a>
                            @elseif($res->status === 'completed')
                                <a href="{{ route('cashier.process', $res->id) }}"
                                   title="Bayar di Kasir"
                                   class="px-2 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-amber-200">
                                    <span>💳</span> Bayar
                                </a>
                            @endif
                            @php
                                $rawPh = $res->patient?->phone_number ?? '';
                                $cleanPh = preg_replace('/\D/', '', $rawPh);
                                if (str_starts_with($cleanPh, '0')) {
                                    $waNum = '62' . substr($cleanPh, 1);
                                } elseif (str_starts_with($cleanPh, '8')) {
                                    $waNum = '62' . $cleanPh;
                                } else {
                                    $waNum = $cleanPh;
                                }
                                $msg = "Halo Kak " . ($res->patient?->full_name ?? 'Pasien') . ", terkait riwayat terapi [{$res->reservation_code}] di Sport Therapy Clinic. Terima kasih!";
                                $waLink = !empty($waNum) ? "https://wa.me/{$waNum}?text=" . rawurlencode($msg) : null;
                            @endphp
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank"
                                   title="Hubungi Pasien via WhatsApp"
                                   class="px-2 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-emerald-200">
                                    <span>📱</span> WA
                                </a>
                            @endif
                            <a href="{{ route('reservations.show', $res->id) }}"
                               class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded-lg transition">
                                Detail →
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">📜</div>
                        <p class="font-medium text-gray-500">Belum ada data riwayat terapi yang selesai atau dibatalkan.</p>
                        <p class="text-xs text-gray-400 mt-1">Hanya reservasi yang telah selesai atau dibatalkan yang ditampilkan di halaman ini.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $reservations->links() }}
    </div>
</div>

@endsection
