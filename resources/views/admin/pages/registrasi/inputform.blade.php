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


@endsection
