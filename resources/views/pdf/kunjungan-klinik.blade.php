<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Print Kunjungan Klinik</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header h2 {
            margin: 5px 0 0;
            font-size: 15px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 11px;
        }

        .line {
            border-bottom: 2px solid #222;
            margin-top: 15px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table.info td {
            padding: 5px 3px;
            vertical-align: top;
        }

        table.info td:first-child {
            width: 170px;
            font-weight: bold;
        }

        table.detail {
            border: 1px solid #333;
        }

        table.detail th,
        table.detail td {
            border: 1px solid #333;
            padding: 7px;
            vertical-align: top;
        }

        table.detail th {
            width: 180px;
            text-align: left;
            background: #f2f2f2;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
        }

        .signature {
            width: 250px;
            margin-left: auto;
            text-align: center;
        }

        .signature-space {
            height: 70px;
        }

        .print-info {
            margin-top: 25px;
            font-size: 10px;
            color: #666;
            text-align: right;
        }

        @media print {
            body {
                margin: 20px;
            }
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <h1>SIKES BOARDING</h1>
        <h2>REKAM KUNJUNGAN KLINIK</h2>
        <p>SMK Negeri Jawa Tengah</p>

        <div class="line"></div>
    </div>


    {{-- DATA SISWA --}}
    <div class="section-title">
        DATA SISWA
    </div>

    <table class="info">
        <tr>
            <td>NISN</td>
            <td>: {{ $kunjungan->siswa->nisn ?? '-' }}</td>
        </tr>

        <tr>
            <td>Nama Siswa</td>
            <td>: {{ $kunjungan->siswa->nama ?? '-' }}</td>
        </tr>

        <tr>
            <td>Kelas</td>
            <td>: {{ $kunjungan->siswa->kelas->nama_kelas ?? '-' }}</td>
        </tr>

        <tr>
            <td>Jurusan</td>
            <td>: {{ $kunjungan->siswa->jurusan->nama_jurusan ?? '-' }}</td>
        </tr>

        <tr>
            <td>Periode</td>
            <td>: {{ $kunjungan->periode->nama_periode ?? '-' }}</td>
        </tr>
    </table>


    {{-- DATA KUNJUNGAN --}}
    <div class="section-title">
        DATA KUNJUNGAN
    </div>

    <table class="detail">

        <tr>
            <th>Tanggal Kunjungan</th>
            <td>
                {{ $kunjungan->tanggal_kunjungan
                    ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d-m-Y')
                    : '-'
                }}
            </td>
        </tr>

        <tr>
            <th>Keluhan</th>
            <td>
                {{ $kunjungan->keluhan ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Pemeriksaan</th>
            <td>
                {{ $kunjungan->pemeriksaan ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Penyakit</th>
            <td>
                {{ $kunjungan->penyakit->nama_penyakit ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Diagnosis</th>
            <td>
                {{ $kunjungan->diagnosis ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Tindakan</th>
            <td>
                {{ $kunjungan->tindakan ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Status</th>
            <td>
                {{ $kunjungan->status ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Catatan</th>
            <td>
                {{ $kunjungan->catatan ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Pemeriksa</th>
            <td>
                {{ $kunjungan->pemeriksa->name ?? '-' }}
            </td>
        </tr>

    </table>


    {{-- OBAT --}}
    <div class="section-title">
        OBAT YANG DIBERIKAN
    </div>

    <table class="detail">

        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">
                    No
                </th>

                <th>
                    Nama Obat
                </th>

                <th style="width: 120px;">
                    Jumlah
                </th>
            </tr>
        </thead>

        <tbody>

            @forelse ($kunjungan->kunjunganObat as $index => $item)

                <tr>
                    <td style="text-align: center;">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item->obat->nama_obat ?? '-' }}
                    </td>

                    <td>
                        {{ $item->jumlah ?? '-' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="3" style="text-align: center;">
                        Tidak ada obat
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- TANDA TANGAN --}}
    <div class="footer">

        <div class="signature">

            <div>
                Semarang,
                {{ $kunjungan->tanggal_kunjungan
                    ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d-m-Y')
                    : now()->format('d-m-Y')
                }}
            </div>

            <div>
                Petugas Klinik
            </div>

            <div class="signature-space"></div>

            <strong>
                {{ $kunjungan->pemeriksa->name ?? '-' }}
            </strong>

        </div>

    </div>


    <div class="print-info">
        Dicetak pada {{ now()->format('d-m-Y H:i') }}
    </div>

</body>
</html>