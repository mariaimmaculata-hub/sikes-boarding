<script setup>

import KlinikLayout from '@/Layouts/KlinikLayout.vue'

import {
    Link,
} from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    PencilSquareIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    penyakit: {
        type: Object,
        required: true,
    },
})

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
                    Detail Penyakit
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Informasi lengkap mengenai data penyakit yang tersimpan di klinik.
                </p>

            </div>


            <!-- AKSI -->

            <div
                class="flex flex-col gap-2 sm:flex-row"
            >

                <!-- KEMBALI -->

                <Link
                    :href="route('klinik.penyakit.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50"
                >

                    <ArrowLeftIcon
                        class="h-4 w-4"
                    />

                    Kembali

                </Link>


                <!-- EDIT -->

                <Link
                    :href="route(
                        'klinik.penyakit.edit',
                        props.penyakit.id
                    )"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >

                    <PencilSquareIcon
                        class="h-4 w-4"
                    />

                    Edit Penyakit

                </Link>

            </div>

        </div>


        <!-- ==================================================
             MAIN CARD
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >


            <!-- ==================================================
                 CARD HEADER
            ================================================== -->

            <div
                class="border-b border-slate-100 px-6 py-6"
            >

                <div
                    class="flex flex-col gap-4 sm:flex-row sm:items-center"
                >


                    <!-- ICON -->

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-rose-50"
                    >

                        <ExclamationTriangleIcon
                            class="h-7 w-7 text-rose-600"
                        />

                    </div>


                    <!-- TITLE -->

                    <div
                        class="min-w-0"
                    >

                        <p
                            class="text-xs font-bold uppercase tracking-wider text-rose-500"
                        >
                            Data Penyakit
                        </p>


                        <h2
                            class="mt-1 text-xl font-bold text-slate-800"
                        >

                            {{
                                props.penyakit.nama_penyakit
                                    || '-'
                            }}

                        </h2>


                        <p
                            class="mt-1 text-sm text-slate-400"
                        >

                            ID Data:
                            #{{ props.penyakit.id }}

                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 INFORMASI
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2"
            >


                <!-- ==================================================
                     NAMA PENYAKIT
                ================================================== -->

                <div
                    class="rounded-xl border border-slate-200 bg-slate-50/60 p-5"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-100"
                        >

                            <ExclamationTriangleIcon
                                class="h-4 w-4 text-rose-600"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Nama Penyakit
                            </p>


                            <p
                                class="mt-1 text-sm font-bold text-slate-800"
                            >

                                {{
                                    props.penyakit.nama_penyakit
                                        || '-'
                                }}

                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     KATEGORI
                ================================================== -->

                <div
                    class="rounded-xl border border-slate-200 bg-slate-50/60 p-5"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-100"
                        >

                            <InformationCircleIcon
                                class="h-4 w-4 text-blue-600"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Kategori
                            </p>


                            <span
                                v-if="props.penyakit.kategori"
                                class="mt-1 inline-flex items-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700"
                            >

                                {{ props.penyakit.kategori }}

                            </span>


                            <p
                                v-else
                                class="mt-1 text-sm font-semibold text-slate-400"
                            >
                                Tidak ada kategori
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     KETERANGAN
                ================================================== -->

                <div
                    class="md:col-span-2 rounded-xl border border-slate-200 bg-white p-5"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100"
                        >

                            <InformationCircleIcon
                                class="h-4 w-4 text-slate-600"
                            />

                        </div>


                        <div
                            class="min-w-0 flex-1"
                        >

                            <p
                                class="text-xs font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Keterangan
                            </p>


                            <p
                                v-if="props.penyakit.keterangan"
                                class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600"
                            >

                                {{ props.penyakit.keterangan }}

                            </p>


                            <p
                                v-else
                                class="mt-2 text-sm italic text-slate-400"
                            >

                                Tidak ada keterangan tambahan.

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 INFORMASI SISTEM
            ================================================== -->

            <div
                class="border-t border-slate-100 bg-slate-50/50 px-6 py-5"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100"
                    >

                        <InformationCircleIcon
                            class="h-4 w-4 text-slate-500"
                        />

                    </div>


                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Informasi Data
                        </p>


                        <p
                            class="mt-1 text-xs leading-5 text-slate-400"
                        >

                            Data penyakit ini digunakan sebagai referensi
                            dalam pencatatan pelayanan kesehatan siswa di klinik.

                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FOOTER
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:justify-end"
            >


                <!-- KEMBALI -->

                <Link
                    :href="route('klinik.penyakit.index')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >

                    <ArrowLeftIcon
                        class="h-4 w-4"
                    />

                    Kembali

                </Link>


                <!-- EDIT -->

                <Link
                    :href="route(
                        'klinik.penyakit.edit',
                        props.penyakit.id
                    )"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                >

                    <PencilSquareIcon
                        class="h-4 w-4"
                    />

                    Edit Penyakit

                </Link>

            </div>

        </div>

    </div>

</KlinikLayout>

</template>
