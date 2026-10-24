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
        otherFee: '{{ $reservation->other_fee > 0 ? (float)$reservation->other_fee : '' }}',
        discountType: 'rupiah',
        discountValue: '',
        paidInput: {{ $total + (float)($reservation->other_fee ?? 0) }},
        paymentMethod: 'Cash',
        paymentMethodOther: '',
        get otherFeeAmount() {
            let val = parseFloat(this.otherFee) || 0;
            return Math.max(0, val);
        },
        get discountAmount() {
            let val = parseFloat(this.discountValue) || 0;
            if (val <= 0) return 0;
            if (this.discountType === 'percent') {
                let pct = Math.min(100, Math.max(0, val));
                return Math.round((this.baseTotal * pct) / 100);
            }
            return Math.min(this.baseTotal + this.otherFeeAmount, Math.max(0, val));
        },
        get grandTotal() {
            return Math.max(0, (this.baseTotal + this.otherFeeAmount) - this.discountAmount);
        },
        get changeAmount() {
            return Math.max(0, (parseFloat(this.paidInput) || 0) - this.grandTotal);
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },
        formatInput(num) {
            return (num === '' || num === null) ? '' : this.formatRupiah(num);
        },
        // Format input nominal dengan separator ribuan (titik) sambil menjaga posisi kursor
        maskRupiah(e) {
            const el = e.target;
            const digitsBeforeCaret = el.value.slice(0, el.selectionStart).replace(/\D/g, '').length;
            const digits = el.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
            const formatted = digits === '' ? '' : this.formatRupiah(parseInt(digits, 10));
            el.value = formatted;
            let pos = 0, seen = 0;
            while (pos < formatted.length && seen < digitsBeforeCaret) {
                if (/\d/.test(formatted[pos])) seen++;
                pos++;
            }
            el.setSelectionRange(pos, pos);
            return digits === '' ? '' : parseInt(digits, 10);
        },
        maskPercent(e) {
            let val = e.target.value.replace(',', '.').replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
            e.target.value = val;
            return val;
        },
        onDiscountInput(e) {
            this.discountValue = this.discountType === 'percent' ? this.maskPercent(e) : this.maskRupiah(e);
            this.onDiscountChange();
        },
        onPaymentMethodChange() {
            if (this.paymentMethod !== 'Cash') {
                this.paidInput = this.grandTotal;
            }
        },
        onRecalculate() {
            this.$nextTick(() => {
                if (this.paymentMethod !== 'Cash') {
                    this.paidInput = this.grandTotal;
                }
            });
        },
        onDiscountChange() {
            this.onRecalculate();
        },
        onOtherFeeChange() {
            this.onRecalculate();
        },
        setDiscountType(type) {
            this.discountType = type;
            this.discountValue = '';
            this.onRecalculate();
        },
        setPercentPreset(pct) {
            this.discountType = 'percent';
            this.discountValue = pct;
            this.onRecalculate();
        },
        setOtherFeePreset(val) {
            this.otherFee = val > 0 ? val : '';
            this.onRecalculate();
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

                {{-- Section Diskon & Total Tagihan --}}
                <div class="bg-gray-50/80 border border-gray-200/80 rounded-2xl p-4 space-y-4">
                    {{-- Input Biaya Lain-lain (Di Luar Penanganan) --}}
                    <div class="pb-4 border-b border-gray-200/80">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                                <span>🏷️</span> Biaya Lain-lain (Di Luar Penanganan)
                            </label>
                            <span class="text-[10px] font-semibold text-gray-500 bg-gray-200/70 px-2 py-0.5 rounded-md">
                                Opsional
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mb-2.5">
                            Biaya tambahan di luar tindakan penanganan terapi (contoh: Kinesio tape, sewa alat khusus, obat/salep luar, perban, administrasi, dll.)
                        </p>

                        {{-- Input Nominal Rupiah Biaya Lain-lain & Keterangan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                                    Nominal Rupiah (Rp)
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-xs text-gray-400 font-bold">Rp</span>
                                    <input type="text"
                                           inputmode="numeric"
                                           autocomplete="off"
                                           :value="formatInput(otherFee)"
                                           @input="otherFee = maskRupiah($event); onOtherFeeChange()"
                                           placeholder="0"
                                           class="w-full pl-10 pr-4 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-800 bg-white shadow-2xs">
                                    <input type="hidden" name="other_fee" :value="otherFee">
                                </div>
                            </div>

                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">
                                    Keterangan / Rincian Biaya Lain (Opsional)
                                </label>
                                <input type="text"
                                       name="other_fee_description"
                                       value="{{ old('other_fee_description', $reservation->other_fee_description) }}"
                                       placeholder="Misal: Kinesio Tape 2 roll, Kasa & Plester..."
                                       class="w-full px-3 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-medium text-gray-800 bg-white shadow-2xs">
                            </div>
                        </div>

                        {{-- Tombol Cepat Nominal Biaya Lain-lain --}}
                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                            <span class="text-[10px] text-gray-400 font-semibold">Cepat:</span>
                            <button type="button" @click="setOtherFeePreset(0)" class="px-2 py-0.5 bg-white hover:bg-gray-100 text-gray-600 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">Reset (0)</button>
                            <button type="button" @click="setOtherFeePreset(15000)" class="px-2 py-0.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">+15rb</button>
                            <button type="button" @click="setOtherFeePreset(25000)" class="px-2 py-0.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">+25rb</button>
                            <button type="button" @click="setOtherFeePreset(50000)" class="px-2 py-0.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">+50rb</button>
                            <button type="button" @click="setOtherFeePreset(75000)" class="px-2 py-0.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">+75rb</button>
                            <button type="button" @click="setOtherFeePreset(100000)" class="px-2 py-0.5 bg-white hover:bg-emerald-50 text-emerald-700 hover:border-emerald-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">+100rb</button>

                            <div x-show="otherFeeAmount > 0" class="text-[11px] font-bold text-emerald-700 ml-auto">
                                Tambahan: +Rp <span x-text="formatRupiah(otherFeeAmount)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Input Diskon / Potongan Harga --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Diskon / Potongan Promo
                            </label>

                            {{-- Pilihan Jenis Diskon: Rupiah (Rp) vs Persentase (%) --}}
                            <div class="inline-flex p-0.5 bg-gray-200/80 rounded-lg text-xs font-semibold">
                                <button type="button" @click="setDiscountType('rupiah')"
                                        :class="discountType === 'rupiah' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer font-bold">
                                    Rp (Nominal)
                                </button>
                                <button type="button" @click="setDiscountType('percent')"
                                        :class="discountType === 'percent' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                        class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer font-bold">
                                    % (Persen)
                                </button>
                            </div>
                        </div>

                        {{-- Input Nilai Diskon --}}
                        <div class="relative">
                            {{-- Prefix Rp jika rupiah --}}
                            <span x-show="discountType === 'rupiah'" class="absolute left-3.5 top-2.5 text-xs text-gray-400 font-bold">Rp</span>

                            <input type="text"
                                   :inputmode="discountType === 'percent' ? 'decimal' : 'numeric'"
                                   autocomplete="off"
                                   :value="discountType === 'percent' ? discountValue : formatInput(discountValue)"
                                   @input="onDiscountInput($event)"
                                   :placeholder="discountType === 'percent' ? 'Masukkan persentase (misal: 10)' : '0'"
                                   :class="discountType === 'rupiah' ? 'pl-10 pr-4' : 'pl-4 pr-10'"
                                   class="w-full py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-800 bg-white shadow-2xs">

                            {{-- Suffix % jika percent --}}
                            <span x-show="discountType === 'percent'" class="absolute right-3.5 top-2.5 text-xs text-blue-600 font-bold">%</span>
                        </div>

                        {{-- Hidden inputs nilai diskon untuk request form submission --}}
                        <input type="hidden" name="discount" :value="discountAmount">
                        <input type="hidden" name="discount_type" :value="discountType">
                        <input type="hidden" name="discount_value" :value="discountValue">

                        {{-- Keterangan / Feedback Nilai Diskon & Tombol Preset Cepat --}}
                        <div class="flex flex-wrap items-center justify-between gap-2 mt-2">
                            {{-- Preset Tombol Cepat Persentase --}}
                            <div x-show="discountType === 'percent'" class="flex items-center gap-1.5">
                                <span class="text-[10px] text-gray-400 font-semibold">Cepat:</span>
                                <template x-for="pct in [5, 10, 15, 20, 50]">
                                    <button type="button" @click="setPercentPreset(pct)"
                                            class="px-2 py-0.5 bg-white hover:bg-blue-50 text-blue-700 hover:border-blue-300 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer"
                                            x-text="pct + '%'"></button>
                                </template>
                            </div>

                            {{-- Preset Tombol Cepat Rupiah --}}
                            <div x-show="discountType === 'rupiah'" class="flex items-center gap-1.5">
                                <span class="text-[10px] text-gray-400 font-semibold">Cepat:</span>
                                <button type="button" @click="discountValue = 10000; onDiscountChange()" class="px-2 py-0.5 bg-white hover:bg-blue-50 text-blue-700 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">10rb</button>
                                <button type="button" @click="discountValue = 25000; onDiscountChange()" class="px-2 py-0.5 bg-white hover:bg-blue-50 text-blue-700 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">25rb</button>
                                <button type="button" @click="discountValue = 50000; onDiscountChange()" class="px-2 py-0.5 bg-white hover:bg-blue-50 text-blue-700 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">50rb</button>
                                <button type="button" @click="discountValue = 100000; onDiscountChange()" class="px-2 py-0.5 bg-white hover:bg-blue-50 text-blue-700 border border-gray-200 rounded text-[10px] font-bold transition cursor-pointer">100rb</button>
                            </div>

                            {{-- Preview Potongan Rupiah saat Diskon Persen / Nominal --}}
                            <div x-show="discountAmount > 0" class="text-[11px] font-bold text-rose-600 ml-auto">
                                Potongan: -Rp <span x-text="formatRupiah(discountAmount)"></span>
                                <span x-show="discountType === 'percent'" class="text-gray-400 font-normal" x-text="'(' + discountValue + '%)'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Total Tagihan Akhir (Setelah Biaya Lain-lain & Diskon) --}}
                    <div class="pt-3 border-t border-gray-200/80 space-y-1.5">
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Subtotal Sesi Penanganan:</span>
                            <span class="font-bold text-gray-800" x-text="'Rp ' + formatRupiah(baseTotal)"></span>
                        </div>
                        <div x-show="otherFeeAmount > 0" class="flex items-center justify-between text-xs text-emerald-700">
                            <span>Biaya Lain-lain:</span>
                            <span class="font-bold font-mono" x-text="'+ Rp ' + formatRupiah(otherFeeAmount)"></span>
                        </div>
                        <div x-show="discountAmount > 0" class="flex items-center justify-between text-xs text-rose-600">
                            <span x-text="'Diskon ' + (discountType === 'percent' ? '(' + discountValue + '%)' : '') + ':'"></span>
                            <span class="font-bold font-mono" x-text="'- Rp ' + formatRupiah(discountAmount)"></span>
                        </div>
                        <div class="p-3 bg-blue-50/80 border border-blue-200 rounded-xl flex items-center justify-between mt-2">
                            <div>
                                <span class="text-xs font-bold text-blue-900 block">Total Tagihan Akhir</span>
                                <span class="text-[10px] text-blue-600">Nominal bersih yang wajib dibayar pasien</span>
                            </div>
                            <span class="text-xl font-black text-blue-700 font-mono" x-text="'Rp ' + formatRupiah(grandTotal)"></span>
                        </div>
                    </div>
                </div>

                {{-- Status & Pilihan Metode Pembayaran Kasir --}}
                <div class="p-4 bg-blue-50/70 border border-blue-200/80 rounded-2xl space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <span class="text-2xl">🏢</span>
                            <div>
                                <span class="text-xs font-bold text-blue-900 block">Pembayaran Tunai / Loket Kasir (Offline)</span>
                                <span class="text-[11px] text-blue-700">Pasien membayar langsung di loket kasir klinik</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-blue-600 text-white font-bold rounded-lg text-[10px] uppercase tracking-wide shrink-0">
                            Kasir Klinik
                        </span>
                    </div>

                    {{-- Select Option Metode Pembayaran --}}
                    <div class="pt-3 border-t border-blue-200/70">
                        <label for="payment_method" class="block text-xs font-bold text-blue-950 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Metode / Kanal Pembayaran <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] font-normal text-blue-700 normal-case">Pilih opsi pembayaran pasien</span>
                        </label>
                        <div class="relative">
                            <select name="payment_method" id="payment_method" x-model="paymentMethod" @change="onPaymentMethodChange()" required
                                    class="w-full px-3.5 py-2.5 text-xs font-bold border border-blue-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white text-gray-800 shadow-2xs">
                                <option value="Cash">💵 Cash / Tunai</option>
                                <option value="QRIS">📱 QRIS (Barcode / E-Wallet)</option>
                                <option value="Transfer Bank">🏦 Transfer Bank</option>
                                <option value="Debit">💳 Kartu Debit / EDC</option>
                                <option value="Lainnya">✨ Lainnya (Asuransi / Voucher / dll)</option>
                            </select>
                        </div>
                        <p class="text-[10px] text-blue-600 mt-1">
                            * Keterangan metode pembayaran ini tersimpan untuk memudahkan pelaporan & rekapitulasi keuangan kasir.
                        </p>
                    </div>

                    {{-- Form Keterangan Tambahan Jika Memilih Lainnya --}}
                    <div x-show="paymentMethod === 'Lainnya'" style="display: none;" class="pt-1">
                        <label class="block text-xs font-semibold text-gray-700 mb-1">
                            Keterangan Metode Pembayaran Lainnya <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="payment_method_detail" x-model="paymentMethodOther"
                               :required="paymentMethod === 'Lainnya'"
                               placeholder="Contoh: Asuransi Mandiri Inhealth, Klaim BPJS Ketenagakerjaan, Voucher..."
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white font-medium text-gray-800">
                    </div>
                </div>

                {{-- Input Uang Diterima & Kembalian --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Nominal Diterima Kasir <span class="text-rose-500">*</span></span>
                            <span class="text-[10px] font-normal text-emerald-600 normal-case" x-show="paymentMethod !== 'Cash'">✓ Otomatis Pas (Non-Tunai)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-gray-400 font-bold">Rp</span>
                            <input type="text"
                                   inputmode="numeric"
                                   autocomplete="off"
                                   :value="formatInput(paidInput)"
                                   @input="paidInput = maskRupiah($event)"
                                   required
                                   class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-900 text-sm">
                            <input type="hidden" name="paid_amount" :value="paidInput">
                        </div>
                        <div class="flex gap-1.5 mt-1.5" x-show="paymentMethod === 'Cash'">
                            <button type="button" @click="paidInput = grandTotal" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded cursor-pointer">
                                Uang Pas
                            </button>
                            <button type="button" @click="paidInput = Math.ceil(grandTotal / 50000) * 50000" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded cursor-pointer">
                                Bulat 50rb
                            </button>
                            <button type="button" @click="paidInput = Math.ceil(grandTotal / 100000) * 100000" class="px-2 py-0.5 bg-gray-100 hover:bg-gray-200 text-[10px] font-semibold text-gray-600 rounded cursor-pointer">
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
