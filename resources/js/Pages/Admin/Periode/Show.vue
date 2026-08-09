<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    ClockIcon,
    PencilSquareIcon,
    UserGroupIcon,
    UserIcon,
    HeartIcon,
} from '@heroicons/vue/24/outline'


// ==================================================
// PROPS
// ==================================================

const props = defineProps({
    periode: {
        type: Object,
        required: true,
    },
})


// ==================================================
// DATA
// ==================================================

const siswa = computed(() => props.periode.siswa ?? [])

const jumlahSiswa = computed(() => siswa.value.length)

const jumlahTksi = computed(() =>
    (props.periode.tksiBatches ?? []).length
)

const jumlahPemeriksaan = computed(() =>
    (props.periode.pemeriksaanBerkala ?? []).length
)

const jumlahKunjungan = computed(() =>
    (props.periode.kunjunganKlinik ?? []).length
)


// ==================================================
// STATUS
// ==================================================

const statusLabel = computed(() => {

    switch (
        String(props.periode.status ?? '').toLowerCase()
    ) {

        case 'aktif':
            return 'Aktif'

        case 'selesai':
            return 'Selesai'

        case 'draft':
            return 'Draft'

        default:
            return props.periode.status ?? '-'
    }
})


const statusClass = computed(() => {

    switch (
        String(props.periode.status ?? '').toLowerCase()
    ) {

        case 'aktif':
            return 'border-emerald-200 bg-emerald-50 text-emerald-700'

        case 'selesai':
            return 'border-slate-200 bg-slate-50 text-slate-600'

        case 'draft':
            return 'border-amber-200 bg-amber-50 text-amber-700'

        default:
            return 'border-slate-200 bg-slate-50 text-slate-600'
    }
})


// ==================================================
// FORMAT DATE
// ==================================================

const formatDate = (date) => {

    if (!date) {
        return '-'
    }

    const value = new Date(date)

    if (Number.isNaN(value.getTime())) {
        return date
    }

    return value.toLocaleDateString(
        'id-ID',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
        }
    )
}


// ==================================================
// BREADCRUMB
// ==================================================

const breadcrumbs = [
    {
        name: 'Dashboard',
        url: route('admin.dashboard'),
    },
    {
        name: 'Periode',
        url: route('admin.periode.index'),
    },
    {
        name: 'Detail Periode',
        url: '#',
    },
]
</script>


<template>

    <Head
        :title="`Detail ${periode.nama_periode}`"
    />

    <AdminLayout :breadcrumbs="breadcrumbs">

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div
                    class="flex items-center gap-3"
                >

                    <Link
                        :href="route('admin.periode.index')"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                        title="Kembali"
                    >

                        <ArrowLeftIcon
                            class="h-5 w-5"
                        />

                    </Link>


                    <div>

                        <h1
                            class="text-2xl font-bold text-slate-800"
                        >
                            Detail Periode
                        </h1>

                        <p
                            class="mt-1 text-sm text-slate-500"
                        >
                            Informasi lengkap periode pemeriksaan kesehatan siswa.
                        </p>

                    </div>

                </div>


                <!-- EDIT -->

                <Link
                    :href="route(
                        'admin.periode.edit',
                        periode.id
                    )"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                >

                    <PencilSquareIcon
                        class="h-4 w-4"
                    />

                    Edit Periode

                </Link>

            </div>


            <!-- ==================================================
                 PERIODE HEADER CARD
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <div
                    class="flex flex-col gap-5 p-5 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div
                        class="flex min-w-0 items-center gap-4"
                    >

                        <div
                            class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-100"
                        >

                            <CalendarDaysIcon
                                class="h-7 w-7 text-blue-700"
                            />

                        </div>


                        <div class="min-w-0">

                            <div
                                class="flex flex-wrap items-center gap-2"
                            >

                                <h2
                                    class="text-lg font-bold text-slate-800"
                                >
                                    {{ periode.nama_periode }}
                                </h2>


                                <span
                                    :class="[
                                        'inline-flex rounded-full border px-2.5 py-1 text-xs font-bold',
                                        statusClass
                                    ]"
                                >
                                    {{ statusLabel }}
                                </span>

                            </div>


                            <p
                                class="mt-1 text-xs text-slate-400"
                            >
                                ID Periode: {{ periode.id }}
                            </p>

                        </div>

                    </div>


                    <!-- PEMBUAT -->

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600"
                        >

                            {{
                                periode.pembuat?.name
                                    ?.charAt(0)
                                    ?.toUpperCase()
                                || '?'
                            }}

                        </div>


                        <div>

                            <p
                                class="text-[11px] text-slate-400"
                            >
                                Dibuat Oleh
                            </p>

                            <p
                                class="text-sm font-semibold text-slate-700"
                            >
                                {{ periode.pembuat?.name || '-' }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- INFO -->

                <div
                    class="grid grid-cols-1 border-t border-slate-100 sm:grid-cols-3"
                >

                    <!-- MULAI -->

                    <div
                        class="border-b border-slate-100 px-5 py-4 sm:border-b-0 sm:border-r"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Tanggal Mulai
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_mulai) }}
                        </p>

                    </div>


                    <!-- SELESAI -->

                    <div
                        class="border-b border-slate-100 px-5 py-4 sm:border-b-0 sm:border-r"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Tanggal Selesai
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_selesai) }}
                        </p>

                    </div>


                    <!-- DURASI -->

                    <div
                        class="px-5 py-4"
                    >

                        <p
                            class="text-xs text-slate-400"
                        >
                            Periode
                        </p>

                        <p
                            class="mt-1 text-sm font-bold text-slate-700"
                        >
                            {{ formatDate(periode.tanggal_mulai) }}
                            –
                            {{ formatDate(periode.tanggal_selesai) }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 SUMMARY
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
            >

                <!-- SISWA -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Siswa Peserta
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahSiswa }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                        >

                            <UserGroupIcon
                                class="h-5 w-5 text-blue-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- PEMERIKSAAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Pemeriksaan
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahPemeriksaan }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-emerald-700"
                            />

                        </div>

                    </div>

                </div>


                <!-- KUNJUNGAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Kunjungan Klinik
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahKunjungan }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-100"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-rose-600"
                            />

                        </div>

                    </div>

                </div>


                <!-- TKSI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center justify-between"
                    >

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400"
                            >
                                Batch TKSI
                            </p>

                            <p
                                class="mt-1 text-2xl font-bold text-slate-800"
                            >
                                {{ jumlahTksi }}
                            </p>

                        </div>


                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100"
                        >

                            <ClockIcon
                                class="h-5 w-5 text-amber-700"
                            />

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 DAFTAR SISWA
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >

                <!-- HEADER -->

                <div
                    class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-100"
                        >

                            <UserGroupIcon
                                class="h-5 w-5 text-blue-700"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Siswa Peserta Periode
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Daftar siswa yang mengikuti periode ini.
                            </p>

                        </div>

                    </div>


                    <span
                        class="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700"
                    >

                        {{ jumlahSiswa }} siswa

                    </span>

                </div>


                <!-- DESKTOP -->

                <div
                    v-if="siswa.length > 0"
                    class="hidden overflow-x-auto lg:block"
                >

                    <table
                        class="min-w-full"
                    >

                        <thead>

                            <tr
                                class="border-b border-slate-200 bg-slate-50"
                            >

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    No
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Siswa
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    NISN
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Kelas
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                                >
                                    Jurusan
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <tr
                                v-for="(item, index) in siswa"
                                :key="item.id"
                                class="transition hover:bg-slate-50"
                            >

                                <td
                                    class="px-5 py-4 text-sm text-slate-500"
                                >
                                    {{ index + 1 }}
                                </td>


                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="flex items-center gap-3"
                                    >

                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                        >

                                            {{
                                                item.nama
                                                    ?.charAt(0)
                                                    ?.toUpperCase()
                                                || '?'
                                            }}

                                        </div>


                                        <p
                                            class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                        >
                                            {{ item.nama }}
                                        </p>

                                    </div>

                                </td>


                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{ item.nisn || '-' }}
                                </td>


                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{ item.kelas?.nama_kelas || '-' }}
                                </td>


                                <td
                                    class="whitespace-nowrap px-5 py-4 text-sm text-slate-600"
                                >
                                    {{
                                        item.kelas?.jurusan?.nama_jurusan
                                        || '-'
                                    }}
                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>


                <!-- MOBILE -->

                <div
                    v-if="siswa.length > 0"
                    class="divide-y divide-slate-100 lg:hidden"
                >

                    <div
                        v-for="(item, index) in siswa"
                        :key="item.id"
                        class="p-4"
                    >

                        <div
                            class="flex items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                            >

                                {{
                                    item.nama
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                    || '?'
                                }}

                            </div>


                            <div class="min-w-0 flex-1">

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ item.nama }}
                                </p>

                                <p
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    NISN: {{ item.nisn || '-' }}
                                </p>

                            </div>


                            <span
                                class="text-xs font-semibold text-slate-400"
                            >
                                #{{ index + 1 }}
                            </span>

                        </div>


                        <div
                            class="mt-3 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3"
                        >

                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    Kelas
                                </p>

                                <p
                                    class="mt-0.5 text-xs font-semibold text-slate-700"
                                >
                                    {{ item.kelas?.nama_kelas || '-' }}
                                </p>

                            </div>


                            <div>

                                <p
                                    class="text-[11px] text-slate-400"
                                >
                                    Jurusan
                                </p>

                                <p
                                    class="mt-0.5 text-xs font-semibold text-slate-700"
                                >
                                    {{
                                        item.kelas?.jurusan?.nama_jurusan
                                        || '-'
                                    }}
                                </p>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- EMPTY -->

                <div
                    v-if="siswa.length === 0"
                    class="px-5 py-16 text-center"
                >

                    <div
                        class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                    >

                        <UserGroupIcon
                            class="h-6 w-6 text-slate-400"
                        />

                    </div>


                    <p
                        class="text-sm font-bold text-slate-700"
                    >
                        Belum ada siswa
                    </p>


                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Belum ada siswa yang terdaftar pada periode ini.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 RELATED DATA
            ================================================== -->

            <div
                class="grid grid-cols-1 gap-4 md:grid-cols-3"
            >

                <!-- PEMERIKSAAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-emerald-700"
                            />

                        </div>


                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Pemeriksaan Berkala
                            </h3>

                            <p
                                class="text-xs text-slate-400"
                            >
                                {{ jumlahPemeriksaan }} data
                            </p>

                        </div>

                    </div>

                </div>


                <!-- KUNJUNGAN -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-rose-100"
                        >

                            <ClipboardDocumentCheckIcon
                                class="h-5 w-5 text-rose-600"
                            />

                        </div>


                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Kunjungan Klinik
                            </h3>

                            <p
                                class="text-xs text-slate-400"
                            >
                                {{ jumlahKunjungan }} data
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TKSI -->

                <div
                    class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100"
                        >

                            <ClockIcon
                                class="h-5 w-5 text-amber-700"
                            />

                        </div>


                        <div>

                            <h3
                                class="text-sm font-bold text-slate-800"
                            >
                                Batch TKSI
                            </h3>

                            <p
                                class="text-xs text-slate-400"
                            >
                                {{ jumlahTksi }} data
                            </p>

                        </div>

                    </div>

                </div>

            </div>


        </div>

    </AdminLayout>

</template>
