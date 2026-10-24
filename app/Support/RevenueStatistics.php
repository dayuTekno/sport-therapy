<?php

namespace App\Support;

use App\Models\Reservation;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Statistik pendapatan real klinik dari transaksi kasir yang sudah lunas, per rentang tanggal.
 *
 * Pendapatan real = nilai tagihan bersih (total_price + other_fee - discount), BUKAN paid_amount,
 * karena paid_amount adalah uang yang diterima kasir (termasuk kembalian).
 * Semua agregasi dilakukan di database & tetap mengikuti ClinicScope (data klinik aktif saja).
 */
class RevenueStatistics
{
    public const MAX_RANGE_DAYS = 731;

    /** Rentang lebih panjang dari ini ditampilkan per bulan, bukan per hari */
    public const DAILY_BUCKET_MAX_DAYS = 62;

    protected const NET_EXPR = 'CASE WHEN (COALESCE(total_price, 0) + COALESCE(other_fee, 0) - COALESCE(discount, 0)) < 0 THEN 0 '
        . 'ELSE (COALESCE(total_price, 0) + COALESCE(other_fee, 0) - COALESCE(discount, 0)) END';

    public function __construct(public readonly Carbon $from, public readonly Carbon $to)
    {
    }

    /**
     * Buat dari input request (rev_from / rev_to); default: awal bulan ini s/d hari ini
     */
    public static function fromInput(?string $from, ?string $to): self
    {
        $parse = function (?string $value, Carbon $default): Carbon {
            try {
                return $value ? Carbon::createFromFormat('Y-m-d', $value)->startOfDay() : $default;
            } catch (\Throwable) {
                return $default;
            }
        };

        $start = $parse($from, now()->startOfMonth());
        $end = $parse($to, now()->startOfDay());

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }
        if ($start->diffInDays($end) > self::MAX_RANGE_DAYS) {
            $start = $end->copy()->subDays(self::MAX_RANGE_DAYS);
        }

        return new self($start->startOfDay(), $end->endOfDay());
    }

    public function days(): int
    {
        return (int) $this->from->copy()->startOfDay()->diffInDays($this->to->copy()->startOfDay()) + 1;
    }

    protected function paidQuery(Carbon $from, Carbon $to)
    {
        return Reservation::query()
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$from, $to]);
    }

    /**
     * Ringkasan periode + perbandingan dengan periode sebelumnya yang sama panjang
     */
    public function summary(): array
    {
        $current = $this->totals($this->from, $this->to);

        $prevTo = $this->from->copy()->subDay()->endOfDay();
        $prevFrom = $prevTo->copy()->subDays($this->days() - 1)->startOfDay();
        $previous = $this->totals($prevFrom, $prevTo);

        $change = $previous['revenue'] > 0
            ? round((($current['revenue'] - $previous['revenue']) / $previous['revenue']) * 100, 1)
            : null;

        return $current + [
            'average' => $current['transactions'] > 0 ? $current['revenue'] / $current['transactions'] : 0,
            'previous_revenue' => $previous['revenue'],
            'previous_from' => $prevFrom,
            'previous_to' => $prevTo,
            'change_percent' => $change,
        ];
    }

    protected function totals(Carbon $from, Carbon $to): array
    {
        $row = $this->paidQuery($from, $to)
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('COALESCE(SUM(' . self::NET_EXPR . '), 0) as revenue')
            ->selectRaw('COALESCE(SUM(discount), 0) as discount_total')
            ->selectRaw('COALESCE(SUM(other_fee), 0) as other_fee_total')
            ->first();

        return [
            'transactions' => (int) ($row->transactions ?? 0),
            'revenue' => (float) ($row->revenue ?? 0),
            'discount_total' => (float) ($row->discount_total ?? 0),
            'other_fee_total' => (float) ($row->other_fee_total ?? 0),
        ];
    }

    /**
     * Deret waktu pendapatan (per hari, atau per bulan untuk rentang panjang), termasuk periode tanpa transaksi
     */
    public function series(): array
    {
        $rows = $this->paidQuery($this->from, $this->to)
            ->selectRaw('DATE(paid_at) as day')
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('COALESCE(SUM(' . self::NET_EXPR . '), 0) as revenue')
            ->groupByRaw('DATE(paid_at)')
            ->get()
            ->keyBy(fn ($r) => substr((string) $r->day, 0, 10));

        $monthly = $this->days() > self::DAILY_BUCKET_MAX_DAYS;
        $buckets = [];

        foreach (CarbonPeriod::create($this->from->copy()->startOfDay(), $this->to->copy()->startOfDay()) as $date) {
            $key = $monthly ? $date->format('Y-m') : $date->format('Y-m-d');
            $local = $date->copy()->locale('id');
            $buckets[$key] ??= [
                'label' => $monthly ? $local->translatedFormat('M Y') : $local->translatedFormat('d M'),
                'full_label' => $monthly ? $local->translatedFormat('F Y') : $local->translatedFormat('l, d M Y'),
                'revenue' => 0.0,
                'transactions' => 0,
            ];

            if ($row = $rows->get($date->format('Y-m-d'))) {
                $buckets[$key]['revenue'] += (float) $row->revenue;
                $buckets[$key]['transactions'] += (int) $row->transactions;
            }
        }

        return [
            'granularity' => $monthly ? 'month' : 'day',
            'points' => array_values($buckets),
        ];
    }

    /**
     * Pendapatan per metode pembayaran ("Lainnya: ..." digabung menjadi "Lainnya")
     */
    public function byPaymentMethod(): array
    {
        $methodExpr = "CASE WHEN payment_method IS NULL OR payment_method = '' THEN 'Cash' "
            . "WHEN payment_method LIKE 'Lainnya%' THEN 'Lainnya' ELSE payment_method END";

        return $this->paidQuery($this->from, $this->to)
            ->selectRaw("{$methodExpr} as method")
            ->selectRaw('COUNT(*) as transactions')
            ->selectRaw('COALESCE(SUM(' . self::NET_EXPR . '), 0) as revenue')
            ->groupByRaw($methodExpr)
            ->orderByDesc('revenue')
            ->get()
            ->map(fn ($r) => [
                'method' => (string) $r->method,
                'transactions' => (int) $r->transactions,
                'revenue' => (float) $r->revenue,
            ])
            ->all();
    }

    /**
     * Angka ringkas untuk sumbu: 0 / 500rb / 1,2jt
     */
    public static function compactRupiah(float $value): string
    {
        if ($value >= 1_000_000_000) {
            return rtrim(rtrim(number_format($value / 1_000_000_000, 1, ',', '.'), '0'), ',') . 'M';
        }
        if ($value >= 1_000_000) {
            return rtrim(rtrim(number_format($value / 1_000_000, 1, ',', '.'), '0'), ',') . 'jt';
        }
        if ($value >= 1_000) {
            return number_format($value / 1_000, 0, ',', '.') . 'rb';
        }

        return number_format($value, 0, ',', '.');
    }

    /**
     * Batas atas sumbu Y yang "bulat" + 4 garis bantu
     */
    public static function niceTicks(float $max): array
    {
        if ($max <= 0) {
            return [0, 250000, 500000, 750000, 1000000];
        }

        $rawStep = $max / 4;
        $magnitude = 10 ** floor(log10($rawStep));
        $step = collect([1, 2, 2.5, 5, 10])->map(fn ($m) => $m * $magnitude)->first(fn ($s) => $s >= $rawStep);

        return array_map(fn ($i) => $i * $step, range(0, 4));
    }
}
