@extends('admin.layouts.app')

@section('title', 'Stok Peralatan Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Inventaris'],
    ['label' => 'Stok Peralatan Terapi'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Stok Peralatan Terapi</h1>
        <p class="text-xs text-gray-500 mt-1">Inventaris dan manajemen stok alat terapi fisik klinik</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('therapy-equipments.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">
            + Tambah Alat Terapi
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center justify-between">
    <form method="GET" action="{{ route('therapy-equipments.index') }}" class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode alat..."
               class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-64">
        
        <select name="category" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg hover:bg-gray-700">Filter</button>
        @if(request('search') || request('category'))
            <a href="{{ route('therapy-equipments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kode Alat</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nama Peralatan</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kategori</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Stok</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kondisi</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($equipments as $item)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-medium text-blue-600">{{ $item->equipment_code }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $item->name }}
                        @if($item->description)
                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($item->description, 50) }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $item->category ?: '-' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full font-bold {{ $item->stock <= 5 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $item->stock }} {{ $item->uom }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($item->condition == 'baik')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-50 text-emerald-600">Baik</span>
                        @elseif($item->condition == 'perlu_perbaikan')
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-50 text-amber-600">Perlu Servis</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-rose-50 text-rose-600">Rusak</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($item->is_available)
                            <span class="text-xs text-emerald-600 font-medium">● Tersedia</span>
                        @else
                            <span class="text-xs text-gray-400 font-medium">○ Tidak Tersedia</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('therapy-equipments.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                        <form action="{{ route('therapy-equipments.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus alat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada data peralatan terapi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $equipments->links() }}
    </div>
</div>

@endsection
