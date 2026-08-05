<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowLeftIcon } from '@heroicons/vue/24/outline';

const form = useForm({
    student_id: '',
    hand_eye_score: '',
    agility_score: '',
    fitness_status: 'Cukup'
});

const submit = () => {
    alert('Hasil tes kebugaran berhasil dicatat!');
    window.history.back();
};
</script>

<template>
    <PetugasLayout>
        <Head title="Catat Hasil Tes Kebugaran" />

        <div class="space-y-6 max-w-xl">
            <div class="flex items-center space-x-3">
                <Link :href="route('petugas.tksi.index')" class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Catat Hasil TKSI</h2>
                    <p class="text-xs text-slate-500 font-medium">Buat rekam uji kebugaran jasmani siswa.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8">
                <form @submit.prevent="submit" class="space-y-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Siswa / NIS</label>
                        <input type="text" v-model="form.student_id" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" placeholder="Masukkan nama atau NIS" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Skor Koordinasi (Mata-Tangan)</label>
                            <input type="number" v-model="form.hand_eye_score" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" placeholder="0 - 100" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Skor Kelincahan</label>
                            <input type="number" v-model="form.agility_score" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" placeholder="0 - 100" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Kesimpulan Kebugaran</label>
                        <select v-model="form.fitness_status" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm">
                            <option value="Sangat Baik">Sangat Baik</option>
                            <option value="Baik">Baik</option>
                            <option value="Cukup">Cukup</option>
                            <option value="Kurang">Kurang</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                        <Link :href="route('petugas.tksi.index')" class="px-4 py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition">Batal</Link>
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-md">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </PetugasLayout>
</template>
