<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { ArrowLeftIcon, CalendarDaysIcon, HeartIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

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
        { date: '04 Mar 2026', type: 'Kunjungan Klinik', result: 'Keluhan demam dan batuk (ISPA). Diberikan Paracetamol & Vitamin.' }
    ]
};
</script>

<template>
    <PendampingLayout>
        <Head title="Profil Siswa Boarding" />

        <div class="space-y-6 max-w-4xl">
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('pendamping.siswa.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Profil Siswa</h2>
                    <p class="text-xs text-slate-500 font-medium">Informasi biodata, riwayat kesehatan, dan kebugaran.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Bio -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-blue-100 text-blue-800 font-extrabold text-2xl flex items-center justify-center border-4 border-blue-50">
                        {{ student.name.charAt(0) }}
                    </div>
                    <h3 class="text-base font-extrabold text-slate-900 mt-4 leading-tight">{{ student.name }}</h3>
                    <span class="text-slate-400 text-xs font-semibold mt-1">NIS: {{ student.nis }}</span>
                    <span class="mt-3 text-[9px] font-extrabold px-3 py-1 rounded-full border uppercase tracking-wider" :class="student.status_style">
                        {{ student.status }}
                    </span>

                    <div class="w-full mt-6 pt-6 border-t border-slate-100 space-y-3 text-left text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Kelas:</span>
                            <span class="text-slate-800 font-bold">{{ student.class }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400 font-semibold">Asrama:</span>
                            <span class="text-slate-800 font-bold">{{ student.dorm }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right details -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 lg:col-span-2 space-y-6">
                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 border-b border-slate-100 pb-2 mb-4 uppercase tracking-wider">Biodata Lengkap</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 font-semibold block">Tempat, Tanggal Lahir</span>
                                <span class="text-slate-800 font-bold">{{ student.place_birth }}, {{ student.date_birth }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block">Tinggi / Berat Badan</span>
                                <span class="text-slate-800 font-bold">{{ student.height }} / {{ student.weight }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-extrabold text-slate-800 border-b border-slate-100 pb-2 mb-4 uppercase tracking-wider">Riwayat Medis Terakhir</h4>
                        <div class="space-y-4">
                            <div v-for="(log, idx) in student.history" :key="idx" class="flex space-x-3 items-start text-xs">
                                <div class="p-2 rounded-xl" :class="log.type.includes('Klinik') ? 'bg-rose-50 text-rose-600' : 'bg-blue-50 text-blue-600'">
                                    <component :is="log.type.includes('Klinik') ? HeartIcon : CalendarDaysIcon" class="w-4 h-4" />
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-slate-800">{{ log.type }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold">{{ log.date }}</span>
                                    </div>
                                    <p class="text-slate-500 font-medium">{{ log.result }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
