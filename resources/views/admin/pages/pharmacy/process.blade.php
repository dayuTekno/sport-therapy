@extends('admin.layouts.app')

@section('title', 'Proses Resep Obat')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Apotik', 'url' => route('pharmacy.index')],
    ['label' => 'Penyiapan Obat'],
]" />

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Penyiapan Obat</h1>
        <p class="text-sm text-gray-500">No. Antrian: <span class="font-bold text-blue-600">{{ $queue->queue_code ?? $queue->queue_number }}</span> | Pasien: {{ $queue->patient->full_name }}</p>
    </div>
    <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-xs text-green-600 font-bold uppercase">Status Pembayaran</p>
        <p class="text-sm font-bold text-green-800">LUNAS / BPJS</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Daftar Obat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-semibold text-gray-800">Daftar Resep Obat</h3>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded">DARI DOKTER: {{ $queue->doctor->full_name ?? '-' }}</span>
            </div>
            
            <div class="p-0">
                <table class="w-full text-sm text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-600">
                            <th class="p-4 font-medium">Nama Obat</th>
                            <th class="p-4 font-medium text-center">Jumlah</th>
                            <th class="p-4 font-medium">Aturan Pakai / Instruksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($record->medicines as $m)
                            <tr class="hover:bg-blue-50/50 transition-colors">
                                <td class="p-4">
                                    <p class="font-bold text-gray-800 text-base">{{ $m->medicine->medicine_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $m->medicine->medicine_international_name }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="px-3 py-1 bg-gray-100 rounded-full font-bold text-gray-700 text-lg">
                                        {{ $m->quantity }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg text-yellow-800 font-medium">
                                        {{ $m->instructions ?? '-' }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-white border-t flex justify-end gap-3">
                <a href="{{ route('pharmacy.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">Kembali</a>
                <form action="{{ route('pharmacy.store', $queue->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow-lg shadow-blue-100 flex items-center gap-2 text-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Serahkan Obat & Selesai
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Catatan/Instruksi --}}
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Instruksi Apoteker
            </h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex gap-2">
                    <span class="text-blue-500 font-bold">1.</span>
                    Pastikan nama pasien sesuai dengan resep.
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-500 font-bold">2.</span>
                    Racik obat sesuai dengan instruksi dokter.
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-500 font-bold">3.</span>
                    Tuliskan etiket obat dengan jelas sesuai aturan pakai.
                </li>
                <li class="flex gap-2">
                    <span class="text-blue-500 font-bold">4.</span>
                    Panggil pasien dan jelaskan aturan pakai saat penyerahan.
                </li>
            </ul>
        </div>
    </div>
</div>

@endsection
