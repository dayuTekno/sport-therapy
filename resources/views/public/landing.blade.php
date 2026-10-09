<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportClinic.io — Platform SaaS Manajemen Klinik Fisioterapi & Olahraga Modern</title>
    <meta name="description" content="Platform cloud multi-tenant untuk manajemen klinik fisioterapi, rehabilitasi cedera olahraga berjenjang, otomasi booking WhatsApp, kasir dan rekam medis digital.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white" x-data="{ mobileMenuOpen: false }">

    {{-- ========================================================================= --}}
    {{-- 1. NAVBAR UTAMA (MODERN GLASSMORPHISM)                                   --}}
    {{-- ========================================================================= --}}
    <header class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-200/80 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 flex items-center leading-tight">
                        SportClinic<span class="text-blue-600">.io</span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-indigo-50 border border-indigo-100/80 px-1.5 py-0.5 rounded-md inline-block">
                        PaaS / SaaS Platform
                    </span>
                </div>
            </a>

            {{-- Desktop Navigation Links --}}
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-600">
                <a href="#fitur" class="hover:text-blue-600 transition-colors py-1">Fitur Klinis</a>
                <a href="#alur" class="hover:text-blue-600 transition-colors py-1">Alur Terapi</a>
                <a href="#komparasi" class="hover:text-blue-600 transition-colors py-1">Keunggulan</a>
                <a href="#pricing" class="hover:text-blue-600 transition-colors py-1">Paket & Harga</a>
                <a href="#faq" class="hover:text-blue-600 transition-colors py-1">FAQ</a>
            </nav>

            {{-- Right Actions --}}
            <div class="hidden sm:flex items-center gap-3">
                @auth
                    <a href="{{ auth()->user()->isSaasAdmin() ? route('saas.dashboard') : route('dashboard') }}" 
                       class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition flex items-center gap-2">
                        <span>Buka Dashboard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 text-xs font-bold text-slate-700 hover:text-blue-600 transition">
                        Masuk Klinik
                    </a>
                    <a href="{{ route('register.clinic') }}" 
                       class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/25 transition flex items-center gap-1.5">
                        <span>Coba Gratis 14 Hari</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @endauth
            </div>

            {{-- Mobile Menu Hamburger --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div x-show="mobileMenuOpen" x-transition class="lg:hidden bg-white border-b border-slate-200 px-4 pt-3 pb-6 space-y-3">
            <a href="#fitur" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700">Fitur Klinis</a>
            <a href="#alur" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700">Alur Terapi</a>
            <a href="#komparasi" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700">Keunggulan</a>
            <a href="#pricing" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700">Paket & Harga</a>
            <a href="#faq" @click="mobileMenuOpen = false" class="block py-2 text-sm font-semibold text-slate-700">FAQ</a>
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                @auth
                    <a href="{{ auth()->user()->isSaasAdmin() ? route('saas.dashboard') : route('dashboard') }}" class="w-full text-center py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold">
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="w-full text-center py-2 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold">
                        Masuk Portal Klinik
                    </a>
                    <a href="{{ route('register.clinic') }}" class="w-full text-center py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/25">
                        Daftar Uji Coba Gratis
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ========================================================================= --}}
    {{-- 2. HERO SECTION DENGAN UI PREVIEW MOCKUP PROFESIONAL                     --}}
    {{-- ========================================================================= --}}
    <section class="relative overflow-hidden pt-12 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white via-slate-50 to-slate-100">
        {{-- Background grid decoration --}}
        <div class="absolute inset-0 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:24px_24px] opacity-40 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Kiri: Teks Value Proposition & Call to Action --}}
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    {{-- Pill Badge --}}
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50/90 border border-blue-200/80 text-blue-700 text-xs font-semibold shadow-xs">
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                        </span>
                        <span>{{ $hero->content_json['badge_text'] ?? 'Platform Operasional Fisioterapi & Sport Clinic No. 1' }}</span>
                    </div>

                    {{-- Main Headline --}}
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-[1.12]">
                        {{ $hero->title ?? 'Platform Manajemen Klinik Fisioterapi & Cedera Olahraga Modern' }}
                    </h1>

                    {{-- Subtitle --}}
                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed mx-auto lg:mx-0 font-normal">
                        {{ $hero->subtitle ?? 'Tingkatkan efisiensi klinik dengan sistem otomatisasi reservasi WhatsApp, lembar evaluasi rehabilitasi fisik berjenjang (Tahap A s/d D), rekam medis digital khusus atlet, dan billing kasir instan.' }}
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('register.clinic') }}" 
                           class="w-full sm:w-auto px-7 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm rounded-xl shadow-xl shadow-blue-500/25 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>🚀</span>
                            <span>{{ $hero->content_json['cta_primary_text'] ?? 'Mulai Uji Coba Gratis (14 Hari)' }}</span>
                        </a>

                        <a href="#pricing" 
                           class="w-full sm:w-auto px-6 py-4 bg-white hover:bg-slate-50 text-slate-800 font-bold text-sm rounded-xl border border-slate-200 shadow-xs transition flex items-center justify-center gap-2">
                            <span>{{ $hero->content_json['cta_secondary_text'] ?? 'Lihat Paket & Harga' }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                    </div>

                    {{-- Trust Checkmarks --}}
                    <div class="pt-4 flex flex-wrap items-center justify-center lg:justify-start gap-5 sm:gap-6 text-xs text-slate-500 font-medium">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Tanpa Kartu Kredit</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Setup Mandiri < 2 Menit</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>Dukungan Multi-Terapis</span>
                        </span>
                    </div>

                    {{-- Social Proof Mini Strip --}}
                    <div class="pt-2 flex items-center justify-center lg:justify-start gap-3">
                        <div class="flex -space-x-2">
                            <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">DW</div>
                            <div class="w-7 h-7 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">RA</div>
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">BS</div>
                            <div class="w-7 h-7 rounded-full bg-amber-600 text-white text-[10px] font-bold flex items-center justify-center border-2 border-white">FT</div>
                        </div>
                        <div class="text-xs text-slate-500 text-left">
                            <div class="text-amber-500 font-bold tracking-tight">★★★★★ <span class="text-slate-800 font-bold">4.9 / 5.0</span></div>
                            <span class="text-[11px]">Dipercaya pemilik klinik fisioterapi & kedokteran olahraga</span>
                        </div>
                    </div>

                </div>

                {{-- Kanan: Mockup UI Software Terapi Interaktif & Elegan --}}
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-lg lg:max-w-none">
                        
                        {{-- Ambient Gradient Halo --}}
                        <div class="absolute -inset-2 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-3xl opacity-20 blur-2xl"></div>

                        {{-- Main OS Window Container --}}
                        <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-200/90 overflow-hidden">
                            
                            {{-- Window Title Bar --}}
                            <div class="bg-slate-100/90 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-3 h-3 rounded-full bg-rose-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                                </div>
                                <div class="px-3 py-1 rounded-md bg-white border border-slate-200 text-[11px] font-mono text-slate-500 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>app.sportclinic.io/sessions/active</span>
                                </div>
                                <span class="text-[10px] font-bold font-mono text-slate-400">v2.5 PRO</span>
                            </div>

                            {{-- Window Body Content --}}
                            <div class="p-5 space-y-4 bg-slate-50/50">
                                
                                {{-- Card Pasien Sedang Terapi --}}
                                <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-xs">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 font-black text-xs flex items-center justify-center shrink-0">
                                                RS
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900 leading-tight">Rian Santoso (24 th)</h4>
                                                <p class="text-[11px] text-slate-500">Atlet Sepak Bola • Sesi #3 Hari Ini</p>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                            Sedang Terapi
                                        </span>
                                    </div>

                                    {{-- Tahapan Berjenjang Tracker --}}
                                    <div class="mt-4 pt-3 border-t border-slate-100">
                                        <div class="flex items-center justify-between text-[11px] font-semibold text-slate-700 mb-2">
                                            <span>Protokol Rehabilitasi ACL</span>
                                            <span class="text-blue-600 font-bold">Tahap B (Fase 2)</span>
                                        </div>
                                        <div class="grid grid-cols-4 gap-1.5 text-[10px] font-bold text-center">
                                            <div class="py-1.5 px-1 rounded-md bg-emerald-600 text-white flex items-center justify-center gap-1 shadow-2xs">
                                                <span>A</span> <span class="text-[9px]">✓</span>
                                            </div>
                                            <div class="py-1.5 px-1 rounded-md bg-blue-600 text-white flex items-center justify-center gap-1 shadow-2xs animate-pulse">
                                                <span>B</span> <span class="text-[9px]">⏳</span>
                                            </div>
                                            <div class="py-1.5 px-1 rounded-md bg-slate-200 text-slate-500">
                                                <span>C</span>
                                            </div>
                                            <div class="py-1.5 px-1 rounded-md bg-slate-200 text-slate-500">
                                                <span>D</span>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-[10px] text-slate-500 flex justify-between">
                                            <span>Fase Reduksi Nyeri (Done)</span>
                                            <span class="font-bold text-blue-600">ROM & Strengthening</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mini Widget Kasir Terpadu --}}
                                <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-3.5 rounded-xl border border-emerald-200 text-xs flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs">
                                            💳
                                        </div>
                                        <div>
                                            <span class="text-[10px] font-bold uppercase text-emerald-800 tracking-wider block">Billing Kasir Otomatis</span>
                                            <span class="text-xs font-bold text-slate-900 font-mono">INV-20261009-08 • Rp 350.000</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-600 text-white">
                                        LUNAS
                                    </span>
                                </div>

                                {{-- Mini Widget WhatsApp Notification --}}
                                <div class="bg-white p-3 rounded-xl border border-slate-200 text-xs flex items-center gap-3 shadow-2xs">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-sm shrink-0">
                                        📱
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-slate-900">WhatsApp Reminder</span>
                                            <span class="text-[9px] text-slate-400">Baru Saja</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 truncate">
                                            "Halo Kak Rian, konfirmasi jadwal terapi Anda pukul 14:00 WIB..."
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 3. METRICS & IMPACT BANNER (BUKTI EFISIENSI)                              --}}
    {{-- ========================================================================= --}}
    <section class="py-12 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 lg:gap-8 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                
                <div class="pt-4 md:pt-0 text-center px-4">
                    <div class="text-3xl lg:text-4xl font-extrabold text-blue-600 font-mono">4 Tahap</div>
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mt-1">Protokol Klinis Berjenjang</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Fase Akut hingga Return to Sport</p>
                </div>

                <div class="pt-4 md:pt-0 text-center px-4">
                    <div class="text-3xl lg:text-4xl font-extrabold text-indigo-600 font-mono">98.5%</div>
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mt-1">Kehadiran Tepat Waktu</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Pengingat WhatsApp 1-klik otomatis</p>
                </div>

                <div class="pt-4 md:pt-0 text-center px-4">
                    <div class="text-3xl lg:text-4xl font-extrabold text-emerald-600 font-mono">100%</div>
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mt-1">Isolasi Data Aman</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tenant scoping database terenkripsi</p>
                </div>

                <div class="pt-4 md:pt-0 text-center px-4">
                    <div class="text-3xl lg:text-4xl font-extrabold text-slate-900 font-mono">< 2 Menit</div>
                    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider mt-1">Setup Siap Pakai</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Tanpa instalasi server lokal rumit</p>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 4. FITUR UNGGULAN KLINIS (BENTO GRID MODUL SAAS)                         --}}
    {{-- ========================================================================= --}}
    <section id="fitur" class="py-20 lg:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-widest bg-blue-100/70 border border-blue-200 px-3.5 py-1 rounded-full">
                    Dirancang Khusus Fisioterapi
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-4">
                    {{ $features->title ?? 'Fitur Unggulan Dirancang Khusus untuk Fisioterapi & Sport Clinic' }}
                </h2>
                <p class="text-sm sm:text-base text-slate-600 mt-3 leading-relaxed">
                    {{ $features->subtitle ?? 'Bukan sekadar sistem rumah sakit umum yang kaku. Setiap fitur dioptimalkan untuk alur pemulihan fisik atlet, penanganan cedera ligamen, dan kepuasan pasien aktif.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @php
                    $featureList = $features->content_json ?: [];
                @endphp
                @foreach($featureList as $item)
                    <div class="bg-white p-8 rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-blue-300 transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <div class="w-14 h-14 rounded-2xl bg-blue-50 group-hover:bg-blue-600 text-blue-600 group-hover:text-white border border-blue-100 group-hover:border-blue-600 flex items-center justify-center text-2xl transition-all duration-200 mb-6 shadow-2xs">
                                {{ $item['icon'] ?? '⚡' }}
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $item['title'] ?? '-' }}
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-2.5">
                                {{ $item['description'] ?? '-' }}
                            </p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-1.5 text-xs font-semibold text-blue-600 group-hover:translate-x-1 transition-transform">
                            <span>Pelajari cara kerja</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 5. ALUR KERJA TERPADU (4 STEP CLINICAL REHABILITATION WORKFLOW)           --}}
    {{-- ========================================================================= --}}
    <section id="alur" class="py-20 lg:py-24 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-widest bg-emerald-50 border border-emerald-200 px-3.5 py-1 rounded-full">
                    Alur Kerja Sederhana
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-4">
                    Bagaimana Sistem Menghubungkan Admisi, Terapis & Kasir
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Proses terstruktur yang meminimalisir kesalahan manusia dan mempercepat rotasi pasien.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                
                {{-- Step 1 --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 relative group hover:bg-white hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-extrabold text-sm flex items-center justify-center mb-5 shadow-sm">
                        01
                    </div>
                    <h4 class="font-bold text-base text-slate-900">Reservasi Pasien</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Pasien mendaftar online atau via staf admisi. Jadwal otomatis terkunci dan tombol kirim WhatsApp siap 1-klik sebagai pengingat.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 relative group hover:bg-white hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center mb-5 shadow-sm">
                        02
                    </div>
                    <h4 class="font-bold text-base text-slate-900">Tindakan Berjenjang</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Terapis menjalankan sesi fisioterapi dan mengisi evaluasi tahapan klinis (Tahap A Akut s/d Tahap D Return to Sport).
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 relative group hover:bg-white hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 text-white font-extrabold text-sm flex items-center justify-center mb-5 shadow-sm">
                        03
                    </div>
                    <h4 class="font-bold text-base text-slate-900">Kasir & Billing Terpadu</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Tagihan terkumpul otomatis per reservasi kendati pasien melakukan beberapa sesi terapi berjenjang di hari yang sama.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 relative group hover:bg-white hover:shadow-lg transition">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-extrabold text-sm flex items-center justify-center mb-5 shadow-sm">
                        04
                    </div>
                    <h4 class="font-bold text-base text-slate-900">Cetak Struk & Arsip</h4>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        Kwitansi kasir resmi tercetak instan dan data langsung dialihkan ke menu riwayat terapi sebagai arsip digital jangka panjang.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 6. PERBANDINGAN: CARA LAMA VS SPORTCLINIC.IO                             --}}
    {{-- ========================================================================= --}}
    <section id="komparasi" class="py-20 bg-slate-50 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-widest bg-indigo-50 border border-indigo-200 px-3.5 py-1 rounded-full">
                    Mengapa Beralih?
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-4">
                    Cara Manual vs Kecepatan SportClinic.io
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Lihat bagaimana efisiensi operasional meningkat drastis setelah mendigitalisasi klinik Anda.
                </p>
            </div>

            <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-200">
                    
                    {{-- Cara Konvensional --}}
                    <div class="p-8 space-y-4 bg-rose-50/20">
                        <div class="flex items-center gap-2 text-rose-700 font-extrabold text-base mb-6">
                            <span class="text-xl">❌</span>
                            <span>Klinik Konvensional (Buku / Excel)</span>
                        </div>

                        <div class="space-y-3 text-xs sm:text-sm text-slate-600">
                            <div class="flex items-start gap-2.5">
                                <span class="text-rose-500 font-bold shrink-0">✕</span>
                                <span>Pencatatan reservasi di buku tulis rentan bentrok jadwal dan hilang.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-rose-500 font-bold shrink-0">✕</span>
                                <span>Staf harus menyimpan nomor WhatsApp manual untuk mengingatkan pasien.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-rose-500 font-bold shrink-0">✕</span>
                                <span>Progres evaluasi cedera atlet tidak memiliki tahapan klinis terukur (A-D).</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-rose-500 font-bold shrink-0">✕</span>
                                <span>Pembayaran kasir terpisah-pisah per tindakan sehingga sulit direkapitulasi.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-rose-500 font-bold shrink-0">✕</span>
                                <span>Stok peralatan terapi (taping, es, perban) sering habis tanpa peringatan.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dengan SportClinic.io --}}
                    <div class="p-8 space-y-4 bg-emerald-50/20">
                        <div class="flex items-center gap-2 text-emerald-700 font-extrabold text-base mb-6">
                            <span class="text-xl">✅</span>
                            <span>Dengan SportClinic.io Cloud Platform</span>
                        </div>

                        <div class="space-y-3 text-xs sm:text-sm text-slate-700">
                            <div class="flex items-start gap-2.5">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>Manajemen antrean & slot jam terapi otomatis rapi tanpa overlap.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>1-klik pengingat WhatsApp dengan template pesan ramah dan profesional.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>Protokol evaluasi berjenjang (Tahap A-D) terdokumentasi visual.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>1 tagihan kasir per reservasi dengan kwitansi resmi yang siap dicetak.</span>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <span class="text-emerald-600 font-bold shrink-0">✓</span>
                                <span>Stok modalitas terpantau realtime per gudang klinik Anda.</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 7. PAKET & HARGA BERLANGGANAN (TRANSPARENT PRICING TIERS)                 --}}
    {{-- ========================================================================= --}}
    <section id="pricing" class="py-20 lg:py-28 bg-slate-50/60 border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-xs font-bold text-teal-700 uppercase tracking-widest bg-teal-50 border border-teal-200 px-3.5 py-1 rounded-full">
                    Investasi Transparan & Fleksibel
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight mt-4">
                    Pilihan Paket Berlangganan Smart SportClinic
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Skema langganan fleksibel tanpa biaya tersembunyi. Termasuk seluruh modul operasional terapi berjenjang, kasir, dan rekam medis.
                </p>
            </div>

            {{-- TABEL RINCIAN BIAYA & KOMITMEN (PERSIS SESUAI SPESIFIKASI TABEL PRICING) --}}
            <div class="max-w-5xl mx-auto mb-16 overflow-hidden rounded-2xl shadow-xl border border-teal-900/15">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gradient-to-r from-[#00828a] to-[#0a6c74] text-white">
                            <th class="py-4.5 px-6 sm:px-8 text-sm sm:text-base font-bold tracking-wide w-1/2">
                                Pilihan Paket
                            </th>
                            <th class="py-4.5 px-6 sm:px-8 text-sm sm:text-base font-bold tracking-wide w-1/2 text-right sm:text-left">
                                Rincian Biaya & Komitmen
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-teal-100/60">
                        @php
                            $plan6 = $plans->firstWhere('slug', 'paket-6-bulan') ?? $plans->first();
                            $plan1Year = $plans->firstWhere('slug', 'paket-1-tahun') ?? $plans->skip(1)->first();
                        @endphp
                        {{-- ROW 1: PAKET 6 BULAN --}}
                        @if($plan6)
                        <tr class="bg-[#edf9f9] hover:bg-[#e4f6f6] transition-colors">
                            <td class="py-6 px-6 sm:px-8 align-top">
                                <h3 class="text-base sm:text-lg font-black text-[#003865] tracking-tight">
                                    {{ $plan6->name }}
                                </h3>
                                <p class="text-xs sm:text-sm font-semibold text-[#003865]/80 mt-1">
                                    {{ $plan6->subtitle ?: '(Paket Minimum Subscribe Awal — Paling Fleksibel)' }}
                                </p>
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800">
                                        ⚡ Kuota 5 Terapis & 500 Pasien/bln
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        ✓ Coba 14 Hari Gratis
                                    </span>
                                </div>
                            </td>
                            <td class="py-6 px-6 sm:px-8 align-top">
                                <div class="flex flex-col sm:items-start items-end justify-between h-full">
                                    <div>
                                        <div class="text-lg sm:text-xl font-black text-[#003865] font-mono">
                                            {{ $plan6->formatted_monthly_rate }}
                                        </div>
                                        <div class="text-xs sm:text-sm font-bold text-[#003865] mt-2">
                                            {{ $plan6->formatted_commitment }}
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('register.clinic', ['plan_id' => $plan6->id]) }}" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-bold shadow-sm transition">
                                            <span>Pilih Paket 6 Bulan</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif

                        {{-- ROW 2: PAKET 1 TAHUN --}}
                        @if($plan1Year)
                        <tr class="bg-white hover:bg-slate-50 transition-colors">
                            <td class="py-6 px-6 sm:px-8 align-top">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base sm:text-lg font-black text-[#003865] tracking-tight">
                                        {{ $plan1Year->name }}
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                        Hemat Ekstra
                                    </span>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-[#003865]/80 mt-1">
                                    {{ $plan1Year->subtitle ?: '(Hemat Ekstra — Tarif Bulanan Lebih Ringan)' }}
                                </p>
                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                        🚀 Kuota 15 Terapis & 2.500 Pasien/bln
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        ⭐ Rekomendasi Klinik
                                    </span>
                                </div>
                            </td>
                            <td class="py-6 px-6 sm:px-8 align-top">
                                <div class="flex flex-col sm:items-start items-end justify-between h-full">
                                    <div>
                                        <div class="text-lg sm:text-xl font-black text-[#003865] font-mono">
                                            {{ $plan1Year->formatted_monthly_rate }}
                                        </div>
                                        <div class="text-xs sm:text-sm font-bold text-[#003865] mt-2">
                                            {{ $plan1Year->formatted_commitment }}
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('register.clinic', ['plan_id' => $plan1Year->id]) }}" 
                                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold shadow-md shadow-blue-500/20 transition">
                                            <span>Pilih Paket 1 Tahun (Hemat)</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- KARTU DETAIL FITUR & KEUNGGULAN (2 COLUMN GRID) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch max-w-5xl mx-auto">
                @foreach($plans as $plan)
                    @php
                        $isFeatured = $plan->slug === 'paket-1-tahun';
                        $planFeatures = is_array($plan->features_json) ? $plan->features_json : json_decode($plan->features_json, true) ?? [];
                    @endphp
                    <div class="relative bg-white rounded-3xl border {{ $isFeatured ? 'border-2 border-blue-600 shadow-2xl ring-4 ring-blue-50' : 'border-slate-200 shadow-sm' }} p-8 flex flex-col justify-between hover:shadow-lg transition">
                        
                        @if($isFeatured)
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-md">
                                Pilihan Terpopuler (Hemat 20%)
                            </div>
                        @else
                            <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1 bg-teal-700 text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-md">
                                Paling Fleksibel
                            </div>
                        @endif

                        <div>
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-extrabold text-slate-900">{{ $plan->name }}</h3>
                            </div>
                            <p class="text-xs font-semibold text-blue-700 mt-1">{{ $plan->subtitle }}</p>
                            <p class="text-xs text-slate-500 mt-2">{{ $plan->description }}</p>

                            {{-- Price display --}}
                            <div class="my-6 py-4 border-y border-slate-100">
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-3xl sm:text-4xl font-black text-slate-900 font-mono">
                                        Rp {{ number_format($plan->monthly_rate, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">/ bulan</span>
                                </div>
                                <div class="text-xs font-bold text-slate-700 mt-1.5 flex items-center gap-1.5">
                                    <span class="text-blue-600">📌</span>
                                    <span>{{ $plan->formatted_commitment }}</span>
                                </div>
                                <span class="text-[11px] text-emerald-600 font-bold block mt-2">✓ Termasuk Uji Coba Gratis 14 Hari Pertama</span>
                            </div>

                            {{-- Core Limits & Features --}}
                            <div class="space-y-3.5 text-xs sm:text-sm text-slate-700">
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    <span class="text-blue-600">👥</span>
                                    <span>Kapasitas hingga <strong>{{ $plan->max_therapists }} Terapis Terdaftar</strong></span>
                                </div>
                                <div class="font-bold text-slate-900 flex items-center gap-2">
                                    <span class="text-blue-600">🏃‍♂️</span>
                                    <span>Kapasitas hingga <strong>{{ number_format($plan->max_patients) }} Pasien / Bulan</strong></span>
                                </div>
                                @foreach($planFeatures as $feat)
                                    <div class="flex items-start gap-2.5">
                                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        <span class="text-xs text-slate-600">{{ $feat }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-8 pt-4">
                            <a href="{{ route('register.clinic', ['plan_id' => $plan->id]) }}" 
                               class="w-full py-3.5 px-4 rounded-xl text-center text-xs font-bold transition block {{ $isFeatured ? 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white shadow-lg shadow-blue-500/25' : 'bg-slate-900 hover:bg-slate-800 text-white' }}">
                                Daftar {{ $plan->name }} (Coba 14 Hari) →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Custom enterprise consultation box --}}
            <div class="mt-12 p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left max-w-5xl mx-auto">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm">Butuh langganan multi-cabang rumah sakit atau kustomisasi alur khusus?</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Kami menyediakan dedicated server hosting, migrasi database lama, dan training staf langsung di lokasi.</p>
                </div>
                <a href="#kontak" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shrink-0">
                    Konsultasi Kebutuhan Khusus 💬
                </a>
            </div>

        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 8. FAQ SECTION (PERTANYAAN SERING DIAJUKAN)                              --}}
    {{-- ========================================================================= --}}
    <section id="faq" class="py-20 bg-slate-50 border-t border-slate-200/80" x-data="{ activeFaq: 1 }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-blue-700 uppercase tracking-widest bg-blue-50 border border-blue-200 px-3.5 py-1 rounded-full">
                    Pertanyaan Umum
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-4">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Jawaban transparan untuk membantu Anda memulai tanpa keraguan.
                </p>
            </div>

            <div class="space-y-4">
                
                {{-- FAQ Item 1 --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                    <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full px-6 py-5 text-left font-bold text-slate-900 text-sm flex items-center justify-between gap-4">
                        <span>Apakah data rekam medis pasien di klinik kami aman dan terisolasi?</span>
                        <span class="text-slate-400 font-mono text-lg" x-text="activeFaq === 1 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 1" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Sangat aman. Platform kami menggunakan arsitektur <em>Scoped Multi-Tenancy</em> tingkat lanjut di mana setiap data pasien, tindakan terapi, dan transaksi kasir secara ketat terisolasi berdasarkan ID klinik masing-masing. Klinik lain tidak memiliki akses ke data klinik Anda.
                    </div>
                </div>

                {{-- FAQ Item 2 --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                    <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full px-6 py-5 text-left font-bold text-slate-900 text-sm flex items-center justify-between gap-4">
                        <span>Apakah sistem ini dapat dibuka menggunakan tablet atau iPad di ruang terapi?</span>
                        <span class="text-slate-400 font-mono text-lg" x-text="activeFaq === 2 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 2" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Ya! Seluruh tampilan SportClinic.io didesain 100% responsif. Terapis dapat membawa tablet atau iPad saat menangani pasien untuk mencatat evaluasi dan langsung menyimpan data pemulihan secara real-time.
                    </div>
                </div>

                {{-- FAQ Item 3 --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                    <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full px-6 py-5 text-left font-bold text-slate-900 text-sm flex items-center justify-between gap-4">
                        <span>Bagaimana cara kerja pengingat WhatsApp ke pasien?</span>
                        <span class="text-slate-400 font-mono text-lg" x-text="activeFaq === 3 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 3" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Di menu reservasi terapi terdapat tombol langsung berlogo WhatsApp. Saat diklik, sistem secara otomatis merangkai pesan personal (nama pasien, jadwal jam, nama terapis penanggung jawab) dan membuka aplikasi WhatsApp tanpa perlu menyimpan nomor pasien di kontak HP.
                    </div>
                </div>

                {{-- FAQ Item 4 --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                    <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full px-6 py-5 text-left font-bold text-slate-900 text-sm flex items-center justify-between gap-4">
                        <span>Apakah kami perlu membayar sebelum mencoba fitur-fiturnya?</span>
                        <span class="text-slate-400 font-mono text-lg" x-text="activeFaq === 4 ? '−' : '+'"></span>
                    </button>
                    <div x-show="activeFaq === 4" x-collapse class="px-6 pb-5 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-3">
                        Tidak. Anda dapat langsung mendaftarkan klinik dan menikmati masa uji coba gratis (Free Trial) selama 14 hari penuh dengan semua fitur aktif tanpa kewajiban memasukkan nomor kartu kredit.
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 9. PRE-FOOTER CALL TO ACTION (CTA BANNER)                                --}}
    {{-- ========================================================================= --}}
    <section class="py-20 bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-tight">
                Siap Membawa Pengalaman Terapi Klinik Anda ke Level Berikutnya?
            </h2>
            <p class="text-sm sm:text-base text-blue-100 max-w-2xl mx-auto leading-relaxed">
                Bergabunglah dengan klinik fisioterapi modern di Indonesia. Mulai uji coba gratis 14 hari Anda sekarang dan rasakan kemudahannya.
            </p>
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register.clinic') }}" 
                   class="w-full sm:w-auto px-8 py-4 bg-white hover:bg-slate-100 text-blue-700 font-bold text-sm rounded-xl shadow-xl transition transform hover:-translate-y-0.5">
                    Daftarkan Klinik Saya Sekarang 🚀
                </a>
                <a href="#kontak" 
                   class="w-full sm:w-auto px-7 py-4 bg-blue-800/60 hover:bg-blue-800 text-white font-bold text-sm rounded-xl border border-white/20 transition">
                    Konsultasi dengan Tim Kami
                </a>
            </div>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- 10. FOOTER ELEGAN (KONTAK, LEGAL & IDENTITY)                             --}}
    {{-- ========================================================================= --}}
    <footer id="kontak" class="bg-slate-950 text-white pt-16 pb-12 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-800/80 text-xs">
                
                {{-- Col 1: Identity & Contact info --}}
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-base">
                            ⚡
                        </div>
                        <span class="text-lg font-black tracking-tight text-white">SportClinic<span class="text-blue-500">.io</span></span>
                    </div>
                    <p class="text-slate-400 max-w-sm leading-relaxed">
                        Platform PaaS / SaaS operasional klinik fisioterapi, rehabilitasi cedera fisik atlet, dan manajemen kasir terpadu nomor satu di Indonesia.
                    </p>
                    <div class="text-slate-400 space-y-1.5 pt-2">
                        <div class="flex items-center gap-2">
                            <span>📧</span>
                            <span>{{ $contact->content_json['email'] ?? 'support@sportclinic.io' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>📱</span>
                            <span>{{ $contact->content_json['phone'] ?? '+62 851-8303-6722' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>🏢</span>
                            <span>{{ $contact->content_json['address'] ?? 'Darma Ayu Tekno, Jakarta Selatan, Indonesia' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Col 2: Navigation --}}
                <div>
                    <h4 class="font-bold text-white uppercase tracking-wider mb-4 text-[11px]">Navigasi Solusi</h4>
                    <ul class="space-y-2.5 text-slate-400">
                        <li><a href="#fitur" class="hover:text-white transition">Fitur Klinis</a></li>
                        <li><a href="#alur" class="hover:text-white transition">Alur Rehabilitasi</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Paket Langganan</a></li>
                        <li><a href="{{ route('register.clinic') }}" class="hover:text-white transition">Pendaftaran Mitra Baru</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Portal Login Klinik</a></li>
                    </ul>
                </div>

                {{-- Col 3: Compliance & Security --}}
                <div>
                    <h4 class="font-bold text-white uppercase tracking-wider mb-4 text-[11px]">Standar Keamanan</h4>
                    <ul class="space-y-2.5 text-slate-400">
                        <li class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span>
                            <span>Isolasi Data Per Tenant</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span>
                            <span>Enkripsi Database Cloud</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span>
                            <span>Otomasi Backup Harian</span>
                        </li>
                        <li class="flex items-center gap-1.5">
                            <span class="text-emerald-400">✓</span>
                            <span>SLA Server 99.9% Uptime</span>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Bottom Copyright Strip --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
                <div>
                    &copy; {{ date('Y') }} SportClinic.io — Dikembangkan oleh <strong>Darma Ayu Tekno</strong>. Seluruh Hak Cipta Dilindungi.
                </div>
                <div class="font-mono text-[11px] text-slate-600">
                    Platform as a Service (PaaS) • Multi-Tenancy Architecture
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
