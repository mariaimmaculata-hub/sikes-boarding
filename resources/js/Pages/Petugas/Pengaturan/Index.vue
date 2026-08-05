<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { Cog6ToothIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    name: 'Rina Lestari',
    email: 'petugas@gmail.com',
    password: ''
});

const submit = () => {
    alert('Pengaturan akun petugas berhasil disimpan!');
};
</script>

<template>
    <PetugasLayout>
        <Head title="Pengaturan Akun" />

        <div class="space-y-6 max-w-xl">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Ubah nama akun dan preferensi profile petugas.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2 flex items-center space-x-2">
                        <Cog6ToothIcon class="w-5 h-5 text-blue-600" />
                        <span>Profil Pengguna</span>
                    </h3>

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

                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Email</label>
                        <input 
                            id="email" 
                            type="email" 
                            v-model="form.email"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <div class="flex justify-end pt-4 border-t border-slate-100">
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
    </PetugasLayout>
</template>
