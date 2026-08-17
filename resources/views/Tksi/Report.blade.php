<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Report TKSI</title>

    <style>

        @page {
            size: A4 landscape;
            margin: 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1e293b;
            margin: 0;
        }


        /* =========================================================
           HEADER
        ========================================================== */

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10px;
            color: #64748b;
        }


        /* =========================================================
           INFO
        ========================================================== */

        .info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info td {
            border: none;
            padding: 3px 5px;
            vertical-align: top;
        }

        .info .label {
            width: 100px;
            font-weight: bold;
        }


        /* =========================================================
           TABLE
        ========================================================== */

        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report th,
        table.report td {
            border: 1px solid #94a3b8;
            padding: 6px 5px;
            vertical-align: middle;
        }

        table.report th {
            background: #f1f5f9;
            color: #334155;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        table.report td {
            font-size: 9px;
        }


        /* =========================================================
           ALIGNMENT
        ========================================================== */

        .center {
            text-align: center;
        }

        .component {
            text-align: center;
        }

        .average {
            text-align: center;
            font-weight: bold;
            color: #7e22ce;
        }


        /* =========================================================
           WIDTH
        ========================================================== */

        .col-no {
            width: 4%;
        }

        .col-siswa {
            width: 20%;
        }

        .col-kelas {
            width: 10%;
        }

        .col-component {
            width: 9%;
        }

        .col-average {
            width: 9%;
        }


        /* =========================================================
           KOMPONEN
        ========================================================== */

        .component-value {
            font-size: 10px;
            font-weight: bold;
            color: #1e293b;
        }

        .component-status {
            margin-top: 2px;
            font-size: 7px;
            color: #64748b;
        }


        /* =========================================================
           EMPTY
        ========================================================== */

        .empty {
            text-align: center;
            padding: 15px !important;
            color: #64748b;
        }


        /* =========================================================
           FOOTER
        ========================================================== */

        .footer {
            margin-top: 18px;
            text-align: right;
            font-size: 8px;
            color: #64748b;
        }


        /* =========================================================
           PAGE
        ========================================================== */

        tr {
            page-break-inside: avoid;
        }

    </style>

</head>


<body>


    {{-- =========================================================
         NORMALISASI DATA
    ========================================================== --}}

    @php

        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */

        if ($data instanceof \Illuminate\Support\Collection) {
            $rows = $data;
        } elseif (is_array($data)) {
            $rows = collect($data);
        } else {
            $rows = collect();
        }


        /*
        |--------------------------------------------------------------------------
        | Komponen
        |--------------------------------------------------------------------------
        */

        if ($komponenOptions instanceof \Illuminate\Support\Collection) {
            $komponenList = $komponenOptions->values();
        } elseif (is_array($komponenOptions ?? null)) {
            $komponenList = collect($komponenOptions)->values();
        } else {
            $komponenList = collect();
        }

    @endphp


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="header">

        <h2>
            REPORT HASIL TKSI
        </h2>

        <p>
            SIKES BOARDING
        </p>

    </div>


    {{-- =========================================================
         INFORMASI REPORT
    ========================================================== --}}

    <table class="info">

        <tr>

            <td class="label">
                Periode
            </td>

            <td>
                :
            </td>

            <td>

                @if (!empty($periode))

                    {{ $periode->nama_periode ?? 'Semua Periode' }}

                @else

                    Semua Periode

                @endif

            </td>

        </tr>


        <tr>

            <td class="label">
                Komponen
            </td>

            <td>
                :
            </td>

            <td>

                @if ($komponenList->isNotEmpty())

                    {{ $komponenList->implode(', ') }}

                @else

                    Semua Komponen

                @endif

            </td>

        </tr>


        <tr>

            <td class="label">
                Jumlah Siswa
            </td>

            <td>
                :
            </td>

            <td>
                {{ $rows->count() }} siswa
            </td>

        </tr>

    </table>


    {{-- =========================================================
         TABLE REPORT
    ========================================================== --}}

    <table class="report">

        <thead>

            <tr>

                {{-- NO --}}

                <th class="col-no">
                    No
                </th>


                {{-- SISWA --}}

                <th class="col-siswa">
                    Siswa
                </th>


                {{-- KELAS --}}

                <th class="col-kelas">
                    Kelas
                </th>


                {{-- KOMPONEN --}}

                @foreach ($komponenList as $namaKomponen)

                    <th class="col-component">

                        {{ $namaKomponen }}

                    </th>

                @endforeach


                {{-- RATA-RATA --}}

                <th class="col-average">
                    Rata-rata
                </th>

            </tr>

        </thead>


        <tbody>


            {{-- =================================================
                 DATA
            ================================================== --}}

            @forelse ($rows as $index => $item)

                @php

                    /*
                    |--------------------------------------------------------------------------
                    | Item
                    |--------------------------------------------------------------------------
                    */

                    if (is_array($item)) {

                        $siswa =
                            $item['siswa']
                            ?? null;

                        $komponen =
                            $item['komponen']
                            ?? [];

                        $rataRata =
                            $item['rata_rata']
                            ?? null;

                    } else {

                        $siswa =
                            $item->siswa
                            ?? null;

                        $komponen =
                            $item->komponen
                            ?? [];

                        $rataRata =
                            $item->rata_rata
                            ?? null;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Siswa
                    |--------------------------------------------------------------------------
                    */

                    if (is_array($siswa)) {

                        $nisn =
                            $siswa['nisn']
                            ?? '-';

                        $nama =
                            $siswa['nama']
                            ?? '-';

                        $kelas =
                            $siswa['kelas']
                            ?? null;

                    } else {

                        $nisn =
                            $siswa->nisn
                            ?? '-';

                        $nama =
                            $siswa->nama
                            ?? '-';

                        $kelas =
                            $siswa->kelas
                            ?? null;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Kelas
                    |--------------------------------------------------------------------------
                    */

                    if (is_array($kelas)) {

                        $namaKelas =
                            $kelas['nama_kelas']
                            ?? '-';

                    } else {

                        $namaKelas =
                            $kelas->nama_kelas
                            ?? '-';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Komponen
                    |--------------------------------------------------------------------------
                    */

                    if ($komponen instanceof \Illuminate\Support\Collection) {

                        $komponen = $komponen->toArray();

                    }

                @endphp


                <tr>


                    {{-- NO --}}

                    <td class="center">

                        {{ $loop->iteration }}

                    </td>


                    {{-- SISWA --}}

                    <td>

                        <strong>
                            {{ $nama }}
                        </strong>

                        <br>

                        <span style="font-size: 8px; color: #64748b;">

                            NISN:
                            {{ $nisn }}

                        </span>

                    </td>


                    {{-- KELAS --}}

                    <td class="center">

                        {{ $namaKelas }}

                    </td>


                    {{-- KOMPONEN --}}

                    @foreach ($komponenList as $namaKomponen)

                        @php

                            $componentData =
                                $komponen[$namaKomponen]
                                ?? null;


                            if (
                                $componentData instanceof \Illuminate\Support\Collection
                            ) {

                                $componentData =
                                    $componentData->toArray();

                            }


                            if (is_array($componentData)) {

                                $nilai =
                                    $componentData['nilai']
                                    ?? null;

                            } elseif (is_object($componentData)) {

                                $nilai =
                                    $componentData->nilai
                                    ?? null;

                            } else {

                                $nilai =
                                    $componentData;

                            }

                        @endphp


                        <td class="component">

                            @if (
                                $nilai !== null &&
                                $nilai !== ''
                            )

                                <div class="component-value">

                                    {{ $nilai }}

                                </div>


                                <div class="component-status">

                                    Selesai

                                </div>

                            @else

                                <div
                                    style="color:#94a3b8;"
                                >
                                    -
                                </div>


                                <div class="component-status">

                                    Belum

                                </div>

                            @endif

                        </td>

                    @endforeach


                    {{-- RATA-RATA --}}

                    <td class="average">

                        @if (
                            $rataRata !== null &&
                            $rataRata !== ''
                        )

                            {{ number_format((float) $rataRata, 2) }}

                        @else

                            -

                        @endif

                    </td>


                </tr>

            @empty


                {{-- =================================================
                     EMPTY
                ================================================== --}}

                <tr>

                    <td
                        colspan="{{ 4 + $komponenList->count() }}"
                        class="empty"
                    >

                        Belum ada data TKSI.

                    </td>

                </tr>


            @endforelse

        </tbody>

    </table>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}

    <div class="footer">

        Dicetak pada:

        {{ now()->format('d-m-Y H:i') }}

    </div>


</body>

</html>