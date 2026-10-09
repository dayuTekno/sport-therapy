@extends('admin.layouts.app')

@section('title', 'Kwitansi Pembayaran Terapi - ' . ($reservation->invoice_code ?? 'INV-' . $reservation->id))

@section('content')

<div class="max-w-3xl mx-auto print:max-w-full">
    {{-- Action bar (hidden on print) --}}
    <div class="mb-6 flex items-center justify-between print:hidden">
        <a href="{{ route('cashier.index', ['tab' => 'paid']) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition flex items-center gap-1.5">
            ← Kembali ke Kasir
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition flex items-center gap-1.5 cursor-pointer">
                <span>🖨️</span> Cetak Kwitansi
            </button>
        </div>
    </div>

    @include('admin.partials.alert')

    {{-- KARTU KWITANSI RESMI --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 sm:p-10 print:border-none print:shadow-none print:p-0">
        {{-- Header Klinik --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b-2 border-gray-100 gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="text-2xl font-black text-blue-900 tracking-tight">SPORT THERAPY CLINIC</span>
                </div>
                <p class="text-xs text-gray-500 mt-1">Sport Physiotherapy & Injury Rehabilitation Center</p>
                <p class="text-[11px] text-gray-400">Jl. Pelayanan Kesehatan No. 10 | Telp/WA: 0851-8303-6722</p>
            </div>

            <div class="text-left sm:text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black uppercase rounded-full border border-emerald-300 tracking-wider">
                    ✓ LUNAS / PAID
                </span>
                <div class="font-mono text-sm font-bold text-gray-800 mt-2">
                    {{ $reservation->invoice_code ?? ('INV-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT)) }}
                </div>
                <div class="text-[11px] text-gray-500">
                    {{ $reservation->paid_at ? $reservation->paid_at->format('d M Y, H:i') : now()->format('d M Y, H:i') }} WIB
                </div>
            </div>
        </div>

        {{-- Info Pasien & Reservasi --}}
        <div class="grid grid-cols-2 gap-6 py-6 border-b border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 uppercase font-semibold text-[10px] block mb-1">Ditagihkan Kepada:</span>
                <div class="font-bold text-gray-900 text-sm">{{ $reservation->patient?->full_name ?? '-' }}</div>
                <div class="text-gray-600 mt-0.5">No. HP: {{ $reservation->patient?->phone_number ?? '-' }}</div>
                <div class="text-gray-500 mt-0.5">Usia: {{ $reservation->patient?->age ?? '-' }} Tahun</div>
                @if($reservation->patient?->address)
                    <div class="text-gray-500 mt-0.5 line-clamp-1">{{ $reservation->patient->address }}</div>
                @endif
            </div>

            <div class="text-right">
                <span class="text-gray-400 uppercase font-semibold text-[10px] block mb-1">Informasi Reservasi:</span>
                <div class="font-mono font-bold text-blue-700 text-sm">{{ $reservation->reservation_code }}</div>
                <div class="text-gray-600 mt-0.5">Jadwal: {{ $reservation->preferred_schedule }}</div>
                <div class="text-gray-500 mt-0.5">Terapis: {{ $reservation->therapist?->full_name ?? 'Terapis Klinik' }}</div>
            </div>
        </div>

        {{-- Rincian Item Pembayaran (Detail Tagihan Sesi Terapi) --}}
        <div class="py-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 mb-3">
                Detail Tagihan Sesi Terapi (Itemized Billing)
            </h4>
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-gray-500 uppercase text-[10px]">
                        <th class="py-2.5 w-8">No</th>
                        <th class="py-2.5">Deskripsi Tindakan & Jenjang Terapi</th>
                        <th class="py-2.5 text-center">Durasi</th>
                        <th class="py-2.5 text-right">Tarif</th>
                        <th class="py-2.5 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reservation->sessions as $idx => $sess)
                        @php
                            $sessPrice = $sess->total_price > 0 ? (float)$sess->total_price : (float)($sess->therapyType?->price ?? 0);
                        @endphp
                        <tr>
                            <td class="py-3 font-semibold text-gray-400">{{ $idx + 1 }}</td>
                            <td class="py-3">
                                <div class="font-bold text-gray-900 text-xs">
                                    Tahap {{ $sess->stage_number }} ({{ chr(64 + $sess->stage_number) }}): {{ $sess->stage_name }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    Terapis: {{ $sess->therapist?->full_name ?? ($reservation->therapist?->full_name ?? 'Terapis Klinik') }}
                                    • Sesi Ke-{{ $sess->daily_session_order ?? 1 }}
                                </div>
                            </td>
                            <td class="py-3 text-center text-gray-700">
                                {{ $sess->therapyType?->duration_minutes ?? 45 }} Menit
                            </td>
                            <td class="py-3 text-right text-gray-800">
                                Rp {{ number_format($sessPrice, 0, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-bold text-gray-900">
                                Rp {{ number_format($sessPrice, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-400">
                                Sesi Terapi Konsultasi Umum - Rp {{ number_format($reservation->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Total & Ringkasan Pembayaran --}}
        <div class="border-t border-gray-200 pt-4 pb-6 flex flex-col sm:flex-row justify-between gap-6">
            <div class="text-xs text-gray-500 space-y-1">
                <div><strong>Pembayaran:</strong> <span class="font-bold text-gray-800">Kasir Offline (Tunai)</span></div>
                @if($reservation->cashier_notes)
                    <div><strong>Catatan Kasir:</strong> {{ $reservation->cashier_notes }}</div>
                @endif
                <div class="text-[11px] text-gray-400 mt-3 italic">
                    * Terima kasih atas kepercayaan Anda berkonsultasi & terapi di Sport Therapy Clinic.
                </div>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-xs">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal Tagihan:</span>
                    <span>Rp {{ number_format($reservation->total_price, 0, ',', '.') }}</span>
                </div>
                @if($reservation->discount > 0)
                    <div class="flex justify-between text-rose-600">
                        <span>Diskon:</span>
                        <span>- Rp {{ number_format($reservation->discount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between text-sm font-black text-gray-900 pt-2 border-t border-gray-200">
                    <span>Total Pembayaran:</span>
                    <span class="text-blue-700">Rp {{ number_format(max(0, $reservation->total_price - $reservation->discount), 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600 pt-1">
                    <span>Nominal Diterima:</span>
                    <span class="font-bold">Rp {{ number_format($reservation->paid_amount, 0, ',', '.') }}</span>
                </div>
                @if($reservation->paid_amount > max(0, $reservation->total_price - $reservation->discount))
                    <div class="flex justify-between text-emerald-700 font-bold">
                        <span>Kembalian:</span>
                        <span>Rp {{ number_format($reservation->paid_amount - max(0, $reservation->total_price - $reservation->discount), 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tanda Tangan Kasir --}}
        <div class="border-t border-gray-100 pt-6 flex justify-between items-end text-xs">
            <div class="text-gray-400 text-[10px]">
                Kwitansi ini sah dan dicetak otomatis oleh sistem komputer Sport Therapy RSTBDI.
            </div>

            <div class="text-center w-48">
                <div class="text-gray-500 text-[11px] mb-12">Petugas Kasir,</div>
                <div class="font-bold text-gray-900 border-b border-gray-400 pb-1">
                    {{ $reservation->cashier?->name ?? (auth()->user()->name ?? 'Petugas Kasir') }}
                </div>
                <div class="text-[10px] text-gray-400 mt-0.5">Sport Therapy Clinic</div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body {
        background-color: white !important;
        font-size: 12pt;
    }
    header, aside, footer {
        display: none !important;
    }
}
</style>

@endsection
