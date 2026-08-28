<script setup>
import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { Link, router } from '@inertiajs/vue3'

import {
    MagnifyingGlassIcon,
    EyeIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswas: {
        type: Object,
        required: true,
    },
})


// ======================================================
// STATE
// ======================================================

const search = ref('')


// ======================================================
// FILTER DATA
// ======================================================

const filteredSiswas = computed(() => {
    const data = props.siswas?.data ?? []

    if (!search.value.trim()) {
        return data
    }

    const keyword = search.value
        .toLowerCase()
        .trim()

    return data.filter((siswa) => {
        return (
            String(siswa.nisn ?? '')
                .toLowerCase()
                .includes(keyword)

            ||

            String(siswa.nama ?? '')
                .toLowerCase()
                .includes(keyword)

            ||

            String(siswa.kelas?.tingkat ?? '')
                .toLowerCase()
                .includes(keyword)

            ||

            String(siswa.kelas?.nama_kelas ?? '')
                .toLowerCase()
                .includes(keyword)

            ||

            String(
                siswa.kelas?.jurusan?.nama_jurusan ?? ''
            )
                .toLowerCase()
                .includes(keyword)
        )
    })
})


// ======================================================
// RESET SEARCH
// ======================================================

const resetSearch = () => {
    search.value = ''
}


// ======================================================
// PAGINATION
// ======================================================

const goToPage = (url) => {
    if (!url) {
        return
    }

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

    <KlinikLayout>

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <h1
                        class="text-2xl font-bold tracking-tight text-slate-800"
                    >
                        Data Siswa
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Daftar siswa yang terdaftar pada periode aktif.
                    </p>

                </div>


                <!-- PERIODE AKTIF -->

                <div
                    v-if="periode"
                    class="rounded-xl border border-pink-100 bg-pink-50 px-4 py-2.5 shadow-sm"
                >

                    <p
                        class="text-[10px] font-bold uppercase tracking-wider text-pink-500"
                    >
                        Periode Aktif
                    </p>

                    <p
                        class="mt-0.5 text-sm font-bold text-pink-800"
                    >
                        {{ periode.nama_periode }}
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 TIDAK ADA PERIODE
            ================================================== -->

            <div
                v-if="!periode"
                class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100"
                    >

                        <span
                            class="text-sm font-bold text-amber-600"
                        >
                            !
                        </span>

                    </div>


                    <div>

                        <p
                            class="text-sm font-bold text-amber-800"
                        >
                            Belum ada periode aktif
                        </p>

                        <p
                            class="mt-1 text-xs leading-relaxed text-amber-600"
                        >
                            Data siswa Klinik akan ditampilkan setelah
                            Admin mengaktifkan periode.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 DATA SISWA
            ================================================== -->

            <div
                v-else
                class="space-y-5"
            >

                <!-- ==================================================
                     SEARCH CARD
                ================================================== -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white shadow-sm"
                >

                    <div
                        class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <div>

                            <div
                                class="flex items-center gap-2"
                            >

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-pink-100"
                                >

                                    <MagnifyingGlassIcon
                                        class="h-4 w-4 text-pink-600"
                                    />

                                </div>

                                <h2
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Daftar Siswa
                                </h2>

                            </div>

                            <p
                                class="mt-1.5 text-xs text-slate-400"
                            >
                                Siswa yang terdaftar pada
                                <span class="font-semibold text-pink-600">
                                    {{ periode.nama_periode }}
                                </span>
                            </p>

                        </div>


                        <!-- SEARCH -->

                        <div class="relative w-full sm:w-80">

                            <MagnifyingGlassIcon
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari NISN, nama, kelas, atau jurusan..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-10 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                            />

                            <button
                                v-if="search"
                                type="button"
                                @click="resetSearch"
                                class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-1 text-slate-400 transition hover:bg-pink-50 hover:text-pink-600"
                                title="Hapus pencarian"
                            >

                                <XMarkIcon class="h-4 w-4" />

                            </button>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     TABLE CARD
                ================================================== -->

                <div
                    class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
                >

                    <!-- TABLE HEADER -->

                    <div
                        class="flex items-center justify-between border-b border-pink-50 px-5 py-4"
                    >

                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Data Siswa
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Menampilkan
                                <span
                                    class="font-bold text-pink-600"
                                >
                                    {{ filteredSiswas.length }}
                                </span>
                                siswa pada halaman ini.
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
                                        JK
                                    </th>

                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Aksi
                                    </th>

                                </tr>

                            </thead>


                            <tbody
                                class="divide-y divide-slate-100"
                            >

                                <tr
                                    v-for="(siswa, index) in filteredSiswas"
                                    :key="siswa.id"
                                    class="transition hover:bg-pink-50/40"
                                >

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

                                        {{ siswa.nisn || '-' }}

                                    </td>


                                    <!-- NAMA -->

                                    <td class="px-5 py-4">

                                        <div
                                            class="flex items-center gap-3"
                                        >

                                            <div
                                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-xs font-bold text-pink-700 ring-1 ring-pink-200"
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

                                                <p
                                                    class="text-xs text-slate-400"
                                                >
                                                    {{ siswa.no_hp || '-' }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>


                                    <!-- KELAS -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-700"
                                    >

                                        {{ siswa.kelas?.nama_kelas || '-' }}

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


                                    <!-- JK -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4 text-center"
                                    >

                                        <span
                                            class="inline-flex rounded-lg bg-pink-50 px-2.5 py-1 text-xs font-bold text-pink-700 ring-1 ring-pink-100"
                                        >

                                            {{
                                                siswa.jenis_kelamin === 'L'
                                                    ? 'L'
                                                    : 'P'
                                            }}

                                        </span>

                                    </td>


                                    <!-- AKSI -->

                                    <td
                                        class="whitespace-nowrap px-5 py-4"
                                    >

                                        <div
                                            class="flex items-center justify-end"
                                        >

                                            <Link
                                                :href="route(
                                                    'klinik.siswa.show',
                                                    siswa.id
                                                )"
                                                title="Detail Siswa"
                                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-pink-600 transition hover:bg-pink-50 hover:text-pink-700"
                                            >

                                                <EyeIcon
                                                    class="h-4 w-4"
                                                />

                                                Detail

                                            </Link>

                                        </div>

                                    </td>

                                </tr>


                                <!-- EMPTY -->

                                <tr
                                    v-if="filteredSiswas.length === 0"
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
                                                    class="h-6 w-6 text-pink-400"
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
                                                yang digunakan.
                                            </p>

                                            <button
                                                v-if="search"
                                                type="button"
                                                @click="resetSearch"
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
                         MOBILE CARD
                    ================================================== -->

                    <div
                        class="divide-y divide-slate-100 lg:hidden"
                    >

                        <div
                            v-for="siswa in filteredSiswas"
                            :key="siswa.id"
                            class="p-4 transition hover:bg-pink-50/30"
                        >

                            <div
                                class="flex items-start justify-between gap-3"
                            >

                                <div
                                    class="flex min-w-0 items-center gap-3"
                                >

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-pink-100 text-sm font-bold text-pink-700 ring-1 ring-pink-200"
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

                                        <p
                                            class="text-xs text-slate-400"
                                        >
                                            NISN:
                                            {{ siswa.nisn || '-' }}
                                        </p>

                                    </div>

                                </div>


                                <span
                                    class="shrink-0 rounded-lg bg-pink-50 px-2.5 py-1 text-[10px] font-bold text-pink-700 ring-1 ring-pink-100"
                                >

                                    {{
                                        siswa.jenis_kelamin === 'L'
                                            ? 'Laki-laki'
                                            : 'Perempuan'
                                    }}

                                </span>

                            </div>


                            <div
                                class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"
                            >

                                <div>

                                    <p class="text-slate-400">
                                        Kelas
                                    </p>

                                    <p
                                        class="mt-0.5 font-semibold text-slate-700"
                                    >
                                        {{
                                            siswa.kelas?.nama_kelas || '-'
                                        }}
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

                            </div>


                            <div
                                class="mt-4 flex items-center justify-end border-t border-slate-100 pt-3"
                            >

                                <Link
                                    :href="route(
                                        'klinik.siswa.show',
                                        siswa.id
                                    )"
                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-pink-600 transition hover:bg-pink-50 hover:text-pink-700"
                                >

                                    <EyeIcon class="h-4 w-4" />

                                    Detail

                                </Link>

                            </div>

                        </div>


                        <!-- MOBILE EMPTY -->

                        <div
                            v-if="filteredSiswas.length === 0"
                            class="px-5 py-16 text-center"
                        >

                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-pink-50"
                            >

                                <MagnifyingGlassIcon
                                    class="h-6 w-6 text-pink-400"
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
                                Coba ubah pencarian.
                            </p>

                        </div>

                    </div>


                    <!-- ==================================================
                         PAGINATION
                    ================================================== -->

                    <div
                        v-if="siswas.last_page > 1"
                        class="flex flex-col gap-3 border-t border-pink-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <p
                            class="text-xs font-medium text-slate-500"
                        >

                            Menampilkan

                            <span
                                class="font-bold text-slate-700"
                            >
                                {{ siswas.from ?? 0 }}
                            </span>

                            –

                            <span
                                class="font-bold text-slate-700"
                            >
                                {{ siswas.to ?? 0 }}
                            </span>

                            dari

                            <span
                                class="font-bold text-slate-700"
                            >
                                {{ siswas.total }}
                            </span>

                            siswa

                        </p>


                        <div
                            class="flex items-center gap-1"
                        >

                            <!-- PREVIOUS -->

                            <button
                                type="button"
                                :disabled="!siswas.prev_page_url"
                                @click="goToPage(siswas.prev_page_url)"
                                class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition hover:border-pink-200 hover:bg-pink-50 hover:text-pink-600 disabled:cursor-not-allowed disabled:opacity-40"
                            >

                                <ChevronLeftIcon
                                    class="h-4 w-4"
                                />

                            </button>


                            <!-- PAGE NUMBERS -->

                            <template
                                v-for="link in siswas.links.slice(1, -1)"
                                :key="link.label"
                            >

                                <button
                                    v-if="link.url"
                                    type="button"
                                    @click="goToPage(link.url)"
                                    :class="[
                                        'min-w-9 rounded-lg border px-2.5 py-2 text-xs font-bold transition',

                                        link.active
                                            ? 'border-pink-600 bg-pink-600 text-white shadow-sm'
                                            : 'border-slate-200 bg-white text-slate-600 hover:border-pink-200 hover:bg-pink-50 hover:text-pink-600'
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
                                class="rounded-lg border border-slate-200 bg-white p-2 text-slate-500 transition hover:border-pink-200 hover:bg-pink-50 hover:text-pink-600 disabled:cursor-not-allowed disabled:opacity-40"
                            >

                                <ChevronRightIcon
                                    class="h-4 w-4"
                                />

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </KlinikLayout>

</template>