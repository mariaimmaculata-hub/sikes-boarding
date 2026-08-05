<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { HeartIcon } from '@heroicons/vue/24/outline';

const visits = ref([
    { id: 1, date: '04 Mar 2026', nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', complaints: 'Demam tinggi, batuk', diagnosis: 'ISPA', therapy: 'Paracetamol 500mg' }
]);
</script>

<template>
    <PendampingLayout>
        <Head title="Kunjungan Klinik" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Kunjungan Klinik</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Log kunjungan medis harian siswa boarding ke klinik.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6">Tanggal</th>
                                <th class="py-3 px-6">NIS</th>
                                <th class="py-3 px-6">Nama Siswa</th>
                                <th class="py-3 px-6">Kelas</th>
                                <th class="py-3 px-6">Keluhan</th>
                                <th class="py-3 px-6">Diagnosa</th>
                                <th class="py-3 px-6">Terapi / Obat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in visits" :key="v.id" class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ v.date }}</td>
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">{{ v.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <HeartIcon class="w-4 h-4 text-rose-500" />
                                        <span>{{ v.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ v.class }}</td>
                                <td class="py-4 px-6 text-slate-600 text-xs max-w-xs truncate">{{ v.complaints }}</td>
                                <td class="py-4 px-6">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-rose-50 text-rose-700 border-rose-200 uppercase tracking-wider">
                                        {{ v.diagnosis }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs truncate max-w-xs">{{ v.therapy }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
