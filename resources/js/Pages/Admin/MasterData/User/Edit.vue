<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

const roles = [
    { id: 'admin', name: 'Admin' },
    { id: 'petugas', name: 'Petugas Klinik' },
    { id: 'pendamping', name: 'Pendamping' }
];

// Mock query logic based on ID
const form = useForm({
    id: props.id,
    name: props.id === '1' ? 'Admin SiKes' : (props.id === '2' ? 'Petugas Klinik' : 'Pendamping Siswa'),
    email: props.id === '1' ? 'admin@sekolah.id' : (props.id === '2' ? 'petugas@sekolah.id' : 'pendamping@sekolah.id'),
    role: props.id === '1' ? 'admin' : (props.id === '2' ? 'petugas' : 'pendamping'),
    password: '' // Keep empty to indicate unmodified password
});

const submit = () => {
    alert(`Pengguna ${form.name} berhasil diperbarui!`);
    window.history.back();
};
</script>

<template>
    <AdminLayout>
        <Head title="Ubah User" />

        <div class="space-y-6 max-w-xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.master.user.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Ubah User</h2>
                    <p class="text-xs text-slate-500 font-medium">Perbarui detail akun pengguna.</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    
                    <!-- Nama Pengguna -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                        <input 
                            id="name" 
                            type="text" 
                            v-model="form.name"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Email / Username -->
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email / Username</label>
                        <input 
                            id="email" 
                            type="email" 
                            v-model="form.email"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Peran / Role -->
                    <div class="space-y-1.5">
                        <label for="role" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Peran (Role)</label>
                        <select 
                            id="role" 
                            v-model="form.role"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        >
                            <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
                        </select>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1.5">
                        <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Ubah Kata Sandi (Opsional)</label>
                        <input 
                            id="password" 
                            type="password" 
                            placeholder="Biarkan kosong jika tidak diubah"
                            v-model="form.password"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Buttons Group -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link 
                            :href="route('admin.master.user.index')"
                            class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition"
                        >
                            Batal
                        </Link>
                        <button 
                            type="submit"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                        >
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
