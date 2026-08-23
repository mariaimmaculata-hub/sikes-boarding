<script setup>
import { computed, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    InformationCircleIcon,
    UserIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()

// ============================================================
// PROPS
// ============================================================

const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },

    periode: {
        type: Object,
        required: true,
    },

    jenis: {
        type: [String, Number],
        required: true,
    },

    pemeriksaan: {
        type: Object,
        default: null,
    },
})

// ============================================================
// DATA USER
// ============================================================

const user = computed(() => page.props.auth?.user)

// ============================================================
// JENIS PEMERIKSAAN
// ============================================================

const jenisLabel = computed(() => {
    return String(props.jenis) === 'berkala_1'
        ? 'Pemeriksaan Berkala 1'
        : 'Pemeriksaan Berkala 2'
})

// ============================================================
// FORM
// ============================================================

const form = useForm({
    // ========================================================
    // DATA PEMERIKSAAN
    // ========================================================

    tanggal_pemeriksaan:
        props.pemeriksaan?.tanggal_pemeriksaan
        ?? new Date().toISOString().slice(0, 10),

    status:
        props.pemeriksaan?.status ?? 'belum',

    // ========================================================
    // ANTROPOMETRI
    // ========================================================

    berat_badan:
        props.pemeriksaan?.berat_badan ?? '',

    tinggi_badan:
        props.pemeriksaan?.tinggi_badan ?? '',

    imt:
        props.pemeriksaan?.imt ?? '',

    // ========================================================
    // TANDA VITAL
    // ========================================================

    tekanan_darah:
        props.pemeriksaan?.tekanan_darah ?? '',

    denyut_nadi:
        props.pemeriksaan?.denyut_nadi ?? '',

    suhu_tubuh:
        props.pemeriksaan?.suhu_tubuh ?? '',

    saturasi_oksigen:
        props.pemeriksaan?.saturasi_oksigen ?? '',

    // ========================================================
    // PEMERIKSAAN FISIK
    // ========================================================

    mata:
        props.pemeriksaan?.mata ?? '',

    telinga:
        props.pemeriksaan?.telinga ?? '',

    gigi_mulut:
        props.pemeriksaan?.gigi_mulut ?? '',

    kondisi_umum:
        props.pemeriksaan?.kondisi_umum ?? '',

    // ========================================================
    // KEBERSIHAN TUBUH
    // DARI KEPALA SAMPAI KAKI
    // ========================================================

    kebersihan_rambut:
        props.pemeriksaan?.kebersihan_rambut ?? '',

    kebersihan_wajah:
        props.pemeriksaan?.kebersihan_wajah ?? '',

    kebersihan_telinga:
        props.pemeriksaan?.kebersihan_telinga ?? '',

    kebersihan_hidung:
        props.pemeriksaan?.kebersihan_hidung ?? '',

    kebersihan_mulut_gigi:
        props.pemeriksaan?.kebersihan_mulut_gigi ?? '',

    kebersihan_tangan_kuku:
        props.pemeriksaan?.kebersihan_tangan_kuku ?? '',

    kebersihan_kulit_badan:
        props.pemeriksaan?.kebersihan_kulit_badan ?? '',

    kebersihan_kaki_kuku:
        props.pemeriksaan?.kebersihan_kaki_kuku ?? '',

    // ========================================================
    // HASIL PEMERIKSAAN
    // ========================================================

    keluhan:
        props.pemeriksaan?.keluhan ?? '',

    hasil_pemeriksaan:
        props.pemeriksaan?.hasil_pemeriksaan ?? '',

    rekomendasi:
        props.pemeriksaan?.rekomendasi ?? '',

    // ========================================================
    // CATATAN
    // ========================================================

    catatan:
        props.pemeriksaan?.catatan ?? '',
})

// ============================================================
// STATE
// ============================================================

const showSuccess = ref(false)

// ============================================================
// IMT OTOMATIS
// ============================================================

const calculatedImt = computed(() => {
    const berat = Number(form.berat_badan)
    const tinggi = Number(form.tinggi_badan)

    if (
        !berat ||
        !tinggi ||
        berat <= 0 ||
        tinggi <= 0
    ) {
        return ''
    }

    const tinggiMeter = tinggi / 100

    const hasil =
        berat / (tinggiMeter * tinggiMeter)

    return hasil.toFixed(2)
})

// ============================================================
// UPDATE IMT
// ============================================================

const updateImt = () => {
    form.imt = calculatedImt.value
}

// ============================================================
// KATEGORI IMT
// ============================================================

const kategoriImt = computed(() => {
    const imt = Number(calculatedImt.value)

    if (!imt) {
        return ''
    }

    if (imt < 18.5) {
        return 'Berat badan kurang'
    }

    if (imt < 25) {
        return 'Berat badan normal'
    }

    if (imt < 30) {
        return 'Berat badan berlebih'
    }

    return 'Obesitas'
})

// ============================================================
// ERROR HELPER
// ============================================================

const errorFor = (field) => {
    return form.errors[field] ?? ''
}

// ============================================================
// SUBMIT
// ============================================================

const submit = () => {
    updateImt()

    form.post(
        route(
            'klinik.kesehatan.pemeriksaan.store',
            {
                siswa: props.siswa.id,
                jenis: props.jenis,
            }
        ),
        {
            preserveScroll: true,

            onSuccess: () => {
                showSuccess.value = true
            },

            onError: (errors) => {
                console.log('VALIDATION ERROR:', errors)
            },

            onFinish: () => {
                console.log('Submit selesai')
            },
        }
    )
}

// ============================================================
// BATAL
// ============================================================

const cancel = () => {
    window.history.back()
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

                <div class="flex items-start gap-3">

                    <Link
                        href="/klinik/kesehatan/pemeriksaan-berkala"
                        class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 shadow-sm transition hover:bg-slate-50 hover:text-blue-700"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                    </Link>

                    <div>

                        <h1
                            class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl"
                        >
                            {{ jenisLabel }}
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Lengkapi hasil pemeriksaan kesehatan siswa.
                        </p>

                    </div>

                </div>

                <!-- PERIODE -->

                <div
                    class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-2.5"
                >

                    <div
                        class="text-[10px] font-bold uppercase tracking-wider text-blue-500"
                    >
                        Periode Aktif
                    </div>

                    <div class="mt-0.5 text-sm font-bold text-blue-900">
                        {{ periode.nama_periode }}
                    </div>

                </div>

            </div>


            <!-- ==================================================
                 IDENTITAS SISWA
            ================================================== -->

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex items-center gap-3 border-b border-slate-100 px-5 py-4"
                >

                    <div
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                    >
                        <UserIcon class="h-5 w-5" />
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Identitas Siswa
                        </h2>

                        <p class="text-xs text-slate-400">
                            Data siswa diambil otomatis dari data master.
                        </p>
                    </div>

                </div>

                <div
                    class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4"
                >

                    <!-- NISN -->

                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                        >
                            NISN
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ siswa.nisn || '-' }}
                        </p>
                    </div>

                    <!-- NAMA -->

                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                        >
                            Nama Siswa
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ siswa.nama || '-' }}
                        </p>
                    </div>

                    <!-- KELAS -->

                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                        >
                            Kelas
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ siswa.kelas?.nama_kelas || '-' }}
                        </p>
                    </div>

                    <!-- JURUSAN -->

                    <div>
                        <p
                            class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                        >
                            Jurusan
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ siswa.kelas?.jurusan?.nama_jurusan || '-' }}
                        </p>
                    </div>

                </div>

            </section>


            <!-- ==================================================
                 FORM
            ================================================== -->

            <form
                @submit.prevent="submit"
                class="space-y-6"
            >

                <!-- ==================================================
                     DATA PEMERIKSAAN
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="flex items-center gap-3 border-b border-slate-100 px-5 py-4"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"
                        >
                            <CalendarDaysIcon class="h-5 w-5" />
                        </div>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Data Pemeriksaan
                            </h2>

                            <p class="text-xs text-slate-400">
                                Informasi dasar pemeriksaan kesehatan.
                            </p>
                        </div>

                    </div>

                    <div
                        class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
                    >

                        <!-- TANGGAL -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Tanggal Pemeriksaan
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                v-model="form.tanggal_pemeriksaan"
                                type="date"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                :class="{
                                    'border-rose-300 focus:border-rose-500 focus:ring-rose-50':
                                        errorFor('tanggal_pemeriksaan')
                                }"
                            />

                            <p
                                v-if="errorFor('tanggal_pemeriksaan')"
                                class="mt-1 text-xs font-medium text-rose-500"
                            >
                                {{ errorFor('tanggal_pemeriksaan') }}
                            </p>

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Status Pemeriksaan
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                v-model="form.status"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            >

                                <option value="belum">
                                    Belum Selesai
                                </option>

                                <option value="selesai">
                                    Selesai
                                </option>

                            </select>

                            <p
                                v-if="errorFor('status')"
                                class="mt-1 text-xs font-medium text-rose-500"
                            >
                                {{ errorFor('status') }}
                            </p>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     ANTROPOMETRI
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="text-sm font-bold text-slate-800">
                            Antropometri
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Pengukuran berat badan, tinggi badan, dan indeks massa tubuh.
                        </p>

                    </div>

                    <div
                        class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3"
                    >

                        <!-- BERAT BADAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Berat Badan
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.berat_badan"
                                    @input="updateImt"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="Contoh: 55"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    kg
                                </span>

                            </div>

                            <p
                                v-if="errorFor('berat_badan')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('berat_badan') }}
                            </p>

                        </div>


                        <!-- TINGGI BADAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Tinggi Badan
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.tinggi_badan"
                                    @input="updateImt"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    placeholder="Contoh: 168"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    cm
                                </span>

                            </div>

                            <p
                                v-if="errorFor('tinggi_badan')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('tinggi_badan') }}
                            </p>

                        </div>


                        <!-- IMT -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                IMT
                            </label>

                            <div class="relative">

                                <input
                                    :value="calculatedImt"
                                    type="text"
                                    readonly
                                    placeholder="-"
                                    class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 pr-16 text-sm font-semibold text-slate-700 outline-none"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    kg/m²
                                </span>

                            </div>

                            <p
                                v-if="kategoriImt"
                                class="mt-1.5 text-xs font-semibold text-blue-600"
                            >
                                {{ kategoriImt }}
                            </p>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     TANDA VITAL
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="text-sm font-bold text-slate-800">
                            Tanda Vital
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Hasil pengukuran tanda-tanda vital siswa.
                        </p>

                    </div>

                    <div
                        class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4"
                    >

                        <!-- TEKANAN DARAH -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Tekanan Darah
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.tekanan_darah"
                                    type="text"
                                    placeholder="110/70"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-16 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    mmHg
                                </span>

                            </div>

                            <p
                                v-if="errorFor('tekanan_darah')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('tekanan_darah') }}
                            </p>

                        </div>


                        <!-- NADI -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Denyut Nadi
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.denyut_nadi"
                                    type="number"
                                    min="0"
                                    placeholder="78"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-14 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    bpm
                                </span>

                            </div>

                            <p
                                v-if="errorFor('denyut_nadi')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('denyut_nadi') }}
                            </p>

                        </div>


                        <!-- SUHU -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Suhu Tubuh
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.suhu_tubuh"
                                    type="number"
                                    min="0"
                                    step="0.1"
                                    placeholder="36.5"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    °C
                                </span>

                            </div>

                            <p
                                v-if="errorFor('suhu_tubuh')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('suhu_tubuh') }}
                            </p>

                        </div>


                        <!-- SATURASI -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Saturasi Oksigen
                            </label>

                            <div class="relative">

                                <input
                                    v-model="form.saturasi_oksigen"
                                    type="number"
                                    min="0"
                                    max="100"
                                    step="0.1"
                                    placeholder="98"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-10 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                                />

                                <span
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                                >
                                    %
                                </span>

                            </div>

                            <p
                                v-if="errorFor('saturasi_oksigen')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('saturasi_oksigen') }}
                            </p>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     PEMERIKSAAN FISIK
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-purple-50 text-purple-600"
                            >
                                <ClipboardDocumentCheckIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Pemeriksaan Fisik
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Pemeriksaan kondisi fisik siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2"
                    >

                        <!-- MATA -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Kondisi Mata
                            </label>

                            <select
                                v-model="form.mata"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            >

                                <option value="">
                                    Pilih kondisi
                                </option>

                                <option value="Normal">
                                    Normal
                                </option>

                                <option value="Perlu Pemeriksaan">
                                    Perlu Pemeriksaan
                                </option>

                                <option value="Gangguan">
                                    Gangguan
                                </option>

                            </select>

                            <p
                                v-if="errorFor('mata')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('mata') }}
                            </p>

                        </div>


                        <!-- TELINGA -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Kondisi Telinga
                            </label>

                            <select
                                v-model="form.telinga"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            >

                                <option value="">
                                    Pilih kondisi
                                </option>

                                <option value="Normal">
                                    Normal
                                </option>

                                <option value="Perlu Pemeriksaan">
                                    Perlu Pemeriksaan
                                </option>

                                <option value="Gangguan">
                                    Gangguan
                                </option>

                            </select>

                            <p
                                v-if="errorFor('telinga')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('telinga') }}
                            </p>

                        </div>


                        <!-- GIGI MULUT -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Kondisi Gigi & Mulut
                            </label>

                            <select
                                v-model="form.gigi_mulut"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            >

                                <option value="">
                                    Pilih kondisi
                                </option>

                                <option value="Normal">
                                    Normal
                                </option>

                                <option value="Perlu Perawatan">
                                    Perlu Perawatan
                                </option>

                                <option value="Gangguan">
                                    Gangguan
                                </option>

                            </select>

                            <p
                                v-if="errorFor('gigi_mulut')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('gigi_mulut') }}
                            </p>

                        </div>


                        <!-- KONDISI UMUM -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Kondisi Umum
                            </label>

                            <select
                                v-model="form.kondisi_umum"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            >

                                <option value="">
                                    Pilih kondisi
                                </option>

                                <option value="Sehat">
                                    Sehat
                                </option>

                                <option value="Perlu Pemantauan">
                                    Perlu Pemantauan
                                </option>

                                <option value="Tidak Sehat">
                                    Tidak Sehat
                                </option>

                            </select>

                            <p
                                v-if="errorFor('kondisi_umum')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('kondisi_umum') }}
                            </p>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     KEBERSIHAN TUBUH
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div
                        class="border-b border-slate-100 px-5 py-4"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600"
                            >
                                <ClipboardDocumentCheckIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Pemeriksaan Kebersihan Diri
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Pemeriksaan kebersihan siswa dari ujung kepala hingga kaki.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div
                            class="mb-5 rounded-xl border border-cyan-100 bg-cyan-50 p-4"
                        >

                            <div class="flex items-start gap-3">

                                <InformationCircleIcon
                                    class="mt-0.5 h-5 w-5 shrink-0 text-cyan-600"
                                />

                                <div>

                                    <p class="text-xs font-bold text-cyan-900">
                                        Pemeriksaan Kebersihan
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-cyan-700">
                                        Periksa kebersihan secara berurutan mulai dari rambut,
                                        wajah, telinga, hidung, mulut dan gigi, tangan dan kuku,
                                        kulit tubuh, hingga kaki dan kuku.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div
                            class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3"
                        >

                            <!-- RAMBUT -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    1. Kebersihan Rambut
                                </label>

                                <select
                                    v-model="form.kebersihan_rambut"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_rambut')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_rambut') }}
                                </p>

                            </div>


                            <!-- WAJAH -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    2. Kebersihan Wajah
                                </label>

                                <select
                                    v-model="form.kebersihan_wajah"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_wajah')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_wajah') }}
                                </p>

                            </div>


                            <!-- TELINGA -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    3. Kebersihan Telinga
                                </label>

                                <select
                                    v-model="form.kebersihan_telinga"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_telinga')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_telinga') }}
                                </p>

                            </div>


                            <!-- HIDUNG -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    4. Kebersihan Hidung
                                </label>

                                <select
                                    v-model="form.kebersihan_hidung"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_hidung')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_hidung') }}
                                </p>

                            </div>


                            <!-- MULUT GIGI -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    5. Kebersihan Mulut & Gigi
                                </label>

                                <select
                                    v-model="form.kebersihan_mulut_gigi"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_mulut_gigi')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_mulut_gigi') }}
                                </p>

                            </div>


                            <!-- TANGAN KUKU -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    6. Kebersihan Tangan & Kuku
                                </label>

                                <select
                                    v-model="form.kebersihan_tangan_kuku"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_tangan_kuku')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_tangan_kuku') }}
                                </p>

                            </div>


                            <!-- KULIT BADAN -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    7. Kebersihan Kulit & Badan
                                </label>

                                <select
                                    v-model="form.kebersihan_kulit_badan"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_kulit_badan')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_kulit_badan') }}
                                </p>

                            </div>


                            <!-- KAKI KUKU -->

                            <div>

                                <label
                                    class="mb-1.5 block text-xs font-bold text-slate-700"
                                >
                                    8. Kebersihan Kaki & Kuku
                                </label>

                                <select
                                    v-model="form.kebersihan_kaki_kuku"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-cyan-500 focus:ring-4 focus:ring-cyan-50"
                                >

                                    <option value="">
                                        Pilih kondisi
                                    </option>

                                    <option value="Bersih">
                                        Bersih
                                    </option>

                                    <option value="Cukup Bersih">
                                        Cukup Bersih
                                    </option>

                                    <option value="Kotor">
                                        Kotor
                                    </option>

                                </select>

                                <p
                                    v-if="errorFor('kebersihan_kaki_kuku')"
                                    class="mt-1 text-xs text-rose-500"
                                >
                                    {{ errorFor('kebersihan_kaki_kuku') }}
                                </p>

                            </div>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     KELUHAN & HASIL
                ================================================== -->

                <section
                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                >

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="text-sm font-bold text-slate-800">
                            Keluhan & Hasil Pemeriksaan
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-400">
                            Catat keluhan, hasil pemeriksaan, dan tindak lanjut.
                        </p>

                    </div>


                    <div class="space-y-5 p-5">

                        <!-- KELUHAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Keluhan
                            </label>

                            <textarea
                                v-model="form.keluhan"
                                rows="3"
                                placeholder="Tuliskan keluhan siswa jika ada..."
                                class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            ></textarea>

                            <p
                                v-if="errorFor('keluhan')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('keluhan') }}
                            </p>

                        </div>


                        <!-- HASIL -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Hasil Pemeriksaan
                            </label>

                            <textarea
                                v-model="form.hasil_pemeriksaan"
                                rows="4"
                                placeholder="Tuliskan hasil pemeriksaan secara keseluruhan..."
                                class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            ></textarea>

                            <p
                                v-if="errorFor('hasil_pemeriksaan')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('hasil_pemeriksaan') }}
                            </p>

                        </div>


                        <!-- REKOMENDASI -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Rekomendasi / Tindak Lanjut
                            </label>

                            <textarea
                                v-model="form.rekomendasi"
                                rows="3"
                                placeholder="Tuliskan rekomendasi atau tindak lanjut..."
                                class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            ></textarea>

                            <p
                                v-if="errorFor('rekomendasi')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('rekomendasi') }}
                            </p>

                        </div>


                        <!-- CATATAN -->

                        <div>

                            <label
                                class="mb-1.5 block text-xs font-bold text-slate-700"
                            >
                                Catatan Tambahan
                            </label>

                            <textarea
                                v-model="form.catatan"
                                rows="3"
                                placeholder="Catatan tambahan jika diperlukan..."
                                class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            ></textarea>

                            <p
                                v-if="errorFor('catatan')"
                                class="mt-1 text-xs text-rose-500"
                            >
                                {{ errorFor('catatan') }}
                            </p>

                        </div>

                    </div>

                </section>


                <!-- ==================================================
                     INFORMASI
                ================================================== -->

                <div
                    class="flex items-start gap-3 rounded-2xl border border-blue-100 bg-blue-50 p-4"
                >

                    <InformationCircleIcon
                        class="mt-0.5 h-5 w-5 shrink-0 text-blue-600"
                    />

                    <div class="text-xs leading-5 text-blue-800">

                        <p class="font-bold">
                            Informasi Pemeriksaan
                        </p>

                        <p class="mt-0.5 text-blue-700">
                            Data pemeriksaan akan disimpan sebagai
                            {{ jenisLabel }}
                            pada periode
                            {{ periode.nama_periode }}.
                        </p>

                        <p class="mt-1 text-blue-700">
                            Pemeriksaan dilakukan secara menyeluruh mulai
                            dari pengukuran tubuh, tanda vital, pemeriksaan
                            fisik, hingga kebersihan diri siswa.
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     ACTION
                ================================================== -->

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >

                    <button
                        type="button"
                        @click="cancel"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </button>


                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-6 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <CheckCircleIcon
                            class="h-5 w-5"
                        />

                        <span v-if="!form.processing">
                            Simpan Pemeriksaan
                        </span>

                        <span v-else>
                            Menyimpan...
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </KlinikLayout>
</template>