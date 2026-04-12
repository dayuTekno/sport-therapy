@extends('admin.layouts.app')

@section('title', 'ICD')

@section('content')

@php

$field =
    'w-full rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/10 dark:border-gray-600 dark:bg-gray-900/50 dark:text-white dark:placeholder:text-gray-500 dark:focus:border-blue-400';
$fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
$fileField =
    'w-full rounded-xl border border-dashed border-gray-300 bg-gray-50/80 px-4 py-3 text-sm text-gray-700 shadow-sm transition file:mr-3 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-600 dark:bg-gray-900/30 dark:text-gray-200 dark:file:bg-blue-500 dark:hover:border-blue-500';
$label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
$hint = 'mt-1.5 text-xs text-gray-500 dark:text-gray-400';
@endphp


<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'ICD'],
]" />

<div x-data="{ tab: '{{ request('version', '9') }}', open: false }">

    {{-- ================= TAB ================= --}}
    <div class="mb-4 border-b flex gap-4">
        <button @click="tab='9'"
            :class="tab === '9' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="px-4 py-2">
            ICD 9
        </button>

        <button @click="tab='10'"
            :class="tab === '10' ? 'border-b-2 border-blue-600 text-blue-600' : ''"
            class="px-4 py-2">
            ICD 10
        </button>
    </div>

    {{-- ================= HEADER ================= --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold text-gray-800">
            ICD <span x-text="tab"></span>
        </h1>

        <div class="flex gap-2">

            {{-- IMPORT --}}
            <button 
                @click="open = true"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Import Excel
            </button>

            {{-- TAMBAH --}}
            <a :href="`{{ route('icds.create') }}?version=${tab}`"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Tambah ICD
            </a>

        </div>
    </div>

    {{-- ================= SEARCH ================= --}}
    <form method="GET" action="{{ route('icds.index') }}" class="mb-4 flex items-center gap-2">
        
        <input type="hidden" name="version" :value="tab">

        <input type="text" 
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama ICD..."
            class="w-full md:w-64 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">

        <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Cari
        </button>

        @if(request('search'))
            <a href="{{ route('icds.index') }}?version={{ request('version', 9) }}"
                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                Reset
            </a>
        @endif
    </form>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Kode ICD
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Nama
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Version
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                    Aksi
                </th>
            </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($icds as $icd)
                <tr>
                    <td class="px-6 py-4 font-medium">
                        {{ $icd->icd_code }}
                    </td>
                    <td class="px-6 py-4 font-medium">
                        {{ $icd->name }}
                    </td>
                    <td class="px-6 py-4 font-medium">
                        {{ $icd->version }}
                    </td>

                    <td class="px-6 py-4 text-right space-x-2">

                        {{-- EDIT --}}
                        <a href="{{ route('icds.edit', $icd->id) }}"
                        class="inline-flex items-center justify-center
                                w-8 h-8 rounded-lg
                                text-blue-600 hover:bg-blue-50"
                        title="Edit">
                            ✏️
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('icds.destroy', $icd->id) }}"
                            method="POST"
                            class="inline"
                            onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="inline-flex items-center justify-center
                                        w-8 h-8 rounded-lg
                                        text-red-600 hover:bg-red-50"
                                    title="Hapus">
                                🗑️
                            </button>
                        </form>

                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-6 py-6 text-center text-gray-500">
                        Data ICD <span x-text="tab"></span> belum tersedia
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= PAGINATION ================= --}}
    @if ($icds->hasPages())
        <div class="mt-6">
            {{ $icds->appends([
                'search' => request('search'),
                'version' => request('version', 9)
            ])->links() }}
        </div>
    @endif

    {{-- ================= MODAL IMPORT ================= --}}
    <div 
        x-show="open"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-transition
    >
        <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">
                    Upload Excel ICD <span x-text="tab"></span>
                </h2>
                <button @click="open = false">✕</button>
            </div>

            <form action="{{ route('icds.import') }}" method="POST" enctype="multipart/form-data">
                @csrf


                <div class="pt-6 pb-6">
                    <label class="{{ $label }}">Kategori ICD</label>
                    <input type="text" name="category" :value="tab" readonly
                        class="{{ $field }} @error('category'){{ $fieldErr }}@enderror">
                    @error('category')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <input 
                    type="file" 
                    name="file"
                    accept=".xlsx,.xls,.csv"
                    class="w-full border rounded-lg p-2 mb-4"
                    required
                >

                <div class="flex justify-end gap-2">
                    <button type="button"
                        @click="open = false"
                        class="px-4 py-2 bg-gray-300 rounded-lg">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                        Upload
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>

<script src="//unpkg.com/alpinejs" defer></script>

@endsection