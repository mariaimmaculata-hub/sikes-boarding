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

// Mock loading existing data based on ID
const form = useForm({
    id: props.id,
    code: props.id === '1' ? 'RPL' : (props.id === '2' ? 'TKJ' : 'AK'),
    name: props.id === '1' ? 'Rekayasa Perangkat Lunak' : (props.id === '2' ? 'Teknik Komputer & Jaringan' : 'Akuntansi & Keuangan Lembaga')
});

const submit = () => {
    alert(`Jurusan ${form.name} (${form.code}) berhasil diperbarui!`);
    window.history.back();
};
</script>

<template>
    <AdminLayout>
        <Head title="Ubah Jurusan" />

        <div class="space-y-6 max-w-xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.master.jurusan.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Ubah Jurusan</h2>
                    <p class="text-xs text-slate-500 font-medium">Perbarui detail kompetensi keahlian.</p>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    
                    <!-- Kode Jurusan -->
                    <div class="space-y-1.5">
                        <label for="code" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kode Jurusan</label>
                        <input 
                            id="code" 
                            type="text" 
                            v-model="form.code"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Nama Jurusan -->
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Jurusan</label>
                        <input 
                            id="name" 
                            type="text" 
                            v-model="form.name"
                            required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>

                    <!-- Buttons Group -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link 
                            :href="route('admin.master.jurusan.index')"
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
