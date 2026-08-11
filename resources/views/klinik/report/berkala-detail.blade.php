<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>
        Detail Pemeriksaan Berkala
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

        .grid td {
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
            Detail Pemeriksaan
            {{ $pemeriksaan->jenis_pemeriksaan === 'berkala_1'
                ? 'Berkala 1'
                : 'Berkala 2'
            }}
        </h1>

        <div class="muted">
            {{ $pemeriksaan->periode?->nama_periode ?? '-' }}
        </div>

    </div>


    <table class="info">

        <tr>
            <td class="label">
                Nama Siswa
            </td>

            <td>
                {{ $pemeriksaan->siswa?->nama ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                NISN
            </td>

            <td>
                {{ $pemeriksaan->siswa?->nisn ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Kelas
            </td>

            <td>
                {{ $pemeriksaan->siswa?->kelas?->nama_kelas ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Jurusan
            </td>

            <td>
                {{ $pemeriksaan->siswa?->kelas?->jurusan?->nama_jurusan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Tanggal
            </td>

            <td>
                {{ $pemeriksaan->tanggal_pemeriksaan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Status
            </td>

            <td>
                {{ ucfirst($pemeriksaan->status ?? '-') }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Pemeriksa
            </td>

            <td>
                {{ $pemeriksaan->pemeriksa?->name ?? '-' }}
            </td>
        </tr>

    </table>


    <div class="section">
        Antropometri
    </div>

    <table class="grid">

        <tr>
            <td class="label">
                Berat Badan
            </td>

            <td>
                {{ $pemeriksaan->berat_badan ?? '-' }} kg
            </td>

            <td class="label">
                Tinggi Badan
            </td>

            <td>
                {{ $pemeriksaan->tinggi_badan ?? '-' }} cm
            </td>

            <td class="label">
                IMT
            </td>

            <td>
                {{ $pemeriksaan->imt ?? '-' }}
            </td>
        </tr>

    </table>


    <div class="section">
        Tanda Vital
    </div>

    <table class="grid">

        <tr>

            <td class="label">
                Tekanan Darah
            </td>

            <td>
                {{ $pemeriksaan->tekanan_darah ?? '-' }}
            </td>

            <td class="label">
                Denyut Nadi
            </td>

            <td>
                {{ $pemeriksaan->denyut_nadi ?? '-' }} bpm
            </td>

            <td class="label">
                Suhu Tubuh
            </td>

            <td>
                {{ $pemeriksaan->suhu_tubuh ?? '-' }} °C
            </td>

        </tr>

    </table>


    <div class="section">
        Pemeriksaan Fisik
    </div>

    <table class="grid">

        <tr>
            <td class="label">Mata</td>
            <td>{{ $pemeriksaan->mata ?? '-' }}</td>

            <td class="label">Telinga</td>
            <td>{{ $pemeriksaan->telinga ?? '-' }}</td>
        </tr>

        <tr>
            <td class="label">Gigi & Mulut</td>
            <td>{{ $pemeriksaan->gigi_mulut ?? '-' }}</td>

            <td class="label">Kondisi Umum</td>
            <td>{{ $pemeriksaan->kondisi_umum ?? '-' }}</td>
        </tr>
    </table>


    <div class="section">
        Hasil Pemeriksaan
    </div>

    <table class="grid">

        <tr>
            <td class="label">
                Keluhan
            </td>

            <td>
                {{ $pemeriksaan->keluhan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Hasil Pemeriksaan
            </td>

            <td>
                {{ $pemeriksaan->hasil_pemeriksaan ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Rekomendasi
            </td>

            <td>
                {{ $pemeriksaan->rekomendasi ?? '-' }}
            </td>
        </tr>

        <tr>
            <td class="label">
                Catatan
            </td>

            <td>
                {{ $pemeriksaan->catatan ?? '-' }}
            </td>
        </tr>

    </table>

</body>
</html>