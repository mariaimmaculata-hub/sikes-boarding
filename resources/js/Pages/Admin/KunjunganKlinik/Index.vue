<script setup>
import { computed, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

import {
    MagnifyingGlassIcon,
    FunnelIcon,
    EyeIcon,
    HeartIcon,
    CheckCircleIcon,
    ClockIcon,
    UserIcon,
    CalendarDaysIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


// =====================================================
// PROPS
// =====================================================

const props = defineProps({
    kunjungan: {
        type: Object,
        required: true,
    },

    periodes: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            periode_id: '',
            status: '',
        }),
    },

    statistics: {
        type: Object,
        default: () => ({
            total: 0,
            selesai: 0,
            proses: 0,
        }),
    },
})


// =====================================================
// FILTER
// =====================================================

const search = ref(props.filters.search ?? '')
const periodeId = ref(props.filters.periode_id ?? '')
const status = ref(props.filters.status ?? '')


// =====================================================
// SEARCH DENGAN DEBOUNCE
// =====================================================

let searchTimeout = null

watch(search, () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        applyFilter()
    }, 400)
})


// =====================================================
// APPLY FILTER
// =====================================================

const applyFilter = () => {
    router.get(
        route('admin.kunjungan.index'),
        {
            search: search.value || undefined,
            periode_id: periodeId.value || undefined,
            status: status.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}


// =====================================================
// RESET FILTER
// =====================================================

const resetFilter = () => {
    search.value = ''
    periodeId.value = ''
    status.value = ''

    router.get(
        route('admin.kunjungan.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}


// =====================================================
// CEK FILTER AKTIF
// =====================================================

const hasFilter = computed(() => {
    return (
        search.value ||
        periodeId.value ||
        status.value
    )
})


// =====================================================
// FORMAT TANGGAL
// =====================================================

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    return new Date(tanggal).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}


// =====================================================
// STATUS LABEL
// =====================================================

const statusLabel = (value) => {
    if (!value) return '-'

    const labels = {
        selesai: 'Selesai',
        proses: 'Proses',
        menunggu: 'Menunggu',
        batal: 'Batal',
    }

    return labels[value.toLowerCase()] ?? value
}


// =====================================================
// STATUS CLASS
// =====================================================

const statusClass = (value) => {
    if (!value) {
        return 'bg-slate-100 text-slate-600'
    }

    switch (value.toLowerCase()) {

        case 'selesai':
            return 'bg-emerald-100 text-emerald-700'

        case 'proses':
            return 'bg-amber-100 text-amber-700'

        case 'menunggu':
            return 'bg-yellow-100 text-yellow-700'

        case 'batal':
            return 'bg-rose-100 text-rose-700'

        default:
            return 'bg-slate-100 text-slate-600'
    }
}


// =====================================================
// KELAS SISWA
// =====================================================

const namaKelas = (siswa) => {
    if (!siswa?.kelas) return '-'

    return siswa.kelas.nama_kelas ?? '-'
}
</script>


<template>

    <!-- ==================================================
         ADMIN LAYOUT
    ================================================== -->

    <AdminLayout>

        <div class="space-y-6">


            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >

                <div>

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-100"
                        >

                            <HeartIcon
                                class="h-6 w-6 text-pink-600"
                            />

                        </div>

                        <div>

                            <h1
                                class="text-2xl font-bold text-slate-800"
                            >
                                Kunjungan Klinik
                            </h1>

                            <p
                                class="mt-1 text-sm text-slate-500"
                            >
                                Rekap seluruh kunjungan kesehatan siswa.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 STATISTICS
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-3"
            >

                <!-- TOTAL -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:shadow-md"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-pink-600"
                            />

                        </div>

                        <div>

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Total Kunjungan
                            </p>

                            <p
                                class="text-xl font-bold text-slate-800"
                            >
                                {{ statistics.total }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- SELESAI -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:shadow-md"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <CheckCircleIcon
                                class="h-5 w-5 text-emerald-600"
                            />

                        </div>

                        <div>

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Selesai
                            </p>

                            <p
                                class="text-xl font-bold text-slate-800"
                            >
                                {{ statistics.selesai }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- PROSES -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:shadow-md"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                        >

                            <ClockIcon
                                class="h-5 w-5 text-amber-600"
                            />

                        </div>

                        <div>

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Dalam Proses
                            </p>

                            <p
                                class="text-xl font-bold text-slate-800"
                            >
                                {{ statistics.proses }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FILTER
            ================================================== -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm"
            >

                <div
                    class="mb-4 flex items-center gap-2"
                >

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-100"
                    >

                        <FunnelIcon
                            class="h-4 w-4 text-pink-600"
                        />

                    </div>

                    <h2
                        class="text-sm font-bold text-slate-700"
                    >
                        Filter Kunjungan
                    </h2>

                </div>


                <div
                    class="grid grid-cols-1 gap-3 md:grid-cols-4"
                >

                    <!-- SEARCH -->

                    <div class="relative md:col-span-2">

                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-pink-400"
                        />

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama atau NISN siswa..."
                            class="w-full rounded-xl border border-pink-100 bg-pink-50/40 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />

                    </div>


                    <!-- PERIODE -->

                    <select
                        v-model="periodeId"
                        @change="applyFilter"
                        class="rounded-xl border border-pink-100 bg-pink-50/40 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                    >

                        <option value="">
                            Semua Periode
                        </option>

                        <option
                            v-for="periode in periodes"
                            :key="periode.id"
                            :value="periode.id"
                        >
                            {{ periode.nama_periode }}
                        </option>

                    </select>


                    <!-- STATUS -->

                    <select
                        v-model="status"
                        @change="applyFilter"
                        class="rounded-xl border border-pink-100 bg-pink-50/40 px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="selesai">
                            Selesai
                        </option>

                        <option value="proses">
                            Proses
                        </option>

                        <option value="menunggu">
                            Menunggu
                        </option>

                        <option value="batal">
                            Batal
                        </option>

                    </select>

                </div>


                <!-- RESET -->

                <div
                    v-if="hasFilter"
                    class="mt-3"
                >

                    <button
                        type="button"
                        @click="resetFilter"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-500 transition hover:text-rose-700"
                    >

                        <XMarkIcon class="h-4 w-4" />

                        Reset Filter

                    </button>

                </div>

            </div>


            <!-- ==================================================
                 TABLE
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
            >

                <!-- TABLE HEADER -->

                <div
                    class="border-b border-pink-100 px-6 py-5"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-pink-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-pink-600"
                            />

                        </div>

                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Daftar Kunjungan Klinik
                            </h2>

                            <p
                                class="text-xs text-slate-400"
                            >
                                Data kunjungan kesehatan siswa.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TABLE -->

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1000px]">

                        <thead>

                            <tr
                                class="border-b border-pink-100 bg-pink-50/50"
                            >

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
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
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Periode
                                </th>

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Tanggal
                                </th>

                                <th
                                    class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Keluhan
                                </th>

                                <th
                                    class="px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Status
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
                                v-for="(item, index) in kunjungan.data"
                                :key="item.id"
                                class="border-b border-pink-50 transition hover:bg-pink-50/40"
                            >

                                <!-- NO -->

                                <td
                                    class="px-5 py-4 text-sm font-medium text-slate-500"
                                >

                                    {{
                                        (kunjungan.current_page - 1)
                                        * kunjungan.per_page
                                        + index
                                        + 1
                                    }}

                                </td>


                                <!-- SISWA -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pink-100"
                                        >

                                            <UserIcon
                                                class="h-4 w-4 text-pink-600"
                                            />

                                        </div>

                                        <div>

                                            <p
                                                class="text-sm font-bold text-slate-700"
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

                                </td>


                                <!-- KELAS -->

                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm font-medium text-slate-600"
                                    >
                                        {{ namaKelas(item.siswa) }}
                                    </span>

                                </td>


                                <!-- PERIODE -->

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-2">

                                        <CalendarDaysIcon
                                            class="h-4 w-4 text-pink-500"
                                        />

                                        <span
                                            class="text-sm font-medium text-slate-600"
                                        >
                                            {{
                                                item.periode?.nama_periode
                                                ?? '-'
                                            }}
                                        </span>

                                    </div>

                                </td>


                                <!-- TANGGAL -->

                                <td class="px-5 py-4">

                                    <span
                                        class="text-sm text-slate-600"
                                    >
                                        {{
                                            formatTanggal(
                                                item.tanggal_kunjungan
                                            )
                                        }}
                                    </span>

                                </td>


                                <!-- KELUHAN -->

                                <td class="max-w-[220px] px-5 py-4">

                                    <p
                                        class="truncate text-sm text-slate-600"
                                        :title="item.keluhan"
                                    >
                                        {{ item.keluhan ?? '-' }}
                                    </p>

                                </td>


                                <!-- STATUS -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-3 py-1 text-xs font-bold',
                                            statusClass(item.status)
                                        ]"
                                    >
                                        {{ statusLabel(item.status) }}
                                    </span>

                                </td>


                                <!-- AKSI -->

                                <td class="px-5 py-4 text-center">

                                    <Link
                                        :href="route(
                                            'admin.kunjungan.show',
                                            item.id
                                        )"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700 transition hover:bg-pink-100"
                                    >

                                        <EyeIcon class="h-4 w-4" />

                                        Detail

                                    </Link>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr v-if="!kunjungan.data.length">

                                <td
                                    colspan="8"
                                    class="px-6 py-14 text-center"
                                >

                                    <div
                                        class="mx-auto flex max-w-sm flex-col items-center"
                                    >

                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-pink-50"
                                        >

                                            <HeartIcon
                                                class="h-7 w-7 text-pink-300"
                                            />

                                        </div>

                                        <p
                                            class="mt-4 text-sm font-semibold text-slate-600"
                                        >
                                            Belum ada data kunjungan
                                        </p>

                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            Data kunjungan klinik akan muncul
                                            di halaman ini.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- ==================================================
                     PAGINATION
                ================================================== -->

                <div
                    v-if="kunjungan.last_page > 1"
                    class="flex flex-col gap-3 border-t border-pink-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <p class="text-xs text-slate-400">

                        Menampilkan

                        <span class="font-semibold text-slate-600">
                            {{ kunjungan.from }}
                        </span>

                        sampai

                        <span class="font-semibold text-slate-600">
                            {{ kunjungan.to }}
                        </span>

                        dari

                        <span class="font-semibold text-slate-600">
                            {{ kunjungan.total }}
                        </span>

                        kunjungan

                    </p>


                    <div class="flex items-center gap-1">

                        <Link
                            v-for="link in kunjungan.links"
                            :key="link.label"
                            :href="link.url ?? '#'"
                            v-html="link.label"
                            :class="[
                                'min-w-[36px] rounded-lg px-3 py-2 text-center text-xs font-semibold transition',

                                link.active
                                    ? 'bg-pink-600 text-white shadow-sm'

                                    : link.url
                                        ? 'bg-pink-50 text-pink-700 hover:bg-pink-100'

                                        : 'cursor-not-allowed bg-slate-50 text-slate-300'
                            ]"
                            preserve-scroll
                            preserve-state
                        />

                    </div>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>
