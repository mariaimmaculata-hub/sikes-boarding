<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    ArrowLeftIcon,
    UserCircleIcon,
    HomeIcon,
    BuildingOffice2Icon,
    ClipboardDocumentCheckIcon,
    HeartIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    nis: {
        type: String,
        required: true
    }
})

/*
|--------------------------------------------------------------------------
| Dummy Data
|--------------------------------------------------------------------------
*/

const students = [
{
    nis:'2024001',
    nama:'Ahmad Fauzi',
    jurusan:'Teknik Pemesinan',
    kelas:'X',
    asrama:'Putra A-01',
    status:'Sehat',
    batch:'Batch 1',
    klinik:'0 kali',
    jk:'Laki-laki',
    ttl:'Semarang, 12 April 2009',
    alamat:'Kab. Pemalang',
    wali:'Bapak Suyono',
    telp:'081234567890',
    tinggi:'170 cm',
    berat:'60 kg',
    golongan:'O',
    alergi:'Tidak Ada',
    penyakit:'Tidak Ada'
},

{
    nis:'202400ZZ2',
    nama:'Budi Santoso',
    jurusan:'Teknik Pemesinan',
    kelas:'X',
    asrama:'Putra A-02',
    status:'Perlu Pemantauan',
    batch:'Batch 2',
    klinik:'2 kali',
    jk:'Laki-laki',
    ttl:'Tegal, 20 Juni 2009',
    alamat:'Kab. Tegal',
    wali:'Bapak Slamet',
    telp:'081298765432',
    tinggi:'168 cm',
    berat:'55 kg',
    golongan:'A',
    alergi:'Debu',
    penyakit:'Asma'
},

{
    nis:'240003',
    nama:'Rina Lestari',
    jurusan:'Teknik Elektronika Industri',
    kelas:'XI',
    asrama:'Putri B-04',
    status:'Sakit',
    batch:'Batch 3',
    klinik:'5 kali',
    jk:'Perempuan',
    ttl:'Brebes, 10 Februari 2008',
    alamat:'Kab. Brebes',
    wali:'Ibu Sri Wahyuni',
    telp:'081377778888',
    tinggi:'160 cm',
    berat:'48 kg',
    golongan:'B',
    alergi:'Seafood',
    penyakit:'Maag'
}
]

const student = computed(() => {
    return students.find(item => item.nis == props.nis)
})

const statusColor = computed(() => {

    if(!student.value) return ''

    if(student.value.status == 'Sehat')
        return 'bg-green-100 text-green-700'

    if(student.value.status == 'Perlu Pemantauan')
        return 'bg-yellow-100 text-yellow-700'

    return 'bg-red-100 text-red-700'
})

const histories = [
{
    tanggal:'12 Agustus 2026',
    kegiatan:'Pemeriksaan Berkala',
    hasil:'Tekanan darah normal, berat badan ideal.'
},
{
    tanggal:'15 Agustus 2026',
    kegiatan:'Kunjungan Klinik',
    hasil:'Keluhan demam ringan, diberikan Paracetamol.'
},
{
    tanggal:'22 Agustus 2026',
    kegiatan:'Pemeriksaan Berkala',
    hasil:'Kondisi kembali normal.'
}
]
</script>

<template>

<PendampingLayout>

<Head title="Detail Siswa Boarding"/>

<div class="max-w-7xl mx-auto p-6 space-y-6">

    <!-- Header -->

    <div class="flex items-center gap-3">

        <Link
            :href="route('pendamping.siswa.kelas',{
                jurusan:'t-pemesinan',
                kelas:student?.kelas
            })"
            class="p-2 rounded-xl hover:bg-slate-100"
        >
            <ArrowLeftIcon class="w-5 h-5"/>
        </Link>

        <div>

            <h1 class="text-2xl font-bold text-slate-800">
                Detail Siswa Boarding
            </h1>

            <p class="text-sm text-slate-500">
                Informasi lengkap siswa boarding
            </p>

        </div>

    </div>


    <div class="grid lg:grid-cols-3 gap-6">

        <!-- Profil -->

        <div class="bg-white rounded-2xl border shadow-sm p-6">

            <div class="flex flex-col items-center">

                <div class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center">

                    <UserCircleIcon class="w-16 h-16 text-blue-600"/>

                </div>

                <h2 class="mt-4 text-xl font-bold">
                    {{ student.nama }}
                </h2>

                <p class="text-slate-500">
                    {{ student.nis }}
                </p>

                <span
                    class="mt-3 px-4 py-1 rounded-full text-xs font-semibold"
                    :class="statusColor"
                >
                    {{ student.status }}
                </span>

            </div>

            <div class="border-t mt-6 pt-6 space-y-4">

                <div class="flex justify-between">
                    <span>Jurusan</span>
                    <span class="font-semibold">{{ student.jurusan }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Kelas</span>
                    <span class="font-semibold">{{ student.kelas }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Asrama</span>
                    <span class="font-semibold">{{ student.asrama }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Batch</span>
                    <span class="font-semibold">{{ student.batch }}</span>
                </div>

                <div class="flex justify-between">
                    <span>Kunjungan Klinik</span>
                    <span class="font-semibold">{{ student.klinik }}</span>
                </div>

            </div>

        </div>


        <!-- Biodata -->

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border shadow-sm">

                <div class="border-b p-5 flex items-center gap-2">

                    <BuildingOffice2Icon class="w-5 h-5 text-blue-600"/>

                    <h3 class="font-bold">
                        Biodata Siswa
                    </h3>

                </div>

                <div class="grid md:grid-cols-2 gap-6 p-6">

                    <div>

                        <label class="text-xs text-slate-500">
                            Jenis Kelamin
                        </label>

                        <p class="font-semibold">
                            {{ student.jk }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Tempat, Tanggal Lahir
                        </label>

                        <p class="font-semibold">
                            {{ student.ttl }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Alamat
                        </label>

                        <p class="font-semibold">
                            {{ student.alamat }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Wali
                        </label>

                        <p class="font-semibold">
                            {{ student.wali }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            No HP Orang Tua
                        </label>

                        <p class="font-semibold">
                            {{ student.telp }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- Kondisi Fisik -->

            <div class="bg-white rounded-2xl border shadow-sm">

                <div class="border-b p-5 flex items-center gap-2">

                    <HeartIcon class="w-5 h-5 text-red-500"/>

                    <h3 class="font-bold">
                        Kondisi Kesehatan
                    </h3>

                </div>

                <div class="grid md:grid-cols-2 gap-6 p-6">

                    <div>

                        <label class="text-xs text-slate-500">
                            Tinggi Badan
                        </label>

                        <p class="font-semibold">
                            {{ student.tinggi }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Berat Badan
                        </label>

                        <p class="font-semibold">
                            {{ student.berat }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Golongan Darah
                        </label>

                        <p class="font-semibold">
                            {{ student.golongan }}
                        </p>

                    </div>

                    <div>

                        <label class="text-xs text-slate-500">
                            Alergi
                        </label>

                        <p class="font-semibold">
                            {{ student.alergi }}
                        </p>

                    </div>

                    <div class="md:col-span-2">

                        <label class="text-xs text-slate-500">
                            Riwayat Penyakit
                        </label>

                        <p class="font-semibold">
                            {{ student.penyakit }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- Riwayat -->

            <div class="bg-white rounded-2xl border shadow-sm">

                <div class="border-b p-5 flex items-center gap-2">

                    <ClipboardDocumentCheckIcon class="w-5 h-5 text-green-600"/>

                    <h3 class="font-bold">
                        Riwayat Pemeriksaan
                    </h3>

                </div>

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs">
                                Tanggal
                            </th>

                            <th class="px-5 py-3 text-left text-xs">
                                Kegiatan
                            </th>

                            <th class="px-5 py-3 text-left text-xs">
                                Hasil
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <tr
                            v-for="item in histories"
                            :key="item.tanggal"
                            class="border-t"
                        >

                            <td class="px-5 py-4">
                                {{ item.tanggal }}
                            </td>

                            <td class="px-5 py-4">
                                {{ item.kegiatan }}
                            </td>

                            <td class="px-5 py-4">
                                {{ item.hasil }}
                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</PendampingLayout>

</template>