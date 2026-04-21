@extends('admin.layouts.app')

@section('title', 'Tambah Dokter')

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
        ['label' => 'Dokter', 'url' => route('doctors.index')],
        ['label' => 'Tambah Dokter'],
    ]" />
    <div class="mt-6 w-full bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Tambah Data Dokter</h2>

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

        <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            <div class="pt-6">
                <label class="{{ $label }}">Kode Dokter</label>
                <input type="text" name="doctor_code"
                    value="{{ old('doctor_code', $settings['doctor_code'] ?? '') }}"
                    class="{{ $field }} @error('doctor_code'){{ $fieldErr }}@enderror" placeholder="Kode Dokter">
                @error('doctor_code')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="pt-6">
                <label class="{{ $label }}">NIP</label>
                <input type="text" name="employee_code"
                    value="{{ old('employee_code', $settings['employee_code'] ?? '') }}"
                    class="{{ $field }} @error('employee_code'){{ $fieldErr }}@enderror" placeholder="Nomor Induk Pegawai">
                @error('employee_code')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            

            <div class="pt-6">
                <label class="{{ $label }}">Nama Dokter</label>
                <input type="text" name="full_name"
                    value="{{ old('full_name', $settings['full_name'] ?? '') }}"
                    class="{{ $field }} @error('full_name'){{ $fieldErr }}@enderror" placeholder="Nama Dokter">
                @error('full_name')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">No. Lisensi</label>
                <input type="text" name="license_number"
                    value="{{ old('license_number', $settings['license_number'] ?? '') }}"
                    class="{{ $field }} @error('license_number'){{ $fieldErr }}@enderror" placeholder="Nomor Lisensi">
                @error('license_number')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">Spesialisasi</label>
                <input type="text" name="specialization"
                    value="{{ old('specialization', $settings['specialization'] ?? '') }}"
                    class="{{ $field }} @error('specialization'){{ $fieldErr }}@enderror"
                    placeholder="Spesialisasi">
                @error('specialization')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">No. Hp</label>
                <input type="text" name="phone" value="{{ old('phone', $settings['phone'] ?? '') }}"
                    class="{{ $field }} @error('phone'){{ $fieldErr }}@enderror" placeholder="+62819.....">
                @error('phone')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-6">
                <label class="{{ $label }}">Email</label>
                <input type="text" name="email"
                    value="{{ old('email', $settings['email'] ?? '') }}"
                    class="{{ $field }} @error('email'){{ $fieldErr }}@enderror"
                    placeholder="....@....com">
                @error('email')
                    <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2 pt-6">
                <label class="{{ $label }}">Status</label>
                <select name="is_active" class="select2 w-full @error('is_active'){{ $fieldErr }}@enderror">
                    <option value="1">Aktif</option>
                    <option value="0">Non Aktif</option>
                </select>

                @error('is_active')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>            

            {{-- Buttons --}}
            <div class="flex pt-6 items-center justify-between">
                <a href="{{ route('doctors.index') }}"
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
