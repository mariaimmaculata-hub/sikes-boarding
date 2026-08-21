<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Detail Kunjungan - {{ $kunjungan->siswa?->nama }}
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 40px;
            color: #1e293b;
            background: white;
        }

        .header {
            border-bottom: 2px solid #1e293b;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
        }

        .subtitle {
            margin-top: 5px;
            color: #64748b;
            font-size: 13px;
        }

        .section {
            margin-top: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 7px;
        }

        .content {
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .student-table td {
            padding: 8px 0;
            vertical-align: top;
        }

        .label {
            width: 160px;
            color: #64748b;
            font-size: 13px;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
        }

        .medicine {
            border: 1px solid #e2e8f0;
            padding: 12px;
            margin-bottom: 8px;
            border-radius: 6px;
        }

        .medicine-name {
            font-weight: bold;
        }

        .medicine-info {
            margin-top: 5px;
            font-size: 13px;
            color: #475569;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
            font-size: 11px;
            color: #94a3b8;
        }

        @media print {

            body {
                padding: 20px;
            }

            .no-print {
                display: none !important;
            }

        }

    </style>

</head>

<body>

    <div class="header">

        <div class="title">
            DETAIL KUNJUNGAN KLINIK
        </div>

        <div class="subtitle">
            Riwayat pemeriksaan kesehatan siswa
        </div>

    </div>


    <table class="student-table">

        <tr>
            <td class="label">
                Nama Siswa
            </td>

            <td class="value">
                {{ $kunjungan->siswa?->nama ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                NISN
            </td>

            <td class="value">
                {{ $kunjungan->siswa?->nisn ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Kelas
            </td>

            <td class="value">
                {{ $kunjungan->siswa?->kelas?->nama_kelas ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Jurusan
            </td>

            <td class="value">
                {{ $kunjungan->siswa?->kelas?->jurusan?->nama_jurusan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Tanggal Kunjungan
            </td>

            <td class="value">
                {{ $kunjungan->tanggal_kunjungan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Pemeriksa
            </td>

            <td class="value">
                {{ $kunjungan->pemeriksa?->name ?? '-' }}
            </td>
        </tr>

    </table>


    <div class="section">

        <div class="section-title">
            Keluhan
        </div>

        <div class="content">
            {{ $kunjungan->keluhan ?: '-' }}
        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Pemeriksaan
        </div>

        <div class="content">
            {{ $kunjungan->pemeriksaan ?: '-' }}
        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Diagnosis
        </div>

        <div class="content">
            {{ $kunjungan->penyakit?->nama_penyakit ?: '-' }}
        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Tindakan
        </div>

        <div class="content">
            {{ $kunjungan->tindakan ?: '-' }}
        </div>

    </div>


    <div class="section">

        <div class="section-title">
            Obat yang Diberikan
        </div>


        @forelse($kunjungan->obat ?? [] as $obat)

            <div class="medicine">

                <div class="medicine-name">
                    {{ $obat->nama_obat ?? '-' }}
                </div>

                <div class="medicine-info">

                    Jumlah:
                    {{ $obat->jumlah ?? 0 }}
                    {{ $obat->satuan ?? '' }}

                </div>

                @if($obat->keterangan)

                    <div class="medicine-info">

                        Keterangan:
                        {{ $obat->keterangan }}

                    </div>

                @endif

            </div>

        @empty

            <div class="content">
                Tidak ada obat yang diberikan.
            </div>

        @endforelse

    </div>


    <div class="section">

        <div class="section-title">
            Catatan
        </div>

        <div class="content">
            {{ $kunjungan->catatan ?: '-' }}
        </div>

    </div>


    <div class="footer">

        Dokumen detail kunjungan klinik siswa.

    </div>


    <script>

        window.onload = function () {
            window.print();
        };

    </script>

</body>

</html>