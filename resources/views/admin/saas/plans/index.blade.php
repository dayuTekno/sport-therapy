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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between"
                 x-data="{ editing: {{ (string) old('_plan_id') === (string) $p->id ? 'true' : 'false' }} }">
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

                    <div class="text-right shrink-0 ml-4">
                        <span class="text-xl font-black text-gray-900 font-mono">
                            {{ $p->formatted_monthly_rate }}
                        </span>
                        <span class="text-[10px] text-gray-400 block font-medium">Total: Rp {{ number_format($p->price, 0, ',', '.') }}</span>
                        <button type="button" @click="editing = !editing"
                                class="mt-2 px-3 py-1.5 rounded-lg text-[11px] font-bold transition cursor-pointer"
                                :class="editing ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                                x-text="editing ? 'Tutup Edit' : '✏️ Edit Paket'"></button>
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

                {{-- FORM EDIT PAKET --}}
                <form x-show="editing" @if((string) old('_plan_id') !== (string) $p->id) style="display: none;" @endif action="{{ route('saas.plans.update', $p->id) }}" method="POST"
                      class="mt-4 pt-4 border-t border-dashed border-blue-200 space-y-3.5 text-xs">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_plan_id" value="{{ $p->id }}">
                    @php $isOld = (string) old('_plan_id') === (string) $p->id; @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Paket <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required value="{{ $isOld ? old('name') : $p->name }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Sub-Judul / Tagline</label>
                            <input type="text" name="subtitle" value="{{ $isOld ? old('subtitle') : $p->subtitle }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Total Kontrak <span class="text-rose-500">*</span></label>
                            <x-rupiah-input name="price" :value="$isOld ? old('price') : $p->price" required class="font-bold" />
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Tarif / Bulan</label>
                            <x-rupiah-input name="monthly_rate" :value="$isOld ? old('monthly_rate') : $p->monthly_rate" placeholder="Otomatis" />
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Siklus <span class="text-rose-500">*</span></label>
                            @php $cycle = $isOld ? old('billing_cycle') : $p->billing_cycle; @endphp
                            <select name="billing_cycle" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <option value="6_months" @selected($cycle === '6_months')>6 Bulan</option>
                                <option value="yearly" @selected($cycle === 'yearly')>1 Tahun (Tahunan)</option>
                                <option value="monthly" @selected($cycle === 'monthly')>Bulanan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Durasi (Bulan)</label>
                            <input type="number" name="duration_in_months" min="1" value="{{ $isOld ? old('duration_in_months') : $p->duration_in_months }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Keterangan Komitmen Periode</label>
                        <input type="text" name="commitment_label" value="{{ $isOld ? old('commitment_label') : $p->commitment_label }}"
                               placeholder="Kosongkan untuk dibuat otomatis"
                               class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Max Terapis <span class="text-rose-500">*</span></label>
                            <input type="number" name="max_therapists" required min="1" value="{{ $isOld ? old('max_therapists') : $p->max_therapists }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Max Pasien <span class="text-rose-500">*</span></label>
                            <input type="number" name="max_patients" required min="1" value="{{ $isOld ? old('max_patients') : $p->max_patients }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Masa Trial (Hari)</label>
                            <input type="number" name="trial_days" min="0" max="365" value="{{ $isOld ? old('trial_days') : $p->trial_days }}"
                                   placeholder="Default"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Urutan Tampil</label>
                            <input type="number" name="sort_order" min="0" value="{{ $isOld ? old('sort_order') : $p->sort_order }}"
                                   class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Deskripsi Singkat</label>
                        <textarea name="description" rows="2"
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ $isOld ? old('description') : $p->description }}</textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1">Daftar Fitur (Satu baris per fitur)</label>
                        <textarea name="features" rows="4"
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-mono">{{ $isOld ? old('features') : implode("\n", $features) }}</textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" @checked($isOld ? old('is_active') : $p->is_active)
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="font-bold text-gray-700">Paket aktif (tampil di landing page & bisa dibeli)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="editing = false" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 font-semibold rounded-xl border border-gray-200 transition cursor-pointer">Batal</button>
                            <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">Simpan Perubahan</button>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-400">Perubahan harga & kuota berlaku untuk pembelian/aktivasi berikutnya; kuota klinik yang sudah berjalan tidak berubah otomatis.</p>
                </form>
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
                        <x-rupiah-input name="price" :value="old('_plan_id') ? '' : old('price')" required placeholder="1.500.000" class="font-bold" />
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
                        <x-rupiah-input name="monthly_rate" :value="old('_plan_id') ? '' : old('monthly_rate')" placeholder="250.000" />
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
