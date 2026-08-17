<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
} from '@heroicons/vue/24/outline'


// ==================================================
// PROPS
// ==================================================

const props = defineProps({
    kunjungan: {
        type: Object,
        required: true,
    },
})

const kunjungan = props.kunjungan


// ==================================================
// BREADCRUMB
// ==================================================

const breadcrumbs = [
    {
        name: 'Dashboard',
        url: route('admin.dashboard'),
    },
    {
        name: 'Kunjungan Klinik',
        url: route('admin.kunjungan.index'),
    },
    {
        name: 'Detail Kunjungan',
        url: '#',
    },
]


// ==================================================
// FORMAT TANGGAL
// ==================================================

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}


// ==================================================
// FORMAT WAKTU
// ==================================================

const formatWaktu = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        timeZone: 'Asia/Jakarta',
    }) + ' WIB'
}


// ==================================================
// KELAS SISWA
// ==================================================

const kelasSiswa = () => {
    const siswa = kunjungan?.siswa

    if (!siswa) return '-'

    if (siswa.kelas?.nama_kelas) {
        return siswa.kelas.nama_kelas
    }

    if (siswa.kelas?.nama) {
        return siswa.kelas.nama
    }

    return siswa.kelas || '-'
}


// ==================================================
// NAMA PEMERIKSA
// ==================================================

const pemeriksaNama = () => {
    return (
        kunjungan?.pemeriksa?.name ||
        kunjungan?.user?.name ||
        kunjungan?.pemeriksa_nama ||
        '-'
    )
}


// ==================================================
// STATUS STYLE
// ==================================================

const statusStyle = (status) => {
    switch (String(status || '').toLowerCase()) {

        case 'selesai':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200'

        case 'berlangsung':
            return 'bg-blue-50 text-blue-700 border-blue-200'

        case 'menunggu':
            return 'bg-amber-50 text-amber-700 border-amber-200'

        case 'batal':
            return 'bg-red-50 text-red-700 border-red-200'

        default:
            return 'bg-slate-50 text-slate-600 border-slate-200'
    }
}


// ==================================================
// INITIAL NAMA SISWA
// ==================================================

const initialNama = () => {
    const nama = kunjungan?.siswa?.nama || '?'

    return nama.charAt(0).toUpperCase()
}
</script>


<template>

    <Head title="Detail Kunjungan Klinik" />


    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="min-h-screen bg-slate-50">


            <!-- ================================================= -->
            <!-- TOP HEADER -->
            <!-- ================================================= -->

            <div class="border-b border-slate-200 bg-white">

                <div class="mx-auto max-w-7xl px-6 py-6">

                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <!-- ================================================= -->
                        <!-- JUDUL + TOMBOL KEMBALI -->
                        <!-- ================================================= -->

                        <div class="flex items-center gap-3">

                            <Link
                                :href="route('admin.kunjungan.index')"
                                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                                title="Kembali"
                            >

                                <ArrowLeftIcon
                                    class="h-5 w-5"
                                />

                            </Link>


                            <div>

                                <div
                                    class="mb-1 flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-[#2563EB]"
                                >

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-[#2563EB]"
                                    ></span>

                                    Kunjungan Klinik

                                </div>


                                <h1
                                    class="text-2xl font-bold tracking-tight text-slate-900"
                                >
                                    Detail Kunjungan
                                </h1>


                                <p
                                    class="mt-1 text-sm text-slate-500"
                                >
                                    Informasi lengkap riwayat pemeriksaan kesehatan siswa.
                                </p>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- STATUS -->
                        <!-- ================================================= -->

                        <div
                            class="inline-flex w-fit items-center gap-2 rounded-lg border px-3.5 py-2 text-sm font-semibold"
                            :class="statusStyle(kunjungan.status)"
                        >

                            <span
                                class="h-2 w-2 rounded-full bg-current"
                            ></span>

                            {{ kunjungan.status || 'Tidak ada status' }}

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================================================= -->
            <!-- CONTENT -->
            <!-- ================================================= -->

            <main class="mx-auto max-w-7xl px-6 py-7">


                <!-- ================================================= -->
                <!-- STUDENT HERO -->
                <!-- ================================================= -->

                <section
                    class="relative mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <!-- ACCENT -->

                    <div
                        class="absolute left-0 top-0 h-full w-1 bg-[#2563EB]"
                    ></div>


                    <div class="p-6 md:p-7">

                        <div
                            class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between"
                        >


                            <!-- ================================================= -->
                            <!-- SISWA -->
                            <!-- ================================================= -->

                            <div class="flex items-center gap-4">

                                <div
                                    class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-[#2563EB] text-xl font-bold text-white shadow-sm"
                                >
                                    {{ initialNama() }}
                                </div>


                                <div>

                                    <p
                                        class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                                    >
                                        Siswa
                                    </p>


                                    <h2
                                        class="mt-1 text-xl font-bold text-slate-900"
                                    >
                                        {{ kunjungan.siswa?.nama || '-' }}
                                    </h2>


                                    <div
                                        class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500"
                                    >

                                        <span>
                                            NISN {{ kunjungan.siswa?.nisn || '-' }}
                                        </span>


                                        <span
                                            class="hidden text-slate-300 sm:inline"
                                        >
                                            •
                                        </span>


                                        <span>
                                            {{ kelasSiswa() }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- ================================================= -->
                            <!-- TANGGAL -->
                            <!-- ================================================= -->

                            <div
                                class="flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3"
                            >

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-[#2563EB] shadow-sm"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />

                                    </svg>

                                </div>


                                <div>

                                    <p
                                        class="text-xs font-medium text-slate-400"
                                    >
                                        Tanggal Kunjungan
                                    </p>


                                    <p
                                        class="mt-0.5 text-sm font-semibold text-slate-800"
                                    >
                                        {{ formatTanggal(kunjungan.updated_at) }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- ================================================= -->
                        <!-- IDENTITAS -->
                        <!-- ================================================= -->

                        <div
                            class="mt-6 grid grid-cols-1 border-t border-slate-100 pt-5 sm:grid-cols-3"
                        >


                            <!-- JENIS KELAMIN -->

                            <div
                                class="border-b border-slate-100 pb-4 sm:border-b-0 sm:border-r sm:pr-5"
                            >

                                <p
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Jenis Kelamin
                                </p>


                                <p
                                    class="mt-1 text-sm font-semibold text-slate-800"
                                >
                                    {{ kunjungan.siswa?.jenis_kelamin || '-' }}
                                </p>

                            </div>


                            <!-- PERIODE -->

                            <div
                                class="border-b border-slate-100 py-4 sm:border-b-0 sm:border-r sm:px-5 sm:py-0"
                            >

                                <p
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Periode
                                </p>


                                <p
                                    class="mt-1 text-sm font-semibold text-slate-800"
                                >
                                    {{ kunjungan.periode?.nama_periode || '-' }}
                                </p>

                            </div>


                            <!-- WAKTU -->

                            <div
                                class="pt-4 sm:pl-5 sm:pt-0"
                            >

                                <p
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Waktu Kunjungan
                                </p>


                                <p
                                    class="mt-1 text-sm font-semibold text-slate-800"
                                >
                                    {{ formatWaktu(kunjungan.updated_at) }}
                                </p>

                            </div>

                        </div>

                    </div>

                </section>


                <!-- ================================================= -->
                <!-- MAIN GRID -->
                <!-- ================================================= -->

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">


                    <!-- ================================================= -->
                    <!-- PEMERIKSAAN -->
                    <!-- ================================================= -->

                    <section
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:col-span-2"
                    >

                        <div
                            class="border-b border-slate-100 px-6 py-5"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#2563EB]"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >

                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                        />

                                    </svg>

                                </div>


                                <div>

                                    <h2
                                        class="font-bold text-slate-900"
                                    >
                                        Hasil Pemeriksaan
                                    </h2>


                                    <p
                                        class="text-xs text-slate-500"
                                    >
                                        Catatan pemeriksaan kesehatan siswa
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="divide-y divide-slate-100">


                            <!-- ================================================= -->
                            <!-- KELUHAN -->
                            <!-- ================================================= -->

                            <div class="px-6 py-5">

                                <div class="mb-2 flex items-center gap-2">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-slate-400"
                                    ></span>


                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Keluhan
                                    </p>

                                </div>


                                <p
                                    class="text-sm leading-6 text-slate-700"
                                >
                                    {{ kunjungan.keluhan || 'Tidak ada keluhan yang dicatat.' }}
                                </p>

                            </div>


                            <!-- ================================================= -->
                            <!-- PEMERIKSAAN -->
                            <!-- ================================================= -->

                            <div class="px-6 py-5">

                                <div class="mb-2 flex items-center gap-2">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-[#2563EB]"
                                    ></span>


                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Pemeriksaan
                                    </p>

                                </div>


                                <p
                                    class="text-sm leading-6 text-slate-700"
                                >
                                    {{ kunjungan.pemeriksaan || 'Tidak ada data pemeriksaan.' }}
                                </p>

                            </div>


                            <!-- ================================================= -->
                            <!-- DIAGNOSIS -->
                            <!-- ================================================= -->

                            <div
                                class="bg-blue-50/50 px-6 py-5"
                            >

                                <div class="mb-2 flex items-center gap-2">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-[#2563EB]"
                                    ></span>


                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-[#2563EB]"
                                    >
                                        Diagnosis
                                    </p>

                                </div>


                                <p
                                    class="text-sm font-semibold leading-6 text-blue-900"
                                >
                                    {{ kunjungan.diagnosis || 'Tidak ada diagnosis.' }}
                                </p>

                            </div>


                            <!-- ================================================= -->
                            <!-- TINDAKAN -->
                            <!-- ================================================= -->

                            <div class="px-6 py-5">

                                <div class="mb-2 flex items-center gap-2">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-slate-400"
                                    ></span>


                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Tindakan
                                    </p>

                                </div>


                                <p
                                    class="text-sm leading-6 text-slate-700"
                                >
                                    {{ kunjungan.tindakan || 'Tidak ada tindakan yang dicatat.' }}
                                </p>

                            </div>


                            <!-- ================================================= -->
                            <!-- CATATAN -->
                            <!-- ================================================= -->

                            <div class="px-6 py-5">

                                <div class="mb-2 flex items-center gap-2">

                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-slate-400"
                                    ></span>


                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                    >
                                        Catatan Tambahan
                                    </p>

                                </div>


                                <p
                                    class="text-sm leading-6 text-slate-700"
                                >
                                    {{ kunjungan.catatan || 'Tidak ada catatan tambahan.' }}
                                </p>

                            </div>

                        </div>

                    </section>


                    <!-- ================================================= -->
                    <!-- SIDEBAR -->
                    <!-- ================================================= -->

                    <aside class="space-y-6">


                        <!-- ================================================= -->
                        <!-- PEMERIKSA -->
                        <!-- ================================================= -->

                        <section
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                        >

                            <div
                                class="border-b border-slate-100 px-5 py-4"
                            >

                                <p
                                    class="text-xs font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Pemeriksa
                                </p>

                            </div>


                            <div class="p-5">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-[#2563EB]"
                                    >

                                        {{
                                            pemeriksaNama()
                                                ?.charAt(0)
                                                ?.toUpperCase() || '?'
                                        }}

                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="truncate font-semibold text-slate-900"
                                        >
                                            {{ pemeriksaNama() }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-500"
                                        >
                                            Petugas Klinik
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </section>


                        <!-- ================================================= -->
                        <!-- DETAIL KUNJUNGAN -->
                        <!-- ================================================= -->

                        <section
                            class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                        >

                            <p
                                class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-400"
                            >
                                Informasi Kunjungan
                            </p>


                            <div class="space-y-4">


                                <!-- TANGGAL -->

                                <div>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        Tanggal
                                    </p>


                                    <p
                                        class="mt-1 text-sm font-semibold text-slate-800"
                                    >
                                        {{ formatTanggal(kunjungan.updated_at) }}
                                    </p>

                                </div>


                                <!-- WAKTU -->

                                <div>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        Waktu
                                    </p>


                                    <p
                                        class="mt-1 text-sm font-semibold text-slate-800"
                                    >
                                        {{ formatWaktu(kunjungan.updated_at) }}
                                    </p>

                                </div>


                                <!-- STATUS -->

                                <div>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        Status
                                    </p>


                                    <span
                                        class="mt-1 inline-flex rounded-md border px-2.5 py-1 text-xs font-semibold"
                                        :class="statusStyle(kunjungan.status)"
                                    >
                                        {{ kunjungan.status || '-' }}
                                    </span>

                                </div>

                            </div>

                        </section>

                    </aside>

                </div>


                <!-- ================================================= -->
                <!-- OBAT -->
                <!-- ================================================= -->

                <section
                    class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="flex items-center justify-between border-b border-slate-100 px-6 py-5"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-[#2563EB]"
                            >

                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19.428 15.341A8 8 0 018.66 3.88m10.768 11.461A8 8 0 116.66 4.88m12.768 10.461L15 21.77M9 2.23l6 3.46"
                                    />

                                </svg>

                            </div>


                            <div>

                                <h2
                                    class="font-bold text-slate-900"
                                >
                                    Obat
                                </h2>


                                <p
                                    class="text-xs text-slate-500"
                                >
                                    Obat yang diberikan kepada siswa
                                </p>

                            </div>

                        </div>


                        <span
                            v-if="kunjungan.kunjungan_obat?.length"
                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600"
                        >
                            {{ kunjungan.kunjungan_obat.length }} obat
                        </span>

                    </div>


                    <div class="p-6">


                        <!-- ADA OBAT -->

                        <div
                            v-if="kunjungan.kunjungan_obat?.length"
                            class="overflow-x-auto"
                        >

                            <table
                                class="w-full min-w-[600px] text-sm"
                            >

                                <thead>

                                    <tr
                                        class="border-b border-slate-100 text-left"
                                    >

                                        <th
                                            class="pb-3 pr-4 text-xs font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            #
                                        </th>


                                        <th
                                            class="pb-3 pr-4 text-xs font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Nama Obat
                                        </th>


                                        <th
                                            class="pb-3 pr-4 text-xs font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Jumlah
                                        </th>


                                        <th
                                            class="pb-3 text-xs font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Keterangan
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr
                                        v-for="(item, index) in kunjungan.kunjungan_obat"
                                        :key="item.id"
                                        class="border-b border-slate-50 last:border-0"
                                    >

                                        <td
                                            class="py-4 pr-4 text-slate-400"
                                        >
                                            {{ index + 1 }}
                                        </td>


                                        <td
                                            class="py-4 pr-4 font-semibold text-slate-800"
                                        >
                                            {{ item.obat?.nama_obat || item.nama_obat || '-' }}
                                        </td>


                                        <td
                                            class="py-4 pr-4 text-slate-600"
                                        >
                                            {{ item.jumlah || '-' }}
                                        </td>


                                        <td
                                            class="py-4 text-slate-500"
                                        >
                                            {{ item.keterangan || '-' }}
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>


                        <!-- TIDAK ADA OBAT -->

                        <div
                            v-else
                            class="flex flex-col items-center justify-center py-10 text-center"
                        >

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400"
                            >

                                <svg
                                    class="h-6 w-6"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.8"
                                        d="M19.428 15.341A8 8 0 018.66 3.88m10.768 11.461A8 8 0 116.66 4.88m12.768 10.461L15 21.77M9 2.23l6 3.46"
                                    />

                                </svg>

                            </div>


                            <p
                                class="mt-3 text-sm font-semibold text-slate-600"
                            >
                                Tidak ada obat
                            </p>


                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Tidak ada obat yang diberikan pada kunjungan ini.
                            </p>

                        </div>

                    </div>

                </section>

            </main>

        </div>

    </AdminLayout>

</template>