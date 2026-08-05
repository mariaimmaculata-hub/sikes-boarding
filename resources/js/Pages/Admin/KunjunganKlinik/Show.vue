<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CalendarDaysIcon, HeartIcon, UserIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

// Mock visit details based on ID
const visit = {
    id: props.id,
    date: props.id === '1' ? '04 Mar 2026' : (props.id === '2' ? '08 Mar 2026' : '10 Mar 2026'),
    nis: props.id === '1' ? '2024001' : (props.id === '2' ? '2023102' : '2022203'),
    name: props.id === '1' ? 'Ahmad Fauzi' : (props.id === '2' ? 'Rina Agustin' : 'Dimas Saputra'),
    class: props.id === '1' ? 'X TKJ 1' : (props.id === '2' ? 'XI AK 2' : 'XII TAV 1'),
    dorm: props.id === '1' ? 'Asrama Putra - Kamar 5' : (props.id === '2' ? 'Asrama Putri - Kamar 12' : 'Asrama Putra - Kamar 1'),
    complaints: props.id === '1' ? 'Demam tinggi semenjak kemarin malam, batuk berdahak disertai tenggorokan gatal dan pilek.' : (props.id === '2' ? 'Nyeri ulu hati menjalar ke perut kiri bawah, mual tapi tidak muntah.' : 'Sakit kepala berdenyut bagian belakang sejak bangun pagi.'),
    diagnosis: props.id === '1' ? 'ISPA (Infeksi Saluran Pernapasan Akut)' : (props.id === '2' ? 'Dispepsia (Sakit Maag)' : 'Tension Headache (Sakit Kepala Ketegangan)'),
    therapy: props.id === '1' ? 'Paracetamol 500mg (3x1 sesudah makan), Ambroxol sirup (3x1 sendok makan), Vitamin C (1x1 tablet).' : (props.id === '2' ? 'Antasida tablet kunyah (3x1 sebelum makan), Domperidone 10mg (3x1).' : 'Ibuprofen 400mg (2x1 sesudah makan), istirahat baring di klinik selama 2 jam.'),
    attending_staff: props.id === '3' ? 'Dr. Handoko (Dokter Mitra)' : 'Indah Fitriani, A.Md.Kep (Petugas Klinik)',
    temp: props.id === '1' ? '38.5 °C (Demam)' : '36.6 °C (Normal)',
    bp: '120/80 mmHg',
    weight: '64 kg'
};
</script>

<template>
    <AdminLayout>
        <Head title="Detail Kunjungan Klinik" />

        <div class="space-y-6 max-w-3xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.kunjungan.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Kunjungan Klinik</h2>
                    <p class="text-xs text-slate-500 font-medium">Catatan lengkap rekam medis tindakan &amp; terapi obat siswa.</p>
                </div>
            </div>

            <!-- Detail Box Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <!-- Info row top -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Pemeriksaan Tanggal</span>
                        <div class="flex items-center space-x-2">
                            <CalendarDaysIcon class="w-5 h-5 text-rose-500" />
                            <span class="font-extrabold text-slate-800 text-base">{{ visit.date }}</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1.5 text-xs text-slate-500 font-bold">
                        <UserIcon class="w-4 h-4 text-slate-400" />
                        <span>Pemeriksa: <span class="text-slate-800">{{ visit.attending_staff }}</span></span>
                    </div>
                </div>

                <!-- Student summary -->
                <div class="bg-slate-50 rounded-2xl p-4 md:p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Nama Siswa</span>
                        <span class="text-blue-900 font-bold text-sm">{{ visit.name }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">NIS</span>
                        <span class="text-slate-800 font-bold text-sm">{{ visit.nis }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Kelas / Rombel</span>
                        <span class="text-slate-800 font-bold text-sm">{{ visit.class }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-semibold block mb-0.5">Asrama / Kamar</span>
                        <span class="text-slate-800 font-bold text-sm">{{ visit.dorm }}</span>
                    </div>
                </div>

                <!-- Medical Symptoms -->
                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-1.5 flex items-center space-x-2">
                        <HeartIcon class="w-5 h-5 text-rose-500" />
                        <span>Pemeriksaan Klinis &amp; Gejala</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Suhu Badan (Klinis)</span>
                            <span class="text-slate-800 font-bold text-sm" :class="{ 'text-rose-600': visit.temp.includes('Demam') }">{{ visit.temp }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Tekanan Darah</span>
                            <span class="text-slate-800 font-bold text-sm">{{ visit.bp }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Berat Badan</span>
                            <span class="text-slate-800 font-bold text-sm">{{ visit.weight }}</span>
                        </div>
                    </div>
                </div>

                <!-- Details text areas -->
                <div class="space-y-4 pt-4 border-t border-slate-100 text-xs">
                    <div class="space-y-1.5">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block">Keluhan Utama</span>
                        <p class="text-slate-700 font-medium leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100">{{ visit.complaints }}</p>
                    </div>

                    <div class="space-y-1.5 pt-2">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block">Diagnosa Medis</span>
                        <div class="bg-rose-50 text-rose-950 font-bold px-3 py-2.5 rounded-xl border border-rose-100 text-xs tracking-wide">
                            {{ visit.diagnosis }}
                        </div>
                    </div>

                    <div class="space-y-1.5 pt-2">
                        <span class="text-slate-400 font-bold uppercase tracking-wider block">Terapi Pengobatan / Tindakan Medis</span>
                        <p class="text-slate-700 font-medium leading-relaxed bg-slate-50/50 p-3 rounded-xl border border-slate-100">{{ visit.therapy }}</p>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
