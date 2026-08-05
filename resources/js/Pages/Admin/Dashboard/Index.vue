<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    UserGroupIcon, 
    AcademicCapIcon, 
    HeartIcon, 
    UserPlusIcon, 
    ClipboardDocumentCheckIcon,
    ChevronRightIcon,
    CalendarDaysIcon,
    BoltIcon,
    ClockIcon
} from '@heroicons/vue/24/outline';

// Stats Cards data
const stats = [
    { name: 'Total Siswa', value: '1.248', icon: UserGroupIcon, color: 'border-blue-600 text-blue-600 bg-blue-50/50' },
    { name: 'Total Kelas', value: '36', icon: AcademicCapIcon, color: 'border-green-600 text-green-600 bg-green-50/50' },
    { name: 'Pegawai Klinik', value: '12', icon: HeartIcon, color: 'border-purple-600 text-purple-600 bg-purple-50/50' },
    { name: 'Pendamping Siswa', value: '24', icon: UserPlusIcon, color: 'border-orange-600 text-orange-600 bg-orange-50/50' },
    { name: 'Pemeriksaan Hari Ini', value: '18', icon: ClipboardDocumentCheckIcon, color: 'border-rose-600 text-rose-600 bg-rose-50/50' }
];

// Ills lists
const illnesses = [
    { name: 'ISPA', count: 42, color: 'bg-rose-500' },
    { name: 'Demam', count: 28, color: 'bg-orange-500' },
    { name: 'Sakit Kepala', count: 15, color: 'bg-amber-500' },
    { name: 'Batuk', count: 12, color: 'bg-blue-500' },
    { name: 'Diare', count: 9, color: 'bg-teal-500' }
];

// Today's schedule
const schedules = [
    { time: '08:00 - 10:00', title: 'Pemeriksaan Berkala', desc: 'Kelas X - Asrama Putra' },
    { time: '13:00 - 15:00', title: 'Tes Kebugaran (TKSI)', desc: 'Kelas XI - Lapangan Indoor' },
    { time: '15:30 - 17:00', title: 'Pemeriksaan Berkala', desc: 'Kelas XII - Asrama Putri' }
];

// Students under watch
const watchedStudents = [
    { name: 'Ahmad Fauzi', class: 'X TKJ 1', status: 'Sering Sakit', style: 'bg-rose-50 text-rose-700 border-rose-200' },
    { name: 'Rina Agustin', class: 'XI AK 2', status: 'Pemantauan', style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { name: 'Dimas Saputra', class: 'XII TAV 1', status: 'Pemantauan', style: 'bg-amber-50 text-amber-700 border-amber-200' },
    { name: 'Siti Nurhaliza', class: 'XI MPLB 1', status: 'Pemantauan', style: 'bg-amber-50 text-amber-700 border-amber-200' }
];

// Notifications
const notifications = [
    { text: 'Pemeriksaan berkala kelas X TKJ 1 (Besok, 09 Mei 2025)', time: '2 jam lalu', type: 'pemeriksaan', icon: CalendarDaysIcon, color: 'bg-blue-100 text-blue-700' },
    { text: 'Tes Kebugaran kelas XI AK 2 (Hari ini, 13:00 - 15:00)', time: '3 jam lalu', type: 'tksi', icon: BoltIcon, color: 'bg-emerald-100 text-emerald-700' },
    { text: 'Stok obat Paracetamol menipis (Sisa 12 tablet)', time: '5 jam lalu', type: 'obat', icon: ClockIcon, color: 'bg-amber-100 text-amber-700' }
];
</script>

<template>
    <AdminLayout>
        <Head title="Admin Dashboard" />

        <!-- DASHBOARD CONTAINER -->
        <div class="space-y-6">
            
            <!-- Welcome Header -->
            <div class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-lg flex items-center justify-between">
                <!-- Background ambient lights -->
                <div class="absolute right-0 top-0 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10 space-y-2">
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Selamat datang, Admin!</h2>
                    <p class="text-sm md:text-base text-blue-100/90 max-w-2xl font-medium leading-relaxed">
                        Kelola data kesehatan dan kebugaran siswa dengan mudah dan terintegrasi.
                    </p>
                </div>
                
                <!-- School graphic / building illustration placeholder -->
                <div class="hidden md:block relative z-10 w-44 h-auto opacity-95">
                    <svg viewBox="0 0 120 80" fill="none" class="w-full text-blue-300">
                        <!-- Building Outline SVG -->
                        <path d="M10 70h100M20 70V30l40-15 40 15v40M45 70V50h30v20" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" />
                        <rect x="30" y="38" width="10" height="10" rx="1.5" fill="currentColor" fill-opacity="0.3" />
                        <rect x="80" y="38" width="10" height="10" rx="1.5" fill="currentColor" fill-opacity="0.3" />
                        <!-- Red/Yellow Cross on Top -->
                        <path d="M60 10v6M57 13h6" stroke="#EF4444" stroke-width="2" />
                    </svg>
                </div>
            </div>

            <!-- STATISTICS CARDS GRID -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                <div 
                    v-for="(item, idx) in stats" 
                    :key="idx" 
                    class="bg-white border-t-4 rounded-2xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg hover:-translate-y-0.5 transition duration-300"
                    :class="item.color.split(' ')[0]"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-xs font-bold uppercase tracking-wider">{{ item.name }}</span>
                        <div class="p-2 rounded-xl" :class="item.color.split(' ').slice(1).join(' ')">
                            <component :is="item.icon" class="w-5 h-5" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-2xl font-extrabold text-slate-900">{{ item.value }}</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                        <span>Lihat detail</span>
                        <ChevronRightIcon class="w-3.5 h-3.5" />
                    </div>
                </div>
            </div>

            <!-- CHARTS & ILLNESS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Grafik Pemeriksaan Berkala -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Grafik Pemeriksaan Berkala</h3>
                        <span class="text-slate-400 text-xs font-semibold">(3 Bulan Terakhir)</span>
                    </div>
                    
                    <!-- Responsive SVG Chart (Pemeriksaan Berkala) -->
                    <div class="my-6 h-48 w-full flex items-center justify-center">
                        <svg viewBox="0 0 300 150" class="w-full h-full text-blue-600">
                            <!-- Horizontal Grid lines -->
                            <line x1="30" y1="20" x2="280" y2="20" stroke="#f1f5f9" stroke-width="1" />
                            <line x1="30" y1="60" x2="280" y2="60" stroke="#f1f5f9" stroke-width="1" />
                            <line x1="30" y1="100" x2="280" y2="100" stroke="#f1f5f9" stroke-width="1" />
                            <!-- Bottom axis line -->
                            <line x1="30" y1="120" x2="280" y2="120" stroke="#cbd5e1" stroke-width="1.5" />
                            
                            <!-- Graph Area Path -->
                            <!-- Points: Feb (50, 100) -> Mar (150, 60) -> Apr (250, 80) -->
                            <path d="M50,100 L150,60 L250,80 L250,120 L50,120 Z" fill="url(#blue-gradient)" fill-opacity="0.1" />
                            <!-- Graph Line Path -->
                            <path d="M50,100 L150,60 L250,80" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            
                            <!-- Points Dots -->
                            <circle cx="50" cy="100" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            <circle cx="150" cy="60" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            <circle cx="250" cy="80" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            
                            <!-- Point Values -->
                            <text x="50" y="88" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">120</text>
                            <text x="150" y="48" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">145</text>
                            <text x="250" y="68" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">130</text>
                            
                            <!-- Labels -->
                            <text x="50" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">Februari</text>
                            <text x="150" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">Maret</text>
                            <text x="250" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">April</text>
                            
                            <!-- Gradients Definition -->
                            <defs>
                                <linearGradient id="blue-gradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="currentColor" />
                                    <stop offset="100%" stop-color="currentColor" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- Grafik Kunjungan Klinik -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Grafik Kunjungan Klinik</h3>
                        <span class="text-slate-400 text-xs font-semibold">(3 Bulan Terakhir)</span>
                    </div>

                    <!-- Responsive SVG Chart (Kunjungan Klinik) -->
                    <div class="my-6 h-48 w-full flex items-center justify-center">
                        <svg viewBox="0 0 300 150" class="w-full h-full text-green-600">
                            <!-- Horizontal Grid lines -->
                            <line x1="30" y1="20" x2="280" y2="20" stroke="#f1f5f9" stroke-width="1" />
                            <line x1="30" y1="60" x2="280" y2="60" stroke="#f1f5f9" stroke-width="1" />
                            <line x1="30" y1="100" x2="280" y2="100" stroke="#f1f5f9" stroke-width="1" />
                            <!-- Bottom axis line -->
                            <line x1="30" y1="120" x2="280" y2="120" stroke="#cbd5e1" stroke-width="1.5" />
                            
                            <!-- Graph Area Path -->
                            <!-- Points: Feb (50, 95) -> Mar (150, 65) -> Apr (250, 75) -->
                            <path d="M50,95 L150,65 L250,75 L250,120 L50,120 Z" fill="url(#green-gradient)" fill-opacity="0.1" />
                            <!-- Graph Line Path -->
                            <path d="M50,95 L150,65 L250,75" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                            
                            <!-- Points Dots -->
                            <circle cx="50" cy="95" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            <circle cx="150" cy="65" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            <circle cx="250" cy="75" r="4.5" fill="currentColor" stroke="#fff" stroke-width="2" />
                            
                            <!-- Point Values -->
                            <text x="50" y="83" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">85</text>
                            <text x="150" y="53" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">72</text>
                            <text x="250" y="63" text-anchor="middle" font-size="9" font-weight="bold" fill="#1e293b">90</text>
                            
                            <!-- Labels -->
                            <text x="50" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">Februari</text>
                            <text x="150" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">Maret</text>
                            <text x="250" y="136" text-anchor="middle" font-size="9" font-weight="bold" fill="#64748b">April</text>
                            
                            <!-- Gradients Definition -->
                            <defs>
                                <linearGradient id="green-gradient" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="currentColor" />
                                    <stop offset="100%" stop-color="currentColor" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>

                <!-- 5 Penyakit Terbanyak (List Sidebar) -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">5 Penyakit Terbanyak Bulan Ini</h3>
                        <span class="text-slate-400 text-xs font-semibold">(Bulan Berjalan)</span>
                    </div>

                    <!-- Illness Progress bar style lists -->
                    <div class="my-4 space-y-3.5">
                        <div v-for="(ill, idx) in illnesses" :key="idx" class="space-y-1">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <span class="text-slate-700 flex items-center space-x-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" :class="ill.color"></span>
                                    <span>{{ ill.name }}</span>
                                </span>
                                <span class="text-slate-900 font-bold">{{ ill.count }} kasus</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" :class="ill.color" :style="{ width: `${(ill.count / 50) * 100}%` }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                        <span>Lihat semua</span>
                        <ChevronRightIcon class="w-4 h-4" />
                    </div>
                </div>

            </div>

            <!-- JADWAL, SISWA PEMANTAUAN & NOTIFIKASI BOTTOM SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- JADWAL HARI INI -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
                            <CalendarDaysIcon class="w-5 h-5 text-blue-600" />
                            <span>Jadwal Hari Ini</span>
                        </h3>
                        
                        <!-- Schedules List -->
                        <div class="space-y-3">
                            <div v-for="(sched, idx) in schedules" :key="idx" class="flex space-x-3.5 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition">
                                <div class="bg-blue-50 text-blue-700 text-xs font-bold px-2 py-1 h-fit rounded-lg flex-shrink-0 text-center leading-relaxed">
                                    {{ sched.time.split(' ')[0] }}
                                    <span class="block text-[9px] font-medium text-blue-500/80">Mulai</span>
                                </div>
                                <div class="space-y-0.5">
                                    <span class="text-sm font-bold text-slate-900">{{ sched.title }}</span>
                                    <p class="text-xs text-slate-500 font-semibold">{{ sched.desc }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                        <span>Lihat semua jadwal</span>
                        <ChevronRightIcon class="w-4 h-4" />
                    </div>
                </div>

                <!-- SISWA PERLU PEMANTAUAN -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
                            <UserGroupIcon class="w-5 h-5 text-green-600" />
                            <span>Siswa Perlu Pemantauan</span>
                        </h3>

                        <!-- Table layout for students under watch -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-2">Nama</th>
                                        <th class="pb-2">Kelas</th>
                                        <th class="pb-2 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(student, idx) in watchedStudents" :key="idx" class="border-b border-slate-100/50 hover:bg-slate-50/50 transition">
                                        <td class="py-2.5 text-xs font-bold text-slate-900">{{ student.name }}</td>
                                        <td class="py-2.5 text-xs font-semibold text-slate-500">{{ student.class }}</td>
                                        <td class="py-2.5 text-right">
                                            <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border" :class="student.style">
                                                {{ student.status }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                        <span>Lihat semua</span>
                        <ChevronRightIcon class="w-4 h-4" />
                    </div>
                </div>

                <!-- NOTIFIKASI -->
                <div class="bg-white rounded-2xl shadow-md p-5 border border-slate-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2">
                            <BellIcon class="w-5 h-5 text-purple-600" />
                            <span>Notifikasi</span>
                        </h3>

                        <!-- Notification items -->
                        <div class="space-y-3.5">
                            <div v-for="(notif, idx) in notifications" :key="idx" class="flex space-x-3 items-start">
                                <div class="p-2 rounded-xl flex-shrink-0" :class="notif.color">
                                    <component :is="notif.icon" class="w-4 h-4" />
                                </div>
                                <div class="space-y-1">
                                    <p class="text-xs font-semibold text-slate-800 leading-normal">{{ notif.text }}</p>
                                    <span class="text-[10px] text-slate-400 font-bold block">{{ notif.time }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-blue-600 hover:text-blue-800 cursor-pointer">
                        <span>Lihat semua notifikasi</span>
                        <ChevronRightIcon class="w-4 h-4" />
                    </div>
                </div>

            </div>

        </div>
    </AdminLayout>
</template>
