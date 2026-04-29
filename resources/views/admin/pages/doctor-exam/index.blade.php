@extends('admin.layouts.app')

@section('title', 'Amnesa Dokter')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Amnesa Dokter', 'url' => ''],
]" />

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Daftar Antrian Pemeriksaan Dokter</h1>
    <p class="text-sm text-gray-500">Tanggal: {{ now()->format('d M Y') }}</p>
</div>

@include('admin.partials.alert')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase">
                    <th class="p-4 font-medium">No. Antrian</th>
                    <th class="p-4 font-medium">Pasien</th>
                    <th class="p-4 font-medium">Poliklinik</th>
                    <th class="p-4 font-medium">Dokter Tujuan</th>
                    <th class="p-4 font-medium">Status Perawat</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($queues as $q)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-bold text-gray-800">{{ $q->queue_code }}</td>
                        <td class="p-4">
                            <div class="font-medium text-gray-800">{{ $q->patient->full_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $q->patient->nik ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-sm text-gray-600">{{ $q->poly->name ?? '-' }}</td>
                        <td class="p-4 text-sm text-gray-600">{{ $q->doctor->full_name ?? '-' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">✓ Selesai Amnesa</span>
                        </td>
                        <td class="p-4">
                            @if($q->status == 'doctor_exam')
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Sedang Diperiksa</span>
                            @else
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-medium">Menunggu Dokter</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('doctor-exam.process', $q->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                Periksa Pasien
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-500">Belum ada pasien yang menunggu pemeriksaan dokter hari ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
