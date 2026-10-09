@extends('admin.layouts.app')

@section('title', 'Edit Pasien')

@section('content')

@php
    $label = 'mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Pasien', 'url' => route('patients.index')],
    ['label' => 'Edit Pasien'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Data Pasien</h1>
    <p class="text-xs text-gray-500 mt-1">Perbarui data profil pasien klinik terapi olahraga.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 w-full max-w-4xl mx-auto">
    <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- BIODATA PASIEN --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900">Biodata Pasien</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- No HP --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">
                        Nomor HP / WhatsApp Pasien <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="phone_number" id="phone_number" 
                               value="{{ old('phone_number', $patient->phone_number) }}" 
                               class="{{ $input }} pl-10 font-mono @error('phone_number'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: 08123456789 atau 85183036722" required>
                        <span class="absolute left-3.5 top-3 text-gray-400">📱</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Nomor HP digunakan untuk deteksi otomatis di Form Reservasi.</p>
                    @error('phone_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="{{ $label }}">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $patient->full_name) }}" 
                           class="{{ $input }} @error('full_name'){{ $fieldErr }}@enderror" 
                           placeholder="Nama lengkap pasien" required>
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Usia --}}
                <div>
                    <label class="{{ $label }}">Usia (Tahun) <span class="text-red-500">*</span></label>
                    <input type="number" name="age" id="age" value="{{ old('age', $patient->age) }}" min="1" max="120"
                           class="{{ $input }} @error('age'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: 28" required>
                    @error('age')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <label class="{{ $label }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" id="gender" class="{{ $input }} @error('gender'){{ $fieldErr }}@enderror" required>
                        <option value="" disabled>Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pekerjaan --}}
                <div>
                    <label class="{{ $label }}">Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $patient->occupation) }}" 
                           class="{{ $input }} @error('occupation'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: Atlet Basket, Karyawan Swasta, Pelajar" required>
                    @error('occupation')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Alamat Tempat Tinggal / Domisili <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="2" 
                              class="{{ $input }} @error('address'){{ $fieldErr }}@enderror" 
                              placeholder="Alamat lengkap tempat tinggal pasien" required>{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('patients.index') }}" 
               class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-200 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
