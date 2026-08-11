<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    UserIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
} from '@heroicons/vue/24/outline'

import TksiLayout from '@/Layouts/TksiLayout.vue'

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    siswaTerpilih: {
        type: Object,
        default: null,
    },

    komponen: {
        type: Array,
        default: () => [],
    },

    hasil: {
        type: Object,
        default: () => ({}),
    },

    flash: {
        type: Object,
        default: () => ({}),
    },
})

const tanggal = ref(
    new Date().toISOString().slice(0, 10)
)

const form = reactive({
    hasil: {},
})

const errors = ref({})
const processing = ref(false)

/*
|--------------------------------------------------------------------------
| Ambil data siswa
|--------------------------------------------------------------------------
|
| Controller sudah mengirim siswaTerpilih.
| Kalau belum ada, coba cari dari array siswa.
|
*/

const siswa = computed(() => {
    if (props.siswaTerpilih) {
        return props.siswaTerpilih
    }

    return props.siswa[0] ?? null
})

/*
|--------------------------------------------------------------------------
| Inisialisasi form
|--------------------------------------------------------------------------
|
| WAJIB dibuat untuk semua komponen supaya:
| form.hasil[item.key].nilai
| tidak undefined.
|
*/

function initForm() {
    const hasilLama = props.hasil || {}

    const data = {}

    props.komponen.forEach(item => {
        data[item.key] = {
            komponen: item.key,
            nilai: hasilLama[item.key]?.nilai ?? '',
            catatan: hasilLama[item.key]?.catatan ?? '',
        }
    })

    form.hasil = data

    /*
    | Jika ada tanggal dari hasil lama,
    | gunakan tanggal tersebut.
    */
    const tanggalLama = Object.values(hasilLama)[0]?.tanggal

    if (tanggalLama) {
        tanggal.value = tanggalLama
    }
}

initForm()

/*
|--------------------------------------------------------------------------
| Judul halaman
|--------------------------------------------------------------------------
*/

const sudahLengkap = computed(() => {
    return props.komponen.length > 0 &&
        props.komponen.every(item => {
            const nilai = form.hasil[item.key]?.nilai
            return nilai !== '' && nilai !== null && nilai !== undefined
        })
})

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

function submit() {
    errors.value = {}
    processing.value = true

    const hasil = Object.values(form.hasil).map(item => ({
        komponen: item.komponen,
        nilai: item.nilai,
        catatan: item.catatan || null,
    }))

    router.post(
        route('tksi.input.store'),
        {
            siswa_id: siswa.value.id,
            tanggal: tanggal.value,
            hasil,
        },
        {
            preserveScroll: true,

            onError: err => {
                errors.value = err
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>


<template>
    <TksiLayout>

        <Head
            :title="sudahLengkap ? 'Edit Hasil TKSI' : 'Input Hasil TKSI'"
        />

        <div class="space-y-6">

            <!-- HEADER -->
            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 p-6 text-white shadow-lg md:p-8"
            >

                <div
                    class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-16 right-20 h-48 w-48 rounded-full bg-white/5"
                ></div>

                <div class="relative z-10">

                    <div class="flex items-center gap-3">

                        <Link
                            :href="route('tksi.input.index')"
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition hover:bg-white/20"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>

                        <div>

                            <h1 class="text-2xl font-bold tracking-tight">
                                {{ sudahLengkap
                                    ? 'Edit Hasil TKSI'
                                    : 'Input Hasil TKSI'
                                }}
                            </h1>

                            <p class="mt-1 text-sm font-medium text-white/80">
                                {{
                                    sudahLengkap
                                        ? 'Perbarui hasil tes kebugaran siswa.'
                                        : 'Masukkan hasil tes kebugaran siswa.'
                                }}
                            </p>

                        </div>

                    </div>

                </div>
            </div>


            <!-- TIDAK ADA PERIODE -->
            <div
                v-if="!periode"
                class="rounded-2xl border border-rose-100 bg-rose-50 p-6"
            >

                <p class="text-sm font-extrabold text-rose-700">
                    Tidak ada periode aktif.
                </p>

                <p class="mt-1 text-xs text-rose-600">
                    Input TKSI belum dapat dilakukan sampai admin
                    mengaktifkan periode.
                </p>

            </div>


            <template v-if="periode && siswa">

                <!-- PERIODE -->
                <div
                    class="rounded-2xl border border-blue-100 bg-blue-50 p-5"
                >

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700"
                        >
                            <CalendarDaysIcon class="h-5 w-5" />
                        </div>

                        <div class="min-w-0">

                            <p
                                class="text-[10px] font-extrabold uppercase tracking-wider text-blue-500"
                            >
                                Periode Aktif
                            </p>

                            <h2
                                class="mt-1 text-base font-extrabold text-blue-900"
                            >
                                {{ periode.nama_periode }}
                            </h2>

                            <p class="mt-1 text-xs font-medium text-blue-700">
                                {{ periode.tanggal_mulai }}
                                —
                                {{ periode.tanggal_selesai }}
                            </p>

                        </div>

                        <span
                            class="ml-auto rounded-full bg-emerald-100 px-3 py-1 text-[9px] font-extrabold uppercase text-emerald-700"
                        >
                            Aktif
                        </span>

                    </div>

                </div>


                <!-- DATA SISWA -->
                <div
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                        >
                            <UserIcon class="h-5 w-5" />
                        </div>

                        <div>

                            <h2 class="text-sm font-extrabold text-slate-800">
                                Data Peserta
                            </h2>

                            <p class="text-xs text-slate-400">
                                Siswa yang mengikuti tes TKSI.
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4"
                    >

                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                NISN
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.nisn || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Nama
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.nama }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Kelas
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.kelas?.nama_kelas || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Jenis Kelamin
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.jenis_kelamin || '-' }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TANGGAL -->
                <div
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                >

                    <label
                        class="mb-2 block text-xs font-bold text-slate-600"
                    >
                        Tanggal Tes
                    </label>

                    <input
                        v-model="tanggal"
                        type="date"
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100 md:w-1/2"
                    />

                    <p
                        v-if="errors.tanggal"
                        class="mt-1 text-xs font-semibold text-rose-600"
                    >
                        {{ errors.tanggal }}
                    </p>

                </div>


                <!-- HASIL TES -->
                <div>

                    <div class="mb-4">

                        <h2 class="text-base font-extrabold text-slate-800">
                            Hasil Tes TKSI
                        </h2>

                        <p class="mt-1 text-xs font-medium text-slate-400">
                            Masukkan hasil setiap komponen tes.
                        </p>

                    </div>


                    <div
                        v-for="(item, index) in komponen"
                        :key="item.key"
                        class="mb-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm"
                    >

                        <div class="flex items-start gap-4">

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xs font-extrabold text-blue-700"
                            >
                                {{ String(index + 1).padStart(2, '0') }}
                            </div>


                            <div class="min-w-0 flex-1">

                                <h3
                                    class="text-sm font-extrabold text-slate-800"
                                >
                                    {{ item.nama }}
                                </h3>

                                <p
                                    class="mt-1 text-xs leading-5 text-slate-400"
                                >
                                    {{ item.deskripsi }}
                                </p>


                                <div
                                    class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3"
                                >

                                    <!-- NILAI -->
                                    <div>

                                        <label
                                            class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                        >
                                            Nilai
                                        </label>

                                        <div class="relative">

                                            <input
                                                v-model="form.hasil[item.key].nilai"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="Masukkan nilai"
                                                class="w-full rounded-xl border border-slate-200 px-4 py-3 pr-24 text-sm font-bold text-slate-800 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                            />

                                            <span
                                                class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400"
                                            >
                                                {{ item.satuan }}
                                            </span>

                                        </div>

                                        <p
                                            v-if="errors[`hasil.${index}.nilai`]"
                                            class="mt-1 text-xs font-semibold text-rose-600"
                                        >
                                            {{ errors[`hasil.${index}.nilai`] }}
                                        </p>

                                    </div>


                                    <!-- CATATAN -->
                                    <div class="md:col-span-2">

                                        <label
                                            class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                        >
                                            Catatan
                                        </label>

                                        <input
                                            v-model="form.hasil[item.key].catatan"
                                            type="text"
                                            placeholder="Catatan hasil tes (opsional)"
                                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                        />

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- BUTTON -->
                <div
                    class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:flex-row sm:justify-end"
                >

                    <Link
                        :href="route('tksi.input.index')"
                        class="rounded-xl border border-slate-200 px-5 py-3 text-center text-xs font-extrabold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </Link>

                    <button
                        type="button"
                        @click="submit"
                        :disabled="processing"
                        class="rounded-xl bg-blue-900 px-6 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50"
                    >

                        {{ processing
                            ? 'Menyimpan...'
                            : sudahLengkap
                                ? 'Perbarui Hasil TKSI'
                                : 'Simpan Hasil TKSI'
                        }}

                    </button>

                </div>

            </template>


            <!-- SISWA TIDAK DITEMUKAN -->
            <div
                v-if="periode && !siswa"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-6"
            >

                <p class="text-sm font-extrabold text-amber-700">
                    Siswa tidak ditemukan.
                </p>

                <p class="mt-1 text-xs text-amber-600">
                    Silakan kembali ke daftar peserta TKSI.
                </p>

                <Link
                    :href="route('tksi.input.index')"
                    class="mt-4 inline-flex rounded-xl bg-blue-900 px-4 py-2.5 text-xs font-bold text-white"
                >
                    Kembali ke Daftar Siswa
                </Link>

            </div>

        </div>

    </TksiLayout>
</template>

