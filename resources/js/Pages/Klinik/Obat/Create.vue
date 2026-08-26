<script setup>

import { computed } from 'vue'
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
    CalendarDaysIcon,
    ArchiveBoxIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// FORM
// ======================================================

const form = useForm({

    // DATA OBAT
    nama_obat: '',
    satuan: '',
    keterangan: '',

    // BATCH PERTAMA
    tanggal_masuk: '',
    tanggal_kadaluarsa: '',
    jumlah: 0,

})


// ======================================================
// SUBMIT
// ======================================================

const submit = () => {

    form.post(
        route('klinik.obat.store'),
        {
            preserveScroll: true,

            onSuccess: () => {
                form.reset()
            },
        }
    )

}


// ======================================================
// ERROR STATE
// ======================================================

const hasErrors = computed(() => {

    return Object.keys(form.errors).length > 0

})


// ======================================================
// FORMAT ERROR
// ======================================================

const getError = (field) => {

    return form.errors[field] ?? null

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

                <h1
                    class="mt-1 text-2xl font-bold text-slate-800"
                >
                    Tambah Obat
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Tambahkan obat baru beserta batch pertama dan stok awal.
                </p>

            </div>


            <!-- KEMBALI -->

            <Link
                :href="route('klinik.obat.index')"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
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
             FORM CARD
        ================================================== -->

        <form
            @submit.prevent="submit"
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >


            <!-- ==================================================
                 FORM HEADER
            ================================================== -->

            <div
                class="border-b border-slate-100 px-6 py-5"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50"
                    >

                        <BeakerIcon
                            class="h-5 w-5 text-blue-600"
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
                            Isi informasi dasar obat yang akan disimpan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FORM BODY
            ================================================== -->

            <div
                class="space-y-6 px-6 py-6"
            >


                <!-- ==================================================
                     INFORMASI DASAR OBAT
                ================================================== -->

                <div>

                    <div class="mb-4">

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Informasi Dasar Obat
                        </h3>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Data ini akan menjadi identitas utama obat.
                        </p>

                    </div>


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
                                    : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                            ]"
                        />


                        <p
                            v-if="getError('nama_obat')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getError('nama_obat') }}
                        </p>


                        <p
                            v-else
                            class="mt-1.5 text-xs text-slate-400"
                        >
                            Nama obat harus unik. Jika obat sudah ada, gunakan menu tambah batch.
                        </p>

                    </div>


                    <!-- ==================================================
                         SATUAN
                    ================================================== -->

                    <div class="mt-5">

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
                                    : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                            ]"
                        />


                        <p
                            v-if="getError('satuan')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getError('satuan') }}
                        </p>


                        <p
                            v-else
                            class="mt-1.5 text-xs text-slate-400"
                        >
                            Contoh: tablet, kapsul, botol, sachet.
                        </p>

                    </div>


                    <!-- ==================================================
                         KETERANGAN
                    ================================================== -->

                    <div class="mt-5">

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
                                    : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                            ]"
                        ></textarea>


                        <p
                            v-if="getError('keterangan')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getError('keterangan') }}
                        </p>


                        <p
                            v-else
                            class="mt-1.5 text-xs text-slate-400"
                        >
                            Keterangan bersifat opsional.
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     PEMBATAS
                ================================================== -->

                <div class="border-t border-slate-100"></div>


                <!-- ==================================================
                     BATCH PERTAMA
                ================================================== -->

                <div>

                    <div class="mb-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50"
                            >

                                <ArchiveBoxIcon
                                    class="h-5 w-5 text-emerald-600"
                                />

                            </div>


                            <div>

                                <h3
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Batch Pertama
                                </h3>

                                <p
                                    class="mt-1 text-xs text-slate-400"
                                >
                                    Tentukan tanggal masuk, kadaluarsa, dan jumlah stok awal.
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         TANGGAL MASUK + TANGGAL KADALUARSA
                    ================================================== -->

                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >


                        <!-- TANGGAL MASUK -->

                        <div>

                            <label
                                for="tanggal_masuk"
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
                                    id="tanggal_masuk"
                                    v-model="form.tanggal_masuk"
                                    type="date"
                                    :class="[
                                        'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:ring-2',

                                        getError('tanggal_masuk')
                                            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                            : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                    ]"
                                />

                            </div>


                            <p
                                v-if="getError('tanggal_masuk')"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ getError('tanggal_masuk') }}
                            </p>


                            <p
                                v-else
                                class="mt-1.5 text-xs text-slate-400"
                            >
                                Tanggal obat masuk ke klinik.
                            </p>

                        </div>


                        <!-- TANGGAL KADALUARSA -->

                        <div>

                            <label
                                for="tanggal_kadaluarsa"
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
                                    id="tanggal_kadaluarsa"
                                    v-model="form.tanggal_kadaluarsa"
                                    type="date"
                                    :min="form.tanggal_masuk || undefined"
                                    :class="[
                                        'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition focus:ring-2',

                                        getError('tanggal_kadaluarsa')
                                            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                            : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                    ]"
                                />

                            </div>


                            <p
                                v-if="getError('tanggal_kadaluarsa')"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ getError('tanggal_kadaluarsa') }}
                            </p>


                            <p
                                v-else
                                class="mt-1.5 text-xs text-slate-400"
                            >
                                Tidak boleh sebelum tanggal masuk.
                            </p>

                        </div>

                    </div>


                    <!-- ==================================================
                         JUMLAH
                    ================================================== -->

                    <div class="mt-5">

                        <label
                            for="jumlah"
                            class="mb-1.5 block text-sm font-semibold text-slate-700"
                        >

                            Jumlah Stok Awal

                            <span class="text-rose-500">
                                *
                            </span>

                        </label>


                        <div class="relative">

                            <ArchiveBoxIcon
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                            />


                            <input
                                id="jumlah"
                                v-model.number="form.jumlah"
                                type="number"
                                min="1"
                                step="1"
                                placeholder="Contoh: 100"
                                :class="[
                                    'w-full rounded-xl border bg-white py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',

                                    getError('jumlah')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                        </div>


                        <p
                            v-if="getError('jumlah')"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ getError('jumlah') }}
                        </p>


                        <p
                            v-else
                            class="mt-1.5 text-xs text-slate-400"
                        >
                            Jumlah ini otomatis menjadi stok awal batch pertama.
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     INFO BATCH
                ================================================== -->

                <div
                    class="rounded-xl border border-emerald-100 bg-emerald-50 p-4"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100"
                        >

                            <ArchiveBoxIcon
                                class="h-4 w-4 text-emerald-600"
                            />

                        </div>


                        <div class="min-w-0">

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-emerald-600"
                            >
                                Batch Pertama
                            </p>


                            <p
                                class="mt-1 text-sm font-bold text-emerald-800"
                            >
                                Stok akan disimpan sebagai batch
                            </p>


                            <p
                                class="mt-1 text-xs leading-5 text-emerald-700"
                            >
                                Obat yang sama tidak perlu dibuat ulang.
                                Jika nanti Paracetamol datang dengan tanggal
                                kadaluarsa berbeda, gunakan menu
                                <strong>Tambah Batch</strong>.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     PREVIEW
                ================================================== -->

                <div
                    class="rounded-xl border border-blue-100 bg-blue-50 p-4"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100"
                        >

                            <BeakerIcon
                                class="h-4 w-4 text-blue-600"
                            />

                        </div>


                        <div
                            class="min-w-0"
                        >

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-blue-500"
                            >
                                Preview
                            </p>


                            <p
                                class="mt-1 text-sm font-bold text-blue-800"
                            >
                                {{ form.nama_obat || 'Nama obat' }}
                            </p>


                            <div
                                class="mt-2 grid grid-cols-1 gap-1 text-xs text-blue-600 sm:grid-cols-3"
                            >

                                <p>

                                    <span class="font-semibold">
                                        Jumlah:
                                    </span>

                                    {{ form.jumlah || 0 }}
                                    {{ form.satuan || '' }}

                                </p>


                                <p>

                                    <span class="font-semibold">
                                        Masuk:
                                    </span>

                                    {{ form.tanggal_masuk || '-' }}

                                </p>


                                <p>

                                    <span class="font-semibold">
                                        Exp:
                                    </span>

                                    {{ form.tanggal_kadaluarsa || '-' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FOOTER
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4 sm:flex-row sm:justify-end"
            >


                <!-- BATAL -->

                <Link
                    :href="route('klinik.obat.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </Link>


                <!-- SIMPAN -->

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <CheckIcon
                        class="h-4 w-4"
                    />

                    <span>

                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Obat'
                        }}

                    </span>

                </button>

            </div>

        </form>

    </div>

</KlinikLayout>

</template>