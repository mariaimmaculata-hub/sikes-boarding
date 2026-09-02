<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import { computed, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    UserIcon,
    AcademicCapIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    HeartIcon,
    UserCircleIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },

    periode: {
        type: Object,
        default: null,
    },
})


// ======================================================
// FORMAT DATE
// ======================================================

const formatDate = (date) => {
    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return date
    }

    return parsed.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}


// ======================================================
// FORMAT DATETIME
// ======================================================

const formatDateTime = (date) => {
    if (!date) {
        return '-'
    }

    const parsed = new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return date
    }

    return parsed.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}


// ======================================================
// FORMAT STATUS
// ======================================================

const formatStatus = (status) => {
    if (!status) {
        return '-'
    }

    return String(status)
        .replaceAll('_', ' ')
        .replace(/\b\w/g, char => char.toUpperCase())
}


// ======================================================
// JENIS KELAMIN
// ======================================================

const formatJenisKelamin = (jenisKelamin) => {
    if (jenisKelamin === 'L') {
        return 'Laki-laki'
    }

    if (jenisKelamin === 'P') {
        return 'Perempuan'
    }

    return '-'
}


// ======================================================
// STATUS PEMERIKSAAN
// ======================================================

const pemeriksaanStatusClass = (status) => {
    switch (status) {
        case 'selesai':
            return 'bg-emerald-100 text-emerald-700'

        case 'belum':
        case 'belum_selesai':
            return 'bg-amber-100 text-amber-700'

        case 'tidak_hadir':
            return 'bg-rose-100 text-rose-700'

        default:
            return 'bg-slate-100 text-slate-600'
    }
}


// ======================================================
// PEMERIKSAAN BERKALA - SEMUA PERIODE
// ======================================================

const semuaPemeriksaanBerkala = computed(() => {
    const data =
        props.siswa.pemeriksaan_berkala ??
        props.siswa.pemeriksaanBerkala ??
        []

    return [...data].sort((a, b) => {
        const aDate = new Date(a.tanggal_pemeriksaan ?? 0).getTime()
        const bDate = new Date(b.tanggal_pemeriksaan ?? 0).getTime()

        return aDate - bDate
    })
})


// ======================================================
// PEMERIKSAAN PERIODE AKTIF
// ======================================================

const pemeriksaanPeriodeAktif = computed(() => {
    if (!props.periode?.id) {
        return semuaPemeriksaanBerkala.value
    }

    return semuaPemeriksaanBerkala.value.filter(
        item => String(item.periode_id) === String(props.periode.id)
    )
})

const pemeriksaan = () => pemeriksaanPeriodeAktif.value

const pemeriksaan1 = () => {
    return pemeriksaan().find(
        item =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_1'
    ) ?? null
}

const pemeriksaan2 = () => {
    return pemeriksaan().find(
        item =>
            String(item.jenis_pemeriksaan ?? '')
                .toLowerCase() === 'berkala_2'
    ) ?? null
}


// ======================================================
// RIWAYAT BERKALA SEMUA PERIODE
// HANYA MENAMPILKAN PERIODE YANG SUDAH MEMILIKI DATA
// ======================================================

const riwayatBerkalaSemuaPeriode = computed(() => {
    const mapPeriode = new Map()

    semuaPemeriksaanBerkala.value.forEach((item) => {
        const periodeId =
            item.periode_id ??
            item.periode?.id ??
            'tanpa-periode'

        if (!mapPeriode.has(periodeId)) {
            mapPeriode.set(periodeId, {
                id: periodeId,
                nama:
                    item.periode?.nama_periode ??
                    item.nama_periode ??
                    'Periode',
                tanggal_mulai:
                    item.periode?.tanggal_mulai ??
                    null,
                tanggal_selesai:
                    item.periode?.tanggal_selesai ??
                    null,
                berkala1: null,
                berkala2: null,
            })
        }

        const periodeItem = mapPeriode.get(periodeId)
        const jenis = String(item.jenis_pemeriksaan ?? '').toLowerCase()

        if (jenis === 'berkala_1') {
            periodeItem.berkala1 = item
        }

        if (jenis === 'berkala_2') {
            periodeItem.berkala2 = item
        }
    })

    return Array.from(mapPeriode.values()).sort((a, b) => {
        const aDate = new Date(a.tanggal_mulai ?? 0).getTime()
        const bDate = new Date(b.tanggal_mulai ?? 0).getTime()

        return aDate - bDate
    })
})

const jumlahRiwayatBerkala = computed(() => {
    return semuaPemeriksaanBerkala.value.length
})

const tampilkanKondisiUmum = (item) => {
    if (!item) return '-'

    return (
        item.kondisi_umum ??
        item.kondisi ??
        item.status_kesehatan ??
        '-'
    )
}

const tampilkanHasilPemeriksaan = (item) => {
    if (!item) return []

    const hasil = []

    const tambahData = (label, value, satuan = '') => {
        if (
            value !== null &&
            value !== undefined &&
            String(value).trim() !== '' &&
            String(value).trim() !== '-'
        ) {
            hasil.push({
                label,
                value: `${value}${satuan}`,
            })
        }
    }

    // ANTROPOMETRI
    tambahData('Berat Badan', item.berat_badan, ' kg')
    tambahData('Tinggi Badan', item.tinggi_badan, ' cm')
    tambahData('IMT', item.imt)

    // PEMERIKSAAN FISIK
    tambahData('Mata', item.mata)
    tambahData('Telinga', item.telinga)
    tambahData('Gigi & Mulut', item.gigi_mulut)

    // TANDA VITAL
    tambahData('Tekanan Darah', item.tekanan_darah, ' mmHg')
    tambahData('Denyut Nadi', item.denyut_nadi, ' x/menit')
    tambahData('Suhu Tubuh', item.suhu_tubuh, ' °C')
    tambahData('Saturasi Oksigen', item.saturasi_oksigen, '%')

    // KEBERSIHAN
    tambahData('Kebersihan Rambut', item.kebersihan_rambut)
    tambahData('Kebersihan Wajah', item.kebersihan_wajah)
    tambahData('Kebersihan Telinga', item.kebersihan_telinga)
    tambahData('Kebersihan Hidung', item.kebersihan_hidung)
    tambahData('Kebersihan Mulut & Gigi', item.kebersihan_mulut_gigi)
    tambahData('Kebersihan Tangan & Kuku', item.kebersihan_tangan_kuku)
    tambahData('Kebersihan Kulit & Badan', item.kebersihan_kulit_badan)
    tambahData('Kebersihan Kaki & Kuku', item.kebersihan_kaki_kuku)

    // HASIL PEMERIKSAAN
    tambahData('Keluhan', item.keluhan)
    tambahData('Hasil Pemeriksaan', item.hasil_pemeriksaan)
    tambahData('Rekomendasi', item.rekomendasi)
    tambahData('Catatan', item.catatan)

    return hasil
}

const tampilkanTanggalPemeriksaan = (item) => {
    if (!item) return '-'

    return formatDate(item.tanggal_pemeriksaan)
}


// ======================================================
// KUNJUNGAN KLINIK
// ======================================================

const kunjunganKlinik = () => {
    return (
        props.siswa.kunjungan_klinik ??
        props.siswa.kunjunganKlinik ??
        []
    )
}


// ======================================================
// TANGGAL KUNJUNGAN
// ======================================================

const tanggalKunjungan = (kunjungan) => {
    return (
        kunjungan.tanggal_kunjungan ??
        kunjungan.created_at ??
        kunjungan.tanggal ??
        null
    )
}


// ======================================================
// OBAT KUNJUNGAN
// ======================================================

const obatKunjungan = (kunjungan) => {
    return (
        kunjungan.obat ??
        kunjungan.kunjungan_obat ??
        []
    )
}


// ======================================================
// JUMLAH KUNJUNGAN
// ======================================================

const jumlahKunjungan = () => {
    return kunjunganKlinik().length
}


// ======================================================
// INITIAL NAMA SISWA
// ======================================================

const initial = props.siswa.nama
    ? props.siswa.nama.charAt(0).toUpperCase()
    : '?'



// ======================================================
// MODAL DETAIL PEMERIKSAAN BERKALA
// ======================================================

const detailPemeriksaan = ref(null)

const bukaDetailPemeriksaan = (item) => {
    if (!item) return
    detailPemeriksaan.value = item
}

const tutupDetailPemeriksaan = () => {
    detailPemeriksaan.value = null
}

const kelompokDetailPemeriksaan = computed(() => {
    const item = detailPemeriksaan.value
    if (!item) return []

    const isValid = (value) =>
        value !== null &&
        value !== undefined &&
        String(value).trim() !== '' &&
        String(value).trim() !== '-'

    const buatKelompok = (judul, data) => ({
        judul,
        data: data.filter(([label, value]) => isValid(value)),
    })

    return [
        buatKelompok('Antropometri', [
            ['Berat Badan', item.berat_badan ? `${item.berat_badan} kg` : null],
            ['Tinggi Badan', item.tinggi_badan ? `${item.tinggi_badan} cm` : null],
            ['IMT', item.imt],
        ]),
        buatKelompok('Pemeriksaan Fisik', [
            ['Mata', item.mata],
            ['Telinga', item.telinga],
            ['Gigi & Mulut', item.gigi_mulut],
        ]),
        buatKelompok('Tanda Vital', [
            ['Tekanan Darah', item.tekanan_darah ? `${item.tekanan_darah} mmHg` : null],
            ['Denyut Nadi', item.denyut_nadi ? `${item.denyut_nadi} x/menit` : null],
            ['Suhu Tubuh', item.suhu_tubuh ? `${item.suhu_tubuh} °C` : null],
            ['Saturasi Oksigen', item.saturasi_oksigen ? `${item.saturasi_oksigen}%` : null],
        ]),
        buatKelompok('Kebersihan', [
            ['Kebersihan Rambut', item.kebersihan_rambut],
            ['Kebersihan Wajah', item.kebersihan_wajah],
            ['Kebersihan Telinga', item.kebersihan_telinga],
            ['Kebersihan Hidung', item.kebersihan_hidung],
            ['Kebersihan Mulut & Gigi', item.kebersihan_mulut_gigi],
            ['Kebersihan Tangan & Kuku', item.kebersihan_tangan_kuku],
            ['Kebersihan Kulit & Badan', item.kebersihan_kulit_badan],
            ['Kebersihan Kaki & Kuku', item.kebersihan_kaki_kuku],
        ]),
        buatKelompok('Hasil Pemeriksaan', [
            ['Keluhan', item.keluhan],
            ['Hasil Pemeriksaan', item.hasil_pemeriksaan],
            ['Rekomendasi', item.rekomendasi],
            ['Catatan', item.catatan],
        ]),
    ].filter(group => group.data.length)
})

const statusKondisiClass = (item) => {
    const value = String(tampilkanKondisiUmum(item) || '').toLowerCase()

    if (value.includes('sehat') && !value.includes('kurang')) {
        return 'bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-200'
    }

    if (value.includes('kurang') || value.includes('perlu')) {
        return 'bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-200'
    }

    return 'bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-200'
}

const ringkasHasil = (item) => {
    if (!item) return '-'
    return item.hasil_pemeriksaan || item.kondisi_umum || item.kondisi || '-'
}

</script>


<template>
<KlinikLayout>
    <div class="space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Link
                    :href="route('klinik.siswa.index')"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-pink-100 bg-white text-slate-500 shadow-sm transition hover:bg-pink-50 hover:text-pink-600"
                >
                    <ArrowLeftIcon class="h-5 w-5" />
                </Link>

                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Detail Siswa</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Informasi lengkap dan riwayat kesehatan siswa.
                    </p>
                </div>
            </div>

            <div
                v-if="periode"
                class="rounded-xl border border-pink-100 bg-pink-50 px-4 py-2.5"
            >
                <p class="text-[10px] font-bold uppercase tracking-wider text-pink-500">
                    Periode Aktif
                </p>
                <p class="mt-0.5 text-sm font-bold text-pink-800">
                    {{ periode.nama_periode }}
                </p>
            </div>
        </div>

        <!-- PROFILE SISWA -->
        <div class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm">
            <div class="border-b border-pink-100 bg-gradient-to-r from-pink-50 to-rose-50 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-pink-100 text-lg font-bold text-pink-700 ring-4 ring-white">
                        {{ initial }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">{{ siswa.nama || '-' }}</h2>
                        <p class="mt-0.5 text-xs text-slate-400">
                            NISN: {{ siswa.nisn || '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-5 flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-pink-50">
                        <UserIcon class="h-5 w-5 text-pink-600" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Data Pribadi</h3>
                        <p class="text-xs text-slate-400">Informasi identitas siswa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <p class="text-xs font-medium text-slate-400">NISN</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ siswa.nisn || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Nama Lengkap</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ siswa.nama || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">NIK</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ siswa.nik || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Tempat Lahir</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ siswa.tempat_lahir || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Tanggal Lahir</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ formatDate(siswa.tanggal_lahir) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Jenis Kelamin</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ formatJenisKelamin(siswa.jenis_kelamin) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Nomor HP</p>
                        <p class="mt-1 text-sm font-semibold text-slate-700">{{ siswa.no_hp || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Email</p>
                        <p class="mt-1 break-all text-sm font-semibold text-slate-700">{{ siswa.email || '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-400">Status Siswa</p>
                        <div class="mt-1">
                            <span
                                :class="[
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',
                                    siswa.status === 'aktif'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-slate-100 text-slate-500'
                                ]"
                            >
                                {{ siswa.status === 'aktif' ? 'Aktif' : formatStatus(siswa.status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AKADEMIK -->
        <div class="rounded-2xl border border-pink-100 bg-white shadow-sm">
            <div class="border-b border-pink-100 px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-pink-50">
                        <AcademicCapIcon class="h-5 w-5 text-pink-600" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Informasi Akademik</h2>
                        <p class="mt-0.5 text-xs text-slate-400">Data kelas dan angkatan siswa</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-xl border border-pink-100 bg-pink-50/50 p-4">
                    <p class="text-xs font-medium text-slate-400">Kelas</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">{{ siswa.kelas?.nama_kelas || '-' }}</p>
                </div>
                <div class="rounded-xl border border-pink-100 bg-pink-50/50 p-4">
                    <p class="text-xs font-medium text-slate-400">Tingkat</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">{{ siswa.kelas?.tingkat || '-' }}</p>
                </div>
                <div class="rounded-xl border border-pink-100 bg-pink-50/50 p-4">
                    <p class="text-xs font-medium text-slate-400">Jurusan</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">{{ siswa.kelas?.jurusan?.nama_jurusan || '-' }}</p>
                </div>
                <div class="rounded-xl border border-pink-100 bg-pink-50/50 p-4">
                    <p class="text-xs font-medium text-slate-400">Angkatan</p>
                    <p class="mt-1 text-sm font-bold text-slate-800">{{ siswa.angkatan || '-' }}</p>
                </div>
            </div>
        </div>

        <!-- PEMERIKSAAN BERKALA RINGKAS -->
        <div class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm">
            <div class="border-b border-pink-100 px-5 py-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                            <ClipboardDocumentCheckIcon class="h-5 w-5 text-pink-600" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Pemeriksaan Berkala</h2>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Ringkasan pemeriksaan dari seluruh periode yang memiliki data
                            </p>
                        </div>
                    </div>

                    <span class="w-fit rounded-full bg-pink-50 px-3 py-1.5 text-xs font-bold text-pink-700">
                        {{ jumlahRiwayatBerkala }} Pemeriksaan
                    </span>
                </div>
            </div>

            <div
                v-if="!riwayatBerkalaSemuaPeriode.length"
                class="px-5 py-16 text-center"
            >
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pink-50">
                    <ClipboardDocumentCheckIcon class="h-7 w-7 text-pink-300" />
                </div>
                <h3 class="mt-4 text-sm font-bold text-slate-700">
                    Belum ada riwayat pemeriksaan berkala
                </h3>
                <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-slate-400">
                    Riwayat pemeriksaan akan muncul setelah terdapat data pemeriksaan.
                </p>
            </div>

            <div v-else class="overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-0">
                    <thead>
                        <tr class="border-b border-pink-100 bg-pink-50">
                            <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">
                                Data
                            </th>

                            <th
                                v-for="periodeItem in riwayatBerkalaSemuaPeriode"
                                :key="periodeItem.id"
                                colspan="2"
                                class="border-l border-pink-100 px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-pink-600"
                            >
                                {{ periodeItem.nama }}
                            </th>
                        </tr>

                        <tr class="bg-rose-50/70">
                            <th class="px-5 py-2"></th>

                            <template
                                v-for="periodeItem in riwayatBerkalaSemuaPeriode"
                                :key="`header-${periodeItem.id}`"
                            >
                                <th class="border-l border-pink-100 px-4 py-2 text-center text-[10px] font-bold uppercase tracking-wider text-rose-600">
                                    Berkala 1
                                </th>
                                <th class="border-l border-pink-100 px-4 py-2 text-center text-[10px] font-bold uppercase tracking-wider text-pink-600">
                                    Berkala 2
                                </th>
                            </template>
                        </tr>
                    </thead>

                    <tbody>
                        <!-- KONDISI UMUM -->
                        <tr class="border-t border-slate-100">
                            <th class="whitespace-nowrap bg-white px-5 py-4 text-left text-xs font-bold text-slate-700">
                                Kondisi Umum
                            </th>

                            <template
                                v-for="periodeItem in riwayatBerkalaSemuaPeriode"
                                :key="`kondisi-${periodeItem.id}`"
                            >
                                <td class="border-l border-slate-100 px-4 py-4 align-top">
                                    <span
                                        v-if="periodeItem.berkala1"
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold',
                                            statusKondisiClass(periodeItem.berkala1)
                                        ]"
                                    >
                                        {{ tampilkanKondisiUmum(periodeItem.berkala1) }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400">-</span>
                                </td>

                                <td class="border-l border-slate-100 px-4 py-4 align-top">
                                    <span
                                        v-if="periodeItem.berkala2"
                                        :class="[
                                            'inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold',
                                            statusKondisiClass(periodeItem.berkala2)
                                        ]"
                                    >
                                        {{ tampilkanKondisiUmum(periodeItem.berkala2) }}
                                    </span>
                                    <span v-else class="text-xs text-slate-400">-</span>
                                </td>
                            </template>
                        </tr>

                        <!-- HASIL PEMERIKSAAN -->
                        <tr class="border-t border-slate-100">
                            <th class="bg-white px-5 py-4 text-left text-xs font-bold text-slate-700">
                                Hasil Pemeriksaan
                            </th>

                            <template
                                v-for="periodeItem in riwayatBerkalaSemuaPeriode"
                                :key="`hasil-${periodeItem.id}`"
                            >
                                <td class="border-l border-slate-100 px-4 py-4 align-top">
                                    <p
                                        v-if="periodeItem.berkala1"
                                        class="line-clamp-2 max-w-[220px] text-xs leading-relaxed text-slate-600"
                                        :title="ringkasHasil(periodeItem.berkala1)"
                                    >
                                        {{ ringkasHasil(periodeItem.berkala1) }}
                                    </p>
                                    <span v-else class="text-xs text-slate-400">-</span>

                                    <button
                                        v-if="periodeItem.berkala1"
                                        type="button"
                                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-pink-200 bg-white px-3 py-1.5 text-[11px] font-bold text-pink-600 shadow-sm transition hover:bg-pink-50 hover:text-pink-700"
                                        @click="bukaDetailPemeriksaan(periodeItem.berkala1)"
                                    >
                                        Lihat Detail
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </td>

                                <td class="border-l border-slate-100 px-4 py-4 align-top">
                                    <p
                                        v-if="periodeItem.berkala2"
                                        class="line-clamp-2 max-w-[220px] text-xs leading-relaxed text-slate-600"
                                        :title="ringkasHasil(periodeItem.berkala2)"
                                    >
                                        {{ ringkasHasil(periodeItem.berkala2) }}
                                    </p>
                                    <span v-else class="text-xs text-slate-400">-</span>

                                    <button
                                        v-if="periodeItem.berkala2"
                                        type="button"
                                        class="mt-3 inline-flex items-center gap-1.5 rounded-lg border border-pink-200 bg-white px-3 py-1.5 text-[11px] font-bold text-pink-600 shadow-sm transition hover:bg-pink-50 hover:text-pink-700"
                                        @click="bukaDetailPemeriksaan(periodeItem.berkala2)"
                                    >
                                        Lihat Detail
                                        <span aria-hidden="true">→</span>
                                    </button>
                                </td>
                            </template>
                        </tr>

                        <!-- TANGGAL -->
                        <tr class="border-t border-slate-100">
                            <th class="bg-white px-5 py-4 text-left text-xs font-bold text-slate-700">
                                Tanggal
                            </th>

                            <template
                                v-for="periodeItem in riwayatBerkalaSemuaPeriode"
                                :key="`tanggal-${periodeItem.id}`"
                            >
                                <td class="border-l border-slate-100 px-4 py-4 text-xs text-slate-600">
                                    <div v-if="periodeItem.berkala1" class="flex items-center gap-2">
                                        <CalendarDaysIcon class="h-4 w-4 shrink-0 text-pink-500" />
                                        {{ tampilkanTanggalPemeriksaan(periodeItem.berkala1) }}
                                    </div>
                                    <span v-else>-</span>
                                </td>

                                <td class="border-l border-slate-100 px-4 py-4 text-xs text-slate-600">
                                    <div v-if="periodeItem.berkala2" class="flex items-center gap-2">
                                        <CalendarDaysIcon class="h-4 w-4 shrink-0 text-pink-500" />
                                        {{ tampilkanTanggalPemeriksaan(periodeItem.berkala2) }}
                                    </div>
                                    <span v-else>-</span>
                                </td>
                            </template>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="riwayatBerkalaSemuaPeriode.length"
                class="flex flex-wrap items-center justify-between gap-2 border-t border-pink-100 bg-pink-50/40 px-5 py-3"
            >
                <p class="text-[10px] text-slate-400">
                    Detail BB, TB, IMT, tanda vital, kebersihan, keluhan, rekomendasi, dan catatan tersedia melalui tombol Lihat Detail.
                </p>
                <p class="text-[10px] text-slate-400">
                    Geser tabel ke kanan/kiri untuk melihat seluruh periode.
                </p>
            </div>
        </div>

        <!-- RIWAYAT KUNJUNGAN KLINIK -->
        <div class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm">
            <div class="border-b border-pink-100 px-5 py-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50">
                            <HeartIcon class="h-5 w-5 text-pink-600" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800">Riwayat Kunjungan Klinik</h2>
                            <p class="mt-0.5 text-xs text-slate-400">
                                Riwayat pelayanan kesehatan siswa
                            </p>
                        </div>
                    </div>

                    <span class="w-fit rounded-full bg-pink-50 px-3 py-1.5 text-xs font-bold text-pink-700">
                        {{ jumlahKunjungan() }} Kunjungan
                    </span>
                </div>
            </div>

            <div v-if="!kunjunganKlinik().length" class="px-5 py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pink-50">
                    <HeartIcon class="h-7 w-7 text-pink-300" />
                </div>
                <h3 class="mt-4 text-sm font-bold text-slate-700">Belum ada kunjungan klinik</h3>
                <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-slate-400">
                    Belum terdapat riwayat kunjungan klinik untuk siswa ini pada periode yang ditampilkan.
                </p>
            </div>

            <div v-else class="max-h-[600px] overflow-auto">
                <table class="min-w-[1450px] w-full">
                    <thead class="sticky top-0 z-20 bg-pink-50">
                        <tr class="border-b border-pink-100">
                            <th class="sticky left-0 z-30 w-12 bg-pink-50 px-4 py-3 text-center text-[10px] font-bold uppercase tracking-wider text-pink-500">No</th>
                            <th class="min-w-[150px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Tanggal</th>
                            <th class="min-w-[150px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Periode</th>
                            <th class="min-w-[190px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Keluhan</th>
                            <th class="min-w-[190px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Pemeriksaan</th>
                            <th class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Diagnosis</th>
                            <th class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Tindakan</th>
                            <th class="min-w-[200px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Catatan</th>
                            <th class="min-w-[180px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Obat</th>
                            <th class="min-w-[160px] px-4 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-pink-500">Pemeriksa</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        <tr
                            v-for="(kunjungan, index) in kunjunganKlinik()"
                            :key="kunjungan.id"
                            class="transition hover:bg-pink-50/40"
                        >
                            <td class="sticky left-0 z-10 bg-white px-4 py-3 text-center">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-pink-50 text-xs font-bold text-pink-700">
                                    {{ index + 1 }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="flex items-start gap-2">
                                    <CalendarDaysIcon class="mt-0.5 h-4 w-4 shrink-0 text-pink-500" />
                                    <div>
                                        <p class="text-xs font-semibold text-slate-700">
                                            {{ formatDate(tanggalKunjungan(kunjungan)) }}
                                        </p>
                                        <p class="mt-0.5 text-[10px] text-slate-400">
                                            {{ formatDateTime(tanggalKunjungan(kunjungan)).split(' pukul ')[1] || '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <span class="inline-flex rounded-lg bg-pink-50 px-2.5 py-1.5 text-[11px] font-semibold text-pink-700">
                                    {{ kunjungan.periode?.nama_periode || '-' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <p class="line-clamp-2 text-xs leading-relaxed text-slate-700" :title="kunjungan.keluhan || '-'">
                                    {{ kunjungan.keluhan || '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <p class="line-clamp-2 text-xs leading-relaxed text-slate-700" :title="kunjungan.pemeriksaan || '-'">
                                    {{ kunjungan.pemeriksaan || '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <p class="line-clamp-2 text-xs font-semibold leading-relaxed text-slate-700" :title="kunjungan.diagnosis || '-'">
                                    {{ kunjungan.diagnosis || '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <p class="line-clamp-2 text-xs leading-relaxed text-slate-700" :title="kunjungan.tindakan || '-'">
                                    {{ kunjungan.tindakan || '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <p class="line-clamp-2 text-xs leading-relaxed text-slate-600" :title="kunjungan.catatan || '-'">
                                    {{ kunjungan.catatan || '-' }}
                                </p>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div v-if="obatKunjungan(kunjungan).length" class="space-y-1.5">
                                    <div
                                        v-for="obat in obatKunjungan(kunjungan)"
                                        :key="obat.id"
                                        class="flex items-center justify-between gap-2"
                                    >
                                        <span class="text-xs font-semibold text-slate-700">
                                            {{ obat.nama_obat || obat.obat?.nama_obat || '-' }}
                                        </span>
                                        <span class="shrink-0 rounded-md bg-pink-50 px-1.5 py-0.5 text-[10px] font-bold text-pink-700">
                                            {{ obat.jumlah || 0 }}
                                            {{ obat.satuan || obat.obat?.satuan || '' }}
                                        </span>
                                    </div>
                                </div>
                                <span v-else class="text-xs text-slate-400">-</span>
                            </td>

                            <td class="px-4 py-3 align-top">
                                <div class="flex items-center gap-2">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-pink-50">
                                        <UserCircleIcon class="h-4 w-4 text-pink-600" />
                                    </div>
                                    <p class="text-xs font-semibold text-slate-700">
                                        {{ kunjungan.pemeriksa?.name || kunjungan.pemeriksa?.nama || '-' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                v-if="kunjunganKlinik().length"
                class="flex flex-wrap items-center justify-between gap-2 border-t border-pink-100 bg-pink-50/40 px-5 py-3"
            >
                <p class="text-[10px] text-slate-400">
                    Menampilkan {{ jumlahKunjungan() }} riwayat kunjungan
                </p>
                <p class="text-[10px] text-slate-400">
                    Geser tabel ke samping atau ke bawah untuk melihat data lainnya
                </p>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="pb-4 text-center text-xs text-slate-400">
            Data kesehatan siswa bersifat terbatas dan digunakan
            untuk keperluan pelayanan kesehatan sekolah.
        </div>
    </div>

    <!-- ==================================================
         MODAL DETAIL PEMERIKSAAN
    ================================================== -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="detailPemeriksaan"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
                @click.self="tutupDetailPemeriksaan"
            >
                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-y-3 scale-95 opacity-0"
                    enter-to-class="translate-y-0 scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="translate-y-0 scale-100 opacity-100"
                    leave-to-class="translate-y-3 scale-95 opacity-0"
                >
                    <div
                        class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                    >
                        <!-- MODAL HEADER -->
                        <div class="flex items-center justify-between border-b border-pink-100 bg-gradient-to-r from-pink-50 to-rose-50 px-5 py-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-pink-600 shadow-sm">
                                    <ClipboardDocumentCheckIcon class="h-5 w-5" />
                                </div>

                                <div class="min-w-0">
                                    <h2 class="text-base font-bold text-slate-800">
                                        Detail Pemeriksaan Berkala
                                    </h2>
                                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                        <span class="font-semibold text-pink-700">
                                            {{ formatStatus(detailPemeriksaan.jenis_pemeriksaan) }}
                                        </span>
                                        <span>•</span>
                                        <span>
                                            {{ formatDate(detailPemeriksaan.tanggal_pemeriksaan) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="ml-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-slate-400 transition hover:bg-white hover:text-slate-700"
                                aria-label="Tutup"
                                @click="tutupDetailPemeriksaan"
                            >
                                <span class="text-2xl leading-none">×</span>
                            </button>
                        </div>

                        <!-- MODAL BODY -->
                        <div class="overflow-y-auto p-5">
                            <!-- RINGKASAN -->
                            <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div class="rounded-xl border border-pink-100 bg-pink-50/50 p-4">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-pink-500">
                                        Kondisi Umum
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ tampilkanKondisiUmum(detailPemeriksaan) }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Status Pemeriksaan
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ formatStatus(detailPemeriksaan.status) }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Pemeriksa
                                    </p>
                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ detailPemeriksaan.pemeriksa?.name || detailPemeriksaan.pemeriksa?.nama || '-' }}
                                    </p>
                                </div>
                            </div>

                            <!-- DETAIL GROUP -->
                            <div class="space-y-5">
                                <section
                                    v-for="group in kelompokDetailPemeriksaan"
                                    :key="group.judul"
                                    class="overflow-hidden rounded-xl border border-slate-200"
                                >
                                    <div class="border-b border-slate-100 bg-slate-50 px-4 py-3">
                                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">
                                            {{ group.judul }}
                                        </h3>
                                    </div>

                                    <div class="grid grid-cols-1 gap-px bg-slate-100 sm:grid-cols-2 lg:grid-cols-3">
                                        <div
                                            v-for="[label, value] in group.data"
                                            :key="label"
                                            class="bg-white px-4 py-3"
                                        >
                                            <p class="text-[10px] font-medium text-slate-400">
                                                {{ label }}
                                            </p>
                                            <p class="mt-1 break-words text-sm font-semibold leading-relaxed text-slate-700">
                                                {{ value }}
                                            </p>
                                        </div>
                                    </div>
                                </section>

                                <div
                                    v-if="!kelompokDetailPemeriksaan.length"
                                    class="rounded-xl border border-dashed border-slate-200 p-8 text-center"
                                >
                                    <p class="text-sm font-semibold text-slate-500">
                                        Belum ada detail pemeriksaan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- MODAL FOOTER -->
                        <div class="flex items-center justify-end border-t border-slate-100 bg-slate-50 px-5 py-3">
                            <button
                                type="button"
                                class="rounded-xl bg-pink-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-pink-700"
                                @click="tutupDetailPemeriksaan"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</KlinikLayout>
</template>
