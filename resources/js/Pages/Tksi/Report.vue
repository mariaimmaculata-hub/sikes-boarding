<script setup>

import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import TksiLayout from '@/Layouts/TksiLayout.vue'

import {
    ClipboardDocumentCheckIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ClockIcon,
    ArrowDownTrayIcon,
    EyeIcon,
    XMarkIcon,
    ChartBarIcon,
    FunnelIcon,
    ChevronDownIcon,
    MagnifyingGlassIcon,
    ArrowPathIcon,
} from '@heroicons/vue/24/outline'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({

    data: {
        type: Array,
        default: () => [],
    },

    periodes: {
        type: Array,
        default: () => [],
    },

    komponenOptions: {
        type: Array,
        default: () => [],
    },

    tingkatOptions: {
        type: Array,
        default: () => [],
    },

    jurusanOptions: {
        type: Array,
        default: () => [],
    },

    statistik: {
        type: Object,
        default: () => ({
            total_siswa: 0,
            total_hasil: 0,
            jumlah_komponen: 0,
            siswa_lengkap: 0,
        }),
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            periode_id: '',
            tingkat: '',
            jurusan_id: '',
            komponen: [],
        }),
    },

    periode: {
        type: Object,
        default: null,
    },

})


/*
|--------------------------------------------------------------------------
| STATE FILTER
|--------------------------------------------------------------------------
*/

const search = ref(
    props.filters?.search ?? ''
)

const periodeId = ref(
    props.filters?.periode_id ?? ''
)

const tingkat = ref(
    props.filters?.tingkat ?? ''
)

const jurusanId = ref(
    props.filters?.jurusan_id ?? ''
)

const selectedKomponen = ref(
    Array.isArray(props.filters?.komponen)
        ? props.filters.komponen.map(item => String(item))
        : props.filters?.komponen
            ? [String(props.filters.komponen)]
            : []
)

const selectedData = ref(null)

const openDropdown = ref(null)


/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

const totalSiswa = computed(() => {
    return props.statistik?.total_siswa ?? 0
})

const totalHasil = computed(() => {
    return props.statistik?.total_hasil ?? 0
})

const jumlahKomponen = computed(() => {
    return props.statistik?.jumlah_komponen
        ?? props.komponenOptions?.length
        ?? 0
})

const siswaLengkap = computed(() => {
    return props.statistik?.siswa_lengkap ?? 0
})


/*
|--------------------------------------------------------------------------
| PERSENTASE SISWA LENGKAP
|--------------------------------------------------------------------------
*/

const progressLengkap = computed(() => {

    if (!totalSiswa.value) {
        return 0
    }

    return Math.round(
        (
            siswaLengkap.value /
            totalSiswa.value
        ) * 100
    )
})


/*
|--------------------------------------------------------------------------
| KOMPONEN YANG DITAMPILKAN
|--------------------------------------------------------------------------
*/

const displayedKomponen = computed(() => {

    if (!selectedKomponen.value.length) {
        return props.komponenOptions
    }

    return props.komponenOptions.filter(
        komponen =>
            selectedKomponen.value.includes(
                String(komponen)
            )
    )
})


/*
|--------------------------------------------------------------------------
| LEBAR TABLE
|--------------------------------------------------------------------------
*/

const tableMinWidth = computed(() => {

    const fixedColumns =
        60 +
        240 +
        150 +
        140 +
        130 +
        110

    const komponenWidth =
        displayedKomponen.value.length * 170

    return fixedColumns + komponenWidth
})


/*
|--------------------------------------------------------------------------
| NAMA PERIODE
|--------------------------------------------------------------------------
*/

const selectedPeriodeName = computed(() => {

    if (!periodeId.value) {
        return 'Semua Periode'
    }

    return (
        props.periodes.find(
            periode =>
                String(periode.id) ===
                String(periodeId.value)
        )?.nama_periode
        || 'Periode Dipilih'
    )
})


/*
|--------------------------------------------------------------------------
| NAMA JURUSAN
|--------------------------------------------------------------------------
*/

const selectedJurusanName = computed(() => {

    if (!jurusanId.value) {
        return 'Semua Jurusan'
    }

    return (
        props.jurusanOptions.find(
            jurusan =>
                String(jurusan.id) ===
                String(jurusanId.value)
        )?.nama_jurusan
        || 'Jurusan Dipilih'
    )
})


/*
|--------------------------------------------------------------------------
| LABEL KOMPONEN
|--------------------------------------------------------------------------
*/

const selectedKomponenLabel = computed(() => {

    if (!selectedKomponen.value.length) {
        return 'Semua Komponen'
    }

    if (
        selectedKomponen.value.length ===
        props.komponenOptions.length
    ) {
        return 'Semua Komponen'
    }

    if (selectedKomponen.value.length === 1) {
        return selectedKomponen.value[0]
    }

    return `${selectedKomponen.value.length} Komponen`
})


/*
|--------------------------------------------------------------------------
| FILTER AKTIF
|--------------------------------------------------------------------------
*/

const activeFilterCount = computed(() => {

    let count = 0

    if (search.value.trim()) {
        count++
    }

    if (periodeId.value) {
        count++
    }

    if (tingkat.value) {
        count++
    }

    if (jurusanId.value) {
        count++
    }

    if (selectedKomponen.value.length) {
        count++
    }

    return count
})


/*
|--------------------------------------------------------------------------
| DROPDOWN
|--------------------------------------------------------------------------
*/

function toggleDropdown(name) {

    if (openDropdown.value === name) {
        openDropdown.value = null
    } else {
        openDropdown.value = name
    }
}


function closeDropdown() {
    openDropdown.value = null
}


/*
|--------------------------------------------------------------------------
| KOMPONEN
|--------------------------------------------------------------------------
*/

function isKomponenSelected(komponen) {

    return selectedKomponen.value.includes(
        String(komponen)
    )
}


function toggleKomponen(komponen) {

    const value = String(komponen)

    if (
        selectedKomponen.value.includes(value)
    ) {

        selectedKomponen.value =
            selectedKomponen.value.filter(
                item => item !== value
            )

    } else {

        selectedKomponen.value = [
            ...selectedKomponen.value,
            value
        ]
    }
}


function selectAllKomponen() {

    selectedKomponen.value =
        props.komponenOptions.map(
            komponen => String(komponen)
        )
}


function clearKomponen() {

    selectedKomponen.value = []
}


/*
|--------------------------------------------------------------------------
| APPLY FILTER
|--------------------------------------------------------------------------
*/

function applyFilter() {

    router.get(
        '/tksi/report',
        {
            search:
                search.value.trim() || null,

            periode_id:
                periodeId.value || null,

            tingkat:
                tingkat.value || null,

            jurusan_id:
                jurusanId.value || null,

            komponen:
                selectedKomponen.value.length
                    ? selectedKomponen.value
                    : null,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )

    closeDropdown()
}


/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

function resetFilter() {

    search.value = ''
    periodeId.value = ''
    tingkat.value = ''
    jurusanId.value = ''
    selectedKomponen.value = []

    router.get(
        '/tksi/report',
        {},
        {
            preserveState: false,
            preserveScroll: false,
            replace: true,
        }
    )

    closeDropdown()
}


/*
|--------------------------------------------------------------------------
| DETAIL
|--------------------------------------------------------------------------
*/

function openDetail(item) {

    selectedData.value = item
}


function closeDetail() {

    selectedData.value = null
}


/*
|--------------------------------------------------------------------------
| FORMAT NILAI
|--------------------------------------------------------------------------
*/

function formatNilai(nilai) {

    if (
        nilai === null ||
        nilai === undefined ||
        nilai === ''
    ) {
        return '-'
    }

    return nilai
}


/*
|--------------------------------------------------------------------------
| FORMAT RATA-RATA
|--------------------------------------------------------------------------
*/

function formatRataRata(nilai) {

    if (
        nilai === null ||
        nilai === undefined ||
        nilai === ''
    ) {
        return '-'
    }

    const number = Number(nilai)

    if (Number.isNaN(number)) {
        return '-'
    }

    return number.toFixed(2)
}


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function formatTanggal(tanggal) {

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
            month: 'short',
            year: 'numeric',
        }
    )
}


/*
|--------------------------------------------------------------------------
| FORMAT WAKTU
|--------------------------------------------------------------------------
*/
function formatWaktu(tanggal) {

    if (!tanggal) {
        return ''
    }

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) {
        return ''
    }

    return date.toLocaleTimeString('id-ID', {
        timeZone: 'Asia/Jakarta',
        hour: '2-digit',
        minute: '2-digit',
    })
}
/*
|--------------------------------------------------------------------------
| CEK NILAI KOMPONEN
|--------------------------------------------------------------------------
*/

function isBeepTest(namaKomponen) {
    return String(namaKomponen ?? '').toLowerCase().replace(/[^a-z0-9]/g, '') === 'beeptest'
}

/*
|--------------------------------------------------------------------------
| JENIS KELAMIN SISWA
|--------------------------------------------------------------------------
*/

function isPuteraReport(item) {
    const gender = String(
        item?.siswa?.jenis_kelamin ?? ''
    )
        .trim()
        .toLowerCase()

    return [
        'laki-laki',
        'laki laki',
        'putera',
        'l',
        'male',
        'm',
    ].includes(gender)
}


/*
|--------------------------------------------------------------------------
| DATA BEEP TEST
|--------------------------------------------------------------------------
|
| Beep Test disimpan sebagai:
| level + balikan.
|
| Contoh:
| Level 12, Balikan 3 -> L12 B3
| Level 9, Balikan 10 -> L9 B10
|
| Jika backend hanya mengirim nilai 5.8, fungsi ini tetap mencoba
| membaca bagian level dan balikan dari nilai tersebut.
|--------------------------------------------------------------------------
*/

function getBeepData(item, namaKomponen) {
    const komponen = item?.komponen?.[namaKomponen]

    if (!komponen) {
        return null
    }

    let level = komponen.level
    let balikan = komponen.balikan

    if (
        level !== null &&
        level !== undefined &&
        level !== '' &&
        balikan !== null &&
        balikan !== undefined &&
        balikan !== ''
    ) {
        level = Number(level)
        balikan = Number(balikan)

        if (
            !Number.isNaN(level) &&
            !Number.isNaN(balikan)
        ) {
            return {
                level,
                balikan,
            }
        }
    }

    // Fallback apabila backend hanya mengirim nilai, misalnya 12.3.
    if (
        komponen.nilai !== null &&
        komponen.nilai !== undefined &&
        komponen.nilai !== ''
    ) {
        const nilai = Number(komponen.nilai)

        if (!Number.isNaN(nilai)) {
            const text = String(komponen.nilai)
            const parts = text.split('.')

            if (parts.length === 2) {
                const parsedLevel = Number(parts[0])
                const parsedBalikan = Number(parts[1])

                if (
                    !Number.isNaN(parsedLevel) &&
                    !Number.isNaN(parsedBalikan)
                ) {
                    return {
                        level: parsedLevel,
                        balikan: parsedBalikan,
                    }
                }
            }
        }
    }

    return null
}


function formatBeepTest(item, namaKomponen) {
    const beep = getBeepData(item, namaKomponen)

    if (!beep) {
        return '-'
    }

    return `L${beep.level} B${beep.balikan}`
}


/*
|--------------------------------------------------------------------------
| BANDINGKAN LEVEL + BALIKAN
|--------------------------------------------------------------------------
|
| Tidak menggunakan desimal biasa karena L9 B10 tidak boleh dibaca
| sebagai 10.0. Yang dibandingkan adalah pasangan level dan balikan.
|--------------------------------------------------------------------------
*/

function beepAtLeast(level, balikan, targetLevel, targetBalikan) {
    if (level > targetLevel) {
        return true
    }

    if (level < targetLevel) {
        return false
    }

    return balikan >= targetBalikan
}


/*
|--------------------------------------------------------------------------
| KATEGORI BEEP TEST SESUAI NORMA TKSI
|--------------------------------------------------------------------------
|
| PUTERA
| 5 : >= L12 B3
| 4 : L9 B10 - L12 B2
| 3 : L7 B4  - L9 B9
| 2 : L4 B8  - L7 B3
| 1 : <= L4 B7
|
| PUTERI
| 5 : >= L7 B10
| 4 : L6 B2  - L7 B9
| 3 : L4 B6  - L6 B1
| 2 : L1 B5  - L4 B5
| 1 : <= L1 B4
|--------------------------------------------------------------------------
*/

function kategoriBeepReport(item, namaKomponen) {
    const beep = getBeepData(item, namaKomponen)

    if (!beep) {
        return null
    }

    const { level, balikan } = beep
    const putera = isPuteraReport(item)

    if (putera) {
        if (beepAtLeast(level, balikan, 12, 3)) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (beepAtLeast(level, balikan, 9, 10)) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (beepAtLeast(level, balikan, 7, 4)) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (beepAtLeast(level, balikan, 4, 8)) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }

    if (beepAtLeast(level, balikan, 7, 10)) {
        return {
            skor: 5,
            kategori: 'Baik Sekali',
        }
    }

    if (beepAtLeast(level, balikan, 6, 2)) {
        return {
            skor: 4,
            kategori: 'Baik',
        }
    }

    if (beepAtLeast(level, balikan, 4, 6)) {
        return {
            skor: 3,
            kategori: 'Sedang',
        }
    }

    if (beepAtLeast(level, balikan, 1, 5)) {
        return {
            skor: 2,
            kategori: 'Kurang',
        }
    }

    return {
        skor: 1,
        kategori: 'Kurang Sekali',
    }
}

/*
|--------------------------------------------------------------------------
| HASIL AKHIR TKSI
|--------------------------------------------------------------------------
|
| Menghitung hasil akhir berdasarkan seluruh komponen yang memiliki nilai.
| Setiap kategori dikonversikan menjadi skor:
|
| 5 = Baik Sekali
| 4 = Baik
| 3 = Sedang
| 2 = Kurang
| 1 = Kurang Sekali
|
*/

function skorKategori(kategori) {

    const text = String(kategori ?? '')
        .trim()
        .toLowerCase()

    if (
        text.includes('sangat baik') ||
        text.includes('baik sekali')
    ) {
        return 5
    }

    if (text === 'baik' || text.includes('baik')) {
        return 4
    }

    if (
        text.includes('cukup') ||
        text.includes('sedang')
    ) {
        return 3
    }

    if (
        text.includes('kurang sekali') ||
        text.includes('sangat kurang')
    ) {
        return 1
    }

    if (text.includes('kurang')) {
        return 2
    }

    if (
        text.includes('buruk') ||
        text.includes('sangat buruk')
    ) {
        return 1
    }

    return null
}


function getSkorKomponen(item, namaKomponen) {

    if (!hasNilai(item, namaKomponen)) {
        return null
    }

    // Beep Test menggunakan norma khusus
    if (isBeepTest(namaKomponen)) {

        const hasil = kategoriBeepReport(
            item,
            namaKomponen
        )

        return hasil?.skor ?? null
    }

    const komponen =
        item?.komponen?.[namaKomponen]

    if (!komponen) {
        return null
    }

    const kategori =
        komponen.kategori ??
        komponen.kategori_nilai ??
        ''

    return skorKategori(kategori)
}


/*
|--------------------------------------------------------------------------
| HASIL AKHIR SISWA
|--------------------------------------------------------------------------
*/

function hasilAkhirTKSI(item) {

    const skor = []

    props.komponenOptions.forEach(
        namaKomponen => {

            const nilai =
                getSkorKomponen(
                    item,
                    namaKomponen
                )

            if (
                nilai !== null &&
                nilai !== undefined
            ) {
                skor.push(nilai)
            }
        }
    )

    // Belum ada komponen yang memiliki hasil
    if (!skor.length) {
        return null
    }

    const rata =
        skor.reduce(
            (total, nilai) => total + nilai,
            0
        ) / skor.length

    if (rata >= 4.5) {
        return {
            skor: rata,
            kategori: 'Baik Sekali',
        }
    }

    if (rata >= 3.5) {
        return {
            skor: rata,
            kategori: 'Baik',
        }
    }

    if (rata >= 2.5) {
        return {
            skor: rata,
            kategori: 'Sedang',
        }
    }

    if (rata >= 1.5) {
        return {
            skor: rata,
            kategori: 'Kurang',
        }
    }

    return {
        skor: rata,
        kategori: 'Kurang Sekali',
    }
}

function hasilAkhirClass(item) {

    const hasil = hasilAkhirTKSI(item)

    if (!hasil) {
        return 'bg-slate-100 text-slate-500'
    }

    switch (hasil.kategori) {

        case 'Baik Sekali':
            return 'bg-emerald-100 text-emerald-700'

        case 'Baik':
            return 'bg-green-100 text-green-700'

        case 'Sedang':
            return 'bg-pink-100 text-pink-700'

        case 'Kurang':
            return 'bg-amber-100 text-amber-700'

        case 'Kurang Sekali':
            return 'bg-red-100 text-red-700'

        default:
            return 'bg-slate-100 text-slate-500'
    }
}
/*
|--------------------------------------------------------------------------
| NORMA BEEP TEST UNTUK DETAIL REPORT
|--------------------------------------------------------------------------
*/

function normaBeepReport(item) {
    if (isPuteraReport(item)) {
        return [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≥ L12 B3',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: 'L9 B10 – L12 B2',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: 'L7 B4 – L9 B9',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: 'L4 B8 – L7 B3',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '≤ L4 B7',
            },
        ]
    }

    return [
        {
            skor: 5,
            kategori: 'Baik Sekali',
            range: '≥ L7 B10',
        },
        {
            skor: 4,
            kategori: 'Baik',
            range: 'L6 B2 – L7 B9',
        },
        {
            skor: 3,
            kategori: 'Sedang',
            range: 'L4 B6 – L6 B1',
        },
        {
            skor: 2,
            kategori: 'Kurang',
            range: 'L1 B5 – L4 B5',
        },
        {
            skor: 1,
            kategori: 'Kurang Sekali',
            range: '≤ L1 B4',
        },
    ]
}


/*
|--------------------------------------------------------------------------
| CEK NILAI KOMPONEN
|--------------------------------------------------------------------------
*/

function hasNilai(
    item,
    namaKomponen
) {
    const komponen = item?.komponen?.[namaKomponen]

    if (!komponen) {
        return false
    }

    if (isBeepTest(namaKomponen)) {
        return Boolean(getBeepData(item, namaKomponen))
    }

    return (
        komponen.nilai !== null &&
        komponen.nilai !== undefined &&
        komponen.nilai !== ''
    )
}


/*
|--------------------------------------------------------------------------
| CEK SUDAH TES
|--------------------------------------------------------------------------
*/

function sudahTes(item) {

    if (
        item?.sudah_tes !== undefined
    ) {
        return Boolean(item.sudah_tes)
    }

    if (!item?.komponen) {
        return false
    }

    return Object.keys(item.komponen).some(
        namaKomponen =>
            hasNilai(
                item,
                namaKomponen
            )
    )
}


/*
|--------------------------------------------------------------------------
| CEK SEMUA KOMPONEN LENGKAP
|--------------------------------------------------------------------------
*/

function semuaKomponenLengkap(item) {

    if (!props.komponenOptions.length) {
        return false
    }

    return props.komponenOptions.every(
        namaKomponen =>
            hasNilai(
                item,
                namaKomponen
            )
    )
}


/*
|--------------------------------------------------------------------------
| STATUS KOMPONEN
|--------------------------------------------------------------------------
*/
function komponenClass(item, namaKomponen) {
    const komponen = item?.komponen?.[namaKomponen]

    if (!hasNilai(item, namaKomponen)) {
        return sudahTes(item)
            ? 'bg-amber-100 text-amber-700'
            : 'bg-slate-100 text-slate-500'
    }

    let kategori = ''

    if (isBeepTest(namaKomponen)) {
        kategori =
            kategoriBeepReport(
                item,
                namaKomponen
            )?.kategori ?? ''
    } else {
        kategori =
            komponen.kategori ??
            komponen.kategori_nilai ??
            ''
    }

    const kategoriLower = String(kategori).toLowerCase()

    if (
        kategoriLower.includes('sangat baik') ||
        kategoriLower.includes('baik')
    ) {
        return 'bg-emerald-100 text-emerald-700'
    }

    if (
        kategoriLower.includes('cukup') ||
        kategoriLower.includes('sedang')
    ) {
        return 'bg-pink-100 text-pink-700'
    }

    if (
        kategoriLower.includes('kurang') ||
        kategoriLower.includes('rendah')
    ) {
        return 'bg-amber-100 text-amber-700'
    }

    if (
        kategoriLower.includes('buruk') ||
        kategoriLower.includes('sangat kurang')
    ) {
        return 'bg-red-100 text-red-700'
    }

    return 'bg-slate-100 text-slate-600'
}


/*
|--------------------------------------------------------------------------
| LABEL KOMPONEN
|--------------------------------------------------------------------------
*/
function komponenLabel(item, namaKomponen) {
    const komponen = item?.komponen?.[namaKomponen]

    if (!hasNilai(item, namaKomponen)) {
        return sudahTes(item)
            ? 'Belum Diisi'
            : 'Belum Tes'
    }

    if (isBeepTest(namaKomponen)) {
        const hasil = kategoriBeepReport(
            item,
            namaKomponen
        )

        return hasil
            ? `${hasil.kategori} (Skor ${hasil.skor})`
            : '-'
    }

    if (komponen.kategori) {
        return komponen.kategori
    }

    if (komponen.kategori_nilai) {
        return komponen.kategori_nilai
    }

    return '-'
}


/*
|--------------------------------------------------------------------------
| NILAI YANG DITAMPILKAN DI REPORT
|--------------------------------------------------------------------------
*/

function komponenNilaiLabel(item, namaKomponen) {
    if (!hasNilai(item, namaKomponen)) {
        return '-'
    }

    if (isBeepTest(namaKomponen)) {
        return formatBeepTest(
            item,
            namaKomponen
        )
    }

    return formatNilai(
        item?.komponen?.[namaKomponen]?.nilai
    )
}


/*
|--------------------------------------------------------------------------
| PARAMETER EXPORT
|--------------------------------------------------------------------------
*/

function buildExportParams() {

    const params =
        new URLSearchParams()

    if (search.value.trim()) {

        params.set(
            'search',
            search.value.trim()
        )
    }

    if (periodeId.value) {

        params.set(
            'periode_id',
            periodeId.value
        )
    }

    if (tingkat.value) {

        params.set(
            'tingkat',
            tingkat.value
        )
    }

    if (jurusanId.value) {

        params.set(
            'jurusan_id',
            jurusanId.value
        )
    }

    selectedKomponen.value.forEach(
        komponen => {

            params.append(
                'komponen[]',
                komponen
            )
        }
    )

    return params
}


/*
|--------------------------------------------------------------------------
| EXPORT EXCEL
|--------------------------------------------------------------------------
*/

function exportExcel() {

    const params =
        buildExportParams()

    window.location.href =
        `/tksi/report/excel?${params.toString()}`
}


/*
|--------------------------------------------------------------------------
| EXPORT PDF
|--------------------------------------------------------------------------
*/

function exportPdf() {

    const params =
        buildExportParams()

    window.location.href =
        `/tksi/report/pdf?${params.toString()}`
}

</script>


<template>

<TksiLayout>

    <div class="space-y-6">


        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-700 via-pink-700 to-rose-800 p-6 text-white shadow-lg md:p-8"
        >

            <div
                class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-16 right-20 h-48 w-48 rounded-full bg-white/5"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-20 -left-10 h-40 w-40 rounded-full bg-pink-400/10"
            ></div>


            <div class="relative z-10">

                <div
                    class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between"
                >

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-6 w-6"
                            />

                        </div>


                        <div>

                            <h1
                                class="text-2xl font-bold tracking-tight"
                            >
                                Report TKSI
                            </h1>

                            <p
                                class="mt-1 max-w-2xl text-sm font-medium text-white/80"
                            >
                                Laporan hasil tes kebugaran siswa
                                pada periode aktif.
                            </p>

                        </div>

                    </div>


                    <div
                        v-if="periode"
                        class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 backdrop-blur-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/10"
                            >

                                <CalendarDaysIcon
                                    class="h-5 w-5"
                                />

                            </div>


                            <div>

                                <p
                                    class="text-[9px] font-extrabold uppercase tracking-wider text-white/60"
                                >
                                    Periode Aktif
                                </p>

                                <p
                                    class="mt-0.5 text-sm font-extrabold text-white"
                                >
                                    {{ periode.nama_periode }}
                                </p>

                                <p
                                    class="mt-0.5 text-[10px] font-medium text-white/70"
                                >
                                    {{ formatTanggal(periode.tanggal_mulai) }}
                                    —
                                    {{ formatTanggal(periode.tanggal_selesai) }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- NO PERIODE -->
        <!-- ========================================================= -->

        <div
            v-if="!periodes.length"
            class="rounded-2xl border border-amber-200 bg-amber-50 p-6"
        >

            <div class="flex items-start gap-4">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-100"
                >

                    <ClockIcon
                        class="h-5 w-5 text-amber-600"
                    />

                </div>


                <div>

                    <h3
                        class="font-bold text-amber-800"
                    >
                        Belum Ada Periode
                    </h3>

                    <p
                        class="mt-1 text-sm text-amber-700"
                    >
                        Belum terdapat periode pemeriksaan yang tersedia.
                    </p>

                </div>

            </div>

        </div>



        <!-- ========================================================= -->
        <!-- CONTENT -->
        <!-- ========================================================= -->

        <template v-else>


            <!-- INFO PERIODE -->

            <div
                class="rounded-2xl border border-pink-100 bg-pink-50 p-5"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-pink-100"
                    >

                        <CalendarDaysIcon
                            class="h-6 w-6 text-pink-600"
                        />

                    </div>


                    <div>

                        <p
                            class="text-[10px] font-bold uppercase tracking-wide text-pink-500"
                        >
                            Periode TKSI
                        </p>

                        <p
                            class="mt-0.5 font-bold text-pink-700"
                        >
                            {{ selectedPeriodeName }}
                        </p>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- STATISTIK -->
            <!-- ===================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
            >

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Total Siswa
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-slate-800"
                    >
                        {{ totalSiswa }}
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Siswa dalam periode
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Total Hasil
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-pink-600"
                    >
                        {{ totalHasil }}
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Hasil pemeriksaan TKSI
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Komponen TKSI
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-pink-600"
                    >
                        {{ jumlahKomponen }}
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Komponen pemeriksaan
                    </p>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <p
                        class="text-xs font-semibold uppercase text-slate-400"
                    >
                        Siswa Lengkap
                    </p>

                    <p
                        class="mt-2 text-3xl font-bold text-emerald-600"
                    >
                        {{ progressLengkap }}%
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        {{ siswaLengkap }} siswa lengkap
                    </p>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- FILTER -->
            <!-- ===================================================== -->

            <div
                class="rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div class="flex items-center gap-2">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-pink-50"
                        >

                            <FunnelIcon
                                class="h-4 w-4 text-pink-600"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Filter Data
                            </h2>

                            <p
                                class="text-xs text-slate-400"
                            >
                                Sesuaikan data laporan TKSI
                            </p>

                        </div>

                    </div>


                    <span
                        v-if="activeFilterCount"
                        class="w-fit rounded-full bg-pink-50 px-3 py-1.5 text-[10px] font-bold text-pink-700"
                    >
                        {{ activeFilterCount }} filter aktif
                    </span>

                </div>



                <div class="p-5">


                    <!-- BARIS 1 -->

                    <div
                        class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5"
                    >


                        <!-- SEARCH -->

                        <div
                            class="relative xl:col-span-2"
                        >

                            <MagnifyingGlassIcon
                                class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                            />

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Cari siswa / NISN..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-9 pr-9 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-pink-500 focus:bg-white focus:ring-2 focus:ring-pink-100"
                                @keyup.enter="applyFilter"
                            />

                            <button
                                v-if="search"
                                type="button"
                                @click="search = ''"
                                class="absolute right-2 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100"
                            >

                                <XMarkIcon
                                    class="h-4 w-4"
                                />

                            </button>

                        </div>



                        <!-- PERIODE -->

                        <div class="relative">

                            <button
                                type="button"
                                @click="toggleDropdown('periode')"
                                class="inline-flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >

                                <span
                                    class="flex min-w-0 items-center gap-2"
                                >

                                    <CalendarDaysIcon
                                        class="h-4 w-4 shrink-0 text-pink-500"
                                    />

                                    <span class="truncate">
                                        {{ selectedPeriodeName }}
                                    </span>

                                </span>


                                <ChevronDownIcon
                                    class="h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform"
                                    :class="{
                                        'rotate-180':
                                            openDropdown === 'periode'
                                    }"
                                />

                            </button>


                            <div
                                v-if="openDropdown === 'periode'"
                                class="absolute left-0 right-0 z-40 mt-2 max-h-72 overflow-y-auto rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                            >

                                <div
                                    class="mb-1 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Pilih Periode
                                </div>


                                <button
                                    type="button"
                                    @click="periodeId = ''; closeDropdown()"
                                    class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold transition hover:bg-slate-50"
                                    :class="
                                        !periodeId
                                            ? 'bg-pink-50 text-pink-700'
                                            : 'text-slate-600'
                                    "
                                >
                                    Semua Periode
                                </button>


                                <button
                                    v-for="item in periodes"
                                    :key="item.id"
                                    type="button"
                                    @click="periodeId = item.id; closeDropdown()"
                                    class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold transition hover:bg-slate-50"
                                    :class="
                                        String(periodeId) === String(item.id)
                                            ? 'bg-pink-50 text-pink-700'
                                            : 'text-slate-600'
                                    "
                                >

                                    {{ item.nama_periode }}

                                    <span
                                        v-if="item.status === 'aktif'"
                                        class="ml-1 text-[10px] text-emerald-600"
                                    >
                                        Aktif
                                    </span>

                                </button>

                            </div>

                        </div>



                        <!-- TINGKAT -->

                        <div class="relative">

                            <button
                                type="button"
                                @click="toggleDropdown('tingkat')"
                                class="inline-flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >

                                <span class="truncate">

                                    {{
                                        tingkat
                                            ? `Tingkat ${tingkat}`
                                            : 'Semua Tingkat'
                                    }}

                                </span>


                                <ChevronDownIcon
                                    class="h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform"
                                    :class="{
                                        'rotate-180':
                                            openDropdown === 'tingkat'
                                    }"
                                />

                            </button>


                            <div
                                v-if="openDropdown === 'tingkat'"
                                class="absolute left-0 right-0 z-40 mt-2 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                            >

                                <div
                                    class="mb-1 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Tingkat Kelas
                                </div>


                                <button
                                    type="button"
                                    @click="tingkat = ''; closeDropdown()"
                                    class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold hover:bg-slate-50"
                                    :class="
                                        !tingkat
                                            ? 'bg-pink-50 text-pink-700'
                                            : 'text-slate-600'
                                    "
                                >
                                    Semua Tingkat
                                </button>


                                <button
                                    v-for="item in tingkatOptions"
                                    :key="item"
                                    type="button"
                                    @click="tingkat = item; closeDropdown()"
                                    class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold hover:bg-slate-50"
                                    :class="
                                        String(tingkat) === String(item)
                                            ? 'bg-pink-50 text-pink-700'
                                            : 'text-slate-600'
                                    "
                                >
                                    Tingkat {{ item }}
                                </button>

                            </div>

                        </div>



                        <!-- JURUSAN -->

                        <div class="relative">

                            <button
                                type="button"
                                @click="toggleDropdown('jurusan')"
                                class="inline-flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >

                                <span class="truncate">
                                    {{ selectedJurusanName }}
                                </span>


                                <ChevronDownIcon
                                    class="h-3.5 w-3.5 shrink-0 text-slate-400 transition-transform"
                                    :class="{
                                        'rotate-180':
                                            openDropdown === 'jurusan'
                                    }"
                                />

                            </button>


                            <div
                                v-if="openDropdown === 'jurusan'"
                                class="absolute left-0 right-0 z-40 mt-2 rounded-xl border border-slate-200 bg-white p-2 shadow-xl"
                            >

                                <div
                                    class="mb-1 px-3 py-2 text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Jurusan
                                </div>


                                <button
                                    type="button"
                                    @click="jurusanId = ''; closeDropdown()"
                                    class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold hover:bg-slate-50"
                                    :class="
                                        !jurusanId
                                            ? 'bg-pink-50 text-pink-700'
                                            : 'text-slate-600'
                                    "
                                >
                                    Semua Jurusan
                                </button>


                                <div
                                    class="max-h-60 overflow-y-auto"
                                >

                                    <button
                                        v-for="jurusan in jurusanOptions"
                                        :key="jurusan.id"
                                        type="button"
                                        @click="jurusanId = jurusan.id; closeDropdown()"
                                        class="w-full rounded-lg px-3 py-2.5 text-left text-xs font-semibold hover:bg-slate-50"
                                        :class="
                                            String(jurusanId) === String(jurusan.id)
                                                ? 'bg-pink-50 text-pink-700'
                                                : 'text-slate-600'
                                        "
                                    >
                                        {{ jurusan.nama_jurusan }}
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- BARIS 2 -->

                    <div
                        class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5"
                    >


                        <!-- KOMPONEN -->

                        <div
                            class="relative xl:col-span-2"
                        >

                            <button
                                type="button"
                                @click="toggleDropdown('komponen')"
                                class="inline-flex w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100"
                            >

                                <span
                                    class="flex min-w-0 items-center gap-2"
                                >

                                    <FunnelIcon
                                        class="h-4 w-4 shrink-0 text-pink-500"
                                    />

                                    <span class="truncate">
                                        {{ selectedKomponenLabel }}
                                    </span>

                                </span>


                                <span
                                    class="flex shrink-0 items-center gap-2"
                                >

                                    <span
                                        v-if="selectedKomponen.length"
                                        class="flex h-5 min-w-5 items-center justify-center rounded-full bg-pink-600 px-1.5 text-[9px] font-bold text-white"
                                    >
                                        {{ selectedKomponen.length }}
                                    </span>


                                    <ChevronDownIcon
                                        class="h-3.5 w-3.5 text-slate-400 transition-transform"
                                        :class="{
                                            'rotate-180':
                                                openDropdown === 'komponen'
                                        }"
                                    />

                                </span>

                            </button>


                            <div
                                v-if="openDropdown === 'komponen'"
                                class="absolute left-0 z-40 mt-2 w-full max-w-md rounded-xl border border-slate-200 bg-white p-3 shadow-xl"
                            >

                                <div
                                    class="mb-2 flex items-center justify-between"
                                >

                                    <div>

                                        <p
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            Komponen TKSI
                                        </p>

                                        <p
                                            class="text-[10px] text-slate-400"
                                        >
                                            Pilih komponen yang ditampilkan
                                        </p>

                                    </div>


                                    <div class="flex items-center gap-1">

                                        <button
                                            type="button"
                                            @click="selectAllKomponen"
                                            class="rounded-md px-2 py-1 text-[10px] font-bold text-pink-600 hover:bg-pink-50"
                                        >
                                            Semua
                                        </button>

                                        <button
                                            type="button"
                                            @click="clearKomponen"
                                            class="rounded-md px-2 py-1 text-[10px] font-bold text-slate-500 hover:bg-slate-50"
                                        >
                                            Bersihkan
                                        </button>

                                    </div>

                                </div>


                                <div
                                    v-if="komponenOptions.length"
                                    class="max-h-60 overflow-y-auto rounded-lg border border-slate-100 p-1"
                                >

                                    <label
                                        v-for="komponen in komponenOptions"
                                        :key="komponen"
                                        class="flex cursor-pointer items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-slate-50"
                                    >

                                        <input
                                            type="checkbox"
                                            :checked="isKomponenSelected(komponen)"
                                            @change="toggleKomponen(komponen)"
                                            class="h-4 w-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500"
                                        />

                                        <span
                                            class="text-xs font-medium text-slate-700"
                                        >
                                            {{ komponen }}
                                        </span>

                                    </label>

                                </div>


                                <div
                                    v-else
                                    class="rounded-lg bg-slate-50 px-3 py-4 text-center text-xs text-slate-400"
                                >
                                    Belum ada komponen TKSI.
                                </div>


                                <div
                                    class="mt-2 rounded-lg bg-slate-50 px-3 py-2"
                                >

                                    <span
                                        class="text-[10px] font-semibold text-slate-500"
                                    >

                                        <template
                                            v-if="selectedKomponen.length"
                                        >
                                            {{ selectedKomponen.length }}
                                            komponen dipilih
                                        </template>

                                        <template v-else>
                                            Semua komponen
                                        </template>

                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- ACTION -->

                        <div
                            class="flex flex-wrap items-center gap-2 xl:col-span-3 xl:justify-end"
                        >

                            <button
                                type="button"
                                @click="applyFilter"
                                class="inline-flex items-center gap-2 rounded-xl bg-pink-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-pink-700"
                            >

                                <FunnelIcon
                                    class="h-4 w-4"
                                />

                                Terapkan

                                <span
                                    v-if="activeFilterCount"
                                    class="flex h-5 min-w-5 items-center justify-center rounded-full bg-white/20 px-1.5 text-[9px] font-bold"
                                >
                                    {{ activeFilterCount }}
                                </span>

                            </button>


                            <button
                                v-if="activeFilterCount"
                                type="button"
                                @click="resetFilter"
                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50"
                            >

                                <ArrowPathIcon
                                    class="h-4 w-4"
                                />

                                Reset

                            </button>

                        </div>

                    </div>



                    <!-- FILTER AKTIF -->

                    <div
                        v-if="activeFilterCount"
                        class="mt-4 flex flex-wrap items-center gap-2 rounded-xl border border-pink-100 bg-pink-50 px-4 py-3"
                    >

                        <CheckCircleIcon
                            class="h-4 w-4 text-pink-500"
                        />

                        <span
                            class="text-xs font-semibold text-pink-700"
                        >
                            Filter aktif:
                        </span>


                        <span
                            v-if="search"
                            class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-slate-700"
                        >
                            Pencarian: "{{ search }}"
                        </span>


                        <span
                            v-if="periodeId"
                            class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-pink-700"
                        >
                            {{ selectedPeriodeName }}
                        </span>


                        <span
                            v-if="tingkat"
                            class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-pink-700"
                        >
                            Tingkat {{ tingkat }}
                        </span>


                        <span
                            v-if="jurusanId"
                            class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-pink-700"
                        >
                            {{ selectedJurusanName }}
                        </span>


                        <span
                            v-if="selectedKomponen.length"
                            class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-fuchsia-700"
                        >
                            {{ selectedKomponenLabel }}
                        </span>

                    </div>

                </div>

            </div>



            <!-- ===================================================== -->
            <!-- TABLE -->
            <!-- ===================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >


                <!-- HEADER TABLE -->

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div>

                        <h2
                            class="text-sm font-bold text-slate-800"
                        >
                            Hasil TKSI Siswa
                        </h2>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >

                            <template
                                v-if="selectedKomponen.length"
                            >

                                Menampilkan hasil komponen

                                <strong class="text-pink-500">
                                    {{ selectedKomponenLabel }}
                                </strong>

                                setiap siswa.

                            </template>

                            <template v-else>
                                Rekap seluruh komponen TKSI setiap siswa.
                            </template>

                        </p>

                    </div>


                    <div
                        class="flex flex-wrap items-center gap-2"
                    >

                        <button
                            type="button"
                            @click="exportExcel"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-emerald-700"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh Excel

                        </button>


                        <button
                            type="button"
                            @click="exportPdf"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50"
                        >

                            <ArrowDownTrayIcon
                                class="h-4 w-4"
                            />

                            Unduh PDF

                        </button>

                    </div>

                </div>



                <!-- EMPTY -->

                <div
                    v-if="!data.length"
                    class="px-6 py-14 text-center"
                >

                    <ClipboardDocumentCheckIcon
                        class="mx-auto h-10 w-10 text-slate-300"
                    />

                    <p
                        class="mt-3 text-sm font-semibold text-slate-600"
                    >
                        Tidak ada data siswa
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Tidak terdapat siswa yang sesuai dengan filter yang dipilih.
                    </p>

                </div>



                <!-- TABLE -->

                <div
                    v-else
                    class="w-full overflow-x-auto"
                >

                    <table
                        class="w-full table-fixed text-left"
                        :style="{
                            minWidth: `${tableMinWidth}px`
                        }"
                    >

                        <thead
                            class="border-b border-slate-100 bg-slate-50"
                        >

                            <tr>

                                <th
                                    class="w-[60px] min-w-[60px] px-4 py-4 text-center text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    No
                                </th>


                                <th
                                    class="w-[240px] min-w-[240px] px-5 py-4 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Siswa
                                </th>


                                <th
                                    class="w-[150px] min-w-[150px] px-5 py-4 text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Kelas
                                </th>


                                <th
    class="w-[180px] min-w-[180px] px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wide text-pink-500"
>
    Tanggal Tes
</th>


                                <th
                                    v-for="namaKomponen in displayedKomponen"
                                    :key="namaKomponen"
                                    class="w-[170px] min-w-[170px] max-w-[170px] px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wide text-pink-500"
                                >

                                    <div
                                        class="mx-auto max-w-[140px] whitespace-normal break-words leading-4"
                                    >
                                        {{ namaKomponen }}
                                    </div>

                                </th>


                               <th
    class="w-[160px] min-w-[160px] px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wide text-pink-500"
>
    Hasil Akhir
</th>


                                <th
                                    class="w-[110px] min-w-[110px] px-5 py-4 text-center text-[11px] font-bold uppercase tracking-wide text-slate-400"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>



                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="(item, index) in data"
                                :key="item.siswa_id"
                                class="transition hover:bg-slate-50/70"
                            >


                                <!-- NO -->

                                <td
                                    class="px-4 py-5 text-center text-xs text-slate-400"
                                >
                                    {{ index + 1 }}
                                </td>



                                <!-- SISWA -->

                                <td
                                    class="px-5 py-5"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.siswa?.nama ?? '-' }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        NISN:
                                        {{ item.siswa?.nisn || '-' }}
                                    </p>

                                </td>



                                <!-- KELAS -->

                                <td
                                    class="px-5 py-5"
                                >

                                    <p
                                        class="text-sm font-semibold text-slate-700"
                                    >
                                        {{ item.siswa?.kelas?.nama_kelas || '-' }}
                                    </p>

                                    <p
                                        v-if="item.siswa?.jurusan"
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        {{ item.siswa.jurusan.nama_jurusan }}
                                    </p>

                                </td>



                                <!-- TANGGAL TES -->

                                <td
                                        class="w-[180px] min-w-[180px] px-5 py-5 text-center"
                                >

                                    <div
                                        v-if="item.tanggal_tes"
                                        class="flex flex-col items-center"
                                    >

                                        <div
                                            class="flex items-center gap-1.5 text-sm font-semibold text-slate-700"
                                        >

                                            <CalendarDaysIcon
                                                class="h-4 w-4 text-pink-500"
                                            />

                                            {{ formatTanggal(item.tanggal_tes) }}

                                        </div>


                                        <span
                                            class="mt-1 text-[10px] text-slate-400"
                                        >
                                            {{ formatWaktu(item.tanggal_tes) }}
                                        </span>

                                    </div>


                                    <span
                                        v-else
                                        class="text-xs text-slate-400"
                                    >
                                        -
                                    </span>

                                </td>



                                <!-- KOMPONEN -->

                                <td
                                    v-for="namaKomponen in displayedKomponen"
                                    :key="namaKomponen"
                                    class="w-[170px] min-w-[170px] max-w-[170px] px-5 py-5 text-center"
                                >

                                    <div
                                        class="flex min-h-[55px] flex-col items-center justify-center gap-1.5"
                                    >

                                        <span
                                            :class="[
                                                'inline-flex min-w-[70px] justify-center rounded-full px-3 py-1.5 text-xs font-bold',
                                                komponenClass(
                                                    item,
                                                    namaKomponen
                                                )
                                            ]"
                                        >

                                            {{
                                                komponenNilaiLabel(
                                                    item,
                                                    namaKomponen
                                                )
                                            }}

                                        </span>


                                        <span
                                            :class="[
                                                'text-[10px] font-medium',
                                                hasNilai(
                                                    item,
                                                    namaKomponen
                                                )
                                                    ? 'text-emerald-600'
                                                    : 'text-amber-600'
                                            ]"
                                        >

                                            {{
                                                komponenLabel(
                                                    item,
                                                    namaKomponen
                                                )
                                            }}

                                        </span>

                                    </div>

                                </td>



                                <!-- RATA-RATA -->
<td
    class="w-[160px] min-w-[160px] px-5 py-5 text-center"
>

    <template v-if="hasilAkhirTKSI(item)">

        <span
            :class="[
                'inline-flex min-w-[110px] justify-center rounded-full px-3 py-1.5 text-xs font-bold',
                hasilAkhirClass(item)
            ]"
        >
            {{ hasilAkhirTKSI(item).kategori }}
        </span>

        <p
            class="mt-1 text-[10px] text-slate-400"
        >
            Berdasarkan
            {{ props.komponenOptions.filter(
                komponen => hasNilai(item, komponen)
            ).length }}
            komponen
        </p>

    </template>

    <span
        v-else
        class="text-xs text-slate-400"
    >
        Belum Ada Hasil
    </span>

</td>

                                <!-- AKSI -->

                                <td
                                    class="w-[110px] min-w-[110px] px-5 py-5 text-center"
                                >

                                    <button
                                        type="button"
                                        @click="openDetail(item)"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-pink-50 px-3 py-2 text-xs font-bold text-pink-700 transition hover:bg-pink-100"
                                    >

                                        <EyeIcon
                                            class="h-4 w-4"
                                        />

                                        Detail

                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>



                <!-- FOOTER -->

                <div
                    v-if="data.length"
                    class="border-t border-slate-100 bg-slate-50 px-6 py-3"
                >

                    <div
                        class="flex flex-col gap-2 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <span>

                            Menampilkan

                            <strong
                                class="font-semibold text-slate-700"
                            >
                                {{ data.length }}
                            </strong>

                            siswa

                        </span>


                        <span>

                            <template
                                v-if="selectedKomponen.length"
                            >

                                {{ selectedKomponen.length }}
                                komponen ditampilkan

                            </template>

                            <template v-else>

                                {{ komponenOptions.length }}
                                komponen per siswa

                            </template>

                        </span>

                    </div>

                </div>

            </div>

        </template>



        <!-- ========================================================= -->
        <!-- MODAL DETAIL -->
        <!-- ========================================================= -->

        <Teleport to="body">

            <div
                v-if="selectedData"
                class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm"
                @click.self="closeDetail"
            >

                <div
                    class="flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                >


                    <!-- HEADER -->

                    <div
                        class="flex items-start justify-between border-b border-slate-100 px-6 py-5"
                    >

                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-pink-500"
                            >
                                Detail Hasil TKSI
                            </p>

                            <h2
                                class="mt-1 text-lg font-bold text-slate-800"
                            >
                                {{ selectedData.siswa?.nama || '-' }}
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                NISN:
                                {{ selectedData.siswa?.nisn || '-' }}
                            </p>

                        </div>


                        <button
                            type="button"
                            @click="closeDetail"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100"
                        >

                            <XMarkIcon
                                class="h-5 w-5"
                            />

                        </button>

                    </div>



                    <!-- BODY -->

                    <div
                        class="min-h-0 flex-1 overflow-y-auto p-6"
                    >


                        <!-- IDENTITAS -->

                        <div
                            class="mb-4 rounded-xl border border-pink-100 bg-pink-50 p-4"
                        >

                            <div
                                class="flex items-start gap-3"
                            >

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-100"
                                >

                                    <UserGroupIcon
                                        class="h-5 w-5 text-pink-600"
                                    />

                                </div>


                                <div>

                                    <p
                                        class="text-xs font-bold uppercase tracking-wide text-pink-600"
                                    >
                                        Data Siswa
                                    </p>

                                    <p
                                        class="mt-1 text-sm font-bold text-pink-700"
                                    >
                                        {{ selectedData.siswa?.nama || '-' }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-pink-700"
                                    >

                                        NISN:
                                        {{ selectedData.siswa?.nisn || '-' }}

                                        <span class="mx-1">
                                            •
                                        </span>

                                        Kelas:
                                        {{
                                            selectedData.siswa?.kelas?.nama_kelas
                                            || '-'
                                        }}

                                        <span
                                            v-if="selectedData.siswa?.jurusan"
                                            class="mx-1"
                                        >
                                            •
                                        </span>

                                        <span
                                            v-if="selectedData.siswa?.jurusan"
                                        >
                                            {{ selectedData.siswa.jurusan.nama_jurusan }}
                                        </span>

                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- TANGGAL TES -->

                        <div
                            class="mb-6 rounded-xl border border-pink-100 bg-white p-4 shadow-sm"
                        >

                            <div
                                class="flex items-center gap-3"
                            >

                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50"
                                >

                                    <CalendarDaysIcon
                                        class="h-5 w-5 text-pink-600"
                                    />

                                </div>


                                <div>

                                    <p
                                        class="text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                    >
                                        Tanggal Tes
                                    </p>

                                    <p
                                        class="mt-1 text-sm font-bold text-slate-800"
                                    >
                                        {{
                                            formatTanggal(
                                                selectedData.tanggal_tes
                                            )
                                        }}
                                    </p>

                                    <p
                                        v-if="selectedData.tanggal_tes"
                                        class="mt-0.5 text-xs text-slate-400"
                                    >
                                        Pukul
                                        {{
                                            formatWaktu(
                                                selectedData.tanggal_tes
                                            )
                                        }}
                                        WIB
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- STATUS BELUM TES -->

                        <div
                            v-if="!sudahTes(selectedData)"
                            class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5"
                        >

                            <div
                                class="flex items-center gap-3"
                            >

                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100"
                                >

                                    <ClockIcon
                                        class="h-6 w-6 text-amber-600"
                                    />

                                </div>


                                <div>

                                    <p
                                        class="text-xs font-bold uppercase tracking-wide text-amber-600"
                                    >
                                        Status TKSI
                                    </p>

                                    <p
                                        class="mt-1 text-lg font-bold text-amber-800"
                                    >
                                        Belum Tes
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-amber-700"
                                    >
                                        Siswa belum melakukan tes TKSI pada periode ini.
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- RATA-RATA -->

                        <div
                            v-else
                            class="mb-6 rounded-xl border border-pink-100 bg-pink-50 p-5"
                        >

                            <div
                                class="flex items-center justify-between"
                            >

                                <div>

                                    <p
                                        class="text-xs font-bold uppercase tracking-wide text-pink-500"
                                    >
                                        Rata-rata TKSI
                                    </p>

                                    <p
                                        class="mt-1 text-3xl font-bold text-pink-700"
                                    >
                                        {{
                                            formatRataRata(
                                                selectedData.rata_rata
                                            )
                                        }}
                                    </p>

                                </div>


                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-100"
                                >

                                    <ChartBarIcon
                                        class="h-6 w-6 text-pink-600"
                                    />

                                </div>

                            </div>

                        </div>



                        <!-- KOMPONEN -->

                        <h3
                            class="mb-3 text-sm font-bold text-slate-800"
                        >
                            Hasil Komponen TKSI
                        </h3>


                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2"
                        >

                            <div
                                v-for="namaKomponen in komponenOptions"
                                :key="namaKomponen"
                                class="rounded-xl border border-slate-100 p-4 transition hover:border-slate-200 hover:bg-slate-50"
                            >

                                <div
                                    class="flex items-start justify-between gap-3"
                                >

                                    <div>

                                        <p
                                            class="text-xs font-medium text-slate-400"
                                        >
                                            {{ namaKomponen }}
                                        </p>


                                        <p
                                            class="mt-1 text-lg font-bold text-slate-700"
                                        >

                                            {{
                                                komponenNilaiLabel(
                                                    selectedData,
                                                    namaKomponen
                                                )
                                            }}

                                        </p>

                                    </div>


                                    <span
                                        :class="[
                                            'rounded-full px-2.5 py-1 text-[10px] font-bold',
                                            komponenClass(
                                                selectedData,
                                                namaKomponen
                                            )
                                        ]"
                                    >

                                        {{
                                            komponenLabel(
                                                selectedData,
                                                namaKomponen
                                            )
                                        }}

                                    </span>

                                </div>



                                <!-- CATATAN -->

                                <p
                                    v-if="
                                        selectedData
                                            .komponen?.[namaKomponen]
                                            ?.catatan
                                    "
                                    class="mt-3 rounded-lg bg-slate-50 p-3 text-xs leading-5 text-slate-600"
                                >

                                    {{
                                        selectedData
                                            .komponen[
                                                namaKomponen
                                            ]
                                            .catatan
                                    }}

                                </p>

                            </div>

                        </div>



                        <!-- NORMA BEEP TEST -->

                        <div
                            v-if="komponenOptions.some(item => isBeepTest(item)) && selectedData"
                            class="mt-5 rounded-xl border border-pink-100 bg-pink-50 p-4"
                        >

                            <p class="text-xs font-bold uppercase tracking-wide text-pink-700">
                                Norma Beep Test — {{ isPuteraReport(selectedData) ? 'Putera' : 'Puteri' }}
                            </p>

                            <div class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-5">

                                <div
                                    v-for="norm in normaBeepReport(selectedData)"
                                    :key="norm.skor"
                                    class="rounded-xl border border-pink-100 bg-white p-3"
                                >
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-extrabold text-slate-700">
                                            {{ norm.kategori }}
                                        </span>
                                        <span class="rounded-full bg-pink-100 px-2 py-0.5 text-[9px] font-bold text-pink-700">
                                            {{ norm.skor }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-[10px] leading-4 text-slate-500">
                                        {{ norm.range }}
                                    </p>
                                </div>

                            </div>

                        </div>


                        <!-- EMPTY KOMPONEN -->

                        <div
                            v-if="!komponenOptions.length"
                            class="rounded-xl bg-slate-50 p-6 text-center"
                        >

                            <ClipboardDocumentCheckIcon
                                class="mx-auto h-8 w-8 text-slate-300"
                            />

                            <p
                                class="mt-2 text-sm text-slate-500"
                            >
                                Belum ada komponen TKSI.
                            </p>

                        </div>

                    </div>



                    <!-- FOOTER -->

                    <div
                        class="flex shrink-0 justify-end border-t border-slate-100 bg-white px-6 py-4"
                    >

                        <button
                            type="button"
                            @click="closeDetail"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Tutup
                        </button>

                    </div>

                </div>

            </div>

        </Teleport>

    </div>

</TksiLayout>

</template>