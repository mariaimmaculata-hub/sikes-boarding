<?php

namespace App\Http\Controllers\Tksi;

use App\Http\Controllers\Controller;
use App\Exports\Tksi\TksReportExport;
use App\Models\Periode;
use App\Models\TksiHasil;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TksiReportController extends Controller
{
    /**
     * =========================================================
     * REPORT TKSI
     * =========================================================
     *
     * Konsep:
     *
     * 1 siswa = 1 baris
     * 1 siswa = 6 komponen TKSI
     *
     */
    public function index(Request $request)
    {
        $periodeId = $request->periode_id;

        $periodes = Periode::orderByDesc('id')->get();

        $komponenOptions = $this->getKomponen();

        $data = $this->getReportData($periodeId, $komponenOptions);

        $statistik = [
            'total_siswa' => $data->count(),

            'total_hasil' => TksiHasil::when(
                $periodeId,
                fn ($query) => $query->where('periode_id', $periodeId)
            )->count(),

            'jumlah_komponen' => $komponenOptions->count(),

            'siswa_lengkap' => $data
                ->filter(function ($item) use ($komponenOptions) {
                    return $item['total_komponen']
                        === $komponenOptions->count();
                })
                ->count(),
        ];

        return Inertia::render('Tksi/Report', [
            'data' => $data,

            'periodes' => $periodes,

            'komponenOptions' => $komponenOptions,

            'statistik' => $statistik,

            'filters' => [
                'periode_id' => $periodeId,
            ],
        ]);
    }


    /**
     * =========================================================
     * AMBIL NAMA 6 KOMPONEN
     * =========================================================
     */
    private function getKomponen()
    {
        return TksiHasil::query()
            ->whereNotNull('komponen')
            ->where('komponen', '!=', '')
            ->distinct()
            ->orderBy('komponen')
            ->pluck('komponen')
            ->values();
    }


    /**
     * =========================================================
     * DATA REPORT
     * =========================================================
     *
     * Hasil:
     *
     * [
     *     [
     *         siswa,
     *         periode,
     *         komponen => [
     *             komponen1 => ...,
     *             komponen2 => ...,
     *             ...
     *         ]
     *     ]
     * ]
     *
     */
    private function getReportData($periodeId, $komponenOptions)
    {
        $query = TksiHasil::with([
            'siswa.kelas',
            'periode',
        ]);

        if ($periodeId) {
            $query->where('periode_id', $periodeId);
        }

        $hasil = $query
            ->orderBy('siswa_id')
            ->orderBy('tanggal')
            ->orderBy('komponen')
            ->get();

        return $hasil
            ->groupBy('siswa_id')
            ->map(function ($items) use ($komponenOptions) {

                $first = $items->first();

                $komponen = [];

                foreach ($komponenOptions as $namaKomponen) {

                    /*
                     * Cari hasil komponen milik siswa
                     */
                    $hasilKomponen = $items
                        ->where('komponen', $namaKomponen)
                        ->sortByDesc('tanggal')
                        ->first();

                    $komponen[$namaKomponen] = $hasilKomponen
                        ? [
                            'id' => $hasilKomponen->id,

                            'nilai' => $hasilKomponen->nilai,

                            'catatan' => $hasilKomponen->catatan,

                            'tanggal' => $hasilKomponen->tanggal,
                        ]
                        : null;
                }

                /*
                 * Nilai yang tersedia
                 */
                $nilai = collect($komponen)
                    ->filter()
                    ->pluck('nilai')
                    ->map(fn ($value) => (float) $value);

                return [

                    'siswa_id' => $first->siswa_id,

                    'siswa' => $first->siswa,

                    'periode' => $first->periode,

                    'komponen' => $komponen,

                    'total_komponen' => collect($komponen)
                        ->filter()
                        ->count(),

                    'total_nilai' => $nilai->sum(),

                    'rata_rata' => $nilai->count()
                        ? round($nilai->avg(), 2)
                        : null,
                ];
            })
            ->values();
    }


    /**
     * =========================================================
     * EXPORT EXCEL
     * =========================================================
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new TksReportExport(
                $request->periode_id
            ),
            'report-tksi.xlsx'
        );
    }


    /**
     * =========================================================
     * EXPORT PDF
     * =========================================================
     */
    public function exportPdf(Request $request)
    {
        $periodeId = $request->periode_id;

        $komponenOptions = $this->getKomponen();

        $data = $this->getReportData(
            $periodeId,
            $komponenOptions
        );

        $periode = $periodeId
            ? Periode::find($periodeId)
            : null;

        $pdf = Pdf::loadView('tksi.report', [

            'data' => $data,

            'periode' => $periode,

            'komponenOptions' => $komponenOptions,

            'statistik' => [
                'total_siswa' => $data->count(),

                'jumlah_komponen' => $komponenOptions->count(),
            ],

        ])->setPaper('a4', 'landscape');

        return $pdf->download('report-tksi.pdf');
    }
}