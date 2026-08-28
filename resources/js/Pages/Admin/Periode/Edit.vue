<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckIcon,
    MagnifyingGlassIcon,
    UserGroupIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


const props = defineProps({
    periode: {
        type: Object,
        required: true,
    },

    siswas: {
        type: Array,
        default: () => [],
    },
})


// ==================================================
// FORM
// ==================================================

const form = useForm({
    nama_periode: props.periode.nama_periode ?? '',
    tanggal_mulai: props.periode.tanggal_mulai
        ? String(props.periode.tanggal_mulai).substring(0, 10)
        : '',
    tanggal_selesai: props.periode.tanggal_selesai
        ? String(props.periode.tanggal_selesai).substring(0, 10)
        : '',
    status: props.periode.status ?? 'draft',

    siswa_ids: (props.periode.siswa ?? []).map(
        siswa => siswa.id
    ),
})


// ==================================================
// STATE
// ==================================================

const search = ref('')
const showStudentList = ref(true)


// ==================================================
// FILTER SISWA
// ==================================================

const filteredSiswas = computed(() => {

    const keyword = search.value
        .toLowerCase()
        .trim()

    if (!keyword) {
        return props.siswas
    }

    return props.siswas.filter(siswa => {

        const nama = String(
            siswa.nama ?? ''
        ).toLowerCase()

        const nisn = String(
            siswa.nisn ?? ''
        ).toLowerCase()

        const kelas = String(
            siswa.kelas?.nama_kelas ?? ''
        ).toLowerCase()

        const jurusan = String(
            siswa.kelas?.jurusan?.nama_jurusan ?? ''
        ).toLowerCase()

        return (
            nama.includes(keyword) ||
            nisn.includes(keyword) ||
            kelas.includes(keyword) ||
            jurusan.includes(keyword)
        )
    })
})


// ==================================================
// CHECK SISWA
// ==================================================

const isSelected = (id) => {
    return form.siswa_ids.includes(id)
}


const toggleStudent = (id) => {

    const index = form.siswa_ids.indexOf(id)

    if (index === -1) {
        form.siswa_ids.push(id)
    } else {
        form.siswa_ids.splice(index, 1)
    }
}


// ==================================================
// SELECT ALL
// ==================================================

const allFilteredSelected = computed(() => {

    if (filteredSiswas.value.length === 0) {
        return false
    }

    return filteredSiswas.value.every(
        siswa => form.siswa_ids.includes(siswa.id)
    )
})


const toggleSelectAll = () => {

    const ids = filteredSiswas.value.map(
        siswa => siswa.id
    )

    if (allFilteredSelected.value) {

        form.siswa_ids = form.siswa_ids.filter(
            id => !ids.includes(id)
        )

    } else {

        form.siswa_ids = [
            ...new Set([
                ...form.siswa_ids,
                ...ids,
            ]),
        ]

    }
}


// ==================================================
// RESET
// ==================================================

const resetForm = () => {

    form.nama_periode = props.periode.nama_periode ?? ''

    form.tanggal_mulai = props.periode.tanggal_mulai
        ? String(props.periode.tanggal_mulai).substring(0, 10)
        : ''

    form.tanggal_selesai = props.periode.tanggal_selesai
        ? String(props.periode.tanggal_selesai).substring(0, 10)
        : ''

    form.status = props.periode.status ?? 'draft'

    form.siswa_ids = (props.periode.siswa ?? []).map(
        siswa => siswa.id
    )

    search.value = ''

    form.clearErrors()
}


// ==================================================
// SUBMIT
// ==================================================

const submit = () => {

    form.put(
        route(
            'admin.periode.update',
            props.periode.id
        ),
        {
            preserveScroll: true,
            onSuccess: () => {
                // Redirect ditangani oleh controller.
            },
        }
    )
}


// ==================================================
// BREADCRUMB
// ==================================================

const breadcrumbs = [
    {
        name: 'Dashboard',
        url: route('admin.dashboard'),
    },
    {
        name: 'Periode',
        url: route('admin.periode.index'),
    },
    {
        name: 'Edit Periode',
        url: '#',
    },
]
</script>


<template>

    <Head title="Edit Periode" />

    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <div class="flex items-center gap-3">

                        <Link
                            :href="route('admin.periode.index')"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                            title="Kembali"
                        >

                            <ArrowLeftIcon
                                class="h-5 w-5"
                            />

                        </Link>


                        <div>

                            <h1
                                class="text-2xl font-bold text-slate-800"
                            >
                                Edit Periode
                            </h1>

                            <p
                                class="mt-1 text-sm text-slate-500"
                            >
                                Perbarui informasi dan peserta periode pemeriksaan kesehatan.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 ERROR VALIDATION
            ================================================== -->

            <div
                v-if="Object.keys(form.errors).length > 0"
                class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100"
                    >

                        <XMarkIcon
                            class="h-4 w-4 text-rose-600"
                        />

                    </div>


                    <div>

                        <p
                            class="text-sm font-bold text-rose-800"
                        >
                            Data belum dapat diperbarui
                        </p>

                        <p
                            class="mt-1 text-xs text-rose-600"
                        >
                            Periksa kembali bagian yang masih memiliki kesalahan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FORM
            ================================================== -->

            <form
                @submit.prevent="submit"
                class="space-y-6"
            >

                <!-- ==================================================
                     INFORMASI PERIODE
                ================================================== -->

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-pink-100"
                            >

                                <CalendarDaysIcon
                                    class="h-5 w-5 text-pink-700"
                                />

                            </div>


                            <div>

                                <h2
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Informasi Periode
                                </h2>

                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    Tentukan informasi dasar periode pemeriksaan.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
                    >

                        <!-- NAMA -->

                        <div class="md:col-span-2">

                            <label
                                for="nama_periode"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama Periode
                                <span class="text-rose-500">*</span>
                            </label>


                            <input
                                id="nama_periode"
                                v-model="form.nama_periode"
                                type="text"
                                placeholder="Contoh: Pemeriksaan Kesehatan Semester Ganjil 2026/2027"
                                maxlength="100"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.nama_periode
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />


                            <p
                                v-if="form.errors.nama_periode"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.nama_periode }}
                            </p>

                        </div>


                        <!-- TANGGAL MULAI -->

                        <div>

                            <label
                                for="tanggal_mulai"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Mulai
                                <span class="text-rose-500">*</span>
                            </label>


                            <input
                                id="tanggal_mulai"
                                v-model="form.tanggal_mulai"
                                type="date"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.tanggal_mulai
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />


                            <p
                                v-if="form.errors.tanggal_mulai"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tanggal_mulai }}
                            </p>

                        </div>


                        <!-- TANGGAL SELESAI -->

                        <div>

                            <label
                                for="tanggal_selesai"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Selesai
                                <span class="text-rose-500">*</span>
                            </label>


                            <input
                                id="tanggal_selesai"
                                v-model="form.tanggal_selesai"
                                type="date"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.tanggal_selesai
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />


                            <p
                                v-if="form.errors.tanggal_selesai"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tanggal_selesai }}
                            </p>

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label
                                for="status"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status
                                <span class="text-rose-500">*</span>
                            </label>


                            <select
                                id="status"
                                v-model="form.status"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.status
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            >

                                <option value="draft">
                                    Draft
                                </option>

                                <option value="aktif">
                                    Aktif
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                            </select>


                            <p
                                v-if="form.errors.status"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.status }}
                            </p>

                        </div>


                        <!-- INFO STATUS -->

                        <div class="flex items-end">

                            <div
                                class="w-full rounded-xl border border-pink-100 bg-pink-50 px-4 py-3"
                            >

                                <p
                                    class="text-xs font-bold text-pink-700"
                                >
                                    Status Periode
                                </p>

                                <p
                                    class="mt-0.5 text-[11px] leading-relaxed text-pink-600"
                                >
                                    Draft untuk persiapan, Aktif untuk periode
                                    berjalan, dan Selesai untuk periode yang
                                    sudah berakhir.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     PESERTA SISWA
                ================================================== -->

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <!-- HEADER -->

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100"
                                >

                                    <UserGroupIcon
                                        class="h-5 w-5 text-emerald-700"
                                    />

                                </div>


                                <div>

                                    <h2
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        Peserta Periode
                                    </h2>

                                    <p
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        Pilih siswa yang mengikuti periode pemeriksaan.
                                    </p>

                                </div>

                            </div>


                            <!-- TOTAL -->

                            <div
                                class="inline-flex w-fit items-center rounded-full bg-pink-50 px-3 py-1.5 text-xs font-bold text-pink-700"
                            >

                                {{ form.siswa_ids.length }}
                                siswa dipilih

                            </div>

                        </div>

                    </div>


                    <!-- SEARCH -->

                    <div
                        class="border-b border-slate-100 bg-slate-50/50 p-4"
                    >

                        <div class="relative">

                            <MagnifyingGlassIcon
                                class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari nama, NISN, kelas, atau jurusan..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                            />

                        </div>

                    </div>


                    <!-- SELECT ALL -->

                    <div
                        class="flex items-center justify-between border-b border-slate-100 px-5 py-3"
                    >

                        <label
                            class="flex cursor-pointer items-center gap-2 text-xs font-semibold text-slate-600"
                        >

                            <input
                                type="checkbox"
                                :checked="allFilteredSelected"
                                @change="toggleSelectAll"
                                class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                            />

                            Pilih semua yang tampil

                        </label>


                        <button
                            type="button"
                            @click="showStudentList = !showStudentList"
                            class="text-xs font-semibold text-pink-600 hover:text-pink-700"
                        >

                            {{
                                showStudentList
                                    ? 'Sembunyikan'
                                    : 'Tampilkan'
                            }}

                        </button>

                    </div>


                    <!-- STUDENT LIST -->

                    <div
                        v-if="showStudentList"
                        class="max-h-[480px] overflow-y-auto"
                    >

                        <div
                            v-if="filteredSiswas.length === 0"
                            class="px-5 py-12 text-center"
                        >

                            <div
                                class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                            >

                                <MagnifyingGlassIcon
                                    class="h-6 w-6 text-slate-400"
                                />

                            </div>


                            <p
                                class="text-sm font-bold text-slate-700"
                            >
                                Siswa tidak ditemukan
                            </p>


                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Coba ubah kata pencarian.
                            </p>

                        </div>


                        <label
                            v-for="siswa in filteredSiswas"
                            :key="siswa.id"
                            class="flex cursor-pointer items-center gap-3 border-b border-slate-100 px-5 py-3 transition hover:bg-slate-50"
                        >

                            <input
                                type="checkbox"
                                :checked="isSelected(siswa.id)"
                                @change="toggleStudent(siswa.id)"
                                class="h-4 w-4 shrink-0 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                            />


                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600"
                            >

                                {{
                                    siswa.nama
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                    || '?'
                                }}

                            </div>


                            <div class="min-w-0 flex-1">

                                <p
                                    class="truncate text-sm font-semibold text-slate-800"
                                >
                                    {{ siswa.nama }}
                                </p>

                                <div
                                    class="mt-0.5 flex flex-wrap items-center gap-x-2 text-[11px] text-slate-400"
                                >

                                    <span>
                                        NISN: {{ siswa.nisn || '-' }}
                                    </span>

                                    <span>
                                        •
                                    </span>

                                    <span>
                                        {{
                                            siswa.kelas?.nama_kelas
                                            || '-'
                                        }}
                                    </span>

                                    <span
                                        v-if="
                                            siswa.kelas?.jurusan?.nama_jurusan
                                        "
                                    >
                                        •
                                        {{
                                            siswa.kelas.jurusan.nama_jurusan
                                        }}
                                    </span>

                                </div>

                            </div>


                            <div
                                v-if="isSelected(siswa.id)"
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-pink-100"
                            >

                                <CheckIcon
                                    class="h-4 w-4 text-pink-700"
                                />

                            </div>

                        </label>

                    </div>


                    <!-- SELECTED INFO -->

                    <div
                        class="border-t border-slate-100 bg-slate-50/50 px-5 py-3"
                    >

                        <div
                            class="flex flex-col gap-1 text-xs sm:flex-row sm:items-center sm:justify-between"
                        >

                            <span class="text-slate-500">
                                Total siswa tersedia:
                                <strong class="text-slate-700">
                                    {{ siswas.length }}
                                </strong>
                            </span>

                            <span class="font-semibold text-pink-600">
                                Terpilih:
                                {{ form.siswa_ids.length }}
                                siswa
                            </span>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     ACTION
                ================================================== -->

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between"
                >

                    <Link
                        :href="route('admin.periode.index')"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >

                        <ArrowLeftIcon
                            class="h-4 w-4"
                        />

                        Batal

                    </Link>


                    <div class="flex flex-col gap-2 sm:flex-row">

                        <button
                            type="button"
                            @click="resetForm"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >

                            Reset

                        </button>


                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-800 disabled:cursor-not-allowed disabled:opacity-60"
                        >

                            <svg
                                v-if="form.processing"
                                class="h-4 w-4 animate-spin"
                                viewBox="0 0 24 24"
                                fill="none"
                            >

                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                />

                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                />

                            </svg>

                            <span>
                                {{
                                    form.processing
                                        ? 'Menyimpan...'
                                        : 'Simpan Perubahan'
                                }}
                            </span>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </AdminLayout>

</template>