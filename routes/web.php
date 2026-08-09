<?php

use App\Http\Controllers\ProfileController;
//admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Master\SiswaController;
use App\Http\Controllers\Admin\Master\SiswaImportController;
use App\Http\Controllers\Admin\Master\UserController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\SiswaPeriodeAktifController;
use App\Http\Controllers\Admin\KunjunganController;

use App\Http\Controllers\KlinikController;
use App\Http\Controllers\TksiController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PenyakitController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaBoardingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
});


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

        Route::get('/dashboard', [DashboardController::class, 'index'])
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

        Route::resource('siswa', SiswaController::class);
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

Route::get('/kunjungan', [KunjunganController::class, 'index'])
    ->name('kunjungan.index');

Route::get('/kunjungan/{kunjungan}', [KunjunganController::class, 'show'])
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



        /*
        |--------------------------------------------------------------------------
        | Notifikasi
        |--------------------------------------------------------------------------
        */

        Route::get('/notifikasi', function () {
            return Inertia::render('Admin/Notifikasi/Index');
        })->name('notifikasi.index');
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

        Route::get('/dashboard', [KlinikController::class, 'dashboard']);


        /*
        |--------------------------------------------------------------------------
        | Data Siswa
        |--------------------------------------------------------------------------
        |
        | Klinik hanya melihat data siswa.
        | Pengelolaan siswa tetap milik Admin.
        |
        */

        Route::get('/siswa', [SiswaBoardingController::class, 'index'])
            ->name('siswa.index');

        Route::get('/siswa/{siswa}', [SiswaBoardingController::class, 'showSiswa'])
            ->name('siswa.detail');


        /*
        |--------------------------------------------------------------------------
        | Pemeriksaan Berkala
        |--------------------------------------------------------------------------
        */

        Route::get('/pemeriksaan', [PemeriksaanController::class, 'index'])
            ->name('pemeriksaan.index');

        Route::get('/pemeriksaan/{pemeriksaan}', [PemeriksaanController::class, 'show'])
            ->name('pemeriksaan.show');

        Route::get('/pemeriksaan/{pemeriksaan}/edit', [PemeriksaanController::class, 'edit'])
            ->name('pemeriksaan.edit');

        Route::put('/pemeriksaan/{pemeriksaan}', [PemeriksaanController::class, 'update'])
            ->name('pemeriksaan.update');


        /*
        |--------------------------------------------------------------------------
        | Report Berkala
        |--------------------------------------------------------------------------
        */

       Route::get('/report-berkala', [KlinikController::class, 'reportBerkala']);

        /*
        |--------------------------------------------------------------------------
        | Kunjungan Klinik
        |--------------------------------------------------------------------------
        */

        Route::get('/kunjungan', [KunjunganController::class, 'index'])
            ->name('kunjungan.index');

        Route::get('/kunjungan/create', [KunjunganController::class, 'create'])
            ->name('kunjungan.create');

        Route::post('/kunjungan', [KunjunganController::class, 'store'])
            ->name('kunjungan.store');

        Route::get('/kunjungan/{kunjungan}', [KunjunganController::class, 'show'])
            ->name('kunjungan.show');

        Route::get('/kunjungan/{kunjungan}/edit', [KunjunganController::class, 'edit'])
            ->name('kunjungan.edit');

        Route::put('/kunjungan/{kunjungan}', [KunjunganController::class, 'update'])
            ->name('kunjungan.update');


        /*
        |--------------------------------------------------------------------------
        | Data Obat
        |--------------------------------------------------------------------------
        */

        Route::resource('/obat', ObatController::class)
            ->names('obat');


        /*
        |--------------------------------------------------------------------------
        | Data Penyakit
        |--------------------------------------------------------------------------
        */

        Route::resource('/penyakit', PenyakitController::class)
            ->names('penyakit');


        /*
        |--------------------------------------------------------------------------
        | Notifikasi
        |--------------------------------------------------------------------------
        */

        Route::get('/notifikasi', function () {
            return Inertia::render('Klinik/Notifikasi/Index');
        })->name('notifikasi.index');
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

        Route::get('/dashboard', [TksiController::class, 'dashboard'])
    ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Data Siswa
        |--------------------------------------------------------------------------
        */

        Route::get('/siswa', [SiswaBoardingController::class, 'index'])
            ->name('siswa.index');

        Route::get('/siswa/jurusan/{jurusan}', [SiswaBoardingController::class, 'showJurusan'])
            ->name('siswa.jurusan');

        Route::get('/siswa/jurusan/{jurusan}/kelas/{kelas}', [SiswaBoardingController::class, 'showKelas'])
            ->name('siswa.kelas');

        Route::get('/siswa/{siswa}', [SiswaBoardingController::class, 'showSiswa'])
            ->name('siswa.detail');


        /*
        |--------------------------------------------------------------------------
        | Panduan TKSI
        |--------------------------------------------------------------------------
        */

        Route::get('/panduan', [TksiController::class, 'tksiPanduan'])
    ->name('panduan');

Route::get('/panduan/hand-eye', [TksiController::class, 'panduanHandEye'])
    ->name('panduan.hand-eye');

Route::get('/panduan/vertical-jump', [TksiController::class, 'panduanVerticalJump'])
    ->name('panduan.vertical-jump');

Route::get('/panduan/t-test', [TksiController::class, 'panduanTTest'])
    ->name('panduan.t-test');

Route::get('/panduan/hand-touch', [TksiController::class, 'panduanHandTouch'])
    ->name('panduan.hand-touch');

Route::get('/panduan/dipping', [TksiController::class, 'panduanDipping'])
    ->name('panduan.dipping');

Route::get('/panduan/beep', [TksiController::class, 'panduanBeep'])
    ->name('panduan.beep');


        /*
        |--------------------------------------------------------------------------
        | Input TKSI
        |--------------------------------------------------------------------------
        */

        Route::get('/input', [TksiController::class, 'tksi'])
    ->name('input.index');

Route::get('/input/create', [TksiController::class, 'tksiCreate'])
    ->name('input.create');

Route::post('/input', [TksiController::class, 'tksiStore'])
    ->name('input.store');

Route::get('/input/{id}', [TksiController::class, 'tksiShow'])
    ->name('input.show');

Route::get('/input/{id}/isi', [TksiController::class, 'tksiIsi'])
    ->name('input.isi');

        /*
        |--------------------------------------------------------------------------
        | Report Periode
        |--------------------------------------------------------------------------
        */

        Route::get('/report-periode', [TksiController::class, 'reportPeriode'])
    ->name('report.periode');

        /*
        |--------------------------------------------------------------------------
        | Notifikasi
        |--------------------------------------------------------------------------
        */

        Route::get('/notifikasi', [TksiController::class, 'notifikasi'])
    ->name('notifikasi.index');
    });


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