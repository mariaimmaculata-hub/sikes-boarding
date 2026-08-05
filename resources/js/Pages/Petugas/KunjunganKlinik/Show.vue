<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

defineProps({
    id: {
        type: String,
        required: true
    }
});

const visit = {
    date: '12 Feb 2026',
    name: 'Nadia Putri',
    class: 'XI TKJ 1',
    complaints: 'Demam tinggi semenjak kemarin malam, batuk berdahak disertai tenggorokan gatal.',
    diagnosis: 'ISPA',
    therapy: 'Paracetamol 500mg (3x1 sesudah makan), Ambroxol sirup (3x1 sendok makan)'
};
</script>

<template>
    <PetugasLayout>
        <Head title="Detail Kunjungan Klinik" />

        <div class="space-y-6 max-w-xl">
            <div class="flex items-center space-x-3">
                <Link :href="route('petugas.kunjungan.index')" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Kunjungan</h2>
                    <p class="text-xs text-slate-500 font-medium">Informasi rekam medis kunjungan klinik siswa.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-4 text-xs">
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Tanggal</span>
                    <span class="font-extrabold text-slate-800">{{ visit.date }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Nama Lengkap</span>
                    <span class="font-extrabold text-blue-900 text-sm">{{ visit.name }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Kelas</span>
                    <span class="font-bold text-slate-800">{{ visit.class }}</span>
                </div>
                <div class="space-y-1 border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase block">Keluhan</span>
                    <p class="text-slate-700 font-medium leading-relaxed">{{ visit.complaints }}</p>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Diagnosa</span>
                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-rose-50 text-rose-700 border-rose-200 uppercase">
                        {{ visit.diagnosis }}
                    </span>
                </div>
                <div class="space-y-1.5 pt-2">
                    <span class="text-slate-400 font-bold uppercase block">Terapi / Obat</span>
                    <p class="bg-blue-50/50 border border-blue-100 text-blue-950 font-medium leading-relaxed p-3 rounded-xl">{{ visit.therapy }}</p>
                </div>
            </div>
        </div>
    </PetugasLayout>
</template>
