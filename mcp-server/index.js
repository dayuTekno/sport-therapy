#!/usr/bin/env node

const path = require('path');
const dotenv = require('dotenv');
const mysql = require('mysql2/promise');
const { v4: uuidv4 } = require('uuid');
const { McpServer } = require('@modelcontextprotocol/sdk/server/mcp.js');
const { StdioServerTransport } = require('@modelcontextprotocol/sdk/server/stdio.js');
const { z } = require('zod');

// Load environment from Laravel .env file
dotenv.config({ path: path.resolve(__dirname, '../.env') });

const dbConfig = {
  host: process.env.DB_HOST || '127.0.0.1',
  port: parseInt(process.env.MYSQL_PORT || (process.env.DB_CONNECTION === 'pgsql' ? '3306' : process.env.DB_PORT) || '3306', 10),
  database: process.env.DB_DATABASE || 'db_clinic',
  user: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD !== undefined ? process.env.DB_PASSWORD : 'localhost',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
};

const pool = mysql.createPool(dbConfig);

// Helper function to test DB connection
async function testDbConnection() {
  try {
    const conn = await pool.getConnection();
    conn.release();
    return true;
  } catch (err) {
    console.error('Database connection failed:', err.message);
    return false;
  }
}

// Generate reservation code: RSV-YYYYMMDD-XXX
async function generateReservationCode() {
  const now = new Date();
  const yyyy = now.getFullYear();
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const dd = String(now.getDate()).padStart(2, '0');
  const dateStr = `${yyyy}${mm}${dd}`;

  const [rows] = await pool.query(
    "SELECT COUNT(*) as cnt FROM reservations WHERE DATE(created_at) = CURDATE()"
  );
  const count = rows[0]?.cnt || 0;
  const seq = String(count + 1).padStart(3, '0');
  return `RSV-${dateStr}-${seq}`;
}

// Initialize MCP Server
const server = new McpServer({
  name: 'sport-therapist-clinic-mcp',
  version: '2.0.0',
});

// ==========================================
// TOOL 1: Lookup Patient by Phone (No NIK)
// ==========================================
server.tool(
  'lookup_patient_by_phone',
  'Cari data master pasien berdasarkan Nomor HP/WA. Mencegah penginputan berulang jika pasien pernah terdaftar.',
  {
    phone_number: z.string().describe('Nomor HP/WA pasien (misal: 08123456789 atau 85183036722)'),
  },
  async ({ phone_number }) => {
    const cleanedPhone = phone_number.replace(/\D/g, '');
    const searchPhone = cleanedPhone.startsWith('0') ? cleanedPhone.substring(1) : cleanedPhone;

    const [rows] = await pool.query(
      `SELECT id, patient_code, full_name, age, gender, occupation, phone_number, address, created_at 
       FROM master_patients 
       WHERE phone_number LIKE ? OR phone_number LIKE ? 
       LIMIT 1`,
      [`%${searchPhone}%`, `%${cleanedPhone}%`]
    );

    if (rows.length === 0) {
      return {
        content: [
          {
            type: 'text',
            text: JSON.stringify({
              found: false,
              message: `Pasien dengan nomor HP ${phone_number} belum terdaftar di Master Data Pasien.`,
            }, null, 2),
          },
        ],
      };
    }

    const patient = rows[0];
    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            found: true,
            message: 'Data master pasien ditemukan (auto-fill biodata).',
            patient: {
              id: patient.id,
              patient_code: patient.patient_code,
              full_name: patient.full_name,
              age: patient.age,
              gender: patient.gender,
              occupation: patient.occupation,
              phone_number: patient.phone_number,
              address: patient.address,
              registered_since: patient.created_at,
            },
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 2: Save or Update Master Patient
// ==========================================
server.tool(
  'save_or_update_patient',
  'Daftarkan atau perbarui master data pasien tanpa NIK, dikunci berdasarkan Nomor HP.',
  {
    phone_number: z.string().describe('Nomor HP/WA pasien (kunci utama unik)'),
    full_name: z.string().describe('Nama Lengkap Pasien'),
    age: z.number().optional().describe('Usia pasien'),
    gender: z.enum(['male', 'female']).optional().describe('Jenis Kelamin (male: Laki-laki, female: Perempuan)'),
    occupation: z.string().optional().describe('Pekerjaan pasien'),
    address: z.string().optional().describe('Alamat pasien'),
  },
  async ({ phone_number, full_name, age, gender, occupation, address }) => {
    const cleanedPhone = phone_number.replace(/\D/g, '');
    const searchPhone = cleanedPhone.startsWith('0') ? cleanedPhone.substring(1) : cleanedPhone;

    const [existing] = await pool.query(
      `SELECT id FROM master_patients WHERE phone_number LIKE ? OR phone_number LIKE ? LIMIT 1`,
      [`%${searchPhone}%`, `%${cleanedPhone}%`]
    );

    let patientId;
    let actionType;

    if (existing.length > 0) {
      patientId = existing[0].id;
      actionType = 'updated';

      await pool.query(
        `UPDATE master_patients 
         SET full_name = COALESCE(?, full_name),
             age = COALESCE(?, age),
             gender = COALESCE(?, gender),
             occupation = COALESCE(?, occupation),
             address = COALESCE(?, address),
             updated_at = NOW()
         WHERE id = ?`,
        [full_name, age || null, gender || null, occupation || null, address || null, patientId]
      );
    } else {
      actionType = 'created';
      const patientCode = uuidv4();

      const [result] = await pool.query(
        `INSERT INTO master_patients 
         (patient_code, full_name, age, gender, occupation, phone_number, address, created_at, updated_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
        [patientCode, full_name, age || null, gender || 'male', occupation || null, phone_number, address || null]
      );
      patientId = result.insertId;
    }

    const [patientRows] = await pool.query(
      `SELECT id, patient_code, full_name, age, gender, occupation, phone_number, address FROM master_patients WHERE id = ?`,
      [patientId]
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            status: 'success',
            action: actionType,
            message: `Data pasien berhasil di-${actionType}.`,
            patient: patientRows[0],
          }, null, 2),
        },
      ],
    };
  }
);

// =========================================================================
// TOOL 3: Create Therapy Reservation (10 Fields + Initial Stage A Session)
// =========================================================================
server.tool(
  'create_therapy_reservation',
  'Buat reservasi terapi baru (10 data pasien & keluhan). Otomatis reuse/simpan Master Pasien via Nomor HP dan inisialisasi sesi Terapi Tahap A.',
  {
    full_name: z.string().describe('1. Nama Lengkap Pasien'),
    age: z.number().describe('2. Usia Pasien'),
    gender: z.enum(['male', 'female']).describe('3. Jenis Kelamin (male = Laki-laki, female = Perempuan)'),
    occupation: z.string().describe('4. Pekerjaan'),
    address: z.string().describe('5. Alamat'),
    phone_number: z.string().describe('6. Nomor HP/WA'),
    chief_complaint: z.string().describe('7. Keluhan Utama'),
    complaint_duration: z.string().describe('8. Sudah berapa lama keluhan dirasakan (misal: 2 minggu, 3 hari)'),
    medical_history: z.string().optional().describe('9. Riwayat penyakit (jika ada, opsional)'),
    preferred_schedule: z.string().describe('10. Hari dan jam yang diinginkan untuk terapi (misal: Senin, 10:00 WIB)'),
    therapist_id: z.number().optional().describe('ID Terapis yang diinginkan (opsional)'),
  },
  async ({
    full_name,
    age,
    gender,
    occupation,
    address,
    phone_number,
    chief_complaint,
    complaint_duration,
    medical_history,
    preferred_schedule,
    therapist_id,
  }) => {
    const cleanedPhone = phone_number.replace(/\D/g, '');
    const searchPhone = cleanedPhone.startsWith('0') ? cleanedPhone.substring(1) : cleanedPhone;

    // 1. Check or save patient
    const [existing] = await pool.query(
      `SELECT id, patient_code, full_name, age, gender, occupation, address FROM master_patients 
       WHERE phone_number LIKE ? OR phone_number LIKE ? LIMIT 1`,
      [`%${searchPhone}%`, `%${cleanedPhone}%`]
    );

    let patientId;
    let isExistingPatient = false;

    if (existing.length > 0) {
      patientId = existing[0].id;
      isExistingPatient = true;

      await pool.query(
        `UPDATE master_patients 
         SET full_name = COALESCE(?, full_name),
             age = COALESCE(?, age),
             gender = COALESCE(?, gender),
             occupation = COALESCE(?, occupation),
             address = COALESCE(?, address),
             updated_at = NOW()
         WHERE id = ?`,
        [full_name, age, gender, occupation, address, patientId]
      );
    } else {
      const patientCode = uuidv4();
      const [insertRes] = await pool.query(
        `INSERT INTO master_patients 
         (patient_code, full_name, age, gender, occupation, phone_number, address, created_at, updated_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
        [patientCode, full_name, age, gender, occupation, phone_number, address]
      );
      patientId = insertRes.insertId;
    }

    // 2. Validasi jadwal bentrok terapis (tolak jika terapis yang sama diminta pada jam yang sama)
    if (therapist_id) {
      const [conflicts] = await pool.query(
        `SELECT r.id, r.reservation_code, r.preferred_schedule, r.confirmed_schedule, p.full_name as patient_name, t.full_name as therapist_name
         FROM reservations r
         JOIN master_patients p ON r.patient_id = p.id
         JOIN therapists t ON r.therapist_id = t.id
         WHERE r.therapist_id = ?
           AND r.status IN ('pending_confirmation', 'confirmed', 'in_progress')
           AND (r.preferred_schedule = ? OR (r.confirmed_schedule IS NOT NULL AND r.confirmed_schedule LIKE ?))
         LIMIT 1`,
        [therapist_id, preferred_schedule, `%${preferred_schedule}%`]
      );

      if (conflicts.length > 0) {
        const c = conflicts[0];
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                status: 'error',
                message: `Jadwal bentrok: Terapis ${c.therapist_name} sudah memiliki jadwal reservasi (${c.reservation_code}) pada waktu '${c.preferred_schedule}' dengan pasien ${c.patient_name}. Permintaan jadwal ditolak, silakan pilih jam atau terapis lain.`,
              }, null, 2),
            },
          ],
        };
      }
    }

    // 3. Generate code and create reservation
    const reservationCode = await generateReservationCode();

    const [resResult] = await pool.query(
      `INSERT INTO reservations 
       (reservation_code, patient_id, therapist_id, chief_complaint, complaint_duration, medical_history, preferred_schedule, status, current_stage, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_confirmation', 1, NOW(), NOW())`,
      [
        reservationCode,
        patientId,
        therapist_id || null,
        chief_complaint,
        complaint_duration,
        medical_history || null,
        preferred_schedule,
      ]
    );

    const reservationId = resResult.insertId;

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            status: 'success',
            reservation_id: reservationId,
            reservation_code: reservationCode,
            confirmation_message: 'Setelah data dikirim, kami akan mengonfirmasi jadwal terapi.',
            patient: {
              id: patientId,
              is_existing_patient: isExistingPatient,
              full_name,
              phone_number,
              age,
              gender,
              occupation,
              address,
            },
            reservation_details: {
              chief_complaint,
              complaint_duration,
              medical_history: medical_history || '-',
              preferred_schedule,
              assigned_therapist_id: therapist_id || null,
              status: 'pending_confirmation',
            },
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 4: List Reservations
// ==========================================
server.tool(
  'list_reservations',
  'Lihat daftar reservasi terapi (filter berdasarkan status, nomor HP, atau tampilkan semua).',
  {
    status: z.enum(['all', 'pending_confirmation', 'confirmed', 'in_progress', 'completed', 'cancelled']).optional().describe('Filter status reservasi (default: all)'),
    phone_number: z.string().optional().describe('Filter berdasarkan nomor HP pasien'),
    limit: z.number().optional().describe('Jumlah maksimal data yang diambil (default: 20)'),
  },
  async ({ status = 'all', phone_number, limit = 20 }) => {
    let sql = `
      SELECT 
        r.id,
        r.reservation_code,
        r.status,
        r.current_stage,
        r.chief_complaint,
        r.complaint_duration,
        r.medical_history,
        r.preferred_schedule,
        r.confirmed_schedule,
        r.created_at,
        p.full_name AS patient_name,
        p.phone_number AS patient_phone,
        p.age AS patient_age,
        p.gender AS patient_gender,
        p.occupation AS patient_occupation,
        t.full_name AS therapist_name,
        t.specialization AS therapist_specialization
      FROM reservations r
      JOIN master_patients p ON r.patient_id = p.id
      LEFT JOIN therapists t ON r.therapist_id = t.id
      WHERE 1=1
    `;
    const params = [];

    if (status && status !== 'all') {
      sql += ' AND r.status = ?';
      params.push(status);
    }

    if (phone_number) {
      const cleaned = phone_number.replace(/\D/g, '');
      sql += ' AND (p.phone_number LIKE ?)';
      params.push(`%${cleaned}%`);
    }

    sql += ' ORDER BY r.created_at DESC LIMIT ?';
    params.push(limit);

    const [rows] = await pool.query(sql, params);

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            count: rows.length,
            reservations: rows,
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 5: Confirm Reservation Schedule
// ==========================================
server.tool(
  'confirm_reservation_schedule',
  'Konfirmasi jadwal terapi dan tentukan Terapis penanggung jawab.',
  {
    reservation_id: z.number().describe('ID reservasi yang akan dikonfirmasi'),
    confirmed_schedule: z.string().describe('Jadwal terapi yang disetujui/dikonfirmasi (Format datetime YYYY-MM-DD HH:mm:ss atau deskripsi jadwal)'),
    therapist_id: z.number().optional().describe('ID Terapis yang ditugaskan'),
    notes: z.string().optional().describe('Catatan khusus untuk terapi'),
  },
  async ({ reservation_id, confirmed_schedule, therapist_id, notes }) => {
    const [check] = await pool.query('SELECT id, reservation_code, status FROM reservations WHERE id = ?', [reservation_id]);
    if (check.length === 0) {
      return {
        content: [
          {
            type: 'text',
            text: JSON.stringify({ status: 'error', message: `Reservasi dengan ID ${reservation_id} tidak ditemukan.` }),
          },
        ],
      };
    }

    const targetTherapistId = therapist_id || check[0].therapist_id;

    if (targetTherapistId) {
      const [conflicts] = await pool.query(
        `SELECT r.id, r.reservation_code, r.confirmed_schedule, p.full_name as patient_name, t.full_name as therapist_name
         FROM reservations r
         JOIN master_patients p ON r.patient_id = p.id
         JOIN therapists t ON r.therapist_id = t.id
         WHERE r.therapist_id = ?
           AND r.id != ?
           AND r.status IN ('pending_confirmation', 'confirmed', 'in_progress')
           AND (r.confirmed_schedule = ? OR r.preferred_schedule = ?)
         LIMIT 1`,
        [targetTherapistId, reservation_id, confirmed_schedule, confirmed_schedule]
      );

      if (conflicts.length > 0) {
        const c = conflicts[0];
        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                status: 'error',
                message: `Jadwal bentrok: Terapis ${c.therapist_name} sudah memiliki jadwal (${c.reservation_code}) pada jam '${confirmed_schedule}' dengan pasien ${c.patient_name}. Konfirmasi jadwal ditolak.`,
              }, null, 2),
            },
          ],
        };
      }
    }

    await pool.query(
      `UPDATE reservations 
       SET status = 'confirmed',
           confirmed_schedule = ?,
           therapist_id = COALESCE(?, therapist_id),
           notes = COALESCE(?, notes),
           updated_at = NOW()
       WHERE id = ?`,
      [confirmed_schedule, therapist_id || null, notes || null, reservation_id]
    );

    // Update first session scheduled_at as well
    await pool.query(
      `UPDATE therapy_sessions 
       SET scheduled_at = ?,
           therapist_id = COALESCE(?, therapist_id),
           status = 'scheduled',
           updated_at = NOW()
       WHERE reservation_id = ? AND stage_number = 1`,
      [confirmed_schedule, therapist_id || null, reservation_id]
    );

    const [updatedRows] = await pool.query(
      `SELECT r.*, p.full_name AS patient_name, p.phone_number AS patient_phone, t.full_name AS therapist_name
       FROM reservations r
       JOIN master_patients p ON r.patient_id = p.id
       LEFT JOIN therapists t ON r.therapist_id = t.id
       WHERE r.id = ?`,
      [reservation_id]
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            status: 'success',
            message: 'Jadwal terapi berhasil dikonfirmasi.',
            reservation: updatedRows[0],
          }, null, 2),
        },
      ],
    };
  }
);

// =========================================================================
// TOOL 6: Advance Therapy Stage (Terapi Berjenjang: Terapi A -> Terapi B dst.)
// ==========================================
server.tool(
  'advance_therapy_stage',
  'Lanjutkan sesi terapi berjenjang pasien dari satu tahap ke tahap berikutnya (misal: setelah Terapi A selesai, lanjut Terapi B).',
  {
    reservation_id: z.number().describe('ID reservasi pasien'),
    current_stage_evaluation: z.string().describe('Catatan evaluasi hasil terapi pada tahap saat ini'),
    next_stage_therapy_code: z.string().optional().describe('Kode jenis terapi berikutnya (misal: TRP-B, TRP-C). Kosongkan untuk otomatis ke jenjang berikutnya'),
    scheduled_at: z.string().optional().describe('Jadwal untuk terapi tahap berikutnya (YYYY-MM-DD HH:mm:ss)'),
    therapist_id: z.number().optional().describe('ID Terapis untuk tahap berikutnya (opsional, default: terapis sebelumnya)'),
  },
  async ({ reservation_id, current_stage_evaluation, next_stage_therapy_code, scheduled_at, therapist_id }) => {
    const [resRows] = await pool.query(
      'SELECT id, patient_id, therapist_id, current_stage FROM reservations WHERE id = ?',
      [reservation_id]
    );

    if (resRows.length === 0) {
      return {
        content: [
          {
            type: 'text',
            text: JSON.stringify({ status: 'error', message: `Reservasi ID ${reservation_id} tidak ditemukan.` }),
          },
        ],
      };
    }

    const reservation = resRows[0];
    const currentStage = reservation.current_stage;
    const nextStage = currentStage + 1;

    // 1. Mark current stage as completed with evaluation notes
    await pool.query(
      `UPDATE therapy_sessions 
       SET status = 'completed',
           evaluation_notes = ?,
           recommended_next_stage = ?,
           completed_at = NOW(),
           updated_at = NOW()
       WHERE reservation_id = ? AND stage_number = ?`,
      [
        current_stage_evaluation,
        `Lanjut ke Tahap ${nextStage}`,
        reservation_id,
        currentStage,
      ]
    );

    // 2. Determine next therapy type
    let nextType = null;
    if (next_stage_therapy_code) {
      const [typeRows] = await pool.query('SELECT * FROM therapy_types WHERE code = ?', [next_stage_therapy_code]);
      if (typeRows.length > 0) nextType = typeRows[0];
    }

    if (!nextType) {
      const [typeRows] = await pool.query('SELECT * FROM therapy_types WHERE stage_order = ?', [nextStage]);
      if (typeRows.length > 0) nextType = typeRows[0];
    }

    const nextStageName = nextType 
      ? nextType.name 
      : `Terapi Tahap ${String.fromCharCode(64 + nextStage)}: Latihan Pemulihan & Penguatan Lanjutan`;

    const nextTherapistId = therapist_id || reservation.therapist_id;

    // 3. Insert new session for the next stage
    const [sessionRes] = await pool.query(
      `INSERT INTO therapy_sessions 
       (reservation_id, patient_id, therapist_id, therapy_type_id, stage_number, stage_name, scheduled_at, status, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
      [
        reservation_id,
        reservation.patient_id,
        nextTherapistId || null,
        nextType?.id || null,
        nextStage,
        nextStageName,
        scheduled_at || null,
        scheduled_at ? 'scheduled' : 'pending',
      ]
    );

    // 4. Update reservation current_stage
    await pool.query(
      'UPDATE reservations SET current_stage = ?, updated_at = NOW() WHERE id = ?',
      [nextStage, reservation_id]
    );

    // 5. Fetch full progression history
    const [allSessions] = await pool.query(
      `SELECT s.id, s.stage_number, s.stage_name, s.status, s.evaluation_notes, s.scheduled_at, s.completed_at, t.full_name as therapist_name
       FROM therapy_sessions s
       LEFT JOIN therapists t ON s.therapist_id = t.id
       WHERE s.reservation_id = ?
       ORDER BY s.stage_number ASC`,
      [reservation_id]
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            status: 'success',
            message: `Berhasil menyelesaikan Terapi Tahap ${currentStage} dan membuka ${nextStageName}.`,
            reservation_id,
            current_stage: nextStage,
            new_session_id: sessionRes.insertId,
            progression_history: allSessions,
          }, null, 2),
        },
      ],
    };
  }
);

// =========================================================================
// TOOL 7: Get Patient Sequential Therapy History (Riwayat Jenjang Terapi)
// ==========================================
server.tool(
  'get_patient_therapy_history',
  'Lihat rekam jejak terapi berjenjang pasien dari tahap awal sampai tahap lanjutan lengkap dengan evaluasi.',
  {
    reservation_id: z.number().optional().describe('ID Reservasi'),
    phone_number: z.string().optional().describe('Nomor HP Pasien'),
  },
  async ({ reservation_id, phone_number }) => {
    let patientId = null;
    let patientData = null;

    if (reservation_id) {
      const [resRows] = await pool.query(
        `SELECT r.id, r.reservation_code, r.patient_id, p.full_name as patient_name, p.phone_number, p.age, p.occupation, r.chief_complaint
         FROM reservations r
         JOIN master_patients p ON r.patient_id = p.id
         WHERE r.id = ?`,
        [reservation_id]
      );
      if (resRows.length > 0) {
        patientId = resRows[0].patient_id;
        patientData = resRows[0];
      }
    } else if (phone_number) {
      const cleaned = phone_number.replace(/\D/g, '');
      const search = cleaned.startsWith('0') ? cleaned.substring(1) : cleaned;
      const [pRows] = await pool.query(
        `SELECT id as patient_id, full_name as patient_name, phone_number, age, occupation
         FROM master_patients
         WHERE phone_number LIKE ? OR phone_number LIKE ? LIMIT 1`,
        [`%${search}%`, `%${cleaned}%`]
      );
      if (pRows.length > 0) {
        patientId = pRows[0].patient_id;
        patientData = pRows[0];
      }
    }

    if (!patientId) {
      return {
        content: [
          {
            type: 'text',
            text: JSON.stringify({ status: 'error', message: 'Data pasien tidak ditemukan.' }),
          },
        ],
      };
    }

    const [sessions] = await pool.query(
      `SELECT s.id, s.stage_number, s.stage_name, s.daily_session_order, s.status, s.scheduled_at, s.completed_at,
              s.actions_taken, s.evaluation_notes, s.recommended_next_stage,
              t.full_name as therapist_name, t.specialization as therapist_specialization
       FROM therapy_sessions s
       LEFT JOIN therapists t ON s.therapist_id = t.id
       WHERE s.patient_id = ?
       ORDER BY s.scheduled_at ASC, s.daily_session_order ASC`,
      [patientId]
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            patient: {
              id: patientId,
              name: patientData.patient_name,
              phone: patientData.phone_number,
              age: patientData.age,
              occupation: patientData.occupation,
              chief_complaint: patientData.chief_complaint || '-',
            },
            total_sessions_recorded: sessions.length,
            sessions: sessions,
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 8: List Catalog of Therapy Types
// ==========================================
server.tool(
  'list_therapy_types',
  'Lihat katalog jenis dan tahapan terapi berjenjang yang tersedia di klinik.',
  {},
  async () => {
    const [rows] = await pool.query(
      `SELECT id, code, name, description, duration_minutes, stage_order, price, is_active 
       FROM therapy_types 
       WHERE is_active = 1 
       ORDER BY stage_order ASC`
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            count: rows.length,
            therapy_stages: rows,
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 9: List Therapists (Pure Terapis)
// ==========================================
server.tool(
  'list_therapists',
  'Lihat daftar Terapis aktif beserta keahlian / spesialisasi (sepenuhnya Terapis tanpa dokter).',
  {},
  async () => {
    const [rows] = await pool.query(
      `SELECT id, therapist_code, full_name, specialization, phone, email, is_active 
       FROM therapists 
       WHERE is_active = 1`
    );

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            count: rows.length,
            therapists: rows,
          }, null, 2),
        },
      ],
    };
  }
);

// =========================================================================
// TOOL 10: List Therapy Equipment Stock (Pengganti Apotek & Obat)
// =========================================================================
server.tool(
  'list_therapy_equipments',
  'Lihat stok peralatan terapi klinik (menggantikan menu apotek/obat).',
  {
    category: z.string().optional().describe('Filter kategori peralatan terapi'),
  },
  async ({ category }) => {
    let sql = `SELECT id, equipment_code, name, category, stock, uom, \`condition\`, is_available, description FROM therapy_equipments WHERE 1=1`;
    const params = [];

    if (category) {
      sql += ' AND category LIKE ?';
      params.push(`%${category}%`);
    }

    sql += ' ORDER BY name ASC';
    const [rows] = await pool.query(sql, params);

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            count: rows.length,
            title: 'Stok Peralatan Terapi',
            equipments: rows,
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 11: Update Therapy Equipment Stock
// ==========================================
server.tool(
  'update_equipment_stock',
  'Perbarui jumlah stok peralatan terapi (tambah, kurangi, atau set stok baru).',
  {
    equipment_id: z.number().describe('ID peralatan terapi'),
    stock_delta: z.number().optional().describe('Perubahan stok (+ atau -)'),
    new_stock: z.number().optional().describe('Set langsung nilai total stok baru'),
    notes: z.string().optional().describe('Catatan perubahan stok'),
  },
  async ({ equipment_id, stock_delta, new_stock, notes }) => {
    const [item] = await pool.query('SELECT * FROM therapy_equipments WHERE id = ?', [equipment_id]);
    if (item.length === 0) {
      return {
        content: [
          {
            type: 'text',
            text: JSON.stringify({ status: 'error', message: `Peralatan dengan ID ${equipment_id} tidak ditemukan.` }),
          },
        ],
      };
    }

    let finalStock = item[0].stock;
    if (new_stock !== undefined) {
      finalStock = new_stock;
    } else if (stock_delta !== undefined) {
      finalStock += stock_delta;
    }

    if (finalStock < 0) finalStock = 0;

    await pool.query('UPDATE therapy_equipments SET stock = ?, updated_at = NOW() WHERE id = ?', [finalStock, equipment_id]);

    const [updated] = await pool.query('SELECT * FROM therapy_equipments WHERE id = ?', [equipment_id]);

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            status: 'success',
            message: 'Stok peralatan terapi berhasil diperbarui.',
            equipment: updated[0],
            notes: notes || '-',
          }, null, 2),
        },
      ],
    };
  }
);

// ==========================================
// TOOL 12: Clinic Overview / Dashboard Summary
// ==========================================
server.tool(
  'get_clinic_summary',
  'Dapatkan ringkasan statistik klinik terapi (total pasien, antrian reservasi, jenjang terapi berjalan, terapis, stok alat).',
  {},
  async () => {
    const [patientCount] = await pool.query('SELECT COUNT(*) AS total FROM master_patients');
    const [pendingRes] = await pool.query("SELECT COUNT(*) AS total FROM reservations WHERE status = 'pending_confirmation'");
    const [confirmedRes] = await pool.query("SELECT COUNT(*) AS total FROM reservations WHERE status = 'confirmed'");
    const [therapistCount] = await pool.query('SELECT COUNT(*) AS total FROM therapists WHERE is_active = 1');
    const [equipmentStats] = await pool.query('SELECT COUNT(*) AS total_items, SUM(stock) AS total_units FROM therapy_equipments');
    const [activeStages] = await pool.query("SELECT stage_number, COUNT(*) as count FROM therapy_sessions WHERE status IN ('pending', 'scheduled', 'in_progress') GROUP BY stage_number");

    return {
      content: [
        {
          type: 'text',
          text: JSON.stringify({
            clinic_name: 'Smart Clinic Sport Therapist',
            summary: {
              total_patients: patientCount[0]?.total || 0,
              pending_reservations: pendingRes[0]?.total || 0,
              confirmed_reservations: confirmedRes[0]?.total || 0,
              active_therapists: therapistCount[0]?.total || 0,
              active_sessions_by_stage: activeStages,
              therapy_equipment: {
                total_types: equipmentStats[0]?.total_items || 0,
                total_stock_units: equipmentStats[0]?.total_units || 0,
              },
            },
          }, null, 2),
        },
      ],
    };
  }
);

// Start Server with Stdio Transport
async function main() {
  const isConnected = await testDbConnection();
  if (!isConnected) {
    console.error('[MCP Warning] Database is not reachable at startup.');
  }

  const transport = new StdioServerTransport();
  await server.connect(transport);
  console.error('[MCP Server] Sport Therapist Clinic MCP Server running on stdio');
}

main().catch((error) => {
  console.error('[MCP Server Error]', error);
  process.exit(1);
});
