@extends('admin.layouts.app')

@section('title', 'Tambah ICD')

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

    {{-- Breadcrumb --}}
    <x-breadcrumb :items="[
        ['label' => 'Master Data', 'url' => ''],
        ['label' => 'ICD', 'url' => route('icds9.index')],
        ['label' => 'Tambah ICD'],
    ]" />
    <div class="mt-6 w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Tambah Data ICD</h2>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('icds9.store') }}" method="POST">
            @csrf

            <div class="pt-6">
                <label class="{{ $label }}">Kategori ICD</label>
                <input type="number" name="category"
                    value="{{ old('category', $category ?? '') }}"
                    class="{{ $field }} @error('category'){{ $fieldErr }}@enderror" placeholder="Nama ICD">
                @error('category')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">Kode ICD</label>
                <input type="text" name="icd_code"
                    value="{{ old('icd_code', $settings['icd_code'] ?? '') }}"
                    class="{{ $field }} @error('icd_code'){{ $fieldErr }}@enderror" placeholder="Kode ICD">
                @error('icd_code')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">Nama ICD</label>
                <input type="text" name="name"
                    value="{{ old('name', $settings['name'] ?? '') }}"
                    class="{{ $field }} @error('name'){{ $fieldErr }}@enderror" placeholder="Nama ICD">
                @error('name')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">Versi</label>
                <input type="text" name="version"
                    value="{{ old('version', $settings['version'] ?? '') }}"
                    class="{{ $field }} @error('version'){{ $fieldErr }}@enderror"
                    placeholder="Versi">
                @error('version')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2 pt-6">
                <label class="{{ $label }}">Status</label>
                <select name="is_active" class="select2 w-full @error('is_active'){{ $fieldErr }}@enderror">
                    <option value="1" selected>Aktif</option>
                    <option value="0">Non Aktif</option>
                </select>

                @error('is_active')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex pt-6 items-center justify-between">
                <a href="{{ route('icds9.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script>
        $(function() {
            $('.select2').select2({
                placeholder: "Pilih kategori",
                allowClear: true,
                width: '100%'
            });
        });
    </script>


@endsection
