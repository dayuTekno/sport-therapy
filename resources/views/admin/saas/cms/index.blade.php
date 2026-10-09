@extends('admin.layouts.app')

@section('title', 'Kelola Bagian Luar Website (CMS Landing Page)')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'CMS Bagian Luar Website'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>🎨</span> Kelola Bagian Luar Website (CMS Landing Page)
        </h1>
        <p class="text-xs text-gray-500 mt-1">Ubah tampilan headline, fitur unggulan, dan informasi kontak publik di halaman depan website.</p>
    </div>

    <a href="{{ url('/') }}" target="_blank" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 transition flex items-center gap-1.5">
        <span>🌐</span> Buka Landing Page Publik ↗
    </a>
</div>

@include('admin.partials.alert')

<div class="space-y-8" x-data="{ activeTab: 'hero' }">

    {{-- TAB NAVIGATION --}}
    <div class="border-b border-gray-200">
        <nav class="flex space-x-6 text-sm font-bold">
            <button @click="activeTab = 'hero'" 
                    :class="activeTab === 'hero' ? 'border-blue-600 text-blue-600 border-b-2' : 'text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 transition flex items-center gap-2">
                <span>⚡</span> Hero Banner Utama
            </button>
            <button @click="activeTab = 'features'" 
                    :class="activeTab === 'features' ? 'border-blue-600 text-blue-600 border-b-2' : 'text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 transition flex items-center gap-2">
                <span>🩺</span> Fitur Unggulan (6 Kartu)
            </button>
            <button @click="activeTab = 'contact'" 
                    :class="activeTab === 'contact' ? 'border-blue-600 text-blue-600 border-b-2' : 'text-gray-500 hover:text-gray-700'"
                    class="py-3 px-1 transition flex items-center gap-2">
                <span>📱</span> Kontak & Footer Publik
            </button>
        </nav>
    </div>

    {{-- 1. HERO SECTION SETTINGS --}}
    <div x-show="activeTab === 'hero'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Pengaturan Hero Banner Utama</h3>
                <p class="text-xs text-gray-500">Teks headline pembuka yang paling pertama dilihat oleh calon mitra klinik.</p>
            </div>
            <span class="text-[10px] font-bold uppercase bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full border border-blue-100">
                Section #1
            </span>
        </div>

        <form action="{{ route('saas.cms.hero') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Badge Header Kecil
                </label>
                <input type="text" name="badge_text" value="{{ old('badge_text', $hero->content_json['badge_text'] ?? '') }}"
                       placeholder="Contoh: ⚡ Platform Fisioterapi & Sport Clinic No. 1 di Indonesia"
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-blue-800 bg-blue-50/30">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Headline Utama (Title) <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $hero->title) }}" required
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-900 text-sm">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Subjudul / Deskripsi Penjelas <span class="text-rose-500">*</span>
                </label>
                <textarea name="subtitle" rows="3" required
                          class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 leading-relaxed">{{ old('subtitle', $hero->subtitle) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Teks Tombol CTA Utama (Daftar) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="cta_primary_text" value="{{ old('cta_primary_text', $hero->content_json['cta_primary_text'] ?? 'Daftar Uji Coba Gratis (14 Hari)') }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Teks Tombol CTA Sekunder (Pricing) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="cta_secondary_text" value="{{ old('cta_secondary_text', $hero->content_json['cta_secondary_text'] ?? 'Lihat Paket & Harga') }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    Simpan Perubahan Hero Banner
                </button>
            </div>
        </form>
    </div>

    {{-- 2. FEATURES SECTION SETTINGS --}}
    <div x-show="activeTab === 'features'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" style="display: none;">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Pengaturan 6 Fitur Unggulan</h3>
                <p class="text-xs text-gray-500">Nilai jual dan modul keunggulan sistem yang ditampilkan di halaman depan.</p>
            </div>
            <span class="text-[10px] font-bold uppercase bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-full border border-emerald-100">
                Section #2
            </span>
        </div>

        <form action="{{ route('saas.cms.features') }}" method="POST" class="space-y-6 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 border-b border-gray-100">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Judul Section Fitur <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $features->title) }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Subjudul Section <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="subtitle" value="{{ old('subtitle', $features->subtitle) }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            @php
                $currentFeatures = $features->content_json ?: [];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @for($i = 0; $i < 6; $i++)
                    @php $f = $currentFeatures[$i] ?? []; @endphp
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-700 uppercase tracking-wider text-[10px]">Kartu Fitur #{{ $i + 1 }}</span>
                            <input type="text" name="features[{{ $i }}][icon]" value="{{ $f['icon'] ?? '⚡' }}" class="w-12 text-center text-base p-1 border rounded-lg bg-white">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 block font-semibold uppercase">Judul Fitur</label>
                            <input type="text" name="features[{{ $i }}][title]" value="{{ $f['title'] ?? '' }}" required
                                   class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white font-bold">
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 block font-semibold uppercase">Uraian Fitur</label>
                            <textarea name="features[{{ $i }}][description]" rows="2" required
                                      class="w-full px-2.5 py-1.5 text-xs border border-gray-300 rounded-lg bg-white">{{ $f['description'] ?? '' }}</textarea>
                        </div>
                    </div>
                @endfor
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    Simpan Perubahan 6 Fitur
                </button>
            </div>
        </form>
    </div>

    {{-- 3. CONTACT SECTION SETTINGS --}}
    <div x-show="activeTab === 'contact'" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8" style="display: none;">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Pengaturan Kontak & Footer</h3>
                <p class="text-xs text-gray-500">Informasi kontak dukungan pelanggan dan legalitas yang tampil di bagian bawah website.</p>
            </div>
            <span class="text-[10px] font-bold uppercase bg-purple-50 text-purple-700 px-2 py-0.5 rounded-full border border-purple-100">
                Section #3
            </span>
        </div>

        <form action="{{ route('saas.cms.contact') }}" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Email Resmi SaaS <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $contact->content_json['email'] ?? 'sales@sportclinic.io') }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nomor WhatsApp / Telepon <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone', $contact->content_json['phone'] ?? '+62 851-8303-6722') }}" required
                           class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Alamat Kantor Penyedia Platform <span class="text-rose-500">*</span>
                </label>
                <textarea name="address" rows="2" required
                          class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('address', $contact->content_json['address'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Jam Layanan Operasional
                </label>
                <input type="text" name="hours" value="{{ old('hours', $contact->content_json['hours'] ?? 'Senin - Sabtu: 08.00 - 20.00 WIB') }}"
                       class="w-full px-3.5 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    Simpan Perubahan Kontak & Footer
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
