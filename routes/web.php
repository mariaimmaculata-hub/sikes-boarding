<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PendampingController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\TKSIController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\PenyakitController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaBoardingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'petugas') {
        return redirect()->route('petugas.dashboard');
    } elseif ($user->role === 'pendamping') {
        return redirect()->route('pendamping.dashboard');
    }
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Panel Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return Inertia::render('Admin/Dashboard/Index');
        })->name('dashboard');

        // Master Data
        Route::prefix('master')->name('master.')->group(function () {
            // Jurusan
            Route::get('jurusan', function () { return Inertia::render('Admin/MasterData/Jurusan/Index'); })->name('jurusan.index');
            Route::get('jurusan/create', function () { return Inertia::render('Admin/MasterData/Jurusan/Create'); })->name('jurusan.create');
            Route::get('jurusan/{id}/edit', function ($id) { return Inertia::render('Admin/MasterData/Jurusan/Edit', ['id' => $id]); })->name('jurusan.edit');

            // Kelas
            Route::get('kelas', function () { return Inertia::render('Admin/MasterData/Kelas/Index'); })->name('kelas.index');
            Route::get('kelas/create', function () { return Inertia::render('Admin/MasterData/Kelas/Create'); })->name('kelas.create');
            Route::get('kelas/{id}/edit', function ($id) { return Inertia::render('Admin/MasterData/Kelas/Edit', ['id' => $id]); })->name('kelas.edit');

            // Siswa
            Route::get('siswa', function () { return Inertia::render('Admin/MasterData/Siswa/Index'); })->name('siswa.index');
            Route::get('siswa/create', function () { return Inertia::render('Admin/MasterData/Siswa/Create'); })->name('siswa.create');
            Route::get('siswa/{id}/edit', function ($id) { return Inertia::render('Admin/MasterData/Siswa/Edit', ['id' => $id]); })->name('siswa.edit');
            Route::get('siswa/{id}', function ($id) { return Inertia::render('Admin/MasterData/Siswa/Show', ['id' => $id]); })->name('siswa.show');

            // User
            Route::get('user', function () { return Inertia::render('Admin/MasterData/User/Index'); })->name('user.index');
            Route::get('user/create', function () { return Inertia::render('Admin/MasterData/User/Create'); })->name('user.create');
            Route::get('user/{id}/edit', function ($id) { return Inertia::render('Admin/MasterData/User/Edit', ['id' => $id]); })->name('user.edit');
        });

        // Pemeriksaan Berkala
        Route::get('pemeriksaan', function () { return Inertia::render('Admin/PemeriksaanBerkala/Index'); })->name('pemeriksaan.index');
        Route::get('pemeriksaan/{id}', function ($id) { return Inertia::render('Admin/PemeriksaanBerkala/Show', ['id' => $id]); })->name('pemeriksaan.show');

        // Kunjungan Klinik
        Route::get('kunjungan', function () { return Inertia::render('Admin/KunjunganKlinik/Index'); })->name('kunjungan.index');
        Route::get('kunjungan/{id}', function ($id) { return Inertia::render('Admin/KunjunganKlinik/Show', ['id' => $id]); })->name('kunjungan.show');

        // TKSI & Panduan
        Route::get('tksi', function () { return Inertia::render('Admin/TKSI/Index'); })->name('tksi.index');
        Route::get('tksi/{id}', function ($id) { return Inertia::render('Admin/TKSI/Show', ['id' => $id]); })->name('tksi.show');

        // Laporan
        Route::get('laporan', function () { return Inertia::render('Admin/Laporan/Index'); })->name('laporan.index');

        // Pengaturan
        Route::get('pengaturan', function () { return Inertia::render('Admin/Pengaturan/Index'); })->name('pengaturan.index');
    });

    // Route untuk Pendamping
Route::middleware(['auth', 'role:pendamping'])
->prefix('pendamping')
->name('pendamping.')
->group(function () {

    Route::get('/dashboard', [PendampingController::class, 'dashboard'])
        ->name('dashboard');


    // Siswa Boarding
    Route::get('/siswa-boarding', [SiswaBoardingController::class, 'index'])
        ->name('siswa.index');

    Route::get('/siswa-boarding/jurusan/{jurusan}', [SiswaBoardingController::class, 'showJurusan'])
        ->name('siswa.jurusan');

    Route::get('/siswa-boarding/jurusan/{jurusan}/kelas/{kelas}', [SiswaBoardingController::class, 'showKelas'])
        ->name('siswa.kelas');

    Route::get('/siswa-boarding/siswa/{siswa}', [SiswaBoardingController::class, 'showSiswa'])
        ->name('siswa.detail');



    // Pemeriksaan Berkala
    Route::get('/pemeriksaan', [PendampingController::class, 'pemeriksaan'])
        ->name('pemeriksaan.index');


    // TAMBAHKAN INI
    Route::get('/pemeriksaan/{id}', [PendampingController::class, 'pemeriksaanShow'])
        ->name('pemeriksaan.show');



    // TKSI Daftar Tes
// TKSI
Route::get('/tksi', [PendampingController::class, 'tksi'])
    ->name('tksi.index');


// Tambah Batch Tes
Route::get('/tksi/create', [PendampingController::class, 'tksiCreate'])
    ->name('tksi.create');
    

Route::post('/tksi',
[PendampingController::class,'tksiStore'])
->name('tksi.store');




Route::get('/tksi/panduan', [PendampingController::class, 'tksiPanduan'])
    ->name('tksi.panduan');
    

Route::get('/tksi/panduan/hand-eye', [PendampingController::class, 'panduanHandEye'])
    ->name('tksi.panduan.hand-eye');

Route::get('/tksi/panduan/vertical-jump', [PendampingController::class, 'panduanVerticalJump'])
    ->name('tksi.panduan.vertical-jump');

Route::get('/tksi/panduan/t-test', [PendampingController::class, 'panduanTTest'])
    ->name('tksi.panduan.t-test');

Route::get('/tksi/panduan/hand-touch', [PendampingController::class, 'panduanHandTouch'])
    ->name('tksi.panduan.hand-touch');

Route::get('/tksi/panduan/dipping', [PendampingController::class, 'panduanDipping'])
    ->name('tksi.panduan.dipping');

Route::get('/tksi/panduan/beep', [PendampingController::class, 'panduanBeep'])
    ->name('tksi.panduan.beep');

Route::get('/tksi/{id}',
    [PendampingController::class,'tksiShow'])
    ->name('tksi.show');
    // Isi Tes Siswa
Route::get('/tksi/{id}/isi', [PendampingController::class, 'tksiIsi'])
    ->name('tksi.isi');

    Route::get('/kunjungan', [PendampingController::class, 'kunjungan'])
        ->name('kunjungan.index');

    Route::get('/pengingat', [PendampingController::class, 'pengingat'])
        ->name('pengingat.index');

    Route::get('/laporan', [PendampingController::class, 'laporan'])
        ->name('laporan.index');

    Route::get('/pengaturan', [PendampingController::class, 'pengaturan'])
        ->name('pengaturan.index');

});

    // Route untuk Petugas Klinik
    Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [PetugasController::class, 'dashboard'])->name('dashboard');
        
        // Master Data - Siswa
        Route::resource('/master/siswa', SiswaController::class)->names('master.siswa');
        
        // Master Data - Kelas (Hierarki)
        Route::get('/master/kelas', [KelasController::class, 'index'])->name('master.kelas.index');
        Route::get('/master/kelas/jurusan/{jurusan}', [KelasController::class, 'showJurusan'])->name('master.kelas.jurusan');
        Route::get('/master/kelas/jurusan/{jurusan}/kelas/{kelas}', [KelasController::class, 'showKelas'])->name('master.kelas.detail');
        
        // Pemeriksaan Berkala
        Route::resource('/pemeriksaan', PemeriksaanController::class)->names('pemeriksaan');
        
        // Kunjungan Klinik
        Route::resource('/kunjungan', KunjunganController::class)->names('kunjungan');
        
        // TKSI
        Route::resource('/tksi', TKSIController::class)->names('tksi');
        
        // Data Obat
        Route::resource('/obat', ObatController::class)->names('obat');
        
        // Data Penyakit
        Route::resource('/penyakit', PenyakitController::class)->names('penyakit');
        
        // Laporan
        Route::get('/laporan', [PetugasController::class, 'laporan'])->name('laporan');
        
        // Pengaturan
        Route::get('/pengaturan', [PetugasController::class, 'pengaturan'])->name('pengaturan');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
