@extends('admin.layouts.app')

@section('title', 'Form Reservasi Terapi')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Reservasi', 'url' => route('reservations.index')],
    ['label' => 'Formulir Reservasi Terapi'],
]" />

<div class="max-w-3xl mx-auto bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="border-b border-gray-100 pb-5 mb-6">
        <h2 class="text-xl font-bold text-gray-900">📋 Formulir Reservasi Terapi Pasien</h2>
        <p class="text-xs text-gray-500 mt-1">Cukup input nomor HP pasien untuk memuat data dari Master Data Pasien. Jika belum terdaftar, Anda dapat mendaftarkan pasien ke Master Data.</p>
    </div>

    {{-- Notification Message --}}
    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center gap-3">
            <span class="text-lg">✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs flex items-center gap-3">
            <span class="text-lg">⚠️</span>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm" class="space-y-6">
        @csrf

        {{-- SECTION A: IDENTITAS PASIEN (CUKUP INPUT NO HP) --}}
        <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">A</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800">Identitas Pasien (Input Nomor HP)</h3>
                </div>
            </div>

            {{-- Input Nomor HP --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                    Nomor HP / WhatsApp Pasien <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input type="tel" name="phone_number" id="phone_number" 
                               value="{{ old('phone_number', request('phone')) }}" required
                               inputmode="tel"
                               autocomplete="tel"
                               placeholder="Ketik nomor HP (Contoh: 08123456789 atau 85183036722)"
                               class="w-full text-sm font-mono border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 bg-white">
                        <span class="absolute left-3.5 top-2.5 text-gray-400">📱</span>
                        <span id="phoneLoading" class="hidden absolute right-3.5 top-3 text-xs text-blue-600 animate-pulse font-medium">
                            Mengecek Master Data...
                        </span>
                    </div>
                    <button type="button" id="btnSearchPatient"
                            class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-xl transition flex items-center justify-center gap-2 shrink-0 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cek Pasien
                    </button>
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Sistem akan otomatis mengecek nomor HP ini di Master Data Pasien.</p>
                @error('phone_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- STATE 1: PASIEN DITEMUKAN (TAMPIL OTOMATIS) --}}
            <div id="patientFoundCard" class="hidden bg-emerald-50/50 border border-emerald-200 rounded-xl p-4 sm:p-5 transition">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-emerald-200/60 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">✓</span>
                        <span class="text-xs font-bold text-emerald-800 uppercase tracking-wide">Data Pasien Ditemukan di Master Data</span>
                    </div>
                    <span id="disp_code" class="text-[11px] font-mono font-medium text-emerald-700 bg-emerald-100/70 px-2.5 py-0.5 rounded-full"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 text-xs">
                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Nama Lengkap</span>
                        <strong id="disp_full_name" class="text-sm font-bold text-gray-900 block mt-0.5">-</strong>
                    </div>

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Usia</span>
                        <strong id="disp_age" class="text-sm font-bold text-gray-900 block mt-0.5">-</strong>
                    </div>

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Jenis Kelamin</span>
                        <strong id="disp_gender" class="text-sm font-bold text-gray-900 block mt-0.5">-</strong>
                    </div>

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Pekerjaan</span>
                        <strong id="disp_occupation" class="text-sm font-bold text-gray-900 block mt-0.5">-</strong>
                    </div>

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100 sm:col-span-2">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Alamat Domisili</span>
                        <p id="disp_address" class="text-xs text-gray-800 font-medium mt-0.5">-</p>
                    </div>
                </div>

                <div class="mt-3 flex items-center justify-between text-[11px] pt-2 border-t border-emerald-100 text-emerald-800">
                    <span>Biodata di atas diambil langsung dari Master Data Pasien.</span>
                    <a id="linkEditPatient" href="#" target="_blank" class="text-emerald-700 hover:text-emerald-900 font-semibold underline flex items-center gap-1">
                        ✏️ Edit Pasien di Master Data
                    </a>
                </div>

                {{-- Hidden Inputs to pass to Reservation Controller --}}
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                <input type="hidden" name="full_name" id="full_name" value="{{ old('full_name') }}">
                <input type="hidden" name="age" id="age" value="{{ old('age') }}">
                <input type="hidden" name="gender" id="gender" value="{{ old('gender') }}">
                <input type="hidden" name="occupation" id="occupation" value="{{ old('occupation') }}">
                <input type="hidden" name="address" id="address" value="{{ old('address') }}">
            </div>

            {{-- STATE 2: NOMOR TIDAK DITEMUKAN (TOMBOL KE MASTER DATA PASIEN) --}}
            <div id="patientNotFoundCard" class="hidden bg-amber-50 border border-amber-200 rounded-xl p-5 text-xs text-amber-900">
                <div class="flex items-start gap-3">
                    <span class="text-2xl leading-none">⚠️</span>
                    <div class="space-y-2 flex-1">
                        <div>
                            <h4 class="font-bold text-sm text-amber-900">Nomor HP Tidak Ditemukan di Master Data Pasien!</h4>
                            <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                Pasien dengan nomor HP <span id="notFoundPhoneDisp" class="font-mono font-bold text-amber-950 bg-amber-100 px-1.5 py-0.5 rounded"></span> belum terdaftar di Master Data Pasien.
                                Silakan daftarkan data pasien terlebih dahulu di Master Data untuk melanjutkan pembuatan reservasi terapi.
                            </p>
                        </div>
                        <div class="pt-2">
                            <a id="btnGoToMasterPatient" href="{{ route('patients.create') }}" 
                               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-blue-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                                Input Data Pasien di Master Data Pasien
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATE 3: BELUM MENGISI NOMOR HP --}}
            <div id="patientInitialPrompt" class="bg-blue-50/70 border border-blue-100 rounded-xl p-4 text-xs text-blue-800 flex items-center gap-3">
                <span class="text-lg">💡</span>
                <div>
                    Cukup ketik nomor HP pasien di atas. Jika pasien sudah pernah berobat/terdaftar, biodata akan langsung dimuat secara otomatis.
                </div>
            </div>
        </div>

        {{-- SECTION B: KELUHAN & RENCANA JADWAL TERAPI --}}
        <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">B</span>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800">Keluhan Cedera & Rencana Jadwal Terapi</h3>
            </div>

            {{-- 7. Keluhan Utama --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    7. Keluhan Utama Cedera / Gejala *
                </label>
                <textarea name="chief_complaint" id="chief_complaint" rows="3" required 
                          placeholder="Jelaskan cedera atau keluhan nyeri yang dirasakan secara spesifik (contoh: Nyeri sendi lutut kanan saat berlari)..."
                          class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">{{ old('chief_complaint') }}</textarea>
                @error('chief_complaint') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- 8. Sudah berapa lama keluhan dirasakan --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        8. Berapa Lama Keluhan Dirasakan *
                    </label>
                    <input type="text" name="complaint_duration" id="complaint_duration" value="{{ old('complaint_duration') }}" required
                           placeholder="Contoh: 3 hari, 2 minggu, 1 bulan"
                           class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    @error('complaint_duration') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- 9. Riwayat penyakit (jika ada) --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                        9. Riwayat Cedera / Medis (Jika Ada)
                    </label>
                    <input type="text" name="medical_history" id="medical_history" value="{{ old('medical_history') }}"
                           placeholder="Pernah ACL, operasi lutut, dll (opsional)"
                           class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
            </div>

            {{-- 10. Hari dan jam yang diinginkan untuk terapi (Date & Time Picker Interaktif) --}}
            <div class="bg-white p-4 sm:p-5 rounded-2xl border border-blue-200 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 border-b border-gray-100 pb-2.5">
                    <label class="block text-xs font-bold uppercase text-gray-800 flex items-center gap-1.5">
                        <span class="text-blue-600">📅</span> 10. Hari & Jam yang Diinginkan *
                    </label>
                    <span class="text-[11px] text-blue-600 font-medium bg-blue-50 px-2 py-0.5 rounded-md">
                        Klik kolom atau tombol jam di bawah untuk memilih
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Datepicker Section --}}
                    <div>
                        <label for="preferred_date" class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                            <span>🗓️</span> Tanggal / Hari Kedatangan *
                        </label>
                        <div class="relative">
                            <input type="text" name="preferred_date" id="preferred_date"
                                   placeholder="Pilih tanggal kedatangan..."
                                   value="{{ old('preferred_date', date('Y-m-d')) }}" required readonly
                                   class="w-full text-sm font-semibold text-gray-800 border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer pl-10 shadow-xs">
                            <span class="absolute left-3.5 top-2.5 text-gray-400 pointer-events-none">📅</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Klik kolom untuk membuka kalender.</p>
                        
                        {{-- Quick Date Chips --}}
                        <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                            <span class="text-[10px] uppercase font-bold text-gray-400">Pintas:</span>
                            <button type="button" class="quick-date-btn text-[11px] px-2.5 py-1 rounded-lg bg-blue-600 text-white font-medium transition cursor-pointer" data-days="0">Hari Ini</button>
                            <button type="button" class="quick-date-btn text-[11px] px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-medium transition cursor-pointer" data-days="1">Besok</button>
                            <button type="button" class="quick-date-btn text-[11px] px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-blue-50 text-gray-700 hover:text-blue-700 font-medium transition cursor-pointer" data-days="2">Lusa</button>
                        </div>
                        @error('preferred_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Timepicker Section --}}
                    <div>
                        <label for="preferred_time" class="block text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                            <span>⏰</span> Jam / Waktu Terapi *
                        </label>
                        <div class="relative">
                            <input type="text" name="preferred_time" id="preferred_time"
                                   placeholder="Pilih jam kedatangan..."
                                   value="{{ old('preferred_time', '09:00') }}" required readonly
                                   class="w-full text-sm font-semibold text-gray-800 border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer pl-10 shadow-xs">
                            <span class="absolute left-3.5 top-2.5 text-gray-400 pointer-events-none">⏰</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Klik untuk pilih jam, atau klik opsi di bawah:</p>

                        {{-- Quick Time Chips --}}
                        <div class="mt-2.5 space-y-1.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] text-gray-400 font-bold uppercase shrink-0">Pagi:</span>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="08:00">08:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-blue-600 bg-blue-600 text-white font-bold transition cursor-pointer" data-time="09:00">09:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="10:00">10:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="11:00">11:00</button>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[10px] text-gray-400 font-bold uppercase shrink-0">Siang/Sore:</span>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="13:00">13:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="14:00">14:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="15:00">15:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="16:00">16:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="17:00">17:00</button>
                                <button type="button" class="quick-time-btn text-[11px] px-2.5 py-1 rounded-lg border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 font-medium transition cursor-pointer" data-time="19:00">19:00</button>
                            </div>
                        </div>
                        @error('preferred_time') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Live Schedule Preview Box --}}
                <div class="flex items-center gap-3 p-3 bg-gradient-to-r from-blue-50 to-indigo-50/60 border border-blue-100 rounded-xl text-xs text-blue-900">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm font-bold shrink-0 shadow-xs">
                        ✓
                    </div>
                    <div class="flex-1">
                        <span class="text-[10px] uppercase font-bold text-blue-600 block tracking-wider">Jadwal Rencana Kedatangan Terpilih:</span>
                        <strong id="previewScheduleText" class="font-extrabold text-blue-950 text-sm block mt-0.5">-</strong>
                    </div>
                </div>

                <input type="hidden" name="preferred_schedule" id="preferred_schedule" value="{{ old('preferred_schedule') }}">
                @error('preferred_schedule') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pilihan Terapis (Opsional) --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    Pilihan Terapis (Opsional)
                </label>
                <select name="therapist_id" id="therapist_id" 
                        class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">-- Ditentukan oleh Klinik --</option>
                    @foreach($therapists as $t)
                        <option value="{{ $t->id }}" {{ old('therapist_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->full_name }} ({{ $t->specialization ?: 'Terapis' }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-gray-500 mt-1">Dapat dikosongkan jika pasien belum menentukan terapis spesifik.</p>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('reservations.index') }}" class="px-4 py-2.5 text-xs font-medium text-gray-600 hover:text-gray-800">
                ← Kembali ke Daftar Reservasi
            </a>
            <button type="submit" id="btnSubmitReservation" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md shadow-blue-200 transition flex items-center gap-2">
                <span>Kirim Data Reservasi</span>
                <span>→</span>
            </button>
        </div>
    </form>
</div>

{{-- Auto-complete Patient Lookup Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone_number');
    const searchBtn = document.getElementById('btnSearchPatient');
    const loading = document.getElementById('phoneLoading');
    
    const cardFound = document.getElementById('patientFoundCard');
    const cardNotFound = document.getElementById('patientNotFoundCard');
    const cardInitial = document.getElementById('patientInitialPrompt');
    
    const notFoundPhoneDisp = document.getElementById('notFoundPhoneDisp');
    const btnGoToMasterPatient = document.getElementById('btnGoToMasterPatient');
    const linkEditPatient = document.getElementById('linkEditPatient');
    
    // Display elements in Found Card
    const dispCode = document.getElementById('disp_code');
    const dispFullName = document.getElementById('disp_full_name');
    const dispAge = document.getElementById('disp_age');
    const dispGender = document.getElementById('disp_gender');
    const dispOccupation = document.getElementById('disp_occupation');
    const dispAddress = document.getElementById('disp_address');

    // Hidden form inputs
    const inputPatientId = document.getElementById('patient_id');
    const inputFullName = document.getElementById('full_name');
    const inputAge = document.getElementById('age');
    const inputGender = document.getElementById('gender');
    const inputOccupation = document.getElementById('occupation');
    const inputAddress = document.getElementById('address');

    let debounceTimer;

    function resetCards() {
        cardFound.classList.add('hidden');
        cardNotFound.classList.add('hidden');
        cardInitial.classList.remove('hidden');
        inputPatientId.value = '';
    }

    function checkPatient(phone) {
        const trimmed = (phone || '').trim();
        if (!trimmed || trimmed.length < 5) {
            resetCards();
            return;
        }

        loading.classList.remove('hidden');

        fetch(`{{ route('api.patients.lookup') }}?phone=${encodeURIComponent(trimmed)}`)
            .then(res => res.json())
            .then(data => {
                loading.classList.add('hidden');
                cardInitial.classList.add('hidden');

                if (data.found && data.patient) {
                    // Populate display
                    cardNotFound.classList.add('hidden');
                    cardFound.classList.remove('hidden');

                    dispCode.textContent = data.patient.patient_code ? 'RM: ' + data.patient.patient_code.substring(0, 8) : 'Pasien Terdaftar';
                    dispFullName.textContent = data.patient.full_name || '-';
                    dispAge.textContent = (data.patient.age ? data.patient.age + ' Tahun' : '-');
                    dispGender.textContent = data.patient.gender_label || (data.patient.gender === 'male' ? 'Laki-laki' : 'Perempuan');
                    dispOccupation.textContent = data.patient.occupation || '-';
                    dispAddress.textContent = data.patient.address || '-';

                    // Set hidden inputs
                    inputPatientId.value = data.patient.id || '';
                    inputFullName.value = data.patient.full_name || '';
                    inputAge.value = data.patient.age || '';
                    inputGender.value = data.patient.gender || '';
                    inputOccupation.value = data.patient.occupation || '';
                    inputAddress.value = data.patient.address || '';

                    // Edit link
                    linkEditPatient.href = `/patients/${data.patient.id}/edit`;
                } else {
                    // Patient not found
                    cardFound.classList.add('hidden');
                    cardNotFound.classList.remove('hidden');
                    inputPatientId.value = '';

                    notFoundPhoneDisp.textContent = trimmed;
                    const createUrl = `{{ route('patients.create') }}?phone=${encodeURIComponent(trimmed)}&from=reservation`;
                    btnGoToMasterPatient.href = createUrl;
                }
            })
            .catch(() => {
                loading.classList.add('hidden');
            });
    }

    // Event: Input typing debounced
    phoneInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const val = this.value.trim();

        if (val.length >= 8) {
            debounceTimer = setTimeout(() => {
                checkPatient(val);
            }, 450);
        } else {
            resetCards();
        }
    });

    // Event: Click Search Button
    searchBtn.addEventListener('click', function(e) {
        e.preventDefault();
        checkPatient(phoneInput.value);
    });

    // Event: Enter key on phone input triggers search instead of early form submission
    phoneInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            checkPatient(this.value);
        }
    });

    // Validation on form submission: check if patient is verified
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        if (!inputPatientId.value) {
            e.preventDefault();
            alert('Silakan masukkan nomor HP pasien yang terdaftar di Master Data atau daftarkan pasien baru terlebih dahulu melalui tombol yang tersedia.');
            phoneInput.focus();
        }
    });

    // Auto-check on load if phone parameter is present
    const initialPhone = phoneInput.value.trim();
    if (initialPhone && initialPhone.length >= 6) {
        checkPatient(initialPhone);
    }

    // Elements untuk Schedule Picker
    const prefDateInput = document.getElementById('preferred_date');
    const prefTimeInput = document.getElementById('preferred_time');
    const prefSchedInput = document.getElementById('preferred_schedule');
    const previewSchedText = document.getElementById('previewScheduleText');

    let fpDate = null;
    let fpTime = null;

    function syncSchedulePreview(overrideDate, overrideTime) {
        let dateVal = overrideDate || (prefDateInput ? prefDateInput.value : '');
        let timeVal = overrideTime || (prefTimeInput && prefTimeInput.value ? prefTimeInput.value : '09:00');

        if (!dateVal) {
            if (previewSchedText) previewSchedText.textContent = 'Silakan pilih tanggal dan jam kedatangan.';
            return;
        }

        try {
            const [year, month, day] = dateVal.split('-').map(Number);
            const dateObj = new Date(year, month - 1, day);
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = new Intl.DateTimeFormat('id-ID', options).format(dateObj);
            const scheduleString = `${formattedDate} - Pukul ${timeVal} WIB`;

            if (prefSchedInput) prefSchedInput.value = scheduleString;
            if (previewSchedText) previewSchedText.textContent = scheduleString;
        } catch (e) {
            const fallback = `${dateVal} - Pukul ${timeVal} WIB`;
            if (prefSchedInput) prefSchedInput.value = fallback;
            if (previewSchedText) previewSchedText.textContent = fallback;
        }
    }

    function highlightSelectedTimeChip(timeStr) {
        document.querySelectorAll('.quick-time-btn').forEach(btn => {
            if (btn.getAttribute('data-time') === timeStr) {
                btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600', 'font-bold');
                btn.classList.remove('border-gray-200', 'text-gray-700', 'hover:bg-blue-50');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600', 'font-bold');
                btn.classList.add('border-gray-200', 'text-gray-700', 'hover:bg-blue-50');
            }
        });
    }

    function initFlatpickr() {
        const pickerLib = window.flatpickr || (typeof flatpickr !== 'undefined' ? flatpickr : null);
        if (pickerLib) {
            fpDate = pickerLib(prefDateInput, {
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "l, j F Y",
                minDate: "today",
                defaultDate: prefDateInput.value || "today",
                disableMobile: true,
                onChange: function(selectedDates, dateStr) {
                    syncSchedulePreview(dateStr, prefTimeInput.value);
                }
            });

            fpTime = pickerLib(prefTimeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                defaultDate: prefTimeInput.value || "09:00",
                minuteIncrement: 15,
                disableMobile: true,
                onChange: function(selectedDates, timeStr) {
                    highlightSelectedTimeChip(timeStr);
                    syncSchedulePreview(prefDateInput.value, timeStr);
                }
            });
        }
    }

    // Initialize flatpickr on load
    initFlatpickr();

    // Event listener untuk tombol tanggal cepat (Hari Ini, Besok, Lusa)
    document.querySelectorAll('.quick-date-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const daysToAdd = parseInt(this.getAttribute('data-days') || '0', 10);
            const targetDate = new Date();
            targetDate.setDate(targetDate.getDate() + daysToAdd);
            
            const yyyy = targetDate.getFullYear();
            const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            const dd = String(targetDate.getDate()).padStart(2, '0');
            const formattedYmd = `${yyyy}-${mm}-${dd}`;

            if (fpDate) {
                fpDate.setDate(formattedYmd, true);
            } else {
                prefDateInput.value = formattedYmd;
                syncSchedulePreview();
            }

            document.querySelectorAll('.quick-date-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            this.classList.remove('bg-gray-100', 'text-gray-700');
            this.classList.add('bg-blue-600', 'text-white');
        });
    });

    // Event listener untuk tombol jam cepat (08:00, 09:00, 10:00, dll)
    document.querySelectorAll('.quick-time-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const timeVal = this.getAttribute('data-time');
            if (fpTime) {
                fpTime.setDate(timeVal, true);
            } else {
                prefTimeInput.value = timeVal;
                syncSchedulePreview();
            }
            highlightSelectedTimeChip(timeVal);
        });
    });

    // Native click fallback
    if (prefDateInput) {
        prefDateInput.addEventListener('click', () => {
            if (fpDate) fpDate.open();
            else if (prefDateInput.showPicker) prefDateInput.showPicker();
        });
        prefDateInput.addEventListener('change', () => syncSchedulePreview());
    }

    if (prefTimeInput) {
        prefTimeInput.addEventListener('click', () => {
            if (fpTime) fpTime.open();
            else if (prefTimeInput.showPicker) prefTimeInput.showPicker();
        });
        prefTimeInput.addEventListener('change', () => syncSchedulePreview());
    }

    // Initial sync
    syncSchedulePreview();
    if (prefTimeInput && prefTimeInput.value) {
        highlightSelectedTimeChip(prefTimeInput.value);
    }
});
</script>

{{-- Flatpickr CSS & JS (Local + CDN fallback) --}}
<link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/flatpickr/themes/airbnb.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('vendor/flatpickr/l10n/id.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>

@endsection
