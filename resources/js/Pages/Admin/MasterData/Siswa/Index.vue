<script setup>
import { computed, ref, watch } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'

import {
    ArrowUpTrayIcon,
    UserPlusIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
    ExclamationTriangleIcon,
    CheckIcon,
    AcademicCapIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    siswas: {
        type: Object,
        required: true,
    },

    angkatanOptions: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            status: '',
            angkatan: '',
            jenis_kelamin: '',
            kelas: '',
        }),
    },
})


// ======================================================
// STATE FILTER
// ======================================================

const search = ref(props.filters.search ?? '')
const statusFilter = ref(props.filters.status ?? '')
const angkatanFilter = ref(props.filters.angkatan ?? '')
const jenisKelaminFilter = ref(props.filters.jenis_kelamin ?? '')
const kelasFilter = ref(props.filters.kelas ?? '')

const showFilter = ref(false)


// ======================================================
// BULK SELECTION
// ======================================================

const selectedIds = ref([])

const isBulkUpdating = ref(false)


// ======================================================
// DATA SISWA PADA HALAMAN SAAT INI
// ======================================================

const currentSiswaIds = computed(() => {
    return (props.siswas.data ?? []).map(
        siswa => siswa.id
    )
})


// ======================================================
// JUMLAH YANG DIPILIH
// ======================================================

const selectedCount = computed(() => {
    return selectedIds.value.length
})


// ======================================================
// CEK SEMUA DIPILIH
// ======================================================

const isAllSelected = computed(() => {

    const ids = currentSiswaIds.value

    if (!ids.length) {
        return false
    }

    return ids.every(id =>
        selectedIds.value.includes(id)
    )
})


// ======================================================
// CEK SEBAGIAN DIPILIH
// ======================================================

const isSomeSelected = computed(() => {

    const ids = currentSiswaIds.value

    if (!ids.length) {
        return false
    }

    const selectedOnPage = ids.filter(id =>
        selectedIds.value.includes(id)
    )

    return (
        selectedOnPage.length > 0 &&
        selectedOnPage.length < ids.length
    )
})


// ======================================================
// SELECT SATU SISWA
// ======================================================

const toggleSelect = (id) => {

    const index = selectedIds.value.indexOf(id)

    if (index === -1) {

        selectedIds.value.push(id)

    } else {

        selectedIds.value.splice(index, 1)

    }
}


// ======================================================
// SELECT SEMUA PADA HALAMAN
// ======================================================

const toggleSelectAll = () => {

    const ids = currentSiswaIds.value

    if (!ids.length) {
        return
    }

    if (isAllSelected.value) {

        // Hapus semua ID pada halaman ini
        selectedIds.value =
            selectedIds.value.filter(
                id => !ids.includes(id)
            )

    } else {

        // Tambahkan semua ID pada halaman ini
        ids.forEach(id => {

            if (!selectedIds.value.includes(id)) {

                selectedIds.value.push(id)

            }

        })

    }
}


// ======================================================
// BERSIHKAN SELECTION
// ======================================================

const clearSelection = () => {
    selectedIds.value = []
}


// ======================================================
// UPDATE STATUS MASSAL
// ======================================================

const updateBulkStatus = (status) => {

    if (!selectedIds.value.length) {
        return
    }


    let label = 'Nonaktif'

    if (status === 'lulus') {
        label = 'Lulus'
    }


    let message =
        `Apakah Anda yakin ingin mengubah ` +
        `${selectedIds.value.length} siswa menjadi ${label}?`


    // Penjelasan tambahan ketika lulus
    if (status === 'lulus') {

        message +=
            `\n\nSiswa yang dinyatakan Lulus juga akan ` +
            `diproses perubahan kelasnya sesuai aturan kelulusan.`

    }


    const confirmed = confirm(message)

    if (!confirmed) {
        return
    }


    isBulkUpdating.value = true


    router.patch(
        route('admin.master.siswa.bulk-status'),
        {
            ids: selectedIds.value,
            status: status,
        },
        {
            preserveScroll: true,

            onSuccess: () => {

                selectedIds.value = []

            },

            onFinish: () => {

                isBulkUpdating.value = false

            },
        }
    )
}


// ======================================================
// NAIK KELAS
// ======================================================

const naikKelas = () => {

    if (!selectedIds.value.length) {
        return
    }


    const confirmed = confirm(
        `Proses kenaikan kelas untuk ` +
        `${selectedIds.value.length} siswa?\n\n` +

        `Kelas 10 → Kelas 11\n` +
        `Kelas 11 → Kelas 12\n` +
        `Kelas 12 → Lulus\n\n` +

        `Jika siswa kelas 12 diproses, statusnya akan otomatis menjadi Lulus.`
    )


    if (!confirmed) {
        return
    }


    isBulkUpdating.value = true


    router.patch(
        route('admin.master.siswa.bulk-naik-kelas'),
        {
            ids: selectedIds.value,
        },
        {
            preserveScroll: true,

            onSuccess: () => {

                selectedIds.value = []

            },

            onFinish: () => {

                isBulkUpdating.value = false

            },
        }
    )
}


// ======================================================
// PAGE
// ======================================================

const page = usePage()


const flashSuccess = computed(() => {
    return page.props.flash?.success ?? null
})


const flashError = computed(() => {
    return page.props.flash?.error ?? null
})


// ======================================================
// KELAS OPTIONS
// ======================================================

const kelasOptions = [
    '10',
    '11',
    '12',
]


// ======================================================
// FILTER AKTIF
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        statusFilter.value ||
        angkatanFilter.value ||
        jenisKelaminFilter.value ||
        kelasFilter.value
    )

})


// ======================================================
// JUMLAH FILTER TAMBAHAN AKTIF
// ======================================================

const activeFilterCount = computed(() => {

    let count = 0

    if (angkatanFilter.value) {
        count++
    }

    if (jenisKelaminFilter.value) {
        count++
    }

    if (kelasFilter.value) {
        count++
    }

    return count

})


// ======================================================
// REQUEST FILTER KE LARAVEL
// ======================================================

let searchTimer = null


const applyFilter = () => {

    // Saat filter berubah, selection dibersihkan
    selectedIds.value = []


    router.get(
        route('admin.master.siswa.index'),
        {
            search: search.value || undefined,
            status: statusFilter.value || undefined,
            angkatan: angkatanFilter.value || undefined,
            jenis_kelamin: jenisKelaminFilter.value || undefined,
            kelas: kelasFilter.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )

}


// ======================================================
// SEARCH
// ======================================================

watch(search, () => {

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {

        applyFilter()

    }, 400)

})


// ======================================================
// FILTER SELECT
// ======================================================

watch(
    [
        statusFilter,
        angkatanFilter,
        jenisKelaminFilter,
        kelasFilter,
    ],
    () => {

        applyFilter()

    }
)


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = ''
    statusFilter.value = ''
    angkatanFilter.value = ''
    jenisKelaminFilter.value = ''
    kelasFilter.value = ''

    selectedIds.value = []


    router.get(
        route('admin.master.siswa.index'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )

}


// ======================================================
// DELETE
// ======================================================

const deleteSiswa = (siswa) => {

    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus data siswa "${siswa.nama}"?`
    )


    if (!confirmed) {
        return
    }


    router.delete(
        route(
            'admin.master.siswa.destroy',
            siswa.id
        ),
        {
            preserveScroll: true,
        }
    )

}


// ======================================================
// PAGINATION
// ======================================================

const goToPage = (url) => {

    if (!url) {
        return
    }


    // Selection dibersihkan ketika pindah halaman
    selectedIds.value = []


    router.get(
        url,
        {},
        {
            preserveScroll: true,
            preserveState: true,
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
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >

            <div>

                <h1 class="text-2xl font-bold text-slate-800">
                    Data Siswa
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola data siswa SMKN Jateng Semarang.
                </p>

            </div>


            <!-- BUTTON -->

            <div class="flex flex-col gap-2 sm:flex-row">

                <Link
                    :href="route('admin.master.siswa.import')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >

                    <ArrowUpTrayIcon class="h-5 w-5" />

                    Import Siswa

                </Link>


                <Link
                    :href="route('admin.master.siswa.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-800"
                >

                    <UserPlusIcon class="h-5 w-5" />

                    Tambah Siswa

                </Link>

            </div>

        </div>


        <!-- ==================================================
             FLASH SUCCESS
        ================================================== -->

        <div
            v-if="flashSuccess"
            class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
        >

            <span>
                {{ flashSuccess }}
            </span>

            <button
                type="button"
                @click="page.props.flash.success = null"
                class="rounded-lg p-1 hover:bg-emerald-100"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FLASH ERROR
        ================================================== -->

        <div
            v-if="flashError"
            class="flex items-start justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
        >

            <div class="flex items-start gap-3">

                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100"
                >

                    <ExclamationTriangleIcon
                        class="h-4 w-4 text-rose-600"
                    />

                </div>


                <div>

                    <p class="font-bold text-rose-800">
                        Data siswa tidak dapat dihapus
                    </p>

                    <p class="mt-0.5 text-xs text-rose-600">
                        {{ flashError }}
                    </p>

                </div>

            </div>


            <button
                type="button"
                @click="page.props.flash.error = null"
                class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-100 hover:text-rose-700"
                title="Tutup"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FILTER CARD
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[3fr_2fr_1fr]"
            >


                <!-- SEARCH -->

                <div class="relative w-full">

                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari NISN, nama, kelas, atau jurusan..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                    />

                </div>


                <!-- STATUS -->

                <div class="w-full">

                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="nonaktif">
                            Nonaktif
                        </option>

                        <option value="lulus">
                            Lulus
                        </option>

                    </select>

                </div>


                <!-- FILTER + RESET -->

                <div class="flex w-full gap-2">

                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        :class="[
                            'flex-1 inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',
                            showFilter || activeFilterCount > 0
                                ? 'border-pink-200 bg-pink-50 text-pink-700'
                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                        ]"
                    >

                        <FunnelIcon class="h-4 w-4" />

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
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >

                        <XMarkIcon class="h-4 w-4" />

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
                    class="grid grid-cols-1 gap-4 md:grid-cols-3"
                >


                    <!-- ANGKATAN -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Angkatan
                        </label>

                        <select
                            v-model="angkatanFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                        >

                            <option value="">
                                Semua Angkatan
                            </option>

                            <option
                                v-for="angkatan in angkatanOptions"
                                :key="angkatan"
                                :value="angkatan"
                            >

                                {{ angkatan }}

                            </option>

                        </select>

                    </div>


                    <!-- JENIS KELAMIN -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Jenis Kelamin
                        </label>

                        <select
                            v-model="jenisKelaminFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                        >

                            <option value="">
                                Semua Jenis Kelamin
                            </option>

                            <option value="L">
                                Laki-laki
                            </option>

                            <option value="P">
                                Perempuan
                            </option>

                        </select>

                    </div>


                    <!-- KELAS -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Kelas
                        </label>

                        <select
                            v-model="kelasFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                        >

                            <option value="">
                                Semua Kelas
                            </option>

                            <option
                                v-for="tingkat in kelasOptions"
                                :key="tingkat"
                                :value="tingkat"
                            >

                                Kelas {{ tingkat }}

                            </option>

                        </select>

                    </div>

                </div>


                <!-- FILTER AKTIF -->

                <div
                    v-if="activeFilterCount > 0"
                    class="mt-4 flex flex-wrap items-center gap-2"
                >

                    <span class="text-xs font-medium text-slate-400">
                        Filter aktif:
                    </span>


                    <span
                        v-if="angkatanFilter"
                        class="rounded-full bg-pink-100 px-2.5 py-1 text-xs font-semibold text-pink-700"
                    >
                        Angkatan {{ angkatanFilter }}
                    </span>


                    <span
                        v-if="jenisKelaminFilter"
                        class="rounded-full bg-pink-100 px-2.5 py-1 text-xs font-semibold text-pink-700"
                    >

                        {{
                            jenisKelaminFilter === 'L'
                                ? 'Laki-laki'
                                : 'Perempuan'
                        }}

                    </span>


                    <span
                        v-if="kelasFilter"
                        class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700"
                    >
                        Kelas {{ kelasFilter }}
                    </span>

                </div>

            </div>

        </div>


        <!-- ==================================================
             TABLE CARD
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >


            <!-- ==================================================
                 TABLE HEADER
            ================================================== -->

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div
                    class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
                >

                    <div>

                        <h2 class="text-sm font-bold text-slate-800">
                            Daftar Siswa
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">

                            Menampilkan
                            {{ siswas.data?.length ?? 0 }}
                            siswa pada halaman ini.

                        </p>

                    </div>


                    <!-- ==================================================
                         BULK ACTION BAR
                    ================================================== -->

                    <div
                        v-if="selectedCount > 0"
                        class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    >

                        <!-- JUMLAH DIPILIH -->

                        <div
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700"
                        >

                            <CheckIcon class="h-4 w-4" />

                            {{ selectedCount }} siswa dipilih

                        </div>


                        <!-- NONAKTIF -->

                        <button
                            type="button"
                            :disabled="isBulkUpdating"
                            @click="updateBulkStatus('nonaktif')"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            <XMarkIcon class="h-4 w-4" />

                            Nonaktif

                        </button>


                        <!-- LULUS -->

                        <button
                            type="button"
                            :disabled="isBulkUpdating"
                            @click="updateBulkStatus('lulus')"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-pink-200 bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700 transition hover:bg-pink-100 disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            <AcademicCapIcon class="h-4 w-4" />

                            Lulus

                        </button>


                        <!-- NAIK KELAS -->

                        <button
                            type="button"
                            :disabled="isBulkUpdating"
                            @click="naikKelas"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >

                            <AcademicCapIcon class="h-4 w-4" />

                            Naik Kelas

                        </button>


                        <!-- BATAL -->

                        <button
                            type="button"
                            :disabled="isBulkUpdating"
                            @click="clearSelection"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-500 transition hover:bg-slate-50 disabled:opacity-50"
                        >

                            Batal

                        </button>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 DESKTOP TABLE
            ================================================== -->

            <div class="hidden overflow-x-auto lg:block">

                <table class="min-w-full">

                    <thead>

                        <tr
                            class="border-b border-slate-200 bg-slate-50"
                        >


                            <!-- CHECKBOX -->

                            <th
                                class="w-12 whitespace-nowrap px-5 py-3 text-center"
                            >

                                <input
                                    type="checkbox"
                                    :checked="isAllSelected"
                                    :indeterminate="isSomeSelected"
                                    @change="toggleSelectAll"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                                />

                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                No
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                NISN
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Nama Siswa
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Kelas
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                                Jurusan
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Angkatan
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                JK
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500">
                                Status
                            </th>


                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">


                        <!-- DATA -->

                        <tr
                            v-for="(siswa, index) in siswas.data"
                            :key="siswa.id"
                            :class="[
                                'transition',
                                selectedIds.includes(siswa.id)
                                    ? 'bg-pink-50/60'
                                    : 'hover:bg-slate-50'
                            ]"
                        >


                            <!-- CHECKBOX -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center"
                            >

                                <input
                                    type="checkbox"
                                    :checked="selectedIds.includes(siswa.id)"
                                    @change="toggleSelect(siswa.id)"
                                    class="h-4 w-4 cursor-pointer rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                                />

                            </td>


                            <!-- NO -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                            >

                                {{
                                    (siswas.current_page - 1) *
                                    siswas.per_page +
                                    index +
                                    1
                                }}

                            </td>


                            <!-- NISN -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-700"
                            >
                                {{ siswa.nisn }}
                            </td>


                            <!-- NAMA -->

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-xs font-bold text-pink-700"
                                    >

                                        {{
                                            siswa.nama
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                        }}

                                    </div>


                                    <div>

                                        <p
                                            class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                        >
                                            {{ siswa.nama }}
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            {{ siswa.no_hp || '-' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- KELAS -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-700"
                            >

                                {{ siswa.kelas?.tingkat || '-' }}

                            </td>


                            <!-- JURUSAN -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                            >

                                {{
                                    siswa.kelas?.jurusan
                                        ?.nama_jurusan || '-'
                                }}

                            </td>


                            <!-- ANGKATAN -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center text-sm text-slate-600"
                            >

                                {{ siswa.angkatan || '-' }}

                            </td>


                            <!-- JK -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center"
                            >

                                <span
                                    class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600"
                                >

                                    {{
                                        siswa.jenis_kelamin === 'L'
                                            ? 'L'
                                            : 'P'
                                    }}

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center"
                            >

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',

                                        siswa.status === 'aktif'
                                            ? 'bg-emerald-100 text-emerald-700'

                                            : siswa.status === 'lulus'
                                                ? 'bg-pink-100 text-pink-700'

                                                : 'bg-slate-100 text-slate-500'
                                    ]"
                                >

                                    {{
                                        siswa.status === 'aktif'
                                            ? 'Aktif'

                                            : siswa.status === 'lulus'
                                                ? 'Lulus'

                                                : 'Nonaktif'
                                    }}

                                </span>

                            </td>


                            <!-- AKSI -->

                            <td class="whitespace-nowrap px-5 py-4">

                                <div
                                    class="flex items-center justify-end gap-1"
                                >

                                    <Link
                                        :href="route(
                                            'admin.master.siswa.show',
                                            siswa.id
                                        )"
                                        title="Detail"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-pink-50 hover:text-pink-600"
                                    >

                                        <EyeIcon class="h-4 w-4" />

                                    </Link>


                                    <Link
                                        :href="route(
                                            'admin.master.siswa.edit',
                                            siswa.id
                                        )"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-600"
                                    >

                                        <PencilSquareIcon class="h-4 w-4" />

                                    </Link>


                                    <button
                                        type="button"
                                        @click="deleteSiswa(siswa)"
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                    >

                                        <TrashIcon class="h-4 w-4" />

                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr
                            v-if="!siswas.data?.length"
                        >

                            <td
                                colspan="10"
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
                                        Data siswa tidak ditemukan
                                    </p>


                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Coba ubah kata pencarian atau
                                        filter yang digunakan.
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

            <div class="divide-y divide-slate-100 lg:hidden">


                <!-- MOBILE SELECT ALL -->

                <div
                    v-if="siswas.data?.length"
                    class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3"
                >

                    <label
                        class="flex cursor-pointer items-center gap-2 text-xs font-bold text-slate-600"
                    >

                        <input
                            type="checkbox"
                            :checked="isAllSelected"
                            :indeterminate="isSomeSelected"
                            @change="toggleSelectAll"
                            class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                        />

                        Pilih semua

                    </label>


                    <span
                        v-if="selectedCount > 0"
                        class="text-xs font-semibold text-pink-600"
                    >

                        {{ selectedCount }} dipilih

                    </span>

                </div>


                <div
                    v-for="siswa in siswas.data"
                    :key="siswa.id"
                    :class="[
                        'p-4 transition',
                        selectedIds.includes(siswa.id)
                            ? 'bg-pink-50/60'
                            : ''
                    ]"
                >

                    <div
                        class="flex items-start justify-between gap-3"
                    >

                        <div
                            class="flex min-w-0 items-center gap-3"
                        >

                            <!-- CHECKBOX -->

                            <input
                                type="checkbox"
                                :checked="selectedIds.includes(siswa.id)"
                                @change="toggleSelect(siswa.id)"
                                class="h-4 w-4 shrink-0 cursor-pointer rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                            />


                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pink-100 text-sm font-bold text-pink-700"
                            >

                                {{
                                    siswa.nama
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                }}

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ siswa.nama }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    NISN: {{ siswa.nisn }}
                                </p>

                            </div>

                        </div>


                        <span
                            :class="[
                                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold',

                                siswa.status === 'aktif'
                                    ? 'bg-emerald-100 text-emerald-700'

                                    : siswa.status === 'lulus'
                                        ? 'bg-pink-100 text-pink-700'

                                        : 'bg-slate-100 text-slate-500'
                            ]"
                        >

                            {{
                                siswa.status === 'aktif'
                                    ? 'Aktif'

                                    : siswa.status === 'lulus'
                                        ? 'Lulus'

                                        : 'Nonaktif'
                            }}

                        </span>

                    </div>


                    <div
                        class="mt-4 grid grid-cols-2 gap-3 text-xs"
                    >

                        <div>

                            <p class="text-slate-400">
                                Kelas
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >

                                {{ siswa.kelas?.tingkat || '-' }}

                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Jurusan
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >

                                {{
                                    siswa.kelas?.jurusan
                                        ?.nama_jurusan || '-'
                                }}

                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Angkatan
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >

                                {{ siswa.angkatan || '-' }}

                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Jenis Kelamin
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >

                                {{
                                    siswa.jenis_kelamin === 'L'
                                        ? 'Laki-laki'
                                        : 'Perempuan'
                                }}

                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-4 flex items-center justify-end gap-1 border-t border-slate-100 pt-3"
                    >

                        <Link
                            :href="route(
                                'admin.master.siswa.show',
                                siswa.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-pink-600 hover:bg-pink-50"
                        >

                            <EyeIcon class="h-4 w-4" />

                            Detail

                        </Link>


                        <Link
                            :href="route(
                                'admin.master.siswa.edit',
                                siswa.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                        >

                            <PencilSquareIcon class="h-4 w-4" />

                            Edit

                        </Link>


                        <button
                            type="button"
                            @click="deleteSiswa(siswa)"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                        >

                            <TrashIcon class="h-4 w-4" />

                            Hapus

                        </button>

                    </div>

                </div>


                <!-- MOBILE EMPTY -->

                <div
                    v-if="!siswas.data?.length"
                    class="px-5 py-16 text-center"
                >

                    <p class="text-sm font-bold text-slate-700">
                        Data siswa tidak ditemukan
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        Coba ubah pencarian atau filter.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 PAGINATION
            ================================================== -->

            <div
                v-if="siswas.last_page > 1"
                class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <p class="text-xs font-medium text-slate-500">

                    Menampilkan

                    <span class="font-bold text-slate-700">
                        {{ siswas.from ?? 0 }}
                    </span>

                    –

                    <span class="font-bold text-slate-700">
                        {{ siswas.to ?? 0 }}
                    </span>

                    dari

                    <span class="font-bold text-slate-700">
                        {{ siswas.total }}
                    </span>

                    siswa

                </p>


                <div class="flex items-center gap-1">


                    <!-- PREVIOUS -->

                    <button
                        type="button"
                        :disabled="!siswas.prev_page_url"
                        @click="goToPage(siswas.prev_page_url)"
                        class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >

                        <ChevronLeftIcon class="h-4 w-4" />

                    </button>


                    <!-- PAGE NUMBERS -->

                    <template
                        v-for="(link, index) in siswas.links?.slice(1, -1)"
                        :key="`${index}-${link.label}`"
                    >

                        <button
                            v-if="link.url"
                            type="button"
                            @click="goToPage(link.url)"
                            :class="[
                                'min-w-9 rounded-lg border px-2.5 py-2 text-xs font-bold transition',

                                link.active
                                    ? 'border-pink-700 bg-pink-700 text-white'

                                    : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                            ]"
                        >

                            <span
                                v-html="link.label"
                            ></span>

                        </button>


                        <span
                            v-else
                            class="px-1 text-slate-400"
                            v-html="link.label"
                        ></span>

                    </template>


                    <!-- NEXT -->

                    <button
                        type="button"
                        :disabled="!siswas.next_page_url"
                        @click="goToPage(siswas.next_page_url)"
                        class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >

                        <ChevronRightIcon class="h-4 w-4" />

                    </button>

                </div>

            </div>

        </div>

    </div>

</AdminLayout>

</template>