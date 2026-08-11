<script setup>
import { computed, ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    HeartIcon,
    PlusIcon,
    TrashIcon,
    UserIcon,
    BeakerIcon,
} from '@heroicons/vue/24/outline'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswas: {
        type: Array,
        default: () => [],
    },

    obatList: {
        type: Array,
        default: () => [],
    },

    siswa: {
        type: Object,
        default: null,
    },
})


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({
    periode_id: props.periode?.id ?? null,

    siswa_id: props.siswa?.id ?? null,

    tanggal_kunjungan: new Date()
        .toISOString()
        .slice(0, 10),

    status: 'selesai',

    keluhan: '',

    pemeriksaan: '',

    diagnosis: '',

    tindakan: '',

    catatan: '',

    obat: [],
})


/*
|--------------------------------------------------------------------------
| SISWA
|--------------------------------------------------------------------------
*/

const searchSiswa = ref('')

const showSiswaDropdown = ref(false)


const selectedSiswa = computed(() => {
    if (!form.siswa_id) {
        return null
    }

    return props.siswas.find(
        siswa => Number(siswa.id) === Number(form.siswa_id)
    ) ?? null
})


const filteredSiswas = computed(() => {
    const keyword = searchSiswa.value
        .toLowerCase()
        .trim()

    if (!keyword) {
        return props.siswas.slice(0, 20)
    }

    return props.siswas
        .filter(siswa => {
            const nama =
                siswa.nama?.toLowerCase() ?? ''

            const nisn =
                siswa.nisn?.toString() ?? ''

            return (
                nama.includes(keyword) ||
                nisn.includes(keyword)
            )
        })
        .slice(0, 20)
})


function selectSiswa(siswa) {
    form.siswa_id = siswa.id

    searchSiswa.value = ''

    showSiswaDropdown.value = false

    form.clearErrors('siswa_id')
}


function clearSiswa() {
    form.siswa_id = null

    searchSiswa.value = ''

    showSiswaDropdown.value = false
}


function closeDropdown() {
    setTimeout(() => {
        showSiswaDropdown.value = false
    }, 150)
}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusOptions = [
    {
        value: 'selesai',
        label: 'Selesai',
        description: 'Pemeriksaan telah selesai dilakukan.',
    },
]


/*
|--------------------------------------------------------------------------
| OBAT
|--------------------------------------------------------------------------
*/

const selectedObatId = ref(null)

const selectedObatJumlah = ref(1)

const selectedObatKeterangan = ref('')

const obatError = ref('')


/*
|--------------------------------------------------------------------------
| OBAT YANG TERSEDIA
|--------------------------------------------------------------------------
*/

const availableObat = computed(() => {
    return props.obatList.filter(obat => {

        const sudahDipilih = form.obat.some(
            item =>
                Number(item.obat_id) ===
                Number(obat.id)
        )

        return (
            !sudahDipilih &&
            Number(obat.stok) > 0
        )
    })
})


/*
|--------------------------------------------------------------------------
| TAMBAH OBAT
|--------------------------------------------------------------------------
*/

function addObat() {
    obatError.value = ''

    if (!selectedObatId.value) {
        obatError.value =
            'Silakan pilih obat terlebih dahulu.'

        return
    }

    const obat = props.obatList.find(
        item =>
            Number(item.id) ===
            Number(selectedObatId.value)
    )

    if (!obat) {
        obatError.value =
            'Obat tidak ditemukan.'

        return
    }

    const jumlah =
        Number(selectedObatJumlah.value)

    const stok =
        Number(obat.stok)


    if (
        !Number.isInteger(jumlah) ||
        jumlah < 1
    ) {
        obatError.value =
            'Jumlah obat minimal 1.'

        return
    }


    if (stok <= 0) {
        obatError.value =
            'Stok obat sudah habis.'

        return
    }


    if (jumlah > stok) {
        obatError.value =
            `Jumlah melebihi stok yang tersedia (${stok} ${obat.satuan ?? ''}).`

        return
    }


    form.obat.push({
        obat_id: obat.id,

        nama_obat: obat.nama_obat,

        satuan: obat.satuan,

        stok: stok,

        jumlah: jumlah,

        keterangan:
            selectedObatKeterangan.value || '',
    })


    selectedObatId.value = null

    selectedObatJumlah.value = 1

    selectedObatKeterangan.value = ''

    form.clearErrors('obat')
}


/*
|--------------------------------------------------------------------------
| HAPUS OBAT
|--------------------------------------------------------------------------
*/

function removeObat(index) {
    form.obat.splice(index, 1)

    obatError.value = ''
}


/*
|--------------------------------------------------------------------------
| UPDATE JUMLAH OBAT
|--------------------------------------------------------------------------
*/

function validateObatJumlah(item) {
    const obat = props.obatList.find(
        obat =>
            Number(obat.id) ===
            Number(item.obat_id)
    )

    if (!obat) {
        return
    }

    const stok =
        Number(obat.stok)

    let jumlah =
        Number(item.jumlah)


    if (
        !Number.isInteger(jumlah) ||
        jumlah < 1
    ) {
        jumlah = 1
    }


    if (jumlah > stok) {
        jumlah = stok
    }


    item.jumlah = jumlah
}


/*
|--------------------------------------------------------------------------
| TOTAL OBAT
|--------------------------------------------------------------------------
*/

const totalJenisObat = computed(() => {
    return form.obat.length
})


const totalJumlahObat = computed(() => {
    return form.obat.reduce(
        (total, item) =>
            total + Number(item.jumlah || 0),
        0
    )
})


/*
|--------------------------------------------------------------------------
| CAN SUBMIT
|--------------------------------------------------------------------------
*/

const canSubmit = computed(() => {
    return (
        !!form.periode_id &&
        !!form.siswa_id &&
        !!form.tanggal_kunjungan &&
        !!form.status &&
        !!form.keluhan?.trim()
    )
})


/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

function submit() {

    if (!canSubmit.value) {
        return
    }


    form.post(
        route(
            'klinik.kesehatan.kunjungan.store'
        ),
        {
            preserveScroll: true,

            onSuccess: () => {
                /*
                 * Redirect dilakukan oleh controller.
                 *
                 * Contoh controller:
                 *
                 * return redirect()
                 *     ->route(
                 *         'klinik.kesehatan.kunjungan.index'
                 *     )
                 *     ->with(
                 *         'success',
                 *         'Kunjungan berhasil disimpan.'
                 *     );
                 */
            },

            onError: errors => {
                console.error(
                    'Validation error:',
                    errors
                )
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

                    <div
                        class="flex items-center gap-2 text-xs font-semibold text-slate-400"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-4 w-4"
                        />

                        Kesehatan

                        <span>/</span>

                        Kunjungan Klinik

                        <span>/</span>

                        <span class="text-slate-500">
                            Kunjungan Baru
                        </span>

                    </div>


                    <h1
                        class="mt-1 text-2xl font-bold text-slate-800"
                    >
                        Kunjungan Klinik Baru
                    </h1>


                    <p
                        class="mt-1 text-sm text-slate-500"
                    >
                        Catat pemeriksaan dan kondisi kesehatan
                        siswa saat berkunjung ke klinik.
                    </p>

                </div>


                <!-- KEMBALI -->

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


            <!-- ==================================================
                 PERIODE AKTIF
            ================================================== -->

            <div
                v-if="periode"
                class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <CalendarDaysIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>


                    <div>

                        <p
                            class="text-xs font-semibold uppercase tracking-wide text-blue-500"
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

            </div>


            <!-- ==================================================
                 TIDAK ADA PERIODE
            ================================================== -->

            <div
                v-else
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4"
            >

                <div
                    class="flex items-start gap-3"
                >

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
                            Periode aktif tidak ditemukan
                        </p>


                        <p
                            class="mt-0.5 text-xs text-rose-600"
                        >
                            Data kunjungan membutuhkan periode aktif.
                            Silakan aktifkan periode terlebih dahulu.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 ERROR GLOBAL
            ================================================== -->

            <div
                v-if="Object.keys(form.errors).length"
                class="rounded-2xl border border-rose-200 bg-rose-50 p-4"
            >

                <div
                    class="flex items-start gap-3"
                >

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
                            Data belum lengkap
                        </p>


                        <p
                            class="mt-0.5 text-xs text-rose-600"
                        >
                            Periksa kembali kolom yang ditandai
                            sebelum menyimpan data.
                        </p>


                        <div
                            class="mt-3 space-y-1"
                        >

                            <p
                                v-for="(error, field) in form.errors"
                                :key="field"
                                class="text-xs font-medium text-rose-700"
                            >
                                • {{ error }}
                            </p>

                        </div>

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
                     DATA SISWA
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"
                            >

                                <UserIcon
                                    class="h-5 w-5 text-blue-600"
                                />

                            </div>


                            <div>

                                <h2
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Data Siswa
                                </h2>


                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    Pilih siswa yang melakukan kunjungan.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">


                        <!-- SISWA TERPILIH -->

                        <div
                            v-if="selectedSiswa"
                            class="rounded-xl border border-blue-200 bg-blue-50/60 p-4"
                        >

                            <div
                                class="flex items-center justify-between gap-4"
                            >

                                <div
                                    class="flex min-w-0 items-center gap-3"
                                >

                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                                    >

                                        {{
                                            selectedSiswa.nama
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                        }}

                                    </div>


                                    <div
                                        class="min-w-0"
                                    >

                                        <p
                                            class="truncate text-sm font-bold text-slate-800"
                                        >
                                            {{ selectedSiswa.nama }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-500"
                                        >
                                            NISN:
                                            {{ selectedSiswa.nisn ?? '-' }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-500"
                                        >

                                            {{
                                                selectedSiswa.kelas?.nama_kelas
                                                ??
                                                selectedSiswa.kelas?.tingkat
                                                ??
                                                '-'
                                            }}


                                            <span
                                                v-if="
                                                    selectedSiswa.kelas?.jurusan
                                                        ?.nama_jurusan
                                                "
                                            >

                                                ·

                                                {{
                                                    selectedSiswa.kelas.jurusan
                                                        .nama_jurusan
                                                }}

                                            </span>

                                        </p>

                                    </div>

                                </div>


                                <button
                                    v-if="!props.siswa"
                                    type="button"
                                    @click="clearSiswa"
                                    class="shrink-0 rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100"
                                >
                                    Ganti
                                </button>

                            </div>

                        </div>


                        <!-- PILIH SISWA -->

                        <div
                            v-else
                            class="relative"
                        >

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >

                                Siswa

                                <span class="text-rose-500">
                                    *
                                </span>

                            </label>


                            <div class="relative">

                                <UserIcon
                                    class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                />


                                <input
                                    v-model="searchSiswa"
                                    @focus="
                                        showSiswaDropdown = true
                                    "
                                    @blur="closeDropdown"
                                    type="text"
                                    placeholder="Cari nama atau NISN siswa..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                />

                            </div>


                            <!-- DROPDOWN -->

                            <div
                                v-if="showSiswaDropdown"
                                class="absolute left-0 right-0 z-30 mt-2 max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"
                            >

                                <button
                                    v-for="siswa in filteredSiswas"
                                    :key="siswa.id"
                                    type="button"
                                    @mousedown.prevent="
                                        selectSiswa(siswa)
                                    "
                                    class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-slate-50"
                                >

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                    >

                                        {{
                                            siswa.nama
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                        }}

                                    </div>


                                    <div
                                        class="min-w-0"
                                    >

                                        <p
                                            class="truncate text-sm font-semibold text-slate-800"
                                        >
                                            {{ siswa.nama }}
                                        </p>


                                        <p
                                            class="text-xs text-slate-400"
                                        >

                                            NISN:
                                            {{ siswa.nisn ?? '-' }}

                                            ·

                                            {{
                                                siswa.kelas?.nama_kelas
                                                ??
                                                siswa.kelas?.tingkat
                                                ??
                                                '-'
                                            }}

                                        </p>

                                    </div>

                                </button>


                                <div
                                    v-if="
                                        filteredSiswas.length === 0
                                    "
                                    class="px-4 py-6 text-center"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-600"
                                    >
                                        Siswa tidak ditemukan
                                    </p>


                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Coba gunakan nama atau NISN
                                        yang berbeda.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <p
                            v-if="form.errors.siswa_id"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ form.errors.siswa_id }}
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     INFORMASI KUNJUNGAN
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50"
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
                                    Informasi dasar kunjungan siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
                    >

                        <!-- TANGGAL -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >

                                Tanggal Kunjungan

                                <span class="text-rose-500">
                                    *
                                </span>

                            </label>


                            <div class="relative">

                                <CalendarDaysIcon
                                    class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                                />


                                <input
                                    v-model="
                                        form.tanggal_kunjungan
                                    "
                                    type="date"
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                />

                            </div>


                            <p
                                v-if="
                                    form.errors.tanggal_kunjungan
                                "
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{
                                    form.errors.tanggal_kunjungan
                                }}
                            </p>

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >

                                Status Pemeriksaan

                                <span class="text-rose-500">
                                    *
                                </span>

                            </label>


                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            >

                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value"
                                    :value="option.value"
                                >
                                    {{ option.label }}
                                </option>

                            </select>


                            <p
                                class="mt-1.5 text-[11px] text-slate-400"
                            >

                                {{
                                    statusOptions.find(
                                        option =>
                                            option.value ===
                                            form.status
                                    )?.description
                                }}

                            </p>


                            <p
                                v-if="form.errors.status"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.status }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     HASIL PEMERIKSAAN
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50"
                            >

                                <HeartIcon
                                    class="h-5 w-5 text-emerald-600"
                                />

                            </div>


                            <div>

                                <h2
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Hasil Pemeriksaan
                                </h2>


                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    Catat keluhan dan hasil pemeriksaan siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="space-y-5 p-5">


                        <!-- KELUHAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >

                                Keluhan

                                <span class="text-rose-500">
                                    *
                                </span>

                            </label>


                            <textarea
                                v-model="form.keluhan"
                                rows="3"
                                placeholder="Tuliskan keluhan yang disampaikan siswa..."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            ></textarea>


                            <p
                                v-if="form.errors.keluhan"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.keluhan }}
                            </p>

                        </div>


                        <!-- PEMERIKSAAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Pemeriksaan
                            </label>


                            <textarea
                                v-model="form.pemeriksaan"
                                rows="4"
                                placeholder="Tuliskan hasil pemeriksaan fisik atau kondisi siswa..."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            ></textarea>


                            <p
                                v-if="form.errors.pemeriksaan"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.pemeriksaan }}
                            </p>

                        </div>


                        <!-- DIAGNOSIS -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Diagnosis
                            </label>


                            <textarea
                                v-model="form.diagnosis"
                                rows="3"
                                placeholder="Tuliskan diagnosis berdasarkan hasil pemeriksaan..."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            ></textarea>


                            <p
                                v-if="form.errors.diagnosis"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.diagnosis }}
                            </p>

                        </div>


                        <!-- TINDAKAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Tindakan
                            </label>


                            <textarea
                                v-model="form.tindakan"
                                rows="3"
                                placeholder="Tuliskan tindakan atau penanganan yang diberikan..."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            ></textarea>


                            <p
                                v-if="form.errors.tindakan"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tindakan }}
                            </p>

                        </div>


                        <!-- CATATAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Catatan
                            </label>


                            <textarea
                                v-model="form.catatan"
                                rows="3"
                                placeholder="Tambahkan catatan lain jika diperlukan..."
                                class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                            ></textarea>


                            <p
                                v-if="form.errors.catatan"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.catatan }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     OBAT
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <!-- HEADER -->

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50"
                            >

                                <BeakerIcon
                                    class="h-5 w-5 text-amber-600"
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
                                    Tambahkan obat yang diberikan kepada siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="space-y-5 p-5">


                        <!-- TAMBAH OBAT -->

                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                        >

                            <div
                                class="grid grid-cols-1 gap-4 md:grid-cols-12"
                            >

                                <!-- OBAT -->

                                <div
                                    class="md:col-span-5"
                                >

                                    <label
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Obat
                                    </label>


                                    <select
                                        v-model="selectedObatId"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    >

                                        <option
                                            :value="null"
                                        >
                                            Pilih obat
                                        </option>


                                        <option
                                            v-for="obat in availableObat"
                                            :key="obat.id"
                                            :value="obat.id"
                                        >

                                            {{ obat.nama_obat }}

                                            —
                                            Stok:
                                            {{ obat.stok }}
                                            {{ obat.satuan }}

                                        </option>

                                    </select>

                                </div>


                                <!-- JUMLAH -->

                                <div
                                    class="md:col-span-2"
                                >

                                    <label
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Jumlah
                                    </label>


                                    <input
                                        v-model.number="
                                            selectedObatJumlah
                                        "
                                        type="number"
                                        min="1"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />

                                </div>


                                <!-- KETERANGAN -->

                                <div
                                    class="md:col-span-4"
                                >

                                    <label
                                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Keterangan
                                    </label>


                                    <input
                                        v-model="
                                            selectedObatKeterangan
                                        "
                                        type="text"
                                        placeholder="Contoh: 3x sehari"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    />

                                </div>


                                <!-- BUTTON -->

                                <div
                                    class="flex items-end md:col-span-1"
                                >

                                    <button
                                        type="button"
                                        @click="addObat"
                                        :disabled="
                                            !availableObat.length
                                        "
                                        class="inline-flex w-full items-center justify-center gap-1.5 rounded-xl bg-blue-600 px-3 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                                    >

                                        <PlusIcon
                                            class="h-4 w-4"
                                        />

                                        Tambah

                                    </button>

                                </div>

                            </div>


                            <!-- ERROR OBAT -->

                            <p
                                v-if="obatError"
                                class="mt-3 text-xs font-medium text-rose-600"
                            >
                                {{ obatError }}
                            </p>

                        </div>


                        <!-- LIST OBAT -->

                        <div
                            v-if="form.obat.length"
                            class="overflow-hidden rounded-xl border border-slate-200"
                        >

                            <div
                                class="border-b border-slate-100 bg-slate-50 px-4 py-3"
                            >

                                <div
                                    class="flex flex-wrap items-center justify-between gap-2"
                                >

                                    <p
                                        class="text-xs font-bold uppercase tracking-wide text-slate-500"
                                    >
                                        Daftar Obat
                                    </p>


                                    <p
                                        class="text-xs font-semibold text-slate-500"
                                    >

                                        {{ totalJenisObat }}
                                        jenis ·

                                        {{ totalJumlahObat }}
                                        item

                                    </p>

                                </div>

                            </div>


                            <div
                                class="divide-y divide-slate-100"
                            >

                                <div
                                    v-for="(item, index) in form.obat"
                                    :key="
                                        `${item.obat_id}-${index}`
                                    "
                                    class="flex flex-col gap-4 p-4 md:flex-row md:items-center"
                                >

                                    <!-- ICON -->

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50"
                                    >

                                        <BeakerIcon
                                            class="h-5 w-5 text-amber-600"
                                        />

                                    </div>


                                    <!-- NAMA -->

                                    <div
                                        class="min-w-0 flex-1"
                                    >

                                        <p
                                            class="text-sm font-bold text-slate-800"
                                        >
                                            {{ item.nama_obat }}
                                        </p>


                                        <p
                                            class="mt-0.5 text-xs text-slate-400"
                                        >

                                            Stok tersedia:
                                            {{ item.stok }}
                                            {{ item.satuan }}

                                        </p>


                                        <p
                                            v-if="item.keterangan"
                                            class="mt-1 text-xs text-slate-500"
                                        >

                                            {{ item.keterangan }}

                                        </p>

                                    </div>


                                    <!-- JUMLAH -->

                                    <div
                                        class="flex items-center gap-2"
                                    >

                                        <label
                                            class="text-xs font-semibold text-slate-500"
                                        >
                                            Jumlah
                                        </label>


                                        <input
                                            v-model.number="
                                                item.jumlah
                                            "
                                            @change="
                                                validateObatJumlah(
                                                    item
                                                )
                                            "
                                            type="number"
                                            min="1"
                                            :max="item.stok"
                                            class="w-24 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-center text-sm font-semibold text-slate-700 outline-none focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                                        />


                                        <span
                                            class="text-xs text-slate-400"
                                        >
                                            {{ item.satuan }}
                                        </span>

                                    </div>


                                    <!-- DELETE -->

                                    <button
                                        type="button"
                                        @click="
                                            removeObat(index)
                                        "
                                        class="inline-flex items-center justify-center rounded-lg p-2 text-rose-500 transition hover:bg-rose-50"
                                        title="Hapus obat"
                                    >

                                        <TrashIcon
                                            class="h-5 w-5"
                                        />

                                    </button>

                                </div>

                            </div>

                        </div>


                        <!-- EMPTY -->

                        <div
                            v-else
                            class="rounded-xl border border-dashed border-slate-200 px-5 py-8 text-center"
                        >

                            <BeakerIcon
                                class="mx-auto h-8 w-8 text-slate-300"
                            />


                            <p
                                class="mt-2 text-sm font-semibold text-slate-500"
                            >
                                Belum ada obat
                            </p>


                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                Jika siswa mendapatkan obat,
                                pilih obat pada form di atas.
                            </p>

                        </div>


                        <!-- SERVER ERROR OBAT -->

                        <p
                            v-if="form.errors['obat']"
                            class="text-xs font-medium text-rose-600"
                        >
                            {{ form.errors['obat'] }}
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     FOOTER
                ================================================== -->

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end"
                >

                    <Link
                        :href="
                            route(
                                'klinik.kesehatan.kunjungan.index'
                            )
                        "
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </Link>


                    <button
                        type="submit"
                        :disabled="
                            form.processing ||
                            !canSubmit
                        "
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >

                        <CheckCircleIcon
                            class="h-5 w-5"
                        />


                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Kunjungan'
                        }}

                    </button>

                </div>


            </form>

        </div>

    </KlinikLayout>

</template>