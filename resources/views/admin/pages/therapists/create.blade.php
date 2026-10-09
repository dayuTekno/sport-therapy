@extends('admin.layouts.app')

@section('title', 'Tambah Terapis')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => route('therapists.index')],
    ['label' => 'Tambah Terapis'],
]" />

<div class="max-w-2xl bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-lg font-bold text-gray-800 mb-6">Tambah Terapis Baru</h2>

    <form action="{{ route('therapists.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kode Terapis *</label>
            <input type="text" name="therapist_code" value="{{ old('therapist_code', 'TRP-' . rand(100, 999)) }}" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('therapist_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Nama Lengkap Terapis *</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Contoh: Terapis Bagas, S.Ft" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('full_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Keahlian / Spesialisasi</label>
            <input type="text" name="specialization" value="{{ old('specialization') }}" placeholder="Contoh: Fisioterapi Olahraga, Manual Therapy, Cedera Lutut"
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Nomor HP / WA</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="08123456789"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="terapis@mail.com"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600">
                <span class="text-sm font-medium text-gray-700">Terapis Aktif Bertugas</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('therapists.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Simpan Terapis</button>
        </div>
    </form>
</div>

@endsection
