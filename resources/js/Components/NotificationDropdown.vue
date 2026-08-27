<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    BellIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
    ArrowRightIcon,
} from '@heroicons/vue/24/outline'

/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

const page = usePage()

/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const isOpen = ref(false)

/*
|--------------------------------------------------------------------------
| NOTIFICATION COUNT
|--------------------------------------------------------------------------
*/

const notificationCount = computed(() => {
    return Number(
        page.props.notificationCount ?? 0
    )
})

/*
|--------------------------------------------------------------------------
| RECENT NOTIFICATIONS
|--------------------------------------------------------------------------
*/

const recentNotifications = computed(() => {
    return page.props.recentNotifications ?? []
})

/*
|--------------------------------------------------------------------------
| TYPE CLASS
|--------------------------------------------------------------------------
*/

const typeClass = (type) => {

    return {
        success:
            'bg-emerald-100 text-emerald-600',

        warning:
            'bg-amber-100 text-amber-600',

        danger:
            'bg-red-100 text-red-600',

        info:
            'bg-blue-100 text-blue-600',

    }[type] ?? 'bg-blue-100 text-blue-600'
}

/*
|--------------------------------------------------------------------------
| TYPE ICON
|--------------------------------------------------------------------------
*/

const typeIcon = (type) => {

    return {
        success:
            CheckCircleIcon,

        warning:
            ExclamationTriangleIcon,

        danger:
            XCircleIcon,

        info:
            InformationCircleIcon,

    }[type] ?? InformationCircleIcon
}

/*
|--------------------------------------------------------------------------
| TOGGLE
|--------------------------------------------------------------------------
*/

const toggle = () => {
    isOpen.value =
        !isOpen.value
}

/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

const close = () => {
    isOpen.value = false
}

/*
|--------------------------------------------------------------------------
| CLICK OUTSIDE
|--------------------------------------------------------------------------
*/

const handleClickOutside = (event) => {

    const target = event.target

    if (
        !target.closest(
            '[data-notification-dropdown]'
        )
    ) {
        close()
    }
}

/*
|--------------------------------------------------------------------------
| MOUNT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    document.addEventListener(
        'click',
        handleClickOutside
    )

})

/*
|--------------------------------------------------------------------------
| UNMOUNT
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {

    document.removeEventListener(
        'click',
        handleClickOutside
    )

})
</script>


<template>

    <div
        data-notification-dropdown
        class="relative"
    >

        <!-- ===================================================== -->
        <!-- BELL BUTTON -->
        <!-- ===================================================== -->

        <button
            type="button"
            @click.stop="toggle"
            class="relative rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none"
            title="Notifikasi"
        >

            <BellIcon
                class="h-6 w-6"
            />

            <!-- BADGE UNREAD -->

            <span
                v-if="notificationCount > 0"
                class="absolute right-1 top-1 flex min-h-4 min-w-4 items-center justify-center rounded-full border border-white bg-rose-500 px-1 text-[9px] font-bold text-white"
            >
                {{
                    notificationCount > 99
                        ? '99+'
                        : notificationCount
                }}
            </span>

        </button>


        <!-- ===================================================== -->
        <!-- DROPDOWN -->
        <!-- ===================================================== -->

        <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >

            <div
                v-if="isOpen"
                class="absolute right-0 top-full z-50 mt-3 w-[360px] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
            >

                <!-- ================================================= -->
                <!-- HEADER -->
                <!-- ================================================= -->

                <div
                    class="flex items-center justify-between border-b border-slate-100 px-4 py-4"
                >

                    <div>

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Notifikasi
                        </h3>

                        <p
                            class="mt-0.5 text-[11px] text-slate-400"
                        >
                            {{ notificationCount }}
                            belum dibaca
                        </p>

                    </div>


                    <Link
                        href="/notifikasi"
                        @click="close"
                        class="text-xs font-semibold text-blue-600 transition hover:text-blue-700"
                    >
                        Lihat semua
                    </Link>

                </div>


                <!-- ================================================= -->
                <!-- LIST -->
                <!-- ================================================= -->

                <div
                    v-if="recentNotifications.length"
                    class="max-h-[390px] overflow-y-auto"
                >

                    <Link
                        v-for="notification in recentNotifications"
                        :key="notification.id"
                        :href="
                            route(
                                'notifikasi.show',
                                notification.id
                            )
                        "
                        @click="close"
                        class="flex gap-3 border-b border-slate-100 px-4 py-4 text-left transition last:border-b-0 hover:bg-slate-50"
                        :class="{
                            'bg-blue-50/40':
                                !notification.read,
                        }"
                    >

                        <!-- ICON -->

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                            :class="
                                typeClass(
                                    notification.type
                                )
                            "
                        >

                            <component
                                :is="
                                    typeIcon(
                                        notification.type
                                    )
                                "
                                class="h-5 w-5"
                            />

                        </div>


                        <!-- CONTENT -->

                        <div
                            class="min-w-0 flex-1"
                        >

                            <div
                                class="flex items-start justify-between gap-2"
                            >

                                <h4
                                    class="line-clamp-1 text-xs font-bold text-slate-700"
                                >
                                    {{ notification.title }}
                                </h4>


                                <!-- UNREAD DOT -->

                                <span
                                    v-if="!notification.read"
                                    class="mt-1 h-2 w-2 shrink-0 rounded-full bg-blue-500"
                                />

                            </div>


                            <p
                                class="mt-1 line-clamp-2 text-[11px] leading-5 text-slate-500"
                            >
                                {{ notification.message }}
                            </p>


                            <div
                                class="mt-2 flex items-center justify-between gap-2"
                            >

                                <span
                                    class="text-[10px] font-medium text-slate-400"
                                >
                                    {{ notification.created_at }}
                                </span>


                                <span
                                    class="rounded-full px-2 py-0.5 text-[9px] font-semibold capitalize"
                                    :class="
                                        typeClass(
                                            notification.type
                                        )
                                    "
                                >
                                    {{ notification.type }}
                                </span>

                            </div>

                        </div>

                    </Link>

                </div>


                <!-- ================================================= -->
                <!-- EMPTY -->
                <!-- ================================================= -->

                <div
                    v-else
                    class="px-5 py-10 text-center"
                >

                    <BellIcon
                        class="mx-auto h-9 w-9 text-slate-300"
                    />

                    <p
                        class="mt-3 text-xs font-semibold text-slate-500"
                    >
                        Tidak ada notifikasi
                    </p>

                    <p
                        class="mt-1 text-[11px] text-slate-400"
                    >
                        Belum ada pemberitahuan.
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- FOOTER -->
                <!-- ================================================= -->

                <div
                    class="border-t border-slate-100 bg-slate-50 px-4 py-3"
                >

                    <Link
                        href="/notifikasi"
                        @click="close"
                        class="flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-100"
                    >

                        Semua Notifikasi

                        <ArrowRightIcon
                            class="h-4 w-4"
                        />

                    </Link>

                </div>

            </div>

        </transition>

    </div>

</template>