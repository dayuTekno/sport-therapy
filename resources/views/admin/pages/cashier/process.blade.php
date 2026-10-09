@extends('admin.layouts.app')

@section('title', 'Proses Pembayaran Reservasi Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Kasir', 'url' => route('cashier.index')],
    ['label' => 'Proses Pembayaran Reservasi Terapi'],
]" />

<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>💳</span> Proses Pembayaran Kasir Pasien
        </h1>
        <p class="text-xs text-gray-500 mt-1">Selesaikan billing reservasi terapi pasien dan terbitkan kwitansi resmi.</p>
    </div>

    <a href="{{ route('cashier.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
        ← Kembali ke Daftar Kasir
    </a>
</div>

@include('admin.partials.alert')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" 
     x-data="{
        baseTotal: {{ $total }},
        discount: 0,
        paidInput: {{ $total }},
        get grandTotal() {
            return Math.max(0, this.baseTotal - (parseFloat(this.discount) || 0));
        },
        get changeAmount() {
            return Math.max(0, (parseFloat(this.paidInput) || 0) - this.grandTotal);
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
     }">

    {{-- KIRI: DATA PASIEN & DETAIL RESERVASI --}}
    <div class="lg:col-span-1 space-y-5">
        {{-- Card Data Pasien --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 pb-2 border-b border-gray-100 flex items-center gap-1.5">
                <span>👤</span> Data Pasien
            </h3>
            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Nama Pasien</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $reservation->patient?->full_name ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-gray-400 block font-medium">Nomor WhatsApp</span>
                        <span class="font-mono font-bold text-emerald-600">📱 {{ $reservation->patient?->phone_number ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block font-medium">Usia</span>
                        <span class="font-medium text-gray-800">{{ $reservation->patient?->age ?? '-' }} Tahun</span>
                    </div>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Alamat</span>
                    <span class="text-gray-700 leading-relaxed block">{{ $reservation->patient?->address ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Card Data Reservasi --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-3 pb-2 border-b border-blue-50 flex items-center gap-1.5">
                <span>📋</span> Data Reservasi Klinik
            </h3>
            <div class="space-y-2.5 text-xs">
                <div>
                    <span class="text-gray-400 block font-medium">Kode Reservasi</span>
                    <span class="font-mono font-bold text-blue-700 text-sm">{{ $reservation->reservation_code }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Terapis Penanggung Jawab</span>
                    <span class="font-semibold text-gray-800">{{ $reservation->therapist?->full_name ?? 'Terapis Klinik' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Jadwal yang Disepakati</span>
                    <span class="text-gray-800 font-medium">🕒 {{ $reservation->preferred_schedule }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block font-medium">Keluhan Utama</span>
                    <p class="font-medium text-gray-700 bg-red-50/70 p-2 rounded-lg border border-red-100 text-[11px] leading-relaxed mt-0.5">
                        {{ $reservation->chief_complaint }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: FORM BILLING & DETAIL TAGIHAN SESI --}}
    <div class="lg:col-span-2">
        <form action="{{ route('cashier.store', $reservation->id) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf

            <div class="p-5 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                        <span>🧾</span> Rincian Tagihan Sesi Terapi (Itemized Billing)
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Rincian seluruh tindakan sesi terapi yang dilakukan pada reservasi ini</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400 font-semibold uppercase block">Total Sesi</span>
                    <span class="font-bold text-blue-700 text-xs">{{ $reservation->sessions->count() }} Sesi Terapi</span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                {{-- Tabel Detail Tagihan Sesi Terapi --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-2xs">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-gray-50 text-gray-600 font-semibold uppercase text-[11px]">
                            <tr>
                                <th class="p-3.5">No</th>
                                <th class="p-3.5">Jenjang / Tahap Terapi</th>
                                <th class="p-3.5">Terapis</th>
                                <th class="p-3.5 text-center">Durasi</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5 text-right">Tarif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reservation->sessions as $idx => $sess)
                                @php
                                    $sessionPrice = $sess->total_price > 0 ? (float)$sess->total_price : (float)($sess->therapyType?->price ?? 0);
                                @endphp
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="p-3.5 font-bold text-gray-400 w-10">{{ $idx + 1 }}</td>
                                    <td class="p-3.5">
                                        <div class="font-bold text-gray-900 text-xs">
                                            Tahap {{ $sess->stage_number }} ({{ chr(64 + $sess->stage_number) }}): {{ $sess->stage_name }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">
                                            Sesi Ke-{{ $sess->daily_session_order ?? 1 }} • {{ $sess->scheduled_at ? $sess->scheduled_at->format('d M Y, H:i') : '-' }} WIB
                                        </div>
                                    </td>
                                    <td class="p-3.5 text-gray-700">
                                        {{ $sess->therapist?->full_name ?? ($reservation->therapist?->full_name ?? '-') }}
                                    </td>
                                    <td class="p-3.5 text-center font-medium text-gray-600">
                                        {{ $sess->therapyType?->duration_minutes ?? 45 }} Menit
                                    </td>
                                    <td class="p-3.5 text-center">
                                        @if($sess->status == 'completed')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                ✓ Selesai
                                            </span>
                                        @elseif($sess->status == 'in_progress')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                                Sedang Terapi
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                Terjadwal
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-right font-bold text-gray-900">
                                        Rp {{ number_format($sessionPrice, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-400">
                                        Tidak ada sesi terapi yang tercatat dalam reservasi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="5" class="p-3.5 text-right font-bold text-gray-700 uppercase tracking-wider text-xs">
                                    Subtotal Tagihan Sesi:
                                </td>
                                <td class="p-3.5 text-right font-black text-gray-900 text-sm">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Formulir Pembayaran Interaktif --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    {{-- Diskon / Potongan --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Diskon / Potongan Harga (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-gray-400 font-bold">Rp</span>
                            <input type="number" name="discount" x-model="discount" min="0" :max="baseTotal"
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-800"
                                   placeholder="0">
                        </div>
                        <span class="text-[10px] text-gray-400 mt-1 block">Kosongkan jika tidak ada diskon promo klinik.</span>
                    </div>

                    {{-- Total Akhir yang Harus Dibayar --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Total Tagihan Akhir
                        </label>
                        <div class="p-2.5 bg-blue-50/70 border border-blue-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-900">Total Bersih:</span>
                            <span class="text-lg font-black text-blue-700 font-mono" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                        </div>
                    </div>
                </div>

                {{-- Status Pembayaran Offline Kasir --}}
                <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="text-xl">🏢</span>
                        <div>
                            <span class="text-xs font-bold text-blue-900 block">Pembayaran Tunai di Kasir (Offline)</span>
                            <span class="text-[11px] text-blue-700">Pasien membayar langsung di loket kasir klinik</span>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" value="Kasir Offline">
                    <span class="px-2.5 py-1 bg-blue-600 text-white font-bold rounded-lg text-[10px] uppercase tracking-wide">
                        Kasir Klinik
                    </span>
                </div>

                {{-- Input Uang Diterima & Kembalian --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nominal Diterima Kasir <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-gray-400 font-bold">Rp</span>
                            <input type="number" name="paid_amount" x-model="paidInput" min="0" required
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-900 text-sm">
                        </div>
                        <div class="flex gap-1.5 mt-1.5">
                            <button type="button" @click="paidInput = grandTotal" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded">
                                Uang Pas
                            </button>
                            <button type="button" @click="paidInput = Math.ceil(grandTotal / 50000) * 50000" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded">
                                Bulat 50rb
                            </button>
                            <button type="button" @click="paidInput = Math.ceil(grandTotal / 100000) * 100000" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded">
                                Bulat 100rb
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Uang Kembalian Pasien
                        </label>
                        <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900">Kembalian:</span>
                            <span class="text-lg font-black text-emerald-700 font-mono" x-text="'Rp ' + formatRupiah(changeAmount)"></span>
                        </div>
                    </div>
                </div>

                {{-- Catatan Kasir --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Catatan Transaksi / Kasir (Opsional)
                    </label>
                    <textarea name="cashier_notes" rows="2" placeholder="Catatan tambahan kasir atau pesan pada kwitansi..."
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="p-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('cashier.index') }}" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition">
                    Batalkan & Kembali
                </a>

                <button type="submit"
                        :disabled="paidInput < grandTotal"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold rounded-xl shadow-md shadow-emerald-200 transition flex items-center gap-2 cursor-pointer">
                    <span>✓</span> Konfirmasi Pembayaran & Terbitkan Invoice
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
