@extends('admin.layouts.app')

@section('title', 'Detail Referal - ' . $referral->name)

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Master Data', 'url' => '#'],
    ['label' => 'Referal', 'url' => route('referrals.index')],
    ['label' => $referral->name],
]" />

{{-- Header & Action Button --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-xl font-bold text-gray-900">{{ $referral->name }}</h1>
            <span class="font-mono text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 font-bold">
                {{ $referral->code ?: 'REF-' . $referral->id }}
            </span>
            @if($referral->is_active)
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">
                    Aktif
                </span>
            @else
                <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold">
                    Non-Aktif
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500 mt-1">
            {{ $referral->agency ? $referral->agency . ' • ' : '' }}
            {{ $referral->phone ? 'Telp: ' . $referral->phone . ' • ' : '' }}
            {{ $referral->email ?: '' }}
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('referrals.edit', $referral->id) }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold rounded-xl border border-amber-200 transition">
            ✏️ Edit Referal
        </a>
        <a href="{{ route('referrals.index') }}"
           class="inline-flex items-center gap-1 px-3.5 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs font-semibold rounded-xl border border-gray-200 transition">
            ← Kembali
        </a>
    </div>
</div>

{{-- Metric Cards --}}
@php
    $totalCompleted = $referral->completed_sessions_count ?? 0;
    $rate = $referral->commission_amount ?? 0;
    $totalCommission = $totalCompleted * $rate;
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Pasien Terafiliasi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shrink-0">
            👥
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Pasien Terafiliasi</span>
            <div class="text-xl font-extrabold text-gray-900 mt-0.5">{{ number_format($referral->patients_count ?? 0) }} Pasien</div>
        </div>
    </div>

    {{-- Sesi Terapi Selesai --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0">
            ✓
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Sesi Terapi Selesai</span>
            <div class="text-xl font-extrabold text-emerald-700 mt-0.5">
                {{ number_format($totalCompleted) }} Sesi
            </div>
            <span class="text-[10px] text-gray-400">dari total {{ number_format($referral->therapy_sessions_count ?? 0) }} sesi</span>
        </div>
    </div>

    {{-- Tarif Komisi per Sesi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
            🏷️
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Tarif Komisi / Sesi</span>
            <div class="text-base font-extrabold text-purple-700 mt-0.5 font-mono">
                Rp {{ number_format($rate, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">per sesi terapi selesai</span>
        </div>
    </div>

    {{-- Total Akumulasi Komisi --}}
    <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-xs flex items-center gap-3.5">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
            💰
        </div>
        <div>
            <span class="text-xs text-gray-500 font-medium">Akumulasi Hak Komisi</span>
            <div class="text-base font-extrabold text-amber-700 mt-0.5 font-mono">
                Rp {{ number_format($totalCommission, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-gray-400">({{ number_format($totalCompleted) }} sesi × Rp {{ number_format($rate, 0, ',', '.') }})</span>
        </div>
    </div>
</div>

@if($referral->notes)
    <div class="mb-6 p-4 rounded-xl bg-gray-50 border border-gray-200 text-xs text-gray-700">
        <strong class="font-bold text-gray-900 block mb-1">📝 Catatan Mitra / Perjanjian Kerja Sama:</strong>
        {{ $referral->notes }}
    </div>
@endif

{{-- Tabel Pasien Terafiliasi --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-bold text-gray-900">Daftar Pasien Terafiliasi</h2>
            <p class="text-[11px] text-gray-500 mt-0.5">Pasien yang didaftarkan melalui rujukan mitra <strong>{{ $referral->name }}</strong>.</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700">
            Total: {{ $referral->patients->count() }} Pasien
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
            <thead class="bg-gray-50/75 text-gray-500 font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-5 py-3.5">No</th>
                    <th class="px-5 py-3.5">Kode / RM</th>
                    <th class="px-5 py-3.5">Nama Pasien</th>
                    <th class="px-5 py-3.5">No. HP / WA</th>
                    <th class="px-5 py-3.5">Usia / Gender</th>
                    <th class="px-5 py-3.5 text-center">Total Sesi Terapi</th>
                    <th class="px-5 py-3.5 text-center">Sesi Selesai</th>
                    <th class="px-5 py-3.5">Kontribusi Komisi</th>
                    <th class="px-5 py-3.5">Tanggal Terdaftar</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($referral->patients as $idx => $patient)
                    @php
                        $patientCompleted = $patient->completed_sessions_count ?? 0;
                        $patientCommission = $patientCompleted * $rate;
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-5 py-3.5 text-gray-400">{{ $idx + 1 }}</td>
                        <td class="px-5 py-3.5 font-mono text-gray-600">
                            {{ substr($patient->patient_code, 0, 8) }}...
                        </td>
                        <td class="px-5 py-3.5 font-bold text-gray-900">
                            {{ $patient->full_name }}
                            @if($patient->occupation)
                                <div class="text-[10px] text-gray-400 font-normal">{{ $patient->occupation }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 font-mono text-gray-700">
                            {{ $patient->phone_number ?: '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-gray-600">
                            {{ $patient->age ? $patient->age . ' Thn' : '-' }} • 
                            {{ $patient->gender === 'male' ? 'L' : ($patient->gender === 'female' ? 'P' : '-') }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded font-mono bg-gray-100 text-gray-700 text-xs">
                                {{ number_format($patient->therapy_sessions_count ?? 0) }} Sesi
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="px-2.5 py-0.5 rounded-full font-bold text-emerald-800 bg-emerald-50 border border-emerald-100 text-xs">
                                {{ number_format($patientCompleted) }} Selesai
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-gray-900 whitespace-nowrap">
                            @if($patientCommission > 0)
                                <span class="font-bold text-amber-700">Rp {{ number_format($patientCommission, 0, ',', '.') }}</span>
                            @else
                                <span class="text-gray-400">Rp 0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 whitespace-nowrap">
                            {{ $patient->created_at ? $patient->created_at->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-5 py-3.5 text-right whitespace-nowrap">
                            <a href="{{ route('patients.edit', $patient->id) }}" 
                               class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                                Edit Pasien
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-5 py-8 text-center text-gray-400">
                            Belum ada pasien yang terafiliasi dengan referal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
