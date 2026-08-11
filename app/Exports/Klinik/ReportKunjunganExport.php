<?php

namespace App\Exports\Klinik;

use App\Models\Periode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportKunjunganExport implements
    FromCollection,
    WithHeadings,
    WithTitle
{
    protected $periode;
    protected $kelasId;

    public function __construct(
        Periode $periode,
        $kelasId = null
    ) {
        $this->periode = $periode;
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $query = $this->periode->siswa()
            ->with([
                'kelas.jurusan',
                'kunjunganKlinik' => function ($query) {
                    $query->where(
                        'periode_id',
                        $this->periode->id
                    )
                    ->with([
                        'pemeriksa',
                        'kunjunganObat.obat',
                    ])
                    ->orderByDesc('tanggal_kunjungan');
                },
            ]);

        if ($this->kelasId) {
            $query->where(
                'kelas_id',
                $this->kelasId
            );
        }

        return $query
            ->orderBy('nama')
            ->get()
            ->flatMap(function ($siswa) {

                if ($siswa->kunjunganKlinik->isEmpty()) {
                    return [[
                        $siswa->nama,
                        $siswa->nisn ?? '-',
                        $siswa->kelas?->nama_kelas ?? '-',
                        $siswa->kelas?->jurusan?->nama_jurusan ?? '-',
                        '-',
                        'Belum ada kunjungan',
                        '-',
                        '-',
                        '-',
                        '-',
                    ]];
                }

                return $siswa->kunjunganKlinik->map(
                    function ($kunjungan) use ($siswa) {

                        $obat = $kunjungan->kunjunganObat
                            ->map(function ($item) {
                                return $item->obat?->nama_obat
                                    . ' (' . $item->jumlah . ')';
                            })
                            ->implode(', ');

                        return [
                            $siswa->nama,
                            $siswa->nisn ?? '-',
                            $siswa->kelas?->nama_kelas ?? '-',
                            $siswa->kelas?->jurusan?->nama_jurusan ?? '-',

                            optional(
                                $kunjungan->tanggal_kunjungan
                            )->format('d-m-Y H:i'),

                            $kunjungan->keluhan ?? '-',
                            $kunjungan->diagnosis ?? '-',
                            $kunjungan->tindakan ?? '-',

                            ucfirst(
                                $kunjungan->status ?? '-'
                            ),

                            $kunjungan->pemeriksa?->name ?? '-',

                            $obat ?: '-',
                        ];
                    }
                );
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Jurusan',
            'Tanggal Kunjungan',
            'Keluhan',
            'Diagnosis',
            'Tindakan',
            'Status',
            'Pemeriksa',
            'Obat',
        ];
    }

    public function title(): string
    {
        return 'Rekap Kunjungan Klinik';
    }
}