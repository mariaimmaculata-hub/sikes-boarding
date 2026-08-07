<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    ArrowLeftIcon,
    CalendarDaysIcon,
    UserGroupIcon,
    ClipboardDocumentCheckIcon,
    EyeIcon
} from '@heroicons/vue/24/outline'

const props = defineProps({
    batch: Object,
    siswa: Array
})

const kategoriColor = (kategori) => {
    if (kategori === 'Baik Sekali')
        return 'bg-emerald-100 text-emerald-700'

    if (kategori === 'Baik')
        return 'bg-green-100 text-green-700'

    if (kategori === 'Sedang')
        return 'bg-yellow-100 text-yellow-700'

    if (kategori === 'Kurang')
        return 'bg-orange-100 text-orange-700'

    if (kategori === 'Kurang Sekali')
        return 'bg-red-100 text-red-700'

    return 'bg-slate-100 text-slate-600'
}
</script>

<template>

<PendampingLayout>

<Head :title="batch.nama_tes"/>

<div class="max-w-7xl mx-auto p-6 space-y-6">

    <!-- Header -->

    <div class="flex items-center gap-3">

        <Link
            :href="route('pendamping.tksi.index')"
            class="p-2 rounded-xl hover:bg-slate-100"
        >
            <ArrowLeftIcon class="w-5 h-5"/>
        </Link>

        <div>

            <h1 class="text-2xl font-bold">

                {{ batch.nama_tes }}

            </h1>

            <p class="text-slate-500">

                Detail Batch Tes TKSI

            </p>

        </div>

    </div>



    <!-- Informasi Batch -->

    <div class="grid lg:grid-cols-3 gap-5">

        <div class="bg-white rounded-2xl border p-6">

            <div class="flex items-center gap-2 mb-4">

                <CalendarDaysIcon class="w-5 h-5 text-blue-600"/>

                <h3 class="font-bold">

                    Informasi Tes

                </h3>

            </div>

            <div class="space-y-3 text-sm">

                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Batch
                    </span>

                    <span class="font-semibold">
                        {{ batch.periode }}
                    </span>

                </div>

                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Tanggal
                    </span>

                    <span class="font-semibold">
                        {{ batch.tanggal }}
                    </span>

                </div>

                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Total Peserta
                    </span>

                    <span class="font-semibold">
                        {{ siswa.length }} siswa
                    </span>

                </div>

            </div>

        </div>



        <div class="lg:col-span-2 bg-white rounded-2xl border p-6">

            <div class="flex items-center gap-2 mb-4">

                <ClipboardDocumentCheckIcon
                    class="w-5 h-5 text-green-600"
                />

                <h3 class="font-bold">

                    Komponen TKSI

                </h3>

            </div>

            <div class="grid md:grid-cols-2 gap-3">

                <div class="rounded-xl bg-slate-50 p-3">

                    Hand and Eye Coordination Test

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    Vertical Jump Test

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    T Test

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    Hand Touch Reaction Test

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    Dipping Test

                </div>

                <div class="rounded-xl bg-slate-50 p-3">

                    Beep Test

                </div>

            </div>

        </div>

    </div>



    <!-- Daftar Peserta -->

    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b flex items-center gap-2">

            <UserGroupIcon class="w-5 h-5 text-blue-600"/>

            <h3 class="font-bold">

                Daftar Peserta

            </h3>

        </div>

        <table class="w-full">

            <thead class="bg-slate-50">

                <tr>

                    <th class="px-6 py-4 text-left">
                        Nama
                    </th>

                    <th>
                        Kelas
                    </th>

                    <th>
                        Jurusan
                    </th>

                    <th>
                        Kategori
                    </th>

                    <th>
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody>

                <tr
                    v-for="item in siswa"
                    :key="item.id"
                    class="border-t"
                >

                    <td class="px-6 py-4">

                        <div>

                            <p class="font-semibold">

                                {{ item.nama }}

                            </p>

                            <p class="text-xs text-slate-500">

                                {{ item.nis }}

                            </p>

                        </div>

                    </td>

                    <td class="text-center">

                        {{ item.kelas }}

                    </td>

                    <td class="text-center text-sm">

                        {{ item.jurusan }}

                    </td>

                    <td class="text-center">

                        <span
                            class="px-3 py-1 rounded-full text-xs font-bold"
                            :class="kategoriColor(item.kategori)"
                        >

                            {{ item.kategori }}

                        </span>

                    </td>

                    <td class="text-center">

                        <Link
                            v-if="item.status=='Belum'"
                            :href="route('pendamping.tksi.isi',item.id)"
                            class="bg-blue-600 text-white px-3 py-2 rounded-lg text-xs font-semibold"
                        >

                            Isi Tes

                        </Link>

                        <Link
                            v-else
                            :href="route('pendamping.tksi.hasil',item.id)"
                            class="inline-flex p-2 rounded-lg hover:bg-slate-100"
                        >

                            <EyeIcon class="w-5 h-5"/>

                        </Link>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</PendampingLayout>

</template>