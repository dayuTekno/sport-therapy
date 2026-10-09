@extends('admin.layouts.app')

@section('title', 'Manajemen Paket Langganan SaaS')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Paket Langganan & Pricing'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>💎</span> Manajemen Paket Langganan & Pricing
        </h1>
        <p class="text-xs text-gray-500 mt-1">Konfigurasi tier paket langganan SaaS, kuota terapis, dan tarif bulanan/tahunan.</p>
    </div>
</div>

@include('admin.partials.alert')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- DAFTAR PAKET AKTIF --}}
    <div class="lg:col-span-2 space-y-4">
        @foreach($plans as $p)
            @php
                $features = is_array($p->features_json) ? $p->features_json : json_decode($p->features_json, true) ?? [];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between">
                <div class="flex items-start justify-between border-b border-gray-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-gray-900">{{ $p->name }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-100 text-gray-500' }}">
                                {{ $p->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                        @if($p->subtitle)
                            <p class="text-xs font-semibold text-blue-700 mt-0.5">{{ $p->subtitle }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">{{ $p->description ?: 'Paket langganan resmi platform.' }}</p>
                        <div class="mt-2 text-xs font-bold text-emerald-700">
                            {{ $p->formatted_commitment }}
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-xl font-black text-gray-900 font-mono">
                            {{ $p->formatted_monthly_rate }}
                        </span>
                        <span class="text-[10px] text-gray-400 block font-medium">Total: Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 py-4 text-xs">
                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Kuota Terapis:</span>
                        <span class="font-bold text-gray-800">{{ $p->max_therapists }} Praktisi</span>
                    </div>
                    <div class="p-2.5 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Kuota Pasien:</span>
                        <span class="font-bold text-gray-800">{{ number_format($p->max_patients) }} / bln</span>
                    </div>
                    <div class="p-2.5 bg-blue-50/70 rounded-xl border border-blue-100">
                        <span class="text-[10px] text-blue-600 font-bold uppercase block">Klinik Aktif:</span>
                        <span class="font-bold text-blue-900">{{ $p->clinics_count }} Mitra</span>
                    </div>
                </div>

                @if(!empty($features))
                    <div class="space-y-1 py-2 text-xs text-gray-600">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Fitur Paket:</span>
                        @foreach($features as $f)
                            <div class="flex items-center gap-1.5 text-[11px]">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>{{ $f }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- FORM TAMBAH PAKET BARU --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-900 text-sm mb-4 pb-2 border-b border-gray-100 flex items-center gap-1.5">
                <span>➕</span> Tambah Tier Paket Baru
            </h3>

            <form action="{{ route('saas.plans.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf

                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Nama Paket <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Contoh: PAKET BERLANGGANAN 2 TAHUN"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Sub-Judul / Tagline Komitmen
                    </label>
                    <input type="text" name="subtitle" placeholder="(Paket Minimum Subscribe Awal — Paling Fleksibel)"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Total Kontrak (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="price" required min="0" placeholder="1500000"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Siklus Komitmen <span class="text-rose-500">*</span>
                        </label>
                        <select name="billing_cycle" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <option value="6_months">6 Bulan</option>
                            <option value="yearly">1 Tahun (Tahunan)</option>
                            <option value="monthly">Bulanan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Tarif / Bulan (Rp)
                        </label>
                        <input type="number" name="monthly_rate" min="0" placeholder="250000"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Durasi (Bulan)
                        </label>
                        <input type="number" name="duration_in_months" min="1" placeholder="6"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Keterangan Komitmen Periode
                    </label>
                    <input type="text" name="commitment_label" placeholder="Total Periode 6 Bulan Pertama: Rp 1.500.000,- (All-in)"
                           class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Max Terapis <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="max_therapists" required min="1" value="15"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Max Pasien <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="max_patients" required min="1" value="2000"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Deskripsi Singkat
                    </label>
                    <textarea name="description" rows="2" placeholder="Uraian peruntukan paket..."
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">
                        Daftar Fitur (Satu baris per fitur)
                    </label>
                    <textarea name="features" rows="4" placeholder="Fitur A&#10;Fitur B&#10;Fitur C"
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                    Simpan Paket Baru
                </button>
            </form>
        </div>
    </div>
</div>

@endsection
