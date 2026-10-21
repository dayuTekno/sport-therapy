@extends('admin.layouts.app')

@section('title', 'Edit Data Referal')

@section('content')

@php
    $label = 'mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-200';
    $input = 'block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $fieldErr = ' border-red-400 focus:border-red-500 focus:ring-red-500/15';
@endphp

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Referal', 'url' => route('referrals.index')],
    ['label' => 'Edit Referal'],
]" />

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="text-xl font-bold text-gray-900">Edit Data Referal</h1>
        <p class="text-xs text-gray-500 mt-1">Perbarui profil mitra referal dan aturan komisi terapi.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('referrals.show', $referral->id) }}" class="text-xs text-emerald-600 hover:text-emerald-800 font-semibold flex items-center gap-1">
            👁️ Lihat Pasien Terafiliasi
        </a>
        <span class="text-gray-300">|</span>
        <a href="{{ route('referrals.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
            ← Kembali
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 w-full max-w-3xl mx-auto">
    <form action="{{ route('referrals.update', $referral->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- INFORMASI REFERAL --}}
        <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                <span class="text-lg">🤝</span>
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900">Identitas Mitra Referal</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Kode Referal --}}
                <div>
                    <label class="{{ $label }}">Kode Referal <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="code" 
                           value="{{ old('code', $referral->code) }}" 
                           class="{{ $input }} font-mono @error('code'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: REF-001" required>
                    @error('code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Nama Referal --}}
                <div>
                    <label class="{{ $label }}">Nama Lengkap Referal / Perujuk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $referral->name) }}" 
                           class="{{ $input }} @error('name'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: dr. Bambang, Sp.OT / Coach Rian" required>
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Asal Lembaga / Instansi --}}
                <div>
                    <label class="{{ $label }}">Asal Lembaga / Instansi / Profesi</label>
                    <input type="text" name="agency" id="agency" value="{{ old('agency', $referral->agency) }}" 
                           class="{{ $input }} @error('agency'){{ $fieldErr }}@enderror" 
                           placeholder="Contoh: RSUD Daerah, Klinik Pratama, Klub Futsal">
                    @error('agency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- No. HP / WhatsApp --}}
                <div>
                    <label class="{{ $label }}">Nomor HP / WhatsApp</label>
                    <div class="relative">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $referral->phone) }}" 
                               class="{{ $input }} pl-10 font-mono @error('phone'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: 08123456789">
                        <span class="absolute left-3.5 top-3 text-gray-400">📱</span>
                    </div>
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Email (Opsional)</label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email', $referral->email) }}" 
                               class="{{ $input }} pl-10 @error('email'){{ $fieldErr }}@enderror" 
                               placeholder="contoh@email.com">
                        <span class="absolute left-3.5 top-3 text-gray-400">✉️</span>
                    </div>
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- REKENING BANK / NOMOR DANA --}}
        <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                <span class="text-lg">🏦</span>
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900">Rekening Bank / Nomor DANA (Pembayaran Komisi)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Bank / E-Wallet --}}
                <div>
                    <label class="{{ $label }}">Bank / E-Wallet</label>
                    <div class="relative">
                        <input list="bank_options" name="bank_name" id="bank_name" 
                               value="{{ old('bank_name', $referral->bank_name) }}" 
                               class="{{ $input }} @error('bank_name'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: DANA, BCA, BRI, Mandiri">
                        <datalist id="bank_options">
                            <option value="DANA">DANA (E-Wallet)</option>
                            <option value="BCA">Bank BCA</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="Mandiri">Bank Mandiri</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="BSI">Bank Syariah Indonesia (BSI)</option>
                            <option value="CIMB Niaga">Bank CIMB Niaga</option>
                            <option value="Permata">Bank Permata</option>
                            <option value="OVO">OVO</option>
                            <option value="GoPay">GoPay</option>
                            <option value="ShopeePay">ShopeePay</option>
                        </datalist>
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Pilih dari list atau ketik bank/e-wallet.</p>
                    @error('bank_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- No. Rekening / No. DANA --}}
                <div>
                    <label class="{{ $label }}">Nomor Rekening / No. DANA</label>
                    <div class="relative">
                        <input type="text" name="bank_account_number" id="bank_account_number" 
                               value="{{ old('bank_account_number', $referral->bank_account_number) }}" 
                               class="{{ $input }} font-mono @error('bank_account_number'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: 08123456789 / 8830123456">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Nomor rekening bank atau nomor HP akun DANA.</p>
                    @error('bank_account_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Atas Nama (A/N) --}}
                <div>
                    <label class="{{ $label }}">Atas Nama (A/N)</label>
                    <div class="relative">
                        <input type="text" name="bank_account_name" id="bank_account_name" 
                               value="{{ old('bank_account_name', $referral->bank_account_name) }}" 
                               class="{{ $input }} @error('bank_account_name'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: dr. Bambang / Rian">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Nama pemilik rekening / akun DANA.</p>
                    @error('bank_account_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- PENGATURAN KOMISI & PERJANJIAN --}}
        <div>
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-gray-100">
                <span class="text-lg">💰</span>
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-900">Perhitungan Komisi & Catatan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Tarif Komisi per Sesi Terapi --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Tarif Komisi per Sesi Terapi (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-gray-500 font-bold text-sm">Rp</span>
                        <input type="number" name="commission_amount" id="commission_amount" 
                               value="{{ old('commission_amount', $referral->commission_amount ?? 0) }}" min="0" step="1000"
                               class="{{ $input }} pl-11 font-mono font-bold text-gray-900 @error('commission_amount'){{ $fieldErr }}@enderror" 
                               placeholder="Contoh: 25000">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">
                        Nominal komisi yang dihitung per sesi terapi yang berhasil diselesaikan oleh pasien rujukan mitra ini.
                    </p>
                    @error('commission_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Catatan / Keterangan --}}
                <div class="md:col-span-2">
                    <label class="{{ $label }}">Catatan Khusus / Perjanjian Kerja Sama (Opsional)</label>
                    <textarea name="notes" id="notes" rows="2" 
                              class="{{ $input }} @error('notes'){{ $fieldErr }}@enderror" 
                              placeholder="Keterangan tambahan seputar perjanjian komisi, metode transfer, dll.">{{ old('notes', $referral->notes) }}</textarea>
                    @error('notes') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status Aktif --}}
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" 
                               {{ old('is_active', $referral->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-xs font-semibold text-gray-800">Status Aktif (Tampil di pilihan referal input pasien)</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <a href="{{ route('referrals.index') }}" 
               class="px-4 py-2.5 text-sm text-gray-600 hover:text-gray-800">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-200 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
