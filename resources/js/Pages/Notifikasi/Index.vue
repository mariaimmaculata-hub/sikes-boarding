<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'

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
} from '@heroicons/vue/24/outline'

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },

    unreadCount: {
        type: Number,
        default: 0,
    },

    role: {
        type: String,
        default: '',
    },
})

/*
|--------------------------------------------------------------------------
| Role
|--------------------------------------------------------------------------
*/

const roleName = computed(() => {
    const roles = {
        admin: 'Admin',
        klinik: 'Klinik',
        tksi: 'TKSI',
    }

    return roles[props.role] ?? props.role
})

/*
|--------------------------------------------------------------------------
| Layout sesuai role
|--------------------------------------------------------------------------
*/

const currentLayout = computed(() => {
    if (props.role === 'admin') {
        return AdminLayout
    }

    if (props.role === 'klinik') {
        return KlinikLayout
    }

    if (props.role === 'tksi') {
        return TksiLayout
    }

    return KlinikLayout
})

/*
|--------------------------------------------------------------------------
| Title
|--------------------------------------------------------------------------
*/

const pageTitle = computed(() => {
    return `Notifikasi ${roleName.value}`
})

/*
|--------------------------------------------------------------------------
| Notification Type
|--------------------------------------------------------------------------
*/

const typeClass = (type) => {
    if (type === 'success') {
        return 'bg-emerald-100 text-emerald-600'
    }

    if (type === 'warning') {
        return 'bg-amber-100 text-amber-600'
    }

    if (type === 'danger') {
        return 'bg-red-100 text-red-600'
    }

    return 'bg-blue-100 text-blue-600'
}

const typeIcon = (type) => {
    if (type === 'success') {
        return CheckCircleIcon
    }

    if (type === 'warning') {
        return ExclamationTriangleIcon
    }

    if (type === 'danger') {
        return XCircleIcon
    }

    return InformationCircleIcon
}
</script>

<template>
    <Head :title="pageTitle" />

    <!-- Layout sesuai role -->
    <component :is="currentLayout">
        <div class="space-y-6">

            <!-- HEADER -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">

                    <Link
                        href="/dashboard"
                        class="flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50"
                    >
                        <ArrowLeftIcon class="h-4 w-4" />
                    </Link>

                    <div>
                        <h1 class="text-xl font-bold text-slate-800">
                            Notifikasi
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Informasi dan pemberitahuan untuk
                            {{ roleName }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- ROLE INFO -->
            <div
                class="rounded-2xl border border-blue-100 bg-blue-50 px-5 py-4"
            >
                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600"
                    >
                        <BellIcon class="h-5 w-5" />
                    </div>

                    <div>
                        <p class="text-sm font-bold text-blue-800">
                            Notifikasi {{ roleName }}
                        </p>

                        <p class="mt-0.5 text-xs text-blue-600">
                            Menampilkan informasi yang berkaitan dengan
                            aktivitas {{ roleName }}.
                        </p>
                    </div>

                </div>
            </div>

            <!-- EMPTY -->
            <div
                v-if="!notifications.length"
                class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center"
            >
                <BellIcon
                    class="mx-auto h-10 w-10 text-slate-300"
                />

                <h3 class="mt-3 text-sm font-bold text-slate-600">
                    Tidak ada notifikasi
                </h3>

                <p class="mt-1 text-xs text-slate-400">
                    Belum ada pemberitahuan untuk akun Anda.
                </p>
            </div>

            <!-- NOTIFICATION LIST -->
            <div
                v-else
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
            >

                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="flex gap-4 border-b border-slate-100 px-5 py-5 transition last:border-b-0 hover:bg-slate-50"
                    :class="{
                        'bg-blue-50/30': !notification.read
                    }"
                >

                    <!-- ICON -->
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                        :class="typeClass(notification.type)"
                    >
                        <component
                            :is="typeIcon(notification.type)"
                            class="h-5 w-5"
                        />
                    </div>

                    <!-- CONTENT -->
                    <div class="min-w-0 flex-1">

                        <div
                            class="flex items-start justify-between gap-3"
                        >

                            <div>

                                <h3
                                    class="text-sm font-bold text-slate-700"
                                >
                                    {{ notification.title }}
                                </h3>

                                <p
                                    class="mt-1 text-sm leading-6 text-slate-500"
                                >
                                    {{ notification.message }}
                                </p>

                            </div>

                            <!-- UNREAD -->
                            <span
                                v-if="!notification.read"
                                class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"
                            />

                        </div>

                        <p
                            class="mt-2 text-[11px] font-medium text-slate-400"
                        >
                            {{ notification.created_at }}
                        </p>

                    </div>

                </div>

            </div>

        </div>
    </component>
</template>