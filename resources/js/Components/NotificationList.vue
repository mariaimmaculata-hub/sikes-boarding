<script setup>
import {
    BellIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
} from '@heroicons/vue/24/outline'

defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },
})

function typeClass(type) {

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

function iconComponent(type) {

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

    <div
        v-if="!notifications.length"
        class="rounded-2xl border border-slate-200 bg-white px-6 py-14 text-center"
    >

        <BellIcon
            class="mx-auto h-10 w-10 text-slate-300"
        />

        <h3
            class="mt-3 text-sm font-bold text-slate-600"
        >
            Tidak ada notifikasi
        </h3>

        <p
            class="mt-1 text-xs text-slate-400"
        >
            Belum ada informasi terbaru.
        </p>

    </div>


    <div
        v-else
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
    >

        <div
            v-for="notification in notifications"
            :key="notification.id"
            class="flex gap-4 border-b border-slate-100 px-5 py-4 last:border-b-0"
            :class="{
                'bg-blue-50/40':
                    !notification.read_at
            }"
        >

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                :class="typeClass(notification.type)"
            >

                <component
                    :is="iconComponent(notification.type)"
                    class="h-5 w-5"
                />

            </div>


            <div class="min-w-0 flex-1">

                <div
                    class="flex items-start justify-between gap-3"
                >

                    <h3
                        class="text-sm font-bold text-slate-700"
                    >
                        {{ notification.title }}
                    </h3>

                    <span
                        v-if="!notification.read_at"
                        class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"
                    />

                </div>


                <p
                    class="mt-1 text-sm leading-6 text-slate-500"
                >
                    {{ notification.message }}
                </p>


                <p
                    class="mt-2 text-[11px] text-slate-400"
                >
                    {{ notification.created_at }}
                </p>

            </div>

        </div>

    </div>

</template>