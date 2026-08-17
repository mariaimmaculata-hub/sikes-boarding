<script setup>
import { computed, ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    ClipboardDocumentCheckIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    ArrowRightIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    XMarkIcon,
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

    statistik: {
        type: Object,
        default: () => ({
            total: 0,
            lengkap: 0,
            belum_lengkap: 0,
        }),
    },

    komponen: {
        type: Array,
        default: () => [],
    },

    flash: {
        type: Object,
        default: () => ({}),
    },
})

const formatTanggal = (tanggal) => {
    if (!tanggal) return '-'

    const date = new Date(tanggal)

    if (isNaN(date.getTime())) return '-'

    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date)
}

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

const statusFilter = ref('semua')
const tingkatFilter = ref('semua')
const jurusanFilter = ref('semua')
const kelasFilter = ref('semua')
const search = ref('')

/*
|--------------------------------------------------------------------------
| DAFTAR TINGKAT
|--------------------------------------------------------------------------
*/

const daftarTingkat = computed(() => {
    const tingkat = props.siswa
        .map(student => student.kelas?.tingkat)
        .filter(Boolean)

    return [...new Set(tingkat)].sort((a, b) => {
        const urutan = {
            X: 1,
            XI: 2,
            XII: 3,
        }

        return (urutan[a] ?? 99) - (urutan[b] ?? 99)
    })
})

/*
|--------------------------------------------------------------------------
| DAFTAR JURUSAN
|--------------------------------------------------------------------------
*/

const daftarJurusan = computed(() => {
    const jurusan = props.siswa
        .map(student => student.kelas?.jurusan)
        .filter(Boolean)

    const unique = new Map()

    jurusan.forEach(item => {
        if (!unique.has(item.id)) {
            unique.set(item.id, item)
        }
    })

    return Array.from(unique.values()).sort((a, b) =>
        (a.nama_jurusan || '').localeCompare(
            b.nama_jurusan || '',
            'id'
        )
    )
})

/*
|--------------------------------------------------------------------------
| DAFTAR KELAS
|--------------------------------------------------------------------------
*/

const daftarKelas = computed(() => {
    const kelas = props.siswa
        .map(student => student.kelas)
        .filter(Boolean)

    const unique = new Map()

    kelas.forEach(item => {
        if (!unique.has(item.id)) {
            unique.set(item.id, item)
        }
    })

    return Array.from(unique.values()).sort((a, b) =>
        (a.nama_kelas || '').localeCompare(
            b.nama_kelas || '',
            'id'
        )
    )
})

/*
|--------------------------------------------------------------------------
| FILTER KELAS BERDASARKAN TINGKAT + JURUSAN
|--------------------------------------------------------------------------
*/

const daftarKelasTersedia = computed(() => {
    return daftarKelas.value.filter(kelas => {
        const sesuaiTingkat =
            tingkatFilter.value === 'semua' ||
            kelas.tingkat === tingkatFilter.value

        const sesuaiJurusan =
            jurusanFilter.value === 'semua' ||
            kelas.jurusan?.id == jurusanFilter.value

        return sesuaiTingkat && sesuaiJurusan
    })
})

/*
|--------------------------------------------------------------------------
| SISWA HASIL FILTER
|--------------------------------------------------------------------------
*/

const siswaTampil = computed(() => {
    const keyword = search.value
        .trim()
        .toLowerCase()

    return props.siswa.filter(student => {
        /*
        | Status
        */

        const sesuaiStatus =
            statusFilter.value === 'semua' ||
            (
                statusFilter.value === 'lengkap' &&
                student.lengkap
            ) ||
            (
                statusFilter.value === 'belum' &&
                !student.lengkap
            )

        if (!sesuaiStatus) {
            return false
        }

        /*
        | Tingkat
        */

        const sesuaiTingkat =
            tingkatFilter.value === 'semua' ||
            student.kelas?.tingkat === tingkatFilter.value

        if (!sesuaiTingkat) {
            return false
        }

        /*
        | Jurusan
        */

        const sesuaiJurusan =
            jurusanFilter.value === 'semua' ||
            student.kelas?.jurusan?.id == jurusanFilter.value

        if (!sesuaiJurusan) {
            return false
        }

        /*
        | Kelas
        */

        const sesuaiKelas =
            kelasFilter.value === 'semua' ||
            student.kelas?.id == kelasFilter.value

        if (!sesuaiKelas) {
            return false
        }

        /*
        | SEARCH GLOBAL
        */

        if (keyword) {
            const dataSearch = [
                student.nama,
                student.nisn,
                student.jenis_kelamin,

                student.kelas?.nama_kelas,
                student.kelas?.tingkat,

                student.kelas?.jurusan?.nama_jurusan,
            ]
                .filter(Boolean)
                .join(' ')
                .toLowerCase()

            if (!dataSearch.includes(keyword)) {
                return false
            }
        }

        return true
    })
})

/*
|--------------------------------------------------------------------------
| JUMLAH HASIL FILTER
|--------------------------------------------------------------------------
*/

const jumlahHasilFilter = computed(() => {
    return siswaTampil.value.length
})

/*
|--------------------------------------------------------------------------
| ADA FILTER AKTIF?
|--------------------------------------------------------------------------
*/

const adaFilterAktif = computed(() => {
    return (
        statusFilter.value !== 'semua' ||
        tingkatFilter.value !== 'semua' ||
        jurusanFilter.value !== 'semua' ||
        kelasFilter.value !== 'semua' ||
        search.value.trim() !== ''
    )
})

/*
|--------------------------------------------------------------------------
| RESET FILTER
|--------------------------------------------------------------------------
*/

const resetFilter = () => {
    statusFilter.value = 'semua'
    tingkatFilter.value = 'semua'
    jurusanFilter.value = 'semua'
    kelasFilter.value = 'semua'
    search.value = ''
}

/*
|--------------------------------------------------------------------------
| JIKA TINGKAT BERUBAH
|--------------------------------------------------------------------------
*/

const ubahTingkat = () => {
    /*
    Jika kelas yang sedang dipilih tidak termasuk
    dalam tingkat baru, reset kelas.
    */

    if (
        kelasFilter.value !== 'semua' &&
        !daftarKelasTersedia.value.some(
            kelas => kelas.id == kelasFilter.value
        )
    ) {
        kelasFilter.value = 'semua'
    }
}

/*
|--------------------------------------------------------------------------
| JIKA JURUSAN BERUBAH
|--------------------------------------------------------------------------
*/

const ubahJurusan = () => {
    /*
    Jika kelas yang sedang dipilih tidak termasuk
    dalam jurusan baru, reset kelas.
    */

    if (
        kelasFilter.value !== 'semua' &&
        !daftarKelasTersedia.value.some(
            kelas => kelas.id == kelasFilter.value
        )
    ) {
        kelasFilter.value = 'semua'
    }
}
</script>

<template>
    <TksiLayout>

        <Head title="Input TKSI" />

        <div class="space-y-6">

            <!-- ========================================================= -->
            <!-- HEADER -->
            <!-- ========================================================= -->

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

                    <div class="flex items-center gap-4">

                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white/10"
                        >
                            <ClipboardDocumentCheckIcon class="h-6 w-6" />
                        </div>

                        <div>

                            <h1 class="text-2xl font-bold tracking-tight">
                                Input TKSI
                            </h1>

                            <p class="mt-1 max-w-2xl text-sm font-medium text-white/80">
                                Kelola hasil tes kebugaran siswa pada periode aktif.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- FLASH SUCCESS -->
            <!-- ========================================================= -->

            <div
                v-if="flash.success"
                class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4"
            >

                <CheckCircleIcon
                    class="h-5 w-5 shrink-0 text-emerald-600"
                />

                <p class="text-sm font-bold text-emerald-700">
                    {{ flash.success }}
                </p>

            </div>


            <!-- ========================================================= -->
            <!-- TIDAK ADA PERIODE -->
            <!-- ========================================================= -->

            <div
                v-if="!periode"
                class="rounded-2xl border border-rose-100 bg-rose-50 p-6"
            >

                <div class="flex items-start gap-3">

                    <ExclamationCircleIcon
                        class="h-6 w-6 shrink-0 text-rose-600"
                    />

                    <div>

                        <h2 class="text-sm font-extrabold text-rose-700">
                            Tidak ada periode aktif
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-rose-600">
                            Input TKSI belum dapat dilakukan sampai admin
                            mengaktifkan periode.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ========================================================= -->
            <!-- PERIODE AKTIF -->
            <!-- ========================================================= -->

            <template v-if="periode">

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
    {{ formatTanggal(periode.tanggal_mulai) }}
    —
    {{ formatTanggal(periode.tanggal_selesai) }}
</p>

                        </div>

                        <span
                            class="ml-auto rounded-full bg-emerald-100 px-3 py-1 text-[9px] font-extrabold uppercase text-emerald-700"
                        >
                            Aktif
                        </span>

                    </div>

                </div>


                <!-- ===================================================== -->
                <!-- STATISTIK -->
                <!-- ===================================================== -->

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <!-- TOTAL -->

                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700"
                            >
                                <UserGroupIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p
                                    class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                                >
                                    Total Peserta
                                </p>

                                <p
                                    class="mt-1 text-2xl font-extrabold text-slate-800"
                                >
                                    {{ statistik.total }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- LENGKAP -->

                    <div
                        class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"
                            >
                                <CheckCircleIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p
                                    class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600"
                                >
                                    Sudah Lengkap
                                </p>

                                <p
                                    class="mt-1 text-2xl font-extrabold text-emerald-800"
                                >
                                    {{ statistik.lengkap }}
                                </p>

                            </div>

                        </div>

                    </div>


                    <!-- BELUM -->

                    <div
                        class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm"
                    >

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700"
                            >
                                <ExclamationCircleIcon class="h-5 w-5" />
                            </div>

                            <div>

                                <p
                                    class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600"
                                >
                                    Belum Lengkap
                                </p>

                                <p
                                    class="mt-1 text-2xl font-extrabold text-amber-800"
                                >
                                    {{ statistik.belum_lengkap }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ===================================================== -->
                <!-- DAFTAR PESERTA -->
                <!-- ===================================================== -->

                <div
                    class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm"
                >

                    <!-- HEADER -->

                    <div
                        class="border-b border-slate-100 px-6 py-5"
                    >

                        <div>

                            <h2 class="text-sm font-extrabold text-slate-800">
                                Daftar Peserta TKSI
                            </h2>

                            <p class="mt-1 text-[10px] font-medium text-slate-400">
                                Status pengisian hasil TKSI pada periode aktif.
                            </p>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- FILTER -->
                    <!-- ================================================= -->

                    <div
                        class="border-b border-slate-100 bg-slate-50/70 px-6 py-5"
                    >

                        <div class="flex items-center gap-2">

                            <FunnelIcon
                                class="h-4 w-4 text-slate-500"
                            />

                            <p
                                class="text-xs font-extrabold text-slate-700"
                            >
                                Filter Peserta
                            </p>

                        </div>


                        <!-- SEARCH -->

                        <div class="mt-4">

                            <label
                                class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400"
                            >
                                Cari Siswa
                            </label>

                            <div class="relative">

                                <MagnifyingGlassIcon
                                    class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                />

                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari nama, NISN, kelas, jurusan, tingkat..."
                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-10 text-xs font-medium text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                />

                                <button
                                    v-if="search"
                                    type="button"
                                    @click="search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                                >
                                    <XMarkIcon class="h-4 w-4" />
                                </button>

                            </div>

                        </div>


                        <!-- FILTER SELECT -->

                        <div
                            class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"
                        >

                            <!-- STATUS -->

                            <div>

                                <label
                                    class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Status
                                </label>

                                <select
                                    v-model="statusFilter"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >

                                    <option value="semua">
                                        Semua Status
                                    </option>

                                    <option value="belum">
                                        Belum Lengkap
                                    </option>

                                    <option value="lengkap">
                                        Sudah Lengkap
                                    </option>

                                </select>

                            </div>


                            <!-- TINGKAT -->

                            <div>

                                <label
                                    class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Tingkat
                                </label>

                                <select
                                    v-model="tingkatFilter"
                                    @change="ubahTingkat"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >

                                    <option value="semua">
                                        Semua Tingkat
                                    </option>

                                    <option
                                        v-for="tingkat in daftarTingkat"
                                        :key="tingkat"
                                        :value="tingkat"
                                    >
                                        Tingkat {{ tingkat }}
                                    </option>

                                </select>

                            </div>


                            <!-- JURUSAN -->

                            <div>

                                <label
                                    class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Jurusan
                                </label>

                                <select
                                    v-model="jurusanFilter"
                                    @change="ubahJurusan"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >

                                    <option value="semua">
                                        Semua Jurusan
                                    </option>

                                    <option
                                        v-for="jurusan in daftarJurusan"
                                        :key="jurusan.id"
                                        :value="jurusan.id"
                                    >
                                        {{ jurusan.nama_jurusan }}
                                    </option>

                                </select>

                            </div>


                            <!-- KELAS -->

                            <div>

                                <label
                                    class="mb-1.5 block text-[10px] font-bold uppercase tracking-wider text-slate-400"
                                >
                                    Kelas
                                </label>

                                <select
                                    v-model="kelasFilter"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-xs font-semibold text-slate-700 outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                                >

                                    <option value="semua">
                                        Semua Kelas
                                    </option>

                                    <option
                                        v-for="kelas in daftarKelasTersedia"
                                        :key="kelas.id"
                                        :value="kelas.id"
                                    >
                                        {{ kelas.nama_kelas }}
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- FILTER INFO -->

                        <div
                            class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >

                            <p class="text-[10px] font-semibold text-slate-400">
                                Menampilkan
                                <span class="font-extrabold text-slate-700">
                                    {{ jumlahHasilFilter }}
                                </span>
                                dari
                                <span class="font-extrabold text-slate-700">
                                    {{ props.siswa.length }}
                                </span>
                                siswa
                            </p>


                            <button
                                v-if="adaFilterAktif"
                                type="button"
                                @click="resetFilter"
                                class="inline-flex items-center justify-center gap-1.5 text-[10px] font-extrabold text-blue-600 hover:text-blue-800"
                            >

                                <XMarkIcon class="h-3.5 w-3.5" />

                                Reset Filter

                            </button>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- DATA SISWA -->
                    <!-- ================================================= -->

                    <div
                        v-if="siswaTampil.length"
                        class="divide-y divide-slate-100"
                    >

                        <div
                            v-for="student in siswaTampil"
                            :key="student.id"
                            class="flex flex-col gap-4 px-6 py-5 transition hover:bg-slate-50 md:flex-row md:items-center md:justify-between"
                        >

                            <!-- IDENTITAS -->

                            <div
                                class="flex min-w-0 items-center gap-4"
                            >

                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-extrabold text-blue-700"
                                >
                                    {{
                                        student.nama
                                            ?.charAt(0)
                                            ?.toUpperCase()
                                    }}
                                </div>


                                <div class="min-w-0">

                                    <h3
                                        class="truncate text-sm font-extrabold text-slate-800"
                                    >
                                        {{ student.nama }}
                                    </h3>


                                    <div
                                        class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-[10px] font-semibold text-slate-400"
                                    >

                                        <span>
                                            NISN:
                                            {{ student.nisn || '-' }}
                                        </span>

                                        <span>
                                            Kelas:
                                            {{
                                                student.kelas?.nama_kelas ||
                                                '-'
                                            }}
                                        </span>

                                        <span>
                                            Tingkat:
                                            {{
                                                student.kelas?.tingkat ||
                                                '-'
                                            }}
                                        </span>

                                        <span>
                                            Jurusan:
                                            {{
                                                student.kelas?.jurusan
                                                    ?.nama_jurusan ||
                                                '-'
                                            }}
                                        </span>

                                        <span>
                                            {{
                                                student.jenis_kelamin || '-'
                                            }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- STATUS + AKSI -->

                            <div
                                class="flex flex-col gap-3 sm:flex-row sm:items-center"
                            >

                                <!-- SUDAH LENGKAP -->

                                <div
                                    v-if="student.lengkap"
                                    class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-[10px] font-extrabold text-emerald-700"
                                >

                                    <CheckCircleIcon class="h-3.5 w-3.5" />

                                    Sudah Lengkap

                                </div>


                                <!-- BELUM LENGKAP -->

                                <div
                                    v-else
                                    class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-[10px] font-extrabold text-amber-700"
                                >

                                    <ExclamationCircleIcon
                                        class="h-3.5 w-3.5"
                                    />

                                    Belum Lengkap
                                    ({{ student.jumlah_hasil }}/{{
                                        student.total_komponen
                                    }})

                                </div>


                                <!-- AKSI -->

                                <Link
                                    :href="
                                        route(
                                            'tksi.input.create',
                                            {
                                                siswa: student.id,
                                            }
                                        )
                                    "
                                    class="inline-flex items-center justify-center gap-1 rounded-xl border border-slate-200 px-4 py-2.5 text-[10px] font-extrabold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                                >

                                    {{
                                        student.lengkap
                                            ? 'Edit Hasil'
                                            : 'Input Hasil'
                                    }}

                                    <ArrowRightIcon
                                        class="h-3.5 w-3.5"
                                    />

                                </Link>

                            </div>

                        </div>

                    </div>


                    <!-- ================================================= -->
                    <!-- KOSONG -->
                    <!-- ================================================= -->

                    <div
                        v-else
                        class="px-6 py-14 text-center"
                    >

                        <MagnifyingGlassIcon
                            class="mx-auto h-10 w-10 text-slate-300"
                        />

                        <p
                            class="mt-3 text-sm font-bold text-slate-500"
                        >
                            Tidak ada data siswa
                        </p>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Coba ubah kata pencarian atau filter yang digunakan.
                        </p>

                        <button
                            v-if="adaFilterAktif"
                            type="button"
                            @click="resetFilter"
                            class="mt-4 rounded-xl bg-blue-50 px-4 py-2 text-[10px] font-extrabold text-blue-700 transition hover:bg-blue-100"
                        >
                            Reset Filter
                        </button>

                    </div>

                </div>

            </template>

        </div>

    </TksiLayout>
</template>