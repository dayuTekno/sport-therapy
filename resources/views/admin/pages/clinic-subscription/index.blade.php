@extends('admin.layouts.app')

@section('title', 'Langganan Paket Klinik')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Settings', 'url' => '#'],
    ['label' => 'Langganan Paket'],
]" />

@php
    $expired = $clinic->isSubscriptionExpired();
    $days = $clinic->daysRemaining();
    $statusText = match (true) {
        $expired => 'Expired / Habis',
        $clinic->status === 'trial' => 'Masa Uji Coba (Trial)',
        $clinic->status === 'active' => 'Berlangganan Aktif',
        default => ucfirst($clinic->status),
    };
    $endDate = $clinic->effectiveEndDate();
@endphp

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
        <span>💳</span> Langganan Paket
    </h1>
    <p class="text-xs text-gray-500 mt-1">Pilih paket untuk berlangganan atau memperpanjang masa aktif klinik. Paket aktif setelah pembayaran diverifikasi admin.</p>
</div>

@include('admin.partials.alert')

{{-- STATUS LANGGANAN SAAT INI --}}
<div class="bg-white rounded-2xl shadow-sm border {{ $expired ? 'border-rose-200' : 'border-gray-100' }} p-5 mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
    <div>
        <span class="text-gray-400 font-semibold block">Status</span>
        <span class="font-bold {{ $expired ? 'text-rose-600' : ($clinic->status === 'trial' ? 'text-amber-600' : 'text-emerald-600') }}">{{ $statusText }}</span>
    </div>
    <div>
        <span class="text-gray-400 font-semibold block">Paket Saat Ini</span>
        <span class="font-bold text-gray-900">{{ $clinic->plan?->name ?? '-' }}</span>
    </div>
    <div>
        <span class="text-gray-400 font-semibold block">Berlaku Hingga</span>
        <span class="font-bold text-gray-900">{{ $endDate?->format('d M Y') ?? '-' }}</span>
    </div>
    <div>
        <span class="text-gray-400 font-semibold block">Sisa Waktu</span>
        <span class="font-bold {{ $days !== null && $days <= 7 ? 'text-rose-600' : 'text-gray-900' }}">{{ $days !== null ? max(0, $days) . ' hari' : '-' }}</span>
    </div>
</div>

@if($pendingRequest)
    {{-- REQUEST MENUNGGU VERIFIKASI --}}
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6 text-xs">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Menunggu Verifikasi Admin</span>
                <h3 class="text-sm font-bold text-gray-900 mt-2">{{ $pendingRequest->plan?->name }} — Rp {{ number_format($pendingRequest->amount, 0, ',', '.') }}</h3>
                <p class="text-gray-600 mt-1">
                    No. Request <span class="font-mono font-bold">{{ $pendingRequest->invoice_code }}</span> ·
                    {{ $pendingRequest->payment_method }} · dikirim {{ $pendingRequest->created_at?->format('d M Y, H:i') }}
                </p>
                @if($pendingRequest->payment_proof_path)
                    <a href="{{ route('clinic.subscription.proof', $pendingRequest->id) }}" target="_blank" class="inline-block mt-2 font-bold text-blue-600 hover:underline">📎 Lihat bukti pembayaran</a>
                @else
                    <p class="mt-2 font-semibold text-rose-600">Bukti pembayaran belum diunggah — unggah agar verifikasi lebih cepat.</p>
                @endif
            </div>
            <div class="flex flex-col gap-2 sm:w-72">
                <form action="{{ route('clinic.subscription.upload-proof', $pendingRequest->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-2">
                    @csrf
                    <input type="file" name="payment_proof" required accept=".jpg,.jpeg,.png,.webp,.pdf"
                           class="flex-1 min-w-0 text-[11px] file:mr-2 file:px-2 file:py-1 file:rounded-lg file:border-0 file:bg-white file:text-gray-700 file:font-semibold">
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-bold shrink-0 cursor-pointer">Unggah</button>
                </form>
                <form action="{{ route('clinic.subscription.cancel', $pendingRequest->id) }}" method="POST" onsubmit="return confirm('Batalkan request pembelian paket ini?')">
                    @csrf
                    <button type="submit" class="w-full px-3 py-1.5 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-[11px] font-bold cursor-pointer">Batalkan Request</button>
                </form>
            </div>
        </div>
    </div>
@else
    {{-- FORM PEMBELIAN PAKET --}}
    <form action="{{ route('clinic.subscription.store') }}" method="POST" enctype="multipart/form-data"
          x-data="{ planId: @js((string) old('subscription_plan_id', $clinic->plan?->is_active ? $clinic->subscription_plan_id : $plans->first()?->id)) }"
          class="mb-6">
        @csrf

        <h3 class="text-sm font-bold text-gray-800 mb-3">1. Pilih Paket</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @forelse($plans as $plan)
                @php $features = is_array($plan->features_json) ? $plan->features_json : []; @endphp
                <label class="block bg-white rounded-2xl border-2 p-5 cursor-pointer transition"
                       :class="planId === '{{ $plan->id }}' ? 'border-blue-500 shadow-md shadow-blue-500/10' : 'border-gray-100 hover:border-blue-200'">
                    <input type="radio" name="subscription_plan_id" value="{{ $plan->id }}" x-model="planId" class="sr-only">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-sm font-black text-gray-900 block">{{ $plan->name }}</span>
                            @if($plan->subtitle)
                                <span class="text-[11px] text-blue-700 font-semibold">{{ $plan->subtitle }}</span>
                            @endif
                        </div>
                        <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0"
                              :class="planId === '{{ $plan->id }}' ? 'border-blue-600 bg-blue-600' : 'border-gray-300'">
                            <span class="w-2 h-2 rounded-full bg-white" x-show="planId === '{{ $plan->id }}'"></span>
                        </span>
                    </div>
                    <div class="mt-3">
                        <span class="text-xl font-black text-gray-900 font-mono">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                        <span class="text-[11px] text-gray-500 block">{{ $plan->formatted_monthly_rate }} · {{ \App\Models\ClinicSubscription::planDurationInMonths($plan) }} bulan</span>
                    </div>
                    <div class="mt-3 text-[11px] text-gray-600">
                        Kuota {{ $plan->max_therapists }} terapis · {{ number_format($plan->max_patients, 0, ',', '.') }} pasien/bln
                    </div>
                    @if(!empty($features))
                        <ul class="mt-3 space-y-1 text-[11px] text-gray-600">
                            @foreach(array_slice($features, 0, 4) as $f)
                                <li class="flex gap-1.5"><span class="text-emerald-500 font-bold">✓</span><span>{{ $f }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </label>
            @empty
                <div class="md:col-span-2 p-6 bg-white rounded-2xl border border-gray-100 text-center text-xs text-gray-400">Belum ada paket yang tersedia.</div>
            @endforelse
        </div>

        @if($plans->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 grid grid-cols-1 lg:grid-cols-2 gap-6 text-xs">
                <div>
                    <h3 class="text-sm font-bold text-gray-800 mb-3">2. Lakukan Pembayaran</h3>
                    @if($paymentInstructions)
                        <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-xl text-blue-950 whitespace-pre-line leading-relaxed">{{ $paymentInstructions }}</div>
                    @else
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                            Hubungi admin platform untuk informasi rekening / metode pembayaran:
                            <a href="https://wa.me/6285183036722?text={{ rawurlencode('Halo Admin, saya ingin informasi rekening / metode pembayaran paket langganan untuk klinik ' . $clinic->name . '.') }}"
                               target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1 font-bold text-emerald-600 hover:text-emerald-700 hover:underline whitespace-nowrap">
                                💬 0851-8303-6722 (WhatsApp)
                            </a>
                        </div>
                    @endif
                    <p class="text-[10px] text-gray-400 mt-2">Jika masa langganan masih berjalan, masa aktif paket baru disambung dari tanggal berakhir saat ini.</p>
                </div>

                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-gray-800">3. Kirim Konfirmasi</h3>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                        <select name="payment_method" required class="w-full px-3 py-2.5 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 font-semibold">
                            @foreach(['Transfer Bank', 'QRIS', 'Lainnya'] as $method)
                                <option value="{{ $method }}" @selected(old('payment_method') === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Bukti Pembayaran</label>
                        <input type="file" name="payment_proof" accept=".jpg,.jpeg,.png,.webp,.pdf"
                               class="w-full text-xs border border-gray-300 rounded-xl p-1.5 file:mr-3 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-gray-100 file:text-gray-700 file:font-semibold">
                        <p class="text-[10px] text-gray-400 mt-1">JPG, PNG, WEBP, atau PDF maks. 4 MB. Bisa diunggah belakangan.</p>
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 uppercase tracking-wider mb-1.5">Catatan (Opsional)</label>
                        <textarea name="notes" rows="2" placeholder="Misal: transfer dari rekening a.n. ..."
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                        Kirim Request Pembelian Paket
                    </button>
                </div>
            </div>
        @endif
    </form>
@endif

{{-- RIWAYAT REQUEST --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-800">Riwayat Pembelian Paket</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">No. Request</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Paket</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Nominal</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Masa Aktif / Catatan Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($requests as $req)
                    <tr>
                        <td class="px-4 py-3">
                            <span class="font-mono font-bold text-gray-900 block">{{ $req->invoice_code }}</span>
                            <span class="text-[10px] text-gray-400">{{ $req->created_at?->format('d M Y, H:i') }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $req->plan?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-gray-900">Rp {{ number_format($req->amount, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $req->status_class }}">{{ $req->status_label }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if($req->payment_status === 'paid')
                                {{ $req->starts_at?->format('d M Y') }} – {{ $req->expires_at?->format('d M Y') }}
                            @endif
                            @if($req->admin_notes)
                                <span class="block text-[11px] {{ in_array($req->payment_status, ['rejected', 'failed']) ? 'text-rose-600' : 'text-gray-500' }}">{{ $req->admin_notes }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-gray-400">Belum ada riwayat pembelian paket.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
