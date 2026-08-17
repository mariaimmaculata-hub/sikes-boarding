<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue';
import { Link } from '@inertiajs/vue3';

import {
    ArrowLeftIcon,
    UserIcon,
    AcademicCapIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
    BuildingOffice2Icon,
    PhoneIcon,
    IdentificationIcon,
    MapPinIcon,
    UserCircleIcon,
    ClockIcon,
    DocumentTextIcon,
    BeakerIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },

    periode: {
        type: Object,
        default: null,
    },
});


// ======================================================
// FORMAT DATE
// ======================================================

const formatDate = (date) => {
    if (!date) {
        return '-';
    }

    return new Date(date).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
};


// ======================================================
// FORMAT DATETIME
// ======================================================

const formatDateTime = (date) => {
    if (!date) {
        return '-';
    }

    return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};


// ======================================================
// JENIS KELAMIN
// ======================================================

const formatJenisKelamin = (jenisKelamin) => {
    if (jenisKelamin === 'L') {
        return 'Laki-laki';
    }

    if (jenisKelamin === 'P') {
        return 'Perempuan';
    }

    return '-';
};


// ======================================================
// STATUS PEMERIKSAAN
// ======================================================

const pemeriksaanStatusClass = (status) => {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-100 text-emerald-700';

        case 'belum':
        case 'belum_selesai':
            return 'bg-amber-100 text-amber-700';

        case 'tidak_hadir':
            return 'bg-rose-100 text-rose-700';

        default:
            return 'bg-slate-100 text-slate-600';
    }
};


// ======================================================
// STATUS KUNJUNGAN
// ======================================================

const kunjunganStatusClass = (status) => {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-100 text-emerald-700';

        case 'dirujuk':
            return 'bg-rose-100 text-rose-700';

        case 'dalam_perawatan':
            return 'bg-amber-100 text-amber-700';

        default:
            return 'bg-slate-100 text-slate-600';
    }
};


// ======================================================
// LABEL STATUS
// ======================================================

const formatStatus = (status) => {
    if (!status) {
        return '-';
    }

    return String(status)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase());
};


// ======================================================
// PEMERIKSAAN BERKALA
// ======================================================

const pemeriksaan = () => {
    return props.siswa.pemeriksaan_berkala
        ?? props.siswa.pemeriksaanBerkala
        ?? [];
};


// ======================================================
// PEMERIKSAAN 1
// ======================================================
const pemeriksaan1 = () => {
    const data = pemeriksaan();

    return data.find(
        (item) =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_1'
    ) ?? null;
};




// ======================================================
// PEMERIKSAAN 2
// ======================================================

const pemeriksaan2 = () => {
    const data = pemeriksaan();

    return data.find(
        (item) =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_2'
    ) ?? null;
};

// ======================================================
// INITIAL
// ======================================================

const initial = props.siswa.nama
    ? props.siswa.nama.charAt(0).toUpperCase()
    : '?';

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

            <div class="flex items-center gap-3">

                <Link
                    :href="route('klinik.siswa.index')"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-blue-600"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>

                <div>

                  

                    <h1
                        class="mt-1 text-2xl font-bold text-slate-800"
                    >
                        Detail Siswa
                    </h1>

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >
                        Informasi lengkap dan riwayat kesehatan siswa.
                    </p>

                </div>

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
             PROFILE SISWA
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 bg-slate-50 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-700"
                    >
                        {{ initial }}
                    </div>

                    <div>

                        <h2
                            class="text-base font-bold text-slate-800"
                        >
                            {{ siswa.nama || '-' }}
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            NISN: {{ siswa.nisn || '-' }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- DATA PRIBADI -->

            <div class="p-5">

                <div
                    class="mb-4 flex items-center gap-2"
                >

                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <UserIcon class="h-4 w-4 text-blue-600" />
                    </div>

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Data Pribadi
                        </h3>

                        <p
                            class="text-xs text-slate-400"
                        >
                            Informasi identitas siswa
                        </p>

                    </div>

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


                    <!-- NIK -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            NIK
                        </p>

                        <p
                            class="mt-1 text-sm font-semibold text-slate-700"
                        >
                            {{ siswa.nik || '-' }}
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
                            {{ formatJenisKelamin(siswa.jenis_kelamin) }}
                        </p>

                    </div>


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


                    <!-- EMAIL -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            Email
                        </p>

                        <p
                            class="mt-1 break-all text-sm font-semibold text-slate-700"
                        >
                            {{ siswa.email || '-' }}
                        </p>

                    </div>


                    <!-- STATUS -->

                    <div>

                        <p
                            class="text-xs font-medium text-slate-400"
                        >
                            Status Siswa
                        </p>

                        <div class="mt-1">

                            <span
                                :class="[
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                    siswa.status === 'aktif'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-slate-100 text-slate-500'
                                ]"
                            >
                                {{
                                    siswa.status === 'aktif'
                                        ? 'Aktif'
                                        : formatStatus(siswa.status)
                                }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ==================================================
             AKADEMIK
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50"
                    >
                        <AcademicCapIcon
                            class="h-5 w-5 text-blue-600"
                        />
                    </div>

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Informasi Akademik
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Data kelas dan angkatan siswa
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4"
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
                        class="mt-1 text-sm font-bold text-slate-800"
                    >
                        {{ siswa.kelas?.nama_kelas || '-' }}
                    </p>

                </div>


                <!-- TINGKAT -->

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Tingkat
                    </p>

                    <p
                        class="mt-1 text-sm font-bold text-slate-800"
                    >
                        {{ siswa.kelas?.tingkat || '-' }}
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
                        class="mt-1 text-sm font-bold text-slate-800"
                    >
                        {{
                            siswa.kelas?.jurusan?.nama_jurusan
                            || '-'
                        }}
                    </p>

                </div>


                <!-- ANGKATAN -->

                <div
                    class="rounded-xl border border-slate-100 bg-slate-50 p-4"
                >

                    <p
                        class="text-xs font-medium text-slate-400"
                    >
                        Angkatan
                    </p>

                    <p
                        class="mt-1 text-sm font-bold text-slate-800"
                    >
                        {{ siswa.angkatan || '-' }}
                    </p>

                </div>

            </div>

        </div>



        <!-- ==================================================
             PEMERIKSAAN BERKALA
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center justify-between gap-4">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50"
                        >
                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-rose-500"
                            />
                        </div>

                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Pemeriksaan Berkala
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Riwayat pemeriksaan kesehatan pada periode aktif
                            </p>

                        </div>

                    </div>

                    <span
                        class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700"
                    >
                        2 Pemeriksaan
                    </span>

                </div>

            </div>


            <div class="p-5">

                <div
                    class="grid grid-cols-1 gap-5 lg:grid-cols-2"
                >

                    <!-- ==========================================
                         PEMERIKSAAN 1
                    =========================================== -->

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200"
                    >

                        <div
                            class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100 text-sm font-bold text-blue-700"
                                >
                                    1
                                </div>

                                <div>

                                    <h3
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        Pemeriksaan 1
                                    </h3>

                                    <p
                                        class="text-[11px] text-slate-400"
                                    >
                                        Pemeriksaan berkala pertama
                                    </p>

                                </div>

                            </div>


                            <span
                                v-if="pemeriksaan1()?.status"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    pemeriksaanStatusClass(
                                        pemeriksaan1()?.status
                                    )
                                ]"
                            >
                                {{
                                    formatStatus(
                                        pemeriksaan1()?.status
                                    )
                                }}
                            </span>

                        </div>


                        <div
                            v-if="pemeriksaan1()"
                            class="space-y-4 p-5"
                        >

                            <!-- JENIS -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Jenis Pemeriksaan
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-slate-700"
                                >
                                    {{
                                        pemeriksaan1()
                                            ?.jenis_pemeriksaan || '-'
                                    }}
                                </p>

                            </div>


                            <!-- TANGGAL -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Tanggal Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 flex items-center gap-2 text-sm font-semibold text-slate-700"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 text-slate-400"
                                    />

                                    {{
                                        formatDate(
                                            pemeriksaan1()
                                                ?.tanggal_pemeriksaan
                                        )
                                    }}

                                </div>

                            </div>


                            <!-- HASIL -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Hasil Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                                >
                                    {{
    pemeriksaan1()?.hasil_pemeriksaan || '-'
}}
                                </div>

                            </div>


                            <!-- CATATAN -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Catatan
                                </p>

                                <p
                                    class="mt-1 text-sm leading-relaxed text-slate-600"
                                >
                                    {{
                                        pemeriksaan1()?.catatan || '-'
                                    }}
                                </p>

                            </div>


                            <!-- PEMERIKSA -->

                            <div
                                class="border-t border-slate-100 pt-4"
                            >

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Pemeriksa
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-slate-700"
                                >
                                    {{
                                        pemeriksaan1()?.pemeriksa?.name
                                        || pemeriksaan1()?.pemeriksa?.nama
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <!-- BELUM ADA -->

                        <div
                            v-else
                            class="p-8 text-center"
                        >

                            <div
                                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100"
                            >
                                <ClipboardDocumentCheckIcon
                                    class="h-5 w-5 text-slate-400"
                                />
                            </div>

                            <p
                                class="mt-3 text-sm font-semibold text-slate-500"
                            >
                                Belum ada pemeriksaan
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Pemeriksaan pertama belum dilakukan.
                            </p>

                        </div>

                    </div>



                    <!-- ==========================================
                         PEMERIKSAAN 2
                    =========================================== -->

                    <div
                        class="overflow-hidden rounded-2xl border border-slate-200"
                    >

                        <div
                            class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-5 py-4"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-100 text-sm font-bold text-purple-700"
                                >
                                    2
                                </div>

                                <div>

                                    <h3
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        Pemeriksaan 2
                                    </h3>

                                    <p
                                        class="text-[11px] text-slate-400"
                                    >
                                        Pemeriksaan berkala kedua
                                    </p>

                                </div>

                            </div>


                            <span
                                v-if="pemeriksaan2()?.status"
                                :class="[
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    pemeriksaanStatusClass(
                                        pemeriksaan2()?.status
                                    )
                                ]"
                            >
                                {{
                                    formatStatus(
                                        pemeriksaan2()?.status
                                    )
                                }}
                            </span>

                        </div>


                        <div
                            v-if="pemeriksaan2()"
                            class="space-y-4 p-5"
                        >

                            <!-- JENIS -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Jenis Pemeriksaan
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-slate-700"
                                >
                                    {{
                                        pemeriksaan2()
                                            ?.jenis_pemeriksaan || '-'
                                    }}
                                </p>

                            </div>


                            <!-- TANGGAL -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Tanggal Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 flex items-center gap-2 text-sm font-semibold text-slate-700"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 text-slate-400"
                                    />

                                    {{
                                        formatDate(
                                            pemeriksaan2()
                                                ?.tanggal_pemeriksaan
                                        )
                                    }}

                                </div>

                            </div>


                            <!-- HASIL -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Hasil Pemeriksaan
                                </p>

                                <div
                                    class="mt-1 rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                                >
                                    {{
    pemeriksaan2()?.hasil_pemeriksaan || '-'
}}
                                </div>

                            </div>


                            <!-- CATATAN -->

                            <div>

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Catatan
                                </p>

                                <p
                                    class="mt-1 text-sm leading-relaxed text-slate-600"
                                >
                                    {{
                                        pemeriksaan2()?.catatan || '-'
                                    }}
                                </p>

                            </div>


                            <!-- PEMERIKSA -->

                            <div
                                class="border-t border-slate-100 pt-4"
                            >

                                <p
                                    class="text-xs text-slate-400"
                                >
                                    Pemeriksa
                                </p>

                                <p
                                    class="mt-1 text-sm font-semibold text-slate-700"
                                >
                                    {{
                                        pemeriksaan2()?.pemeriksa?.name
                                        || pemeriksaan2()?.pemeriksa?.nama
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <!-- BELUM ADA -->

                        <div
                            v-else
                            class="p-8 text-center"
                        >

                            <div
                                class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100"
                            >
                                <ClipboardDocumentCheckIcon
                                    class="h-5 w-5 text-slate-400"
                                />
                            </div>

                            <p
                                class="mt-3 text-sm font-semibold text-slate-500"
                            >
                                Belum ada pemeriksaan
                            </p>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Pemeriksaan kedua belum dilakukan.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ==================================================
             KUNJUNGAN KLINIK
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-lg bg-orange-50"
                    >
                        <HeartIcon
                            class="h-5 w-5 text-orange-500"
                        />
                    </div>

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Riwayat Kunjungan Klinik
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Seluruh kunjungan klinik pada periode aktif
                        </p>

                    </div>

                </div>

            </div>


            <!-- TIDAK ADA KUNJUNGAN -->

            <div
                v-if="!siswa.kunjunganKlinik?.length"
                class="px-5 py-14 text-center"
            >

                <div
                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                >

                    <HeartIcon
                        class="h-6 w-6 text-slate-400"
                    />

                </div>

                <p
                    class="mt-3 text-sm font-bold text-slate-700"
                >
                    Belum ada kunjungan klinik
                </p>

                <p
                    class="mt-1 text-xs text-slate-400"
                >
                    Siswa belum memiliki riwayat kunjungan pada periode ini.
                </p>

            </div>


            <!-- LIST KUNJUNGAN -->

            <div
                v-else
                class="divide-y divide-slate-100"
            >

                <div
                    v-for="(kunjungan, index) in siswa.kunjunganKlinik"
                    :key="kunjungan.id"
                    class="p-5"
                >

                    <!-- HEADER KUNJUNGAN -->

                    <div
                        class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                    >

                        <div class="flex items-start gap-3">

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50"
                            >
                                <HeartIcon
                                    class="h-5 w-5 text-orange-500"
                                />
                            </div>

                            <div>

                                <div
                                    class="flex flex-wrap items-center gap-2"
                                >

                                    <h3
                                        class="text-sm font-bold text-slate-800"
                                    >
                                        Kunjungan Klinik #{{ index + 1 }}
                                    </h3>

                                    <span
                                        :class="[
                                            'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                            kunjunganStatusClass(
                                                kunjungan.status
                                            )
                                        ]"
                                    >
                                        {{
                                            formatStatus(
                                                kunjungan.status
                                            )
                                        }}
                                    </span>

                                </div>

                                <div
                                    class="mt-1 flex items-center gap-1.5 text-xs text-slate-400"
                                >

                                    <CalendarDaysIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    {{
                                        formatDateTime(
                                            kunjungan.tanggal_kunjungan
                                        )
                                    }}

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- DETAIL KUNJUNGAN -->

                    <div
                        class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2"
                    >

                        <!-- KELUHAN -->

                        <div>

                            <div
                                class="mb-1.5 flex items-center gap-2"
                            >

                                <DocumentTextIcon
                                    class="h-4 w-4 text-slate-400"
                                />

                                <p
                                    class="text-xs font-bold text-slate-500"
                                >
                                    Keluhan
                                </p>

                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                            >
                                {{ kunjungan.keluhan || '-' }}
                            </div>

                        </div>


                        <!-- PEMERIKSAAN -->

                        <div>

                            <div
                                class="mb-1.5 flex items-center gap-2"
                            >

                                <ClipboardDocumentCheckIcon
                                    class="h-4 w-4 text-slate-400"
                                />

                                <p
                                    class="text-xs font-bold text-slate-500"
                                >
                                    Pemeriksaan
                                </p>

                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                            >
                                {{ kunjungan.pemeriksaan || '-' }}
                            </div>

                        </div>


                        <!-- DIAGNOSIS -->

                        <div>

                            <div
                                class="mb-1.5 flex items-center gap-2"
                            >

                                <BeakerIcon
                                    class="h-4 w-4 text-slate-400"
                                />

                                <p
                                    class="text-xs font-bold text-slate-500"
                                >
                                    Diagnosis
                                </p>

                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                            >
                                {{ kunjungan.diagnosis || '-' }}
                            </div>

                        </div>


                        <!-- TINDAKAN -->

                        <div>

                            <div
                                class="mb-1.5 flex items-center gap-2"
                            >

                                <HeartIcon
                                    class="h-4 w-4 text-slate-400"
                                />

                                <p
                                    class="text-xs font-bold text-slate-500"
                                >
                                    Tindakan
                                </p>

                            </div>

                            <div
                                class="rounded-xl bg-slate-50 p-3 text-sm leading-relaxed text-slate-700"
                            >
                                {{ kunjungan.tindakan || '-' }}
                            </div>

                        </div>

                    </div>



                    <!-- CATATAN + PEMERIKSA -->

                    <div
                        class="mt-5 grid grid-cols-1 gap-5 border-t border-slate-100 pt-5 md:grid-cols-2"
                    >

                        <div>

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Catatan
                            </p>

                            <p
                                class="mt-1 text-sm leading-relaxed text-slate-600"
                            >
                                {{ kunjungan.catatan || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs font-medium text-slate-400"
                            >
                                Pemeriksa
                            </p>

                            <div
                                class="mt-1 flex items-center gap-2"
                            >

                                <div
                                    class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-100"
                                >
                                    <UserIcon
                                        class="h-3.5 w-3.5 text-blue-600"
                                    />
                                </div>

                                <p
                                    class="text-sm font-semibold text-slate-700"
                                >
                                    {{
                                        kunjungan.pemeriksa?.name
                                        || kunjungan.pemeriksa?.nama
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>



                    <!-- OBAT -->

                    <div
                        v-if="kunjungan.kunjungan_obat?.length"
                        class="mt-5 border-t border-slate-100 pt-5"
                    >

                        <div
                            class="mb-3 flex items-center gap-2"
                        >

                            <BeakerIcon
                                class="h-4 w-4 text-blue-500"
                            />

                            <p
                                class="text-xs font-bold text-slate-600"
                            >
                                Obat
                            </p>

                        </div>


                        <div
                            class="overflow-x-auto rounded-xl border border-slate-200"
                        >

                            <table
                                class="min-w-full text-left"
                            >

                                <thead
                                    class="bg-slate-50"
                                >

                                    <tr>

                                        <th
                                            class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Obat
                                        </th>

                                        <th
                                            class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Jumlah
                                        </th>

                                        <th
                                            class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                        >
                                            Aturan
                                        </th>

                                    </tr>

                                </thead>

                                <tbody
                                    class="divide-y divide-slate-100"
                                >

                                    <tr
                                        v-for="obat in kunjungan.kunjungan_obat"
                                        :key="obat.id"
                                    >

                                        <td
                                            class="px-4 py-3 text-sm font-semibold text-slate-700"
                                        >
                                            {{
                                                obat.obat?.nama_obat
                                                || obat.nama_obat
                                                || '-'
                                            }}
                                        </td>

                                        <td
                                            class="px-4 py-3 text-sm text-slate-600"
                                        >
                                            {{ obat.jumlah || '-' }}
                                        </td>

                                        <td
                                            class="px-4 py-3 text-sm text-slate-600"
                                        >
                                            {{
                                                obat.aturan_pakai
                                                || obat.dosis
                                                || '-'
                                            }}
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ==================================================
             FOOTER
        ================================================== -->



    </div>

</KlinikLayout>

</template>