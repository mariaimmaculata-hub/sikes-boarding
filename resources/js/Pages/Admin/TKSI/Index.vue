<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    EyeIcon, 
    MagnifyingGlassIcon,
    BoltIcon
} from '@heroicons/vue/24/outline';

// Mock TKSI logs
const tksiRecords = ref([
    { id: 1, date: '10 Feb 2026', nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', hand_eye_score: '85', agility_score: 'Baik', fitness_status: 'Sangat Baik', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 2, date: '14 Feb 2026', nis: '2023102', name: 'Rina Agustin', class: 'XI AK 2', hand_eye_score: '72', agility_score: 'Cukup', fitness_status: 'Cukup', status_style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { id: 3, date: '18 Feb 2026', nis: '2022203', name: 'Dimas Saputra', class: 'XII TAV 1', hand_eye_score: '80', agility_score: 'Baik', fitness_status: 'Baik', status_style: 'bg-blue-50 text-blue-700 border-blue-200' }
]);

const searchQuery = ref('');

const filteredTksi = computed(() => {
    if (!searchQuery.value) return tksiRecords.value;
    return tksiRecords.value.filter(t => t.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || t.nis.includes(searchQuery.value));
});
</script>

<template>
    <AdminLayout>
        <Head title="Tes Kebugaran (TKSI)" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Tes Kebugaran &amp; Koordinasi (TKSI)</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Lihat riwayat tes koordinasi mata-tangan dan skor jasmani kebugaran siswa boarding.</p>
            </div>

            <!-- Card Wrap -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                <!-- Search & Filters -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="relative w-full sm:max-w-xs">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <MagnifyingGlassIcon class="w-4.5 h-4.5" />
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari Nama / NIS..." 
                            v-model="searchQuery"
                            class="pl-10 pr-4 py-2 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6">Tanggal Tes</th>
                                <th class="py-3 px-6">NIS</th>
                                <th class="py-3 px-6">Nama Siswa</th>
                                <th class="py-3 px-6">Kelas</th>
                                <th class="py-3 px-6 text-center">Skor Koordinasi Mata-Tangan</th>
                                <th class="py-3 px-6 text-center">Kelincahan Jasmani</th>
                                <th class="py-3 px-6 text-center">Status Kebugaran</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="t in filteredTksi" 
                                :key="t.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ t.date }}</td>
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">{{ t.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <BoltIcon class="w-4.5 h-4.5 text-emerald-500" />
                                        <span>{{ t.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ t.class }}</td>
                                <td class="py-4 px-6 text-center font-bold text-slate-800 text-sm">{{ t.hand_eye_score }} / 100</td>
                                <td class="py-4 px-6 text-center font-semibold text-slate-600 text-sm">{{ t.agility_score }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border uppercase tracking-wider" :class="t.status_style">
                                        {{ t.fitness_status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.tksi.show', t.id)"
                                        class="inline-flex p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                        title="Detail Tes"
                                    >
                                        <EyeIcon class="w-4.5 h-4.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredTksi.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Tidak ditemukan data tes kebugaran siswa.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
