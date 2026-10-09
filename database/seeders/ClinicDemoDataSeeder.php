<?php

namespace Database\Seeders;

use App\Models\Antrian;
use App\Models\Clinic;
use App\Models\MasterDoctor;
use App\Models\MasterEselon;
use App\Models\MasterIcd;
use App\Models\MasterMedicine;
use App\Models\MasterPatients;
use App\Models\MasterPolyclinic;
use App\Models\MasterProcedure;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordMedicine;
use App\Models\MedicalRecordTreatment;
use App\Models\Reservation;
use App\Models\Therapist;
use App\Models\TherapyEquipment;
use App\Models\TherapySession;
use App\Models\TherapyType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClinicDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ----------------------------------------------------------------------
        // 1. PASTIKAN KLINIK TERDAFTAR (TENANT UTAMA)
        // ----------------------------------------------------------------------
        $clinic = Clinic::first();
        if (!$clinic) {
            $clinic = Clinic::create([
                'clinic_code' => 'CLN-RSTBDI-001',
                'name' => 'Sport Therapy Clinic RSTBDI',
                'slug' => 'sport-therapy-rstbdi',
                'subdomain' => 'rstbdi',
                'phone_number' => '085183036722',
                'email' => 'klinik.rstbdi@mail.com',
                'address' => 'Jl. Pelayanan Kesehatan No. 10',
                'status' => 'active',
                'max_therapists' => 10,
                'max_patients' => 1000,
                'is_active' => true,
                'notes' => 'Klinik utama / tenant awal platform Sport Therapy RSTBDI.',
            ]);
        }

        $clinicId = $clinic->id;

        // ----------------------------------------------------------------------
        // 2. MASTER ESELON / KATEGORI PENJAMIN PASIEN
        // ----------------------------------------------------------------------
        $eselonsData = [
            ['eselon_code' => 'UMUM', 'name' => 'Pasien Umum / Mandiri', 'desc' => 'Pembayaran tunai / transfer mandiri'],
            ['eselon_code' => 'BPJS', 'name' => 'BPJS Kesehatan', 'desc' => 'Jaminan BPJS Kesehatan'],
            ['eselon_code' => 'ASR-MANDIRI', 'name' => 'Asuransi Swasta / Mandiri Inhealth', 'desc' => 'Klaim asuransi rekanan perusahaan'],
            ['eselon_code' => 'ATLET-CLUB', 'name' => 'Klub Olahraga / Atlet Prestasi', 'desc' => 'Kerjasama klub olahraga binaan klinik'],
        ];

        $eselonModels = [];
        foreach ($eselonsData as $e) {
            $eselonModels[$e['eselon_code']] = MasterEselon::firstOrCreate(
                ['eselon_code' => $e['eselon_code']],
                ['name' => $e['name'], 'desc' => $e['desc'], 'is_active' => true]
            );
        }

        // ----------------------------------------------------------------------
        // 3. MASTER POLIKLINIK
        // ----------------------------------------------------------------------
        $polyData = [
            ['polyclinic_code' => 'POL-SPORTS', 'name' => 'Poli Fisioterapi & Cedera Olahraga'],
            ['polyclinic_code' => 'POL-REHAB', 'name' => 'Poli Rehabilitasi Medik & Fisik'],
            ['polyclinic_code' => 'POL-ORTHO', 'name' => 'Poli Konsultasi Ortopedi'],
        ];

        $polyModels = [];
        foreach ($polyData as $p) {
            $polyModels[$p['polyclinic_code']] = MasterPolyclinic::firstOrCreate(
                ['polyclinic_code' => $p['polyclinic_code']],
                ['name' => $p['name'], 'is_active' => true]
            );
        }

        // ----------------------------------------------------------------------
        // 4. MASTER DOKTER
        // ----------------------------------------------------------------------
        $doctorsData = [
            [
                'doctor_code' => 'DOC-001',
                'employee_code' => 'EMP-D01',
                'license_number' => 'SIP-503/001/IDI/2024',
                'full_name' => 'dr. Raditya Pratama, Sp.KFR',
                'specialization' => 'Spesialis Kedokteran Fisik & Rehabilitasi Olahraga',
                'phone' => '081298761111',
                'email' => 'dr.raditya@mail.com',
            ],
            [
                'doctor_code' => 'DOC-002',
                'employee_code' => 'EMP-D02',
                'license_number' => 'SIP-503/002/IDI/2024',
                'full_name' => 'dr. Sarah Amelia, Sp.OT',
                'specialization' => 'Spesialis Orthopedi & Traumatologi Olahraga',
                'phone' => '081298762222',
                'email' => 'dr.sarah@mail.com',
            ],
        ];

        $doctorModels = [];
        foreach ($doctorsData as $d) {
            $doctorModels[] = MasterDoctor::firstOrCreate(
                ['doctor_code' => $d['doctor_code']],
                $d
            );
        }

        // ----------------------------------------------------------------------
        // 5. MASTER TINDAKAN / PROSEDUR KLINIK
        // ----------------------------------------------------------------------
        $proceduresData = [
            ['procedure_code' => 'PRC-01', 'name' => 'Terapi Manual & Mobilisasi Sendi', 'price' => 150000, 'desc' => 'Manipulasi jaringan lunak dan sendi oleh fisioterapis'],
            ['procedure_code' => 'PRC-02', 'name' => 'Elektroterapi TENS & NMES', 'price' => 120000, 'desc' => 'Stimulasi listrik pereda nyeri dan aktivasi saraf otot'],
            ['procedure_code' => 'PRC-03', 'name' => 'Ultrasound Diathermy (USD)', 'price' => 135000, 'desc' => 'Gelombang suara penghangat jaringan dalam untuk relaksasi'],
            ['procedure_code' => 'PRC-04', 'name' => 'Latihan Penguatan Fungsional & Keseimbangan', 'price' => 180000, 'desc' => 'Exercise therapy pengembalian stabilitas pasca cedera'],
            ['procedure_code' => 'PRC-05', 'name' => 'Dry Needling & Myofascial Release', 'price' => 200000, 'desc' => 'Pelepasan trigger point otot yang tegang kronis'],
        ];

        $procedureModels = [];
        foreach ($proceduresData as $prc) {
            $procedureModels[] = MasterProcedure::firstOrCreate(
                ['procedure_code' => $prc['procedure_code']],
                $prc
            );
        }

        // ----------------------------------------------------------------------
        // 6. MASTER OBAT & BAHAN MEDIS (FARMASI)
        // ----------------------------------------------------------------------
        $medicinesData = [
            ['medicine_name' => 'Natrium Diklofenak 50mg Tablet', 'price' => 15000, 'discount_from_source' => 0],
            ['medicine_name' => 'Paracetamol 500mg Tablet', 'price' => 8000, 'discount_from_source' => 0],
            ['medicine_name' => 'Voltaren Emulgel 20g (Perontok Nyeri Topikal)', 'price' => 85000, 'discount_from_source' => 5000],
            ['medicine_name' => 'Mecobalamin 500mcg Kapsul', 'price' => 35000, 'discount_from_source' => 0],
            ['medicine_name' => 'Kinesio Tape Roll Waterproof 5m', 'price' => 65000, 'discount_from_source' => 0],
        ];

        $medicineModels = [];
        foreach ($medicinesData as $med) {
            $medicineModels[] = MasterMedicine::firstOrCreate(
                ['medicine_name' => $med['medicine_name']],
                $med
            );
        }

        // ----------------------------------------------------------------------
        // 7. MASTER DIAGNOSA ICD-10 & ICD-9
        // ----------------------------------------------------------------------
        $icdData = [
            ['category' => '10', 'icd_code' => 'M25.5', 'name' => 'Pain in joint (Nyeri Sendi Lutut / Ankle)', 'version' => 'ICD-10'],
            ['category' => '10', 'icd_code' => 'M75.1', 'name' => 'Rotator cuff syndrome (Cedera Sendi Bahu)', 'version' => 'ICD-10'],
            ['category' => '10', 'icd_code' => 'M54.5', 'name' => 'Low back pain (Nyeri Pinggang Bawah)', 'version' => 'ICD-10'],
            ['category' => '10', 'icd_code' => 'S83.5', 'name' => 'Sprain and strain of cruciate ligament (Cedera ACL)', 'version' => 'ICD-10'],
            ['category' => '9', 'icd_code' => '93.11', 'name' => 'Assisting exercise / Terapi Latihan Aktif', 'version' => 'ICD-9'],
            ['category' => '9', 'icd_code' => '93.35', 'name' => 'Other physical therapy (Ultrasound & Elektroterapi)', 'version' => 'ICD-9'],
        ];

        $icdModels = [];
        foreach ($icdData as $icd) {
            $icdModels[] = MasterIcd::firstOrCreate(
                ['icd_code' => $icd['icd_code'], 'category' => $icd['category']],
                $icd
            );
        }

        // ----------------------------------------------------------------------
        // 8. TERAPIS OLAHRAGA (SPORTS THERAPISTS)
        // ----------------------------------------------------------------------
        $therapistsData = [
            [
                'therapist_code' => 'TRP-001',
                'full_name' => 'Arif Hidayat, S.Ftr',
                'specialization' => 'Spesialis Cedera Lutut (ACL/MCL) & Ankle Sprain',
                'phone' => '081311223344',
                'email' => 'arif.terapis@mail.com',
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
            [
                'therapist_code' => 'TRP-002',
                'full_name' => 'Nadia Safitri, S.Or',
                'specialization' => 'Terapis Pemulihan Atlet & Koreksi Postur Skoliosis',
                'phone' => '081322334455',
                'email' => 'nadia.terapis@mail.com',
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
            [
                'therapist_code' => 'TRP-003',
                'full_name' => 'Hendra Setiawan, A.Md.Ft',
                'specialization' => 'Spesialis Manual Therapy Bahu & Punggung Bawah',
                'phone' => '081333445566',
                'email' => 'hendra.terapis@mail.com',
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
        ];

        $therapistModels = [];
        foreach ($therapistsData as $t) {
            $therapistModels[] = Therapist::firstOrCreate(
                ['therapist_code' => $t['therapist_code'], 'clinic_id' => $clinicId],
                $t
            );
        }

        // ----------------------------------------------------------------------
        // 9. KATALOG JENIS TERAPI BERJENJANG (STAGE A - D)
        // ----------------------------------------------------------------------
        $therapyTypesData = [
            [
                'code' => 'STG-A',
                'name' => 'Terapi A - Fase Akut & Pengurangan Nyeri',
                'description' => 'Fokus peredaan bengkak, inflamasi, dan nyeri akut menggunakan modalitas TENS, Cryo, dan manual relaksasi.',
                'duration_minutes' => 45,
                'stage_order' => 1,
                'price' => 175000,
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
            [
                'code' => 'STG-B',
                'name' => 'Terapi B - Mobilisasi Sendi & Fleksibilitas Otot',
                'description' => 'Mengembalikan rentang gerak sendi (Range of Motion) yang kaku dengan Ultrasound Diathermy dan joint mobilization.',
                'duration_minutes' => 45,
                'stage_order' => 2,
                'price' => 200000,
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
            [
                'code' => 'STG-C',
                'name' => 'Terapi C - Penguatan & Stabilitas Fungsional',
                'description' => 'Latihan beban bertahap, aktivasi core stability, dan proprioceptive exercise dengan alat resistance.',
                'duration_minutes' => 60,
                'stage_order' => 3,
                'price' => 250000,
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
            [
                'code' => 'STG-D',
                'name' => 'Terapi D - Return-to-Sport & Agility Drill',
                'description' => 'Simulasi gerakan olahraga spesifik, uji kelincahan, dan evaluasi kesiapan bertanding tanpa rasa takut cedera.',
                'duration_minutes' => 60,
                'stage_order' => 4,
                'price' => 300000,
                'is_active' => true,
                'clinic_id' => $clinicId,
            ],
        ];

        $therapyTypeModels = [];
        foreach ($therapyTypesData as $tt) {
            $therapyTypeModels[] = TherapyType::firstOrCreate(
                ['code' => $tt['code'], 'clinic_id' => $clinicId],
                $tt
            );
        }

        // ----------------------------------------------------------------------
        // 10. INVENTARIS PERALATAN TERAPI KLINIK
        // ----------------------------------------------------------------------
        $equipmentsData = [
            ['equipment_code' => 'EQP-01', 'name' => 'Mesin Digital TENS & NMES 4-Channel', 'category' => 'Elektroterapi', 'stock' => 6, 'uom' => 'Unit', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
            ['equipment_code' => 'EQP-02', 'name' => 'Mesin Ultrasound Therapy Multi-Frequency', 'category' => 'Modalitas Fisik', 'stock' => 4, 'uom' => 'Unit', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
            ['equipment_code' => 'EQP-03', 'name' => 'Ice Compression Boot (Cryo System)', 'category' => 'Recovery Dingin', 'stock' => 2, 'uom' => 'Unit', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
            ['equipment_code' => 'EQP-04', 'name' => 'Bosu Ball & Balance Pad Pro Set', 'category' => 'Keseimbangan', 'stock' => 8, 'uom' => 'Set', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
            ['equipment_code' => 'EQP-05', 'name' => 'Heavy Resistance Bands Set 5-Level', 'category' => 'Penguatan', 'stock' => 15, 'uom' => 'Set', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
            ['equipment_code' => 'EQP-06', 'name' => 'Goniometer & Laser ROM Measurement Tool', 'category' => 'Alat Ukur', 'stock' => 5, 'uom' => 'Unit', 'condition' => 'baik', 'is_available' => true, 'clinic_id' => $clinicId],
        ];

        foreach ($equipmentsData as $eq) {
            TherapyEquipment::firstOrCreate(
                ['equipment_code' => $eq['equipment_code'], 'clinic_id' => $clinicId],
                $eq
            );
        }

        // ----------------------------------------------------------------------
        // 11. MASTER PASIEN KLINIK
        // ----------------------------------------------------------------------
        $patientsData = [
            [
                'patient_code' => 'PAS-RSTBDI-001',
                'nik' => '3201018901010001',
                'full_name' => 'Bambang Pratama (Atlet Futsal)',
                'date_of_birth' => '1998-05-12',
                'gender' => 'male',
                'phone_number' => '081211112222',
                'address' => 'Komplek Olahraga Graha Atlet No. 5',
                'clinic_id' => $clinicId,
            ],
            [
                'patient_code' => 'PAS-RSTBDI-002',
                'nik' => '3201018902020002',
                'full_name' => 'Jessica Nathania (Pelari Maraton)',
                'date_of_birth' => '2001-08-24',
                'gender' => 'female',
                'phone_number' => '081222223333',
                'address' => 'Jl. Kenanga Asri Blok C-12',
                'clinic_id' => $clinicId,
            ],
            [
                'patient_code' => 'PAS-RSTBDI-003',
                'nik' => '3201018903030003',
                'full_name' => 'Reza Gunawan (Pekerja Kantoran)',
                'date_of_birth' => '1992-11-03',
                'gender' => 'male',
                'phone_number' => '081233334444',
                'address' => 'Apartemen Sentral Tower B Lt. 15',
                'clinic_id' => $clinicId,
            ],
            [
                'patient_code' => 'PAS-RSTBDI-004',
                'nik' => '3201018904040004',
                'full_name' => 'Dian Maharani (Pemain Bulutangkis)',
                'date_of_birth' => '2003-02-18',
                'gender' => 'female',
                'phone_number' => '081244445555',
                'address' => 'Jl. Cemara Hijau No. 20',
                'clinic_id' => $clinicId,
            ],
            [
                'patient_code' => 'PAS-RSTBDI-005',
                'nik' => '3201018905050005',
                'full_name' => 'Kevin Sanjaya (Pemain Tenis Lapangan)',
                'date_of_birth' => '1996-09-30',
                'gender' => 'male',
                'phone_number' => '081255556666',
                'address' => 'Jl. Bunga Melati No. 8',
                'clinic_id' => $clinicId,
            ],
        ];

        $patientModels = [];
        foreach ($patientsData as $p) {
            $patientModels[] = MasterPatients::firstOrCreate(
                ['patient_code' => $p['patient_code'], 'clinic_id' => $clinicId],
                $p
            );
        }

        // Petugas kasir untuk kwitansi
        $cashierUser = User::where('clinic_id', $clinicId)->first() ?? User::first();

        // ----------------------------------------------------------------------
        // 12. RESERVASI TERAPI & SESI EVALUASI BERJENJANG
        // ----------------------------------------------------------------------
        // Reservasi 1: Bambang Pratama - Selesai & Lunas Kasir (Tahap 3 Aktif)
        $res1 = Reservation::firstOrCreate(
            ['reservation_code' => 'RSV-RSTBDI-001', 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[0]->id,
                'therapist_id' => $therapistModels[0]->id,
                'chief_complaint' => 'Nyeri ligamen lutut kanan (Sprain ACL grade 1) saat mendarat lompatan futsal.',
                'complaint_duration' => '3 minggu yang lalu',
                'medical_history' => 'Tidak ada riwayat operasi sebelumnya.',
                'preferred_schedule' => 'Setiap Selasa & Kamis Pukul 09:00 WIB',
                'confirmed_schedule' => Carbon::now()->subDays(2)->setTime(9, 0),
                'status' => 'completed',
                'current_stage' => 3,
                'payment_status' => 'paid',
                'total_price' => 375000,
                'discount' => 25000,
                'paid_amount' => 350000,
                'payment_method' => 'qris',
                'invoice_code' => 'INV/' . date('Ymd') . '/001',
                'paid_at' => Carbon::now()->subHours(2),
                'cashier_user_id' => $cashierUser?->id,
                'cashier_notes' => 'Pembayaran lunas via QRIS Statis Kasir Klinik.',
                'created_at' => Carbon::now()->subDays(5),
            ]
        );

        // Sesi Berjenjang untuk Reservasi 1
        TherapySession::firstOrCreate(
            ['reservation_id' => $res1->id, 'stage_number' => 1, 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[0]->id,
                'therapist_id' => $therapistModels[0]->id,
                'therapy_type_id' => $therapyTypeModels[0]->id,
                'daily_session_order' => 1,
                'stage_name' => 'Terapi A - Penanganan Akut & Pereda Inflamasi',
                'scheduled_at' => Carbon::now()->subDays(4)->setTime(9, 0),
                'completed_at' => Carbon::now()->subDays(4)->setTime(10, 0),
                'status' => 'completed',
                'evaluation_notes' => 'Bengkak sendi lutut menurun drastis. Nyeri gerak berkurang dari skala VAS 7 ke VAS 4.',
                'recommended_next_stage' => 'Lanjut ke Terapi B (Mobilisasi Sendi)',
            ]
        );

        TherapySession::firstOrCreate(
            ['reservation_id' => $res1->id, 'stage_number' => 2, 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[0]->id,
                'therapist_id' => $therapistModels[0]->id,
                'therapy_type_id' => $therapyTypeModels[1]->id,
                'daily_session_order' => 2,
                'stage_name' => 'Terapi B - Mobilisasi Patella & Fleksi Lutut',
                'scheduled_at' => Carbon::now()->subDays(2)->setTime(9, 0),
                'completed_at' => Carbon::now()->subDays(2)->setTime(9, 50),
                'status' => 'completed',
                'evaluation_notes' => 'Fleksi lutut mencapai 120 derajat tanpa rasa sakit tajam. Siap latihan kekuatan.',
                'recommended_next_stage' => 'Lanjut ke Terapi C (Penguatan Kuadrisep & Hamstring)',
            ]
        );

        TherapySession::firstOrCreate(
            ['reservation_id' => $res1->id, 'stage_number' => 3, 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[0]->id,
                'therapist_id' => $therapistModels[0]->id,
                'therapy_type_id' => $therapyTypeModels[2]->id,
                'daily_session_order' => 3,
                'stage_name' => 'Terapi C - Penguatan & Stabilitas Fungsional',
                'scheduled_at' => Carbon::now()->addDays(2)->setTime(9, 0),
                'status' => 'scheduled',
                'evaluation_notes' => 'Target: Single leg balance dan squat beban parsial.',
                'recommended_next_stage' => null,
            ]
        );

        // Reservasi 2: Jessica Nathania - Sedang Terapi Hari Ini (Tahap 2)
        $res2 = Reservation::firstOrCreate(
            ['reservation_code' => 'RSV-RSTBDI-002', 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[1]->id,
                'therapist_id' => $therapistModels[1]->id,
                'chief_complaint' => 'Nyeri tajam pada tumit dan tendon achilles kiri saat berlari cepat.',
                'complaint_duration' => '10 hari terakhir',
                'preferred_schedule' => 'Pukul 10:30 WIB Hari Ini',
                'confirmed_schedule' => Carbon::now()->setTime(10, 30),
                'status' => 'in_progress',
                'current_stage' => 2,
                'payment_status' => 'paid',
                'total_price' => 200000,
                'discount' => 0,
                'paid_amount' => 200000,
                'payment_method' => 'cash',
                'invoice_code' => 'INV/' . date('Ymd') . '/002',
                'paid_at' => Carbon::now()->setTime(10, 0),
                'cashier_user_id' => $cashierUser?->id,
                'cashier_notes' => 'Tunai pas Rp 200.000.',
                'created_at' => Carbon::now()->subDays(2),
            ]
        );

        TherapySession::firstOrCreate(
            ['reservation_id' => $res2->id, 'stage_number' => 1, 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[1]->id,
                'therapist_id' => $therapistModels[1]->id,
                'therapy_type_id' => $therapyTypeModels[0]->id,
                'daily_session_order' => 1,
                'stage_name' => 'Terapi A - Cryotherapy & Ultrasound Tendon Achilles',
                'scheduled_at' => Carbon::now()->subDays(2)->setTime(10, 30),
                'completed_at' => Carbon::now()->subDays(2)->setTime(11, 15),
                'status' => 'completed',
                'evaluation_notes' => 'Ketegangan gastrocnemius berkurang. Nyeri tekan achilles menurun.',
                'recommended_next_stage' => 'Lanjut Terapi B',
            ]
        );

        TherapySession::firstOrCreate(
            ['reservation_id' => $res2->id, 'stage_number' => 2, 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[1]->id,
                'therapist_id' => $therapistModels[1]->id,
                'therapy_type_id' => $therapyTypeModels[1]->id,
                'daily_session_order' => 2,
                'stage_name' => 'Terapi B - Stretching Eksentrik & Calf Release',
                'scheduled_at' => Carbon::now()->setTime(10, 30),
                'status' => 'in_progress',
                'evaluation_notes' => 'Sedang dilakukan manual release betis dan taping achilles.',
            ]
        );

        // Reservasi 3: Reza Gunawan - Terkonfirmasi (Tahap 1 Menunggu Sesi)
        Reservation::firstOrCreate(
            ['reservation_code' => 'RSV-RSTBDI-003', 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[2]->id,
                'therapist_id' => $therapistModels[2]->id,
                'chief_complaint' => 'Nyeri pinggang bawah (Low back pain) akibat duduk terlalu lama di depan komputer.',
                'complaint_duration' => '1 bulan',
                'preferred_schedule' => 'Pukul 14:00 WIB',
                'confirmed_schedule' => Carbon::now()->setTime(14, 0),
                'status' => 'confirmed',
                'current_stage' => 1,
                'payment_status' => 'unpaid',
                'total_price' => 175000,
                'discount' => 0,
                'created_at' => Carbon::now()->subDay(),
            ]
        );

        // Reservasi 4: Dian Maharani - Booking Online Masuk (Menunggu Konfirmasi)
        Reservation::firstOrCreate(
            ['reservation_code' => 'RSV-RSTBDI-004', 'clinic_id' => $clinicId],
            [
                'patient_id' => $patientModels[3]->id,
                'therapist_id' => null,
                'chief_complaint' => 'Bahu kanan ngilu saat melakukan smash bulutangkis (impingement bahu).',
                'complaint_duration' => '5 hari',
                'preferred_schedule' => 'Besok Pagi Pukul 10:00 WIB',
                'status' => 'pending_confirmation',
                'current_stage' => 1,
                'payment_status' => 'unpaid',
                'created_at' => Carbon::now(),
            ]
        );

        // ----------------------------------------------------------------------
        // 13. DATA ANTRIAN POLIKLINIK & REKAM MEDIS HARI INI (DASHBOARD CLINIC)
        // ----------------------------------------------------------------------
        $today = now()->format('Y-m-d');

        // Antrian 1: Pasien Bambang Pratama (Selesai Pemeriksaan & Pembayaran Obat)
        $queue1 = Antrian::firstOrCreate(
            ['queue_number' => 1, 'created_at' => Carbon::today()->setTime(8, 0)],
            [
                'patient_id' => $patientModels[0]->id,
                'eselon_id' => $eselonModels['ATLET-CLUB']->id,
                'poly_id' => $polyModels['POL-SPORTS']->id,
                'doctor_id' => $doctorModels[0]->id,
                'status' => 'completed',
            ]
        );

        $rec1 = MedicalRecord::firstOrCreate(
            ['queue_id' => $queue1->id],
            [
                'patient_id' => $patientModels[0]->id,
                'systolic' => 120,
                'diastolic' => 80,
                'temperature' => 36.6,
                'heart_rate' => 74,
                'respiratory_rate' => 18,
                'weight' => 70,
                'height' => 175,
                'nurse_name' => 'Perawat Siti',
                'symptoms' => 'Lutut kanan nyeri saat fleksi maksimal setelah sparing futsal.',
                'diagnosis' => 'Sprain ligamen collateral medial dan ketegangan hamstring ringan.',
                'icd10_id' => $icdModels[0]->id,
                'icd9_id' => $icdModels[4]->id,
                'discount' => 0,
            ]
        );

        MedicalRecordTreatment::firstOrCreate(
            ['medical_record_id' => $rec1->id, 'procedure_id' => $procedureModels[0]->id]
        );
        MedicalRecordTreatment::firstOrCreate(
            ['medical_record_id' => $rec1->id, 'procedure_id' => $procedureModels[1]->id]
        );

        MedicalRecordMedicine::firstOrCreate(
            ['medical_record_id' => $rec1->id, 'medicine_id' => $medicineModels[0]->id],
            ['quantity' => '10', 'instructions' => '2x1 tablet sesudah makan']
        );
        MedicalRecordMedicine::firstOrCreate(
            ['medical_record_id' => $rec1->id, 'medicine_id' => $medicineModels[2]->id],
            ['quantity' => '1', 'instructions' => 'Oleskan 3x sehari pada sendi yang ngilu']
        );

        // Antrian 2: Pasien Jessica Nathania (Selesai Dokter, Berada di Farmasi)
        $queue2 = Antrian::firstOrCreate(
            ['queue_number' => 2, 'created_at' => Carbon::today()->setTime(8, 30)],
            [
                'patient_id' => $patientModels[1]->id,
                'eselon_id' => $eselonModels['UMUM']->id,
                'poly_id' => $polyModels['POL-SPORTS']->id,
                'doctor_id' => $doctorModels[0]->id,
                'status' => 'pharmacy',
            ]
        );

        $rec2 = MedicalRecord::firstOrCreate(
            ['queue_id' => $queue2->id],
            [
                'patient_id' => $patientModels[1]->id,
                'systolic' => 115,
                'diastolic' => 75,
                'temperature' => 36.5,
                'heart_rate' => 68,
                'respiratory_rate' => 16,
                'weight' => 52,
                'height' => 163,
                'nurse_name' => 'Perawat Siti',
                'symptoms' => 'Tendon achilles nyeri tegang terutama di pagi hari.',
                'diagnosis' => 'Tendinitis achilles kronis fase subakut.',
                'icd10_id' => $icdModels[0]->id,
                'discount' => 0,
            ]
        );

        MedicalRecordTreatment::firstOrCreate(
            ['medical_record_id' => $rec2->id, 'procedure_id' => $procedureModels[2]->id]
        );

        MedicalRecordMedicine::firstOrCreate(
            ['medical_record_id' => $rec2->id, 'medicine_id' => $medicineModels[3]->id],
            ['quantity' => '10', 'instructions' => '1x1 kapsul']
        );

        // Antrian 3: Pasien Reza Gunawan (Sedang Diperiksa Dokter)
        Antrian::firstOrCreate(
            ['queue_number' => 3, 'created_at' => Carbon::today()->setTime(9, 15)],
            [
                'patient_id' => $patientModels[2]->id,
                'eselon_id' => $eselonModels['ASR-MANDIRI']->id,
                'poly_id' => $polyModels['POL-REHAB']->id,
                'doctor_id' => $doctorModels[1]->id,
                'status' => 'doctor_exam',
            ]
        );

        // Antrian 4: Pasien Dian Maharani (Menunggu Antrian di Ruang Tunggu)
        Antrian::firstOrCreate(
            ['queue_number' => 4, 'created_at' => Carbon::today()->setTime(9, 45)],
            [
                'patient_id' => $patientModels[3]->id,
                'eselon_id' => $eselonModels['UMUM']->id,
                'poly_id' => $polyModels['POL-SPORTS']->id,
                'doctor_id' => $doctorModels[0]->id,
                'status' => 'waiting',
            ]
        );
    }
}
