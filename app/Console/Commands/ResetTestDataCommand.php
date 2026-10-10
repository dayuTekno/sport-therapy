<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetTestDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clinic:reset-test-data 
                            {--keep-patients : Tetap simpan data master pasien (hanya bersihkan riwayat transaksi)}
                            {--force : Jalankan tanpa konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan data transaksi & testing di database tanpa menghapus akun user dan data master website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keepPatients = $this->option('keep-patients');
        $force = $this->option('force');

        $this->info("==================================================");
        $this->info("   PEMBERSIHAN DATA TESTING KLINIK SPORT THERAPY   ");
        $this->info("==================================================");

        $tablesToClean = [
            'therapy_sessions'            => 'Sesi Terapi & Rekam Medis Evaluasi',
            'reservations'                => 'Reservasi / Booking Terapi',
            'antrians'                    => 'Antrian Klinik / Kasir',
            'admission_queues'            => 'Antrian Admisi Pendaftaran',
            'medical_record_diagnoses'    => 'Diagnosa Rekam Medis',
            'medical_record_procedures'   => 'Tindakan Medis',
            'medical_record_treatments'   => 'Treatment Terapi',
            'medical_record_medicines'    => 'Resep Obat Rekam Medis',
            'medical_records'             => 'Rekam Medis Pasien',
        ];

        if (!$keepPatients) {
            $tablesToClean['master_patients'] = 'Data Pasien Terdaftar';
        }

        $this->warn("Tabel yang AKAN DIBERSIHKAN (Truncate):");
        foreach ($tablesToClean as $table => $desc) {
            $this->line(" - <fg=red>{$table}</> ({$desc})");
        }

        $this->newLine();
        $this->info("Tabel yang TETAP AMAN & DIPERTAHANKAN:");
        $preserved = [
            'users' => 'Akun Login Pengguna & Staff',
            'roles & permissions' => 'Hak Akses & Role Spatie',
            'master_clinics / clinics' => 'Profil Klinik Tenant',
            'therapists' => 'Daftar Terapis Olahraga',
            'therapy_types' => 'Katalog Jenjang Terapi (Stage A - D)',
            'therapy_equipments' => 'Inventaris Peralatan Terapi',
            'master_eselons' => 'Kategori Jaminan (Umum/BPJS/Klub)',
            'master_polyclinics' => 'Daftar Poliklinik',
            'master_doctors' => 'Daftar Dokter',
            'master_procedures' => 'Daftar Prosedur / Tindakan',
            'master_medicines' => 'Daftar Obat Farmasi',
            'master_icds' => 'Katalog ICD Diagnosa',
        ];
        foreach ($preserved as $table => $desc) {
            $this->line(" - <fg=green>{$table}</> ({$desc})");
        }

        $this->newLine();

        if (!$force && !$this->confirm('Apakah Anda yakin ingin mengosongkan data transaksi di atas?', false)) {
            $this->info("Pembersihan dibatalkan.");
            return 0;
        }

        $driver = DB::getDriverName();
        $this->info("Membersihkan data menggunakan driver: {$driver}...");

        $existingTables = [];
        foreach (array_keys($tablesToClean) as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
            }
        }

        if (empty($existingTables)) {
            $this->info("Tidak ada tabel yang ditemukan untuk dibersihkan.");
            return 0;
        }

        DB::beginTransaction();
        try {
            if ($driver === 'pgsql') {
                $tablesList = implode(', ', array_map(fn($t) => "\"{$t}\"", $existingTables));
                DB::statement("TRUNCATE TABLE {$tablesList} RESTART IDENTITY CASCADE;");
            } elseif ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                foreach ($existingTables as $table) {
                    DB::table($table)->truncate();
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } else {
                DB::statement('PRAGMA foreign_keys = OFF;');
                foreach ($existingTables as $table) {
                    DB::table($table)->truncate();
                }
                DB::statement('PRAGMA foreign_keys = ON;');
            }

            DB::commit();
            $this->info("✓ Berhasil mengosongkan " . count($existingTables) . " tabel data transaksi.");
            $this->info("✓ ID Sequence telah di-reset ke 1.");
            $this->info("✓ Akun users dan master data website tetap aman 100%.");
            $this->info("Database sekarang siap untuk pengujian ulang dari awal! 🚀");
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Gagal membersihkan database: " . $e->getMessage());
            return 1;
        }
    }
}
