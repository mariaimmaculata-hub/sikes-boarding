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

// Nama Jurusan
const departmentName = computed(() => {
    switch (props.jurusan) {
        case 't-konstruksi':
            return 'Teknik Konstruksi Perumahan';

        case 't-elektronika':
            return 'Teknik Elektronika Industri';

        case 't-kendaraan':
            return 'Teknik Kendaraan Ringan';

        case 't-listrik':
            return 'Teknik Instalasi Tenaga Listrik';

        case 't-pemesinan':
            return 'Teknik Pemesinan';

        default:
            return props.jurusan;
    }
});

// Dummy Data
const students = computed(() => {

    const boysNames = [
        'Ahmad Fauzi',
        'Budi Santoso',
        'Candra Wijaya',
        'Dedi Kurniawan',
        'Eko Prasetyo',
        'Fajar Ramadhan',
        'Guntur Wibowo',
        'Hendra Saputra',
        'Ilham Hidayat',
        'Joko Susilo',
        'Kurnia Sandy',
        'Latif Nur',
        'Mulyadi',
        'Naufal Rizqi',
        'Oki Ardiansyah',
        'Rian Hidayat',
        'Surya Darma',
        'Taufik Ismail',
        'Utomo Tri',
        'Wawan Setiawan',
        'Yanto Basna',
        'Zainal Abidin',
        'Adi Nugroho',
        'Bagus Kahfi'
    ];

    const girlsNames = [
        'Aulia Rahma',
        'Rina Lestari',
        'Nadia Putri',
        'Putri Anjani',
        'Salma Putri',
        'Dewi Lestari',
        'Siti Nurhaliza',
        'Fitriani',
        'Laras Ayu',
        'Megawati',
        'Novita Sari',
        'Oktavia',
        'Putri Lestari',
        'Qonitah',
        'Risma',
        'Sari Indah',
        'Tri Wahyuni',
        'Ulfa',
        'Vina',
        'Wulandari',
        'Yuni',
        'Zahra',
        'Amalia',
        'Bella'
    ];

    const healthStatus = [
        'Sehat',
        'Perlu Pemantauan',
        'Sakit'
    ];

    const batch = [
    'Batch 1',
    'Batch 2',
    'Batch 3'
];

const clinicVisit = [
    'Belum Pernah',
    '1 Kali',
    '2 Kali',
    '3 Kali',
    '5 Kali'
];

    const isGirlsDept =
        props.jurusan === 't-elektronika' ||
        props.jurusan === 't-konstruksi';

    const namesSource = isGirlsDept
        ? girlsNames
        : boysNames;


    const defaultDorm = isGirlsDept
        ? 'Asrama Putri'
        : 'Asrama Putra';

    const list = [];

    for (let i = 1; i <= 24; i++) {

        
        let dorm = `${defaultDorm} - Kamar ${Math.ceil(i / 6)}`;
        let name = namesSource[(i - 1) % namesSource.length];

        if (!isGirlsDept && i > 18) {


            dorm = `Asrama Putri - Kamar ${Math.ceil((i - 18) / 2)}`;

            name = girlsNames[(i - 1) % girlsNames.length];
        }

        const nis =
            `202${5 - (props.kelas === 'X'
                ? 1
                : props.kelas === 'XI'
                ? 2
                : 3)
            }0${i < 10 ? '0' + i : i}`;

        list.push({
    nis,
    name,
    dorm,
    status: healthStatus[i % 3],
    batch: batch[i % 3],
    clinic: clinicVisit[i % 5]
});
    }

    return list;

});

// Search
const filteredStudents = computed(() => {

    if (!searchQuery.value) {

        return students.value;

    }

    const query = searchQuery.value.toLowerCase();

    return students.value.filter(student =>

        student.name.toLowerCase().includes(query) ||

        student.nis.includes(query)

    );

});
</script>

<template>
    <PendampingLayout>

        <Head :title="`Kelas ${props.kelas} - ${departmentName}`" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">

            <!-- Header -->
            <div class="flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <Link
                        :href="route('pendamping.siswa.jurusan', props.jurusan)"
                        class="p-2 rounded-xl hover:bg-slate-100 transition"
                    >
                        <ArrowLeftIcon class="w-5 h-5 text-slate-600"/>
                    </Link>

                    <div>

                        <h1 class="text-2xl font-bold text-slate-800">
                            Kelas {{ props.kelas }}
                        </h1>

                        <p class="text-sm text-slate-500 mt-1">
                            {{ departmentName }}
                        </p>

                    </div>

                </div>

            </div>

            <!-- Breadcrumb -->

            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">

                <Link
                    :href="route('pendamping.dashboard')"
                    class="flex items-center gap-1 hover:text-blue-600"
                >
                    <HomeIcon class="w-4 h-4"/>
                    Home
                </Link>

                <span>/</span>

                <Link
                    :href="route('pendamping.siswa.index')"
                    class="hover:text-blue-600"
                >
                    Siswa Boarding
                </Link>

                <span>/</span>

                <Link
                    :href="route('pendamping.siswa.jurusan', props.jurusan)"
                    class="hover:text-blue-600"
                >
                    {{ departmentName }}
                </Link>

                <span>/</span>

                <span class="text-blue-600">
                    Kelas {{ props.kelas }}
                </span>

            </nav>

            <!-- Card -->

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

                <!-- Header Card -->

                <div class="p-5 border-b border-slate-100 flex items-center justify-between">

                    <div>

                        <h2 class="font-bold text-slate-800">
                            Daftar Siswa Boarding
                        </h2>

                        <p class="text-xs text-slate-500 mt-1">
                            Total {{ filteredStudents.length }} siswa
                        </p>
                        <div class="flex gap-6 mt-3 text-xs">

    <span class="font-semibold text-green-600">
        🟢 Sehat :
        {{ filteredStudents.filter(s=>s.status=='Sehat').length }}
    </span>

    <span class="font-semibold text-yellow-600">
        🟡 Pemantauan :
        {{ filteredStudents.filter(s=>s.status=='Perlu Pemantauan').length }}
    </span>

    <span class="font-semibold text-red-600">
        🔴 Sakit :
        {{ filteredStudents.filter(s=>s.status=='Sakit').length }}
    </span>

</div>

                    </div>

                    <!-- Search -->

                    <div class="relative w-72">

                        <MagnifyingGlassIcon
                            class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
                        />

                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari NIS / Nama Siswa..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        />

                    </div>

                </div>

                <!-- Table -->

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead>

                        <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">

                            <th class="px-6 py-4 text-left">
                                No
                            </th>

                            <th class="px-6 py-4 text-left">
                                NIS
                            </th>

                            <th class="px-6 py-4 text-left">
                                Nama Siswa
                            </th>


                            <th class="px-6 py-4 text-left">
                                Asrama
                            </th>

                            <th class="px-6 py-4 text-left">
                                Status
                            </th>

                            <th class="px-6 py-4 text-left">
                                Batch Pemeriksaan
                            </th>

                            <th class="px-6 py-4 text-left">
                            Kunjungan Klinik</th>

                            <th class="px-6 py-4 text-right">
                                Aksi
                            </th>

                        </tr>

                        </thead>

                        <tbody>
    <tr
        v-for="(student, index) in filteredStudents"
        :key="student.nis"
        class="border-b border-slate-100 hover:bg-slate-50 transition"
    >
        <!-- No -->
        <td class="px-6 py-4 text-sm text-slate-500">
            {{ index + 1 }}
        </td>

        <!-- NIS -->
        <td class="px-6 py-4 text-sm font-medium text-slate-700">
            {{ student.nis }}
        </td>

        <!-- Nama -->
        <td class="px-6 py-4">
            <div class="font-semibold text-slate-800">
                {{ student.name }}
            </div>
        </td>


        <!-- Asrama -->
        <td class="px-6 py-4 text-sm text-slate-600">
            {{ student.dorm }}
        </td>

        <!-- Status -->
        <td class="px-6 py-4">

            <span
                v-if="student.status=='Sehat'"
                class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-3 py-1 text-xs font-semibold"
            >
                Sehat
            </span>

            <span
                v-else-if="student.status=='Perlu Pemantauan'"
                class="inline-flex items-center rounded-full bg-yellow-100 text-yellow-700 px-3 py-1 text-xs font-semibold"
            >
                Perlu Pemantauan
            </span>

            <span
                v-else
                class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-3 py-1 text-xs font-semibold"
            >
                Sakit
            </span>

        </td>

      <td class="px-6 py-4 text-sm text-slate-600">
    {{ student.batch }}
    </td>

<td class="px-6 py-4 text-sm text-slate-600">
    {{ student.clinic }}
</td>
        <!-- Aksi -->
        <td class="px-6 py-4 text-right">

            <Link
                :href="route('pendamping.siswa.detail', student.nis)"
                class="inline-flex items-center gap-2 bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-2 rounded-lg text-xs font-semibold transition"
            >
                <EyeIcon class="w-4 h-4"/>
                Detail
            </Link>

        </td>

    </tr>

    <!-- Empty State -->
    <tr v-if="filteredStudents.length === 0">
        <td
            colspan="8"
            class="text-center py-10 text-slate-400"
        >
            Tidak ada data siswa.
        </td>
    </tr>

</tbody>

                    </table>

                </div>

            </div>

        </div>

    </PendampingLayout>

</template>