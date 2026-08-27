<script setup>
import { computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import TksiLayout from '@/Layouts/TksiLayout.vue'

import {
    BellIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
    ArrowLeftIcon,
    ArrowTopRightOnSquareIcon,
    ClockIcon,
    UserIcon,
    CheckIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps({
    notification: {
        type: Object,
        required: true,
    },
})

/*
|--------------------------------------------------------------------------
| ROLE
|--------------------------------------------------------------------------
*/

const roleName = computed(() => {
    const roles = {
        admin: 'Admin',
        klinik: 'Klinik',
        tksi: 'TKSI',
    }

    return roles[props.notification.role] ?? props.notification.role ?? '-'
})

/*
|--------------------------------------------------------------------------
| LAYOUT BERDASARKAN ROLE
|--------------------------------------------------------------------------
*/

const currentLayout = computed(() => {
    const layouts = {
        admin: AdminLayout,
        klinik: KlinikLayout,
        tksi: TksiLayout,
    }

    return layouts[props.notification.role] ?? KlinikLayout
})

/*
|--------------------------------------------------------------------------
| STATUS READ
|--------------------------------------------------------------------------
*/

const isRead = computed(() => {
    if (typeof props.notification.read === 'boolean') {
        return props.notification.read
    }

    return (
        props.notification.read_at !== null &&
        props.notification.read_at !== undefined
    )
})

/*
|--------------------------------------------------------------------------
| TYPE
|--------------------------------------------------------------------------
*/

const typeName = computed(() => {
    const types = {
        success: 'Berhasil',
        warning: 'Peringatan',
        danger: 'Penting',
        info: 'Informasi',
    }

    return types[props.notification.type] ?? 'Informasi'
})

/*
|--------------------------------------------------------------------------
| TYPE CLASS
|--------------------------------------------------------------------------
*/

const typeClass = computed(() => {
    const classes = {
        success: {
            icon: 'bg-emerald-100 text-emerald-600',
            badge: 'bg-emerald-50 text-emerald-600',
            border: 'border-emerald-100',
            bg: 'bg-emerald-50/50',
        },

        warning: {
            icon: 'bg-amber-100 text-amber-600',
            badge: 'bg-amber-50 text-amber-600',
            border: 'border-amber-100',
            bg: 'bg-amber-50/50',
        },

        danger: {
            icon: 'bg-red-100 text-red-600',
            badge: 'bg-red-50 text-red-600',
            border: 'border-red-100',
            bg: 'bg-red-50/50',
        },

        info: {
            icon: 'bg-blue-100 text-blue-600',
            badge: 'bg-blue-50 text-blue-600',
            border: 'border-blue-100',
            bg: 'bg-blue-50/50',
        },
    }

    return classes[props.notification.type] ?? classes.info
})

/*
|--------------------------------------------------------------------------
| TYPE ICON
|--------------------------------------------------------------------------
*/

const typeIcon = computed(() => {
    const icons = {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        danger: XCircleIcon,
        info: InformationCircleIcon,
    }

    return icons[props.notification.type] ?? InformationCircleIcon
})

/*
|--------------------------------------------------------------------------
| MARK AS READ
|--------------------------------------------------------------------------
*/

const markAsRead = () => {
    if (isRead.value) {
        return
    }

    router.patch(
        route('notifikasi.read', props.notification.id),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| BACK
|--------------------------------------------------------------------------
*/

const goBack = () => {
    window.history.back()
}
</script>

<template>
    <Head :title="notification.title" />

    <component :is="currentLayout">

        <!--
        ============================================================
        CONTENT
        ============================================================
        -->

        <div class="space-y-6">

            <!-- ================================================== -->
            <!-- HEADER -->
            <!-- ================================================== -->

            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >

                <div class="flex items-center gap-3">

                    <!-- BACK -->
                    <button
                        type="button"
                        @click="goBack"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50"
                        title="Kembali"
                    >
                        <ArrowLeftIcon class="h-4 w-4" />
                    </button>

                    <!-- TITLE -->
                    <div>

                        <h1 class="text-xl font-bold text-slate-800">
                            Detail Notifikasi
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Informasi pemberitahuan untuk akun
                            {{ roleName }}.
                        </p>

                    </div>

                </div>

                <!-- STATUS -->
                <div class="flex items-center gap-2">

                    <span
                        v-if="isRead"
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600"
                    >
                        <CheckIcon class="h-3.5 w-3.5" />
                        Sudah dibaca
                    </span>

                    <button
                        v-else
                        type="button"
                        @click="markAsRead"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-600 transition hover:bg-blue-100"
                    >
                        <CheckIcon class="h-4 w-4" />
                        Tandai dibaca
                    </button>

                </div>

            </div>


            <!-- ================================================== -->
            <!-- NOTIFICATION DETAIL -->
            <!-- ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
            >

                <!-- ================================================== -->
                <!-- TOP HEADER -->
                <!-- ================================================== -->

                <div
                    class="border-b border-slate-100 px-5 py-6 sm:px-6"
                >

                    <div class="flex items-start gap-4">

                        <!-- ICON -->
                        <div
                            class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl"
                            :class="typeClass.icon"
                        >
                            <component
                                :is="typeIcon"
                                class="h-6 w-6"
                            />
                        </div>

                        <!-- TITLE -->
                        <div class="min-w-0 flex-1">

                            <div
                                class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between"
                            >

                                <div>

                                    <h2
                                        class="text-lg font-bold text-slate-800"
                                    >
                                        {{ notification.title }}
                                    </h2>

                                    <p
                                        class="mt-1 text-sm text-slate-400"
                                    >
                                        {{ notification.created_at }}
                                    </p>

                                </div>

                                <!-- TYPE -->
                                <span
                                    class="w-fit rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="typeClass.badge"
                                >
                                    {{ typeName }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================== -->
                <!-- INFO BAR -->
                <!-- ================================================== -->

                <div class="px-5 py-5 sm:px-6">

                    <div
                        class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"
                    >

                        <!-- ROLE -->
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500"
                                >
                                    <UserIcon class="h-4 w-4" />
                                </div>

                                <div>

                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wide text-slate-400"
                                    >
                                        Penerima
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-semibold text-slate-700"
                                    >
                                        {{ roleName }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- STATUS -->
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500"
                                >
                                    <CheckCircleIcon class="h-4 w-4" />
                                </div>

                                <div>

                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wide text-slate-400"
                                    >
                                        Status
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-semibold"
                                        :class="
                                            isRead
                                                ? 'text-emerald-600'
                                                : 'text-blue-600'
                                        "
                                    >
                                        {{
                                            isRead
                                                ? 'Sudah dibaca'
                                                : 'Belum dibaca'
                                        }}
                                    </p>

                                </div>

                            </div>

                        </div>


                        <!-- WAKTU -->
                        <div
                            class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3"
                        >

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-slate-500"
                                >
                                    <ClockIcon class="h-4 w-4" />
                                </div>

                                <div>

                                    <p
                                        class="text-[11px] font-medium uppercase tracking-wide text-slate-400"
                                    >
                                        Waktu
                                    </p>

                                    <p
                                        class="mt-0.5 text-sm font-semibold text-slate-700"
                                    >
                                        {{ notification.created_at }}
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================================== -->
                <!-- MESSAGE -->
                <!-- ================================================== -->

                <div
                    class="border-t border-slate-100 px-5 py-7 sm:px-6"
                >

                    <div class="mb-4">

                        <h3
                            class="text-sm font-bold text-slate-700"
                        >
                            Isi Notifikasi
                        </h3>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Pesan pemberitahuan yang dikirimkan kepada
                            akun {{ roleName }}.
                        </p>

                    </div>


                    <!-- MESSAGE BOX -->
                    <div
                        class="rounded-xl border p-5"
                        :class="[
                            typeClass.border,
                            typeClass.bg,
                        ]"
                    >

                        <div class="flex items-start gap-3">

                            <component
                                :is="typeIcon"
                                class="mt-0.5 h-5 w-5 shrink-0"
                                :class="
                                    notification.type === 'success'
                                        ? 'text-emerald-600'
                                        : notification.type === 'warning'
                                            ? 'text-amber-600'
                                            : notification.type === 'danger'
                                                ? 'text-red-600'
                                                : 'text-blue-600'
                                "
                            />

                            <p
                                class="text-sm leading-7 text-slate-600"
                            >
                                {{ notification.message }}
                            </p>

                        </div>

                    </div>

                </div>


            </div>

        </div>

    </component>
</template>