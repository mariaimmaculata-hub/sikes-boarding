<script setup>
import { computed, reactive, ref } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3'
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
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
// FORM
// ============================================================

const form = useForm({
    tanggal_pemeriksaan:
        props.pemeriksaan?.tanggal_pemeriksaan
        ?? new Date().toISOString().slice(0, 10),

    berat_badan:
        props.pemeriksaan?.berat_badan ?? '',

    tinggi_badan:
        props.pemeriksaan?.tinggi_badan ?? '',

    imt:
        props.pemeriksaan?.imt ?? '',

    tekanan_darah:
        props.pemeriksaan?.tekanan_darah ?? '',

    denyut_nadi:
        props.pemeriksaan?.denyut_nadi ?? '',

    suhu_tubuh:
        props.pemeriksaan?.suhu_tubuh ?? '',

    mata:
        props.pemeriksaan?.mata ?? '',

    telinga:
        props.pemeriksaan?.telinga ?? '',

    gigi_mulut:
        props.pemeriksaan?.gigi_mulut ?? '',

    kondisi_umum:
        props.pemeriksaan?.kondisi_umum ?? '',

    keluhan:
        props.pemeriksaan?.keluhan ?? '',

    hasil_pemeriksaan:
        props.pemeriksaan?.hasil_pemeriksaan ?? '',

    rekomendasi:
        props.pemeriksaan?.rekomendasi ?? '',

    status:
        props.pemeriksaan?.status ?? 'Sehat',

    catatan:
        props.pemeriksaan?.catatan ?? '',
})

// ============================================================
// STATE
// ============================================================

const showSuccess = ref(false)

// ============================================================
// JENIS PEMERIKSAAN
// ============================================================

const jenisLabel = computed(() => {
    return String(props.jenis) === '1'
        ? 'Pemeriksaan Berkala 1'
        : 'Pemeriksaan Berkala 2'
})

// ============================================================
// IMT OTOMATIS
// ============================================================

const calculatedImt = computed(() => {
    const berat = Number(form.berat_badan)
    const tinggi = Number(form.tinggi_badan)

    if (!berat || !tinggi || berat <= 0 || tinggi <= 0) {
        return ''
    }

    const tinggiMeter = tinggi / 100
    const hasil = berat / (tinggiMeter * tinggiMeter)

    return hasil.toFixed(2)
})

// Sinkronkan hasil IMT ke form
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
        `/klinik/kesehatan/pemeriksaan-berkala/${props.siswa.id}/${props.jenis}`,
        {
            preserveScroll: true,

            onSuccess: () => {
                showSuccess.value = true
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

                    <div
                        class="mb-1 flex items-center gap-2 text-xs font-semibold text-slate-400"
                    >
                        <ClipboardDocumentCheckIcon
                            class="h-4 w-4"
                        />

                        <span>
                            Kesehatan
                        </span>

                        <span>
                            /
                        </span>

                        <span>
                            Pemeriksaan Berkala
                        </span>
                    </div>

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


            <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">

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
                 PEMERIKSAAN DASAR
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


                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

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
                            Status Kesehatan
                            <span class="text-rose-500">*</span>
                        </label>

                        <select
                            v-model="form.status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                        >
                            <option value="Sehat">
                                Sehat
                            </option>

                            <option value="Perlu Pemantauan">
                                Perlu Pemantauan
                            </option>

                            <option value="Perlu Tindak Lanjut">
                                Perlu Tindak Lanjut
                            </option>
                        </select>
                    </div>

                </div>

            </section>


            <!-- ==================================================
                 ANTROPOMETRI
            ================================================== -->

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <h2 class="text-sm font-bold text-slate-800">
                        Antropometri
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Pengukuran berat badan, tinggi badan, dan indeks massa tubuh.
                    </p>

                </div>


                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

                    <!-- BERAT -->

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
                    </div>


                    <!-- TINGGI -->

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

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <h2 class="text-sm font-bold text-slate-800">
                        Tanda Vital
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Hasil pengukuran tanda-tanda vital siswa.
                    </p>

                </div>


                <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-3">

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
                                placeholder="Contoh: 110/70"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-16 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            />

                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                            >
                                mmHg
                            </span>

                        </div>
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
                                placeholder="Contoh: 78"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-14 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            />

                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                            >
                                bpm
                            </span>

                        </div>
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
                                placeholder="Contoh: 36.5"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 pr-12 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                            />

                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400"
                            >
                                °C
                            </span>

                        </div>
                    </div>

                </div>

            </section>


            <!-- ==================================================
                 PEMERIKSAAN FISIK
            ================================================== -->

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <h2 class="text-sm font-bold text-slate-800">
                        Pemeriksaan Fisik
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Kondisi fisik siswa berdasarkan hasil pemeriksaan.
                    </p>

                </div>


                <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2">

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
                    </div>


                    <!-- GIGI -->

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

                            <option value="Baik">
                                Baik
                            </option>

                            <option value="Cukup">
                                Cukup
                            </option>

                            <option value="Kurang Baik">
                                Kurang Baik
                            </option>

                        </select>
                    </div>

                </div>

            </section>


            <!-- ==================================================
                 KELUHAN DAN HASIL
            ================================================== -->

            <section
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

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

                    </div>


                    <!-- REKOMENDASI -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold text-slate-700"
                        >
                            Rekomendasi
                        </label>

                        <textarea
                            v-model="form.rekomendasi"
                            rows="3"
                            placeholder="Tuliskan rekomendasi atau tindak lanjut..."
                            class="w-full resize-none rounded-xl border border-slate-200 px-3.5 py-3 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-50"
                        ></textarea>

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

                    </div>

                </div>

            </section>


            <!-- ==================================================
                 INFO
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
