<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CalendarDaysIcon, BoltIcon, UserIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

// Mock record details based on ID
const record = {
    id: props.id,
    date: props.id === '1' ? '10 Feb 2026' : (props.id === '2' ? '14 Feb 2026' : '18 Feb 2026'),
    nis: props.id === '1' ? '2024001' : (props.id === '2' ? '2023102' : '2022203'),
    name: props.id === '1' ? 'Ahmad Fauzi' : (props.id === '2' ? 'Rina Agustin' : 'Dimas Saputra'),
    class: props.id === '1' ? 'X TKJ 1' : (props.id === '2' ? 'XI AK 2' : 'XII TAV 1'),
    dorm: props.id === '1' ? 'Asrama Putra - Kamar 5' : (props.id === '2' ? 'Asrama Putri - Kamar 12' : 'Asrama Putra - Kamar 1'),
    hand_eye_score: props.id === '1' ? '85' : (props.id === '2' ? '72' : '80'),
    agility_score: props.id === '1' ? '82' : (props.id === '2' ? '65' : '75'),
    vo2max: props.id === '1' ? '45.2 ml/kg/min' : (props.id === '2' ? '38.5 ml/kg/min' : '42.1 ml/kg/min'),
    run_1000m: props.id === '1' ? '4 Menit 12 Detik' : (props.id === '2' ? '5 Menit 05 Detik' : '4 Menit 35 Detik'),
    push_ups: props.id === '1' ? '38 kali' : (props.id === '2' ? '20 kali' : '32 kali'),
    sit_ups: props.id === '1' ? '42 kali' : (props.id === '2' ? '25 kali' : '35 kali'),
    fitness_status: props.id === '1' ? 'Sangat Baik' : (props.id === '2' ? 'Cukup' : 'Baik'),
    status_style: props.id === '1' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : (props.id === '2' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200'),
    examiner: 'Dwi Prasetyo, S.Pd.Jas (Guru Penjasorkes)',
    notes: props.id === '2' ? 'Dianjurkan meningkatkan latihan daya tahan jantung-paru (cardio) secara teratur dan memperbaiki kelenturan fisik.' : 'Pertahankan kebugaran jasmani Anda dengan latihan rutin dan pola hidup sehat.'
};
</script>

<template>
    <AdminLayout>
        <Head title="Detail Tes Kebugaran" />

        <div class="space-y-6 max-w-3xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.tksi.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Hasil Tes Kebugaran (TKSI)</h2>
                    <p class="text-xs text-slate-500 font-medium">Laporan lengkap rekam uji jasmani &amp; atletik siswa.</p>
                </div>
            </div>

            <!-- Detail Box Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <!-- Info row top -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Tes Diadakan</span>
                        <div class="flex items-center space-x-2">
                            <CalendarDaysIcon class="w-5 h-5 text-emerald-500" />
                            <span class="font-extrabold text-slate-800 text-base">{{ record.date }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1.5 text-xs text-slate-500 font-bold">
                        <UserIcon class="w-4 h-4 text-slate-400" />
                        <span>Penguji: <span class="text-slate-800">{{ record.examiner }}</span></span>
                    </div>
                </div>

                <!-- Student summary -->
                <div class="bg-slate-50 rounded-2xl p-4 md:p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Nama Siswa</span>
                        <span class="text-blue-900 font-bold text-sm">{{ record.name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">NIS</span>
                        <span class="text-slate-800 font-bold text-sm">{{ record.nis }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Kelas</span>
                        <span class="text-slate-800 font-bold text-sm">{{ record.class }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Asrama</span>
                        <span class="text-slate-800 font-bold text-sm">{{ record.dorm }}</span>
                    </div>
                </div>

                <!-- Fitness Parameters Grid -->
                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-1.5 flex items-center space-x-2">
                        <BoltIcon class="w-5 h-5 text-emerald-500" />
                        <span>Skor Parameter Kebugaran</span>
                    </h4>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Koordinasi Mata-Tangan</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.hand_eye_score }} / 100</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Kelincahan Jasmani</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.agility_score }} / 100</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">VO2 Max (Est.)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.vo2max }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Lari 1000m (Ketahanan)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.run_1000m }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Push Up (Kekuatan)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.push_ups }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Sit Up (Kekuatan)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.sit_ups }}</span>
                        </div>
                    </div>
                </div>

                <!-- Overall Health Classification -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kesimpulan Kebugaran Akhir</span>
                        <span class="text-[10px] font-extrabold px-3 py-1 rounded-full border uppercase tracking-wider" :class="record.status_style">
                            Kategori: {{ record.fitness_status }}
                        </span>
                    </div>
                    <div class="bg-emerald-50/40 border border-emerald-100 rounded-xl p-4 text-emerald-950 font-medium text-xs leading-relaxed">
                        {{ record.notes }}
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
