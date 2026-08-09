<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

import {
    ArrowLeftIcon,
    UserIcon,
    AcademicCapIcon,
    PhoneIcon,
    MapPinIcon,
    CheckCircleIcon,
    PencilSquareIcon,
} from '@heroicons/vue/24/outline'


const props = defineProps({
    siswa: {
        type: Object,
        required: true,
    },

    kelas: {
        type: Array,
        default: () => [],
    },
})


// ==================================================
// FORM
// ==================================================

const form = useForm({
    nisn: props.siswa.nisn ?? '',
    nama: props.siswa.nama ?? '',
    tempat_lahir: props.siswa.tempat_lahir ?? '',
    tanggal_lahir: props.siswa.tanggal_lahir
        ? String(props.siswa.tanggal_lahir).substring(0, 10)
        : '',
    jenis_kelamin: props.siswa.jenis_kelamin ?? '',
    kelas_id: props.siswa.kelas_id ?? '',
    angkatan: props.siswa.angkatan ?? '',
    alamat: props.siswa.alamat ?? '',
    no_hp: props.siswa.no_hp ?? '',
    nama_orang_tua: props.siswa.nama_orang_tua ?? '',
    no_hp_orang_tua: props.siswa.no_hp_orang_tua ?? '',
    status: props.siswa.status ?? 'aktif',
})


// ==================================================
// SUBMIT
// ==================================================

const submit = () => {
    form.put(
        route(
            'admin.master.siswa.update',
            props.siswa.id
        )
    )
}
</script>


<template>

    <AdminLayout>

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div class="flex items-start gap-3">

                    <Link
                        :href="route('admin.master.siswa.index')"
                        class="mt-1 rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                        title="Kembali"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                    </Link>

                    <div>

                        <h1 class="text-2xl font-bold text-slate-800">
                            Edit Data Siswa
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Perbarui informasi data siswa.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FORM
            ================================================== -->

            <form
                @submit.prevent="submit"
                class="space-y-6"
            >

                <!-- ==================================================
                     DATA IDENTITAS
                ================================================== -->

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                                <UserIcon class="h-5 w-5" />

                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Data Identitas
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Informasi dasar identitas siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <!-- NISN -->

                        <div>

                            <label
                                for="nisn"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                NISN
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="nisn"
                                v-model="form.nisn"
                                type="text"
                                maxlength="20"
                                placeholder="Masukkan NISN"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.nisn
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.nisn"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.nisn }}
                            </p>

                        </div>


                        <!-- NAMA -->

                        <div>

                            <label
                                for="nama"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Nama Lengkap
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="nama"
                                v-model="form.nama"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.nama
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.nama"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.nama }}
                            </p>

                        </div>


                        <!-- TEMPAT LAHIR -->

                        <div>

                            <label
                                for="tempat_lahir"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Tempat Lahir
                            </label>

                            <input
                                id="tempat_lahir"
                                v-model="form.tempat_lahir"
                                type="text"
                                placeholder="Contoh: Pemalang"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.tempat_lahir
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.tempat_lahir"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tempat_lahir }}
                            </p>

                        </div>


                        <!-- TANGGAL LAHIR -->

                        <div>

                            <label
                                for="tanggal_lahir"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Tanggal Lahir
                            </label>

                            <input
                                id="tanggal_lahir"
                                v-model="form.tanggal_lahir"
                                type="date"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.tanggal_lahir
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.tanggal_lahir"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.tanggal_lahir }}
                            </p>

                        </div>


                        <!-- JENIS KELAMIN -->

                        <div class="md:col-span-2">

                            <label class="mb-2 block text-sm font-semibold text-slate-700">
                                Jenis Kelamin
                                <span class="text-rose-500">*</span>
                            </label>

                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                                <!-- LAKI-LAKI -->

                                <label
                                    :class="[
                                        'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                        form.jenis_kelamin === 'L'
                                            ? 'border-blue-300 bg-blue-50 ring-1 ring-blue-200'
                                            : 'border-slate-200 hover:bg-slate-50'
                                    ]"
                                >

                                    <input
                                        v-model="form.jenis_kelamin"
                                        type="radio"
                                        value="L"
                                        class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                    />

                                    <div>

                                        <p class="text-sm font-semibold text-slate-700">
                                            Laki-laki
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Jenis kelamin laki-laki
                                        </p>

                                    </div>

                                </label>


                                <!-- PEREMPUAN -->

                                <label
                                    :class="[
                                        'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                        form.jenis_kelamin === 'P'
                                            ? 'border-blue-300 bg-blue-50 ring-1 ring-blue-200'
                                            : 'border-slate-200 hover:bg-slate-50'
                                    ]"
                                >

                                    <input
                                        v-model="form.jenis_kelamin"
                                        type="radio"
                                        value="P"
                                        class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                    />

                                    <div>

                                        <p class="text-sm font-semibold text-slate-700">
                                            Perempuan
                                        </p>

                                        <p class="text-xs text-slate-400">
                                            Jenis kelamin perempuan
                                        </p>

                                    </div>

                                </label>

                            </div>

                            <p
                                v-if="form.errors.jenis_kelamin"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.jenis_kelamin }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     DATA AKADEMIK
                ================================================== -->

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">

                                <AcademicCapIcon class="h-5 w-5" />

                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Data Akademik
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Informasi kelas dan angkatan siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <!-- KELAS -->

                        <div>

                            <label
                                for="kelas_id"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Kelas
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                id="kelas_id"
                                v-model="form.kelas_id"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition focus:ring-2',
                                    form.errors.kelas_id
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            >

                                <option value="">
                                    Pilih kelas
                                </option>

                                <option
                                    v-for="item in props.kelas"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.nama_kelas }}

                                    <template v-if="item.jurusan">
                                        — {{ item.jurusan.nama_jurusan }}
                                    </template>

                                </option>

                            </select>

                            <p
                                v-if="form.errors.kelas_id"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.kelas_id }}
                            </p>

                        </div>


                        <!-- ANGKATAN -->

                        <div>

                            <label
                                for="angkatan"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Angkatan
                                <span class="text-rose-500">*</span>
                            </label>

                            <input
                                id="angkatan"
                                v-model="form.angkatan"
                                type="number"
                                min="2000"
                                max="2100"
                                placeholder="Contoh: 2026"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.angkatan
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.angkatan"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.angkatan }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     DATA KONTAK
                ================================================== -->

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                                <PhoneIcon class="h-5 w-5" />

                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Data Kontak
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Informasi alamat dan kontak siswa.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">

                        <!-- ALAMAT -->

                        <div class="md:col-span-2">

                            <label
                                for="alamat"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Alamat
                            </label>

                            <div class="relative">

                                <MapPinIcon
                                    class="pointer-events-none absolute left-3 top-3 h-5 w-5 text-slate-400"
                                />

                                <textarea
                                    id="alamat"
                                    v-model="form.alamat"
                                    rows="3"
                                    placeholder="Masukkan alamat lengkap siswa"
                                    :class="[
                                        'w-full resize-none rounded-xl border bg-white py-2.5 pl-10 pr-3.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                        form.errors.alamat
                                            ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                            : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                    ]"
                                ></textarea>

                            </div>

                            <p
                                v-if="form.errors.alamat"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.alamat }}
                            </p>

                        </div>


                        <!-- NO HP -->

                        <div>

                            <label
                                for="no_hp"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                No. HP Siswa
                            </label>

                            <input
                                id="no_hp"
                                v-model="form.no_hp"
                                type="text"
                                maxlength="20"
                                placeholder="Contoh: 081234567890"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.no_hp
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.no_hp"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.no_hp }}
                            </p>

                        </div>


                        <!-- NAMA ORANG TUA -->

                        <div>

                            <label
                                for="nama_orang_tua"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                Nama Orang Tua
                            </label>

                            <input
                                id="nama_orang_tua"
                                v-model="form.nama_orang_tua"
                                type="text"
                                placeholder="Masukkan nama orang tua"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.nama_orang_tua
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.nama_orang_tua"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.nama_orang_tua }}
                            </p>

                        </div>


                        <!-- NO HP ORANG TUA -->

                        <div>

                            <label
                                for="no_hp_orang_tua"
                                class="mb-1.5 block text-sm font-semibold text-slate-700"
                            >
                                No. HP Orang Tua
                            </label>

                            <input
                                id="no_hp_orang_tua"
                                v-model="form.no_hp_orang_tua"
                                type="text"
                                maxlength="20"
                                placeholder="Contoh: 081234567890"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    form.errors.no_hp_orang_tua
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'
                                ]"
                            />

                            <p
                                v-if="form.errors.no_hp_orang_tua"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.no_hp_orang_tua }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     STATUS
                ================================================== -->

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                                <CheckCircleIcon class="h-5 w-5" />

                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-slate-800">
                                    Status Siswa
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Tentukan status siswa saat ini.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="p-5">

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">

                            <!-- AKTIF -->

                            <label
                                :class="[
                                    'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                    form.status === 'aktif'
                                        ? 'border-emerald-300 bg-emerald-50 ring-1 ring-emerald-200'
                                        : 'border-slate-200 hover:bg-slate-50'
                                ]"
                            >

                                <input
                                    v-model="form.status"
                                    type="radio"
                                    value="aktif"
                                    class="h-4 w-4 border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                />

                                <div>

                                    <p class="text-sm font-semibold text-slate-700">
                                        Aktif
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Masih bersekolah
                                    </p>

                                </div>

                            </label>


                            <!-- NONAKTIF -->

                            <label
                                :class="[
                                    'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                    form.status === 'nonaktif'
                                        ? 'border-slate-300 bg-slate-50 ring-1 ring-slate-200'
                                        : 'border-slate-200 hover:bg-slate-50'
                                ]"
                            >

                                <input
                                    v-model="form.status"
                                    type="radio"
                                    value="nonaktif"
                                    class="h-4 w-4 border-slate-300 text-slate-600 focus:ring-slate-500"
                                />

                                <div>

                                    <p class="text-sm font-semibold text-slate-700">
                                        Nonaktif
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Tidak aktif
                                    </p>

                                </div>

                            </label>


                            <!-- LULUS -->

                            <label
                                :class="[
                                    'flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition',
                                    form.status === 'lulus'
                                        ? 'border-blue-300 bg-blue-50 ring-1 ring-blue-200'
                                        : 'border-slate-200 hover:bg-slate-50'
                                ]"
                            >

                                <input
                                    v-model="form.status"
                                    type="radio"
                                    value="lulus"
                                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                />

                                <div>

                                    <p class="text-sm font-semibold text-slate-700">
                                        Lulus
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Telah menyelesaikan sekolah
                                    </p>

                                </div>

                            </label>

                        </div>

                        <p
                            v-if="form.errors.status"
                            class="mt-1.5 text-xs font-medium text-rose-600"
                        >
                            {{ form.errors.status }}
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     ACTION
                ================================================== -->

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                    <Link
                        :href="route('admin.master.siswa.show', props.siswa.id)"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                    >
                        Batal
                    </Link>


                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                    >

                        <PencilSquareIcon class="h-5 w-5" />

                        <span>
                            {{
                                form.processing
                                    ? 'Menyimpan...'
                                    : 'Simpan Perubahan'
                            }}
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </AdminLayout>

</template>
