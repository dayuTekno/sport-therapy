<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Sakit - {{ $patient->full_name }}</title>
    <style>
        @page {
            margin: 0;
            size: a4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #1e293b;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            position: relative;
        }

        /* DEKORASI SUDUT KANAN ATAS */
        .corner-top-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 320px;
            height: 190px;
            background-color: #f1f3f5;
            border-bottom-left-radius: 95px;
            z-index: 1;
        }

        /* DEKORASI SUDUT KIRI BAWAH */
        .corner-bottom-left-bg {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 280px;
            height: 110px;
            background-color: #f1f3f5;
            border-top-right-radius: 90px;
            z-index: 1;
        }

        .corner-bottom-left-green {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 190px;
            height: 75px;
            background-color: #00aa55;
            border-top-right-radius: 65px;
            z-index: 2;
        }

        /* KONTEN UTAMA DOKUMEN */
        .page-content {
            position: relative;
            z-index: 10;
            padding: 42px 50px 40px 50px;
        }

        /* HEADER KOP */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .title-main {
            font-size: 21pt;
            font-weight: 800;
            line-height: 1.15;
            color: #0f172a;
            border-bottom: 2.5px solid #0f172a;
            display: inline-block;
            padding-bottom: 3px;
            margin-bottom: 6px;
        }

        .title-number {
            font-size: 10pt;
            letter-spacing: 0.5px;
            color: #334155;
            margin-top: 6px;
        }

        /* KOP KLINIK DI KANAN ATAS */
        .clinic-kop-box {
            text-align: right;
            padding-right: 10px;
            padding-top: 5px;
        }

        .kop-inner-table {
            border-collapse: collapse;
            float: right;
        }

        .kop-inner-table td {
            padding: 0 4px;
            vertical-align: middle;
        }

        .clinic-name-heading {
            font-size: 12.5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            line-height: 1.15;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .clinic-address-text {
            font-size: 7.8pt;
            color: #334155;
            line-height: 1.35;
            margin: 3px 0 0 0;
        }

        .clinic-logo-img {
            max-width: 55px;
            max-height: 55px;
            object-fit: contain;
            margin-right: 8px;
        }

        /* BODY SURAT */
        .intro-text {
            font-weight: bold;
            font-size: 10pt;
            color: #0f172a;
            margin-bottom: 12px;
        }

        /* TABEL IDENTITAS PASIEN */
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .patient-table td {
            padding: 4px 0;
            vertical-align: bottom;
            font-size: 9.5pt;
        }

        .patient-table td.label-col {
            width: 90px;
            color: #334155;
            font-size: 9.5pt;
        }

        .patient-table td.colon-col {
            width: 12px;
            color: #334155;
        }

        .patient-table td.value-col {
            border-bottom: 1px solid #cbd5e1;
            font-size: 9.5pt;
            color: #0f172a;
            padding-left: 4px;
            padding-bottom: 3px;
        }

        .patient-name-val {
            font-size: 10.5pt;
            font-weight: 600;
        }

        /* DIAGNOSA PASIEN */
        .diag-intro {
            font-size: 9.5pt;
            color: #1e293b;
            margin-bottom: 10px;
            line-height: 1.45;
        }

        .diag-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }

        .diag-grid-table td {
            width: 50%;
            padding: 2.5px 12px 2.5px 0;
            vertical-align: top;
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
        }

        /* REKOMENDASI KONTROL */
        .control-intro {
            font-size: 9.5pt;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.45;
        }

        .control-table {
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .control-table td {
            padding: 4px 8px 4px 0;
            vertical-align: middle;
            font-size: 9.5pt;
        }

        .control-val {
            border-bottom: 1px solid #94a3b8;
            font-weight: 600;
            color: #0f172a;
            padding: 0 16px 2px 8px;
        }

        .closing-text {
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.45;
            margin-bottom: 25px;
        }

        /* TANDA TANGAN & PENGESAHAN */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .signature-section td {
            padding: 0;
            vertical-align: top;
        }

        .signature-box {
            float: right;
            text-align: left;
            width: 250px;
            font-size: 9.5pt;
            color: #0f172a;
        }

        .sign-date-place {
            font-size: 9.5pt;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .sign-title-role {
            font-size: 9.5pt;
            color: #334155;
            margin-bottom: 4px;
        }

        /* WADAH CAP & TANDA TANGAN (TUMPANG TINDIH OTENTIK) */
        .sign-stamp-stack {
            position: relative;
            height: 85px;
            margin: 4px 0;
        }

        .stamp-image {
            position: absolute;
            left: -15px;
            top: 2px;
            width: 120px;
            height: 80px;
            object-fit: contain;
            opacity: 0.88;
            z-index: 1;
        }

        .signature-image {
            position: absolute;
            left: 20px;
            top: 10px;
            width: 150px;
            height: 70px;
            object-fit: contain;
            z-index: 2;
        }

        /* FALLBACK CAP JIKA BELUM ADA FILE */
        .stamp-fallback {
            position: absolute;
            left: -10px;
            top: 10px;
            border: 2px solid #2e1065;
            color: #4338ca;
            padding: 6px 10px;
            font-size: 8.5pt;
            font-weight: 900;
            text-transform: uppercase;
            border-radius: 6px;
            letter-spacing: 0.5px;
            transform: rotate(-10deg);
            opacity: 0.85;
            z-index: 1;
            line-height: 1.15;
            text-align: center;
        }

        /* FALLBACK TTD JIKA BELUM ADA FILE */
        .signature-fallback {
            position: absolute;
            left: 30px;
            top: 18px;
            font-family: 'Brush Script MT', 'Times New Roman', cursive, serif;
            font-size: 24pt;
            color: #0f172a;
            z-index: 2;
            transform: rotate(-4deg);
        }

        .sign-name-text {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
    </style>
</head>
<body>

    {{-- DEKORASI SUDUT (PERSIS SEPERTI DI FOTO) --}}
    <div class="corner-top-right"></div>
    <div class="corner-bottom-left-bg"></div>
    <div class="corner-bottom-left-green"></div>

    <div class="page-content">

        {{-- HEADER SURAT & KOP KLINIK --}}
        <table class="header-table">
            <tr>
                {{-- KIRI: JUDUL BESAR SURAT KETERANGAN SAKIT --}}
                <td style="width: 50%;">
                    <div class="title-main">
                        Surat<br>
                        Keterangan Sakit
                    </div>
                    <div class="title-number">
                        No. {{ $letterNumber }}
                    </div>
                </td>

                {{-- KANAN: LOGO & KOP KLINIK DALAM KOTAK MELENGKUNG --}}
                <td style="width: 50%;" class="clinic-kop-box">
                    <table class="kop-inner-table">
                        <tr>
                            @if(!empty($logoBase64))
                                <td style="vertical-align: middle;">
                                    <img src="{{ $logoBase64 }}" class="clinic-logo-img" alt="Logo">
                                </td>
                            @endif
                            <td style="text-align: left;">
                                <div class="clinic-name-heading">{{ $clinic->name }}</div>
                                <div class="clinic-address-text">
                                    {{ $clinic->address ?: 'Perum BDI Jl. Kenanga Blok.M-25 RT/RW 17/04 Tambak - Indramayu' }}<br>
                                    TELP. {{ $clinic->phone_number ?: '0896-5477-3992' }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- INTRO BODY --}}
        <div class="intro-text">
            Yang bertanda tangan di bawah ini menerangkan bahwa:
        </div>

        {{-- TABEL IDENTITAS PASIEN BERGARIS BAWAH --}}
        <table class="patient-table">
            <tr>
                <td class="label-col">Nama</td>
                <td class="colon-col">:</td>
                <td class="value-col patient-name-val">{{ $patient->full_name }}</td>
            </tr>
            <tr>
                <td class="label-col">Umur</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient->age ? $patient->age . ' Tahun' : '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Alamat</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient->address ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Pekerjaan</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient->occupation ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Telp</td>
                <td class="colon-col">:</td>
                <td class="value-col">{{ $patient->phone_number ?: '-' }}</td>
            </tr>
        </table>

        {{-- DIAGNOSA HASIL PEMERIKSAAN & TERAPI --}}
        <div class="diag-intro">
            Berdasarkan hasil pemeriksaan dan terapi yang telah dilakukan, pasien didiagnosa mengalami sebagai berikut:
        </div>

        <table class="diag-grid-table">
            @php
                // Pecah item diagnosa menjadi array untuk render 2 kolom seperti di foto
                $diagnosesList = [];
                if (!empty($diagnosisInput)) {
                    $diagnosesList = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $diagnosisInput)));
                } elseif (!empty($session->evaluation_notes)) {
                    $diagnosesList = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $session->evaluation_notes)));
                } else {
                    $diagnosesList = [
                        'Lumbar muscle strain',
                        'Lateral thigh muscle fatigue',
                        'Strain otot paha lateral',
                        'Strain hamstring'
                    ];
                }
                $chunked = array_chunk($diagnosesList, 2);
            @endphp

            @foreach($chunked as $row)
                <tr>
                    <td>- {{ $row[0] ?? '' }}</td>
                    <td>@if(isset($row[1])) - {{ $row[1] }} @endif</td>
                </tr>
            @endforeach
        </table>

        {{-- ANJURAN KONTROL ULANG --}}
        <div class="control-intro">
            Sehubung dengan kondisi tersebut, pasien disarankan untuk melakukan kontrol ulang perkembangan masa recovery atau pemulihan pada :
        </div>

        <table class="control-table">
            <tr>
                <td style="font-weight: bold; width: 65px;">Tanggal</td>
                <td class="control-val">{{ $controlDate }}</td>
                <td style="font-weight: bold; width: 50px; text-align: center;">JAM</td>
                <td class="control-val">{{ $controlTime }}</td>
            </tr>
        </table>

        {{-- KALIMAT PENUTUP --}}
        <div class="closing-text">
            Demikian surat keterangan sakit ini diberikan untuk digunakan sebagai mana mestinya.
        </div>

        {{-- AREA TANDA TANGAN & STEMPEL --}}
        <table class="signature-section">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;">
                    <div class="signature-box">
                        <div class="sign-date-place">
                            {{ $city }}, {{ $letterDate }}
                        </div>
                        <div class="sign-title-role">
                            {{ $signerTitle }}
                        </div>

                        {{-- WADAH OVERLAPPING CAP DAN TTD --}}
                        <div class="sign-stamp-stack">
                            {{-- Cap / Stempel --}}
                            @if(!empty($stampBase64))
                                <img src="{{ $stampBase64 }}" class="stamp-image" alt="Cap Stempel">
                            @else
                                <div class="stamp-fallback">
                                    {{ Str::limit($clinic->name, 15) }}<br>
                                    <span style="font-size: 7pt;">TERAPIS OLAHRAGA</span>
                                </div>
                            @endif

                            {{-- Tanda Tangan --}}
                            @if(!empty($signatureBase64))
                                <img src="{{ $signatureBase64 }}" class="signature-image" alt="Tanda Tangan">
                            @else
                                <div class="signature-fallback">
                                    {{ Str::limit($signerName, 12) }}
                                </div>
                            @endif
                        </div>

                        {{-- Nama Pemeriksa Tertanda --}}
                        <div class="sign-name-text">
                            {{ $signerName }}
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>
