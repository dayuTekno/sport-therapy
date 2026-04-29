@extends('admin.layouts.app')

@section('title', 'Pilih Poliklinik')

@section('content')

@php
    $label = 'mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Registrasi', 'url' => route('registrasi.index')],
    ['label' => 'Antrian Poliklinik'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Antrian Pasien ke Poliklinik</h1>
</div>

@include('admin.partials.alert')

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 w-full max-w-2xl">
    
    <!-- Info Pasien -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg">
        <h3 class="text-sm font-semibold text-blue-800 mb-2">Informasi Pasien</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">NIK</p>
                <p class="font-medium text-gray-800">{{ $patient->nik }}</p>
            </div>
            <div>
                <p class="text-gray-500">Nama Lengkap</p>
                <p class="font-medium text-gray-800">{{ $patient->full_name }}</p>
            </div>
            <div>
                <p class="text-gray-500">Nomor RM</p>
                <p class="font-medium text-gray-800">{{ substr($patient->patient_code, 0, 8) }}</p>
            </div>
            <div>
                <p class="text-gray-500">Default Eselon</p>
                <p class="font-medium text-gray-800">
                    @if($patient->eselon)
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ $patient->eselon->name }}</span>
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Form Pilih Poli -->
    <form action="{{ route('registrasi.queue.store', $patient->id) }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="{{ $label }}">Kategori Pasien / Eselon <span class="text-red-500">*</span></label>
            <select name="eselon_id" class="{{ $input }} @error('eselon_id'){{ $fieldErr }}@enderror" required>
                <option value="" disabled {{ !$patient->eselon_id ? 'selected' : '' }}>Pilih Kategori / Eselon</option>
                @foreach ($eselons as $eselon)
                    <option value="{{ $eselon->id }}" {{ $patient->eselon_id == $eselon->id ? 'selected' : '' }}>{{ $eselon->name }}</option>
                @endforeach
            </select>
            @error('eselon_id')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Tujuan Poliklinik <span class="text-red-500">*</span></label>
            <select name="poly_id" id="poly_select" class="{{ $input }} @error('poly_id'){{ $fieldErr }}@enderror" required>
                <option value="" disabled selected>Pilih Poliklinik</option>
                @foreach ($polyclinics as $poly)
                    <option value="{{ $poly->id }}">{{ $poly->name }}</option>
                @endforeach
            </select>
            @error('poly_id')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="{{ $label }}">Pilih Ketersediaan Dokter <span class="text-red-500">*</span></label>
            <select name="doctor_id" id="doctor_select" class="{{ $input }} @error('doctor_id'){{ $fieldErr }}@enderror" required>
                <option value="" disabled selected>Pilih Poliklinik Dahulu</option>
            </select>
            @error('doctor_id')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-500">Pasien akan dimasukkan ke antrian perawat untuk poliklinik dan dokter yang dipilih.</p>
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('registrasi.index') }}" 
               class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">
                Batal
            </a>
            <button type="submit" 
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium">
                Daftarkan ke Antrian
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('poly_select').addEventListener('change', function() {
        let polyId = this.value;
        let doctorSelect = document.getElementById('doctor_select');
        
        doctorSelect.innerHTML = '<option value="" disabled selected>Memuat dokter...</option>';
        
        if (polyId) {
            fetch(`/api/polyclinics/${polyId}/doctors`)
                .then(response => response.json())
                .then(data => {
                    doctorSelect.innerHTML = '<option value="" disabled selected>Pilih Dokter</option>';
                    if (data.length === 0) {
                        doctorSelect.innerHTML = '<option value="" disabled selected>Tidak ada dokter tersedia</option>';
                    } else {
                        data.forEach(doctor => {
                            let option = document.createElement('option');
                            option.value = doctor.id;
                            option.text = doctor.full_name;
                            doctorSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error fetching doctors:', error);
                    doctorSelect.innerHTML = '<option value="" disabled selected>Gagal memuat dokter</option>';
                });
        }
    });
</script>
@endpush


@endsection
