<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon, 
    MagnifyingGlassIcon,
    XMarkIcon
} from '@heroicons/vue/24/outline';

const penyakitList = ref([
    { id: '1', name: 'ISPA', code: 'J06' },
    { id: '2', name: 'Demam', code: 'R50' },
    { id: '3', name: 'Diare', code: 'A09' }
]);

const searchQuery = ref('');
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingIdx = ref(-1);

const form = ref({
    id: '',
    name: '',
    code: ''
});

const filteredPenyakit = computed(() => {
    if (!searchQuery.value) return penyakitList.value;
    const query = searchQuery.value.toLowerCase();
    return penyakitList.value.filter(p => p.name.toLowerCase().includes(query) || p.code.toLowerCase().includes(query));
});

const openCreateModal = () => {
    isEditing.value = false;
    form.value = { id: (penyakitList.value.length + 1).toString(), name: '', code: '' };
    isModalOpen.value = true;
};

const openEditModal = (penyakit, idx) => {
    isEditing.value = true;
    editingIdx.value = idx;
    form.value = { ...penyakit };
    isModalOpen.value = true;
};

const savePenyakit = () => {
    if (!form.value.name || !form.value.code) {
        alert('Nama Penyakit dan Kode ICD wajib diisi.');
        return;
    }

    if (isEditing.value) {
        penyakitList.value[editingIdx.value] = { ...form.value };
    } else {
        penyakitList.value.push({ ...form.value });
    }
    isModalOpen.value = false;
};

const deletePenyakit = (idx) => {
    if (confirm('Apakah Anda yakin ingin menghapus data penyakit ini?')) {
        penyakitList.value.splice(idx, 1);
    }
};
</script>

<template>
    <PetugasLayout>
        <Head title="Data Obat &amp; Penyakit" />

        <div class="space-y-6">
            <!-- Header section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">Data Obat &amp; Penyakit</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Manajemen katalog obat-obatan dan daftar diagnosis penyakit klinik.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2.5 px-4 rounded-xl shadow-md transition flex items-center justify-center space-x-1.5 self-start sm:self-auto"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah Penyakit</span>
                </button>
            </div>

            <!-- Tab Layout Switcher -->
            <div class="border-b border-slate-200">
                <div class="flex space-x-6 text-sm font-bold">
                    <Link :href="route('petugas.obat.index')" class="text-slate-400 hover:text-slate-600 pb-3 px-1">
                        Katalog Obat
                    </Link>
                    <Link :href="route('petugas.penyakit.index')" class="border-b-2 border-blue-600 text-blue-600 pb-3 px-1">
                        Daftar Diagnosis Penyakit
                    </Link>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div class="relative w-full max-w-xs">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <MagnifyingGlassIcon class="w-4.5 h-4.5" />
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari Kode ICD / Nama Penyakit..." 
                            v-model="searchQuery"
                            class="pl-9 pr-4 py-2 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        />
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3.5 px-6">ID</th>
                                <th class="py-3.5 px-6">Nama Penyakit</th>
                                <th class="py-3.5 px-6">Kode ICD</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(p, idx) in filteredPenyakit" :key="idx" class="border-b border-slate-100 hover:bg-slate-50/50 transition text-sm">
                                <td class="py-4 px-6 font-bold text-slate-400 text-xs">#{{ p.id }}</td>
                                <td class="py-4 px-6 font-extrabold text-blue-900">{{ p.name }}</td>
                                <td class="py-4 px-6"><span class="bg-slate-100 text-slate-700 border border-slate-200 text-xs font-bold px-2.5 py-0.5 rounded-md">{{ p.code }}</span></td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button @click="openEditModal(p, idx)" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg inline-flex" title="Edit">
                                        <PencilSquareIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="deletePenyakit(idx)" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg inline-flex" title="Hapus">
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
                    <span class="text-sm font-extrabold text-slate-800">{{ isEditing ? 'Edit Data Penyakit' : 'Tambah Penyakit Baru' }}</span>
                    <button @click="isModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100">
                        <XMarkIcon class="w-5 h-5" />
                    </button>
                </div>
                <form @submit.prevent="savePenyakit" class="p-6 space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Nama Penyakit</label>
                        <input type="text" v-model="form.name" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Kode ICD</label>
                        <input type="text" v-model="form.code" required class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. J06, R50" />
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
