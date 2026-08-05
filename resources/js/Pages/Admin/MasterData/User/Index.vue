<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    PlusIcon, 
    PencilSquareIcon, 
    TrashIcon, 
    MagnifyingGlassIcon,
    UserIcon,
    ShieldCheckIcon,
    HeartIcon
} from '@heroicons/vue/24/outline';

// Mock Users Data
const users = ref([
    { id: 1, name: 'Admin SiKes', email: 'admin@sekolah.id', role: 'admin', status: 'Aktif', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 2, name: 'Petugas Klinik', email: 'petugas@sekolah.id', role: 'petugas', status: 'Aktif', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 3, name: 'Pendamping Siswa', email: 'pendamping@sekolah.id', role: 'pendamping', status: 'Aktif', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 4, name: 'Dr. Handoko', email: 'handoko@sekolah.id', role: 'petugas', status: 'Aktif', status_style: 'bg-emerald-50 text-emerald-700 border-emerald-200' },
    { id: 5, name: 'Suhartono, S.Pd', email: 'suhartono@sekolah.id', role: 'pendamping', status: 'Non-Aktif', status_style: 'bg-slate-50 text-slate-500 border-slate-200' }
]);

const selectedRole = ref('ALL');
const searchQuery = ref('');

const filteredUsers = computed(() => {
    return users.value.filter(u => {
        const matchesRole = selectedRole.value === 'ALL' || u.role === selectedRole.value;
        const matchesSearch = u.name.toLowerCase().includes(searchQuery.value.toLowerCase()) || u.email.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesRole && matchesSearch;
    });
});

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        users.value = users.value.filter(u => u.id !== id);
    }
};

const getRoleBadge = (role) => {
    if (role === 'admin') return 'bg-purple-50 text-purple-700 border-purple-200';
    if (role === 'petugas') return 'bg-blue-50 text-blue-700 border-blue-200';
    return 'bg-green-50 text-green-700 border-green-200';
};
</script>

<template>
    <AdminLayout>
        <Head title="Data User" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Data User</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Kelola hak akses pengguna sistem (Admin, Petugas Klinik, Pendamping).</p>
                </div>
                <Link 
                    :href="route('admin.master.user.create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-md hover:shadow-lg transition-all flex items-center justify-center space-x-1.5 self-start sm:self-center"
                >
                    <PlusIcon class="w-4 h-4" />
                    <span>Tambah User</span>
                </Link>
            </div>

            <!-- Table & Filters Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 overflow-hidden">
                
                <!-- Filters Section -->
                <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
                        <!-- Role Filter -->
                        <div class="flex items-center space-x-2 w-full sm:max-w-xs">
                            <label for="role-filter" class="text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap">Filter Peran:</label>
                            <select 
                                id="role-filter"
                                v-model="selectedRole"
                                class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                            >
                                <option value="ALL">Semua Peran</option>
                                <option value="admin">Admin</option>
                                <option value="petugas">Petugas Klinik</option>
                                <option value="pendamping">Pendamping</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div class="relative w-full">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <MagnifyingGlassIcon class="w-4.5 h-4.5" />
                            </span>
                            <input 
                                type="text" 
                                placeholder="Cari nama / email..." 
                                v-model="searchQuery"
                                class="pl-10 pr-4 py-2 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                            />
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6">Nama Pengguna</th>
                                <th class="py-3 px-6">Email / Username</th>
                                <th class="py-3 px-6">Peran (Role)</th>
                                <th class="py-3 px-6 text-center">Status</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="user in filteredUsers" 
                                :key="user.id" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition cursor-pointer"
                            >
                                <td class="py-4 px-6 font-bold text-blue-900 text-sm">
                                    <div class="flex items-center space-x-2.5">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200">
                                            <UserIcon class="w-4 h-4" />
                                        </div>
                                        <span>{{ user.name }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-600 text-sm">{{ user.email }}</td>
                                <td class="py-4 px-6">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border uppercase tracking-wider" :class="getRoleBadge(user.role)">
                                        {{ user.role === 'admin' ? 'Admin' : (user.role === 'petugas' ? 'Petugas Klinik' : 'Pendamping') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border uppercase tracking-wider" :class="user.status_style">
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2" @click.stop>
                                    <Link 
                                        :href="route('admin.master.user.edit', user.id)"
                                        class="inline-flex p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Ubah"
                                    >
                                        <PencilSquareIcon class="w-4.5 h-4.5" />
                                    </Link>
                                    <button 
                                        v-if="user.id !== 1"
                                        @click="deleteUser(user.id)"
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
                <div v-if="filteredUsers.length === 0" class="py-12 text-center text-slate-400 font-medium">
                    Tidak ditemukan data pengguna.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
