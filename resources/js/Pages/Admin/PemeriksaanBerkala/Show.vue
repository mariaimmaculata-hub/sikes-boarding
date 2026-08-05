<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeftIcon, CalendarDaysIcon, HeartIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    id: {
        type: String,
        required: true
    }
});

// Mock detail data based on ID
const record = {
    id: props.id,
    date: props.id === '1' ? '10 Feb 2026' : (props.id === '2' ? '12 Feb 2026' : '15 Feb 2026'),
    nis: props.id === '1' ? '2024001' : (props.id === '2' ? '2023102' : '2022203'),
    name: props.id === '1' ? 'Ahmad Fauzi' : (props.id === '2' ? 'Rina Agustin' : 'Dimas Saputra'),
    class: props.id === '1' ? 'X TKJ 1' : (props.id === '2' ? 'XI AK 2' : 'XII TAV 1'),
    height: props.id === '1' ? '172 cm' : (props.id === '2' ? '160 cm' : '175 cm'),
    weight: props.id === '1' ? '64 kg' : (props.id === '2' ? '50 kg' : '68 kg'),
    bp: props.id === '1' ? '120/80 mmHg' : (props.id === '2' ? '110/70 mmHg' : '130/85 mmHg'),
    pulse: '76 bpm',
    temp: '36.5 °C',
    eye_vision: 'VOD: 6/6, VOS: 6/6 (Normal)',
    dental: 'Karies gigi ringan (dianjurkan kontrol)',
    ears_throat: 'Normal',
    physical_status: props.id === '3' ? 'Pemantauan' : 'Sehat',
    status_style: props.id === '3' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200',
    notes: 'Kondisi fisik secara umum baik. Pertahankan pola makan sehat dan olahraga teratur. Dianjurkan kontrol dokter gigi untuk karies ringan.'
};
</script>

<template>
    <AdminLayout>
        <Head title="Detail Pemeriksaan Berkala" />

        <div class="space-y-6 max-w-3xl">
            <!-- Header Section -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('admin.pemeriksaan.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Detail Pemeriksaan Berkala</h2>
                    <p class="text-xs text-slate-500 font-medium">Laporan lengkap hasil uji fisik dan klinis rutin siswa.</p>
                </div>
            </div>

            <!-- Detail Grid Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <!-- Top Summary Row -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div class="space-y-1">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Informasi Pemeriksaan</span>
                        <div class="flex items-center space-x-2">
                            <CalendarDaysIcon class="w-5 h-5 text-blue-600" />
                            <span class="font-extrabold text-slate-800 text-base">Tanggal: {{ record.date }}</span>
                        </div>
                    </div>
                    <span class="text-xs font-extrabold px-3 py-1.5 rounded-full border tracking-wide uppercase self-start sm:self-center" :class="record.status_style">
                        Status: {{ record.physical_status }}
                    </span>
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
                        <span class="text-slate-400 font-semibold block mb-0.5">Tipe Log</span>
                        <span class="text-slate-800 font-bold text-sm">Rutin Berkala</span>
                    </div>
                </div>

                <!-- Checkup Parameters Grid -->
                <div class="space-y-4">
                    <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-1.5 flex items-center space-x-2">
                        <HeartIcon class="w-5 h-5 text-rose-500" />
                        <span>Parameter Vital &amp; Fisik</span>
                    </h4>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-5 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Tinggi Badan (TB)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.height }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Berat Badan (BB)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.weight }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Tekanan Darah (BP)</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.bp }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Denyut Nadi</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.pulse }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Suhu Tubuh</span>
                            <span class="text-slate-800 font-bold text-sm">{{ record.temp }}</span>
                        </div>
                    </div>
                </div>

                <!-- Secondary Parameters (Ears, Eyes, etc) -->
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h4 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-1.5 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Pemeriksaan Organ &amp; Indra</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Penglihatan (Mata)</span>
                            <span class="text-slate-800 font-bold">{{ record.eye_vision }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Kesehatan Gigi &amp; Mulut</span>
                            <span class="text-slate-800 font-bold">{{ record.dental }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-semibold block mb-0.5">Tenggorokan, THT &amp; Leher</span>
                            <span class="text-slate-800 font-bold">{{ record.ears_throat }}</span>
                        </div>
                    </div>
                </div>

                <!-- Health Notes / Advice -->
                <div class="space-y-2 pt-4 border-t border-slate-100 text-xs">
                    <span class="text-slate-400 font-semibold block">Catatan Medis &amp; Saran Petugas</span>
                    <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 text-blue-950 font-medium leading-relaxed">
                        {{ record.notes }}
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
