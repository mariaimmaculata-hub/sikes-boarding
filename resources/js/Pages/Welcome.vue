<script setup>

import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    Line,
    Doughnut,
    Bar
} from 'vue-chartjs'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Filler
} from 'chart.js'


/*
|--------------------------------------------------------------------------
| CHART JS
|--------------------------------------------------------------------------
*/

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    ArcElement,
    Filler
)


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({

    stats: {
        type: Object,
        default: () => ({})
    },

    lineChart: {
        type: Object,
        default: () => ({
            labels: [],
            data: []
        })
    },

    doughnutChart: {
        type: Object,
        default: () => ({
            labels: [],
            data: []
        })
    },

    diseaseChart: {
        type: Object,
        default: () => ({
            labels: [],
            data: []
        })
    },

    tableData: {
        type: Array,
        default: () => []
    },

    recentVisits: {
        type: Array,
        default: () => []
    },

    diseases: {
        type: Array,
        default: () => []
    }

})


/*
|--------------------------------------------------------------------------
| HELPER
|--------------------------------------------------------------------------
*/

const numberFormat = (value) => {
    return Number(value ?? 0).toLocaleString('id-ID')
}

const percentage = (value) => {
    return `${Number(value ?? 0).toFixed(1)}%`
}


/*
|--------------------------------------------------------------------------
| STATISTIK UTAMA
|--------------------------------------------------------------------------
*/

const totalSiswa = computed(() =>
    numberFormat(props.stats?.totalSiswa)
)


/*
|--------------------------------------------------------------------------
| PERIODE AKTIF
|--------------------------------------------------------------------------
*/

const periodeAktif = computed(() =>
    props.stats?.periodeAktif ??
    props.stats?.namaPeriode ??
    '-'
)


/*
|--------------------------------------------------------------------------
| PEMERIKSAAN BERKALA
|--------------------------------------------------------------------------
*/

const berkala1 = computed(() =>
    numberFormat(
        props.stats?.berkala1 ??
        props.stats?.berkala_1_selesai ??
        0
    )
)

const berkala2 = computed(() =>
    numberFormat(
        props.stats?.berkala2 ??
        props.stats?.berkala_2_selesai ??
        0
    )
)

const totalPemeriksaan = computed(() => {

    const value =
        props.stats?.pemeriksaanBerkala ??
        props.stats?.totalPemeriksaan ??
        (
            Number(
                props.stats?.berkala1 ??
                props.stats?.berkala_1_selesai ??
                0
            ) +
            Number(
                props.stats?.berkala2 ??
                props.stats?.berkala_2_selesai ??
                0
            )
        )

    return numberFormat(value)

})


/*
|--------------------------------------------------------------------------
| TKSI
|--------------------------------------------------------------------------
*/

const tksiSelesai = computed(() =>
    numberFormat(
        props.stats?.tksiSelesai ??
        props.stats?.tesKebugaran ??
        0
    )
)

const tksiBulanIni = computed(() =>
    numberFormat(
        props.stats?.tksiBulanIni ??
        props.stats?.tesKebugaranBulanIni ??
        0
    )
)


/*
|--------------------------------------------------------------------------
| KUNJUNGAN KLINIK
|--------------------------------------------------------------------------
*/

const totalKunjungan = computed(() =>
    numberFormat(
        props.stats?.totalKunjungan ??
        0
    )
)

const kunjunganBulanIni = computed(() =>
    numberFormat(
        props.stats?.kunjunganBulanIni ??
        0
    )
)

const totalKunjungan30Hari = computed(() =>
    numberFormat(
        props.stats?.totalKunjungan30Hari ??
        0
    )
)


/*
|--------------------------------------------------------------------------
| STATISTIK TAMBAHAN
|--------------------------------------------------------------------------
*/

const totalKelas = computed(() =>
    numberFormat(
        props.stats?.totalKelas
    )
)

const rataRataKunjungan = computed(() =>
    Number(
        props.stats?.rataRataKunjungan ??
        0
    ).toFixed(1)
)

const puncakKunjungan = computed(() =>
    numberFormat(
        props.stats?.puncakKunjungan
    )
)

const tren7Hari = computed(() =>
    Number(
        props.stats?.tren7Hari ??
        0
    )
)


/*
|--------------------------------------------------------------------------
| STATUS KESEHATAN
|--------------------------------------------------------------------------
*/

const sehat = computed(() =>
    numberFormat(
        props.stats?.sehat
    )
)

const perluPerhatian = computed(() =>
    numberFormat(
        props.stats?.perluPerhatian
    )
)

const rujuk = computed(() =>
    numberFormat(
        props.stats?.rujuk
    )
)

const belumDiperiksa = computed(() =>
    numberFormat(
        props.stats?.belumDiperiksa
    )
)

const totalStatusKesehatan = computed(() =>
    numberFormat(
        props.stats?.totalStatusKesehatan
    )
)


/*
|--------------------------------------------------------------------------
| PROGRESS PEMERIKSAAN KESEHATAN
|--------------------------------------------------------------------------
*/

const progressKesehatan = computed(() => {

    const total = Number(
        props.stats?.totalSiswa ?? 0
    )

    const diperiksa = Number(
        props.stats?.totalStatusKesehatan ?? 0
    )

    if (total <= 0) {
        return 0
    }

    return Math.min(
        100,
        Math.round(
            (diperiksa / total) * 100
        )
    )

})


/*
|--------------------------------------------------------------------------
| PROGRESS TKSI
|--------------------------------------------------------------------------
*/

const progressTKSI = computed(() => {

    const total = Number(
        props.stats?.totalSiswa ?? 0
    )

    const selesai = Number(
        props.stats?.tksiSelesai ??
        props.stats?.tesKebugaran ??
        0
    )

    if (total <= 0) {
        return 0
    }

    return Math.min(
        100,
        Math.round(
            (selesai / total) * 100
        )
    )

})


/*
|--------------------------------------------------------------------------
| LINE CHART
|--------------------------------------------------------------------------
*/

const lineChartData = computed(() => ({

    labels:
        props.lineChart?.labels ?? [],

    datasets: [

        {
            label: 'Jumlah Kunjungan',

            data:
                props.lineChart?.data ?? [],

            borderColor:
                '#D4458A',

            backgroundColor:
                'rgba(212, 69, 138, 0.10)',

            borderWidth:
                2.5,

            fill:
                true,

            tension:
                0.35,

            pointBackgroundColor:
                '#D4458A',

            pointBorderColor:
                '#ffffff',

            pointBorderWidth:
                2,

            pointRadius:
                4,

            pointHoverRadius:
                6
        }

    ]

}))


const lineChartOptions = {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {
            display: false
        },

        tooltip: {

            padding: 10,

            backgroundColor:
                '#7D1E4D',

            titleFont: {
                size: 12,
                weight: 'bold'
            },

            bodyFont: {
                size: 11
            },

            cornerRadius: 8

        }

    },

    scales: {

        y: {

            beginAtZero: true,

            ticks: {

                precision: 0,

                color:
                    '#9A265E',

                font: {
                    size: 10
                }

            },

            grid: {
                color:
                    '#FAD4E6'
            }

        },

        x: {

            grid: {
                display: false
            },

            ticks: {

                color:
                    '#9A265E',

                font: {
                    size: 10
                }

            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| DOUGHNUT CHART
|--------------------------------------------------------------------------
*/

const doughnutChartData = computed(() => ({

    labels:
        props.doughnutChart?.labels ?? [],

    datasets: [

        {

            data:
                props.doughnutChart?.data ?? [],

            backgroundColor: [
                '#D4458A',
                '#EE7CAE',
                '#F5A9C9'
            ],

            borderWidth:
                0,

            hoverOffset:
                5

        }

    ]

}))


const doughnutChartOptions = {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {

            position:
                'bottom',

            labels: {

                color:
                    '#9A265E',

                font: {

                    size: 11,

                    weight:
                        '600'

                },

                padding:
                    15,

                usePointStyle:
                    true

            }

        },

        tooltip: {

            padding:
                10,

            callbacks: {

                label:
                    (context) => {

                        return ` ${context.label}: ${context.raw}%`

                    }

            }

        }

    },

    cutout:
        '68%'

}


/*
|--------------------------------------------------------------------------
| DISEASE BAR CHART
|--------------------------------------------------------------------------
*/

const diseaseChartData = computed(() => ({

    labels:
        props.diseaseChart?.labels ?? [],

    datasets: [

        {

            label:
                'Jumlah Kasus',

            data:
                props.diseaseChart?.data ?? [],

            backgroundColor: [
                '#D4458A',
                '#E45E9C',
                '#EE7CAE',
                '#F5A9C9',
                '#C73E80',
                '#B73574',
                '#9A265E',
                '#FAD4E6'
            ],

            borderRadius:
                6,

            borderSkipped:
                false

        }

    ]

}))


const diseaseChartOptions = {

    responsive: true,

    maintainAspectRatio: false,

    plugins: {

        legend: {
            display: false
        },

        tooltip: {

            padding:
                10,

            backgroundColor:
                '#7D1E4D',

            cornerRadius:
                8

        }

    },

    scales: {

        y: {

            beginAtZero:
                true,

            ticks: {

                precision:
                    0,

                color:
                    '#9A265E'

            },

            grid: {

                color:
                    '#FAD4E6'

            }

        },

        x: {

            grid: {
                display: false
            },

            ticks: {
                color:
                    '#9A265E'
            }

        }

    }

}


/*
|--------------------------------------------------------------------------
| PENYAKIT TERATAS
|--------------------------------------------------------------------------
*/

const topDisease = computed(() => {

    if (!props.diseases?.length) {
        return null
    }

    return [...props.diseases].sort(
        (a, b) =>
            Number(
                b.total ??
                b.jumlah ??
                0
            )
            -
            Number(
                a.total ??
                a.jumlah ??
                0
            )
    )[0]

})


/*
|--------------------------------------------------------------------------
| AKTIVITAS
|--------------------------------------------------------------------------
*/

const recentActivityCount = computed(() =>
    props.recentVisits?.length ?? 0
)


/*
|--------------------------------------------------------------------------
| STATUS SISTEM
|--------------------------------------------------------------------------
*/

const systemStatus = computed(() => {

    if (
        props.stats &&
        Object.keys(
            props.stats
        ).length > 0
    ) {
        return 'Sistem Aktif'
    }

    return 'Menunggu Data'

})

</script>


<template>

<Head title="SiKes-Boarding | SMKN Jateng Semarang" />


<div class="min-h-screen bg-[#FDF1F7] text-slate-800 font-sans">


<!-- ================================================================== -->
<!-- HERO -->
<!-- ================================================================== -->

<section
    class="
        relative
        bg-gradient-to-br
        from-[#D4458A]
        via-[#C73E80]
        to-[#9A265E]
        text-white
        overflow-hidden
    "
>

    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] bg-white/10 rounded-full blur-[120px] pointer-events-none"
    ></div>

    <div
        class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-white/10 rounded-full blur-[100px] pointer-events-none"
    ></div>

    <div
        class="absolute inset-0 opacity-40 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:3rem_3rem] pointer-events-none"
    ></div>


    <!-- HEADER -->

    <header
        class="relative z-10 max-w-7xl mx-auto px-5 sm:px-6 py-5 sm:py-6 flex items-center justify-between"
    >

        <div class="flex items-center gap-3">

            <div
                class="bg-white/10 p-2.5 rounded-xl backdrop-blur-md border border-white/10"
            >

                <svg
                    class="w-6 h-6 text-pink-200"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >

                    <path
                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                    />

                    <path
                        d="M12 6v12M8 10h8"
                        stroke-width="1.5"
                    />

                </svg>

            </div>

            <div>

                <p
                    class="text-white text-xl sm:text-2xl font-bold tracking-tight leading-none"
                >
                    SMKN Jateng
                </p>

                <p
                    class="text-white/60 text-[9px] tracking-[0.2em] uppercase mt-1"
                >
                    Semarang
                </p>

            </div>

        </div>


        <div class="hidden md:block">

            <span
                class="text-white/90 font-bold text-xl lg:text-2xl tracking-wider"
            >
                SiKes-Boarding
            </span>

        </div>


        <Link
            :href="route('login')"
            class="px-5 sm:px-6 py-2 border border-white/80 text-white text-sm sm:text-base font-semibold rounded-xl hover:bg-white hover:text-[#D4458A] transition duration-300"
        >
            Login
        </Link>

    </header>


    <!-- HERO CONTENT -->

    <div
        class="relative z-10 max-w-7xl mx-auto px-5 sm:px-6 pt-12 sm:pt-16 pb-20"
    >

        <div class="text-center max-w-4xl mx-auto">

            <h1
                class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-tight"
            >
                Sistem Informasi Kesehatan &
                Kebugaran Siswa Boarding
            </h1>

            <p
                class="text-base sm:text-lg md:text-xl text-white/75 mt-6 max-w-2xl mx-auto leading-relaxed"
            >
                Pantau kesehatan dan kebugaran siswa secara
                terintegrasi melalui satu sistem informasi.
            </p>

        </div>


        <!-- SERVICE -->

        <div
            class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 max-w-6xl mx-auto mt-14"
        >

            <!-- KLINIK -->

            <Link
                :href="route('login', { role: 'klinik' })"
                class="group bg-white rounded-2xl shadow-xl p-6 sm:p-8 hover:-translate-y-1.5 hover:shadow-2xl transition duration-300 flex flex-col sm:flex-row gap-5 items-center sm:items-start"
            >

                <div
                    class="bg-[#FDF1F7] p-4 rounded-2xl flex-shrink-0 group-hover:scale-105 transition"
                >

                    <svg
                        class="w-11 h-11 text-[#D4458A]"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >

                        <path
                            d="M9 2h6v7h7v6h-7v7H9v-7H2V9h7V2z"
                        />

                    </svg>

                </div>


                <div class="text-center sm:text-left">

                    <h2
                        class="text-xl sm:text-2xl font-bold text-[#9A265E]"
                    >
                        Layanan Klinik & Kesehatan
                    </h2>

                    <p
                        class="text-slate-600 mt-3 text-sm sm:text-base leading-relaxed"
                    >
                        Kelola pemeriksaan berkala, kunjungan
                        klinik, dan riwayat kesehatan siswa.
                    </p>

                    <p
                        class="text-sm text-[#D4458A] font-bold mt-4"
                    >
                        Login sebagai Petugas Klinik →
                    </p>

                </div>

            </Link>


            <!-- TKSI -->

            <Link
                :href="route('login', { role: 'tksi' })"
                class="group bg-white rounded-2xl shadow-xl p-6 sm:p-8 hover:-translate-y-1.5 hover:shadow-2xl transition duration-300 flex flex-col sm:flex-row gap-5 items-center sm:items-start"
            >

                <div
                    class="bg-[#FDF1F7] p-4 rounded-2xl flex-shrink-0 group-hover:scale-105 transition"
                >

                    <svg
                        class="w-11 h-11 text-[#D4458A]"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >

                        <circle
                            cx="15"
                            cy="4"
                            r="2"
                        />

                        <path
                            d="M11 9l3 1.5 3-1M9 13l3.5-3v4l4 3.5M13 14l-2.5 4H8"
                        />

                    </svg>

                </div>


                <div class="text-center sm:text-left">

                    <h2
                        class="text-xl sm:text-2xl font-bold text-[#9A265E]"
                    >
                        Layanan Tes Kebugaran
                    </h2>

                    <p
                        class="text-slate-600 mt-3 text-sm sm:text-base leading-relaxed"
                    >
                        Catat dan pantau hasil tes kebugaran
                        fisik siswa secara terstruktur.
                    </p>

                    <p
                        class="text-sm text-[#D4458A] font-bold mt-4"
                    >
                        Login sebagai Petugas TKSI →
                    </p>

                </div>

            </Link>

        </div>

    </div>

</section>


<!-- WAVE -->

<div
    class="-mt-8 sm:-mt-12 relative z-10"
>

    <svg
        class="w-full h-10 sm:h-12"
        viewBox="0 0 1200 120"
        preserveAspectRatio="none"
    >

        <path
            d="M0,0 C300,120 900,120 1200,0 L1200,120 L0,120 Z"
            fill="#FDF1F7"
        />

    </svg>

</div>


<!-- ================================================================== -->
<!-- STATISTIK -->
<!-- ================================================================== -->

<section
    class="bg-[#FDF1F7] py-12 sm:py-16"
>

<div
    class="max-w-7xl mx-auto px-5 sm:px-6 space-y-8 sm:space-y-10"
>


    <!-- TITLE -->

    <div
        class="text-center max-w-3xl mx-auto"
    >

        <div
            class="inline-flex items-center gap-2 text-[#D4458A] text-xs font-bold uppercase tracking-wider mb-2"
        >

            <span
                class="w-8 h-px bg-[#D4458A]"
            ></span>

            DATA SISTEM

            <span
                class="w-8 h-px bg-[#D4458A]"
            ></span>

        </div>


        <h2
            class="text-2xl sm:text-3xl font-extrabold text-[#7D1E4D]"
        >
            Statistik Kesehatan & Kebugaran
        </h2>


        <p
            class="text-sm sm:text-base text-slate-500 mt-2"
        >
            Ringkasan data kesehatan dan kebugaran siswa
            berdasarkan periode aktif.
        </p>


        <div
            v-if="props.stats?.periodeAktif || props.stats?.namaPeriode"
            class="mt-3 inline-flex items-center gap-2 bg-[#FAD4E6] text-[#9A265E] px-3 py-1.5 rounded-lg text-xs font-bold"
        >

            Periode Aktif:

            {{ periodeAktif }}

        </div>

    </div>


    <!-- STAT CARD UTAMA -->

    <div
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"
    >


        <!-- TOTAL SISWA -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] border-t-4 border-t-[#D4458A] shadow-sm hover:shadow-md transition"
        >

            <div
                class="flex items-center justify-between"
            >

                <p
                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider"
                >
                    Siswa
                </p>

                <span class="text-xl">
                    👥
                </span>

            </div>


            <p
                class="text-2xl sm:text-3xl font-black text-[#7D1E4D] mt-3"
            >
                {{ totalSiswa }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa periode aktif
            </p>

        </div>


        <!-- BERKALA 1 -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] border-t-4 border-t-[#E45E9C] shadow-sm hover:shadow-md transition"
        >

            <div
                class="flex items-center justify-between"
            >

                <p
                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider"
                >
                    Berkala 1
                </p>

                <span class="text-xl">
                    🩺
                </span>

            </div>


            <p
                class="text-2xl sm:text-3xl font-black text-[#7D1E4D] mt-3"
            >
                {{ berkala1 }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa sudah diperiksa
            </p>

        </div>


        <!-- BERKALA 2 -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] border-t-4 border-t-[#EE7CAE] shadow-sm hover:shadow-md transition"
        >

            <div
                class="flex items-center justify-between"
            >

                <p
                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider"
                >
                    Berkala 2
                </p>

                <span class="text-xl">
                    🩺
                </span>

            </div>


            <p
                class="text-2xl sm:text-3xl font-black text-[#7D1E4D] mt-3"
            >
                {{ berkala2 }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa sudah diperiksa
            </p>

        </div>


        <!-- TKSI -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] border-t-4 border-t-[#C73E80] shadow-sm hover:shadow-md transition"
        >

            <div
                class="flex items-center justify-between"
            >

                <p
                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider"
                >
                    TKSI
                </p>

                <span class="text-xl">
                    🏃
                </span>

            </div>


            <p
                class="text-2xl sm:text-3xl font-black text-[#7D1E4D] mt-3"
            >
                {{ tksiSelesai }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa sudah tes
            </p>

        </div>


        <!-- KUNJUNGAN -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] border-t-4 border-t-[#B73574] shadow-sm hover:shadow-md transition"
        >

            <div
                class="flex items-center justify-between"
            >

                <p
                    class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-wider"
                >
                    Klinik
                </p>

                <span class="text-xl">
                    🏥
                </span>

            </div>


            <p
                class="text-2xl sm:text-3xl font-black text-[#7D1E4D] mt-3"
            >
                {{ totalKunjungan }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Total kunjungan klinik
            </p>

        </div>

    </div>


    <!-- RINGKASAN PEMERIKSAAN -->

    <div
        class="grid grid-cols-1 md:grid-cols-3 gap-4"
    >


        <!-- BERKALA 1 -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] shadow-sm"
        >

            <div
                class="flex items-center justify-between"
            >

                <div>

                    <p
                        class="text-xs font-bold text-[#D4458A] uppercase tracking-wider"
                    >
                        Pemeriksaan Berkala 1
                    </p>


                    <p
                        class="text-2xl font-black text-[#7D1E4D] mt-2"
                    >
                        {{ berkala1 }}
                        <span
                            class="text-sm font-semibold text-slate-400"
                        >
                            siswa
                        </span>
                    </p>


                    <p
                        class="text-[10px] text-slate-400 mt-1"
                    >
                        Siswa yang sudah menyelesaikan pemeriksaan tahap 1.
                    </p>

                </div>


                <div
                    class="w-11 h-11 rounded-xl bg-[#FDF1F7] flex items-center justify-center text-xl font-black text-[#D4458A]"
                >
                    1
                </div>

            </div>

        </div>


        <!-- BERKALA 2 -->

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] shadow-sm"
        >

            <div
                class="flex items-center justify-between"
            >

                <div>

                    <p
                        class="text-xs font-bold text-[#C73E80] uppercase tracking-wider"
                    >
                        Pemeriksaan Berkala 2
                    </p>


                    <p
                        class="text-2xl font-black text-[#7D1E4D] mt-2"
                    >
                        {{ berkala2 }}
                        <span
                            class="text-sm font-semibold text-slate-400"
                        >
                            siswa
                        </span>
                    </p>


                    <p
                        class="text-[10px] text-slate-400 mt-1"
                    >
                        Siswa yang sudah menyelesaikan pemeriksaan tahap 2.
                    </p>

                </div>


                <div
                    class="w-11 h-11 rounded-xl bg-[#FDF1F7] flex items-center justify-center text-xl font-black text-[#C73E80]"
                >
                    2
                </div>

            </div>

        </div>


        <!-- TKSI -->

        <div
            class="bg-gradient-to-br from-[#E45E9C] to-[#9A265E] rounded-2xl p-5 text-white shadow-sm"
        >

            <p
                class="text-xs font-bold uppercase tracking-wider text-white/80"
            >
                Tes TKSI Periode Aktif
            </p>


            <p
                class="text-3xl font-black mt-2"
            >
                {{ tksiSelesai }}
                <span
                    class="text-sm font-semibold text-white/70"
                >
                    siswa
                </span>
            </p>


            <p
                class="text-xs text-white/75 mt-1"
            >
                Siswa yang sudah menyelesaikan tes kebugaran.
            </p>

        </div>

    </div>


    <!-- KUNJUNGAN KLINIK -->

    <div
        class="grid grid-cols-1 md:grid-cols-3 gap-4"
    >

        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] shadow-sm"
        >

            <p
                class="text-xs font-bold text-[#D4458A] uppercase tracking-wider"
            >
                Total Kunjungan Klinik
            </p>


            <p
                class="text-3xl font-black text-[#7D1E4D] mt-2"
            >
                {{ totalKunjungan }}
            </p>


            <p
                class="text-xs text-slate-400 mt-1"
            >
                Seluruh kunjungan yang tercatat.
            </p>

        </div>


        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] shadow-sm"
        >

            <p
                class="text-xs font-bold text-[#C73E80] uppercase tracking-wider"
            >
                Kunjungan Bulan Ini
            </p>


            <p
                class="text-3xl font-black text-[#7D1E4D] mt-2"
            >
                {{ kunjunganBulanIni }}
            </p>


            <p
                class="text-xs text-slate-400 mt-1"
            >
                Kunjungan klinik pada bulan berjalan.
            </p>

        </div>


        <div
            class="bg-white rounded-2xl p-5 border border-[#F5A9C9] shadow-sm"
        >

            <p
                class="text-xs font-bold text-[#B73574] uppercase tracking-wider"
            >
                30 Hari Terakhir
            </p>


            <p
                class="text-3xl font-black text-[#7D1E4D] mt-2"
            >
                {{ totalKunjungan30Hari }}
            </p>


            <p
                class="text-xs text-slate-400 mt-1"
            >
                Aktivitas kunjungan klinik.
            </p>

        </div>

    </div>


    <!-- PROGRESS TKSI -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
        >

            <div>

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    🏃 Progress Tes TKSI
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Persentase siswa periode aktif yang sudah menyelesaikan tes TKSI.
                </p>

            </div>


            <div
                class="text-2xl font-black text-[#D4458A]"
            >
                {{ progressTKSI }}%
            </div>

        </div>


        <div
            class="mt-5 h-3 bg-[#FDF1F7] rounded-full overflow-hidden"
        >

            <div
                class="h-full bg-gradient-to-r from-[#F5A9C9] via-[#E45E9C] to-[#B73574] rounded-full transition-all duration-500"
                :style="{
                    width: `${progressTKSI}%`
                }"
            ></div>

        </div>


        <div
            class="flex justify-between mt-2 text-[10px] text-slate-400"
        >

            <span>
                {{ tksiSelesai }} siswa sudah tes
            </span>


            <span>
                {{ totalSiswa }} siswa total
            </span>

        </div>

    </div>


    <!-- STATUS KESEHATAN -->

    <div
        class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >

        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-[#D4458A] uppercase tracking-wider"
            >
                Sehat
            </p>


            <p
                class="text-2xl font-black text-[#D4458A] mt-2"
            >
                {{ sehat }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-[#C73E80] uppercase tracking-wider"
            >
                Perlu Perhatian
            </p>


            <p
                class="text-2xl font-black text-[#C73E80] mt-2"
            >
                {{ perluPerhatian }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-[#9A265E] uppercase tracking-wider"
            >
                Rujuk
            </p>


            <p
                class="text-2xl font-black text-[#9A265E] mt-2"
            >
                {{ rujuk }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-[#7D1E4D] uppercase tracking-wider"
            >
                Belum Diperiksa
            </p>


            <p
                class="text-2xl font-black text-[#7D1E4D] mt-2"
            >
                {{ belumDiperiksa }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                Siswa
            </p>

        </div>

    </div>


    <!-- PROGRESS KESEHATAN -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
        >

            <div>

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    📊 Progress Pemeriksaan Kesehatan
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Persentase siswa yang sudah memiliki status kesehatan
                    pada periode aktif.
                </p>

            </div>


            <div
                class="text-2xl font-black text-[#D4458A]"
            >
                {{ progressKesehatan }}%
            </div>

        </div>


        <div
            class="mt-5 h-3 bg-[#FDF1F7] rounded-full overflow-hidden"
        >

            <div
                class="h-full bg-gradient-to-r from-[#F5A9C9] via-[#E45E9C] to-[#B73574] rounded-full transition-all duration-500"
                :style="{
                    width: `${progressKesehatan}%`
                }"
            ></div>

        </div>


        <div
            class="flex justify-between mt-2 text-[10px] text-slate-400"
        >

            <span>
                {{ totalStatusKesehatan }} siswa sudah diperiksa
            </span>


            <span>
                {{ totalSiswa }} siswa total
            </span>

        </div>

    </div>


    <!-- SECONDARY STAT -->

    <div
        class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >

        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
            >
                Total Kelas
            </p>


            <p
                class="text-xl font-black text-[#7D1E4D] mt-2"
            >
                {{ totalKelas }}
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
            >
                Rata-rata Harian
            </p>


            <p
                class="text-xl font-black text-[#7D1E4D] mt-2"
            >
                {{ rataRataKunjungan }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                kunjungan / hari
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
            >
                Puncak Kunjungan
            </p>


            <p
                class="text-xl font-black text-[#7D1E4D] mt-2"
            >
                {{ puncakKunjungan }}
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                tertinggi dalam 30 hari
            </p>

        </div>


        <div
            class="bg-white rounded-xl p-4 border border-[#F5A9C9]"
        >

            <p
                class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
            >
                Tren 7 Hari
            </p>


            <p
                class="text-xl font-black text-[#D4458A] mt-2"
            >
                {{ tren7Hari >= 0 ? '+' : '' }}{{ tren7Hari }}%
            </p>


            <p
                class="text-[10px] text-slate-400 mt-1"
            >
                perubahan kunjungan
            </p>

        </div>

    </div>


    <!-- LINE CHART -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-[#FAD4E6] pb-4 mb-5"
        >

            <div>

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    📈 Aktivitas Kunjungan Klinik
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Pergerakan jumlah kunjungan dalam 30 hari terakhir.
                </p>

            </div>


            <div
                class="text-xs font-semibold text-[#D4458A] bg-[#FDF1F7] px-3 py-1.5 rounded-lg w-fit"
            >
                30 Hari Terakhir
            </div>

        </div>


        <div
            v-if="props.lineChart?.labels?.length"
            class="h-64 sm:h-80"
        >

            <Line
                :data="lineChartData"
                :options="lineChartOptions"
            />

        </div>


        <div
            v-else
            class="h-64 flex items-center justify-center text-sm text-slate-400"
        >
            Belum tersedia data kunjungan.
        </div>

    </div>


    <!-- HEALTH + DISEASE -->

    <div
        class="grid grid-cols-1 lg:grid-cols-2 gap-6"
    >


        <!-- STATUS KESEHATAN -->

        <div
            class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
        >

            <div
                class="border-b border-[#FAD4E6] pb-4 mb-4"
            >

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    🩺 Distribusi Status Kesehatan
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Menggunakan status pemeriksaan terbaru setiap siswa.
                </p>

            </div>


            <div
                v-if="
                    props.doughnutChart?.labels?.length &&
                    Number(props.stats?.totalStatusKesehatan ?? 0) > 0
                "
                class="h-72"
            >

                <Doughnut
                    :data="doughnutChartData"
                    :options="doughnutChartOptions"
                />

            </div>


            <div
                v-else
                class="h-72 flex items-center justify-center text-sm text-slate-400"
            >
                Belum tersedia data kesehatan.
            </div>


            <div
                class="grid grid-cols-3 gap-2 mt-4"
            >

                <div
                    class="text-center bg-[#FDF1F7] rounded-xl p-3"
                >

                    <p
                        class="text-lg font-black text-[#D4458A]"
                    >
                        {{ sehat }}
                    </p>

                    <p
                        class="text-[10px] text-[#9A265E]"
                    >
                        Sehat
                    </p>

                </div>


                <div
                    class="text-center bg-[#FAD4E6] rounded-xl p-3"
                >

                    <p
                        class="text-lg font-black text-[#C73E80]"
                    >
                        {{ perluPerhatian }}
                    </p>

                    <p
                        class="text-[10px] text-[#9A265E]"
                    >
                        Perhatian
                    </p>

                </div>


                <div
                    class="text-center bg-[#F5A9C9] rounded-xl p-3"
                >

                    <p
                        class="text-lg font-black text-[#7D1E4D]"
                    >
                        {{ rujuk }}
                    </p>

                    <p
                        class="text-[10px] text-[#7D1E4D]"
                    >
                        Rujuk
                    </p>

                </div>

            </div>

        </div>


        <!-- PENYAKIT -->

        <div
            class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
        >

            <div
                class="flex items-start justify-between gap-3 border-b border-[#FAD4E6] pb-4 mb-4"
            >

                <div>

                    <h3
                        class="text-lg font-bold text-[#7D1E4D]"
                    >
                        🦠 Penyakit yang Sering Ditemukan
                    </h3>


                    <p
                        class="text-xs text-slate-400 mt-1"
                    >
                        Ringkasan kasus berdasarkan data klinik.
                    </p>

                </div>


                <div
                    v-if="topDisease"
                    class="text-right"
                >

                    <p
                        class="text-[9px] text-slate-400 uppercase font-bold"
                    >
                        Terbanyak
                    </p>


                    <p
                        class="text-xs font-bold text-[#D4458A] mt-1"
                    >
                        {{
                            topDisease.nama ??
                            topDisease.name ??
                            '-'
                        }}
                    </p>

                </div>

            </div>


            <div
                v-if="props.diseaseChart?.labels?.length"
                class="h-72"
            >

                <Bar
                    :data="diseaseChartData"
                    :options="diseaseChartOptions"
                />

            </div>


            <div
                v-else
                class="h-72 flex items-center justify-center text-sm text-slate-400"
            >
                Belum tersedia data penyakit.
            </div>

        </div>

    </div>


    <!-- REKAP PENYAKIT -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="flex items-center justify-between border-b border-[#FAD4E6] pb-4 mb-4"
        >

            <div>

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    📋 Ringkasan Penyakit
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Daftar penyakit berdasarkan jumlah kasus.
                </p>

            </div>


            <span
                class="hidden sm:block text-xs font-semibold text-slate-400"
            >
                Data agregat
            </span>

        </div>


        <div class="overflow-x-auto">

            <table
                class="w-full min-w-[500px] text-sm"
            >

                <thead>

                    <tr
                        class="bg-[#FDF1F7] border-b border-[#F5A9C9] text-xs uppercase text-[#9A265E]"
                    >

                        <th class="px-4 py-3 text-left">
                            Penyakit
                        </th>

                        <th class="px-4 py-3 text-center">
                            Jumlah Kasus
                        </th>

                        <th class="px-4 py-3 text-center">
                            Persentase
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="(item, index) in props.diseases"
                        :key="item.id ?? index"
                        class="border-b border-[#FDF1F7] hover:bg-[#FDF1F7] transition"
                    >

                        <td
                            class="px-4 py-3 font-semibold text-slate-700"
                        >

                            <div
                                class="flex items-center gap-3"
                            >

                                <span
                                    class="w-7 h-7 rounded-lg bg-[#FDF1F7] text-[#D4458A] flex items-center justify-center text-xs font-bold"
                                >
                                    {{ index + 1 }}
                                </span>


                                {{
                                    item.nama ??
                                    item.name ??
                                    '-'
                                }}

                            </div>

                        </td>


                        <td
                            class="px-4 py-3 text-center font-bold text-slate-800"
                        >

                            {{
                                numberFormat(
                                    item.total ??
                                    item.jumlah ??
                                    0
                                )
                            }}

                        </td>


                        <td
                            class="px-4 py-3 text-center font-semibold text-[#D4458A]"
                        >

                            {{
                                percentage(
                                    item.percentage ??
                                    item.persentase ??
                                    0
                                )
                            }}

                        </td>

                    </tr>


                    <tr
                        v-if="props.diseases.length === 0"
                    >

                        <td
                            colspan="3"
                            class="py-10 text-center text-slate-400"
                        >
                            Belum ada data penyakit.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- REKAP KELAS -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="border-b border-[#FAD4E6] pb-4 mb-4"
        >

            <h3
                class="text-lg font-bold text-[#7D1E4D]"
            >
                📚 Rekapitulasi Kesehatan per Kelas
            </h3>


            <p
                class="text-xs text-slate-400 mt-1"
            >
                Setiap siswa hanya dihitung satu kali berdasarkan
                status pemeriksaan terbarunya.
            </p>

        </div>


        <div class="overflow-x-auto">

            <table
                class="w-full min-w-[900px] text-sm"
            >

                <thead>

                    <tr
                        class="bg-[#FDF1F7] border-b border-[#F5A9C9] text-xs uppercase text-[#9A265E]"
                    >

                        <th class="px-4 py-3 text-left">
                            Kelas
                        </th>

                        <th class="px-4 py-3 text-center">
                            Total Siswa
                        </th>

                        <th class="px-4 py-3 text-center">
                            Sehat
                        </th>

                        <th class="px-4 py-3 text-center">
                            Perhatian
                        </th>

                        <th class="px-4 py-3 text-center">
                            Rujuk
                        </th>

                        <th class="px-4 py-3 text-center">
                            Belum
                        </th>

                        <th class="px-4 py-3 text-center">
                            Progress
                        </th>

                        <th class="px-4 py-3 text-center">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="(row, index) in props.tableData"
                        :key="row.class ?? index"
                        class="border-b border-[#FDF1F7] hover:bg-[#FDF1F7] transition"
                    >

                        <td
                            class="px-4 py-3 font-bold text-[#9A265E]"
                        >
                            {{ row.class ?? '-' }}
                        </td>


                        <td
                            class="px-4 py-3 text-center font-bold"
                        >
                            {{ numberFormat(row.total) }}
                        </td>


                        <td
                            class="px-4 py-3 text-center text-[#D4458A] font-semibold"
                        >
                            {{ numberFormat(row.healthy) }}
                        </td>


                        <td
                            class="px-4 py-3 text-center text-[#C73E80] font-semibold"
                        >
                            {{ numberFormat(row.attention) }}
                        </td>


                        <td
                            class="px-4 py-3 text-center text-[#9A265E] font-semibold"
                        >
                            {{ numberFormat(row.refer) }}
                        </td>


                        <td
                            class="px-4 py-3 text-center text-slate-500 font-semibold"
                        >

                            {{
                                numberFormat(
                                    row.notExamined ??
                                    Math.max(
                                        0,
                                        Number(row.total ?? 0) -
                                        Number(row.examined ?? 0)
                                    )
                                )
                            }}

                        </td>


                        <td
                            class="px-4 py-3"
                        >

                            <div
                                class="flex items-center gap-2 justify-center"
                            >

                                <span
                                    class="text-xs font-semibold text-slate-600 w-12 text-right"
                                >
                                    {{ row.progress ?? 0 }}%
                                </span>


                                <div
                                    class="w-24 h-2.5 bg-[#FAD4E6] rounded-full overflow-hidden"
                                >

                                    <div
                                        class="h-full bg-gradient-to-r from-[#F5A9C9] to-[#D4458A] rounded-full transition-all"
                                        :style="{
                                            width: `${Math.min(
                                                100,
                                                Math.max(
                                                    0,
                                                    Number(row.progress ?? 0)
                                                )
                                            )}%`
                                        }"
                                    ></div>

                                </div>

                            </div>

                        </td>


                        <td
                            class="px-4 py-3 text-center"
                        >

                            <span
                                :class="[
                                    'px-2.5 py-1 rounded-full text-[10px] font-bold inline-block',
                                    row.badgeClass ??
                                    'bg-[#FDF1F7] text-[#9A265E]'
                                ]"
                            >
                                {{ row.status ?? '-' }}
                            </span>

                        </td>

                    </tr>


                    <tr
                        v-if="props.tableData.length === 0"
                    >

                        <td
                            colspan="8"
                            class="text-center py-10 text-slate-400"
                        >
                            Belum ada data kelas.
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- AKTIVITAS TERBARU -->

    <div
        class="bg-white rounded-2xl border border-[#F5A9C9] shadow-sm p-5 sm:p-6"
    >

        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#FAD4E6] pb-4"
        >

            <div>

                <h3
                    class="text-lg font-bold text-[#7D1E4D]"
                >
                    🏥 Aktivitas Klinik Terbaru
                </h3>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Informasi aktivitas ditampilkan secara anonim
                    untuk menjaga privasi siswa.
                </p>

            </div>


            <div
                class="bg-[#FDF1F7] text-[#D4458A] px-3 py-1.5 rounded-lg text-xs font-bold w-fit"
            >

                {{ numberFormat(recentActivityCount) }}
                aktivitas terbaru

            </div>

        </div>


        <div
            class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5"
        >

            <div
                class="rounded-xl bg-[#FDF1F7] border border-[#FAD4E6] p-4"
            >

                <p
                    class="text-[10px] font-bold text-[#9A265E] uppercase"
                >
                    Total Aktivitas
                </p>


                <p
                    class="text-2xl font-black text-[#7D1E4D] mt-2"
                >
                    {{ numberFormat(recentActivityCount) }}
                </p>

            </div>


            <div
                class="rounded-xl bg-[#FDF1F7] border border-[#FAD4E6] p-4"
            >

                <p
                    class="text-[10px] font-bold text-[#D4458A] uppercase"
                >
                    Status
                </p>


                <p
                    class="text-lg font-black text-[#B73574] mt-2"
                >
                    Tercatat
                </p>

            </div>


            <div
                class="rounded-xl bg-[#FDF1F7] border border-[#FAD4E6] p-4"
            >

                <p
                    class="text-[10px] font-bold text-[#D4458A] uppercase"
                >
                    Privasi
                </p>


                <p
                    class="text-lg font-black text-[#B73574] mt-2"
                >
                    Terlindungi
                </p>

            </div>

        </div>


        <div
            class="mt-5 bg-[#FDF1F7] border border-[#FAD4E6] rounded-xl p-4"
        >

            <div class="flex gap-3">

                <div
                    class="w-8 h-8 rounded-lg bg-[#FAD4E6] text-[#D4458A] flex items-center justify-center flex-shrink-0"
                >
                    🔒
                </div>


                <div>

                    <p
                        class="text-sm font-bold text-[#9A265E]"
                    >
                        Perlindungan Data Siswa
                    </p>


                    <p
                        class="text-xs text-slate-600 mt-1 leading-relaxed"
                    >
                        Data statistik publik hanya menampilkan
                        informasi agregat. Identitas, keluhan,
                        dan riwayat kesehatan individual siswa
                        hanya dapat diakses oleh petugas yang
                        memiliki hak akses setelah login.
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- INFO SYSTEM -->

    <div
        class="bg-gradient-to-r from-[#D4458A] via-[#C73E80] to-[#9A265E] rounded-2xl p-6 sm:p-8 text-white shadow-lg"
    >

        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-6"
        >

            <div>

                <div
                    class="flex items-center gap-2 mb-2"
                >

                    <span
                        class="w-2.5 h-2.5 bg-pink-200 rounded-full animate-pulse"
                    ></span>


                    <span
                        class="text-xs font-bold uppercase tracking-wider text-white/80"
                    >
                        Sistem Aktif
                    </span>

                </div>


                <h3
                    class="text-xl sm:text-2xl font-bold"
                >
                    SiKes-Boarding
                </h3>


                <p
                    class="text-sm text-white/75 mt-2 max-w-2xl leading-relaxed"
                >
                    Sistem informasi kesehatan dan kebugaran
                    siswa boarding untuk membantu pencatatan,
                    pemantauan, dan pengelolaan data kesehatan
                    secara terintegrasi.
                </p>

            </div>


            <Link
                :href="route('login')"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-[#B73574] rounded-xl font-bold text-sm hover:bg-[#FDF1F7] transition shadow-sm whitespace-nowrap"
            >
                Masuk ke Sistem →
            </Link>

        </div>

    </div>

</div>

</section>


<!-- ================================================================== -->
<!-- FOOTER -->
<!-- ================================================================== -->

<footer
    class="bg-white border-t border-[#F5A9C9] py-8"
>

    <div
        class="max-w-7xl mx-auto px-5 sm:px-6"
    >

        <div
            class="flex flex-col lg:flex-row justify-between gap-5"
        >

            <div>

                <p
                    class="font-bold text-[#7D1E4D]"
                >
                    SiKes-Boarding
                </p>


                <p
                    class="text-xs text-slate-400 mt-1"
                >
                    Sistem Informasi Kesehatan &
                    Kebugaran Siswa Boarding
                </p>

            </div>


            <div
                class="text-xs sm:text-sm text-slate-500"
            >

                <span
                    class="font-bold text-[#7D1E4D]"
                >
                    SMKN Jateng Semarang
                </span>


                <span class="mx-2 hidden sm:inline">
                    |
                </span>


                <span
                    class="block sm:inline mt-1 sm:mt-0"
                >
                    Jl. Brotojoyo No.1,
                    Semarang Utara,
                    Kota Semarang
                </span>

            </div>

        </div>


        <div
            class="border-t border-[#FAD4E6] mt-6 pt-5 flex flex-col sm:flex-row justify-between gap-2 text-xs text-slate-400"
        >

            <p>
                © 2026 SMKN Jateng Semarang.
                Seluruh Hak Cipta Dilindungi.
            </p>


            <p>
                Data publik ditampilkan dalam bentuk agregat
                untuk menjaga privasi siswa.
            </p>

        </div>

    </div>

</footer>


</div>

</template>
