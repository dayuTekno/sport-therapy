@extends('admin.layouts.app')

@section('title', 'Master Data Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Pasien'],
]" />

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Master Data Pasien</h1>
        <p class="text-xs text-gray-500 mt-1">Kelola data pasien terdaftar untuk keperluan reservasi dan rekam terapi.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('reservations.create') }}"
           class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold rounded-xl border border-emerald-200 transition">
            📋 Buat Reservasi
        </a>
        <a href="{{ route('patients.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 shadow-sm transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pasien
        </a>
    </div>
</div>

{{-- Success/Error Messages --}}
@if (session('success'))
    <div class="mb-4 p-4 text-xs font-medium text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center gap-2">
        <span>✓</span> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 text-xs font-medium text-red-800 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
        <span>⚠️</span> {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
        <form action="{{ route('patients.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari Nama / No. HP / Pekerjaan / NIK..."
                   class="w-full px-3.5 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-xs">
            <button type="submit" class="px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl hover:bg-gray-50 text-xs font-semibold shadow-sm">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('patients.index') }}" class="px-3 py-2 text-xs text-gray-500 hover:text-gray-700 flex items-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
            <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider">
            <tr>
                <th class="px-5 py-3.5">Kode / RM</th>
                <th class="px-5 py-3.5">Nama Pasien</th>
                <th class="px-5 py-3.5">No. HP / WA</th>
                <th class="px-5 py-3.5">Usia</th>
                <th class="px-5 py-3.5">Gender</th>
                <th class="px-5 py-3.5">Pekerjaan</th>
                <th class="px-5 py-3.5">Alamat</th>
                <th class="px-5 py-3.5 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
            @forelse ($patients as $patient)
                <tr class="hover:bg-gray-50/60 transition">
                    <td class="px-5 py-3.5 font-mono text-gray-600">
                        {{ substr($patient->patient_code, 0, 8) }}...
                    </td>
                    <td class="px-5 py-3.5 font-semibold text-gray-900">
                        {{ $patient->full_name }}
                        @if($patient->nik)
                            <div class="text-[10px] text-gray-400 font-mono font-normal">NIK: {{ $patient->nik }}</div>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-gray-700 font-mono">
                        <span class="inline-flex items-center gap-1">
                            <span>📱</span>
                            <strong>{{ $patient->phone_number ?? '-' }}</strong>
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-700">
                        {{ $patient->age ? $patient->age . ' Thn' : '-' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $patient->gender === 'male' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                            {{ $patient->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600">
                        {{ $patient->occupation ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-gray-500 max-w-[200px] truncate" title="{{ $patient->address }}">
                        {{ $patient->address ?? '-' }}
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="{{ route('reservations.create', ['phone' => $patient->phone_number]) }}" 
                               title="Buat Reservasi untuk Pasien Ini"
                               class="p-1.5 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </a>
                            <a href="{{ route('patients.edit', $patient->id) }}" 
                               title="Edit Data Pasien"
                               class="p-1.5 text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Hapus Pasien"
                                        class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-500">
                        Data pasien belum ditemukan. Silakan tambah pasien baru.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($patients->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $patients->links() }}
        </div>
    @endif
</div>

@endsection
