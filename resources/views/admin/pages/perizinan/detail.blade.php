@extends('admin.layouts.app')

@section('title', 'Detail Perizinan')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Perizinan', 'url' => route('perizinan')],
    ['label' => 'Detail'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6 space-y-8">

    <h2 class="text-xl font-semibold border-b pb-3">
        Detail Perizinan
    </h2>

    {{-- ================= USER INFO ================= --}}
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            Informasi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Judul Perizinan</p>
                <p class="font-medium">Perizinan tahap ke 1</p>
            </div>

            <div>
                <p class="text-gray-500">Perusahaan</p>
                <p class="font-medium">Darma Ayu Teknologi</p>
            </div>

            <div>
                <p class="text-gray-500">Badan Usaha</p>
                <p class="font-medium">IT Consultant</p>
            </div>

            <div>
                <p class="text-gray-500">Status Perizinan</p>
                <p class="font-medium">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu Approval</span>
                </p>
            </div>
        </div>
    </div>

    {{-- ================= PERUSAHAAN ================= --}}
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            Item Perizinan
        </h3>

            <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                No.
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Nama File
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Status Approval                
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Di Approve oleh
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Tgl Approval
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                Aksi
            </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 bg-white">
            <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    1
                </td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    BPKB
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-yellow-100 text-yellow-700">Menunggu Approval</span>
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    -
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    -
                </td>
            <td class="px-6 py-4 flex items-center space-x-2">

                {{-- APPROVE --}}
                    <button type="submit"
                        class="inline-flex items-center justify-center
                            w-8 h-8 rounded-lg
                            text-emerald-600 hover:bg-emerald-50"
                        title="Approve">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </button>

                {{-- REJECT --}}
                    <button type="submit"
                        class="inline-flex items-center justify-center
                            w-8 h-8 rounded-lg
                            text-red-600 hover:bg-red-50"
                        title="Reject">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

            </td>

            </tr>

                        <tr>
                <td class="px-6 py-4 font-medium text-gray-800">
                    2
                </td>
                <td class="px-6 py-4 font-medium text-gray-800">
                    STNK
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    <span class="inline-flex px-2.5 py-1 rounded-md text-xs font-medium bg-green-100 text-green-700">Disetujui</span>
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    Admin
                </td>

                <td class="px-6 py-4 text-sm text-gray-600">
                    27-10-2025
                </td>
                <td class="px-6 py-4 space-x-2">

                </td>
            </tr>
        </tbody>
    </table>
              
    </div>

    {{-- ================= ACTION ================= --}}
    <div class="flex justify-end gap-2 pt-4 border-t">
        <a href="{{ route('listpengaju') }}"
           class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">
            Kembali
        </a>
    </div>

</div>

@endsection
