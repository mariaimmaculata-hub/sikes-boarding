<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowLeftIcon, ChevronRightIcon, HomeIcon } from '@heroicons/vue/24/outline';

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
    { id: 'X', name: 'Kelas X', studentsCount: 24 },
    { id: 'XI', name: 'Kelas XI', studentsCount: 24 },
    { id: 'XII', name: 'Kelas XII', studentsCount: 24 }
];
</script>

<template>
    <PetugasLayout>
        <Head :title="`Daftar Kelas - ${departmentName}`" />

        <div class="space-y-6">
            <!-- Header & Back Button -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('petugas.master.kelas.index')"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">{{ departmentName }}</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Pilih tingkatan kelas untuk melihat daftar siswa.</p>
                </div>
            </div>

            <!-- Breadcrumb info banner -->
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 bg-white border border-slate-100 px-4 py-2.5 rounded-xl w-fit">
                <Link :href="route('petugas.dashboard')" class="hover:text-blue-600 transition flex items-center space-x-1">
                    <HomeIcon class="w-3.5 h-3.5" />
                    <span>Home</span>
                </Link>
                <span>/</span>
                <Link :href="route('petugas.master.kelas.index')" class="hover:text-blue-600 transition">Master Data</Link>
                <span>/</span>
                <span class="text-slate-700">Kelas</span>
                <span>/</span>
                <span class="text-blue-600 font-bold">{{ departmentName }}</span>
            </nav>

            <!-- Classes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link 
                    v-for="c in classes" 
                    :key="c.id"
                    :href="route('petugas.master.kelas.detail', { jurusan: props.jurusan, kelas: c.id })"
                    class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex flex-col justify-between group"
                >
                    <div class="space-y-1">
                        <span class="text-slate-400 text-[10px] font-extrabold uppercase tracking-widest block">Tingkatan</span>
                        <h3 class="text-lg font-extrabold text-slate-800 group-hover:text-blue-600 transition leading-snug">{{ c.name }}</h3>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <span>{{ c.studentsCount }} Siswa Terdaftar</span>
                        <ChevronRightIcon class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition duration-300" />
                    </div>
                </Link>
            </div>
        </div>
    </PetugasLayout>
</template>
