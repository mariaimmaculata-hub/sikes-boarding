<script setup>

import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    Link,
    useForm,
} from '@inertiajs/vue3'

import {
    BeakerIcon,
    ArrowLeftIcon,
    CheckIcon,
    ExclamationTriangleIcon,
    ArchiveBoxIcon,
    PlusIcon,
    CalendarDaysIcon,
    CubeIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({

    obat: {
        type: Object,
        required: true,
    },

})


// ======================================================
// FORM EDIT OBAT
// ======================================================

const form = useForm({

    nama_obat:
        props.obat.nama_obat ?? '',

    satuan:
        props.obat.satuan ?? '',

    keterangan:
        props.obat.keterangan ?? '',

})


// ======================================================
// FORM TAMBAH BATCH
// ======================================================

const batchForm = useForm({

    tanggal_masuk: '',

    tanggal_kadaluarsa: '',

    jumlah: 0,

})


// ======================================================
// STATE
// ======================================================

const showBatchForm = ref(false)


// ======================================================
// SUBMIT EDIT OBAT
// ======================================================

const submit = () => {

    form.put(
        route(
            'klinik.obat.update',
            props.obat.id
        ),
        {
            preserveScroll: true,
        }
    )

}


// ======================================================
// BUKA FORM TAMBAH BATCH
// ======================================================

const openBatchForm = () => {

    batchForm.reset()

    batchForm.clearErrors()

    batchForm.jumlah = 0

    showBatchForm.value = true

}


// ======================================================
// TUTUP FORM TAMBAH BATCH
// ======================================================

const closeBatchForm = () => {

    showBatchForm.value = false

    batchForm.reset()

    batchForm.clearErrors()

}


// ======================================================
// SUBMIT BATCH
// ======================================================

const submitBatch = () => {

    batchForm.post(
        route(
            'klinik.obat.batch.store',
            props.obat.id
        ),
        {
            preserveScroll: true,

            onSuccess: () => {

                closeBatchForm()

            },
        }
    )

}


// ======================================================
// ERROR EDIT
// ======================================================

const hasErrors = computed(() => {

    return Object.keys(form.errors).length > 0

})


// ======================================================
// ERROR EDIT
// ======================================================

const getError = (field) => {

    return form.errors[field] ?? null

}


// ======================================================
// ERROR BATCH
// ======================================================

const getBatchError = (field) => {

    return batchForm.errors[field] ?? null

}


// ======================================================
// TOTAL STOK
// ======================================================

const totalStok = computed(() => {

    return props.obat.batches?.reduce(
        (total, batch) => {

            return total + Number(batch.stok ?? 0)

        },
        0
    ) ?? 0

})


// ======================================================
// JUMLAH BATCH
// ======================================================

const jumlahBatch = computed(() => {

    return props.obat.batches?.length ?? 0

})


// ======================================================
// FORMAT TANGGAL
// ======================================================

const formatTanggal = (tanggal) => {

    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) {
        return tanggal
    }

    return date.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        }
    )

}


// ======================================================
// CEK EXPIRED
// ======================================================

const isExpired = (tanggal) => {

    if (!tanggal) {
        return false
    }

    const today = new Date()

    today.setHours(
        0,
        0,
        0,
        0
    )

    const expiredDate = new Date(
        tanggal
    )

    expiredDate.setHours(
        0,
        0,
        0,
        0
    )

    return expiredDate < today

}


// ======================================================
// CEK AKAN EXPIRED
// ======================================================

const isNearExpired = (tanggal) => {

    if (!tanggal) {
        return false
    }

    const today = new Date()

    today.setHours(
        0,
        0,
        0,
        0
    )

    const expiredDate = new Date(
        tanggal
    )

    expiredDate.setHours(
        0,
        0,
        0,
        0
    )

    const difference =
        expiredDate.getTime() -
        today.getTime()

    const days =
        difference /
        (
            1000 *
            60 *
            60 *
            24
        )

    return (
        days >= 0 &&
        days <= 30
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

                <div class="flex items-center gap-3">

                    <h1
                        class="text-2xl font-bold text-slate-800"
                    >
                        Edit Obat
                    </h1>

                    <span
                        class="rounded-full bg-pink-50 px-3 py-1 text-xs font-bold text-pink-600"
                    >
                        {{ jumlahBatch }} Batch
                    </span>

                </div>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Perbarui informasi obat dan kelola batch stok obat.
                </p>

            </div>


            <!-- KEMBALI -->

            <Link
                :href="route('klinik.obat.index')"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-pink-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-pink-50 hover:text-pink-700"
            >

                <ArrowLeftIcon
                    class="h-4 w-4"
                />

                Kembali

            </Link>

        </div>


        <!-- ==================================================
             ERROR SUMMARY
        ================================================== -->

        <div
            v-if="hasErrors"
            class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
        >

            <div
                class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100"
            >

                <ExclamationTriangleIcon
                    class="h-4 w-4 text-rose-600"
                />

            </div>


            <div>

                <p
                    class="text-sm font-bold text-rose-800"
                >
                    Periksa kembali data yang dimasukkan
                </p>

                <p
                    class="mt-0.5 text-xs text-rose-600"
                >
                    Terdapat beberapa data yang belum sesuai.
                </p>

            </div>

        </div>


        <!-- ==================================================
             INFORMASI OBAT
        ================================================== -->

        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
        >

            <!-- HEADER -->

            <div
                class="border-b border-pink-100 px-6 py-5"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50"
                    >

                        <BeakerIcon
                            class="h-5 w-5 text-pink-600"
                        />

                    </div>


                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Informasi Obat
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Perbarui identitas utama obat.
                        </p>

                    </div>

                </div>

            </div>


            <!-- BODY -->

            <div
                class="space-y-6 px-6 py-6"
            >


                <!-- ==================================================
                     NAMA OBAT
                ================================================== -->

                <div>

                    <label
                        for="nama_obat"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >

                        Nama Obat

                        <span class="text-rose-500">
                            *
                        </span>

                    </label>


                    <input
                        id="nama_obat"
                        v-model="form.nama_obat"
                        type="text"
                        placeholder="Contoh: Paracetamol"
                        autocomplete="off"
                        :class="[
                            'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',

                            getError('nama_obat')
                                ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                        ]"
                    />


                    <p
                        v-if="getError('nama_obat')"
                        class="mt-1.5 text-xs font-medium text-rose-600"
                    >
                        {{ getError('nama_obat') }}
                    </p>

                </div>


                <!-- ==================================================
                     SATUAN
                ================================================== -->

                <div>

                    <label
                        for="satuan"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >
                        Satuan
                    </label>


                    <input
                        id="satuan"
                        v-model="form.satuan"
                        type="text"
                        placeholder="Contoh: tablet, kapsul, botol"
                        autocomplete="off"
                        :class="[
                            'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',

                            getError('satuan')
                                ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                        ]"
                    />


                    <p
                        v-if="getError('satuan')"
                        class="mt-1.5 text-xs font-medium text-rose-600"
                    >
                        {{ getError('satuan') }}
                    </p>

                </div>


                <!-- ==================================================
                     KETERANGAN
                ================================================== -->

                <div>

                    <label
                        for="keterangan"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >
                        Keterangan
                    </label>


                    <textarea
                        id="keterangan"
                        v-model="form.keterangan"
                        rows="4"
                        placeholder="Masukkan keterangan tambahan mengenai obat..."
                        :class="[
                            'w-full resize-none rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',

                            getError('keterangan')
                                ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                        ]"
                    ></textarea>


                    <p
                        v-if="getError('keterangan')"
                        class="mt-1.5 text-xs font-medium text-rose-600"
                    >
                        {{ getError('keterangan') }}
                    </p>

                </div>


                <!-- ==================================================
                     INFO TOTAL STOK
                ================================================== -->

                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-2"
                >


                    <!-- TOTAL STOK -->

                    <div
                        class="rounded-xl border border-pink-100 bg-pink-50/60 p-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
                            >

                                <CubeIcon
                                    class="h-5 w-5 text-pink-600"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-xs font-semibold text-pink-500"
                                >
                                    TOTAL STOK
                                </p>

                                <p
                                    class="mt-1 text-xl font-bold text-pink-800"
                                >
                                    {{ totalStok }}
                                    {{ form.satuan || '' }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- JUMLAH BATCH -->

                    <div
                        class="rounded-xl border border-pink-100 bg-pink-50/60 p-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
                            >

                                <ArchiveBoxIcon
                                    class="h-5 w-5 text-pink-600"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-xs font-semibold text-pink-500"
                                >
                                    JUMLAH BATCH
                                </p>

                                <p
                                    class="mt-1 text-xl font-bold text-pink-800"
                                >
                                    {{ jumlahBatch }}
                                    Batch
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div
                class="flex flex-col-reverse gap-3 border-t border-pink-100 bg-pink-50/30 px-6 py-4 sm:flex-row sm:justify-end"
            >

                <Link
                    :href="route('klinik.obat.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-pink-100 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-pink-50 hover:text-pink-700"
                >
                    Batal
                </Link>


                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <CheckIcon
                        class="h-4 w-4"
                    />

                    <span>
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Perubahan'
                        }}
                    </span>

                </button>

            </div>

        </form>


        <!-- ==================================================
             BATCH SECTION
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
        >

            <!-- BATCH HEADER -->

            <div
                class="flex flex-col gap-4 border-b border-pink-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50"
                        >

                            <ArchiveBoxIcon
                                class="h-5 w-5 text-pink-600"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Riwayat Batch Obat
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Kelola stok berdasarkan tanggal masuk dan kadaluarsa.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TAMBAH BATCH -->

                <button
                    type="button"
                    @click="openBatchForm"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-pink-700"
                >

                    <PlusIcon
                        class="h-4 w-4"
                    />

                    Tambah Batch

                </button>

            </div>


            <!-- ==================================================
                 FORM TAMBAH BATCH
            ================================================== -->

            <div
                v-if="showBatchForm"
                class="border-b border-pink-100 bg-pink-50/40 px-6 py-6"
            >

                <div
                    class="mb-5 flex items-center justify-between"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Tambah Batch Baru
                        </h3>

                        <p
                            class="mt-1 text-xs text-slate-500"
                        >
                            Tambahkan stok baru untuk {{ props.obat.nama_obat }}.
                        </p>

                    </div>


                    <button
                        type="button"
                        @click="closeBatchForm"
                        class="text-sm font-semibold text-slate-500 transition hover:text-pink-600"
                    >
                        Batal
                    </button>

                </div>


                <!-- ERROR BATCH -->

                <div
                    v-if="Object.keys(batchForm.errors).length"
                    class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3"
                >

                    <p
                        class="text-sm font-semibold text-rose-700"
                    >
                        Periksa data batch terlebih dahulu.
                    </p>

                </div>


                <div
                    class="grid grid-cols-1 gap-5 md:grid-cols-3"
                >


                    <!-- TANGGAL MASUK -->

                    <div>

                        <label
                            for="batch_tanggal_masuk"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >

                            Tanggal Masuk

                            <span class="text-rose-500">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <CalendarDaysIcon
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="batch_tanggal_masuk"
                                v-model="batchForm.tanggal_masuk"
                                type="date"
                                :class="[
                                    'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:ring-2',

                                    getBatchError('tanggal_masuk')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />

                        </div>


                        <p
                            v-if="getBatchError('tanggal_masuk')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getBatchError('tanggal_masuk') }}
                        </p>

                    </div>


                    <!-- TANGGAL EXPIRED -->

                    <div>

                        <label
                            for="batch_tanggal_kadaluarsa"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >

                            Tanggal Kadaluarsa

                            <span class="text-rose-500">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <CalendarDaysIcon
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="batch_tanggal_kadaluarsa"
                                v-model="batchForm.tanggal_kadaluarsa"
                                type="date"
                                :min="batchForm.tanggal_masuk || undefined"
                                :class="[
                                    'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:ring-2',

                                    getBatchError('tanggal_kadaluarsa')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />

                        </div>


                        <p
                            v-if="getBatchError('tanggal_kadaluarsa')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getBatchError('tanggal_kadaluarsa') }}
                        </p>

                    </div>


                    <!-- JUMLAH -->

                    <div>

                        <label
                            for="batch_jumlah"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >

                            Jumlah

                            <span class="text-rose-500">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <ArchiveBoxIcon
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                id="batch_jumlah"
                                v-model.number="batchForm.jumlah"
                                type="number"
                                min="1"
                                step="1"
                                placeholder="Contoh: 100"
                                :class="[
                                    'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:ring-2',

                                    getBatchError('jumlah')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />

                        </div>


                        <p
                            v-if="getBatchError('jumlah')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getBatchError('jumlah') }}
                        </p>

                    </div>

                </div>


                <!-- SIMPAN BATCH -->

                <div
                    class="mt-5 flex justify-end"
                >

                    <button
                        type="button"
                        @click="submitBatch"
                        :disabled="batchForm.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-pink-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <PlusIcon
                            class="h-4 w-4"
                        />

                        {{
                            batchForm.processing
                                ? 'Menyimpan...'
                                : 'Simpan Batch'
                        }}

                    </button>

                </div>

            </div>


            <!-- ==================================================
                 RIWAYAT BATCH
            ================================================== -->

            <div class="px-6 py-6">


                <!-- KOSONG -->

                <div
                    v-if="!props.obat.batches?.length"
                    class="rounded-xl border border-dashed border-pink-200 bg-pink-50/30 px-6 py-10 text-center"
                >

                    <ArchiveBoxIcon
                        class="mx-auto h-10 w-10 text-pink-200"
                    />

                    <p
                        class="mt-3 text-sm font-semibold text-slate-600"
                    >
                        Belum ada batch
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Tambahkan batch pertama untuk obat ini.
                    </p>

                </div>


                <!-- TABEL -->

                <div
                    v-else
                    class="overflow-x-auto"
                >

                    <table
                        class="w-full min-w-[760px] text-left"
                    >

                        <thead>

                            <tr
                                class="border-b border-pink-100"
                            >

                                <th
                                    class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    #
                                </th>

                                <th
                                    class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Tanggal Masuk
                                </th>

                                <th
                                    class="px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Kadaluarsa
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Jumlah Awal
                                </th>

                                <th
                                    class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Stok Saat Ini
                                </th>

                                <th
                                    class="px-4 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-pink-50"
                        >

                            <tr
                                v-for="(batch, index) in props.obat.batches"
                                :key="batch.id"
                                class="transition hover:bg-pink-50/40"
                            >

                                <!-- NOMOR -->

                                <td
                                    class="px-4 py-4 text-sm font-semibold text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <!-- MASUK -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <div
                                        class="flex items-center gap-2"
                                    >

                                        <CalendarDaysIcon
                                            class="h-4 w-4 text-pink-400"
                                        />

                                        <span
                                            class="text-sm font-medium text-slate-700"
                                        >
                                            {{ formatTanggal(batch.tanggal_masuk) }}
                                        </span>

                                    </div>

                                </td>


                                <!-- EXP -->

                                <td
                                    class="px-4 py-4"
                                >

                                    <span
                                        class="text-sm font-medium"
                                        :class="
                                            isExpired(batch.tanggal_kadaluarsa)
                                                ? 'text-rose-600'
                                                : isNearExpired(batch.tanggal_kadaluarsa)
                                                    ? 'text-amber-600'
                                                    : 'text-slate-700'
                                        "
                                    >

                                        {{ formatTanggal(batch.tanggal_kadaluarsa) }}

                                    </span>

                                </td>


                                <!-- JUMLAH -->

                                <td
                                    class="px-4 py-4 text-right"
                                >

                                    <span
                                        class="text-sm font-semibold text-slate-700"
                                    >

                                        {{ batch.jumlah }}

                                        {{ form.satuan || '' }}

                                    </span>

                                </td>


                                <!-- STOK -->

                                <td
                                    class="px-4 py-4 text-right"
                                >

                                    <span
                                        class="text-sm font-bold"
                                        :class="
                                            batch.stok > 0
                                                ? 'text-pink-600'
                                                : 'text-slate-400'
                                        "
                                    >

                                        {{ batch.stok }}

                                        {{ form.satuan || '' }}

                                    </span>

                                </td>


                                <!-- STATUS -->

                                <td
                                    class="px-4 py-4 text-center"
                                >

                                    <span
                                        v-if="batch.stok <= 0"
                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500"
                                    >
                                        Habis
                                    </span>


                                    <span
                                        v-else-if="isExpired(batch.tanggal_kadaluarsa)"
                                        class="inline-flex rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-600"
                                    >
                                        Expired
                                    </span>


                                    <span
                                        v-else-if="isNearExpired(batch.tanggal_kadaluarsa)"
                                        class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-600"
                                    >
                                        Segera Expired
                                    </span>


                                    <span
                                        v-else
                                        class="inline-flex rounded-full bg-pink-50 px-2.5 py-1 text-xs font-bold text-pink-600"
                                    >
                                        Aktif
                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</KlinikLayout>

</template>
