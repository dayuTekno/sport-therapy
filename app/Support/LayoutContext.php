<?php

namespace App\Support;

use App\Models\Clinic;
use App\Models\LandingPageSetting;
use App\Models\Reservation;
use Illuminate\Support\Facades\Storage;

/**
 * Data bersama layout admin (sidebar, header, banner) yang di-memoize per request,
 * agar query & cek file yang sama tidak dijalankan berulang di setiap partial.
 * Didaftarkan sebagai scoped singleton di AppServiceProvider.
 */
class LayoutContext
{
    protected bool $clinicResolved = false;
    protected ?Clinic $activeClinic = null;
    protected ?array $branding = null;
    protected ?array $reservationBadges = null;
    protected array $fileExists = [];

    /**
     * Klinik yang sedang aktif: klinik yang ditinjau SaaS admin, klinik milik user, atau klinik pertama
     */
    public function activeClinic(): ?Clinic
    {
        if (!$this->clinicResolved) {
            $this->clinicResolved = true;
            $this->activeClinic = session()->has('active_clinic_id')
                ? Clinic::find(session('active_clinic_id'))
                : (auth()->user()?->clinic ?: Clinic::first());
        }

        return $this->activeClinic;
    }

    /**
     * Klinik yang sedang ditinjau SaaS admin (mode impersonasi), atau null
     */
    public function impersonatedClinic(): ?Clinic
    {
        return session()->has('active_clinic_id') ? $this->activeClinic() : null;
    }

    public function branding(): array
    {
        if ($this->branding === null) {
            $this->branding = LandingPageSetting::getSection('branding')?->content_json ?? [];
        }

        return $this->branding;
    }

    /**
     * URL file di disk public jika ada, selain itu fallback (hasil cek file disimpan per request)
     */
    public function publicFileUrl(?string $path, string $fallback): string
    {
        if (!$path) {
            return $fallback;
        }

        $this->fileExists[$path] ??= Storage::disk('public')->exists($path);

        return $this->fileExists[$path] ? asset('storage/' . $path) : $fallback;
    }

    /**
     * Badge jumlah reservasi di sidebar dalam satu query agregat (tetap mengikuti ClinicScope)
     */
    public function reservationBadges(): array
    {
        if ($this->reservationBadges === null) {
            $row = Reservation::query()
                ->selectRaw("COUNT(*) FILTER (WHERE status = 'pending_confirmation') as pending_count")
                ->selectRaw("COUNT(*) FILTER (WHERE status IN ('completed', 'cancelled')) as history_count")
                ->first();

            $this->reservationBadges = [
                'pending' => (int) ($row->pending_count ?? 0),
                'history' => (int) ($row->history_count ?? 0),
            ];
        }

        return $this->reservationBadges;
    }
}
