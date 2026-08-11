<script setup>

import { computed, ref } from 'vue';

import KlinikLayout from '@/Layouts/KlinikLayout.vue';

import { Link, usePage } from '@inertiajs/vue3';

import {
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    EyeIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PROPS
// ======================================================

const props = defineProps({

    periode: {
        type: Object,
        default: null,
    },

    siswas: {
        type: Array,
        default: () => [],
    },

});


// ======================================================
// PAGE
// ======================================================

const page = usePage();


// ======================================================
// STATE
// ======================================================

const search = ref('');

const showFilter = ref(false);

const kelasFilter = ref('');

const statusFilter = ref('');


// ======================================================
// FLASH
// ======================================================

const flashSuccess = computed(() => {

    return page.props.flash?.success ?? null;

});

const flashError = computed(() => {

    return page.props.flash?.error ?? null;

});


// ======================================================
// FILTER OPTIONS
// ======================================================

const kelasOptions = computed(() => {

    return [
        ...new Set(
            props.siswas
                .map(
                    (siswa) =>
                        siswa.kelas?.tingkat
                )
                .filter(Boolean)
        ),
    ].sort();

});


// ======================================================
// FILTER DATA
// ======================================================

const filteredSiswas = computed(() => {

    let data = props.siswas ?? [];


    // ==================================================
    // SEARCH
    // ==================================================

    if (search.value.trim()) {

        const keyword =
            search.value
                .toLowerCase()
                .trim();

        data = data.filter((siswa) => {

            return (

                String(
                    siswa.nisn ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.nama ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.tingkat ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.nama_kelas ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.jurusan
                        ?.nama_jurusan ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

            );

        });

    }


    // ==================================================
    // FILTER KELAS
    // ==================================================

    if (kelasFilter.value) {

        data = data.filter(
            (siswa) =>
                String(
                    siswa.kelas?.tingkat
                ) ===
                String(
                    kelasFilter.value
                )
        );

    }


    // ==================================================
    // FILTER STATUS
    // ==================================================

    if (statusFilter.value) {

        data = data.filter((siswa) => {

            const berkala1 =
                siswa.berkala_1?.selesai;

            const berkala2 =
                siswa.berkala_2?.selesai;


            if (
                statusFilter.value ===
                'selesai'
            ) {

                return (
                    berkala1 &&
                    berkala2
                );

            }


            if (
                statusFilter.value ===
                'belum'
            ) {

                return (
                    !berkala1 ||
                    !berkala2
                );

            }


            if (
                statusFilter.value ===
                'sebagian'
            ) {

                return (
                    (berkala1 &&
                        !berkala2)

                    ||

                    (!berkala1 &&
                        berkala2)
                );

            }


            return true;

        });

    }


    return data;

});


// ======================================================
// ACTIVE FILTER
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        kelasFilter.value ||
        statusFilter.value
    );

});


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = '';

    kelasFilter.value = '';

    statusFilter.value = '';

};


// ======================================================
// STATUS SISWA
// ======================================================

const getStatus = (siswa) => {

    const berkala1 =
        siswa.berkala_1?.selesai;

    const berkala2 =
        siswa.berkala_2?.selesai;


    if (
        berkala1 &&
        berkala2
    ) {

        return 'selesai';

    }


    if (
        berkala1 ||
        berkala2
    ) {

        return 'sebagian';

    }


    return 'belum';

};


// ======================================================
// STATUS LABEL
// ======================================================

const getStatusLabel = (siswa) => {

    const status =
        getStatus(siswa);


    if (
        status ===
        'selesai'
    ) {

        return 'Selesai';

    }


    if (
        status ===
        'sebagian'
    ) {

        return 'Sebagian';

    }


    return 'Belum Lengkap';

};


// ======================================================
// STATUS CLASS
// ======================================================

const getStatusClass = (siswa) => {

    const status =
        getStatus(siswa);


    if (
        status ===
        'selesai'
    ) {

        return 'bg-emerald-100 text-emerald-700';

    }


    if (
        status ===
        'sebagian'
    ) {

        return 'bg-amber-100 text-amber-700';

    }


    return 'bg-rose-100 text-rose-700';

};


// ======================================================
// CLEAR FLASH
// ======================================================

const clearFlash = () => {

    if (page.props.flash) {

        page.props.flash.success =
            null;

        page.props.flash.error =
            null;

    }

};

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
                    Pemeriksaan Berkala
                </h1>


                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Kelola pemeriksaan kesehatan berkala
                    siswa pada periode aktif.
                </p>

            </div>


            <!-- PERIODE -->

            <div
                v-if="periode"
                class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5"
            >

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-blue-500"
                >
                    Periode Aktif
                </p>


                <p
                    class="mt-0.5 text-sm font-bold text-blue-800"
                >
                    {{ periode.nama_periode }}
                </p>

            </div>

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
             NO PERIOD
        ================================================== -->

        <div
            v-if="!periode"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-6"
        >

            <div
                class="flex items-start gap-3"
            >

                <ExclamationTriangleIcon
                    class="h-5 w-5 shrink-0 text-amber-600"
                />


                <div>

                    <p
                        class="text-sm font-bold text-amber-800"
                    >
                        Belum ada periode aktif
                    </p>


                    <p
                        class="mt-1 text-xs text-amber-600"
                    >
                        Pemeriksaan berkala belum dapat
                        dilakukan karena belum ada periode
                        yang aktif.
                    </p>

                </div>

            </div>

        </div>


        <!-- ==================================================
             CONTENT
        ================================================== -->

        <template v-else>


            <!-- ==================================================
                 SUMMARY
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-3"
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
                                class="text-xs font-semibold text-slate-400"
                            >
                                Total Siswa
                            </p>


                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ siswas.length }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-blue-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- SELESAI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Pemeriksaan Lengkap
                            </p>


                            <p
                                class="mt-1 text-2xl font-bold text-emerald-600"
                            >

                                {{
                                    siswas.filter(
                                        siswa =>
                                            siswa.berkala_1?.selesai &&
                                            siswa.berkala_2?.selesai
                                    ).length
                                }}

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


                <!-- BELUM -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Perlu Dilengkapi
                            </p>


                            <p
                                class="mt-1 text-2xl font-bold text-rose-600"
                            >

                                {{
                                    siswas.filter(
                                        siswa =>
                                            !siswa.berkala_1?.selesai ||
                                            !siswa.berkala_2?.selesai
                                    ).length
                                }}

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
                class="rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[3fr_1fr_1fr]"
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
                            placeholder="Cari NISN, nama, kelas, atau jurusan..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <!-- STATUS -->

                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="selesai">
                            Lengkap
                        </option>

                        <option value="sebagian">
                            Sebagian
                        </option>

                        <option value="belum">
                            Belum Lengkap
                        </option>

                    </select>


                    <!-- FILTER -->

                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        :class="[
                            'inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',
                            showFilter || kelasFilter
                                ? 'border-blue-200 bg-blue-50 text-blue-700'
                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                        ]"
                    >

                        <FunnelIcon
                            class="h-4 w-4"
                        />

                        Filter Kelas

                    </button>

                </div>


                <!-- FILTER KELAS -->

                <div
                    v-if="showFilter"
                    class="border-t border-slate-100 bg-slate-50/70 px-4 py-4"
                >

                    <div
                        class="max-w-xs"
                    >

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Tingkat Kelas
                        </label>


                        <select
                            v-model="kelasFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Semua Kelas
                            </option>


                            <option
                                v-for="kelas in kelasOptions"
                                :key="kelas"
                                :value="kelas"
                            >

                                Kelas {{ kelas }}

                            </option>

                        </select>

                    </div>


                    <button
                        v-if="hasActiveFilter"
                        type="button"
                        @click="resetFilter"
                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
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
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- TABLE HEADER -->

                <div
                    class="flex flex-col gap-1 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Daftar Pemeriksaan Siswa
                        </h2>


                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Menampilkan
                            {{ filteredSiswas.length }}
                            siswa pada periode
                            {{ periode.nama_periode }}.
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
                                    NISN
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Nama Siswa
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kelas
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Jurusan
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 1
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 2
                                </th>


                                <th
                                    class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="(siswa, index) in filteredSiswas"
                                :key="siswa.id"
                                class="transition hover:bg-slate-50"
                            >

                                <!-- NO -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <!-- NISN -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-slate-700"
                                >
                                    {{ siswa.nisn }}
                                </td>


                                <!-- NAMA -->

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

                                        </div>

                                    </div>

                                </td>


                                <!-- KELAS -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-700"
                                >
                                    {{
                                        siswa.kelas?.nama_kelas ||
                                        siswa.kelas?.tingkat ||
                                        '-'
                                    }}
                                </td>


                                <!-- JURUSAN -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{
                                        siswa.kelas?.jurusan
                                            ?.nama_jurusan ||
                                        '-'
                                    }}
                                </td>


                                <!-- BERKALA 1 -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <Link
                                        v-if="siswa.berkala_1?.selesai"
                                        :href="route(
                                            'klinik.kesehatan.pemeriksaan.show',
                                            siswa.berkala_1.id
                                        )"
                                        class="group inline-flex min-w-[110px] flex-col items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 transition hover:border-emerald-300 hover:bg-emerald-100"
                                    >

                                        <span
                                            class="flex items-center gap-1.5 text-xs font-bold text-emerald-700"
                                        >

                                            <CheckCircleIcon
                                                class="h-4 w-4"
                                            />

                                            Selesai

                                        </span>


                                        <span
                                            v-if="siswa.berkala_1.tanggal"
                                            class="mt-0.5 text-[10px] text-emerald-600"
                                        >

                                            {{
                                                new Date(
                                                    siswa.berkala_1.tanggal
                                                ).toLocaleDateString(
                                                    'id-ID'
                                                )
                                            }}

                                        </span>

                                    </Link>


                                    <Link
                                        v-else
                                        :href="route(
                                            'klinik.kesehatan.pemeriksaan.create',
                                            {
                                                siswa: siswa.id,
                                                jenis: 1
                                            }
                                        )"
                                        class="inline-flex min-w-[110px] flex-col items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 transition hover:border-rose-300 hover:bg-rose-100"
                                    >

                                        <span
                                            class="flex items-center gap-1.5 text-xs font-bold text-rose-700"
                                        >

                                            <ExclamationTriangleIcon
                                                class="h-4 w-4"
                                            />

                                            Lengkapi

                                        </span>


                                        <span
                                            class="mt-0.5 text-[10px] text-rose-500"
                                        >
                                            Belum diisi
                                        </span>

                                    </Link>

                                </td>


                                <!-- BERKALA 2 -->

                                <td
                                    class="px-5 py-4 text-center"
                                >

                                    <Link
                                        v-if="siswa.berkala_2?.selesai"
                                        :href="route(
                                            'klinik.kesehatan.pemeriksaan.show',
                                            siswa.berkala_2.id
                                        )"
                                        class="group inline-flex min-w-[110px] flex-col items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 transition hover:border-emerald-300 hover:bg-emerald-100"
                                    >

                                        <span
                                            class="flex items-center gap-1.5 text-xs font-bold text-emerald-700"
                                        >

                                            <CheckCircleIcon
                                                class="h-4 w-4"
                                            />

                                            Selesai

                                        </span>


                                        <span
                                            v-if="siswa.berkala_2.tanggal"
                                            class="mt-0.5 text-[10px] text-emerald-600"
                                        >

                                            {{
                                                new Date(
                                                    siswa.berkala_2.tanggal
                                                ).toLocaleDateString(
                                                    'id-ID'
                                                )
                                            }}

                                        </span>

                                    </Link>


                                    <Link
                                        v-else
                                        :href="route(
                                            'klinik.kesehatan.pemeriksaan.create',
                                            {
                                                siswa: siswa.id,
                                                jenis: 2
                                            }
                                        )"
                                        class="inline-flex min-w-[110px] flex-col items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 transition hover:border-rose-300 hover:bg-rose-100"
                                    >

                                        <span
                                            class="flex items-center gap-1.5 text-xs font-bold text-rose-700"
                                        >

                                            <ExclamationTriangleIcon
                                                class="h-4 w-4"
                                            />

                                            Lengkapi

                                        </span>


                                        <span
                                            class="mt-0.5 text-[10px] text-rose-500"
                                        >
                                            Belum diisi
                                        </span>

                                    </Link>

                                </td>


                                <!-- STATUS -->

                                <td
                                    class="whitespace-nowrap px-5 py-4 text-center"
                                >

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                            getStatusClass(siswa)
                                        ]"
                                    >

                                        {{ getStatusLabel(siswa) }}

                                    </span>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-if="filteredSiswas.length === 0"
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
                                            Data siswa tidak ditemukan
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
                        v-for="(siswa, index) in filteredSiswas"
                        :key="siswa.id"
                        class="p-4"
                    >

                        <!-- SISWA -->

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
                                        siswa.nama
                                            ?.charAt(0)
                                            ?.toUpperCase()
                                    }}
                                </div>


                                <div
                                    class="min-w-0"
                                >

                                    <p
                                        class="truncate text-sm font-bold text-slate-800"
                                    >
                                        {{ siswa.nama }}
                                    </p>


                                    <p
                                        class="text-xs text-slate-400"
                                    >
                                        NISN:
                                        {{ siswa.nisn }}
                                    </p>

                                </div>

                            </div>


                            <span
                                :class="[
                                    'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    getStatusClass(siswa)
                                ]"
                            >
                                {{ getStatusLabel(siswa) }}
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
                                    Kelas
                                </p>


                                <p
                                    class="mt-0.5 font-semibold text-slate-700"
                                >
                                    {{
                                        siswa.kelas?.nama_kelas ||
                                        siswa.kelas?.tingkat ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-slate-400"
                                >
                                    Jurusan
                                </p>


                                <p
                                    class="mt-0.5 font-semibold text-slate-700"
                                >
                                    {{
                                        siswa.kelas?.jurusan
                                            ?.nama_jurusan ||
                                        '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <!-- BERKALA -->

                        <div
                            class="mt-4 grid grid-cols-2 gap-3"
                        >

                            <!-- BERKALA 1 -->

                            <Link
                                v-if="siswa.berkala_1?.selesai"
                                :href="route(
                                    'klinik.kesehatan.pemeriksaan.show',
                                    siswa.berkala_1.id
                                )"
                                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 transition hover:bg-emerald-100"
                            >

                                <p
                                    class="text-[10px] font-bold uppercase tracking-wide text-emerald-600"
                                >
                                    Berkala 1
                                </p>


                                <div
                                    class="mt-1 flex items-center gap-1.5"
                                >

                                    <CheckCircleIcon
                                        class="h-4 w-4 text-emerald-600"
                                    />


                                    <span
                                        class="text-xs font-bold text-emerald-700"
                                    >
                                        Selesai
                                    </span>

                                </div>

                            </Link>


                            <Link
                                v-else
                                :href="route(
                                    'klinik.kesehatan.pemeriksaan.create',
                                    {
                                        siswa: siswa.id,
                                        jenis: 1
                                    }
                                )"
                                class="rounded-xl border border-rose-200 bg-rose-50 p-3 transition hover:bg-rose-100"
                            >

                                <p
                                    class="text-[10px] font-bold uppercase tracking-wide text-rose-600"
                                >
                                    Berkala 1
                                </p>


                                <div
                                    class="mt-1 flex items-center gap-1.5"
                                >

                                    <ExclamationTriangleIcon
                                        class="h-4 w-4 text-rose-600"
                                    />


                                    <span
                                        class="text-xs font-bold text-rose-700"
                                    >
                                        Lengkapi
                                    </span>

                                </div>

                            </Link>


                            <!-- BERKALA 2 -->

                            <Link
                                v-if="siswa.berkala_2?.selesai"
                                :href="route(
                                    'klinik.kesehatan.pemeriksaan.show',
                                    siswa.berkala_2.id
                                )"
                                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 transition hover:bg-emerald-100"
                            >

                                <p
                                    class="text-[10px] font-bold uppercase tracking-wide text-emerald-600"
                                >
                                    Berkala 2
                                </p>


                                <div
                                    class="mt-1 flex items-center gap-1.5"
                                >

                                    <CheckCircleIcon
                                        class="h-4 w-4 text-emerald-600"
                                    />


                                    <span
                                        class="text-xs font-bold text-emerald-700"
                                    >
                                        Selesai
                                    </span>

                                </div>

                            </Link>


                            <Link
                                v-else
                                :href="route(
                                    'klinik.kesehatan.pemeriksaan.create',
                                    {
                                        siswa: siswa.id,
                                        jenis: 2
                                    }
                                )"
                                class="rounded-xl border border-rose-200 bg-rose-50 p-3 transition hover:bg-rose-100"
                            >

                                <p
                                    class="text-[10px] font-bold uppercase tracking-wide text-rose-600"
                                >
                                    Berkala 2
                                </p>


                                <div
                                    class="mt-1 flex items-center gap-1.5"
                                >

                                    <ExclamationTriangleIcon
                                        class="h-4 w-4 text-rose-600"
                                    />


                                    <span
                                        class="text-xs font-bold text-rose-700"
                                    >
                                        Lengkapi
                                    </span>

                                </div>

                            </Link>

                        </div>

                    </div>


                    <!-- MOBILE EMPTY -->

                    <div
                        v-if="filteredSiswas.length === 0"
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
                            Coba ubah kata pencarian atau
                            filter.
                        </p>

                    </div>

                </div>

            </div>

        </template>

    </div>

</KlinikLayout>

</template>
