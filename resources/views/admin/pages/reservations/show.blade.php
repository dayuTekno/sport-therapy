@extends('admin.layouts.app')

@section('title', 'Detail Reservasi Booking Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Reservasi', 'url' => route('reservations.index')],
    ['label' => 'Detail ' . $reservation->reservation_code],
]" />

<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-black text-gray-900 font-mono">{{ $reservation->reservation_code }}</h1>
            @if($reservation->status == 'pending_confirmation')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                    Menunggu Konfirmasi Jadwal
                </span>
            @elseif($reservation->status == 'confirmed')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 border border-blue-200">
                    ✓ Jadwal Terkonfirmasi
                </span>
            @elseif($reservation->status == 'in_progress')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 border border-purple-200">
                    Sedang Terapi Berjenjang
                </span>
            @elseif($reservation->status == 'completed')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    ✓ Terapi Selesai
                </span>
            @elseif($reservation->status == 'cancelled')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                    ✕ Dibatalkan
                </span>
            @else
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-700">
                    {{ ucfirst($reservation->status) }}
                </span>
            @endif

            {{-- Badge Status Pembayaran Kasir --}}
            @if($reservation->payment_status === 'paid')
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                    <span>✓</span> Lunas ({{ $reservation->invoice_code ?? 'PAID' }})
                </span>
            @elseif($reservation->status !== 'cancelled' && ($reservation->total_price > 0 || $reservation->sessions->count() > 0))
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                    <span>⏳</span> Kasir: Menunggu Bayar
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500 mt-1">Booking dibuat pada {{ $reservation->created_at->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if(!in_array($reservation->status, ['cancelled', 'completed']))
            {{-- Tombol Batal --}}
            <button type="button" onclick="openCancelModal()" 
                    class="px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold rounded-xl transition flex items-center gap-1.5 cursor-pointer">
                <span>✕</span> Batalkan Reservasi
            </button>

            {{-- Tombol Selesai --}}
            <form action="{{ route('reservations.complete', $reservation->id) }}" method="POST" onsubmit="return confirm('Tandai seluruh reservasi terapi ini sebagai Selesai?')">
                @csrf
                <button type="submit" 
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                    <span>✓</span> Tandai Selesai
                </button>
            </form>
        @endif

        {{-- Tombol Kasir & Pembayaran Reservasi --}}
        @if($reservation->payment_status === 'paid')
            <a href="{{ route('cashier.receipt', $reservation->id) }}" target="_blank"
               class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>🖨️</span> Cetak Kwitansi Kasir
            </a>
        @elseif($reservation->status !== 'cancelled' && ($reservation->total_price > 0 || $reservation->sessions->count() > 0))
            <a href="{{ route('cashier.process', $reservation->id) }}"
               class="px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                <span>💳</span> Kasir: Bayar Tagihan (Rp {{ number_format($reservation->total_price > 0 ? $reservation->total_price : $reservation->sessions->sum('total_price'), 0, ',', '.') }})
            </a>
        @endif

        @if(in_array($reservation->status, ['cancelled', 'completed']))
            <a href="{{ route('reservations.history') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                ← Ke Riwayat Terapi
            </a>
        @else
            <a href="{{ route('reservations.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
                ← Ke Reservasi Aktif
            </a>
        @endif
    </div>
</div>

@include('admin.partials.alert')

@if($reservation->status == 'cancelled')
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3">
        <span class="text-xl">⚠️</span>
        <div class="text-xs text-rose-800">
            <h4 class="font-bold text-sm">Reservasi Ini Telah Dibatalkan</h4>
            <p class="mt-0.5 leading-relaxed">
                Dibatalkan pada <strong>{{ $reservation->cancelled_at ? $reservation->cancelled_at->format('d M Y, H:i') . ' WIB' : $reservation->updated_at->format('d M Y, H:i') . ' WIB' }}</strong>.
                <br>
                <span class="font-semibold">Alasan Pembatalan:</span> {{ $reservation->cancellation_reason ?: ($reservation->notes ?: 'Tidak ada alasan khusus dicatat.') }}
            </p>
        </div>
    </div>
@elseif($reservation->status == 'completed')
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-start gap-3">
        <span class="text-xl">🎉</span>
        <div class="text-xs text-emerald-800">
            <h4 class="font-bold text-sm">Reservasi Terapi Selesai</h4>
            <p class="mt-0.5 leading-relaxed">
                Reservasi ini telah selesai dilaksanakan {{ $reservation->completed_at ? 'pada ' . $reservation->completed_at->format('d M Y, H:i') . ' WIB' : '' }}. Seluruh catatan evaluasi tersimpan di riwayat sesi terapi pasien.
            </p>
        </div>
    </div>
@endif

{{-- 3 MAIN CARDS: DATA PASIEN, KELUHAN, KONFIRMASI JADWAL --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Card 1: Data Pasien --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                <span>👤</span> Data Pasien
            </h3>
            @if($reservation->patient)
                <a href="{{ route('patients.edit', $reservation->patient->id) }}" target="_blank" class="text-[11px] text-blue-600 hover:underline font-semibold">
                    Edit Pasien ↗
                </a>
            @endif
        </div>
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Nama Lengkap</span>
                <span class="font-bold text-gray-900 text-base">{{ $reservation->patient?->full_name ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Usia</span>
                    <span class="font-medium text-gray-800">{{ $reservation->patient?->age ?? '-' }} Tahun</span>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Jenis Kelamin</span>
                    <span class="font-medium text-gray-800">{{ $reservation->patient?->gender == 'male' ? 'Laki-laki' : ($reservation->patient?->gender == 'female' ? 'Perempuan' : '-') }}</span>
                </div>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Nomor WhatsApp / HP</span>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $reservation->patient?->phone_number ?? '') }}" target="_blank" class="font-mono font-bold text-emerald-600 hover:underline inline-flex items-center gap-1">
                    <span>📱</span> {{ $reservation->patient?->phone_number ?? '-' }}
                </a>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Pekerjaan</span>
                <span class="font-medium text-gray-800">{{ $reservation->patient?->occupation ?? '-' }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Alamat Domisili</span>
                <span class="text-gray-700 text-xs leading-relaxed block">{{ $reservation->patient?->address ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Keluhan Pasien --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <div class="mb-4 border-b border-gray-100 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                <span>🩺</span> Data Keluhan Cedera
            </h3>
        </div>
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Keluhan Utama Cedera</span>
                <p class="font-semibold text-gray-900 bg-red-50/70 p-3 rounded-xl border border-red-100 text-xs leading-relaxed">
                    {{ $reservation->chief_complaint }}
                </p>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Berapa Lama Keluhan Dirasakan</span>
                <span class="font-medium text-gray-800">{{ $reservation->complaint_duration }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Riwayat Penyakit / Cedera Sebelumnya</span>
                <span class="text-gray-700 text-xs">{{ $reservation->medical_history ?: 'Tidak ada riwayat medis tercatat' }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Hari & Jam yang Diinginkan Pasien</span>
                <span class="font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg inline-block text-xs">
                    🕒 {{ $reservation->preferred_schedule }}
                </span>
            </div>
        </div>
    </div>

    {{-- Card 3: Pengingat Jadwal Pasien via WhatsApp --}}
    @php
        $rawPhone = $reservation->patient?->phone_number ?? '';
        $cleanPhone = preg_replace('/\D/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $waNumber = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '8')) {
            $waNumber = '62' . $cleanPhone;
        } else {
            $waNumber = $cleanPhone;
        }

        $patientName = $reservation->patient?->full_name ?? 'Pasien';
        $schedule = $reservation->preferred_schedule ?? '-';
        $therapistName = $reservation->therapist?->full_name ?? 'Terapis Klinik';
        $complaint = $reservation->chief_complaint ?? '-';
        $resCode = $reservation->reservation_code;

        $waMessage = "Halo Kak {$patientName},\n\n"
                   . "Berikut kami sampaikan pengingat jadwal reservasi terapi Anda di *Sport Therapy Clinic*:\n\n"
                   . "📋 *Kode Reservasi*: {$resCode}\n"
                   . "🗓️ *Jadwal Terapi*: {$schedule}\n"
                   . "👨‍⚕️ *Terapis*: {$therapistName}\n"
                   . "🩹 *Keluhan*: {$complaint}\n\n"
                   . "Mohon hadir 10-15 menit sebelum waktu terapi dimulai. Apabila ada perubahan jadwal atau pertanyaan, silakan hubungi kami melalui pesan ini.\n\n"
                   . "Terima kasih dan salam sehat! 🙏\n"
                   . "*Sport Physiotherapy & Injury Rehabilitation*";

        $waUrl = !empty($waNumber) ? "https://wa.me/{$waNumber}?text=" . rawurlencode($waMessage) : '#';
    @endphp

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
        <div>
            <div class="mb-4 border-b border-gray-100 pb-3 flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 flex items-center gap-1.5">
                    <span class="text-emerald-600">📱</span> Pengingat Jadwal Pasien
                </h3>
                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                    WhatsApp Reminder
                </span>
            </div>

            <div class="space-y-3 text-xs mb-4">
                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold uppercase">Nomor WhatsApp Pasien</span>
                    <span class="font-mono font-bold text-gray-800 text-sm flex items-center gap-1 mt-0.5">
                        <span class="text-emerald-500">●</span> {{ $reservation->patient?->phone_number ?? '-' }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold uppercase">Jadwal Terapi Pasien</span>
                    <span class="font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg inline-block text-xs mt-0.5">
                        🕒 {{ $reservation->preferred_schedule }}
                    </span>
                </div>

                <div>
                    <span class="text-[10px] text-gray-400 block font-semibold uppercase">Terapis Penanggung Jawab</span>
                    <span class="font-medium text-gray-800">
                        {{ $reservation->therapist?->full_name ? $reservation->therapist->full_name . ' (' . ($reservation->therapist->specialization ?: 'Terapis') . ')' : 'Ditentukan oleh Klinik' }}
                    </span>
                </div>

                {{-- Preview Pesan WhatsApp --}}
                <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Preview Pesan Pengingat:</span>
                    <p class="text-[11px] text-emerald-900/80 italic leading-relaxed line-clamp-3">
                        "Halo Kak {{ $patientName }}, berikut kami sampaikan pengingat jadwal reservasi terapi Anda di Sport Therapy Clinic: Jadwal {{ $schedule }}..."
                    </p>
                </div>
            </div>

            {{-- Hidden full text for copy --}}
            <textarea id="fullReminderText" class="hidden">{{ $waMessage }}</textarea>
        </div>

        <div class="space-y-2 pt-3 border-t border-gray-100">
            @if(!empty($waNumber))
                <a href="{{ $waUrl }}" target="_blank" 
                   class="w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-200 transition flex items-center justify-center gap-2 group">
                    <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    <span>Kirim Pengingat ke WhatsApp</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            @else
                <button disabled 
                        class="w-full py-2.5 px-4 bg-gray-100 text-gray-400 font-bold text-xs rounded-xl cursor-not-allowed flex items-center justify-center gap-1.5">
                    <span>⚠️</span> Nomor WhatsApp Pasien Tidak Tersedia
                </button>
            @endif

            <button type="button" onclick="copyReminderText()" 
                    class="w-full py-2 px-3 bg-white hover:bg-gray-50 text-gray-600 border border-gray-200 text-xs font-semibold rounded-xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                </svg>
                <span id="copyBtnText">Salin Teks Pengingat</span>
            </button>
        </div>
    </div>
</div>

{{-- SECTION: INTEGRASI MODUL SESI TERAPI PASIEN --}}
<div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-lg mb-8">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 bg-blue-500/30 text-blue-200 rounded-full text-[11px] font-semibold border border-blue-400/30">
                    Sesi Terapi Berjenjang
                </span>
                <span class="text-blue-200 text-xs">• Mendukung >1 Terapi per Hari</span>
            </div>
            <h2 class="text-xl font-bold tracking-tight">Tindakan Terapi Pasien</h2>
            <p class="text-xs text-blue-100/90 leading-relaxed">
                Alur tindakan terapi berjenjang (Tahap A, B, C) serta pencatatan beberapa sesi terapi dalam satu hari dikelola di modul <strong>Sesi Terapi Pasien</strong>. Anda dapat membuka sesi terapi pertama atau sesi lanjutan untuk pasien ini kapan saja.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('therapy-sessions.create', ['patient_id' => $reservation->patient_id, 'reservation_id' => $reservation->id]) }}" 
               class="px-5 py-2.5 bg-white hover:bg-blue-50 text-blue-900 text-xs font-bold rounded-xl shadow-md transition flex items-center gap-2">
                <span class="text-blue-600 font-extrabold text-sm">+</span>
                <span>Buka / Tambah Sesi Terapi Pasien Ini</span>
            </a>
            <a href="{{ route('therapy-sessions.index', ['patient_id' => $reservation->patient_id]) }}" 
               class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-medium rounded-xl border border-white/20 transition">
                Lihat Sesi Terapi →
            </a>
        </div>
    </div>
</div>

{{-- TABEL RIWAYAT SESI TERAPI PASIEN INI --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                <span>📋</span> Riwayat Sesi Terapi Pasien Ini
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Catatan seluruh tahapan sesi terapi yang telah atau sedang dijalani pasien di klinik.</p>
        </div>
        <a href="{{ route('therapy-sessions.create', ['patient_id' => $reservation->patient_id, 'reservation_id' => $reservation->id]) }}" 
           class="text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
            + Tambah Sesi Baru
        </a>
    </div>

    @if($therapySessions && $therapySessions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-xs">
                <thead class="bg-gray-50 text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-5 py-3 text-left">Waktu Sesi</th>
                        <th class="px-5 py-3 text-left">Sesi Ke- (Harian)</th>
                        <th class="px-5 py-3 text-left">Jenjang / Tahapan</th>
                        <th class="px-5 py-3 text-left">Terapis</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Tarif Sesi</th>
                        <th class="px-5 py-3 text-left">Catatan / Evaluasi</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($therapySessions as $session)
                        @php
                            $sessPrice = $session->total_price > 0 ? (float)$session->total_price : (float)($session->therapyType?->price ?? 0);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-5 py-3.5 font-medium text-gray-800 whitespace-nowrap">
                                <div>📅 {{ $session->scheduled_at ? $session->scheduled_at->format('d M Y') : '-' }}</div>
                                <div class="text-[11px] text-gray-400">🕒 {{ $session->scheduled_at ? $session->scheduled_at->format('H:i') . ' WIB' : '-' }}</div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                    Sesi Ke-{{ $session->daily_session_order ?? 1 }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="font-bold text-indigo-700 block text-xs">
                                    Tahap {{ $session->stage_number }} ({{ chr(64 + $session->stage_number) }})
                                </span>
                                <span class="text-[11px] text-gray-500">{{ $session->stage_name }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">
                                {{ $session->therapist?->full_name ?? '-' }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @if($session->status == 'completed')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800">
                                        ✓ Selesai
                                    </span>
                                @elseif($session->status == 'in_progress')
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                        Sedang Terapi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                        Terjadwal
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold text-gray-900 whitespace-nowrap">
                                Rp {{ number_format($sessPrice, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5 max-w-xs truncate text-gray-600">
                                {{ $session->evaluation_notes ?: ($session->actions_taken ?: '-') }}
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('therapy-sessions.show', $session->id) }}" 
                                   class="px-2.5 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-semibold rounded-lg text-xs transition">
                                    Buka Sesi →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="5" class="px-5 py-3.5 text-right font-bold text-gray-700 uppercase tracking-wider text-xs">
                            Total Tagihan Reservasi:
                        </td>
                        <td class="px-5 py-3.5 text-right font-black text-gray-900 text-sm whitespace-nowrap">
                            Rp {{ number_format($reservation->total_price > 0 ? $reservation->total_price : $therapySessions->sum('total_price'), 0, ',', '.') }}
                        </td>
                        <td colspan="2" class="px-5 py-3.5 text-right whitespace-nowrap">
                            @if($reservation->payment_status === 'paid')
                                <a href="{{ route('cashier.receipt', $reservation->id) }}" target="_blank"
                                   class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs shadow-xs transition inline-flex items-center gap-1">
                                    <span>🖨️</span> Kwitansi Kasir
                                </a>
                            @else
                                <a href="{{ route('cashier.process', $reservation->id) }}" 
                                   class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-lg text-xs shadow-xs transition inline-flex items-center gap-1">
                                    <span>💳</span> Bayar di Kasir
                                </a>
                            @endif
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="p-8 text-center text-gray-400 text-xs">
            <p class="text-sm text-gray-500 font-medium">Belum ada sesi terapi yang dicatat untuk pasien ini.</p>
            <p class="mt-1">Setelah booking dikonfirmasi, Anda dapat langsung menambahkan sesi terapi pertama melalui tombol di atas.</p>
        </div>
    @endif
</div>

{{-- MODAL BATALKAN RESERVASI --}}
@if(!in_array($reservation->status, ['cancelled', 'completed']))
<div id="cancelReservationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-100">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-rose-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold text-base shrink-0">
                    ✕
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">Batalkan Reservasi</h3>
                    <p class="text-xs text-gray-500">Reservasi akan dipindahkan ke Riwayat Terapi</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600 text-lg font-bold p-1">
                &times;
            </button>
        </div>

        <form method="POST" action="{{ route('reservations.cancel', $reservation->id) }}">
            @csrf
            <div class="p-5 space-y-4">
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1">
                    <div><span class="text-gray-500">Kode Reservasi:</span> <span class="font-mono font-bold text-gray-900">{{ $reservation->reservation_code }}</span></div>
                    <div><span class="text-gray-500">Pasien:</span> <span class="font-bold text-gray-900">{{ $reservation->patient?->full_name }}</span></div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Alasan Pembatalan <span class="text-rose-500">*</span>
                    </label>
                    <select id="reasonQuickSelect" onchange="applyQuickReason(this.value)" class="w-full mb-2 px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-rose-500">
                        <option value="">Pilih alasan umum...</option>
                        <option value="Pasien meminta pembatalan reservasi">Pasien meminta pembatalan reservasi</option>
                        <option value="Jadwal pasien berhalangan / bentrok">Jadwal pasien berhalangan / bentrok</option>
                        <option value="Pasien tidak hadir pada jadwal yang disepakati (No-Show)">Pasien tidak hadir pada jadwal yang disepakati (No-Show)</option>
                        <option value="Kondisi cedera pasien sudah membaik / sembuh">Kondisi cedera pasien sudah membaik / sembuh</option>
                        <option value="Dirujuk ke penanganan spesialis lain">Dirujuk ke penanganan spesialis lain</option>
                        <option value="Lainnya">Lainnya (Tulis alasan di bawah)</option>
                    </select>

                    <textarea name="cancellation_reason" id="cancellationReasonInput" rows="3" required
                              placeholder="Tulis alasan pembatalan reservasi..."
                              class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-rose-500 focus:outline-hidden"></textarea>
                </div>

                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-800 flex items-start gap-2">
                    <span class="font-bold shrink-0">⚠️</span>
                    <span>Reservasi ini beserta sesi yang belum terlaksana akan dibatalkan dan dipindahkan ke modul <strong>Riwayat Terapi</strong>.</span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeCancelModal()"
                        class="px-4 py-2 bg-white hover:bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl border border-gray-200 transition">
                    Tutup
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-md shadow-rose-200 transition flex items-center gap-1.5">
                    <span>✕</span> Konfirmasi Batalkan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function copyReminderText() {
    const textEl = document.getElementById('fullReminderText');
    if (!textEl) return;
    
    navigator.clipboard.writeText(textEl.value).then(() => {
        const btnText = document.getElementById('copyBtnText');
        if (btnText) {
            const originalText = btnText.textContent;
            btnText.textContent = '✓ Teks Pengingat Berhasil Disalin!';
            setTimeout(() => {
                btnText.textContent = originalText;
            }, 2500);
        }
    }).catch(err => {
        // Fallback for older browsers
        textEl.classList.remove('hidden');
        textEl.select();
        document.execCommand('copy');
        textEl.classList.add('hidden');
        alert('Teks pengingat berhasil disalin!');
    });
}

function openCancelModal() {
    const modal = document.getElementById('cancelReservationModal');
    if (modal) modal.classList.remove('hidden');
}

function closeCancelModal() {
    const modal = document.getElementById('cancelReservationModal');
    if (modal) modal.classList.add('hidden');
}

function applyQuickReason(val) {
    const reasonInput = document.getElementById('cancellationReasonInput');
    if (reasonInput) {
        if (val && val !== 'Lainnya') {
            reasonInput.value = val;
        } else if (val === 'Lainnya') {
            reasonInput.value = '';
            reasonInput.focus();
        }
    }
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('cancelReservationModal');
    if (modal && e.target === modal) {
        closeCancelModal();
    }
});
</script>

@endsection

