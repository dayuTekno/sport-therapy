@extends('admin.layouts.app')

@section('title', 'Jadwal Dokter')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Settings', 'url' => '#'],
    ['label' => 'Jadwal Dokter']
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Manajemen Jadwal Dokter</h1>
    <a href="{{ route('doctor-schedules.create') }}" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Jadwal
    </a>
</div>

@include('admin.partials.alert')

@php
    $days = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-600">
            <thead class="bg-gray-50 text-gray-700 font-medium border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Dokter</th>
                    <th class="px-6 py-4">Poliklinik</th>
                    <th class="px-6 py-4">Hari</th>
                    <th class="px-6 py-4">Jam Praktik</th>
                    <th class="px-6 py-4">Kuota</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $schedule->doctor->full_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">{{ $schedule->polyclinic->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600 font-medium">{{ $days[$schedule->day_of_week] ?? '-' }}</td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                    </td>
                    <td class="px-6 py-4 text-gray-600">{{ $schedule->quota ? $schedule->quota . ' Pasien' : 'Tidak Terbatas' }}</td>
                    <td class="px-6 py-4">
                        @if($schedule->is_active)
                            <span class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-lg">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded-lg">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('doctor-schedules.edit', $schedule->id) }}" class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>
                            <form action="{{ route('doctor-schedules.destroy', $schedule->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
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
                    <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                        <p class="mb-2">Belum ada jadwal dokter</p>
                        <a href="{{ route('doctor-schedules.create') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Tambah Jadwal Baru</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($schedules->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $schedules->links() }}
        </div>
    @endif
</div>

@endsection
