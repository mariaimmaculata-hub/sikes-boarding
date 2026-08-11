<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import {
    ArrowLeftIcon,
    CheckIcon,
    PlusIcon,
    TrashIcon,
    UserIcon,
    ClipboardDocumentCheckIcon,
    BeakerIcon,
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'


defineOptions({
    layout: KlinikLayout,
})


const props = defineProps({
    kunjungan: {
        type: Object,
        required: true,
    },

    siswas: {
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
| DATA OBAT AWAL
|--------------------------------------------------------------------------
*/

const initialObat = (props.kunjungan.obat ?? props.kunjungan.kunjungan_obat ?? [])
    .map((item) => ({
        obat_id: item.obat_id ?? item.obat?.id ?? '',
        jumlah: item.jumlah ?? 1,
        keterangan: item.keterangan ?? '',
    }))


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({
    periode_id:
        props.kunjungan.periode_id
        ?? props.kunjungan.periode?.id
        ?? '',

    siswa_id:
        props.kunjungan.siswa_id
        ?? props.kunjungan.siswa?.id
        ?? '',

    tanggal_kunjungan:
        formatDateTimeLocal(props.kunjungan.tanggal_kunjungan),

    keluhan:
        props.kunjungan.keluhan ?? '',

    pemeriksaan:
        props.kunjungan.pemeriksaan ?? '',

    diagnosis:
        props.kunjungan.diagnosis ?? '',

    tindakan:
        props.kunjungan.tindakan ?? '',

    status:
        props.kunjungan.status ?? 'selesai',

    catatan:
        props.kunjungan.catatan ?? '',

    obat: initialObat,
})


/*
|--------------------------------------------------------------------------
| SISWA TERPILIH
|--------------------------------------------------------------------------
*/

const selectedSiswa = computed(() => {
    return props.siswas.find(
        (siswa) => Number(siswa.id) === Number(form.siswa_id)
    ) ?? null
})


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function formatDateTimeLocal(value) {
    if (!value) {
        return ''
    }

    const date = new Date(value)

    if (Number.isNaN(date.getTime())) {
        return value
    }

    const pad = (number) =>
        String(number).padStart(2, '0')

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`
}


/*
|--------------------------------------------------------------------------
| OBAT
|--------------------------------------------------------------------------
*/

function addObat() {
    form.obat.push({
        obat_id: '',
        jumlah: 1,
        keterangan: '',
    })
}


function removeObat(index) {
    form.obat.splice(index, 1)
}


function getObat(obatId) {
    return props.obatList.find(
        (obat) => Number(obat.id) === Number(obatId)
    ) ?? null
}


function getStok(obatId) {
    const obat = getObat(obatId)

    return obat?.stok ?? 0
}


function getSatuan(obatId) {
    const obat = getObat(obatId)

    return obat?.satuan ?? ''
}


/*
|--------------------------------------------------------------------------
| CEGAH OBAT SAMA
|--------------------------------------------------------------------------
*/

function isDuplicateObat(index) {
    const current = form.obat[index]?.obat_id

    if (!current) {
        return false
    }

    return form.obat.some(
        (item, itemIndex) =>
            itemIndex !== index &&
            Number(item.obat_id) === Number(current)
    )
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
        }
    )
}
</script>


<template>

    <Head title="Edit Kunjungan Klinik" />


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

                </div>


                <h1
                    class="mt-1 text-2xl font-bold text-slate-800"
                >
                    Edit Kunjungan Klinik
                </h1>


                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Perbarui data pemeriksaan dan obat yang diberikan.
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


        <!-- ==================================================
             ERROR
        ================================================== -->

        <div
            v-if="Object.keys(form.errors).length"
            class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
        >

            <p
                class="text-sm font-bold text-rose-800"
            >
                Terdapat kesalahan pada data.
            </p>


            <ul
                class="mt-2 list-inside list-disc space-y-1 text-xs text-rose-600"
            >

                <li
                    v-for="(error, key) in form.errors"
                    :key="key"
                >
                    {{ error }}
                </li>

            </ul>

        </div>


        <!-- ==================================================
             FORM
        ================================================== -->

        <form
            @submit.prevent="submit"
            class="space-y-6"
        >


            <!-- ==================================================
                 DATA KUNJUNGAN
            ================================================== -->

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

                            <UserIcon
                                class="h-5 w-5 text-blue-600"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Data Kunjungan
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
                    class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2"
                >


                    <!-- SISWA -->

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


                    <!-- PERIODE -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Periode
                        </label>


                        <input
                            :value="
                                kunjungan.periode?.nama_periode
                                ?? '-'
                            "
                            type="text"
                            readonly
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm font-medium text-slate-600 outline-none"
                        />

                    </div>


                    <!-- TANGGAL -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Tanggal Kunjungan
                        </label>


                        <input
                            v-model="form.tanggal_kunjungan"
                            type="datetime-local"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        />


                        <p
                            v-if="form.errors.tanggal_kunjungan"
                            class="mt-1 text-xs text-rose-500"
                        >
                            {{ form.errors.tanggal_kunjungan }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 PEMERIKSAAN
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-6 py-5"
                >

                    <h2
                        class="text-sm font-bold text-slate-800"
                    >
                        Pemeriksaan
                    </h2>

                    <p
                        class="mt-0.5 text-xs text-slate-400"
                    >
                        Hasil pemeriksaan kesehatan siswa.
                    </p>

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

                    </div>


                    <!-- DIAGNOSIS -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Diagnosis
                        </label>


                        <textarea
                            v-model="form.diagnosis"
                            rows="3"
                            placeholder="Tuliskan diagnosis..."
                            class="w-full resize-none rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        ></textarea>

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

                    </div>


                    <!-- STATUS -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-600"
                        >
                            Status
                        </label>


                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="selesai">
                                Selesai
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 OBAT
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
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
                                Kelola obat yang diberikan kepada siswa.
                            </p>

                        </div>

                    </div>


                    <button
                        type="button"
                        @click="addObat"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"
                    >

                        <PlusIcon
                            class="h-4 w-4"
                        />

                        Tambah Obat

                    </button>

                </div>


                <div class="p-6">


                    <!-- TIDAK ADA OBAT -->

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
                            Belum ada obat
                        </p>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Klik "Tambah Obat" jika ada obat yang diberikan.
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
                                class="grid grid-cols-1 gap-4 lg:grid-cols-[1fr_150px_auto]"
                            >


                                <!-- OBAT -->

                                <div>

                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-600"
                                    >
                                        Nama Obat
                                    </label>


                                    <select
                                        v-model="item.obat_id"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                    >

                                        <option value="">
                                            Pilih obat
                                        </option>


                                        <option
                                            v-for="obat in obatList"
                                            :key="obat.id"
                                            :value="obat.id"
                                            :disabled="
                                                form.obat.some(
                                                    (other, otherIndex) =>
                                                        otherIndex !== index &&
                                                        Number(other.obat_id) === Number(obat.id)
                                                )
                                            "
                                        >
                                            {{ obat.nama_obat }}
                                            —
                                            Stok {{ obat.stok }}
                                            {{ obat.satuan }}
                                        </option>

                                    </select>


                                    <p
                                        v-if="isDuplicateObat(index)"
                                        class="mt-1 text-xs text-rose-500"
                                    >
                                        Obat ini sudah dipilih.
                                    </p>

                                </div>


                                <!-- JUMLAH -->

                                <div>

                                    <label
                                        class="mb-1.5 block text-xs font-bold text-slate-600"
                                    >
                                        Jumlah
                                    </label>


                                    <div class="relative">

                                        <input
                                            v-model.number="item.jumlah"
                                            type="number"
                                            min="1"
                                            :max="getStok(item.obat_id)"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 pr-16 text-sm text-slate-700 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                        />


                                        <span
                                            class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs font-medium text-slate-400"
                                        >
                                            {{ getSatuan(item.obat_id) }}
                                        </span>

                                    </div>


                                    <p
                                        v-if="item.obat_id"
                                        class="mt-1 text-[11px] text-slate-400"
                                    >
                                        Stok tersedia:
                                        {{ getStok(item.obat_id) }}
                                        {{ getSatuan(item.obat_id) }}
                                    </p>

                                </div>


                                <!-- HAPUS -->

                                <div
                                    class="flex items-end"
                                >

                                    <button
                                        type="button"
                                        @click="removeObat(index)"
                                        class="inline-flex h-[42px] items-center justify-center rounded-xl border border-rose-200 bg-white px-3 text-rose-500 transition hover:bg-rose-50"
                                        title="Hapus obat"
                                    >

                                        <TrashIcon
                                            class="h-5 w-5"
                                        />

                                    </button>

                                </div>

                            </div>


                            <!-- KETERANGAN -->

                            <div class="mt-4">

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-600"
                                >
                                    Keterangan / Aturan Pakai
                                </label>


                                <input
                                    v-model="item.keterangan"
                                    type="text"
                                    placeholder="Contoh: 2x sehari setelah makan"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"
                                />

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FOOTER ACTION
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
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </Link>


                <button
                    type="submit"
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
                            d="M21 12a9 9 0 00-9-9"
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

        </form>

    </div>

</template>