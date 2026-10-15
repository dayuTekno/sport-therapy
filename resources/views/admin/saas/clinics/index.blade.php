@extends('admin.layouts.app')

@section('title', 'Manajemen Klinik Mitra (Tenants)')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Klinik Mitra (Tenants)'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>🏥</span> Manajemen Mitra Klinik (Tenant Directory)
        </h1>
        <p class="text-xs text-gray-500 mt-1">Daftar seluruh klinik terapi dan sport center yang terdaftar di platform SaaS.</p>
    </div>

    <a href="{{ route('saas.clinics.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5 shrink-0">
        <span>+</span> Daftarkan Klinik Baru
    </a>
</div>

@include('admin.partials.alert')

{{-- Filter & Search --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-5">
    <form method="GET" action="{{ route('saas.clinics.index') }}" class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama, kode, email klinik..."
                       class="px-3 py-2 pl-9 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-64">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="status" class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>✓ Aktif</option>
                <option value="trial"     {{ request('status') === 'trial'     ? 'selected' : '' }}>⏳ Trial</option>
                <option value="expired"   {{ request('status') === 'expired'   ? 'selected' : '' }}>⚠️ Expired</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>✕ Suspended</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('saas.clinics.index') }}" class="text-xs text-gray-500 hover:text-rose-600 underline font-medium">Reset</a>
            @endif
        </div>

        <div class="text-xs text-gray-500">
            Total <span class="font-bold text-gray-800">{{ $clinics->total() }}</span> klinik terdaftar
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider w-8">#</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Klinik</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Kontak</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Paket</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Kuota</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($clinics as $i => $c)
                    @php
                        // Hitung sisa hari — gunakan subscription_ends_at lebih dulu, fallback ke trial_ends_at
                        $endDate   = $c->subscription_ends_at ?? ($c->status === 'trial' ? $c->trial_ends_at : null);
                        $remaining = $endDate ? (int) now()->diffInDays($endDate, false) : null;
                        $isExpiredDate = $endDate && $endDate->isPast();

                        // Tentukan warna & label status
                        $statusConfig = match($c->status) {
                            'active'    => [
                                'bg'    => $isExpiredDate ? 'bg-rose-50 border-rose-200 text-rose-700'
                                         : ($remaining !== null && $remaining <= 7 ? 'bg-amber-50 border-amber-200 text-amber-700'
                                         : 'bg-emerald-50 border-emerald-200 text-emerald-700'),
                                'icon'  => $isExpiredDate ? '⚠️' : ($remaining !== null && $remaining <= 7 ? '⏳' : '✓'),
                                'label' => $isExpiredDate ? 'Expired' : ($remaining !== null && $remaining <= 7 ? 'Segera Habis' : 'Aktif'),
                            ],
                            'trial'     => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700',   'icon' => '⏳', 'label' => 'Trial'],
                            'expired'   => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700',      'icon' => '⚠️', 'label' => 'Expired'],
                            'suspended' => ['bg' => 'bg-gray-100 border-gray-300 text-gray-600',     'icon' => '✕',  'label' => 'Suspended'],
                            default     => ['bg' => 'bg-gray-100 border-gray-200 text-gray-500',     'icon' => '?',  'label' => ucfirst($c->status)],
                        };
                    @endphp
                    <tr class="hover:bg-blue-50/30 transition-colors">

                        {{-- NO --}}
                        <td class="px-4 py-3.5 text-center text-gray-400 font-mono text-[10px]">
                            {{ $clinics->firstItem() + $i }}
                        </td>

                        {{-- KLINIK --}}
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                {{-- Avatar initial --}}
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-sm">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-sm truncate max-w-[180px]" title="{{ $c->name }}">{{ $c->name }}</p>
                                    <p class="font-mono text-[10px] text-gray-400">{{ $c->clinic_code }}</p>
                                    @if($c->subdomain)
                                        <p class="text-[10px] text-blue-500 truncate max-w-[180px]">{{ $c->subdomain }}.smartclinictherapy.com</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- KONTAK --}}
                        <td class="px-4 py-3.5">
                            <div class="text-gray-700 truncate max-w-[160px]" title="{{ $c->email }}">
                                <span class="text-gray-400">@</span> {{ $c->email ?? '-' }}
                            </div>
                            <div class="text-gray-400 font-mono text-[11px] mt-0.5">{{ $c->phone_number ?? '-' }}</div>
                            @if($c->address)
                                <div class="text-gray-400 text-[10px] truncate max-w-[160px] mt-0.5" title="{{ $c->address }}">
                                    📍 {{ $c->address }}
                                </div>
                            @endif
                        </td>

                        {{-- PAKET --}}
                        <td class="px-4 py-3.5 text-center">
                            @php $plan = $c->plan; @endphp
                            @if($plan)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 font-bold text-[11px] whitespace-nowrap">
                                    {{ $plan->name }}
                                </span>
                                <div class="text-[10px] text-gray-400 mt-1 whitespace-nowrap">
                                    Rp {{ number_format($plan->price ?? 0, 0, ',', '.') }}<span class="text-gray-300">/bln</span>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-gray-100 border border-gray-200 text-gray-500 font-semibold text-[11px]">
                                    Free Tier
                                </span>
                            @endif
                        </td>

                        {{-- KUOTA --}}
                        <td class="px-4 py-3.5 text-center">
                            <div class="text-gray-800 font-bold">{{ $c->max_therapists }} <span class="text-gray-400 font-normal">terapis</span></div>
                            <div class="text-[10px] text-gray-400">{{ number_format($c->max_patients) }} pasien</div>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-4 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border font-bold text-[11px] whitespace-nowrap {{ $statusConfig['bg'] }}">
                                {{ $statusConfig['icon'] }} {{ $statusConfig['label'] }}
                            </span>

                            {{-- Sub-info tanggal --}}
                            <div class="mt-1 text-[10px] whitespace-nowrap">
                                @if($endDate)
                                    @if($isExpiredDate)
                                        <span class="text-rose-400">Habis {{ $endDate->format('d M Y') }}</span>
                                    @elseif($remaining !== null && $remaining <= 30)
                                        <span class="text-amber-500 font-semibold">{{ $remaining }} hari lagi</span>
                                    @else
                                        <span class="text-gray-400">s/d {{ $endDate->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td class="px-4 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('saas.clinics.impersonate', $c->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-[11px] transition whitespace-nowrap"
                                   title="Masuk sebagai Administrator Klinik Ini">
                                    👁️ Tinjau
                                </a>
                                <a href="{{ route('saas.clinics.edit', $c->id) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-[11px] transition">
                                    ✏️ Edit
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <div class="text-gray-300 text-4xl mb-3">🏥</div>
                            <p class="text-gray-500 font-semibold">Belum ada klinik yang sesuai kriteria</p>
                            <p class="text-gray-400 text-[11px] mt-1">Coba ubah filter atau daftarkan klinik baru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between gap-4 flex-wrap">
        <p class="text-xs text-gray-500">
            Menampilkan <span class="font-bold text-gray-700">{{ $clinics->firstItem() ?? 0 }}–{{ $clinics->lastItem() ?? 0 }}</span>
            dari <span class="font-bold text-gray-700">{{ $clinics->total() }}</span> klinik
        </p>
        {{ $clinics->links() }}
    </div>
</div>

@endsection
