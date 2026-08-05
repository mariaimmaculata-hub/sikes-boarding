<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { 
    UserGroupIcon, 
    AcademicCapIcon, 
    ClipboardDocumentCheckIcon, 
    ExclamationTriangleIcon, 
    CalendarDaysIcon,
    BellIcon,
    ClockIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

// Stats Cards data
const stats = [
    { name: 'Total Siswa Boarding', value: '1.248', icon: UserGroupIcon, color: 'border-blue-600 text-blue-600 bg-blue-50/50' },
    { name: 'Total Kelas', value: '36', icon: AcademicCapIcon, color: 'border-green-600 text-green-600 bg-green-50/50' },
    { name: 'Pemeriksaan Hari Ini', value: '18', icon: CalendarDaysIcon, color: 'border-cyan-600 text-cyan-600 bg-cyan-50/50' },
    { name: 'Siswa Perlu Pemantauan', value: '12', icon: ExclamationTriangleIcon, color: 'border-orange-600 text-orange-600 bg-orange-50/50' },
    { name: 'Kunjungan Klinik Minggu Ini', value: '45', icon: ClipboardDocumentCheckIcon, color: 'border-purple-600 text-purple-600 bg-purple-50/50' }
];

// 5 Penyakit Terbanyak
const illnesses = [
    { name: 'ISPA', count: 42, color: 'bg-rose-500' },
    { name: 'Demam', count: 28, color: 'bg-orange-500' },
    { name: 'Sakit Kepala', count: 15, color: 'bg-amber-500' },
    { name: 'Batuk (Batik)', count: 12, color: 'bg-yellow-500' },
    { name: 'Diare', count: 9, color: 'bg-blue-500' }
];

// Today's schedule
const schedules = [
    { time: '08:00 - 10:00', title: 'Pemeriksaan Kelas X TKJ 1', desc: 'Pemeriksaan fisik berkala rutin bulanan.' },
    { time: '13:00 - 15:00', title: 'Pemeriksaan Kelas XI AK 2', desc: 'Pemeriksaan rekam kesehatan berkala.' },
    { time: '15:30 - 17:00', title: 'Tes Kebugaran Kelas XII RPL 1', desc: 'Uji kekuatan jasmani TKSI.' }
];

// Watchlist
const watchlist = [
    { name: 'Ahmad Fauzi', class: 'Kelas X', dorm: 'Asrama Putra - Kamar 5', reason: 'Flu & Demam Tinggi' },
    { name: 'Rina Agustin', class: 'Kelas XI', dorm: 'Asrama Putri - Kamar 12', reason: 'Dispepsia Akut' },
    { name: 'Dimas Saputra', class: 'Kelas XII', dorm: 'Asrama Putra - Kamar 1', reason: 'Pemulihan Sakit Kepala' },
    { name: 'Siti Nurhaliza', class: 'Kelas XI', dorm: 'Asrama Putri - Kamar 8', reason: 'Asma Ringan' }
];

// Notifications
const notifications = [
    { title: 'Pemeriksaan berkala kelas X TKJ 1 (Besok, 09 Mei 2025)', time: '2 jam lalu', type: 'info' },
    { title: 'Tes Kebugaran kelas XI AK 2 (Besok, 09 Mei 2025)', time: '3 jam lalu', type: 'info' },
    { title: 'Stok obat Paracetamol menipis (Sisa 12 tablet)', time: '5 jam lalu', type: 'warning' }
];
</script>

<template>
    <PendampingLayout>
        <Head title="Pendamping Dashboard" />

        <div class="space-y-6">
            <!-- Welcome Header Section -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-lg border border-white/5">
                <div class="absolute right-0 bottom-0 top-0 w-1/3 opacity-15 pointer-events-none hidden md:block">
                    <!-- Elegant SVG graphic decor inside header banner -->
                    <svg class="w-full h-full text-white" viewBox="0 0 100 100" fill="currentColor">
                        <circle cx="80" cy="50" r="30" />
                        <path d="M40 90 L80 10 L90 50 Z" />
                    </svg>
                </div>
                <div class="relative z-10 space-y-2 max-w-2xl">
                    <h2 class="text-2xl font-bold tracking-tight">Dashboard</h2>
                    <p class="text-white/80 text-sm font-medium leading-relaxed">
                        Selamat datang, Pendamping! Pantau kesehatan dan kebugaran siswa boarding dengan mudah.
                    </p>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div 
                    v-for="(s, idx) in stats" 
                    :key="idx" 
                    class="bg-white border-l-4 rounded-2xl p-5 shadow-sm hover:shadow-md transition duration-300 flex flex-col justify-between"
                    :class="s.color"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ s.name }}</span>
                        <component :is="s.icon" class="w-5 h-5 opacity-75" />
                    </div>
                    <div class="mt-4">
                        <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ s.value }}</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section (2 side-by-side) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Bar Chart 1: Pemeriksaan Berkala -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Grafik Pemeriksaan Berkala (3 Bulan Terakhir)</h3>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Rasio pemeriksaan bulanan</p>
                    </div>

                    <!-- Custom SVG Bar Chart -->
                    <div class="relative h-60 w-full flex items-end justify-around pt-6 border-b border-slate-100">
                        <!-- Feb Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-blue-600 transition">120</span>
                            <div class="bg-blue-600 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 120px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">Februari</span>
                        </div>
                        <!-- Mar Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-blue-600 transition">145</span>
                            <div class="bg-blue-500 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 145px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">Maret</span>
                        </div>
                        <!-- Apr Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-blue-600 transition">130</span>
                            <div class="bg-blue-400 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 130px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">April</span>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart 2: Kunjungan Klinik -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-800 tracking-tight">Grafik Kunjungan Klinik (3 Bulan Terakhir)</h3>
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider mt-0.5">Rasio kunjungan bulanan</p>
                    </div>

                    <!-- Custom SVG Bar Chart -->
                    <div class="relative h-60 w-full flex items-end justify-around pt-6 border-b border-slate-100">
                        <!-- Feb Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-rose-600 transition">85</span>
                            <div class="bg-rose-500 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 85px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">Februari</span>
                        </div>
                        <!-- Mar Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-rose-600 transition">72</span>
                            <div class="bg-rose-400 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 72px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">Maret</span>
                        </div>
                        <!-- Apr Bar -->
                        <div class="flex flex-col items-center group w-1/4">
                            <span class="text-[10px] font-bold text-slate-500 mb-1 group-hover:text-rose-600 transition">90</span>
                            <div class="bg-rose-600 w-10 sm:w-12 rounded-t-lg transition-all duration-500 ease-out shadow-md" style="height: 90px;"></div>
                            <span class="text-[10px] font-bold text-slate-500 mt-2">April</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Penyakit & Watchlist -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- 5 Penyakit Terbanyak -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                        <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">5 Penyakit Terbanyak Bulan Ini</h3>
                        
                        <div class="space-y-3.5">
                            <div v-for="(ill, idx) in illnesses" :key="idx" class="space-y-1">
                                <div class="flex justify-between text-xs font-bold text-slate-700">
                                    <span>{{ idx + 1 }}. {{ ill.name }}</span>
                                    <span>{{ ill.count }} Kasus</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="h-2 rounded-full" :class="ill.color" :style="{ width: `${(ill.count / 42) * 100}%` }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Siswa Perlu Pemantauan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                        <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">Siswa Perlu Pemantauan</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div 
                                v-for="(w, idx) in watchlist" 
                                :key="idx" 
                                class="border border-slate-100 rounded-xl p-3.5 flex items-center justify-between hover:bg-slate-50 transition"
                            >
                                <div class="space-y-0.5">
                                    <span class="text-xs font-extrabold text-blue-900 block">{{ w.name }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold block">{{ w.class }} | {{ w.dorm }}</span>
                                </div>
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                    {{ w.reason }}
                                </span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Jadwal & Notifikasi -->
                <div class="space-y-6">
                    <!-- Jadwal Hari Ini -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">Jadwal Hari Ini</h3>
                            
                            <div class="space-y-4">
                                <div v-for="(sch, idx) in schedules" :key="idx" class="flex space-x-3.5 items-start">
                                    <div class="p-2 bg-blue-50 text-blue-600 rounded-xl flex-shrink-0">
                                        <ClockIcon class="w-4.5 h-4.5" />
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-extrabold text-blue-600 block uppercase tracking-wider">{{ sch.time }}</span>
                                        <span class="text-xs font-bold text-slate-800 block">{{ sch.title }}</span>
                                        <p class="text-[10px] text-slate-400 font-medium leading-relaxed">{{ sch.desc }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link 
                            :href="route('pendamping.pengingat.index')"
                            class="mt-6 w-full py-2 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl text-center hover:bg-slate-50 transition flex items-center justify-center space-x-1"
                        >
                            <span>Lihat Semua Jadwal</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <!-- Notifikasi Feed -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">Notifikasi Terbaru</h3>
                            
                            <div class="space-y-3.5">
                                <div v-for="(notif, idx) in notifications" :key="idx" class="flex space-x-3 items-start text-xs">
                                    <div class="p-1.5 rounded-lg flex-shrink-0 mt-0.5" :class="notif.type === 'warning' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600'">
                                        <BellIcon class="w-3.5 h-3.5" />
                                    </div>
                                    <div class="space-y-0.5">
                                        <span class="font-semibold text-slate-700 leading-tight block">{{ notif.title }}</span>
                                        <span class="text-[9px] text-slate-400 font-bold block">{{ notif.time }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Link 
                            :href="route('pendamping.pengingat.index')"
                            class="mt-6 w-full py-2 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl text-center hover:bg-slate-50 transition flex items-center justify-center space-x-1"
                        >
                            <span>Lihat Semua Notifikasi</span>
                            <ChevronRightIcon class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </PendampingLayout>
</template>
