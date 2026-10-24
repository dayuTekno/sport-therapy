@extends('admin.layouts.app')

@section('title', 'Program Referal Klinik')

@section('content')

<x-breadcrumb :items="[
    ['label' => 'SaaS Administrator', 'url' => route('saas.dashboard')],
    ['label' => 'Referal Klinik'],
]" />

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
        <span>🤝</span> Program Referal Klinik
    </h1>
    <p class="text-xs text-gray-500 mt-1">Setiap klinik otomatis memiliki kode referal. Komisi <strong>tidak</strong> diberikan saat klinik mendaftar / masa trial, melainkan dicatat sekali saat klinik yang diundang mulai berlangganan paket (status Trial/Expired → Aktif).</p>
</div>

@include('admin.partials.alert')

{{-- SETTING KOMISI PER PAKET --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <form action="{{ route('saas.referrals.settings') }}" method="POST" class="text-xs">
        @csrf

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-800">Setting Komisi per Paket Langganan</h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Besaran komisi mengikuti paket yang dilanggan klinik terundang. Isi 0 jika paket tidak memberikan komisi.</p>
            </div>
            <label class="flex items-center gap-2.5 px-3 py-2 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer shrink-0">
                <input type="checkbox" name="is_enabled" value="1" @checked(old('is_enabled', $settings['is_enabled']))
                       class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <span class="font-bold text-gray-800">Program Komisi Aktif</span>
            </label>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-xs">
                <thead>
                    <tr class="text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="py-2 pr-4">Paket</th>
                        <th class="py-2 pr-4 text-right">Harga Paket</th>
                        <th class="py-2 pr-4">Jenis Komisi</th>
                        <th class="py-2 pr-4">Nilai Komisi</th>
                        <th class="py-2 text-right">Komisi Diterima Perujuk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($plans as $plan)
                        <tr x-data="{
                                type: @js(old('plans.' . $plan->id . '.type', $plan->referral_commission_type ?: 'rupiah')),
                                value: @js((float) old('plans.' . $plan->id . '.value', (float) $plan->referral_commission_value)),
                                price: {{ (float) $plan->price }},
                                formatRupiah(num) {
                                    return new Intl.NumberFormat('id-ID').format(num);
                                },
                                get commission() {
                                    let val = parseFloat(this.value) || 0;
                                    if (this.type === 'percent') {
                                        return Math.round(this.price * Math.min(100, val) / 100);
                                    }
                                    return Math.round(val);
                                },
                                setType(type) {
                                    this.type = type;
                                    this.value = 0;
                                },
                                onInput(e) {
                                    if (this.type === 'percent') {
                                        let val = e.target.value.replace(',', '.').replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
                                        e.target.value = val;
                                        this.value = val;
                                        return;
                                    }
                                    let digits = e.target.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                                    this.value = digits === '' ? '' : parseInt(digits, 10);
                                    e.target.value = digits === '' ? '' : this.formatRupiah(this.value);
                                }
                            }">
                            <td class="py-3 pr-4">
                                <span class="font-bold text-gray-900 block">{{ $plan->name }}</span>
                                @unless($plan->is_active)
                                    <span class="text-[10px] font-semibold text-gray-400">Paket nonaktif</span>
                                @endunless
                            </td>
                            <td class="py-3 pr-4 text-right font-mono text-gray-700 whitespace-nowrap">Rp {{ number_format($plan->price, 0, ',', '.') }}</td>
                            <td class="py-3 pr-4">
                                <input type="hidden" name="plans[{{ $plan->id }}][type]" :value="type">
                                <div class="inline-flex p-0.5 bg-gray-200/80 rounded-lg font-semibold">
                                    <button type="button" @click="setType('rupiah')"
                                            :class="type === 'rupiah' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                            class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer font-bold whitespace-nowrap">
                                        Rp (Nominal)
                                    </button>
                                    <button type="button" @click="setType('percent')"
                                            :class="type === 'percent' ? 'bg-white text-blue-700 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                                            class="px-2.5 py-1 rounded-md transition text-[11px] cursor-pointer font-bold whitespace-nowrap">
                                        % (Persen)
                                    </button>
                                </div>
                            </td>
                            <td class="py-3 pr-4">
                                <div class="relative w-40">
                                    <span x-show="type === 'rupiah'" class="absolute left-3 top-2 text-xs text-gray-400 font-bold">Rp</span>
                                    <input type="text"
                                           :inputmode="type === 'percent' ? 'decimal' : 'numeric'"
                                           autocomplete="off"
                                           :value="type === 'percent' ? value : (value === '' ? '' : formatRupiah(value))"
                                           @input="onInput($event)"
                                           required
                                           :placeholder="type === 'percent' ? 'Misal: 10' : 'Misal: 1.000'"
                                           :class="type === 'rupiah' ? 'pl-9 pr-3' : 'pl-3 pr-8'"
                                           class="w-full py-2 text-xs border rounded-xl focus:ring-2 focus:ring-blue-500 font-bold text-gray-800 bg-white shadow-2xs {{ $errors->has('plans.' . $plan->id . '.value') ? 'border-rose-400' : 'border-gray-300' }}">
                                    <span x-show="type === 'percent'" class="absolute right-3 top-2 text-xs text-blue-600 font-bold">%</span>
                                    <input type="hidden" name="plans[{{ $plan->id }}][value]" :value="value">
                                </div>
                                @error('plans.' . $plan->id . '.value')
                                    <p class="text-[10px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </td>
                            <td class="py-3 text-right whitespace-nowrap">
                                <span class="font-mono font-black text-emerald-700" x-text="'Rp ' + formatRupiah(commission)"></span>
                                <span class="block text-[10px] text-gray-400" x-show="type === 'percent'" x-text="(parseFloat(value) || 0) + '% dari harga paket'"></span>
                                <span class="block text-[10px] text-gray-400" x-show="type === 'rupiah'">Nominal tetap</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-400">Belum ada paket langganan. Tambahkan paket di menu Paket Langganan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <p class="text-[10px] text-gray-400">Perubahan setting tidak mengubah komisi yang sudah tercatat.</p>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition cursor-pointer">
                Simpan Setting Komisi
            </button>
        </div>
    </form>
</div>

<div class="mb-6">
    {{-- RINGKASAN & TOP REFERRER --}}
    <div class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <span class="text-[11px] text-gray-500 font-semibold block">Klinik via Referal</span>
                <span class="text-2xl font-black text-gray-900">{{ $totalReferredClinics }}</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <span class="text-[11px] text-gray-500 font-semibold block">Komisi Belum Dibayar</span>
                <span class="text-lg font-black text-amber-600">Rp {{ number_format($totalPending, 0, ',', '.') }}</span>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                <span class="text-[11px] text-gray-500 font-semibold block">Komisi Sudah Dibayar</span>
                <span class="text-lg font-black text-emerald-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Top Klinik Perujuk</h3>
                <ul class="divide-y divide-gray-100 text-xs">
                    @forelse($topReferrers as $ref)
                        <li class="py-2 flex items-center justify-between gap-2">
                            <span class="min-w-0">
                                <span class="font-bold text-gray-900 block truncate">{{ $ref->name }}</span>
                                <span class="font-mono text-[10px] text-blue-600">{{ $ref->referral_code }}</span>
                            </span>
                            <span class="text-right shrink-0">
                                <span class="font-bold text-gray-800 block">{{ $ref->referred_clinics_count }} klinik</span>
                                <span class="text-[10px] text-gray-500">Rp {{ number_format($ref->total_commission ?? 0, 0, ',', '.') }}</span>
                            </span>
                        </li>
                    @empty
                        <li class="py-6 text-center text-gray-400">Belum ada klinik yang mendaftar via referal.</li>
                    @endforelse
                </ul>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Belum Berlangganan</h3>
                <ul class="divide-y divide-gray-100 text-xs">
                    @forelse($awaitingClinics as $ac)
                        <li class="py-2 flex items-center justify-between gap-2">
                            <span class="min-w-0">
                                <a href="{{ route('saas.clinics.edit', $ac->id) }}" class="font-bold text-gray-900 hover:text-blue-600 block truncate">{{ $ac->name }}</a>
                                <span class="text-[10px] text-gray-500">via {{ $ac->referrer?->name ?? '-' }}</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 shrink-0">{{ ucfirst($ac->status) }}</span>
                        </li>
                    @empty
                        <li class="py-6 text-center text-gray-400">Tidak ada klinik referal yang menunggu.</li>
                    @endforelse
                </ul>
                <p class="text-[10px] text-gray-400 mt-2">Komisi tercatat otomatis saat klinik mulai berlangganan paket, yaitu saat statusnya diubah dari Trial/Expired menjadi Aktif di menu Mitra Klinik.</p>
            </div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-5">
    <form method="GET" action="{{ route('saas.referrals.index') }}" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari klinik perujuk, kode, atau klinik terundang..."
               class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full sm:w-72">
        <select name="status" class="px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <option value="">Semua Status</option>
            <option value="pending" @selected(request('status') === 'pending')>Menunggu Pembayaran</option>
            <option value="paid" @selected(request('status') === 'paid')>Sudah Dibayar</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-700 transition">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('saas.referrals.index') }}" class="text-xs text-gray-500 hover:text-rose-600 underline font-medium">Reset</a>
        @endif
    </form>
</div>

{{-- TABEL KOMISI --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-xs">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Klinik Perujuk</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-500 uppercase tracking-wider">Klinik Terundang</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Harga Paket</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Komisi</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($commissions as $cm)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-900 block">{{ $cm->referrer?->name ?? '-' }}</span>
                            <span class="font-mono text-[10px] text-blue-600">{{ $cm->referrer?->referral_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-gray-900 block">{{ $cm->referred?->name ?? '-' }}</span>
                            <span class="text-[10px] text-gray-400">Tercatat {{ $cm->created_at?->format('d M Y') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-mono text-gray-700">Rp {{ number_format($cm->base_amount, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-gray-400">{{ $cm->plan?->name }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span class="font-mono font-bold text-gray-900">Rp {{ number_format($cm->commission_amount, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-gray-400">
                                {{ $cm->commission_type === 'percent' ? \App\Models\ClinicReferralCommission::formatPercent($cm->commission_value) . ' dari paket' : 'Nominal tetap' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold
                                {{ $cm->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($cm->status === 'cancelled' ? 'bg-gray-100 text-gray-500' : 'bg-amber-100 text-amber-700') }}">
                                {{ $cm->status_label }}
                            </span>
                            @if($cm->paid_at)
                                <span class="block text-[10px] text-gray-400 mt-0.5">{{ $cm->paid_at->format('d M Y') }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            @if($cm->status === 'pending')
                                <form action="{{ route('saas.referrals.paid', $cm->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Tandai komisi Rp {{ number_format($cm->commission_amount, 0, ',', '.') }} sudah dibayarkan?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-bold transition cursor-pointer">Tandai Dibayar</button>
                                </form>
                                <form action="{{ route('saas.referrals.cancel', $cm->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Batalkan komisi referal ini?')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 rounded-lg text-[11px] font-bold transition cursor-pointer">Batalkan</button>
                                </form>
                            @else
                                <span class="text-[10px] text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">Belum ada komisi referal yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">{{ $commissions->links() }}</div>
    @endif
</div>

@endsection
