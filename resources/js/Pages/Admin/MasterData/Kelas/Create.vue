<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const departments = [
    { id: 'RPL', name: 'Rekayasa Perangkat Lunak' },
    { id: 'TKJ', name: 'Teknik Komputer & Jaringan' },
    { id: 'AK', name: 'Akuntansi & Keuangan Lembaga' }
];

const levels = ['X', 'XI', 'XII'];

const form = useForm({
    name: '',
    level: 'X',
    department: 'RPL'
});

const submit = () => {
    alert(`Kelas ${form.name} berhasil disimpan!`);
    window.history.back();
};
</script>

<template>
    <AdminLayout>
        <Head title="Tambah Kelas" />

        <div class="space-y-6 max-w-xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.master.kelas.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Kelas</h2>
                    <p class="text-xs text-slate-500 font-medium">Buat rombel / kelas siswa baru.</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    
                    <!-- Nama Kelas -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Kelas</label>
                        <input 
                            id="name" 
                            type="text" 
                            placeholder="Contoh: X RPL 1"
                            v-model="form.name"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Tingkat -->
                    <div class="space-y-1.5">
                        <label for="level" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tingkat</label>
                        <select 
                            id="level" 
                            v-model="form.level"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="lvl in levels" :key="lvl" :value="lvl">Kelas {{ lvl }}</option>
                        </select>
                    </div>

                    <!-- Jurusan -->
                    <div class="space-y-1.5">
                        <label for="dept" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jurusan</label>
                        <select 
                            id="dept" 
                            v-model="form.department"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="d in departments" :key="d.id" :value="d.id">{{ d.name }}</option>
                        </select>
                    </div>

                    <!-- Buttons Group -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link 
                            :href="route('admin.master.kelas.index')"
                            class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition"
                        >
                            Batal
                        </Link>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                        >
                            Simpan Kelas
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
