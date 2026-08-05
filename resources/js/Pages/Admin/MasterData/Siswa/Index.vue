<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    EyeIcon, 
    MagnifyingGlassIcon,
    UserIcon
} from '@heroicons/vue/24/outline';

// Mock Jurusan & Kelas list
const departments = [
    { id: 'ALL', name: 'Semua Jurusan' },
    { id: 'RPL', name: 'Rekayasa Perangkat Lunak' },
    { id: 'TKJ', name: 'Teknik Komputer & Jaringan' },
    { id: 'AK', name: 'Akuntansi & Keuangan' }
];

const classes = {
    'ALL': [{ id: 'ALL', name: 'Semua Kelas' }],
    'RPL': [
        { id: 'ALL', name: 'Semua Kelas' },
        { id: 'X RPL 1', name: 'X RPL 1' },
        { id: 'XI RPL 1', name: 'XI RPL 1' },
        { id: 'XII RPL 1', name: 'XII RPL 1' }
    ],
    'TKJ': [
        { id: 'ALL', name: 'Semua Kelas' },
        { id: 'X TKJ 1', name: 'X TKJ 1' },
        { id: 'XI TKJ 1', name: 'XI TKJ 1' },
        { id: 'XII TKJ 1', name: 'XII TKJ 1' }
    ],
    'AK': [
        { id: 'ALL', name: 'Semua Kelas' },
        { id: 'X AK 2', name: 'X AK 2' },
        { id: 'XI AK 2', name: 'XI AK 2' },
        { id: 'XII AK 2', name: 'XII AK 2' }
    ]
};

// Mock Siswa
const students = ref([
    { id: 1, nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', dept: 'TKJ', dorm: 'Asrama Putra - Kamar 5', status: 'Sering Sakit', status_style: 'bg-rose-50 text-rose-700 border-rose-200' },
    { id: 2, nis: '2023102', name: 'Rina Agustin', class: 'XI AK 2', dept: 'AK', dorm: 'Asrama Putri - Kamar 12', status: 'Pemantauan', status_style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { id: 3, nis: '2022203', name: 'Dimas Saputra', class: 'XII TAV 1', dept: 'TKJ', dorm: 'Asrama Putra - Kamar 1', status: 'Pemantauan', status_style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { id: 4, nis: '2023150', name: 'Siti Nurhaliza', class: 'XI AK 2', dept: 'AK', dorm: 'Asrama Putri - Kamar 8', status: 'Sehat', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 5, nis: '2024045', name: 'Budi Santoso', class: 'X RPL 1', dept: 'RPL', dorm: 'Asrama Putra - Kamar 4', status: 'Sehat', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' }
]);

// Filters State
const selectedDept = ref('ALL');
const selectedClass = ref('ALL');
const searchQuery = ref('');

// Computed classes option based on selected department
const classOptions = computed(() => {
    return classes[selectedDept.value] || [{ id: 'ALL', name: 'Semua Kelas' }];
});

// Watch department change to reset class filter
const onDeptChange = () => {
    selectedClass.value = 'ALL';
};

const filteredStudents = computed(() => {
    return students.value.filter(s => {
        const matchesDept = selectedDept.value === 'ALL' || s.dept === selectedDept.value;
        const matchesClass = selectedClass.value === 'ALL' || s.class === selectedClass.value;
        const matchesSearch = s.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || s.nis.includes(searchQuery.value);
        return matchesDept && matchesClass && matchesSearch;
    });
});
</script>

<template>
    <AdminLayout>
        <Head title="Data Siswa" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Data Siswa</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Kelola data biodata, asrama, dan status kesehatan siswa boarding.</p>
                </div>
                <Link 
                    :href="route('admin.master.siswa.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-1.5 self-start sm:self-center"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah Siswa</span>
                </Link>
            </div>

            <!-- Table & Filters Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                
                <!-- Filter Section -->
                <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 w-full md:max-w-2xl">
                        <!-- Select Jurusan -->
                        <div class="flex items-center space-x-2">
                            <select 
                                v-model="selectedDept"
                                @change="onDeptChange"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                            >
                                <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                            </select>
                        </div>
                        
                        <!-- Select Kelas -->
                        <div class="flex items-center space-x-2">
                            <select 
                                v-model="selectedClass"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                            >
                                <option v-for="c in classOptions" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>

                        <!-- Search Box -->
                        <div class="relative w-full">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <MagnifyingGlassIcon class="w-4 h-4" />
                            </span>
                            <input 
                                type="text" 
                                placeholder="Cari Nama / NIS..." 
                                v-model="searchQuery"
                                class="pl-9 pr-4 py-2 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                            />
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6">NIS</th>
                                <th class="py-3 px-6">Nama Siswa</th>
                                <th class="py-3 px-6">Kelas</th>
                                <th class="py-3 px-6">Asrama</th>
                                <th class="py-3 px-6 text-center">Status Kesehatan</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="student in filteredStudents" 
                                :key="student.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ student.nis }}</td>
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-7.5 h-7.5 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 border border-slate-200">
                                            <UserIcon class="w-4.5 h-4.5" />
                                        </div>
                                        <span>{{ student.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ student.class }}</td>
                                <td class="py-4 px-6 font-medium text-slate-500 text-xs">{{ student.dorm }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border tracking-wide uppercase" :class="student.status_style">
                                        {{ student.status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.master.siswa.show', student.id)"
                                        class="inline-flex p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition"
                                        title="Detail Profile"
                                    >
                                        <EyeIcon class="w-4.5 h-4.5" />
                                    </Link>
                                    <Link 
                                        :href="route('admin.master.siswa.edit', student.id)"
                                        class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Ubah"
                                    >
                                        <PencilSquareIcon class="w-4.5 h-4.5" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredStudents.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Tidak ditemukan data siswa yang cocok dengan filter.
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
