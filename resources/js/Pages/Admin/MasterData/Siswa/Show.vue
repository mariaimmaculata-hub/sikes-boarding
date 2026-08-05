<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CalendarDaysIcon, HeartIcon, BoltIcon, ClipboardIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

// Mock detail data based on ID
const student = {
    id: props.id,
    nis: props.id === '1' ? '2024001' : (props.id === '2' ? '2023102' : '2022203'),
    name: props.id === '1' ? 'Ahmad Fauzi' : (props.id === '2' ? 'Rina Agustin' : 'Dimas Saputra'),
    class: props.id === '1' ? 'X TKJ 1' : (props.id === '2' ? 'XI AK 2' : 'XII TAV 1'),
    dorm: props.id === '1' ? 'Asrama Putra - Kamar 5' : (props.id === '2' ? 'Asrama Putri - Kamar 12' : 'Asrama Putra - Kamar 1'),
    status: props.id === '1' ? 'Sering Sakit' : (props.id === '2' ? 'Pemantauan' : 'Sehat'),
    status_style: props.id === '1' ? 'bg-rose-50 text-rose-700 border-rose-200' : (props.id === '2' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200'),
    gender: props.id === '2' ? 'Perempuan' : 'Laki-laki',
    place_birth: 'Semarang',
    date_birth: '12 April 2008',
    blood_type: 'O',
    height: '172 cm',
    weight: '64 kg',
    allergies: 'Tidak Ada',
    history: [
        { date: '10 Feb 2026', type: 'Pemeriksaan Berkala', result: 'Tekanan darah normal, berat badan ideal.' },
        { date: '04 Mar 2026', type: 'Kunjungan Klinik', result: 'Keluhan demam dan batuk (ISPA). Diberikan Paracetamol & Vitamin.' },
        { date: '18 Apr 2026', type: 'Tes Kebugaran (TKSI)', result: 'Skor Koordinasi: 85 (Sangat Baik), Kebugaran: Baik.' }
    ]
};
</script>

<template>
    <AdminLayout>
        <Head title="Profil Siswa" />

        <div class="space-y-6 max-w-4xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.master.siswa.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Profil Siswa</h2>
                    <p class="text-xs text-slate-500 font-medium">Informasi biodata, riwayat kesehatan, dan kebugaran.</p>
                </div>
            </div>

            <!-- Profile Info Cards Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Card: Bio Summary -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-full bg-blue-100 text-blue-800 font-extrabold text-3xl flex items-center justify-center border-4 border-blue-50 shadow-inner">
                        {{ student.name.charAt(0) }}
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-4 leading-tight">{{ student.name }}</h3>
                    <span class="text-slate-400 text-xs font-semibold mt-1">NIS: {{ student.nis }}</span>

                    <span class="mt-3 text-[10px] font-extrabold px-3 py-1 rounded-full border tracking-wider uppercase" :class="student.status_style">
                        {{ student.status }}
                    </span>

                    <div class="w-full mt-6 pt-6 border-t border-slate-100 space-y-3.5 text-left">
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Kelas:</span>
                            <span class="text-slate-800 font-bold">{{ student.class }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Asrama:</span>
                            <span class="text-slate-800 font-bold text-right">{{ student.dorm }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Gol. Darah:</span>
                            <span class="text-slate-800 font-bold">{{ student.blood_type }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Card: Full Bio & Measurements -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 lg:col-span-2 space-y-6">
                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2 mb-4">Biodata Lengkap</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">Tempat, Tanggal Lahir</span>
                                <span class="text-slate-800 font-bold">{{ student.place_birth }}, {{ student.date_birth }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">Jenis Kelamin</span>
                                <span class="text-slate-800 font-bold">{{ student.gender }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">Tinggi / Berat Badan</span>
                                <span class="text-slate-800 font-bold">{{ student.height }} / {{ student.weight }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block mb-1">Riwayat Alergi</span>
                                <span class="text-rose-600 font-bold">{{ student.allergies }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2 mb-4">Riwayat Pemeriksaan &amp; Kunjungan</h4>
                        <div class="space-y-4">
                            <div v-for="(log, idx) in student.history" :key="idx" class="flex space-x-3.5 items-start">
                                <div class="p-2 rounded-xl" :class="log.type.includes('Klinik') ? 'bg-rose-50 text-rose-600' : (log.type.includes('Kebugaran') ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600')">
                                    <component :is="log.type.includes('Klinik') ? HeartIcon : (log.type.includes('Kebugaran') ? BoltIcon : CalendarDaysIcon)" class="w-4.5 h-4.5" />
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-bold text-slate-900">{{ log.type }}</span>
                                        <span class="text-[9px] font-bold text-slate-400">{{ log.date }}</span>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium leading-relaxed">{{ log.result }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
