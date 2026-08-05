<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { BellIcon, ClockIcon } from '@heroicons/vue/24/outline';

const tasks = ref([
    { id: 1, time: '08:00 - 10:00', title: 'Pemeriksaan Kelas X TKJ 1', status: 'Selesai', style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 2, time: '13:00 - 15:00', title: 'Pemeriksaan Kelas XI AK 2', status: 'Akan Datang', style: 'bg-blue-50 text-blue-700 border-blue-200' }
]);
</script>

<template>
    <PendampingLayout>
        <Head title="Pengingat &amp; Tugas" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengingat &amp; Tugas</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Daftar agenda kegiatan kesehatan pendamping boarding.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 space-y-6">
                <div class="space-y-4">
                    <div v-for="t in tasks" :key="t.id" class="flex items-center justify-between border border-slate-100 rounded-2xl p-4 hover:bg-slate-50 transition">
                        <div class="flex space-x-3.5 items-start">
                            <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                                <ClockIcon class="w-5 h-5" />
                            </div>
                            <div class="space-y-0.5">
                                <span class="text-xs font-bold text-slate-800 block">{{ t.title }}</span>
                                <span class="text-[10px] text-slate-400 font-bold block">{{ t.time }}</span>
                            </div>
                        </div>
                        <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border uppercase tracking-wider" :class="t.style">
                            {{ t.status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
