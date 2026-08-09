<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    CalendarDaysIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ClockIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline'


// ==================================================
// PROPS
// ==================================================

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    statistik: {
        type: Object,
        default: () => ({
            total: 0,
            berkala_1: 0,
            berkala_2: 0,
            tksi: 0,
        }),
    },
})


// ==================================================
// STATUS
// ==================================================

const statusLabel = (status) => {
    return status === 'selesai'
        ? 'Selesai'
        : 'Belum'
}


const statusClass = (status) => {
    return status === 'selesai'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-slate-200 bg-slate-50 text-slate-500'
}


const overallStatusClass = (status) => {
    return status === 'lengkap'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-amber-200 bg-amber-50 text-amber-700'
}


const overallStatusLabel = (status) => {
    return status === 'lengkap'
        ? 'Lengkap'
        : 'Belum'
}


// ==================================================
// FORMAT TANGGAL
// ==================================================

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    return new Date(date).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        }
    )
}
</script>


<template>

    <AdminLayout>

        <div class="space-y-6">


            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >

                <div>

                    <div
                        class="flex items-center gap-2 text-xs font-semibold text-slate-400"
                    >

                        <Link
                            :href="route('admin.periode.index')"
                            class="transition hover:text-blue-600"
                        >
                            Periode
                        </Link>

                        <span>/</span>

                        <span class="text-slate-600">
                            Siswa Periode Aktif
                        </span>

                    </div>


                    <h1
                        class="mt-2 text-2xl font-bold text-slate-800"
                    >
                        Siswa Periode Aktif
                    </h1>


                    <p
                        v-if="periode"
                        class="mt-1 text-sm text-slate-500"
                    >
                        Periode:
                        <span class="font-semibold text-slate-700">
                            {{ periode.nama_periode }}
                        </span>
                    </p>

                    <p
                        v-else
                        class="mt-1 text-sm text-slate-500"
                    >
                        Belum ada periode aktif.
                    </p>

                </div>


                <!-- STATUS PERIODE -->

                <div
                    v-if="periode"
                    class="inline-flex w-fit items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700"
                >

                    <span
                        class="h-2 w-2 rounded-full bg-emerald-500"
                    ></span>

                    Periode Aktif

                </div>

            </div>


            <!-- ==================================================
                 TIDAK ADA PERIODE
            ================================================== -->

            <div
                v-if="!periode"
                class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center shadow-sm"
            >

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100"
                >

                    <CalendarDaysIcon
                        class="h-7 w-7 text-slate-400"
                    />

                </div>


                <h2
                    class="mt-4 text-base font-bold text-slate-700"
                >
                    Belum Ada Periode Aktif
                </h2>


                <p
                    class="mx-auto mt-1 max-w-md text-sm text-slate-400"
                >
                    Aktifkan atau buat periode terlebih dahulu
                    untuk melihat daftar siswa.
                </p>

            </div>


            <template v-else>


                <!-- ==================================================
                     STATISTIK
                ================================================== -->

                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
                >


                    <!-- TOTAL -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-center justify-between"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Total Siswa
                                </p>

                                <p
                                    class="mt-2 text-3xl font-bold text-slate-800"
                                >
                                    {{ statistik.total }}
                                </p>

                            </div>


                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50"
                            >

                                <UserGroupIcon
                                    class="h-6 w-6 text-blue-600"
                                />

                            </div>

                        </div>

                    </div>


                    <!-- BERKALA 1 -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-center justify-between"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Berkala 1
                                </p>

                                <p
                                    class="mt-2 text-3xl font-bold text-slate-800"
                                >
                                    {{ statistik.berkala_1 }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    siswa selesai
                                </p>

                            </div>


                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50"
                            >

                                <CheckCircleIcon
                                    class="h-6 w-6 text-emerald-600"
                                />

                            </div>

                        </div>

                    </div>


                    <!-- BERKALA 2 -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-center justify-between"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Berkala 2
                                </p>

                                <p
                                    class="mt-2 text-3xl font-bold text-slate-800"
                                >
                                    {{ statistik.berkala_2 }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    siswa selesai
                                </p>

                            </div>


                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50"
                            >

                                <CheckCircleIcon
                                    class="h-6 w-6 text-emerald-600"
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
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    TKSI
                                </p>

                                <p
                                    class="mt-2 text-3xl font-bold text-slate-800"
                                >
                                    {{ statistik.tksi }}
                                </p>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    siswa selesai
                                </p>

                            </div>


                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50"
                            >

                                <CheckCircleIcon
                                    class="h-6 w-6 text-blue-600"
                                />

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     TABLE
                ================================================== -->

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <!-- TABLE HEADER -->

                    <div
                        class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Daftar Siswa
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Status pemeriksaan kesehatan
                                pada periode aktif.
                            </p>

                        </div>


                        <div
                            class="text-xs font-semibold text-slate-500"
                        >

                            {{ siswa.length }} siswa

                        </div>

                    </div>


                    <!-- ==================================================
                         DESKTOP
                    ================================================== -->

                    <div class="hidden overflow-x-auto lg:block">

                        <table class="min-w-full">

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
                                        Kelas
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
                                        TKSI
                                    </th>

                                    <th
                                        class="px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Aksi
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
                                        class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                                    >
                                        {{ index + 1 }}
                                    </td>


                                    <!-- SISWA -->

                                    <td class="px-5 py-4">

                                        <div
                                            class="flex items-center gap-3"
                                        >

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                                            >
                                                {{
                                                    item.nama
                                                        ?.charAt(0)
                                                        ?.toUpperCase()
                                                }}
                                            </div>


                                            <div>

                                                <p
                                                    class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                                >
                                                    {{ item.nama }}
                                                </p>

                                                <p
                                                    class="text-xs text-slate-400"
                                                >
                                                    NISN: {{ item.nisn || '-' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                   <!-- KELAS -->

<td
    class="whitespace-nowrap px-5 py-4"
>

    <p
        class="text-sm font-semibold text-slate-700"
    >
        {{ item.kelas?.nama_kelas?.replace(/\s+\d+$/, '') ?? '-' }}
    </p>

    <p
        class="text-xs text-slate-400"
    >
        {{ item.jurusan?.nama_jurusan ?? '-' }}
    </p>

</td>

                                    <!-- BERKALA 1 -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-center"
                                    >

                                        <span
                                            :class="statusClass(
                                                item.berkala_1.status
                                            )"
                                            class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                        >

                                            <CheckCircleIcon
                                                v-if="
                                                    item.berkala_1.status ===
                                                    'selesai'
                                                "
                                                class="h-3.5 w-3.5"
                                            />

                                            <ClockIcon
                                                v-else
                                                class="h-3.5 w-3.5"
                                            />

                                            {{
                                                statusLabel(
                                                    item.berkala_1.status
                                                )
                                            }}

                                        </span>

                                    </td>


                                    <!-- BERKALA 2 -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-center"
                                    >

                                        <span
                                            :class="statusClass(
                                                item.berkala_2.status
                                            )"
                                            class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                        >

                                            <CheckCircleIcon
                                                v-if="
                                                    item.berkala_2.status ===
                                                    'selesai'
                                                "
                                                class="h-3.5 w-3.5"
                                            />

                                            <ClockIcon
                                                v-else
                                                class="h-3.5 w-3.5"
                                            />

                                            {{
                                                statusLabel(
                                                    item.berkala_2.status
                                                )
                                            }}

                                        </span>

                                    </td>


                                    <!-- TKSI -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-center"
                                    >

                                        <span
                                            :class="statusClass(
                                                item.tksi.status
                                            )"
                                            class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                        >

                                            <CheckCircleIcon
                                                v-if="
                                                    item.tksi.status ===
                                                    'selesai'
                                                "
                                                class="h-3.5 w-3.5"
                                            />

                                            <ClockIcon
                                                v-else
                                                class="h-3.5 w-3.5"
                                            />

                                            {{
                                                statusLabel(
                                                    item.tksi.status
                                                )
                                            }}

                                        </span>

                                    </td>


                                    <!-- STATUS -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-center"
                                    >

                                        <span
                                            :class="
                                                overallStatusClass(
                                                    item.status
                                                )
                                            "
                                            class="inline-flex rounded-full border px-3 py-1 text-xs font-bold"
                                        >
                                            {{
                                                overallStatusLabel(
                                                    item.status
                                                )
                                            }}
                                        </span>

                                    </td>


                                    <!-- AKSI -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-right"
                                    >

<Link
    :href="
        route(
            'admin.periode.siswa-aktif.show',
            item.id
        )
    "
    title="Detail siswa"
    class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
>
    <EyeIcon class="h-4 w-4" />
</Link>



                                    </td>

                                </tr>


                                <!-- EMPTY -->

                                <tr v-if="siswa.length === 0">

                                    <td
                                        colspan="8"
                                        class="px-5 py-16 text-center"
                                    >

                                        <div
                                            class="mx-auto max-w-sm"
                                        >

                                            <div
                                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                            >

                                                <UserGroupIcon
                                                    class="h-6 w-6 text-slate-400"
                                                />

                                            </div>

                                            <p
                                                class="mt-3 text-sm font-bold text-slate-700"
                                            >
                                                Belum ada siswa
                                            </p>

                                            <p
                                                class="mt-1 text-xs text-slate-400"
                                            >
                                                Belum ada siswa yang
                                                terdaftar pada periode ini.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <!-- ==================================================
                         MOBILE
                    ================================================== -->

                    <div
                        class="divide-y divide-slate-100 lg:hidden"
                    >

                        <div
                            v-for="item in siswa"
                            :key="item.id"
                            class="p-4"
                        >

                            <!-- HEADER -->

                            <div
                                class="flex items-start justify-between gap-3"
                            >

                                <div
                                    class="flex min-w-0 items-center gap-3"
                                >

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700"
                                    >
                                        {{
                                            item.nama
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                        }}
                                    </div>


                                    <div class="min-w-0">

                                        <p
                                            class="truncate text-sm font-bold text-slate-800"
                                        >
                                            {{ item.nama }}
                                        </p>

                                        <p
    class="text-xs text-slate-400"
>
    {{ item.kelas?.nama_kelas?.replace(/\s+\d+$/, '') ?? '-' }}
    •
    {{ item.jurusan?.nama_jurusan ?? '-' }}
</p>

                                    </div>

                                </div>


                                <span
                                    :class="
                                        overallStatusClass(
                                            item.status
                                        )
                                    "
                                    class="shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-bold"
                                >
                                    {{
                                        overallStatusLabel(
                                            item.status
                                        )
                                    }}
                                </span>

                            </div>


                            <!-- STATUS -->

                            <div
                                class="mt-4 grid grid-cols-3 gap-2"
                            >

                                <div
                                    class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-center"
                                >

                                    <p
                                        class="text-[10px] font-semibold text-slate-400"
                                    >
                                        Berkala 1
                                    </p>

                                    <p
                                        :class="
                                            item.berkala_1.status ===
                                            'selesai'
                                                ? 'text-emerald-600'
                                                : 'text-slate-400'
                                        "
                                        class="mt-1 text-xs font-bold"
                                    >
                                        {{
                                            statusLabel(
                                                item.berkala_1.status
                                            )
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-center"
                                >

                                    <p
                                        class="text-[10px] font-semibold text-slate-400"
                                    >
                                        Berkala 2
                                    </p>

                                    <p
                                        :class="
                                            item.berkala_2.status ===
                                            'selesai'
                                                ? 'text-emerald-600'
                                                : 'text-slate-400'
                                        "
                                        class="mt-1 text-xs font-bold"
                                    >
                                        {{
                                            statusLabel(
                                                item.berkala_2.status
                                            )
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="rounded-xl border border-slate-100 bg-slate-50 p-3 text-center"
                                >

                                    <p
                                        class="text-[10px] font-semibold text-slate-400"
                                    >
                                        TKSI
                                    </p>

                                    <p
                                        :class="
                                            item.tksi.status ===
                                            'selesai'
                                                ? 'text-emerald-600'
                                                : 'text-slate-400'
                                        "
                                        class="mt-1 text-xs font-bold"
                                    >
                                        {{
                                            statusLabel(
                                                item.tksi.status
                                            )
                                        }}
                                    </p>

                                </div>

                            </div>


                            <!-- ACTION -->

                            <div
                                class="mt-4 flex justify-end border-t border-slate-100 pt-3"
                            >


<Link
    :href="
        route(
            'admin.periode.siswa-aktif.show',
            item.id
        )
    "
    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-50"
>
    <EyeIcon class="h-4 w-4" />

    Detail
</Link>



                            </div>

                        </div>


                        <!-- MOBILE EMPTY -->

                        <div
                            v-if="siswa.length === 0"
                            class="px-5 py-16 text-center"
                        >

                            <p
                                class="text-sm font-bold text-slate-700"
                            >
                                Belum ada siswa
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Belum ada siswa pada periode aktif.
                            </p>

                        </div>

                    </div>

                </div>

            </template>

        </div>

    </AdminLayout>

</template>