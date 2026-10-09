<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="default" />
    <meta name="format-detection" content="telephone=no" />
    <title>@yield('title', 'Admin Dashboard') - SportClinic</title>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

<body x-data="{ page: 'ecommerce', loaded: true, darkMode: false, stickyMenu: false, sidebarToggle: false, scrollTop: false, showAlert: false, alertMessage: '', alertType: '' }" x-init="darkMode = JSON.parse(localStorage.getItem('darkMode'));
$watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)));

@if(session('success'))
showAlert = true;
alertMessage = '{{ addslashes(session('success')) }}';
alertType = 'success';
@elseif(session('error'))
showAlert = true;
alertMessage = '{{ addslashes(session('error')) }}';
alertType = 'error';
@elseif(isset($errors) && $errors->any())
showAlert = true;
alertMessage = '{{ addslashes($errors->first()) }}';
alertType = 'error';
@endif" :class="{ 'dark bg-gray-900': darkMode === true }" class="min-h-screen bg-gray-50 text-gray-900 dark:text-gray-100">
    {{-- Preloader --}}
    @include('admin.partials.preloader')

    {{-- Overlay for mobile sidebar --}}
    @include('admin.partials.overlay')

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            {{-- Header --}}
            @include('admin.partials.header')

            {{-- MAIN CONTENT --}}
            <main class="flex-1 pb-10">
                <div class="p-3 sm:p-4 md:p-6 mx-auto max-w-7xl">

                    {{-- Alert --}}
                    <template x-if="showAlert">
                        <div x-transition class="mb-4 p-4 rounded shadow text-white"
                            :class="alertType === 'success' ? 'bg-green-500' : 'bg-red-500'">
                            <div class="flex justify-between items-center">
                                <span x-text="alertMessage"></span>
                                <button type="button" @click="showAlert = false" class="ml-4 font-bold">×</button>
                            </div>
                        </div>
                    </template>

                    {{-- Impersonation Alert Banner --}}
                    @if(session()->has('active_clinic_id'))
                        @php
                            $activeClinicObj = \App\Models\Clinic::find(session('active_clinic_id'));
                        @endphp
                        <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl shrink-0">
                                    👁️
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm tracking-wide flex items-center gap-2">
                                        MODE PENINJAUAN KLINIK AKTIF
                                        <span class="px-2 py-0.5 text-[10px] bg-white text-amber-800 rounded-full font-extrabold uppercase">SaaS Impersonation</span>
                                    </h4>
                                    <p class="text-xs text-amber-100">
                                        Anda sedang melihat dan mengelola data klinik: <strong>{{ $activeClinicObj?->name ?? 'Klinik #' . session('active_clinic_id') }}</strong> ({{ $activeClinicObj?->code ?? '-' }}).
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route('saas.clinics.exit-impersonation') }}" class="px-3.5 py-2 bg-white hover:bg-amber-50 text-amber-800 text-xs font-bold rounded-xl shadow transition flex items-center gap-1.5">
                                    <span>Keluar Mode Tinjau</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </a>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
