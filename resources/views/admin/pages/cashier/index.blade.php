@extends('admin.layouts.app')

@section('title', 'Kasir & Pembayaran Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Kasir & Pembayaran'],
]" />

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>💳</span> Kasir & Pembayaran Terapi Pasien
        </h1>
        <p class="text-xs text-gray-500 mt-1">Kelola penagihan pembayaran per reservasi pasien lengkap dengan rincian detail seluruh sesi terapi.</p>
    </div>
</div>

@include('admin.partials.alert')

{{-- METRIC SUMMARY CARDS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-lg shrink-0">
            ⏳
        </div>
        <div>
            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider block">Menunggu Pembayaran</span>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-black text-amber-700">{{ $unpaidCount }} Reservasi</span>
                <span class="text-xs font-bold text-gray-600">(Total Rp {{ number_format($unpaidTotal, 0, ',', '.') }})</span>
            </div>
            <span class="text-[11px] text-gray-500 block">Reservasi dengan sesi terapi aktif / selesai yang belum lunas</span>
        </div>
    </div>

    <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
            ✓
        </div>
        <div>
            <span class="text-[11px] text-gray-400 font-semibold uppercase tracking-wider block">Penerimaan Hari Ini (Lunas)</span>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-black text-emerald-700">Rp {{ number_format($todayPaidTotal, 0, ',', '.') }}</span>
                <span class="text-xs font-bold text-gray-600">({{ $todayPaidCount }} Transaksi)</span>
            </div>
            <span class="text-[11px] text-emerald-600 font-medium block">Kasir klinik / QRIS / Transfer / Debit</span>
        </div>
    </div>
</div>

{{-- TOP TABS: UNPAID VS PAID --}}
<div class="border-b border-gray-200 mb-6">
    <nav class="flex space-x-6" aria-label="Tabs">
        <a href="{{ route('cashier.index', ['tab' => 'unpaid']) }}"
           class="{{ $tab !== 'paid' ? 'border-amber-500 text-amber-700 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>⚡ Menunggu Pembayaran</span>
            <span class="{{ $tab !== 'paid' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $unpaidCount }}
            </span>
        </a>
        <a href="{{ route('cashier.index', ['tab' => 'paid']) }}"
           class="{{ $tab === 'paid' ? 'border-emerald-600 text-emerald-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>✓ Riwayat Pembayaran (Lunas)</span>
            <span class="{{ $tab === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $todayPaidCount }}
            </span>
        </a>
    </nav>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" action="{{ route('cashier.index') }}" class="flex flex-wrap gap-3 items-center justify-between">
        <input type="hidden" name="tab" value="{{ $tab }}">
        
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari pasien, kode reservasi, invoice..."
                       class="px-3 py-2 pl-9 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span>Tanggal:</span>
                <input type="date" name="date" value="{{ request('date') }}"
                       class="px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                Filter
            </button>
            @if(request('search') || request('date'))
                <a href="{{ route('cashier.index', ['tab' => $tab]) }}" class="text-xs text-gray-500 hover:text-gray-700 underline font-medium">Reset</a>
            @endif
        </div>

        <div class="text-xs text-gray-500">
            Menampilkan <span class="font-bold text-gray-800">{{ $reservations->total() }}</span> reservasi
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                @if($tab === 'paid')
                    <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">No. Invoice & Tanggal</th>
                @else
                    <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Reservasi & Jadwal</th>
                @endif
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Pasien & Kontak</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Detail Tagihan Sesi Terapi</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Terapis</th>
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Total Tagihan</th>
                <th class="px-6 py-3.5 text-center font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($reservations as $res)
                @php
                    $sessionCount = $res->sessions->count();
                    $calculatedTotal = $res->total_price > 0 ? $res->total_price : $res->sessions->sum('total_price');
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    {{-- Kolom 1: Identitas Transaksi / Reservasi --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($tab === 'paid')
                            <div class="font-mono font-bold text-emerald-700 text-xs">{{ $res->invoice_code ?? 'INV-' . $res->id }}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5">
                                {{ $res->paid_at ? $res->paid_at->format('d M Y, H:i') : '-' }} WIB
                            </div>
                            <div class="text-[10px] text-gray-400 font-mono mt-0.5">
                                Ref: {{ $res->reservation_code }}
                            </div>
                        @else
                            <div class="font-mono font-bold text-blue-700 text-xs">
                                {{ $res->reservation_code }}
                            </div>
                            <div class="text-[11px] text-gray-600 mt-0.5">
                                🕒 {{ $res->preferred_schedule }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">
                                Dibuat: {{ $res->created_at->format('d M Y') }}
                            </div>
                        @endif
                    </td>

                    {{-- Kolom 2: Pasien --}}
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 text-xs">{{ $res->patient?->full_name ?? '-' }}</div>
                        <div class="text-[11px] text-gray-500 font-mono mt-0.5">📱 {{ $res->patient?->phone_number ?? '-' }}</div>
                        @if($res->patient?->age)
                            <div class="text-[10px] text-gray-400">Usia: {{ $res->patient->age }} thn</div>
                        @endif
                    </td>

                    {{-- Kolom 3: Rincian Detail Sesi Terapi dalam Reservasi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1.5 mb-1.5">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md text-[10px] font-bold border border-blue-100">
                                {{ $sessionCount }} Sesi Terapi
                            </span>
                        </div>
                        @if($sessionCount > 0)
                            <div class="space-y-1">
                                @foreach($res->sessions as $sess)
                                    <div class="text-[11px] text-gray-700 flex items-center justify-between gap-3 bg-gray-50/80 px-2 py-1 rounded-lg border border-gray-100">
                                        <div class="truncate max-w-[220px]">
                                            <span class="font-bold text-indigo-700">Tahap {{ chr(64 + $sess->stage_number) }}:</span>
                                            <span>{{ $sess->stage_name }}</span>
                                        </div>
                                        <span class="font-mono text-[10px] text-gray-600 font-semibold shrink-0">
                                            Rp {{ number_format($sess->total_price > 0 ? $sess->total_price : ($sess->therapyType?->price ?? 0), 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum ada sesi tercatat</span>
                        @endif
                    </td>

                    {{-- Kolom 4: Terapis --}}
                    <td class="px-6 py-4 text-xs text-gray-700">
                        {{ $res->therapist?->full_name ?? 'Terapis Klinik' }}
                    </td>

                    {{-- Kolom 5: Total Tagihan --}}
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        @if($tab === 'paid')
                            <div class="font-bold text-emerald-700 text-sm">
                                Rp {{ number_format($res->paid_amount, 0, ',', '.') }}
                            </div>
                            @if($res->discount > 0)
                                <div class="text-[10px] text-gray-400">
                                    Diskon: Rp {{ number_format($res->discount, 0, ',', '.') }}
                                </div>
                            @endif
                        @else
                            <div class="font-bold text-gray-900 text-sm">
                                Rp {{ number_format($calculatedTotal, 0, ',', '.') }}
                            </div>
                        @endif
                    </td>

                    {{-- Kolom 6: Status Pembayaran Kasir --}}
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        @if($tab === 'paid')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✓ Lunas (Kasir)
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Kasir
                            </span>
                        @endif
                    </td>

                    {{-- Kolom 7: Aksi --}}
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        @if($tab === 'paid')
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('cashier.receipt', $res->id) }}" target="_blank"
                                   class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg text-xs font-semibold transition flex items-center gap-1">
                                    <span>🖨️</span> Kwitansi
                                </a>
                                <a href="{{ route('reservations.show', $res->id) }}"
                                   class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">
                                    Detail
                                </a>
                            </div>
                        @else
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('cashier.process', $res->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 text-xs font-bold shadow-xs transition">
                                    <span>💳</span> Bayar di Kasir →
                                </a>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        @if($tab === 'paid')
                            <div class="text-3xl mb-2">🧾</div>
                            <p class="font-medium text-gray-500">Belum ada riwayat transaksi pembayaran reservasi.</p>
                        @else
                            <div class="text-3xl mb-2">🎉</div>
                            <p class="font-medium text-gray-500">Tidak ada antrean pembayaran reservasi saat ini.</p>
                            <p class="text-xs text-gray-400 mt-1">Setiap reservasi pasien yang telah memiliki tindakan terapi akan otomatis muncul di sini untuk proses kasir.</p>
                        @endif
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
