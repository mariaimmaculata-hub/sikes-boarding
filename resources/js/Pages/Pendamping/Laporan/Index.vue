<script setup>
import { Head } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { ArrowDownTrayIcon, ChartBarIcon } from '@heroicons/vue/24/outline';

const exportReport = (name) => {
    alert(`Mengekspor Laporan ${name}...`);
};
</script>

<template>
    <PendampingLayout>
        <Head title="Laporan &amp; Analisis" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan &amp; Analisis</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Unduh dokumen berkala kesehatan siswa boarding.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="p-3 bg-blue-50 text-blue-700 w-fit rounded-2xl">
                            <ChartBarIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-900">Rekap Fisik &amp; Kebugaran (TKSI)</h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">Laporan koordinasi jasmani mata-tangan dan kelincahan siswa boarding.</p>
                    </div>

                    <button 
                        @click="exportReport('TKSI')"
                        class="mt-6 w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl flex items-center justify-center space-x-2 transition"
                    >
                        <ArrowDownTrayIcon class="w-4 h-4" />
                        <span>Unduh PDF</span>
                    </button>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
