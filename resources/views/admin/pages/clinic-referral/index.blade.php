@extends('admin.layouts.app')

@section('title', 'Referal Klinik - Undang Klinik Mitra')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Settings', 'url' => '#'],
    ['label' => 'Referal Klinik'],
]" />

@php
    $shareMessage = "Halo! Kami di {$clinic->name} memakai Smart Clinic Therapy untuk mengelola klinik terapi. "
        . "Daftar lewat link ini dan gunakan kode referal {$clinic->referral_code}: {$shareLink}";
    $subscribedCount = $referredClinics->filter(fn ($c) => $c->referralCommission)->count();
@endphp

{{-- HEADER --}}
<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
        <span>🤝</span> Referal Klinik
    </h1>
    <p class="text-xs text-gray-500 mt-1">
        Bagikan kode referal klinik Anda ke klinik lain. Komisi Anda dapatkan saat klinik yang Anda undang berlangganan paket — bukan saat mendaftar atau selama masa uji coba gratis.
    </p>
</div>

@include('admin.partials.alert')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- KARTU KODE REFERAL --}}
    <div class="lg:col-span-2 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl shadow-md p-6 text-white"
         x-data="{
            copied: '',
            copy(text, key) {
                const done = () => { this.copied = key; setTimeout(() => this.copied = '', 2000); };
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(done);
                } else {
                    const el = document.createElement('textarea');
                    el.value = text;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    el.remove();
                    done();
                }
            }
         }">
        <span class="text-[11px] font-bold uppercase tracking-wider text-blue-100">Kode Referal Klinik Anda</span>
        <div class="flex flex-wrap items-center gap-3 mt-2">
            <span class="text-3xl font-black font-mono tracking-widest">{{ $clinic->referral_code }}</span>
            <button type="button" @click="copy(@js($clinic->referral_code), 'code')"
                    class="px-3 py-1.5 bg-white/15 hover:bg-white/25 rounded-lg text-xs font-bold transition cursor-pointer">
                <span x-text="copied === 'code' ? '✓ Tersalin' : '📋 Salin Kode'"></span>
            </button>
        </div>

        <div class="mt-5">
            <span class="text-[11px] font-bold uppercase tracking-wider text-blue-100">Link Undangan Pendaftaran</span>
            <div class="mt-1.5 flex flex-col sm:flex-row gap-2">
                <input type="text" readonly value="{{ $shareLink }}" @focus="$event.target.select()"
                       class="flex-1 min-w-0 px-3 py-2 text-xs font-mono rounded-xl bg-white/10 border border-white/20 text-white focus:ring-2 focus:ring-white/40">
                <button type="button" @click="copy(@js($shareLink), 'link')"
                        class="px-4 py-2 bg-white text-blue-700 hover:bg-blue-50 rounded-xl text-xs font-bold transition shrink-0 cursor-pointer">
                    <span x-text="copied === 'link' ? '✓ Link Tersalin' : 'Salin Link'"></span>
                </button>
                <a href="https://wa.me/?text={{ rawurlencode($shareMessage) }}" target="_blank" rel="noopener"
                   class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 rounded-xl text-xs font-bold transition shrink-0 text-center">
                    Bagikan via WhatsApp
                </a>
            </div>
            <p class="text-[11px] text-blue-100 mt-2">Kode referal otomatis terisi saat klinik lain membuka link ini.</p>
        </div>
    </div>

    {{-- INFO KOMISI --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Komisi Referal</h3>
        @if($settings['is_enabled'])
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                <span class="text-[11px] text-emerald-700 block mb-1.5">Komisi per klinik sesuai paket yang dilanggan:</span>
                <ul class="space-y-1">
                    @forelse($plans as $plan)
                        <li class="flex items-center justify-between gap-2 text-[11px]">
                            <span class="text-gray-700 font-semibold">{{ $plan->name }}</span>
                            <span class="font-black text-emerald-700 font-mono whitespace-nowrap">{{ $plan->formatted_referral_commission }}</span>
                        </li>
                    @empty
                        <li class="text-[11px] text-gray-500">Belum ada paket langganan.</li>
                    @endforelse
                </ul>
            </div>
        @else
            <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl mb-4 text-[11px] text-gray-500">
                Program komisi referal sedang tidak aktif. Kode referal tetap bisa dibagikan.
            </div>
        @endif
        <ol class="space-y-2 text-[11px] text-gray-600 list-decimal list-inside">
            <li>Bagikan kode atau link undangan ke klinik lain.</li>
            <li>Klinik tersebut mendaftar dengan kode referal Anda.</li>
            <li>Selama masa uji coba gratis belum ada komisi. Komisi tercatat saat klinik itu berlangganan paket.</li>
            <li>Admin platform membayarkan komisi ke klinik Anda.</li>
        </ol>
    </div>
</div>

{{-- RINGKASAN --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Klinik Diundang</span>
        <span class="text-2xl font-black text-gray-900">{{ $referredClinics->count() }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Sudah Berlangganan</span>
        <span class="text-2xl font-black text-blue-700">{{ $subscribedCount }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Komisi Menunggu</span>
        <span class="text-lg font-black text-amber-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Komisi Diterima</span>
        <span class="text-lg font-black text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
    </div>
</div>

{{-- DAFTAR KLINIK YANG DIUNDANG --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800">Klinik yang Mendaftar dengan Kode Anda</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Klinik</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Tanggal Daftar</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Status Klinik</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Komisi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($referredClinics as $rc)
                    @php $commission = $rc->referralCommission; @endphp
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-900 block">{{ $rc->name }}</span>
                            <span class="text-[10px] text-gray-400">{{ $rc->plan?->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $rc->created_at?->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $statusClass = match ($rc->status) {
                                    'active' => 'bg-emerald-100 text-emerald-700',
                                    'trial' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                                $statusText = match ($rc->status) {
                                    'active' => 'Berlangganan',
                                    'trial' => 'Masa Trial',
                                    'expired' => 'Expired',
                                    default => ucfirst($rc->status),
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($commission)
                                <span class="font-bold font-mono {{ $commission->status === 'cancelled' ? 'text-gray-400 line-through' : 'text-gray-900' }}">
                                    Rp {{ number_format($commission->commission_amount, 0, ',', '.') }}
                                </span>
                                <span class="block text-[10px] font-semibold {{ $commission->status === 'paid' ? 'text-emerald-600' : ($commission->status === 'cancelled' ? 'text-gray-400' : 'text-amber-600') }}">
                                    {{ $commission->status_label }}
                                </span>
                            @else
                                <span class="text-[10px] text-gray-400">Menunggu klinik berlangganan</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                            Belum ada klinik yang mendaftar dengan kode referal Anda. Bagikan link undangan di atas untuk mulai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
