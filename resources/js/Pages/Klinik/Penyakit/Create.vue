<script setup>

import { computed } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    Link,
    useForm,
} from '@inertiajs/vue3'

import {
    ExclamationTriangleIcon,
    ArrowLeftIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// FORM
// ======================================================

const form = useForm({
    nama_penyakit: '',
    kategori: '',
    keterangan: '',
})


// ======================================================
// SUBMIT
// ======================================================

const submit = () => {

    form.post(
        route('klinik.penyakit.store'),
        {
            preserveScroll: true,
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

                <!-- TITLE -->

                <div class="flex items-center gap-3">

                    <!-- ICON -->

                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-600"
                    >

                        <ExclamationTriangleIcon
                            class="h-6 w-6"
                        />

                    </div>


                    <!-- TEXT -->

                    <div>

                        <h1
                            class="text-xl font-bold text-slate-900 lg:text-2xl"
                        >
                            Tambah Penyakit
                        </h1>


                        <p
                            class="mt-0.5 text-sm text-slate-500"
                        >
                            Tambahkan data penyakit baru ke dalam data penyakit klinik.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 KEMBALI
            ================================================== -->

            <Link
                :href="route('klinik.penyakit.index')"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-pink-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-pink-200 hover:bg-pink-50 hover:text-pink-700"
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
            class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
        >


            <!-- ==================================================
                 FORM HEADER
            ================================================== -->

            <div
                class="border-b border-pink-100 px-6 py-5"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <!-- ICON -->

                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50 text-pink-600"
                    >

                        <ExclamationTriangleIcon
                            class="h-5 w-5"
                        />

                    </div>


                    <!-- TEXT -->

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Informasi Penyakit
                        </h2>

                        <p
                            class="mt-0.5 text-xs text-slate-400"
                        >
                            Isi informasi penyakit yang akan digunakan dalam pelayanan klinik.
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
                     NAMA PENYAKIT
                ================================================== -->

                <div>

                    <label
                        for="nama_penyakit"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >

                        Nama Penyakit

                        <span class="text-pink-500">*</span>

                    </label>


                    <input
                        id="nama_penyakit"
                        v-model="form.nama_penyakit"
                        type="text"
                        placeholder="Contoh: Influenza"
                        autocomplete="off"
                        :class="[
                            'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                            getError('nama_penyakit')
                                ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                        ]"
                    />


                    <p
                        v-if="getError('nama_penyakit')"
                        class="mt-1.5 text-xs font-medium text-rose-600"
                    >
                        {{ getError('nama_penyakit') }}
                    </p>


                    <p
                        v-else
                        class="mt-1.5 text-xs text-slate-400"
                    >
                        Masukkan nama penyakit secara jelas dan spesifik.
                    </p>

                </div>


                <!-- ==================================================
                     KATEGORI
                ================================================== -->

                <div>

                    <label
                        for="kategori"
                        class="mb-1.5 block text-sm font-semibold text-slate-700"
                    >

                        Kategori

                    </label>


                    <input
                        id="kategori"
                        v-model="form.kategori"
                        type="text"
                        placeholder="Contoh: Infeksi, Pernapasan, Pencernaan"
                        autocomplete="off"
                        :class="[
                            'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                            getError('kategori')
                                ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                : 'border-pink-100 focus:border-pink-500 focus:ring-pink-100'
                        ]"
                    />


                    <p
                        v-if="getError('kategori')"
                        class="mt-1.5 text-xs font-medium text-rose-600"
                    >
                        {{ getError('kategori') }}
                    </p>


                    <p
                        v-else
                        class="mt-1.5 text-xs text-slate-400"
                    >
                        Contoh: Infeksi, Pernapasan, Pencernaan, Kulit.
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
                        placeholder="Masukkan keterangan tambahan mengenai penyakit..."
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


                    <p
                        v-else
                        class="mt-1.5 text-xs text-slate-400"
                    >
                        Keterangan bersifat opsional.
                    </p>

                </div>


                <!-- ==================================================
                     PREVIEW
                ================================================== -->

                <div
                    class="rounded-xl border border-pink-100 bg-pink-50/70 p-4"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <!-- ICON -->

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-pink-100 text-pink-600"
                        >

                            <ExclamationTriangleIcon
                                class="h-4 w-4"
                            />

                        </div>


                        <!-- PREVIEW CONTENT -->

                        <div
                            class="min-w-0"
                        >

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-pink-500"
                            >
                                Preview
                            </p>


                            <p
                                class="mt-1 text-sm font-bold text-slate-800"
                            >
                                {{ form.nama_penyakit || 'Nama penyakit' }}
                            </p>


                            <p
                                class="mt-1 text-xs text-slate-500"
                            >

                                Kategori:

                                <span class="font-medium text-pink-700">
                                    {{ form.kategori || 'Belum ditentukan' }}
                                </span>

                            </p>


                            <p
                                v-if="form.keterangan"
                                class="mt-1 text-xs leading-relaxed text-slate-500"
                            >
                                {{ form.keterangan }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FOOTER
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 border-t border-pink-100 bg-pink-50/30 px-6 py-4 sm:flex-row sm:justify-end"
            >


                <!-- ==================================================
                     BATAL
                ================================================== -->

                <Link
                    :href="route('klinik.penyakit.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-pink-200 hover:bg-pink-50 hover:text-pink-700"
                >

                    Batal

                </Link>


                <!-- ==================================================
                     SIMPAN
                ================================================== -->

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <CheckIcon
                        class="h-4 w-4"
                    />

                    <span>
                        {{
                            form.processing
                                ? 'Menyimpan...'
                                : 'Simpan Penyakit'
                        }}
                    </span>

                </button>

            </div>

        </form>

    </div>

</KlinikLayout>

</template>