<script setup>

import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    UserGroupIcon,
    AcademicCapIcon,
    HeartIcon,
    UserPlusIcon,
    ClipboardDocumentCheckIcon,
    ChevronRightIcon,
    CalendarDaysIcon,
    BellIcon
} from '@heroicons/vue/24/outline'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({

    stats: {
        type: Array,
        default: () => []
    },

    penyakitTerbanyak: {
        type: Array,
        default: () => []
    },

    pemeriksaanBulanan: {
        type: Array,
        default: () => []
    },

    kunjunganBulanan: {
        type: Array,
        default: () => []
    },

    jadwalHariIni: {
        type: Array,
        default: () => []
    },

    siswaPemantauan: {
        type: Array,
        default: () => []
    },

    notifications: {
        type: Array,
        default: () => []
    }

})


/*
|--------------------------------------------------------------------------
| ICON STATISTIK
|--------------------------------------------------------------------------
*/

const statIcons = {

    siswa: UserGroupIcon,

    kelas: AcademicCapIcon,

    klinik: HeartIcon,

    pendamping: UserPlusIcon,

    pemeriksaan: ClipboardDocumentCheckIcon,

}


/*
|--------------------------------------------------------------------------
| WARNA STATISTIK
|--------------------------------------------------------------------------
| Mengikuti tema AdminLayout:
| Pink → Rose → Fuchsia → Amber
|--------------------------------------------------------------------------
*/

const statColors = {

    siswa:
        'border-pink-600 text-pink-600 bg-pink-50/50',

    kelas:
        'border-rose-600 text-rose-600 bg-rose-50/50',

    klinik:
        'border-fuchsia-600 text-fuchsia-600 bg-fuchsia-50/50',

    pendamping:
        'border-amber-600 text-amber-600 bg-amber-50/50',

    pemeriksaan:
        'border-pink-700 text-pink-700 bg-pink-50/50',

}


/*
|--------------------------------------------------------------------------
| FORMAT ANGKA
|--------------------------------------------------------------------------
*/

const formatNumber = (value) => {

    return Number(
        value ?? 0
    ).toLocaleString('id-ID')

}


/*
|--------------------------------------------------------------------------
| WARNA PENYAKIT
|--------------------------------------------------------------------------
*/

const illnessColors = [

    'bg-pink-600',

    'bg-rose-600',

    'bg-fuchsia-600',

    'bg-pink-500',

    'bg-rose-500',

]


const getIllnessColor = (index) => {

    return illnessColors[
        index % illnessColors.length
    ]

}


/*
|--------------------------------------------------------------------------
| MAX JUMLAH PENYAKIT
|--------------------------------------------------------------------------
*/

const maxIllnessCount = () => {

    if (!props.penyakitTerbanyak.length) {

        return 1

    }

    return Math.max(

        ...props.penyakitTerbanyak.map(
            item => Number(item.count ?? 0)
        ),

        1

    )

}


/*
|--------------------------------------------------------------------------
| BAR PENYAKIT
|--------------------------------------------------------------------------
*/

const illnessWidth = (count) => {

    const max = maxIllnessCount()

    return `${Math.min(

        (
            Number(count ?? 0) /
            max
        ) * 100,

        100

    )}%`

}


/*
|--------------------------------------------------------------------------
| CHART MAX
|--------------------------------------------------------------------------
*/

const getChartMax = (data) => {

    if (!data || !data.length) {

        return 10

    }

    return Math.max(

        ...data.map(
            item => Number(item.value ?? 0)
        ),

        10

    )

}


/*
|--------------------------------------------------------------------------
| CHART POINT
|--------------------------------------------------------------------------
*/

const chartPoint = (
    data,
    index,
    width = 250,
    height = 100
) => {

    if (!data || !data.length) {

        return '0,100'

    }

    const max =
        getChartMax(data)

    const x =
        data.length === 1

            ? width / 2

            : 50 +
              (
                  index *
                  (
                      (width - 50) /
                      (data.length - 1)
                  )
              )

    const value =
        Number(
            data[index]?.value ?? 0
        )

    const y =
        height -
        (
            (value / max) *
            (height - 20)
        )

    return `${x},${y}`

}


/*
|--------------------------------------------------------------------------
| CHART LINE
|--------------------------------------------------------------------------
*/

const chartLine = (data) => {

    if (!data || !data.length) {

        return ''

    }

    return data

        .map(
            (_, index) =>
                chartPoint(
                    data,
                    index
                )
        )

        .join(' L')

}


/*
|--------------------------------------------------------------------------
| CHART AREA
|--------------------------------------------------------------------------
*/

const chartArea = (data) => {

    if (!data || !data.length) {

        return ''

    }

    const points = data

        .map(
            (_, index) =>
                chartPoint(
                    data,
                    index
                )
        )

        .join(' L')

    const lastX =
        data.length === 1
            ? 50
            : 250

    return `M${points} L${lastX},120 L50,120 Z`

}


/*
|--------------------------------------------------------------------------
| NILAI TITIK GRAFIK
|--------------------------------------------------------------------------
*/

const getPointValue = (item) => {

    return formatNumber(
        item?.value ?? 0
    )

}

</script>


<template>

    <AdminLayout>

        <Head title="Admin Dashboard" />


        <div class="space-y-6">


            <!-- ==================================================
                 WELCOME HEADER
            =================================================== -->

            <div
                class="bg-gradient-to-r from-pink-700 via-pink-700 to-rose-800 rounded-3xl p-6 md:p-8 text-white relative overflow-hidden shadow-lg flex items-center justify-between"
            >

                <!-- BACKGROUND DECORATION -->

                <div
                    class="absolute right-0 top-0 w-80 h-80 bg-white/5 rounded-full blur-2xl pointer-events-none"
                ></div>


                <div
                    class="absolute -right-20 -bottom-32 w-72 h-72 bg-pink-400/10 rounded-full blur-3xl pointer-events-none"
                ></div>


                <!-- TEXT -->

                <div class="relative z-10 space-y-2">

                    <h2
                        class="text-2xl md:text-3xl font-extrabold tracking-tight"
                    >

                        Selamat datang, Admin!

                    </h2>


                    <p
                        class="text-sm md:text-base text-pink-100/90 max-w-2xl font-medium leading-relaxed"
                    >

                        Kelola data kesehatan dan kebugaran siswa
                        dengan mudah dan terintegrasi.

                    </p>

                </div>


                <!-- ==================================================
                     ILUSTRASI GEDUNG
                =================================================== -->

                <div
                    class="hidden md:block relative z-10 w-44 h-auto opacity-95"
                >

                    <svg
                        viewBox="0 0 120 80"
                        fill="none"
                        class="w-full text-pink-200"
                    >

                        <path
                            d="M10 70h100M20 70V30l40-15 40 15v40M45 70V50h30v20"
                            stroke="currentColor"
                            stroke-width="2.5"
                            stroke-linecap="round"
                        />


                        <rect
                            x="30"
                            y="38"
                            width="10"
                            height="10"
                            rx="1.5"
                            fill="currentColor"
                            fill-opacity="0.3"
                        />


                        <rect
                            x="80"
                            y="38"
                            width="10"
                            height="10"
                            rx="1.5"
                            fill="currentColor"
                            fill-opacity="0.3"
                        />


                        <path
                            d="M60 10v6M57 13h6"
                            stroke="#FCA5A5"
                            stroke-width="2"
                        />

                    </svg>

                </div>

            </div>



            <!-- ==================================================
                 STATISTIK
            =================================================== -->

            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5"
            >

                <div
                    v-for="(item, idx) in stats"
                    :key="idx"
                    class="bg-white border-t-4 rounded-2xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg hover:-translate-y-0.5 transition duration-300"
                    :class="statColors[item.type]?.split(' ')[0]"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <span
                            class="text-slate-500 text-xs font-bold uppercase tracking-wider"
                        >

                            {{ item.name }}

                        </span>


                        <div
                            class="p-2 rounded-xl"
                            :class="
                                statColors[item.type]
                                    ?.split(' ')
                                    .slice(1)
                                    .join(' ')
                            "
                        >

                            <component
                                :is="statIcons[item.type]"
                                class="w-5 h-5"
                            />

                        </div>

                    </div>


                    <div class="mt-4">

                        <span
                            class="text-2xl font-extrabold text-slate-900"
                        >

                            {{ formatNumber(item.value) }}

                        </span>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 GRAFIK + PENYAKIT
            =================================================== -->

            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-6"
            >


                <!-- ==================================================
                     PEMERIKSAAN BERKALA
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900"
                        >

                            Grafik Pemeriksaan Berkala

                        </h3>


                        <span
                            class="text-slate-400 text-xs font-semibold"
                        >

                            (3 Bulan Terakhir)

                        </span>

                    </div>


                    <div
                        class="my-6 h-48 w-full flex items-center justify-center"
                    >

                        <svg
                            viewBox="0 0 300 150"
                            class="w-full h-full text-pink-600"
                            preserveAspectRatio="none"
                        >

                            <!-- GRID -->

                            <line
                                x1="30"
                                y1="20"
                                x2="280"
                                y2="20"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="60"
                                x2="280"
                                y2="60"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="100"
                                x2="280"
                                y2="100"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="120"
                                x2="280"
                                y2="120"
                                stroke="#fbcfe8"
                                stroke-width="1.5"
                            />


                            <!-- AREA -->

                            <path
                                v-if="pemeriksaanBulanan.length"
                                :d="chartArea(pemeriksaanBulanan)"
                                fill="currentColor"
                                fill-opacity="0.08"
                            />


                            <!-- LINE -->

                            <path
                                v-if="pemeriksaanBulanan.length"
                                :d="`M${chartLine(pemeriksaanBulanan)}`"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />


                            <!-- POINT -->

                            <template
                                v-for="(item, index) in pemeriksaanBulanan"
                                :key="index"
                            >

                                <circle
                                    :cx="
                                        chartPoint(
                                            pemeriksaanBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    :cy="
                                        chartPoint(
                                            pemeriksaanBulanan,
                                            index
                                        ).split(',')[1]
                                    "
                                    r="4.5"
                                    fill="currentColor"
                                    stroke="#fff"
                                    stroke-width="2"
                                />


                                <text
                                    :x="
                                        chartPoint(
                                            pemeriksaanBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    :y="
                                        Number(
                                            chartPoint(
                                                pemeriksaanBulanan,
                                                index
                                            ).split(',')[1]
                                        ) - 10
                                    "
                                    text-anchor="middle"
                                    font-size="9"
                                    font-weight="bold"
                                    fill="#1e293b"
                                >

                                    {{ getPointValue(item) }}

                                </text>


                                <text
                                    :x="
                                        chartPoint(
                                            pemeriksaanBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    y="136"
                                    text-anchor="middle"
                                    font-size="9"
                                    font-weight="bold"
                                    fill="#64748b"
                                >

                                    {{ item.label }}

                                </text>

                            </template>


                            <!-- EMPTY -->

                            <text
                                v-if="!pemeriksaanBulanan.length"
                                x="150"
                                y="75"
                                text-anchor="middle"
                                font-size="11"
                                fill="#94a3b8"
                            >

                                Belum ada data

                            </text>

                        </svg>

                    </div>

                </div>



                <!-- ==================================================
                     KUNJUNGAN KLINIK
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900"
                        >

                            Grafik Kunjungan Klinik

                        </h3>


                        <span
                            class="text-slate-400 text-xs font-semibold"
                        >

                            (3 Bulan Terakhir)

                        </span>

                    </div>


                    <div
                        class="my-6 h-48 w-full flex items-center justify-center"
                    >

                        <svg
                            viewBox="0 0 300 150"
                            class="w-full h-full text-rose-600"
                            preserveAspectRatio="none"
                        >

                            <!-- GRID -->

                            <line
                                x1="30"
                                y1="20"
                                x2="280"
                                y2="20"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="60"
                                x2="280"
                                y2="60"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="100"
                                x2="280"
                                y2="100"
                                stroke="#fce7f3"
                            />


                            <line
                                x1="30"
                                y1="120"
                                x2="280"
                                y2="120"
                                stroke="#fbcfe8"
                                stroke-width="1.5"
                            />


                            <!-- AREA -->

                            <path
                                v-if="kunjunganBulanan.length"
                                :d="chartArea(kunjunganBulanan)"
                                fill="currentColor"
                                fill-opacity="0.08"
                            />


                            <!-- LINE -->

                            <path
                                v-if="kunjunganBulanan.length"
                                :d="`M${chartLine(kunjunganBulanan)}`"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />


                            <!-- POINT -->

                            <template
                                v-for="(item, index) in kunjunganBulanan"
                                :key="index"
                            >

                                <circle
                                    :cx="
                                        chartPoint(
                                            kunjunganBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    :cy="
                                        chartPoint(
                                            kunjunganBulanan,
                                            index
                                        ).split(',')[1]
                                    "
                                    r="4.5"
                                    fill="currentColor"
                                    stroke="#fff"
                                    stroke-width="2"
                                />


                                <text
                                    :x="
                                        chartPoint(
                                            kunjunganBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    :y="
                                        Number(
                                            chartPoint(
                                                kunjunganBulanan,
                                                index
                                            ).split(',')[1]
                                        ) - 10
                                    "
                                    text-anchor="middle"
                                    font-size="9"
                                    font-weight="bold"
                                    fill="#1e293b"
                                >

                                    {{ getPointValue(item) }}

                                </text>


                                <text
                                    :x="
                                        chartPoint(
                                            kunjunganBulanan,
                                            index
                                        ).split(',')[0]
                                    "
                                    y="136"
                                    text-anchor="middle"
                                    font-size="9"
                                    font-weight="bold"
                                    fill="#64748b"
                                >

                                    {{ item.label }}

                                </text>

                            </template>


                            <!-- EMPTY -->

                            <text
                                v-if="!kunjunganBulanan.length"
                                x="150"
                                y="75"
                                text-anchor="middle"
                                font-size="11"
                                fill="#94a3b8"
                            >

                                Belum ada data

                            </text>

                        </svg>

                    </div>

                </div>



                <!-- ==================================================
                     PENYAKIT TERBANYAK
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900"
                        >

                            5 Penyakit Terbanyak Bulan Ini

                        </h3>


                        <span
                            class="text-slate-400 text-xs font-semibold"
                        >

                            (Bulan Berjalan)

                        </span>

                    </div>


                    <div class="my-4 space-y-3.5">

                        <div
                            v-for="(ill, idx) in penyakitTerbanyak"
                            :key="idx"
                            class="space-y-1"
                        >

                            <div
                                class="flex items-center justify-between text-xs font-semibold"
                            >

                                <span
                                    class="text-slate-700 flex items-center space-x-1.5"
                                >

                                    <span
                                        class="w-2.5 h-2.5 rounded-full"
                                        :class="
                                            getIllnessColor(idx)
                                        "
                                    ></span>


                                    <span>

                                        {{ ill.name }}

                                    </span>

                                </span>


                                <span
                                    class="text-slate-900 font-bold"
                                >

                                    {{ formatNumber(ill.count) }}
                                    kasus

                                </span>

                            </div>


                            <div
                                class="w-full bg-slate-100 h-2 rounded-full overflow-hidden"
                            >

                                <div
                                    class="h-full rounded-full transition-all duration-500"
                                    :class="
                                        getIllnessColor(idx)
                                    "
                                    :style="{
                                        width:
                                            illnessWidth(
                                                ill.count
                                            )
                                    }"
                                ></div>

                            </div>

                        </div>


                        <!-- EMPTY -->

                        <div
                            v-if="!penyakitTerbanyak.length"
                            class="py-8 text-center text-xs font-semibold text-slate-400"
                        >

                            Belum ada data penyakit bulan ini.

                        </div>

                    </div>


                    <!-- LINK -->

                    <Link
                        href="/admin/kunjungan"
                        class="pt-3 border-t border-pink-100 flex items-center justify-between text-xs font-bold text-pink-600 hover:text-pink-800 transition"
                    >

                        <span>

                            Lihat semua

                        </span>


                        <ChevronRightIcon
                            class="w-4 h-4"
                        />

                    </Link>

                </div>

            </div>



            <!-- ==================================================
                 BAGIAN BAWAH
            =================================================== -->

            <div
                class="grid grid-cols-1 lg:grid-cols-3 gap-6"
            >


                <!-- ==================================================
                     JADWAL HARI INI
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2"
                        >

                            <CalendarDaysIcon
                                class="w-5 h-5 text-pink-600"
                            />

                            <span>

                                Jadwal Hari Ini

                            </span>

                        </h3>


                        <!-- ADA DATA -->

                        <div
                            v-if="jadwalHariIni.length"
                            class="space-y-3"
                        >

                            <div
                                v-for="(sched, idx) in jadwalHariIni"
                                :key="idx"
                                class="flex space-x-3.5 p-3 rounded-xl hover:bg-pink-50/50 border border-transparent hover:border-pink-100 transition"
                            >

                                <!-- TIME -->

                                <div
                                    class="bg-pink-50 text-pink-700 text-xs font-bold px-2 py-1 h-fit rounded-lg flex-shrink-0 text-center leading-relaxed"
                                >

                                    {{ sched.time }}

                                    <span
                                        class="block text-[9px] font-medium text-pink-500/80"
                                    >

                                        Mulai

                                    </span>

                                </div>


                                <!-- DESCRIPTION -->

                                <div class="space-y-0.5">

                                    <span
                                        class="text-sm font-bold text-slate-900"
                                    >

                                        {{ sched.title }}

                                    </span>


                                    <p
                                        class="text-xs text-slate-500 font-semibold"
                                    >

                                        {{ sched.desc }}

                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- EMPTY -->

                        <div
                            v-else
                            class="py-8 text-center"
                        >

                            <CalendarDaysIcon
                                class="w-10 h-10 mx-auto text-pink-200"
                            />


                            <p
                                class="mt-2 text-xs font-semibold text-slate-400"
                            >

                                Tidak ada jadwal hari ini.

                            </p>

                        </div>

                    </div>


                    <!-- LINK -->

                    <Link
                        href="/admin/periode"
                        class="pt-4 mt-4 border-t border-pink-100 flex items-center justify-between text-xs font-bold text-pink-600 hover:text-pink-800 transition"
                    >

                        <span>

                            Lihat semua jadwal

                        </span>


                        <ChevronRightIcon
                            class="w-4 h-4"
                        />

                    </Link>

                </div>



                <!-- ==================================================
                     SISWA PEMANTAUAN
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2"
                        >

                            <UserGroupIcon
                                class="w-5 h-5 text-rose-600"
                            />

                            <span>

                                Siswa Perlu Pemantauan

                            </span>

                        </h3>


                        <!-- ADA DATA -->

                        <div
                            v-if="siswaPemantauan.length"
                            class="overflow-x-auto"
                        >

                            <table
                                class="w-full text-left border-collapse"
                            >

                                <thead>

                                    <tr
                                        class="border-b border-pink-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                                    >

                                        <th class="pb-2">

                                            Nama

                                        </th>


                                        <th class="pb-2">

                                            Kelas

                                        </th>


                                        <th class="pb-2 text-right">

                                            Status

                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr
                                        v-for="(student, idx) in siswaPemantauan"
                                        :key="idx"
                                        class="border-b border-pink-100/50 hover:bg-pink-50/40 transition"
                                    >

                                        <td
                                            class="py-2.5 text-xs font-bold text-slate-900"
                                        >

                                            {{ student.name }}

                                        </td>


                                        <td
                                            class="py-2.5 text-xs font-semibold text-slate-500"
                                        >

                                            {{ student.class }}

                                        </td>


                                        <td
                                            class="py-2.5 text-right"
                                        >

                                            <span
                                                class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border"
                                                :class="
                                                    student.style ??
                                                    'bg-amber-50 text-amber-700 border-amber-200'
                                                "
                                            >

                                                {{ student.status }}

                                            </span>

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>


                        <!-- EMPTY -->

                        <div
                            v-else
                            class="py-8 text-center"
                        >

                            <UserGroupIcon
                                class="w-10 h-10 mx-auto text-pink-200"
                            />


                            <p
                                class="mt-2 text-xs font-semibold text-slate-400"
                            >

                                Belum ada siswa dalam pemantauan.

                            </p>

                        </div>

                    </div>


                    <!-- LINK -->

                    <Link
                        href="/admin/pemeriksaan"
                        class="pt-4 mt-4 border-t border-pink-100 flex items-center justify-between text-xs font-bold text-pink-600 hover:text-pink-800 transition"
                    >

                        <span>

                            Lihat semua

                        </span>


                        <ChevronRightIcon
                            class="w-4 h-4"
                        />

                    </Link>

                </div>



                <!-- ==================================================
                     NOTIFIKASI
                =================================================== -->

                <div
                    class="bg-white rounded-2xl shadow-md p-5 border border-pink-100 flex flex-col justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-900 mb-4 flex items-center space-x-2"
                        >

                            <BellIcon
                                class="w-5 h-5 text-fuchsia-600"
                            />

                            <span>

                                Notifikasi

                            </span>

                        </h3>


                        <!-- ADA DATA -->

                        <div
                            v-if="notifications.length"
                            class="space-y-3.5"
                        >

                            <div
                                v-for="(notif, idx) in notifications"
                                :key="idx"
                                class="flex space-x-3 items-start"
                            >

                                <!-- ICON -->

                                <div
                                    class="p-2 rounded-xl flex-shrink-0"
                                    :class="
                                        notif.color ??
                                        'bg-pink-100 text-pink-700'
                                    "
                                >

                                    <component
                                        :is="
                                            notif.icon ??
                                            BellIcon
                                        "
                                        class="w-4 h-4"
                                    />

                                </div>


                                <!-- CONTENT -->

                                <div class="space-y-1">

                                    <p
                                        class="text-xs font-semibold text-slate-800 leading-normal"
                                    >

                                        {{ notif.text }}

                                    </p>


                                    <span
                                        class="text-[10px] text-slate-400 font-bold block"
                                    >

                                        {{ notif.time }}

                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- EMPTY -->

                        <div
                            v-else
                            class="py-8 text-center"
                        >

                            <BellIcon
                                class="w-10 h-10 mx-auto text-pink-200"
                            />


                            <p
                                class="mt-2 text-xs font-semibold text-slate-400"
                            >

                                Tidak ada notifikasi baru.

                            </p>

                        </div>

                    </div>


                    <!-- LINK -->

                    <Link
                        href="/admin/notifikasi"
                        class="pt-4 mt-4 border-t border-pink-100 flex items-center justify-between text-xs font-bold text-pink-600 hover:text-pink-800 transition"
                    >

                        <span>

                            Lihat semua notifikasi

                        </span>


                        <ChevronRightIcon
                            class="w-4 h-4"
                        />

                    </Link>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>