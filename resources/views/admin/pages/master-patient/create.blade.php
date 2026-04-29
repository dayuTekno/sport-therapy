@extends('admin.layouts.app')

@section('title', 'Tambah Pasien')

@section('content')

@php
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Pasien', 'url' => route('patients.index')],
    ['label' => 'Tambah'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Tambah Data Pasien</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 w-full">
    <form action="{{ route('patients.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="{{ $label }}">NIK <span class="text-red-500">*</span></label>
            <input type="text" name="nik" value="{{ old('nik') }}" 
                   class="{{ $input }} @error('nik'){{ $fieldErr }}@enderror" 
                   maxlength="16" placeholder="Masukkan 16 digit NIK" required>
            @error('nik')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" 
                   class="{{ $input }} @error('full_name'){{ $fieldErr }}@enderror" 
                   placeholder="Nama lengkap pasien" required>
            @error('full_name')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                       class="{{ $input }} @error('date_of_birth'){{ $fieldErr }}@enderror" required>
                @error('date_of_birth')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="{{ $label }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="gender" class="{{ $input }} @error('gender'){{ $fieldErr }}@enderror" required>
                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">Nomor Telepon</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" 
                       class="{{ $input }} @error('phone_number'){{ $fieldErr }}@enderror" 
                       placeholder="08xxxxxxxxxx">
                @error('phone_number')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $label }}">Default Eselon (Opsional)</label>
                <select name="eselon_id" class="{{ $input }} @error('eselon_id'){{ $fieldErr }}@enderror">
                    <option value="">Tidak ada</option>
                    @foreach($eselons as $eselon)
                        <option value="{{ $eselon->id }}" {{ old('eselon_id') == $eselon->id ? 'selected' : '' }}>{{ $eselon->name }}</option>
                    @endforeach
                </select>
                @error('eselon_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="{{ $label }}">Alamat</label>
            <textarea name="address" rows="3" 
                      class="{{ $input }} @error('address'){{ $fieldErr }}@enderror" 
                      placeholder="Alamat lengkap">{{ old('address') }}</textarea>
            @error('address')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('patients.index') }}" 
               class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">
                Batal
            </a>
            <button type="submit" 
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium">
                Simpan Pasien
            </button>
        </div>
    </form>
</div>

@endsection
