@extends('admin.layouts.app')

@section('title', 'Obat')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Obat'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Obat</h1>

    <!-- Right Actions -->
    <div class="flex items-center gap-2" x-data="{ open: false }">

        <!-- Import Button -->
        <button 
            @click="open = true"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Import Excel
        </button>

        <!-- Tambah -->
        <a href="{{ route('medicines.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Tambah Obat
        </a>

        <!-- Modal -->
        <div 
            x-show="open"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            x-transition
        >
            <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">

                <!-- Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Upload Excel</h2>
                    <button @click="open = false" class="text-gray-500 hover:text-black">✕</button>
                </div>

                <!-- Form -->
                <form action="{{ route('medicines.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input 
                        type="file" 
                        name="file"
                        accept=".xlsx,.xls,.csv"
                        class="w-full border rounded-lg p-2 mb-4"
                        required
                    >

                    <div class="flex justify-end gap-2">
                        <button 
                            type="button"
                            @click="open = false"
                            class="px-4 py-2 bg-gray-300 rounded-lg">
                            Batal
                        </button>

                        <button 
                            type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Upload
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<form method="GET" action="{{ route('medicines.index') }}" class="mb-4 flex items-center gap-2">
    
    <input type="text" 
           name="search"
           value="{{ request('search') }}"
           placeholder="Cari nama obat..."
           class="w-full md:w-64 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">

    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        Cari
    </button>

    @if(request('search'))
        <a href="{{ route('medicines.index') }}"
           class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
            Reset
        </a>
    @endif

</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Internasional
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Harga
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Diskon
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($medicines as $med)
            <tr>
                <td class="px-6 py-4 font-medium">
                    {{ $med->medicine_name }}
                </td>
                <td class="px-6 py-4 font-medium">
                    {{ $med->medicine_international_name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $med->price ?: '-' }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $med->discount_from_source ?: '-' }}
                </td>

                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        {{-- EDIT --}}
                        <a href="{{ route('medicines.edit', $med->id) }}" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('medicines.destroy', $med->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
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
                    <td colspan="4"
                        class="px-6 py-6 text-center text-gray-500">
                        Data belum tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($medicines->hasPages())
    <div class="mt-6">
        {{ $medicines->links() }}
    </div>
@endif


<script src="//unpkg.com/alpinejs" defer></script>

@endsection
