@extends('admin.layouts.app')

@section('title', 'Data Pengaju')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Menu'],
    ['label' => 'Pengaduan'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Pengaduan</h1>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Email                
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Telp
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Isi Pengaduan
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
            <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    tipeh
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    ajisanthoshol2211@gmail.com
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    085183036722
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    hallo, saya mengalami kendala dan terhambat saat memberikan dokumen secara offline
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection
