# Sport Therapist Clinic MCP Server (v2.0)

Model Context Protocol (MCP) Server untuk sistem klinik **Sport Therapist** murni (tanpa dokter) dengan alur **Terapi Berjenjang (Multi-Stage / Sequential Therapy)**.

---

## 🌟 Fitur Utama & Penyesuaian

1. **Sepenuhnya Terapis (Tanpa Dokter)**:
   - Istilah dan entitas dokter dieliminasi total.
   - Tabel [`therapists`](file:///Users/developer/Work/Darma%20Ayu%20Tekno/Project/Laravel/SPORT-THERAPIST-RSTBDI/app/Models/Therapist.php) mengelola profil Terapis Olahraga & Spesialisasi.
2. **Terapi Berjenjang (Sequential Multi-Stage Therapy)**:
   - Mendukung alur terapi bertahap (misal: **Terapi Tahap A $\rightarrow$ Terapi Tahap B $\rightarrow$ Terapi Tahap C $\rightarrow$ Terapi Tahap D**).
   - Setiap jenjang mencatat catatan evaluasi perkembangan pasien, terapis penanggung jawab, jadwal sesi, serta status penyelesaian.
   - Tabel [`therapy_types`](file:///Users/developer/Work/Darma%20Ayu%20Tekno/Project/Laravel/SPORT-THERAPIST-RSTBDI/app/Models/TherapyType.php) dan [`therapy_sessions`](file:///Users/developer/Work/Darma%20Ayu%20Tekno/Project/Laravel/SPORT-THERAPIST-RSTBDI/app/Models/TherapySession.php).
3. **Reservasi 10 Data (Pengganti Antrian)**:
   - Data pasien & keluhan (Nama Lengkap, Usia, Jenis Kelamin, Pekerjaan, Alamat, No HP, Keluhan Utama, Durasi Keluhan, Riwayat Penyakit, Jadwal Diinginkan).
4. **Tanpa NIK/KTP & Kunci Nomor HP**:
   - Master data pasien mendeteksi nomor HP secara cerdas. Jika sudah pernah terdaftar, pasien tidak perlu menginput ulang identitas diri.
5. **Inventaris Peralatan Terapi (Pengganti Apotek & Obat)**:
   - Mengelola stok alat terapi fisik (Ultrasound, TENS, K-Tape, Massage Gun, Foam Roller, dll).

---

## 🛠️ Daftar Tools MCP

| Nama Tool | Deskripsi |
|---|---|
| `lookup_patient_by_phone` | Cek master data pasien via Nomor HP/WA (auto-fill biodata). |
| `save_or_update_patient` | Simpan/update profil pasien tanpa NIK, berkunci Nomor HP. |
| `create_therapy_reservation` | Buat reservasi dengan 10 data keluhan & otomatis membuka Terapi Tahap 1. |
| `list_reservations` | Daftar reservasi terapi (filter status atau nomor HP). |
| `confirm_reservation_schedule` | Konfirmasi jadwal dan tentukan Terapis penanggung jawab. |
| `advance_therapy_stage` | Selesaikan tahap terapi saat ini dengan catatan evaluasi dan buka tahap terapi berikutnya (Terapi A $\rightarrow$ Terapi B $\rightarrow$ Terapi C). |
| `get_patient_therapy_history` | Lihat seluruh riwayat perkembangan jenjang terapi pasien dan catatan evaluasi. |
| `list_therapy_types` | Katalog jenis & tahapan terapi berjenjang di klinik. |
| `list_therapists` | Daftar Terapis aktif & spesialisasinya (murni Terapis). |
| `list_therapy_equipments` | Inventaris & stok peralatan terapi. |
| `update_equipment_stock` | Tambah/kurang/atur stok alat terapi. |
| `get_clinic_summary` | Ringkasan statistik operasional klinik terapi. |

---

## 🚀 Pengujian & Menjalankan

```bash
cd mcp-server
node test-client.js
```
