<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    ArrowRightIcon,
    HeartIcon,
    ClockIcon,
    BellIcon,
} from '@heroicons/vue/24/outline'

import KlinikLayout from '@/Layouts/KlinikLayout.vue'


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

const page = usePage()


/*
|--------------------------------------------------------------------------
| PROPS DARI CONTROLLER
|--------------------------------------------------------------------------
*/

const user = computed(() => page.props.user)

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

<KlinikLayout>

    <div class="space-y-6">


        <!-- ==================================================
             WELCOME HEADER
        ================================================== -->

        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-600 via-rose-500 to-pink-500 p-6 text-white shadow-md md:p-8"
        >

            <!-- DECORATION -->

            <div
                class="pointer-events-none absolute -right-8 -top-12 hidden h-48 w-48 rounded-full bg-white/10 md:block"
            ></div>

            <div
                class="pointer-events-none absolute -bottom-16 right-24 hidden h-40 w-40 rounded-full bg-white/10 md:block"
            ></div>


            <!-- CONTENT -->

            <div class="relative z-10 space-y-2">

                <p
                    class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/70"
                >
                    SIKES BOARDING
                </p>

                <h2
                    class="text-2xl font-bold tracking-tight md:text-3xl"
                >
                    Dashboard Klinik
                </h2>

                <p
                    class="max-w-2xl text-sm font-medium text-white/85"
                >

                    Selamat datang,

                    <span class="font-bold text-white">
                        {{ user?.name || 'Petugas Klinik' }}
                    </span>

                    😊

                    Berikut ringkasan kesehatan siswa.

                </p>

            </div>

        </div>



        <!-- ==================================================
             STATISTICS
        ================================================== -->

        <div
            class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
        >

            <div
                v-for="(s, idx) in stats"
                :key="idx"
                class="group relative flex min-h-[145px] flex-col justify-between overflow-hidden rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:border-pink-100 hover:shadow-md"
                :class="s.color"
            >

                <!-- AKSEN PINK -->

                <div
                    class="absolute left-0 top-0 h-full w-1 bg-pink-400"
                ></div>


                <!-- TITLE -->

                <div class="flex items-center justify-between">

                    <span
                        class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                    >
                        {{ s.name }}
                    </span>

                    <div
                        class="h-2 w-2 rounded-full bg-pink-400 opacity-70"
                    ></div>

                </div>


                <!-- VALUE -->

                <div class="mt-4 flex flex-col">

                    <span
                        class="text-2xl font-extrabold leading-none tracking-tight text-slate-900"
                    >
                        {{ s.value }}
                    </span>


                    <!-- LINK -->

                    <Link
                        v-if="s.link"
                        :href="route('klinik.kesehatan.pemeriksaan.index')"
                        class="mt-2 flex w-fit items-center gap-1 text-[9px] font-bold leading-tight text-pink-600 transition hover:text-pink-700 hover:underline"
                    >

                        <span>
                            {{ s.sub }}
                        </span>

                        <ArrowRightIcon
                            class="h-2.5 w-2.5"
                        />

                    </Link>


                    <!-- TEXT -->

                    <span
                        v-else
                        class="mt-2 text-[9px] font-semibold leading-tight text-slate-400"
                    >
                        {{ s.sub }}
                    </span>

                </div>

            </div>

        </div>



        <!-- ==================================================
             MAIN CONTENT
        ================================================== -->

        <div
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
        >


            <!-- ==================================================
                 SISWA PERLU PERHATIAN
            ================================================== -->

            <div
                class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-2"
            >

                <div class="space-y-4">


                    <!-- HEADER -->

                    <div
                        class="flex items-center justify-between border-b border-slate-100 pb-3"
                    >

                        <h3
                            class="flex items-center gap-2 text-sm font-extrabold text-slate-800"
                        >

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-pink-50"
                            >

                                <HeartIcon
                                    class="h-4.5 w-4.5 text-pink-500"
                                />

                            </span>

                            <span>
                                Siswa Perlu Perhatian
                            </span>

                        </h3>


                        <span
                            class="rounded-full bg-pink-50 px-2.5 py-1 text-[9px] font-bold text-pink-600"
                        >
                            Monitoring
                        </span>

                    </div>



                    <!-- ADA DATA -->

                    <div
                        v-if="attentionStudents.length"
                        class="divide-y divide-slate-100"
                    >

                        <div
                            v-for="student in attentionStudents"
                            :key="student.id"
                            class="flex items-center justify-between gap-4 rounded-xl py-3 text-xs transition hover:bg-pink-50/40"
                        >

                            <!-- STUDENT -->

                            <div
                                class="min-w-0 space-y-1"
                            >

                                <span
                                    class="block truncate font-extrabold text-slate-800"
                                >
                                    {{ student.name }}
                                </span>

                                <span
                                    class="block text-[10px] font-semibold text-slate-400"
                                >
                                    {{ student.class }}
                                </span>

                            </div>


                            <!-- STATUS -->

                            <div
                                class="flex shrink-0 items-center gap-2"
                            >

                                <span
                                    class="hidden rounded-lg border border-slate-100 bg-slate-50 px-2 py-1 text-[10px] font-semibold text-slate-500 sm:inline-block"
                                >
                                    {{ student.note }}
                                </span>


                                <span
                                    class="rounded-full border border-pink-100 bg-pink-50 px-2.5 py-1 text-[9px] font-extrabold uppercase text-pink-700"
                                >
                                    {{ student.status }}
                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- TIDAK ADA DATA -->

                    <div
                        v-else
                        class="rounded-2xl border border-dashed border-pink-100 bg-pink-50/30 px-4 py-10 text-center"
                    >

                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-pink-50"
                        >

                            <HeartIcon
                                class="h-6 w-6 text-pink-300"
                            />

                        </div>


                        <p
                            class="mt-3 text-sm font-semibold text-slate-400"
                        >
                            Belum ada siswa yang perlu perhatian.
                        </p>

                        <p
                            class="mt-1 text-[10px] font-medium text-slate-400"
                        >
                            Kondisi siswa dalam keadaan terpantau.
                        </p>

                    </div>

                </div>



                <!-- BUTTON -->

                <Link
                    :href="route('klinik.kesehatan.pemeriksaan.index')"
                    class="mt-6 flex w-full items-center justify-center gap-1.5 rounded-xl border border-pink-100 bg-white py-2.5 text-center text-xs font-bold text-pink-600 transition hover:border-pink-200 hover:bg-pink-50"
                >

                    <span>
                        Lihat Pemeriksaan
                    </span>

                    <ArrowRightIcon
                        class="h-3.5 w-3.5"
                    />

                </Link>

            </div>



            <!-- ==================================================
                 RIGHT COLUMN
            ================================================== -->

            <div class="space-y-6">


                <!-- ==================================================
                     AKTIVITAS TERBARU
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                >

                    <div class="space-y-4">


                        <!-- HEADER -->

                        <div
                            class="flex items-center gap-2 border-b border-slate-100 pb-3"
                        >

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-pink-50"
                            >

                                <ClockIcon
                                    class="h-4 w-4 text-pink-500"
                                />

                            </span>

                            <h3
                                class="text-sm font-extrabold text-slate-800"
                            >
                                Aktivitas Terbaru
                            </h3>

                        </div>



                        <!-- DATA -->

                        <div
                            v-if="activities.length"
                            class="space-y-4 text-xs"
                        >

                            <div
                                v-for="(act, idx) in activities"
                                :key="idx"
                                class="flex items-start gap-3"
                            >

                                <!-- TIMELINE ICON -->

                                <div
                                    class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-pink-50"
                                >

                                    <ClockIcon
                                        class="h-4 w-4 text-pink-500"
                                    />

                                </div>


                                <!-- CONTENT -->

                                <div
                                    class="min-w-0 space-y-1"
                                >

                                    <span
                                        class="block font-bold leading-tight text-slate-800"
                                    >
                                        {{ act.text }}
                                    </span>

                                    <span
                                        class="block text-[10px] font-semibold text-slate-500"
                                    >
                                        {{ act.staff }}
                                    </span>

                                    <span
                                        class="block text-[9px] font-bold text-pink-400"
                                    >
                                        {{ act.time }}
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- EMPTY -->

                        <div
                            v-else
                            class="rounded-xl bg-slate-50 py-6 text-center text-xs font-semibold text-slate-400"
                        >
                            Belum ada aktivitas.
                        </div>

                    </div>

                </div>



                <!-- ==================================================
                     PENGINGAT
                ================================================== -->

                <div
                    class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                >

                    <div class="space-y-4">


                        <!-- HEADER -->

                        <div
                            class="flex items-center gap-2 border-b border-slate-100 pb-3"
                        >

                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-rose-50"
                            >

                                <BellIcon
                                    class="h-4 w-4 text-rose-500"
                                />

                            </span>

                            <h3
                                class="text-sm font-extrabold text-slate-800"
                            >
                                Jadwal & Pengingat
                            </h3>

                        </div>



                        <!-- DATA -->

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
                                    class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl bg-rose-50"
                                >

                                    <BellIcon
                                        class="h-4 w-4 text-rose-500"
                                    />

                                </div>


                                <!-- CONTENT -->

                                <div
                                    class="min-w-0 space-y-1"
                                >

                                    <span
                                        class="block font-bold leading-tight text-slate-800"
                                    >
                                        {{ rem.title }}
                                    </span>


                                    <span
                                        class="block text-[10px] font-semibold text-slate-400"
                                    >
                                        Tanggal: {{ rem.date }}
                                    </span>


                                    <span
                                        class="block text-[10px] font-semibold text-slate-400"
                                    >
                                        Batas: {{ rem.deadline }}
                                    </span>


                                    <span
                                        class="mt-1 inline-block rounded-md border border-rose-100 bg-rose-50 px-2 py-1 text-[8px] font-extrabold uppercase tracking-wider text-rose-600"
                                    >
                                        {{ rem.status }}
                                    </span>

                                </div>

                            </div>

                        </div>



                        <!-- EMPTY -->

                        <div
                            v-else
                            class="rounded-xl border border-dashed border-slate-100 bg-slate-50 px-4 py-6 text-center text-xs font-semibold text-slate-400"
                        >
                            Tidak ada pengingat saat ini.
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</KlinikLayout>

</template>