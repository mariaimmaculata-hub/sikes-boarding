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
                 WELCOME
            ========================================================== -->

            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-900 via-blue-800 to-indigo-900 p-6 text-white shadow-lg md:p-8"
            >

                <div
                    class="pointer-events-none absolute bottom-0 right-0 top-0 hidden w-1/4 opacity-10 md:block"
                >
                    <svg
                        class="h-full w-full text-white"
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


                <div class="relative z-10 space-y-2">

                    <h2
                        class="text-2xl font-bold tracking-tight"
                    >
                        Dashboard TKSI
                    </h2>


                    <p
                        class="text-sm font-medium text-white/80"
                    >

                        Selamat datang,

                        <span class="font-bold">
                            {{ user?.name || 'Petugas TKSI' }}
                        </span>

                        😊

                        Berikut ringkasan pelaksanaan TKSI siswa.

                    </p>


                    <div
                        v-if="periode"
                        class="pt-2"
                    >
                        <span
                            class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white backdrop-blur"
                        >
                            Periode:
                            {{ periode.nama_periode }}
                        </span>
                    </div>

                </div>

            </div>


            <!-- =========================================================
                 STATISTICS
            ========================================================== -->

            <div
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4"
            >

                <div
                    v-for="(s, idx) in stats"
                    :key="idx"
                    class="flex min-h-[145px] flex-col justify-between rounded-2xl border-l-4 bg-white p-5 shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md"
                    :class="s.color"
                >

                    <div class="flex items-center justify-between">

                        <span
                            class="text-[11px] font-bold uppercase tracking-wider text-slate-500"
                        >
                            {{ s.name }}
                        </span>

                    </div>


                    <div class="mt-3 flex flex-col">

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
                     PERLU PERHATIAN
                ====================================================== -->

                <div
                    class="flex flex-col justify-between rounded-2xl border border-slate-100 bg-white p-6 shadow-sm lg:col-span-2"
                >

                    <div class="space-y-4">

                        <h3
                            class="flex items-center gap-1.5 border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                        >

                            <HeartIcon
                                class="h-5 w-5 text-rose-500"
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

                                <div
                                    class="min-w-0 space-y-0.5"
                                >

                                    <span
                                        class="block truncate font-extrabold text-blue-900"
                                    >
                                        {{ student.name }}
                                    </span>


                                    <span
                                        class="block text-[10px] font-bold text-slate-400"
                                    >
                                        {{ student.class }}
                                    </span>

                                </div>


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
                            class="rounded-xl bg-slate-50 px-4 py-8 text-center"
                        >

                            <HeartIcon
                                class="mx-auto h-8 w-8 text-slate-300"
                            />

                            <p
                                class="mt-2 text-sm font-semibold text-slate-400"
                            >
                                Semua peserta TKSI sudah selesai.
                            </p>

                        </div>

                    </div>


                    <Link
                        :href="route('tksi.tksi.index')"
                        class="mt-6 flex w-full items-center justify-center gap-1 rounded-xl border border-slate-200 py-2.5 text-center text-xs font-bold text-slate-700 transition hover:bg-slate-50"
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

                <div class="space-y-6">


                    <!-- =================================================
                         AKTIVITAS
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                    >

                        <div class="space-y-4">

                            <h3
                                class="border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                            >
                                Aktivitas Terbaru
                            </h3>


                            <div
                                v-if="activities.length"
                                class="space-y-4 text-xs"
                            >

                                <div
                                    v-for="(act, idx) in activities"
                                    :key="idx"
                                    class="flex items-start gap-3"
                                >

                                    <div
                                        class="mt-0.5 flex-shrink-0 rounded-lg bg-blue-50 p-1.5 text-blue-600"
                                    >

                                        <ClockIcon
                                            class="h-4 w-4"
                                        />

                                    </div>


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


                            <div
                                v-else
                                class="py-6 text-center text-xs font-semibold text-slate-400"
                            >
                                Belum ada aktivitas TKSI.
                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         REMINDER
                    ================================================== -->

                    <div
                        class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm"
                    >

                        <div class="space-y-4">

                            <h3
                                class="border-b border-slate-100 pb-2 text-sm font-extrabold text-slate-800"
                            >
                                Jadwal & Pengingat
                            </h3>


                            <div
                                v-if="reminders.length"
                                class="space-y-4 text-xs"
                            >

                                <div
                                    v-for="(rem, idx) in reminders"
                                    :key="idx"
                                    class="flex items-start gap-3.5"
                                >

                                    <div
                                        class="mt-0.5 flex-shrink-0 rounded-lg bg-rose-50 p-1.5 text-rose-600"
                                    >

                                        <BellIcon
                                            class="h-4 w-4"
                                        />

                                    </div>


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


                            <div
                                v-else
                                class="rounded-xl bg-slate-50 px-4 py-6 text-center text-xs font-semibold text-slate-400"
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