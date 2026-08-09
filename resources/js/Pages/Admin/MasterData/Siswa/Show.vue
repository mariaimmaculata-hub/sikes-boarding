<script setup>
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    PencilSquareIcon,
    UserIcon,
    AcademicCapIcon,
    PhoneIcon,
    MapPinIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },
})

const siswa = computed(() => props.siswa)

const initial = computed(() => {
    return siswa.value.nama
        ? siswa.value.nama.charAt(0).toUpperCase()
        : 'S'
})

const jenisKelamin = computed(() => {
    return siswa.value.jenis_kelamin === 'L'
        ? 'Laki-laki'
        : 'Perempuan'
})

const statusLabel = computed(() => {
    switch (siswa.value.status) {
        case 'aktif':
            return 'Aktif'
        case 'lulus':
            return 'Lulus'
        case 'pindah':
            return 'Pindah'
        case 'nonaktif':
            return 'Nonaktif'
        default:
            return siswa.value.status || '-'
    }
})

const statusClass = computed(() => {
    switch (siswa.value.status) {
        case 'aktif':
            return 'bg-emerald-100 text-emerald-700'
        case 'lulus':
            return 'bg-blue-100 text-blue-700'
        case 'pindah':
            return 'bg-amber-100 text-amber-700'
        default:
            return 'bg-slate-100 text-slate-600'
    }
})

const formatDate = (date) => {
    if (!date) return '-'

    const value = new Date(date)

    if (Number.isNaN(value.getTime())) {
        return date
    }

    return value.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
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

                <div class="mb-2">

                    <Link
                        :href="route('admin.master.siswa.index')"
                        class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-blue-600"
                    >

                        <ArrowLeftIcon class="h-4 w-4" />

                        Kembali ke Data Siswa

                    </Link>

                </div>

                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Detail Siswa
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Informasi lengkap data siswa.
                </p>

            </div>


            <!-- EDIT -->

            <Link
                :href="route(
                    'admin.master.siswa.edit',
                    siswa.id
                )"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
            >

                <PencilSquareIcon class="h-5 w-5" />

                Edit Data

            </Link>

        </div>


        <!-- ==================================================
             PROFILE CARD
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 bg-slate-50/70 px-6 py-6"
            >

                <div
                    class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div
                        class="flex items-center gap-4"
                    >

                        <!-- AVATAR -->

                        <div
                            class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-2xl font-bold text-blue-700"
                        >
                            {{ initial }}
                        </div>


                        <!-- NAME -->

                        <div>

                            <h2
                                class="text-xl font-bold text-slate-800"
                            >
                                {{ siswa.nama }}
                            </h2>

                            <p
                                class="mt-1 text-sm text-slate-500"
                            >
                                NISN: {{ siswa.nisn }}
                            </p>

                            <div
                                class="mt-2 flex flex-wrap items-center gap-2"
                            >

                                <span
                                    class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700"
                                >
                                    {{ siswa.kelas?.nama_kelas || '-' }}
                                </span>

                                <span
                                    :class="[
                                        'rounded-full px-2.5 py-1 text-xs font-bold',
                                        statusClass
                                    ]"
                                >
                                    {{ statusLabel }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 DATA UTAMA
            ================================================== -->

            <div class="p-6">

                <div
                    class="mb-5 flex items-center gap-2"
                >

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100"
                    >

                        <UserIcon
                            class="h-4 w-4 text-blue-600"
                        />

                    </div>

                    <h3
                        class="text-sm font-bold text-slate-800"
                    >
                        Informasi Pribadi
                    </h3>

                </div>


                <div
                    class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-3"
                >

                    <!-- NISN -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            NISN
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ siswa.nisn || '-' }}
                        </p>

                    </div>


                    <!-- NAMA -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            Nama Lengkap
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ siswa.nama || '-' }}
                        </p>

                    </div>


                    <!-- JENIS KELAMIN -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            Jenis Kelamin
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ jenisKelamin }}
                        </p>

                    </div>


                    <!-- TEMPAT LAHIR -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
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
                            class="text-xs font-medium text-slate-400"
                        >
                            Tanggal Lahir
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ formatDate(siswa.tanggal_lahir) }}
                        </p>

                    </div>


                    <!-- ANGKATAN -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            Angkatan
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ siswa.angkatan || '-' }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             AKADEMIK
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >

            <div
                class="mb-5 flex items-center gap-2"
            >

                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100"
                >

                    <AcademicCapIcon
                        class="h-4 w-4 text-indigo-600"
                    />

                </div>

                <h3
                    class="text-sm font-bold text-slate-800"
                >
                    Informasi Akademik
                </h3>

            </div>


            <div
                class="grid grid-cols-1 gap-5 sm:grid-cols-2"
            >

                <!-- KELAS -->

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Kelas
                    </p>

                    <p
                        class="mt-1 text-base font-bold text-slate-800"
                    >
                        {{ siswa.kelas?.nama_kelas || '-' }}
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-500"
                    >
                        Tingkat {{ siswa.kelas?.tingkat || '-' }}
                    </p>

                </div>


                <!-- JURUSAN -->

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Jurusan
                    </p>

                    <p
                        class="mt-1 text-base font-bold text-slate-800"
                    >
                        {{
                            siswa.kelas?.jurusan
                                ?.nama_jurusan || '-'
                        }}
                    </p>

                </div>

            </div>

        </div>


        <!-- ==================================================
             KONTAK
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >

            <div
                class="mb-5 flex items-center gap-2"
            >

                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100"
                >

                    <PhoneIcon
                        class="h-4 w-4 text-emerald-600"
                    />

                </div>

                <h3
                    class="text-sm font-bold text-slate-800"
                >
                    Kontak & Alamat
                </h3>

            </div>


            <div
                class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2"
            >

                <!-- NO HP -->

                <div>

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Nomor HP
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-700"
                    >
                        {{ siswa.no_hp || '-' }}
                    </p>

                </div>


                <!-- ALAMAT -->

                <div class="sm:row-span-2">

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Alamat
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold leading-6 text-slate-700"
                    >
                        {{ siswa.alamat || '-' }}
                    </p>

                </div>


                <!-- ORANG TUA -->

                <div>

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Nama Orang Tua / Wali
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-700"
                    >
                        {{ siswa.nama_orang_tua || '-' }}
                    </p>

                </div>


                <!-- HP ORANG TUA -->

                <div>

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Nomor HP Orang Tua / Wali
                    </p>

                    <p
                        class="mt-1 text-sm font-semibold text-slate-700"
                    >
                        {{ siswa.no_hp_orang_tua || '-' }}
                    </p>

                </div>

            </div>

        </div>


        <!-- ==================================================
             PERIODE
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
        >

            <div
                class="mb-5 flex items-center gap-2"
            >

                <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100"
                >

                    <CalendarDaysIcon
                        class="h-4 w-4 text-purple-600"
                    />

                </div>

                <div>

                    <h3
                        class="text-sm font-bold text-slate-800"
                    >
                        Periode Siswa
                    </h3>

                    <p
                        class="text-xs text-slate-400"
                    >
                        Periode yang pernah diikuti siswa.
                    </p>

                </div>

            </div>


            <div
                v-if="siswa.periode?.length"
                class="flex flex-wrap gap-2"
            >

                <span
                    v-for="periode in siswa.periode"
                    :key="periode.id"
                    class="rounded-xl bg-purple-50 px-3 py-2 text-xs font-semibold text-purple-700"
                >
                    {{ periode.nama_periode || periode.nama || `Periode ${periode.id}` }}
                </span>

            </div>


            <div
                v-else
                class="rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-400"
            >
                Belum ada periode siswa.
            </div>

        </div>


        <!-- ==================================================
             RIWAYAT KESEHATAN
        ================================================== -->

        <div
            class="grid grid-cols-1 gap-6 lg:grid-cols-2"
        >

            <!-- PEMERIKSAAN BERKALA -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >

                <div
                    class="mb-5 flex items-center gap-2"
                >

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-100"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-4 w-4 text-rose-600"
                        />

                    </div>

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Pemeriksaan Berkala
                        </h3>

                        <p
                            class="text-xs text-slate-400"
                        >
                            Riwayat pemeriksaan kesehatan.
                        </p>

                    </div>

                </div>


                <div
                    v-if="siswa.pemeriksaan_berkala?.length"
                    class="space-y-3"
                >

                    <div
                        v-for="pemeriksaan in siswa.pemeriksaan_berkala.slice(0, 5)"
                        :key="pemeriksaan.id"
                        class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                    >

                        <div
                            class="flex items-center justify-between gap-3"
                        >

                            <p
                                class="text-sm font-semibold text-slate-700"
                            >
                                Pemeriksaan
                            </p>

                            <span
                                class="text-xs text-slate-400"
                            >
                                {{
                                    formatDate(
                                        pemeriksaan.tanggal_pemeriksaan
                                    )
                                }}
                            </span>

                        </div>

                    </div>

                </div>


                <div
                    v-else
                    class="rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-400"
                >
                    Belum ada riwayat pemeriksaan.
                </div>

            </div>


            <!-- KUNJUNGAN KLINIK -->

            <div
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"
            >

                <div
                    class="mb-5 flex items-center gap-2"
                >

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100"
                    >

                        <HeartIcon
                            class="h-4 w-4 text-orange-600"
                        />

                    </div>

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Kunjungan Klinik
                        </h3>

                        <p
                            class="text-xs text-slate-400"
                        >
                            Riwayat kunjungan kesehatan.
                        </p>

                    </div>

                </div>


                <div
                    v-if="siswa.kunjungan_klinik?.length"
                    class="space-y-3"
                >

                    <div
                        v-for="kunjungan in siswa.kunjungan_klinik.slice(0, 5)"
                        :key="kunjungan.id"
                        class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                    >

                        <div
                            class="flex items-center justify-between gap-3"
                        >

                            <p
                                class="text-sm font-semibold text-slate-700"
                            >
                                Kunjungan Klinik
                            </p>

                            <span
                                class="text-xs text-slate-400"
                            >
                                {{
                                    formatDate(
                                        kunjungan.tanggal_kunjungan
                                    )
                                }}
                            </span>

                        </div>

                    </div>

                </div>


                <div
                    v-else
                    class="rounded-xl bg-slate-50 px-4 py-5 text-center text-sm text-slate-400"
                >
                    Belum ada riwayat kunjungan klinik.
                </div>

            </div>

        </div>


        <!-- ==================================================
             FOOTER ACTION
        ================================================== -->

        <div
            class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
        >

            <Link
                :href="route('admin.master.siswa.index')"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >

                <ArrowLeftIcon class="h-4 w-4" />

                Kembali

            </Link>


            <Link
                :href="route(
                    'admin.master.siswa.edit',
                    siswa.id
                )"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
            >

                <PencilSquareIcon class="h-4 w-4" />

                Edit Data Siswa

            </Link>

        </div>

    </div>
</AdminLayout>

</template>