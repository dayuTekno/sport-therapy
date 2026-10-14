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
        <p class="text-xs text-gray-500 mt-1">Pilih nama pasien dari dropdown Master Data. Pilihan dropdown menampilkan <strong>Nama</strong> dan <strong>Alamat</strong> pasien terdaftar. Jika belum terdaftar, Anda dapat mendaftarkan pasien baru ke Master Data.</p>
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

        {{-- SECTION A: IDENTITAS PASIEN (DROPDOWN CARI NAMA, TAMPIL NAMA & ALAMAT) --}}
        <div class="bg-gray-50/70 p-5 rounded-2xl border border-gray-200/80 space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-xs font-bold text-white">A</span>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-800">Identitas Pasien (Pilih Pasien Terdaftar)</h3>
                </div>
                <a href="{{ route('patients.create', ['from' => 'reservation']) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-xl border border-blue-200 transition">
                    <span>+ Pasien Baru</span>
                </a>
            </div>

            {{-- Searchable Dropdown: Cari Nama, Tampil Nama dan Alamat --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1.5">
                    Pilih Pasien (Cari Berdasarkan Nama) <span class="text-red-500">*</span>
                </label>

                <div class="relative" id="patientComboboxWrapper">
                    {{-- Input Pencarian Pasien --}}
                    <div class="relative">
                        <input type="text" id="patientSearchInput"
                               autocomplete="off"
                               placeholder="🔍 Ketik nama pasien untuk mencari... (Tampil nama & alamat)"
                               class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pl-10 pr-20 bg-white shadow-xs cursor-pointer">
                        <span class="absolute left-3.5 top-2.5 text-gray-400 pointer-events-none">👤</span>
                        
                        {{-- Icon Clear & Chevron --}}
                        <div class="absolute right-3 top-2.5 flex items-center gap-1.5">
                            <button type="button" id="btnClearPatient" class="hidden text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100 transition" title="Bersihkan & Pilih Ulang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <button type="button" id="btnToggleDropdown" class="text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100 transition" title="Buka / Tutup Pilihan">
                                <svg id="dropdownChevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Dropdown Menu List: Menampilkan Nama dan Alamat --}}
                    <div id="patientDropdownMenu" class="hidden absolute z-30 left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-2xl shadow-xl max-h-72 overflow-y-auto divide-y divide-gray-100">
                        <div id="patientDropdownList">
                            {{-- Populated by JS --}}
                        </div>
                        <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-xs sticky bottom-0">
                            <span class="text-gray-500">Pasien tidak ada di daftar?</span>
                            <a href="{{ route('patients.create', ['from' => 'reservation']) }}" class="text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1">
                                <span>+ Daftarkan Pasien Baru</span>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-gray-500 mt-1.5 flex items-center gap-1">
                    <span>💡</span>
                    <span>Pilihan dropdown menampilkan <strong>Nama</strong> dan <strong>Alamat</strong> pasien dari Master Data Pasien.</span>
                </p>
                @error('patient_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- STATE 1: PASIEN DITEMUKAN / TERPILIH --}}
            <div id="patientFoundCard" class="hidden bg-emerald-50/60 border border-emerald-200 rounded-xl p-4 sm:p-5 transition shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-emerald-200/60 pb-3 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">✓</span>
                        <span class="text-xs font-bold text-emerald-800 uppercase tracking-wide">Data Pasien Terpilih dari Master Data</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="disp_code" class="text-[11px] font-mono font-medium text-emerald-700 bg-emerald-100/70 px-2.5 py-0.5 rounded-full"></span>
                        <button type="button" id="btnChangePatient" class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 bg-white border border-emerald-200 px-2.5 py-0.5 rounded-lg hover:bg-emerald-50 transition shadow-2xs">
                            Ganti Pasien
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 text-xs">
                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Nama Lengkap</span>
                        <strong id="disp_full_name" class="text-sm font-bold text-gray-900 block mt-0.5">-</strong>
                    </div>

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100">
                        <span class="text-gray-400 block text-[10px] uppercase font-semibold">Nomor WhatsApp / HP</span>
                        <strong id="disp_phone" class="text-sm font-mono font-bold text-gray-900 block mt-0.5">-</strong>
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

                    <div class="bg-white/80 p-3 rounded-lg border border-emerald-100 sm:col-span-1 md:col-span-1">
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
                <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id', request('patient_id')) }}">
                <input type="hidden" name="phone_number" id="phone_number" value="{{ old('phone_number', request('phone')) }}">
                <input type="hidden" name="full_name" id="full_name" value="{{ old('full_name') }}">
                <input type="hidden" name="age" id="age" value="{{ old('age') }}">
                <input type="hidden" name="gender" id="gender" value="{{ old('gender') }}">
                <input type="hidden" name="occupation" id="occupation" value="{{ old('occupation') }}">
                <input type="hidden" name="address" id="address" value="{{ old('address') }}">
            </div>

            {{-- STATE 2: BELUM MEMILIH PASIEN --}}
            <div id="patientInitialPrompt" class="bg-blue-50/70 border border-blue-100 rounded-xl p-4 text-xs text-blue-800 flex items-center gap-3">
                <span class="text-lg">💡</span>
                <div>
                    Pilih nama pasien dari dropdown di atas. Anda dapat mengetik nama untuk mencari pasien dengan cepat. Pilihan dropdown memuat informasi <strong>Nama</strong> dan <strong>Alamat</strong> pasien.
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
                
                {{-- Warning Banner Bentrok Terapis --}}
                <div id="therapistConflictWarning" class="hidden mt-3 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 flex items-start gap-3 transition">
                    <span class="text-xl leading-none shrink-0">⚠️</span>
                    <div class="space-y-1 flex-1">
                        <strong class="font-bold text-rose-900 block" id="therapistConflictTitle">Jadwal Terapis Bentrok!</strong>
                        <p class="text-xs text-rose-700 leading-relaxed" id="therapistConflictDesc"></p>
                        <p class="text-[11px] text-rose-600 font-semibold mt-1">
                            ℹ️ Permintaan reservasi pada jam tersebut akan ditolak oleh sistem. Silakan pilih jam atau terapis lain.
                        </p>
                    </div>
                </div>
                @error('therapist_id') <p class="text-xs text-rose-600 mt-2 font-semibold">⚠️ {{ $message }}</p> @enderror
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

{{-- Searchable Patient Dropdown Script (Cari Nama, Tampil Nama & Alamat) --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Master data pasien yang di-inject langsung dari controller
    const allPatients = @json($patients ?? []);

    // UI Elements
    const searchInput = document.getElementById('patientSearchInput');
    const dropdownMenu = document.getElementById('patientDropdownMenu');
    const dropdownList = document.getElementById('patientDropdownList');
    const btnClearPatient = document.getElementById('btnClearPatient');
    const btnToggleDropdown = document.getElementById('btnToggleDropdown');
    const dropdownChevron = document.getElementById('dropdownChevron');
    const btnChangePatient = document.getElementById('btnChangePatient');
    const comboboxWrapper = document.getElementById('patientComboboxWrapper');

    const cardFound = document.getElementById('patientFoundCard');
    const cardInitial = document.getElementById('patientInitialPrompt');

    // Display fields in Found Card
    const dispCode = document.getElementById('disp_code');
    const dispFullName = document.getElementById('disp_full_name');
    const dispPhone = document.getElementById('disp_phone');
    const dispAge = document.getElementById('disp_age');
    const dispGender = document.getElementById('disp_gender');
    const dispOccupation = document.getElementById('disp_occupation');
    const dispAddress = document.getElementById('disp_address');
    const linkEditPatient = document.getElementById('linkEditPatient');

    // Hidden form inputs
    const inputPatientId = document.getElementById('patient_id');
    const inputPhoneNumber = document.getElementById('phone_number');
    const inputFullName = document.getElementById('full_name');
    const inputAge = document.getElementById('age');
    const inputGender = document.getElementById('gender');
    const inputOccupation = document.getElementById('occupation');
    const inputAddress = document.getElementById('address');

    let selectedPatient = null;

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    function openDropdown() {
        dropdownMenu.classList.remove('hidden');
        if (dropdownChevron) dropdownChevron.classList.add('rotate-180');
    }

    function closeDropdown() {
        dropdownMenu.classList.add('hidden');
        if (dropdownChevron) dropdownChevron.classList.remove('rotate-180');
    }

    function toggleDropdown() {
        if (dropdownMenu.classList.contains('hidden')) {
            openDropdown();
        } else {
            closeDropdown();
        }
    }

    // Render list pasien di dalam dropdown (Menampilkan Nama & Alamat)
    function renderDropdown(items) {
        dropdownList.innerHTML = '';

        if (!items || items.length === 0) {
            const query = searchInput.value.trim();
            dropdownList.innerHTML = `
                <div class="p-5 text-center text-xs text-gray-500">
                    <div class="text-2xl mb-1">🔍</div>
                    <p class="font-bold text-gray-800">Pasien Tidak Ditemukan</p>
                    <p class="text-gray-400 mt-0.5 text-[11px]">
                        Tidak ada pasien dengan kata kunci "${escapeHtml(query)}".
                    </p>
                    <div class="pt-3">
                        <a href="{{ route('patients.create') }}?from=reservation&name=${encodeURIComponent(query)}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                            <span>+ Daftarkan Pasien Baru</span>
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        items.forEach(p => {
            const itemEl = document.createElement('div');
            itemEl.className = 'px-4 py-3 hover:bg-blue-50/80 cursor-pointer transition border-b border-gray-100 last:border-b-0 flex items-start justify-between gap-3 group';
            
            const genderLabel = p.gender === 'female' ? 'Perempuan' : (p.gender === 'male' ? 'Laki-laki' : '');
            const ageLabel = p.age ? `${p.age} Th` : '';
            const codeBadge = p.patient_code 
                ? `<span class="text-[10px] font-mono text-blue-700 bg-blue-50 border border-blue-200 px-1.5 py-0.5 rounded font-semibold">${escapeHtml(p.patient_code.substring(0, 10))}</span>` 
                : '';
            const addressText = p.address ? escapeHtml(p.address) : '<span class="italic text-gray-400">Alamat belum diisi</span>';
            const phoneText = p.phone_number ? `📱 ${escapeHtml(p.phone_number)}` : '';

            itemEl.innerHTML = `
                <div class="space-y-1 flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-bold text-gray-900 text-sm group-hover:text-blue-700 transition-colors">${escapeHtml(p.full_name)}</span>
                        ${codeBadge}
                    </div>
                    {{-- TAMPILKAN ALAMAT SECARA JELAS --}}
                    <div class="text-xs text-gray-600 flex items-start gap-1.5">
                        <span class="text-gray-400 shrink-0 mt-0.5">📍</span>
                        <span class="line-clamp-2 leading-relaxed">${addressText}</span>
                    </div>
                    {{-- DETAIL PENDUKUNG (NO HP & USIA) --}}
                    <div class="text-[11px] text-gray-400 flex items-center gap-2 pt-0.5 flex-wrap">
                        ${phoneText ? `<span class="font-mono text-gray-500">${phoneText}</span>` : ''}
                        ${ageLabel ? `<span>• Usia: ${ageLabel}</span>` : ''}
                        ${genderLabel ? `<span>• ${genderLabel}</span>` : ''}
                    </div>
                </div>
                <div class="shrink-0 self-center">
                    <span class="text-[11px] font-bold text-blue-600 bg-white group-hover:bg-blue-600 group-hover:text-white border border-blue-200 group-hover:border-blue-600 px-2.5 py-1 rounded-lg transition shadow-2xs">
                        Pilih Pasien
                    </span>
                </div>
            `;

            itemEl.addEventListener('click', function(e) {
                e.stopPropagation();
                selectPatient(p);
            });

            dropdownList.appendChild(itemEl);
        });
    }

    // Pilih pasien dari dropdown
    function selectPatient(p) {
        selectedPatient = p;
        searchInput.value = p.full_name || '';
        btnClearPatient.classList.remove('hidden');
        closeDropdown();

        // Populate card view
        dispCode.textContent = p.patient_code ? 'RM: ' + p.patient_code.substring(0, 8) : 'Pasien Terdaftar';
        dispFullName.textContent = p.full_name || '-';
        dispPhone.textContent = p.phone_number || '-';
        dispAge.textContent = (p.age ? p.age + ' Tahun' : '-');
        dispGender.textContent = p.gender_label || (p.gender === 'female' ? 'Perempuan' : (p.gender === 'male' ? 'Laki-laki' : '-'));
        dispOccupation.textContent = p.occupation || '-';
        dispAddress.textContent = p.address || '-';

        // Set form hidden inputs
        inputPatientId.value = p.id || '';
        inputPhoneNumber.value = p.phone_number || '';
        inputFullName.value = p.full_name || '';
        inputAge.value = p.age || '';
        inputGender.value = p.gender || '';
        inputOccupation.value = p.occupation || '';
        inputAddress.value = p.address || '';

        // Edit link di master data
        if (linkEditPatient) {
            linkEditPatient.href = `/patients/${p.id}/edit`;
        }

        // Tampilkan card hasil terpilih
        cardFound.classList.remove('hidden');
        cardInitial.classList.add('hidden');
    }

    // Bersihkan pilihan pasien
    function clearSelection() {
        selectedPatient = null;
        searchInput.value = '';
        btnClearPatient.classList.add('hidden');

        inputPatientId.value = '';
        inputPhoneNumber.value = '';
        inputFullName.value = '';
        inputAge.value = '';
        inputGender.value = '';
        inputOccupation.value = '';
        inputAddress.value = '';

        cardFound.classList.add('hidden');
        cardInitial.classList.remove('hidden');

        renderDropdown(allPatients);
        openDropdown();
        searchInput.focus();
    }

    // Filter pasien saat mengetik nama di search input
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        btnClearPatient.classList.toggle('hidden', query.length === 0);
        openDropdown();

        if (!query) {
            renderDropdown(allPatients);
            return;
        }

        const filtered = allPatients.filter(p => {
            const name = (p.full_name || '').toLowerCase();
            const addr = (p.address || '').toLowerCase();
            const phone = (p.phone_number || '').toLowerCase();
            const code = (p.patient_code || '').toLowerCase();
            return name.includes(query) || addr.includes(query) || phone.includes(query) || code.includes(query);
        });

        renderDropdown(filtered);
    });

    // Buka dropdown saat input di-klik atau fokus
    searchInput.addEventListener('focus', function() {
        if (!selectedPatient) {
            renderDropdown(allPatients);
        }
        openDropdown();
    });

    searchInput.addEventListener('click', function() {
        openDropdown();
    });

    if (btnToggleDropdown) {
        btnToggleDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleDropdown();
        });
    }

    if (btnClearPatient) {
        btnClearPatient.addEventListener('click', function(e) {
            e.stopPropagation();
            clearSelection();
        });
    }

    if (btnChangePatient) {
        btnChangePatient.addEventListener('click', function() {
            clearSelection();
        });
    }

    // Tutup dropdown saat klik di luar combobox
    document.addEventListener('click', function(e) {
        if (comboboxWrapper && !comboboxWrapper.contains(e.target)) {
            closeDropdown();
        }
    });

    // Tombol Escape menutup dropdown
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    // Validasi form sebelum kirim: pastikan pasien sudah dipilih
    document.getElementById('reservationForm').addEventListener('submit', function(e) {
        if (!inputPatientId.value) {
            e.preventDefault();
            alert('Silakan pilih pasien dari dropdown Master Data atau daftarkan pasien baru terlebih dahulu.');
            searchInput.focus();
            openDropdown();
        }
    });

    // Initial render dropdown
    renderDropdown(allPatients);

    // Auto-select jika ada query param atau old value
    const initialPatientId = "{{ old('patient_id', request('patient_id')) }}";
    const initialPhone = "{{ old('phone_number', request('phone')) }}";

    if (initialPatientId) {
        const found = allPatients.find(p => String(p.id) === String(initialPatientId));
        if (found) {
            selectPatient(found);
        } else {
            fetch(`{{ route('api.patients.lookup') }}?id=${encodeURIComponent(initialPatientId)}`)
                .then(r => r.json())
                .then(d => { if (d.found && d.patient) selectPatient(d.patient); });
        }
    } else if (initialPhone) {
        const cleanInitial = initialPhone.replace(/\D/g, '');
        const found = allPatients.find(p => {
            const pPhone = (p.phone_number || '').replace(/\D/g, '');
            return pPhone.includes(cleanInitial) || cleanInitial.includes(pPhone);
        });
        if (found) {
            selectPatient(found);
        } else {
            fetch(`{{ route('api.patients.lookup') }}?phone=${encodeURIComponent(initialPhone)}`)
                .then(r => r.json())
                .then(d => { if (d.found && d.patient) selectPatient(d.patient); });
        }
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

        if (typeof checkTherapistAvailability === 'function') {
            checkTherapistAvailability();
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

    // ==========================================
    // Realtime Therapist Schedule Conflict Check
    // ==========================================
    const therapistSelect = document.getElementById('therapist_id');
    const warningBox = document.getElementById('therapistConflictWarning');
    const warningDesc = document.getElementById('therapistConflictDesc');
    const btnSubmit = document.getElementById('btnSubmitReservation');
    const reservationForm = document.getElementById('reservationForm');

    let currentConflict = null;

    async function checkTherapistAvailability() {
        const therapistId = therapistSelect ? therapistSelect.value : '';
        const dateVal = prefDateInput ? prefDateInput.value : '';
        const timeVal = prefTimeInput && prefTimeInput.value ? prefTimeInput.value : '09:00';

        if (!therapistId || !dateVal) {
            if (warningBox) warningBox.classList.add('hidden');
            clearBookedChips();
            currentConflict = null;
            return;
        }

        try {
            const url = `{{ route('api.therapists.check-availability') }}?therapist_id=${encodeURIComponent(therapistId)}&date=${encodeURIComponent(dateVal)}&time=${encodeURIComponent(timeVal)}`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            // Tandai tombol jam yang sudah terisi
            updateBookedChips(data.booked_slots || []);

            if (!data.available && data.conflict) {
                currentConflict = data.conflict;
                if (warningBox) {
                    warningBox.classList.remove('hidden');
                    warningDesc.textContent = data.conflict.message;
                }
            } else {
                currentConflict = null;
                if (warningBox) {
                    warningBox.classList.add('hidden');
                }
            }
        } catch (err) {
            console.error('Error saat mengecek ketersediaan terapis:', err);
        }
    }

    function clearBookedChips() {
        document.querySelectorAll('.quick-time-btn').forEach(btn => {
            btn.classList.remove('line-through', 'border-rose-400', 'bg-rose-50', 'text-rose-700', 'opacity-60');
            btn.removeAttribute('title');
        });
    }

    function updateBookedChips(bookedSlots) {
        clearBookedChips();
        document.querySelectorAll('.quick-time-btn').forEach(btn => {
            const time = btn.getAttribute('data-time');
            const slot = bookedSlots.find(s => s.time === time);
            if (slot) {
                btn.classList.add('line-through', 'border-rose-400', 'bg-rose-50', 'text-rose-700', 'opacity-60');
                btn.setAttribute('title', `Sudah Terjadwal: ${slot.patient_name} (${slot.title})`);
            }
        });
    }

    if (therapistSelect) {
        therapistSelect.addEventListener('change', checkTherapistAvailability);
    }

    if (reservationForm) {
        reservationForm.addEventListener('submit', function(e) {
            if (currentConflict) {
                e.preventDefault();
                alert(currentConflict.message || 'Jadwal bentrok: Terapis yang dipilih sudah memiliki jadwal pada jam tersebut. Silakan pilih jam atau terapis lain.');
                if (warningBox) {
                    warningBox.classList.remove('hidden');
                    warningBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        });
    }

    // Initial sync
    syncSchedulePreview();
    if (prefTimeInput && prefTimeInput.value) {
        highlightSelectedTimeChip(prefTimeInput.value);
    }
    checkTherapistAvailability();
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
