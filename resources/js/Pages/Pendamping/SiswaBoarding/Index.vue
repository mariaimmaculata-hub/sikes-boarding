<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { 
    HomeIcon, 
    CpuChipIcon, 
    TruckIcon, 
    BoltIcon, 
    Cog6ToothIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const departments = [
    { id: 't-konstruksi', name: 'Teknik Konstruksi Perumahan', classesCount: 3, studentsCount: 72, icon: HomeIcon },
    { id: 't-elektronika', name: 'Teknik Elektronika Industri', classesCount: 3, studentsCount: 72, icon: CpuChipIcon },
    { id: 't-kendaraan', name: 'Teknik Kendaraan Ringan', classesCount: 3, studentsCount: 72, icon: TruckIcon },
    { id: 't-listrik', name: 'Teknik Instalasi Tenaga Listrik', classesCount: 3, studentsCount: 72, icon: BoltIcon },
    { id: 't-pemesinan', name: 'Teknik Pemesinan', classesCount: 3, studentsCount: 72, icon: Cog6ToothIcon }
];
</script>

<template>
    <PendampingLayout>
        <Head title="Siswa Boarding" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">
            <!-- Header Section -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Siswa Boarding</h1>
                <p class="text-sm text-gray-500 font-medium mt-0.5">Kelola data siswa boarding berdasarkan jurusan dan kelas</p>
            </div>

            <!-- Breadcrumbs -->
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 bg-white border border-slate-100 px-4 py-2.5 rounded-xl w-fit">
                <Link :href="route('pendamping.dashboard')" class="hover:text-blue-600 transition flex items-center space-x-1">
                    <HomeIcon class="w-3.5 h-3.5" />
                    <span>Home</span>
                </Link>
                <span>/</span>
                <span class="text-slate-500">Pendamping Panel</span>
                <span>/</span>
                <span class="text-blue-600 font-bold">Siswa Boarding</span>
            </nav>

            <!-- Department Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link 
                    v-for="dept in departments" 
                    :key="dept.id" 
                    :href="route('pendamping.siswa.jurusan', dept.id)"
                    class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex flex-col justify-between group"
                >
                    <div class="space-y-4">
                        <div class="p-3.5 bg-blue-50 text-blue-600 rounded-2xl w-fit group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <component :is="dept.icon" class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800 group-hover:text-blue-600 transition leading-snug">{{ dept.name }}</h3>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <div class="space-x-4">
                            <span>{{ dept.classesCount }} Kelas</span>
                            <span>{{ dept.studentsCount }} Siswa</span>
                        </div>
                        <span class="text-blue-600 font-bold hover:underline flex items-center space-x-0.5">
                            <span>Lihat Kelas</span>
                            <ChevronRightIcon class="w-3.5 h-3.5 text-blue-600 group-hover:translate-x-1 transition duration-300" />
                        </span>
                    </div>
                </Link>
            </div>
        </div>
    </PendampingLayout>
</template>
