<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Riwayat Pemeriksaan Berkala
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1e293b;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 10px;
            color: #64748b;
        }

        .student {
            margin-bottom: 18px;
        }

        .student table {
            width: 100%;
            border-collapse: collapse;
        }

        .student td {
            padding: 5px 7px;
        }

        .student .label {
            width: 110px;
            font-weight: bold;
            color: #475569;
        }

        .history {
            width: 100%;
            border-collapse: collapse;
        }

        .history th {
            background: #1e3a8a;
            color: white;
            padding: 7px;
            border: 1px solid #cbd5e1;
            text-align: center;
        }

        .history td {
            padding: 7px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .status {
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #64748b;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #64748b;
            text-align: right;
        }

    </style>

</head>

<body>

    <div class="header">

        <h1>
            RIWAYAT PEMERIKSAAN BERKALA SISWA
        </h1>

        <p>
            Sistem Informasi Kesehatan Siswa Boarding
        </p>

    </div>


    <div class="student">

        <table>

            <tr>

                <td class="label">
                    NISN
                </td>

                <td>
                    :
                    {{ $kunjungan->siswa?->nisn ?? '-' }}
                </td>

                <td class="label">
                    Nama
                </td>

                <td>
                    :
                    {{ $kunjungan->siswa?->nama ?? '-' }}
                </td>

            </tr>

            <tr>

                <td class="label">
                    Kelas
                </td>

                <td>
                    :
                    {{ $kunjungan->siswa?->kelas?->nama_kelas ?? '-' }}
                </td>

                <td class="label">
                    Jurusan
                </td>

                <td>
                    :
                    {{ $kunjungan->siswa?->kelas?->jurusan?->nama_jurusan ?? '-' }}
                </td>

            </tr>

        </table>

    </div>


    <table class="history">

        <thead>

            <tr>

                <th style="width: 35px;">
                    No
                </th>

                <th>
                    Jenis Pemeriksaan
                </th>

                <th style="width: 90px;">
                    Tanggal
                </th>

                <th style="width: 75px;">
                    Status
                </th>

                <th>
                    Hasil
                </th>

                <th>
                    Catatan
                </th>

                <th>
                    Pemeriksa
                </th>

                <th>
                    Periode
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse(
                $pemeriksaanBerkala
                as $index => $item
            )

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>

                        @if(
                            $item->jenis_pemeriksaan === 'berkala_1'
                        )

                            Pemeriksaan Berkala 1

                        @elseif(
                            $item->jenis_pemeriksaan === 'berkala_2'
                        )

                            Pemeriksaan Berkala 2

                        @else

                            {{ $item->jenis_pemeriksaan }}

                        @endif

                    </td>

                    <td class="center">

                        {{ $item->tanggal_pemeriksaan
                            ? $item->tanggal_pemeriksaan->format('d/m/Y')
                            : '-' }}

                    </td>

                    <td class="center status">

                        {{ ucfirst($item->status ?? '-') }}

                    </td>

                    <td>
                        {{ $item->hasil ?? '-' }}
                    </td>

                    <td>
                        {{ $item->catatan ?? '-' }}
                    </td>

                    <td>

                        {{ $item->pemeriksa?->name ?? '-' }}

                    </td>

                    <td>

                        {{ $item->periode?->nama_periode ?? '-' }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="empty"
                    >

                        Belum terdapat riwayat
                        pemeriksaan berkala.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        Dicetak pada:
        {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>