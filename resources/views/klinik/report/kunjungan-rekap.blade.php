<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .periode {
            color: #64748b;
            margin-bottom: 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f1f5f9;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

    </style>

</head>

<body>

    <h1>
        Rekap Kunjungan Klinik
    </h1>

    <div class="periode">
        Periode:
        {{ $periode->nama_periode }}
    </div>


    <table>

        <thead>

            <tr>

                <th>No</th>

                <th>Nama Siswa</th>

                <th>NISN</th>

                <th>Kelas</th>

                <th>Tanggal</th>

                <th>Keluhan</th>

                <th>Diagnosis</th>

                <th>Tindakan</th>

                <th>Status</th>

                <th>Pemeriksa</th>

                <th>Obat</th>

            </tr>

        </thead>

        <tbody>

            @foreach($data as $index => $item)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $item['nama'] }}
                    </td>

                    <td>
                        {{ $item['nisn'] }}
                    </td>

                    <td>
                        {{ $item['kelas'] }}
                    </td>

                    <td>
                        {{ $item['tanggal'] }}
                    </td>

                    <td>
                        {{ $item['keluhan'] }}
                    </td>

                    <td>
                        {{ $item['diagnosis'] }}
                    </td>

                    <td>
                        {{ $item['tindakan'] }}
                    </td>

                    <td>
                        {{ $item['status'] }}
                    </td>

                    <td>
                        {{ $item['pemeriksa'] }}
                    </td>

                    <td>
                        {{ $item['obat'] }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>