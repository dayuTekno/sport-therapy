@extends('admin.layouts.app')

@section('title', 'Proses Amnesa')

@section('content')

@php
    $label = 'mb-1 block text-sm font-medium text-gray-700';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Amnesa Perawat', 'url' => route('anamnesis.index')],
    ['label' => 'Proses Amnesa'],
]" />

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pemeriksaan Awal (Amnesa)</h1>
        <p class="text-sm text-gray-500">No. Antrian: <span class="font-bold text-blue-600">{{ $queue->queue_code }}</span> | Poli: {{ $queue->poly->name ?? '-' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Info Pasien -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-24">
            <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2">Informasi Pasien</h3>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500 text-xs">Nama Lengkap</p>
                    <p class="font-medium text-gray-800">{{ $queue->patient->full_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Nomor RM / NIK</p>
                    <p class="font-medium text-gray-800">{{ substr($queue->patient->patient_code, 0, 8) }} / {{ $queue->patient->nik }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Jenis Kelamin</p>
                    <p class="font-medium text-gray-800">{{ $queue->patient->gender == 'male' ? 'Laki-laki' : 'Perempuan' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Tanggal Lahir</p>
                    <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($queue->patient->date_of_birth)->format('d M Y') }} ({{ \Carbon\Carbon::parse($queue->patient->date_of_birth)->age }} Tahun)</p>
                </div>
                <div>
                    <p class="text-gray-500 text-xs">Eselon</p>
                    <p class="font-medium text-gray-800">
                        @if($queue->patient->eselon)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ $queue->patient->eselon->name }}</span>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Amnesa -->
    <div class="lg:col-span-2">
        <form action="{{ route('anamnesis.store', $queue->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
            @csrf

            <!-- Tanda-tanda Vital -->
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800">Tanda-tanda Vital</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="{{ $label }}">Tekanan Darah</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="systolic" value="{{ old('systolic', $record->systolic ?? '') }}" class="{{ $input }} text-center" placeholder="Sys">
                            <span class="text-gray-400">/</span>
                            <input type="number" name="diastolic" value="{{ old('diastolic', $record->diastolic ?? '') }}" class="{{ $input }} text-center" placeholder="Dia">
                        </div>
                    </div>
                    <div>
                        <label class="{{ $label }}">Suhu Tubuh (°C)</label>
                        <input type="number" step="0.1" name="temperature" value="{{ old('temperature', $record->temperature ?? '') }}" class="{{ $input }}" placeholder="Mis: 36.5">
                    </div>
                    <div>
                        <label class="{{ $label }}">Nadi (x/mnt)</label>
                        <input type="number" name="heart_rate" value="{{ old('heart_rate', $record->heart_rate ?? '') }}" class="{{ $input }}" placeholder="Mis: 80">
                    </div>
                    <div>
                        <label class="{{ $label }}">Pernapasan (x/mnt)</label>
                        <input type="number" name="respiratory_rate" value="{{ old('respiratory_rate', $record->respiratory_rate ?? '') }}" class="{{ $input }}" placeholder="Mis: 20">
                    </div>
                    <div>
                        <label class="{{ $label }}">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight" value="{{ old('weight', $record->weight ?? '') }}" class="{{ $input }}" placeholder="Mis: 60">
                    </div>
                    <div>
                        <label class="{{ $label }}">Tinggi Badan (cm)</label>
                        <input type="number" step="0.1" name="height" value="{{ old('height', $record->height ?? '') }}" class="{{ $input }}" placeholder="Mis: 165">
                    </div>
                </div>
            </div>

            <!-- Keluhan Utama -->
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800">Pemeriksaan Subjektif</h3>
                <div>
                    <label class="{{ $label }}">Keluhan Utama <span class="text-red-500">*</span></label>
                    <textarea name="symptoms" rows="3" class="{{ $input }}" placeholder="Catat keluhan yang dirasakan pasien..." required>{{ old('symptoms', $record->symptoms ?? '') }}</textarea>
                </div>
            </div>

            <!-- Diagnosa & Multi ICD-10 -->
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800">Diagnosa Keperawatan (ICD-10)</h3>
                <div class="space-y-3">
                    <div>
                        <label class="{{ $label }}">Pilih ICD-10 (Diagnosa) <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">- Bisa pilih lebih dari 1</span></label>
                        <select name="icd10_ids[]" id="icd10_select" class="w-full" multiple="multiple" required>
                            @foreach($nurseDiagnoses as $d)
                                <option value="{{ $d->icd_id }}" selected>{{ $d->icd->icd_code ?? '' }} - {{ $d->icd->name ?? '' }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Cari berdasarkan kode atau nama penyakit. Diagnosa pertama yang dipilih otomatis menjadi diagnosa utama.</p>
                    </div>

                    <!-- ICD-9 Toggle -->
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" id="has_icd9" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ $nurseProcedures->count() > 0 ? 'checked' : '' }}>
                        <label for="has_icd9" class="text-sm font-medium text-gray-700">Ada Tindakan/Prosedur? (ICD-9)</label>
                    </div>

                    <div id="icd9_container" style="display: {{ $nurseProcedures->count() > 0 ? 'block' : 'none' }};">
                        <label class="{{ $label }}">Pilih ICD-9 (Tindakan) <span class="text-gray-400 font-normal">- Bisa pilih lebih dari 1</span></label>
                        <select name="icd9_ids[]" id="icd9_select" class="w-full" multiple="multiple">
                            @foreach($nurseProcedures as $p)
                                <option value="{{ $p->icd_id }}" selected>{{ $p->icd->icd_code ?? '' }} - {{ $p->icd->name ?? '' }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Cari berdasarkan kode atau nama tindakan (ICD-9)</p>
                    </div>

                    <div>
                        <label class="{{ $label }}">Catatan Diagnosa / Tindakan Perawat</label>
                        <textarea name="diagnosis" rows="2" class="{{ $input }}" placeholder="Catatan tambahan diagnosa keperawatan...">{{ old('diagnosis', $record->diagnosis ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t">
                <a href="{{ route('anamnesis.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Kirim ke Dokter
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle ICD-9
        $('#has_icd9').change(function() {
            if ($(this).is(':checked')) {
                $('#icd9_container').show();
            } else {
                $('#icd9_container').hide();
                $('#icd9_select').val(null).trigger('change');
            }
        });

        // Multi Select2 for ICD-10
        $('#icd10_select').select2({
            placeholder: 'Ketik kode atau nama penyakit (ICD-10)...',
            allowClear: true,
            multiple: true,
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("api.icds.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, category: 10 };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });

        // Multi Select2 for ICD-9
        $('#icd9_select').select2({
            placeholder: 'Ketik kode atau nama tindakan (ICD-9)...',
            allowClear: true,
            multiple: true,
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("api.icds.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term, category: 9 };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });
    });
</script>
@endpush
@endsection
