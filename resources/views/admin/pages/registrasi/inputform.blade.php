@extends('admin.layouts.app')

@section('title', 'Data Pengaju')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Menu'],
    ['label' => 'Registrasi'],
    ['label' => 'Input Data Pasien'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Input Data Pasien</h1>
</div>

@include('admin.partials.alert')


        <div class="bg-white rounded-xl shadow overflow-hidden">
            <form method="POST" action="{{ route('registrasi.store') }}">
                @csrf
            <!-- ====== Form Elements Section Start -->
             <div
                class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800"
              >
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Elements -->
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        NIK
                      </label>
                      <input
                        readonly
                        value="{{ request('nik') }}"
                        name="nik"
                        placeholder="321************"
                        type="text"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      />
                    </div>

                                        <!-- Elements -->
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Nama Lengkap
                      </label>
                      <input
                      name="full_name"
                        type="text"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      />
                    </div>
                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Elements -->
                                        <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Tanggal Lahir
                      </label>

                      <div class="relative">
                        <input
                          type="date"
                          name="date_of_birth"
                          placeholder="Select date"
                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                          onclick="this.showPicker()"
                        />
                        <span
                          class="pointer-events-none absolute top-1/2 right-3 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                        >
                          <svg
                            class="fill-current"
                            width="20"
                            height="20"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                              fill=""
                            />
                          </svg>
                        </span>
                      </div>
                    </div>

                                        <!-- Elements -->
                                       <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Jenis Kelamin
                      </label>
                      <div
                        x-data="{ isOptionSelected: false }"
                        class="relative z-20 bg-transparent"
                      >
                        <select
                        name="gender"
                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                          :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
                          @change="isOptionSelected = true"
                        >
                          <option
                            value="male"
                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                          >
                            Laki-laki
                          </option>
                          <option
                            value="female"
                            class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                          >
                            Perempuan
                          </option>
                        </select>
                        <span
                          class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400"
                        >
                          <svg
                            class="stroke-current"
                            width="20"
                            height="20"
                            viewBox="0 0 20 20"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <path
                              d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                              stroke=""
                              stroke-width="1.5"
                              stroke-linecap="round"
                              stroke-linejoin="round"
                            />
                          </svg>
                        </span>
                      </div>
                    </div>
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        No. Hp
                      </label>
                      <div class="relative">
                        <span
                          class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400"
                        >
                         +62
                        </span>
                        <input
                        name="phone_number"
                          type="text"
                          placeholder="8**********"
                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        />
                      </div>
                    </div>
                                        <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Alamat
                      </label>
                      <input
                      name="address"
                        type="text"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                      />
                    </div>
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        Default Eselon (Opsional)
                      </label>
                      <select
                      name="eselon_id"
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                      >
                        <option value="">Tidak ada</option>
                        @foreach($eselons as $eselon)
                            <option value="{{ $eselon->id }}">{{ $eselon->name }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="md:col-span-2">
                      <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Referal Terapis (Opsional)
                      </label>
                      <div class="relative" id="regReferralComboboxWrapper">
                        <input type="hidden" name="referral_therapist_id" id="reg_referral_therapist_id" value="{{ old('referral_therapist_id') }}">
                        <div class="relative">
                          <input type="text" id="regTherapistSearchInput"
                            autocomplete="off"
                            placeholder="🔍 Ketik nama terapis untuk mencari dan memilih referal..."
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-10 pr-20 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                          />
                          <span class="absolute left-3.5 top-3 text-gray-400 pointer-events-none">🩺</span>
                          <div class="absolute right-3 top-2.5 flex items-center gap-1.5">
                            <button type="button" id="regBtnClearReferral" class="hidden text-gray-400 hover:text-red-600 p-1 rounded-full transition" title="Hapus">
                              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                            <button type="button" id="regBtnToggleDropdown" class="text-gray-400 hover:text-gray-700 p-1 rounded-full transition" title="Buka / Tutup">
                              <svg id="regReferralChevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                          </div>
                        </div>

                        <div id="regReferralDropdownMenu" class="hidden absolute z-30 left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-60 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700">
                          <div class="p-2.5 bg-gray-50 dark:bg-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer flex items-center justify-between text-xs text-gray-600 dark:text-gray-300 transition" id="regOptionNoReferral">
                            <span class="font-medium italic">— Tanpa Referal (Bukan Rujukan) —</span>
                            <span class="text-[10px] text-gray-400 bg-white dark:bg-gray-800 px-2 py-0.5 rounded border border-gray-200 dark:border-gray-700">Kosongkan</span>
                          </div>
                          <div id="regTherapistDropdownList" class="divide-y divide-gray-100 dark:divide-gray-700"></div>
                        </div>
                      </div>

                      <div id="regSelectedReferralBadge" class="hidden mt-2 p-2.5 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg flex items-center justify-between text-xs transition">
                        <div class="flex items-center gap-2">
                          <span>🩺</span>
                          <span id="regBadgeTherapistName" class="font-bold text-blue-900 dark:text-blue-200">-</span>
                          <span id="regBadgeTherapistSpec" class="text-blue-700 dark:text-blue-300 text-[11px]">-</span>
                        </div>
                        <button type="button" id="regBtnRemoveBadge" class="text-xs text-red-600 hover:underline font-semibold">✕ Hapus</button>
                      </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-3 text-sm font-medium text-white transition rounded-lg bg-brand-500 shadow-theme-xs hover:bg-brand-600 active:scale-95"
                        >
                            <!-- Icon Submit (Paper Plane) -->
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>

                            Submit
                        </button>
                    </div>
              </div>
            <!-- ====== Form Elements Section End -->
            </form>
        </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const therapists = @json($therapists ?? []);
    const initialId = @json(old('referral_therapist_id', null));

    const searchInput = document.getElementById('regTherapistSearchInput');
    const hiddenInput = document.getElementById('reg_referral_therapist_id');
    const dropdownMenu = document.getElementById('regReferralDropdownMenu');
    const dropdownList = document.getElementById('regTherapistDropdownList');
    const dropdownChevron = document.getElementById('regReferralChevron');
    const btnClear = document.getElementById('regBtnClearReferral');
    const btnToggle = document.getElementById('regBtnToggleDropdown');
    const optionNoReferral = document.getElementById('regOptionNoReferral');
    const wrapper = document.getElementById('regReferralComboboxWrapper');

    const badge = document.getElementById('regSelectedReferralBadge');
    const badgeName = document.getElementById('regBadgeTherapistName');
    const badgeSpec = document.getElementById('regBadgeTherapistSpec');
    const btnRemoveBadge = document.getElementById('regBtnRemoveBadge');

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

        badgeName.textContent = therapist.full_name;
        badgeSpec.textContent = (therapist.specialization ? '• ' + therapist.specialization : '') + (therapist.therapist_code ? ' (' + therapist.therapist_code + ')' : '');
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
                    <p class="font-bold">Terapis Tidak Ditemukan</p>
                    <p class="text-[11px] text-gray-400">Tidak ada terapis "${escapeHtml(query)}"</p>
                </div>
            `;
            return;
        }

        items.forEach(t => {
            const isSelected = currentSelected && String(currentSelected.id) === String(t.id);
            const itemEl = document.createElement('div');
            itemEl.className = `p-2.5 cursor-pointer transition flex items-center justify-between text-xs hover:bg-blue-50 dark:hover:bg-gray-700 ${isSelected ? 'bg-blue-50 dark:bg-gray-700 font-semibold text-blue-900 dark:text-blue-300' : 'text-gray-800 dark:text-gray-200'}`;
            itemEl.innerHTML = `
                <div>
                    <div>${escapeHtml(t.full_name)}</div>
                    <div class="text-[11px] text-gray-400">${escapeHtml(t.specialization || 'Terapis')} ${t.therapist_code ? '[' + escapeHtml(t.therapist_code) + ']' : ''}</div>
                </div>
                ${isSelected ? '<span class="text-blue-600 font-bold">✓</span>' : '<span class="text-gray-400 text-[10px]">Pilih</span>'}
            `;
            itemEl.addEventListener('click', function(e) {
                e.stopPropagation();
                selectTherapist(t);
            });
            dropdownList.appendChild(itemEl);
        });
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        openDropdown();
        if (!query) {
            renderList(therapists);
            return;
        }
        const filtered = therapists.filter(t => (t.full_name || '').toLowerCase().includes(query) || (t.specialization || '').toLowerCase().includes(query));
        renderList(filtered);
    });

    searchInput.addEventListener('focus', function() {
        openDropdown();
        renderList(therapists);
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

    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            closeDropdown();
            if (currentSelected) {
                searchInput.value = currentSelected.full_name;
            } else if (!hiddenInput.value) {
                searchInput.value = '';
            }
        }
    });

    renderList(therapists);

    if (initialId) {
        const found = therapists.find(t => String(t.id) === String(initialId));
        if (found) selectTherapist(found);
    }
});
</script>


@endsection
