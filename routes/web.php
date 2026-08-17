<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
//admin
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

//TKSI
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



Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/notifikasi', [
        NotificationController::class,
        'index'
    ])->name('notifikasi.index');

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

       Route::get('/dashboard', [AdminDashboardController::class, 'index'])
    ->name('dashboard');
    

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

        Route::resource('siswa', AdminSiswaController::class);
        //import
         Route::get('/siswa-import', [SiswaImportController::class, 'create'])
            ->name('siswa.import');

        Route::post('/siswa-import', [SiswaImportController::class, 'store'])
            ->name('siswa.import.store');

        Route::resource(
            'user',
            UserController::class
        )->except([
            'show',
        ]);
       

    });
        
    // ==================================================
// PERIODE
// ==================================================

Route::get('/periode/siswa-aktif', [
    SiswaPeriodeAktifController::class,
    'index'
])->name('periode.siswa-aktif');
Route::get( '/periode/siswa-aktif/{siswaId}',
[SiswaPeriodeAktifController::class, 'show'] )
->name('periode.siswa-aktif.show');

Route::get(
            '/periode/report',
            [PeriodeController::class, 'report']
        )->name('periode.report');
       Route::get(
    '/periode/report/{periode}',
    [PeriodeController::class, 'showReport']
)->name('periode.report.show');

Route::resource('periode', PeriodeController::class);

        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Berkala
        |--------------------------------------------------------------------------
        */

        Route::get('/pemeriksaan', function () {
            return Inertia::render('Admin/PemeriksaanBerkala/Index');
        })->name('pemeriksaan.index');

        Route::get('/pemeriksaan/{id}', function ($id) {
            return Inertia::render('Admin/PemeriksaanBerkala/Show', [
                'id' => $id,
            ]);
        })->name('pemeriksaan.show');


        /*
        |--------------------------------------------------------------------------
        | Report Berkala
        |--------------------------------------------------------------------------
        */

        Route::get('/report-berkala', function () {
            return Inertia::render('Admin/PemeriksaanBerkala/Report');
        })->name('report.berkala');


/*
|--------------------------------------------------------------------------
| Kunjungan Klinik
|--------------------------------------------------------------------------
*/

Route::get('/kunjungan', [AdminKunjunganController::class, 'index'])
    ->name('kunjungan.index');

Route::get('/kunjungan/{kunjungan}', [AdminKunjunganController::class, 'show'])
    ->name('kunjungan.show');

        /*
        |--------------------------------------------------------------------------
        | TKSI
        |--------------------------------------------------------------------------
        */

        Route::get('/tksi', function () {
            return Inertia::render('Admin/TKSI/Index');
        })->name('tksi.index');

        Route::get('/tksi/{id}', function ($id) {
            return Inertia::render('Admin/TKSI/Show', [
                'id' => $id,
            ]);
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

        // Dashboard
        Route::get('/dashboard', [
            KlinikDashboardController::class,
            'index'
        ])->name('dashboard');
        

        // Siswa
Route::get('/siswa', [
    KlinikSiswaController::class,
    'index'
])->name('siswa.index');

Route::get('/siswa/{siswa}', [
    KlinikSiswaController::class,
    'show'
])->name('siswa.show');


        // Pemeriksaan Berkala
        Route::get(
            '/kesehatan/pemeriksaan-berkala',
            [PemeriksaanBerkalaController::class, 'index']
        )->name('kesehatan.pemeriksaan.index');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/{siswa}/{jenis}',
            [PemeriksaanBerkalaController::class, 'create']
        )->name('kesehatan.pemeriksaan.create');


        Route::post(
            '/kesehatan/pemeriksaan-berkala/{siswa}/{jenis}',
            [PemeriksaanBerkalaController::class, 'store']
        )->name('kesehatan.pemeriksaan.store');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/detail/{pemeriksaanBerkala}',
            [PemeriksaanBerkalaController::class, 'show']
        )->name('kesehatan.pemeriksaan.show');


        Route::get(
            '/kesehatan/pemeriksaan-berkala/{pemeriksaanBerkala}/edit',
            [PemeriksaanBerkalaController::class, 'edit']
        )->name('kesehatan.pemeriksaan.edit');


        Route::put(
            '/kesehatan/pemeriksaan-berkala/{pemeriksaanBerkala}',
            [PemeriksaanBerkalaController::class, 'update']
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
                    [ReportController::class, 'berkala']
                )->name('berkala');

                Route::get(
                    '/berkala/excel',
                    [ReportController::class, 'downloadExcel']
                )->name('berkala.excel');

                Route::get(
                    '/berkala/pdf',
                    [ReportController::class, 'downloadPdf']
                )->name('berkala.pdf');

                Route::get(
                    '/berkala/detail/{pemeriksaan}/pdf',
                    [ReportController::class, 'downloadDetailPdf']
                )->name('berkala.detail.pdf');


                /*
                |--------------------------------------------------------------------------
                | REPORT KUNJUNGAN KLINIK
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/kunjungan',
                    [ReportController::class, 'kunjungan']
                )->name('kunjungan');

                Route::get(
                    '/kunjungan/excel',
                    [ReportController::class, 'downloadKunjunganExcel']
                )->name('kunjungan.excel');

                Route::get(
                    '/kunjungan/pdf',
                    [ReportController::class, 'downloadKunjunganPdf']
                )->name('kunjungan.pdf');

                Route::get(
                    '/kunjungan/{kunjungan}/pdf',
                    [ReportController::class, 'downloadKunjunganDetailPdf']
                )->name('kunjungan.detail.pdf');
            });
    

/*
|--------------------------------------------------------------------------
| Kunjungan Klinik
|--------------------------------------------------------------------------
*/

Route::get(
    '/kesehatan/kunjungan',
    [KlinikKunjunganController::class, 'index']
)->name('kesehatan.kunjungan.index');

Route::get(
    '/kesehatan/kunjungan/create',
    [KlinikKunjunganController::class, 'create']
)->name('kesehatan.kunjungan.create');

Route::post(
    '/kesehatan/kunjungan',
    [KlinikKunjunganController::class, 'store']
)->name('kesehatan.kunjungan.store');

Route::get(
    '/kesehatan/kunjungan/{kunjungan}',
    [KlinikKunjunganController::class, 'show']
)->name('kesehatan.kunjungan.show');

Route::get(
    '/kesehatan/kunjungan/{kunjungan}/edit',
    [KlinikKunjunganController::class, 'edit']
)->name('kesehatan.kunjungan.edit');

Route::put(
    '/kesehatan/kunjungan/{kunjungan}',
    [KlinikKunjunganController::class, 'update']
)->name('kesehatan.kunjungan.update');

Route::delete(
    '/kesehatan/kunjungan/{kunjungan}',
    [KlinikKunjunganController::class, 'destroy']
)->name('kesehatan.kunjungan.destroy');
    });


// ==================================================
// OBAT
// ==================================================

Route::get('/klinik/obat', [
    KlinikObatController::class,
    'index'
])->name('klinik.obat.index');

Route::get('/klinik/obat/create', [
    KlinikObatController::class,
    'create'
])->name('klinik.obat.create');

Route::post('/klinik/obat', [
    KlinikObatController::class,
    'store'
])->name('klinik.obat.store');

Route::get('/klinik/obat/{obat}/edit', [
    KlinikObatController::class,
    'edit'
])->name('klinik.obat.edit');

Route::put('/klinik/obat/{obat}', [
    KlinikObatController::class,
    'update'
])->name('klinik.obat.update');

Route::delete('/klinik/obat/{obat}', [
    KlinikObatController::class,
    'destroy'
])->name('klinik.obat.destroy');


// ==================================================
// PENYAKIT
// ==================================================

Route::get('/klinik/penyakit', [
    KlinikPenyakitController::class,
    'index'
])->name('klinik.penyakit.index');

Route::get('/klinik/penyakit/create', [
    KlinikPenyakitController::class,
    'create'
])->name('klinik.penyakit.create');
Route::get('/klinik/penyakit/{penyakit}', [KlinikPenyakitController::class, 'show'])
    ->name('klinik.penyakit.show');

Route::post('/klinik/penyakit', [
    KlinikPenyakitController::class,
    'store'
])->name('klinik.penyakit.store');

Route::get('/klinik/penyakit/{penyakit}/edit', [
    KlinikPenyakitController::class,
    'edit'
])->name('klinik.penyakit.edit');

Route::put('/klinik/penyakit/{penyakit}', [
    KlinikPenyakitController::class,
    'update'
])->name('klinik.penyakit.update');

Route::delete('/klinik/penyakit/{penyakit}', [
    KlinikPenyakitController::class,
    'destroy'
])->name('klinik.penyakit.destroy');


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

        Route::get('/dashboard', [TksiDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/panduan', function () {
    return Inertia::render('Tksi/Tksi/Panduan');
})->name('panduan');

          Route::get('/tksi', function () {
            return Inertia::render('Tksi/Tksi/Index');
        })->name('tksi.index');

     
Route::get('/input', [TksiTksiController::class, 'index'])
    ->name('input.index');

Route::get('/input/create/{siswa}', [TksiTksiController::class, 'create'])
    ->name('input.create');

Route::post('/input', [TksiTksiController::class, 'store'])
    ->name('input.store');
    Route::patch('/input/{tksiHasil}', [TksiTksiController::class, 'update'])
    ->name('input.update');


    //report tksi
Route::get('/report', [ TksiReportController::class, 'index' ])
->name('report'); 
Route::get('/report/excel', 
[ TksiReportController::class, 'exportExcel' ])
->name('report.excel');
 Route::get('/report/pdf', [ TksiReportController::class, 'exportPdf' ])
 ->name('report.pdf');

});

        /*
        |--------------------------------------------------------------------------
        | Data Siswa
        |--------------------------------------------------------------------------
        */

        // Route::get('/siswa', [SiswaBoardingController::class, 'index'])
        //     ->name('siswa.index');

        // Route::get('/siswa/jurusan/{jurusan}', [SiswaBoardingController::class, 'showJurusan'])
        //     ->name('siswa.jurusan');

        // Route::get('/siswa/jurusan/{jurusan}/kelas/{kelas}', [SiswaBoardingController::class, 'showKelas'])
        //     ->name('siswa.kelas');

        // Route::get('/siswa/{siswa}', [SiswaBoardingController::class, 'showSiswa'])
        //     ->name('siswa.detail');


        /*
        |--------------------------------------------------------------------------
        | Panduan TKSI
        |--------------------------------------------------------------------------
        */

//         Route::get('/panduan', [TksiController::class, 'tksiPanduan'])
//     ->name('panduan');

// Route::get('/panduan/hand-eye', [TksiController::class, 'panduanHandEye'])
//     ->name('panduan.hand-eye');

// Route::get('/panduan/vertical-jump', [TksiController::class, 'panduanVerticalJump'])
//     ->name('panduan.vertical-jump');

// Route::get('/panduan/t-test', [TksiController::class, 'panduanTTest'])
//     ->name('panduan.t-test');

// Route::get('/panduan/hand-touch', [TksiController::class, 'panduanHandTouch'])
//     ->name('panduan.hand-touch');

// Route::get('/panduan/dipping', [TksiController::class, 'panduanDipping'])
//     ->name('panduan.dipping');

// Route::get('/panduan/beep', [TksiController::class, 'panduanBeep'])
//     ->name('panduan.beep');


        /*
        |--------------------------------------------------------------------------
        | Input TKSI
        |--------------------------------------------------------------------------
        */

//         Route::get('/input', [TksiController::class, 'tksi'])
//     ->name('input.index');

// Route::get('/input/create', [TksiController::class, 'tksiCreate'])
//     ->name('input.create');

// Route::post('/input', [TksiController::class, 'tksiStore'])
//     ->name('input.store');

// Route::get('/input/{id}', [TksiController::class, 'tksiShow'])
//     ->name('input.show');

// Route::get('/input/{id}/isi', [TksiController::class, 'tksiIsi'])
//     ->name('input.isi');

        /*
        |--------------------------------------------------------------------------
        | Report Periode
        |--------------------------------------------------------------------------
    //     */

    //     Route::get('/report-periode', [TksiController::class, 'reportPeriode'])
    // ->name('report.periode');

      


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});


require __DIR__.'/auth.php';