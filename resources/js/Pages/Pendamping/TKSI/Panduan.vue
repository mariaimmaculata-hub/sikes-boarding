<script setup>
import PendampingLayout from '@/Layouts/PendampingLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

import {
    HandRaisedIcon,
    ArrowUpCircleIcon,
    ArrowsRightLeftIcon,
    BoltIcon,
    FireIcon,
    HeartIcon
} from '@heroicons/vue/24/outline'

const tes = [
    {
        title: 'Hand and Eye Coordination Test',
        description: 'Mengukur koordinasi mata dan tangan melalui aktivitas melempar dan menangkap bola.',
        icon: HandRaisedIcon,
        color: 'bg-blue-100 text-blue-700',
        route: 'pendamping.tksi.panduan.hand-eye'
    },
    {
        title: 'Vertical Jump Test',
        description: 'Mengukur daya ledak (power) otot tungkai melalui tes loncat tegak.',
        icon: ArrowUpCircleIcon,
        color: 'bg-green-100 text-green-700',
        route: 'pendamping.tksi.panduan.vertical-jump'
    },
    {
        title: 'T Test',
        description: 'Mengukur kelincahan siswa dengan lintasan berbentuk huruf T.',
        icon: ArrowsRightLeftIcon,
        color: 'bg-orange-100 text-orange-700',
        route: 'pendamping.tksi.panduan.t-test'
    },
    {
        title: 'Hand Touch Reaction Test',
        description: 'Mengukur kecepatan reaksi terhadap aba-aba warna.',
        icon: BoltIcon,
        color: 'bg-yellow-100 text-yellow-700',
        route: 'pendamping.tksi.panduan.hand-touch'
    },
    {
        title: 'Dipping Test',
        description: 'Mengukur daya tahan otot lengan melalui gerakan dipping.',
        icon: FireIcon,
        color: 'bg-red-100 text-red-700',
        route: 'pendamping.tksi.panduan.dipping'
    },
    {
        title: 'Beep Test',
        description: 'Mengukur daya tahan kardiorespirasi (VO₂Max) menggunakan tes lari 20 meter.',
        icon: HeartIcon,
        color: 'bg-pink-100 text-pink-700',
        route: 'pendamping.tksi.panduan.beep'
    }
]
</script>

<template>
    <Head title="Panduan TKSI" />

    <PendampingLayout>

        <div class="space-y-6">

            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h1 class="text-2xl font-bold text-slate-800">
                    Panduan Tes Kebugaran Siswa Indonesia (TKSI)
                </h1>

                <p class="text-slate-500 mt-2">
                    Fase EF (SMA/SMK/MA Kelas X, XI, XII)
                </p>

                <p class="mt-4 text-slate-600 leading-relaxed">
                    Pilih salah satu komponen tes untuk melihat prosedur
                    pelaksanaan, peralatan, penskoran, serta norma penilaian
                    sesuai pedoman resmi Tes Kebugaran Siswa Indonesia (TKSI).
                </p>

            </div>

            <!-- Card -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <div
                    v-for="item in tes"
                    :key="item.title"
                    class="bg-white rounded-2xl border shadow-sm hover:shadow-lg transition duration-300 overflow-hidden"
                >

                    <div class="p-6">

                        <div
                            class="w-14 h-14 rounded-xl flex items-center justify-center mb-5"
                            :class="item.color"
                        >
                            <component
                                :is="item.icon"
                                class="w-8 h-8"
                            />
                        </div>

                        <h2 class="font-bold text-lg text-slate-800 mb-3">
                            {{ item.title }}
                        </h2>

                        <p class="text-sm text-slate-500 leading-relaxed mb-6">
                            {{ item.description }}
                        </p>

                        <Link
                            :href="route(item.route)"
                            class="inline-flex items-center justify-center w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-3 rounded-xl transition"
                        >
                            Lihat Panduan
                        </Link>

                    </div>

                </div>

            </div>

        </div>

    </PendampingLayout>

</template>