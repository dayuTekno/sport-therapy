@extends('admin.layouts.app')

@section('title', 'Edit Peralatan Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Inventaris', 'url' => route('therapy-equipments.index')],
    ['label' => 'Edit Peralatan'],
]" />

<div class="max-w-2xl bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-lg font-bold text-gray-800 mb-6">Edit Peralatan Terapi: {{ $equipment->name }}</h2>

    <form action="{{ route('therapy-equipments.update', $equipment->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kode Alat *</label>
            <input type="text" name="equipment_code" value="{{ old('equipment_code', $equipment->equipment_code) }}" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('equipment_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Nama Peralatan Terapi *</label>
            <input type="text" name="name" value="{{ old('name', $equipment->name) }}" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kategori</label>
                <input type="text" name="category" value="{{ old('category', $equipment->category) }}"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Satuan (UOM) *</label>
                <input type="text" name="uom" value="{{ old('uom', $equipment->uom) }}" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Jumlah Stok *</label>
                <input type="number" name="stock" value="{{ old('stock', $equipment->stock) }}" min="0" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kondisi Alat *</label>
                <select name="condition" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="baik" {{ $equipment->condition == 'baik' ? 'selected' : '' }}>Baik & Layak Pakai</option>
                    <option value="perlu_perbaikan" {{ $equipment->condition == 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Servis / Perbaikan</option>
                    <option value="rusak" {{ $equipment->condition == 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Deskripsi / Catatan</label>
            <textarea name="description" rows="3"
                      class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description', $equipment->description) }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_available" value="1" {{ $equipment->is_available ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                <span class="text-sm font-medium text-gray-700">Tersedia untuk digunakan dalam sesi terapi</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('therapy-equipments.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Perbarui Alat</button>
        </div>
    </form>
</div>

@endsection
