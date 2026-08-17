<script setup>

import { computed, ref } from 'vue'

import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    Link,
    router,
} from '@inertiajs/vue3'

import {
    PlusIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
} from '@heroicons/vue/24/outline'


// ============================================================
// PROPS
// ============================================================

const props = defineProps({
    penyakits: {
        type: Array,
        default: () => [],
    },
})


// ============================================================
// STATE
// ============================================================

const search = ref('')

const deletingId = ref(null)


// ============================================================
// FILTER DATA
// ============================================================

const filteredPenyakits = computed(() => {

    const keyword = search.value
        .toLowerCase()
        .trim()

    if (!keyword) {
        return props.penyakits
    }

    return props.penyakits.filter((penyakit) => {

        return (

            penyakit.nama_penyakit
                ?.toLowerCase()
                .includes(keyword)

            ||

            penyakit.kategori
                ?.toLowerCase()
                .includes(keyword)

            ||

            penyakit.keterangan
                ?.toLowerCase()
                .includes(keyword)

        )

    })

})


// ============================================================
// FORMAT KETERANGAN
// ============================================================

const formatKeterangan = (keterangan) => {

    if (!keterangan) {
        return '-'
    }

    if (keterangan.length <= 70) {
        return keterangan
    }

    return `${keterangan.substring(0, 70)}...`

}


// ============================================================
// HAPUS
// ============================================================

const deletePenyakit = (penyakit) => {

    const confirmed = window.confirm(
        `Apakah Anda yakin ingin menghapus penyakit "${penyakit.nama_penyakit}"?`
    )

    if (!confirmed) {
        return
    }

    deletingId.value = penyakit.id

    router.delete(
        route(
            'klinik.penyakit.destroy',
            penyakit.id
        ),
        {
            preserveScroll: true,

            onFinish: () => {
                deletingId.value = null
            },
        }
    )

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

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600"
                    >

                        <ExclamationTriangleIcon
                            class="h-6 w-6"
                        />

                    </div>


                    <div>

                        <h1
                            class="text-xl font-bold text-slate-900 lg:text-2xl"
                        >
                            Data Penyakit
                        </h1>


                        <p
                            class="mt-0.5 text-sm text-slate-500"
                        >
                            Kelola data penyakit yang digunakan dalam pelayanan klinik.
                        </p>

                    </div>

                </div>

            </div>


            <!-- TAMBAH -->

            <Link
                :href="route('klinik.penyakit.create')"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
            >

                <PlusIcon
                    class="h-5 w-5"
                />

                Tambah Penyakit

            </Link>

        </div>


        <!-- ==================================================
             STATISTIK
        ================================================== -->

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-2"
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
                            class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                        >
                            Total Penyakit
                        </p>


                        <p
                            class="mt-1 text-2xl font-bold text-slate-900"
                        >
                            {{ props.penyakits.length }}
                        </p>

                    </div>


                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100 text-rose-600"
                    >

                        <ExclamationTriangleIcon
                            class="h-5 w-5"
                        />

                    </div>

                </div>

            </div>


            <!-- HASIL PENCARIAN -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex items-center justify-between"
                >

                    <div>

                        <p
                            class="text-xs font-semibold uppercase tracking-wider text-slate-400"
                        >
                            Hasil Pencarian
                        </p>


                        <p
                            class="mt-1 text-2xl font-bold text-slate-900"
                        >
                            {{ filteredPenyakits.length }}
                        </p>

                    </div>


                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700"
                    >

                        <MagnifyingGlassIcon
                            class="h-5 w-5"
                        />

                    </div>

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
                 TOOLBAR
            ================================================== -->

            <div
                class="border-b border-slate-200 p-4 lg:p-5"
            >

                <div
                    class="relative max-w-md"
                >

                    <MagnifyingGlassIcon
                        class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />


                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama penyakit, kategori, atau keterangan..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-500/20"
                    />

                </div>

            </div>


            <!-- ==================================================
                 TABLE
            ================================================== -->

            <div class="overflow-x-auto">

                <table
                    class="w-full text-sm"
                >


                    <!-- HEADER -->

                    <thead
                        class="border-b border-slate-200 bg-slate-50"
                    >

                        <tr>

                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                No
                            </th>


                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Nama Penyakit
                            </th>


                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Kategori
                            </th>


                            <th
                                class="px-5 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Keterangan
                            </th>


                            <th
                                class="whitespace-nowrap px-5 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <!-- BODY -->

                    <tbody
                        class="divide-y divide-slate-100"
                    >


                        <!-- DATA -->

                        <tr
                            v-for="(penyakit, index) in filteredPenyakits"
                            :key="penyakit.id"
                            class="transition hover:bg-slate-50/70"
                        >


                            <!-- NO -->

                            <td
                                class="whitespace-nowrap px-5 py-4 font-medium text-slate-500"
                            >

                                {{ index + 1 }}

                            </td>


                            <!-- NAMA -->

                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600"
                                    >

                                        <ExclamationTriangleIcon
                                            class="h-5 w-5"
                                        />

                                    </div>


                                    <div>

                                        <p
                                            class="font-semibold text-slate-800"
                                        >
                                            {{ penyakit.nama_penyakit }}
                                        </p>

                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >
                                            ID #{{ penyakit.id }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- KATEGORI -->

                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <span
                                    v-if="penyakit.kategori"
                                    class="inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700"
                                >
                                    {{ penyakit.kategori }}
                                </span>


                                <span
                                    v-else
                                    class="text-slate-400"
                                >
                                    -
                                </span>

                            </td>


                            <!-- KETERANGAN -->

                            <td
                                class="max-w-md px-5 py-4 text-slate-500"
                            >

                                <span
                                    :title="penyakit.keterangan || ''"
                                >
                                    {{ formatKeterangan(penyakit.keterangan) }}
                                </span>

                            </td>


                            <!-- AKSI -->

                            <td
                                class="px-5 py-4"
                            >

                                <div
                                    class="flex items-center justify-center gap-2"
                                >


                                    <!-- DETAIL -->

                                    <Link
                                        :href="route(
                                            'klinik.penyakit.show',
                                            penyakit.id
                                        )"
                                        title="Lihat detail"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700 transition hover:bg-blue-100"
                                    >

                                        <EyeIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>


                                    <!-- EDIT -->

                                    <Link
                                        :href="route(
                                            'klinik.penyakit.edit',
                                            penyakit.id
                                        )"
                                        title="Edit penyakit"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600 transition hover:bg-amber-100"
                                    >

                                        <PencilSquareIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>


                                    <!-- HAPUS -->

                                    <button
                                        type="button"
                                        title="Hapus penyakit"
                                        :disabled="deletingId === penyakit.id"
                                        @click="deletePenyakit(penyakit)"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-600 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50"
                                    >

                                        <TrashIcon
                                            v-if="deletingId !== penyakit.id"
                                            class="h-4 w-4"
                                        />

                                        <span
                                            v-else
                                            class="h-4 w-4 animate-spin rounded-full border-2 border-rose-300 border-t-rose-600"
                                        ></span>

                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- ==================================================
                             EMPTY
                        ================================================== -->

                        <tr
                            v-if="filteredPenyakits.length === 0"
                        >

                            <td
                                colspan="5"
                                class="px-5 py-14 text-center"
                            >

                                <div
                                    class="flex flex-col items-center"
                                >

                                    <div
                                        class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400"
                                    >

                                        <ExclamationTriangleIcon
                                            class="h-7 w-7"
                                        />

                                    </div>


                                    <p
                                        class="font-semibold text-slate-700"
                                    >

                                        {{
                                            search
                                                ? 'Data penyakit tidak ditemukan'
                                                : 'Belum ada data penyakit'
                                        }}

                                    </p>


                                    <p
                                        class="mt-1 text-sm text-slate-400"
                                    >

                                        {{
                                            search
                                                ? 'Coba gunakan kata kunci pencarian lainnya.'
                                                : 'Silakan tambahkan data penyakit terlebih dahulu.'
                                        }}

                                    </p>


                                    <!-- TAMBAH DARI EMPTY STATE -->

                                    <Link
                                        v-if="!search"
                                        :href="route('klinik.penyakit.create')"
                                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                    >

                                        <PlusIcon
                                            class="h-4 w-4"
                                        />

                                        Tambah Penyakit

                                    </Link>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- ==================================================
                 FOOTER TABLE
            ================================================== -->

            <div
                v-if="filteredPenyakits.length > 0"
                class="border-t border-slate-100 bg-slate-50/50 px-5 py-3"
            >

                <p
                    class="text-xs text-slate-400"
                >

                    Menampilkan
                    <span class="font-semibold text-slate-600">
                        {{ filteredPenyakits.length }}
                    </span>
                    data penyakit

                    <span v-if="search">
                        dari
                        <span class="font-semibold text-slate-600">
                            {{ props.penyakits.length }}
                        </span>
                        total data.
                    </span>

                </p>

            </div>

        </div>

    </div>

</KlinikLayout>

</template>
