@extends('admin.layouts.app')

@section('title', 'Pemeriksaan Dokter')

@section('content')

@php
    $label = 'mb-1 block text-sm font-medium text-gray-700';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Amnesa Dokter', 'url' => route('doctor-exam.index')],
    ['label' => 'Pemeriksaan'],
]" />

<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pemeriksaan Dokter</h1>
        <p class="text-sm text-gray-500">No. Antrian: <span class="font-bold text-blue-600">{{ $queue->queue_code }}</span> | Poli: {{ $queue->poly->name ?? '-' }} | Dokter: {{ $queue->doctor->full_name ?? '-' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- KOLOM KIRI: Info Pasien + Hasil Amnesa Perawat --}}
    <div class="lg:col-span-1 space-y-4">
        {{-- Info Pasien --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
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

        {{-- Hasil Amnesa Perawat --}}
        <div class="bg-white rounded-xl shadow-sm border border-green-200 p-5">
            <h3 class="font-semibold text-green-800 mb-4 border-b border-green-100 pb-2 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Hasil Amnesa Perawat
            </h3>

            {{-- TTV --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Tanda-tanda Vital</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Tekanan Darah</p>
                        <p class="font-medium text-gray-800">{{ $record->systolic ?? '-' }}/{{ $record->diastolic ?? '-' }} mmHg</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Suhu</p>
                        <p class="font-medium text-gray-800">{{ $record->temperature ?? '-' }} °C</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Nadi</p>
                        <p class="font-medium text-gray-800">{{ $record->heart_rate ?? '-' }} x/mnt</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Pernapasan</p>
                        <p class="font-medium text-gray-800">{{ $record->respiratory_rate ?? '-' }} x/mnt</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Berat Badan</p>
                        <p class="font-medium text-gray-800">{{ $record->weight ?? '-' }} kg</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-2">
                        <p class="text-gray-500 text-xs">Tinggi Badan</p>
                        <p class="font-medium text-gray-800">{{ $record->height ?? '-' }} cm</p>
                    </div>
                </div>
            </div>

            {{-- Keluhan --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Keluhan Pasien</p>
                <p class="text-sm text-gray-800 bg-gray-50 rounded-lg p-3">{{ $record->symptoms ?? '-' }}</p>
            </div>

            {{-- ICD-10 Perawat --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">ICD-10 Diagnosa Perawat</p>
                @if($nurseDiagnoses->count() > 0)
                    <div class="space-y-1">
                        @foreach($nurseDiagnoses as $d)
                            <div class="flex items-center gap-2">
                                @if($d->type == 'primary')
                                    <span class="px-1.5 py-0.5 bg-blue-600 text-white rounded text-[10px] font-bold">UTAMA</span>
                                @else
                                    <span class="px-1.5 py-0.5 bg-gray-300 text-gray-700 rounded text-[10px] font-bold">SEK</span>
                                @endif
                                <span class="text-sm text-gray-800">{{ $d->icd->icd_code ?? '' }} - {{ $d->icd->name ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">-</p>
                @endif
            </div>

            {{-- ICD-9 Perawat --}}
            @if($nurseProcedures->count() > 0)
            <div class="mb-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">ICD-9 Tindakan Perawat</p>
                <div class="space-y-1">
                    @foreach($nurseProcedures as $p)
                        <span class="inline-block px-2 py-1 bg-purple-50 text-purple-700 rounded text-xs font-medium mr-1 mb-1">{{ $p->icd->icd_code ?? '' }} - {{ $p->icd->name ?? '' }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if($record->diagnosis)
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Catatan Perawat</p>
                <p class="text-sm text-gray-800 bg-gray-50 rounded-lg p-3">{{ $record->diagnosis }}</p>
            </div>
            @endif

            <div class="mt-3 text-xs text-gray-400">
                Diperiksa oleh: {{ $record->nurse_name ?? '-' }} | {{ $record->updated_at->format('d M Y H:i') }}
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Form Pemeriksaan Dokter --}}
    <div class="lg:col-span-2">
        <form action="{{ route('doctor-exam.store', $queue->id) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6" id="doctorForm">
            @csrf

            {{-- STEP 1: ICD-10 Dokter (Multi) --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">1</span>
                    Diagnosa Dokter (ICD-10) <span class="text-red-500">*</span>
                </h3>

                <div>
                    <label class="{{ $label }}">Pilih ICD-10 <span class="text-red-500">*</span> <span class="text-gray-400 font-normal">- Bisa pilih lebih dari 1</span></label>
                    <select name="doctor_icd10_ids[]" id="doctor_icd10_select" class="w-full" multiple="multiple" required>
                        @if($doctorDiagnoses->count() > 0)
                            @foreach($doctorDiagnoses as $d)
                                <option value="{{ $d->icd_id }}" selected>{{ $d->icd->icd_code ?? '' }} - {{ $d->icd->name ?? '' }}</option>
                            @endforeach
                        @else
                            @foreach($nurseDiagnoses as $d)
                                <option value="{{ $d->icd_id }}" selected>{{ $d->icd->icd_code ?? '' }} - {{ $d->icd->name ?? '' }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Diagnosa pertama = Diagnosa Utama. Sudah diisi dari hasil perawat, bisa diubah/ditambah.</p>
                </div>
            </div>

            {{-- STEP 2: ICD-9 Dokter (Multi) --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">2</span>
                    Prosedur Medis (ICD-9)
                </h3>

                <div class="flex items-center gap-2 mb-4">
                    <input type="checkbox" id="doctor_has_icd9" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        {{ ($doctorProcedures->count() > 0 || $nurseProcedures->count() > 0) ? 'checked' : '' }}>
                    <label for="doctor_has_icd9" class="text-sm font-medium text-gray-700">Perlu ICD-9? (Ada Prosedur Medis?)</label>
                </div>

                <div id="doctor_icd9_container" style="display: {{ ($doctorProcedures->count() > 0 || $nurseProcedures->count() > 0) ? 'block' : 'none' }};">
                    <label class="{{ $label }}">Pilih ICD-9 <span class="text-gray-400 font-normal">- Bisa pilih lebih dari 1</span></label>
                    <select name="doctor_icd9_ids[]" id="doctor_icd9_select" class="w-full" multiple="multiple">
                        @if($doctorProcedures->count() > 0)
                            @foreach($doctorProcedures as $p)
                                <option value="{{ $p->icd_id }}" selected>{{ $p->icd->icd_code ?? '' }} - {{ $p->icd->name ?? '' }}</option>
                            @endforeach
                        @else
                            @foreach($nurseProcedures as $p)
                                <option value="{{ $p->icd_id }}" selected>{{ $p->icd->icd_code ?? '' }} - {{ $p->icd->name ?? '' }}</option>
                            @endforeach
                        @endif
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Cari berdasarkan kode atau nama prosedur (ICD-9)</p>
                </div>
            </div>

            {{-- STEP 3: Anamnesa Dokter --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">3</span>
                    Anamnesa & Diagnosa Dokter
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="{{ $label }}">Diagnosa Dokter <span class="text-red-500">*</span></label>
                        <textarea name="doctor_diagnosis" rows="3" class="{{ $input }}" placeholder="Tuliskan hasil diagnosa dokter..." required>{{ old('doctor_diagnosis', $record->doctor_diagnosis ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="{{ $label }}">Catatan Pemeriksaan Dokter</label>
                        <textarea name="doctor_notes" rows="2" class="{{ $input }}" placeholder="Catatan tambahan: riwayat penyakit, pemeriksaan fisik, dll...">{{ old('doctor_notes', $record->doctor_notes ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- STEP 4: Tindakan dari Master Prosedur (Multi-select) --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">4</span>
                    Tindakan Medis
                </h3>

                <div>
                    <label class="{{ $label }}">Pilih Tindakan dari Master Data <span class="text-gray-400 font-normal">- Bisa pilih lebih dari 1</span></label>
                    <select name="treatment_ids[]" id="treatment_select" class="w-full" multiple="multiple">
                        @foreach($treatments as $t)
                            <option value="{{ $t->procedure_id }}" selected>{{ $t->procedure->procedure_code ?? '' }} - {{ $t->procedure->name ?? '' }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Cari berdasarkan kode atau nama tindakan dari master data prosedur</p>
                </div>
            </div>

            {{-- STEP 5: Resep Obat (Multi-select with Quantity & Instructions) --}}
            <div>
                <h3 class="font-semibold text-gray-800 mb-4 border-b pb-2 text-blue-800 flex items-center gap-2">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">5</span>
                    Resep Obat
                </h3>

                <div class="space-y-4">
                    <div>
                        <label class="{{ $label }}">Cari Obat</label>
                        <select id="medicine_search" class="w-full"></select>
                        <p class="mt-1 text-xs text-gray-500">Cari obat dari master data untuk ditambahkan ke daftar resep</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left border-collapse" id="medicine_table">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="p-2 font-medium text-gray-700">Nama Obat</th>
                                    <th class="p-2 font-medium text-gray-700 w-24">Jumlah</th>
                                    <th class="p-2 font-medium text-gray-700">Aturan Pakai</th>
                                    <th class="p-2 font-medium text-gray-700 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="medicine_list">
                                @forelse($medicines as $m)
                                    <tr class="medicine-row">
                                        <td class="p-2">
                                            <input type="hidden" name="medicine_ids[]" value="{{ $m->medicine_id }}">
                                            <span class="font-medium text-gray-800">{{ $m->medicine->medicine_name }}</span>
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="quantities[]" value="{{ $m->quantity }}" class="{{ $input }} text-center" placeholder="Jml">
                                        </td>
                                        <td class="p-2">
                                            <input type="text" name="instructions[]" value="{{ $m->instructions }}" class="{{ $input }}" placeholder="Contoh: 3 x 1 sesudah makan">
                                        </td>
                                        <td class="p-2 text-right">
                                            <button type="button" class="text-red-500 hover:text-red-700 remove-medicine">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="empty_medicine_msg">
                                        <td colspan="4" class="p-4 text-center text-gray-500 italic">Belum ada obat ditambahkan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-4 flex justify-end gap-3 border-t">
                <a href="{{ route('doctor-exam.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium text-sm">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 font-medium text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan & Selesai
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
{{-- Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Toggle ICD-9
        $('#doctor_has_icd9').change(function() {
            if ($(this).is(':checked')) {
                $('#doctor_icd9_container').show();
            } else {
                $('#doctor_icd9_container').hide();
                $('#doctor_icd9_select').val(null).trigger('change');
            }
        });

        // Multi Select2 for Doctor ICD-10
        $('#doctor_icd10_select').select2({
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

        // Multi Select2 for Doctor ICD-9
        $('#doctor_icd9_select').select2({
            placeholder: 'Ketik kode atau nama prosedur (ICD-9)...',
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

        // Multi Select2 for Treatments (Master Procedures)
        $('#treatment_select').select2({
            placeholder: 'Ketik kode atau nama tindakan...',
            allowClear: true,
            multiple: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.procedures.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { 
                        q: params.term,
                        poly_id: '{{ $queue->poly_id }}'
                    };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        });

        // Medicine Search & Add
        $('#medicine_search').select2({
            placeholder: 'Cari nama obat...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("api.medicines.search") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return { results: data.results };
                },
                cache: true
            }
        }).on('select2:select', function (e) {
            var data = e.params.data;
            addMedicineRow(data.id, data.text);
            $(this).val(null).trigger('change');
        });

        function addMedicineRow(id, name) {
            // Check if already exists
            if ($('input[name="medicine_ids[]"][value="' + id + '"]').length > 0) {
                alert('Obat ini sudah ada dalam daftar');
                return;
            }

            $('#empty_medicine_msg').hide();

            var row = `
                <tr class="medicine-row">
                    <td class="p-2">
                        <input type="hidden" name="medicine_ids[]" value="${id}">
                        <span class="font-medium text-gray-800">${name}</span>
                    </td>
                    <td class="p-2">
                        <input type="text" name="quantities[]" class="{{ $input }} text-center" placeholder="Jml">
                    </td>
                    <td class="p-2">
                        <input type="text" name="instructions[]" class="{{ $input }}" placeholder="Contoh: 3 x 1 sesudah makan">
                    </td>
                    <td class="p-2 text-right">
                        <button type="button" class="text-red-500 hover:text-red-700 remove-medicine">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </td>
                </tr>
            `;
            $('#medicine_list').append(row);
        }

        $(document).on('click', '.remove-medicine', function() {
            $(this).closest('tr').remove();
            if ($('.medicine-row').length === 0) {
                $('#empty_medicine_msg').show();
            }
        });
    });
</script>
@endpush
@endsection
