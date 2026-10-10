@extends('admin.layouts.app')

@section('title', 'Detail Lembar Rekam Terapi Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Laporan', 'url' => route('reports.medical_records.index')],
    ['label' => 'Rekam Terapi Pasien', 'url' => route('reports.medical_records.index')],
    ['label' => 'Detail Rekam Terapi'],
]" />

{{-- Header & Action Bar --}}
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📄</span> Lembar Rekam Terapi Pasien
            </h1>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                Tahap {{ $session->stage_number }} ({{ chr(64 + ($session->stage_number ?: 1)) }})
            </span>
            @if($session->status == 'completed')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    ✓ Selesai
                </span>
            @elseif($session->status == 'in_progress')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                    Sedang Terapi
                </span>
            @else
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                    Terjadwal
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500 mt-1">
            Sesi ID: <strong class="font-mono text-gray-700">#{{ $session->id }}</strong> 
            @if($session->reservation?->reservation_code)
                • No. Reservasi: <strong class="font-mono text-blue-600">{{ $session->reservation->reservation_code }}</strong>
            @endif
            • Waktu: {{ $session->scheduled_at ? $session->scheduled_at->format('d F Y - H:i') : '-' }} WIB
        </p>
    </div>

    <div class="flex items-center gap-2 print:hidden">
        <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-semibold text-xs flex items-center gap-2 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Rekam Terapi
        </button>
        <a href="{{ route('reports.medical_records.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold text-xs transition shadow-sm">
            ← Kembali ke Laporan
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KOLOM KIRI: INFO PASIEN & TERAPIS --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- INFO PASIEN --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4 border-b pb-2 flex items-center gap-1.5">
                <span>👤</span> Informasi Pasien
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Nama Lengkap</span>
                    <span class="text-gray-900 font-bold text-sm text-right">{{ $session->patient?->full_name ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">No. RM</span>
                    <span class="font-mono text-gray-800 font-semibold text-xs">{{ $session->patient?->patient_code ? substr($session->patient->patient_code, 0, 8) : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">No. WhatsApp / HP</span>
                    <span class="font-mono text-emerald-600 font-semibold text-xs">{{ $session->patient?->phone_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Jenis Kelamin</span>
                    <span class="text-gray-800 font-medium text-xs">
                        @if($session->patient?->gender)
                            {{ $session->patient->gender == 'female' ? 'Perempuan' : 'Laki-laki' }}
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Usia</span>
                    <span class="text-gray-800 font-medium text-xs">{{ $session->patient?->age ? $session->patient->age . ' Tahun' : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Pekerjaan</span>
                    <span class="text-gray-800 font-medium text-xs text-right">{{ $session->patient?->occupation ?? '-' }}</span>
                </div>
                <div class="pt-2 border-t mt-2">
                    <span class="text-gray-500 text-xs block mb-1">Alamat Domisili</span>
                    <p class="text-xs text-gray-700 bg-gray-50 p-2.5 rounded-xl">{{ $session->patient?->address ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- INFO TERAPIS --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4 border-b pb-2 flex items-center gap-1.5">
                <span>👨‍⚕️</span> Terapis Penanggung Jawab
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Nama Terapis</span>
                    <span class="text-gray-900 font-bold text-sm text-right">{{ $session->therapist?->full_name ?? 'Belum Ditentukan' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Kode Terapis</span>
                    <span class="font-mono text-gray-700 text-xs">{{ $session->therapist?->therapist_code ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Spesialisasi</span>
                    <span class="text-gray-800 font-medium text-xs">{{ $session->therapist?->specialization ?: 'Fisioterapis Olahraga' }}</span>
                </div>
                @if($session->therapist?->phone_number)
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Kontak</span>
                    <span class="font-mono text-gray-700 text-xs">{{ $session->therapist->phone_number }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- JADWAL & STATUS PEMBAYARAN --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-4 border-b pb-2 flex items-center gap-1.5">
                <span>💳</span> Jadwal & Status Sesi
            </h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Jadwal Sesi</span>
                    <span class="text-gray-800 font-semibold text-xs">{{ $session->scheduled_at ? $session->scheduled_at->format('d M Y, H:i') : '-' }} WIB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Urutan Harian</span>
                    <span class="text-purple-700 font-bold text-xs">Sesi ke-{{ $session->daily_session_order ?? 1 }}</span>
                </div>
                @if($session->completed_at)
                <div class="flex justify-between">
                    <span class="text-gray-500 text-xs">Waktu Selesai</span>
                    <span class="text-emerald-700 font-semibold text-xs">{{ $session->completed_at->format('d M Y, H:i') }} WIB</span>
                </div>
                @endif
                <div class="flex justify-between pt-2 border-t">
                    <span class="text-gray-500 text-xs">Tarif Sesi</span>
                    <span class="font-bold text-gray-900 text-sm">
                        Rp {{ number_format($session->total_price > 0 ? $session->total_price : ($session->therapyType?->price ?? 0), 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs">Status Bayar</span>
                    @if($session->payment_status == 'paid' || $session->reservation?->payment_status == 'paid')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            ✓ Lunas
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Masuk Kasir
                        </span>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- KOLOM KANAN: HASIL EVALUASI & REKAM KLINIS --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- CARD EVALUASI KLINIS UTAMA --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 bg-gradient-to-r from-blue-50/50 to-indigo-50/50 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-base">Evaluasi Klinis & Tindakan Fisioterapi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $session->stage_name ?: ($session->therapyType?->name ?? 'Tahapan Terapi') }}</p>
                </div>
                <span class="px-3 py-1 rounded-xl bg-blue-600 text-white font-bold text-xs shadow-sm">
                    Tahap {{ $session->stage_number }} ({{ chr(64 + ($session->stage_number ?: 1)) }})
                </span>
            </div>

            <div class="p-6 space-y-6">

                {{-- Keluhan Awal Pasien --}}
                @if($session->reservation?->complaints || $session->reservation?->injury_description)
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Keluhan & Riwayat Cedera Awal (Subjective)
                    </h4>
                    <div class="p-4 bg-amber-50/40 rounded-xl text-sm text-gray-700 italic border-l-4 border-amber-400">
                        "{{ $session->reservation->complaints ?: $session->reservation->injury_description }}"
                    </div>
                </div>
                @endif

                {{-- Tindakan Fisioterapi yang Dilakukan --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Tindakan Terapi yang Dilakukan (Objective / Action)
                    </h4>
                    <div class="p-4 bg-gray-50 rounded-xl text-sm text-gray-800">
                        @if(!empty($session->actions_taken))
                            <p class="whitespace-pre-line">{{ $session->actions_taken }}</p>
                        @else
                            <p class="text-gray-400 italic">Tindakan disesuaikan dengan protokol baku {{ $session->stage_name ?: ($session->therapyType?->name ?? 'Fisioterapi') }}.</p>
                        @endif
                    </div>
                </div>

                {{-- Hasil Evaluasi Klinis --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        Evaluasi Hasil Terapi & Kondisi Nyeri Pasien (Assessment)
                    </h4>
                    <div class="p-4 bg-emerald-50/30 rounded-xl border border-emerald-100 text-sm text-gray-800">
                        @if(!empty($session->evaluation_notes))
                            <p class="font-medium whitespace-pre-line">{{ $session->evaluation_notes }}</p>
                        @else
                            <p class="text-gray-400 italic">Belum ada evaluasi klinis yang diinput untuk sesi ini.</p>
                        @endif
                    </div>
                </div>

                {{-- Rekomendasi Terapi Lanjutan --}}
                <div class="space-y-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                        Rencana / Rekomendasi Terapi Lanjutan (Plan)
                    </h4>
                    <div class="p-4 bg-purple-50/30 rounded-xl border border-purple-100 text-sm text-gray-800">
                        @if(!empty($session->recommended_next_stage))
                            <p class="font-bold text-purple-900">👉 {{ $session->recommended_next_stage }}</p>
                        @else
                            <p class="text-gray-400 italic">Belum ada rekomendasi tahapan lanjutan.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- RIWAYAT JEJAK TAHAPAN TERAPI BERJENJANG PASIEN --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 bg-gray-50/75 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="font-black text-gray-900 text-sm flex items-center gap-2">
                        <span>📜</span> Riwayat Seluruh Sesi Terapi Pasien Ini
                    </h3>
                    <p class="text-xs text-gray-500">Perjalanan pemulihan berjenjang yang telah dan sedang dijalani.</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-200 text-gray-700">
                    Total {{ $patientSessions->count() }} Sesi
                </span>
            </div>

            <div class="p-5">
                <div class="space-y-3">
                    @forelse($patientSessions as $idx => $ps)
                        <div class="p-3.5 rounded-xl border {{ $ps->id == $session->id ? 'bg-blue-50/50 border-blue-200 ring-2 ring-blue-400/20' : 'bg-gray-50/50 border-gray-100' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-start gap-3">
                                <span class="w-6 h-6 rounded-full {{ $ps->id == $session->id ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center font-bold text-[11px] shrink-0">
                                    {{ $idx + 1 }}
                                </span>
                                <div>
                                    <div class="font-bold text-gray-900 flex items-center gap-2">
                                        <span>{{ $ps->stage_name ?: ($ps->therapyType?->name ?? 'Tahap ' . $ps->stage_number) }}</span>
                                        @if($ps->id == $session->id)
                                            <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 font-extrabold text-[9px] uppercase">Sesi Ini</span>
                                        @endif
                                    </div>
                                    <div class="text-gray-500 text-[11px] mt-0.5">
                                        Jadwal: {{ $ps->scheduled_at ? $ps->scheduled_at->format('d M Y - H:i') : '-' }} WIB • Terapis: {{ $ps->therapist?->full_name ?? '-' }}
                                    </div>
                                    @if(!empty($ps->evaluation_notes))
                                        <div class="text-gray-600 italic text-[11px] mt-1 bg-white p-2 rounded-lg border border-gray-100">
                                            "{{ Str::limit($ps->evaluation_notes, 90) }}"
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                                @if($ps->status == 'completed')
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-100 text-emerald-800">
                                        ✓ Selesai
                                    </span>
                                @elseif($ps->status == 'in_progress')
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-blue-100 text-blue-800">
                                        Sedang Terapi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-amber-100 text-amber-800">
                                        Terjadwal
                                    </span>
                                @endif

                                @if($ps->id != $session->id)
                                    <a href="{{ route('reports.medical_records.show', $ps->id) }}" class="p-1 text-blue-600 hover:text-blue-800 font-bold" title="Lihat Sesi Ini">
                                        Lihat →
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">Belum ada riwayat sesi lain.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

@endsection
