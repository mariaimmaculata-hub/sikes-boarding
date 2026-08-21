<script setup>

import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { usePage, Link } from '@inertiajs/vue3'

import {
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
    ClipboardDocumentCheckIcon,
    ExclamationTriangleIcon,
    CheckCircleIcon,
    LockClosedIcon,
    EyeIcon,
    CalendarDaysIcon,
    UserIcon,
    HeartIcon,
    ScaleIcon,
    InformationCircleIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({

    periode: {
        type: Object,
        default: null,
    },

    siswas: {
        type: Array,
        default: () => [],
    },

})


// ======================================================
// PAGE
// ======================================================

const page = usePage()


// ======================================================
// STATE
// ======================================================

const search = ref('')
const showFilter = ref(false)
const kelasFilter = ref('')
const statusFilter = ref('')


// ======================================================
// POPUP HASIL
// ======================================================

const showResultModal = ref(false)
const selectedPemeriksaan = ref(null)
const selectedSiswa = ref(null)


// ======================================================
// POPUP BERKALA
// ======================================================

const popupBerkala = ref(null)
const popupClickLocked = ref(false)


// ======================================================
// FLASH
// ======================================================

const flashSuccess = computed(() => {
    return page.props.flash?.success ?? null
})

const flashError = computed(() => {
    return page.props.flash?.error ?? null
})


// ======================================================
// FASE PEMERIKSAAN
// ======================================================

const fasePemeriksaan = computed(() => {

    return Number(
        props.periode?.fase_pemeriksaan ?? 0
    )

})


// ======================================================
// STATUS PERIODE
// ======================================================

const periodeAktif = computed(() => {

    return props.periode?.status === 'aktif'

})

const periodeSelesai = computed(() => {

    return props.periode?.status === 'selesai'

})


// ======================================================
// AKSES BERKALA 1
// ======================================================

const aksesBerkala1 = computed(() => {

    return props.periode?.berkala_1?.akses ?? 'closed'

})


// ======================================================
// AKSES BERKALA 2
// ======================================================

const aksesBerkala2 = computed(() => {

    return props.periode?.berkala_2?.akses ?? 'closed'

})


// ======================================================
// STATUS AKTIF BERDASARKAN FASE + STATUS PERIODE
// ======================================================

const berkala1Aktif = computed(() => {

    return (
        periodeAktif.value &&
        fasePemeriksaan.value === 1 &&
        aksesBerkala1.value === 'open'
    )

})


const berkala2Aktif = computed(() => {

    return (
        periodeAktif.value &&
        fasePemeriksaan.value === 2 &&
        aksesBerkala2.value === 'open'
    )

})


// ======================================================
// VIEW BERKALA 1
// ======================================================

const berkala1View = computed(() => {

    // Kalau periode selesai, semua hasil hanya bisa dilihat
    if (periodeSelesai.value) {
        return true
    }

    // Kalau sudah masuk fase 2,
    // Berkala 1 menjadi view only
    if (fasePemeriksaan.value === 2) {
        return true
    }

    return aksesBerkala1.value === 'view'

})


// ======================================================
// VIEW BERKALA 2
// ======================================================

const berkala2View = computed(() => {

    if (periodeSelesai.value) {
        return true
    }

    return aksesBerkala2.value === 'view'

})


// ======================================================
// LABEL TAHAP
// ======================================================

const tahapLabel = computed(() => {

    if (fasePemeriksaan.value === 1) {
        return 'Berkala 1'
    }

    if (fasePemeriksaan.value === 2) {
        return 'Berkala 2'
    }

    return '-'

})


// ======================================================
// DESKRIPSI TAHAP
// ======================================================

const tahapDescription = computed(() => {

    if (periodeSelesai.value) {

        return 'Periode pemeriksaan telah selesai. Hasil Berkala 1 dan Berkala 2 hanya dapat dilihat.'

    }

    if (fasePemeriksaan.value === 1) {

        return 'Pemeriksaan Berkala 1 sedang aktif dan dapat diisi atau diedit.'

    }

    if (fasePemeriksaan.value === 2) {

        return 'Pemeriksaan Berkala 1 telah selesai. Saat ini memasuki tahap Berkala 2.'

    }

    return 'Tahap pemeriksaan belum ditentukan.'

})


// ======================================================
// LABEL STATUS PERIODE
// ======================================================

const statusPeriodeLabel = computed(() => {

    if (periodeSelesai.value) {
        return 'Periode Selesai'
    }

    if (periodeAktif.value) {
        return 'Periode Aktif'
    }

    return 'Status Tidak Diketahui'

})


// ======================================================
// FILTER OPTIONS
// ======================================================

const kelasOptions = computed(() => {

    return [
        ...new Set(
            props.siswas
                .map(siswa => siswa.kelas?.tingkat)
                .filter(Boolean)
        ),
    ].sort()

})


// ======================================================
// STATUS SISWA
// ======================================================

const getStatus = (siswa) => {

    if (fasePemeriksaan.value === 1) {

        return siswa.berkala_1?.status === 'selesai'
            ? 'selesai'
            : 'belum'

    }

    if (fasePemeriksaan.value === 2) {

        return siswa.berkala_2?.status === 'selesai'
            ? 'selesai'
            : 'belum'

    }

    /*
    |--------------------------------------------------------------------------
    | Kalau periode sudah selesai
    |--------------------------------------------------------------------------
    | Status keseluruhan siswa dianggap selesai jika kedua
    | pemeriksaan sudah selesai.
    |--------------------------------------------------------------------------
    */

    if (periodeSelesai.value) {

        const b1Selesai =
            siswa.berkala_1?.status === 'selesai'

        const b2Selesai =
            siswa.berkala_2?.status === 'selesai'

        return (
            b1Selesai || b2Selesai
        )
            ? 'selesai'
            : 'belum'

    }

    return 'belum'

}


// ======================================================
// STATUS LABEL
// ======================================================

const getStatusLabel = (siswa) => {

    return getStatus(siswa) === 'selesai'
        ? 'Selesai'
        : 'Belum Selesai'

}


// ======================================================
// STATUS CLASS
// ======================================================

const getStatusClass = (siswa) => {

    return getStatus(siswa) === 'selesai'
        ? 'bg-emerald-100 text-emerald-700'
        : 'bg-rose-100 text-rose-700'

}


// ======================================================
// BOLEH EDIT BERKALA 1
// ======================================================

const berkala1BisaEdit = computed(() => {

    return (
        periodeAktif.value &&
        fasePemeriksaan.value === 1 &&
        aksesBerkala1.value === 'open'
    )

})


// ======================================================
// BOLEH EDIT BERKALA 2
// ======================================================

const berkala2BisaEdit = computed(() => {

    return (
        periodeAktif.value &&
        fasePemeriksaan.value === 2 &&
        aksesBerkala2.value === 'open'
    )

})


// ======================================================
// BISA EDIT JENIS PEMERIKSAAN
// ======================================================

const bisaEditPemeriksaan = (jenis) => {

    if (!periodeAktif.value) {
        return false
    }

    if (jenis === 'berkala_1') {
        return berkala1BisaEdit.value
    }

    if (jenis === 'berkala_2') {
        return berkala2BisaEdit.value
    }

    return false

}


// ======================================================
// OPEN RESULT MODAL
// ======================================================

const openResultModal = (siswa, jenis) => {

    let pemeriksaan = null

    if (jenis === 'berkala_1') {

        pemeriksaan = siswa.berkala_1

    }

    if (jenis === 'berkala_2') {

        pemeriksaan = siswa.berkala_2

    }

    if (!pemeriksaan) {

        console.warn(
            'Data pemeriksaan tidak ditemukan:',
            jenis,
            siswa
        )

        return

    }

    selectedSiswa.value = siswa

    selectedPemeriksaan.value = {

        ...pemeriksaan,

        jenis_pemeriksaan:
            pemeriksaan.jenis_pemeriksaan ?? jenis,

    }

    showResultModal.value = true

    popupBerkala.value = null
    popupClickLocked.value = false

}


// ======================================================
// TUTUP RESULT MODAL
// ======================================================

const closeResultModal = () => {

    showResultModal.value = false

    selectedPemeriksaan.value = null

    selectedSiswa.value = null

}


// ======================================================
// JENIS PEMERIKSAAN TERPILIH
// ======================================================

const selectedJenis = computed(() => {

    return (
        selectedPemeriksaan.value
            ?.jenis_pemeriksaan ?? null
    )

})


// ======================================================
// ROUTE EDIT PEMERIKSAAN
// ======================================================

const editPemeriksaanUrl = computed(() => {

    if (
        !selectedSiswa.value ||
        !selectedJenis.value
    ) {
        return '#'
    }

    const jenis =
        selectedJenis.value === 'berkala_1'
            ? 1
            : 2

    return route(
        'klinik.kesehatan.pemeriksaan.create',
        {
            siswa: selectedSiswa.value.id,
            jenis,
        }
    )

})


// ======================================================
// FORMAT NILAI
// ======================================================

const displayValue = (value) => {

    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return '-'
    }

    return value

}


// ======================================================
// FORMAT TANGGAL
// ======================================================

const formatTanggal = (tanggal) => {

    if (!tanggal) {
        return '-'
    }

    const value = String(tanggal)

    const match =
        value.match(/^(\d{4})-(\d{2})-(\d{2})/)

    if (!match) {
        return tanggal
    }

    const [, tahun, bulan, hari] = match

    const namaBulan = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ]

    return `${hari} ${namaBulan[Number(bulan) - 1]} ${tahun}`

}


// ======================================================
// POPUP BERKALA
// ======================================================

const catatanBerkala1 = computed(() => {

    if (periodeSelesai.value) {

        return 'Periode telah selesai. Hasil Berkala 1 hanya dapat dilihat.'

    }

    if (fasePemeriksaan.value === 1) {

        return 'Berkala 1 sedang aktif. Data pemeriksaan dapat diisi atau diedit.'

    }

    return 'Berkala 1 telah selesai dan hanya dapat dilihat.'

})


const catatanBerkala2 = computed(() => {

    if (periodeSelesai.value) {

        return 'Periode telah selesai. Hasil Berkala 2 hanya dapat dilihat.'

    }

    if (fasePemeriksaan.value === 2) {

        return 'Berkala 2 sedang aktif. Data pemeriksaan dapat diisi atau diedit.'

    }

    return 'Berkala 2 belum memasuki tahap pemeriksaan.'

})


// ======================================================
// TOGGLE POPUP
// ======================================================

const hoverPopupBerkala = (jenis) => {

    if (!popupClickLocked.value) {
        popupBerkala.value = jenis
    }

}


const leavePopupBerkala = () => {

    setTimeout(() => {

        if (!popupClickLocked.value) {
            popupBerkala.value = null
        }

    }, 100)

}


const clickBerkala = (jenis) => {

    if (popupBerkala.value === jenis) {

        popupBerkala.value = null
        popupClickLocked.value = false

        return

    }

    popupBerkala.value = jenis
    popupClickLocked.value = true

}


// ======================================================
// CLICK OUTSIDE
// ======================================================

const handleClickOutside = (event) => {

    if (!popupBerkala.value) {
        return
    }

    const target = event.target

    if (
        !target.closest(
            '[data-berkala-popup]'
        )
    ) {

        popupBerkala.value = null
        popupClickLocked.value = false

    }

}


onMounted(() => {

    document.addEventListener(
        'click',
        handleClickOutside
    )

})


onBeforeUnmount(() => {

    document.removeEventListener(
        'click',
        handleClickOutside
    )

})


// ======================================================
// LABEL MODAL
// ======================================================

const modalJenisLabel = computed(() => {

    const jenis =
        selectedPemeriksaan.value
            ?.jenis_pemeriksaan

    if (jenis === 'berkala_1') {
        return 'Pemeriksaan Berkala 1'
    }

    if (jenis === 'berkala_2') {
        return 'Pemeriksaan Berkala 2'
    }

    return 'Pemeriksaan Berkala'

})


// ======================================================
// FILTER DATA
// ======================================================

const filteredSiswas = computed(() => {

    let data = props.siswas ?? []

    if (search.value.trim()) {

        const keyword =
            search.value
                .toLowerCase()
                .trim()

        data = data.filter((siswa) => {

            return (

                String(siswa.nisn ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(siswa.nama ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.tingkat ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.nama_kelas ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(
                    siswa.kelas?.jurusan
                        ?.nama_jurusan ?? ''
                )
                    .toLowerCase()
                    .includes(keyword)

            )

        })

    }

    if (kelasFilter.value) {

        data = data.filter(
            siswa =>
                String(
                    siswa.kelas?.tingkat ?? ''
                ) ===
                String(kelasFilter.value)
        )

    }

    if (statusFilter.value) {

        data = data.filter(
            siswa =>
                getStatus(siswa) ===
                statusFilter.value
        )

    }

    return data

})


// ======================================================
// ACTIVE FILTER
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        kelasFilter.value ||
        statusFilter.value
    )

})


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = ''
    kelasFilter.value = ''
    statusFilter.value = ''

}


// ======================================================
// JUMLAH SELESAI
// ======================================================

const jumlahSelesai = computed(() => {

    return props.siswas.filter(
        siswa =>
            getStatus(siswa) === 'selesai'
    ).length

})


// ======================================================
// JUMLAH BELUM
// ======================================================

const jumlahBelumSelesai = computed(() => {

    return props.siswas.filter(
        siswa =>
            getStatus(siswa) === 'belum'
    ).length

})


// ======================================================
// CLEAR FLASH
// ======================================================

const clearFlash = () => {

    if (page.props.flash) {

        page.props.flash.success = null
        page.props.flash.error = null

    }

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

                <h1 class="mt-1 text-2xl font-bold text-slate-800">
                    Pemeriksaan Berkala
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Kelola pemeriksaan kesehatan berkala
                    siswa berdasarkan tahap periode.
                </p>

            </div>


            <!-- STATUS PERIODE -->

            <div
                v-if="periode"
                class="rounded-xl border px-4 py-3"
                :class="
                    periodeSelesai
                        ? 'border-slate-200 bg-slate-50'
                        : 'border-blue-100 bg-blue-50'
                "
            >

                <p
                    class="text-[10px] font-bold uppercase tracking-wider"
                    :class="
                        periodeSelesai
                            ? 'text-slate-500'
                            : 'text-blue-500'
                    "
                >
                    {{ statusPeriodeLabel }}
                </p>

                <p
                    class="mt-0.5 text-sm font-bold"
                    :class="
                        periodeSelesai
                            ? 'text-slate-700'
                            : 'text-blue-800'
                    "
                >
                    {{ periode.nama_periode }}
                </p>

            </div>

        </div>


        <!-- ==================================================
             TAHAP
        ================================================== -->

        <div
            v-if="periode"
            class="rounded-2xl border p-5"
            :class="
                periodeSelesai
                    ? 'border-slate-200 bg-slate-50'
                    : fasePemeriksaan === 1
                        ? 'border-blue-200 bg-blue-50'
                        : 'border-emerald-200 bg-emerald-50'
            "
        >

            <div class="flex items-start gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                    :class="
                        periodeSelesai
                            ? 'bg-slate-200'
                            : fasePemeriksaan === 1
                                ? 'bg-blue-100'
                                : 'bg-emerald-100'
                    "
                >

                    <ClipboardDocumentCheckIcon
                        class="h-5 w-5"
                        :class="
                            periodeSelesai
                                ? 'text-slate-500'
                                : fasePemeriksaan === 1
                                    ? 'text-blue-600'
                                    : 'text-emerald-600'
                        "
                    />

                </div>


                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-wide"
                        :class="
                            periodeSelesai
                                ? 'text-slate-500'
                                : fasePemeriksaan === 1
                                    ? 'text-blue-600'
                                    : 'text-emerald-600'
                        "
                    >
                        {{ periodeSelesai
                            ? 'Status Pemeriksaan'
                            : 'Tahap Pemeriksaan Aktif'
                        }}
                    </p>

                    <p
                        class="mt-0.5 text-lg font-bold"
                        :class="
                            periodeSelesai
                                ? 'text-slate-700'
                                : fasePemeriksaan === 1
                                    ? 'text-blue-800'
                                    : 'text-emerald-800'
                        "
                    >
                        {{
                            periodeSelesai
                                ? 'Periode Selesai'
                                : tahapLabel
                        }}
                    </p>

                    <p
                        class="mt-1 text-xs"
                        :class="
                            periodeSelesai
                                ? 'text-slate-500'
                                : fasePemeriksaan === 1
                                    ? 'text-blue-600'
                                    : 'text-emerald-600'
                        "
                    >
                        {{ tahapDescription }}
                    </p>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FLASH SUCCESS
        ================================================== -->

        <div
            v-if="flashSuccess"
            class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
        >

            <div class="flex items-center gap-2">

                <CheckCircleIcon class="h-5 w-5" />

                <span>
                    {{ flashSuccess }}
                </span>

            </div>

            <button
                type="button"
                @click="clearFlash"
                class="rounded-lg p-1 transition hover:bg-emerald-100"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FLASH ERROR
        ================================================== -->

        <div
            v-if="flashError"
            class="flex items-start justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
        >

            <div class="flex items-start gap-3">

                <ExclamationTriangleIcon
                    class="mt-0.5 h-5 w-5 text-rose-600"
                />

                <div>

                    <p class="font-bold text-rose-800">
                        Terjadi kesalahan
                    </p>

                    <p class="mt-0.5 text-xs text-rose-600">
                        {{ flashError }}
                    </p>

                </div>

            </div>

            <button
                type="button"
                @click="clearFlash"
                class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-100"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             NO PERIOD
        ================================================== -->

        <div
            v-if="!periode"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-6"
        >

            <div class="flex items-start gap-3">

                <ExclamationTriangleIcon
                    class="h-5 w-5 shrink-0 text-amber-600"
                />

                <div>

                    <p class="text-sm font-bold text-amber-800">
                        Belum ada periode
                    </p>

                    <p class="mt-1 text-xs text-amber-600">
                        Pemeriksaan berkala belum dapat dilakukan
                        karena belum ada periode yang tersedia.
                    </p>

                </div>

            </div>

        </div>


        <!-- ==================================================
             CONTENT
        ================================================== -->

        <template v-else>


            <!-- ==================================================
                 SUMMARY
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-3"
            >

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p class="text-xs font-semibold text-slate-400">
                        Total Siswa
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        {{ siswas.length }}
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p class="text-xs font-semibold text-slate-400">
                        {{ periodeSelesai ? 'Pemeriksaan Selesai' : tahapLabel + ' Selesai' }}
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-emerald-600"
                    >
                        {{ jumlahSelesai }}
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p class="text-xs font-semibold text-slate-400">
                        {{ periodeSelesai ? 'Pemeriksaan Belum Selesai' : tahapLabel + ' Belum Selesai' }}
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-rose-600"
                    >
                        {{ jumlahBelumSelesai }}
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 FILTER
            ================================================== -->

            <div
                class="rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[3fr_1fr_1fr]"
                >

                    <div class="relative">

                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                        />

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari NISN, nama, kelas, atau jurusan..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                        />

                    </div>


                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="selesai">
                            Selesai
                        </option>

                        <option value="belum">
                            Belum Selesai
                        </option>

                    </select>


                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >

                        <FunnelIcon class="h-4 w-4" />

                        Filter Kelas

                    </button>

                </div>


                <div
                    v-if="showFilter"
                    class="border-t border-slate-100 bg-slate-50/70 px-4 py-4"
                >

                    <label
                        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                    >
                        Tingkat Kelas
                    </label>

                    <select
                        v-model="kelasFilter"
                        class="w-full max-w-xs rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700"
                    >

                        <option value="">
                            Semua Kelas
                        </option>

                        <option
                            v-for="kelas in kelasOptions"
                            :key="kelas"
                            :value="kelas"
                        >
                            Kelas {{ kelas }}
                        </option>

                    </select>


                    <button
                        v-if="hasActiveFilter"
                        type="button"
                        @click="resetFilter"
                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-xs font-semibold text-slate-500 hover:bg-slate-100"
                    >

                        <XMarkIcon class="h-4 w-4" />

                        Reset Filter

                    </button>

                </div>

            </div>


            <!-- ==================================================
                 TABLE
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <h2 class="text-sm font-bold text-slate-800">
                        Daftar Pemeriksaan Siswa
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-400">
                        Menampilkan {{ filteredSiswas.length }}
                        siswa pada periode
                        {{ periode.nama_periode }}.
                    </p>

                </div>


                <!-- ==================================================
                     DESKTOP
                ================================================== -->

                <div class="hidden overflow-x-auto lg:block">

                    <table class="min-w-full">

                        <thead>

                            <tr class="border-b border-slate-200 bg-slate-50">

                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                    No
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                    NISN
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                    Nama Siswa
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                    Kelas
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-bold uppercase text-slate-500">
                                    Jurusan
                                </th>


                                <!-- B1 -->

                                <th
                                    class="relative px-5 py-3 text-center text-xs font-bold uppercase text-slate-500"
                                >

                                    <div
                                        data-berkala-popup
                                        class="relative inline-flex items-center justify-center"
                                        @mouseenter="hoverPopupBerkala('berkala_1')"
                                        @mouseleave="leavePopupBerkala"
                                    >

                                        <button
                                            type="button"
                                            @click.stop="clickBerkala('berkala_1')"
                                            class="inline-flex items-center gap-1 rounded-lg px-2 py-1 transition hover:bg-blue-50 hover:text-blue-600"
                                        >

                                            <span>
                                                Berkala 1
                                            </span>

                                            <InformationCircleIcon
                                                class="h-3.5 w-3.5 text-slate-400"
                                            />

                                        </button>


                                        <div
                                            v-if="popupBerkala === 'berkala_1'"
                                            class="absolute left-1/2 top-full z-[100] mt-2 w-72 -translate-x-1/2"
                                            @mouseenter="hoverPopupBerkala('berkala_1')"
                                            @mouseleave="leavePopupBerkala"
                                        >

                                            <div
                                                class="rounded-xl border border-blue-100 bg-white p-4 text-left shadow-2xl ring-1 ring-black/5"
                                            >

                                                <div class="flex items-start gap-2">

                                                    <div
                                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-blue-50"
                                                    >

                                                        <CalendarDaysIcon
                                                            class="h-4 w-4 text-blue-600"
                                                        />

                                                    </div>

                                                    <div>

                                                        <p class="text-xs font-bold text-slate-800">
                                                            Periode Berkala 1
                                                        </p>

                                                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500 normal-case">
                                                            {{ catatanBerkala1 }}
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </th>


                                <!-- B2 -->

                                <th
                                    class="relative px-5 py-3 text-center text-xs font-bold uppercase text-slate-500"
                                >

                                    <div
                                        data-berkala-popup
                                        class="relative inline-flex items-center justify-center"
                                        @mouseenter="hoverPopupBerkala('berkala_2')"
                                        @mouseleave="leavePopupBerkala"
                                    >

                                        <button
                                            type="button"
                                            @click.stop="clickBerkala('berkala_2')"
                                            class="inline-flex items-center gap-1 rounded-lg px-2 py-1 transition hover:bg-emerald-50 hover:text-emerald-600"
                                        >

                                            <span>
                                                Berkala 2
                                            </span>

                                            <InformationCircleIcon
                                                class="h-3.5 w-3.5 text-slate-400"
                                            />

                                        </button>


                                        <div
                                            v-if="popupBerkala === 'berkala_2'"
                                            class="absolute left-1/2 top-full z-[100] mt-2 w-72 -translate-x-1/2"
                                            @mouseenter="hoverPopupBerkala('berkala_2')"
                                            @mouseleave="leavePopupBerkala"
                                        >

                                            <div
                                                class="rounded-xl border border-emerald-100 bg-white p-4 text-left shadow-2xl ring-1 ring-black/5"
                                            >

                                                <div class="flex items-start gap-2">

                                                    <div
                                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50"
                                                    >

                                                        <CalendarDaysIcon
                                                            class="h-4 w-4 text-emerald-600"
                                                        />

                                                    </div>

                                                    <div>

                                                        <p class="text-xs font-bold text-slate-800">
                                                            Periode Berkala 2
                                                        </p>

                                                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500 normal-case">
                                                            {{ catatanBerkala2 }}
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </th>


                                <th class="px-5 py-3 text-center text-xs font-bold uppercase text-slate-500">
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            <tr
                                v-for="(siswa, index) in filteredSiswas"
                                :key="siswa.id"
                                class="transition hover:bg-slate-50"
                            >

                                <td class="px-5 py-4 text-sm text-slate-500">
                                    {{ index + 1 }}
                                </td>


                                <td class="px-5 py-4 text-sm font-semibold text-slate-700">
                                    {{ siswa.nisn }}
                                </td>


                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                        >
                                            {{ siswa.nama?.charAt(0)?.toUpperCase() }}
                                        </div>

                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ siswa.nama }}
                                        </p>

                                    </div>

                                </td>


                                <td class="px-5 py-4 text-sm font-medium text-slate-700">
                                    {{
                                        siswa.kelas?.nama_kelas ||
                                        siswa.kelas?.tingkat ||
                                        '-'
                                    }}
                                </td>


                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{
                                        siswa.kelas?.jurusan?.nama_jurusan ||
                                        '-'
                                    }}
                                </td>


                                <!-- ==================================================
                                     B1
                                ================================================== -->

                                <td class="px-5 py-4 text-center">

                                    <button
                                        v-if="siswa.berkala_1?.status === 'selesai'"
                                        type="button"
                                        @click="openResultModal(siswa, 'berkala_1')"
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 hover:bg-emerald-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700">

                                            <CheckCircleIcon class="h-4 w-4" />

                                            Selesai

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-emerald-600">
                                            Klik untuk melihat hasil
                                        </span>

                                    </button>


                                    <Link
                                        v-else-if="berkala1BisaEdit"
                                        :href="
                                            route(
                                                'klinik.kesehatan.pemeriksaan.create',
                                                {
                                                    siswa: siswa.id,
                                                    jenis: 1
                                                }
                                            )
                                        "
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 hover:bg-blue-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-blue-700">

                                            <ClipboardDocumentCheckIcon class="h-4 w-4" />

                                            Lengkapi

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-blue-600">
                                            Bisa diisi
                                        </span>

                                    </Link>


                                    <button
                                        v-else-if="siswa.berkala_1 && berkala1View"
                                        type="button"
                                        @click="openResultModal(siswa, 'berkala_1')"
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 hover:bg-slate-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-600">

                                            <EyeIcon class="h-4 w-4" />

                                            Lihat

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-slate-400">
                                            Hasil Berkala 1
                                        </span>

                                    </button>


                                    <div
                                        v-else
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500">

                                            <LockClosedIcon class="h-4 w-4" />

                                            Ditutup

                                        </span>

                                    </div>

                                </td>


                                <!-- ==================================================
                                     B2
                                ================================================== -->

                                <td class="px-5 py-4 text-center">

                                    <button
                                        v-if="siswa.berkala_2?.status === 'selesai'"
                                        type="button"
                                        @click="openResultModal(siswa, 'berkala_2')"
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 hover:bg-emerald-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700">

                                            <CheckCircleIcon class="h-4 w-4" />

                                            Selesai

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-emerald-600">
                                            Klik untuk melihat hasil
                                        </span>

                                    </button>


                                    <Link
                                        v-else-if="berkala2BisaEdit"
                                        :href="
                                            route(
                                                'klinik.kesehatan.pemeriksaan.create',
                                                {
                                                    siswa: siswa.id,
                                                    jenis: 2
                                                }
                                            )
                                        "
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 hover:bg-emerald-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700">

                                            <ClipboardDocumentCheckIcon class="h-4 w-4" />

                                            Lengkapi

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-emerald-600">
                                            Bisa diisi
                                        </span>

                                    </Link>


                                    <button
                                        v-else-if="siswa.berkala_2 && berkala2View"
                                        type="button"
                                        @click="openResultModal(siswa, 'berkala_2')"
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 hover:bg-slate-100"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-600">

                                            <EyeIcon class="h-4 w-4" />

                                            Lihat

                                        </span>

                                        <span class="mt-0.5 text-[10px] text-slate-400">
                                            Hasil Berkala 2
                                        </span>

                                    </button>


                                    <div
                                        v-else
                                        class="inline-flex min-w-[115px] flex-col items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2"
                                    >

                                        <span class="flex items-center gap-1.5 text-xs font-bold text-slate-500">

                                            <LockClosedIcon class="h-4 w-4" />

                                            Ditutup

                                        </span>

                                    </div>

                                </td>


                                <!-- STATUS -->

                                <td class="px-5 py-4 text-center">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                            getStatusClass(siswa)
                                        ]"
                                    >

                                        {{ getStatusLabel(siswa) }}

                                    </span>

                                </td>

                            </tr>


                            <tr v-if="filteredSiswas.length === 0">

                                <td
                                    colspan="8"
                                    class="px-5 py-16 text-center"
                                >

                                    <MagnifyingGlassIcon
                                        class="mx-auto h-6 w-6 text-slate-400"
                                    />

                                    <p class="mt-3 text-sm font-bold text-slate-700">
                                        Data siswa tidak ditemukan
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Coba ubah kata pencarian atau filter.
                                    </p>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- ==================================================
                     MOBILE
                ================================================== -->

                <div class="divide-y divide-slate-100 lg:hidden">

                    <div
                        v-for="siswa in filteredSiswas"
                        :key="siswa.id"
                        class="p-4"
                    >

                        <div class="flex items-start justify-between gap-3">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                                >
                                    {{ siswa.nama?.charAt(0)?.toUpperCase() }}
                                </div>

                                <div>

                                    <p class="text-sm font-bold text-slate-800">
                                        {{ siswa.nama }}
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        NISN: {{ siswa.nisn }}
                                    </p>

                                </div>

                            </div>


                            <span
                                :class="[
                                    'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                    getStatusClass(siswa)
                                ]"
                            >
                                {{ getStatusLabel(siswa) }}
                            </span>

                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-3 text-xs">

                            <div>

                                <p class="text-slate-400">
                                    Kelas
                                </p>

                                <p class="mt-0.5 font-semibold text-slate-700">
                                    {{
                                        siswa.kelas?.nama_kelas ||
                                        siswa.kelas?.tingkat ||
                                        '-'
                                    }}
                                </p>

                            </div>


                            <div>

                                <p class="text-slate-400">
                                    Jurusan
                                </p>

                                <p class="mt-0.5 font-semibold text-slate-700">
                                    {{
                                        siswa.kelas?.jurusan?.nama_jurusan ||
                                        '-'
                                    }}
                                </p>

                            </div>

                        </div>


                        <div class="mt-4 grid grid-cols-2 gap-3">


                            <!-- ==================================================
                                 MOBILE B1
                            ================================================== -->

                            <button
                                v-if="siswa.berkala_1?.status === 'selesai'"
                                type="button"
                                @click="openResultModal(siswa, 'berkala_1')"
                                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-left hover:bg-emerald-100"
                            >

                                <p class="text-[10px] font-bold uppercase text-emerald-600">
                                    Berkala 1
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <CheckCircleIcon class="h-4 w-4 text-emerald-600" />

                                    <span class="text-xs font-bold text-emerald-700">
                                        Selesai
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-emerald-600">
                                    Klik untuk melihat
                                </p>

                            </button>


                            <Link
                                v-else-if="berkala1BisaEdit"
                                :href="
                                    route(
                                        'klinik.kesehatan.pemeriksaan.create',
                                        {
                                            siswa: siswa.id,
                                            jenis: 1
                                        }
                                    )
                                "
                                class="rounded-xl border border-blue-200 bg-blue-50 p-3"
                            >

                                <p class="text-[10px] font-bold uppercase text-blue-600">
                                    Berkala 1
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <ClipboardDocumentCheckIcon class="h-4 w-4 text-blue-600" />

                                    <span class="text-xs font-bold text-blue-700">
                                        {{
                                            siswa.berkala_1
                                                ? 'Edit Data'
                                                : 'Lengkapi'
                                        }}
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-blue-500">
                                    Bisa diisi
                                </p>

                            </Link>


                            <button
                                v-else-if="siswa.berkala_1 && berkala1View"
                                type="button"
                                @click="openResultModal(siswa, 'berkala_1')"
                                class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-left hover:bg-slate-100"
                            >

                                <p class="text-[10px] font-bold uppercase text-slate-500">
                                    Berkala 1
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <EyeIcon class="h-4 w-4 text-slate-500" />

                                    <span class="text-xs font-bold text-slate-600">
                                        Lihat
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-slate-400">
                                    Hasil Berkala 1
                                </p>

                            </button>


                            <div
                                v-else
                                class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                            >

                                <p class="text-[10px] font-bold uppercase text-slate-400">
                                    Berkala 1
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <LockClosedIcon class="h-4 w-4 text-slate-400" />

                                    <span class="text-xs font-bold text-slate-500">
                                        Ditutup
                                    </span>

                                </div>

                            </div>


                            <!-- ==================================================
                                 MOBILE B2
                            ================================================== -->

                            <button
                                v-if="siswa.berkala_2?.status === 'selesai'"
                                type="button"
                                @click="openResultModal(siswa, 'berkala_2')"
                                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-left hover:bg-emerald-100"
                            >

                                <p class="text-[10px] font-bold uppercase text-emerald-600">
                                    Berkala 2
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <CheckCircleIcon class="h-4 w-4 text-emerald-600" />

                                    <span class="text-xs font-bold text-emerald-700">
                                        Selesai
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-emerald-600">
                                    Klik untuk melihat
                                </p>

                            </button>


                            <Link
                                v-else-if="berkala2BisaEdit"
                                :href="
                                    route(
                                        'klinik.kesehatan.pemeriksaan.create',
                                        {
                                            siswa: siswa.id,
                                            jenis: 2
                                        }
                                    )
                                "
                                class="rounded-xl border border-emerald-200 bg-emerald-50 p-3"
                            >

                                <p class="text-[10px] font-bold uppercase text-emerald-600">
                                    Berkala 2
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <ClipboardDocumentCheckIcon class="h-4 w-4 text-emerald-600" />

                                    <span class="text-xs font-bold text-emerald-700">
                                        {{
                                            siswa.berkala_2
                                                ? 'Edit Data'
                                                : 'Lengkapi'
                                        }}
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-emerald-600">
                                    Bisa diisi
                                </p>

                            </Link>


                            <button
                                v-else-if="siswa.berkala_2 && berkala2View"
                                type="button"
                                @click="openResultModal(siswa, 'berkala_2')"
                                class="rounded-xl border border-slate-200 bg-slate-50 p-3 text-left hover:bg-slate-100"
                            >

                                <p class="text-[10px] font-bold uppercase text-slate-500">
                                    Berkala 2
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <EyeIcon class="h-4 w-4 text-slate-500" />

                                    <span class="text-xs font-bold text-slate-600">
                                        Lihat
                                    </span>

                                </div>

                                <p class="mt-1 text-[10px] text-slate-400">
                                    Hasil Berkala 2
                                </p>

                            </button>


                            <div
                                v-else
                                class="rounded-xl border border-slate-200 bg-slate-50 p-3"
                            >

                                <p class="text-[10px] font-bold uppercase text-slate-400">
                                    Berkala 2
                                </p>

                                <div class="mt-1 flex items-center gap-1.5">

                                    <LockClosedIcon class="h-4 w-4 text-slate-400" />

                                    <span class="text-xs font-bold text-slate-500">
                                        Ditutup
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div
                        v-if="filteredSiswas.length === 0"
                        class="px-5 py-16 text-center"
                    >

                        <MagnifyingGlassIcon
                            class="mx-auto h-6 w-6 text-slate-400"
                        />

                        <p class="mt-3 text-sm font-bold text-slate-700">
                            Data siswa tidak ditemukan
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            Coba ubah kata pencarian atau filter.
                        </p>

                    </div>

                </div>

            </div>

        </template>

    </div>


    <!-- ==========================================================
         POPUP DETAIL PEMERIKSAAN
    =========================================================== -->

    <Teleport to="body">

        <div
            v-if="showResultModal"
            class="fixed inset-0 z-[999] flex items-center justify-center p-4"
        >

            <div
                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                @click="closeResultModal"
            ></div>


            <div
                class="relative flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
            >


                <!-- HEADER -->

                <div
                    class="flex items-start justify-between border-b border-slate-100 px-6 py-5"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <CheckCircleIcon
                                class="h-5 w-5 text-emerald-600"
                            />

                        </div>

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-emerald-600"
                            >
                                Pemeriksaan Selesai
                            </p>

                            <h2
                                class="mt-0.5 text-lg font-bold text-slate-800"
                            >
                                {{ modalJenisLabel }}
                            </h2>

                        </div>

                    </div>


                    <button
                        type="button"
                        @click="closeResultModal"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-100"
                    >

                        <XMarkIcon class="h-6 w-6" />

                    </button>

                </div>


                <!-- CONTENT -->

                <div
                    v-if="selectedPemeriksaan && selectedSiswa"
                    class="overflow-y-auto px-6 py-6"
                >


                    <!-- IDENTITAS -->

                    <div
                        class="rounded-2xl border border-blue-100 bg-blue-50 p-5"
                    >

                        <div class="flex items-start gap-4">

                            <div
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-lg font-bold text-blue-700"
                            >
                                {{
                                    selectedSiswa.nama
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                }}
                            </div>


                            <div class="min-w-0">

                                <h3
                                    class="text-base font-bold text-slate-800"
                                >
                                    {{ selectedSiswa.nama }}
                                </h3>


                                <div
                                    class="mt-2 grid grid-cols-1 gap-x-6 gap-y-2 text-xs text-slate-600 sm:grid-cols-2"
                                >

                                    <p>
                                        <b>NISN:</b>
                                        {{ displayValue(selectedSiswa.nisn) }}
                                    </p>


                                    <p>
                                        <b>Kelas:</b>
                                        {{
                                            selectedSiswa.kelas?.nama_kelas ||
                                            selectedSiswa.kelas?.tingkat ||
                                            '-'
                                        }}
                                    </p>


                                    <p>
                                        <b>Jurusan:</b>
                                        {{
                                            selectedSiswa.kelas?.jurusan?.nama_jurusan ||
                                            '-'
                                        }}
                                    </p>


                                    <p>
                                        <b>Jenis Kelamin:</b>
                                        {{
                                            displayValue(
                                                selectedSiswa.jenis_kelamin
                                            )
                                        }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- TANGGAL & STATUS -->

                    <div
                        class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2"
                    >

                        <div
                            class="rounded-xl border border-slate-200 bg-white p-4"
                        >

                            <div class="flex items-center gap-3">

                                <CalendarDaysIcon
                                    class="h-5 w-5 text-blue-500"
                                />

                                <div>

                                    <p
                                        class="text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                    >
                                        Tanggal Pemeriksaan
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-bold text-slate-700"
                                    >
                                        {{
                                            formatTanggal(
                                                selectedPemeriksaan.tanggal_pemeriksaan
                                            )
                                        }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div
                            class="rounded-xl border border-emerald-200 bg-emerald-50 p-4"
                        >

                            <div class="flex items-center gap-3">

                                <CheckCircleIcon
                                    class="h-5 w-5 text-emerald-600"
                                />

                                <div>

                                    <p
                                        class="text-[10px] font-bold uppercase tracking-wide text-emerald-500"
                                    >
                                        Status
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-bold text-emerald-700"
                                    >
                                        {{
                                            selectedPemeriksaan.status === 'selesai'
                                                ? 'Selesai'
                                                : 'Belum Selesai'
                                        }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- ANTROPOMETRI -->

                    <div class="mt-6">

                        <div class="mb-3 flex items-center gap-2">

                            <ScaleIcon
                                class="h-5 w-5 text-blue-600"
                            />

                            <h3 class="text-sm font-bold text-slate-800">
                                Antropometri
                            </h3>

                        </div>


                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-3"
                        >

                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    Berat Badan
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >

                                    {{ displayValue(selectedPemeriksaan.berat_badan) }}

                                    <span
                                        v-if="
                                            selectedPemeriksaan.berat_badan !== null &&
                                            selectedPemeriksaan.berat_badan !== ''
                                        "
                                        class="text-xs font-normal text-slate-400"
                                    >
                                        kg
                                    </span>

                                </p>

                            </div>


                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    Tinggi Badan
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >

                                    {{ displayValue(selectedPemeriksaan.tinggi_badan) }}

                                    <span
                                        v-if="
                                            selectedPemeriksaan.tinggi_badan !== null &&
                                            selectedPemeriksaan.tinggi_badan !== ''
                                        "
                                        class="text-xs font-normal text-slate-400"
                                    >
                                        cm
                                    </span>

                                </p>

                            </div>


                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    IMT
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >
                                    {{ displayValue(selectedPemeriksaan.imt) }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- TANDA VITAL -->

                    <div class="mt-6">

                        <div class="mb-3 flex items-center gap-2">

                            <HeartIcon
                                class="h-5 w-5 text-rose-500"
                            />

                            <h3 class="text-sm font-bold text-slate-800">
                                Tanda Vital
                            </h3>

                        </div>


                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-3"
                        >

                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    Tekanan Darah
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >
                                    {{ displayValue(selectedPemeriksaan.tekanan_darah) }}
                                </p>

                            </div>


                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    Denyut Nadi
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >

                                    {{ displayValue(selectedPemeriksaan.denyut_nadi) }}

                                    <span
                                        v-if="
                                            selectedPemeriksaan.denyut_nadi !== null &&
                                            selectedPemeriksaan.denyut_nadi !== ''
                                        "
                                        class="text-xs font-normal text-slate-400"
                                    >
                                        bpm
                                    </span>

                                </p>

                            </div>


                            <div
                                class="rounded-xl border border-slate-200 p-4"
                            >

                                <p class="text-xs text-slate-400">
                                    Suhu Tubuh
                                </p>

                                <p
                                    class="mt-1 text-base font-bold text-slate-700"
                                >

                                    {{ displayValue(selectedPemeriksaan.suhu_tubuh) }}

                                    <span
                                        v-if="
                                            selectedPemeriksaan.suhu_tubuh !== null &&
                                            selectedPemeriksaan.suhu_tubuh !== ''
                                        "
                                        class="text-xs font-normal text-slate-400"
                                    >
                                        °C
                                    </span>

                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- PEMERIKSAAN FISIK -->

                    <div class="mt-6">

                        <div class="mb-3 flex items-center gap-2">

                            <InformationCircleIcon
                                class="h-5 w-5 text-purple-600"
                            />

                            <h3 class="text-sm font-bold text-slate-800">
                                Pemeriksaan Fisik
                            </h3>

                        </div>


                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                        >

                            <div class="rounded-xl border border-slate-200 p-4">

                                <p class="text-xs text-slate-400">
                                    Mata
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ displayValue(selectedPemeriksaan.mata) }}
                                </p>

                            </div>


                            <div class="rounded-xl border border-slate-200 p-4">

                                <p class="text-xs text-slate-400">
                                    Telinga
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ displayValue(selectedPemeriksaan.telinga) }}
                                </p>

                            </div>


                            <div class="rounded-xl border border-slate-200 p-4">

                                <p class="text-xs text-slate-400">
                                    Gigi & Mulut
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ displayValue(selectedPemeriksaan.gigi_mulut) }}
                                </p>

                            </div>


                            <div class="rounded-xl border border-slate-200 p-4">

                                <p class="text-xs text-slate-400">
                                    Kondisi Umum
                                </p>

                                <p class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ displayValue(selectedPemeriksaan.kondisi_umum) }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- KELUHAN -->

                    <div class="mt-6">

                        <h3 class="mb-3 text-sm font-bold text-slate-800">
                            Keluhan
                        </h3>

                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{ displayValue(selectedPemeriksaan.keluhan) }}
                            </p>

                        </div>

                    </div>


                    <!-- HASIL -->

                    <div class="mt-6">

                        <h3 class="mb-3 text-sm font-bold text-slate-800">
                            Hasil Pemeriksaan
                        </h3>

                        <div
                            class="rounded-xl border border-slate-200 bg-slate-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-slate-700"
                            >
                                {{ displayValue(selectedPemeriksaan.hasil_pemeriksaan) }}
                            </p>

                        </div>

                    </div>


                    <!-- REKOMENDASI -->

                    <div class="mt-6">

                        <h3 class="mb-3 text-sm font-bold text-slate-800">
                            Rekomendasi
                        </h3>

                        <div
                            class="rounded-xl border border-blue-100 bg-blue-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-blue-800"
                            >
                                {{ displayValue(selectedPemeriksaan.rekomendasi) }}
                            </p>

                        </div>

                    </div>


                    <!-- CATATAN -->

                    <div class="mt-6">

                        <h3 class="mb-3 text-sm font-bold text-slate-800">
                            Catatan
                        </h3>

                        <div
                            class="rounded-xl border border-amber-100 bg-amber-50 p-4"
                        >

                            <p
                                class="whitespace-pre-line text-sm leading-6 text-amber-800"
                            >
                                {{ displayValue(selectedPemeriksaan.catatan) }}
                            </p>

                        </div>

                    </div>


                    <!-- PEMERIKSA -->

                    <div
                        class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-200"
                            >

                                <UserIcon
                                    class="h-5 w-5 text-slate-500"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Diperiksa Oleh
                                </p>

                                <p
                                    class="mt-0.5 text-sm font-bold text-slate-700"
                                >
                                    {{
                                        selectedPemeriksaan.pemeriksa?.name ||
                                        selectedPemeriksaan.pemeriksa?.nama ||
                                        'Tidak diketahui'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     FOOTER
                ================================================== -->

                <div
                    class="flex items-center justify-between gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4"
                >

                    <!-- EDIT HANYA JIKA PERIODE AKTIF
                         DAN JENIS PEMERIKSAAN SEDANG AKTIF -->

                    <Link
                        v-if="
                            selectedSiswa &&
                            selectedPemeriksaan &&
                            bisaEditPemeriksaan(
                                selectedPemeriksaan.jenis_pemeriksaan
                            )
                        "
                        :href="editPemeriksaanUrl"
                        class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-5 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-100"
                    >

                        <PencilSquareIcon class="h-4 w-4" />

                        Edit Data

                    </Link>


                    <div v-else></div>


                    <button
                        type="button"
                        @click="closeResultModal"
                        class="rounded-xl bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700"
                    >
                        Tutup
                    </button>

                </div>

            </div>

        </div>

    </Teleport>

</KlinikLayout>

</template>