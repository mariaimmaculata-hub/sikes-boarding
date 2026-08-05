<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { CalendarDaysIcon } from '@heroicons/vue/24/outline';

const checkups = ref([
    { id: 1, date: '10 Feb 2026', nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', height: '172 cm', weight: '64 kg', bp: '120/80 mmHg', status: 'Sehat' },
    { id: 2, date: '12 Feb 2026', nis: '2023102', name: 'Rina Agustin', class: 'XI AK 2', height: '160 cm', weight: '50 kg', bp: '110/70 mmHg', status: 'Sehat' }
]);
</script>

<template>
    <PendampingLayout>
        <Head title="Pemeriksaan Berkala" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pemeriksaan Berkala</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Catatan rutin pemeriksaan fisik siswa boarding.</p>
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
                                <th class="py-3 px-6">TB / BB</th>
                                <th class="py-3 px-6">Tensi Darah</th>
                                <th class="py-3 px-6 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="c in checkups" :key="c.id" class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ c.date }}</td>
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">{{ c.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <CalendarDaysIcon class="w-4 h-4 text-slate-400" />
                                        <span>{{ c.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ c.class }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ c.height }} / {{ c.weight }}</td>
                                <td class="py-4 px-6 font-medium text-slate-500 text-xs">{{ c.bp }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200 uppercase tracking-wider">
                                        {{ c.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
