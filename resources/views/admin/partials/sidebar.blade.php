<aside x-data="{ selected: null }" :class="sidebarToggle ? 'translate-x-0 lg:w-[90px]' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-50 flex h-screen w-[290px] max-w-[85vw] flex-col
           overflow-y-hidden border-r border-gray-200 bg-white px-5
           dark:border-gray-800 dark:bg-black lg:static lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
    <!-- HEADER LOGO -->
    <div :class="sidebarToggle ? 'justify-between lg:justify-center' : 'justify-between'" class="flex items-center gap-2 pt-6 pb-5 border-b border-gray-100 dark:border-gray-800 lg:border-none">
        <a href="{{ auth()->user()->isSaasAdmin() && !session()->has('active_clinic_id') ? route('saas.dashboard') : route('dashboard') }}" class="flex items-center gap-3">
            {{-- LOGO --}}
            <img class="w-[40px] h-[40px] dark:hidden object-contain" src="{{ asset('storage/icons/hospital.png') }}"
                alt="Logo" />
            <img class="w-[40px] h-[40px] hidden dark:block object-contain" src="{{ asset('storage/icons/hospital.png') }}"
                alt="Logo" />

            {{-- TEXT --}}
            <div class="flex flex-col" :class="sidebarToggle ? 'hidden' : ''">
                <span class="text-sm font-black tracking-tight text-gray-900 dark:text-white whitespace-nowrap">
                    @if(auth()->user()->isSaasAdmin() && !session()->has('active_clinic_id'))
                        SportTherapy <span class="text-blue-600 text-xs font-bold uppercase tracking-wider">SaaS</span>
                    @else
                        {{ Str::limit(auth()->user()->clinic?->name ?? 'Sport Therapy', 18) }}
                    @endif
                </span>
                <span class="text-[10px] text-gray-400 font-medium">
                    @if(auth()->user()->isSaasAdmin() && !session()->has('active_clinic_id'))
                        Platform Control Center
                    @else
                        Sistem Klinik Fisioterapi
                    @endif
                </span>
            </div>
        </a>

        {{-- CLOSE BUTTON ON MOBILE --}}
        <button type="button" @click="sidebarToggle = false" 
                class="lg:hidden p-2 text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl transition"
                aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- ICON SAAT SIDEBAR COLLAPSE --}}
        <img class="logo-icon" :class="sidebarToggle ? 'lg:block' : 'hidden'"
            src="{{ asset('/storage/images/logo-icon.svg') }}" />
    </div>

    {{-- IMPERSONATION BANNER DI SIDEBAR JIKA SEDANG AKTIF --}}
    @if(session()->has('active_clinic_id'))
        @php
            $activeClinic = \App\Models\Clinic::find(session('active_clinic_id'));
        @endphp
        <div class="mb-4 p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-800 dark:text-amber-400 text-xs" :class="sidebarToggle ? 'hidden' : ''">
            <div class="font-bold flex items-center justify-between gap-1 mb-1">
                <span class="flex items-center gap-1">👁️ Mode Tinjau</span>
                <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.5 rounded bg-amber-200 text-amber-900">Aktif</span>
            </div>
            <p class="font-bold text-gray-900 dark:text-gray-100 truncate mb-2">
                {{ $activeClinic?->name ?? 'Klinik #' . session('active_clinic_id') }}
            </p>
            <div class="flex flex-col gap-1.5">
                <a href="{{ route('saas.clinics.exit-impersonation') }}" 
                   class="w-full py-1.5 px-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-[11px] font-bold text-center transition block shadow-sm">
                    Keluar Mode Tinjau
                </a>
                <a href="{{ route('saas.dashboard') }}" 
                   class="w-full py-1 px-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded text-[10px] font-medium text-center transition block">
                    ← Kembali ke Panel SaaS
                </a>
            </div>
        </div>
    @endif

    <!-- MENU CONTAINER -->
    <div class="flex flex-col overflow-y-auto no-scrollbar">
        <nav>

            {{-- ========================================================================= --}}
            {{-- KONDISI 1: ADMINISTRATOR SAAS (TIDAK SEDANG IMPERSONATE KLINIK)          --}}
            {{-- ========================================================================= --}}
            @if(auth()->user()->isSaasAdmin() && !session()->has('active_clinic_id'))

                <!-- GROUP: PLATFORM SAAS -->
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Platform SaaS</span>
                </h3>

                <ul class="flex flex-col gap-2 mb-6">
                    <!-- DASHBOARD SAAS -->
                    <li>
                        <a href="{{ route('saas.dashboard') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('saas.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Dashboard SaaS
                            </span>
                        </a>
                    </li>

                    <!-- MANAJEMEN MITRA KLINIK -->
                    <li>
                        <a href="{{ route('saas.clinics.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('saas.clinics*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Mitra Klinik
                            </span>
                            @php $clinicCount = \App\Models\Clinic::where('status', 'active')->count(); @endphp
                            @if($clinicCount > 0)
                                <span class="ml-auto inline-flex px-2 py-0.5 items-center justify-center rounded-full bg-blue-100 text-[10px] font-bold text-blue-700" :class="sidebarToggle ? 'lg:hidden' : ''">
                                    {{ $clinicCount }}
                                </span>
                            @endif
                        </a>
                    </li>

                    <!-- PAKET LANGGANAN -->
                    <li>
                        <a href="{{ route('saas.plans.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('saas.plans*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Paket Langganan
                            </span>
                        </a>
                    </li>

                    <!-- CMS LANDING PAGE -->
                    <li>
                        <a href="{{ route('saas.cms.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('saas.cms*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                CMS Landing Page
                            </span>
                            <span class="ml-auto inline-flex px-1.5 py-0.5 items-center justify-center rounded bg-emerald-100 text-[9px] font-bold text-emerald-700" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Publik
                            </span>
                        </a>
                    </li>
                </ul>

                <!-- GROUP: KEAMANAN & AKSES -->
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Keamanan Platform</span>
                </h3>

                <ul class="flex flex-col gap-2 mb-6">
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('users*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Pengguna (Users)
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('roles.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('roles*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Roles & Hak Akses
                            </span>
                        </a>
                    </li>
                </ul>

                <!-- GROUP: TAUTAN EKSTERNAL -->
                <h3 class="mb-3 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Pratinjau Publik</span>
                </h3>

                <ul class="flex flex-col gap-2 mb-6">
                    <li>
                        <a href="{{ url('/') }}" target="_blank"
                            class="menu-item group flex items-center gap-3 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Buka Landing Page ↗
                            </span>
                        </a>
                    </li>
                </ul>

            {{-- ========================================================================= --}}
            {{-- KONDISI 2: KLINIK OPERASIONAL (ATAU SAAS ADMIN DALAM MODE IMPERSONASI)     --}}
            {{-- ========================================================================= --}}
            @else

                @if(session()->has('active_clinic_id'))
                    <!-- Tautan Cepat Kembali ke SaaS Platform -->
                    <div class="mb-4">
                        <a href="{{ route('saas.dashboard') }}"
                           class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs transition dark:bg-blue-900/30 dark:text-blue-300">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                            </svg>
                            <span :class="sidebarToggle ? 'lg:hidden' : ''">Kembali ke Panel SaaS</span>
                        </a>
                    </div>
                @endif

                <!-- GROUP: MENU LAYANAN -->
                <h3 class="mb-4 text-xs uppercase text-gray-400 font-semibold tracking-wider">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Menu Layanan</span>
                </h3>

                <ul class="flex flex-col gap-3 mb-6">

                    <!-- DASHBOARD KLINIK -->
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2 7-7 7 7v10a1 1 0 01-1 1h-3v-6H9v6H6a1 1 0 01-1-1z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Dashboard
                            </span>
                        </a>
                    </li>

                    @can('menu.registrasi')
                    <li>
                        <a href="{{ route('reservations.create') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('reservations.create', 'registrasi*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Buat Reservasi
                            </span>
                        </a>
                    </li>
                    @endcan

                    @can('menu.antrian-admisi')
                    <li>
                        <a href="{{ route('reservations.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('reservations.index', 'reservations.show', 'admin.antrian*') && !request()->routeIs('reservations.history') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Reservasi Terapi
                            </span>
                            @php $pendingResCount = \App\Models\Reservation::where('status', 'pending_confirmation')->count(); @endphp
                            @if($pendingResCount > 0)
                                <span class="ml-auto inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold text-amber-700" :class="sidebarToggle ? 'lg:hidden' : ''">
                                    {{ $pendingResCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @endcan

                    @canany(['menu.riwayat-terapi', 'menu.antrian-admisi', 'menu.sesi-terapi'])
                    <li>
                        <a href="{{ route('reservations.history') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('reservations.history') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Riwayat Terapi
                            </span>
                            @php $histCount = \App\Models\Reservation::whereIn('status', ['completed', 'cancelled'])->count(); @endphp
                            @if($histCount > 0)
                                <span class="ml-auto inline-flex px-2 py-0.5 items-center justify-center rounded-full bg-gray-100 text-[10px] font-bold text-gray-600" :class="sidebarToggle ? 'lg:hidden' : ''">
                                    {{ $histCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @endcanany

                    @canany(['menu.amnesa-dokter', 'menu.sesi-terapi'])
                    <li>
                        <a href="{{ route('therapy-sessions.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('therapy-sessions*', 'doctor-exam*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 100-8 4 4 0 000 8z M4 21a8 8 0 0116 0 M12 14v4m-2-2h4" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Sesi Terapi Pasien
                            </span>
                        </a>
                    </li>
                    @endcanany

                    {{-- Kasir & Pembayaran --}}
                    @can('menu.kasir')
                    <li>
                        <a href="{{ route('cashier.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('cashier*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Kasir & Pembayaran
                            </span>
                            @php
                                $unpaidResCount = \App\Models\Reservation::where('payment_status', 'unpaid')
                                    ->where('status', '!=', 'cancelled')
                                    ->where(function($q) {
                                        $q->whereHas('sessions')->orWhere('total_price', '>', 0);
                                    })->count();
                            @endphp
                            @if($unpaidResCount > 0)
                                <span class="ml-auto inline-flex h-5 w-5 items-center justify-center rounded-full bg-amber-100 text-[10px] font-bold text-amber-800" :class="sidebarToggle ? 'lg:hidden' : ''">
                                    {{ $unpaidResCount }}
                                </span>
                            @endif
                        </a>
                    </li>
                    @endcan

                    {{-- Stok Peralatan Terapi --}}
                    @can('menu.apoteker')
                    <li>
                        <a href="{{ route('therapy-equipments.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('therapy-equipments*', 'pharmacy*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Stok Peralatan Terapi
                            </span>
                        </a>
                    </li>
                    @endcan
                </ul>

                @if(auth()->user()->can('menu.master-data') || auth()->user()->can('menu.pasien') || auth()->user()->can('menu.terapis') || auth()->user()->can('menu.jenjang-terapi') || auth()->user()->can('menu.peralatan-terapi'))
                <!-- MASTER DATA -->
                <h3 class="mb-4 text-xs uppercase text-gray-400 font-semibold tracking-wider">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Master Data</span>
                </h3>

                <ul class="flex flex-col gap-3 mb-6">
                    @canany(['menu.pasien', 'menu.master-data'])
                    <li>
                        <a href="{{ route('patients.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('patients*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0116 0" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Pasien
                            </span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['menu.terapis', 'menu.master-data'])
                    <li>
                        <a href="{{ route('therapists.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('therapists*', 'doctors*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Terapis
                            </span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['menu.peralatan-terapi', 'menu.apoteker', 'menu.master-data'])
                    <li>
                        <a href="{{ route('therapy-equipments.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('therapy-equipments*', 'medicines*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Peralatan Terapi
                            </span>
                        </a>
                    </li>
                    @endcanany

                    @canany(['menu.jenjang-terapi', 'menu.master-data'])
                    <li>
                        <a href="{{ route('therapy-types.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('therapy-types*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Jenjang Terapi
                            </span>
                        </a>
                    </li>
                    @endcanany
                </ul>
                @endif

                @can('menu.rekap-medis')
                <!-- LAPORAN -->
                <h3 class="mb-4 text-xs uppercase text-gray-400 font-semibold tracking-wider">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Laporan</span>
                </h3>

                <ul class="flex flex-col gap-3 mb-6">
                    <li>
                        <a href="{{ route('reports.medical_records.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->is('reports/medical-records*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Rekap Rekam Medis
                            </span>
                        </a>
                    </li>
                </ul>
                @endcan

                @if(auth()->user()->can('menu.users') || auth()->user()->can('menu.roles'))
                <!-- SETTINGS -->
                <h3 class="mb-4 text-xs uppercase text-gray-400 font-semibold tracking-wider">
                    <span :class="sidebarToggle ? 'lg:hidden' : ''">Settings</span>
                </h3>

                <ul class="flex flex-col gap-3 mb-6">
                    @canany(['menu.roles'])
                    <li>
                        <a href="{{ route('roles.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('roles*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3l7 3v6c0 4.5-3 8.5-7 9-4-.5-7-4.5-7-9V6l7-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9a2 2 0 100 4 2 2 0 000-4zM8.5 16c0-1.5 7-1.5 7 0" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                Roles
                            </span>
                        </a>
                    </li>
                    @endcanany

                    @can('menu.users')
                    <li>
                        <a href="{{ route('users.index') }}"
                            class="menu-item group flex items-center gap-3
                            {{ request()->routeIs('users*') ? 'menu-item-active' : 'menu-item-inactive' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 21a8 8 0 0116 0" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 10v2m-1-1h2" />
                            </svg>
                            <span class="menu-item-text font-medium text-sm" :class="sidebarToggle ? 'lg:hidden' : ''">
                                User
                            </span>
                        </a>
                    </li>
                    @endcan
                </ul>
                @endif

            @endif
        </nav>
    </div>
</aside>
