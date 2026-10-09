@extends('admin.layouts.app')

@section('title', 'Terapis')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Terapis'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Master Data Terapis</h1>
        <p class="text-xs text-gray-500 mt-1">Daftar tenaga fisioterapi dan sport therapist klinik</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('therapists.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm transition">
            + Tambah Terapis
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kode</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nama Lengkap Terapis</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Keahlian / Spesialisasi</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Nomor HP/WA</th>
                <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($therapists as $t)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-medium text-blue-600">{{ $t->therapist_code }}</td>
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $t->full_name }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $t->specialization ?: '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-mono">{{ $t->phone ?: '-' }}</td>
                    <td class="px-6 py-4">
                        @if($t->is_active)
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">Aktif</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('therapists.edit', $t->id) }}" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</a>
                        <form action="{{ route('therapists.destroy', $t->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus Terapis ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">Belum ada data Terapis.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $therapists->links() }}
    </div>
</div>

@endsection
