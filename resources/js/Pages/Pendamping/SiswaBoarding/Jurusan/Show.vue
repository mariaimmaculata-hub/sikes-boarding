<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { 
    ArrowLeftIcon, 
    HomeIcon, 
    AcademicCapIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
    jurusan: {
        type: String,
        required: true
    }
});

// Resolve friendly name
const departmentName = computed(() => {
    if (props.jurusan === 't-konstruksi') return 'Teknik Konstruksi Perumahan';
    if (props.jurusan === 't-elektronika') return 'Teknik Elektronika Industri';
    if (props.jurusan === 't-kendaraan') return 'Teknik Kendaraan Ringan';
    if (props.jurusan === 't-listrik') return 'Teknik Instalasi Tenaga Listrik';
    if (props.jurusan === 't-pemesinan') return 'Teknik Pemesinan';
    return props.jurusan;
});

const classes = [
    { id: 'X', name: 'Kelas X', grade: 'Tingkat 1', studentsCount: 24 },
    { id: 'XI', name: 'Kelas XI', grade: 'Tingkat 2', studentsCount: 24 },
    { id: 'XII', name: 'Kelas XII', grade: 'Tingkat 3', studentsCount: 24 }
];
</script>

<template>
    <PendampingLayout>
        <Head :title="`Daftar Kelas - ${departmentName}`" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">
            <!-- Header & Back Button -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('pendamping.siswa.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">{{ departmentName }}</h1>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Daftar kelas di jurusan {{ departmentName }}</p>
                </div>
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
                <Link :href="route('pendamping.siswa.index')" class="hover:text-blue-600 transition">Siswa Boarding</Link>
                <span>/</span>
                <span class="text-blue-600 font-bold">{{ departmentName }}</span>
            </nav>

            <!-- Classes Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link 
                    v-for="c in classes" 
                    :key="c.id"
                    :href="route('pendamping.siswa.kelas', { jurusan: props.jurusan, kelas: c.id })"
                    class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm hover:shadow-md hover:border-emerald-300 transition flex flex-col justify-between group"
                >
                    <div class="space-y-4">
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl w-fit group-hover:bg-emerald-600 group-hover:text-white transition duration-300">
                            <AcademicCapIcon class="w-6 h-6" />
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-lg font-extrabold text-slate-800 group-hover:text-emerald-600 transition leading-snug">{{ c.name }}</h3>
                            <span class="text-slate-400 text-xs font-semibold block">{{ c.grade }}</span>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <span>{{ c.studentsCount }} Siswa</span>
                        <span class="text-emerald-600 font-bold hover:underline flex items-center space-x-0.5">
                            <span>Lihat Siswa</span>
                            <ChevronRightIcon class="w-3.5 h-3.5 text-emerald-600 group-hover:translate-x-1 transition duration-300" />
                        </span>
                    </div>
                </Link>
            </div>
        </div>
    </PendampingLayout>
</template>
