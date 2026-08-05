<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { 
    ClipboardDocumentCheckIcon, 
    PlusCircleIcon, 
    ExclamationTriangleIcon,
    ArrowRightIcon,
    ClockIcon,
    BellIcon,
    HeartIcon
} from '@heroicons/vue/24/outline';

const stats = [
    { 
        name: 'Jumlah Siswa Pemeriksaan Berkala', 
        value: '48 siswa', 
        sub: '75% dari total siswa', 
        icon: ClipboardDocumentCheckIcon, 
        color: 'border-emerald-600 text-emerald-600 bg-emerald-50/50' 
    },
    { 
        name: 'Jumlah Kunjungan Klinik', 
        value: '12 kunjungan', 
        sub: 'Bulan ini', 
        icon: PlusCircleIcon, 
        color: 'border-blue-600 text-blue-600 bg-blue-50/50' 
    },
    { 
        name: 'Siswa Perlu Perhatian', 
        value: '5 siswa', 
        sub: 'Lihat detail', 
        icon: ExclamationTriangleIcon, 
        color: 'border-rose-600 text-rose-600 bg-rose-50/50', 
        link: true 
    }
];

// Siswa Perlu Perhatian
const attentionStudents = [
    { name: 'Aulia Rahma', class: 'X AKL 2', dorm: 'Asrama Putri', status: 'Perlu Perhatian', note: 'IMT tidak normal' },
    { name: 'Nadia Putri', class: 'XI TKJ 1', dorm: 'Asrama Putri', status: 'Perlu Perhatian', note: 'Tekanan darah tinggi' },
    { name: 'Dewi Lestari', class: 'X MPLB 1', dorm: 'Asrama Putri', status: 'Perlu Perhatian', note: 'Sering sakit kepala' },
    { name: 'Salma Putri', class: 'X Akuntansi 1', dorm: 'Asrama Putri', status: 'Perlu Perhatian', note: 'Hasil TKSI kurang' },
    { name: 'Putri Anjani', class: 'XI RPL 1', dorm: 'Asrama Putri', status: 'Perlu Perhatian', note: 'Berat badan kurang' }
];

// Aktivitas Terbaru
const activities = [
    { text: 'Pemeriksaan berkala untuk kelas X AKL 2 telah selesai.', staff: 'Rina Lestari (Petugas Klinik)', time: 'Hari ini, 09:30', type: 'checkup' },
    { text: 'Siti Aminah melakukan kunjungan klinik.', staff: 'Keluhan: Demam, Batuk', time: 'Kemarin, 10:15', type: 'visit' },
    { text: 'Pengingat: Pemeriksaan Berkala Semester Genap 2024/2025', staff: 'Jadwal: 20 - 24 Mei 2025', time: '18 Mei 2025', type: 'reminder' }
];

// Jadwal & Pengingat
const reminders = [
    { title: 'Pemeriksaan Berkala - X TKJ 1 & X RPL 1', date: 'Hari ini, 09:30', deadline: '20 Mei', status: 'Perlu Perhatian' },
    { title: 'Tes Kebugaran (TKSI) - XI AKL 1 & XI MPLB 1', date: 'Hari ini, 09:30', deadline: '23 Mei', status: 'Perlu Perhatian' },
    { title: 'Rapat Evaluasi Kesehatan Siswa - Ruang UKS', date: 'Hari ini, 09:30', deadline: '27 Mei', status: 'Perlu Perhatian' }
];
</script>

<template>
    <PetugasLayout>
        <Head title="Petugas Dashboard" />

        <div class="space-y-6">
            <!-- Welcome Header Banner -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 rounded-3xl p-6 md:p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute right-0 bottom-0 top-0 w-1/4 opacity-10 pointer-events-none hidden md:block">
                    <svg class="w-full h-full text-white" viewBox="0 0 100 100" fill="currentColor">
                        <circle cx="70" cy="50" r="30" />
                    </svg>
                </div>
                <div class="relative z-10 space-y-2">
                    <h2 class="text-2xl font-bold tracking-tight">Dashboard Petugas Klinik</h2>
                    <p class="text-white/80 text-sm font-medium">
                        Selamat datang, Rina Lestari 😊 Berikut ringkasan kesehatan siswa bimbingan Anda.
                    </p>
                </div>
            </div>

            <!-- Stats Card row (3 columns) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div 
                    v-for="(s, idx) in stats" 
                    :key="idx"
                    class="bg-white border-l-4 rounded-2xl p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                    :class="s.color"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ s.name }}</span>
                        <component :is="s.icon" class="w-5 h-5 opacity-80" />
                    </div>
                    <div class="mt-4 flex flex-col">
                        <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ s.value }}</span>
                        
                        <Link 
                            v-if="s.link"
                            :href="route('petugas.pemeriksaan.index')"
                            class="text-[10px] font-bold text-rose-600 hover:underline mt-1 flex items-center space-x-0.5"
                        >
                            <span>{{ s.sub }}</span>
                            <ArrowRightIcon class="w-3 h-3" />
                        </Link>
                        <span v-else class="text-[10px] font-bold text-slate-400 mt-1">{{ s.sub }}</span>
                    </div>
                </div>
            </div>

            <!-- Bottom listings section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Siswa Perlu Perhatian -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2 flex items-center space-x-1.5">
                            <HeartIcon class="w-5 h-5 text-rose-500" />
                            <span>Siswa Perlu Perhatian</span>
                        </h3>
                        <div class="divide-y divide-slate-100">
                            <div v-for="(std, idx) in attentionStudents" :key="idx" class="py-3 flex items-center justify-between text-xs">
                                <div class="space-y-0.5">
                                    <span class="font-extrabold text-blue-900 block">{{ std.name }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold block">{{ std.class }} • {{ std.dorm }}</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="font-semibold text-slate-500 text-[10px] bg-slate-50 border border-slate-200 rounded-lg px-2 py-0.5">{{ std.note }}</span>
                                    <span class="font-extrabold px-2.5 py-0.5 text-[9px] rounded-full border bg-rose-50 text-rose-700 border-rose-200 uppercase">
                                        {{ std.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <Link :href="route('petugas.pemeriksaan.index')" class="mt-6 w-full py-2.5 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl text-center hover:bg-slate-50 transition flex items-center justify-center space-x-1">
                        <span>Lihat Semua Daftar</span>
                        <ArrowRightIcon class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <!-- Right Agenda Tasks -->
                <div class="space-y-6">
                    <!-- Aktivitas Terbaru -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">Aktivitas Terbaru</h3>
                            <div class="space-y-4 text-xs">
                                <div v-for="(act, idx) in activities" :key="idx" class="flex space-x-3 items-start">
                                    <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600 flex-shrink-0 mt-0.5">
                                        <ClockIcon class="w-4.5 h-4.5" />
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-800 block">{{ act.text }}</span>
                                        <span class="text-[10px] text-slate-500 font-semibold block">{{ act.staff }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold block">{{ act.time }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal & Pengingat -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">Jadwal &amp; Pengingat</h3>
                            <div class="space-y-4 text-xs">
                                <div v-for="(rem, idx) in reminders" :key="idx" class="flex space-x-3.5 items-start">
                                    <div class="p-1.5 rounded-lg bg-rose-50 text-rose-600 flex-shrink-0 mt-0.5">
                                        <BellIcon class="w-4.5 h-4.5" />
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-800 block leading-tight">{{ rem.title }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold block mt-1">Tanggal: {{ rem.date }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold block">Batas: {{ rem.deadline }}</span>
                                        <span class="inline-block text-[8px] font-extrabold px-2 py-0.5 rounded border border-rose-200 bg-rose-50 text-rose-700 tracking-wider uppercase mt-1">
                                            {{ rem.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </PetugasLayout>
</template>
