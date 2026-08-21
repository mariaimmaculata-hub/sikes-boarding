<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Detail Kunjungan Klinik -
        {{ $kunjungan->siswa?->nama ?? 'Siswa' }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #ffffff;
        }

        .container {
            width: 100%;
            padding: 30px 35px;
        }

        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 20px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }

        .header-subtitle {
            font-size: 11px;
            color: #64748b;
        }

        .section {
            margin-top: 18px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .student-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .student-table td {
            padding: 5px 4px;
            vertical-align: top;
        }

        .student-label {
            width: 150px;
            font-weight: bold;
            color: #64748b;
        }

        .student-value {
            color: #1e293b;
        }

        .info-table {
            margin-top: 10px;
        }

        .info-table td {
            width: 33.33%;
            padding: 5px;
            vertical-align: top;
        }

        .info-box {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            padding: 10px;
            min-height: 55px;
        }

        .info-label {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 11px;
            font-weight: bold;
            color: #1e293b;
        }

        .content-box {
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 12px;
            min-height: 50px;
            line-height: 1.6;
        }

        .label {
            font-size: 10px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .value {
            font-size: 11px;
            color: #334155;
            white-space: pre-line;
        }

        .medicine-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .medicine-table th {
            background: #eff6ff;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            padding: 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .medicine-table td {
            padding: 8px;
            border: 1px solid #cbd5e1;
            font-size: 10px;
        }

        .empty {
            color: #94a3b8;
            font-style: italic;
        }

        .status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
        }

        .status-selesai {
            background: #d1fae5;
            color: #047857;
        }

        .status-proses {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-menunggu {
            background: #fef3c7;
            color: #b45309;
        }

        .status-batal {
            background: #ffe4e6;
            color: #be123c;
        }

        .footer {
            margin-top: 35px;
            border-top: 1px solid #cbd5e1;
            padding-top: 10px;
            font-size: 9px;
            color: #94a3b8;
        }

        .signature {
            margin-top: 35px;
            width: 100%;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 65px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>

<div class="container">

    {{-- ======================================================
         HEADER
    ======================================================= --}}

    <div class="header">

        <div class="header-title">
            DETAIL KUNJUNGAN KLINIK
        </div>

        <div class="header-subtitle">
            Sistem Informasi Kesehatan Siswa
        </div>

    </div>


    {{-- ======================================================
         DATA SISWA
    ======================================================= --}}

    <div class="section">

        <div class="section-title">
            Data Siswa
        </div>

        <div class="student-box">

            <table class="student-table">

                <tr>

                    <td class="student-label">
                        Nama Siswa
                    </td>

                    <td class="student-value">
                        :
                        {{ $kunjungan->siswa?->nama ?? '-' }}
                    </td>

                </tr>

                <tr>

                    <td class="student-label">
                        NISN
                    </td>

                    <td class="student-value">
                        :
                        {{ $kunjungan->siswa?->nisn ?? '-' }}
                    </td>

                </tr>

                <tr>

                    <td class="student-label">
                        Kelas
                    </td>

                    <td class="student-value">
                        :
                        {{ $kunjungan->siswa?->kelas?->nama_kelas ?? '-' }}
                    </td>

                </tr>

                @if($kunjungan->siswa?->kelas?->jurusan?->nama_jurusan)

                    <tr>

                        <td class="student-label">
                            Jurusan
                        </td>

                        <td class="student-value">
                            :
                            {{ $kunjungan->siswa->kelas->jurusan->nama_jurusan }}
                        </td>

                    </tr>

                @endif

            </table>

        </div>

    </div>


    {{-- ======================================================
         INFORMASI KUNJUNGAN
    ======================================================= --}}

    <div class="section">

        <div class="section-title">
            Informasi Kunjungan
        </div>

        <table class="info-table">

            <tr>

                <td>

                    <div class="info-box">

                        <div class="info-label">
                            Tanggal Kunjungan
                        </div>

                        <div class="info-value">
                            {{ $kunjungan->tanggal_kunjungan ?? '-' }}
                        </div>

                    </div>

                </td>


                <td>

                    <div class="info-box">

                        <div class="info-label">
                            Status
                        </div>

                        <div class="info-value">

                            @php

                                $status = $kunjungan->status ?? '';

                                $statusLabel = match ($status) {

                                    'selesai' => 'Selesai',

                                    'proses',
                                    'diperiksa' => 'Dalam Pemeriksaan',

                                    'menunggu' => 'Menunggu',

                                    'batal' => 'Dibatalkan',

                                    default => $status ?: '-',

                                };

                                $statusClass = match ($status) {

                                    'selesai' => 'status-selesai',

                                    'proses',
                                    'diperiksa' => 'status-proses',

                                    'menunggu' => 'status-menunggu',

                                    'batal' => 'status-batal',

                                    default => '',

                                };

                            @endphp

                            @if($statusClass)

                                <span class="status {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                            @else

                                {{ $statusLabel }}

                            @endif

                        </div>

                    </div>

                </td>


                <td>

                    <div class="info-box">

                        <div class="info-label">
                            Pemeriksa
                        </div>

                        <div class="info-value">
                            {{ $kunjungan->pemeriksa?->name ?? '-' }}
                        </div>

                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ======================================================
         HASIL PEMERIKSAAN
    ======================================================= --}}

    <div class="section">

        <div class="section-title">
            Hasil Pemeriksaan
        </div>


        {{-- KELUHAN --}}

        <div class="content-box">

            <div class="label">
                Keluhan
            </div>

            <div class="value">
                {{ $kunjungan->keluhan ?: '-' }}
            </div>

        </div>


        {{-- PEMERIKSAAN --}}

        <div
            class="content-box"
            style="margin-top: 10px;"
        >

            <div class="label">
                Pemeriksaan
            </div>

            <div class="value">
                {{ $kunjungan->pemeriksaan ?: '-' }}
            </div>

        </div>


        {{-- DIAGNOSIS --}}

        <div
            class="content-box"
            style="margin-top: 10px;"
        >

            <div class="label">
                Diagnosis
            </div>

            <div class="value">
                {{ $kunjungan->penyakit?->nama_penyakit ?: '-' }}
            </div>

            @if($kunjungan->penyakit?->kategori)

                <div
                    style="
                        margin-top: 5px;
                        font-size: 9px;
                        color: #64748b;
                    "
                >
                    Kategori:
                    {{ $kunjungan->penyakit->kategori }}
                </div>

            @endif

        </div>


        {{-- TINDAKAN --}}

        <div
            class="content-box"
            style="margin-top: 10px;"
        >

            <div class="label">
                Tindakan
            </div>

            <div class="value">
                {{ $kunjungan->tindakan ?: '-' }}
            </div>

        </div>


        {{-- CATATAN --}}

        <div
            class="content-box"
            style="margin-top: 10px;"
        >

            <div class="label">
                Catatan
            </div>

            <div class="value">
                {{ $kunjungan->catatan ?: '-' }}
            </div>

        </div>

    </div>


    {{-- ======================================================
         OBAT
    ======================================================= --}}

    <div class="section">

        <div class="section-title">
            Obat yang Diberikan
        </div>

        @if($kunjungan->obat && $kunjungan->obat->count())

            <table class="medicine-table">

                <thead>

                    <tr>

                        <th style="width: 40px;">
                            No
                        </th>

                        <th>
                            Nama Obat
                        </th>

                        <th style="width: 100px;">
                            Jumlah
                        </th>

                        <th>
                            Keterangan
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($kunjungan->obat as $index => $obat)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ $obat->nama_obat ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                {{ $obat->jumlah ?? 0 }}
                                {{ $obat->satuan ?? '' }}
                            </td>

                            <td>
                                {{ $obat->keterangan ?? '-' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        @else

            <div class="content-box">

                <span class="empty">
                    Tidak ada obat yang diberikan.
                </span>

            </div>

        @endif

    </div>


    {{-- ======================================================
         TANDA TANGAN
    ======================================================= --}}

    <table class="signature">

        <tr>

            <td>

                Mengetahui,

                <div class="signature-space"></div>

                <strong>
                    Petugas Klinik
                </strong>

            </td>


            <td>

                Pemeriksa,

                <div class="signature-space"></div>

                <strong>
                    {{ $kunjungan->pemeriksa?->name ?? '-' }}
                </strong>

            </td>

        </tr>

    </table>


    {{-- ======================================================
         FOOTER
    ======================================================= --}}

    <div class="footer">

        Dokumen ini dibuat secara otomatis oleh Sistem Informasi
        Kesehatan Siswa.

        <br>

        Dicetak pada:
        {{ now()->format('d F Y H:i') }} WIB

    </div>

</div>

</body>
</html>