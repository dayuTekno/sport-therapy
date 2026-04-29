@extends('admin.layouts.app')

@section('title', 'Obat')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Obat'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Master Data Obat</h1>

    <!-- Right Actions -->
    <div class="flex items-center gap-2" x-data="{ openImport: false }">

        <!-- Import Button -->
        <button 
            @click="openImport = true"
            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Import Excel
        </button>

        <!-- Tambah -->
        <a href="{{ route('medicines.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4"></path></svg>
            Tambah Obat
        </a>

        <!-- Modal Import -->
        <div x-show="openImport" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-transition>
            <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Upload Excel</h2>
                    <button @click="openImport = false" class="text-gray-500 hover:text-black">✕</button>
                </div>
                <form action="{{ route('medicines.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="w-full border rounded-lg p-2 mb-4" required>
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="openImport = false" class="px-4 py-2 bg-gray-300 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form method="GET" action="{{ route('medicines.index') }}" class="mb-4 flex items-center gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama obat..." class="w-full md:w-64 px-4 py-2 border rounded-lg focus:ring focus:ring-blue-200">
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Cari</button>
    @if(request('search'))
        <a href="{{ route('medicines.index') }}" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Reset</a>
    @endif
</form>

<div class="bg-white rounded-xl shadow overflow-hidden" x-data="{ openOpname: false, selectedMed: {}, actualStock: 0 }">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Obat</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Internasional</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stok Tersedia</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($medicines as $med)
            <tr>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">{{ $med->medicine_name }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 italic">
                    {{ $med->medicine_international_name ?: '-' }}
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-3 py-1 rounded-full text-sm font-bold {{ $med->stock <= 10 ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ number_format($med->stock, 0) }} {{ $med->uom }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    Rp {{ number_format($med->price, 0, ',', '.') }}
                </td>

                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        {{-- STOCK OPNAME BUTTON --}}
                        <button 
                            @click="openOpname = true; selectedMed = { id: {{ $med->id }}, name: '{{ $med->medicine_name }}', stock: {{ $med->stock }}, uom: '{{ $med->uom }}' }; actualStock = {{ $med->stock }}"
                            class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100" title="Stock Opname">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                        </button>

                        {{-- EDIT --}}
                        <a href="{{ route('medicines.edit', $med->id) }}" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('medicines.destroy', $med->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Data belum tersedia</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    {{-- Modal Stock Opname --}}
    <div x-show="openOpname" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-transition x-cloak>
        <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-gray-800">Stock Opname</h2>
                <button @click="openOpname = false" class="text-gray-500 hover:text-black">✕</button>
            </div>
            
            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-blue-600 uppercase font-bold">Nama Obat</p>
                <p class="text-sm font-semibold text-gray-800" x-text="selectedMed.name"></p>
                <p class="text-xs text-gray-500 mt-1">Stok di Sistem: <span x-text="selectedMed.stock"></span> <span x-text="selectedMed.uom"></span></p>
            </div>

            <form :action="`/medicines/${selectedMed.id}/stock-opname`" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Fisik Sebenarnya</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="actual_stock" x-model="actualStock" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        <span class="text-gray-500 text-sm" x-text="selectedMed.uom"></span>
                    </div>
                    <p class="mt-1 text-[10px] text-gray-400 italic">* Mengupdate stok sistem sesuai jumlah fisik yang ada di gudang/apotek.</p>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="openOpname = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Update Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-6">
    {{ $medicines->links() }}
</div>

@endsection

@push('scripts')
<script src="//unpkg.com/alpinejs" defer></script>
@endpush
