<script setup>
import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    HeartIcon,
    ClipboardDocumentCheckIcon,
    ArrowDownTrayIcon,
    CheckCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'

/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    periode: {
        type: Object,
        default: () => ({}),
    },

    summary: {
        type: Object,
        default: () => ({
            total_siswa: 0,
            total_kunjungan: 0,
            total_lengkap: 0,
        }),
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    komponenOptions: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| BREADCRUMBS
|--------------------------------------------------------------------------
*/

const breadcrumbs = [
    {
        label: 'Periode',
    },
    {
        label: 'Report',
    },
    {
        label: props.periode?.nama_periode || 'Detail',
    },
]


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

const formatTanggal = (tanggal) => {
    if (!tanggal) {
        return '-'
    }

    return tanggal
}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

const statusClass = (lengkap) => {
    return lengkap
        ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
        : 'bg-red-50 text-red-700 border border-red-200'
}

const statusLabel = (lengkap) => {
    return lengkap ? 'Lengkap' : 'Belum'
}


/*
|--------------------------------------------------------------------------
| KONDISI UMUM BERKALA
|--------------------------------------------------------------------------
*/

const getKondisiUmum = (pemeriksaan) => {

    if (!pemeriksaan) {
        return '-'
    }

    if (
        pemeriksaan.kondisi_umum !== undefined &&
        pemeriksaan.kondisi_umum !== null &&
        pemeriksaan.kondisi_umum !== ''
    ) {
        return pemeriksaan.kondisi_umum
    }

    if (
        pemeriksaan.kondisiUmum !== undefined &&
        pemeriksaan.kondisiUmum !== null &&
        pemeriksaan.kondisiUmum !== ''
    ) {
        return pemeriksaan.kondisiUmum
    }

    const hasil = pemeriksaan.hasil

    if (!hasil) {
        return '-'
    }

    /*
    |--------------------------------------------------------------------------
    | HASIL OBJECT
    |--------------------------------------------------------------------------
    */

    if (
        typeof hasil === 'object' &&
        !Array.isArray(hasil)
    ) {

        const kandidat = [
            'kondisi_umum',
            'kondisiUmum',
            'kondisi umum',
            'kondisi',
            'status_kesehatan',
            'status kesehatan',
            'status',
            'hasil_akhir',
            'hasil akhir',
            'indikator_kondisi_umum',
        ]

        for (const key of kandidat) {

            if (
                hasil[key] !== undefined &&
                hasil[key] !== null &&
                hasil[key] !== ''
            ) {
                return hasil[key]
            }
        }

        const keyDitemukan = Object.keys(hasil).find((key) => {

            const normal = key
                .toLowerCase()
                .replace(/[_-]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()

            return (
                normal.includes('kondisi umum') ||
                normal === 'kondisi' ||
                normal.includes('status kesehatan')
            )
        })

        if (keyDitemukan) {
            return hasil[keyDitemukan]
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HASIL ARRAY
    |--------------------------------------------------------------------------
    */

    if (Array.isArray(hasil)) {

        const indikator = hasil.find((item) => {

            if (
                !item ||
                typeof item !== 'object'
            ) {
                return false
            }

            const nama = String(
                item.indikator ??
                item.nama_indikator ??
                item.komponen ??
                item.nama ??
                ''
            )
                .toLowerCase()
                .replace(/[_-]/g, ' ')
                .replace(/\s+/g, ' ')
                .trim()

            return (
                nama.includes('kondisi umum') ||
                nama === 'kondisi' ||
                nama.includes('status kesehatan')
            )
        })

        if (indikator) {
            return (
                indikator.hasil ??
                indikator.nilai ??
                indikator.kategori ??
                indikator.jawaban ??
                '-'
            )
        }
    }

    return '-'
}


/*
|--------------------------------------------------------------------------
| NORMALISASI NAMA
|--------------------------------------------------------------------------
*/

const normalizeKomponen = (value) => {

    return String(value ?? '')
        .toLowerCase()
        .replace(/[_-]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim()
}


/*
|--------------------------------------------------------------------------
| CEK BEEP TEST
|--------------------------------------------------------------------------
*/

const isBeepTest = (namaKomponen) => {

    return normalizeKomponen(namaKomponen)
        .replace(/\s/g, '') === 'beeptest'
}


/*
|--------------------------------------------------------------------------
| JENIS KELAMIN
|--------------------------------------------------------------------------
*/

const isPutera = (item) => {

    const gender = String(
        item?.jenis_kelamin ??
        item?.jenisKelamin ??
        item?.siswa?.jenis_kelamin ??
        item?.siswa?.jenisKelamin ??
        item?.tksi?.jenis_kelamin ??
        item?.tksi?.jenisKelamin ??
        ''
    )
        .trim()
        .toLowerCase()

    return [
        'laki-laki',
        'laki laki',
        'laki',
        'putera',
        'putra',
        'l',
        'male',
        'm',
    ].includes(gender)
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA KOMPONEN
|--------------------------------------------------------------------------
*/

const getSemuaKomponen = (item) => {

    const tksi = item?.tksi ?? item

    const sumber =
        tksi?.komponen ??
        item?.komponen ??
        {}

    if (
        !sumber ||
        typeof sumber !== 'object' ||
        Array.isArray(sumber)
    ) {
        return {}
    }

    return sumber
}


/*
|--------------------------------------------------------------------------
| CARI KOMPONEN
|--------------------------------------------------------------------------
*/

const getKomponenData = (
    item,
    namaKomponen
) => {

    const sumber =
        getSemuaKomponen(item)

    if (!Object.keys(sumber).length) {
        return null
    }

    if (
        sumber[namaKomponen] !== undefined
    ) {
        return sumber[namaKomponen]
    }

    const target =
        normalizeKomponen(namaKomponen)

    const keyDitemukan =
        Object.keys(sumber).find((key) => {

            return (
                normalizeKomponen(key) === target
            )
        })

    if (keyDitemukan) {
        return sumber[keyDitemukan]
    }

    return null
}


/*
|--------------------------------------------------------------------------
| SKOR KATEGORI
|--------------------------------------------------------------------------
*/

const skorKategori = (kategori) => {

    const text = String(kategori ?? '')
        .trim()
        .toLowerCase()
        .replace(/[_-]/g, ' ')
        .replace(/\s+/g, ' ')

    if (
        text.includes('sangat baik') ||
        text.includes('baik sekali')
    ) {
        return 5
    }

    if (
        text === 'baik' ||
        text.startsWith('baik ')
    ) {
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

    if (
        text.includes('kurang')
    ) {
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


/*
|--------------------------------------------------------------------------
| DATA BEEP TEST
|--------------------------------------------------------------------------
*/

const getBeepData = (
    item,
    namaKomponen
) => {

    const komponen =
        getKomponenData(
            item,
            namaKomponen
        )

    if (!komponen) {
        return null
    }

    let level =
        komponen.level

    let balikan =
        komponen.balikan

    if (
        level !== null &&
        level !== undefined &&
        level !== '' &&
        balikan !== null &&
        balikan !== undefined &&
        balikan !== ''
    ) {

        level =
            Number(level)

        balikan =
            Number(balikan)

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

    const nilai =
        komponen.nilai ??
        komponen.hasil ??
        komponen.value ??
        null

    if (
        nilai !== null &&
        nilai !== undefined &&
        nilai !== ''
    ) {

        const text =
            String(nilai)
                .trim()
                .toLowerCase()

        let match =
            text.match(
                /l\s*(\d+(?:\.\d+)?)\s*b\s*(\d+(?:\.\d+)?)/i
            )

        if (match) {

            const parsedLevel =
                Number(match[1])

            const parsedBalikan =
                Number(match[2])

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

        const parts =
            text.split('.')

        if (parts.length === 2) {

            const parsedLevel =
                Number(parts[0])

            const parsedBalikan =
                Number(parts[1])

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

    return null
}


/*
|--------------------------------------------------------------------------
| BANDINGKAN LEVEL + BALIKAN
|--------------------------------------------------------------------------
*/

const beepAtLeast = (
    level,
    balikan,
    targetLevel,
    targetBalikan
) => {

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
| KATEGORI BEEP TEST
|--------------------------------------------------------------------------
*/

const kategoriBeepReport = (
    item,
    namaKomponen
) => {

    const beep =
        getBeepData(
            item,
            namaKomponen
        )

    if (!beep) {
        return null
    }

    const {
        level,
        balikan,
    } = beep

    const putera =
        isPutera(item)

    if (putera) {

        if (
            beepAtLeast(
                level,
                balikan,
                12,
                3
            )
        ) {

            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (
            beepAtLeast(
                level,
                balikan,
                9,
                10
            )
        ) {

            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (
            beepAtLeast(
                level,
                balikan,
                7,
                4
            )
        ) {

            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (
            beepAtLeast(
                level,
                balikan,
                4,
                8
            )
        ) {

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

    if (
        beepAtLeast(
            level,
            balikan,
            7,
            10
        )
    ) {

        return {
            skor: 5,
            kategori: 'Baik Sekali',
        }
    }

    if (
        beepAtLeast(
            level,
            balikan,
            6,
            2
        )
    ) {

        return {
            skor: 4,
            kategori: 'Baik',
        }
    }

    if (
        beepAtLeast(
            level,
            balikan,
            4,
            6
        )
    ) {

        return {
            skor: 3,
            kategori: 'Sedang',
        }
    }

    if (
        beepAtLeast(
            level,
            balikan,
            1,
            5
        )
    ) {

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
| CEK ADA NILAI
|--------------------------------------------------------------------------
*/

const hasNilai = (
    item,
    namaKomponen
) => {

    const komponen =
        getKomponenData(
            item,
            namaKomponen
        )

    if (!komponen) {
        return false
    }

    if (
        isBeepTest(namaKomponen)
    ) {

        return Boolean(
            getBeepData(
                item,
                namaKomponen
            )
        )
    }

    return (
        komponen.nilai !== null &&
        komponen.nilai !== undefined &&
        komponen.nilai !== ''
    )
}


/*
|--------------------------------------------------------------------------
| SUDAH TES
|--------------------------------------------------------------------------
*/

const sudahTes = (item) => {

    if (
        item?.sudah_tes !== undefined
    ) {

        return Boolean(
            item.sudah_tes
        )
    }

    if (
        item?.tksi?.sudah_tes !== undefined
    ) {

        return Boolean(
            item.tksi.sudah_tes
        )
    }

    const semua =
        getSemuaKomponen(item)

    return Object.keys(semua).some(
        (nama) =>
            hasNilai(
                item,
                nama
            )
    )
}


/*
|--------------------------------------------------------------------------
| SEMUA KOMPONEN LENGKAP
|--------------------------------------------------------------------------
*/

const semuaKomponenLengkap = (item) => {

    if (
        !props.komponenOptions.length
    ) {

        const semua =
            getSemuaKomponen(item)

        return (
            Object.keys(semua).length > 0 &&
            Object.keys(semua).every(
                (nama) =>
                    hasNilai(
                        item,
                        nama
                    )
            )
        )
    }

    return props.komponenOptions.every(
        (namaKomponen) =>
            hasNilai(
                item,
                namaKomponen
            )
    )
}


/*
|--------------------------------------------------------------------------
| HASIL AKHIR TKSI
|--------------------------------------------------------------------------
*/

const hasilAkhirTKSI = (item) => {

    if (!item) {
        return null
    }

    const semuaData =
        getSemuaKomponen(item)

    const semuaKey =
        Object.keys(semuaData)

    let daftarKomponen = []

    if (
        Array.isArray(props.komponenOptions) &&
        props.komponenOptions.length > 0
    ) {

        daftarKomponen = [
            ...props.komponenOptions
        ]

    } else {

        daftarKomponen = [
            ...semuaKey
        ]
    }

    const sudahAda =
        new Set()

    daftarKomponen =
        daftarKomponen.filter(
            (nama) => {

                const normal =
                    normalizeKomponen(
                        nama
                    )

                if (!normal) {
                    return false
                }

                if (
                    sudahAda.has(normal)
                ) {
                    return false
                }

                sudahAda.add(normal)

                return true
            }
        )

    const komponenYangDipakai = []

    daftarKomponen.forEach(
        (namaKomponen) => {

            const data =
                getKomponenData(
                    item,
                    namaKomponen
                )

            if (data) {

                komponenYangDipakai.push({
                    nama: namaKomponen,
                    data,
                })
            }
        }
    )

    if (
        komponenYangDipakai.length <
        semuaKey.length
    ) {

        semuaKey.forEach(
            (key) => {

                const sudahMasuk =
                    komponenYangDipakai.some(
                        (itemKomponen) =>
                            normalizeKomponen(
                                itemKomponen.nama
                            ) ===
                            normalizeKomponen(
                                key
                            )
                    )

                if (!sudahMasuk) {

                    komponenYangDipakai.push({
                        nama: key,
                        data: semuaData[key],
                    })
                }
            }
        )
    }

    const skorKomponen = []

    komponenYangDipakai.forEach(
        ({
            nama,
            data,
        }) => {

            if (!data) {
                return
            }

            if (
                isBeepTest(nama)
            ) {

                const hasilBeep =
                    kategoriBeepReport(
                        item,
                        nama
                    )

                if (hasilBeep) {

                    skorKomponen.push({
                        nama,
                        skor:
                            Number(
                                hasilBeep.skor
                            ),
                        kategori:
                            hasilBeep.kategori,
                    })
                }

                return
            }

            const nilaiAda =
                data.nilai !== null &&
                data.nilai !== undefined &&
                data.nilai !== ''

            if (!nilaiAda) {
                return
            }

            const kategori =
                data.kategori ??
                data.kategori_nilai ??
                data.kategoriNilai ??
                data.hasil ??
                ''

            const skor =
                skorKategori(
                    kategori
                )

            if (
                skor !== null &&
                skor !== undefined
            ) {

                skorKomponen.push({
                    nama,
                    skor:
                        Number(skor),
                    kategori,
                })
            }
        }
    )

    if (
        !skorKomponen.length
    ) {
        return null
    }

    const totalSkor =
        skorKomponen.reduce(
            (
                total,
                komponen
            ) => {

                return (
                    total +
                    Number(
                        komponen.skor
                    )
                )

            },
            0
        )

    const jumlahKomponen =
        skorKomponen.length

    const rataRata =
        totalSkor /
        jumlahKomponen

    let kategoriAkhir =
        'Kurang Sekali'

    if (
        rataRata >= 4.5
    ) {

        kategoriAkhir =
            'Baik Sekali'

    } else if (
        rataRata >= 3.5
    ) {

        kategoriAkhir =
            'Baik'

    } else if (
        rataRata >= 2.5
    ) {

        kategoriAkhir =
            'Sedang'

    } else if (
        rataRata >= 1.5
    ) {

        kategoriAkhir =
            'Kurang'
    }

    return {

        totalSkor,

        jumlahKomponen,

        rataRata:
            Number(
                rataRata.toFixed(2)
            ),

        skor:
            Number(
                rataRata.toFixed(2)
            ),

        kategori:
            kategoriAkhir,

        detail:
            skorKomponen,
    }
}


/*
|--------------------------------------------------------------------------
| GET KATEGORI TKSI
|--------------------------------------------------------------------------
*/

const getKategoriTksi = (item) => {

    const hasil =
        hasilAkhirTKSI(item)

    if (!hasil) {
        return '-'
    }

    return hasil.kategori
}


/*
|--------------------------------------------------------------------------
| CLASS HASIL AKHIR
|--------------------------------------------------------------------------
*/

const hasilAkhirClass = (item) => {

    const hasil =
        hasilAkhirTKSI(item)

    if (!hasil) {
        return 'bg-slate-100 text-slate-500 border-slate-200'
    }

    switch (hasil.kategori) {

        case 'Baik Sekali':
            return 'bg-emerald-100 text-emerald-700 border-emerald-200'

        case 'Baik':
            return 'bg-green-100 text-green-700 border-green-200'

        case 'Sedang':
            return 'bg-blue-100 text-blue-700 border-blue-200'

        case 'Kurang':
            return 'bg-amber-100 text-amber-700 border-amber-200'

        case 'Kurang Sekali':
            return 'bg-red-100 text-red-700 border-red-200'

        default:
            return 'bg-slate-100 text-slate-500 border-slate-200'
    }
}


/*
|--------------------------------------------------------------------------
| DOT
|--------------------------------------------------------------------------
*/

const hasilAkhirDotClass = (item) => {

    const hasil =
        hasilAkhirTKSI(item)

    if (!hasil) {
        return 'bg-slate-400'
    }

    switch (hasil.kategori) {

        case 'Baik Sekali':
            return 'bg-emerald-500'

        case 'Baik':
            return 'bg-green-500'

        case 'Sedang':
            return 'bg-blue-500'

        case 'Kurang':
            return 'bg-amber-500'

        case 'Kurang Sekali':
            return 'bg-red-500'

        default:
            return 'bg-slate-400'
    }
}


/*
|--------------------------------------------------------------------------
| DOWNLOAD REPORT
|--------------------------------------------------------------------------
*/

const downloadReport = () => {

    if (
        !props.periode?.id
    ) {
        return
    }

    window.location.href =
        route(
            'admin.periode.report.download',
            props.periode.id
        )
}

</script>


<template>

    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">


            <!-- =====================================================
                 HEADER
            ====================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div class="p-6">

                    <!-- =================================================
                         TOMBOL KEMBALI
                         KIRI ATAS
                    ================================================== -->

                    <div class="mb-5">

                        <Link
                            :href="route('admin.periode.index')"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-800"
                        >

                            <ArrowLeftIcon
                                class="h-4 w-4"
                            />

                            Kembali

                        </Link>

                    </div>


                    <!-- =================================================
                         HEADER CONTENT
                    ================================================== -->

                    <div
                        class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
                    >

                        <div>

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100"
                                >

                                    <ClipboardDocumentCheckIcon
                                        class="h-6 w-6 text-blue-600"
                                    />

                                </div>


                                <div>

                                    <p
                                        class="text-xs font-bold uppercase tracking-wider text-blue-600"
                                    >
                                        Laporan Kesehatan Siswa
                                    </p>

                                    <h1
                                        class="mt-1 text-2xl font-bold text-slate-800"
                                    >
                                        Detail Report Periode
                                    </h1>

                                </div>

                            </div>


                            <div
                                class="mt-5 flex flex-wrap gap-x-6 gap-y-3"
                            >

                                <div>

                                    <p
                                        class="text-xs font-medium text-slate-400"
                                    >
                                        Periode
                                    </p>

                                    <p
                                        class="mt-1 text-sm font-bold text-slate-700"
                                    >
                                        {{ periode.nama_periode || '-' }}
                                    </p>

                                </div>


                                <div
                                    class="hidden h-8 w-px bg-slate-200 sm:block"
                                ></div>


                                <div>

                                    <p
                                        class="text-xs font-medium text-slate-400"
                                    >
                                        Rentang Periode
                                    </p>

                                    <div
                                        class="mt-1 flex items-center gap-2 text-sm text-slate-600"
                                    >

                                        <CalendarDaysIcon
                                            class="h-4 w-4 text-slate-400"
                                        />

                                        {{ formatTanggal(periode.tanggal_mulai) }}

                                        <span class="text-slate-300">
                                            —
                                        </span>

                                        {{ formatTanggal(periode.tanggal_selesai) }}

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             DOWNLOAD REPORT
                        ================================================== -->

                        <div class="flex flex-wrap gap-2">

                            <button
                                type="button"
                                @click="downloadReport"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700"
                            >

                                <ArrowDownTrayIcon
                                    class="h-4 w-4"
                                />

                                Unduh Report

                            </button>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 SUMMARY
            ====================================================== -->

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-3"
            >

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100"
                        >

                            <UserGroupIcon
                                class="h-6 w-6 text-blue-600"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Siswa
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ summary?.total_siswa ?? 0 }}
                            </p>

                        </div>

                    </div>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100"
                        >

                            <HeartIcon
                                class="h-6 w-6 text-orange-600"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Kunjungan Klinik
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ summary?.total_kunjungan ?? 0 }}
                            </p>

                        </div>

                    </div>

                </div>


                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <CheckCircleIcon
                                class="h-6 w-6 text-emerald-600"
                            />

                        </div>


                        <div>

                            <p
                                class="text-xs font-bold uppercase tracking-wide text-slate-400"
                            >
                                Data Lengkap
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >

                                {{ summary?.total_lengkap ?? 0 }}

                                <span
                                    class="text-sm font-medium text-slate-400"
                                >
                                    /
                                    {{ summary?.total_siswa ?? 0 }}
                                </span>

                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =====================================================
                 TABLE
            ====================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-4 border-b border-slate-200 px-6 py-5 lg:flex-row lg:items-center lg:justify-between"
                >

                    <div>

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100"
                            >

                                <ClipboardDocumentCheckIcon
                                    class="h-5 w-5 text-blue-600"
                                />

                            </div>


                            <div>

                                <h2
                                    class="text-sm font-bold text-slate-800"
                                >
                                    HASIL PEMERIKSAAN SISWA
                                </h2>

                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    Rekap hasil pemeriksaan setiap siswa dalam periode
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        class="inline-flex w-fit items-center gap-2 rounded-lg bg-slate-50 px-3 py-2"
                    >

                        <UserGroupIcon
                            class="h-4 w-4 text-slate-400"
                        />

                        <span
                            class="text-xs font-semibold text-slate-600"
                        >
                            {{ siswa.length }} siswa
                        </span>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full min-w-[1400px]">

                        <thead>

                            <tr
                                class="border-b border-slate-100 bg-slate-50"
                            >

                                <th
                                    class="w-14 px-4 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    No
                                </th>

                                <th
                                    class="w-64 px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Siswa
                                </th>

                                <th
                                    class="w-36 px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kelas
                                </th>

                                <th
                                    class="w-28 px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kunjungan
                                </th>

                                <th
                                    class="w-64 px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 1
                                </th>

                                <th
                                    class="w-64 px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Berkala 2
                                </th>

                                <th
                                    class="w-96 px-5 py-4 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    TKSI
                                </th>

                                <th
                                    class="w-32 px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Status
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="(item, index) in siswa"
                                :key="item.id ?? index"
                                class="border-b border-slate-100 align-top transition hover:bg-slate-50/70"
                            >

                                <td class="px-4 py-5 text-center">

                                    <span
                                        class="text-sm font-semibold text-slate-500"
                                    >
                                        {{ index + 1 }}
                                    </span>

                                </td>


                                <td class="px-5 py-5">

                                    <p
                                        class="text-sm font-bold text-slate-700"
                                    >
                                        {{ item.nama || '-' }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        NISN: {{ item.nisn || '-' }}
                                    </p>

                                </td>


                                <td class="px-5 py-5">

                                    <span
                                        class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600"
                                    >
                                        {{ item.kelas || '-' }}
                                    </span>

                                    <p
                                        v-if="item.jurusan"
                                        class="mt-1 text-[11px] text-slate-400"
                                    >
                                        {{ item.jurusan }}
                                    </p>

                                </td>


                                <td class="px-5 py-5 text-center">

                                    <span
                                        class="text-lg font-bold text-slate-700"
                                    >
                                        {{ item.jumlah_kunjungan ?? 0 }}
                                    </span>

                                    <p
                                        class="text-[11px] text-slate-400"
                                    >
                                        kunjungan
                                    </p>

                                </td>


                                <td class="px-5 py-5">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold',
                                            statusClass(item.b1?.lengkap)
                                        ]"
                                    >
                                        {{ statusLabel(item.b1?.lengkap) }}
                                    </span>

                                    <div
                                        v-if="item.b1?.lengkap"
                                        class="mt-3"
                                    >

                                        <p
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            Kondisi Umum
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold text-slate-600"
                                        >
                                            {{ getKondisiUmum(item.b1) }}
                                        </p>

                                        <div
                                            v-if="item.b1?.tanggal"
                                            class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-400"
                                        >

                                            <CalendarDaysIcon
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ formatTanggal(item.b1.tanggal) }}

                                        </div>

                                    </div>

                                    <p
                                        v-else
                                        class="mt-3 text-xs italic text-slate-400"
                                    >
                                        Pemeriksaan Berkala 1 belum dilakukan.
                                    </p>

                                </td>


                                <td class="px-5 py-5">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold',
                                            statusClass(item.b2?.lengkap)
                                        ]"
                                    >
                                        {{ statusLabel(item.b2?.lengkap) }}
                                    </span>

                                    <div
                                        v-if="item.b2?.lengkap"
                                        class="mt-3"
                                    >

                                        <p
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            Kondisi Umum
                                        </p>

                                        <p
                                            class="mt-1 text-sm font-semibold text-slate-600"
                                        >
                                            {{ getKondisiUmum(item.b2) }}
                                        </p>

                                        <div
                                            v-if="item.b2?.tanggal"
                                            class="mt-2 flex items-center gap-1.5 text-[11px] text-slate-400"
                                        >

                                            <CalendarDaysIcon
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ formatTanggal(item.b2.tanggal) }}

                                        </div>

                                    </div>

                                    <p
                                        v-else
                                        class="mt-3 text-xs italic text-slate-400"
                                    >
                                        Pemeriksaan Berkala 2 belum dilakukan.
                                    </p>

                                </td>


                                <td class="px-5 py-5">

                                    <span
                                        :class="[
                                            'inline-flex rounded-full border px-2.5 py-1 text-[11px] font-bold',
                                            statusClass(item.tksi?.lengkap)
                                        ]"
                                    >
                                        {{ statusLabel(item.tksi?.lengkap) }}
                                    </span>

                                    <div
                                        v-if="item.tksi?.lengkap"
                                        class="mt-3"
                                    >

                                        <p
                                            class="text-xs font-bold text-slate-700"
                                        >
                                            Kategori Hasil Akhir
                                        </p>

                                        <div
                                            v-if="hasilAkhirTKSI(item.tksi)"
                                            class="mt-2"
                                        >

                                            <span
                                                :class="[
                                                    'inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-bold',
                                                    hasilAkhirClass(item.tksi)
                                                ]"
                                            >

                                                <span
                                                    :class="[
                                                        'h-2 w-2 rounded-full',
                                                        hasilAkhirDotClass(item.tksi)
                                                    ]"
                                                ></span>

                                                {{ getKategoriTksi(item.tksi) }}

                                            </span>


                                            <div
                                                class="mt-3 rounded-xl border border-slate-100 bg-slate-50 p-3"
                                            >

                                                <div
                                                    class="grid grid-cols-3 gap-3"
                                                >

                                                    <div>

                                                        <p
                                                            class="text-[10px] uppercase text-slate-400"
                                                        >
                                                            Total Skor
                                                        </p>

                                                        <p
                                                            class="mt-1 text-lg font-bold text-slate-700"
                                                        >
                                                            {{
                                                                hasilAkhirTKSI(
                                                                    item.tksi
                                                                )?.totalSkor ?? 0
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <p
                                                            class="text-[10px] uppercase text-slate-400"
                                                        >
                                                            Rata-rata
                                                        </p>

                                                        <p
                                                            class="mt-1 text-lg font-bold text-slate-700"
                                                        >
                                                            {{
                                                                Number(
                                                                    hasilAkhirTKSI(
                                                                        item.tksi
                                                                    )?.rataRata ?? 0
                                                                ).toFixed(2)
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div>

                                                        <p
                                                            class="text-[10px] uppercase text-slate-400"
                                                        >
                                                            Komponen
                                                        </p>

                                                        <p
                                                            class="mt-1 text-lg font-bold text-slate-700"
                                                        >
                                                            {{
                                                                hasilAkhirTKSI(
                                                                    item.tksi
                                                                )?.jumlahKomponen ?? 0
                                                            }}
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div
                                                v-if="
                                                    hasilAkhirTKSI(
                                                        item.tksi
                                                    )?.detail?.length
                                                "
                                                class="mt-3"
                                            >

                                                <p
                                                    class="mb-2 text-[10px] font-bold uppercase tracking-wide text-slate-400"
                                                >
                                                    Detail Skor Komponen
                                                </p>

                                                <div
                                                    class="space-y-1.5"
                                                >

                                                    <div
                                                        v-for="detail in hasilAkhirTKSI(item.tksi).detail"
                                                        :key="detail.nama"
                                                        class="flex items-center justify-between rounded-lg border border-slate-100 bg-white px-3 py-2"
                                                    >

                                                        <div>

                                                            <p
                                                                class="text-[11px] font-semibold text-slate-600"
                                                            >
                                                                {{ detail.nama }}
                                                            </p>

                                                            <p
                                                                v-if="detail.kategori"
                                                                class="text-[10px] text-slate-400"
                                                            >
                                                                {{ detail.kategori }}
                                                            </p>

                                                        </div>


                                                        <span
                                                            class="flex h-7 w-7 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-600"
                                                        >
                                                            {{ detail.skor }}
                                                        </span>

                                                    </div>

                                                </div>

                                            </div>

                                        </div>


                                        <p
                                            v-else
                                            class="mt-2 text-sm font-semibold text-red-400"
                                        >
                                            Belum ada hasil perhitungan komponen.
                                        </p>


                                        <div
                                            v-if="item.tksi?.tanggal"
                                            class="mt-3 flex items-center gap-1.5 text-[11px] text-slate-400"
                                        >

                                            <CalendarDaysIcon
                                                class="h-3.5 w-3.5"
                                            />

                                            {{ formatTanggal(item.tksi.tanggal) }}

                                        </div>

                                    </div>


                                    <p
                                        v-else
                                        class="mt-3 text-xs italic text-slate-400"
                                    >
                                        Tes TKSI belum dilakukan.
                                    </p>

                                </td>


                                <td class="px-5 py-5 text-center">

                                    <div
                                        v-if="
                                            item.status === 'Lengkap'
                                        "
                                        class="inline-flex flex-col items-center gap-1"
                                    >

                                        <CheckCircleIcon
                                            class="h-6 w-6 text-emerald-500"
                                        />

                                        <span
                                            class="text-xs font-bold text-emerald-600"
                                        >
                                            Lengkap
                                        </span>

                                    </div>


                                    <div
                                        v-else
                                        class="inline-flex flex-col items-center gap-1"
                                    >

                                        <XCircleIcon
                                            class="h-6 w-6 text-red-500"
                                        />

                                        <span
                                            class="text-xs font-bold text-red-600"
                                        >
                                            Belum Lengkap
                                        </span>

                                    </div>

                                </td>

                            </tr>


                            <tr
                                v-if="!siswa.length"
                            >

                                <td
                                    colspan="8"
                                    class="px-6 py-16 text-center"
                                >

                                    <div
                                        class="flex flex-col items-center"
                                    >

                                        <div
                                            class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                        >

                                            <UserGroupIcon
                                                class="h-6 w-6 text-slate-300"
                                            />

                                        </div>


                                        <p
                                            class="mt-3 text-sm font-semibold text-slate-600"
                                        >
                                            Belum ada siswa
                                        </p>


                                        <p
                                            class="mt-1 text-xs text-slate-400"
                                        >
                                            Belum ada siswa yang terdaftar pada periode ini.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =====================================================
                 KETERANGAN
            ====================================================== -->

            <div
                class="rounded-2xl border border-blue-100 bg-blue-50/50 p-5"
            >

                <div class="flex gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-100"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5 text-blue-600"
                        />

                    </div>


                    <div>

                        <h3
                            class="text-xs font-bold text-slate-700"
                        >
                            Keterangan Laporan
                        </h3>


                        <p
                            class="mt-1 text-xs leading-5 text-slate-500"
                        >

                            Laporan menampilkan rekap pemeriksaan kesehatan
                            setiap siswa berdasarkan periode

                            <span
                                class="font-semibold text-slate-700"
                            >
                                {{ periode.nama_periode || '-' }}
                            </span>.

                            Pemeriksaan Berkala 1 dan Berkala 2 ditampilkan
                            berdasarkan indikator
                            <b>kondisi umum</b>.

                            TKSI dihitung berdasarkan kategori setiap komponen
                            dengan bobot:

                            <b>
                                Baik Sekali = 5,
                                Baik = 4,
                                Sedang = 3,
                                Kurang = 2,
                                Kurang Sekali = 1
                            </b>.

                            Khusus <b>Beep Test</b>, skor ditentukan berdasarkan
                            norma Level dan Balikan sesuai jenis kelamin.

                            Seluruh skor komponen kemudian dijumlahkan dan
                            dibagi dengan jumlah komponen yang memiliki hasil.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </AdminLayout>

</template>