<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeftIcon,
    CheckIcon,
    UserIcon,
    ClipboardDocumentCheckIcon,
    BeakerIcon,
    CalendarDaysIcon,
    ExclamationTriangleIcon,
    HeartIcon,
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'

defineOptions({
    layout: KlinikLayout,
})

/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    kunjungan: {
        type: Object,
        required: true,
    },

    siswas: {
        type: Array,
        default: () => [],
    },

    penyakitList: {
        type: Array,
        default: () => [],
    },

    obatList: {
        type: Array,
        default: () => [],
    },
})

/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function formatTanggal(value) {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value)
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
    })
}

function formatTanggalWaktu(value) {
    if (!value) {
        return '-'
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return String(value)
    }

    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

/*
|--------------------------------------------------------------------------
| PERIODE
|--------------------------------------------------------------------------
*/

const periode = computed(() => {
    return props.kunjungan.periode ?? null
})

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({
    siswa_id:
        props.kunjungan.siswa_id ??
        props.kunjungan.siswa?.id ??
        '',

    periode_id:
        props.kunjungan.periode_id ??
        props.kunjungan.periode?.id ??
        '',

    tanggal_kunjungan:
        props.kunjungan.created_at ??
        props.kunjungan.tanggal_kunjungan ??
        '',

    keluhan:
        props.kunjungan.keluhan ??
        '',

    pemeriksaan:
        props.kunjungan.pemeriksaan ??
        '',

    penyakit_id:
        props.kunjungan.penyakit_id ??
        props.kunjungan.penyakit?.id ??
        '',

    triase:
        props.kunjungan.triase ??
        '',

    tindakan:
        props.kunjungan.tindakan ??
        '',

    catatan:
        props.kunjungan.catatan ??
        '',

    /*
    |--------------------------------------------------------------------------
    | OBAT
    |--------------------------------------------------------------------------
    | Tetap dimasukkan ke form agar ketika update:
    | data obat lama tidak hilang.
    |
    | Tetapi pada UI obat TIDAK dapat diedit.
    |--------------------------------------------------------------------------
    */

    obat: (
        props.kunjungan.obat ??
        props.kunjungan.kunjungan_obat ??
        []
    ).map(item => ({
        obat_id:
            item.obat_id ??
            item.obat?.id ??
            '',

        jumlah:
            Number(item.jumlah ?? 1),

        keterangan:
            item.keterangan ?? '',
    })),
})

/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/

const selectedSiswa = computed(() => {
    return (
        props.siswas.find(
            siswa =>
                Number(siswa.id) ===
                Number(form.siswa_id)
        ) ?? null
    )
})

/*
|--------------------------------------------------------------------------
| PENYAKIT
|--------------------------------------------------------------------------
*/

const selectedPenyakit = computed(() => {
    return (
        props.penyakitList.find(
            penyakit =>
                Number(penyakit.id) ===
                Number(form.penyakit_id)
        ) ?? null
    )
})

/*
|--------------------------------------------------------------------------
| OBAT
|--------------------------------------------------------------------------
*/

/*
 * Cari data obat berdasarkan ID.
 */
function getObat(obatId) {
    return (
        props.obatList.find(
            obat =>
                Number(obat.id) ===
                Number(obatId)
        ) ?? null
    )
}

/*
 * Nama obat.
 */
function getNamaObat(obatId) {
    const obat = getObat(obatId)

    return obat?.nama_obat ?? '-'
}

/*
 * Satuan obat.
 */
function getSatuan(obatId) {
    const obat = getObat(obatId)

    return obat?.satuan ?? ''
}

/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

function submit() {
    form.put(
        route(
            'klinik.kesehatan.kunjungan.update',
            props.kunjungan.id
        ),
        {
            preserveScroll: true,

            onSuccess: () => {
                // Controller melakukan redirect.
            },

            onError: errors => {
                console.error(
                    'Gagal memperbarui kunjungan:',
                    errors
                )
            },
        }
    )
}
</script>

<template>

    <Head title="Edit Kunjungan Klinik" />

    <div class="space-y-6">

        <!-- =========================================================
             HEADER
        ========================================================= -->

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
                    Edit Kunjungan Klinik
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Perbarui hasil pemeriksaan kesehatan siswa.
                </p>

            </div>

            <Link
                :href="
                    route(
                        'klinik.kesehatan.kunjungan.index'
                    )
                "
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
            >

                <ArrowLeftIcon
                    class="h-4 w-4"
                />

                Kembali

            </Link>

        </div>


        <!-- =========================================================
             ERROR
        ========================================================= -->

        <div
            v-if="Object.keys(form.errors).length"
            class="rounded-2xl border border-rose-200 bg-rose-50 p-4"
        >

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-100"
                >

                    <ExclamationTriangleIcon
                        class="h-5 w-5 text-rose-600"
                    />

                </div>

                <div>

                    <p
                        class="text-sm font-bold text-rose-800"
                    >
                        Terdapat kesalahan pada data.
                    </p>

                    <div
                        class="mt-2 space-y-1"
                    >

                        <p
                            v-for="(error, key) in form.errors"
                            :key="key"
                            class="text-xs text-rose-600"
                        >
                            • {{ error }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             INFORMASI KUNJUNGAN
        ========================================================= -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-6 py-5"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50"
                    >

                        <CalendarDaysIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Informasi Kunjungan
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Data siswa, periode, dan tanggal kunjungan.
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2"
            >

                <!-- =====================================================
                     SISWA
                ===================================================== -->

                <div class="md:col-span-2">

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Siswa
                    </label>

                    <select
                        v-model="form.siswa_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="">
                            Pilih siswa
                        </option>

                        <option
                            v-for="siswa in siswas"
                            :key="siswa.id"
                            :value="siswa.id"
                        >

                            {{ siswa.nama }}
                            —
                            {{ siswa.nisn }}

                        </option>

                    </select>

                    <p
                        v-if="form.errors.siswa_id"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.siswa_id }}
                    </p>


                    <!-- INFO SISWA -->

                    <div
                        v-if="selectedSiswa"
                        class="mt-3 rounded-xl bg-blue-50 px-4 py-3"
                    >

                        <div
                            class="flex flex-wrap gap-x-6 gap-y-2 text-xs"
                        >

                            <div>

                                <span class="text-blue-400">
                                    NISN
                                </span>

                                <p
                                    class="font-semibold text-blue-800"
                                >
                                    {{ selectedSiswa.nisn }}
                                </p>

                            </div>


                            <div>

                                <span class="text-blue-400">
                                    Kelas
                                </span>

                                <p
                                    class="font-semibold text-blue-800"
                                >
                                    {{
                                        selectedSiswa.kelas?.nama_kelas
                                        ?? '-'
                                    }}
                                </p>

                            </div>


                            <div>

                                <span class="text-blue-400">
                                    Jurusan
                                </span>

                                <p
                                    class="font-semibold text-blue-800"
                                >
                                    {{
                                        selectedSiswa.jurusan?.nama_jurusan
                                        ?? '-'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                     PERIODE
                ===================================================== -->

                <div class="md:col-span-2">

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Periode
                    </label>

                    <div
                        class="flex min-h-[66px] items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"
                    >

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white"
                        >

                            <CalendarDaysIcon
                                class="h-5 w-5 text-blue-600"
                            />

                        </div>

                        <div class="min-w-0">

                            <p
                                class="text-sm font-bold text-blue-800"
                            >
                                {{
                                    periode?.nama_periode
                                    ??
                                    props.kunjungan.periode?.nama_periode
                                    ??
                                    '-'
                                }}
                            </p>

                            <p
                                class="mt-0.5 text-[11px] text-blue-500"
                            >
                                Periode tersimpan pada kunjungan
                            </p>

                        </div>

                    </div>

                    <p
                        v-if="form.errors.periode_id"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.periode_id }}
                    </p>

                </div>


                <!-- =====================================================
                     TANGGAL & WAKTU
                ===================================================== -->

                <div
                    class="md:col-span-2 grid grid-cols-1 gap-5 sm:grid-cols-2"
                >

                    <!-- TANGGAL KUNJUNGAN -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Tanggal Kunjungan
                        </label>

                        <div
                            class="flex min-h-[66px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white"
                            >

                                <CalendarDaysIcon
                                    class="h-5 w-5 text-slate-400"
                                />

                            </div>

                            <div class="min-w-0">

                                <p
                                    class="text-sm font-bold text-slate-700"
                                >
                                    {{
                                        formatTanggal(
                                            kunjungan.created_at
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-0.5 text-[11px] text-slate-400"
                                >
                                    Diambil dari waktu data dibuat
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- TERAKHIR DIPERBARUI -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Terakhir Diperbarui
                        </label>

                        <div
                            class="flex min-h-[66px] items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white"
                            >

                                <CalendarDaysIcon
                                    class="h-5 w-5 text-slate-400"
                                />

                            </div>

                            <div class="min-w-0">

                                <p
                                    class="text-sm font-bold text-slate-700"
                                >
                                    {{
                                        formatTanggalWaktu(
                                            kunjungan.updated_at
                                        )
                                    }}
                                </p>

                                <p
                                    class="mt-0.5 text-[11px] text-slate-400"
                                >
                                    Waktu terakhir data diperbarui
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             PEMERIKSAAN
        ========================================================= -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <div
                class="border-b border-slate-100 px-6 py-5"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
                    >

                        <HeartIcon
                            class="h-5 w-5 text-emerald-600"
                        />

                    </div>

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Pemeriksaan
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Perbarui hasil pemeriksaan kesehatan siswa.
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="grid grid-cols-1 gap-5 p-6"
            >

                <!-- KELUHAN -->

                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Keluhan
                    </label>

                    <textarea
                        v-model="form.keluhan"
                        rows="3"
                        placeholder="Tuliskan keluhan siswa..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <p
                        v-if="form.errors.keluhan"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.keluhan }}
                    </p>

                </div>


                <!-- PEMERIKSAAN -->

                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Pemeriksaan
                    </label>

                    <textarea
                        v-model="form.pemeriksaan"
                        rows="4"
                        placeholder="Tuliskan hasil pemeriksaan..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <p
                        v-if="form.errors.pemeriksaan"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.pemeriksaan }}
                    </p>

                </div>


                <!-- PENYAKIT -->

                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Diagnosis / Penyakit
                    </label>

                    <select
                        v-model="form.penyakit_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="">
                            Tidak ada diagnosis / penyakit
                        </option>

                        <option
                            v-for="penyakit in penyakitList"
                            :key="penyakit.id"
                            :value="penyakit.id"
                        >

                            {{ penyakit.nama_penyakit }}

                            <template
                                v-if="penyakit.kategori"
                            >
                                — {{ penyakit.kategori }}
                            </template>

                        </option>

                    </select>

                    <p
                        v-if="form.errors.penyakit_id"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.penyakit_id }}
                    </p>


                    <div
                        v-if="selectedPenyakit"
                        class="mt-3 rounded-xl bg-amber-50 px-4 py-3"
                    >

                        <div
                            class="flex flex-wrap gap-x-6 gap-y-2 text-xs"
                        >

                            <div>

                                <span class="text-amber-500">
                                    Penyakit
                                </span>

                                <p
                                    class="font-semibold text-amber-800"
                                >
                                    {{
                                        selectedPenyakit.nama_penyakit
                                    }}
                                </p>

                            </div>


                            <div
                                v-if="selectedPenyakit.kategori"
                            >

                                <span class="text-amber-500">
                                    Kategori
                                </span>

                                <p
                                    class="font-semibold text-amber-800"
                                >
                                    {{
                                        selectedPenyakit.kategori
                                    }}
                                </p>

                            </div>

                        </div>


                        <p
                            v-if="selectedPenyakit.keterangan"
                            class="mt-2 text-xs text-amber-700"
                        >
                            {{
                                selectedPenyakit.keterangan
                            }}
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     TRIASE
                ================================================== -->

                <div>

                    <label
                        class="mb-2 block text-xs font-bold text-slate-600"
                    >
                        Triase
                        <span class="text-rose-500">*</span>
                    </label>

                    <div
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                    >

                        <!-- MERAH -->

                        <label
                            class="cursor-pointer rounded-xl border p-4 transition"
                            :class="
                                form.triase === 'merah'
                                    ? 'border-red-500 bg-red-50 ring-2 ring-red-100'
                                    : 'border-slate-200 bg-white hover:bg-red-50'
                            "
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    v-model="form.triase"
                                    type="radio"
                                    value="merah"
                                    class="mt-1"
                                />

                                <div>

                                    <p class="font-bold text-red-700">
                                        🔴 Merah
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5 text-slate-500"
                                    >
                                        Prioritas tinggi / kondisi gawat
                                        darurat yang membutuhkan
                                        penanganan segera.
                                    </p>

                                </div>

                            </div>

                        </label>


                        <!-- KUNING -->

                        <label
                            class="cursor-pointer rounded-xl border p-4 transition"
                            :class="
                                form.triase === 'kuning'
                                    ? 'border-yellow-500 bg-yellow-50 ring-2 ring-yellow-100'
                                    : 'border-slate-200 bg-white hover:bg-yellow-50'
                            "
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    v-model="form.triase"
                                    type="radio"
                                    value="kuning"
                                    class="mt-1"
                                />

                                <div>

                                    <p class="font-bold text-yellow-700">
                                        🟡 Kuning
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5 text-slate-500"
                                    >
                                        Prioritas sedang dan membutuhkan
                                        pemeriksaan atau penanganan.
                                    </p>

                                </div>

                            </div>

                        </label>


                        <!-- HIJAU -->

                        <label
                            class="cursor-pointer rounded-xl border p-4 transition"
                            :class="
                                form.triase === 'hijau'
                                    ? 'border-green-500 bg-green-50 ring-2 ring-green-100'
                                    : 'border-slate-200 bg-white hover:bg-green-50'
                            "
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    v-model="form.triase"
                                    type="radio"
                                    value="hijau"
                                    class="mt-1"
                                />

                                <div>

                                    <p class="font-bold text-green-700">
                                        🟢 Hijau
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5 text-slate-500"
                                    >
                                        Prioritas rendah dan tidak
                                        menunjukkan kondisi gawat darurat.
                                    </p>

                                </div>

                            </div>

                        </label>


                        <!-- HITAM -->

                        <label
                            class="cursor-pointer rounded-xl border p-4 transition"
                            :class="
                                form.triase === 'hitam'
                                    ? 'border-slate-700 bg-slate-100 ring-2 ring-slate-200'
                                    : 'border-slate-200 bg-white hover:bg-slate-50'
                            "
                        >

                            <div class="flex items-start gap-3">

                                <input
                                    v-model="form.triase"
                                    type="radio"
                                    value="hitam"
                                    class="mt-1"
                                />

                                <div>

                                    <p class="font-bold text-slate-800">
                                        ⚫ Hitam
                                    </p>

                                    <p
                                        class="mt-1 text-xs leading-5 text-slate-500"
                                    >
                                        Kondisi meninggal atau tidak
                                        menunjukkan tanda kehidupan.
                                    </p>

                                </div>

                            </div>

                        </label>

                    </div>


                    <p
                        v-if="form.errors.triase"
                        class="mt-2 text-xs font-medium text-rose-600"
                    >
                        {{ form.errors.triase }}
                    </p>

                </div>


                <!-- TINDAKAN -->

                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Tindakan
                    </label>

                    <textarea
                        v-model="form.tindakan"
                        rows="3"
                        placeholder="Tuliskan tindakan yang dilakukan..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <p
                        v-if="form.errors.tindakan"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.tindakan }}
                    </p>

                </div>


                <!-- CATATAN -->

                <div>

                    <label
                        class="mb-1.5 block text-xs font-bold text-slate-600"
                    >
                        Catatan
                    </label>

                    <textarea
                        v-model="form.catatan"
                        rows="3"
                        placeholder="Catatan tambahan..."
                        class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    ></textarea>

                    <p
                        v-if="form.errors.catatan"
                        class="mt-1 text-xs text-rose-500"
                    >
                        {{ form.errors.catatan }}
                    </p>

                </div>

            </div>

        </div>


        <!-- =========================================================
             OBAT - READ ONLY
        ========================================================= -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <!-- HEADER -->

            <div
                class="border-b border-slate-100 px-6 py-5"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
                    >

                        <BeakerIcon
                            class="h-5 w-5 text-emerald-600"
                        />

                    </div>

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Obat yang Diberikan
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Obat yang diberikan pada kunjungan ini.
                            Data obat tidak dapat diubah pada halaman edit.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6">

                <!-- EMPTY -->

                <div
                    v-if="!form.obat.length"
                    class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center"
                >

                    <BeakerIcon
                        class="mx-auto h-8 w-8 text-slate-300"
                    />

                    <p
                        class="mt-2 text-sm font-semibold text-slate-600"
                    >
                        Tidak ada obat
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Tidak ada obat yang diberikan pada kunjungan ini.
                    </p>

                </div>


                <!-- LIST OBAT -->

                <div
                    v-else
                    class="space-y-4"
                >

                    <div
                        v-for="(item, index) in form.obat"
                        :key="index"
                        class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                    >

                        <div
                            class="grid grid-cols-1 gap-4 md:grid-cols-2"
                        >

                            <!-- NAMA OBAT -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-600"
                                >
                                    Nama Obat
                                </label>

                                <div
                                    class="flex min-h-[42px] items-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5"
                                >

                                    <span
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ getNamaObat(item.obat_id) }}
                                    </span>

                                </div>

                            </div>


                            <!-- JUMLAH -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-600"
                                >
                                    Jumlah
                                </label>

                                <div
                                    class="flex min-h-[42px] items-center rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5"
                                >

                                    <span
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.jumlah }}
                                        {{ getSatuan(item.obat_id) }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- KETERANGAN -->

                        <div class="mt-4">

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-600"
                            >
                                Keterangan / Aturan Pakai
                            </label>

                            <div
                                class="min-h-[42px] rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5"
                            >

                                <span
                                    class="text-sm text-slate-700"
                                >
                                    {{
                                        item.keterangan
                                        || '-'
                                    }}
                                </span>

                            </div>

                        </div>


                        <!-- INFO READ ONLY -->

                        <div
                            class="mt-3 flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2"
                        >

                            <span
                                class="text-[11px] text-slate-400"
                            >
                                Obat hanya dapat dilihat pada halaman edit
                                kunjungan.
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             FOOTER
        ========================================================= -->

        <div
            class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end"
        >

            <Link
                :href="
                    route(
                        'klinik.kesehatan.kunjungan.index'
                    )
                "
                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >
                Batal
            </Link>


            <button
                type="button"
                @click="submit"
                :disabled="form.processing"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
            >

                <svg
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                >

                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke-width="3"
                        class="opacity-30"
                    />

                    <path
                        d="M21 12a9 9 0 0 0-9-9"
                        stroke-width="3"
                    />

                </svg>


                <CheckIcon
                    v-else
                    class="h-4 w-4"
                />


                {{
                    form.processing
                        ? 'Menyimpan...'
                        : 'Simpan Perubahan'
                }}

            </button>

        </div>

    </div>

</template>