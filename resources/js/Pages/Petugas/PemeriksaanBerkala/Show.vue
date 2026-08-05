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

const checkup = {
    date: '10 Feb 2026',
    name: 'Aulia Rahma',
    class: 'X AKL 2',
    height: '162 cm',
    weight: '52 kg',
    bp: '115/75 mmHg',
    status: 'Perlu Perhatian',
    notes: 'Kadar hemoglobin rendah, disarankan peningkatan asupan nutrisi zat besi.'
};
</script>

<template>
    <PetugasLayout>
        <Head title="Detail Pemeriksaan Berkala" />

        <div class="space-y-6 max-w-xl">
            <div class="flex items-center space-x-3">
                <Link :href="route('petugas.pemeriksaan.index')" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Pemeriksaan</h2>
                    <p class="text-xs text-slate-500 font-medium">Informasi rekam medis fisik berkala siswa.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-4 text-xs">
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Tanggal Uji</span>
                    <span class="font-extrabold text-slate-800">{{ checkup.date }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Nama Lengkap</span>
                    <span class="font-extrabold text-blue-900 text-sm">{{ checkup.name }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Kelas</span>
                    <span class="font-bold text-slate-800">{{ checkup.class }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">TB / BB</span>
                    <span class="font-bold text-slate-800">{{ checkup.height }} / {{ checkup.weight }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Tekanan Darah</span>
                    <span class="font-bold text-slate-800">{{ checkup.bp }}</span>
                </div>
                <div class="flex justify-between border-b border-slate-100 pb-3">
                    <span class="text-slate-400 font-bold uppercase">Status</span>
                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-amber-50 text-amber-700 border-amber-200 uppercase">
                        {{ checkup.status }}
                    </span>
                </div>
                <div class="space-y-1.5 pt-2">
                    <span class="text-slate-400 font-bold uppercase block">Catatan Medis</span>
                    <p class="bg-blue-50/50 border border-blue-100 text-blue-950 font-medium leading-relaxed p-3 rounded-xl">{{ checkup.notes }}</p>
                </div>
            </div>
        </div>
    </PetugasLayout>
</template>
