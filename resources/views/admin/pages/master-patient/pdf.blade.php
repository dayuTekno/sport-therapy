<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Pasien</title>
    <style>
        @page {
            margin: 12mm 15mm;
            size: a4 landscape;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
        }

        /* HEADER / KOP */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-title {
            font-size: 15pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .kop-subtitle {
            font-size: 10pt;
            font-weight: 600;
            color: #2563eb;
            margin: 2px 0;
        }

        .kop-contact {
            font-size: 8pt;
            color: #64748b;
            margin: 0;
        }

        .divider {
            border: 0;
            border-top: 2px solid #0f172a;
            border-bottom: 1px solid #94a3b8;
            height: 3px;
            margin: 8px 0 14px 0;
        }

        /* DOCUMENT TITLE & META */
        .doc-header {
            margin-bottom: 12px;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #0f172a;
            margin: 0 0 4px 0;
        }

        .meta-table {
            width: 100%;
            font-size: 8pt;
            color: #475569;
            margin-top: 4px;
        }

        .meta-table td {
            padding: 2px 0;
        }

        /* MAIN DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 14px;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }

        .data-table td {
            padding: 5px 7px;
            border: 1px solid #e2e8f0;
            font-size: 8pt;
            vertical-align: top;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }
        .font-bold { font-weight: bold; }

        .badge-male {
            color: #1d4ed8;
            font-weight: bold;
        }

        .badge-female {
            color: #be185d;
            font-weight: bold;
        }

        /* SUMMARY & SIGNATURE */
        .footer-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
            padding: 0;
        }

        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 8pt;
            line-height: 1.6;
            width: 280px;
        }

        .signature-box {
            text-align: right;
            font-size: 8.5pt;
        }

        .signature-space {
            height: 50px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT / HEADER KLINIK --}}
    <table class="kop-table">
        <tr>
            <td style="width: 70%;">
                <h1 class="kop-title">{{ $clinic?->name ?? 'Sport Therapy Clinic' }}</h1>
                <div class="kop-subtitle">Sistem Rumah Terapi</div>
                <p class="kop-contact">
                    {{ $clinic?->address ?? 'Jl. Pelayanan Kesehatan No. 10' }}
                    @if($clinic?->phone_number) | Telp: {{ $clinic->phone_number }} @endif
                    @if($clinic?->email) | Email: {{ $clinic->email }} @endif
                </p>
            </td>
            <td style="width: 30%; text-align: right; vertical-align: top;">
                <div style="font-size: 8pt; color: #64748b;">
                    <strong>Waktu Cetak:</strong><br>
                    {{ $generatedAt }}
                </div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-header">
        <div class="doc-title">Rekapitulasi Master Data Pasien</div>
        <table class="meta-table">
            <tr>
                <td style="width: 50%;">
                    @if($search)
                        <span><strong>Filter Pencarian:</strong> "{{ $search }}"</span>
                    @else
                        <span><strong>Filter:</strong> Semua Pasien Terdaftar</span>
                    @endif
                </td>
                <td style="width: 50%; text-align: right;">
                    <span><strong>Total Data:</strong> {{ $patients->count() }} Pasien</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL DATA PASIEN --}}
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 3%;">No</th>
                <th style="width: 12%;">Kode / No. RM</th>
                <th style="width: 12%;">NIK</th>
                <th style="width: 16%;">Nama Pasien</th>
                <th class="text-center" style="width: 4%;">L/P</th>
                <th class="text-center" style="width: 5%;">Usia</th>
                <th style="width: 11%;">No. HP / WA</th>
                <th style="width: 11%;">Pekerjaan</th>
                <th style="width: 13%;">Referal Terapis</th>
                <th style="width: 13%;">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($patients as $index => $patient)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-mono">{{ $patient->patient_code }}</td>
                    <td class="font-mono">{{ $patient->nik ?: '-' }}</td>
                    <td class="font-bold">{{ $patient->full_name }}</td>
                    <td class="text-center">
                        @if($patient->gender === 'male')
                            <span class="badge-male">L</span>
                        @elseif($patient->gender === 'female')
                            <span class="badge-female">P</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">{{ $patient->age ? $patient->age . ' Th' : '-' }}</td>
                    <td class="font-mono">{{ $patient->phone_number ?: '-' }}</td>
                    <td>{{ $patient->occupation ?: '-' }}</td>
                    <td>{{ $patient->referralTherapist?->full_name ?: '-' }}</td>
                    <td>{{ $patient->address ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 20px; color: #64748b;">
                        Tidak ada data pasien yang sesuai.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- FOOTER / REKAP & TANDA TANGAN --}}
    @php
        $maleCount = $patients->where('gender', 'male')->count();
        $femaleCount = $patients->where('gender', 'female')->count();
    @endphp

    <table class="footer-table">
        <tr>
            <td style="width: 60%;">
                <div class="summary-box">
                    <strong>Ringkasan Data Pasien:</strong><br>
                    • Total Pasien : <strong>{{ $patients->count() }} orang</strong><br>
                    • Laki-laki : <strong>{{ $maleCount }} orang</strong><br>
                    • Perempuan : <strong>{{ $femaleCount }} orang</strong>
                </div>
            </td>
            <td style="width: 40%;">
                <div class="signature-box">
                    <div>Dicetak pada {{ $generatedAt }}</div>
                    <div style="margin-top: 4px;">Petugas Administrator,</div>
                    <div class="signature-space"></div>
                    <div class="signature-name">{{ auth()->user()?->name ?? 'Administrator' }}</div>
                    <div style="font-size: 7.5pt; color: #64748b;">{{ $clinic?->name ?? 'Sistem Rumah Terapi' }}</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
