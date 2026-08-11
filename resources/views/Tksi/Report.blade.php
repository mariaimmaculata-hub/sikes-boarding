<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Report TKSI</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #222;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 4px 0;
            font-size: 11px;
        }

        .filter {
            margin-bottom: 15px;
        }

        .filter table {
            width: auto;
            border: none;
        }

        .filter td {
            border: none;
            padding: 2px 8px 2px 0;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th,
        table.report td {
            border: 1px solid #333;
            padding: 6px;
        }

        table.report th {
            text-align: center;
            font-weight: bold;
        }

        table.report td.center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 10px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>REPORT HASIL TKSI</h2>
        <p>SIKES BOARDING</p>
    </div>

    <div class="filter">
        <table>
            <tr>
                <td><strong>Periode</strong></td>
                <td>:</td>
                <td>{{ $periode->nama_periode ?? 'Semua Periode' }}</td>
            </tr>

            <tr>
                <td><strong>Kategori</strong></td>
                <td>:</td>
                <td>{{ $kategori ?? 'Semua Kategori' }}</td>
            </tr>

            <tr>
                <td><strong>Jumlah Data</strong></td>
                <td>:</td>
                <td>{{ $data->count() }} siswa</td>
            </tr>
        </table>
    </div>

    <table class="report">
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jurusan</th>
                <th>Periode</th>
                <th>Tanggal Tes</th>
                <th>Kategori</th>
                <th>Hasil</th>
                <th>Catatan</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($data as $index => $item)
                <tr>
                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item->siswa->nisn ?? '-' }}
                    </td>

                    <td>
                        {{ $item->siswa->nama ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item->siswa->kelas->nama_kelas ?? '-' }}
                    </td>

                    <td>
                        {{ $item->siswa->jurusan->nama_jurusan ?? '-' }}
                    </td>

                    <td>
                        {{ $item->periode->nama_periode ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item->tanggal_tes
                            ? \Carbon\Carbon::parse($item->tanggal_tes)->format('d-m-Y')
                            : '-' }}
                    </td>

                    <td>
                        {{ $item->kategori ?? '-' }}
                    </td>

                    <td>
                        {{ $item->hasil ?? '-' }}
                    </td>

                    <td>
                        {{ $item->catatan ?? '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="center">
                        Belum ada data TKSI.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada:
        {{ now()->format('d-m-Y H:i') }}
    </div>

</body>

</html>