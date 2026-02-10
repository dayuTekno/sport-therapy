@extends('admin.layouts.app')

@section('title', 'Detail Pengaju')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Pengaju', 'url' => route('listpengaju')],
    ['label' => 'Detail'],
]"/>

<div class="w-full bg-white p-6 rounded-lg shadow mt-6 space-y-8">

    <h2 class="text-xl font-semibold border-b pb-3">
        Detail Pengaju
    </h2>

    {{-- ================= USER INFO ================= --}}
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            Informasi Akun
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Nama</p>
                <p class="font-medium">{{ $user->name }}</p>
            </div>

            <div>
                <p class="text-gray-500">Email</p>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    {{-- ================= PERUSAHAAN ================= --}}
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            Data Perusahaan
        </h3>

        @if ($user->dataRegister)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nama Perusahaan</p>
                    <p class="font-medium">{{ $user->dataRegister->nama_perusahaan }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Bidang Usaha</p>
                    <p class="font-medium">{{ $user->dataRegister->bidang_usaha }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Kota Perusahaan</p>
                    <p class="font-medium">{{ $user->dataRegister->kota_perusahaan }}</p>
                </div>

                <div>
                    <p class="text-gray-500">No Telp Kantor</p>
                    <p class="font-medium">{{ $user->dataRegister->no_telp_kantor }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-gray-500">Alamat Perusahaan</p>
                    <p class="font-medium">{{ $user->dataRegister->alamat }}</p>
                </div>

                <div>
                    <p class="text-gray-500">NPWP</p>
                    <p class="font-medium">{{ $user->dataRegister->npwp }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Tenaga Kerja</p>
                    <p class="font-medium">{{ $user->dataRegister->tenaga_kerja }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Jumlah Kapal</p>
                    <p class="font-medium">{{ $user->dataRegister->jml_kapal }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Kantor Cabang</p>
                    <p class="font-medium">{{ $user->dataRegister->kantor_cabang }}</p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-gray-500">Keterangan</p>
                    <p class="font-medium">{{ $user->dataRegister->keterangan }}</p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500 italic">
                Data perusahaan belum tersedia.
            </p>
        @endif
    </div>

    {{-- ================= PENANGGUNG JAWAB ================= --}}
    @if ($user->dataRegister)
    <div>
        <h3 class="text-lg font-semibold mb-4 text-gray-700">
            Penanggung Jawab
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">Nama</p>
                <p class="font-medium">{{ $user->dataRegister->penanggungjawab }}</p>
            </div>

            <div>
                <p class="text-gray-500">Jabatan</p>
                <p class="font-medium">{{ $user->dataRegister->jabatan_penanggungjawab }}</p>
            </div>

            <div>
                <p class="text-gray-500">No Telp</p>
                <p class="font-medium">{{ $user->dataRegister->no_telp_penanggungjawab }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="text-gray-500">Alamat Rumah</p>
                <p class="font-medium">{{ $user->dataRegister->alamat_rumah_penanggungjawab }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- ================= ACTION ================= --}}
    <div class="flex justify-end gap-2 pt-4 border-t">
        <a href="{{ route('listpengaju') }}"
           class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-50">
            Kembali
        </a>
    </div>

</div>

@endsection
