<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const classes = ['X RPL 1', 'XI RPL 1', 'XII RPL 1', 'X TKJ 1', 'XI TKJ 1', 'XII TKJ 1', 'X AK 2', 'XI AK 2', 'XII AK 2'];
const dorms = ['Asrama Putra - Kamar 1', 'Asrama Putra - Kamar 2', 'Asrama Putra - Kamar 5', 'Asrama Putri - Kamar 8', 'Asrama Putri - Kamar 12'];
const healthStatuses = ['Sehat', 'Pemantauan', 'Sering Sakit'];

const form = useForm({
    nis: '',
    name: '',
    class: 'X RPL 1',
    dorm: 'Asrama Putra - Kamar 1',
    status: 'Sehat'
});

const submit = () => {
    alert(`Siswa ${form.name} (NIS: ${form.nis}) berhasil ditambahkan!`);
    window.history.back();
};
</script>

<template>
    <AdminLayout>
        <Head title="Tambah Siswa" />

        <div class="space-y-6 max-w-xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.master.siswa.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Tambah Siswa</h2>
                    <p class="text-xs text-slate-500 font-medium">Daftarkan profil siswa boarding baru.</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    
                    <!-- NIS -->
                    <div class="space-y-1.5">
                        <label for="nis" class="text-xs font-bold text-slate-700 uppercase tracking-wider">NIS (Nomor Induk Siswa)</label>
                        <input 
                            id="nis" 
                            type="text" 
                            placeholder="Contoh: 2024001"
                            v-model="form.nis"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                        <input 
                            id="name" 
                            type="text" 
                            placeholder="Contoh: Ahmad Fauzi"
                            v-model="form.name"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Kelas -->
                    <div class="space-y-1.5">
                        <label for="class" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kelas</label>
                        <select 
                            id="class" 
                            v-model="form.class"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>

                    <!-- Asrama -->
                    <div class="space-y-1.5">
                        <label for="dorm" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Asrama / Kamar</label>
                        <select 
                            id="dorm" 
                            v-model="form.dorm"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="d in dorms" :key="d" :value="d">{{ d }}</option>
                        </select>
                    </div>

                    <!-- Status Kesehatan -->
                    <div class="space-y-1.5">
                        <label for="status" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Status Kesehatan Awal</label>
                        <select 
                            id="status" 
                            v-model="form.status"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="s in healthStatuses" :key="s" :value="s">{{ s }}</option>
                        </select>
                    </div>

                    <!-- Buttons Group -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link 
                            :href="route('admin.master.siswa.index')"
                            class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition"
                        >
                            Batal
                        </Link>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                        >
                            Simpan Siswa
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
