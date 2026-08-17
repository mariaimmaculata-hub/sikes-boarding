<script setup>
import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue';

import { router } from '@inertiajs/vue3'

import {
    ClipboardDocumentListIcon,
    CalendarDaysIcon,
    ClockIcon,
    ArrowDownTrayIcon,
    XMarkIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline'

/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

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
        default: () => ({
            total_kunjungan: 0,
            total_siswa: 0,
            selesai: 0,
            rujuk: 0,
        }),
    },

    kelas: {
        type: Array,
        default: () => [],
    },

    filter: {
        type: Object,
        default: () => ({
            kelas_id: '',
        }),
    },
})

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const kelasId = ref(
    props.filter?.kelas_id
        ? String(props.filter.kelas_id)
        : ''
)

const search = ref('')

const selectedKunjungan = ref(null)

/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const totalKunjungan = computed(() => {
    return Number(
        props.statistik?.total_kunjungan ?? 0
    )
})

const totalSiswa = computed(() => {
    return Number(
        props.statistik?.total_siswa ?? 0
    )
})

const selesai = computed(() => {
    return Number(
        props.statistik?.selesai ?? 0
    )
})

const rujuk = computed(() => {
    return Number(
        props.statistik?.rujuk ?? 0
    )
})

const persentaseSelesai = computed(() => {
    if (!totalKunjungan.value) {
        return 0
    }

    return Math.round(
        (selesai.value / totalKunjungan.value) * 100
    )
})

const persentaseRujuk = computed(() => {
    if (!totalKunjungan.value) {
        return 0
    }

    return Math.round(
        (rujuk.value / totalKunjungan.value) * 100
    )
})

/*
|--------------------------------------------------------------------------
| SEARCH CLIENT SIDE
|--------------------------------------------------------------------------
*/

const filteredSiswa = computed(() => {
    if (!search.value.trim()) {
        return props.siswa
    }

    const keyword =
        search.value.toLowerCase().trim()

    return props.siswa.filter((item) => {
        const nama =
            item.siswa?.nama?.toLowerCase() ?? ''

        const nisn =
            String(
                item.siswa?.nisn ?? ''
            ).toLowerCase()

        const kelas =
            item.siswa?.kelas?.nama_kelas
                ?.toLowerCase() ?? ''

        const keluhan =
            item.keluhan
                ?.toLowerCase() ?? ''

        const diagnosis =
            item.diagnosis
                ?.toLowerCase() ?? ''

        return (
            nama.includes(keyword) ||
            nisn.includes(keyword) ||
            kelas.includes(keyword) ||
            keluhan.includes(keyword) ||
            diagnosis.includes(keyword)
        )
    })
})

/*
|--------------------------------------------------------------------------
| PERIODE
|--------------------------------------------------------------------------
*/

const changePeriode = (event) => {
    const value = event.target.value

    router.get(
        route('klinik.kesehatan.report.kunjungan'),
        {
            periode_id: value,
            kelas_id: kelasId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| FILTER KELAS
|--------------------------------------------------------------------------
*/

const applyFilter = () => {
    router.get(
        route('klinik.kesehatan.report.kunjungan'),
        {
            periode_id:
                props.periode?.id || undefined,

            kelas_id:
                kelasId.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

const resetFilter = () => {
    kelasId.value = ''
    search.value = ''

    router.get(
        route('klinik.kesehatan.report.kunjungan'),
        {
            periode_id:
                props.periode?.id || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusLabel = (status) => {
    switch (status) {
        case 'selesai':
            return 'Selesai'

        case 'rujuk':
            return 'Dirujuk'

        default:
            return status || '-'
    }
}

const statusClass = (status) => {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-50 text-emerald-700'

        case 'rujuk':
            return 'bg-amber-50 text-amber-700'

        default:
            return 'bg-slate-100 text-slate-600'
    }
}

/*
|--------------------------------------------------------------------------
| DATE
|--------------------------------------------------------------------------
*/

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return date
    }

    return parsedDate.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    )
}

const formatDateTime = (date) => {
    if (!date) {
        return '-'
    }

    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return date
    }

    return parsedDate.toLocaleString(
        'id-ID',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }
    )
}

/*
|--------------------------------------------------------------------------
| OPEN DETAIL
|--------------------------------------------------------------------------
*/

const openDetail = (item) => {
    selectedKunjungan.value = item
}

/*
|--------------------------------------------------------------------------
| CLOSE DETAIL
|--------------------------------------------------------------------------
*/

const closeDetail = () => {
    selectedKunjungan.value = null
}

/*
|--------------------------------------------------------------------------
| DOWNLOAD EXCEL
|--------------------------------------------------------------------------
*/

const downloadExcel = () => {
    const params = new URLSearchParams()

    if (props.periode?.id) {
        params.append(
            'periode_id',
            props.periode.id
        )
    }

    if (kelasId.value) {
        params.append(
            'kelas_id',
            kelasId.value
        )
    }

    window.location.href =
        route(
            'klinik.kesehatan.report.kunjungan.excel'
        ) +
        '?' +
        params.toString()
}

/*
|--------------------------------------------------------------------------
| DOWNLOAD PDF
|--------------------------------------------------------------------------
*/

const downloadPdf = () => {
    const params = new URLSearchParams()

    if (props.periode?.id) {
        params.append(
            'periode_id',
            props.periode.id
        )
    }

    if (kelasId.value) {
        params.append(
            'kelas_id',
            kelasId.value
        )
    }

    window.location.href =
        route(
            'klinik.kesehatan.report.kunjungan.pdf'
        ) +
        '?' +
        params.toString()
}

/*
|--------------------------------------------------------------------------
| DOWNLOAD DETAIL PDF
|--------------------------------------------------------------------------
*/

const downloadDetail = () => {
    if (
        !selectedKunjungan.value?.id
    ) {
        return
    }

    window.open(
        route(
            'klinik.kesehatan.report.kunjungan.detail-pdf',
            selectedKunjungan.value.id
        ),
        '_blank'
    )
}

/*
|--------------------------------------------------------------------------
| OBAT
|--------------------------------------------------------------------------
*/

const obatList = computed(() => {
    return (
        selectedKunjungan.value
            ?.kunjungan_obat ??
        selectedKunjungan.value
            ?.kunjunganObat ??
        []
    )
})
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
                    class="mb-2 flex items-center gap-2 text-xs font-semibold text-slate-400"
                >

                    <ClipboardDocumentListIcon
                        class="h-4 w-4"
                    />

                    <span>Kesehatan</span>


                </div>

                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Report Kunjungan Klinik
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Rekapitulasi kunjungan kesehatan siswa ke klinik berdasarkan periode.
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
                        class="min-w-[220px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option
                            v-for="item in periodeList"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.nama_periode }}

                            {{
                                item.status === 'aktif'
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

            <div
                class="flex items-start gap-4"
            >

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100"
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
                        Belum terdapat periode kesehatan yang tersedia.
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

                <div
                    class="flex items-center gap-3"
                >

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
                            Periode Kesehatan
                        </p>

                        <p
                            class="mt-0.5 font-bold text-blue-800"
                        >
                            {{ periode.nama_periode }}
                        </p>

                        <p
                            class="mt-0.5 text-xs text-blue-600"
                        >
                            {{ formatDate(periode.tanggal_mulai) }}
                            -
                            {{ formatDate(periode.tanggal_selesai) }}
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

                <!-- TOTAL KUNJUNGAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-start justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Total Kunjungan
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold text-slate-800"
                            >
                                {{ totalKunjungan }}
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                seluruh kunjungan
                            </p>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                        >

                            <ClipboardDocumentListIcon
                                class="h-5 w-5 text-blue-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- TOTAL SISWA -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-start justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Siswa Berkunjung
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold text-purple-600"
                            >
                                {{ totalSiswa }}
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                siswa
                            </p>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50"
                        >

                            <UserGroupIcon
                                class="h-5 w-5 text-purple-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- SELESAI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-start justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Selesai
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold text-emerald-600"
                            >
                                {{ selesai }}
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                {{ persentaseSelesai }}% dari kunjungan
                            </p>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
                        >

                            <CheckCircleIcon
                                class="h-5 w-5 text-emerald-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- RUJUK -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-start justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Dirujuk
                            </p>

                            <p
                                class="mt-2 text-3xl font-bold text-amber-600"
                            >
                                {{ rujuk }}
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                {{ persentaseRujuk }}% dari kunjungan
                            </p>

                        </div>

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50"
                        >

                            <ExclamationTriangleIcon
                                class="h-5 w-5 text-amber-600"
                            />

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 FILTER
            ================================================== -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3"
                >

                    <!-- KELAS -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-600"
                        >
                            Kelas
                        </label>

                        <select
                            v-model="kelasId"
                            @change="applyFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
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


                    <!-- SEARCH -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-semibold text-slate-600"
                        >
                            Cari Siswa
                        </label>

                        <div
                            class="relative"
                        >

                            <MagnifyingGlassIcon
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Nama, NISN, kelas..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                            />

                        </div>

                    </div>


                    <!-- RESET -->

                    <div
                        class="flex items-end"
                    >

                        <button
                            type="button"
                            @click="resetFilter"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >

                            <ArrowPathIcon
                                class="h-4 w-4"
                            />

                            Reset Filter

                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- TABLE HEADER -->

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Daftar Kunjungan Klinik
                        </h2>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Rekap kunjungan siswa pada periode yang dipilih.
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
                    v-if="!filteredSiswa.length"
                    class="px-6 py-14 text-center"
                >

                    <ClipboardDocumentListIcon
                        class="mx-auto h-10 w-10 text-slate-300"
                    />

                    <p
                        class="mt-3 text-sm font-semibold text-slate-600"
                    >
                        Tidak ada kunjungan
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Belum terdapat data kunjungan pada filter yang dipilih.
                    </p>

                </div>


                <!-- TABLE -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table
                        class="w-full min-w-[1250px] text-left"
                    >

                        <thead
                            class="border-b border-slate-100 bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="px-6 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Siswa
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Kelas
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Tanggal
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Keluhan
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Diagnosis
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-4 py-3 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pemeriksa
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="item in filteredSiswa"
                                :key="item.id"
                                class="transition hover:bg-slate-50/70"
                            >

                                <!-- SISWA -->

                                <td
                                    class="px-6 py-4"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{
                                            item.siswa?.nama ||
                                            '-'
                                        }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        NISN:
                                        {{
                                            item.siswa?.nisn ||
                                            '-'
                                        }}
                                    </p>

                                </td>


                                <!-- KELAS -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{
                                            item.siswa?.kelas
                                                ?.nama_kelas ||
                                            '-'
                                        }}
                                    </p>

                                    <p
                                        v-if="
                                            item.siswa?.jurusan
                                        "
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        {{
                                            item.siswa.jurusan
                                                .nama_jurusan
                                        }}
                                    </p>

                                </td>


                                <!-- TANGGAL -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{
                                            formatDate(
                                                item.tanggal_kunjungan
                                            )
                                        }}
                                    </p>

                                    <p
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        {{
                                            formatDateTime(
                                                item.tanggal_kunjungan
                                            )
                                                .split(',')
                                                .slice(-1)
                                                .join('')
                                                .trim()
                                        }}
                                    </p>

                                </td>


                                <!-- KELUHAN -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <p
                                        class="max-w-[180px] truncate text-sm text-slate-600"
                                        :title="
                                            item.keluhan ||
                                            '-'
                                        "
                                    >
                                        {{
                                            item.keluhan ||
                                            '-'
                                        }}
                                    </p>

                                </td>


                                <!-- DIAGNOSIS -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <p
                                        class="max-w-[180px] truncate text-sm text-slate-600"
                                        :title="
                                            item.diagnosis ||
                                            '-'
                                        "
                                    >
                                        {{
                                            item.diagnosis ||
                                            '-'
                                        }}
                                    </p>

                                </td>


                                <!-- STATUS -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                            statusClass(
                                                item.status
                                            )
                                        ]"
                                    >
                                        {{
                                            statusLabel(
                                                item.status
                                            )
                                        }}
                                    </span>

                                </td>


                                <!-- PEMERIKSA -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <p
                                        class="text-sm font-medium text-slate-600"
                                    >
                                        {{
                                            item.pemeriksa
                                                ?.name ||
                                            item.pemeriksa
                                                ?.nama ||
                                            '-'
                                        }}
                                    </p>

                                </td>


                                <!-- AKSI -->

                                <td
                                    class="px-6 py-4 text-right"
                                >

                                    <button
                                        type="button"
                                        @click="
                                            openDetail(item)
                                        "
                                        class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                                    >
                                        Detail
                                    </button>

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
                v-if="selectedKunjungan"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="closeDetail"
            >

                <div
                    class="max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl"
                >

                    <!-- HEADER -->

                    <div
                        class="flex items-start justify-between border-b border-slate-100 px-6 py-5"
                    >

                        <div>

                            <div
                                class="flex items-center gap-2"
                            >

                                <span
                                    class="text-xs font-bold uppercase tracking-wide text-blue-500"
                                >
                                    Kunjungan Klinik
                                </span>

                                <span
                                    :class="[
                                        'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                        statusClass(
                                            selectedKunjungan.status
                                        )
                                    ]"
                                >
                                    {{
                                        statusLabel(
                                            selectedKunjungan.status
                                        )
                                    }}
                                </span>

                            </div>

                            <h2
                                class="mt-2 text-lg font-bold text-slate-800"
                            >
                                {{
                                    selectedKunjungan.siswa
                                        ?.nama ||
                                    '-'
                                }}
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                NISN:
                                {{
                                    selectedKunjungan.siswa
                                        ?.nisn ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100"
                        >

                            <XMarkIcon
                                class="h-5 w-5"
                            />

                        </button>

                    </div>


                    <!-- BODY -->

                    <div
                        class="max-h-[calc(90vh-150px)] overflow-y-auto p-6"
                    >

                        <!-- INFORMASI SISWA -->

                        <div
                            class="mb-6 grid grid-cols-1 gap-3 sm:grid-cols-3"
                        >

                            <div
                                class="rounded-xl bg-slate-50 p-4"
                            >

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Kelas
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-slate-700"
                                >
                                    {{
                                        selectedKunjungan
                                            .siswa
                                            ?.kelas
                                            ?.nama_kelas ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <div
                                class="rounded-xl bg-slate-50 p-4"
                            >

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Jurusan
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-slate-700"
                                >
                                    {{
                                        selectedKunjungan
                                            .siswa
                                            ?.jurusan
                                            ?.nama_jurusan ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <div
                                class="rounded-xl bg-slate-50 p-4"
                            >

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Tanggal Kunjungan
                                </p>

                                <p
                                    class="mt-1 text-sm font-bold text-slate-700"
                                >
                                    {{
                                        formatDate(
                                            selectedKunjungan
                                                .tanggal_kunjungan
                                        )
                                    }}
                                </p>

                            </div>

                        </div>


                        <!-- PEMERIKSA -->

                        <div
                            class="mb-6 rounded-xl border border-slate-100 p-4"
                        >

                            <p
                                class="text-xs text-slate-400"
                            >
                                Pemeriksa
                            </p>

                            <p
                                class="mt-1 text-sm font-semibold text-slate-700"
                            >
                                {{
                                    selectedKunjungan
                                        .pemeriksa
                                        ?.name ||
                                    selectedKunjungan
                                        .pemeriksa
                                        ?.nama ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <!-- KELUHAN -->

                        <h3
                            class="mb-3 text-sm font-bold text-slate-800"
                        >
                            Keluhan
                        </h3>

                        <div
                            class="mb-6 rounded-xl bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{
                                    selectedKunjungan
                                        .keluhan ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <!-- PEMERIKSAAN -->

                        <h3
                            class="mb-3 text-sm font-bold text-slate-800"
                        >
                            Pemeriksaan
                        </h3>

                        <div
                            class="mb-6 rounded-xl bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{
                                    selectedKunjungan
                                        .pemeriksaan ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <!-- DIAGNOSIS -->

                        <h3
                            class="mb-3 text-sm font-bold text-slate-800"
                        >
                            Diagnosis
                        </h3>

                        <div
                            class="mb-6 rounded-xl bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{
                                    selectedKunjungan
                                        .diagnosis ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <!-- TINDAKAN -->

                        <h3
                            class="mb-3 text-sm font-bold text-slate-800"
                        >
                            Tindakan
                        </h3>

                        <div
                            class="mb-6 rounded-xl bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{
                                    selectedKunjungan
                                        .tindakan ||
                                    '-'
                                }}
                            </p>

                        </div>


                        <!-- OBAT -->

                        <template
                            v-if="obatList.length"
                        >

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Obat
                            </h3>

                            <div
                                class="mb-6 overflow-hidden rounded-xl border border-slate-100"
                            >

                                <table
                                    class="w-full text-left"
                                >

                                    <thead
                                        class="bg-slate-50"
                                    >

                                        <tr>

                                            <th
                                                class="px-4 py-3 text-xs font-bold text-slate-500"
                                            >
                                                Nama Obat
                                            </th>

                                            <th
                                                class="px-4 py-3 text-center text-xs font-bold text-slate-500"
                                            >
                                                Jumlah
                                            </th>

                                            <th
                                                class="px-4 py-3 text-xs font-bold text-slate-500"
                                            >
                                                Keterangan
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody
                                        class="divide-y divide-slate-100"
                                    >

                                        <tr
                                            v-for="obat in obatList"
                                            :key="obat.id"
                                        >

                                            <td
                                                class="px-4 py-3 text-sm font-semibold text-slate-700"
                                            >
                                                {{
                                                    obat.obat
                                                        ?.nama_obat ||
                                                    '-'
                                                }}
                                            </td>

                                            <td
                                                class="px-4 py-3 text-center text-sm text-slate-600"
                                            >
                                                {{
                                                    obat.jumlah ??
                                                    '-'
                                                }}

                                                {{
                                                    obat.obat
                                                        ?.satuan ||
                                                    ''
                                                }}
                                            </td>

                                            <td
                                                class="px-4 py-3 text-sm text-slate-600"
                                            >
                                                {{
                                                    obat.keterangan ||
                                                    '-'
                                                }}
                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        </template>


                        <!-- CATATAN -->

                        <template
                            v-if="
                                selectedKunjungan
                                    .catatan
                            "
                        >

                            <h3
                                class="mb-3 text-sm font-bold text-slate-800"
                            >
                                Catatan
                            </h3>

                            <div
                                class="mb-2 rounded-xl bg-amber-50 p-4"
                            >

                                <p
                                    class="whitespace-pre-line text-sm leading-6 text-amber-800"
                                >
                                    {{
                                        selectedKunjungan
                                            .catatan
                                    }}
                                </p>

                            </div>

                        </template>

                    </div>


                    <!-- FOOTER -->

                    <div
                        class="flex justify-between border-t border-slate-100 px-6 py-4"
                    >

                        <button
                            type="button"
                            @click="downloadDetail"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh PDF

                        </button>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
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