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

const page = usePage()

const isOpen = ref(false)

const notificationCount = computed(() => {
    return Number(page.props.notificationCount ?? 0)
})

const recentNotifications = computed(() => {
    return page.props.recentNotifications ?? []
})

const typeClass = (type) => {
    return {
        success: 'bg-emerald-100 text-emerald-600',
        warning: 'bg-amber-100 text-amber-600',
        danger: 'bg-red-100 text-red-600',
        info: 'bg-blue-100 text-blue-600',
    }[type] ?? 'bg-blue-100 text-blue-600'
}

const typeIcon = (type) => {
    return {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        danger: XCircleIcon,
        info: InformationCircleIcon,
    }[type] ?? InformationCircleIcon
}

const toggle = () => {
    isOpen.value = !isOpen.value
}

const close = () => {
    isOpen.value = false
}

const handleClickOutside = (event) => {
    const target = event.target

    if (!target.closest('[data-notification-dropdown]')) {
        close()
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div
        data-notification-dropdown
        class="relative"
    >

        <!-- ================================================= -->
        <!-- BELL -->
        <!-- ================================================= -->

        <button
            type="button"
            @click.stop="toggle"
            class="relative rounded-full p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600 focus:outline-none"
            title="Notifikasi"
        >
            <BellIcon class="h-5 w-5 sm:h-6 sm:w-6" />

            <span
                v-if="notificationCount > 0"
                class="absolute right-0.5 top-0.5 flex min-h-3.5 min-w-3.5 items-center justify-center rounded-full border border-white bg-rose-500 px-0.5 text-[8px] font-bold leading-none text-white sm:right-1 sm:top-1 sm:min-h-4 sm:min-w-4 sm:px-1 sm:text-[9px]"
            >
                {{
                    notificationCount > 99
                        ? '99+'
                        : notificationCount
                }}
            </span>
        </button>


        <!-- ================================================= -->
        <!-- DROPDOWN -->
        <!-- ================================================= -->

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="translate-y-1 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0"
        >

            <div
                v-if="isOpen"
                class="
                    absolute right-0 top-full z-50 mt-2
                    w-[min(260px,calc(100vw-1.25rem))]
                    overflow-hidden
                    rounded-xl
                    border border-slate-200
                    bg-white
                    shadow-xl

                    sm:w-[300px]
                    sm:rounded-2xl

                    md:w-[320px]

                    lg:w-[360px]
                "
            >

                <!-- ================================================= -->
                <!-- HEADER -->
                <!-- ================================================= -->

                <div
                    class="
                        flex items-center justify-between
                        border-b border-slate-100
                        px-3 py-2.5

                        sm:px-4 sm:py-3
                    "
                >

                    <div class="min-w-0">

                        <h3
                            class="
                                text-xs font-bold text-slate-800
                                sm:text-sm
                            "
                        >
                            Notifikasi
                        </h3>

                        <p
                            class="
                                mt-0.5 text-[9px] text-slate-400
                                sm:text-[10px]
                            "
                        >
                            {{ notificationCount }} belum dibaca
                        </p>

                    </div>

                    <Link
                        href="/notifikasi"
                        @click="close"
                        class="
                            shrink-0
                            text-[9px] font-semibold text-blue-600
                            hover:text-blue-700
                            sm:text-[11px]
                        "
                    >
                        Lihat semua
                    </Link>

                </div>


                <!-- ================================================= -->
                <!-- LIST -->
                <!-- ================================================= -->

                <div
                    v-if="recentNotifications.length"
                    class="
                        max-h-[280px]
                        overflow-y-auto

                        sm:max-h-[340px]

                        lg:max-h-[390px]
                    "
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
                        class="
                            flex gap-2
                            border-b border-slate-100
                            px-3 py-2.5
                            text-left
                            transition
                            last:border-b-0
                            hover:bg-slate-50

                            sm:gap-3
                            sm:px-4
                            sm:py-3
                        "
                        :class="{
                            'bg-blue-50/40':
                                !notification.read,
                        }"
                    >

                        <!-- ICON -->

                        <div
                            class="
                                flex h-8 w-8 shrink-0
                                items-center justify-center
                                rounded-lg

                                sm:h-9 sm:w-9
                                sm:rounded-xl

                                lg:h-10 lg:w-10
                            "
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
                                class="
                                    h-4 w-4

                                    sm:h-4.5 sm:w-4.5

                                    lg:h-5 lg:w-5
                                "
                            />

                        </div>


                        <!-- CONTENT -->

                        <div class="min-w-0 flex-1">

                            <div
                                class="
                                    flex items-start
                                    justify-between
                                    gap-1.5
                                "
                            >

                                <h4
                                    class="
                                        line-clamp-1
                                        text-[10px]
                                        font-bold
                                        text-slate-700

                                        sm:text-[11px]

                                        lg:text-xs
                                    "
                                >
                                    {{ notification.title }}
                                </h4>

                                <span
                                    v-if="!notification.read"
                                    class="
                                        mt-0.5
                                        h-1.5 w-1.5
                                        shrink-0
                                        rounded-full
                                        bg-blue-500

                                        sm:h-2 sm:w-2
                                    "
                                />

                            </div>


                            <p
                                class="
                                    mt-0.5
                                    line-clamp-2
                                    text-[9px]
                                    leading-4
                                    text-slate-500

                                    sm:text-[10px]
                                    sm:leading-4.5

                                    lg:text-[11px]
                                    lg:leading-5
                                "
                            >
                                {{ notification.message }}
                            </p>


                            <div
                                class="
                                    mt-1.5
                                    flex
                                    items-center
                                    justify-between
                                    gap-1.5

                                    sm:mt-2
                                "
                            >

                                <span
                                    class="
                                        min-w-0
                                        truncate
                                        text-[8px]
                                        font-medium
                                        text-slate-400

                                        sm:text-[9px]

                                        lg:text-[10px]
                                    "
                                >
                                    {{ notification.created_at }}
                                </span>

                                <span
                                    class="
                                        shrink-0
                                        rounded-full
                                        px-1.5 py-0.5
                                        text-[7px]
                                        font-semibold
                                        capitalize

                                        sm:px-2
                                        sm:text-[8px]

                                        lg:text-[9px]
                                    "
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
                    class="
                        px-4 py-7
                        text-center

                        sm:px-5 sm:py-9
                    "
                >

                    <BellIcon
                        class="
                            mx-auto
                            h-7 w-7
                            text-slate-300

                            sm:h-9 sm:w-9
                        "
                    />

                    <p
                        class="
                            mt-2
                            text-[10px]
                            font-semibold
                            text-slate-500

                            sm:mt-3
                            sm:text-xs
                        "
                    >
                        Tidak ada notifikasi
                    </p>

                    <p
                        class="
                            mt-0.5
                            text-[9px]
                            text-slate-400

                            sm:mt-1
                            sm:text-[10px]
                        "
                    >
                        Belum ada pemberitahuan.
                    </p>

                </div>


                <!-- ================================================= -->
                <!-- FOOTER -->
                <!-- ================================================= -->

                <div
                    class="
                        border-t border-slate-100
                        bg-slate-50
                        px-3 py-2

                        sm:px-4 sm:py-2.5
                    "
                >

                    <Link
                        href="/notifikasi"
                        @click="close"
                        class="
                            flex
                            items-center
                            justify-center
                            gap-1.5
                            rounded-lg
                            bg-white
                            px-3 py-2
                            text-[9px]
                            font-semibold
                            text-slate-600
                            transition
                            hover:bg-slate-100

                            sm:gap-2
                            sm:rounded-xl
                            sm:px-4
                            sm:py-2.5
                            sm:text-[10px]

                            lg:text-xs
                        "
                    >

                        Semua Notifikasi

                        <ArrowRightIcon
                            class="
                                h-3 w-3

                                sm:h-3.5 sm:w-3.5

                                lg:h-4 lg:w-4
                            "
                        />

                    </Link>

                </div>

            </div>

        </transition>

    </div>
</template>
