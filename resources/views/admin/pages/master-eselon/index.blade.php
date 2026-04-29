@extends('admin.layouts.app')

@section('title', 'Eselon')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data'],
    ['label' => 'Eselon'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Eselon</h1>

    <a href="{{ route("eselons.create") }}"
       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Tambah Eselon
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                    Kode Eselon
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Desc
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($eselons as $es)
            <tr>
                <td class="px-6 py-4 font-medium">
                    {{ $es->eselon_code }}
                </td>
                <td class="px-6 py-4 font-medium">
                    {{ $es->name }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $es->desc ?: '-' }}
                </td>

                <td class="px-6 py-4 text-right space-x-2">
                    <div class="flex justify-end gap-2">
                        {{-- EDIT --}}
                        <a href="{{ route('eselons.edit', $es->id) }}" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>

                        {{-- DELETE --}}
                        <form action="{{ route('eselons.destroy', $es->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>

            </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-6 py-6 text-center text-gray-500">
                        Data belum tersedia
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if ($eselons->hasPages())
    <div class="mt-6">
        {{ $eselons->links() }}
    </div>
@endif


@endsection
