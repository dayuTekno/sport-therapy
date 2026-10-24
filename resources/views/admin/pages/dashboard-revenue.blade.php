{{-- Statistik Pendapatan Real (filter rentang tanggal) — data dari App\Support\RevenueStatistics --}}
@php
    $fromValue = $revenueStats->from->format('Y-m-d');
    $toValue = $revenueStats->to->format('Y-m-d');
    $today = now();
    $presets = [
        'Hari ini' => [$today->copy(), $today->copy()],
        '7 hari terakhir' => [$today->copy()->subDays(6), $today->copy()],
        '30 hari terakhir' => [$today->copy()->subDays(29), $today->copy()],
        'Bulan ini' => [$today->copy()->startOfMonth(), $today->copy()],
        'Bulan lalu' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
        'Tahun ini' => [$today->copy()->startOfYear(), $today->copy()],
    ];

    $points = $revenueSeries['points'];
    $maxRevenue = collect($points)->max('revenue') ?? 0;
    $ticks = \App\Support\RevenueStatistics::niceTicks((float) $maxRevenue);
    $axisMax = end($ticks) ?: 1;
    $labelEvery = max(1, (int) ceil(count($points) / 8));
    $methodTotal = collect($revenueByMethod)->sum('revenue');
    $change = $revenueSummary['change_percent'];
    $periodText = $revenueStats->from->copy()->locale('id')->translatedFormat('d M Y')
        . ($fromValue !== $toValue ? ' – ' . $revenueStats->to->copy()->locale('id')->translatedFormat('d M Y') : '');
    $prevText = $revenueSummary['previous_from']->copy()->locale('id')->translatedFormat('d M')
        . ' – ' . $revenueSummary['previous_to']->copy()->locale('id')->translatedFormat('d M Y');
@endphp

<section id="statistik-pendapatan" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5 md:p-7 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4 mb-5">
        <div>
            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">Statistik Pendapatan Real</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Nilai tagihan lunas dari kasir (total + biaya lain − diskon), tanpa uang kembalian · {{ $periodText }}
            </p>
        </div>

        {{-- FILTER RENTANG TANGGAL --}}
        <form method="GET" action="{{ route('dashboard') }}#statistik-pendapatan" class="flex flex-wrap items-end gap-2 text-xs">
            <div>
                <label for="rev_from" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dari</label>
                <input type="date" id="rev_from" name="rev_from" value="{{ $fromValue }}" max="{{ $today->format('Y-m-d') }}"
                       class="px-3 py-2 text-xs border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="rev_to" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai</label>
                <input type="date" id="rev_to" name="rev_to" value="{{ $toValue }}" max="{{ $today->format('Y-m-d') }}"
                       class="px-3 py-2 text-xs border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-xl focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-xl transition cursor-pointer">Terapkan</button>
        </form>
    </div>

    {{-- PRESET RENTANG --}}
    <div class="flex flex-wrap gap-1.5 mb-6">
        @foreach($presets as $label => [$pFrom, $pTo])
            @php $isActive = $pFrom->format('Y-m-d') === $fromValue && $pTo->format('Y-m-d') === $toValue; @endphp
            <a href="{{ route('dashboard', ['rev_from' => $pFrom->format('Y-m-d'), 'rev_to' => $pTo->format('Y-m-d')]) }}#statistik-pendapatan"
               class="px-3 py-1.5 rounded-lg text-[11px] font-semibold border transition {{ $isActive ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:border-blue-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="col-span-2 lg:col-span-1 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60">
            <span class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold block">Pendapatan real</span>
            <span class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white block mt-1">Rp {{ number_format($revenueSummary['revenue'], 0, ',', '.') }}</span>
            @if($change !== null)
                <span class="inline-flex items-center gap-1 mt-1.5 text-[11px] font-bold {{ $change >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    <span aria-hidden="true">{{ $change >= 0 ? '▲' : '▼' }}</span>
                    {{ $change >= 0 ? 'Naik' : 'Turun' }} {{ number_format(abs($change), 1, ',', '.') }}%
                    <span class="font-normal text-gray-400">vs {{ $prevText }}</span>
                </span>
            @else
                <span class="block mt-1.5 text-[11px] text-gray-400">Periode sebelumnya ({{ $prevText }}) belum ada pendapatan</span>
            @endif
        </div>
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60">
            <span class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold block">Transaksi lunas</span>
            <span class="text-xl font-black text-gray-900 dark:text-white block mt-1">{{ number_format($revenueSummary['transactions'], 0, ',', '.') }}</span>
            <span class="text-[11px] text-gray-400">dalam {{ $revenueStats->days() }} hari</span>
        </div>
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60">
            <span class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold block">Rata-rata per transaksi</span>
            <span class="text-xl font-black text-gray-900 dark:text-white block mt-1">Rp {{ number_format($revenueSummary['average'], 0, ',', '.') }}</span>
        </div>
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 space-y-1.5">
            <div>
                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold block">Diskon diberikan</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($revenueSummary['discount_total'], 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-semibold block">Biaya lain-lain</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($revenueSummary['other_fee_total'], 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- GRAFIK PENDAPATAN --}}
        <div class="xl:col-span-2">
            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 mb-5">
                Pendapatan {{ $revenueSeries['granularity'] === 'month' ? 'per bulan' : 'per hari' }}
            </h3>

            @if($revenueSummary['transactions'] === 0)
                <div class="h-56 flex items-center justify-center rounded-xl border border-dashed border-gray-200 dark:border-gray-700 text-xs text-gray-400">
                    Belum ada transaksi lunas pada periode ini.
                </div>
            @else
                <div class="relative pt-2" x-data="{ active: null, points: @js($points) }" @mouseleave="active = null">
                    <div class="flex gap-2">
                        {{-- Sumbu Y --}}
                        <div class="relative h-56 w-12 shrink-0 text-[10px] text-gray-400 tabular-nums" aria-hidden="true">
                            @foreach($ticks as $tick)
                                <span class="absolute right-0 translate-y-1/2 leading-none" style="bottom: {{ $tick / $axisMax * 100 }}%">{{ \App\Support\RevenueStatistics::compactRupiah($tick) }}</span>
                            @endforeach
                        </div>

                        <div class="relative flex-1 min-w-0">
                            {{-- Garis bantu --}}
                            <div class="absolute inset-x-0 top-0 h-56 pointer-events-none" aria-hidden="true">
                                @foreach($ticks as $tick)
                                    <div class="absolute inset-x-0 border-t border-gray-100 dark:border-gray-800" style="bottom: {{ $tick / $axisMax * 100 }}%"></div>
                                @endforeach
                            </div>

                            {{-- Kolom --}}
                            <div class="relative h-56 flex items-end gap-[2px]" role="list" aria-label="Grafik pendapatan">
                                @foreach($points as $i => $point)
                                    <div class="group flex-1 h-full flex items-end justify-center cursor-default focus:outline-none"
                                         role="listitem" tabindex="0"
                                         aria-label="{{ $point['full_label'] }}: Rp {{ number_format($point['revenue'], 0, ',', '.') }}, {{ $point['transactions'] }} transaksi"
                                         @mouseenter="active = {{ $i }}" @focus="active = {{ $i }}" @blur="active = null">
                                        <div class="w-full max-w-[24px] rounded-t bg-blue-500 transition-colors group-hover:bg-blue-600 group-focus:bg-blue-600"
                                             style="height: {{ $point['revenue'] > 0 ? max(1, $point['revenue'] / $axisMax * 100) : 0 }}%"></div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Label sumbu X (selektif) --}}
                            <div class="flex gap-[2px] mt-1.5 text-[10px] text-gray-400" aria-hidden="true">
                                @foreach($points as $i => $point)
                                    <div class="flex-1 text-center whitespace-nowrap overflow-visible">
                                        @if($i % $labelEvery === 0 || $i === count($points) - 1)
                                            {{ $point['label'] }}
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            {{-- Tooltip --}}
                            <template x-if="active !== null">
                                <div class="absolute top-0 z-10 pointer-events-none px-3 py-2 rounded-lg bg-gray-900 text-white text-[11px] shadow-lg whitespace-nowrap"
                                     :style="`left: ${(active + 0.5) / points.length * 100}%; transform: translateX(${active > points.length / 2 ? '-105%' : '5%'})`">
                                    <span class="block font-black text-sm" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(points[active].revenue)"></span>
                                    <span class="block text-gray-300" x-text="points[active].transactions + ' transaksi · ' + points[active].full_label"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Tabel data (alternatif grafik) --}}
                <details class="mt-4 text-xs">
                    <summary class="cursor-pointer text-blue-600 font-semibold">Lihat tabel data</summary>
                    <div class="mt-2 max-h-64 overflow-y-auto border border-gray-100 dark:border-gray-800 rounded-xl">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                                <tr class="text-gray-500 dark:text-gray-400">
                                    <th class="px-3 py-2 text-left font-semibold">{{ $revenueSeries['granularity'] === 'month' ? 'Bulan' : 'Tanggal' }}</th>
                                    <th class="px-3 py-2 text-right font-semibold">Transaksi</th>
                                    <th class="px-3 py-2 text-right font-semibold">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 tabular-nums text-gray-700 dark:text-gray-300">
                                @foreach($points as $point)
                                    <tr>
                                        <td class="px-3 py-1.5">{{ $point['full_label'] }}</td>
                                        <td class="px-3 py-1.5 text-right">{{ $point['transactions'] }}</td>
                                        <td class="px-3 py-1.5 text-right font-semibold">Rp {{ number_format($point['revenue'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @endif
        </div>

        {{-- PER METODE PEMBAYARAN --}}
        <div>
            <h3 class="text-xs font-bold text-gray-700 dark:text-gray-200 mb-3">Per metode pembayaran</h3>
            <ul class="space-y-3 text-xs">
                @forelse($revenueByMethod as $row)
                    @php $share = $methodTotal > 0 ? $row['revenue'] / $methodTotal * 100 : 0; @endphp
                    <li>
                        <div class="flex items-baseline justify-between gap-2 mb-1">
                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $row['method'] }}</span>
                            <span class="text-gray-900 dark:text-white font-bold tabular-nums">Rp {{ number_format($row['revenue'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden" aria-hidden="true">
                            <div class="h-full rounded-full bg-blue-500" style="width: {{ $share }}%"></div>
                        </div>
                        <span class="text-[10px] text-gray-400">{{ $row['transactions'] }} transaksi · {{ number_format($share, 1, ',', '.') }}%</span>
                    </li>
                @empty
                    <li class="text-gray-400">Belum ada transaksi.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>
