@extends('admin.layouts.app')

@section('title', 'Tambah Peralatan Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Inventaris', 'url' => route('therapy-equipments.index')],
    ['label' => 'Tambah Peralatan'],
]" />

<div class="max-w-2xl bg-white p-6 rounded-xl shadow-sm border border-gray-100">
    <h2 class="text-lg font-bold text-gray-800 mb-6">Tambah Peralatan Terapi Baru</h2>

    <form action="{{ route('therapy-equipments.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kode Alat *</label>
            <input type="text" name="equipment_code" value="{{ old('equipment_code', 'EQ-' . rand(100, 999)) }}" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('equipment_code') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Nama Peralatan Terapi *</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Ultrasound Therapy Unit" required
                   class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kategori</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="Contoh: Elektroterapi, Recovery"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Satuan (UOM) *</label>
                <input type="text" name="uom" value="{{ old('uom', 'Unit') }}" placeholder="Unit, Roll, Set, Pcs" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Jumlah Stok *</label>
                <input type="number" name="stock" value="{{ old('stock', 1) }}" min="0" required
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Kondisi Alat *</label>
                <select name="condition" required class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                    <option value="baik">Baik & Layak Pakai</option>
                    <option value="perlu_perbaikan">Perlu Servis / Perbaikan</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold uppercase text-gray-600 mb-1">Deskripsi / Catatan</label>
            <textarea name="description" rows="3" placeholder="Keterangan spesifikasi alat..."
                      class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('therapy-equipments.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
            <button type="submit" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">Simpan Alat</button>
        </div>
    </form>
</div>

@endsection
