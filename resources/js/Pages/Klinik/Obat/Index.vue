<script setup>
import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    Link,
    usePage,
    router,
} from '@inertiajs/vue3'

import {
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    PlusIcon,
    PencilSquareIcon,
    TrashIcon,
    BeakerIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    obats: {
        type: Array,
        default: () => [],
    },
})


// ======================================================
// PAGE
// ======================================================

const page = usePage()


// ======================================================
// STATE
// ======================================================

const search = ref('')
const stokFilter = ref('')
const showFilter = ref(false)


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
// FILTER DATA
// ======================================================

const filteredObats = computed(() => {
    let data = props.obats ?? []

    // --------------------------------------------------
    // SEARCH
    // --------------------------------------------------

    if (search.value.trim()) {
        const keyword = search.value
            .toLowerCase()
            .trim()

        data = data.filter((obat) => {
            return (
                String(obat.nama_obat ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(obat.satuan ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(obat.keterangan ?? '')
                    .toLowerCase()
                    .includes(keyword)
            )
        })
    }


    // --------------------------------------------------
    // FILTER STOK
    // --------------------------------------------------

    if (stokFilter.value) {
        data = data.filter((obat) => {
            const stok = Number(obat.stok ?? 0)

            if (stokFilter.value === 'tersedia') {
                return stok > 10
            }

            if (stokFilter.value === 'menipis') {
                return stok > 0 && stok <= 10
            }

            if (stokFilter.value === 'habis') {
                return stok === 0
            }

            return true
        })
    }

    return data
})


// ======================================================
// SUMMARY
// ======================================================

const totalObat = computed(() => {
    return props.obats.length
})

const totalTersedia = computed(() => {
    return props.obats.filter(
        obat => Number(obat.stok ?? 0) > 10
    ).length
})

const totalMenipis = computed(() => {
    return props.obats.filter(obat => {
        const stok = Number(obat.stok ?? 0)

        return stok > 0 && stok <= 10
    }).length
})

const totalHabis = computed(() => {
    return props.obats.filter(
        obat => Number(obat.stok ?? 0) === 0
    ).length
})


// ======================================================
// STOK STATUS
// ======================================================

const getStokStatus = (obat) => {
    const stok = Number(obat.stok ?? 0)

    if (stok === 0) {
        return 'habis'
    }

    if (stok <= 10) {
        return 'menipis'
    }

    return 'tersedia'
}


const getStokLabel = (obat) => {
    const status = getStokStatus(obat)

    if (status === 'habis') {
        return 'Habis'
    }

    if (status === 'menipis') {
        return 'Menipis'
    }

    return 'Tersedia'
}


const getStokClass = (obat) => {
    const status = getStokStatus(obat)

    if (status === 'habis') {
        return 'bg-rose-100 text-rose-700 border border-rose-200'
    }

    if (status === 'menipis') {
        return 'bg-amber-100 text-amber-700 border border-amber-200'
    }

    return 'bg-emerald-100 text-emerald-700 border border-emerald-200'
}


// ======================================================
// FILTER
// ======================================================

const hasActiveFilter = computed(() => {
    return Boolean(
        search.value ||
        stokFilter.value
    )
})


const resetFilter = () => {
    search.value = ''
    stokFilter.value = ''
}


// ======================================================
// DELETE
// ======================================================

const deleteObat = (obat) => {
    const confirmed = window.confirm(
        `Apakah Anda yakin ingin menghapus obat "${obat.nama_obat}"?`
    )

    if (!confirmed) {
        return
    }

    router.delete(
        route('klinik.obat.destroy', obat.id),
        {
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

                    <!-- LABEL -->

                    <div
                        class="flex items-center gap-2 text-xs font-semibold text-pink-500"
                    >

                        <BeakerIcon
                            class="h-4 w-4"
                        />

                        Klinik

                    </div>


                    <!-- TITLE -->

                    <h1
                        class="mt-1 text-2xl font-bold text-slate-800"
                    >
                        Data Obat
                    </h1>


                    <!-- DESCRIPTION -->

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >
                        Kelola data obat dan stok obat klinik.
                    </p>

                </div>


                <!-- TAMBAH OBAT -->

                <Link
                    :href="route('klinik.obat.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-200"
                >

                    <PlusIcon
                        class="h-4 w-4"
                    />

                    Tambah Obat

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
                 SUMMARY
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
            >


                <!-- TOTAL OBAT -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:border-pink-200 hover:shadow-md"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Total Obat
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ totalObat }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50"
                        >

                            <BeakerIcon
                                class="h-5 w-5 text-pink-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- STOK TERSEDIA -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:border-emerald-200 hover:shadow-md"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Stok Tersedia
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-emerald-600"
                            >
                                {{ totalTersedia }}
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


                <!-- STOK MENIPIS -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:border-amber-200 hover:shadow-md"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Stok Menipis
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-amber-600"
                            >
                                {{ totalMenipis }}
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


                <!-- STOK HABIS -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm transition hover:border-rose-200 hover:shadow-md"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Stok Habis
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-rose-600"
                            >
                                {{ totalHabis }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50"
                        >

                            <ExclamationTriangleIcon
                                class="h-5 w-5 text-rose-600"
                            />

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FILTER
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
            >

                <div
                    class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[3fr_1fr_1fr]"
                >


                    <!-- SEARCH -->

                    <div
                        class="relative"
                    >

                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-pink-300"
                        />

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama obat, satuan, atau keterangan..."
                            class="w-full rounded-xl border border-pink-100 bg-pink-50/40 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-400 focus:bg-white focus:ring-2 focus:ring-pink-100"
                        />

                    </div>


                    <!-- STATUS -->

                    <select
                        v-model="stokFilter"
                        class="w-full rounded-xl border border-pink-100 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-pink-400 focus:ring-2 focus:ring-pink-100"
                    >

                        <option value="">
                            Semua Stok
                        </option>

                        <option value="tersedia">
                            Tersedia
                        </option>

                        <option value="menipis">
                            Menipis
                        </option>

                        <option value="habis">
                            Habis
                        </option>

                    </select>


                    <!-- FILTER BUTTON -->

                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        :class="[
                            'inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',
                            showFilter || stokFilter
                                ? 'border-pink-200 bg-pink-50 text-pink-700'
                                : 'border-pink-100 bg-white text-slate-600 hover:bg-pink-50 hover:text-pink-700'
                        ]"
                    >

                        <FunnelIcon
                            class="h-4 w-4"
                        />

                        Filter

                    </button>

                </div>


                <!-- FILTER DETAIL -->

                <div
                    v-if="showFilter"
                    class="border-t border-pink-100 bg-pink-50/40 px-4 py-4"
                >

                    <div
                        class="text-xs text-slate-500"
                    >

                        Filter berdasarkan kondisi stok obat.

                    </div>


                    <button
                        v-if="hasActiveFilter"
                        type="button"
                        @click="resetFilter"
                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-white hover:text-pink-700"
                    >

                        <XMarkIcon
                            class="h-4 w-4"
                        />

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
                    class="flex flex-col gap-1 border-b border-pink-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Daftar Obat
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Menampilkan
                            <span class="font-semibold text-pink-600">
                                {{ filteredObats.length }}
                            </span>
                            dari {{ totalObat }} obat.
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     DESKTOP
                ================================================== -->

                <div
                    class="hidden overflow-x-auto lg:block"
                >

                    <table
                        class="min-w-full"
                    >

                        <thead>

                            <tr
                                class="border-b border-pink-100 bg-pink-50/60"
                            >

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    No
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Nama Obat
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Satuan
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Stok
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Keterangan
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>

                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-pink-50"
                        >

                            <tr
                                v-for="(obat, index) in filteredObats"
                                :key="obat.id"
                                class="transition hover:bg-pink-50/40"
                            >

                                <!-- NO -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <!-- NAMA OBAT -->

                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="flex items-center gap-3"
                                    >

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-pink-700"
                                        >

                                            <BeakerIcon
                                                class="h-4 w-4"
                                            />

                                        </div>


                                        <div>

                                            <p
                                                class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                            >
                                                {{ obat.nama_obat }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- SATUAN -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-700"
                                >
                                    {{ obat.satuan || '-' }}
                                </td>


                                <!-- STOK -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-center"
                                >

                                    <span
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        {{ obat.stok }}
                                    </span>

                                </td>


                                <!-- KETERANGAN -->

                                <td
                                    class="max-w-xs px-5 py-4 text-sm text-slate-600"
                                >

                                    <span
                                        class="line-clamp-2"
                                    >
                                        {{ obat.keterangan || '-' }}
                                    </span>

                                </td>


                                <!-- STATUS -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-center"
                                >

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                            getStokClass(obat)
                                        ]"
                                    >
                                        {{ getStokLabel(obat) }}
                                    </span>

                                </td>


                                <!-- AKSI -->

                                <td
                                    class="whitespace-nowrap px-5 py-4"
                                >

                                    <div
                                        class="flex items-center justify-center gap-2"
                                    >

                                        <!-- EDIT -->

                                        <Link
                                            :href="route(
                                                'klinik.obat.edit',
                                                obat.id
                                            )"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-pink-200 bg-pink-50 text-pink-600 transition hover:bg-pink-100 hover:text-pink-700"
                                            title="Edit"
                                        >

                                            <PencilSquareIcon
                                                class="h-4 w-4"
                                            />

                                        </Link>


                                        <!-- DELETE -->

                                        <button
                                            type="button"
                                            @click="deleteObat(obat)"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100"
                                            title="Hapus"
                                        >

                                            <TrashIcon
                                                class="h-4 w-4"
                                            />

                                        </button>

                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-if="filteredObats.length === 0"
                            >

                                <td
                                    colspan="7"
                                    class="px-5 py-16 text-center"
                                >

                                    <div
                                        class="mx-auto flex max-w-sm flex-col items-center"
                                    >

                                        <div
                                            class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-pink-50"
                                        >

                                            <MagnifyingGlassIcon
                                                class="h-6 w-6 text-pink-300"
                                            />

                                        </div>


                                        <p
                                            class="text-sm font-bold text-slate-700"
                                        >
                                            Data obat tidak ditemukan
                                        </p>


                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            Coba ubah kata pencarian atau filter.
                                        </p>


                                        <button
                                            v-if="hasActiveFilter"
                                            type="button"
                                            @click="resetFilter"
                                            class="mt-3 text-xs font-bold text-pink-600 hover:text-pink-700"
                                        >
                                            Reset pencarian
                                        </button>

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
                    class="divide-y divide-pink-100 lg:hidden"
                >

                    <div
                        v-for="(obat, index) in filteredObats"
                        :key="obat.id"
                        class="p-4 transition hover:bg-pink-50/30"
                    >

                        <!-- HEADER -->

                        <div
                            class="flex items-start justify-between gap-3"
                        >

                            <div
                                class="flex min-w-0 items-center gap-3"
                            >

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pink-100 text-pink-700"
                                >

                                    <BeakerIcon
                                        class="h-5 w-5"
                                    />

                                </div>


                                <div
                                    class="min-w-0"
                                >

                                    <p
                                        class="truncate text-sm font-bold text-slate-800"
                                    >
                                        {{ obat.nama_obat }}
                                    </p>

                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        {{ obat.satuan || '-' }}
                                    </p>

                                </div>

                            </div>


                            <span
                                :class="[
                                    'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    getStokClass(obat)
                                ]"
                            >
                                {{ getStokLabel(obat) }}
                            </span>

                        </div>


                        <!-- INFO -->

                        <div
                            class="mt-4 grid grid-cols-2 gap-3 text-xs"
                        >

                            <div>

                                <p
                                    class="text-slate-400"
                                >
                                    Stok
                                </p>

                                <p
                                    class="mt-0.5 text-sm font-bold text-slate-700"
                                >
                                    {{ obat.stok }}
                                    {{ obat.satuan || '' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-slate-400"
                                >
                                    No
                                </p>

                                <p
                                    class="mt-0.5 font-semibold text-slate-700"
                                >
                                    {{ index + 1 }}
                                </p>

                            </div>

                        </div>


                        <!-- KETERANGAN -->

                        <div
                            class="mt-4 rounded-xl bg-pink-50/50 p-3"
                        >

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Keterangan
                            </p>

                            <p
                                class="mt-1 text-xs font-medium text-slate-700"
                            >
                                {{ obat.keterangan || '-' }}
                            </p>

                        </div>


                        <!-- AKSI -->

                        <div
                            class="mt-4 grid grid-cols-2 gap-3"
                        >

                            <!-- EDIT -->

                            <Link
                                :href="route(
                                    'klinik.obat.edit',
                                    obat.id
                                )"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-pink-200 bg-pink-50 px-3 py-2.5 text-xs font-bold text-pink-700 transition hover:bg-pink-100"
                            >

                                <PencilSquareIcon
                                    class="h-4 w-4"
                                />

                                Edit

                            </Link>


                            <!-- DELETE -->

                            <button
                                type="button"
                                @click="deleteObat(obat)"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100"
                            >

                                <TrashIcon
                                    class="h-4 w-4"
                                />

                                Hapus

                            </button>

                        </div>

                    </div>


                    <!-- MOBILE EMPTY -->

                    <div
                        v-if="filteredObats.length === 0"
                        class="px-5 py-16 text-center"
                    >

                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-pink-50"
                        >

                            <MagnifyingGlassIcon
                                class="h-6 w-6 text-pink-300"
                            />

                        </div>


                        <p
                            class="mt-3 text-sm font-bold text-slate-700"
                        >
                            Data obat tidak ditemukan
                        </p>


                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Coba ubah kata pencarian atau filter.
                        </p>


                        <button
                            v-if="hasActiveFilter"
                            type="button"
                            @click="resetFilter"
                            class="mt-3 text-xs font-bold text-pink-600 hover:text-pink-700"
                        >
                            Reset pencarian
                        </button>

                    </div>

                </div>

            </div>

        </div>

    </KlinikLayout>

</template>