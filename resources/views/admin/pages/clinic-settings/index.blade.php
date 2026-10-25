@extends('admin.layouts.app')

@section('title', 'Pengaturan Klinik & Format Surat')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Settings', 'url' => '#'],
    ['label' => 'Pengaturan Klinik'],
]" />

{{-- HEADER --}}
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
            <span>⚙️</span> Pengaturan Klinik & Pengesahan Surat
        </h1>
        <p class="text-xs text-gray-500 mt-1">
            Kelola profil klinik, logo, stempel cap resmi, tanda tangan, serta nama pemeriksa tertanda untuk Surat Keterangan Sakit & dokumen resmi.
        </p>
    </div>
</div>

@include('admin.partials.alert')

@if ($errors->any())
    <div class="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700">
        <div class="font-bold mb-1">Gagal menyimpan pengaturan:</div>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('clinic.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM KIRI: IDENTITAS KLINIK (2 KOLOM) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. IDENTITAS UTAMA KLINIK --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span>🏥</span> Identitas Resmi Klinik
                </h3>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Nama Klinik / Rumah Terapi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $clinic->name) }}" required
                               placeholder="Contoh: Rumah Sehat Terapi BDI"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-semibold">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Kota / Kabupaten <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="city" value="{{ old('city', $clinic->city ?? 'Indramayu') }}" required
                                   placeholder="Contoh: Indramayu"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                            <p class="text-[10px] text-gray-400 mt-1">Dicantumkan pada penanggalan surat (misal: "Indramayu, 25 September 2026")</p>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nomor Telepon / WhatsApp
                            </label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $clinic->phone_number) }}"
                                   placeholder="Contoh: 0896-5477-3992"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Email Resmi
                        </label>
                        <input type="email" name="email" value="{{ old('email', $clinic->email) }}"
                               placeholder="klinik@mail.com"
                               class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Alamat Lengkap Klinik (Dicetak di Kop Surat)
                        </label>
                        <textarea name="address" rows="3" placeholder="Contoh: Perum BDI Jl. Kenanga Blok.M-25 RT/RW 17/04 Tambak - Indramayu"
                                  class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">{{ old('address', $clinic->address) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 2. INFORMASI TERTANDA / PEMERIKSA SURAT --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <span>✍️</span> Penanggung Jawab / Pemeriksa Surat Keterangan Sakit
                </h3>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap & Gelar Tertanda
                            </label>
                            <input type="text" name="signer_name" value="{{ old('signer_name', $clinic->signer_name) }}"
                                   placeholder="Contoh: Iyon Pramulo, S.Or, M.Pd, AIFO"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs font-bold text-gray-900">
                            <p class="text-[10px] text-gray-400 mt-1">Nama terapis/dokter pemeriksa yang tercetak di bawah tanda tangan</p>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Jabatan / Profesi Pemeriksa
                            </label>
                            <input type="text" name="signer_title" value="{{ old('signer_title', $clinic->signer_title ?? 'Sport Therapis Pemeriksa') }}"
                                   placeholder="Contoh: Sport Therapis Pemeriksa"
                                   class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs">
                            <p class="text-[10px] text-gray-400 mt-1">Dicantumkan di atas cap dan tanda tangan surat</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL SIMPAN --}}
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-2 cursor-pointer">
                    <span>💾</span> Simpan Pengaturan Klinik
                </button>
            </div>

        </div>

        {{-- KOLOM KANAN: LOGO, TANDA TANGAN & CAP (1 KOLOM) --}}
        <div class="space-y-6">

            {{-- 1. UPLOAD LOGO KLINIK --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span>🖼️</span> Logo Klinik
                </h3>
                <p class="text-[11px] text-gray-500 mb-4">
                    Logo ditampilkan di pojok kanan atas kop Surat Keterangan Sakit dan header aplikasi.
                </p>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-2xl bg-gray-50 border border-gray-200 flex items-center justify-center p-2 overflow-hidden shadow-inner shrink-0">
                        @if($clinic->logo_path)
                            <img src="{{ asset('storage/' . $clinic->logo_path) }}" alt="Logo" class="w-full h-full object-contain">
                        @else
                            <span class="text-2xl">🏥</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Status Logo</span>
                        <span class="text-[10px] text-gray-400">
                            {{ $clinic->logo_path ? '✓ Terpasang' : 'Belum diunggah (Gunakan default)' }}
                        </span>
                    </div>
                </div>

                <input type="file" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-[9px] text-gray-400 mt-1.5">Format: PNG / JPG / SVG, Maks. 2MB. Disarankan background transparan.</p>
            </div>

            {{-- 2. UPLOAD CAP / STEMPEL RESMI --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span>🔏</span> Cap / Stempel Resmi
                </h3>
                <p class="text-[11px] text-gray-500 mb-4">
                    Stempel resmi klinik yang akan ditempelkan di atas tanda tangan surat.
                </p>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center p-2 overflow-hidden shadow-inner shrink-0">
                        @if($clinic->stamp_path)
                            <img src="{{ asset('storage/' . $clinic->stamp_path) }}" alt="Cap Stempel" class="w-full h-full object-contain">
                        @else
                            <div class="text-center p-1">
                                <span class="text-xs font-bold text-purple-600 border border-purple-300 rounded px-1 py-0.5 inline-block">RSTBDI</span>
                                <span class="text-[8px] text-gray-400 block mt-0.5">Contoh Cap</span>
                            </div>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Status Cap Stempel</span>
                        <span class="text-[10px] text-gray-400">
                            {{ $clinic->stamp_path ? '✓ Terpasang' : 'Belum diunggah (Contoh cap aktif)' }}
                        </span>
                    </div>
                </div>

                <input type="file" name="stamp" accept="image/png,image/jpeg,image/webp"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                <p class="text-[9px] text-gray-400 mt-1.5">Format: PNG transparan (sangat disarankan agar tumpang tindih rapi dengan tanda tangan).</p>
            </div>

            {{-- 3. UPLOAD TANDA TANGAN PEMERIKSA --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span>🖊️</span> Tanda Tangan Tertanda
                </h3>
                <p class="text-[11px] text-gray-500 mb-4">
                    Tanda tangan digital pemeriksa yang akan dibubuhkan pada dokumen surat.
                </p>

                <div class="flex items-center gap-4 mb-4">
                    <div class="w-24 h-16 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center p-2 overflow-hidden shadow-inner shrink-0">
                        @if($clinic->signature_path)
                            <img src="{{ asset('storage/' . $clinic->signature_path) }}" alt="Tanda Tangan" class="w-full h-full object-contain">
                        @else
                            <span class="text-xs text-gray-400 italic">Belum ada</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-800 block">Status Tanda Tangan</span>
                        <span class="text-[10px] text-gray-400">
                            {{ $clinic->signature_path ? '✓ Terpasang' : 'Belum diunggah' }}
                        </span>
                    </div>
                </div>

                <input type="file" name="signature" accept="image/png,image/jpeg,image/webp"
                       class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-[9px] text-gray-400 mt-1.5">Format: PNG transparan tanpa background putih.</p>
            </div>

            {{-- 4. PRATINJAU AREA CAP & TTD PADA SURAT --}}
            <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 rounded-2xl border border-blue-100 p-5">
                <h4 class="text-xs font-bold text-blue-900 uppercase tracking-wider mb-2">Pratinjau Pengesahan Surat:</h4>
                <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm text-right text-xs">
                    <div class="text-[11px] text-gray-600">@if(filled($clinic->city)){{ $clinic->city }}@else<span class="italic font-normal text-gray-400">belum diatur</span>@endif, {{ now()->translatedFormat('d F Y') }}</div>
                    <div class="text-[11px] font-semibold text-gray-800 mb-2">@if(filled($clinic->signer_title)){{ $clinic->signer_title }}@else<span class="italic font-normal text-gray-400">belum diatur</span>@endif</div>

                    {{-- Stacking Cap & TTD --}}
                    <div class="relative h-20 w-48 ml-auto my-1 flex items-center justify-center">
                        @if($clinic->stamp_path)
                            <img src="{{ asset('storage/' . $clinic->stamp_path) }}" class="absolute left-2 w-20 h-20 object-contain opacity-85 z-0" alt="Cap">
                        @else
                            <div class="absolute left-0 text-[10px] italic text-gray-400 border border-dashed border-gray-300 rounded px-2 py-1 z-0">
                                Cap: belum diatur
                            </div>
                        @endif

                        @if($clinic->signature_path)
                            <img src="{{ asset('storage/' . $clinic->signature_path) }}" class="absolute inset-0 w-full h-full object-contain z-10" alt="TTD">
                        @else
                            <div class="absolute right-0 text-[10px] italic text-gray-400 border border-dashed border-gray-300 rounded px-2 py-1 z-10">
                                TTD: belum diatur
                            </div>
                        @endif
                    </div>

                    <div class="font-bold text-gray-900 text-xs mt-1">
                        @if(filled($clinic->signer_name))
                            <span class="underline">{{ $clinic->signer_name }}</span>
                        @else
                            <span class="italic font-normal text-gray-400">belum diatur</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</form>

@endsection
