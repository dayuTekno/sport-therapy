@extends('admin.layouts.app')

@section('title', 'Master Data Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Pasien'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Master Data Pasien</h1>
    <a href="{{ route('patients.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Pasien
    </a>
</div>

{{-- Success/Error Messages --}}
@if (session('success'))
    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200">
        <form action="{{ route('patients.index') }}" method="GET" class="flex gap-2 w-full max-w-md">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari NIK / Nama / No RM..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm">
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-200">
                Cari
            </button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No RM</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pasien</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($patients as $patient)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ substr($patient->patient_code, 0, 8) }}...
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $patient->nik }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $patient->full_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $patient->phone_number ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('patients.edit', $patient->id) }}" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
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
                    <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                        Data pasien belum tersedia.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if ($patients->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $patients->links() }}
        </div>
    @endif
</div>

@endsection
