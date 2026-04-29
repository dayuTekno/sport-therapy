@extends('admin.layouts.app')

@section('title', 'Proses Pembayaran')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Kasir', 'url' => route('cashier.index')],
    ['label' => 'Detail Pembayaran'],
]" />

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Detail Pembayaran</h1>
    <p class="text-sm text-gray-500">No. Antrian: <span class="font-bold text-blue-600">{{ $queue->queue_code ?? $queue->queue_number }}</span> | Pasien: {{ $queue->patient->full_name }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Pasien --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Billing</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500 text-xs">Kategori Pasien</p>
                    <p class="font-medium text-gray-800">{{ $queue->patient->eselon->name ?? 'Umum' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Layanan Poliklinik</p>
                    <p class="font-medium text-gray-800">{{ $queue->poly->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Dokter Pemeriksa</p>
                    <p class="font-medium text-gray-800">{{ $queue->doctor->full_name ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Rincian Biaya --}}
    <div class="lg:col-span-2" x-data="{ 
        baseTotal: 0,
        discount: 0,
        get grandTotal() {
            return Math.max(0, this.baseTotal - this.discount);
        }
    }" x-init="baseTotal = {{ $total }}">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-800">Rincian Item Pemeriksaan</h3>
            </div>
            
            <table class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600">
                        <th class="p-4 font-medium">Item / Deskripsi</th>
                        <th class="p-4 font-medium text-center">Qty</th>
                        <th class="p-4 font-medium text-right">Harga Satuan</th>
                        <th class="p-4 font-medium text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    {{-- Tindakan --}}
                    @foreach($record->treatments as $t)
                        @php $price = $t->procedure->price ?? 0; @endphp
                        <tr>
                            <td class="p-4">
                                <p class="font-medium text-gray-800">{{ $t->procedure->name }}</p>
                                <p class="text-xs text-gray-400 italic">Tindakan Medis</p>
                            </td>
                            <td class="p-4 text-center">1</td>
                            <td class="p-4 text-right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                            <td class="p-4 text-right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    {{-- Obat --}}
                    @foreach($record->medicines as $m)
                        @php 
                            $qty = ceil((float) $m->quantity);
                            $price = $m->medicine->price ?? 0;
                            $discount = $m->medicine->discount ?? 0;
                            $netPrice = $price - $discount;
                            $subtotal = $qty * $netPrice;
                        @endphp
                        <tr>
                            <td class="p-4">
                                <p class="font-medium text-gray-800">{{ $m->medicine->medicine_name }}</p>
                                <p class="text-xs text-gray-400 italic">Obat / Resep</p>
                                @if($discount > 0)
                                    <span class="text-[10px] bg-red-100 text-red-600 px-1 rounded">Disc: Rp {{ number_format($discount, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">{{ $qty }}</td>
                            <td class="p-4 text-right">
                                @if($discount > 0)
                                    <span class="text-xs text-gray-400 line-through">Rp {{ number_format($price, 0, ',', '.') }}</span><br>
                                @endif
                                Rp {{ number_format($netPrice, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach

                    @if($record->treatments->isEmpty() && $record->medicines->isEmpty())
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 italic">Tidak ada rincian biaya.</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="border-t-2 border-gray-100">
                    <tr class="bg-gray-50">
                        <td colspan="3" class="p-4 text-right font-medium text-gray-600">Subtotal Item</td>
                        <td class="p-4 text-right font-bold text-gray-800">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bg-white">
                        <td colspan="3" class="p-4 text-right font-medium text-gray-600 text-xs">Diskon / Potongan Harga</td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end">
                                <div class="relative w-36">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" x-model.number="discount" 
                                           class="w-full pl-9 pr-3 py-1 text-right border border-gray-200 rounded text-sm focus:ring-blue-500 focus:border-blue-500 font-bold"
                                           placeholder="0">
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-blue-600 text-white">
                        <td colspan="3" class="p-4 text-right font-bold">TOTAL AKHIR (GRAND TOTAL)</td>
                        <td class="p-4 text-right font-extrabold text-xl">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(grandTotal)"></span>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="p-6 bg-white border-t flex justify-end gap-3">
                <a href="{{ route('cashier.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">Batal</a>
                <form action="{{ route('cashier.store', $queue->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="discount" x-model="discount">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow-lg shadow-green-100 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        Konfirmasi & Bayar Lunas
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
