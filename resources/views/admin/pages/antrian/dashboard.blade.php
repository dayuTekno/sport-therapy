@extends('admin.layouts.app')

@section('title', 'Manajemen Antrian Admisi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Antrian Admisi', 'url' => ''],
]" />

<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Antrian Admisi</h1>
        <p class="text-sm text-gray-500">Tanggal: {{ now()->format('d M Y') }}</p>
    </div>
    
    <a href="{{ route('antrian') }}" target="_blank" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
        Buka Kiosk (Layar Pengunjung)
    </a>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <!-- Sedang Dipanggil -->
    <div class="md:col-span-1 bg-white rounded-xl shadow-sm border border-blue-100 p-6 flex flex-col items-center justify-center text-center">
        <h2 class="text-lg font-semibold text-gray-600 mb-2">Sedang Dipanggil</h2>
        @if($currentQueue)
            <div class="text-5xl font-black text-blue-600 my-4">{{ $currentQueue->queue_code }}</div>
            <p class="text-sm text-gray-500">Dipanggil pada: {{ $currentQueue->updated_at->format('H:i') }}</p>
            <form action="{{ route('admin.antrian.call', $currentQueue->id) }}" method="POST" class="mt-4">
                @csrf
                <button class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 text-sm font-medium">Panggil Ulang</button>
            </form>
        @else
            <div class="text-4xl font-bold text-gray-300 my-4">-</div>
            <p class="text-sm text-gray-400">Belum ada antrian yang dipanggil</p>
        @endif
    </div>

    <!-- Daftar Antrian -->
    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-100 bg-gray-50">
            <h2 class="font-semibold text-gray-800">Daftar Antrian Hari Ini</h2>
        </div>
        
        <div class="overflow-y-auto max-h-96">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-xs text-gray-500 uppercase">
                        <th class="p-4 font-medium">No. Antrian</th>
                        <th class="p-4 font-medium">Waktu Ambil</th>
                        <th class="p-4 font-medium">Status</th>
                        <th class="p-4 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($queues as $q)
                        <tr class="hover:bg-gray-50">
                            <td class="p-4 font-bold text-gray-800">{{ $q->queue_code }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ $q->created_at->format('H:i') }}</td>
                            <td class="p-4">
                                @if($q->status == 'waiting')
                                    <span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Menunggu</span>
                                @elseif($q->status == 'called')
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">Dipanggil</span>
                                @else
                                    <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Selesai</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                @if($q->status == 'waiting' || $q->status == 'called')
                                    <form action="{{ route('admin.antrian.call', $q->id) }}" method="POST">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition">
                                            Panggil
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Belum ada antrian hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
