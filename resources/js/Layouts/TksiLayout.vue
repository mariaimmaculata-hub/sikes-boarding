<script setup>

import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'

import {
    HomeIcon,
    ClipboardDocumentListIcon,
    BookOpenIcon,
    ChartBarIcon,
    BellIcon,
    Bars3Icon,
    XMarkIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    UserIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PAGE
// ======================================================

const page = usePage()

const currentUrl = computed(() => page.url)


// ======================================================
// NOTIFICATION
// ======================================================

const notificationCount = computed(() =>
    Number(page.props.notificationCount ?? 0)
)

let notificationTimer = null

onMounted(() => {

    notificationTimer = window.setInterval(() => {

        router.reload({
            only: ['notificationCount'],
            preserveScroll: true,
            preserveState: true,
        })

    }, 10000)

})

onBeforeUnmount(() => {

    if (notificationTimer) {
        window.clearInterval(notificationTimer)
    }

})


// ======================================================
// STATE
// ======================================================

const isMobileSidebarOpen = ref(false)

const isProfileDropdownOpen = ref(false)

const isTksiAccordionOpen = ref(
    currentUrl.value.startsWith('/tksi')
)


// ======================================================
// USER
// ======================================================

const user = computed(() =>
    page.props.auth?.user
)


// ======================================================
// TOGGLE
// ======================================================

const toggleMobileSidebar = () => {

    isMobileSidebarOpen.value =
        !isMobileSidebarOpen.value

}

const toggleProfileDropdown = () => {

    isProfileDropdownOpen.value =
        !isProfileDropdownOpen.value

}

const toggleTksiAccordion = () => {

    isTksiAccordionOpen.value =
        !isTksiAccordionOpen.value

}


// ======================================================
// ACTIVE ROUTE
// ======================================================

const isActive = (path) => {

    return (
        currentUrl.value === path ||
        currentUrl.value.startsWith(`${path}/`)
    )

}


// ======================================================
// CEK ID / UUID
// ======================================================

const isIdentifier = (segment) => {

    if (/^\d+$/.test(segment)) {
        return true
    }

    const uuidRegex =
        /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i

    return uuidRegex.test(segment)

}


// ======================================================
// BREADCRUMB MAP
// ======================================================

const breadcrumbMap = {

    // TKSI
    tksi: 'TKSI',
    panduan: 'Panduan TKSI',
    input: 'Input TKSI',
    report: 'Report Periode',

    // NOTIFIKASI
    notifikasi: 'Notifikasi',

    // ACTION
    create: 'Tambah Data',
    edit: 'Ubah Data',
    show: 'Detail',

}


// ======================================================
// BREADCRUMB
// ======================================================

const breadcrumbs = computed(() => {

    const segments = currentUrl.value
        .split('?')[0]
        .split('/')
        .filter(Boolean)

    const list = [
        {
            name: 'Dashboard',
            url: '/tksi/dashboard',
        },
    ]

    let currentPath = ''

    for (
        let index = 0;
        index < segments.length;
        index++
    ) {

        const segment = segments[index]

        currentPath += `/${segment}`


        // Jangan tampilkan "tksi"
        if (segment === 'tksi') {
            continue
        }


        // Jangan tampilkan dashboard
        if (segment === 'dashboard') {
            continue
        }


        // Jangan tampilkan ID / UUID
        if (isIdentifier(segment)) {
            continue
        }


        const name =
            breadcrumbMap[segment] ??
            segment
                .replace(/[-_]/g, ' ')
                .replace(/\b\w/g, char =>
                    char.toUpperCase()
                )


        // Hindari nama breadcrumb duplikat
        const alreadyExists = list.some(
            item => item.name === name
        )

        if (alreadyExists) {
            continue
        }


        list.push({
            name,
            url: currentPath,
        })

    }


    // Notifikasi
    if (
        currentUrl.value.startsWith('/notifikasi')
    ) {

        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Notifikasi',
                url: currentUrl.value,
            },
        ]

    }


    return list

})

</script>


<template>

    <div
        class="min-h-screen bg-pink-50/40 flex flex-col font-sans text-slate-800 antialiased"
    >

        <!-- ==================================================
             TOP NAVBAR
        ================================================== -->

        <header
            class="bg-white border-b border-pink-100 h-16 fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 lg:px-6 shadow-sm"
        >

            <!-- LEFT -->

            <div class="flex items-center space-x-3">

                <!-- MOBILE MENU -->

                <button
                    type="button"
                    @click="toggleMobileSidebar"
                    class="lg:hidden p-2 rounded-lg text-pink-500 hover:bg-pink-50 focus:outline-none transition"
                    aria-label="Buka menu"
                >

                    <Bars3Icon class="w-6 h-6" />

                </button>


                <!-- LOGO -->

                <div
                    class="flex items-center space-x-3 pl-2 lg:pl-0"
                >

                    <div
                        class="bg-pink-600 p-1.5 rounded-lg flex items-center justify-center shadow-sm"
                    >

                        <svg
                            class="w-6 h-6 text-white"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                            />

                        </svg>

                    </div>


                    <div class="flex flex-col">

                        <span
                            class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight leading-tight"
                        >
                            SIKES BOARDING
                        </span>

                        <span
                            class="text-[9px] sm:text-xs text-slate-500 font-medium"
                        >
                            Sistem Kesehatan & Kebugaran Siswa Boarding
                        </span>

                    </div>

                </div>

            </div>


            <!-- RIGHT -->

            <div class="flex items-center space-x-4">

                <!-- NOTIFICATION -->

                <NotificationDropdown />


                <!-- PROFILE -->

                <div class="relative">

                    <button
                        type="button"
                        @click="toggleProfileDropdown"
                        class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-pink-50 transition focus:outline-none"
                    >

                        <!-- AVATAR -->

                        <div
                            class="w-8 h-8 rounded-full bg-pink-100 text-pink-700 font-bold flex items-center justify-center text-sm border border-pink-200"
                        >

                            {{
                                user?.name
                                    ?.charAt(0)
                                    ?.toUpperCase() || 'T'
                            }}

                        </div>


                        <!-- USER NAME -->

                        <div
                            class="hidden sm:flex flex-col items-start"
                        >

                            <span
                                class="text-sm font-semibold text-slate-700"
                            >

                                {{
                                    user?.name || 'TKSI'
                                }}

                            </span>

                            <span
                                class="text-[10px] font-medium text-slate-400"
                            >
                                TKSI
                            </span>

                        </div>


                        <ChevronDownIcon
                            class="w-4 h-4 text-slate-400 transition duration-200"
                            :class="{
                                'rotate-180':
                                    isProfileDropdownOpen
                            }"
                        />

                    </button>


                    <!-- PROFILE DROPDOWN -->

                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >

                        <div
                            v-if="isProfileDropdownOpen"
                            class="absolute right-0 mt-2 w-48 bg-white border border-pink-100 rounded-2xl shadow-xl py-2 z-50"
                        >

                            <!-- PROFILE -->

                            <Link
                                href="/profile"
                                class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-700 transition font-medium"
                                @click="isProfileDropdownOpen = false"
                            >

                                <UserIcon
                                    class="w-4 h-4 text-pink-400"
                                />

                                <span>
                                    Kelola Profil
                                </span>

                            </Link>


                            <!-- LOGOUT -->

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full text-left flex items-center space-x-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition font-bold border-t border-pink-100"
                            >

                                <svg
                                    class="w-4 h-4 text-rose-500"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 013-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    />

                                </svg>

                                <span>
                                    Logout
                                </span>

                            </Link>

                        </div>

                    </transition>

                </div>

            </div>

        </header>


        <!-- ==================================================
             PAGE BODY
        ================================================== -->

        <div class="flex flex-row pt-16 flex-grow">


            <!-- ==================================================
                 DESKTOP SIDEBAR
            ================================================== -->

            <aside
                class="bg-gradient-to-b from-pink-700 via-pink-700 to-rose-800 text-white w-64 fixed top-16 bottom-0 left-0 z-20 hidden lg:flex flex-col justify-between border-r border-pink-800 shadow-xl overflow-y-auto"
            >

                <div class="py-6 px-4">

                    <!-- TITLE -->

                    <div class="px-3 mb-6">

                        <span
                            class="text-[10px] font-bold text-pink-100 uppercase tracking-widest block"
                        >
                            Menu Navigasi
                        </span>

                        <span
                            class="text-xs text-pink-100/70 font-medium tracking-wide"
                        >
                            TKSI Panel
                        </span>

                    </div>


                    <!-- NAV -->

                    <nav class="space-y-1">


                        <!-- ==================================================
                             DASHBOARD
                        ================================================== -->

                        <Link
                            href="/tksi/dashboard"
                            :class="[
                                'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/tksi/dashboard')
                                    ? 'bg-white text-pink-700 shadow-md'
                                    : 'text-pink-50 hover:bg-white/10 hover:text-white'
                            ]"
                        >

                            <HomeIcon class="w-5 h-5" />

                            <span>
                                Dashboard
                            </span>

                        </Link>


                        <!-- ==================================================
                             TKSI
                        ================================================== -->

                        <div>

                            <button
                                type="button"
                                @click="toggleTksiAccordion"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                    currentUrl.startsWith('/tksi')
                                        ? 'text-white bg-white/15'
                                        : 'text-pink-50 hover:bg-white/10 hover:text-white'
                                ]"
                            >

                                <div
                                    class="flex items-center space-x-3"
                                >

                                    <ClipboardDocumentListIcon
                                        class="w-5 h-5"
                                    />

                                    <span>
                                        TKSI
                                    </span>

                                </div>


                                <ChevronRightIcon
                                    class="w-4 h-4 text-pink-100 transition-transform duration-200"
                                    :class="{
                                        'rotate-90':
                                            isTksiAccordionOpen
                                    }"
                                />

                            </button>


                            <!-- SUBMENU -->

                            <div
                                v-show="isTksiAccordionOpen"
                                class="mt-1 pl-10 pr-2 space-y-1"
                            >

                                <!-- PANDUAN -->

                                <Link
                                    href="/tksi/panduan"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/tksi/panduan')
                                            ? 'text-pink-700 bg-white'
                                            : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                    ]"
                                >

                                    <BookOpenIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Panduan
                                    </span>

                                </Link>


                                <!-- INPUT -->

                                <Link
                                    href="/tksi/input"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/tksi/input')
                                            ? 'text-pink-700 bg-white'
                                            : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                    ]"
                                >

                                    <ClipboardDocumentListIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Input TKSI
                                    </span>

                                </Link>


                                <!-- REPORT -->

                                <Link
                                    href="/tksi/report"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/tksi/report')
                                            ? 'text-pink-700 bg-white'
                                            : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                    ]"
                                >

                                    <ChartBarIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Report Periode
                                    </span>

                                </Link>

                            </div>

                        </div>


                        <!-- ==================================================
                             NOTIFIKASI
                        ================================================== -->

                        <Link
                            href="/notifikasi"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/notifikasi')
                                    ? 'bg-white text-pink-700 shadow-md'
                                    : 'text-pink-50 hover:bg-white/10 hover:text-white'
                            ]"
                        >

                            <div
                                class="flex items-center space-x-3"
                            >

                                <BellIcon class="w-5 h-5" />

                                <span>
                                    Notifikasi
                                </span>

                            </div>


                            <span
                                v-if="notificationCount > 0"
                                class="bg-white text-pink-600 text-[9px] font-bold min-w-5 h-5 px-1 flex items-center justify-center rounded-full shadow-sm"
                            >

                                {{
                                    notificationCount > 99
                                        ? '99+'
                                        : notificationCount
                                }}

                            </span>

                        </Link>

                    </nav>

                </div>


                <!-- FOOTER -->

                <div
                    class="p-4 border-t border-white/10 text-center text-xs text-pink-100/60 font-medium"
                >

                    &copy; 2026 SMKN Jateng Semarang

                </div>

            </aside>


            <!-- ==================================================
                 MOBILE SIDEBAR
            ================================================== -->

            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-x-full"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-full"
            >

                <div
                    v-if="isMobileSidebarOpen"
                    class="fixed inset-0 z-40 lg:hidden flex"
                >

                    <!-- BACKDROP -->

                    <div
                        @click="toggleMobileSidebar"
                        class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                    ></div>


                    <!-- DRAWER -->

                    <aside
                        class="relative bg-gradient-to-b from-pink-700 via-pink-700 to-rose-800 text-white w-64 flex flex-col justify-between shadow-2xl z-50"
                    >

                        <div
                            class="py-6 px-4 overflow-y-auto"
                        >

                            <!-- HEADER -->

                            <div
                                class="flex items-center justify-between px-2 mb-6"
                            >

                                <div>

                                    <span
                                        class="text-xs font-bold text-pink-100 uppercase tracking-widest block"
                                    >
                                        Menu Navigasi
                                    </span>

                                    <span
                                        class="text-[10px] text-pink-100/70"
                                    >
                                        TKSI Panel
                                    </span>

                                </div>


                                <button
                                    type="button"
                                    @click="toggleMobileSidebar"
                                    class="p-1 rounded-lg hover:bg-white/10 text-white"
                                >

                                    <XMarkIcon
                                        class="w-5 h-5"
                                    />

                                </button>

                            </div>


                            <!-- MOBILE NAV -->

                            <nav class="space-y-1">


                                <!-- DASHBOARD -->

                                <Link
                                    href="/tksi/dashboard"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/tksi/dashboard')
                                            ? 'bg-white text-pink-700'
                                            : 'text-pink-50 hover:bg-white/10'
                                    ]"
                                >

                                    <HomeIcon class="w-5 h-5" />

                                    <span>
                                        Dashboard
                                    </span>

                                </Link>


                                <!-- TKSI -->

                                <div>

                                    <button
                                        type="button"
                                        @click="toggleTksiAccordion"
                                        :class="[
                                            'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                            currentUrl.startsWith('/tksi')
                                                ? 'text-white bg-white/15'
                                                : 'text-pink-50 hover:bg-white/10'
                                        ]"
                                    >

                                        <div
                                            class="flex items-center space-x-3"
                                        >

                                            <ClipboardDocumentListIcon
                                                class="w-5 h-5"
                                            />

                                            <span>
                                                TKSI
                                            </span>

                                        </div>


                                        <ChevronRightIcon
                                            class="w-4 h-4 text-pink-100 transition-transform"
                                            :class="{
                                                'rotate-90':
                                                    isTksiAccordionOpen
                                            }"
                                        />

                                    </button>


                                    <!-- SUBMENU -->

                                    <div
                                        v-show="isTksiAccordionOpen"
                                        class="mt-1 pl-10 pr-2 space-y-1"
                                    >

                                        <!-- PANDUAN -->

                                        <Link
                                            href="/tksi/panduan"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/tksi/panduan')
                                                    ? 'text-pink-700 bg-white'
                                                    : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                            ]"
                                        >

                                            <BookOpenIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Panduan
                                            </span>

                                        </Link>


                                        <!-- INPUT -->

                                        <Link
                                            href="/tksi/input"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/tksi/input')
                                                    ? 'text-pink-700 bg-white'
                                                    : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                            ]"
                                        >

                                            <ClipboardDocumentListIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Input TKSI
                                            </span>

                                        </Link>


                                        <!-- REPORT -->

                                        <Link
                                            href="/tksi/report"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/tksi/report')
                                                    ? 'text-pink-700 bg-white'
                                                    : 'text-pink-100/90 hover:text-white hover:bg-white/10'
                                            ]"
                                        >

                                            <ChartBarIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Report Periode
                                            </span>

                                        </Link>

                                    </div>

                                </div>


                                <!-- NOTIFIKASI -->

                                <Link
                                    href="/notifikasi"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/notifikasi')
                                            ? 'bg-white text-pink-700'
                                            : 'text-pink-50 hover:bg-white/10'
                                    ]"
                                >

                                    <div
                                        class="flex items-center space-x-3"
                                    >

                                        <BellIcon class="w-5 h-5" />

                                        <span>
                                            Notifikasi
                                        </span>

                                    </div>


                                    <span
                                        v-if="notificationCount > 0"
                                        class="bg-white text-pink-600 text-[9px] font-bold min-w-5 h-5 px-1 flex items-center justify-center rounded-full"
                                    >

                                        {{
                                            notificationCount > 99
                                                ? '99+'
                                                : notificationCount
                                        }}

                                    </span>

                                </Link>

                            </nav>

                        </div>


                        <!-- FOOTER -->

                        <div
                            class="p-4 border-t border-white/10 text-center text-xs text-pink-100/60"
                        >

                            &copy; 2026 SMKN Jateng Semarang

                        </div>

                    </aside>

                </div>

            </transition>


            <!-- ==================================================
                 MAIN CONTENT
            ================================================== -->

            <main
                class="flex-grow lg:pl-64 flex flex-col justify-between min-h-[calc(100vh-4rem)] min-w-0 overflow-x-auto"
            >

                <div
                    class="p-4 lg:p-6 space-y-6"
                >

                    <!-- BREADCRUMB -->

                    <nav
                        class="flex flex-wrap items-center text-xs font-semibold text-slate-500 gap-x-2 gap-y-1"
                        aria-label="Breadcrumb"
                    >

                        <template
                            v-for="(crumb, idx) in breadcrumbs"
                            :key="`${crumb.url}-${idx}`"
                        >

                            <span
                                v-if="idx > 0"
                                class="text-pink-200"
                            >
                                /
                            </span>


                            <Link
                                v-if="idx < breadcrumbs.length - 1"
                                :href="crumb.url"
                                class="hover:text-pink-600 transition"
                            >

                                {{ crumb.name }}

                            </Link>


                            <span
                                v-else
                                class="text-pink-700 font-bold"
                            >

                                {{ crumb.name }}

                            </span>

                        </template>

                    </nav>


                    <!-- PAGE CONTENT -->

                    <slot />

                </div>


                <!-- FOOTER -->

                <footer
                    class="bg-white border-t border-pink-100 py-4 text-center text-xs font-semibold text-slate-400"
                >

                    &copy; 2026 SMKN Jateng Semarang.
                    All rights reserved.

                </footer>

            </main>

        </div>

    </div>

</template>