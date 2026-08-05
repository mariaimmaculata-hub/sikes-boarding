<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { ArrowLeftIcon, HomeIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
    jurusan: {
        type: String,
        required: true
    },
    kelas: {
        type: String,
        required: true
    }
});

// Resolve department name
const departmentName = computed(() => {
    if (props.jurusan === 't-konstruksi') return 'Teknik Konstruksi Perumahan';
    if (props.jurusan === 't-elektronika') return 'Teknik Elektronika Industri';
    if (props.jurusan === 't-kendaraan') return 'Teknik Kendaraan Ringan';
    if (props.jurusan === 't-listrik') return 'Teknik Instalasi Tenaga Listrik';
    if (props.jurusan === 't-pemesinan') return 'Teknik Pemesinan';
    return props.jurusan;
});

// Generate 24 mock students
const students = computed(() => {
    const boysNames = [
        'Ahmad Fauzi', 'Budi Santoso', 'Candra Wijaya', 'Dedi Kurniawan', 
        'Eko Prasetyo', 'Fajar Ramadhan', 'Guntur Wibowo', 'Hendra Saputra', 
        'Ilham Hidayat', 'Joko Susilo', 'Kurnia Sandy', 'Latif Nur', 
        'Mulyadi', 'Naufal Rizqi', 'Oki Ardiansyah', 'Rian Hidayat', 
        'Surya Darma', 'Taufik Ismail', 'Utomo Tri', 'Wawan Setiawan', 
        'Yanto Basna', 'Zainal Abidin', 'Adi Nugroho', 'Bagus Kahfi'
    ];
    
    const girlsNames = [
        'Aulia Rahma', 'Rina Lestari', 'Nadia Putri', 'Putri Anjani',
        'Salma Putri', 'Dewi Lestari', 'Siti Nurhaliza', 'Fitriani',
        'Laras Ayu', 'Megawati', 'Novita Sari', 'Oktavia',
        'Putri Lestari', 'Qonitah', 'Risma', 'Sari Indah',
        'Tri Wahyuni', 'Ulfa', 'Vina', 'Wulandari',
        'Yuni', 'Zahra', 'Amalia', 'Bella'
    ];

    const isGirlsDept = props.jurusan === 't-elektronika' || props.jurusan === 't-konstruksi'; // Just some logic to vary gender
    const namesSource = isGirlsDept ? girlsNames : boysNames;
    const gender = isGirlsDept ? 'Perempuan' : 'Laki-laki';
    const dormPrefix = isGirlsDept ? 'Asrama Putri' : 'Asrama Putra';

    const list = [];
    for (let i = 1; i <= 24; i++) {
        const nis = `202${5 - parseInt(props.kelas === 'X' ? 1 : (props.kelas === 'XI' ? 2 : 3))}0${i < 10 ? '0' + i : i}`;
        list.push({
            nis,
            name: namesSource[(i - 1) % namesSource.length],
            gender,
            dorm: `${dormPrefix} - Kamar ${Math.ceil(i / 6)}`
        });
    }
    return list;
});
</script>

<template>
    <PetugasLayout>
        <Head :title="`Siswa Kelas ${props.kelas} - ${departmentName}`" />

        <div class="space-y-6">
            <!-- Header & Back -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('petugas.master.kelas.jurusan', props.jurusan)"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 tracking-tight">Daftar Siswa {{ props.kelas }}</h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">{{ departmentName }}</p>
                </div>
            </div>

            <!-- Breadcrumbs -->
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
                <Link :href="route('petugas.master.kelas.jurusan', props.jurusan)" class="hover:text-blue-600 transition">{{ departmentName }}</Link>
                <span>/</span>
                <span class="text-blue-600 font-bold">Kelas {{ props.kelas }}</span>
            </nav>

            <!-- Students List Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3.5 px-6">No</th>
                                <th class="py-3.5 px-6">NIS</th>
                                <th class="py-3.5 px-6">Nama</th>
                                <th class="py-3.5 px-6">Jenis Kelamin</th>
                                <th class="py-3.5 px-6">Asrama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(s, idx) in students" :key="idx" class="border-b border-slate-100 hover:bg-slate-50/50 transition text-sm">
                                <td class="py-3 px-6 text-slate-400 font-bold text-xs">{{ idx + 1 }}</td>
                                <td class="py-3 px-6 text-slate-500 font-bold text-xs">{{ s.nis }}</td>
                                <td class="py-3 px-6 text-blue-900 font-extrabold">{{ s.name }}</td>
                                <td class="py-3 px-6 text-slate-600 font-semibold text-xs">{{ s.gender }}</td>
                                <td class="py-3 px-6 text-slate-500 font-medium text-xs">{{ s.dorm }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PetugasLayout>
</template>
