<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    PencilSquareIcon,
    UserGroupIcon,
    HeartIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'


// ==================================================
// PROPS
// ==================================================

const props = defineProps({
    periode: {
        type: Object,
        required: true,
    },
})


// ==================================================
// DATA SISWA
// ==================================================

const siswa = computed(() => props.periode.siswa ?? [])

const jumlahSiswa = computed(() =>
    siswa.value.length
)


// ==================================================
// STATISTIK PERIODE
// ==================================================

const jumlahBerkala1 = computed(() =>
    Number(props.periode.jumlah_berkala_1 ?? 0)
)

const jumlahBerkala2 = computed(() =>
    Number(props.periode.jumlah_berkala_2 ?? 0)
)

const jumlahTksi = computed(() =>
    Number(props.periode.jumlah_tksi ?? 0)
)

const jumlahKunjungan = computed(() =>
    Number(props.periode.jumlah_kunjungan ?? 0)
)


// ==================================================
// PERSENTASE
// ==================================================

const persenBerkala1 = computed(() => {

    if (jumlahSiswa.value === 0) {
        return 0
    }

    return Math.round(
        (jumlahBerkala1.value / jumlahSiswa.value) * 100
    )
})


const persenBerkala2 = computed(() => {

    if (jumlahSiswa.value === 0) {
        return 0
    }

    return Math.round(
        (jumlahBerkala2.value / jumlahSiswa.value) * 100
    )
})


const persenTksi = computed(() => {

    if (jumlahSiswa.value === 0) {
        return 0
    }

    return Math.round(
        (jumlahTksi.value / jumlahSiswa.value) * 100
    )
})


// ==================================================
// STATUS PERIODE
// ==================================================

const statusLabel = computed(() => {

    switch (
        String(props.periode.status ?? '').toLowerCase()
    ) {

        case 'aktif':
            return 'Aktif'

        case 'selesai':
            return 'Selesai'

        case 'draft':
            return 'Draft'

        default:
            return props.periode.status ?? '-'
    }
})


const statusClass = computed(() => {

    switch (
        String(props.periode.status ?? '').toLowerCase()
    ) {

        case 'aktif':
            return 'border-emerald-200 bg-emerald-50 text-emerald-700'

        case 'selesai':
            return 'border-slate-200 bg-slate-50 text-slate-600'

        case 'draft':
            return 'border-amber-200 bg-amber-50 text-amber-700'

        default:
            return 'border-slate-200 bg-slate-50 text-slate-600'
    }
})


// ==================================================
// STATUS PEMERIKSAAN SISWA
// ==================================================

const isSelesai = (value) => {
    return (
        value === true ||
        value === 1 ||
        value === '1' ||
        value === 'selesai' ||
        value === 'Selesai' ||
        value === 'sudah' ||
        value === 'Sudah'
    )
}


// ==================================================
// STATUS BERKALA 1
// ==================================================

const statusBerkala1 = (item) => {

    if (
        isSelesai(item.berkala_1?.selesai) ||
        isSelesai(item.berkala_1_selesai) ||
        isSelesai(item.b1_selesai)
    ) {
        return 'Selesai'
    }

    return 'Belum Tes'
}


// ==================================================
// STATUS BERKALA 2
// ==================================================

const statusBerkala2 = (item) => {

    if (
        isSelesai(item.berkala_2?.selesai) ||
        isSelesai(item.berkala_2_selesai) ||
        isSelesai(item.b2_selesai)
    ) {
        return 'Selesai'
    }

    return 'Belum Tes'
}


// ==================================================
// STATUS TKSI
// ==================================================

const statusTksi = (item) => {

    if (
        isSelesai(item.tksi?.selesai) ||
        isSelesai(item.tksi_selesai)
    ) {
        return 'Selesai'
    }

    return 'Belum Tes'
}


// ==================================================
// JUMLAH KUNJUNGAN SISWA
// ==================================================

const jumlahKunjunganSiswa = (item) => {

    return Number(
        item.jumlah_kunjungan ??
        item.kunjungan_klinik ??
        0
    )
}


// ==================================================
// FORMAT DATE
// ==================================================

const formatDate = (date) => {

    if (!date) {
        return '-'
    }

    const value = new Date(date)

    if (Number.isNaN(value.getTime())) {
        return date
    }

    return value.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    )
}


// ==================================================
// BREADCRUMB
// ==================================================

const breadcrumbs = [
    {
        name: 'Dashboard',
        url: route('admin.dashboard'),
    },
    {
        name: 'Periode',
        url: route('admin.periode.index'),
    },
    {
        name: 'Detail Periode',
        url: '#',
    },
]
</script>


<template>

    <Head
        :title="`Detail ${periode.nama_periode}`"
    />


    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">


            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <Link
                        :href="route('admin.periode.index')"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                        title="Kembali"
                    >

                        <ArrowLeftIcon
                            class="h-5 w-5"
                        />

                    </Link>


                    <div>

                        <h1
                            class="text-2xl font-bold text-slate-800"
                        >
                            Detail Periode
                        </h1>

                        <p
                            class="mt-1 text-sm text-slate-500"
                        >
                            Informasi lengkap periode pemeriksaan kesehatan siswa.
                        </p>

                    </div>

                </div>


                <Link
                    :href="route(
                        'admin.periode.edit',
                        periode.id
                    )"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                >

                    <PencilSquareIcon
                        class="h-4 w-4"
                    />

                    Edit Periode

                </Link>

            </div>



            <!-- ==================================================
                 PERIODE HEADER CARD
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div
                        class="flex min-w-0 items-center gap-4"
                    >

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100"
                        >

                            <CalendarDaysIcon
                                class="h-7 w-7 text-blue-700"
                            />

                        </div>


                        <div class="min-w-0">

                            <div
                                class="flex flex-wrap items-center gap-2"
                            >

                                <h2
                                    class="text-lg font-bold text-slate-800"
                                >
                                    {{ periode.nama_periode }}
                                </h2>


                                <span
                                    :class="[
                                        'inline-flex rounded-full border px-2.5 py-1 text-xs font-bold',
                                        statusClass
                                    ]"
                                >
                                    {{ statusLabel }}
                                </span>

                            </div>


                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                ID Periode: {{ periode.id }}
                            </p>

                        </div>

                    </div>


                    <!-- PEMBUAT -->

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600"
                        >

                            {{
                                periode.pembuat?.name
                                    ?.charAt(0)
                                    ?.toUpperCase()
                                || '?'
                            }}

                        </div>


                        <div>

                            <p
                                class="text-[11px] text-slate-400"
                            >
                                Dibuat Oleh
                            </p>

                            <p
                                class="text-sm font-semibold text-slate-700"
                            >
                                {{ periode.pembuat?.name || '-' }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- INFO PERIODE -->

                <div
                    class="grid grid-cols-1 border-t border-slate-100 sm:grid-cols-3"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4 sm:border-b-0 sm:border-r"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Tanggal Mulai
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_mulai) }}
                        </p>

                    </div>


                    <div
                        class="border-b border-slate-100 px-5 py-4 sm:border-b-0 sm:border-r"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Tanggal Selesai
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_selesai) }}
                        </p>

                    </div>


                    <div
                        class="px-5 py-4"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Periode
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_mulai) }}
                            –
                            {{ formatDate(periode.tanggal_selesai) }}
                        </p>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 SUMMARY
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5"
            >

                <!-- SISWA -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Siswa Peserta
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahSiswa }}
                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-400"
                            >
                                Siswa dalam periode
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                        >

                            <UserGroupIcon
                                class="h-5 w-5 text-blue-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- B1 -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Berkala 1
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >

                                {{ jumlahBerkala1 }}

                                <span
                                    class="text-sm font-medium text-slate-400"
                                >
                                    / {{ jumlahSiswa }}
                                </span>

                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-400"
                            >
                                {{ persenBerkala1 }}% siswa selesai
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-emerald-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- B2 -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Berkala 2
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >

                                {{ jumlahBerkala2 }}

                                <span
                                    class="text-sm font-medium text-slate-400"
                                >
                                    / {{ jumlahSiswa }}
                                </span>

                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-400"
                            >
                                {{ persenBerkala2 }}% siswa selesai
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-indigo-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- TKSI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                TKSI
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >

                                {{ jumlahTksi }}

                                <span
                                    class="text-sm font-medium text-slate-400"
                                >
                                    / {{ jumlahSiswa }}
                                </span>

                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-400"
                            >
                                {{ persenTksi }}% siswa selesai
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                        >

                            <ClockIcon
                                class="h-5 w-5 text-amber-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- KUNJUNGAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Kunjungan Klinik
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahKunjungan }}
                            </p>

                            <p
                                class="mt-1 text-[11px] text-slate-400"
                            >
                                Total kunjungan periode
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-rose-600"
                            />

                        </div>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 PROGRESS
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-3"
            >

                <!-- B1 -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Pemeriksaan Berkala 1
                            </h3>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Siswa yang sudah menjalani pemeriksaan
                            </p>

                        </div>


                        <span
                            class="text-sm font-bold text-emerald-700"
                        >
                            {{ persenBerkala1 }}%
                        </span>

                    </div>


                    <div
                        class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"
                    >

                        <div
                            class="h-full rounded-full bg-emerald-500 transition-all duration-500"
                            :style="{
                                width: `${persenBerkala1}%`
                            }"
                        ></div>

                    </div>


                    <div
                        class="mt-3 flex justify-between text-xs"
                    >

                        <span
                            class="text-slate-400"
                        >
                            Selesai
                        </span>

                        <span
                            class="font-semibold text-slate-700"
                        >
                            {{ jumlahBerkala1 }} / {{ jumlahSiswa }}
                        </span>

                    </div>

                </div>


                <!-- B2 -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Pemeriksaan Berkala 2
                            </h3>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Siswa yang sudah menjalani pemeriksaan
                            </p>

                        </div>


                        <span
                            class="text-sm font-bold text-indigo-700"
                        >
                            {{ persenBerkala2 }}%
                        </span>

                    </div>


                    <div
                        class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"
                    >

                        <div
                            class="h-full rounded-full bg-indigo-500 transition-all duration-500"
                            :style="{
                                width: `${persenBerkala2}%`
                            }"
                        ></div>

                    </div>


                    <div
                        class="mt-3 flex justify-between text-xs"
                    >

                        <span
                            class="text-slate-400"
                        >
                            Selesai
                        </span>

                        <span
                            class="font-semibold text-slate-700"
                        >
                            {{ jumlahBerkala2 }} / {{ jumlahSiswa }}
                        </span>

                    </div>

                </div>


                <!-- TKSI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Tes Kebugaran (TKSI)
                            </h3>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Siswa yang sudah menyelesaikan tes
                            </p>

                        </div>


                        <span
                            class="text-sm font-bold text-amber-700"
                        >
                            {{ persenTksi }}%
                        </span>

                    </div>


                    <div
                        class="mt-4 h-2 overflow-hidden rounded-full bg-slate-100"
                    >

                        <div
                            class="h-full rounded-full bg-amber-500 transition-all duration-500"
                            :style="{
                                width: `${persenTksi}%`
                            }"
                        ></div>

                    </div>


                    <div
                        class="mt-3 flex justify-between text-xs"
                    >

                        <span
                            class="text-slate-400"
                        >
                            Selesai
                        </span>

                        <span
                            class="font-semibold text-slate-700"
                        >
                            {{ jumlahTksi }} / {{ jumlahSiswa }}
                        </span>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 DAFTAR SISWA
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- HEADER -->

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100"
                        >

                            <UserGroupIcon
                                class="h-5 w-5 text-blue-700"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Status Pemeriksaan Siswa
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Status pemeriksaan setiap siswa dalam periode ini.
                            </p>

                        </div>

                    </div>


                    <span
                        class="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700"
                    >
                        {{ jumlahSiswa }} siswa
                    </span>

                </div>



                <!-- ==================================================
                     DESKTOP TABLE
                ================================================== -->

                <div
                    v-if="siswa.length > 0"
                    class="hidden overflow-x-auto lg:block"
                >

                    <table
                        class="min-w-[1100px] w-full"
                    >

                        <thead>

                            <tr
                                class="border-b border-slate-200 bg-slate-50"
                            >

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    No
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Siswa
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    NISN
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kelas
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Jurusan
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 1
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 2
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kunjungan Klinik
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    TKSI
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="(item, index) in siswa"
                                :key="item.id"
                                class="transition hover:bg-slate-50"
                            >

                                <!-- NO -->

                                <td
                                    class="px-5 py-4 text-sm text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <!-- SISWA -->

                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="flex items-center gap-3"
                                    >

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                        >

                                            {{
                                                item.nama
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                                || '?'
                                            }}

                                        </div>


                                        <p
                                            class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                        >
                                            {{ item.nama }}
                                        </p>

                                    </div>

                                </td>


                                <!-- NISN -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{ item.nisn || '-' }}
                                </td>


                                <!-- KELAS -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{ item.kelas?.nama_kelas || '-' }}
                                </td>


                                <!-- JURUSAN -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >

                                    {{
                                        item.kelas?.jurusan?.nama_jurusan
                                        || '-'
                                    }}

                                </td>


                                <!-- ==================================================
                                     BERKALA 1
                                ================================================== -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <span
                                        v-if="statusBerkala1(item) === 'Selesai'"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700"
                                    >

                                        <CheckCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Selesai

                                    </span>


                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-500"
                                    >

                                        <XCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Belum Tes

                                    </span>

                                </td>


                                <!-- ==================================================
                                     BERKALA 2
                                ================================================== -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <span
                                        v-if="statusBerkala2(item) === 'Selesai'"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700"
                                    >

                                        <CheckCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Selesai

                                    </span>


                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-500"
                                    >

                                        <XCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Belum Tes

                                    </span>

                                </td>


                                <!-- ==================================================
                                     KUNJUNGAN KLINIK
                                ================================================== -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <span
                                        v-if="jumlahKunjunganSiswa(item) > 0"
                                        class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-bold text-rose-700"
                                    >
                                        {{ jumlahKunjunganSiswa(item) }} kali
                                    </span>


                                    <span
                                        v-else
                                        class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-500"
                                    >
                                        0 kali
                                    </span>

                                </td>


                                <!-- ==================================================
                                     TKSI
                                ================================================== -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <span
                                        v-if="statusTksi(item) === 'Selesai'"
                                        class="inline-flex items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-bold text-amber-700"
                                    >

                                        <CheckCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Selesai

                                    </span>


                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-bold text-slate-500"
                                    >

                                        <XCircleIcon
                                            class="h-4 w-4"
                                        />

                                        Belum Tes

                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>



                <!-- ==================================================
                     MOBILE
                ================================================== -->

                <div
                    v-if="siswa.length > 0"
                    class="divide-y divide-slate-100 lg:hidden"
                >

                    <div
                        v-for="(item, index) in siswa"
                        :key="item.id"
                        class="p-4"
                    >

                        <!-- SISWA -->

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                            >

                                {{
                                    item.nama
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                    || '?'
                                }}

                            </div>


                            <div
                                class="min-w-0 flex-1"
                            >

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ item.nama }}
                                </p>

                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    NISN: {{ item.nisn || '-' }}
                                </p>

                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    {{ item.kelas?.nama_kelas || '-' }}
                                    ·
                                    {{
                                        item.kelas?.jurusan?.nama_jurusan
                                        || '-'
                                    }}
                                </p>

                            </div>


                            <span
                                class="text-xs font-semibold text-slate-400"
                            >
                                #{{ index + 1 }}
                            </span>

                        </div>



                        <!-- STATUS -->

                        <div
                            class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3"
                        >

                            <!-- B1 -->

                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    Berkala 1
                                </p>


                                <span
                                    v-if="statusBerkala1(item) === 'Selesai'"
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-700"
                                >

                                    <CheckCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Selesai

                                </span>


                                <span
                                    v-else
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-500"
                                >

                                    <XCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Belum Tes

                                </span>

                            </div>


                            <!-- B2 -->

                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    Berkala 2
                                </p>


                                <span
                                    v-if="statusBerkala2(item) === 'Selesai'"
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2 py-1 text-[11px] font-bold text-indigo-700"
                                >

                                    <CheckCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Selesai

                                </span>


                                <span
                                    v-else
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-500"
                                >

                                    <XCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Belum Tes

                                </span>

                            </div>


                            <!-- KUNJUNGAN -->

                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    Kunjungan Klinik
                                </p>

                                <p
                                    class="mt-1 text-xs font-bold text-rose-600"
                                >

                                    {{ jumlahKunjunganSiswa(item) }}

                                    kunjungan

                                </p>

                            </div>


                            <!-- TKSI -->

                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    TKSI
                                </p>


                                <span
                                    v-if="statusTksi(item) === 'Selesai'"
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-[11px] font-bold text-amber-700"
                                >

                                    <CheckCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Selesai

                                </span>


                                <span
                                    v-else
                                    class="mt-1 inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-500"
                                >

                                    <XCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Belum Tes

                                </span>

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ==================================================
                     EMPTY
                ================================================== -->

                <div
                    v-if="siswa.length === 0"
                    class="px-5 py-16 text-center"
                >

                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                    >

                        <UserGroupIcon
                            class="h-6 w-6 text-slate-400"
                        />

                    </div>


                    <p
                        class="text-sm font-bold text-slate-700"
                    >
                        Belum ada siswa
                    </p>


                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Belum ada siswa yang terdaftar pada periode ini.
                    </p>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>