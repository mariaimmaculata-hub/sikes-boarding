<script setup>
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon, 
    MagnifyingGlassIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';

const siswaList = ref([
    { nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', dorm: 'Asrama Putra', gender: 'Laki-laki' },
    { nis: '2024002', name: 'Aulia Rahma', class: 'X AKL 2', dorm: 'Asrama Putri', gender: 'Perempuan' },
    { nis: '2023102', name: 'Nadia Putri', class: 'XI TKJ 1', dorm: 'Asrama Putri', gender: 'Perempuan' },
    { nis: '2022203', name: 'Dimas Saputra', class: 'XII TAV 1', dorm: 'Asrama Putra', gender: 'Laki-laki' }
]);

const searchQuery = ref('');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingIdx = ref(-1);

const form = ref({
    nis: '',
    name: '',
    class: 'X TKJ 1',
    dorm: 'Asrama Putra',
    gender: 'Laki-laki'
});

const filteredSiswa = computed(() => {
    if (!searchQuery.value) return siswaList.value;
    const query = searchQuery.value.toLowerCase();
    return siswaList.value.filter(s => s.name.toLowerCase().includes(query) || s.nis.includes(query));
});

const openCreateModal = () => {
    isEditing.value = false;
    form.value = { nis: '', name: '', class: 'X TKJ 1', dorm: 'Asrama Putra', gender: 'Laki-laki' };
    isModalOpen.value = true;
};

const openEditModal = (siswa, idx) => {
    isEditing.value = true;
    editingIdx.value = idx;
    form.value = { ...siswa };
    isModalOpen.value = true;
};

const saveSiswa = () => {
    if (!form.value.nis || !form.value.name) {
        alert('NIS dan Nama wajib diisi.');
        return;
    }

    if (isEditing.value) {
        siswaList.value[editingIdx.value] = { ...form.value };
    } else {
        siswaList.value.push({ ...form.value });
    }
    isModalOpen.value = false;
};

const deleteSiswa = (idx) => {
    if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
        siswaList.value.splice(idx, 1);
    }
};
</script>

<template>
    <PetugasLayout>
        <Head title="Data Siswa" />

        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">Data Siswa</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Manajemen daftar semua siswa boarding.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center space-x-1.5 self-start sm:self-auto"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah Siswa</span>
                </button>
            </div>

            <!-- Table & Search Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="relative w-full max-w-xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <MagnifyingGlassIcon class="w-4.5 h-4.5" />
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari NIS / Nama Siswa..." 
                            v-model="searchQuery"
                            class="pl-9 pr-4 py-2 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3.5 px-6">NIS</th>
                                <th class="py-3.5 px-6">Nama</th>
                                <th class="py-3.5 px-6">Kelas</th>
                                <th class="py-3.5 px-6">Asrama</th>
                                <th class="py-3.5 px-6">Jenis Kelamin</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s, idx) in filteredSiswa" :key="idx" class="border-b border-slate-100 hover:bg-slate-50/50 transition text-sm">
                                <td class="py-4 px-6 font-bold text-slate-500 text-xs">{{ s.nis }}</td>
                                <td class="py-4 px-6 font-extrabold text-blue-900">{{ s.name }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-600">{{ s.class }}</td>
                                <td class="py-4 px-6 text-slate-500 text-xs font-semibold">{{ s.dorm }}</td>
                                <td class="py-4 px-6 text-slate-600 font-medium text-xs">{{ s.gender }}</td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button @click="openEditModal(s, idx)" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg inline-flex" title="Edit">
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteSiswa(idx)" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg inline-flex" title="Hapus">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Custom CRUD Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-150 flex items-center justify-between">
                    <span class="text-sm font-extrabold text-slate-800">{{ isEditing ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</span>
                    <button @click="isModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100">
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
                <form @submit.prevent="saveSiswa" class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">NIS</label>
                        <input type="text" v-model="form.nis" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nama Lengkap</label>
                        <input type="text" v-model="form.name" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Kelas</label>
                        <input type="text" v-model="form.class" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Asrama</label>
                        <input type="text" v-model="form.dorm" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Jenis Kelamin</label>
                        <select v-model="form.gender" class="w-full border border-slate-300 rounded-xl px-2.5 py-2 text-sm">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-2 pt-4 border-t border-slate-100">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md">{{ isEditing ? 'Simpan' : 'Tambah' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </PetugasLayout>
</template>
