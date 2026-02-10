@extends('admin.layouts.app')

@section('title', 'Data Pengaju')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Master Data'],
    ['label' => 'Data Pengaju'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Data Pengaju</h1>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama Perusahaan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Bidang Usaha                
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Alamat Perusahaan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                No. Telp Perusahaan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Penanggungjawab
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
        @forelse ($users as $user)
            <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    {{ $user->dataRegister->nama_perusahaan }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $user->dataRegister->bidang_usaha }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $user->dataRegister->alamat }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $user->dataRegister->no_telp_kantor }}
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $user->dataRegister->penanggungjawab }}
                </td>

                <td class="px-6 py-4 text-right space-x-2">

                    {{-- DETAIL --}}
                    <a href="{{ route('pengaju.show', $user->id) }}"
                    class="inline-flex items-center justify-center
                            w-8 h-8 rounded-lg
                            text-emerald-600 hover:bg-emerald-50"
                    title="Detail">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12s3.75-7.5 9.75-7.5
                                    9.75 7.5 9.75 7.5
                                    -3.75 7.5 -9.75 7.5
                                    S2.25 12 2.25 12z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 15.75a3.75 3.75 0 100-7.5
                                    3.75 3.75 0 000 7.5z" />
                        </svg>
                    </a>

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7"
                    class="px-6 py-6 text-center text-gray-500">
                    Data user belum tersedia
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>


{{-- Pagination --}}
@if ($users->hasPages())
    <div class="mt-6">
        {{ $users->links() }}
    </div>
@endif

@endsection
