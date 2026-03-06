@extends('admin.layouts.app')

@section('title', 'Data Pengaju')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Dashboard', 'url' => route('dashboard')],
    ['label' => 'Menu'],
    ['label' => 'Registrasi'],
]" />

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-gray-800">Registrasi</h1>
</div>

@include('admin.partials.alert')

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <form method="POST" action="{{ route('registrasi.check') }}">
                @csrf
            <!-- ====== Form Elements Section Start -->
                          <div class="space-y-6">
                <div
                  class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
                >
                  <div class="px-5 py-4 sm:px-6 sm:py-5">
                    <h3
                      class="text-base font-medium text-gray-800 dark:text-white/90"
                    >
                      Untuk melakukan registrasi, silahkan masukan NIK 16 digit
                    </h3>
                  </div>
                  <div
                    class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800"
                  >
                    <!-- Elements -->
                    <div>
                      <label
                        class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
                      >
                        NIK
                      </label>
                      <div class="relative">
                        <span
                          class="absolute top-1/2 left-0 -translate-y-1/2 border-r border-gray-200 px-3.5 py-3 text-gray-500 dark:border-gray-800 dark:text-gray-400"
                        >
                          <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <rect x="3" y="6" width="18" height="12" rx="2" ry="2"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            
                            <!-- Foto -->
                            <circle cx="9" cy="12" r="2"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            
                            <!-- Garis data -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10h5
                                    M13 13h5
                                    M8 15h10"/>
                            </svg>
                        </span>
                        <input
                            name="nik"
                          type="number"
                          placeholder="321************"
                          class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pl-[62px] text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        />
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
