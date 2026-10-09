@extends('admin.layouts.app')

@section('title', 'SaaS Platform Dashboard')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator'],
    ['label' => 'Platform Dashboard'],
]" />

{{-- Header --}}
<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>👑</span> Administrator SaaS - SportClinic.io
        </h1>
        <p class="text-xs text-gray-500 mt-1">Pusat kendali ekosistem multi-tenant, analitik pendapatan, dan manajemen klinik mitra.</p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('saas.cms.index') }}" class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 transition flex items-center gap-1.5">
            <span>🎨</span> Kelola Landing Page
        </a>
        <a href="{{ route('saas.clinics.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
            <span>+</span> Daftarkan Klinik Baru
        </a>
    </div>
</div>

@include('admin.partials.alert')

{{-- METRIK UTAMA PLATFORM SAAS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Card 1: MRR (Monthly Recurring Revenue) --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black text-xl shrink-0">
            💰
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Estimasi MRR SaaS</span>
            <span class="text-xl font-black text-emerald-700 font-mono">Rp {{ number_format($mrr, 0, ',', '.') }}</span>
            <span class="text-[10px] text-emerald-600 font-medium block mt-0.5">Pendapatan langganan/bln</span>
        </div>
    </div>

    {{-- Card 2: Total Klinik Terdaftar --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xl shrink-0">
            🏥
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Total Mitra Klinik</span>
            <div class="flex items-baseline gap-2">
                <span class="text-xl font-black text-gray-900">{{ $totalClinics }}</span>
                <span class="text-[11px] font-bold text-emerald-600">({{ $activeClinics }} Aktif)</span>
            </div>
            <span class="text-[10px] text-amber-600 font-medium block mt-0.5">{{ $trialClinics }} Masa Uji Coba</span>
        </div>
    </div>

    {{-- Card 3: Total Terapis Global --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black text-xl shrink-0">
            👨‍⚕️
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Terapis di Seluruh Klinik</span>
            <span class="text-xl font-black text-indigo-700">{{ $totalTherapists }} Fisioterapis</span>
            <span class="text-[10px] text-gray-500 font-medium block mt-0.5">Praktisi profesional</span>
        </div>
    </div>

    {{-- Card 4: Total Pasien & Sesi Terlayani --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center font-black text-xl shrink-0">
            🏃‍♂️
        </div>
        <div>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">Total Pasien Terlayani</span>
            <span class="text-xl font-black text-purple-700">{{ $totalPatients }} Pasien</span>
            <span class="text-[10px] text-gray-500 font-medium block mt-0.5">{{ $totalSessions }} Sesi Terapi Selesai</span>
        </div>
    </div>
</div>

{{-- PINTASAN KENDALI SAAS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <a href="{{ route('saas.clinics.index') }}" class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white p-5 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between group">
        <div>
            <span class="text-xs text-blue-200 font-semibold uppercase tracking-wider block">Manajemen Tenant</span>
            <h4 class="text-base font-bold mt-0.5">Daftar Klinik Mitra</h4>
            <p class="text-xs text-blue-200/80 mt-1">Kelola kuota, aktivasi, dan impersonasi klinik.</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl group-hover:translate-x-1 transition">
            →
        </div>
    </a>

    <a href="{{ route('saas.plans.index') }}" class="bg-gradient-to-r from-emerald-900 to-teal-900 text-white p-5 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between group">
        <div>
            <span class="text-xs text-emerald-200 font-semibold uppercase tracking-wider block">Monetisasi SaaS</span>
            <h4 class="text-base font-bold mt-0.5">Paket Langganan & Pricing</h4>
            <p class="text-xs text-emerald-200/80 mt-1">Konfigurasi tarif Starter, Pro, dan Enterprise.</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl group-hover:translate-x-1 transition">
            →
        </div>
    </a>

    <a href="{{ route('saas.cms.index') }}" class="bg-gradient-to-r from-purple-900 to-pink-900 text-white p-5 rounded-2xl shadow-sm hover:shadow-md transition flex items-center justify-between group">
        <div>
            <span class="text-xs text-purple-200 font-semibold uppercase tracking-wider block">Bagian Luar Website</span>
            <h4 class="text-base font-bold mt-0.5">CMS Landing Page Publik</h4>
            <p class="text-xs text-purple-200/80 mt-1">Edit headline, fitur, dan info kontak publik.</p>
        </div>
        <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl group-hover:translate-x-1 transition">
            →
        </div>
    </a>
</div>

{{-- TABEL: KLINIK MITRA TERBARU --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <span>🏥</span> Mitra Klinik Terdaftar Terbaru
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Klinik yang baru saja mendaftar atau aktif dalam platform.</p>
        </div>
        <a href="{{ route('saas.clinics.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
            Lihat Semua Klinik →
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                <tr>
                    <th class="p-4">Kode & Nama Klinik</th>
                    <th class="p-4">Paket Langganan</th>
                    <th class="p-4">Kontak / Email</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Terdaftar</th>
                    <th class="p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($recentClinics as $c)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <span class="font-mono text-[10px] text-gray-400 font-bold block">{{ $c->clinic_code }}</span>
                            <span class="font-bold text-gray-900 text-sm">{{ $c->name }}</span>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                                {{ $c->plan?->name ?? 'Free Tier' }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-600">
                            <div>📧 {{ $c->email ?? '-' }}</div>
                            <div class="font-mono text-[11px] text-gray-400 mt-0.5">📱 {{ $c->phone_number ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-center">
                            @if($c->status === 'active')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Aktif
                                </span>
                            @elseif($c->status === 'trial')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    ⏳ Trial 14 Hari
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    ✕ Suspended
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center text-gray-500">
                            {{ $c->created_at->format('d M Y') }}
                        </td>
                        <td class="p-4 text-right whitespace-nowrap space-x-1">
                            <a href="{{ route('saas.clinics.impersonate', $c->id) }}"
                               class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-xs transition"
                               title="Masuk dan kelola klinik ini sebagai admin">
                                👁️ Tinjau Klinik
                            </a>
                            <a href="{{ route('saas.clinics.edit', $c->id) }}" 
                               class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-xs transition">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-400">Belum ada klinik terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
