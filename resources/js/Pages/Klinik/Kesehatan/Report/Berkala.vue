<script setup>

import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue';
import { router } from '@inertiajs/vue3'

import {
    ClipboardDocumentCheckIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ClockIcon,
    FunnelIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


const props = defineProps({

    periode: {
        type: Object,
        default: null,
    },

    periodeList: {
        type: Array,
        default: () => [],
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    statistik: {
        type: Object,
        default: () => ({}),
    },

    kelas: {
        type: Array,
        default: () => [],
    },

    filter: {
        type: Object,
        default: () => ({}),
    },

})


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const kelasId = ref(
    props.filter?.kelas_id ?? ''
)

const selectedPemeriksaan = ref(null)


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

const totalSiswa = computed(() =>
    props.statistik?.total_siswa ?? 0
)

const berkala1Selesai = computed(() =>
    props.statistik?.berkala_1_selesai ?? 0
)

const berkala2Selesai = computed(() =>
    props.statistik?.berkala_2_selesai ?? 0
)

const lengkap = computed(() =>
    props.statistik?.lengkap ?? 0
)


/*
|--------------------------------------------------------------------------
| PROGRESS
|--------------------------------------------------------------------------
*/

const progress1 = computed(() => {

    if (!totalSiswa.value) {
        return 0
    }

    return Math.round(
        (berkala1Selesai.value /
            totalSiswa.value) *
        100
    )

})


const progress2 = computed(() => {

    if (!totalSiswa.value) {
        return 0
    }

    return Math.round(
        (berkala2Selesai.value /
            totalSiswa.value) *
        100
    )

})


/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/
function applyFilter() {

    router.get(
        '/klinik/kesehatan/report/berkala',
        {
            periode_id: props.periode?.id,
            kelas_id: kelasId.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )

}

function resetFilter() {

    kelasId.value = ''

    router.get(
        '/klinik/kesehatan/report/berkala',
        {
            periode_id: props.periode?.id,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )

}


/*
|--------------------------------------------------------------------------
| GANTI PERIODE
|--------------------------------------------------------------------------
*/
function changePeriode(event) {

    const id = event.target.value

    if (!id) {
        return
    }

    router.get(
        '/klinik/kesehatan/report/berkala',
        {
            periode_id: id,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )

}

/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

function openDetail(item) {

    selectedPemeriksaan.value = item

}


function closeDetail() {

    selectedPemeriksaan.value = null

}


/*
|--------------------------------------------------------------------------
| DETAIL B1
|--------------------------------------------------------------------------
*/

function openB1(item) {

    if (!item.berkala_1) {
        return
    }

    selectedPemeriksaan.value = {
        siswa: item.siswa,
        pemeriksaan: item.berkala_1,
    }

}


/*
|--------------------------------------------------------------------------
| DETAIL B2
|--------------------------------------------------------------------------
*/

function openB2(item) {

    if (!item.berkala_2) {
        return
    }

    selectedPemeriksaan.value = {
        siswa: item.siswa,
        pemeriksaan: item.berkala_2,
    }

}


/*
|--------------------------------------------------------------------------
| DOWNLOAD
|--------------------------------------------------------------------------
*/

function downloadDetail() {

    const pemeriksaan =
        selectedPemeriksaan.value?.pemeriksaan

    if (!pemeriksaan) {
        return
    }

    window.location.href =
        `/klinik/kesehatan/report/berkala/detail/${pemeriksaan.id}/pdf`

}


function downloadExcel() {

    const params =
        new URLSearchParams()

    if (props.periode?.id) {

        params.set(
            'periode_id',
            props.periode.id
        )

    }

    if (kelasId.value) {

        params.set(
            'kelas_id',
            kelasId.value
        )

    }

    window.location.href =
        `/klinik/kesehatan/report/berkala/excel?${params.toString()}`

}


function downloadPdf() {

    const params =
        new URLSearchParams()

    if (props.periode?.id) {

        params.set(
            'periode_id',
            props.periode.id
        )

    }

    if (kelasId.value) {

        params.set(
            'kelas_id',
            kelasId.value
        )

    }

    window.location.href =
        `/klinik/kesehatan/report/berkala/pdf?${params.toString()}`

}


/*
|--------------------------------------------------------------------------
| FORMAT
|--------------------------------------------------------------------------
*/

function formatJenis(jenis) {

    if (jenis === 'berkala_1') {
        return 'Berkala 1'
    }

    if (jenis === 'berkala_2') {
        return 'Berkala 2'
    }

    return '-'

}


function formatDate(date) {

    if (!date) {
        return '-'
    }

    return new Date(date)
        .toLocaleDateString(
            'id-ID',
            {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
            }
        )

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

function statusClass(status) {

    if (status === 'selesai') {

        return 'bg-emerald-100 text-emerald-700'

    }

    return 'bg-amber-100 text-amber-700'

}


function statusLabel(status) {

    if (status === 'selesai') {
        return 'Selesai'
    }

    return 'Belum'

}


function overallClass(status) {

    if (status === 'lengkap') {

        return 'bg-emerald-100 text-emerald-700'

    }

    if (status === 'belum_lengkap') {

        return 'bg-amber-100 text-amber-700'

    }

    return 'bg-slate-100 text-slate-500'

}


function overallLabel(status) {

    if (status === 'lengkap') {
        return 'Lengkap'
    }

    if (status === 'belum_lengkap') {
        return 'Belum Lengkap'
    }

    return 'Belum Diperiksa'

}

</script>
<template>
<KlinikLayout>
    <div class="space-y-6">


        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
        >

            <div>

                <div
                    class="flex items-center gap-2 text-xs font-semibold text-slate-400"
                >

                    <ClipboardDocumentCheckIcon
                        class="h-4 w-4"
                    />

                    Kesehatan

                </div>

                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Report Pemeriksaan Berkala
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Rekapitulasi pemeriksaan kesehatan siswa berdasarkan periode.
                </p>

            </div>


            <!-- PERIODE -->

            <div
                v-if="periode"
                class="flex items-center gap-3"
            >

                <div>

                    <label
                        class="mb-1 block text-[10px] font-bold uppercase tracking-wide text-slate-400"
                    >
                        Periode
                    </label>

                    <select
                        :value="periode.id"
                        @change="changePeriode"
                        class="min-w-[220px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option
                            v-for="item in periodeList"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.nama_periode }}
                            {{ item.status === 'aktif'
                                ? '(Aktif)'
                                : ''
                            }}
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <!-- =====================================================
             NO PERIODE
        ====================================================== -->

        <div
            v-if="!periode"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-6"
        >

            <div class="flex items-start gap-4">

                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                >

                    <ClockIcon
                        class="h-5 w-5 text-amber-600"
                    />

                </div>

                <div>

                    <h3
                        class="font-bold text-amber-800"
                    >
                        Belum Ada Periode
                    </h3>

                    <p
                        class="mt-1 text-sm text-amber-700"
                    >
                        Belum terdapat periode pemeriksaan yang tersedia.
                    </p>

                </div>

            </div>

        </div>


        <template v-else>


            <!-- =================================================
                 INFO PERIODE
            ================================================== -->

            <div
                class="rounded-2xl border border-blue-100 bg-blue-50 p-5"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <CalendarDaysIcon
                            class="h-6 w-6 text-blue-600"
                        />

                    </div>

                    <div>

                        <p
                            class="text-[10px] font-bold uppercase tracking-wide text-blue-500"
                        >
                            Periode Pemeriksaan
                        </p>

                        <p
                            class="mt-0.5 font-bold text-blue-800"
                        >
                            {{ periode.nama_periode }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 STATISTIK
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
            >

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Total Siswa
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-slate-800"
                    >
                        {{ totalSiswa }}
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Berkala 1
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-blue-600"
                    >
                        {{ progress1 }}%
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        {{ berkala1Selesai }} siswa selesai
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Berkala 2
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-purple-600"
                    >
                        {{ progress2 }}%
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        {{ berkala2Selesai }} siswa selesai
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Pemeriksaan Lengkap
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-emerald-600"
                    >
                        {{ lengkap }}
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        B1 + B2 selesai
                    </p>

                </div>

            </div>


            <!-- =================================================
                 FILTER
            ================================================== -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex flex-col gap-4 md:flex-row md:items-end"
                >

                    <div class="flex-1">

                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-600"
                        >
                            Kelas
                        </label>

                        <select
                            v-model="kelasId"
                            @change="applyFilter"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Semua Kelas
                            </option>

                            <option
                                v-for="item in kelas"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.nama_kelas }}

                                <template
                                    v-if="item.jurusan"
                                >
                                    —
                                    {{ item.jurusan.nama_jurusan }}
                                </template>

                            </option>

                        </select>

                    </div>


                    <button
                        type="button"
                        @click="resetFilter"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Reset
                    </button>

                </div>

            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- HEADER TABLE -->

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Daftar Siswa
                        </h2>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Rekap Berkala 1 dan Berkala 2 setiap siswa.
                        </p>

                    </div>


                    <!-- DOWNLOAD -->

                    <div
                        class="flex items-center gap-2"
                    >

                        <button
                            type="button"
                            @click="downloadExcel"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh Excel

                        </button>


                        <button
                            type="button"
                            @click="downloadPdf"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh PDF

                        </button>

                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    v-if="!siswa.length"
                    class="px-6 py-14 text-center"
                >

                    <ClipboardDocumentCheckIcon
                        class="mx-auto h-10 w-10 text-slate-300"
                    />

                    <p
                        class="mt-3 text-sm font-semibold text-slate-600"
                    >
                        Tidak ada siswa
                    </p>

                </div>


                <!-- TABLE -->

                <div
                    v-else
                    class="overflow-x-auto"
                >
<table
    class="w-full min-w-[1000px] table-fixed text-left"
>

                        <thead
    class="border-b border-slate-100 bg-slate-50"
>
    <tr>

        <!-- SISWA -->
        <th
            class="w-[24%] px-6 py-3 text-[11px] font-bold uppercase text-slate-400"
        >
            Siswa
        </th>

        <!-- KELAS -->
        <th
            class="w-[16%] px-4 py-3 text-[11px] font-bold uppercase text-slate-400"
        >
            Kelas
        </th>

        <!-- B1 -->
        <th
            class="w-[17%] px-4 py-3 text-[11px] font-bold uppercase text-blue-500"
        >
            Berkala 1
        </th>

        <!-- B2 -->
        <th
            class="w-[17%] px-4 py-3 text-[11px] font-bold uppercase text-purple-500"
        >
            Berkala 2
        </th>

        <!-- STATUS -->
        <th
            class="w-[14%] px-3 py-3 text-[11px] font-bold uppercase text-slate-400"
        >
            Status
        </th>

        <!-- AKSI -->
        <th
            class="w-[12%] px-3 py-3 text-center text-[11px] font-bold uppercase text-slate-400"
        >
            Aksi
        </th>

    </tr>
</thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="item in siswa"
                                :key="item.id"
                                class="hover:bg-slate-50/70"
                            >

                                <!-- SISWA -->

                                <td class="px-6 py-4">

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.siswa.nama }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        NISN:
                                        {{ item.siswa.nisn || '-' }}
                                    </p>

                                </td>


                                <!-- KELAS -->

                                <td class="px-4 py-4">

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.siswa.kelas?.nama_kelas || '-' }}
                                    </p>

                                    <p
                                        v-if="item.siswa.jurusan"
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        {{ item.siswa.jurusan.nama_jurusan }}
                                    </p>

                                </td>


                                <!-- B1 -->

                                <td class="px-4 py-4">

                                    <template
                                        v-if="item.berkala_1"
                                    >

                                        <div class="space-y-1">

                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                                    statusClass(item.berkala_1.status)
                                                ]"
                                            >
                                                {{
                                                    statusLabel(
                                                        item.berkala_1.status
                                                    )
                                                }}
                                            </span>

                                            <p
                                                class="max-w-[150px] truncate text-xs text-slate-500"
                                                :title="item.kondisi_b1 || '-'"
                                            >
                                                {{
                                                    item.kondisi_b1 || '-'
                                                }}
                                            </p>

                                        </div>

                                    </template>

                                    <span
                                        v-else
                                        class="text-xs text-slate-400"
                                    >
                                        Belum diperiksa
                                    </span>

                                </td>


                                <!-- B2 -->

                                <td class="px-4 py-4">

                                    <template
                                        v-if="item.berkala_2"
                                    >

                                        <div class="space-y-1">

                                            <span
                                                :class="[
                                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                                    statusClass(item.berkala_2.status)
                                                ]"
                                            >
                                                {{
                                                    statusLabel(
                                                        item.berkala_2.status
                                                    )
                                                }}
                                            </span>

                                            <p
                                                class="max-w-[150px] truncate text-xs text-slate-500"
                                                :title="item.kondisi_b2 || '-'"
                                            >
                                                {{
                                                    item.kondisi_b2 || '-'
                                                }}
                                            </p>

                                        </div>

                                    </template>

                                    <span
                                        v-else
                                        class="text-xs text-slate-400"
                                    >
                                        Belum diperiksa
                                    </span>

                                </td>


                                <!-- STATUS -->
<td class="px-3 py-4">

    <span
        :class="[
            'inline-flex max-w-full rounded-full px-2.5 py-1 text-xs font-bold whitespace-nowrap',
            overallClass(
                item.status_keseluruhan
            )
        ]"
    >
        {{
            overallLabel(
                item.status_keseluruhan
            )
        }}
    </span>

</td>


                                <!-- AKSI -->

                                <td
    class="px-3 py-4 text-center"
>

    <div
        class="flex items-center justify-center gap-1.5"
    >

                                        <button
                                            v-if="item.berkala_1"
                                            type="button"
                                            @click="openB1(item)"
                                            class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100"
                                        >
                                            B1
                                        </button>

                                        <button
                                            v-if="item.berkala_2"
                                            type="button"
                                            @click="openB2(item)"
                                            class="rounded-lg bg-purple-50 px-3 py-2 text-xs font-bold text-purple-700 hover:bg-purple-100"
                                        >
                                            B2
                                        </button>

                                        <span
                                            v-if="
                                                !item.berkala_1 &&
                                                !item.berkala_2
                                            "
                                            class="text-xs text-slate-400"
                                        >
                                            -
                                        </span>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </template>


        <!-- =====================================================
             MODAL DETAIL
        ====================================================== -->

        <Teleport to="body">

            <div
                v-if="selectedPemeriksaan"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="closeDetail"
            >

                <div
    class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
>

                    <!-- HEADER -->

                    <div
                        class="flex items-start justify-between border-b border-slate-100 px-6 py-5"
                    >

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-blue-500"
                            >
                                {{
                                    formatJenis(
                                        selectedPemeriksaan.pemeriksaan?.jenis_pemeriksaan
                                    )
                                }}
                            </p>

                            <h2
                                class="mt-1 text-lg font-bold text-slate-800"
                            >
                                {{ selectedPemeriksaan.siswa?.nama }}
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                NISN:
                                {{ selectedPemeriksaan.siswa?.nisn || '-' }}
                            </p>

                        </div>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100"
                        >

                            <XMarkIcon
                                class="h-5 w-5"
                            />

                        </button>

                    </div>


                    <!-- BODY -->

                    <div
    class="min-h-0 flex-1 overflow-y-auto p-6"
>
                        <template
                            v-if="selectedPemeriksaan.pemeriksaan"
                        >

                            <div
                                class="mb-6 flex flex-wrap gap-2"
                            >

                                <span
                                    :class="[
                                        'rounded-lg px-2.5 py-1 text-xs font-bold',
                                        statusClass(
                                            selectedPemeriksaan.pemeriksaan.status
                                        )
                                    ]"
                                >
                                    {{
                                        statusLabel(
                                            selectedPemeriksaan.pemeriksaan.status
                                        )
                                    }}
                                </span>

                                <span
                                    class="text-xs text-slate-400"
                                >
                                    {{
                                        formatDate(
                                            selectedPemeriksaan.pemeriksaan.tanggal_pemeriksaan
                                        )
                                    }}
                                </span>

                            </div>

                            <!-- PEMERIKSA -->

<div
    class="mb-6 rounded-xl border border-emerald-100 bg-emerald-50 p-4"
>
    <div class="flex items-start gap-3">

        <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-100"
        >
            <CheckCircleIcon
                class="h-5 w-5 text-emerald-600"
            />
        </div>

        <div>

            <p
                class="text-xs font-bold uppercase tracking-wide text-emerald-600"
            >
                Pemeriksa
            </p>

            <p
                class="mt-1 text-sm font-bold text-emerald-800"
            >
                {{
                    selectedPemeriksaan
                        .pemeriksaan
                        ?.pemeriksa
                        ?.name || 'Belum tercatat'
                }}
            </p>

            <p class="mt-1 text-xs text-emerald-700">
                Petugas yang melaksanakan pemeriksaan
                {{
                    formatJenis(
                        selectedPemeriksaan
                            .pemeriksaan
                            ?.jenis_pemeriksaan
                    )
                }}.
            </p>

        </div>

    </div>
</div>


                            <!-- ANTROPOMETRI -->

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Antropometri
                            </h3>

                            <div
                                class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3"
                            >

                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Berat Badan
                                    </p>

                                    <p class="mt-1 text-sm font-bold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.berat_badan
                                            ?? '-'
                                        }}

                                        <span
                                            v-if="selectedPemeriksaan.pemeriksaan.berat_badan"
                                            class="text-xs font-normal text-slate-400"
                                        >
                                            kg
                                        </span>

                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Tinggi Badan
                                    </p>

                                    <p class="mt-1 text-sm font-bold">

                                        {{
                                            selectedPemeriksaan.pemeriksaan.tinggi_badan
                                            ?? '-'
                                        }}

                                        <span
                                            v-if="selectedPemeriksaan.pemeriksaan.tinggi_badan"
                                            class="text-xs font-normal text-slate-400"
                                        >
                                            cm
                                        </span>

                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        IMT
                                    </p>

                                    <p class="mt-1 text-sm font-bold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.imt
                                            ?? '-'
                                        }}
                                    </p>

                                </div>

                            </div>


                            <!-- TANDA VITAL -->

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Tanda Vital
                            </h3>

                            <div
                                class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3"
                            >

                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Tekanan Darah
                                    </p>

                                    <p class="mt-1 text-sm font-bold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.tekanan_darah
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Denyut Nadi
                                    </p>

                                    <p class="mt-1 text-sm font-bold">

                                        {{
                                            selectedPemeriksaan.pemeriksaan.denyut_nadi
                                            ?? '-'
                                        }}

                                        <span
                                            v-if="selectedPemeriksaan.pemeriksaan.denyut_nadi"
                                            class="text-xs font-normal text-slate-400"
                                        >
                                            bpm
                                        </span>

                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Suhu Tubuh
                                    </p>

                                    <p class="mt-1 text-sm font-bold">

                                        {{
                                            selectedPemeriksaan.pemeriksaan.suhu_tubuh
                                            ?? '-'
                                        }}

                                        <span
                                            v-if="selectedPemeriksaan.pemeriksaan.suhu_tubuh"
                                            class="text-xs font-normal text-slate-400"
                                        >
                                            °C
                                        </span>

                                    </p>

                                </div>

                            </div>


                            <!-- FISIK -->

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Pemeriksaan Fisik
                            </h3>

                            <div
                                class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-2"
                            >

                                <div class="rounded-xl border border-slate-100 p-4">

                                    <p class="text-xs text-slate-400">
                                        Mata
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.mata
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl border border-slate-100 p-4">

                                    <p class="text-xs text-slate-400">
                                        Telinga
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.telinga
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl border border-slate-100 p-4">

                                    <p class="text-xs text-slate-400">
                                        Gigi & Mulut
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.gigi_mulut
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl border border-slate-100 p-4">

                                    <p class="text-xs text-slate-400">
                                        Kondisi Umum
                                    </p>

                                    <p class="mt-1 text-sm font-semibold">
                                        {{
                                            selectedPemeriksaan.pemeriksaan.kondisi_umum
                                            || '-'
                                        }}
                                    </p>

                                </div>

                            </div>


                            <!-- HASIL -->

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Hasil Pemeriksaan
                            </h3>

                            <div class="space-y-3">

                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Keluhan
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700"
                                    >
                                        {{
                                            selectedPemeriksaan.pemeriksaan.keluhan
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Hasil Pemeriksaan
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700"
                                    >
                                        {{
                                            selectedPemeriksaan.pemeriksaan.hasil_pemeriksaan
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div class="rounded-xl bg-slate-50 p-4">

                                    <p class="text-xs text-slate-400">
                                        Rekomendasi
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700"
                                    >
                                        {{
                                            selectedPemeriksaan.pemeriksaan.rekomendasi
                                            || '-'
                                        }}
                                    </p>

                                </div>


                                <div
                                    v-if="selectedPemeriksaan.pemeriksaan.catatan"
                                    class="rounded-xl bg-amber-50 p-4"
                                >

                                    <p class="text-xs font-bold text-amber-600">
                                        Catatan
                                    </p>

                                    <p
                                        class="mt-1 whitespace-pre-line text-sm text-amber-800"
                                    >
                                        {{
                                            selectedPemeriksaan.pemeriksaan.catatan
                                        }}
                                    </p>

                                </div>

                            </div>

                        </template>

                    </div>


                    <!-- FOOTER -->

                    <div
    class="flex shrink-0 justify-between border-t border-slate-100 bg-white px-6 py-4"
>
                        <button
                            type="button"
                            @click="downloadDetail"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-blue-700"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh PDF

                        </button>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                        >
                            Tutup
                        </button>

                    </div>

                </div>

            </div>

        </Teleport>

    </div>
</KlinikLayout>
</template>