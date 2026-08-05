<script setup>
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { 
    ArrowLeftIcon, 
    HomeIcon, 
    MagnifyingGlassIcon,
    EyeIcon
} from '@heroicons/vue/24/outline';

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

const searchQuery = ref('');

// Resolve friendly department name
const departmentName = computed(() => {
    if (props.jurusan === 't-konstruksi') return 'Teknik Konstruksi Perumahan';
    if (props.jurusan === 't-elektronika') return 'Teknik Elektronika Industri';
    if (props.jurusan === 't-kendaraan') return 'Teknik Kendaraan Ringan';
    if (props.jurusan === 't-listrik') return 'Teknik Instalasi Tenaga Listrik';
    if (props.jurusan === 't-pemesinan') return 'Teknik Pemesinan';
    return props.jurusan;
});

// Generate 24 mock students per class
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

    const isGirlsDept = props.jurusan === 't-elektronika' || props.jurusan === 't-konstruksi';
    const namesSource = isGirlsDept ? girlsNames : boysNames;
    const gender = isGirlsDept ? 'Perempuan' : 'Laki-laki';
    const dormPrefix = isGirlsDept ? 'Asrama Putri' : 'Asrama Putra';

    const list = [];
    for (let i = 1; i <= 24; i++) {
        // Create variations for mixed gender in other departments
        let currentGender = gender;
        let currentDorm = `${dormPrefix} - Kamar ${Math.ceil(i / 6)}`;
        let name = namesSource[(i - 1) % namesSource.length];

        if (props.jurusan !== 't-elektronika' && props.jurusan !== 't-konstruksi') {
            if (i > 18) {
                currentGender = 'Perempuan';
                currentDorm = `Asrama Putri - Kamar ${Math.ceil((i - 18) / 2)}`;
                name = girlsNames[(i - 1) % girlsNames.length];
            }
        }

        const nis = `202${5 - parseInt(props.kelas === 'X' ? 1 : (props.kelas === 'XI' ? 2 : 3))}0${i < 10 ? '0' + i : i}`;
        list.push({
            nis,
            name,
            gender: currentGender,
            dorm: currentDorm
        });
    }
    return list;
});

// Search filter
const filteredStudents = computed(() => {
    if (!searchQuery.value) return students.value;
    const query = searchQuery.value.toLowerCase();
    return students.value.filter(s => s.name.toLowerCase().includes(query) || s.nis.includes(query));
});
</script>

<template>
    <PendampingLayout>
        <Head :title="`Kelas ${props.kelas} - ${departmentName}`" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">
            <!-- Header & Back Button -->
            <div class="flex items-center space-x-3">
                <Link 
                    :href="route('pendamping.siswa.jurusan', props.jurusan)"
                    class="p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800 rounded-xl transition"
                >
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Kelas {{ props.kelas }} - {{ departmentName }}</h1>
                    <p class="text-sm text-gray-500 font-medium mt-0.5">Daftar siswa kelas {{ props.kelas }} jurusan {{ departmentName }}</p>
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
                <Link :href="route('pendamping.siswa.jurusan', props.jurusan)" class="hover:text-blue-600 transition">{{ departmentName }}</Link>
                <span>/</span>
                <span class="text-blue-600 font-bold">Kelas {{ props.kelas }}</span>
            </nav>

            <!-- Table Container -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <!-- Search bar -->
                <div class="p-5 border-b border-slate-100">
                    <div class="relative w-full max-w-xs">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <MagnifyingGlassIcon class="w-4 h-4" />
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari NIS / Nama Siswa..." 
                            v-model="searchQuery"
                            class="pl-10 pr-4 py-2.5 w-full border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-medium"
                        />
                    </div>
                </div>

                <!-- Students Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3.5 px-6">No</th>
                                <th class="py-3.5 px-6">NIS</th>
                                <th class="py-3.5 px-6">Nama Lengkap</th>
                                <th class="py-3.5 px-6">Jenis Kelamin</th>
                                <th class="py-3.5 px-6">Asrama</th>
                                <th class="py-3.5 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(s, idx) in filteredStudents" 
                                :key="idx" 
                                class="border-b border-slate-100 hover:bg-slate-50/50 transition text-sm"
                            >
                                <td class="py-3.5 px-6 text-slate-400 font-bold text-xs">{{ idx + 1 }}</td>
                                <td class="py-3.5 px-6 text-slate-500 font-bold text-xs">{{ s.nis }}</td>
                                <td class="py-3.5 px-6 text-blue-900 font-extrabold">{{ s.name }}</td>
                                <td class="py-3.5 px-6 text-slate-600 font-semibold text-xs">{{ s.gender }}</td>
                                <td class="py-3.5 px-6 text-slate-500 font-medium text-xs">{{ s.dorm }}</td>
                                <td class="py-3.5 px-6 text-right">
                                    <!-- Aksi Detail Button -->
                                    <Link 
                                        :href="route('pendamping.siswa.detail', s.nis)"
                                        class="inline-flex items-center space-x-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-[10px] font-bold px-3 py-1.5 rounded-xl border border-blue-100 transition"
                                    >
                                        <EyeIcon class="w-3.5 h-3.5" />
                                        <span>Detail</span>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PendampingLayout>
</template>
