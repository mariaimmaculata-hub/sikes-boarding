<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon, 
    MagnifyingGlassIcon,
    AcademicCapIcon
} from '@heroicons/vue/24/outline';

// Mock Jurusan Data
const departments = ref([
    { id: 1, code: 'RPL', name: 'Rekayasa Perangkat Lunak', total_classes: 6 },
    { id: 2, code: 'TKJ', name: 'Teknik Komputer & Jaringan', total_classes: 6 },
    { id: 3, code: 'AK', name: 'Akuntansi & Keuangan Lembaga', total_classes: 6 },
    { id: 4, code: 'TAV', name: 'Teknik Audio Video', total_classes: 6 },
    { id: 5, code: 'TPM', name: 'Teknik Pemesinan', total_classes: 6 },
    { id: 6, code: 'NKPI', name: 'Nautika Kapal Penangkap Ikan', total_classes: 6 }
]);

const searchQuery = ref('');

const deleteDepartment = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus jurusan ini?')) {
        departments.value = departments.value.filter(d => d.id !== id);
    }
};
</script>

<template>
    <AdminLayout>
        <Head title="Data Jurusan" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Data Jurusan</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Kelola data kompetensi keahlian / jurusan siswa boarding.</p>
                </div>
                <Link 
                    :href="route('admin.master.jurusan.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-1.5 self-start sm:self-center"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah Jurusan</span>
                </Link>
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
                            placeholder="Cari kode/nama jurusan..." 
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
                                <th class="py-3 px-6">Kode Jurusan</th>
                                <th class="py-3 px-6">Nama Jurusan</th>
                                <th class="py-3 px-6 text-center">Jumlah Kelas</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="dept in departments.filter(d => d.name.toLowerCase().includes(searchQuery.toLowerCase()) || d.code.toLowerCase().includes(searchQuery.toLowerCase()))" 
                                :key="dept.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-extrabold text-blue-900 text-sm">{{ dept.code }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-700 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <AcademicCapIcon class="w-4.5 h-4.5 text-slate-400" />
                                        <span>{{ dept.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-center text-sm font-bold text-slate-600">{{ dept.total_classes }} Kelas</td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.master.jurusan.edit', dept.id)"
                                        class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Ubah"
                                    >
                                        <PencilSquareIcon class="w-4.5 h-4.5" />
                                    </Link>
                                    <button 
                                        @click="deleteDepartment(dept.id)"
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
                <div v-if="departments.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Belum ada data jurusan.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
