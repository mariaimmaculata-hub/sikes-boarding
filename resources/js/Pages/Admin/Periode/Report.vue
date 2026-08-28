<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
    UserGroupIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    reports: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| Breadcrumb
|--------------------------------------------------------------------------
*/

const breadcrumbs = [
    {
        label: 'Periode',
    },
    {
        label: 'Report Periode',
    },
]

/*
|--------------------------------------------------------------------------
| Filter Periode
|--------------------------------------------------------------------------
*/

const selectedPeriode = ref('all')

const filteredReports = computed(() => {
    if (selectedPeriode.value === 'all') {
        return props.reports
    }

    return props.reports.filter(
        report => String(report.id) === String(selectedPeriode.value)
    )
})

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

const totalPeriode = computed(() => filteredReports.value.length)

const totalSiswa = computed(() => {
    return filteredReports.value.reduce(
        (total, report) => total + Number(report.jumlah_siswa || 0),
        0
    )
})

const totalKunjungan = computed(() => {
    return filteredReports.value.reduce(
        (total, report) => total + Number(report.jumlah_kunjungan || 0),
        0
    )
})

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

function statusClass(lengkap) {
    return lengkap
        ? 'bg-emerald-100 text-emerald-700'
        : 'bg-amber-100 text-amber-700'
}

function statusLabel(lengkap) {
    return lengkap ? 'Lengkap' : 'Belum Lengkap'
}
</script>

<template>
    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Report Periode
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Ringkasan data kesehatan dan kelengkapan pemeriksaan
                        setiap periode.
                    </p>
                </div>

                <!-- FILTER PERIODE -->

                <div class="w-full sm:w-64">

                    <label
                        for="periode"
                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                    >
                        Pilih Periode
                    </label>

                    <div class="relative">

                        <CalendarDaysIcon
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        />

                        <select
                            id="periode"
                            v-model="selectedPeriode"
                            class="w-full appearance-none rounded-xl border border-slate-200 bg-white py-2.5 pl-9 pr-9 text-sm font-medium text-slate-700 shadow-sm outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                        >

                            <option value="all">
                                Semua Periode
                            </option>

                            <option
                                v-for="report in reports"
                                :key="report.id"
                                :value="report.id"
                            >
                                {{ report.nama_periode }}
                            </option>

                        </select>

                        <svg
                            class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 9-7 7-7-7"
                            />
                        </svg>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 INFO FILTER
            ================================================== -->

            <div
                v-if="selectedPeriode !== 'all'"
                class="flex items-center justify-between rounded-xl border border-pink-100 bg-pink-50 px-4 py-3"
            >

                <div class="flex items-center gap-2">

                    <CalendarDaysIcon
                        class="h-5 w-5 text-pink-600"
                    />

                    <div>

                        <p class="text-xs font-semibold text-pink-500">
                            Periode yang dipilih
                        </p>

                        <p class="text-sm font-bold text-pink-700">
                            {{
                                filteredReports.length
                                    ? filteredReports[0].nama_periode
                                    : '-'
                            }}
                        </p>

                    </div>

                </div>

                <button
                    type="button"
                    @click="selectedPeriode = 'all'"
                    class="text-xs font-bold text-pink-600 transition hover:text-pink-800"
                >
                    Tampilkan Semua
                </button>

            </div>


            <!-- ==================================================
                 REPORT TABLE
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- TABLE HEADER -->

                <div
                    class="border-b border-slate-200 px-6 py-5"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-pink-100"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-pink-600"
                            />

                        </div>

                        <div>

                            <h2 class="text-sm font-bold text-slate-800">
                                Rekapitulasi Periode
                            </h2>

                            <p class="text-xs text-slate-400">
                                Status kelengkapan pemeriksaan setiap periode.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TABLE -->

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1100px]">

                        <thead>

                            <tr
                                class="border-b border-slate-100 bg-slate-50/70"
                            >

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Periode
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Jumlah Siswa
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Kunjungan Klinik
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Berkala 1
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Berkala 2
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    TKSI
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <!-- DATA -->

                            <tr
                                v-for="report in filteredReports"
                                :key="report.id"
                                class="border-b border-slate-100 transition hover:bg-slate-50/70"
                            >

                                <!-- PERIODE -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-pink-100"
                                        >

                                            <CalendarDaysIcon
                                                class="h-4 w-4 text-pink-600"
                                            />

                                        </div>

                                        <div>

                                            <p
                                                class="text-sm font-bold text-slate-700"
                                            >
                                                {{ report.nama_periode }}
                                            </p>

                                            <p
                                                class="text-xs text-slate-400"
                                            >
                                                Periode kesehatan siswa
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- JUMLAH SISWA -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="text-sm font-bold text-slate-700"
                                    >
                                        {{ report.jumlah_siswa }}
                                    </span>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        siswa
                                    </p>

                                </td>


                                <!-- KUNJUNGAN -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="text-sm font-bold text-slate-700"
                                    >
                                        {{ report.jumlah_kunjungan }}
                                    </span>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        kunjungan
                                    </p>

                                </td>


                                <!-- B1 -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                            statusClass(report.berkala_1?.lengkap)
                                        ]"
                                    >
                                        {{
                                            statusLabel(
                                                report.berkala_1?.lengkap
                                            )
                                        }}
                                    </span>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        {{ report.berkala_1?.selesai || 0 }}
                                        /
                                        {{ report.berkala_1?.total || 0 }}
                                    </p>

                                </td>


                                <!-- B2 -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                            statusClass(report.berkala_2?.lengkap)
                                        ]"
                                    >
                                        {{
                                            statusLabel(
                                                report.berkala_2?.lengkap
                                            )
                                        }}
                                    </span>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        {{ report.berkala_2?.selesai || 0 }}
                                        /
                                        {{ report.berkala_2?.total || 0 }}
                                    </p>

                                </td>


                                <!-- TKSI -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                            statusClass(report.tksi?.lengkap)
                                        ]"
                                    >
                                        {{
                                            statusLabel(
                                                report.tksi?.lengkap
                                            )
                                        }}
                                    </span>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        {{ report.tksi?.selesai || 0 }}
                                        /
                                        {{ report.tksi?.total || 0 }}
                                    </p>

                                </td>


                                <!-- AKSI -->

                                <td class="px-5 py-4 text-center">

                                    <Link
                                        :href="route(
                                            'admin.periode.report.show',
                                            report.id
                                        )"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700 transition hover:bg-pink-100"
                                    >

                                        <EyeIcon class="h-4 w-4" />

                                        Detail

                                    </Link>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr v-if="!filteredReports.length">

                                <td
                                    colspan="7"
                                    class="px-6 py-12 text-center"
                                >

                                    <div
                                        class="mx-auto flex max-w-sm flex-col items-center"
                                    >

                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100"
                                        >

                                            <CalendarDaysIcon
                                                class="h-6 w-6 text-slate-400"
                                            />

                                        </div>

                                        <p
                                            class="mt-3 text-sm font-semibold text-slate-600"
                                        >
                                            {{
                                                selectedPeriode !== 'all'
                                                    ? 'Data periode tidak ditemukan'
                                                    : 'Belum ada data periode'
                                            }}
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            {{
                                                selectedPeriode !== 'all'
                                                    ? 'Silakan pilih periode lainnya.'
                                                    : 'Report akan muncul setelah periode tersedia.'
                                            }}
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>