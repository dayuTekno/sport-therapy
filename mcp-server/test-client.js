const { Client } = require('@modelcontextprotocol/sdk/client/index.js');
const { StdioClientTransport } = require('@modelcontextprotocol/sdk/client/stdio.js');
const path = require('path');

async function runTest() {
  console.log('Testing Upgraded MCP Server (Pure Terapis & Sequential Multi-Stage Therapy)...');
  const serverPath = path.resolve(__dirname, 'index.js');

  const transport = new StdioClientTransport({
    command: 'node',
    args: [serverPath],
  });

  const client = new Client(
    { name: 'test-client', version: '2.0.0' },
    { capabilities: {} }
  );

  await client.connect(transport);
  console.log('Connected to MCP server successfully!');

  // 1. List tools
  const tools = await client.listTools();
  console.log('\nAvailable tools:', tools.tools.map(t => t.name));

  // 2. List therapists (pure terapis)
  const therapistRes = await client.callTool({
    name: 'list_therapists',
    arguments: {},
  });
  console.log('\n[list_therapists]:\n', therapistRes.content[0].text);

  // 3. List therapy stages catalog
  const typesRes = await client.callTool({
    name: 'list_therapy_types',
    arguments: {},
  });
  console.log('\n[list_therapy_types]:\n', typesRes.content[0].text);

  // 4. Create reservation with 10 fields for patient
  const createRes = await client.callTool({
    name: 'create_therapy_reservation',
    arguments: {
      full_name: 'Dimas Wicaksono',
      age: 24,
      gender: 'male',
      occupation: 'Pemain Futsal Profesional',
      address: 'Jl. Olahraga No. 45, Bandung',
      phone_number: '081399887766',
      chief_complaint: 'Robekan parsial hamstring kiri saat sprint',
      complaint_duration: '5 hari yang lalu',
      medical_history: 'Tidak ada',
      preferred_schedule: 'Senin, 09:00 WIB',
      therapist_id: 2,
    },
  });
  console.log('\n[create_therapy_reservation (Stage A auto-initialized)]:\n', createRes.content[0].text);
  const created = JSON.parse(createRes.content[0].text);
  const reservationId = created.reservation_id;

  // 5. Confirm schedule
  const confirmRes = await client.callTool({
    name: 'confirm_reservation_schedule',
    arguments: {
      reservation_id: reservationId,
      confirmed_schedule: '2026-10-12 09:00:00',
      therapist_id: 2,
      notes: 'Terapi A: Penanganan akut, RICE + Manual Soft Tissue Release',
    },
  });
  console.log('\n[confirm_reservation_schedule]:\n', confirmRes.content[0].text);

  // 6. Advance therapy stage: Complete Stage A -> Open Stage B
  const advanceRes = await client.callTool({
    name: 'advance_therapy_stage',
    arguments: {
      reservation_id: reservationId,
      current_stage_evaluation: 'Nyeri akut berkurang dari VAS 8 ke VAS 3. Hematoma mengecil. Siap masuk fase pemulihan elastisitas jaringan.',
      next_stage_therapy_code: 'TRP-B',
      scheduled_at: '2026-10-15 10:00:00',
      therapist_id: 2,
    },
  });
  console.log('\n[advance_therapy_stage (Terapi A -> Terapi B)]:\n', advanceRes.content[0].text);

  // 7. Advance therapy stage again: Complete Stage B -> Open Stage C
  const advanceCRes = await client.callTool({
    name: 'advance_therapy_stage',
    arguments: {
      reservation_id: reservationId,
      current_stage_evaluation: 'Elektroterapi & US selesai. Fleksibilitas hamstring membaik 80%. Siap masuk latihan penguatan fungsional.',
      next_stage_therapy_code: 'TRP-C',
      scheduled_at: '2026-10-19 14:00:00',
      therapist_id: 3,
    },
  });
  console.log('\n[advance_therapy_stage (Terapi B -> Terapi C)]:\n', advanceCRes.content[0].text);

  // 8. Get patient therapy sequential history
  const historyRes = await client.callTool({
    name: 'get_patient_therapy_history',
    arguments: {
      reservation_id: reservationId,
    },
  });
  console.log('\n[get_patient_therapy_history (Multi-stage Track)]:\n', historyRes.content[0].text);

  // 9. Get clinic summary
  const summaryRes = await client.callTool({
    name: 'get_clinic_summary',
    arguments: {},
  });
  console.log('\n[get_clinic_summary]:\n', summaryRes.content[0].text);

  await client.close();
  console.log('\nAll sequential multi-stage therapy tests passed!');
  process.exit(0);
}

runTest().catch((err) => {
  console.error('Test error:', err);
  process.exit(1);
});
