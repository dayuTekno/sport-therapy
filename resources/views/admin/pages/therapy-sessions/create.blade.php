@extends('admin.layouts.app')

@section('title', 'Tambah Sesi Terapi Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Sesi Terapi Pasien', 'url' => route('therapy-sessions.index')],
    ['label' => 'Tambah Sesi Terapi', 'url' => ''],
]" />

<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
            <span>➕</span> Tambah Sesi Terapi Pasien
        </h1>
        <p class="text-xs text-gray-500 mt-1">
            Jadwalkan tahapan terapi berjenjang untuk pasien. Anda dapat menjadwalkan lebih dari satu sesi terapi dalam 1 hari yang sama.
        </p>
    </div>

    <a href="{{ route('therapy-sessions.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
        ← Kembali ke Daftar Sesi
    </a>
</div>

@include('admin.partials.alert')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl">
    <form action="{{ route('therapy-sessions.store') }}" method="POST" class="space-y-6" id="therapySessionForm">
        @csrf

        @if($selectedReservation)
            <input type="hidden" name="reservation_id" value="{{ $selectedReservation->id }}">
            <div class="bg-blue-50/70 p-4 rounded-xl border border-blue-100 text-xs text-blue-900 flex items-center justify-between">
                <div>
                    <span class="font-bold">Terkait Reservasi:</span>
                    <span class="font-mono font-bold">{{ $selectedReservation->reservation_code }}</span>
                    <span class="text-blue-700 block mt-0.5">Keluhan: {{ $selectedReservation->chief_complaint }}</span>
                </div>
                <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-lg font-semibold text-[11px]">
                    Dari Booking
                </span>
            </div>
        @endif

        {{-- SECTION 1: PILIH PASIEN --}}
        <div class="space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">1</span>
                <span>Pilih Pasien Terdaftar</span>
            </h3>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        Nama Pasien / No. HP *
                    </label>
                    <select name="patient_id" id="patient_id" required 
                            class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Pasien Terdaftar --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" 
                                    data-phone="{{ $p->phone_number }}"
                                    data-age="{{ $p->age }}"
                                    data-gender="{{ $p->gender }}"
                                    {{ (old('patient_id', $selectedPatient?->id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->full_name }} — {{ $p->phone_number }} (Usia: {{ $p->age ?? '-' }} thn)
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Info Ringkas Pasien & Deteksi Status Terapi Terakhir --}}
            <div id="patientInfoBox" class="p-4 rounded-xl border border-gray-100 bg-gray-50/70 text-xs text-gray-700 space-y-2 {{ $selectedPatient ? '' : 'hidden' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-gray-400">Ringkasan Pasien Terpilih</span>
                        <h4 id="dispPatientName" class="font-bold text-gray-900 text-sm">{{ $selectedPatient?->full_name }}</h4>
                    </div>
                    <div id="dispPatientStatusBadge" class="text-right">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-lg font-bold border border-indigo-200">
                            Deteksi Alur Terapi Aktif
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-gray-200/60 text-[11px]">
                    <div>
                        <span class="text-gray-400 block">No HP / WhatsApp</span>
                        <strong id="dispPatientPhone" class="font-mono text-blue-600">{{ $selectedPatient?->phone_number ?? '-' }}</strong>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Usia & JK</span>
                        <span id="dispPatientAgeGender" class="font-medium text-gray-800">
                            {{ $selectedPatient?->age ? $selectedPatient->age . ' Thn' : '-' }} ({{ $selectedPatient?->gender == 'male' ? 'L' : 'P' }})
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Sesi Tercatat Hari Ini</span>
                        <span id="dispSessionsTodayCount" class="font-bold text-purple-700">
                            {{ $suggestedDailyOrder - 1 }} Sesi
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block">Rekomendasi Tahap</span>
                        <span id="dispSuggestedStage" class="font-bold text-emerald-700">
                            Tahap {{ $nextStageSuggestion }} ({{ chr(64 + $nextStageSuggestion) }})
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: JADWAL & MULTI-SESI HARIAN --}}
        <div class="space-y-4 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">2</span>
                <span>Waktu Sesi & Pengaturan Multi-Sesi Harian</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tanggal & Jam Sesi --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        Jadwal / Jam Sesi Terapi *
                    </label>
                    <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                           value="{{ old('scheduled_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
                    @error('scheduled_at') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Nomor Urut Sesi Hari Ini (>1 Terapi per Hari) --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1 flex items-center justify-between">
                        <span>Urutan Sesi Hari Ini *</span>
                        <span class="text-[10px] text-purple-700 font-semibold bg-purple-50 px-2 py-0.5 rounded">
                            Bisa &gt;1 Sesi / Hari
                        </span>
                    </label>
                    <div class="flex items-center gap-2">
                        <select name="daily_session_order" id="daily_session_order" required
                                class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="1" {{ old('daily_session_order', $suggestedDailyOrder) == 1 ? 'selected' : '' }}>
                                Sesi Ke-1 (Utama / Sesi Pagi)
                            </option>
                            <option value="2" {{ old('daily_session_order', $suggestedDailyOrder) == 2 ? 'selected' : '' }}>
                                Sesi Ke-2 (Sesi Lanjutan / Sesi Sore)
                            </option>
                            <option value="3" {{ old('daily_session_order', $suggestedDailyOrder) == 3 ? 'selected' : '' }}>
                                Sesi Ke-3 (Terapi Khusus / Intensif)
                            </option>
                            <option value="4" {{ old('daily_session_order', $suggestedDailyOrder) == 4 ? 'selected' : '' }}>
                                Sesi Ke-4 (Tambahan)
                            </option>
                        </select>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">
                        Pilih urutan sesi jika pasien menjalani lebih dari 1 program terapi pada hari yang sama.
                    </p>
                </div>
            </div>
        </div>

        {{-- SECTION 3: TAHAPAN TERAPI BERJENJANG --}}
        <div class="space-y-4 pt-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-2 pb-2 border-b border-gray-100">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-600 text-[10px] font-bold text-white">3</span>
                <span>Tahapan Terapi Berjenjang</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Master Jenjang Terapi --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        Pilih Jenjang Terapi *
                    </label>
                    <select name="therapy_type_id" id="therapy_type_id" required 
                            class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Pilih Jenjang Terapi --</option>
                        @foreach($therapyTypes as $type)
                            <option value="{{ $type->id }}" 
                                    data-stage="{{ $type->stage_order }}"
                                    data-name="{{ $type->name }}"
                                    {{ old('therapy_type_id', $nextStageSuggestion == $type->stage_order ? $type->id : '') == $type->id ? 'selected' : '' }}>
                                Tahap {{ $type->stage_order }} ({{ chr(64 + $type->stage_order) }}): {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Terapis Penanggung Jawab --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        Terapis Penanggung Jawab
                    </label>
                    <select name="therapist_id" id="therapist_id" 
                            class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">-- Ditentukan oleh Klinik --</option>
                        @foreach($therapists as $t)
                            <option value="{{ $t->id }}" {{ old('therapist_id', $selectedReservation?->therapist_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->full_name }} ({{ $t->specialization ?: 'Terapis' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Hidden inputs for stage metadata --}}
            <input type="hidden" name="stage_number" id="stage_number" value="{{ old('stage_number', $nextStageSuggestion) }}">
            <input type="hidden" name="stage_name" id="stage_name" value="{{ old('stage_name', 'Terapi Tahap ' . chr(64 + $nextStageSuggestion)) }}">

            {{-- Catatan / Keluhan Sesi --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    Catatan Sesi / Keluhan Khusus Pasien (Opsional)
                </label>
                <textarea name="session_notes" id="session_notes" rows="3" 
                          placeholder="Masukkan instruksi khusus atau keluhan pasien untuk sesi terapi ini..."
                          class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">{{ old('session_notes', $selectedReservation?->chief_complaint) }}</textarea>
            </div>
        </div>

        {{-- BUTTONS --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('therapy-sessions.index') }}" class="px-4 py-2.5 text-xs font-medium text-gray-600 hover:text-gray-800">
                ← Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-200 transition flex items-center gap-2">
                <span>Simpan & Buka Sesi Terapi</span>
                <span>→</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientSelect = document.getElementById('patient_id');
    const scheduledAtInput = document.getElementById('scheduled_at');
    const dailyOrderSelect = document.getElementById('daily_session_order');
    const therapyTypeSelect = document.getElementById('therapy_type_id');
    const stageNumberInput = document.getElementById('stage_number');
    const stageNameInput = document.getElementById('stage_name');

    const patientInfoBox = document.getElementById('patientInfoBox');
    const dispPatientName = document.getElementById('dispPatientName');
    const dispPatientPhone = document.getElementById('dispPatientPhone');
    const dispPatientAgeGender = document.getElementById('dispPatientAgeGender');
    const dispSessionsTodayCount = document.getElementById('dispSessionsTodayCount');
    const dispSuggestedStage = document.getElementById('dispSuggestedStage');

    // Sync Stage Name & Number when Therapy Type is selected
    therapyTypeSelect.addEventListener('change', function() {
        const selectedOpt = therapyTypeSelect.options[therapyTypeSelect.selectedIndex];
        if (selectedOpt && selectedOpt.dataset.stage) {
            stageNumberInput.value = selectedOpt.dataset.stage;
            stageNameInput.value = selectedOpt.dataset.name;
        }
    });

    // Handle patient selection change
    patientSelect.addEventListener('change', function() {
        const patientId = this.value;
        if (!patientId) {
            patientInfoBox.classList.add('hidden');
            return;
        }

        const selectedOpt = this.options[this.selectedIndex];
        dispPatientName.textContent = selectedOpt.text.split('—')[0].trim();
        dispPatientPhone.textContent = selectedOpt.dataset.phone || '-';
        dispPatientAgeGender.textContent = (selectedOpt.dataset.age ? selectedOpt.dataset.age + ' Thn' : '-') + 
            ' (' + (selectedOpt.dataset.gender === 'female' ? 'P' : 'L') + ')';
        patientInfoBox.classList.remove('hidden');

        // Fetch patient stage & today session count via API
        const targetDate = scheduledAtInput.value ? scheduledAtInput.value.split('T')[0] : '';
        fetch(`{{ url('api/patients') }}/${patientId}/last-stage?date=${targetDate}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    dispSessionsTodayCount.textContent = data.sessions_today_count + ' Sesi';
                    dispSuggestedStage.textContent = `Tahap ${data.suggested_stage} (${String.fromCharCode(64 + data.suggested_stage)})`;

                    // Set daily session order
                    dailyOrderSelect.value = data.next_daily_order > 4 ? 4 : data.next_daily_order;

                    // Match suggested therapy type if not yet manually altered
                    Array.from(therapyTypeSelect.options).forEach(opt => {
                        if (parseInt(opt.dataset.stage) === data.suggested_stage) {
                            opt.selected = true;
                            stageNumberInput.value = opt.dataset.stage;
                            stageNameInput.value = opt.dataset.name;
                        }
                    });
                }
            })
            .catch(err => console.error(err));
    });

    // Trigger initial therapy type sync
    if (therapyTypeSelect.value) {
        therapyTypeSelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection
