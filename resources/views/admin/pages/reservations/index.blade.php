@extends('admin.layouts.app')

@section('title', 'Daftar Reservasi Terapi Aktif')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'Reservasi'],
    ['label' => 'Daftar Reservasi Aktif'],
]" />

<div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
            <span>⚡</span> Reservasi Terapi Pasien
        </h1>
        <p class="text-xs text-gray-500 mt-1">Kelola permohonan booking dan jadwal terapi aktif pasien</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('reservations.create') }}"
           class="px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 shadow-sm transition">
            + Buat Reservasi Baru
        </a>
    </div>
</div>

@include('admin.partials.alert')

{{-- STATUS TABS UNTUK RESERVASI AKTIF --}}
<div class="border-b border-gray-200 mb-6">
    <nav class="flex space-x-6" aria-label="Tabs">
        <a href="{{ route('reservations.index', array_merge(request()->except('status'), ['status' => ''])) }}"
           class="{{ empty(request('status')) ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>⚡ Semua Reservasi Aktif</span>
            <span class="{{ empty(request('status')) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $activeCount }}
            </span>
        </a>
        <a href="{{ route('reservations.index', array_merge(request()->except('status'), ['status' => 'pending_confirmation'])) }}"
           class="{{ request('status') == 'pending_confirmation' ? 'border-amber-600 text-amber-700 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>⏳ Menunggu Konfirmasi</span>
            <span class="{{ request('status') == 'pending_confirmation' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $pendingCount }}
            </span>
        </a>
        <a href="{{ route('reservations.index', array_merge(request()->except('status'), ['status' => 'confirmed'])) }}"
           class="{{ request('status') == 'confirmed' ? 'border-blue-600 text-blue-700 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>✓ Terkonfirmasi</span>
            <span class="{{ request('status') == 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $confirmedCount }}
            </span>
        </a>
        <a href="{{ route('reservations.index', array_merge(request()->except('status'), ['status' => 'in_progress'])) }}"
           class="{{ request('status') == 'in_progress' ? 'border-purple-600 text-purple-700 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }} py-3 px-1 border-b-2 text-sm flex items-center gap-2">
            <span>🩺 Sedang Terapi</span>
            <span class="{{ request('status') == 'in_progress' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }} text-xs py-0.5 px-2 rounded-full font-bold">
                {{ $inProgressCount }}
            </span>
        </a>
    </nav>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4 items-center justify-between">
    <form method="GET" action="{{ route('reservations.index') }}" class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari kode atau nama/no HP pasien..."
                   class="px-3 py-2 pl-9 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-72">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        
        <select name="status" class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Reservasi Aktif ({{ $activeCount }})</option>
            <option value="pending_confirmation" {{ request('status') == 'pending_confirmation' ? 'selected' : '' }}>Menunggu Konfirmasi ({{ $pendingCount }})</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Terkonfirmasi ({{ $confirmedCount }})</option>
            <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>Sedang Terapi Berjenjang ({{ $inProgressCount }})</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('reservations.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline font-medium">Reset</a>
        @endif
    </form>

    <div class="text-xs text-gray-500 hidden md:block">
        Jika reservasi dibatalkan atau selesai, akan otomatis pindah ke <a href="{{ route('reservations.history') }}" class="text-blue-600 font-bold hover:underline">Riwayat Terapi →</a>
    </div>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Kode Reservasi</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Pasien & No HP</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Keluhan Utama</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Jadwal Diinginkan</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Terapis</th>
                <th class="px-6 py-3.5 text-left font-semibold text-gray-600 uppercase tracking-wider text-xs">Status</th>
                <th class="px-6 py-3.5 text-right font-semibold text-gray-600 uppercase tracking-wider text-xs">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($reservations as $res)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-mono font-bold text-blue-600 whitespace-nowrap">
                        <a href="{{ route('reservations.show', $res->id) }}" class="hover:underline">
                            {{ $res->reservation_code }}
                        </a>
                        <div class="text-[10px] text-gray-400 font-sans mt-0.5">
                            {{ $res->created_at->format('d/m/Y H:i') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $res->patient?->full_name }}</div>
                        <div class="text-xs text-gray-500 font-mono mt-0.5">📱 {{ $res->patient?->phone_number }} (Usia: {{ $res->patient?->age ?? '-' }} thn)</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900 max-w-xs truncate font-medium text-xs">{{ $res->chief_complaint }}</div>
                        <div class="text-[11px] text-gray-400 mt-0.5">Durasi: {{ $res->complaint_duration }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 text-xs">
                        <div class="font-medium">{{ $res->preferred_schedule }}</div>
                        @if($res->confirmed_schedule)
                            <div class="text-[11px] text-emerald-600 font-semibold mt-0.5">Terkonfirmasi: {{ \Carbon\Carbon::parse($res->confirmed_schedule)->format('d M Y, H:i') }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs">
                        <div class="font-medium text-gray-900">{{ $res->therapist?->full_name ?? 'Ditentukan Klinik' }}</div>
                        @if($res->therapist)
                            <div class="text-[11px] text-gray-500">{{ $res->therapist->specialization }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($res->status == 'pending_confirmation')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Menunggu Konfirmasi
                            </span>
                        @elseif($res->status == 'confirmed')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                Terkonfirmasi
                            </span>
                        @elseif($res->status == 'in_progress')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-50 text-purple-700 border border-purple-200">
                                Sedang Terapi
                            </span>
                        @elseif($res->status == 'completed')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Selesai
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                Batal
                            </span>
                        @endif
                        @if($res->payment_status === 'paid')
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    <span>✓</span> Lunas
                                </span>
                            </div>
                        @elseif($res->total_price > 0 || $res->sessions->count() > 0)
                            <div class="mt-1">
                                <a href="{{ route('cashier.process', $res->id) }}" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 px-2 py-0.5 rounded-full border border-amber-200 transition">
                                    <span>💳</span> Kasir
                                </a>
                            </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-1.5">
                            @if($res->payment_status === 'paid')
                                <a href="{{ route('cashier.receipt', $res->id) }}" target="_blank"
                                   title="Cetak Kwitansi Pembayaran"
                                   class="px-2.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-blue-200">
                                    <span>🖨️</span> Kwitansi
                                </a>
                            @elseif($res->total_price > 0 || $res->sessions->count() > 0)
                                <a href="{{ route('cashier.process', $res->id) }}"
                                   title="Bayar di Kasir"
                                   class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-amber-200">
                                    <span>💳</span> Kasir
                                </a>
                            @endif
                            @php
                                $rawPh = $res->patient?->phone_number ?? '';
                                $cleanPh = preg_replace('/\D/', '', $rawPh);
                                if (str_starts_with($cleanPh, '0')) {
                                    $waNum = '62' . substr($cleanPh, 1);
                                } elseif (str_starts_with($cleanPh, '8')) {
                                    $waNum = '62' . $cleanPh;
                                } else {
                                    $waNum = $cleanPh;
                                }
                                $msg = "Halo Kak " . ($res->patient?->full_name ?? 'Pasien') . ", pengingat jadwal terapi Anda di Sport Therapy Clinic: " . $res->preferred_schedule . ". Mohon hadir 10-15 menit sebelum sesi. Terima kasih!";
                                $waLink = !empty($waNum) ? "https://wa.me/{$waNum}?text=" . rawurlencode($msg) : null;
                            @endphp
                            @if($waLink)
                                <a href="{{ $waLink }}" target="_blank"
                                   title="Kirim Pengingat WhatsApp"
                                   class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-emerald-200">
                                    <span>📱</span> WA
                                </a>
                            @endif

                            {{-- Tombol Batal --}}
                            <button type="button" 
                                    onclick="openCancelModal('{{ $res->id }}', '{{ $res->reservation_code }}', '{{ addslashes($res->patient?->full_name ?? 'Pasien') }}')"
                                    title="Batalkan Reservasi Ini"
                                    class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg transition text-xs font-semibold flex items-center gap-1 border border-rose-200 cursor-pointer">
                                <span>✕</span> Batal
                            </button>

                            <a href="{{ route('reservations.show', $res->id) }}"
                               class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-medium rounded-lg transition">
                                Detail →
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">⚡</div>
                        <p class="font-medium text-gray-500">Tidak ada reservasi aktif saat ini.</p>
                        <p class="text-xs text-gray-400 mt-1">
                            Reservasi yang selesai atau dibatalkan dapat dilihat pada 
                            <a href="{{ route('reservations.history') }}" class="text-blue-600 font-bold hover:underline">Riwayat Terapi</a>.
                        </p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $reservations->links() }}
    </div>
</div>

{{-- MODAL BATALKAN RESERVASI --}}
<div id="cancelReservationModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl overflow-hidden border border-gray-100 transform transition-all animate-fadeIn">
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

        <form id="cancelReservationForm" method="POST" action="">
            @csrf
            <div class="p-5 space-y-4">
                <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-xs space-y-1">
                    <div><span class="text-gray-500">Kode Reservasi:</span> <span id="modalResCode" class="font-mono font-bold text-gray-900"></span></div>
                    <div><span class="text-gray-500">Pasien:</span> <span id="modalPatientName" class="font-bold text-gray-900"></span></div>
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
                    <span>Reservasi yang dibatalkan akan otomatis turun dan diarsipkan pada modul <strong>Riwayat Terapi</strong>.</span>
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

<script>
function openCancelModal(id, code, patientName) {
    const modal = document.getElementById('cancelReservationModal');
    const form = document.getElementById('cancelReservationForm');
    const codeEl = document.getElementById('modalResCode');
    const nameEl = document.getElementById('modalPatientName');
    const reasonInput = document.getElementById('cancellationReasonInput');
    const quickSelect = document.getElementById('reasonQuickSelect');

    form.action = `/reservations/${id}/cancel`;
    codeEl.textContent = code;
    nameEl.textContent = patientName;
    reasonInput.value = '';
    quickSelect.value = '';

    modal.classList.remove('hidden');
}

function closeCancelModal() {
    const modal = document.getElementById('cancelReservationModal');
    modal.classList.add('hidden');
}

function applyQuickReason(val) {
    const reasonInput = document.getElementById('cancellationReasonInput');
    if (val && val !== 'Lainnya') {
        reasonInput.value = val;
    } else if (val === 'Lainnya') {
        reasonInput.value = '';
        reasonInput.focus();
    }
}

// Close modal on click outside
window.addEventListener('click', function(e) {
    const modal = document.getElementById('cancelReservationModal');
    if (e.target === modal) {
        closeCancelModal();
    }
});
</script>

@endsection
