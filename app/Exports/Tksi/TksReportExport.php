<?php

namespace App\Exports\Tksi;

use App\Models\TksiHasil;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TksReportExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles
{
    /**
     * Filter yang digunakan.
     */
    protected ?int $periodeId;
    protected Collection $komponen;
    protected ?string $search;
    protected ?string $tingkat;
    protected ?int $jurusanId;

    /**
     * Constructor.
     *
     * URUTAN PARAMETER HARUS SAMA DENGAN CONTROLLER:
     *
     * new TksReportExport(
     *     $periodeId,
     *     $komponen,
     *     $search,
     *     $tingkat,
     *     $jurusanId
     * );
     */
    public function __construct(
        $periodeId = null,
        $komponen = [],
        $search = null,
        $tingkat = null,
        $jurusanId = null
    ) {
        /*
         * ---------------------------------------------------------
         * PERIODE
         * ---------------------------------------------------------
         *
         * Pastikan Collection tidak pernah masuk ke sini.
         */
        if ($periodeId instanceof Collection) {
            $periodeId = $periodeId->first();
        }

        if (is_array($periodeId)) {
            $periodeId = $periodeId[0] ?? null;
        }

        $this->periodeId = is_numeric($periodeId)
            ? (int) $periodeId
            : null;


        /*
         * ---------------------------------------------------------
         * KOMPONEN
         * ---------------------------------------------------------
         */
        if ($komponen instanceof Collection) {

            $this->komponen = $komponen
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->unique()
                ->values();

        } elseif (is_array($komponen)) {

            $this->komponen = collect($komponen)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->unique()
                ->values();

        } elseif ($komponen !== null && $komponen !== '') {

            $this->komponen = collect([
                trim((string) $komponen)
            ])
                ->filter()
                ->values();

        } else {

            $this->komponen = collect();
        }


        /*
         * ---------------------------------------------------------
         * SEARCH
         * ---------------------------------------------------------
         */
        $this->search = $search !== null && $search !== ''
            ? trim((string) $search)
            : null;


        /*
         * ---------------------------------------------------------
         * TINGKAT
         * ---------------------------------------------------------
         */
        $this->tingkat = $tingkat !== null && $tingkat !== ''
            ? trim((string) $tingkat)
            : null;


        /*
         * ---------------------------------------------------------
         * JURUSAN
         * ---------------------------------------------------------
         *
         * Disimpan jika nanti dibutuhkan.
         *
         * Tetapi query export DI SINI tidak menggunakan
         * relasi siswa.jurusan karena model Siswa kamu
         * belum mempunyai relasi tersebut.
         */
        if ($jurusanId instanceof Collection) {
            $jurusanId = $jurusanId->first();
        }

        if (is_array($jurusanId)) {
            $jurusanId = $jurusanId[0] ?? null;
        }

        $this->jurusanId = is_numeric($jurusanId)
            ? (int) $jurusanId
            : null;
    }


    /**
     * =========================================================
     * KOMPONEN
     * =========================================================
     *
     * Jika user memilih komponen:
     * gunakan komponen tersebut.
     *
     * Jika tidak memilih:
     * ambil semua komponen yang tersedia di database.
     */
    private function getKomponen(): Collection
    {
        /*
         * Jika ada komponen dari filter.
         */
        if ($this->komponen->isNotEmpty()) {

            return $this->komponen
                ->map(fn ($item) => (string) $item)
                ->filter()
                ->unique()
                ->values();
        }


        /*
         * Jika tidak ada filter komponen,
         * ambil seluruh komponen dari database.
         */
        $query = TksiHasil::query()
            ->whereNotNull('komponen')
            ->where('komponen', '!=', '');


        /*
         * Filter periode hanya jika ada.
         */
        if ($this->periodeId !== null) {
            $query->where(
                'periode_id',
                $this->periodeId
            );
        }


        return $query
            ->distinct()
            ->orderBy('komponen')
            ->pluck('komponen')
            ->map(fn ($item) => (string) $item)
            ->values();
    }


    /**
     * =========================================================
     * COLLECTION
     * =========================================================
     */
    public function collection()
    {
        /*
         * Ambil daftar komponen.
         */
        $komponenOptions = $this->getKomponen();


        /*
         * ---------------------------------------------------------
         * QUERY HASIL TKSI
         * ---------------------------------------------------------
         *
         * HANYA menggunakan relasi yang memang tersedia:
         *
         * siswa.kelas
         * periode
         *
         * Jangan gunakan:
         * siswa.jurusan
         */
        $query = TksiHasil::with([
            'siswa.kelas',
            'periode',
        ]);


        /*
         * ---------------------------------------------------------
         * FILTER PERIODE
         * ---------------------------------------------------------
         */
        if ($this->periodeId !== null) {

            $query->where(
                'periode_id',
                $this->periodeId
            );
        }


        /*
         * ---------------------------------------------------------
         * FILTER KOMPONEN
         * ---------------------------------------------------------
         */
        if ($komponenOptions->isNotEmpty()) {

            $query->whereIn(
                'komponen',
                $komponenOptions->toArray()
            );
        }


        /*
         * ---------------------------------------------------------
         * FILTER SEARCH
         * ---------------------------------------------------------
         *
         * Cari berdasarkan:
         * - nama siswa
         * - NISN
         */
        if ($this->search !== null) {

            $search = $this->search;

            $query->whereHas('siswa', function ($q) use ($search) {

                $q->where(function ($subQuery) use ($search) {

                    $subQuery
                        ->where(
                            'nama',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'nisn',
                            'like',
                            '%' . $search . '%'
                        );

                });

            });
        }


        /*
         * ---------------------------------------------------------
         * FILTER TINGKAT
         * ---------------------------------------------------------
         *
         * Diasumsikan tingkat tersimpan di tabel kelas.
         *
         * Kalau nama kolom tingkat di tabel kelas berbeda,
         * bagian ini perlu disesuaikan.
         */
        if ($this->tingkat !== null) {

            $tingkat = $this->tingkat;

            $query->whereHas('siswa.kelas', function ($q) use ($tingkat) {

                $q->where(
                    'tingkat',
                    $tingkat
                );

            });
        }


        /*
         * ---------------------------------------------------------
         * FILTER JURUSAN
         * ---------------------------------------------------------
         *
         * UNTUK SEMENTARA TIDAK DIJALANKAN.
         *
         * Alasannya:
         * Model Siswa kamu tidak mempunyai relasi jurusan.
         *
         * Kalau nanti struktur database sudah jelas,
         * filter jurusan bisa ditambahkan melalui field FK
         * yang benar.
         */
        /*
        if ($this->jurusanId !== null) {
            ...
        }
        */


        /*
         * ---------------------------------------------------------
         * AMBIL DATA
         * ---------------------------------------------------------
         */
        $hasil = $query
            ->orderBy('siswa_id')
            ->orderBy('tanggal')
            ->orderBy('komponen')
            ->get();


        /*
         * ---------------------------------------------------------
         * KELOMPOKKAN BERDASARKAN SISWA
         * ---------------------------------------------------------
         *
         * 1 siswa = 1 baris Excel.
         */
        $grouped = $hasil->groupBy('siswa_id');


        $rows = collect();


        /*
         * =========================================================
         * LOOP SETIAP SISWA
         * =========================================================
         */
        foreach ($grouped as $items) {

            $first = $items->first();


            /*
             * Data siswa.
             */
            $siswa = $first?->siswa;


            /*
             * Jika data siswa tidak ditemukan,
             * lewati baris tersebut.
             */
            if (!$siswa) {
                continue;
            }


            /*
             * -----------------------------------------------------
             * DATA KOMPONEN
             * -----------------------------------------------------
             *
             * Untuk setiap komponen:
             * ambil nilai terbaru berdasarkan tanggal.
             */
            $komponenData = [];


            foreach ($komponenOptions as $namaKomponen) {

                $hasilKomponen = $items
                    ->where(
                        'komponen',
                        $namaKomponen
                    )
                    ->sortByDesc('tanggal')
                    ->first();


                $komponenData[$namaKomponen] =
                    $hasilKomponen
                        ? $hasilKomponen->nilai
                        : '-';
            }


            /*
             * -----------------------------------------------------
             * IDENTITAS SISWA
             * -----------------------------------------------------
             */
            $row = [

                /*
                 * NISN
                 */
                $siswa->nisn ?? '-',

                /*
                 * Nama
                 */
                $siswa->nama ?? '-',

                /*
                 * Kelas
                 */
                $siswa->kelas?->nama_kelas ?? '-',

                /*
                 * Periode
                 */
                $first->periode?->nama_periode ?? '-',

            ];


            /*
             * -----------------------------------------------------
             * TAMBAHKAN NILAI KOMPONEN
             * -----------------------------------------------------
             */
            foreach ($komponenOptions as $namaKomponen) {

                $row[] =
                    $komponenData[$namaKomponen] ?? '-';
            }


            /*
             * -----------------------------------------------------
             * HITUNG TOTAL & RATA-RATA
             * -----------------------------------------------------
             *
             * Hanya nilai numerik yang dihitung.
             */
            $nilai = collect($komponenData)
                ->filter(function ($value) {

                    return $value !== '-'
                        && $value !== null
                        && $value !== ''
                        && is_numeric($value);

                })
                ->map(function ($value) {

                    return (float) $value;

                });


            /*
             * Total nilai.
             */
            $row[] = $nilai->sum();


            /*
             * Rata-rata.
             */
            $row[] = $nilai->count()
                ? round($nilai->avg(), 2)
                : '-';


            /*
             * Masukkan baris ke hasil Excel.
             */
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
        /*
         * Harus sama persis dengan kolom pada collection().
         */
        $komponenOptions = $this->getKomponen();


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

            /*
             * Header.
             */
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],

        ];
    }
}