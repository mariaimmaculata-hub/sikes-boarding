<script setup>
import { computed } from 'vue'
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

const totalPeriode = computed(() => props.reports.length)

const totalSiswa = computed(() => {
    return props.reports.reduce(
        (total, report) => total + Number(report.jumlah_siswa || 0),
        0
    )
})

const totalKunjungan = computed(() => {
    return props.reports.reduce(
        (total, report) => total + Number(report.jumlah_kunjungan || 0),
        0
    )
})

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

        </div>


        <!-- ==================================================
             SUMMARY
        ================================================== -->

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-3"
        >

            <!-- TOTAL PERIODE -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100"
                    >

                        <CalendarDaysIcon
                            class="h-5 w-5 text-purple-600"
                        />

                    </div>

                    <div>

                        <p class="text-xs font-medium text-slate-400">
                            Total Periode
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ totalPeriode }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- TOTAL SISWA -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <UserGroupIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>

                    <div>

                        <p class="text-xs font-medium text-slate-400">
                            Total Siswa
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ totalSiswa }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- TOTAL KUNJUNGAN -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-orange-100"
                    >

                        <HeartIcon
                            class="h-5 w-5 text-orange-600"
                        />

                    </div>

                    <div>

                        <p class="text-xs font-medium text-slate-400">
                            Total Kunjungan
                        </p>

                        <p class="text-xl font-bold text-slate-800">
                            {{ totalKunjungan }}
                        </p>

                    </div>

                </div>

            </div>

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
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5 text-blue-600"
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
                            v-for="report in reports"
                            :key="report.id"
                            class="border-b border-slate-100 transition hover:bg-slate-50/70"
                        >

                            <!-- PERIODE -->

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-purple-100"
                                    >

                                        <CalendarDaysIcon
                                            class="h-4 w-4 text-purple-600"
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
                                        statusClass(report.berkala_1.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(report.berkala_1.lengkap) }}
                                </span>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    {{ report.berkala_1.selesai }}
                                    /
                                    {{ report.berkala_1.total }}
                                </p>

                            </td>


                            <!-- B2 -->

                            <td class="px-5 py-4 text-center">

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                        statusClass(report.berkala_2.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(report.berkala_2.lengkap) }}
                                </span>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    {{ report.berkala_2.selesai }}
                                    /
                                    {{ report.berkala_2.total }}
                                </p>

                            </td>


                            <!-- TKSI -->

                            <td class="px-5 py-4 text-center">

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                        statusClass(report.tksi.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(report.tksi.lengkap) }}
                                </span>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    {{ report.tksi.selesai }}
                                    /
                                    {{ report.tksi.total }}
                                </p>

                            </td>


                            <!-- AKSI -->

                            <td class="px-5 py-4 text-center">

                                <Link
                                    :href="route(
                                        'admin.periode.report.show',
                                        report.id
                                    )"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                                >

                                    <EyeIcon class="h-4 w-4" />

                                    Detail

                                </Link>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr v-if="!reports.length">

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
                                        Belum ada data periode
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Report akan muncul setelah periode
                                        tersedia.
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