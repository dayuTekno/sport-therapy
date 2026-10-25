@extends('admin.layouts.app')

@section('title', 'Request Pembelian Paket')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Request Pembelian Paket'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
        <span>🧾</span> Monitoring Request Pembelian Paket
    </h1>
    <p class="text-xs text-gray-500 mt-1">Verifikasi pembayaran klinik lalu setujui secara manual. Saat disetujui, paket klinik langsung aktif dan masa langganan diperpanjang sesuai durasi paket.</p>
</div>

@include('admin.partials.alert')

{{-- RINGKASAN --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Menunggu Verifikasi</span>
        <span class="text-2xl font-black text-amber-600">{{ (int) $summary->pending_count }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Nominal Menunggu</span>
        <span class="text-lg font-black text-gray-900">Rp {{ number_format($summary->pending_amount, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Disetujui Bulan Ini</span>
        <span class="text-2xl font-black text-emerald-600">{{ (int) $summary->paid_month_count }}</span>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <span class="text-[11px] text-gray-500 font-semibold block">Pendapatan Bulan Ini</span>
        <span class="text-lg font-black text-emerald-600">Rp {{ number_format($summary->paid_month_amount, 0, ',', '.') }}</span>
    </div>
</div>

{{-- TAB STATUS & PENCARIAN --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-5">
    <form method="GET" action="{{ route('saas.subscription-requests.index') }}" class="flex flex-wrap gap-3 items-center justify-between">
        <div class="inline-flex p-0.5 bg-gray-100 rounded-lg text-xs font-semibold flex-wrap">
            @foreach(['pending' => 'Menunggu', 'paid' => 'Disetujui', 'rejected' => 'Ditolak', 'cancelled' => 'Dibatalkan', 'all' => 'Semua'] as $key => $label)
                <a href="{{ route('saas.subscription-requests.index', array_filter(['status' => $key, 'search' => request('search')])) }}"
                   class="px-3 py-1.5 rounded-md transition {{ $status === $key ? 'bg-white text-blue-700 shadow-xs font-bold' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ $label }}
                    @if($key === 'pending' && $summary->pending_count > 0)
                        <span class="ml-1 px-1.5 rounded-full bg-amber-100 text-amber-700 text-[10px]">{{ (int) $summary->pending_count }}</span>
                    @endif
                </a>
            @endforeach
        </div>
        <div class="flex gap-2">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. request / klinik..."
                   class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-56">
            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">Cari</button>
        </div>
    </form>
</div>

{{-- DAFTAR REQUEST --}}
<div class="space-y-4 mb-8">
    @forelse($subscriptions as $sub)
        @php
            $clinic = $sub->clinic;
            $clinicEnd = $clinic?->subscription_ends_at;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border {{ $sub->payment_status === 'pending' ? 'border-amber-200' : 'border-gray-100' }} p-5 text-xs"
             x-data="{ action: '' }">
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 flex-1">
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Klinik</span>
                        @if($clinic)
                            <a href="{{ route('saas.clinics.edit', $clinic->id) }}" class="font-bold text-gray-900 hover:text-blue-600 block">{{ $clinic->name }}</a>
                            <span class="text-[10px] text-gray-500">{{ $clinic->clinic_code }} · {{ ucfirst($clinic->status) }}{{ $clinicEnd ? ' s/d ' . $clinicEnd->format('d M Y') : '' }}</span>
                        @else
                            <span class="text-gray-400">Klinik terhapus</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Paket Diminta</span>
                        <span class="font-bold text-gray-900 block">{{ $sub->plan?->name ?? '-' }}</span>
                        <span class="text-[10px] text-gray-500">{{ \App\Models\ClinicSubscription::planDurationInMonths($sub->plan) }} bulan</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Nominal</span>
                        <span class="font-mono font-black text-gray-900 block">Rp {{ number_format($sub->amount, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-gray-500">{{ $sub->payment_method ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase block">Request</span>
                        <span class="font-mono font-bold text-gray-900 block">{{ $sub->invoice_code }}</span>
                        <span class="text-[10px] text-gray-500">{{ $sub->created_at?->format('d M Y, H:i') }} · {{ $sub->requester?->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex flex-col items-start lg:items-end gap-2 shrink-0">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sub->status_class }}">{{ $sub->status_label }}</span>
                    @if($sub->payment_proof_path)
                        <a href="{{ route('clinic.subscription.proof', $sub->id) }}" target="_blank" class="font-bold text-blue-600 hover:underline">📎 Lihat Bukti Bayar</a>
                    @else
                        <span class="text-[11px] text-rose-500 font-semibold">Belum ada bukti bayar</span>
                    @endif
                </div>
            </div>

            @if($sub->notes)
                <div class="mt-3 p-2.5 bg-gray-50 rounded-lg text-gray-600"><span class="font-bold">Catatan klinik:</span> {{ $sub->notes }}</div>
            @endif

            @if($sub->payment_status === 'pending')
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex flex-wrap gap-2" x-show="action === ''">
                        <button type="button" @click="action = 'approve'" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition cursor-pointer">✓ Setujui & Aktifkan Paket</button>
                        <button type="button" @click="action = 'reject'" class="px-4 py-2 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-xl font-bold transition cursor-pointer">✕ Tolak</button>
                    </div>

                    <form x-show="action === 'approve'" style="display: none;" action="{{ route('saas.subscription-requests.approve', $sub->id) }}" method="POST" class="space-y-2">
                        @csrf
                        @php
                            $startPreview = ($clinic?->status === 'active' && $clinicEnd?->isFuture()) ? $clinicEnd->copy() : now();
                            $endPreview = $startPreview->copy()->addMonthsNoOverflow(\App\Models\ClinicSubscription::planDurationInMonths($sub->plan));
                        @endphp
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900">
                            Paket <strong>{{ $sub->plan?->name }}</strong> akan aktif <strong>{{ $startPreview->format('d M Y') }}</strong> s/d <strong>{{ $endPreview->format('d M Y') }}</strong>.
                            Kuota klinik mengikuti paket ({{ $sub->plan?->max_therapists }} terapis, {{ number_format($sub->plan?->max_patients ?? 0, 0, ',', '.') }} pasien/bln).
                        </div>
                        <textarea name="admin_notes" rows="2" placeholder="Catatan verifikasi (opsional), misal: transfer BCA diterima 24/10"
                                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition cursor-pointer">Konfirmasi Setujui</button>
                            <button type="button" @click="action = ''" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                        </div>
                    </form>

                    <form x-show="action === 'reject'" style="display: none;" action="{{ route('saas.subscription-requests.reject', $sub->id) }}" method="POST" class="space-y-2">
                        @csrf
                        <textarea name="admin_notes" rows="2" required placeholder="Alasan penolakan (wajib), misal: nominal transfer tidak sesuai"
                                  class="w-full px-3 py-2 text-xs border border-rose-300 rounded-xl focus:ring-2 focus:ring-rose-500"></textarea>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition cursor-pointer">Konfirmasi Tolak</button>
                            <button type="button" @click="action = ''" class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 rounded-xl font-semibold cursor-pointer">Batal</button>
                        </div>
                    </form>
                </div>
            @elseif($sub->processed_at || $sub->admin_notes)
                <div class="mt-3 text-[11px] text-gray-500">
                    @if($sub->processed_at)
                        Diproses {{ $sub->processed_at->format('d M Y, H:i') }} oleh {{ $sub->processor?->name ?? '-' }}.
                    @endif
                    @if($sub->payment_status === 'paid')
                        Masa aktif {{ $sub->starts_at?->format('d M Y') }} – {{ $sub->expires_at?->format('d M Y') }}.
                    @endif
                    @if($sub->admin_notes)
                        <span class="block">Catatan: {{ $sub->admin_notes }}</span>
                    @endif
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center text-xs text-gray-400">
            Tidak ada request pada status ini.
        </div>
    @endforelse

    @if($subscriptions->hasPages())
        <div>{{ $subscriptions->links() }}</div>
    @endif
</div>

{{-- INSTRUKSI PEMBAYARAN --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-sm font-bold text-gray-800 mb-1">Instruksi Pembayaran untuk Klinik</h3>
    <p class="text-[11px] text-gray-500 mb-3">Ditampilkan di halaman Langganan Paket klinik, misalnya nomor rekening tujuan, a.n., atau QRIS.</p>
    <form action="{{ route('saas.subscription-requests.instructions') }}" method="POST" class="space-y-3 text-xs">
        @csrf
        <textarea name="instructions" rows="4" placeholder="Contoh: Transfer ke [nama bank] [nomor rekening] a.n. [nama pemilik rekening]&#10;Konfirmasi via WhatsApp [nomor admin]"
                  class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">{{ old('instructions', $paymentInstructions) }}</textarea>
        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md transition cursor-pointer">Simpan Instruksi</button>
    </form>
</div>

@endsection
