<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    EyeIcon, 
    MagnifyingGlassIcon,
    CalendarDaysIcon
} from '@heroicons/vue/24/outline';

// Mock Pemeriksaan Berkala data
const checkups = ref([
    { id: 1, date: '10 Feb 2026', nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', height: '172 cm', weight: '64 kg', bp: '120/80 mmHg', status: 'Sehat', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 2, date: '12 Feb 2026', nis: '2023102', name: 'Rina Agustin', class: 'XI AK 2', height: '160 cm', weight: '50 kg', bp: '110/70 mmHg', status: 'Sehat', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 3, date: '15 Feb 2026', nis: '2022203', name: 'Dimas Saputra', class: 'XII TAV 1', height: '175 cm', weight: '68 kg', bp: '130/85 mmHg', status: 'Pemantauan', status_style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { id: 4, date: '18 Feb 2026', nis: '2023150', name: 'Siti Nurhaliza', class: 'XI AK 2', height: '158 cm', weight: '48 kg', bp: '105/65 mmHg', status: 'Sehat', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' }
]);

const searchQuery = ref('');

const filteredCheckups = computed(() => {
    if (!searchQuery.value) return checkups.value;
    return checkups.value.filter(c => c.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || c.nis.includes(searchQuery.value));
});
</script>

<template>
    <AdminLayout>
        <Head title="Pemeriksaan Berkala" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pemeriksaan Berkala</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Lihat catatan pemeriksaan kesehatan fisik rutin berkala siswa boarding.</p>
            </div>

            <!-- Card table -->
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
                                <th class="py-3 px-6">Tanggal</th>
                                <th class="py-3 px-6">NIS</th>
                                <th class="py-3 px-6">Nama Siswa</th>
                                <th class="py-3 px-6">Kelas</th>
                                <th class="py-3 px-6">TB / BB</th>
                                <th class="py-3 px-6">Tekanan Darah</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="log in filteredCheckups" 
                                :key="log.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ log.date }}</td>
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">{{ log.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <CalendarDaysIcon class="w-4.5 h-4.5 text-slate-400" />
                                        <span>{{ log.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ log.class }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ log.height }} / {{ log.weight }}</td>
                                <td class="py-4 px-6 font-medium text-slate-500 text-xs">{{ log.bp }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border uppercase tracking-wider" :class="log.status_style">
                                        {{ log.status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.pemeriksaan.show', log.id)"
                                        class="inline-flex p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                        title="Detail Pemeriksaan"
                                    >
                                        <EyeIcon class="w-4.5 h-4.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredCheckups.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Tidak ditemukan data pemeriksaan berkala.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
