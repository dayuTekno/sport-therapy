@extends('admin.layouts.app')

@section('title', 'Katalog Jenjang Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Katalog Jenjang Terapi'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Katalog Jenjang Terapi</h1>
        <p class="text-xs text-gray-500 mt-1">Tahapan terapi berjenjang untuk pemulihan cedera fisik & olahraga</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('therapy-types.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">
            + Tambah Jenjang Terapi
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Urutan</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kode</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nama Tahapan Terapi</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Durasi</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Tarif</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($types as $type)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-bold text-xs inline-flex items-center justify-center">
                            {{ $type->stage_order }}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-mono font-medium text-gray-600">{{ $type->code }}</td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $type->name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $type->description ?: '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $type->duration_minutes }} Menit</td>
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $type->price ? 'Rp ' . number_format($type->price, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @if($type->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('therapy-types.edit', $type->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                        <form action="{{ route('therapy-types.destroy', $type->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jenjang terapi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">Belum ada katalog jenjang terapi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

@endsection
