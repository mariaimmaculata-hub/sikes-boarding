<script setup>
import { computed, ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    ArrowUpTrayIcon,
    DocumentTextIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline'


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

const page = usePage()


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    kelas: {
        type: Array,
        default: () => [],
    },

    jurusan: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({
    file: null,
})


/*
|--------------------------------------------------------------------------
| FILE INPUT
|--------------------------------------------------------------------------
*/

const fileInput = ref(null)


/*
|--------------------------------------------------------------------------
| IMPORT ERRORS
|--------------------------------------------------------------------------
|
| Controller mengirim:
|
| -> with('import_errors', $errors)
|
*/

const importErrors = computed(() => {
    return page.props.flash?.import_errors ?? []
})


/*
|--------------------------------------------------------------------------
| SUCCESS MESSAGE
|--------------------------------------------------------------------------
*/

const successMessage = computed(() => {
    return page.props.flash?.success ?? null
})


/*
|--------------------------------------------------------------------------
| PILIH FILE
|--------------------------------------------------------------------------
*/

const pilihFile = (event) => {
    const file = event.target.files?.[0] ?? null

    form.clearErrors()

    if (!file) {
        form.file = null
        return
    }

    form.file = file
}


/*
|--------------------------------------------------------------------------
| HAPUS FILE
|--------------------------------------------------------------------------
*/

const hapusFile = () => {
    form.file = null

    form.clearErrors()

    if (fileInput.value) {
        fileInput.value.value = ''
    }
}


/*
|--------------------------------------------------------------------------
| SUBMIT IMPORT
|--------------------------------------------------------------------------
*/

const submit = () => {
    if (!form.file) {
        form.setError(
            'file',
            'Silakan pilih file yang ingin diimport.'
        )

        return
    }

    form.post(
        route('admin.master.siswa.import.store'),
        {
            /*
            |--------------------------------------------------------------------------
            | PENTING
            |--------------------------------------------------------------------------
            |
            | Karena ada file upload, Inertia WAJIB menggunakan
            | multipart/form-data.
            |
            */
            forceFormData: true,

            /*
            |--------------------------------------------------------------------------
            | PROSES SELESAI
            |--------------------------------------------------------------------------
            */

            onSuccess: () => {
                form.reset('file')

                if (fileInput.value) {
                    fileInput.value.value = ''
                }
            },

            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            onError: () => {
                // Error akan otomatis masuk ke form.errors
            },
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

            <div class="flex items-start gap-3">

                <Link
                    :href="route('admin.master.siswa.index')"
                    class="mt-1 rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Import Data Siswa
                    </h1>

                    <p class="mt-1 text-sm text-slate-500">
                        Import data siswa secara massal menggunakan CSV,
                        Excel, atau PDF.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 SUCCESS
            ================================================== -->

            <div
                v-if="successMessage"
                class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-emerald-100"
                    >
                        <span class="text-sm font-bold text-emerald-600">
                            ✓
                        </span>
                    </div>

                    <div>

                        <p class="text-sm font-semibold text-emerald-700">
                            Import berhasil
                        </p>

                        <p class="mt-1 text-sm text-emerald-600">
                            {{ successMessage }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 IMPORT
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- HEADER CARD -->

                <div class="border-b border-slate-100 px-5 py-4">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600"
                        >
                            <ArrowUpTrayIcon class="h-5 w-5" />
                        </div>

                        <div>

                            <h2 class="text-sm font-bold text-slate-800">
                                Upload File
                            </h2>

                            <p class="mt-0.5 text-xs text-slate-400">
                                Pilih file data siswa yang ingin diimport.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- CONTENT -->

                <div class="p-5">


                    <!-- ==================================================
                         FILE INPUT
                    ================================================== -->

                    <label
                        for="file"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 px-6 py-12 text-center transition hover:border-blue-300 hover:bg-blue-50/30"
                        :class="{
                            'border-rose-300 bg-rose-50/30':
                                form.errors.file
                        }"
                    >

                        <DocumentTextIcon
                            class="mb-4 h-12 w-12 text-slate-400"
                        />

                        <p class="text-sm font-semibold text-slate-700">
                            Pilih file untuk diimport
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            CSV, XLS, XLSX, atau PDF
                        </p>

                        <input
                            ref="fileInput"
                            id="file"
                            name="file"
                            type="file"
                            accept=".csv,.xls,.xlsx,.pdf,text/csv,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/pdf"
                            class="hidden"
                            @change="pilihFile"
                        />

                    </label>


                    <!-- ==================================================
                         FILE TERPILIH
                    ================================================== -->

                    <div
                        v-if="form.file"
                        class="mt-4 flex items-center gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4"
                    >

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white"
                        >

                            <DocumentTextIcon
                                class="h-6 w-6 text-blue-600"
                            />

                        </div>


                        <div class="min-w-0 flex-1">

                            <p
                                class="truncate text-sm font-semibold text-slate-700"
                            >
                                {{ form.file.name }}
                            </p>

                            <p class="mt-0.5 text-xs text-slate-400">
                                {{
                                    (form.file.size / 1024).toFixed(1)
                                }}
                                KB
                            </p>

                        </div>


                        <!-- HAPUS -->

                        <button
                            type="button"
                            class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-50"
                            @click="hapusFile"
                            :disabled="form.processing"
                        >
                            Hapus
                        </button>

                    </div>


                    <!-- ==================================================
                         ERROR UPLOAD
                    ================================================== -->

                    <div
                        v-if="form.errors.file"
                        class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4"
                    >

                        <div class="flex items-start gap-3">

                            <div class="shrink-0">

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100"
                                >

                                    <span
                                        class="text-sm font-bold text-rose-600"
                                    >
                                        !
                                    </span>

                                </div>

                            </div>


                            <div>

                                <p class="text-sm font-semibold text-rose-700">
                                    Import gagal
                                </p>

                                <p class="mt-1 text-sm text-rose-600">
                                    {{ form.errors.file }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- ==================================================
                         ERROR IMPORT PER BARIS
                    ================================================== -->

                    <div
                        v-if="importErrors.length > 0"
                        class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4"
                    >

                        <div class="flex items-start gap-3">

                            <div class="shrink-0">

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100"
                                >

                                    <span
                                        class="text-sm font-bold text-amber-600"
                                    >
                                        !
                                    </span>

                                </div>

                            </div>


                            <div class="min-w-0 flex-1">

                                <p class="text-sm font-semibold text-amber-800">
                                    Beberapa data tidak diimport
                                </p>

                                <p class="mt-1 text-xs text-amber-600">
                                    Data berikut dilewati karena tidak
                                    memenuhi ketentuan import.
                                </p>


                                <ul
                                    class="mt-3 max-h-60 space-y-1 overflow-y-auto text-sm text-amber-700"
                                >

                                    <li
                                        v-for="(error, index) in importErrors"
                                        :key="index"
                                    >
                                        • {{ error }}
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FORMAT DATA
            ================================================== -->

            <div
                class="rounded-2xl border border-blue-100 bg-blue-50 p-5"
            >

                <div class="flex gap-3">

                    <InformationCircleIcon
                        class="h-5 w-5 shrink-0 text-blue-600"
                    />

                    <div class="min-w-0 text-sm text-slate-600">

                        <p class="font-semibold text-slate-800">
                            Format kolom CSV / Excel
                        </p>

                        <p class="mt-1">
                            Baris pertama harus berisi nama kolom.
                        </p>


                        <!-- HEADER FORMAT -->

                        <div
                            class="mt-3 overflow-x-auto rounded-lg bg-white p-3 font-mono text-xs text-slate-600"
                        >
                            nisn,nama,tempat_lahir,tanggal_lahir,jenis_kelamin,kelas,jurusan,angkatan,alamat,no_hp,nama_orang_tua,no_hp_orang_tua,status
                        </div>


                        <p class="mt-3">
                            Contoh:
                        </p>


                        <!-- CONTOH DATA -->

                        <div
                            class="mt-2 overflow-x-auto rounded-lg bg-white p-3 font-mono text-xs text-slate-600"
                        >
                            0012345678,Ahmad,Pemalang,2010-01-20,L,10,TKJ,2026,Pemalang,081234567890,Budi,081234567891,aktif
                        </div>


                        <!-- CATATAN -->

                        <div
                            class="mt-4 rounded-lg border border-blue-100 bg-white p-3"
                        >

                            <p class="text-xs font-semibold text-slate-700">
                                Catatan:
                            </p>

                            <ul class="mt-2 space-y-1 text-xs text-slate-500">

                                <li>
                                    • Jenis kelamin gunakan
                                    <strong>L</strong> atau
                                    <strong>P</strong>.
                                </li>

                                <li>
                                    • Kelas dapat menggunakan
                                    <strong>10</strong>,
                                    <strong>11</strong>,
                                    <strong>12</strong> atau
                                    <strong>X</strong>,
                                    <strong>XI</strong>,
                                    <strong>XII</strong>.
                                </li>

                                <li>
                                    • Jurusan harus sama dengan
                                    <strong>nama jurusan</strong>
                                    yang ada di database.
                                </li>

                                <li>
                                    • NISN tidak boleh sudah terdaftar.
                                </li>

                                <li>
                                    • Status dapat berupa
                                    <strong>aktif</strong>,
                                    <strong>nonaktif</strong>, atau
                                    <strong>lulus</strong>.
                                </li>

                            </ul>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 ACTION
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
            >

                <Link
                    :href="route('admin.master.siswa.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </Link>


                <button
                    type="button"
                    @click="submit"
                    :disabled="!form.file || form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <ArrowUpTrayIcon
                        v-if="!form.processing"
                        class="h-5 w-5"
                    />

                    <svg
                        v-else
                        class="h-5 w-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >

                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />

                    </svg>


                    <span>
                        {{
                            form.processing
                                ? 'Mengimport...'
                                : 'Import Data'
                        }}
                    </span>

                </button>

            </div>


            <!-- ==================================================
                 DEBUG STATUS
            ================================================== -->

            <div
                v-if="form.processing"
                class="rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-700"
            >

                <div class="flex items-center gap-2">

                    <svg
                        class="h-4 w-4 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >

                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        />

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
                        />

                    </svg>

                    <span>
                        Sedang mengupload dan memproses file...
                    </span>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>