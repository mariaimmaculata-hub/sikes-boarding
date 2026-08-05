<script setup>
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    ChartBarIcon, 
    ArrowDownTrayIcon, 
    CalendarDaysIcon,
    DocumentTextIcon
} from '@heroicons/vue/24/outline';

const exportData = (type) => {
    alert(`Mengekspor laporan ${type} ke format PDF/Excel...`);
};
</script>

<template>
    <AdminLayout>
        <Head title="Laporan &amp; Statistik" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan &amp; Statistik Kesehatan</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Unduh laporan dan analisis statistik kesehatan jasmani siswa boarding.</p>
            </div>

            <!-- Main Statistics Dashboard -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Report Card 1: Periodic Examinations -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="p-3 bg-blue-50 text-blue-700 w-fit rounded-2xl border border-blue-100">
                            <CalendarDaysIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Laporan Pemeriksaan Berkala</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            Unduh seluruh rekam medik fisik berkala siswa per semester. Laporan ini mencakup tinggi badan, berat badan, lingkar perut, tensi darah, dan indeks kebugaran.
                        </p>
                    </div>
                    
                    <button 
                        @click="exportData('Pemeriksaan Berkala')"
                        class="mt-6 w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center justify-center space-x-2 transition"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>Unduh Laporan PDF</span>
                    </button>
                </div>

                <!-- Report Card 2: Clinic Visits -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="p-3 bg-rose-50 text-rose-700 w-fit rounded-2xl border border-rose-100">
                            <DocumentTextIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Laporan Kunjungan Harian Klinik</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            Unduh laporan log kunjungan harian klinik sekolah, keluhan terbanyak, obat terpakai, dan rujukan rumah sakit untuk analisis komprehensif.
                        </p>
                    </div>

                    <button 
                        @click="exportData('Kunjungan Harian Klinik')"
                        class="mt-6 w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl flex items-center justify-center space-x-2 transition"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>Unduh Laporan PDF</span>
                    </button>
                </div>

                <!-- Report Card 3: TKSI Physical Fitness -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="p-3 bg-emerald-50 text-emerald-700 w-fit rounded-2xl border border-emerald-100">
                            <ChartBarIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Laporan Hasil Uji Kebugaran (TKSI)</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            Laporan analisis statistik skor tes koordinasi mata-tangan dan kelincahan motorik jasmani siswa berprestasi.
                        </p>
                    </div>

                    <button 
                        @click="exportData('Hasil Uji Kebugaran')"
                        class="mt-6 w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl flex items-center justify-center space-x-2 transition"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>Unduh Laporan PDF</span>
                    </button>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
