<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    ClipboardDocumentCheckIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline'

import TksiLayout from '@/Layouts/TksiLayout.vue'

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    statistik: {
        type: Object,
        default: () => ({
            total: 0,
            lengkap: 0,
            belum_lengkap: 0,
        }),
    },

    komponen: {
        type: Array,
        default: () => [],
    },

    flash: {
        type: Object,
        default: () => ({}),
    },
})

const filter = ref('semua')

const siswaTampil = computed(() => {
    if (filter.value === 'lengkap') {
        return props.siswa.filter(s => s.lengkap)
    }

    if (filter.value === 'belum') {
        return props.siswa.filter(s => !s.lengkap)
    }

    return props.siswa
})
</script>

<template>
    <TksiLayout>

        <Head title="Input TKSI" />

        <div class="space-y-6">

            <!-- HEADER -->
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 p-6 text-white shadow-lg md:p-8"
            >

                <div
                    class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-16 right-20 h-48 w-48 rounded-full bg-white/5"
                ></div>

                <div class="relative z-10">

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10"
                        >
                            <ClipboardDocumentCheckIcon class="h-6 w-6" />
                        </div>

                        <div>

                            <h1 class="text-2xl font-bold tracking-tight">
                                Input TKSI
                            </h1>

                            <p class="mt-1 max-w-2xl text-sm font-medium text-white/80">
                                Kelola hasil tes kebugaran siswa pada periode aktif.
                            </p>

                        </div>

                    </div>

                </div>
            </div>


            <!-- FLASH SUCCESS -->
            <div
                v-if="flash.success"
                class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4"
            >

                <CheckCircleIcon
                    class="h-5 w-5 shrink-0 text-emerald-600"
                />

                <p class="text-sm font-bold text-emerald-700">
                    {{ flash.success }}
                </p>

            </div>


            <!-- TIDAK ADA PERIODE -->
            <div
                v-if="!periode"
                class="rounded-2xl border border-rose-100 bg-rose-50 p-6"
            >

                <div class="flex items-start gap-3">

                    <ExclamationCircleIcon
                        class="h-6 w-6 shrink-0 text-rose-600"
                    />

                    <div>

                        <h2 class="text-sm font-extrabold text-rose-700">
                            Tidak ada periode aktif
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-rose-600">
                            Input TKSI belum dapat dilakukan sampai admin
                            mengaktifkan periode.
                        </p>

                    </div>

                </div>

            </div>


            <template v-if="periode">

                <!-- PERIODE AKTIF -->
                <div
                    class="rounded-2xl border border-blue-100 bg-blue-50 p-5"
                >

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700"
                        >
                            <CalendarDaysIcon class="h-5 w-5" />
                        </div>

                        <div class="min-w-0">

                            <p
                                class="text-[10px] font-extrabold uppercase tracking-wider text-blue-500"
                            >
                                Periode Aktif
                            </p>

                            <h2
                                class="mt-1 text-base font-extrabold text-blue-900"
                            >
                                {{ periode.nama_periode }}
                            </h2>

                            <p class="mt-1 text-xs font-medium text-blue-700">
                                {{ periode.tanggal_mulai }}
                                —
                                {{ periode.tanggal_selesai }}
                            </p>

                        </div>

                        <span
                            class="ml-auto rounded-full bg-emerald-100 px-3 py-1 text-[9px] font-extrabold uppercase text-emerald-700"
                        >
                            Aktif
                        </span>

                    </div>

                </div>


                <!-- STATISTIK -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <!-- TOTAL -->
                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                            >
                                <UserGroupIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                    Total Peserta
                                </p>

                                <p class="mt-1 text-2xl font-extrabold text-slate-800">
                                    {{ statistik.total }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- LENGKAP -->
                    <div
                        class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"
                            >
                                <CheckCircleIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600">
                                    Sudah Lengkap
                                </p>

                                <p class="mt-1 text-2xl font-extrabold text-emerald-800">
                                    {{ statistik.lengkap }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- BELUM LENGKAP -->
                    <div
                        class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                            >
                                <ExclamationCircleIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600">
                                    Belum Lengkap
                                </p>

                                <p class="mt-1 text-2xl font-extrabold text-amber-800">
                                    {{ statistik.belum_lengkap }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- DAFTAR PESERTA -->
                <div
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm"
                >

                    <!-- HEADER DAFTAR -->
                    <div
                        class="border-b border-slate-100 px-6 py-5"
                    >

                        <div
                            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"
                        >

                            <div>

                                <h2 class="text-sm font-extrabold text-slate-800">
                                    Daftar Peserta TKSI
                                </h2>

                                <p class="mt-1 text-[10px] font-medium text-slate-400">
                                    Status pengisian hasil TKSI pada periode aktif.
                                </p>

                            </div>


                            <!-- FILTER -->
                            <div
                                class="flex rounded-xl bg-slate-100 p-1"
                            >

                                <button
                                    type="button"
                                    @click="filter = 'semua'"
                                    :class="[
                                        'rounded-lg px-3 py-2 text-[10px] font-extrabold transition',
                                        filter === 'semua'
                                            ? 'bg-white text-blue-900 shadow-sm'
                                            : 'text-slate-500 hover:text-slate-700'
                                    ]"
                                >
                                    Semua
                                </button>

                                <button
                                    type="button"
                                    @click="filter = 'belum'"
                                    :class="[
                                        'rounded-lg px-3 py-2 text-[10px] font-extrabold transition',
                                        filter === 'belum'
                                            ? 'bg-white text-amber-700 shadow-sm'
                                            : 'text-slate-500 hover:text-slate-700'
                                    ]"
                                >
                                    Belum Lengkap
                                </button>

                                <button
                                    type="button"
                                    @click="filter = 'lengkap'"
                                    :class="[
                                        'rounded-lg px-3 py-2 text-[10px] font-extrabold transition',
                                        filter === 'lengkap'
                                            ? 'bg-white text-emerald-700 shadow-sm'
                                            : 'text-slate-500 hover:text-slate-700'
                                    ]"
                                >
                                    Sudah Lengkap
                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- DATA SISWA -->
                    <div
                        v-if="siswaTampil.length"
                        class="divide-y divide-slate-100"
                    >

                        <div
                            v-for="student in siswaTampil"
                            :key="student.id"
                            class="flex flex-col gap-4 px-6 py-5 transition hover:bg-slate-50 md:flex-row md:items-center md:justify-between"
                        >

                            <!-- IDENTITAS -->
                            <div class="flex min-w-0 items-center gap-4">

                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-extrabold text-blue-700"
                                >
                                    {{ student.nama?.charAt(0)?.toUpperCase() }}
                                </div>

                                <div class="min-w-0">

                                    <h3
                                        class="truncate text-sm font-extrabold text-slate-800"
                                    >
                                        {{ student.nama }}
                                    </h3>

                                    <div
                                        class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-semibold text-slate-400"
                                    >

                                        <span>
                                            NISN: {{ student.nisn || '-' }}
                                        </span>

                                        <span>
                                            Kelas:
                                            {{ student.kelas?.nama_kelas || '-' }}
                                        </span>

                                        <span>
                                            {{ student.jenis_kelamin || '-' }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- STATUS + AKSI -->
                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center"
                            >

                                <!-- STATUS LENGKAP -->
                                <div
                                    v-if="student.lengkap"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-extrabold text-emerald-700"
                                >

                                    <CheckCircleIcon class="h-3.5 w-3.5" />

                                    Sudah Lengkap

                                </div>


                                <!-- STATUS BELUM -->
                                <div
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-[10px] font-extrabold text-amber-700"
                                >

                                    <ExclamationCircleIcon class="h-3.5 w-3.5" />

                                    Belum Lengkap
                                    ({{ student.jumlah_hasil }}/{{ student.total_komponen }})

                                </div>


                                <!-- AKSI -->
                                <Link
                                    :href="route('tksi.input.create', {
                                        siswa: student.id
                                    })"
                                    class="inline-flex items-center justify-center gap-1 rounded-xl border border-slate-200 px-4 py-2.5 text-[10px] font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                >

                                    {{ student.lengkap
                                        ? 'Edit Hasil'
                                        : 'Input Hasil'
                                    }}

                                    <ArrowRightIcon class="h-3.5 w-3.5" />

                                </Link>

                            </div>

                        </div>

                    </div>


                    <!-- KOSONG -->
                    <div
                        v-else
                        class="px-6 py-12 text-center"
                    >

                        <ClipboardDocumentCheckIcon
                            class="mx-auto h-10 w-10 text-slate-300"
                        />

                        <p class="mt-3 text-sm font-bold text-slate-400">
                            Tidak ada data siswa
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Belum ada siswa yang terdaftar pada periode aktif.
                        </p>

                    </div>

                </div>

            </template>

        </div>

    </TksiLayout>
</template>

