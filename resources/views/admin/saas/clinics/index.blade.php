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
        <p class="text-xs text-gray-500 mt-1">Daftar seluruh klinik fisioterapi dan sport center yang terdaftar di platform SaaS.</p>
    </div>

    <a href="{{ route('saas.clinics.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
        <span>+</span> Daftarkan Klinik Baru
    </a>
</div>

@include('admin.partials.alert')

{{-- Filter & Search --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
    <form method="GET" action="{{ route('saas.clinics.index') }}" class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center w-full lg:w-auto">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Cari nama klinik, kode, email..."
                       class="px-3 py-2 pl-9 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-72">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="status" class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Trial 14 Hari</option>
                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('saas.clinics.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline font-medium">Reset</a>
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
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase">Klinik & Kode</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase">Paket SaaS</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase">Kontak & Alamat</th>
                <th class="px-6 py-3.5 text-center font-semibold text-gray-600 uppercase">Kuota Terapis</th>
                <th class="px-6 py-3.5 text-center font-semibold text-gray-600 uppercase">Status</th>
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($clinics as $c)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-mono text-[10px] text-gray-400 font-bold block">{{ $c->clinic_code }}</span>
                        <span class="font-bold text-gray-900 text-sm block">{{ $c->name }}</span>
                        <span class="text-[10px] text-blue-600 font-mono">{{ $c->subdomain ? $c->subdomain . '.sportclinic.io' : '-' }}</span>
                    </td>

                    <td class="px-6 py-4">
                        <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">
                            {{ $c->plan?->name ?? 'Free Tier' }}
                        </span>
                        <div class="text-[10px] text-gray-400 mt-1">
                            Rp {{ number_format($c->plan?->price ?? 0, 0, ',', '.') }}/bln
                        </div>
                    </td>

                    <td class="px-6 py-4 text-gray-600">
                        <div>📧 {{ $c->email ?? '-' }}</div>
                        <div class="font-mono text-[11px] text-gray-400 mt-0.5">📱 {{ $c->phone_number ?? '-' }}</div>
                        @if($c->address)
                            <div class="text-[10px] text-gray-400 truncate max-w-xs mt-0.5">{{ $c->address }}</div>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-gray-800">{{ $c->max_therapists }}</span> Terapis
                        <div class="text-[10px] text-gray-400">Maks {{ number_format($c->max_patients) }} Pasien</div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        @if($c->status === 'active')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                ✓ Aktif
                            </span>
                        @elseif($c->status === 'trial')
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                ⏳ Trial
                            </span>
                            @if($c->trial_ends_at)
                                <div class="text-[9px] text-gray-400 mt-0.5">s/d {{ $c->trial_ends_at->format('d M Y') }}</div>
                            @endif
                        @else
                            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                ✕ Suspended
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right whitespace-nowrap space-x-1">
                        <a href="{{ route('saas.clinics.impersonate', $c->id) }}"
                           class="px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-lg text-xs transition inline-flex items-center gap-1"
                           title="Masuk sebagai Administrator Klinik Ini">
                            <span>👁️</span> Tinjau
                        </a>
                        <a href="{{ route('saas.clinics.edit', $c->id) }}"
                           class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-xs transition">
                            Edit
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        Belum ada data klinik yang sesuai kriteria pencarian.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $clinics->links() }}
    </div>
</div>

@endsection
