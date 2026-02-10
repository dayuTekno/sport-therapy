@extends('admin.layouts.app')

@section('title', 'Perizinan')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Menu'],
    ['label' => 'Perizinan'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Perizinan</h1>
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
                Total Item Perizinan
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
            <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    Darma Ayu Teknologi
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    IT Consultant
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    2
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu Approval</span>
                </td>

                <td class="px-6 py-4 space-x-2">

                    {{-- DETAIL --}}
                    <a href="{{ route('perizinan.show', 1) }}"
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
        </tbody>
    </table>
</div>

@endsection
