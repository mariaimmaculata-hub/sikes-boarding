<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TKSIController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Petugas/TKSI/Index');
    }

    public function create(): Response
    {
        return Inertia::render('Petugas/TKSI/Create');
    }

    public function store(Request $request)
    {
        return redirect()->route('petugas.tksi.index');
    }

    public function show(string $id)
    {
        return redirect()->route('petugas.tksi.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('petugas.tksi.index');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('petugas.tksi.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('petugas.tksi.index');
    }
}
