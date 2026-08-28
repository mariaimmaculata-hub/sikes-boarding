<script setup>
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    UserGroupIcon,
    CheckCircleIcon,
    ClockIcon,
    EyeIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    CalendarDaysIcon,
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
// STATE FILTER
// ==================================================

const search = ref('')

const tingkatFilter = ref('')

const jurusanFilter = ref('')

const statusPemeriksaanFilter = ref('')

const statusKeseluruhanFilter = ref('')

const showFilter = ref(false)


// ==================================================
// DATA TINGKAT KELAS
// ==================================================

const tingkatKelas = computed(() => {

    const data = props.siswa
        .map(item => getTingkatKelas(item))
        .filter(Boolean)

    return [...new Set(data)]
        .sort((a, b) => {

            const angkaA = parseInt(a)
            const angkaB = parseInt(b)

            if (!isNaN(angkaA) && !isNaN(angkaB)) {
                return angkaA - angkaB
            }

            return a.localeCompare(b)
        })

})


// ==================================================
// DATA JURUSAN
// ==================================================

const jurusanList = computed(() => {

    const data = props.siswa
        .map(item => item.jurusan?.nama_jurusan)
        .filter(Boolean)

    return [...new Set(data)].sort()

})


// ==================================================
// AMBIL TINGKAT KELAS
// ==================================================

const getTingkatKelas = (item) => {

    const namaKelas =
        item.kelas?.nama_kelas ??
        item.kelas ??
        ''

    if (!namaKelas) {
        return '-'
    }

    const value = String(namaKelas).trim()

    // Contoh:
    // X TKR       -> X
    // XI TKR      -> XI
    // XII TKR     -> XII
    // X           -> X
    // 10 TKR      -> 10
    // 11 TKR      -> 11
    // 12 TKR      -> 12

    const match = value.match(
        /^(XII|XI|X|\d{1,2})\b/i
    )

    if (match) {
        return match[1].toUpperCase()
    }

    return value.split(' ')[0]
}


// ==================================================
// FILTER DATA
// ==================================================

const filteredSiswa = computed(() => {

    let data = [...props.siswa]


    // ==================================================
    // SEARCH NAMA / NISN
    // ==================================================

    if (search.value.trim()) {

        const keyword = search.value
            .toLowerCase()
            .trim()

        data = data.filter(item => {

            const nama =
                String(item.nama ?? '')
                    .toLowerCase()

            const nisn =
                String(item.nisn ?? '')
                    .toLowerCase()

            return (
                nama.includes(keyword) ||
                nisn.includes(keyword)
            )

        })

    }


    // ==================================================
    // FILTER TINGKAT KELAS
    // ==================================================

    if (tingkatFilter.value) {

        data = data.filter(item => {

            return (
                getTingkatKelas(item) ===
                tingkatFilter.value
            )

        })

    }


    // ==================================================
    // FILTER JURUSAN
    // ==================================================

    if (jurusanFilter.value) {

        data = data.filter(item => {

            return (
                String(
                    item.jurusan?.nama_jurusan ?? ''
                ) === jurusanFilter.value
            )

        })

    }


    // ==================================================
    // FILTER STATUS PEMERIKSAAN
    // ==================================================

    if (statusPemeriksaanFilter.value) {

        data = data.filter(item => {

            if (
                statusPemeriksaanFilter.value ===
                'berkala_1'
            ) {

                return (
                    item.berkala_1?.status ===
                    'selesai'
                )

            }


            if (
                statusPemeriksaanFilter.value ===
                'berkala_2'
            ) {

                return (
                    item.berkala_2?.status ===
                    'selesai'
                )

            }


            if (
                statusPemeriksaanFilter.value ===
                'tksi'
            ) {

                return (
                    item.tksi?.status ===
                    'selesai'
                )

            }


            return true

        })

    }


    // ==================================================
    // FILTER STATUS KESELURUHAN
    // ==================================================

    if (statusKeseluruhanFilter.value) {

        data = data.filter(item => {

            if (
                statusKeseluruhanFilter.value ===
                'lengkap'
            ) {

                return item.status === 'lengkap'

            }


            if (
                statusKeseluruhanFilter.value ===
                'belum'
            ) {

                return item.status !== 'lengkap'

            }


            return true

        })

    }


    return data

})


// ==================================================
// FILTER AKTIF
// ==================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        tingkatFilter.value ||
        jurusanFilter.value ||
        statusPemeriksaanFilter.value ||
        statusKeseluruhanFilter.value
    )

})


// ==================================================
// JUMLAH FILTER AKTIF
// ==================================================

const activeFilterCount = computed(() => {

    let count = 0

    if (tingkatFilter.value) {
        count++
    }

    if (jurusanFilter.value) {
        count++
    }

    if (statusPemeriksaanFilter.value) {
        count++
    }

    if (statusKeseluruhanFilter.value) {
        count++
    }

    return count

})


// ==================================================
// RESET FILTER
// ==================================================

const resetFilter = () => {

    search.value = ''

    tingkatFilter.value = ''

    jurusanFilter.value = ''

    statusPemeriksaanFilter.value = ''

    statusKeseluruhanFilter.value = ''

}


// ==================================================
// STATUS LABEL
// ==================================================

const statusLabel = (status) => {

    return status === 'selesai'
        ? 'Selesai'
        : 'Belum'

}


// ==================================================
// STATUS CLASS
// ==================================================

const statusClass = (status) => {

    return status === 'selesai'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-slate-200 bg-slate-50 text-slate-500'

}


// ==================================================
// STATUS KESELURUHAN
// ==================================================

const overallStatusClass = (status) => {

    return status === 'lengkap'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-amber-200 bg-amber-50 text-amber-700'

}


const overallStatusLabel = (status) => {

    return status === 'lengkap'
        ? 'Lengkap'
        : 'Belum Lengkap'

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

                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Siswa Periode Aktif
                </h1>

                <p
                    v-if="periode"
                    class="mt-1 text-sm text-slate-500"
                >

                    Periode:

                    <span
                        class="font-semibold text-slate-700"
                    >
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
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50"
                        >

                            <UserGroupIcon
                                class="h-6 w-6 text-pink-600"
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
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50"
                        >

                            <CheckCircleIcon
                                class="h-6 w-6 text-pink-600"
                            />

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

                <!-- SEARCH + FILTER BUTTON -->

                <div
                    class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[1fr_auto]"
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
                            type="text"
                            placeholder="Cari siswa berdasarkan nama atau NISN..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />

                    </div>


                    <!-- BUTTON -->
<!-- BUTTON -->

<div
    class="flex w-full gap-2 sm:w-auto"
>

    <button
        type="button"
        @click="showFilter = !showFilter"
        :class="[

            'inline-flex h-11 min-w-[260px] items-center justify-center gap-2 rounded-xl border px-5 py-2.5 text-sm font-semibold transition',

            showFilter || activeFilterCount > 0
                ? 'border-pink-200 bg-pink-50 text-pink-700'
                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'

        ]"
    >

        <FunnelIcon
            class="h-4 w-4"
        />

        <span>
            Filter
        </span>

        <span
            v-if="activeFilterCount > 0"
            class="flex h-5 min-w-5 items-center justify-center rounded-full bg-pink-600 px-1.5 text-[10px] font-bold text-white"
        >
            {{ activeFilterCount }}
        </span>

    </button>


    <button
        v-if="hasActiveFilter"
        type="button"
        @click="resetFilter"
        class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
    >

        <XMarkIcon
            class="h-4 w-4"
        />

        Reset

    </button>

</div>

                </div>


                <!-- ==================================================
                     FILTER TAMBAHAN
                ================================================== -->

                <div
                    v-if="showFilter"
                    class="border-t border-slate-100 bg-slate-50/70 px-4 py-4"
                >

                    <div
                        class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"
                    >


                        <!-- TINGKAT KELAS -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tingkat Kelas
                            </label>

                            <select
                                v-model="tingkatFilter"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                            >

                                <option value="">
                                    Semua Tingkat
                                </option>

                                <option
                                    v-for="tingkat in tingkatKelas"
                                    :key="tingkat"
                                    :value="tingkat"
                                >
                                    {{ tingkat }}
                                </option>

                            </select>

                        </div>


                        <!-- JURUSAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Jurusan
                            </label>

                            <select
                                v-model="jurusanFilter"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                            >

                                <option value="">
                                    Semua Jurusan
                                </option>

                                <option
                                    v-for="jurusan in jurusanList"
                                    :key="jurusan"
                                    :value="jurusan"
                                >
                                    {{ jurusan }}
                                </option>

                            </select>

                        </div>


                        <!-- STATUS PEMERIKSAAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status Pemeriksaan
                            </label>

                            <select
                                v-model="statusPemeriksaanFilter"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                            >

                                <option value="">
                                    Semua
                                </option>

                                <option value="berkala_1">
                                    Berkala 1 selesai
                                </option>

                                <option value="berkala_2">
                                    Berkala 2 selesai
                                </option>

                                <option value="tksi">
                                    TKSI selesai
                                </option>

                            </select>

                        </div>


                        <!-- STATUS KESELURUHAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status Keseluruhan
                            </label>

                            <select
                                v-model="statusKeseluruhanFilter"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                            >

                                <option value="">
                                    Semua
                                </option>

                                <option value="lengkap">
                                    Lengkap
                                </option>

                                <option value="belum">
                                    Belum Lengkap
                                </option>

                            </select>

                        </div>

                    </div>


                    <!-- FILTER AKTIF -->

                    <div
                        v-if="activeFilterCount > 0"
                        class="mt-4 flex flex-wrap items-center gap-2"
                    >

                        <span
                            class="text-xs font-medium text-slate-400"
                        >
                            Filter aktif:
                        </span>


                        <!-- TINGKAT -->

                        <span
                            v-if="tingkatFilter"
                            class="rounded-full bg-pink-100 px-2.5 py-1 text-xs font-semibold text-pink-700"
                        >
                            Tingkat:
                            {{ tingkatFilter }}
                        </span>


                        <!-- JURUSAN -->

                        <span
                            v-if="jurusanFilter"
                            class="rounded-full bg-pink-100 px-2.5 py-1 text-xs font-semibold text-pink-700"
                        >
                            Jurusan:
                            {{ jurusanFilter }}
                        </span>


                        <!-- STATUS PEMERIKSAAN -->

                        <span
                            v-if="statusPemeriksaanFilter"
                            class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700"
                        >

                            {{
                                statusPemeriksaanFilter === 'berkala_1'
                                    ? 'Berkala 1 selesai'
                                    : statusPemeriksaanFilter === 'berkala_2'
                                        ? 'Berkala 2 selesai'
                                        : 'TKSI selesai'
                            }}

                        </span>


                        <!-- STATUS KESELURUHAN -->

                        <span
                            v-if="statusKeseluruhanFilter"
                            class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700"
                        >

                            {{
                                statusKeseluruhanFilter === 'lengkap'
                                    ? 'Lengkap'
                                    : 'Belum Lengkap'
                            }}

                        </span>

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
                    class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
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
                        Menampilkan
                        {{ filteredSiswa.length }}
                        dari
                        {{ siswa.length }}
                        siswa
                    </div>

                </div>


                <!-- ==================================================
                     DESKTOP
                ================================================== -->

                <div
                    class="hidden overflow-x-auto lg:block"
                >

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
                                v-for="(item, index) in filteredSiswa"
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
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-sm font-bold text-pink-700"
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
                                                NISN:
                                                {{ item.nisn || '-' }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- TINGKAT KELAS -->

                                <td
                                    class="whitespace-nowrap px-5 py-4"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ getTingkatKelas(item) }}
                                    </p>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        {{
                                            item.jurusan?.nama_jurusan
                                            ?? '-'
                                        }}
                                    </p>

                                </td>


                                <!-- BERKALA 1 -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-center"
                                >

                                    <span
                                        :class="statusClass(
                                            item.berkala_1?.status
                                        )"
                                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                    >

                                        <CheckCircleIcon
                                            v-if="
                                                item.berkala_1?.status ===
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
                                                item.berkala_1?.status
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
                                            item.berkala_2?.status
                                        )"
                                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                    >

                                        <CheckCircleIcon
                                            v-if="
                                                item.berkala_2?.status ===
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
                                                item.berkala_2?.status
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
                                            item.tksi?.status
                                        )"
                                        class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-bold"
                                    >

                                        <CheckCircleIcon
                                            v-if="
                                                item.tksi?.status ===
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
                                                item.tksi?.status
                                            )
                                        }}

                                    </span>

                                </td>


                                <!-- STATUS KESELURUHAN -->

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
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-pink-50 hover:text-pink-600"
                                    >

                                        <EyeIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-if="filteredSiswa.length === 0"
                            >

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

                                            <MagnifyingGlassIcon
                                                class="h-6 w-6 text-slate-400"
                                            />

                                        </div>

                                        <p
                                            class="mt-3 text-sm font-bold text-slate-700"
                                        >
                                            Data siswa tidak ditemukan
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            Coba ubah kata pencarian
                                            atau filter yang digunakan.
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
                        v-for="item in filteredSiswa"
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
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pink-100 font-bold text-pink-700"
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

                                        {{ getTingkatKelas(item) }}

                                        •

                                        {{
                                            item.jurusan?.nama_jurusan
                                            ?? '-'
                                        }}

                                    </p>

                                    <p
                                        class="text-[11px] text-slate-400"
                                    >
                                        NISN:
                                        {{ item.nisn || '-' }}
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

                            <!-- BERKALA 1 -->

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
                                        item.berkala_1?.status ===
                                        'selesai'
                                            ? 'text-emerald-600'
                                            : 'text-slate-400'
                                    "
                                    class="mt-1 text-xs font-bold"
                                >
                                    {{
                                        statusLabel(
                                            item.berkala_1?.status
                                        )
                                    }}
                                </p>

                            </div>


                            <!-- BERKALA 2 -->

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
                                        item.berkala_2?.status ===
                                        'selesai'
                                            ? 'text-emerald-600'
                                            : 'text-slate-400'
                                    "
                                    class="mt-1 text-xs font-bold"
                                >
                                    {{
                                        statusLabel(
                                            item.berkala_2?.status
                                        )
                                    }}
                                </p>

                            </div>


                            <!-- TKSI -->

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
                                        item.tksi?.status ===
                                        'selesai'
                                            ? 'text-emerald-600'
                                            : 'text-slate-400'
                                    "
                                    class="mt-1 text-xs font-bold"
                                >
                                    {{
                                        statusLabel(
                                            item.tksi?.status
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
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-pink-600 transition hover:bg-pink-50"
                            >

                                <EyeIcon
                                    class="h-4 w-4"
                                />

                                Detail

                            </Link>

                        </div>

                    </div>


                    <!-- MOBILE EMPTY -->

                    <div
                        v-if="filteredSiswa.length === 0"
                        class="px-5 py-16 text-center"
                    >

                        <p
                            class="text-sm font-bold text-slate-700"
                        >
                            Data siswa tidak ditemukan
                        </p>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Coba ubah pencarian atau filter.
                        </p>

                    </div>

                </div>

            </div>

        </template>

    </div>

</AdminLayout>

</template>