<script setup>
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

import {
    PlusIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
    CalendarDaysIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    periodes: {
        type: Object,
        required: true,
    },
});


// ======================================================
// STATE
// ======================================================

const search = ref('');
const statusFilter = ref('');
const periodeFilter = ref('');
const tanggalMulaiFilter = ref('');
const tanggalSelesaiFilter = ref('');
const showFilter = ref(false);


// ======================================================
// PAGE
// ======================================================

const page = usePage();

const flashSuccess = computed(() => {
    return page.props.flash?.success ?? null;
});

const flashError = computed(() => {
    return page.props.flash?.error ?? null;
});


// ======================================================
// FILTER DATA
// ======================================================

const filteredPeriodes = computed(() => {

    let data = props.periodes.data ?? [];


    // ==================================================
    // SEARCH
    // ==================================================

    if (search.value.trim()) {

        const keyword = search.value
            .toLowerCase()
            .trim();

        data = data.filter((periode) => {

            return (
                String(periode.nama_periode ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(periode.tanggal_mulai ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(periode.tanggal_selesai ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(periode.pembuat?.name ?? '')
                    .toLowerCase()
                    .includes(keyword)
            );

        });

    }


    // ==================================================
    // FILTER PERIODE
    // ==================================================

    if (periodeFilter.value) {

        data = data.filter(
            (periode) =>
                String(periode.id) ===
                String(periodeFilter.value)
        );

    }


    // ==================================================
    // FILTER STATUS
    // ==================================================

    if (statusFilter.value) {

        data = data.filter(
            (periode) =>
                String(periode.status ?? '').toLowerCase() ===
                statusFilter.value.toLowerCase()
        );

    }


    // ==================================================
    // FILTER TANGGAL MULAI
    // ==================================================

    if (tanggalMulaiFilter.value) {

        data = data.filter(
            (periode) =>
                periode.tanggal_mulai &&
                periode.tanggal_mulai >= tanggalMulaiFilter.value
        );

    }


    // ==================================================
    // FILTER TANGGAL SELESAI
    // ==================================================

    if (tanggalSelesaiFilter.value) {

        data = data.filter(
            (periode) =>
                periode.tanggal_selesai &&
                periode.tanggal_selesai <= tanggalSelesaiFilter.value
        );

    }


    return data;

});


// ======================================================
// FILTER AKTIF
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        statusFilter.value ||
        periodeFilter.value ||
        tanggalMulaiFilter.value ||
        tanggalSelesaiFilter.value
    );

});


// ======================================================
// JUMLAH FILTER AKTIF
// ======================================================

const activeFilterCount = computed(() => {

    let count = 0;

    if (periodeFilter.value) {
        count++;
    }

    if (tanggalMulaiFilter.value) {
        count++;
    }

    if (tanggalSelesaiFilter.value) {
        count++;
    }

    if (statusFilter.value) {
        count++;
    }

    return count;

});


// ======================================================
// PERIODE YANG DIPILIH
// ======================================================

const selectedPeriode = computed(() => {

    if (!periodeFilter.value) {
        return null;
    }

    return (props.periodes.data ?? []).find(
        (periode) =>
            String(periode.id) ===
            String(periodeFilter.value)
    ) ?? null;

});


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = '';
    statusFilter.value = '';
    periodeFilter.value = '';
    tanggalMulaiFilter.value = '';
    tanggalSelesaiFilter.value = '';

};


// ======================================================
// FORMAT DATE
// ======================================================

const formatDate = (date) => {

    if (!date) {
        return '-';
    }

    return new Date(date).toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    );

};


// ======================================================
// FORMAT DATE INPUT
// ======================================================

const formatDateInput = (date) => {

    if (!date) {
        return '';
    }

    return String(date).substring(0, 10);

};


// ======================================================
// PILIH PERIODE
// ======================================================

const selectPeriode = () => {

    if (!selectedPeriode.value) {
        return;
    }

    // Ketika periode dipilih,
    // tanggal mulai dan selesai otomatis mengikuti periode tersebut.

    tanggalMulaiFilter.value =
        formatDateInput(
            selectedPeriode.value.tanggal_mulai
        );

    tanggalSelesaiFilter.value =
        formatDateInput(
            selectedPeriode.value.tanggal_selesai
        );

};


// ======================================================
// STATUS BADGE
// ======================================================

const getStatusBadge = (status) => {

    const value = String(status ?? '')
        .toLowerCase();

    if (value === 'aktif') {
        return 'border-emerald-200 bg-emerald-50 text-emerald-700';
    }

    if (value === 'selesai') {
        return 'border-blue-200 bg-blue-50 text-blue-700';
    }

    if (value === 'tidak aktif') {
        return 'border-slate-200 bg-slate-100 text-slate-500';
    }

    return 'border-slate-200 bg-slate-100 text-slate-500';

};


// ======================================================
// DELETE
// ======================================================

const deletePeriode = (periode) => {

    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus periode "${periode.nama_periode}"?`
    );

    if (!confirmed) {
        return;
    }

    router.delete(
        route(
            'admin.periode.destroy',
            periode.id
        ),
        {
            preserveScroll: true,
        }
    );

};


// ======================================================
// PAGINATION
// ======================================================

const goToPage = (url) => {

    if (!url) {
        return;
    }

    router.get(
        url,
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    );

};

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
                    Data Periode
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola periode pemeriksaan kesehatan siswa.
                </p>

            </div>


            <Link
                :href="route('admin.periode.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
            >

                <PlusIcon class="h-5 w-5" />

                Tambah Periode

            </Link>

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
                class="rounded-lg p-1 transition hover:bg-emerald-100"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FLASH ERROR
        ================================================== -->

        <div
            v-if="flashError"
            class="flex items-start justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
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
                        Periode tidak dapat dihapus
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

            <!-- ==================================================
                 SEARCH + STATUS + FILTER BUTTON
            ================================================== -->

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
                        placeholder="Cari nama periode atau pembuat..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />

                </div>


                <!-- STATUS -->

                <div>

                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="tidak aktif">
                            Tidak Aktif
                        </option>

                    </select>

                </div>


                <!-- FILTER BUTTON -->

                <div class="flex w-full gap-2">

                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        :class="[

                            'flex-1 inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',

                            showFilter || activeFilterCount > 0
                                ? 'border-blue-200 bg-blue-50 text-blue-700'
                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'

                        ]"
                    >

                        <FunnelIcon class="h-4 w-4" />

                        <span>
                            Filter
                        </span>

                        <span
                            v-if="activeFilterCount > 0"
                            class="flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[10px] font-bold text-white"
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

                    <!-- ==================================================
                         PILIH PERIODE
                    ================================================== -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Pilih Periode
                        </label>

                        <select
                            v-model="periodeFilter"
                            @change="selectPeriode"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Semua Periode
                            </option>

                            <option
                                v-for="periode in (periodes.data ?? [])"
                                :key="periode.id"
                                :value="periode.id"
                            >

                                {{ periode.nama_periode }}

                            </option>

                        </select>

                    </div>


                    <!-- ==================================================
                         TANGGAL MULAI
                    ================================================== -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Mulai
                        </label>

                        <input
                            v-model="tanggalMulaiFilter"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <!-- ==================================================
                         TANGGAL SELESAI
                    ================================================== -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Tanggal Selesai
                        </label>

                        <input
                            v-model="tanggalSelesaiFilter"
                            type="date"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />

                    </div>

                </div>


                <!-- ==================================================
                     PERIODE TERPILIH
                ================================================== -->

                <div
                    v-if="selectedPeriode"
                    class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"
                >

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100"
                        >

                            <CalendarDaysIcon
                                class="h-5 w-5 text-blue-700"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-blue-600"
                            >
                                Periode Dipilih
                            </p>

                            <p
                                class="mt-0.5 text-sm font-bold text-blue-800"
                            >

                                {{ selectedPeriode.nama_periode }}

                            </p>

                            <p
                                class="mt-1 text-xs text-blue-600"
                            >

                                {{ formatDate(selectedPeriode.tanggal_mulai) }}

                                –

                                {{ formatDate(selectedPeriode.tanggal_selesai) }}

                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     FILTER AKTIF
                ================================================== -->

                <div
                    v-if="activeFilterCount > 0"
                    class="mt-4 flex flex-wrap items-center gap-2"
                >

                    <span
                        class="text-xs font-medium text-slate-400"
                    >
                        Filter aktif:
                    </span>


                    <!-- PERIODE -->

                    <span
                        v-if="periodeFilter"
                        class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700"
                    >

                        Periode:
                        {{ selectedPeriode?.nama_periode ?? '-' }}

                    </span>


                    <!-- TANGGAL MULAI -->

                    <span
                        v-if="tanggalMulaiFilter"
                        class="rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700"
                    >

                        Mulai:
                        {{ formatDate(tanggalMulaiFilter) }}

                    </span>


                    <!-- TANGGAL SELESAI -->

                    <span
                        v-if="tanggalSelesaiFilter"
                        class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-semibold text-violet-700"
                    >

                        Selesai:
                        {{ formatDate(tanggalSelesaiFilter) }}

                    </span>


                    <!-- STATUS -->

                    <span
                        v-if="statusFilter"
                        :class="[

                            'rounded-full px-2.5 py-1 text-xs font-semibold',

                            statusFilter === 'aktif'
                                ? 'bg-emerald-100 text-emerald-700'
                                : 'bg-slate-100 text-slate-600'

                        ]"
                    >

                        {{
                            statusFilter === 'aktif'
                                ? 'Aktif'
                                : 'Tidak Aktif'
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
                class="flex items-center justify-between border-b border-slate-100 px-5 py-4"
            >

                <div>

                    <h2 class="text-sm font-bold text-slate-800">
                        Daftar Periode
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">

                        Menampilkan
                        {{ filteredPeriodes.length }}
                        periode pada halaman ini.

                    </p>

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

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                No
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama Periode
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Mulai
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Selesai
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Dibuat Oleh
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        <tr
                            v-for="(periode, index) in filteredPeriodes"
                            :key="periode.id"
                            class="transition hover:bg-slate-50"
                        >

                            <!-- NO -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                            >

                                {{
                                    (periodes.current_page - 1) *
                                    periodes.per_page +
                                    index +
                                    1
                                }}

                            </td>


                            <!-- NAMA -->

                            <td class="px-5 py-4">

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100"
                                    >

                                        <CalendarDaysIcon
                                            class="h-4 w-4 text-blue-700"
                                        />

                                    </div>


                                    <div>

                                        <p
                                            class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                        >
                                            {{ periode.nama_periode }}
                                        </p>

                                        <p
                                            class="text-xs text-slate-400"
                                        >
                                            ID Periode: {{ periode.id }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- TANGGAL MULAI -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                            >

                                {{ formatDate(periode.tanggal_mulai) }}

                            </td>


                            <!-- TANGGAL SELESAI -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                            >

                                {{ formatDate(periode.tanggal_selesai) }}

                            </td>


                            <!-- STATUS -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center"
                            >

                                <span
                                    :class="[

                                        'inline-flex rounded-full border px-2.5 py-1 text-xs font-bold',

                                        getStatusBadge(periode.status)

                                    ]"
                                >

                                    {{
                                        String(
                                            periode.status ?? ''
                                        ).toLowerCase() === 'aktif'
                                            ? 'Aktif'
                                            : 'Tidak Aktif'
                                    }}

                                </span>

                            </td>


                            <!-- PEMBUAT -->

                            <td class="px-5 py-4">

                                <div
                                    class="flex items-center gap-2"
                                >

                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600"
                                    >

                                        {{
                                            periode.pembuat?.name
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                            || '?'
                                        }}

                                    </div>


                                    <p
                                        class="whitespace-nowrap text-sm font-medium text-slate-700"
                                    >

                                        {{
                                            periode.pembuat?.name || '-'
                                        }}

                                    </p>

                                </div>

                            </td>


                            <!-- AKSI -->

                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <div
                                    class="flex items-center justify-end gap-1"
                                >

                                    <!-- DETAIL -->

                                    <Link
                                        v-if="
                                            route().has(
                                                'admin.periode.show'
                                            )
                                        "
                                        :href="route(
                                            'admin.periode.show',
                                            periode.id
                                        )"
                                        title="Detail"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    >

                                        <EyeIcon class="h-4 w-4" />

                                    </Link>


                                    <!-- EDIT -->

                                    <Link
                                        :href="route(
                                            'admin.periode.edit',
                                            periode.id
                                        )"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-600"
                                    >

                                        <PencilSquareIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>


                                    <!-- DELETE -->

                                    <button
                                        type="button"
                                        @click="deletePeriode(periode)"
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                    >

                                        <TrashIcon class="h-4 w-4" />

                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr v-if="filteredPeriodes.length === 0">

                            <td
                                colspan="7"
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
                                        Data periode tidak ditemukan
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
                 MOBILE CARD
            ================================================== -->

            <div class="divide-y divide-slate-100 lg:hidden">

                <div
                    v-for="(periode, index) in filteredPeriodes"
                    :key="periode.id"
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
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100"
                            >

                                <CalendarDaysIcon
                                    class="h-5 w-5 text-blue-700"
                                />

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ periode.nama_periode }}
                                </p>

                                <p class="text-xs text-slate-400">
                                    ID: {{ periode.id }}
                                </p>

                            </div>

                        </div>


                        <!-- STATUS -->

                        <span
                            :class="[

                                'shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-bold',

                                getStatusBadge(periode.status)

                            ]"
                        >

                            {{
                                String(
                                    periode.status ?? ''
                                ).toLowerCase() === 'aktif'
                                    ? 'Aktif'
                                    : 'Tidak Aktif'
                            }}

                        </span>

                    </div>


                    <!-- INFO -->

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 text-xs"
                    >

                        <div>

                            <p class="text-slate-400">
                                Tanggal Mulai
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >
                                {{ formatDate(periode.tanggal_mulai) }}
                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Tanggal Selesai
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >
                                {{ formatDate(periode.tanggal_selesai) }}
                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Dibuat Oleh
                            </p>

                            <p
                                class="mt-0.5 truncate font-semibold text-slate-700"
                            >
                                {{ periode.pembuat?.name || '-' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-slate-400">
                                Status
                            </p>

                            <span
                                :class="[

                                    'mt-0.5 inline-flex rounded-full border px-2 py-0.5 text-[10px] font-bold',

                                    getStatusBadge(periode.status)

                                ]"
                            >

                                {{
                                    String(
                                        periode.status ?? ''
                                    ).toLowerCase() === 'aktif'
                                        ? 'Aktif'
                                        : 'Tidak Aktif'
                                }}

                            </span>

                        </div>

                    </div>


                    <!-- ACTION -->

                    <div
                        class="mt-4 flex items-center justify-end gap-1 border-t border-slate-100 pt-3"
                    >

                        <!-- DETAIL -->

                        <Link
                            v-if="
                                route().has(
                                    'admin.periode.show'
                                )
                            "
                            :href="route(
                                'admin.periode.show',
                                periode.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50"
                        >

                            <EyeIcon class="h-4 w-4" />

                            Detail

                        </Link>


                        <!-- EDIT -->

                        <Link
                            :href="route(
                                'admin.periode.edit',
                                periode.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                        >

                            <PencilSquareIcon
                                class="h-4 w-4"
                            />

                            Edit

                        </Link>


                        <!-- DELETE -->

                        <button
                            type="button"
                            @click="deletePeriode(periode)"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                        >

                            <TrashIcon class="h-4 w-4" />

                            Hapus

                        </button>

                    </div>

                </div>


                <!-- MOBILE EMPTY -->

                <div
                    v-if="filteredPeriodes.length === 0"
                    class="px-5 py-16 text-center"
                >

                    <p
                        class="text-sm font-bold text-slate-700"
                    >
                        Data periode tidak ditemukan
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Coba ubah pencarian atau filter.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 PAGINATION
            ================================================== -->

            <div
                v-if="periodes.last_page > 1"
                class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <p
                    class="text-xs font-medium text-slate-500"
                >

                    Menampilkan

                    <span class="font-bold text-slate-700">
                        {{ periodes.from ?? 0 }}
                    </span>

                    –

                    <span class="font-bold text-slate-700">
                        {{ periodes.to ?? 0 }}
                    </span>

                    dari

                    <span class="font-bold text-slate-700">
                        {{ periodes.total }}
                    </span>

                    periode

                </p>


                <div class="flex items-center gap-1">

                    <!-- PREVIOUS -->

                    <button
                        type="button"
                        :disabled="!periodes.prev_page_url"
                        @click="goToPage(periodes.prev_page_url)"
                        class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >

                        <ChevronLeftIcon class="h-4 w-4" />

                    </button>


                    <!-- PAGE NUMBERS -->

                    <template
                        v-for="link in periodes.links.slice(1, -1)"
                        :key="link.label"
                    >

                        <button
                            v-if="link.url"
                            type="button"
                            @click="goToPage(link.url)"
                            :class="[

                                'min-w-9 rounded-lg border px-2.5 py-2 text-xs font-bold transition',

                                link.active
                                    ? 'border-blue-700 bg-blue-700 text-white'
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
                        :disabled="!periodes.next_page_url"
                        @click="goToPage(periodes.next_page_url)"
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