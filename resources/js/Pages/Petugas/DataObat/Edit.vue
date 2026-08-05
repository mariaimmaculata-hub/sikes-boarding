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
    name: props.id === '1' ? 'Paracetamol 500mg' : 'Ambroxol Sirup',
    type: props.id === '1' ? 'Tablet' : 'Sirup',
    stock: props.id === '1' ? 12 : 24,
    unit: props.id === '1' ? 'Tablet' : 'Botol'
});

const submit = () => {
    alert('Stok obat berhasil diperbarui!');
    window.history.back();
};
</script>

<template>
    <PetugasLayout>
        <Head title="Edit Obat" />

        <div class="space-y-6 max-w-xl">
            <div class="flex items-center space-x-3">
                <Link :href="route('petugas.obat.index')" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Edit Detail Obat</h2>
                    <p class="text-xs text-slate-500 font-medium">Ubah jumlah stok dan nama obat UKS.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Obat</label>
                        <input type="text" v-model="form.name" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Sediaan / Tipe</label>
                        <select v-model="form.type" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                            <option value="Tablet">Tablet</option>
                            <option value="Sirup">Sirup</option>
                            <option value="Salep">Salep</option>
                            <option value="Kapsul">Kapsul</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Jumlah Stok</label>
                            <input type="number" v-model="form.stock" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Satuan</label>
                            <input type="text" v-model="form.unit" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link :href="route('petugas.obat.index')" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition">Batal</Link>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </PetugasLayout>
</template>
