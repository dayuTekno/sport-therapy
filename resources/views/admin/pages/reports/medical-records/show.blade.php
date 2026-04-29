@extends('admin.layouts.app')

@section('title', 'Detail Rekam Medis')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Laporan', 'url' => route('reports.medical_records.index')],
    ['label' => 'Detail Rekam Medis'],
]" />

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Detail Rekam Medis</h1>
        <p class="text-sm text-gray-500">ID Kunjungan: <span class="font-bold text-blue-600">{{ $queue->queue_code }}</span> | Tanggal: {{ $queue->created_at->format('d M Y H:i') }}</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium text-sm flex items-center gap-2 print:hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Rekam Medis
        </button>
        <a href="{{ route('reports.medical_records.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm print:hidden">
            Kembali
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    
    {{-- Info Pasien --}}
    <div class="md:col-span-1 space-y-6">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4 border-b pb-2">Informasi Pasien</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Nama Lengkap</span>
                    <span class="text-gray-800 font-semibold text-sm">{{ $queue->patient->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">No. RM</span>
                    <span class="text-gray-800 font-medium text-sm">{{ substr($queue->patient->patient_code, 0, 8) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">NIK</span>
                    <span class="text-gray-800 font-medium text-sm">{{ $queue->patient->nik }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Jenis Kelamin</span>
                    <span class="text-gray-800 font-medium text-sm">{{ $queue->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Umur</span>
                    <span class="text-gray-800 font-medium text-sm">{{ \Carbon\Carbon::parse($queue->patient->date_of_birth)->age }} Thn</span>
                </div>
                <div class="flex justify-between pt-2 border-t mt-2">
                    <span class="text-gray-500 text-sm">Eselon / Kategori</span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold">{{ $queue->eselon->name ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4 border-b pb-2">Vital Signs (Amnesa)</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">TD (mmHg)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->systolic }}/{{ $record->diastolic }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Suhu (°C)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->temperature }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Nadi (x/m)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->heart_rate }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Napas (x/m)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->respiratory_rate }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">BB (kg)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->weight }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">TB (cm)</p>
                    <p class="text-sm font-bold text-gray-700">{{ $record->height }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Hasil Pemeriksaan --}}
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Catatan Medis & Diagnosa</h3>
                <span class="text-xs text-gray-500">Pemeriksa: {{ $record->doctor_name ?? $record->nurse_name }}</span>
            </div>
            <div class="p-6 space-y-6">
                {{-- Keluhan --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                        Keluhan Pasien (Subjective)
                    </h4>
                    <div class="p-4 bg-blue-50/30 rounded-lg text-sm text-gray-700 italic border-l-4 border-blue-400">
                        "{{ $record->symptoms }}"
                    </div>
                </div>

                {{-- Diagnosa --}}
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                        Diagnosa (Objective & Assessment)
                    </h4>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-800 font-medium mb-3">{{ $record->doctor_diagnosis ?: 'Belum ada diagnosa narasi' }}</p>
                        
                        <div class="space-y-2">
                            @foreach($record->diagnoses as $d)
                                <div class="flex items-start gap-2">
                                    <span class="px-2 py-0.5 {{ $d->type == 'primary' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-700' }} rounded text-[10px] font-bold uppercase">{{ $d->type }}</span>
                                    <span class="text-sm text-gray-700"><span class="font-bold">{{ $d->icd->icd_code }}</span> - {{ $d->icd->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Catatan Dokter --}}
                @if($record->doctor_notes)
                <div class="space-y-2">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                        Catatan / Rencana Terapi (Plan)
                    </h4>
                    <div class="p-4 border border-gray-100 rounded-lg text-sm text-gray-600">
                        {{ $record->doctor_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tindakan & Obat --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 bg-gray-50 border-b">
                <h3 class="font-bold text-gray-800">Tindakan & Resep Obat</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Tindakan --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tindakan Medis</h4>
                        <ul class="space-y-2">
                            @forelse($record->treatments as $t)
                                <li class="flex items-center gap-3 text-sm p-2 bg-gray-50 rounded">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    {{ $t->procedure->name }}
                                </li>
                            @empty
                                <li class="text-xs text-gray-400 italic">Tidak ada tindakan medis.</li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Resep Obat --}}
                    <div class="space-y-3">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Resep Obat</h4>
                        <ul class="space-y-2">
                            @forelse($record->medicines as $m)
                                <li class="p-2 border border-gray-100 rounded text-sm">
                                    <div class="flex justify-between">
                                        <span class="font-bold text-gray-700">{{ $m->medicine->medicine_name }}</span>
                                        <span class="text-blue-600 font-medium">x {{ $m->quantity }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 italic mt-1">{{ $m->instructions }}</p>
                                </li>
                            @empty
                                <li class="text-xs text-gray-400 italic">Tidak ada resep obat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
