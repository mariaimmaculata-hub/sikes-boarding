<script setup>
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    HeartIcon,
    ClipboardDocumentCheckIcon,
    ArrowDownTrayIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import { Link } from '@inertiajs/vue3'

const props = defineProps({
    periode: Object,
    summary: Object,
    siswa: Array,
})

const statusClass = (lengkap) => {
    return lengkap
        ? 'bg-emerald-50 text-emerald-700'
        : 'bg-red-50 text-red-700'
}

const statusLabel = (lengkap) => {
    return lengkap ? '✓ Lengkap' : '✕ Belum'
}

const formatHasil = (hasil) => {
    if (!hasil || typeof hasil !== 'object') {
        return []
    }

    return Object.entries(hasil)
}

const downloadReport = () => {
    window.location.href = route(
        'admin.periode.report.download',
        props.periode.id
    )
}
</script>

<template>
   <AdminLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">

        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div>

            <Link
                :href="route('admin.periode.report')"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-blue-600"
            >
                <ArrowLeftIcon class="h-4 w-4" />

                Kembali ke Report Periode
            </Link>

            <div class="mt-5">

                <h1 class="text-2xl font-bold text-slate-800">
                    DETAIL REPORT PERIODE
                </h1>

                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">

                    <span class="font-semibold text-slate-700">
                        Periode:
                    </span>

                    <span class="text-slate-600">
                        {{ periode.nama_periode }}
                    </span>

                </div>

                <div class="mt-1 flex items-center gap-2 text-sm text-slate-400">

                    <CalendarDaysIcon class="h-4 w-4" />

                    {{ periode.tanggal_mulai }}
                    —
                    {{ periode.tanggal_selesai }}

                </div>

            </div>

        </div>


        <!-- =====================================================
             SUMMARY
        ====================================================== -->

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

            <!-- SISWA -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100"
                    >
                        <UserGroupIcon
                            class="h-6 w-6 text-blue-600"
                        />
                    </div>

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Siswa
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-800">
                            {{ summary.total_siswa }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- KUNJUNGAN -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100"
                    >
                        <HeartIcon
                            class="h-6 w-6 text-orange-600"
                        />
                    </div>

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Kunjungan
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-800">
                            {{ summary.total_kunjungan }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- DATA LENGKAP -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div class="flex items-center gap-4">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100"
                    >
                        <CheckCircleIcon
                            class="h-6 w-6 text-emerald-600"
                        />
                    </div>

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Data Lengkap
                        </p>

                        <p class="mt-1 text-2xl font-bold text-slate-800">

                            {{ summary.total_lengkap }}

                            <span class="text-sm font-medium text-slate-400">
                                / {{ summary.total_siswa }}
                            </span>

                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             TABLE
        ====================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <!-- TABLE HEADER -->

            <div
                class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between"
            >

                <div>

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
                                DAFTAR HASIL KESEHATAN SISWA
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Rekap pemeriksaan seluruh siswa dalam periode ini
                            </p>

                        </div>

                    </div>

                </div>


                <!-- DOWNLOAD -->

                <button
                    type="button"
                    @click="downloadReport"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700"
                >

                    <ArrowDownTrayIcon class="h-4 w-4" />

                    Unduh Report

                </button>

            </div>


            <!-- TABLE -->

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1250px]">

                    <thead>

                        <tr class="border-b border-slate-100 bg-slate-50/70">

                            <th
                                class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                No
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Siswa
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Kelas
                            </th>

                            <th
                                class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Kunjungan
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                B1
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                B2
                            </th>

                            <th
                                class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                TKSI
                            </th>

                            <th
                                class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="(item, index) in siswa"
                            :key="item.id"
                            class="border-b border-slate-100 align-top transition hover:bg-slate-50/70"
                        >

                            <!-- NO -->

                            <td class="px-5 py-5 text-center">

                                <span class="text-sm font-semibold text-slate-500">
                                    {{ index + 1 }}
                                </span>

                            </td>


                            <!-- SISWA -->

                            <td class="px-5 py-5">

                                <p class="text-sm font-bold text-slate-700">
                                    {{ item.nama }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ item.nisn }}
                                </p>

                            </td>


                            <!-- KELAS -->

                            <td class="px-5 py-5">

                                <span class="text-sm font-medium text-slate-600">
                                    {{ item.kelas }}
                                </span>

                            </td>


                            <!-- KUNJUNGAN -->

                            <td class="px-5 py-5 text-center">

                                <span
                                    class="text-sm font-bold text-slate-700"
                                >
                                    {{ item.jumlah_kunjungan }}
                                </span>

                                <p class="text-xs text-slate-400">
                                    kunjungan
                                </p>

                            </td>


                            <!-- B1 -->

                            <td class="px-5 py-5">

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        statusClass(item.b1.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(item.b1.lengkap) }}
                                </span>

                                <div
                                    v-if="item.b1.lengkap"
                                    class="mt-2 space-y-0.5"
                                >

                                    <p
                                        v-for="[key, value] in formatHasil(item.b1.hasil)"
                                        :key="key"
                                        class="text-xs text-slate-500"
                                    >
                                        <span class="font-medium">
                                            {{ key }}:
                                        </span>

                                        {{ value }}
                                    </p>

                                </div>

                                <p
                                    v-else
                                    class="mt-2 text-xs text-slate-400"
                                >
                                    -
                                </p>

                            </td>


                            <!-- B2 -->

                            <td class="px-5 py-5">

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        statusClass(item.b2.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(item.b2.lengkap) }}
                                </span>

                                <div
                                    v-if="item.b2.lengkap"
                                    class="mt-2 space-y-0.5"
                                >

                                    <p
                                        v-for="[key, value] in formatHasil(item.b2.hasil)"
                                        :key="key"
                                        class="text-xs text-slate-500"
                                    >
                                        <span class="font-medium">
                                            {{ key }}:
                                        </span>

                                        {{ value }}
                                    </p>

                                </div>

                                <p
                                    v-else
                                    class="mt-2 text-xs text-slate-400"
                                >
                                    -
                                </p>

                            </td>


                            <!-- TKSI -->

                            <td class="px-5 py-5">

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                        statusClass(item.tksi.lengkap)
                                    ]"
                                >
                                    {{ statusLabel(item.tksi.lengkap) }}
                                </span>

                                <div
                                    v-if="item.tksi.lengkap"
                                    class="mt-2 space-y-0.5"
                                >

                                    <p
                                        v-for="hasil in item.tksi.hasil"
                                        :key="hasil.komponen"
                                        class="text-xs text-slate-500"
                                    >

                                        <span class="font-medium">
                                            {{ hasil.komponen }}:
                                        </span>

                                        {{ hasil.nilai }}

                                    </p>

                                </div>

                                <p
                                    v-else
                                    class="mt-2 text-xs text-slate-400"
                                >
                                    -
                                </p>

                            </td>


                            <!-- STATUS -->

                            <td class="px-5 py-5 text-center">

                                <div
                                    v-if="item.status === 'Lengkap'"
                                    class="inline-flex flex-col items-center gap-1"
                                >

                                    <CheckCircleIcon
                                        class="h-5 w-5 text-emerald-500"
                                    />

                                    <span class="text-xs font-bold text-emerald-600">
                                        Lengkap
                                    </span>

                                </div>

                                <div
                                    v-else
                                    class="inline-flex flex-col items-center gap-1"
                                >

                                    <XCircleIcon
                                        class="h-5 w-5 text-red-500"
                                    />

                                    <span class="text-xs font-bold text-red-600">
                                        Belum Lengkap
                                    </span>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr v-if="!siswa.length">

                            <td
                                colspan="8"
                                class="px-6 py-12 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <UserGroupIcon
                                        class="h-10 w-10 text-slate-300"
                                    />

                                    <p class="mt-3 text-sm font-semibold text-slate-600">
                                        Belum ada siswa
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Belum ada siswa yang terdaftar pada periode ini.
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