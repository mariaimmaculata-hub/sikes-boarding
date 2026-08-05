<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

const form = useForm({
    id: props.id,
    name: props.id === '1' ? 'ISPA' : 'Dispepsia',
    description: props.id === '1' ? 'Infeksi Saluran Pernapasan Akut' : 'Gangguan lambung atau maag'
});

const submit = () => {
    alert('Detail penyakit berhasil diperbarui!');
    window.history.back();
};
</script>

<template>
    <PetugasLayout>
        <Head title="Edit Penyakit" />

        <div class="space-y-6 max-w-xl">
            <div class="flex items-center space-x-3">
                <Link :href="route('petugas.penyakit.index')" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Penyakit</h2>
                    <p class="text-xs text-slate-500 font-medium">Ubah keterangan dan diagnosa katalog penyakit.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Penyakit / Kode</label>
                        <input type="text" v-model="form.name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Deskripsi</label>
                        <textarea v-model="form.description" required rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link :href="route('petugas.penyakit.index')" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition">Batal</Link>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </PetugasLayout>
</template>
