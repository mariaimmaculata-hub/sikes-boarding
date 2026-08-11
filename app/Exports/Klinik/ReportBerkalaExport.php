<?php

namespace App\Exports\Klinik;

use App\Models\Periode;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportBerkalaExport implements
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
                'pemeriksaanBerkala' => function ($query) {
                    $query->where('periode_id', $this->periode->id)
                        ->whereIn(
                            'jenis_pemeriksaan',
                            ['berkala_1', 'berkala_2']
                        );
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
            ->map(function ($siswa) {

                $b1 = $siswa->pemeriksaanBerkala
                    ->where(
                        'jenis_pemeriksaan',
                        'berkala_1'
                    )
                    ->sortByDesc('id')
                    ->first();

                $b2 = $siswa->pemeriksaanBerkala
                    ->where(
                        'jenis_pemeriksaan',
                        'berkala_2'
                    )
                    ->sortByDesc('id')
                    ->first();

                if (
                    $b1?->status === 'selesai' &&
                    $b2?->status === 'selesai'
                ) {
                    $status = 'Lengkap';
                } elseif ($b1 || $b2) {
                    $status = 'Belum Lengkap';
                } else {
                    $status = 'Belum Diperiksa';
                }

                return [
                    $siswa->nama,
                    $siswa->nisn,
                    $siswa->kelas?->nama_kelas ?? '-',
                    $siswa->kelas?->jurusan?->nama_jurusan ?? '-',

                    $b1?->status ?? 'belum',
                    $b1?->kondisi_umum ?? '-',

                    $b2?->status ?? 'belum',
                    $b2?->kondisi_umum ?? '-',

                    $status,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'Kelas',
            'Jurusan',

            'Berkala 1 - Status',
            'Berkala 1 - Kondisi',

            'Berkala 2 - Status',
            'Berkala 2 - Kondisi',

            'Status Keseluruhan',
        ];
    }

    public function title(): string
    {
        return 'Rekap Berkala';
    }
}