@extends('admin.layouts.app')

@section('title', 'Tambah Jadwal Dokter')

@section('content')

@php
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Settings', 'url' => '#'],
    ['label' => 'Jadwal Dokter', 'url' => route('doctor-schedules.index')],
    ['label' => 'Tambah']
]" />

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Tambah Jadwal Dokter</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 w-full">
    <form action="{{ route('doctor-schedules.store') }}" method="POST" class="space-y-5">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">Pilih Dokter <span class="text-red-500">*</span></label>
                <select name="doctor_id" class="{{ $input }} @error('doctor_id'){{ $fieldErr }}@enderror" required>
                    <option value="" disabled selected>Pilih Dokter</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->full_name }}</option>
                    @endforeach
                </select>
                @error('doctor_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="{{ $label }}">Pilih Poliklinik <span class="text-red-500">*</span></label>
                <select name="poly_id" class="{{ $input }} @error('poly_id'){{ $fieldErr }}@enderror" required>
                    <option value="" disabled selected>Pilih Poliklinik</option>
                    @foreach($polyclinics as $poly)
                        <option value="{{ $poly->id }}" {{ old('poly_id') == $poly->id ? 'selected' : '' }}>{{ $poly->name }}</option>
                    @endforeach
                </select>
                @error('poly_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label class="{{ $label }}">Hari Praktik <span class="text-red-500">*</span></label>
            <select name="day_of_week" class="{{ $input }} @error('day_of_week'){{ $fieldErr }}@enderror" required>
                <option value="" disabled selected>Pilih Hari</option>
                @foreach($days as $key => $day)
                    <option value="{{ $key }}" {{ old('day_of_week') !== null && old('day_of_week') == $key ? 'selected' : '' }}>{{ $day }}</option>
                @endforeach
            </select>
            @error('day_of_week')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="start_time" value="{{ old('start_time') }}" 
                       class="{{ $input }} @error('start_time'){{ $fieldErr }}@enderror" required>
                @error('start_time')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="{{ $label }}">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="end_time" value="{{ old('end_time') }}" 
                       class="{{ $input }} @error('end_time'){{ $fieldErr }}@enderror" required>
                @error('end_time')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="{{ $label }}">Kuota Pasien (Opsional)</label>
                <input type="number" name="quota" value="{{ old('quota') }}" min="1"
                       class="{{ $input }} @error('quota'){{ $fieldErr }}@enderror" placeholder="Kosongkan jika tanpa batas">
                @error('quota')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Jumlah maksimum pasien yang dapat dilayani</p>
            </div>

            <div>
                <label class="{{ $label }}">Status Jadwal</label>
                <div class="flex items-center gap-2 mt-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-700">Jadwal Aktif</label>
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end gap-3 border-t">
            <a href="{{ route('doctor-schedules.index') }}" 
               class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium text-sm">
                Batal
            </a>
            <button type="submit" 
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium text-sm">
                Simpan Jadwal
            </button>
        </div>
    </form>
</div>

@endsection
