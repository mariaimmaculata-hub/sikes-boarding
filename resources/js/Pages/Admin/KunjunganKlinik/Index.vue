<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    EyeIcon, 
    MagnifyingGlassIcon,
    HeartIcon
} from '@heroicons/vue/24/outline';

// Mock Kunjungan Klinik
const visits = ref([
    { id: 1, date: '04 Mar 2026', nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', complaints: 'Demam tinggi, batuk berdahak', diagnosis: 'ISPA', therapy: 'Paracetamol 500mg, Ambroxol, Vitamin C', staff: 'Petugas Klinik' },
    { id: 2, date: '08 Mar 2026', nis: '2023102', name: 'Rina Agustin', class: 'XI AK 2', complaints: 'Nyeri perut sebelah kiri bawah', diagnosis: 'Dispepsia (Maag)', therapy: 'Antasida, Domperidone', staff: 'Petugas Klinik' },
    { id: 3, date: '10 Mar 2026', nis: '2022203', name: 'Dimas Saputra', class: 'XII TAV 1', complaints: 'Sakit kepala, pusing berputar', diagnosis: 'Sakit Kepala Ketegangan', therapy: 'Ibuprofen 400mg, Istirahat 2 Jam', staff: 'Dr. Handoko' }
]);

const searchQuery = ref('');

const filteredVisits = computed(() => {
    if (!searchQuery.value) return visits.value;
    return visits.value.filter(v => v.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || v.nis.includes(searchQuery.value) || v.diagnosis.toLowerCase().includes(searchQuery.value.toLowerCase()));
});
</script>

<template>
    <AdminLayout>
        <Head title="Kunjungan Klinik" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Kunjungan Klinik</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Pantau catatan kunjungan harian, keluhan medis, dan pengobatan siswa boarding.</p>
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
                            placeholder="Cari Nama/NIS/Diagnosa..." 
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
                                <th class="py-3 px-6">Keluhan</th>
                                <th class="py-3 px-6">Diagnosa</th>
                                <th class="py-3 px-6">Terapi / Obat</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="visit in filteredVisits" 
                                :key="visit.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ visit.date }}</td>
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">{{ visit.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <HeartIcon class="w-4.5 h-4.5 text-rose-500" />
                                        <span>{{ visit.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ visit.class }}</td>
                                <td class="py-4 px-6 text-slate-600 text-xs max-w-xs truncate">{{ visit.complaints }}</td>
                                <td class="py-4 px-6">
                                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 border border-rose-100 uppercase tracking-wide">
                                        {{ visit.diagnosis }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-500 text-xs truncate max-w-xs">{{ visit.therapy }}</td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.kunjungan.show', visit.id)"
                                        class="inline-flex p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                        title="Detail Kunjungan"
                                    >
                                        <EyeIcon class="w-4.5 h-4.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredVisits.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Tidak ditemukan data kunjungan klinik.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
