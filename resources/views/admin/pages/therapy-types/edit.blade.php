@extends('admin.layouts.app')

@section('title', 'Edit Jenjang Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => route('therapy-types.index')],
    ['label' => 'Edit Jenjang Terapi'],
]" />

<div class="max-w-2xl bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-lg font-bold text-gray-800 mb-6">Edit Jenjang Terapi: {{ $type->name }}</h2>

    <form action="{{ route('therapy-types.update', $type->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kode Terapi *</label>
                <input type="text" name="code" value="{{ old('code', $type->code) }}" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                @error('code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Urutan Tahap (Stage Order) *</label>
                <input type="number" name="stage_order" value="{{ old('stage_order', $type->stage_order) }}" min="1" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Nama Tahapan Terapi *</label>
            <input type="text" name="name" value="{{ old('name', $type->name) }}" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Durasi Sesi (Menit) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $type->duration_minutes) }}" min="15" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Tarif Layanan (Rp)</label>
                <input type="number" name="price" value="{{ old('price', $type->price) }}"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Deskripsi / Ruang Lingkup Tindakan</label>
            <textarea name="description" rows="3"
                      class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description', $type->description) }}</textarea>
        </div>

        <div class="flex items-center gap-2 pt-2">
            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $type->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
            <label for="is_active" class="text-sm font-medium text-gray-700">Aktifkan Jenjang Terapi Ini (Dapat dipilih pada Sesi Terapi)</label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('therapy-types.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Perbarui Jenjang</button>
        </div>
    </form>
</div>

@endsection
