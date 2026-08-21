<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { Link } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    UserIcon,
    AcademicCapIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
    DocumentTextIcon,
    BeakerIcon,
    ClockIcon,
    UserCircleIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },

    periode: {
        type: Object,
        default: null,
    },
})


// ======================================================
// FORMAT DATE
// ======================================================

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return date
    }

    return parsed.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}


// ======================================================
// FORMAT DATETIME
// ======================================================

const formatDateTime = (date) => {
    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return date
    }

    return parsed.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}


// ======================================================
// FORMAT STATUS
// ======================================================

const formatStatus = (status) => {
    if (!status) {
        return '-'
    }

    return String(status)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}


// ======================================================
// JENIS KELAMIN
// ======================================================

const formatJenisKelamin = (jenisKelamin) => {
    if (jenisKelamin === 'L') {
        return 'Laki-laki'
    }

    if (jenisKelamin === 'P') {
        return 'Perempuan'
    }

    return '-'
}


// ======================================================
// STATUS PEMERIKSAAN
// ======================================================

const pemeriksaanStatusClass = (status) => {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-100 text-emerald-700'

        case 'belum':
        case 'belum_selesai':
            return 'bg-amber-100 text-amber-700'

        case 'tidak_hadir':
            return 'bg-rose-100 text-rose-700'

        default:
            return 'bg-slate-100 text-slate-600'
    }
}



// ======================================================
// PEMERIKSAAN BERKALA
// ======================================================

const pemeriksaan = () => {
    return (
        props.siswa.pemeriksaan_berkala ??
        props.siswa.pemeriksaanBerkala ??
        []
    )
}


// ======================================================
// PEMERIKSAAN 1
// ======================================================

const pemeriksaan1 = () => {
    return pemeriksaan().find(
        (item) =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_1'
    ) ?? null
}


// ======================================================
// PEMERIKSAAN 2
// ======================================================

const pemeriksaan2 = () => {
    return pemeriksaan().find(
        (item) =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_2'
    ) ?? null
}


// ======================================================
// KUNJUNGAN KLINIK
// ======================================================

const kunjunganKlinik = () => {
    return (
        props.siswa.kunjungan_klinik ??
        props.siswa.kunjunganKlinik ??
        []
    )
}


// ======================================================
// TANGGAL KUNJUNGAN
// ======================================================

const tanggalKunjungan = (kunjungan) => {
    /*
     * Backend KunjunganController menggunakan created_at
     * sebagai tanggal kunjungan pada index().
     *
     * Tetap support tanggal_kunjungan apabila data detail
     * dari controller siswa masih mengirim field tersebut.
     */

    return (
        kunjungan.tanggal_kunjungan ??
        kunjungan.created_at ??
        kunjungan.tanggal ??
        null
    )
}


// ======================================================
// OBAT KUNJUNGAN
// ======================================================

const obatKunjungan = (kunjungan) => {
    /*
     * Backend KunjunganController mengirim:
     *
     * 'obat' => [...]
     *
     * bukan:
     *
     * 'kunjungan_obat'
     */

    return (
        kunjungan.obat ??
        kunjungan.kunjungan_obat ??
        []
    )
}


// ======================================================
// JUMLAH KUNJUNGAN
// ======================================================

const jumlahKunjungan = () => {
    return kunjunganKlinik().length
}


// ======================================================
// INITIAL NAMA SISWA
// ======================================================

const initial = props.siswa.nama
    ? props.siswa.nama.charAt(0).toUpperCase()
    : '?'

</script>


<template>

<KlinikLayout>

    <div class="space-y-6">


        <!-- ==================================================
             HEADER
        ================================================== -->

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >

            <div class="flex items-center gap-3">

                <Link
                    :href="route('klinik.siswa.index')"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-blue-600"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Detail Siswa
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Informasi lengkap dan riwayat kesehatan siswa.
                    </p>

                </div>

            </div>


            <!-- PERIODE -->

            <div
                v-if="periode"
                class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5"
            >

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-blue-500"
                >
                    Periode Aktif
                </p>

                <p class="mt-0.5 text-sm font-bold text-blue-800">
                    {{ periode.nama_periode }}
                </p>

            </div>

        </div>



        <!-- ==================================================
             PROFILE SISWA
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 bg-slate-50 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-700"
                    >
                        {{ initial }}
                    </div>

                    <div>

                        <h2 class="text-base font-bold text-slate-800">
                            {{ siswa.nama || '-' }}
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            NISN: {{ siswa.nisn || '-' }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- DATA PRIBADI -->

            <div class="p-5">

                <div class="mb-4 flex items-center gap-2">

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <UserIcon class="h-4 w-4 text-blue-600" />
                    </div>

                    <div>

                        <h3 class="text-sm font-bold text-slate-800">
                            Data Pribadi
                        </h3>

                        <p class="text-xs text-slate-400">
                            Informasi identitas siswa
                        </p>

                    </div>

                </div>


                <div
                    class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-3"
                >

                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            NISN
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ siswa.nisn || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Nama Lengkap
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ siswa.nama || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            NIK
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ siswa.nik || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Tempat Lahir
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ siswa.tempat_lahir || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Tanggal Lahir
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ formatDate(siswa.tanggal_lahir) }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Jenis Kelamin
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ formatJenisKelamin(siswa.jenis_kelamin) }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Nomor HP
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ siswa.no_hp || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Email
                        </p>

                        <p class="mt-1 break-all text-sm font-semibold text-slate-700">
                            {{ siswa.email || '-' }}
                        </p>
                    </div>


                    <div>
                        <p class="text-xs font-medium text-slate-400">
                            Status Siswa
                        </p>

                        <div class="mt-1">

                            <span
                                :class="[
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                    siswa.status === 'aktif'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-slate-100 text-slate-500'
                                ]"
                            >
                                {{
                                    siswa.status === 'aktif'
                                        ? 'Aktif'
                                        : formatStatus(siswa.status)
                                }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ==================================================
             AKADEMIK
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <AcademicCapIcon
                            class="h-5 w-5 text-blue-600"
                        />
                    </div>

                    <div>

                        <h2 class="text-sm font-bold text-slate-800">
                            Informasi Akademik
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Data kelas dan angkatan siswa
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4"
            >

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p class="text-xs font-medium text-slate-400">
                        Kelas
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ siswa.kelas?.nama_kelas || '-' }}
                    </p>

                </div>


                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p class="text-xs font-medium text-slate-400">
                        Tingkat
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ siswa.kelas?.tingkat || '-' }}
                    </p>

                </div>


                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p class="text-xs font-medium text-slate-400">
                        Jurusan
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ siswa.kelas?.jurusan?.nama_jurusan || '-' }}
                    </p>

                </div>


                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p class="text-xs font-medium text-slate-400">
                        Angkatan
                    </p>

                    <p class="mt-1 text-sm font-bold text-slate-800">
                        {{ siswa.angkatan || '-' }}
                    </p>

                </div>

            </div>

        </div>



        <!-- ==================================================
             PEMERIKSAAN BERKALA
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center justify-between gap-4">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50"
                        >
                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-rose-500"
                            />
                        </div>

                        <div>

                            <h2 class="text-sm font-bold text-slate-800">
                                Pemeriksaan Berkala
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Riwayat pemeriksaan kesehatan pada periode aktif
                            </p>

                        </div>

                    </div>

                    <span
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"
                    >
                        2 Pemeriksaan
                    </span>

                </div>

            </div>


            <div class="p-5">

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">


                    <!-- PEMERIKSAAN 1 -->

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200"
                    >

                        <div
                            class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold text-blue-700"
                                >
                                    1
                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-800">
                                        Pemeriksaan 1
                                    </h3>

                                    <p class="text-[11px] text-slate-400">
                                        Pemeriksaan berkala pertama
                                    </p>

                                </div>

                            </div>


                            <span
                                v-if="pemeriksaan1()?.status"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    pemeriksaanStatusClass(
                                        pemeriksaan1()?.status
                                    )
                                ]"
                            >
                                {{ formatStatus(pemeriksaan1()?.status) }}
                            </span>

                        </div>


                        <div
                            v-if="pemeriksaan1()"
                            class="space-y-4 p-5"
                        >

                            <div>

                                <p class="text-xs text-slate-400">
                                    Jenis Pemeriksaan
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ pemeriksaan1()?.jenis_pemeriksaan || '-' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Tanggal Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 flex items-center gap-2 text-sm font-semibold text-slate-700"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 text-slate-400"
                                    />

                                    {{ formatDate(
                                        pemeriksaan1()?.tanggal_pemeriksaan
                                    ) }}

                                </div>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Hasil Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                                >
                                    {{ pemeriksaan1()?.hasil_pemeriksaan || '-' }}
                                </div>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Catatan
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-slate-600">
                                    {{ pemeriksaan1()?.catatan || '-' }}
                                </p>

                            </div>


                            <div class="border-t border-slate-100 pt-4">

                                <p class="text-xs text-slate-400">
                                    Pemeriksa
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{
                                        pemeriksaan1()?.pemeriksa?.name
                                        || pemeriksaan1()?.pemeriksa?.nama
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <div
                            v-else
                            class="p-8 text-center"
                        >

                            <div
                                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100"
                            >
                                <ClipboardDocumentCheckIcon
                                    class="h-5 w-5 text-slate-400"
                                />
                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-500">
                                Belum ada pemeriksaan
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Pemeriksaan pertama belum dilakukan.
                            </p>

                        </div>

                    </div>



                    <!-- PEMERIKSAAN 2 -->

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200"
                    >

                        <div
                            class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100 text-sm font-bold text-purple-700"
                                >
                                    2
                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-800">
                                        Pemeriksaan 2
                                    </h3>

                                    <p class="text-[11px] text-slate-400">
                                        Pemeriksaan berkala kedua
                                    </p>

                                </div>

                            </div>


                            <span
                                v-if="pemeriksaan2()?.status"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    pemeriksaanStatusClass(
                                        pemeriksaan2()?.status
                                    )
                                ]"
                            >
                                {{ formatStatus(pemeriksaan2()?.status) }}
                            </span>

                        </div>


                        <div
                            v-if="pemeriksaan2()"
                            class="space-y-4 p-5"
                        >

                            <div>

                                <p class="text-xs text-slate-400">
                                    Jenis Pemeriksaan
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ pemeriksaan2()?.jenis_pemeriksaan || '-' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Tanggal Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 flex items-center gap-2 text-sm font-semibold text-slate-700"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 text-slate-400"
                                    />

                                    {{ formatDate(
                                        pemeriksaan2()?.tanggal_pemeriksaan
                                    ) }}

                                </div>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Hasil Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                                >
                                    {{ pemeriksaan2()?.hasil_pemeriksaan || '-' }}
                                </div>

                            </div>


                            <div>

                                <p class="text-xs text-slate-400">
                                    Catatan
                                </p>

                                <p class="mt-1 text-sm leading-relaxed text-slate-600">
                                    {{ pemeriksaan2()?.catatan || '-' }}
                                </p>

                            </div>


                            <div class="border-t border-slate-100 pt-4">

                                <p class="text-xs text-slate-400">
                                    Pemeriksa
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{
                                        pemeriksaan2()?.pemeriksa?.name
                                        || pemeriksaan2()?.pemeriksa?.nama
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <div
                            v-else
                            class="p-8 text-center"
                        >

                            <div
                                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100"
                            >
                                <ClipboardDocumentCheckIcon
                                    class="h-5 w-5 text-slate-400"
                                />
                            </div>

                            <p class="mt-3 text-sm font-semibold text-slate-500">
                                Belum ada pemeriksaan
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                Pemeriksaan kedua belum dilakukan.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

<!-- ==================================================
     RIWAYAT KUNJUNGAN KLINIK
================================================== -->

<div
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
>

    <!-- HEADER -->
    <div
        class="border-b border-slate-100 px-5 py-5"
    >

        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
        >

            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50"
                >
                    <HeartIcon
                        class="h-5 w-5 text-orange-500"
                    />
                </div>

                <div>

                    <h2 class="text-sm font-bold text-slate-800">
                        Riwayat Kunjungan Klinik
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Riwayat pelayanan kesehatan siswa
                    </p>

                </div>

            </div>

            <span
                class="w-fit rounded-full bg-orange-50 px-3 py-1.5 text-xs font-bold text-orange-700"
            >
                {{ jumlahKunjungan() }} Kunjungan
            </span>

        </div>

    </div>


    <!-- ==================================================
         TIDAK ADA DATA
         PAKAI v-if SENDIRI
    ================================================== -->

    <div
        v-if="!kunjunganKlinik().length"
        class="px-5 py-16 text-center"
    >

        <div
            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100"
        >
            <HeartIcon
                class="h-7 w-7 text-slate-400"
            />
        </div>

        <h3 class="mt-4 text-sm font-bold text-slate-700">
            Belum ada kunjungan klinik
        </h3>

        <p
            class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-slate-400"
        >
            Belum terdapat riwayat kunjungan klinik
            untuk siswa ini pada periode yang ditampilkan.
        </p>

    </div>

<!-- ==================================================
     TABEL RIWAYAT KUNJUNGAN
================================================== -->

<div
    v-else
    class="max-h-[600px] overflow-auto"
>

    <table class="min-w-[1450px] w-full">

        <!-- HEADER -->

        <thead
            class="sticky top-0 z-20 bg-slate-50"
        >

            <tr class="border-b border-slate-200">

                <!-- NO -->

                <th
                    class="sticky left-0 z-30 w-12 bg-slate-50 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    No
                </th>


                <!-- TANGGAL -->

                <th
                    class="min-w-[150px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Tanggal
                </th>


                <!-- PERIODE -->

                <th
                    class="min-w-[150px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Periode
                </th>


                <!-- KELUHAN -->

                <th
                    class="min-w-[190px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Keluhan
                </th>


                <!-- PEMERIKSAAN -->

                <th
                    class="min-w-[190px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Pemeriksaan
                </th>


                <!-- DIAGNOSIS -->

                <th
                    class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Diagnosis
                </th>


                <!-- TINDAKAN -->

                <th
                    class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Tindakan
                </th>


                <!-- CATATAN -->

                <th
                    class="min-w-[200px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Catatan
                </th>


                <!-- OBAT -->

                <th
                    class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Obat
                </th>



                <!-- PEMERIKSA -->

                <th
                    class="min-w-[160px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    Pemeriksa
                </th>

            </tr>

        </thead>



        <!-- BODY -->

        <tbody class="divide-y divide-slate-100">

            <tr
                v-for="(kunjungan, index) in kunjunganKlinik()"
                :key="kunjungan.id"
                class="transition hover:bg-orange-50/40"
            >

                <!-- NO -->

                <td
                    class="sticky left-0 z-10 bg-white px-4 py-3 text-center"
                >

                    <span
                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-orange-50 text-xs font-bold text-orange-700"
                    >
                        {{ index + 1 }}
                    </span>

                </td>



                <!-- TANGGAL -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <div class="flex items-start gap-2">

                        <CalendarDaysIcon
                            class="mt-0.5 h-4 w-4 shrink-0 text-slate-400"
                        />

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-700"
                            >
                                {{
                                    formatDate(
                                        tanggalKunjungan(kunjungan)
                                    )
                                }}
                            </p>

                            <p
                                class="mt-0.5 text-[10px] text-slate-400"
                            >
                                {{
                                    formatDateTime(
                                        tanggalKunjungan(kunjungan)
                                    ).split(' pukul ')[1] || ''
                                }}
                            </p>

                        </div>

                    </div>

                </td>



                <!-- PERIODE -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <span
                        class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1.5 text-[11px] font-semibold text-blue-700"
                    >
                        {{
                            kunjungan.periode?.nama_periode
                            || '-'
                        }}
                    </span>

                </td>



                <!-- KELUHAN -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <p
                        class="line-clamp-2 text-xs leading-relaxed text-slate-700"
                        :title="kunjungan.keluhan || '-'"
                    >
                        {{ kunjungan.keluhan || '-' }}
                    </p>

                </td>



                <!-- PEMERIKSAAN -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <p
                        class="line-clamp-2 text-xs leading-relaxed text-slate-700"
                        :title="kunjungan.pemeriksaan || '-'"
                    >
                        {{ kunjungan.pemeriksaan || '-' }}
                    </p>

                </td>



                <!-- DIAGNOSIS -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <p
                        class="line-clamp-2 text-xs font-semibold leading-relaxed text-slate-700"
                        :title="kunjungan.diagnosis || '-'"
                    >
                        {{ kunjungan.diagnosis || '-' }}
                    </p>

                </td>



                <!-- TINDAKAN -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <p
                        class="line-clamp-2 text-xs leading-relaxed text-slate-700"
                        :title="kunjungan.tindakan || '-'"
                    >
                        {{ kunjungan.tindakan || '-' }}
                    </p>

                </td>



                <!-- CATATAN -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <p
                        class="line-clamp-2 text-xs leading-relaxed text-slate-600"
                        :title="kunjungan.catatan || '-'"
                    >
                        {{ kunjungan.catatan || '-' }}
                    </p>

                </td>



                <!-- OBAT -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <div
                        v-if="obatKunjungan(kunjungan).length"
                        class="space-y-1.5"
                    >

                        <div
                            v-for="obat in obatKunjungan(kunjungan)"
                            :key="obat.id"
                            class="flex items-center justify-between gap-2"
                        >

                            <span
                                class="text-xs font-semibold text-slate-700"
                            >
                                {{
                                    obat.nama_obat
                                    || obat.obat?.nama_obat
                                    || '-'
                                }}
                            </span>

                            <span
                                class="shrink-0 rounded-md bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold text-blue-700"
                            >
                                {{ obat.jumlah || 0 }}
                                {{
                                    obat.satuan
                                    || obat.obat?.satuan
                                    || ''
                                }}
                            </span>

                        </div>

                    </div>

                    <span
                        v-else
                        class="text-xs text-slate-400"
                    >
                        -
                    </span>

                </td>




                <!-- PEMERIKSA -->

                <td
                    class="px-4 py-3 align-top"
                >

                    <div class="flex items-center gap-2">

                        <div
                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-50"
                        >

                            <UserCircleIcon
                                class="h-4 w-4 text-blue-600"
                            />

                        </div>

                        <p
                            class="text-xs font-semibold text-slate-700"
                        >
                            {{
                                kunjungan.pemeriksa?.name
                                || kunjungan.pemeriksa?.nama
                                || '-'
                            }}
                        </p>

                    </div>

                </td>

            </tr>

        </tbody>

    </table>

</div>



<!-- FOOTER -->

<div
    v-if="kunjunganKlinik().length"
    class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 bg-slate-50 px-5 py-3"
>

    <p class="text-[10px] text-slate-400">
        Menampilkan {{ jumlahKunjungan() }} riwayat kunjungan
    </p>

    <p class="text-[10px] text-slate-400">
        Geser tabel ke samping atau ke bawah untuk melihat data lainnya
    </p>

</div>
  


</div>


        <!-- ==================================================
             FOOTER
        ================================================== -->

        <div
            class="pb-4 text-center text-xs text-slate-400"
        >
            Data kesehatan siswa bersifat terbatas dan digunakan
            untuk keperluan pelayanan kesehatan sekolah.
        </div>

    </div>

</KlinikLayout>

</template>