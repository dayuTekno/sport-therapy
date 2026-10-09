@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <div class="flex w-full min-h-screen min-h-[100dvh] dark:bg-gray-900">

        <!-- LEFT: FORM (MOBILE-FIRST RESPONSIVE) -->
        <div class="flex items-center justify-center w-full lg:w-1/2 px-4 sm:px-8 py-8 sm:py-12">
            <div class="w-full max-w-md bg-white dark:bg-gray-800/80 p-6 sm:p-8 rounded-3xl shadow-lg sm:shadow-none border border-gray-100 sm:border-none dark:border-gray-700/50">

                {{-- Mobile Brand Header --}}
                <div class="mb-6 lg:hidden flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                        <img class="w-6 h-6 object-contain brightness-0 invert" src="{{ asset('storage/icons/hospital.png') }}" alt="Logo" onerror="this.outerHTML='🏥'">
                    </div>
                    <div>
                        <h2 class="text-base font-black tracking-tight text-gray-900 dark:text-white">SportClinic<span class="text-blue-600">.io</span></h2>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium">Platform Klinik Fisioterapi & Olahraga</p>
                    </div>
                </div>

                <h1 class="mb-1 text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Masuk ke Sistem
                </h1>

                <p class="mb-6 text-xs text-gray-500 dark:text-gray-400">
                    Silakan masukkan email dan password akun klinik Anda.
                </p>

                @if ($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-xs text-red-700 dark:text-red-300">
                        <div class="font-bold mb-1">Gagal masuk:</div>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block mb-1.5 text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                            Email Akun
                        </label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               autocomplete="email"
                               inputmode="email"
                               autocapitalize="none"
                               spellcheck="false"
                               placeholder="nama@klinik.com"
                               class="h-12 w-full rounded-xl border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400">
                                    Lupa password?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required
                                   autocomplete="current-password"
                                   autocapitalize="none"
                                   spellcheck="false"
                                   placeholder="••••••••"
                                   class="h-12 w-full rounded-xl border border-gray-300 px-4 pr-12 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white transition" />

                            <button type="button" 
                                    @click="showPassword = !showPassword"
                                    class="absolute right-0 top-0 h-12 w-12 flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition"
                                    aria-label="Tampilkan / Sembunyikan Password">
                                <span x-text="showPassword ? '🙈' : '👁️'" class="text-base select-none"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2.5 cursor-pointer text-xs text-gray-600 dark:text-gray-400 select-none">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-900">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button (Touch Friendly 48px Height) -->
                    <button type="submit" 
                            class="w-full h-12 text-sm font-bold text-white rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] transition shadow-md shadow-blue-500/25 flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>

                <div class="mt-6 pt-5 border-t border-gray-100 dark:border-gray-700/60 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Belum mendaftarkan klinik?
                        <a href="{{ route('register.clinic') }}" class="font-bold text-blue-600 dark:text-blue-400 hover:underline">
                            Daftar Uji Coba Gratis 14 Hari
                        </a>
                    </p>
                </div>

            </div>
        </div>

        <!-- RIGHT: FULL IMAGE (DESKTOP ONLY) -->
        <div class="relative hidden lg:block w-1/2 h-full min-h-[100dvh]">
            <!-- Background Image -->
            <img src="{{ asset('storage/images/klinik.jpg') }}" alt="Klinik"
                class="absolute inset-0 w-full h-full object-cover">

            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/50 to-slate-900/30"></div>

            <!-- Content -->
            <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-8">
                <div class="w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-3xl mb-6 shadow-2xl border border-white/20">
                    🏥
                </div>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-white mb-3 tracking-tight">
                    SportClinic SaaS Platform
                </h2>
                <p class="text-sm text-white/90 max-w-md leading-relaxed">
                    Transformasi Digital Terintegrasi: Rekam Medis Fisioterapi Berjenjang, Otomasi Antrian & WhatsApp, Kasir, dan Manajemen Peralatan.
                </p>
            </div>
        </div>

    </div>

    <!-- DARK MODE TOGGLER -->
    <div class="fixed bottom-4 right-4 z-50">
        <button @click="darkMode = !darkMode" 
                aria-label="Toggle Dark Mode"
                class="w-11 h-11 rounded-2xl bg-white dark:bg-gray-800 text-gray-700 dark:text-yellow-400 shadow-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center text-lg active:scale-95 transition">
            <span x-text="darkMode ? '☀️' : '🌙'"></span>
        </button>
    </div>
@endsection
