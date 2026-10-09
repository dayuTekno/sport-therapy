@extends('admin.layouts.app')

@section('title', 'Lembar Kerja Sesi Terapi Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Sesi Terapi Pasien', 'url' => route('therapy-sessions.index')],
    ['label' => 'Lembar Kerja Sesi - ' . ($session->patient?->full_name ?? 'Pasien')],
]" />

{{-- HEADER --}}
<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <div class="flex flex-wrap items-center gap-3">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>🏃‍♂️</span> {{ $session->patient?->full_name ?? 'Pasien' }}
            </h1>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                Tahap {{ $session->stage_number }} ({{ chr(64 + $session->stage_number) }})
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">
                Sesi Ke-{{ $session->daily_session_order ?? 1 }} Hari Ini
            </span>
            @if($session->status == 'completed')
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    ✓ Selesai
                </span>
                @if($session->payment_status == 'paid' || $session->reservation?->payment_status == 'paid')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-300">
                        💳 Tagihan Lunas ({{ $session->invoice_code ?: ($session->reservation?->invoice_code ?? 'Lunas') }})
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-300">
                        💳 Masuk Kasir (Rp {{ number_format($session->total_price > 0 ? $session->total_price : ($session->therapyType?->price ?? 0), 0, ',', '.') }})
                    </span>
                @endif
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
            Jadwal: {{ $session->scheduled_at ? $session->scheduled_at->format('d F Y - H:i') : '-' }} WIB • Terapis: {{ $session->therapist?->full_name ?? 'Belum ditentukan' }}
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('therapy-sessions.patient-history', $session->patient_id) }}" 
           class="px-3.5 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-200 transition flex items-center gap-1.5">
            <span>📜</span> Riwayat Lengkap Pasien
        </a>
        <a href="{{ route('therapy-sessions.index') }}" 
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
            ← Daftar Sesi
        </a>
    </div>
</div>

@include('admin.partials.alert')

{{-- TOP SECTION: 3 PANELS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Card 1: Data Pasien --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-1.5">
            <span>👤</span> Biodata Pasien
        </h3>
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Nama Pasien</span>
                <span class="font-bold text-gray-900 text-base">{{ $session->patient?->full_name }}</span>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Usia</span>
                    <span class="font-medium text-gray-800">{{ $session->patient?->age ?? '-' }} Tahun</span>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Jenis Kelamin</span>
                    <span class="font-medium text-gray-800">{{ $session->patient?->gender == 'female' ? 'Perempuan' : 'Laki-laki' }}</span>
                </div>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Nomor WhatsApp / HP</span>
                <a href="https://wa.me/{{ preg_replace('/\D/', '', $session->patient?->phone_number ?? '') }}" target="_blank" class="font-mono font-bold text-emerald-600 hover:underline">
                    📱 {{ $session->patient?->phone_number }}
                </a>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Pekerjaan</span>
                <span class="font-medium text-gray-800">{{ $session->patient?->occupation ?? '-' }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Alamat Domisili</span>
                <span class="text-gray-700 text-xs">{{ $session->patient?->address ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Card 2: Detail Sesi Saat Ini --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
        <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-1.5">
            <span>🎯</span> Detail Sesi Terapi Ini
        </h3>
        <div class="space-y-3 text-sm">
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Jenjang & Nama Tahap</span>
                <div class="font-bold text-indigo-900 text-sm mt-0.5">
                    Tahap {{ $session->stage_number }}: {{ $session->stage_name }}
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Urutan Sesi Harian</span>
                    <span class="font-bold text-purple-700">Sesi Ke-{{ $session->daily_session_order ?? 1 }}</span>
                </div>
                <div>
                    <span class="text-[11px] text-gray-400 block font-medium">Waktu Sesi</span>
                    <span class="font-semibold text-gray-800">{{ $session->scheduled_at ? $session->scheduled_at->format('H:i') . ' WIB' : '-' }}</span>
                </div>
            </div>
            <div>
                <span class="text-[11px] text-gray-400 block font-medium">Terapis Penanggung Jawab</span>
                <span class="font-bold text-gray-900">{{ $session->therapist?->full_name ?? 'Belum ditentukan' }}</span>
                @if($session->therapist)
                    <span class="text-[11px] text-gray-500 block">{{ $session->therapist->specialization }}</span>
                @endif
            </div>
            @if($session->reservation)
                <div class="p-2.5 bg-blue-50/70 rounded-xl border border-blue-100 text-xs">
                    <span class="text-[10px] text-blue-500 font-bold uppercase block">Keluhan Booking Awal:</span>
                    <p class="text-blue-900 font-medium mt-0.5">{{ $session->reservation->chief_complaint }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Card 3: Sesi Lain Pasien Ini pada Hari yang Sama (>1 Terapi per Hari) --}}
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-purple-100">
        <div class="flex items-center justify-between mb-4 border-b border-purple-100 pb-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-1.5">
                <span>⚡</span> Sesi Lain Hari Ini
            </h3>
            <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full text-[10px] font-bold border border-purple-200">
                Multi-Sesi Harian
            </span>
        </div>

        <div class="space-y-3">
            <p class="text-xs text-gray-500 leading-relaxed">
                Pasien dimungkinkan menjalani lebih dari satu sesi terapi dalam 1 hari yang sama (misal: Sesi 1 Pagi dan Sesi 2 Sore).
            </p>

            @if($sameDaySessions && $sameDaySessions->count() > 0)
                <div class="space-y-2">
                    @foreach($sameDaySessions as $otherSession)
                        <a href="{{ route('therapy-sessions.show', $otherSession->id) }}" 
                           class="block p-3 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/40 transition">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-gray-800">
                                    Sesi Ke-{{ $otherSession->daily_session_order ?? 1 }} (🕒 {{ $otherSession->scheduled_at ? $otherSession->scheduled_at->format('H:i') : '-' }})
                                </span>
                                @if($otherSession->status == 'completed')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold">✓ Selesai</span>
                                @else
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold">Terjadwal</span>
                                @endif
                            </div>
                            <div class="text-[11px] text-gray-500 mt-1 truncate">
                                Tahap {{ $otherSession->stage_number }}: {{ $otherSession->stage_name }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="p-3 bg-gray-50 rounded-xl text-center text-xs text-gray-400">
                    Belum ada sesi lain untuk pasien ini pada tanggal ini.
                </div>
            @endif


        </div>
    </div>
</div>

{{-- VISUAL TIMELINE ALUR TERAPI BERJENJANG PASIEN --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6 border-b border-gray-100 pb-4">
        <div>
            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span>🔄</span> Timeline Alur Terapi Berjenjang Pasien
            </h2>
            <p class="text-xs text-gray-500 mt-0.5">
                Pantau seluruh rekam jejak tahapan terapi pasien dari tahap awal hingga tahap lanjutan.
            </p>
        </div>
        <div class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
            Total Sesi Pasien: {{ $allSessions->count() }} Sesi
        </div>
    </div>

    {{-- Sessions Timeline --}}
    <div class="space-y-6">
        @forelse($allSessions as $index => $sess)
            <div class="relative pl-8 sm:pl-10 pb-6 border-l-2 {{ $sess->status == 'completed' ? 'border-emerald-500' : ($sess->id == $session->id ? 'border-blue-500' : 'border-gray-200') }} last:border-transparent last:pb-0">
                {{-- Step Indicator Bubble --}}
                <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $sess->status == 'completed' ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : ($sess->id == $session->id ? 'bg-blue-600 text-white ring-4 ring-blue-100 animate-pulse' : 'bg-gray-200 text-gray-600') }}">
                    @if($sess->status == 'completed')
                        ✓
                    @else
                        {{ $sess->stage_number }}
                    @endif
                </div>

                {{-- Session Box --}}
                <div class="p-4 rounded-xl border {{ $sess->id == $session->id ? 'bg-blue-50/50 border-blue-200' : 'bg-gray-50/70 border-gray-100' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">
                                    Tahap {{ $sess->stage_number }} ({{ chr(64 + $sess->stage_number) }})
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full border border-purple-200">
                                    Sesi Ke-{{ $sess->daily_session_order ?? 1 }} Hari Itu
                                </span>
                                @if($sess->id == $session->id)
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-blue-600 text-white rounded-full">
                                        Sesi Terbuka Ini
                                    </span>
                                @endif
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mt-0.5">{{ $sess->stage_name }}</h4>
                        </div>
                        <div>
                            @if($sess->status == 'completed')
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800">
                                    ✓ Selesai ({{ $sess->completed_at ? $sess->completed_at->format('d M Y, H:i') : '' }})
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800">
                                    📅 Terjadwal ({{ $sess->scheduled_at ? $sess->scheduled_at->format('d M Y, H:i') : '-' }})
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-gray-600 space-y-1 mt-2">
                        <div><strong>Terapis:</strong> {{ $sess->therapist?->full_name ?? 'Ditentukan Klinik' }}</div>
                        @if($sess->actions_taken)
                            <div class="mt-2 p-2.5 bg-white rounded-lg border border-gray-200">
                                <span class="text-[10px] font-bold text-gray-500 uppercase block">Tindakan / Terapi yang Diberikan:</span>
                                <p class="text-xs text-gray-800 mt-0.5 leading-relaxed">{{ $sess->actions_taken }}</p>
                            </div>
                        @endif
                        @if($sess->evaluation_notes)
                            <div class="mt-2 p-2.5 bg-white rounded-lg border border-emerald-200">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase block">Hasil & Catatan Evaluasi:</span>
                                <p class="text-xs text-gray-800 mt-0.5 leading-relaxed">{{ $sess->evaluation_notes }}</p>
                            </div>
                        @endif
                        @if($sess->recommended_next_stage)
                            <div class="text-[11px] text-blue-700 font-semibold mt-1">
                                📌 Rekomendasi Lanjutan: {{ $sess->recommended_next_stage }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-xs text-gray-400">Belum ada sesi tercatat untuk pasien ini.</p>
        @endforelse
    </div>
</div>

{{-- LEMBAR KERJA EVALUASI SESI AKTIF --}}
@if($session->status != 'completed')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="mb-5 pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                <span>📝</span> Lembar Tindakan & Evaluasi Sesi Terapi
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">
                Catat tindakan manual therapy/alat yang diaplikasikan, evaluasi perkembangan fisik pasien, dan tentukan opsi kelanjutan tahapan.
            </p>
        </div>

        <form action="{{ route('therapy-sessions.complete', $session->id) }}" method="POST" class="space-y-5" id="evalForm">
            @csrf

            {{-- 1. Tindakan / Terapi yang Dilakukan --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    1. Tindakan / Teknik Terapi yang Dilakukan *
                </label>
                <textarea name="actions_taken" rows="3" required
                          placeholder="Contoh: Cryotherapy 15 menit pada lutut anterior, Trigger Point Release gastrocnemius, Ultrasound terapi 1.5 W/cm², Isometric Quad Sets 3 set x 10 repetisi..."
                          class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">{{ old('actions_taken', $session->actions_taken) }}</textarea>
                @error('actions_taken') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 2. Evaluasi Perkembangan Pasien --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    2. Catatan Evaluasi & Hasil Sesi Terapi *
                </label>
                <textarea name="evaluation_notes" rows="3" required
                          placeholder="Contoh: Penurunan nyeri VAS dari 7 ke VAS 3. ROM fleksi lutut bertambah 25 derajat. Pasien mampu menumpu beban tanpa nyeri tajam..."
                          class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">{{ old('evaluation_notes', $session->evaluation_notes) }}</textarea>
                @error('evaluation_notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- 3. Rekomendasi Terapi Lanjutan --}}
            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    3. Rekomendasi Program / Jenjang Terapi Berikutnya (Opsional)
                </label>
                <input type="text" name="recommended_next_stage" 
                       value="{{ old('recommended_next_stage', 'Lanjut ke Tahap ' . chr(64 + $session->stage_number + 1) . ': Latihan Penguatan & Mobilitas') }}"
                       placeholder="Contoh: Lanjut ke Tahap B (Dynamic Resistance) atau Sesi 2 Sore Hari Ini..."
                       class="w-full text-sm border border-gray-300 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-blue-500 bg-white">
            </div>

            {{-- OPSI OTOMATIS: LANGSUNG BUKA SESI BERIKUTNYA --}}
            <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 space-y-3">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="advance_to_next" id="chkAdvanceToNext" value="1" 
                           class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                    <span class="text-xs font-bold text-blue-900">
                        🔄 Selesaikan Sesi Ini & Langsung Buat Sesi Terapi Lanjutan untuk Pasien
                    </span>
                </label>
                <p class="text-[11px] text-blue-700 ml-6">
                    Centang jika ingin langsung menjadwalkan sesi berikutnya (bisa di hari yang sama beberapa jam kemudian, atau di hari berikutnya).
                </p>

                {{-- Sub-form lanjutan --}}
                <div id="advanceSubForm" class="hidden pl-6 pt-2 space-y-3 border-t border-blue-200/60 mt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Pilih Tahap Berikutnya</label>
                            <select name="next_therapy_type_id" class="w-full text-xs border border-gray-300 rounded-lg p-2 bg-white">
                                <option value="">-- Otomatis Jenjang Berikutnya --</option>
                                @foreach($therapyTypes as $tt)
                                    <option value="{{ $tt->id }}" {{ $session->stage_number + 1 == $tt->stage_order ? 'selected' : '' }}>
                                        Tahap {{ $tt->stage_order }}: {{ $tt->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Jadwal Sesi Baru</label>
                            <input type="datetime-local" name="next_scheduled_at" 
                                   value="{{ now()->addHours(3)->format('Y-m-d\TH:i') }}"
                                   class="w-full text-xs border border-gray-300 rounded-lg p-2 bg-white">
                            <span class="text-[10px] text-blue-600">Bisa di hari yang sama (misal 3 jam lagi)</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase text-gray-700 mb-1">Terapis Sesi Lanjutan</label>
                            <select name="next_therapist_id" class="w-full text-xs border border-gray-300 rounded-lg p-2 bg-white">
                                <option value="">-- Sama dengan Sesi Ini --</option>
                                @foreach($therapists as $t)
                                    <option value="{{ $t->id }}" {{ $session->therapist_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->full_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('therapy-sessions.index') }}" class="px-4 py-2.5 text-xs font-medium text-gray-600 hover:text-gray-800">
                    Kembali
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md shadow-emerald-200 transition flex items-center gap-2">
                    <span>✓</span>
                    <span>Selesaikan Sesi Terapi Ini</span>
                </button>
            </div>
        </form>
    </div>
@else
    {{-- STATUS ALREADY COMPLETED --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
            <h3 class="text-base font-bold text-emerald-800 flex items-center gap-2">
                <span>✓</span> Hasil Evaluasi Sesi Ini (Telah Selesai)
            </h3>
            <span class="text-xs text-gray-500">
                Diselesaikan pada {{ $session->completed_at ? $session->completed_at->format('d M Y - H:i') : '-' }} WIB
            </span>
        </div>

        <div class="space-y-4 text-xs">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <span class="font-bold text-gray-500 uppercase block mb-1">Tindakan / Teknik yang Diberikan:</span>
                <p class="text-gray-900 leading-relaxed text-sm">{{ $session->actions_taken ?: 'Tidak ada catatan' }}</p>
            </div>

            <div class="bg-emerald-50/70 p-4 rounded-xl border border-emerald-100">
                <span class="font-bold text-emerald-700 uppercase block mb-1">Hasil Evaluasi Perkembangan Pasien:</span>
                <p class="text-gray-900 leading-relaxed text-sm">{{ $session->evaluation_notes ?: 'Tidak ada catatan' }}</p>
            </div>

            @if($session->recommended_next_stage)
                <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-100">
                    <span class="font-bold text-blue-700 uppercase block mb-1">Rekomendasi Tahap Berikutnya:</span>
                    <p class="text-blue-900 font-semibold">{{ $session->recommended_next_stage }}</p>
                </div>
            @endif

            {{-- STATUS PEMBAYARAN KASIR --}}
            @php
                $sessPrice = $session->total_price > 0 ? $session->total_price : ($session->therapyType?->price ?? 0);
            @endphp
            @if($session->payment_status === 'paid' || $session->reservation?->payment_status === 'paid')
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            ✓
                        </div>
                        <div>
                            <div class="font-bold text-emerald-900 text-xs">Tagihan Pembayaran Reservasi Telah LUNAS</div>
                            <div class="text-[11px] text-emerald-700 mt-0.5">
                                Invoice: <strong class="font-mono">{{ $session->invoice_code ?: $session->reservation?->invoice_code }}</strong>
                                @if($session->reservation)
                                    • Ref Booking: <strong>{{ $session->reservation->reservation_code }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($session->reservation_id)
                        <a href="{{ route('cashier.receipt', $session->reservation_id) }}" target="_blank" 
                           class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 self-start sm:self-auto">
                            <span>🖨️</span> Cetak Kwitansi Reservasi
                        </a>
                    @endif
                </div>
            @else
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shrink-0">
                            ⏳
                        </div>
                        <div>
                            <div class="font-bold text-amber-900 text-xs">Sesi Selesai — Masuk Antrean Pembayaran Reservasi di Kasir</div>
                            <div class="text-[11px] text-amber-700 mt-0.5">
                                Tarif Sesi Ini: <strong>Rp {{ number_format($sessPrice, 0, ',', '.') }}</strong> ({{ $session->stage_name }})
                                @if($session->reservation)
                                    • Total Tagihan Reservasi: <strong>Rp {{ number_format($session->reservation->total_price, 0, ',', '.') }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($session->reservation_id)
                        <a href="{{ route('cashier.process', $session->reservation_id) }}" 
                           class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5 self-start sm:self-auto">
                            <span>💳</span> Bayar di Kasir →
                        </a>
                    @endif
                </div>
            @endif

            <div class="pt-3 flex items-center gap-3">
                <a href="{{ route('therapy-sessions.create', ['patient_id' => $session->patient_id, 'date' => $session->scheduled_at ? $session->scheduled_at->toDateString() : '']) }}" 
                   class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-2">
                    <span>+</span>
                    <span>Buat Sesi Lanjutan Baru untuk Pasien Ini</span>
                </a>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chkAdvance = document.getElementById('chkAdvanceToNext');
    const advanceSubForm = document.getElementById('advanceSubForm');

    if (chkAdvance && advanceSubForm) {
        chkAdvance.addEventListener('change', function() {
            if (this.checked) {
                advanceSubForm.classList.remove('hidden');
            } else {
                advanceSubForm.classList.add('hidden');
            }
        });
    }
});
</script>

@endsection
