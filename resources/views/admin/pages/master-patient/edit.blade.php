@extends('admin.layouts.app')

@section('title', 'Edit Pasien')

@section('content')

@php
    $label = 'mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Pasien', 'url' => route('patients.index')],
    ['label' => 'Edit Pasien'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900">Edit Data Pasien</h1>
    <p class="text-xs text-gray-500 mt-1">Perbarui data profil pasien klinik terapi olahraga.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 w-full max-w-4xl mx-auto">
    <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- BIODATA PASIEN --}}
        <div>
            <div class="flex items-center gap-2 mb-4">
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900">Biodata Pasien</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- No HP --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">
                        Nomor HP / WhatsApp Pasien <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="phone_number" id="phone_number" 
                               value="{{ old('phone_number', $patient->phone_number) }}" 
                               class="{{ $input }} pl-10 font-mono @error('phone_number'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: 08123456789 atau 85183036722" required>
                        <span class="absolute left-3.5 top-3 text-gray-400">📱</span>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Nomor HP digunakan untuk deteksi otomatis di Form Reservasi.</p>
                    @error('phone_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nama Lengkap --}}
                <div>
                    <label class="{{ $label }}">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $patient->full_name) }}" 
                           class="{{ $input }} @error('full_name'){{ $fieldErr }}@enderror" 
                           placeholder="Nama lengkap pasien" required>
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Usia --}}
                <div>
                    <label class="{{ $label }}">Usia (Tahun) <span class="text-red-500">*</span></label>
                    <input type="number" name="age" id="age" value="{{ old('age', $patient->age) }}" min="1" max="120"
                           class="{{ $input }} @error('age'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: 28" required>
                    @error('age')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Jenis Kelamin --}}
                <div>
                    <label class="{{ $label }}">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" id="gender" class="{{ $input }} @error('gender'){{ $fieldErr }}@enderror" required>
                        <option value="" disabled>Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pekerjaan --}}
                <div>
                    <label class="{{ $label }}">Pekerjaan <span class="text-red-500">*</span></label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $patient->occupation) }}" 
                           class="{{ $input }} @error('occupation'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: Atlet Basket, Karyawan Swasta, Pelajar" required>
                    @error('occupation')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Referal Terapis (Dropdown Search Master Data Terapis) --}}
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="{{ $label }} !mb-0">
                            Referal Terapis <span class="text-xs font-normal normal-case text-gray-500">(Opsional / Jika ada rujukan)</span>
                        </label>
                        <span class="text-[11px] text-gray-400">Master Data Terapis</span>
                    </div>

                    <div class="relative" id="referralComboboxWrapper">
                        {{-- Hidden Input yang dikirim ke controller --}}
                        <input type="hidden" name="referral_therapist_id" id="referral_therapist_id" value="{{ old('referral_therapist_id', $patient->referral_therapist_id) }}">

                        {{-- Input Pencarian --}}
                        <div class="relative">
                            <input type="text" id="therapistSearchInput"
                                   autocomplete="off"
                                   placeholder="🔍 Ketik nama terapis untuk mencari dan memilih referal..."
                                   class="{{ $input }} pl-10 pr-24 cursor-pointer @error('referral_therapist_id'){{ $fieldErr }}@enderror">
                            <span class="absolute left-3.5 top-3 text-gray-400 pointer-events-none text-base">🩺</span>

                            {{-- Action buttons --}}
                            <div class="absolute right-3 top-2.5 flex items-center gap-1.5">
                                <button type="button" id="btnClearReferral" class="hidden text-gray-400 hover:text-red-600 p-1 rounded-full hover:bg-gray-100 transition" title="Hapus Referal">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                                <button type="button" id="btnToggleReferralDropdown" class="text-gray-400 hover:text-gray-700 p-1 rounded-full hover:bg-gray-100 transition" title="Buka / Tutup Daftar Terapis">
                                    <svg id="referralChevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Dropdown Menu Popover --}}
                        <div id="referralDropdownMenu" class="hidden absolute z-30 left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-2xl shadow-xl max-h-72 overflow-y-auto divide-y divide-gray-100">
                            {{-- Option Tanpa Referal --}}
                            <div class="p-2.5 bg-gray-50/75 hover:bg-gray-100 cursor-pointer flex items-center justify-between text-xs text-gray-600 transition" id="optionNoReferral">
                                <span class="font-medium text-gray-500 italic">— Tanpa Referal (Bukan Rujukan) —</span>
                                <span class="text-[10px] text-gray-400 bg-white px-2 py-0.5 rounded border border-gray-200">Kosongkan</span>
                            </div>
                            
                            {{-- Container list hasil pencarian --}}
                            <div id="therapistDropdownList" class="divide-y divide-gray-100">
                                {{-- Populated by JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- Selected Referral Info Banner --}}
                    <div id="selectedReferralBadge" class="hidden mt-2 p-3 bg-blue-50/80 border border-blue-200 rounded-xl flex items-center justify-between text-xs transition">
                        <div class="flex items-center gap-2.5">
                            <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-xs">🩺</span>
                            <div>
                                <div class="font-bold text-blue-950 flex items-center gap-2">
                                    <span id="badgeTherapistName">-</span>
                                    <span id="badgeTherapistCode" class="text-[10px] font-mono bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded"></span>
                                </div>
                                <div class="text-[11px] text-blue-700 mt-0.5">
                                    <span id="badgeTherapistSpec">-</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="btnRemoveBadgeReferral" class="text-xs text-red-600 hover:text-red-800 hover:underline font-semibold ml-2">
                            ✕ Lepas Referal
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-500 mt-1">Data referal diambil dari master data terapis klinik. Dapat dicari dengan mengetik nama terapis.</p>
                    @error('referral_therapist_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Alamat --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Alamat Tempat Tinggal / Domisili <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="2" 
                              class="{{ $input }} @error('address'){{ $fieldErr }}@enderror" 
                              placeholder="Alamat lengkap tempat tinggal pasien" required>{{ old('address', $patient->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('patients.index') }}" 
               class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-200 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

{{-- Script Dropdown Search Referal Terapis --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const therapists = @json($therapists ?? []);
    const initialId = @json(old('referral_therapist_id', $patient->referral_therapist_id));

    const searchInput = document.getElementById('therapistSearchInput');
    const hiddenInput = document.getElementById('referral_therapist_id');
    const dropdownMenu = document.getElementById('referralDropdownMenu');
    const dropdownList = document.getElementById('therapistDropdownList');
    const dropdownChevron = document.getElementById('referralChevron');
    const btnClear = document.getElementById('btnClearReferral');
    const btnToggle = document.getElementById('btnToggleReferralDropdown');
    const optionNoReferral = document.getElementById('optionNoReferral');
    const wrapper = document.getElementById('referralComboboxWrapper');

    const badge = document.getElementById('selectedReferralBadge');
    const badgeName = document.getElementById('badgeTherapistName');
    const badgeCode = document.getElementById('badgeTherapistCode');
    const badgeSpec = document.getElementById('badgeTherapistSpec');
    const btnRemoveBadge = document.getElementById('btnRemoveBadgeReferral');

    let currentSelected = null;

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

    function selectTherapist(therapist) {
        if (!therapist) {
            clearSelection();
            return;
        }
        currentSelected = therapist;
        hiddenInput.value = therapist.id;
        searchInput.value = therapist.full_name;
        btnClear.classList.remove('hidden');

        // Update badge
        badgeName.textContent = therapist.full_name;
        badgeCode.textContent = therapist.therapist_code ? 'Kode: ' + therapist.therapist_code : '';
        badgeSpec.textContent = therapist.specialization || 'Terapis Olahraga';
        badge.classList.remove('hidden');

        closeDropdown();
        renderList(therapists);
    }

    function clearSelection() {
        currentSelected = null;
        hiddenInput.value = '';
        searchInput.value = '';
        btnClear.classList.add('hidden');
        badge.classList.add('hidden');
        renderList(therapists);
    }

    function renderList(items) {
        dropdownList.innerHTML = '';
        if (!items || items.length === 0) {
            const query = searchInput.value.trim();
            dropdownList.innerHTML = `
                <div class="p-4 text-center text-xs text-gray-500">
                    <p class="font-bold text-gray-700">Terapis Tidak Ditemukan</p>
                    <p class="text-gray-400 mt-0.5 text-[11px]">
                        Tidak ada terapis dengan nama "${escapeHtml(query)}" di master data.
                    </p>
                </div>
            `;
            return;
        }

        items.forEach(t => {
            const isSelected = currentSelected && String(currentSelected.id) === String(t.id);
            const itemEl = document.createElement('div');
            itemEl.className = `p-3 cursor-pointer transition flex items-center justify-between text-xs hover:bg-blue-50/70 ${isSelected ? 'bg-blue-50/90 font-semibold text-blue-900' : 'text-gray-800'}`;
            itemEl.innerHTML = `
                <div class="flex items-center gap-2.5">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-bold">🩺</span>
                    <div>
                        <div class="font-medium text-gray-900">${escapeHtml(t.full_name)}</div>
                        <div class="text-[11px] text-gray-500 flex items-center gap-1.5 mt-0.5">
                            <span>${escapeHtml(t.specialization || 'Terapis Olahraga')}</span>
                            ${t.therapist_code ? `<span class="font-mono text-[10px] bg-gray-100 text-gray-600 px-1 rounded">${escapeHtml(t.therapist_code)}</span>` : ''}
                        </div>
                    </div>
                </div>
                ${isSelected ? '<span class="text-blue-600 font-bold">✓ Terpilih</span>' : '<span class="text-gray-400 text-[11px]">Pilih →</span>'}
            `;
            itemEl.addEventListener('click', function(e) {
                e.stopPropagation();
                selectTherapist(t);
            });
            dropdownList.appendChild(itemEl);
        });
    }

    // Filter event
    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        openDropdown();
        if (!query) {
            renderList(therapists);
            return;
        }
        const filtered = therapists.filter(t => {
            const name = (t.full_name || '').toLowerCase();
            const spec = (t.specialization || '').toLowerCase();
            const code = (t.therapist_code || '').toLowerCase();
            return name.includes(query) || spec.includes(query) || code.includes(query);
        });
        renderList(filtered);
    });

    searchInput.addEventListener('focus', function() {
        openDropdown();
        const query = this.value.trim().toLowerCase();
        if (query && (!currentSelected || currentSelected.full_name.toLowerCase() !== query)) {
            const filtered = therapists.filter(t => (t.full_name || '').toLowerCase().includes(query));
            renderList(filtered);
        } else {
            renderList(therapists);
        }
    });

    btnToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        if (dropdownMenu.classList.contains('hidden')) {
            openDropdown();
            renderList(therapists);
        } else {
            closeDropdown();
        }
    });

    btnClear.addEventListener('click', function(e) {
        e.stopPropagation();
        clearSelection();
        closeDropdown();
    });

    btnRemoveBadge.addEventListener('click', function(e) {
        e.stopPropagation();
        clearSelection();
    });

    optionNoReferral.addEventListener('click', function(e) {
        e.stopPropagation();
        clearSelection();
        closeDropdown();
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
            // If text typed doesn't match selected, restore selected full_name or clear
            if (currentSelected) {
                searchInput.value = currentSelected.full_name;
            } else if (!hiddenInput.value) {
                searchInput.value = '';
            }
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDropdown();
        }
    });

    // Initial render
    renderList(therapists);

    // Initial value setup (e.g. from existing referral or old input)
    if (initialId) {
        const found = therapists.find(t => String(t.id) === String(initialId));
        if (found) {
            selectTherapist(found);
        }
    }
});
</script>

@endsection
