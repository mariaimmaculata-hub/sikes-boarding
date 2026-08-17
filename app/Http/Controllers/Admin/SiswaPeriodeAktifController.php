<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PemeriksaanBerkala;
use App\Models\Periode;
use App\Models\TksiHasil;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiswaPeriodeAktifController extends Controller
{
    /**
     * ============================================================
     * DAFTAR SISWA PADA PERIODE AKTIF
     * ============================================================
     */
    public function index()
    {
        $periode = Periode::where('status', 'aktif')
            ->with([
                'siswa.kelas.jurusan',
            ])
            ->first();

        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA PERIODE AKTIF
        |--------------------------------------------------------------------------
        */

        if (!$periode) {
            return Inertia::render('Admin/Periode/SiswaAktif', [
                'periode' => null,

                'siswa' => [],

                'statistik' => [
                    'total' => 0,
                    'berkala_1' => 0,
                    'berkala_2' => 0,
                    'tksi' => 0,
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DATA SISWA
        |--------------------------------------------------------------------------
        */

        $siswa = $periode->siswa->map(function ($item) use ($periode) {

            /*
            |--------------------------------------------------------------------------
            | BERKALA 1
            |--------------------------------------------------------------------------
            */

            $berkala1 = PemeriksaanBerkala::where(
                'periode_id',
                $periode->id
            )
                ->where('siswa_id', $item->id)
                ->whereIn('jenis_pemeriksaan', [
                    'B1',
                    'b1',
                    'berkala_1',
                    'Berkala 1',
                    'Pemeriksaan Berkala 1',
                ])
                ->latest('tanggal_pemeriksaan')
                ->first();


            /*
            |--------------------------------------------------------------------------
            | BERKALA 2
            |--------------------------------------------------------------------------
            */

            $berkala2 = PemeriksaanBerkala::where(
                'periode_id',
                $periode->id
            )
                ->where('siswa_id', $item->id)
                ->whereIn('jenis_pemeriksaan', [
                    'B2',
                    'b2',
                    'berkala_2',
                    'Berkala 2',
                    'Pemeriksaan Berkala 2',
                ])
                ->latest('tanggal_pemeriksaan')
                ->first();


            /*
            |--------------------------------------------------------------------------
            | TKSI
            |
            | SUMBER DATA:
            | tksi_hasil
            |
            | Siswa dianggap sudah melakukan TKSI apabila
            | memiliki minimal 1 hasil TKSI pada periode aktif.
            |--------------------------------------------------------------------------
            */

            $tksi = TksiHasil::where(
                'periode_id',
                $periode->id
            )
                ->where('siswa_id', $item->id)
                ->exists();


            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            */

            $statusBerkala1 = $berkala1
                ? 'selesai'
                : 'belum';

            $statusBerkala2 = $berkala2
                ? 'selesai'
                : 'belum';

            $statusTksi = $tksi
                ? 'selesai'
                : 'belum';


            /*
            |--------------------------------------------------------------------------
            | STATUS KESELURUHAN
            |--------------------------------------------------------------------------
            */

            $lengkap =
                $statusBerkala1 === 'selesai' &&
                $statusBerkala2 === 'selesai' &&
                $statusTksi === 'selesai';


            /*
            |--------------------------------------------------------------------------
            | DATA UNTUK VUE
            |--------------------------------------------------------------------------
            */

            return [
                'id' => $item->id,

                'nisn' => $item->nisn,

                'nama' => $item->nama,

                'kelas' => $item->kelas,

                'jurusan' => $item->kelas?->jurusan,

                'berkala_1' => [
                    'status' => $statusBerkala1,
                ],

                'berkala_2' => [
                    'status' => $statusBerkala2,
                ],

                'tksi' => [
                    'status' => $statusTksi,
                ],

                'status' => $lengkap
                    ? 'lengkap'
                    : 'belum',
            ];
        })->values();


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Admin/Periode/SiswaAktif', [

            'periode' => [
                'id' => $periode->id,

                'nama_periode' =>
                    $periode->nama_periode,

                'tanggal_mulai' =>
                    $periode->tanggal_mulai,

                'tanggal_selesai' =>
                    $periode->tanggal_selesai,

                'status' =>
                    $periode->status,
            ],

            'siswa' =>
                $siswa,

            'statistik' => [

                'total' =>
                    $siswa->count(),

                'berkala_1' =>
                    $siswa
                        ->where(
                            'berkala_1.status',
                            'selesai'
                        )
                        ->count(),

                'berkala_2' =>
                    $siswa
                        ->where(
                            'berkala_2.status',
                            'selesai'
                        )
                        ->count(),

                'tksi' =>
                    $siswa
                        ->where(
                            'tksi.status',
                            'selesai'
                        )
                        ->count(),
            ],
        ]);
    }


    /**
     * ============================================================
     * DETAIL SISWA
     * ============================================================
     */
    public function show($siswaId)
    {
        $periode = Periode::where('status', 'aktif')
            ->with([
                'siswa.kelas.jurusan',
            ])
            ->first();


        /*
        |--------------------------------------------------------------------------
        | CEK PERIODE
        |--------------------------------------------------------------------------
        */

        if (!$periode) {
            abort(
                404,
                'Tidak ada periode aktif.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | CARI SISWA
        |--------------------------------------------------------------------------
        */

        $siswa = $periode->siswa
            ->firstWhere('id', $siswaId);


        if (!$siswa) {
            abort(
                404,
                'Siswa tidak ditemukan pada periode aktif.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BERKALA 1
        |--------------------------------------------------------------------------
        */

        $berkala1 = PemeriksaanBerkala::where(
            'periode_id',
            $periode->id
        )
            ->where('siswa_id', $siswa->id)
            ->whereIn('jenis_pemeriksaan', [
                'B1',
                'b1',
                'berkala_1',
                'Berkala 1',
                'Pemeriksaan Berkala 1',
            ])
            ->latest('tanggal_pemeriksaan')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | BERKALA 2
        |--------------------------------------------------------------------------
        */

        $berkala2 = PemeriksaanBerkala::where(
            'periode_id',
            $periode->id
        )
            ->where('siswa_id', $siswa->id)
            ->whereIn('jenis_pemeriksaan', [
                'B2',
                'b2',
                'berkala_2',
                'Berkala 2',
                'Pemeriksaan Berkala 2',
            ])
            ->latest('tanggal_pemeriksaan')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | TKSI
        |
        | LANGSUNG DARI tksi_hasil
        |--------------------------------------------------------------------------
        */

        $tksiHasil = TksiHasil::where(
            'periode_id',
            $periode->id
        )
            ->where('siswa_id', $siswa->id)
            ->get();


        $tksiSelesai =
            $tksiHasil->isNotEmpty();


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        $statusBerkala1 = $berkala1
            ? 'selesai'
            : 'belum';

        $statusBerkala2 = $berkala2
            ? 'selesai'
            : 'belum';

        $statusTksi = $tksiSelesai
            ? 'selesai'
            : 'belum';


        $lengkap =
            $statusBerkala1 === 'selesai' &&
            $statusBerkala2 === 'selesai' &&
            $statusTksi === 'selesai';


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'Admin/Periode/ShowSiswaAktif',
            [

                'periode' => [

                    'id' =>
                        $periode->id,

                    'nama_periode' =>
                        $periode->nama_periode,

                    'tanggal_mulai' =>
                        $periode->tanggal_mulai,

                    'tanggal_selesai' =>
                        $periode->tanggal_selesai,

                    'status' =>
                        $periode->status,
                ],


                'siswa' => [

                    'id' =>
                        $siswa->id,

                    'nisn' =>
                        $siswa->nisn,

                    'nama' =>
                        $siswa->nama,

                    'kelas' =>
                        $siswa->kelas,

                    'tempat_lahir' =>
                        $siswa->tempat_lahir,

                    'tanggal_lahir' =>
                        $siswa->tanggal_lahir,

                    'jenis_kelamin' =>
                        $siswa->jenis_kelamin,


                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 1
                    |--------------------------------------------------------------------------
                    */

                    'berkala_1' => [

                        'status' =>
                            $statusBerkala1,

                        'data' =>
                            $berkala1,
                    ],


                    /*
                    |--------------------------------------------------------------------------
                    | BERKALA 2
                    |--------------------------------------------------------------------------
                    */

                    'berkala_2' => [

                        'status' =>
                            $statusBerkala2,

                        'data' =>
                            $berkala2,
                    ],


                    /*
                    |--------------------------------------------------------------------------
                    | TKSI
                    |--------------------------------------------------------------------------
                    */

                    'tksi' => [

                        'status' =>
                            $statusTksi,

                        'data' =>
                            $tksiHasil,
                    ],


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS KESELURUHAN
                    |--------------------------------------------------------------------------
                    */

                    'status' =>
                        $lengkap
                            ? 'lengkap'
                            : 'belum',
                ],
            ]
        );
    }
}