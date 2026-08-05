<script setup>
import { Head } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowDownTrayIcon, ChartBarIcon } from '@heroicons/vue/24/outline';

const exportReport = (name) => {
    alert(`Mengekspor Laporan ${name}...`);
};
</script>

<template>
    <PetugasLayout>
        <Head title="Laporan &amp; Analisis" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan &amp; Analisis</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Unduh dokumen berkala rekap medis UKS.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="p-3 bg-rose-50 text-rose-700 w-fit rounded-2xl">
                            <ChartBarIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Rekap Kunjungan Harian</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Laporan rekam medis kunjungan harian klinik sekolah.</p>
                    </div>

                    <button 
                        @click="exportReport('Kunjungan Harian')"
                        class="mt-6 w-full py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl flex items-center justify-center space-x-2 transition"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>Unduh PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </PetugasLayout>
</template>
