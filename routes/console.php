<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\ObatBatch;
use App\Services\NotificationService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $today = now()->startOfDay();

    ObatBatch::with('obat')
        ->where('stok', '>', 0)
        ->whereDate('tanggal_kadaluarsa', '<=', $today->copy()->addDays(7))
        ->get()
        ->each(function (ObatBatch $batch) use ($today) {
            $days = $today->diffInDays($batch->tanggal_kadaluarsa, false);
            $type = $days < 0 ? 'danger' : 'warning';
            $label = $days < 0 ? 'sudah kedaluwarsa' : "akan kedaluwarsa dalam {$days} hari";

            NotificationService::toRole(
                'klinik',
                'Peringatan Kedaluwarsa Obat',
                "Obat {$batch->obat->nama_obat} {$label}. Sisa stok {$batch->stok}.",
                $type,
                route('klinik.obat.index')
            );
        });

    ObatBatch::with('obat')
        ->where('stok', '<=', 5)
        ->where('stok', '>', 0)
        ->get()
        ->each(function (ObatBatch $batch) {
            NotificationService::toRole(
                'klinik',
                'Stok Obat Menipis',
                "Stok {$batch->obat->nama_obat} tersisa {$batch->stok} unit.",
                'warning',
                route('klinik.obat.index')
            );
        });
})->dailyAt('07:00');
