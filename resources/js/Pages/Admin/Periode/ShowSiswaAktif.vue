<script setup>
import { Link } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClockIcon,
} from '@heroicons/vue/24/outline'


// ==================================================
// PROPS
// ==================================================

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswa: {
        type: Object,
        required: true,
    },
})


// ==================================================
// STATUS
// ==================================================

const statusLabel = (status) => {

    return status === 'selesai'
        ? 'Selesai'
        : 'Belum'
}


const statusClass = (status) => {

    return status === 'selesai'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-amber-200 bg-amber-50 text-amber-700'
}


const overallStatusLabel = (status) => {

    return status === 'lengkap'
        ? 'Pemeriksaan Lengkap'
        : 'Belum Lengkap'
}


const overallStatusClass = (status) => {

    return status === 'lengkap'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-amber-200 bg-amber-50 text-amber-700'
}


// ==================================================
// KELAS
// ==================================================

const namaKelas = (kelas) => {

    if (!kelas?.nama_kelas) {
        return '-'
    }

    return kelas.nama_kelas.replace(/\s+\d+$/, '')
}


// ==================================================
// FORMAT TANGGAL
// ==================================================

const formatTanggal = (tanggal) => {

    if (!tanggal) {
        return '-'
    }

    const value = new Date(tanggal)

    if (Number.isNaN(value.getTime())) {
        return tanggal
    }

    return value.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
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

                <!-- JUDUL + TOMBOL KEMBALI -->

                <div
                    class="flex items-center gap-3"
                >

                    <!-- TOMBOL KEMBALI -->

                    <Link
                        :href="route('admin.periode.siswa-aktif')"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                        title="Kembali"
                    >

                        <ArrowLeftIcon
                            class="h-5 w-5"
                        />

                    </Link>


                    <!-- JUDUL -->

                    <div>

                        <h1
                            class="text-2xl font-bold text-slate-800"
                        >
                            Detail Siswa
                        </h1>


                        <p
                            v-if="periode"
                            class="mt-1 text-sm text-slate-500"
                        >

                            Periode:

                            <span
                                class="font-semibold text-slate-700"
                            >
                                {{ periode.nama_periode }}
                            </span>

                        </p>

                    </div>

                </div>


                <!-- STATUS -->

                <div
                    :class="
                        overallStatusClass(
                            siswa.status
                        )
                    "
                    class="inline-flex w-fit items-center gap-2 rounded-xl border px-3 py-2 text-xs font-bold"
                >

                    <CheckCircleIcon
                        v-if="siswa.status === 'lengkap'"
                        class="h-4 w-4"
                    />

                    <ClockIcon
                        v-else
                        class="h-4 w-4"
                    />

                    {{ overallStatusLabel(siswa.status) }}

                </div>

            </div>



            <!-- ==================================================
                 IDENTITAS SISWA
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <h2
                        class="text-sm font-bold text-slate-800"
                    >
                        Identitas Siswa
                    </h2>


                    <p
                        class="mt-0.5 text-xs text-slate-400"
                    >
                        Informasi dasar siswa pada periode aktif.
                    </p>

                </div>


                <div class="p-5">

                    <div
                        class="flex flex-col gap-6 sm:flex-row sm:items-center"
                    >

                        <!-- AVATAR -->

                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-pink-100 text-2xl font-bold text-pink-700"
                        >

                            {{
                                siswa.nama
                                    ?.charAt(0)
                                    ?.toUpperCase()
                                || '?'
                            }}

                        </div>


                        <!-- DATA UTAMA -->

                        <div
                            class="min-w-0 flex-1"
                        >

                            <h3
                                class="text-xl font-bold text-slate-800"
                            >
                                {{ siswa.nama }}
                            </h3>


                            <p
                                class="mt-1 text-sm text-slate-400"
                            >

                                NISN:

                                <span
                                    class="font-semibold text-slate-600"
                                >
                                    {{ siswa.nisn || '-' }}
                                </span>

                            </p>


                            <div
                                class="mt-4 flex flex-wrap gap-2"
                            >

                                <!-- KELAS -->

                                <span
                                    class="inline-flex items-center rounded-lg bg-pink-50 px-3 py-1.5 text-xs font-bold text-pink-700"
                                >
                                    {{ namaKelas(siswa.kelas) }}
                                </span>


                                <!-- JURUSAN -->

                                <span
                                    class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-600"
                                >

                                    {{
                                        siswa.jurusan
                                            ?.nama_jurusan
                                        ?? '-'
                                    }}

                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- DATA TAMBAHAN -->

                    <div
                        class="mt-6 grid grid-cols-1 gap-4 border-t border-slate-100 pt-6 sm:grid-cols-3"
                    >

                        <!-- TEMPAT LAHIR -->

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Tempat Lahir
                            </p>


                            <p
                                class="mt-1 text-sm font-semibold text-slate-700"
                            >
                                {{ siswa.tempat_lahir || '-' }}
                            </p>

                        </div>


                        <!-- TANGGAL LAHIR -->

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Tanggal Lahir
                            </p>


                            <p
                                class="mt-1 text-sm font-semibold text-slate-700"
                            >

                                {{
                                    formatTanggal(
                                        siswa.tanggal_lahir
                                    )
                                }}

                            </p>

                        </div>


                        <!-- JENIS KELAMIN -->

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Jenis Kelamin
                            </p>


                            <p
                                class="mt-1 text-sm font-semibold text-slate-700"
                            >
                                {{ siswa.jenis_kelamin || '-' }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 STATUS PEMERIKSAAN
            ================================================== -->

            <div>

                <div class="mb-4">

                    <h2
                        class="text-sm font-bold text-slate-800"
                    >
                        Status Pemeriksaan
                    </h2>


                    <p
                        class="mt-0.5 text-xs text-slate-400"
                    >
                        Progres pemeriksaan kesehatan siswa
                        pada periode aktif.
                    </p>

                </div>


                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3"
                >


                    <!-- ==================================================
                         BERKALA 1
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-start justify-between gap-4"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pemeriksaan
                                </p>


                                <h3
                                    class="mt-1 text-base font-bold text-slate-800"
                                >
                                    Berkala 1
                                </h3>

                            </div>


                            <div
                                :class="
                                    siswa.berkala_1?.status ===
                                    'selesai'
                                        ? 'bg-emerald-50 text-emerald-600'
                                        : 'bg-amber-50 text-amber-600'
                                "
                                class="flex h-10 w-10 items-center justify-center rounded-xl"
                            >

                                <CheckCircleIcon
                                    v-if="
                                        siswa.berkala_1?.status ===
                                        'selesai'
                                    "
                                    class="h-5 w-5"
                                />


                                <ClockIcon
                                    v-else
                                    class="h-5 w-5"
                                />

                            </div>

                        </div>


                        <div class="mt-5">

                            <span
                                :class="
                                    statusClass(
                                        siswa.berkala_1?.status
                                    )
                                "
                                class="inline-flex rounded-full border px-3 py-1 text-xs font-bold"
                            >

                                {{
                                    statusLabel(
                                        siswa.berkala_1?.status
                                    )
                                }}

                            </span>

                        </div>

                    </div>



                    <!-- ==================================================
                         BERKALA 2
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-start justify-between gap-4"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pemeriksaan
                                </p>


                                <h3
                                    class="mt-1 text-base font-bold text-slate-800"
                                >
                                    Berkala 2
                                </h3>

                            </div>


                            <div
                                :class="
                                    siswa.berkala_2?.status ===
                                    'selesai'
                                        ? 'bg-emerald-50 text-emerald-600'
                                        : 'bg-amber-50 text-amber-600'
                                "
                                class="flex h-10 w-10 items-center justify-center rounded-xl"
                            >

                                <CheckCircleIcon
                                    v-if="
                                        siswa.berkala_2?.status ===
                                        'selesai'
                                    "
                                    class="h-5 w-5"
                                />


                                <ClockIcon
                                    v-else
                                    class="h-5 w-5"
                                />

                            </div>

                        </div>


                        <div class="mt-5">

                            <span
                                :class="
                                    statusClass(
                                        siswa.berkala_2?.status
                                    )
                                "
                                class="inline-flex rounded-full border px-3 py-1 text-xs font-bold"
                            >

                                {{
                                    statusLabel(
                                        siswa.berkala_2?.status
                                    )
                                }}

                            </span>

                        </div>

                    </div>



                    <!-- ==================================================
                         TKSI
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                    >

                        <div
                            class="flex items-start justify-between gap-4"
                        >

                            <div>

                                <p
                                    class="text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pemeriksaan
                                </p>


                                <h3
                                    class="mt-1 text-base font-bold text-slate-800"
                                >
                                    TKSI
                                </h3>

                            </div>


                            <div
                                :class="
                                    siswa.tksi?.status ===
                                    'selesai'
                                        ? 'bg-pink-50 text-pink-600'
                                        : 'bg-amber-50 text-amber-600'
                                "
                                class="flex h-10 w-10 items-center justify-center rounded-xl"
                            >

                                <CheckCircleIcon
                                    v-if="
                                        siswa.tksi?.status ===
                                        'selesai'
                                    "
                                    class="h-5 w-5"
                                />


                                <ClockIcon
                                    v-else
                                    class="h-5 w-5"
                                />

                            </div>

                        </div>


                        <div class="mt-5">

                            <span
                                :class="
                                    statusClass(
                                        siswa.tksi?.status
                                    )
                                "
                                class="inline-flex rounded-full border px-3 py-1 text-xs font-bold"
                            >

                                {{
                                    statusLabel(
                                        siswa.tksi?.status
                                    )
                                }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>



            <!-- ==================================================
                 PERIODE
            ================================================== -->

            <div
                v-if="periode"
                class="rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <div
                        class="flex items-center gap-2"
                    >

                        <CalendarDaysIcon
                            class="h-5 w-5 text-pink-600"
                        />


                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Periode Pemeriksaan
                        </h2>

                    </div>

                </div>


                <div
                    class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-3"
                >

                    <!-- NAMA PERIODE -->

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Nama Periode
                        </p>


                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ periode.nama_periode }}
                        </p>

                    </div>


                    <!-- TANGGAL MULAI -->

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Tanggal Mulai
                        </p>


                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >

                            {{
                                formatTanggal(
                                    periode.tanggal_mulai
                                )
                            }}

                        </p>

                    </div>


                    <!-- TANGGAL SELESAI -->

                    <div>

                        <p
                            class="text-xs font-semibold text-slate-400"
                        >
                            Tanggal Selesai
                        </p>


                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >

                            {{
                                formatTanggal(
                                    periode.tanggal_selesai
                                )
                            }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>