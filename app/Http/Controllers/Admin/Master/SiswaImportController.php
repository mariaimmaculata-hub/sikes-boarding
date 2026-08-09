<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Smalot\PdfParser\Parser;

class SiswaImportController extends Controller
{
    /**
     * =========================================================
     * HALAMAN IMPORT
     * =========================================================
     */
    public function create()
    {
        $kelas = Kelas::with('jurusan')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        $jurusan = Jurusan::orderBy('nama_jurusan')
            ->get();

        return inertia('Admin/MasterData/Siswa/Import', [
            'kelas' => $kelas,
            'jurusan' => $jurusan,
        ]);
    }

    /**
     * =========================================================
     * PROSES IMPORT
     * =========================================================
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'file' => [
                    'required',
                    'file',
                    'max:10240',
                    'mimes:csv,txt,xlsx,xls,pdf',
                ],
            ],
            [
                'file.required' => 'File harus dipilih.',
                'file.file' => 'File tidak valid.',
                'file.max' => 'Ukuran file maksimal 10 MB.',
                'file.mimes' => 'Format file harus CSV, XLS, XLSX, atau PDF.',
            ]
        );

        $file = $request->file('file');

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        try {
            if ($extension === 'pdf') {
                return $this->importPdf($file);
            }

            return $this->importExcel($file);

        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Gagal membaca file: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * =========================================================
     * IMPORT CSV / XLS / XLSX
     * =========================================================
     */
 private function importExcel($file)
{
    $rows = Excel::toArray([], $file);

    if (empty($rows) || empty($rows[0])) {
        return back()->withErrors([
            'file' => 'File tidak memiliki data.'
        ]);
    }

    $data = $rows[0];

    if (count($data) < 2) {
        return back()->withErrors([
            'file' => 'File hanya memiliki header dan tidak memiliki data siswa.'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALISASI HEADER
    |--------------------------------------------------------------------------
    */

    $headers = array_map(function ($header) {

        $header = trim((string) $header);

        // Hapus BOM
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header);

        $header = strtolower($header);

        $header = str_replace(
            [' ', '-', '.'],
            '_',
            $header
        );

        return $header;

    }, $data[0]);

    unset($data[0]);

    $data = array_values($data);

    $inserted = 0;
    $skipped = 0;
    $errors = [];

    DB::beginTransaction();

    try {

        foreach ($data as $index => $row) {

            $rowNumber = $index + 2;

            /*
            |--------------------------------------------------------------------------
            | LEWATI BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                empty(
                    array_filter(
                        $row,
                        fn ($value) =>
                            $value !== null &&
                            trim((string) $value) !== ''
                    )
                )
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | MAPPING
            |--------------------------------------------------------------------------
            */

            $item = $this->mapExcelRow(
                $headers,
                $row
            );

            $nisn = trim(
                (string) ($item['nisn'] ?? '')
            );

            $nama = trim(
                (string) ($item['nama'] ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | NISN & NAMA
            |--------------------------------------------------------------------------
            */

            if ($nisn === '' || $nama === '') {

                $errors[] =
                    "Baris {$rowNumber}: NISN dan Nama wajib diisi.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT
            |--------------------------------------------------------------------------
            */

            if (
                Siswa::where('nisn', $nisn)->exists()
            ) {

                $errors[] =
                    "Baris {$rowNumber}: NISN {$nisn} sudah terdaftar.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | JENIS KELAMIN
            |--------------------------------------------------------------------------
            */

            $jenisKelamin = $this->normalizeGender(
                $item['jenis_kelamin'] ?? ''
            );

            if (!$jenisKelamin) {

                $errors[] =
                    "Baris {$rowNumber}: jenis kelamin '" .
                    ($item['jenis_kelamin'] ?? '') .
                    "' tidak valid. Gunakan L/P.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | KELAS
            |--------------------------------------------------------------------------
            */

            $kelasInput = trim(
                (string) ($item['kelas'] ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | JURUSAN
            |--------------------------------------------------------------------------
            */

            $jurusanInput = trim(
                (string) ($item['jurusan'] ?? '')
            );

            if ($kelasInput === '') {

                $errors[] =
                    "Baris {$rowNumber}: kelas kosong.";

                $skipped++;

                continue;
            }

            if ($jurusanInput === '') {

                $errors[] =
                    "Baris {$rowNumber}: jurusan kosong.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CARI JURUSAN
            |--------------------------------------------------------------------------
            */

            $jurusan = Jurusan::whereRaw(
                'LOWER(TRIM(nama_jurusan)) = ?',
                [
                    strtolower(trim($jurusanInput))
                ]
            )->first();

            if (!$jurusan) {

                $errors[] =
                    "Baris {$rowNumber}: jurusan '{$jurusanInput}' tidak ditemukan di database.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI TINGKAT
            |--------------------------------------------------------------------------
            */

            $tingkat = $this->normalizeTingkat(
                $kelasInput
            );

            /*
            |--------------------------------------------------------------------------
            | CARI KELAS
            |--------------------------------------------------------------------------
            */

            $kelas = $this->findKelas(
                $tingkat,
                $jurusan->id
            );

            if (!$kelas) {

                $errors[] =
                    "Baris {$rowNumber}: kelas '{$kelasInput}' " .
                    "untuk jurusan '{$jurusanInput}' tidak ditemukan. " .
                    "Tingkat yang dicari: {$tingkat}.";

                $skipped++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | ANGKATAN
            |--------------------------------------------------------------------------
            */

            $angkatan = $item['angkatan'] ?? null;

            if (
                $angkatan !== null &&
                $angkatan !== ''
            ) {
                $angkatan = (int) $angkatan;
            } else {
                $angkatan = null;
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN
            |--------------------------------------------------------------------------
            */

            Siswa::create([

                'nisn' => $nisn,

                'nama' => $nama,

                'tempat_lahir' =>
                    $this->nullableValue(
                        $item['tempat_lahir'] ?? null
                    ),

                'tanggal_lahir' =>
                    $this->normalizeDate(
                        $item['tanggal_lahir'] ?? null
                    ),

                'jenis_kelamin' =>
                    $jenisKelamin,

                'kelas_id' =>
                    $kelas->id,

                'angkatan' =>
                    $angkatan,

                'alamat' =>
                    $this->nullableValue(
                        $item['alamat'] ?? null
                    ),

                'no_hp' =>
                    $this->nullableValue(
                        $item['no_hp'] ?? null
                    ),

                'nama_orang_tua' =>
                    $this->nullableValue(
                        $item['nama_orang_tua'] ?? null
                    ),

                'no_hp_orang_tua' =>
                    $this->nullableValue(
                        $item['no_hp_orang_tua'] ?? null
                    ),

                'status' =>
                    $this->normalizeStatus(
                        $item['status'] ?? 'aktif'
                    ),
            ]);

            $inserted++;
        }

        /*
        |--------------------------------------------------------------------------
        | JANGAN COMMIT JIKA TIDAK ADA DATA BERHASIL
        |--------------------------------------------------------------------------
        */

        if ($inserted === 0) {

            DB::rollBack();

            return back()->withErrors([
                'file' =>
                    "Tidak ada data yang berhasil diimport. " .
                    "{$skipped} data dilewati."
            ])->with(
                'import_errors',
                $errors
            );
        }

        DB::commit();

    } catch (\Throwable $e) {

        DB::rollBack();

        return back()->withErrors([
            'file' =>
                'Import gagal: ' .
                $e->getMessage()
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | HASIL
    |--------------------------------------------------------------------------
    */

    $message =
        "Import selesai. {$inserted} siswa berhasil ditambahkan.";

    if ($skipped > 0) {

        $message .=
            " {$skipped} data dilewati.";
    }

    return redirect()
        ->route('admin.master.siswa.index')
        ->with('success', $message)
        ->with(
            'import_errors',
            $errors
        );
}

    /**
     * =========================================================
     * MAPPING ROW
     * =========================================================
     */
    private function mapExcelRow(
    array $headers,
    array $row
): array {

    $data = [];

    foreach ($headers as $index => $header) {

        $value = $row[$index] ?? null;

        $data[$header] =
            is_string($value)
                ? trim($value)
                : $value;
    }

    return [

        'nisn' =>
            $data['nisn']
            ?? null,

        'nama' =>
            $data['nama']
            ?? $data['nama_lengkap']
            ?? null,

        'tempat_lahir' =>
            $data['tempat_lahir']
            ?? null,

        'tanggal_lahir' =>
            $data['tanggal_lahir']
            ?? null,

        'jenis_kelamin' =>
            $data['jenis_kelamin']
            ?? $data['jk']
            ?? null,

        'kelas' =>
            $data['kelas']
            ?? $data['tingkat']
            ?? null,

        'jurusan' =>
            $data['jurusan']
            ?? null,

        'angkatan' =>
            $data['angkatan']
            ?? null,

        'alamat' =>
            $data['alamat']
            ?? null,

        'no_hp' =>
            $data['no_hp']
            ?? null,

        'nama_orang_tua' =>
            $data['nama_orang_tua']
            ?? $data['orang_tua']
            ?? null,

        'no_hp_orang_tua' =>
            $data['no_hp_orang_tua']
            ?? null,

        'status' =>
            $data['status']
            ?? 'aktif',
    ];
}

    /**
     * =========================================================
     * NORMALISASI JENIS KELAMIN
     * =========================================================
     */
    private function normalizeGender($gender): ?string
    {
        $gender = strtoupper(
            trim((string) $gender)
        );

        return match ($gender) {

            'L',
            'LAKI',
            'LAKI-LAKI',
            'LAKI LAKI',
            'PRIA',
            'MALE' => 'L',

            'P',
            'PEREMPUAN',
            'WANITA',
            'FEMALE' => 'P',

            default => null,
        };
    }

    /**
     * =========================================================
     * NORMALISASI TINGKAT
     *
     * 10  -> X
     * 11  -> XI
     * 12  -> XII
     *
     * X   -> X
     * XI  -> XI
     * XII -> XII
     * =========================================================
     */
    private function normalizeTingkat($tingkat): string
{
    $tingkat = strtoupper(
        trim((string) $tingkat)
    );

    return match ($tingkat) {
        'X', '10' => '10',
        'XI', '11' => '11',
        'XII', '12' => '12',
        default => $tingkat,
    };
}

    /**
     * =========================================================
     * CARI KELAS
     * =========================================================
     */
    private function findKelas(
    string $tingkat,
    $jurusanId
): ?Kelas {

    $tingkat = $this->normalizeTingkat($tingkat);

    return Kelas::where('jurusan_id', $jurusanId)
        ->where(function ($query) use ($tingkat) {
            $query->where('tingkat', $tingkat)
                ->orWhere(
                    'tingkat',
                    match ($tingkat) {
                        '10' => 10,
                        '11' => 11,
                        '12' => 12,
                        default => $tingkat,
                    }
                );
        })
        ->first();
}

    /**
     * =========================================================
     * NORMALISASI STATUS
     * =========================================================
     */
    private function normalizeStatus($status): string
    {
        $status = strtolower(
            trim((string) $status)
        );

        return match ($status) {

            'aktif',
            'active' => 'aktif',

            'nonaktif',
            'non aktif',
            'inactive' => 'nonaktif',

            'lulus',
            'graduated' => 'lulus',

            default => 'aktif',
        };
    }

    /**
     * =========================================================
     * NULLABLE VALUE
     * =========================================================
     */
    private function nullableValue($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    /**
     * =========================================================
     * NORMALISASI TANGGAL
     * =========================================================
     */
    private function normalizeDate($value)
    {
        if (
            $value === null ||
            $value === ''
        ) {
            return null;
        }

        /**
         * Excel serial date.
         */
        if (is_numeric($value)) {

            try {

                return \PhpOffice\PhpSpreadsheet\Shared\Date
                    ::excelToDateTimeObject($value)
                    ->format('Y-m-d');

            } catch (\Throwable $e) {

                return null;
            }
        }

        /**
         * Format tanggal biasa.
         */
        $timestamp = strtotime(
            (string) $value
        );

        if ($timestamp === false) {
            return null;
        }

        return date(
            'Y-m-d',
            $timestamp
        );
    }

    /**
     * =========================================================
     * IMPORT PDF
     * =========================================================
     */
    private function importPdf($file)
    {
        $parser = new Parser();

        $pdf = $parser->parseFile(
            $file->getRealPath()
        );

        $text = $pdf->getText();

        if (empty(trim($text))) {
            return back()->withErrors([
                'file' =>
                    'PDF tidak memiliki teks yang dapat dibaca.',
            ]);
        }

        $lines = preg_split(
            "/\r\n|\r|\n/",
            $text
        );

        $inserted = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {

            foreach ($lines as $index => $line) {

                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                /**
                 * Pisahkan kolom PDF.
                 */
                $columns = preg_split(
                    '/\s*\|\s*/',
                    $line
                );

                if (count($columns) < 5) {
                    continue;
                }

                $nisn =
                    trim((string) $columns[0]);

                $nama =
                    trim((string) $columns[1]);

                $jk =
                    trim((string) $columns[2]);

                $kelasInput =
                    trim((string) $columns[3]);

                $jurusanInput =
                    trim((string) $columns[4]);

                $angkatan =
                    trim(
                        (string) (
                            $columns[5] ?? ''
                        )
                    );

                /**
                 * Lewati header.
                 */
                if (
                    strtolower($nisn) === 'nisn'
                ) {
                    continue;
                }

                /**
                 * NISN dan nama wajib.
                 */
                if (
                    $nisn === '' ||
                    $nama === ''
                ) {

                    $errors[] =
                        "Baris " . ($index + 1) .
                        ": NISN dan Nama wajib diisi.";

                    $skipped++;

                    continue;
                }

                /**
                 * Jenis kelamin.
                 */
                $jenisKelamin =
                    $this->normalizeGender($jk);

                if (!$jenisKelamin) {

                    $errors[] =
                        "Baris " . ($index + 1) .
                        ": jenis kelamin '{$jk}' tidak valid.";

                    $skipped++;

                    continue;
                }

                /**
                 * Duplikat NISN.
                 */
                if (
                    Siswa::where(
                        'nisn',
                        $nisn
                    )->exists()
                ) {

                    $errors[] =
                        "Baris " . ($index + 1) .
                        ": NISN {$nisn} sudah terdaftar.";

                    $skipped++;

                    continue;
                }

                /**
                 * Cari jurusan.
                 *
                 * HANYA nama_jurusan.
                 */
                $jurusan =
                    Jurusan::whereRaw(
                        'LOWER(TRIM(nama_jurusan)) = ?',
                        [
                            strtolower(
                                trim($jurusanInput)
                            )
                        ]
                    )->first();

                if (!$jurusan) {

                    $errors[] =
                        "Baris " . ($index + 1) .
                        ": jurusan '{$jurusanInput}' tidak ditemukan di database.";

                    $skipped++;

                    continue;
                }

                /**
                 * Cari tingkat.
                 */
                $tingkat =
                    $this->normalizeTingkat(
                        $kelasInput
                    );

                /**
                 * Cari kelas.
                 */
                $kelas =
                    $this->findKelas(
                        $tingkat,
                        $jurusan->id
                    );

                if (!$kelas) {

                    $errors[] =
                        "Baris " . ($index + 1) .
                        ": kelas '{$kelasInput}' dengan jurusan '{$jurusanInput}' tidak ditemukan di database.";

                    $skipped++;

                    continue;
                }

                /**
                 * Simpan.
                 */
                Siswa::create([

                    'nisn' =>
                        $nisn,

                    'nama' =>
                        $nama,

                    'jenis_kelamin' =>
                        $jenisKelamin,

                    'kelas_id' =>
                        $kelas->id,

                    'angkatan' =>
                        $angkatan !== ''
                            ? (int) $angkatan
                            : null,

                    'status' =>
                        'aktif',
                ]);

                $inserted++;
            }

            DB::commit();

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withErrors([
                'file' =>
                    'Import PDF gagal: ' .
                    $e->getMessage(),
            ]);
        }

        return redirect()
            ->route(
                'admin.master.siswa.index'
            )
            ->with(
                'success',
                "Import PDF selesai. {$inserted} siswa berhasil ditambahkan. {$skipped} data dilewati."
            )
            ->with(
                'import_errors',
                $errors
            );
    }
}