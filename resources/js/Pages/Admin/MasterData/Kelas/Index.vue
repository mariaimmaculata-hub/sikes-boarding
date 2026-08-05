<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon, 
    ClipboardDocumentListIcon 
} from '@heroicons/vue/24/outline';

// Mock Jurusan
const departments = [
    { id: 'ALL', name: 'Semua Jurusan' },
    { id: 'RPL', name: 'Rekayasa Perangkat Lunak' },
    { id: 'TKJ', name: 'Teknik Komputer & Jaringan' },
    { id: 'AK', name: 'Akuntansi & Keuangan Lembaga' }
];

// Mock Kelas Data
const classes = ref([
    { id: 1, name: 'X RPL 1', level: 'X', department: 'RPL', total_students: 36 },
    { id: 2, name: 'X RPL 2', level: 'X', department: 'RPL', total_students: 36 },
    { id: 3, name: 'XI RPL 1', level: 'XI', department: 'RPL', total_students: 34 },
    { id: 4, name: 'XII RPL 1', level: 'XII', department: 'RPL', total_students: 32 },
    
    { id: 5, name: 'X TKJ 1', level: 'X', department: 'TKJ', total_students: 36 },
    { id: 6, name: 'XI TKJ 1', level: 'XI', department: 'TKJ', total_students: 35 },
    { id: 7, name: 'XII TKJ 1', level: 'XII', department: 'TKJ', total_students: 34 },
    
    { id: 8, name: 'X AK 1', level: 'X', department: 'AK', total_students: 36 },
    { id: 9, name: 'XI AK 1', level: 'XI', department: 'AK', total_students: 36 },
    { id: 10, name: 'XII AK 1', level: 'XII', department: 'AK', total_students: 36 }
]);

// Filter State
const selectedDept = ref('ALL');

const filteredClasses = computed(() => {
    if (selectedDept.value === 'ALL') return classes.value;
    return classes.value.filter(c => c.department === selectedDept.value);
});

const deleteClass = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
        classes.value = classes.value.filter(c => c.id !== id);
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Data Kelas" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Data Kelas</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Kelola data kelas siswa boarding berdasarkan jurusan.</p>
                </div>
                <Link 
                    :href="route('admin.master.kelas.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-1.5 self-start sm:self-center"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah Kelas</span>
                </Link>
            </div>

            <!-- Card Wrap -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                <!-- Dropdown Filters -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2.5 w-full sm:max-w-xs">
                        <label for="dept-filter" class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Filter Jurusan:</label>
                        <select 
                            id="dept-filter" 
                            v-model="selectedDept"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6">Nama Kelas</th>
                                <th class="py-3 px-6">Tingkat</th>
                                <th class="py-3 px-6">Jurusan</th>
                                <th class="py-3 px-6 text-center">Jumlah Siswa</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="cls in filteredClasses" 
                                :key="cls.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-extrabold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <ClipboardDocumentListIcon class="w-4.5 h-4.5 text-slate-400" />
                                        <span>{{ cls.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">Kelas {{ cls.level }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ cls.department }}</td>
                                <td class="py-4 px-6 text-center text-sm font-bold text-slate-600">{{ cls.total_students }} Siswa</td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.master.kelas.edit', cls.id)"
                                        class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Ubah"
                                    >
                                        <PencilSquareIcon class="w-4.5 h-4.5" />
                                    </Link>
                                    <button 
                                        @click="deleteClass(cls.id)"
                                        class="inline-flex p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                        title="Hapus"
                                    >
                                        <TrashIcon class="w-4.5 h-4.5" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-if="filteredClasses.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Belum ada data kelas untuk jurusan yang dipilih.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
