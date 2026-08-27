<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;

// ADMIN
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\Master\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\Master\SiswaImportController;
use App\Http\Controllers\Admin\Master\UserController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\SiswaPeriodeAktifController;
use App\Http\Controllers\Admin\KunjunganController as AdminKunjunganController;

// KLINIK
use App\Http\Controllers\Klinik\DashboardController as KlinikDashboardController;
use App\Http\Controllers\Klinik\SiswaController as KlinikSiswaController;
use App\Http\Controllers\Klinik\PemeriksaanBerkalaController;
use App\Http\Controllers\Klinik\ReportController;
use App\Http\Controllers\Klinik\KunjunganController as KlinikKunjunganController;
use App\Http\Controllers\Klinik\ObatController as KlinikObatController;
use App\Http\Controllers\Klinik\PenyakitController as KlinikPenyakitController;

// TKSI
use App\Http\Controllers\Tksi\DashboardController as TksiDashboardController;
use App\Http\Controllers\Tksi\TksiController as TksiTksiController;
use App\Http\Controllers\Tksi\TksiReportController;

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', [WelcomeController::class, 'index'])
    ->name('welcome');


/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
|
| Setelah login, user diarahkan sesuai role:
| admin  -> admin.dashboard
| klinik -> klinik.dashboard
| tksi   -> tksi.dashboard
|
*/

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'klinik' => redirect()->route('klinik.dashboard'),
        'tksi' => redirect()->route('tksi.dashboard'),
        default => abort(403, 'Role tidak dikenali.'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Notifikasi
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/notifikasi', [
    NotificationController::class,
    'index'
])->name('notifikasi.index');

Route::get('/notifikasi/{id}', [
    NotificationController::class,
    'show'
])->name('notifikasi.show');

    Route::patch('/notifikasi/{notification}/read', [
        NotificationController::class,
        'markRead'
    ])->name('notifikasi.read');

    Route::patch('/notifikasi/read-all', [
        NotificationController::class,
        'markAllRead'
    ])->name('notifikasi.read-all');


// HAPUS BANYAK
Route::delete(
    '/notifikasi',
    [NotificationController::class, 'destroyMultiple']
)->name('notifikasi.destroy-multiple');

});


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            AdminDashboardController::class,
            'index'
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Master Data
        |--------------------------------------------------------------------------
        */

        Route::prefix('master')
            ->name('master.')
            ->group(function () {

                /*
                |--------------------------------------------------------------------------
                | Data Siswa
                |--------------------------------------------------------------------------
                */

                Route::patch(
                    '/siswa/bulk-status',
                    [AdminSiswaController::class, 'bulkStatus']
                )->name('siswa.bulk-status');

                Route::patch(
                    '/siswa/bulk-naik-kelas',
                    [AdminSiswaController::class, 'bulkNaikKelas']
                )->name('siswa.bulk-naik-kelas');

                Route::resource(
                    'siswa',
                    AdminSiswaController::class
                );

                // Import
                Route::get(
                    '/siswa-import',
                    [SiswaImportController::class, 'create']
                )->name('siswa.import');

                Route::post(
                    '/siswa-import',
                    [SiswaImportController::class, 'store']
                )->name('siswa.import.store');

                Route::resource(
                    'user',
                    UserController::class
                )->except([
                    'show',
                ]);

            });


        /*
        |--------------------------------------------------------------------------
        | PERIODE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/periode/siswa-aktif',
            [
                SiswaPeriodeAktifController::class,
                'index'
            ]
        )->name('periode.siswa-aktif');

        Route::get(
            '/periode/siswa-aktif/{siswaId}',
            [
                SiswaPeriodeAktifController::class,
                'show'
            ]
        )->name('periode.siswa-aktif.show');

        Route::get(
            '/periode/report',
            [
                PeriodeController::class,
                'report'
            ]
        )->name('periode.report');

        Route::get(
            '/periode/report/{periode}',
            [
                PeriodeController::class,
                'showReport'
            ]
        )->name('periode.report.show');

        Route::post(
            '/periode/deactivate-active-and-create',
            [
                PeriodeController::class,
                'deactivateActiveAndCreate'
            ]
        )->name('periode.deactivate-active-and-create');

        Route::resource(
            'periode',
            PeriodeController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Berkala
        |--------------------------------------------------------------------------
        */

        Route::get('/pemeriksaan', function () {
            return Inertia::render(
                'Admin/PemeriksaanBerkala/Index'
            );
        })->name('pemeriksaan.index');

        Route::get('/pemeriksaan/{id}', function ($id) {
            return Inertia::render(
                'Admin/PemeriksaanBerkala/Show',
                [
                    'id' => $id,
                ]
            );
        })->name('pemeriksaan.show');


        /*
        |--------------------------------------------------------------------------
        | Report Berkala
        |--------------------------------------------------------------------------
        */

        Route::get('/report-berkala', function () {
            return Inertia::render(
                'Admin/PemeriksaanBerkala/Report'
            );
        })->name('report.berkala');


        /*
        |--------------------------------------------------------------------------
        | Kunjungan Klinik
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kunjungan',
            [
                AdminKunjunganController::class,
                'index'
            ]
        )->name('kunjungan.index');

        Route::get(
            '/kunjungan/{kunjungan}',
            [
                AdminKunjunganController::class,
                'show'
            ]
        )->name('kunjungan.show');


        /*
        |--------------------------------------------------------------------------
        | TKSI
        |--------------------------------------------------------------------------
        */

        Route::get('/tksi', function () {
            return Inertia::render(
                'Admin/TKSI/Index'
            );
        })->name('tksi.index');

        Route::get('/tksi/{id}', function ($id) {
            return Inertia::render(
                'Admin/TKSI/Show',
                [
                    'id' => $id,
                ]
            );
        })->name('tksi.show');

    });


/*
|--------------------------------------------------------------------------
| KLINIK
|--------------------------------------------------------------------------
|
| Role klinik menggantikan role petugas.
|
*/

Route::middleware(['auth', 'verified', 'role:klinik'])
    ->prefix('klinik')
    ->name('klinik.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [
                KlinikDashboardController::class,
                'index'
            ]
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Siswa
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/siswa',
            [
                KlinikSiswaController::class,
                'index'
            ]
        )->name('siswa.index');

        Route::get(
            '/siswa/{siswa}',
            [
                KlinikSiswaController::class,
                'show'
            ]
        )->name('siswa.show');


        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Berkala
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kesehatan/pemeriksaan-berkala',
            [
                PemeriksaanBerkalaController::class,
                'index'
            ]
        )->name('kesehatan.pemeriksaan.index');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/{siswa}/{jenis}',
            [
                PemeriksaanBerkalaController::class,
                'create'
            ]
        )->name('kesehatan.pemeriksaan.create');


        Route::post(
            '/kesehatan/pemeriksaan-berkala/{siswa}/{jenis}',
            [
                PemeriksaanBerkalaController::class,
                'store'
            ]
        )->name('kesehatan.pemeriksaan.store');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/detail/{pemeriksaanBerkala}',
            [
                PemeriksaanBerkalaController::class,
                'show'
            ]
        )->name('kesehatan.pemeriksaan.show');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/{pemeriksaanBerkala}/edit',
            [
                PemeriksaanBerkalaController::class,
                'edit'
            ]
        )->name('kesehatan.pemeriksaan.edit');


        Route::put(
            '/kesehatan/pemeriksaan-berkala/{pemeriksaanBerkala}',
            [
                PemeriksaanBerkalaController::class,
                'update'
            ]
        )->name('kesehatan.pemeriksaan.update');


        /*
        |--------------------------------------------------------------------------
        | REPORT BERKALA
        |--------------------------------------------------------------------------
        */

        Route::prefix('kesehatan/report')
            ->name('kesehatan.report.')
            ->group(function () {

                Route::get(
                    '/berkala',
                    [
                        ReportController::class,
                        'berkala'
                    ]
                )->name('berkala');

                Route::get(
                    '/berkala/excel',
                    [
                        ReportController::class,
                        'downloadExcel'
                    ]
                )->name('berkala.excel');

                Route::get(
                    '/berkala/pdf',
                    [
                        ReportController::class,
                        'downloadPdf'
                    ]
                )->name('berkala.pdf');

                Route::get(
                    '/berkala/detail/{pemeriksaan}/pdf',
                    [
                        ReportController::class,
                        'downloadDetailPdf'
                    ]
                )->name('berkala.detail.pdf');


                /*
                |--------------------------------------------------------------------------
                | REPORT KUNJUNGAN KLINIK
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/kunjungan',
                    [
                        ReportController::class,
                        'kunjungan'
                    ]
                )->name('kunjungan');

                Route::get(
                    '/kunjungan/excel',
                    [
                        ReportController::class,
                        'downloadKunjunganExcel'
                    ]
                )->name('kunjungan.excel');

                Route::get(
                    '/kunjungan/pdf',
                    [
                        ReportController::class,
                        'downloadKunjunganPdf'
                    ]
                )->name('kunjungan.pdf');

                Route::get(
                    '/kunjungan/{kunjungan}/pdf',
                    [
                        ReportController::class,
                        'downloadKunjunganDetailPdf'
                    ]
                )->name('kunjungan.detail.pdf');
            });


        /*
        |--------------------------------------------------------------------------
        | Kunjungan Klinik
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kesehatan/kunjungan',
            [
                KlinikKunjunganController::class,
                'index'
            ]
        )->name('kesehatan.kunjungan.index');


        Route::get(
            '/kesehatan/kunjungan/create',
            [
                KlinikKunjunganController::class,
                'create'
            ]
        )->name('kesehatan.kunjungan.create');


        Route::post(
            '/kesehatan/kunjungan',
            [
                KlinikKunjunganController::class,
                'store'
            ]
        )->name('kesehatan.kunjungan.store');


        Route::get(
            '/kesehatan/kunjungan/{kunjungan}',
            [
                KlinikKunjunganController::class,
                'show'
            ]
        )->name('kesehatan.kunjungan.show');


        Route::get(
            '/kesehatan/kunjungan/{kunjungan}/edit',
            [
                KlinikKunjunganController::class,
                'edit'
            ]
        )->name('kesehatan.kunjungan.edit');


        Route::put(
            '/kesehatan/kunjungan/{kunjungan}',
            [
                KlinikKunjunganController::class,
                'update'
            ]
        )->name('kesehatan.kunjungan.update');


        Route::delete(
            '/kesehatan/kunjungan/{kunjungan}',
            [
                KlinikKunjunganController::class,
                'destroy'
            ]
        )->name('kesehatan.kunjungan.destroy');


        /*
        |--------------------------------------------------------------------------
        | PRINT DETAIL KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kesehatan/kunjungan/{kunjungan}/print',
            [
                KlinikKunjunganController::class,
                'print'
            ]
        )->name('kesehatan.kunjungan.print');


        /*
        |--------------------------------------------------------------------------
        | PDF DETAIL KUNJUNGAN
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/kesehatan/kunjungan/{kunjungan}/pdf',
            [
                KlinikKunjunganController::class,
                'pdf'
            ]
        )->name('kesehatan.kunjungan.pdf');


        /*
        |--------------------------------------------------------------------------
        | OBAT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/obat',
            [
                KlinikObatController::class,
                'index'
            ]
        )->name('obat.index');

        Route::get(
            '/obat/create',
            [
                KlinikObatController::class,
                'create'
            ]
        )->name('obat.create');

        Route::post(
            '/obat',
            [
                KlinikObatController::class,
                'store'
            ]
        )->name('obat.store');

        Route::get(
            '/obat/{obat}/edit',
            [
                KlinikObatController::class,
                'edit'
            ]
        )->name('obat.edit');

        Route::put(
            '/obat/{obat}',
            [
                KlinikObatController::class,
                'update'
            ]
        )->name('obat.update');

        Route::delete(
            '/obat/{obat}',
            [
                KlinikObatController::class,
                'destroy'
            ]
        )->name('obat.destroy');

            // =====================================================
    // BATCH OBAT
    // =====================================================

    Route::post('/obat/{obat}/batch', [KlinikObatController::class, 'storeBatch'])
        ->name('obat.batch.store');


        /*
        |--------------------------------------------------------------------------
        | PENYAKIT
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/penyakit',
            [
                KlinikPenyakitController::class,
                'index'
            ]
        )->name('penyakit.index');

        Route::get(
            '/penyakit/create',
            [
                KlinikPenyakitController::class,
                'create'
            ]
        )->name('penyakit.create');

        Route::get(
            '/penyakit/{penyakit}',
            [
                KlinikPenyakitController::class,
                'show'
            ]
        )->name('penyakit.show');

        Route::post(
            '/penyakit',
            [
                KlinikPenyakitController::class,
                'store'
            ]
        )->name('penyakit.store');

        Route::get(
            '/penyakit/{penyakit}/edit',
            [
                KlinikPenyakitController::class,
                'edit'
            ]
        )->name('penyakit.edit');

        Route::put(
            '/penyakit/{penyakit}',
            [
                KlinikPenyakitController::class,
                'update'
            ]
        )->name('penyakit.update');

        Route::delete(
            '/penyakit/{penyakit}',
            [
                KlinikPenyakitController::class,
                'destroy'
            ]
        )->name('penyakit.destroy');

    });


/*
|--------------------------------------------------------------------------
| TKSI
|--------------------------------------------------------------------------
|
| Role tksi menggantikan role pendamping.
|
| Untuk sementara beberapa method masih menggunakan
| PendampingController karena Controller belum kita refactor.
| Pada tahap Controller nanti akan dipindahkan ke TksiController.
|
*/

Route::middleware(['auth', 'verified', 'role:tksi'])
    ->prefix('tksi')
    ->name('tksi.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [
                TksiDashboardController::class,
                'index'
            ]
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Panduan
        |--------------------------------------------------------------------------
        */

        Route::get('/panduan', function () {
            return Inertia::render(
                'Tksi/Tksi/Panduan'
            );
        })->name('panduan');


        /*
        |--------------------------------------------------------------------------
        | TKSI
        |--------------------------------------------------------------------------
        */

        Route::get('/tksi', function () {
            return Inertia::render(
                'Tksi/Tksi/Index'
            );
        })->name('tksi.index');


        /*
        |--------------------------------------------------------------------------
        | Input TKSI
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/input',
            [
                TksiTksiController::class,
                'index'
            ]
        )->name('input.index');

        Route::get(
            '/input/create/{siswa}',
            [
                TksiTksiController::class,
                'create'
            ]
        )->name('input.create');

        Route::post(
            '/input',
            [
                TksiTksiController::class,
                'store'
            ]
        )->name('input.store');

        Route::patch(
            '/input/{tksiHasil}',
            [
                TksiTksiController::class,
                'update'
            ]
        )->name('input.update');


        /*
        |--------------------------------------------------------------------------
        | Report TKSI
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/report',
            [
                TksiReportController::class,
                'index'
            ]
        )->name('report');

        Route::get(
            '/report/excel',
            [
                TksiReportController::class,
                'exportExcel'
            ]
        )->name('report.excel');

        Route::get(
            '/report/pdf',
            [
                TksiReportController::class,
                'exportPdf'
            ]
        )->name('report.pdf');

    });


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get(
        '/profile',
        [
            ProfileController::class,
            'edit'
        ]
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [
            ProfileController::class,
            'update'
        ]
    )->name('profile.update');

    Route::delete(
        '/profile',
        [
            ProfileController::class,
            'destroy'
        ]
    )->name('profile.destroy');

});


require __DIR__.'/auth.php';