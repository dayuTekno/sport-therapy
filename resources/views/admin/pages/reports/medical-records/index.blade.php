@extends('admin.layouts.app')

@section('title', 'Laporan Rekam Medis')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Laporan'],
    ['label' => 'Rekam Medis'],
]" />

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Laporan & History Rekam Medis</h1>
    <p class="text-sm text-gray-500">Rekapitulasi data pemeriksaan pasien berdasarkan rentang waktu.</p>
</div>

{{-- Filter Box --}}
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
    <form method="GET" action="{{ route('reports.medical_records.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Poliklinik</label>
            <select name="poly_id" class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
                <option value="">Semua Poliklinik</option>
                @foreach($polyclinics as $p)
                    <option value="{{ $p->id }}" {{ request('poly_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Filter
            </button>
            <a href="{{ route('reports.medical_records.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium text-sm flex items-center justify-center">
                Reset
            </a>
        </div>
        <div class="md:col-span-4 mt-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Pasien atau No. RM..." class="w-full rounded-lg border-gray-200 focus:ring-blue-500 focus:border-blue-500 text-sm">
        </div>
    </form>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase">
                    <th class="p-4 font-medium text-center">No</th>
                    <th class="p-4 font-medium">Tanggal / No. Antrian</th>
                    <th class="p-4 font-medium">Pasien</th>
                    <th class="p-4 font-medium">Poliklinik / Dokter</th>
                    <th class="p-4 font-medium">Kategori / Eselon</th>
                    <th class="p-4 font-medium">Status</th>
                    <th class="p-4 font-medium text-right">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($records as $index => $q)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-center text-sm text-gray-400">
                            {{ $records->firstItem() + $index }}
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $q->queue_code }}</div>
                            <div class="text-[10px] text-gray-400 uppercase tracking-wider">{{ $q->created_at->format('d M Y | H:i') }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-semibold text-gray-800">{{ $q->patient->full_name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">RM: {{ substr($q->patient->patient_code, 0, 8) }}</div>
                        </td>
                        <td class="p-4">
                            <div class="text-sm text-gray-700 font-medium">{{ $q->poly->name ?? '-' }}</div>
                            <div class="text-xs text-gray-500">{{ $q->doctor->full_name ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-xs font-medium border border-blue-100">
                                {{ $q->eselon->name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($q->status == 'completed')
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-tight">Selesai</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-tight">Selesai Periksa</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('reports.medical_records.show', $q->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-block" title="Lihat Rekam Medis">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                <p class="italic">Tidak ada data rekam medis ditemukan pada periode ini.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $records->links() }}
</div>

@endsection
