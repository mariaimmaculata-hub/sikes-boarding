<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class KlinikController extends Controller
{
    /**
     * Dashboard Klinik
     */
    public function dashboard()
    {
        return Inertia::render('Klinik/Dashboard/Index');
    }

    /**
     * Report Pemeriksaan Berkala
     */
    public function reportBerkala()
    {
        return Inertia::render('Klinik/PemeriksaanBerkala/Report');
    }
}