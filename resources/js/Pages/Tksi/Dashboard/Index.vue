<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    ArrowRightIcon,
    HeartIcon,
    ClockIcon,
    BellIcon,
} from '@heroicons/vue/24/outline'

import TksiLayout from '@/Layouts/TksiLayout.vue'


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

const page = usePage()


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const user = computed(() => page.props.user)

const periode = computed(() => page.props.periode)

const stats = computed(() => page.props.stats ?? [])

const attentionStudents = computed(
    () => page.props.attentionStudents ?? []
)

const activities = computed(
    () => page.props.activities ?? []
)

const reminders = computed(
    () => page.props.reminders ?? []
)
</script>


<template>

    <TksiLayout>

        <div class="space-y-6">


            <!-- =========================================================
                 WELCOME / INFORMASI TKSI
            ========================================================== -->

            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-700 via-pink-600 to-rose-600 p-6 text-white shadow-lg md:p-8"
            >

                <!-- DEKORASI -->

                <div
                    class="pointer-events-none absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-20 right-20 h-48 w-48 rounded-full bg-rose-300/10"
                ></div>

                <div
                    class="pointer-events-none absolute bottom-0 right-0 hidden opacity-10 md:block"
                >

                    <svg
                        class="h-64 w-64"
                        viewBox="0 0 100 100"
                        fill="currentColor"
                    >

                        <circle
                            cx="70"
                            cy="50"
                            r="30"
                        />

                    </svg>

                </div>


                <!-- CONTENT -->

                <div
                    class="relative z-10 max-w-5xl"
                >

                    <!-- LABEL -->

                    <p
                        class="text-[11px] font-bold uppercase tracking-[0.2em] text-pink-100"
                    >
                        Instrumen Kebugaran Siswa Indonesia
                    </p>


                    <!-- TITLE -->

                    <h2
                        class="mt-1 text-2xl font-extrabold tracking-tight md:text-3xl"
                    >
                        Dashboard TKSI
                    </h2>


                    <!-- INFORMASI UTAMA -->

                    <div
                        class="mt-5 max-w-4xl"
                    >

                        <p
                            class="text-sm font-semibold leading-6 text-white/95 md:text-base"
                        >

                            <span class="font-extrabold text-white">
                                TKSI
                            </span>

                            merupakan singkatan dari

                            <span class="font-extrabold text-white">
                                Tes Kebugaran Siswa Indonesia.
                            </span>

                        </p>


                        <p
                            class="mt-1.5 text-xs leading-5 text-pink-50/90 md:text-sm md:leading-6"
                        >

                            Instrumen Kebugaran Siswa Indonesia merupakan
                            satu paket rangkaian tes kebugaran siswa Indonesia
                            yang harus dilakukan secara keseluruhan tanpa
                            mengurangi dan menambahkan item tes lain.

                        </p>


                        <p
                            class="mt-1.5 text-xs leading-5 text-pink-50/90 md:text-sm md:leading-6"
                        >

                            Instrumen ini digunakan bagi siswa

                            <span class="font-semibold text-white">
                                SMA/SMK/MK/Madrasah Aliyah (MA)
                            </span>

                            pada fase EF.

                        </p>

                    </div>


                    <!-- PEMBATAS -->

                    <div
                        class="my-5 h-px w-full bg-white/15"
                    ></div>


                    <!-- WELCOME USER + PERIODE -->

                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >

                        <!-- WELCOME -->

                        <p
                            class="text-sm font-medium leading-6 text-white/85"
                        >

                            Selamat datang,

                            <span class="font-bold text-white">
                                {{ user?.name || 'Petugas TKSI' }}
                            </span>

                            😊

                            <br class="sm:hidden" />

                            Kelola dan pantau pelaksanaan tes kebugaran
                            siswa melalui dashboard ini.

                        </p>


                        <!-- PERIODE -->

                        <div
                            v-if="periode"
                            class="shrink-0"
                        >

                            <span
                                class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm"
                            >

                                Periode:

                                <span class="ml-1">
                                    {{ periode.nama_periode }}
                                </span>

                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================================================
                 STATISTICS
            ========================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >

                <div
                    v-for="(s, idx) in stats"
                    :key="idx"
                    class="flex min-h-[145px] flex-col justify-between rounded-2xl border border-slate-100 border-l-4 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    :class="s.color"
                >

                    <!-- NAME -->

                    <div
                        class="flex items-center justify-between"
                    >

                        <span
                            class="text-[11px] font-bold uppercase tracking-wider text-slate-500"
                        >
                            {{ s.name }}
                        </span>

                    </div>


                    <!-- VALUE -->

                    <div
                        class="mt-3 flex flex-col"
                    >

                        <span
                            class="text-2xl font-extrabold leading-none tracking-tight text-slate-900"
                        >
                            {{ s.value }}
                        </span>

                        <span
                            class="mt-2 text-[9px] font-semibold leading-tight text-slate-400"
                        >
                            {{ s.sub }}
                        </span>

                    </div>

                </div>

            </div>


            <!-- =========================================================
                 CONTENT
            ========================================================== -->

            <div
                class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            >


                <!-- =====================================================
                     PESERTA TKSI PERLU PERHATIAN
                ====================================================== -->

                <div
                    class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-2"
                >

                    <div
                        class="space-y-4"
                    >

                        <!-- TITLE -->

                        <h3
                            class="flex items-center gap-1.5 border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-pink-500"
                            />

                            <span>
                                Peserta TKSI Perlu Perhatian
                            </span>

                        </h3>


                        <!-- ADA DATA -->

                        <div
                            v-if="attentionStudents.length"
                            class="divide-y divide-slate-100"
                        >

                            <div
                                v-for="student in attentionStudents"
                                :key="student.id"
                                class="flex items-center justify-between gap-4 py-3 text-xs"
                            >

                                <!-- SISWA -->

                                <div
                                    class="min-w-0 space-y-0.5"
                                >

                                    <span
                                        class="block truncate font-extrabold text-pink-700"
                                    >
                                        {{ student.name }}
                                    </span>

                                    <span
                                        class="block text-[10px] font-bold text-slate-400"
                                    >
                                        {{ student.class }}
                                    </span>

                                </div>


                                <!-- STATUS -->

                                <div
                                    class="flex shrink-0 items-center gap-3"
                                >

                                    <span
                                        class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-semibold text-slate-500"
                                    >
                                        {{ student.note }}
                                    </span>


                                    <span
                                        class="rounded-full border border-rose-200 bg-rose-50 px-2.5 py-0.5 text-[9px] font-extrabold uppercase text-rose-700"
                                    >
                                        {{ student.status }}
                                    </span>

                                </div>

                            </div>

                        </div>


                        <!-- TIDAK ADA DATA -->

                        <div
                            v-else
                            class="rounded-xl bg-pink-50/50 px-4 py-8 text-center"
                        >

                            <HeartIcon
                                class="mx-auto h-8 w-8 text-pink-200"
                            />

                            <p
                                class="mt-2 text-sm font-semibold text-slate-400"
                            >
                                Semua peserta TKSI sudah selesai.
                            </p>

                        </div>

                    </div>


                    <!-- BUTTON -->

                    <Link
                        :href="route('tksi.tksi.index')"
                        class="mt-6 flex w-full items-center justify-center gap-1 rounded-xl border border-pink-100 py-2.5 text-center text-xs font-bold text-pink-700 transition hover:bg-pink-50"
                    >

                        <span>
                            Lihat Data TKSI
                        </span>

                        <ArrowRightIcon
                            class="h-3.5 w-3.5"
                        />

                    </Link>

                </div>


                <!-- =====================================================
                     RIGHT COLUMN
                ====================================================== -->

                <div
                    class="space-y-6"
                >


                    <!-- =================================================
                         AKTIVITAS TERBARU
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                    >

                        <div
                            class="space-y-4"
                        >

                            <!-- TITLE -->

                            <h3
                                class="border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                            >
                                Aktivitas Terbaru
                            </h3>


                            <!-- ADA DATA -->

                            <div
                                v-if="activities.length"
                                class="space-y-4 text-xs"
                            >

                                <div
                                    v-for="(act, idx) in activities"
                                    :key="idx"
                                    class="flex items-start gap-3"
                                >

                                    <!-- ICON -->

                                    <div
                                        class="mt-0.5 flex-shrink-0 rounded-lg bg-pink-50 p-1.5 text-pink-600"
                                    >

                                        <ClockIcon
                                            class="h-4 w-4"
                                        />

                                    </div>


                                    <!-- CONTENT -->

                                    <div
                                        class="min-w-0 space-y-0.5"
                                    >

                                        <span
                                            class="block font-bold text-slate-800"
                                        >
                                            {{ act.text }}
                                        </span>


                                        <span
                                            class="block text-[10px] font-semibold text-slate-500"
                                        >
                                            {{ act.staff }}
                                        </span>


                                        <span
                                            class="block text-[9px] font-bold text-slate-400"
                                        >
                                            {{ act.time }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- EMPTY -->

                            <div
                                v-else
                                class="py-6 text-center text-xs font-semibold text-slate-400"
                            >
                                Belum ada aktivitas TKSI.
                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         JADWAL & PENGINGAT
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                    >

                        <div
                            class="space-y-4"
                        >

                            <!-- TITLE -->

                            <h3
                                class="border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                            >
                                Jadwal & Pengingat
                            </h3>


                            <!-- ADA DATA -->

                            <div
                                v-if="reminders.length"
                                class="space-y-4 text-xs"
                            >

                                <div
                                    v-for="(rem, idx) in reminders"
                                    :key="idx"
                                    class="flex items-start gap-3.5"
                                >

                                    <!-- ICON -->

                                    <div
                                        class="mt-0.5 flex-shrink-0 rounded-lg bg-rose-50 p-1.5 text-rose-600"
                                    >

                                        <BellIcon
                                            class="h-4 w-4"
                                        />

                                    </div>


                                    <!-- CONTENT -->

                                    <div
                                        class="min-w-0 space-y-0.5"
                                    >

                                        <span
                                            class="block leading-tight font-bold text-slate-800"
                                        >
                                            {{ rem.title }}
                                        </span>


                                        <span
                                            class="mt-1 block text-[10px] font-bold text-slate-400"
                                        >
                                            Tanggal: {{ rem.date }}
                                        </span>


                                        <span
                                            class="block text-[10px] font-bold text-slate-400"
                                        >
                                            Batas: {{ rem.deadline }}
                                        </span>


                                        <span
                                            class="mt-1 inline-block rounded border border-rose-200 bg-rose-50 px-2 py-0.5 text-[8px] font-extrabold uppercase tracking-wider text-rose-700"
                                        >
                                            {{ rem.status }}
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- EMPTY -->

                            <div
                                v-else
                                class="rounded-xl bg-pink-50/50 px-4 py-6 text-center text-xs font-semibold text-slate-400"
                            >
                                Tidak ada jadwal TKSI saat ini.
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </TksiLayout>

</template>
