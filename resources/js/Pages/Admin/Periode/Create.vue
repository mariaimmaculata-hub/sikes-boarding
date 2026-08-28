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
} from '@heroicons/vue/24/outline'

const props = defineProps({
    siswas: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    nama_periode: '',
    tanggal_mulai: '',
    tanggal_selesai: '',
    status: 'draft',
    siswa_ids: [],
})

const searchSiswa = ref('')
const selectAll = ref(false)

const filteredSiswas = computed(() => {
    const keyword = searchSiswa.value.trim().toLowerCase()

    if (!keyword) {
        return props.siswas
    }

    return props.siswas.filter((siswa) => {
        return (
            siswa.nama?.toLowerCase().includes(keyword) ||
            siswa.nisn?.toLowerCase().includes(keyword) ||
            siswa.kelas?.nama_kelas?.toLowerCase().includes(keyword) ||
            siswa.kelas?.jurusan?.nama_jurusan?.toLowerCase().includes(keyword)
        )
    })
})

const selectedCount = computed(() => {
    return form.siswa_ids.length
})

const isSelected = (id) => {
    return form.siswa_ids.includes(id)
}

const toggleSiswa = (id) => {
    if (isSelected(id)) {
        form.siswa_ids = form.siswa_ids.filter(
            (siswaId) => siswaId !== id
        )
    } else {
        form.siswa_ids.push(id)
    }

    updateSelectAllState()
}

const toggleSelectAll = () => {
    if (selectAll.value) {
        const visibleIds = filteredSiswas.value.map(
            (siswa) => siswa.id
        )

        form.siswa_ids = [
            ...new Set([
                ...form.siswa_ids,
                ...visibleIds,
            ]),
        ]
    } else {
        const visibleIds = filteredSiswas.value.map(
            (siswa) => siswa.id
        )

        form.siswa_ids = form.siswa_ids.filter(
            (id) => !visibleIds.includes(id)
        )
    }
}

const updateSelectAllState = () => {
    if (filteredSiswas.value.length === 0) {
        selectAll.value = false
        return
    }

    selectAll.value = filteredSiswas.value.every(
        (siswa) => form.siswa_ids.includes(siswa.id)
    )
}

const submit = () => {
    form.post(route('admin.periode.store'), {
        preserveScroll: true,
    })
}

const formatKelas = (siswa) => {
    const kelas = siswa.kelas?.nama_kelas
    const jurusan = siswa.kelas?.jurusan?.nama_jurusan

    if (kelas && jurusan) {
        return `${kelas} - ${jurusan}`
    }

    return kelas || jurusan || '-'
}

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
        name: 'Tambah Periode',
        url: '#',
    },
]
</script>

<template>
    <Head title="Tambah Periode" />

    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">

            <!-- HEADER -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <div class="flex items-center gap-3">

                        <Link
                            :href="route('admin.periode.index')"
                            class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-pink-600"
                        >

                            <ArrowLeftIcon class="h-5 w-5" />

                        </Link>

                        <div>

                            <h1
                                class="text-2xl font-bold text-slate-800"
                            >
                                Tambah Periode
                            </h1>

                            <p
                                class="mt-1 text-sm text-slate-500"
                            >
                                Buat periode pemeriksaan kesehatan baru.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- FORM -->

            <form
                @submit.prevent="submit"
                class="space-y-6"
            >

                <!-- DATA PERIODE -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
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
                                    Tentukan identitas dan waktu periode.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <!-- NAMA -->

                        <div class="md:col-span-2">

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama Periode
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                v-model="form.nama_periode"
                                type="text"
                                placeholder="Contoh: Pemeriksaan Kesehatan Semester Ganjil 2026/2027"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                            />

                            <p
                                v-if="form.errors.nama_periode"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.nama_periode }}
                            </p>

                        </div>


                        <!-- MULAI -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Mulai
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                v-model="form.tanggal_mulai"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                            />

                            <p
                                v-if="form.errors.tanggal_mulai"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tanggal_mulai }}
                            </p>

                        </div>


                        <!-- SELESAI -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tanggal Selesai
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                v-model="form.tanggal_selesai"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
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
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
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

                    </div>

                </div>


                <!-- SISWA -->

                <div
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
                                >

                                    <UserGroupIcon
                                        class="h-5 w-5 text-pink-700"
                                    />

                                </div>

                                <div>

                                    <h2
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        Siswa Peserta Periode
                                    </h2>

                                    <p
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        Pilih siswa yang mengikuti periode ini.
                                    </p>

                                </div>

                            </div>


                            <!-- COUNT -->

                            <div
                                class="inline-flex w-fit items-center rounded-full bg-pink-50 px-3 py-1.5 text-xs font-bold text-pink-700"
                            >

                                {{ selectedCount }} siswa dipilih

                            </div>

                        </div>

                    </div>


                    <!-- SEARCH -->

                    <div
                        class="border-b border-slate-100 bg-slate-50/70 p-4"
                    >

                        <div
                            class="flex flex-col gap-3 md:flex-row"
                        >

                            <div class="relative flex-1">

                                <MagnifyingGlassIcon
                                    class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                />

                                <input
                                    v-model="searchSiswa"
                                    @input="updateSelectAllState"
                                    type="text"
                                    placeholder="Cari nama siswa, NISN, kelas, atau jurusan..."
                                    class="w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                                />

                            </div>


                            <button
                                type="button"
                                @click="
                                    selectAll = !selectAll;
                                    toggleSelectAll();
                                "
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                            >

                                <CheckIcon class="h-4 w-4" />

                                {{
                                    selectAll
                                        ? 'Batalkan Semua'
                                        : 'Pilih Semua'
                                }}

                            </button>

                        </div>

                    </div>


                    <!-- LIST -->

                    <div class="max-h-[500px] overflow-y-auto">

                        <div
                            v-if="filteredSiswas.length === 0"
                            class="px-5 py-14 text-center"
                        >

                            <UserGroupIcon
                                class="mx-auto h-10 w-10 text-slate-300"
                            />

                            <p
                                class="mt-3 text-sm font-bold text-slate-700"
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
                            class="flex cursor-pointer items-center gap-4 border-b border-slate-100 px-5 py-3.5 transition hover:bg-slate-50"
                        >

                            <input
                                type="checkbox"
                                :value="siswa.id"
                                v-model="form.siswa_ids"
                                @change="updateSelectAllState"
                                class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                            />


                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-pink-100 text-xs font-bold text-pink-700"
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
                                    class="mt-0.5 flex flex-wrap gap-x-3 text-xs text-slate-400"
                                >

                                    <span>
                                        NISN: {{ siswa.nisn || '-' }}
                                    </span>

                                    <span>
                                        {{ formatKelas(siswa) }}
                                    </span>

                                </div>

                            </div>


                            <div
                                v-if="isSelected(siswa.id)"
                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-pink-600"
                            >

                                <CheckIcon
                                    class="h-3.5 w-3.5 text-white"
                                />

                            </div>

                        </label>

                    </div>


                    <p
                        v-if="form.errors.siswa_ids"
                        class="border-t border-rose-100 bg-rose-50 px-5 py-3 text-xs font-medium text-rose-600"
                    >
                        {{ form.errors.siswa_ids }}
                    </p>

                </div>


                <!-- ACTION -->

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end"
                >

                    <Link
                        :href="route('admin.periode.index')"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </Link>


                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-pink-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <span
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                        ></span>

                        <span>
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Periode'
                            }}
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </AdminLayout>
</template>