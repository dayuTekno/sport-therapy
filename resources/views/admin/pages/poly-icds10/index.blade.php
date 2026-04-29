@extends('admin.layouts.app')

@section('title', 'ICD 10 Poly')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Poli'],
    ['label' => 'ICD 10 Poly'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">ICD 10 Poly</h1>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Kode Poliklinik
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama Poliklinik
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Jumlah ICD 10 Terhubung
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($polyclinics as $poly)
            <tr>
                <td class="px-6 py-4 font-medium">
                    {{ $poly->polyclinic_code }}
                </td>
                <td class="px-6 py-4 font-medium">
                    {{ $poly->name }}
                </td>
                <td class="px-6 py-4 font-medium">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-700">
                        {{ $poly->icds10_count }} ICD
                    </span>
                </td>

                <td class="px-6 py-4 text-right space-x-2">

                    {{-- EDIT --}}
                    <a href="{{ route('poly-icds10.edit', $poly->id) }}"
                    class="inline-flex items-center justify-center
                            w-8 h-8 rounded-lg
                            text-blue-600 hover:bg-blue-50"
                    title="Kelola ICD">
                        {{-- settings icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" />
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </a>

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
@if ($polyclinics->hasPages())
    <div class="mt-6">
        {{ $polyclinics->links() }}
    </div>
@endif

@endsection
