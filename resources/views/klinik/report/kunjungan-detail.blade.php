<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>
        Detail Kunjungan Klinik
    </title>

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #334155;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 14px;
            margin-top: 22px;
            margin-bottom: 8px;
        }

        .muted {
            color: #64748b;
        }

        .header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .info {
            width: 100%;
            margin-bottom: 20px;
        }

        .info td {
            padding: 4px 0;
        }

        .label {
            width: 130px;
            color: #64748b;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
        }

        .grid td,
        .grid th {
            border: 1px solid #e2e8f0;
            padding: 9px;
            vertical-align: top;
        }

        .grid .label {
            background: #f8fafc;
            font-weight: bold;
        }

        .section {
            margin-top: 18px;
            font-weight: bold;
            font-size: 13px;
        }

    </style>

</head>

<body>

    <div class="header">

        <h1>
            Detail Kunjungan Klinik
        </h1>

        <div class="muted">
            {{ $kunjungan->periode?->nama_periode ?? '-' }}
        </div>

    </div>


    <table class="info">

        <tr>
            <td class="label">
                Nama Siswa
            </td>

            <td>
                {{ $kunjungan->siswa?->nama ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                NISN
            </td>

            <td>
                {{ $kunjungan->siswa?->nisn ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Kelas
            </td>

            <td>
                {{ $kunjungan->siswa?->kelas?->nama_kelas ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Jurusan
            </td>

            <td>
                {{ $kunjungan->siswa?->kelas?->jurusan?->nama_jurusan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Tanggal Kunjungan
            </td>

            <td>
                {{ $kunjungan->tanggal_kunjungan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Status
            </td>

            <td>
                {{ ucfirst($kunjungan->status ?? '-') }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Pemeriksa
            </td>

            <td>
                {{ $kunjungan->pemeriksa?->name ?? '-' }}
            </td>
        </tr>

    </table>


    <div class="section">
        Hasil Kunjungan
    </div>

    <table class="grid">

        <tr>

            <td class="label">
                Keluhan
            </td>

            <td>
                {{ $kunjungan->keluhan ?? '-' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Pemeriksaan
            </td>

            <td>
                {{ $kunjungan->pemeriksaan ?? '-' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Diagnosis
            </td>

            <td>
                {{ $kunjungan->diagnosis ?? '-' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Tindakan
            </td>

            <td>
                {{ $kunjungan->tindakan ?? '-' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Catatan
            </td>

            <td>
                {{ $kunjungan->catatan ?? '-' }}
            </td>

        </tr>

    </table>


    @if($kunjungan->kunjunganObat->count())

        <div class="section">
            Obat yang Diberikan
        </div>

        <table class="grid">

            <thead>

                <tr>
                    <th>Nama Obat</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Keterangan</th>
                </tr>

            </thead>

            <tbody>

                @foreach($kunjungan->kunjunganObat as $item)

                    <tr>

                        <td>
                            {{ $item->obat?->nama_obat ?? '-' }}
                        </td>

                        <td>
                            {{ $item->jumlah }}
                        </td>

                        <td>
                            {{ $item->obat?->satuan ?? '-' }}
                        </td>

                        <td>
                            {{ $item->keterangan ?? '-' }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    @endif

</body>
</html>