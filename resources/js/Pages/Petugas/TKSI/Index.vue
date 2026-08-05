<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { PlusIcon } from '@heroicons/vue/24/outline';

const tksiRecords = [
    { id: 1, date: '12 Feb 2026', name: 'Salma Putri', class: 'X Akuntansi 1', hand_eye_score: '62', fitness_status: 'Kurang' }
];
</script>

<template>
    <PetugasLayout>
        <Head title="Tes Kebugaran (TKSI)" />

        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Tes Kebugaran &amp; Koordinasi (TKSI)</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Rekam uji jasmani dan status kebugaran siswa.</p>
                </div>
                <Link 
                    :href="route('petugas.tksi.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center space-x-1.5"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Catat Hasil Tes</span>
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                            <th class="py-3 px-6">Tanggal</th>
                            <th class="py-3 px-6">Nama Siswa</th>
                            <th class="py-3 px-6">Kelas</th>
                            <th class="py-3 px-6 text-center">Skor Koordinasi</th>
                            <th class="py-3 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in tksiRecords" :key="t.id" class="border-b border-slate-100 text-sm">
                            <td class="py-4 px-6 font-bold text-slate-500">{{ t.date }}</td>
                            <td class="py-4 px-6 font-bold text-blue-900">{{ t.name }}</td>
                            <td class="py-4 px-6 font-semibold text-slate-600">{{ t.class }}</td>
                            <td class="py-4 px-6 text-center font-bold text-slate-800">{{ t.hand_eye_score }} / 100</td>
                            <td class="py-4 px-6 text-center">
                                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-rose-50 text-rose-700 border-rose-200 uppercase">
                                    {{ t.fitness_status }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </PetugasLayout>
</template>
