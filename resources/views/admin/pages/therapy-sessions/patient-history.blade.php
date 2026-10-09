@extends('admin.layouts.app')

@section('title', 'Rekam Jejak Terapi Berjenjang Pasien')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Sesi Terapi Pasien', 'url' => route('therapy-sessions.index')],
    ['label' => 'Riwayat Terapi ' . $patient->full_name, 'url' => ''],
]" />

<div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                <span>📜</span> Riwayat Terapi Pasien: {{ $patient->full_name }}
            </h1>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                Total {{ $sessions->count() }} Sesi
            </span>
        </div>
        <p class="text-xs text-gray-500 mt-1">
            Rekam jejak seluruh tahapan terapi berjenjang dan program pemulihan yang telah dijalani pasien di klinik.
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="{{ route('therapy-sessions.create', ['patient_id' => $patient->id]) }}" 
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
            <span>+</span> Tambah Sesi Terapi Baru
        </a>
        <a href="{{ route('therapy-sessions.index') }}" 
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition">
            ← Kembali
        </a>
    </div>
</div>

{{-- BIODATA PASIEN CARD --}}
<div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 mb-6">
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
        <div>
            <span class="text-gray-400 font-medium block">Nomor WhatsApp / HP</span>
            <strong class="font-mono text-emerald-600 text-sm">📱 {{ $patient->phone_number }}</strong>
        </div>
        <div>
            <span class="text-gray-400 font-medium block">Usia & Jenis Kelamin</span>
            <span class="font-bold text-gray-800 text-sm">{{ $patient->age ? $patient->age . ' Tahun' : '-' }} ({{ $patient->gender == 'female' ? 'Perempuan' : 'Laki-laki' }})</span>
        </div>
        <div>
            <span class="text-gray-400 font-medium block">Pekerjaan</span>
            <span class="font-medium text-gray-800">{{ $patient->occupation ?? '-' }}</span>
        </div>
        <div>
            <span class="text-gray-400 font-medium block">Alamat Domisili</span>
            <span class="font-medium text-gray-800 truncate block">{{ $patient->address ?? '-' }}</span>
        </div>
    </div>
</div>

{{-- FULL TIMELINE --}}
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    <h3 class="font-bold text-base text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
        <span>🔄</span> Kronologis Tahapan & Sesi Terapi
    </h3>

    <div class="space-y-6">
        @forelse($sessions as $sess)
            <div class="relative pl-8 sm:pl-10 pb-6 border-l-2 {{ $sess->status == 'completed' ? 'border-emerald-500' : 'border-blue-400' }} last:border-transparent last:pb-0">
                <div class="absolute -left-3.5 top-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                    {{ $sess->status == 'completed' ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-blue-600 text-white ring-4 ring-blue-100' }}">
                    @if($sess->status == 'completed')
                        ✓
                    @else
                        {{ $sess->stage_number }}
                    @endif
                </div>

                <div class="p-4 rounded-xl border {{ $sess->status == 'completed' ? 'bg-emerald-50/20 border-emerald-100' : 'bg-gray-50 border-gray-100' }}">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">
                                    Tahap {{ $sess->stage_number }} ({{ chr(64 + $sess->stage_number) }})
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full border border-purple-200">
                                    Sesi Ke-{{ $sess->daily_session_order ?? 1 }} Hari Itu
                                </span>
                            </div>
                            <h4 class="text-sm font-bold text-gray-900 mt-0.5">{{ $sess->stage_name }}</h4>
                        </div>
                        <div class="text-xs">
                            <span class="font-semibold text-gray-700">
                                📅 {{ $sess->scheduled_at ? $sess->scheduled_at->format('d M Y - H:i') : '-' }} WIB
                            </span>
                            @if($sess->status == 'completed')
                                <span class="ml-2 px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-bold">✓ Selesai</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-[10px] font-bold">Terjadwal</span>
                            @endif
                        </div>
                    </div>

                    <div class="text-xs text-gray-600 space-y-1.5 mt-2">
                        <div><strong>Terapis:</strong> {{ $sess->therapist?->full_name ?? 'Ditentukan Klinik' }}</div>
                        @if($sess->actions_taken)
                            <div class="mt-2 p-2.5 bg-white rounded-lg border border-gray-200">
                                <span class="text-[10px] font-bold text-gray-500 uppercase block">Tindakan / Teknik:</span>
                                <p class="text-xs text-gray-800 mt-0.5">{{ $sess->actions_taken }}</p>
                            </div>
                        @endif
                        @if($sess->evaluation_notes)
                            <div class="mt-2 p-2.5 bg-white rounded-lg border border-emerald-200">
                                <span class="text-[10px] font-bold text-emerald-700 uppercase block">Evaluasi & Hasil Sesi:</span>
                                <p class="text-xs text-gray-800 mt-0.5">{{ $sess->evaluation_notes }}</p>
                            </div>
                        @endif
                        @if($sess->recommended_next_stage)
                            <div class="text-[11px] text-blue-700 font-semibold mt-1">
                                📌 Rekomendasi Lanjutan: {{ $sess->recommended_next_stage }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 pt-2 border-t border-gray-200/60 text-right">
                        <a href="{{ route('therapy-sessions.show', $sess->id) }}" class="text-xs text-blue-600 hover:text-blue-800 font-bold">
                            Lihat Lembar Sesi Ini →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-400 text-xs">
                Belum ada rekam jejak sesi terapi untuk pasien ini.
            </div>
        @endforelse
    </div>
</div>

@endsection
