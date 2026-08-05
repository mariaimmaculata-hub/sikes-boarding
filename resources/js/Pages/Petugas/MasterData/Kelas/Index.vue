<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { AcademicCapIcon, ChevronRightIcon } from '@heroicons/vue/24/outline';

const departments = [
    { id: 't-konstruksi', name: 'Teknik Konstruksi Perumahan', classesCount: 3, studentsCount: 72 },
    { id: 't-elektronika', name: 'Teknik Elektronika Industri', classesCount: 3, studentsCount: 72 },
    { id: 't-kendaraan', name: 'Teknik Kendaraan Ringan', classesCount: 3, studentsCount: 72 },
    { id: 't-listrik', name: 'Teknik Instalasi Tenaga Listrik', classesCount: 3, studentsCount: 72 },
    { id: 't-pemesinan', name: 'Teknik Pemesinan', classesCount: 3, studentsCount: 72 }
];
</script>

<template>
    <PetugasLayout>
        <Head title="Master Data Kelas / Asrama" />

        <div class="space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">Data Kelas &amp; Asrama</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Navigasi hierarki jurusan, kelas, dan data asrama siswa.</p>
            </div>

            <!-- Department Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link 
                    v-for="dept in departments" 
                    :key="dept.id" 
                    :href="route('petugas.master.kelas.jurusan', dept.id)"
                    class="bg-white rounded-2xl border border-slate-150 p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition flex flex-col justify-between group"
                >
                    <div class="space-y-3">
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl w-fit group-hover:bg-blue-600 group-hover:text-white transition duration-300">
                            <AcademicCapIcon class="w-6 h-6" />
                        </div>
                        <h3 class="text-base font-extrabold text-slate-800 group-hover:text-blue-600 transition leading-snug">{{ dept.name }}</h3>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-slate-500">
                        <div class="space-x-4">
                            <span>{{ dept.classesCount }} Kelas</span>
                            <span>{{ dept.studentsCount }} Siswa</span>
                        </div>
                        <ChevronRightIcon class="w-4 h-4 text-slate-400 group-hover:translate-x-1 transition duration-300" />
                    </div>
                </Link>
            </div>
        </div>
    </PetugasLayout>
</template>
