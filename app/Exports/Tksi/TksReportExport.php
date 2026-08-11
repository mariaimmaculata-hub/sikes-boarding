<?php

namespace App\Exports\Tksi;

use App\Models\TksiHasil;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TksReportExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    protected $periodeId;

    public function __construct($periodeId = null)
    {
        $this->periodeId = $periodeId;
    }


    /**
     * =========================================================
     * COLLECTION
     * =========================================================
     */
    public function collection()
    {
        /*
         * Ambil seluruh nama komponen
         */
        $komponenOptions = TksiHasil::query()
            ->whereNotNull('komponen')
            ->where('komponen', '!=', '')
            ->distinct()
            ->orderBy('komponen')
            ->pluck('komponen')
            ->values();


        /*
         * Query hasil TKSI
         */
        $query = TksiHasil::with([
            'siswa.kelas',
            'periode',
        ]);

        if ($this->periodeId) {
            $query->where(
                'periode_id',
                $this->periodeId
            );
        }


        $hasil = $query
            ->orderBy('siswa_id')
            ->orderBy('tanggal')
            ->orderBy('komponen')
            ->get();


        /*
         * Kelompokkan berdasarkan siswa
         */
        $grouped = $hasil->groupBy('siswa_id');

        $rows = collect();


        /*
         * 1 siswa = 1 baris
         */
        foreach ($grouped as $items) {

            $first = $items->first();

            $siswa = $first->siswa;


            /*
             * Data komponen
             */
            $komponenData = [];


            foreach ($komponenOptions as $namaKomponen) {

                $hasilKomponen = $items
                    ->where('komponen', $namaKomponen)
                    ->sortByDesc('tanggal')
                    ->first();


                $komponenData[$namaKomponen] =
                    $hasilKomponen
                        ? $hasilKomponen->nilai
                        : '-';
            }


            /*
             * Buat row
             */
            $row = [

                $siswa?->nisn ?? '-',

                $siswa?->nama ?? '-',

                $siswa?->kelas?->nama_kelas ?? '-',

                $first->periode?->nama_periode ?? '-',
            ];


            /*
             * Tambahkan 6 komponen
             */
            foreach ($komponenOptions as $namaKomponen) {

                $row[] =
                    $komponenData[$namaKomponen] ?? '-';
            }


            /*
             * Tambahkan total dan rata-rata
             */
            $nilai = collect($komponenData)
                ->filter(fn ($value) =>
                    $value !== '-' &&
                    $value !== null &&
                    $value !== ''
                )
                ->map(fn ($value) =>
                    (float) $value
                );


            $row[] = $nilai->sum();

            $row[] = $nilai->count()
                ? round($nilai->avg(), 2)
                : '-';


            $rows->push($row);
        }


        return $rows;
    }


    /**
     * =========================================================
     * HEADINGS
     * =========================================================
     */
    public function headings(): array
    {
        $komponenOptions = TksiHasil::query()
            ->whereNotNull('komponen')
            ->where('komponen', '!=', '')
            ->distinct()
            ->orderBy('komponen')
            ->pluck('komponen')
            ->values();


        return array_merge(

            [
                'NISN',
                'Nama Siswa',
                'Kelas',
                'Periode',
            ],

            $komponenOptions->toArray(),

            [
                'Total Nilai',
                'Rata-rata',
            ]

        );
    }


    /**
     * =========================================================
     * STYLES
     * =========================================================
     */
    public function styles(Worksheet $sheet)
    {
        return [

            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],

        ];
    }
}