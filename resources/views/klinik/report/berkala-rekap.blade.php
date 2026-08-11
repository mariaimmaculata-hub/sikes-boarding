<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">

    <style>

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
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
            padding: 6px;
        }

        .center {
            text-align: center;
        }

    </style>

</head>

<body>

    <h1>
        Rekap Pemeriksaan Berkala
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

                <th>B1 Status</th>

                <th>B1 Kondisi</th>

                <th>B2 Status</th>

                <th>B2 Kondisi</th>

                <th>Status Keseluruhan</th>

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
                        {{ $item['nisn'] ?? '-' }}
                    </td>

                    <td>
                        {{ $item['kelas'] }}
                    </td>

                    <td>
                        {{ ucfirst($item['b1_status']) }}
                    </td>

                    <td>
                        {{ $item['b1_kondisi'] }}
                    </td>

                    <td>
                        {{ ucfirst($item['b2_status']) }}
                    </td>

                    <td>
                        {{ $item['b2_kondisi'] }}
                    </td>

                    <td>
                        {{ $item['status'] }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>