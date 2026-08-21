<script setup>

import { computed, ref } from 'vue'

import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import { Link, router, usePage } from '@inertiajs/vue3'

import {
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    CalendarDaysIcon,
    PrinterIcon,
    ArrowDownTrayIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({

    kunjungan: {
        type: Object,
        default: () => ({
            data: [],
            meta: {},
            links: [],
        }),
    },

    statistik: {
        type: Object,
        default: () => ({
            total: 0,
            hari_ini: 0,
            selesai: 0,
            trend_penyakit: [],
            max_trend_penyakit: 0,
        }),
    },

    periodeList: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            periode_id: '',
            tanggal: '',
        }),
    },

})


// ======================================================
// PAGE
// ======================================================

const page = usePage()


// ======================================================
// STATE
// ======================================================

const search = ref(
    props.filters?.search ?? ''
)

const periodeId = ref(
    props.filters?.periode_id ?? ''
)

const tanggal = ref(
    props.filters?.tanggal ?? ''
)

const showFilter = ref(false)

const selectedKunjungan = ref(null)

const showDetail = ref(false)

const deleteTarget = ref(null)

const showDelete = ref(false)

const deleting = ref(false)


// ======================================================
// FLASH
// ======================================================

const flashSuccess = computed(() => {

    return page.props.flash?.success ?? null

})


const flashError = computed(() => {

    return page.props.flash?.error ?? null

})


// ======================================================
// ACTIVE FILTER
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        periodeId.value ||
        tanggal.value
    )

})


// ======================================================
// FILTER
// ======================================================

const applyFilter = () => {

    router.get(
        route('klinik.kesehatan.kunjungan.index'),
        {
            search: search.value || undefined,
            periode_id: periodeId.value || undefined,
            tanggal: tanggal.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )

}


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = ''

    periodeId.value = ''

    tanggal.value = ''

    applyFilter()

}


// ======================================================
// SEARCH
// ======================================================

let searchTimeout = null

const handleSearch = () => {

    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {

        applyFilter()

    }, 400)

}


// ======================================================
// TRUNCATE
// ======================================================

const truncate = (
    text,
    length = 50
) => {

    if (!text) {

        return '-'

    }

    if (text.length <= length) {

        return text

    }

    return `${text.substring(0, length)}...`

}


// ======================================================
// DETAIL
// ======================================================

const openDetail = (item) => {

    selectedKunjungan.value = item

    showDetail.value = true

}


const closeDetail = () => {

    showDetail.value = false

    selectedKunjungan.value = null

}


// ======================================================
// PRINT DETAIL
// ======================================================

const printDetail = () => {

    if (!selectedKunjungan.value?.id) {

        return

    }

    const url = route(
        'klinik.kesehatan.kunjungan.print',
        selectedKunjungan.value.id
    )

    window.open(
        url,
        '_blank',
        'width=900,height=700,noopener,noreferrer'
    )

}


// ======================================================
// DOWNLOAD PDF
// ======================================================

const downloadPdf = () => {

    if (!selectedKunjungan.value?.id) {

        return

    }

    const url = route(
        'klinik.kesehatan.kunjungan.pdf',
        selectedKunjungan.value.id
    )

    window.open(
        url,
        '_blank',
        'noopener,noreferrer'
    )

}


// ======================================================
// DELETE
// ======================================================

const openDelete = (item) => {

    deleteTarget.value = item

    showDelete.value = true

}


const closeDelete = () => {

    if (deleting.value) {

        return

    }

    showDelete.value = false

    deleteTarget.value = null

}


const deleteKunjungan = () => {

    if (
        !deleteTarget.value ||
        deleting.value
    ) {

        return

    }

    deleting.value = true

    router.delete(
        route(
            'klinik.kesehatan.kunjungan.destroy',
            deleteTarget.value.id
        ),
        {

            preserveScroll: true,

            onSuccess: () => {

                showDelete.value = false

                deleteTarget.value = null

            },

            onFinish: () => {

                deleting.value = false

            },

        }
    )

}


// ======================================================
// PAGINATION
// ======================================================

const paginationLinks = computed(() => {

    return props.kunjungan?.links ?? []

})


const goToPage = (url) => {

    if (!url) {

        return

    }

    router.get(
        url,
        {},
        {
            preserveState: true,
            preserveScroll: true,
        }
    )

}


// ======================================================
// CLEAR FLASH
// ======================================================

const clearFlash = () => {

    if (page.props.flash) {

        page.props.flash.success = null

        page.props.flash.error = null

    }

}


// ======================================================
// FORMAT TANGGAL
// ======================================================

const formatDateTime = (value) => {

    if (!value) {

        return '-'

    }

    const date = new Date(value)

    if (isNaN(date.getTime())) {

        return value

    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date)

}


const formatDate = (value) => {

    if (!value) {

        return '-'

    }

    const date = new Date(value)

    if (isNaN(date.getTime())) {

        return value

    }

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date)

}


// ======================================================
// TREN PENYAKIT
// ======================================================

const trendPenyakit = computed(() => {

    return props.statistik?.trend_penyakit ?? []

})


// ======================================================
// TOP 3 PENYAKIT
// ======================================================

const top3Penyakit = computed(() => {

    return [...trendPenyakit.value]
        .sort(
            (a, b) =>
                Number(b.jumlah ?? 0) -
                Number(a.jumlah ?? 0)
        )
        .slice(0, 3)

})


// ======================================================
// MAX BAR
// ======================================================

const maxPenyakit = computed(() => {

    const backendMax = Number(
        props.statistik?.max_trend_penyakit ?? 0
    )

    const localMax = Math.max(
        ...top3Penyakit.value.map(
            item => Number(item.jumlah ?? 0)
        ),
        0
    )

    return Math.max(
        backendMax,
        localMax,
        1
    )

})


// ======================================================
// BAR WIDTH
// ======================================================

const barWidth = (jumlah) => {

    const value = Number(jumlah ?? 0)

    if (!value) {

        return 0

    }

    return Math.max(
        8,
        (value / maxPenyakit.value) * 100
    )

}


// ======================================================
// TOTAL TOP 3
// ======================================================

const totalTop3Penyakit = computed(() => {

    return top3Penyakit.value.reduce(
        (total, penyakit) =>
            total + Number(penyakit.jumlah ?? 0),
        0
    )

})


// ======================================================
// PERSENTASE PENYAKIT
// ======================================================

const persentasePenyakit = (jumlah) => {

    if (!totalTop3Penyakit.value) {

        return 0

    }

    return (
        Number(jumlah ?? 0) /
        totalTop3Penyakit.value
    ) * 100

}


// ======================================================
// TOP PENYAKIT
// ======================================================

const topPenyakit = computed(() => {

    return top3Penyakit.value.length
        ? top3Penyakit.value[0]
        : null

})

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
                    class="mt-1 text-2xl font-bold text-slate-800"
                >
                    Kunjungan Klinik
                </h1>


                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Kelola dan pantau riwayat kunjungan
                    kesehatan siswa.
                </p>

            </div>


            <!-- KUNJUNGAN BARU -->

            <Link
                :href="route(
                    'klinik.kesehatan.kunjungan.create'
                )"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
            >

                <ClipboardDocumentCheckIcon
                    class="h-5 w-5"
                />

                Kunjungan Baru

            </Link>

        </div>


        <!-- ==================================================
             FLASH SUCCESS
        ================================================== -->

        <div
            v-if="flashSuccess"
            class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
        >

            <div
                class="flex items-center gap-2"
            >

                <CheckCircleIcon
                    class="h-5 w-5"
                />

                <span>
                    {{ flashSuccess }}
                </span>

            </div>


            <button
                type="button"
                @click="clearFlash"
                class="rounded-lg p-1 transition hover:bg-emerald-100"
            >

                <XMarkIcon
                    class="h-4 w-4"
                />

            </button>

        </div>


        <!-- ==================================================
             FLASH ERROR
        ================================================== -->

        <div
            v-if="flashError"
            class="flex items-start justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
        >

            <div
                class="flex items-start gap-3"
            >

                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100"
                >

                    <ExclamationTriangleIcon
                        class="h-4 w-4 text-rose-600"
                    />

                </div>


                <div>

                    <p
                        class="font-bold text-rose-800"
                    >
                        Terjadi kesalahan
                    </p>


                    <p
                        class="mt-0.5 text-xs text-rose-600"
                    >
                        {{ flashError }}
                    </p>

                </div>

            </div>


            <button
                type="button"
                @click="clearFlash"
                class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-100"
            >

                <XMarkIcon
                    class="h-4 w-4"
                />

            </button>

        </div>


        <!-- ==================================================
             STATISTIK
             3 CARD SAMA TINGGI
        ================================================== -->

        <div
            class="grid grid-cols-1 items-stretch gap-4 lg:grid-cols-3"
        >


            <!-- ==================================================
                 TOTAL KUNJUNGAN
            ================================================== -->

            <div
                class="flex min-h-[210px] flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex items-start justify-between"
                >

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Total Kunjungan
                        </p>

                        <p
                            class="mt-8 text-5xl  font-bold text-slate-800"
                        >
                            {{ statistik.total ?? 0 }}
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>

                </div>


                <!-- KONTEN PENUH -->

                <div
                    class="mt-auto border-t border-slate-100 pt-4"
                >

                    <p
                        class="text-xs font-medium text-slate-500"
                    >
                        Seluruh riwayat kunjungan
                    </p>

                    <div
                        class="mt-2 flex items-center justify-between"
                    >

                        <span
                            class="text-[11px] text-slate-400"
                        >
                            Data tercatat
                        </span>

                        <span
                            class="rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-600"
                        >
                            Semua Data
                        </span>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 KUNJUNGAN HARI INI
            ================================================== -->

            <div
                class="flex min-h-[210px] flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex items-start justify-between"
                >

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Kunjungan Hari Ini
                        </p>

                        <p
                            class="mt-8 text-5xl font-bold text-amber-600"
                        >
                            {{ statistik.hari_ini ?? 0 }}
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50"
                    >

                        <CalendarDaysIcon
                            class="h-5 w-5 text-amber-600"
                        />

                    </div>

                </div>


                <!-- KONTEN PENUH -->

                <div
                    class="mt-auto border-t border-slate-100 pt-4"
                >

                    <p
                        class="text-xs font-medium text-slate-500"
                    >
                        Kunjungan pada hari ini
                    </p>

                    <div
                        class="mt-2 flex items-center justify-between"
                    >

                        <span
                            class="text-[11px] text-slate-400"
                        >
                            Aktivitas klinik
                        </span>

                        <span
                            class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-600"
                        >
                            Hari Ini
                        </span>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 DISTRIBUSI PENYAKIT
            ================================================== -->

            <div
                class="flex min-h-[210px] flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <!-- HEADER -->

                <div
                    class="flex items-start justify-between"
                >

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Distribusi Penyakit
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-800"
                        >
                            3 Penyakit Terbanyak
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>

                </div>


                <!-- BAR CHART -->

                <div
                    v-if="top3Penyakit.length"
                    class="mt-4 flex-1 space-y-3"
                >

                    <div
                        v-for="(penyakit, index) in top3Penyakit"
                        :key="penyakit.id ?? index"
                    >

                        <!-- LABEL -->

                        <div
                            class="mb-1 flex items-center justify-between gap-3"
                        >

                            <div
                                class="flex min-w-0 items-center gap-2"
                            >

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full"
                                    :class="
                                        index === 0
                                            ? 'bg-blue-500'
                                            : index === 1
                                                ? 'bg-sky-400'
                                                : 'bg-slate-400'
                                    "
                                ></span>


                                <p
                                    class="truncate text-[11px] font-semibold text-slate-700"
                                >
                                    {{ penyakit.nama_penyakit }}
                                </p>

                            </div>


                            <span
                                class="shrink-0 text-[10px] font-bold text-slate-500"
                            >
                                {{ penyakit.jumlah ?? 0 }}
                            </span>

                        </div>


                        <!-- BAR -->

                        <div
                            class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100"
                        >

                            <div
                                class="h-full rounded-full transition-all duration-500"
                                :class="
                                    index === 0
                                        ? 'bg-blue-500'
                                        : index === 1
                                            ? 'bg-sky-400'
                                            : 'bg-slate-400'
                                "
                                :style="{
                                    width: `${barWidth(penyakit.jumlah)}%`
                                }"
                            ></div>

                        </div>

                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    v-else
                    class="mt-4 flex flex-1 items-center rounded-xl bg-slate-50 p-3"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-4 w-4 text-slate-300"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-semibold text-slate-500"
                            >
                                Belum ada data
                            </p>


                            <p
                                class="mt-0.5 text-[10px] text-slate-400"
                            >
                                Belum ada diagnosis penyakit.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div
                    v-if="top3Penyakit.length"
                    class="mt-auto border-t border-slate-100 pt-3"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <span
                            class="text-[10px] text-slate-400"
                        >
                            Total 3 penyakit
                        </span>

                        <span
                            class="text-xs font-bold text-slate-700"
                        >
                            {{ totalTop3Penyakit }} kasus
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FILTER
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="grid grid-cols-1 gap-3 p-4 lg:grid-cols-[3fr_1fr_auto]"
            >

                <!-- SEARCH -->

                <div
                    class="relative"
                >

                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />


                    <input
                        v-model="search"
                        @input="handleSearch"
                        type="text"
                        placeholder="Cari NISN, nama siswa, atau diagnosis..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />

                </div>


                <!-- PERIODE -->

                <select
                    v-model="periodeId"
                    @change="applyFilter"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                >

                    <option value="">
                        Semua Periode
                    </option>


                    <option
                        v-for="periode in periodeList"
                        :key="periode.id"
                        :value="periode.id"
                    >
                        {{ periode.nama_periode }}
                    </option>

                </select>


                <!-- FILTER -->

                <button
                    type="button"
                    @click="showFilter = !showFilter"
                    :class="[

                        'inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',

                        showFilter || tanggal
                            ? 'border-blue-200 bg-blue-50 text-blue-700'
                            : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'

                    ]"
                >

                    <FunnelIcon
                        class="h-4 w-4"
                    />

                    Filter

                </button>

            </div>


            <!-- FILTER TAMBAHAN -->

            <div
                v-if="showFilter"
                class="border-t border-slate-100 bg-slate-50/70 px-4 py-4"
            >

                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-end"
                >

                    <div
                        class="w-full max-w-xs"
                    >

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Kunjungan
                        </label>


                        <input
                            v-model="tanggal"
                            @change="applyFilter"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <button
                        v-if="hasActiveFilter"
                        type="button"
                        @click="resetFilter"
                        class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2.5 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >

                        <XMarkIcon
                            class="h-4 w-4"
                        />

                        Reset Filter

                    </button>

                </div>

            </div>

        </div>


        <!-- ==================================================
             TABLE
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <!-- HEADER -->

            <div
                class="flex flex-col gap-1 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <h2
                        class="text-sm font-bold text-slate-800"
                    >
                        Daftar Kunjungan
                    </h2>


                    <p
                        class="mt-0.5 text-xs text-slate-400"
                    >
                        Riwayat kunjungan siswa ke klinik.
                    </p>

                </div>


                <div
                    v-if="hasActiveFilter"
                    class="text-xs font-medium text-blue-600"
                >
                    Filter aktif
                </div>

            </div>


            <!-- ==================================================
                 DESKTOP
            ================================================== -->

            <div
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
                                class="w-14 px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                No
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama Siswa
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Kelas
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Keluhan
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Diagnosis
                            </th>


                            <th
                                class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Pemeriksa
                            </th>


                            <th
                                class="w-32 px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y divide-slate-100"
                    >

                        <tr
                            v-for="(item, index) in kunjungan.data"
                            :key="item.id"
                            class="transition hover:bg-slate-50"
                        >

                            <td
                                class="px-5 py-4 text-center text-sm text-slate-500"
                            >

                                {{
                                    ((kunjungan.meta?.current_page ?? 1) - 1)
                                    *
                                    (kunjungan.meta?.per_page ?? 10)
                                    +
                                    index + 1
                                }}

                            </td>


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
                                            item.siswa?.nama
                                                ?.charAt(0)
                                                ?.toUpperCase() ?? '?'
                                        }}

                                    </div>


                                    <div
                                        class="min-w-0"
                                    >

                                        <p
                                            class="truncate text-sm font-semibold text-slate-800"
                                        >
                                            {{ item.siswa?.nama ?? '-' }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            NISN:
                                            {{ item.siswa?.nisn ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <td
                                class="px-5 py-4"
                            >

                                <p
                                    class="text-sm font-medium text-slate-700"
                                >
                                    {{
                                        item.siswa?.kelas?.nama_kelas
                                        ?? '-'
                                    }}
                                </p>


                                <p
                                    v-if="item.siswa?.kelas?.jurusan?.nama_jurusan"
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    {{
                                        item.siswa.kelas.jurusan.nama_jurusan
                                    }}
                                </p>

                            </td>


                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <div
                                    class="flex items-center gap-2"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 text-slate-400"
                                    />


                                    <div>

                                        <p
                                            class="text-sm font-medium text-slate-700"
                                        >
                                            {{ formatDate(item.created_at) }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            Diperbarui:
                                            {{ formatDateTime(item.updated_at) }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <td
                                class="max-w-[220px] px-5 py-4"
                            >

                                <p
                                    class="text-sm text-slate-600"
                                >
                                    {{ truncate(item.keluhan) }}
                                </p>

                            </td>


                            <td
                                class="max-w-[220px] px-5 py-4"
                            >

                                <p
                                    class="text-sm font-medium text-slate-700"
                                >
                                    {{ item.penyakit?.nama_penyakit ?? '-' }}
                                </p>


                                <p
                                    v-if="item.penyakit?.kategori"
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    {{ item.penyakit.kategori }}
                                </p>

                            </td>


                            <td
                                class="px-5 py-4"
                            >

                                <div
                                    class="flex items-center gap-2"
                                >

                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                    >

                                        {{
                                            item.pemeriksa?.name
                                                ?.charAt(0)
                                                ?.toUpperCase() ?? '?'
                                        }}

                                    </div>


                                    <span
                                        class="text-sm font-medium text-slate-700"
                                    >
                                        {{
                                            item.pemeriksa?.name ?? '-'
                                        }}
                                    </span>

                                </div>

                            </td>


                            <td
                                class="px-5 py-4"
                            >

                                <div
                                    class="flex items-center justify-center gap-1"
                                >

                                    <button
                                        type="button"
                                        title="Lihat detail"
                                        @click="openDetail(item)"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    >

                                        <EyeIcon
                                            class="h-5 w-5"
                                        />

                                    </button>


                                    <Link
                                        :href="route(
                                            'klinik.kesehatan.kunjungan.edit',
                                            item.id
                                        )"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    >

                                        <PencilSquareIcon
                                            class="h-5 w-5"
                                        />

                                    </Link>


                                    <button
                                        type="button"
                                        title="Hapus"
                                        @click="openDelete(item)"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                    >

                                        <TrashIcon
                                            class="h-5 w-5"
                                        />

                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr
                            v-if="!kunjungan.data?.length"
                        >

                            <td
                                colspan="8"
                                class="px-5 py-16 text-center"
                            >

                                <div
                                    class="mx-auto flex max-w-sm flex-col items-center"
                                >

                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                    >

                                        <MagnifyingGlassIcon
                                            class="h-6 w-6 text-slate-400"
                                        />

                                    </div>


                                    <p
                                        class="text-sm font-bold text-slate-700"
                                    >
                                        Data kunjungan tidak ditemukan
                                    </p>


                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Coba ubah kata pencarian
                                        atau filter.
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
                    v-for="item in kunjungan.data"
                    :key="item.id"
                    class="p-4"
                >

                    <div
                        class="flex items-start justify-between gap-3"
                    >

                        <div
                            class="flex min-w-0 items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                            >

                                {{
                                    item.siswa?.nama
                                        ?.charAt(0)
                                        ?.toUpperCase() ?? '?'
                                }}

                            </div>


                            <div
                                class="min-w-0"
                            >

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ item.siswa?.nama ?? '-' }}
                                </p>


                                <p
                                    class="text-xs text-slate-400"
                                >
                                    NISN:
                                    {{ item.siswa?.nisn ?? '-' }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="mt-4 grid grid-cols-2 gap-3 text-xs"
                    >

                        <div>

                            <p
                                class="text-slate-400"
                            >
                                Kelas
                            </p>


                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >
                                {{
                                    item.siswa?.kelas?.nama_kelas
                                    ?? '-'
                                }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-slate-400"
                            >
                                Tanggal
                            </p>


                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >
                                {{ formatDate(item.created_at) }}
                            </p>


                            <p
                                class="mt-0.5 text-[11px] text-slate-400"
                            >
                                Diperbarui:
                                {{ formatDateTime(item.updated_at) }}
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-4 rounded-xl bg-slate-50 p-3"
                    >

                        <p
                            class="text-[10px] font-bold uppercase tracking-wide text-slate-400"
                        >
                            Keluhan
                        </p>


                        <p
                            class="mt-1 text-xs leading-5 text-slate-600"
                        >
                            {{ item.keluhan || '-' }}
                        </p>

                    </div>


                    <div
                        v-if="item.penyakit"
                        class="mt-3 rounded-xl bg-blue-50 p-3"
                    >

                        <p
                            class="text-[10px] font-bold uppercase tracking-wide text-blue-500"
                        >
                            Diagnosis
                        </p>


                        <p
                            class="mt-1 text-xs font-semibold text-blue-800"
                        >
                            {{ item.penyakit?.nama_penyakit ?? '-' }}
                        </p>


                        <p
                            v-if="item.penyakit?.kategori"
                            class="mt-1 text-[11px] text-blue-600"
                        >
                            {{ item.penyakit.kategori }}
                        </p>

                    </div>


                    <div
                        class="mt-4 flex gap-2"
                    >

                        <button
                            type="button"
                            @click="openDetail(item)"
                            class="flex-1 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50"
                        >

                            <span
                                class="flex items-center justify-center gap-1.5"
                            >

                                <EyeIcon
                                    class="h-4 w-4"
                                />

                                Detail

                            </span>

                        </button>


                        <Link
                            :href="route(
                                'klinik.kesehatan.kunjungan.edit',
                                item.id
                            )"
                            class="flex-1 rounded-xl bg-blue-600 px-3 py-2.5 text-center text-xs font-bold text-white transition hover:bg-blue-700"
                        >

                            <span
                                class="flex items-center justify-center gap-1.5"
                            >

                                <PencilSquareIcon
                                    class="h-4 w-4"
                                />

                                Edit

                            </span>

                        </Link>

                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    v-if="!kunjungan.data?.length"
                    class="px-5 py-16 text-center"
                >

                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                    >

                        <MagnifyingGlassIcon
                            class="h-6 w-6 text-slate-400"
                        />

                    </div>


                    <p
                        class="mt-3 text-sm font-bold text-slate-700"
                    >
                        Data kunjungan tidak ditemukan
                    </p>


                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Coba ubah kata pencarian atau filter.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 PAGINATION
            ================================================== -->

            <div
                v-if="kunjungan.data?.length"
                class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <p
                    class="text-xs text-slate-500"
                >

                    Menampilkan

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ kunjungan.from ?? 0 }}
                    </span>

                    -

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ kunjungan.to ?? 0 }}
                    </span>

                    dari

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ kunjungan.total ?? 0 }}
                    </span>

                    kunjungan

                </p>


                <div
                    class="flex items-center gap-1"
                >

                    <button
                        v-for="(link, index) in paginationLinks"
                        :key="index"
                        type="button"
                        :disabled="!link.url"
                        @click="goToPage(link.url)"
                        :class="[

                            'min-w-9 rounded-lg px-3 py-2 text-xs font-semibold transition',

                            link.active
                                ? 'bg-blue-600 text-white'
                                : link.url
                                    ? 'text-slate-600 hover:bg-slate-100'
                                    : 'cursor-not-allowed text-slate-300'

                        ]"
                        v-html="link.label"
                    ></button>

                </div>

            </div>

        </div>

    </div>


    <!-- ==================================================
         DETAIL MODAL
    ================================================== -->

    <Teleport to="body">

        <Transition name="fade">

            <div
                v-if="showDetail && selectedKunjungan"
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
            >

                <!-- OVERLAY -->

                <div
                    class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                    @click="closeDetail"
                ></div>


                <!-- MODAL -->

                <div
                    class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                >

                    <!-- HEADER -->

                    <div
                        class="flex shrink-0 items-start justify-between border-b border-slate-200 px-6 py-5"
                    >

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wider text-slate-400"
                            >
                                Detail Kunjungan
                            </p>


                            <h2
                                class="mt-1 text-xl font-bold text-slate-800"
                            >
                                {{
                                    selectedKunjungan.siswa?.nama
                                    ?? '-'
                                }}
                            </h2>


                            <p
                                class="mt-1 text-sm text-slate-500"
                            >

                                NISN:
                                {{
                                    selectedKunjungan.siswa?.nisn
                                    ?? '-'
                                }}

                                ·

                                {{
                                    selectedKunjungan.siswa?.kelas
                                        ?.nama_kelas ?? '-'
                                }}

                            </p>

                        </div>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        >

                            <XMarkIcon
                                class="h-5 w-5"
                            />

                        </button>

                    </div>


                    <!-- BODY -->

                    <div
                        class="min-h-0 flex-1 overflow-y-auto px-6 py-6"
                    >

                        <!-- META -->

                        <div
                            class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2"
                        >

                            <div
                                class="rounded-xl bg-slate-50 p-4"
                            >

                                <p
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Tanggal Kunjungan
                                </p>


                                <p
                                    class="mt-1 text-sm font-semibold text-slate-800"
                                >
                                    {{
                                        formatDate(
                                            selectedKunjungan.created_at
                                        )
                                    }}
                                </p>


                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    Diperbarui:
                                    {{
                                        formatDateTime(
                                            selectedKunjungan.updated_at
                                        )
                                    }}
                                </p>

                            </div>


                            <div
                                class="rounded-xl bg-slate-50 p-4"
                            >

                                <p
                                    class="text-xs font-medium text-slate-400"
                                >
                                    Pemeriksa
                                </p>


                                <p
                                    class="mt-1 text-sm font-semibold text-slate-800"
                                >
                                    {{
                                        selectedKunjungan.pemeriksa?.name
                                        ?? '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <!-- DETAIL -->

                        <div
                            class="space-y-5"
                        >

                            <!-- KELUHAN -->

                            <div>

                                <p
                                    class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Keluhan
                                </p>


                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-slate-700"
                                >
                                    {{
                                        selectedKunjungan.keluhan
                                        || '-'
                                    }}
                                </p>

                            </div>


                            <!-- PEMERIKSAAN -->

                            <div>

                                <p
                                    class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pemeriksaan
                                </p>


                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-slate-700"
                                >
                                    {{
                                        selectedKunjungan.pemeriksaan
                                        || '-'
                                    }}
                                </p>

                            </div>


                            <!-- DIAGNOSIS -->

                            <div>

                                <p
                                    class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Diagnosis
                                </p>


                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-slate-700"
                                >
                                    {{
                                        selectedKunjungan.penyakit?.nama_penyakit
                                        || '-'
                                    }}
                                </p>

                            </div>


                            <!-- TINDAKAN -->

                            <div>

                                <p
                                    class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Tindakan
                                </p>


                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-slate-700"
                                >
                                    {{
                                        selectedKunjungan.tindakan
                                        || '-'
                                    }}
                                </p>

                            </div>


                            <!-- OBAT -->

                            <div>

                                <p
                                    class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Obat yang Diberikan
                                </p>


                                <div
                                    v-if="selectedKunjungan.obat?.length"
                                    class="space-y-2"
                                >

                                    <div
                                        v-for="(obat, index) in selectedKunjungan.obat"
                                        :key="obat.id ?? index"
                                        class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                                    >

                                        <div
                                            class="flex items-start justify-between gap-4"
                                        >

                                            <div
                                                class="min-w-0"
                                            >

                                                <p
                                                    class="text-sm font-bold text-slate-800"
                                                >
                                                    {{ obat.nama_obat ?? '-' }}
                                                </p>


                                                <p
                                                    class="mt-1 text-xs text-slate-500"
                                                >

                                                    Jumlah:

                                                    <span
                                                        class="font-semibold text-slate-700"
                                                    >
                                                        {{ obat.jumlah ?? 0 }}
                                                        {{ obat.satuan ?? '' }}
                                                    </span>

                                                </p>

                                            </div>


                                            <div
                                                class="shrink-0 rounded-lg bg-white px-2.5 py-1.5 text-xs font-bold text-blue-600"
                                            >

                                                {{ obat.jumlah ?? 0 }}
                                                {{ obat.satuan ?? '' }}

                                            </div>

                                        </div>


                                        <div
                                            v-if="obat.keterangan"
                                            class="mt-3 border-t border-slate-200 pt-3"
                                        >

                                            <p
                                                class="text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                            >
                                                Keterangan
                                            </p>


                                            <p
                                                class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-600"
                                            >
                                                {{ obat.keterangan }}
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                <div
                                    v-else
                                    class="rounded-xl bg-slate-50 p-4"
                                >

                                    <p
                                        class="text-sm text-slate-400"
                                    >
                                        Tidak ada obat yang diberikan.
                                    </p>

                                </div>

                            </div>


                            <!-- CATATAN -->

                            <div>

                                <p
                                    class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Catatan
                                </p>


                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-slate-700"
                                >
                                    {{
                                        selectedKunjungan.catatan
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- FOOTER -->

                    <div
                        class="relative z-20 flex shrink-0 flex-col gap-2 border-t border-slate-200 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div
                            class="flex flex-wrap gap-2"
                        >

                            <!-- PRINT -->

                            <button
                                type="button"
                                @click.stop="printDetail"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800"
                            >

                                <PrinterIcon
                                    class="h-4 w-4"
                                />

                                Print

                            </button>


                            <!-- PDF -->

                            <button
                                type="button"
                                @click.stop="downloadPdf"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                            >

                                <ArrowDownTrayIcon
                                    class="h-4 w-4"
                                />

                                Unduh PDF

                            </button>

                        </div>


                        <div
                            class="flex gap-2"
                        >

                            <button
                                type="button"
                                @click="closeDetail"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >
                                Tutup
                            </button>


                            <Link
                                :href="route(
                                    'klinik.kesehatan.kunjungan.edit',
                                    selectedKunjungan.id
                                )"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                Edit Data
                            </Link>

                        </div>

                    </div>

                </div>

            </div>

        </Transition>

    </Teleport>


    <!-- ==================================================
         DELETE MODAL
    ================================================== -->

    <Teleport to="body">

        <Transition name="fade">

            <div
                v-if="showDelete && deleteTarget"
                class="fixed inset-0 z-[10000] flex items-center justify-center p-4"
            >

                <!-- OVERLAY -->

                <div
                    class="absolute inset-0 z-0 bg-slate-900/50 backdrop-blur-sm"
                    @click="closeDelete"
                ></div>


                <!-- MODAL -->

                <div
                    class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
                >

                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-50"
                    >

                        <ExclamationTriangleIcon
                            class="h-6 w-6 text-rose-600"
                        />

                    </div>


                    <h3
                        class="mt-4 text-lg font-bold text-slate-800"
                    >
                        Hapus Kunjungan?
                    </h3>


                    <p
                        class="mt-2 text-sm leading-6 text-slate-500"
                    >

                        Data kunjungan

                        <span
                            class="font-semibold text-slate-700"
                        >
                            {{
                                deleteTarget.siswa?.nama ?? '-'
                            }}
                        </span>

                        akan dihapus secara permanen.

                    </p>


                    <div
                        class="mt-6 flex justify-end gap-2"
                    >

                        <button
                            type="button"
                            :disabled="deleting"
                            @click="closeDelete"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50"
                        >
                            Batal
                        </button>


                        <button
                            type="button"
                            :disabled="deleting"
                            @click="deleteKunjungan"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >

                            <svg
                                v-if="deleting"
                                class="h-4 w-4 animate-spin"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                            >

                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-width="3"
                                    class="opacity-30"
                                />


                                <path
                                    d="M21 12a9 9 0 00-9-9"
                                    stroke-width="3"
                                />

                            </svg>


                            {{
                                deleting
                                    ? 'Menghapus...'
                                    : 'Hapus'
                            }}

                        </button>

                    </div>

                </div>

            </div>

        </Transition>

    </Teleport>

</KlinikLayout>

</template>


<style scoped>

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

</style>