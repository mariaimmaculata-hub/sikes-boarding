<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    data: {
        type: Array,
        default: () => [],
    },

    periodes: {
        type: Array,
        default: () => [],
    },

    komponenOptions: {
        type: Array,
        default: () => [],
    },

    statistik: {
        type: Object,
        default: () => ({
            total_siswa: 0,
            total_hasil: 0,
            jumlah_komponen: 0,
            siswa_lengkap: 0,
        }),
    },

    filters: {
        type: Object,
        default: () => ({
            periode_id: null,
        }),
    },
})

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

const periodeId = computed({
    get() {
        return props.filters?.periode_id ?? ''
    },

    set(value) {
        router.get(
            '/tksi/report',
            {
                periode_id: value || null,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        )
    },
})

/*
|--------------------------------------------------------------------------
| FORMAT NILAI
|--------------------------------------------------------------------------
*/

const formatNilai = (nilai) => {
    if (
        nilai === null ||
        nilai === undefined ||
        nilai === ''
    ) {
        return '-'
    }

    return nilai
}

/*
|--------------------------------------------------------------------------
| FORMAT RATA-RATA
|--------------------------------------------------------------------------
*/

const formatRataRata = (nilai) => {
    if (
        nilai === null ||
        nilai === undefined ||
        nilai === ''
    ) {
        return '-'
    }

    const number = Number(nilai)

    if (Number.isNaN(number)) {
        return '-'
    }

    return number.toFixed(2)
}

/*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

const exportExcel = () => {
    const params = new URLSearchParams()

    if (periodeId.value) {
        params.append('periode_id', periodeId.value)
    }

    window.location.href =
        `/tksi/report/excel?${params.toString()}`
}

const exportPdf = () => {
    const params = new URLSearchParams()

    if (periodeId.value) {
        params.append('periode_id', periodeId.value)
    }

    window.location.href =
        `/tksi/report/pdf?${params.toString()}`
}

/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

const resetFilter = () => {
    router.get(
        '/tksi/report',
        {},
        {
            preserveState: false,
            preserveScroll: false,
        }
    )
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">

        <!-- =========================================================
             HEADER
        ========================================================== -->

        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Laporan TKSI
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Laporan hasil Tes Kebugaran dan Satuan Individu
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                <!-- EXPORT EXCEL -->
                <button
                    type="button"
                    @click="exportExcel"
                    class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-green-700"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8v11a2 2 0 01-2 2z"
                        />
                    </svg>

                    Excel
                </button>

                <!-- EXPORT PDF -->
                <button
                    type="button"
                    @click="exportPdf"
                    class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-red-700"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.586-1.414l-5.414-5.414A2 2 0 0011.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6M9 17h6M9 9h2"
                        />
                    </svg>

                    PDF
                </button>

            </div>
        </div>


        <!-- =========================================================
             FILTER
        ========================================================== -->

        <div class="mb-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                <!-- PERIODE -->
                <div>
                    <label
                        for="periode"
                        class="mb-2 block text-sm font-medium text-gray-700"
                    >
                        Periode
                    </label>

                    <select
                        id="periode"
                        v-model="periodeId"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
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
                </div>

                <!-- JUMLAH KOMPONEN -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Komponen
                    </label>

                    <div
                        class="flex h-[42px] items-center rounded-lg border border-gray-200 bg-gray-50 px-3 text-sm text-gray-600"
                    >
                        {{ komponenOptions.length }} Komponen
                    </div>
                </div>

                <!-- RESET -->
                <div class="flex items-end">

                    <button
                        type="button"
                        @click="resetFilter"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                    >
                        Reset Filter
                    </button>

                </div>

            </div>

        </div>


        <!-- =========================================================
             STATISTIK
        ========================================================== -->

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <!-- TOTAL SISWA -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Total Siswa
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-800">
                            {{ statistik.total_siswa ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-blue-100 p-3 text-blue-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                            />
                        </svg>
                    </div>

                </div>
            </div>


            <!-- TOTAL HASIL -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Total Hasil
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-800">
                            {{ statistik.total_hasil ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-green-100 p-3 text-green-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>

                </div>
            </div>


            <!-- JUMLAH KOMPONEN -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Komponen TKSI
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-800">
                            {{ statistik.jumlah_komponen ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-purple-100 p-3 text-purple-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a3 3 0 006 0M9 5h6"
                            />
                        </svg>
                    </div>

                </div>
            </div>


            <!-- SISWA LENGKAP -->
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm text-gray-500">
                            Siswa Lengkap
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-800">
                            {{ statistik.siswa_lengkap ?? 0 }}
                        </p>
                    </div>

                    <div class="rounded-lg bg-orange-100 p-3 text-orange-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A9.955 9.955 0 0112 3c-1.657 0-3.224.402-4.618 1.102M4.27 7.27A9.956 9.956 0 003 12c0 4.97 4.03 9 9 9 1.657 0 3.224-.402 4.618-1.102"
                            />
                        </svg>
                    </div>

                </div>
            </div>

        </div>


        <!-- =========================================================
             TABLE
        ========================================================== -->

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <!-- TABLE HEADER -->

            <div class="border-b border-gray-200 px-5 py-4">

                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">
                            Hasil TKSI Siswa
                        </h2>

                        <p class="text-sm text-gray-500">
                            Setiap siswa ditampilkan dalam satu baris dengan seluruh komponen TKSI.
                        </p>
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ data.length }} siswa
                    </div>

                </div>

            </div>


            <!-- TABLE -->

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50">

                        <tr class="border-b border-gray-200">

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700"
                            >
                                No
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700"
                            >
                                NISN
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700"
                            >
                                Nama Siswa
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-left font-semibold text-gray-700"
                            >
                                Kelas
                            </th>

                            <th
                                v-for="namaKomponen in komponenOptions"
                                :key="namaKomponen"
                                class="whitespace-nowrap px-4 py-3 text-center font-semibold text-gray-700"
                            >
                                {{ namaKomponen }}
                            </th>

                            <th
                                class="whitespace-nowrap px-4 py-3 text-center font-semibold text-gray-700"
                            >
                                Rata-rata
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        <!-- DATA -->

                        <tr
                            v-for="(item, index) in data"
                            :key="item.siswa_id"
                            class="transition hover:bg-gray-50"
                        >

                            <!-- NO -->

                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ index + 1 }}
                            </td>


                            <!-- NISN -->

                            <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-800">
                                {{ item.siswa?.nisn ?? '-' }}
                            </td>


                            <!-- NAMA -->

                            <td class="whitespace-nowrap px-4 py-3 text-gray-800">
                                {{ item.siswa?.nama ?? '-' }}
                            </td>


                            <!-- KELAS -->

                            <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                {{ item.siswa?.kelas?.nama_kelas ?? '-' }}
                            </td>


                            <!-- 6 KOMPONEN -->

                            <td
                                v-for="namaKomponen in komponenOptions"
                                :key="namaKomponen"
                                class="whitespace-nowrap px-4 py-3 text-center"
                            >

                                <span
                                    v-if="item.komponen?.[namaKomponen]"
                                    class="font-medium text-gray-800"
                                >
                                    {{ formatNilai(
                                        item.komponen[namaKomponen].nilai
                                    ) }}
                                </span>

                                <span
                                    v-else
                                    class="text-gray-400"
                                >
                                    -
                                </span>

                            </td>


                            <!-- RATA-RATA -->

                            <td
                                class="whitespace-nowrap px-4 py-3 text-center font-semibold text-blue-600"
                            >
                                {{ formatRataRata(item.rata_rata) }}
                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr v-if="data.length === 0">

                            <td
                                :colspan="5 + komponenOptions.length"
                                class="px-6 py-12 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="mb-3 rounded-full bg-gray-100 p-4">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-8 w-8 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 13h6m-3-3v6m9-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>

                                    </div>

                                    <p class="font-medium text-gray-700">
                                        Belum ada data TKSI
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Belum terdapat hasil pemeriksaan untuk filter yang dipilih.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- FOOTER -->

            <div
                v-if="data.length > 0"
                class="border-t border-gray-200 bg-gray-50 px-5 py-3"
            >

                <div class="flex flex-col gap-2 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">

                    <span>
                        Menampilkan
                        <strong class="font-semibold text-gray-700">
                            {{ data.length }}
                        </strong>
                        siswa
                    </span>

                    <span>
                        {{ komponenOptions.length }} komponen per siswa
                    </span>

                </div>

            </div>

        </div>

    </div>
</template>
