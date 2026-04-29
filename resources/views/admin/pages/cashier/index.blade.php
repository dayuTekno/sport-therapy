@extends('admin.layouts.app')

@section('title', 'Kasir - Daftar Pembayaran')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Kasir'],
]" />

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kasir</h1>
        <p class="text-sm text-gray-500">Daftar pasien menunggu pembayaran (Umum/Cash)</p>
    </div>
</div>

@include('admin.partials.alert')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No. Antri</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pasien</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Poli / Dokter</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($queues as $q)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $q->updated_at->format('H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-0.5 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                            {{ $q->queue_code ?? $q->queue_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $q->patient->full_name }}</div>
                        <div class="text-xs text-gray-500">RM: {{ substr($q->patient->patient_code, 0, 8) }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $q->poly->name ?? '-' }}<br>
                        <span class="text-xs italic">{{ $q->doctor->full_name ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">
                            {{ $q->patient->eselon->name ?? ($q->eselon->name ?? 'Umum') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('cashier.process', $q->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Proses Bayar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                        Tidak ada antrian pembayaran saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
