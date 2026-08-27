<script setup>

import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import NotificationDropdown from '@/Components/NotificationDropdown.vue'

import {
    HomeIcon,
    UserGroupIcon,
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


const page = usePage()


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const isMobileSidebarOpen = ref(false)
const isProfileDropdownOpen = ref(false)
const isTksiAccordionOpen = ref(true)


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

const user = computed(() => page.props.auth?.user)


/*
|--------------------------------------------------------------------------
| MOBILE SIDEBAR
|--------------------------------------------------------------------------
*/

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value
}


/*
|--------------------------------------------------------------------------
| PROFILE DROPDOWN
|--------------------------------------------------------------------------
*/

const toggleProfileDropdown = () => {
    isProfileDropdownOpen.value =
        !isProfileDropdownOpen.value
}


/*
|--------------------------------------------------------------------------
| TKSI ACCORDION
|--------------------------------------------------------------------------
*/

const toggleTksiAccordion = () => {
    isTksiAccordionOpen.value =
        !isTksiAccordionOpen.value
}


/*
|--------------------------------------------------------------------------
| ACTIVE ROUTE
|--------------------------------------------------------------------------
*/

const isActive = (url) => {
    return page.url === url
}


/*
|--------------------------------------------------------------------------
| BREADCRUMB
|--------------------------------------------------------------------------
*/

const breadcrumbs = computed(() => {

    const path = page.url.split('?')[0]

    if (path === '/tksi/dashboard') {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
        ]
    }

    if (path === '/tksi/panduan') {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Panduan TKSI',
                url: '/tksi/panduan',
            },
        ]
    }

    if (path.startsWith('/tksi/panduan/')) {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Panduan TKSI',
                url: '/tksi/panduan',
            },
            {
                name: 'Detail Panduan',
                url: path,
            },
        ]
    }

    if (path === '/tksi/input') {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Input TKSI',
                url: '/tksi/input',
            },
        ]
    }

    if (path === '/tksi/input/create') {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Input TKSI',
                url: '/tksi/input',
            },
            {
                name: 'Tambah Data',
                url: '/tksi/input/create',
            },
        ]
    }

    if (path.startsWith('/tksi/input/')) {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Input TKSI',
                url: '/tksi/input',
            },
            {
                name: 'Detail TKSI',
                url: path,
            },
        ]
    }

    if (path === '/tksi/report') {
        return [
            {
                name: 'Dashboard',
                url: '/tksi/dashboard',
            },
            {
                name: 'Report Periode',
                url: '/tksi/Report',
            },
        ]
    }

 /*
|--------------------------------------------------------------------------
| NOTIFIKASI
|--------------------------------------------------------------------------
*/

if (path === '/notifikasi') {
    return [
        {
            name: 'Dashboard',
            url: '/tksi/dashboard',
        },
        {
            name: 'Notifikasi',
            url: '/notifikasi',
        },
    ]
}

/*
|--------------------------------------------------------------------------
| DETAIL NOTIFIKASI
|--------------------------------------------------------------------------
|
| URL:
| /notifikasi/{id}
|
| Jangan tampilkan UUID / ID notifikasi.
| Tampilkan nama halaman "Detail Notifikasi".
|
|--------------------------------------------------------------------------
*/

if (path.startsWith('/notifikasi/')) {
    return [
        {
            name: 'Dashboard',
            url: '/tksi/dashboard',
        },
        {
            name: 'Notifikasi',
            url: '/notifikasi',
        },
        {
            name: 'Detail Notifikasi',
            url: path,
        },
    ]
}

    return [
        {
            name: 'Dashboard',
            url: '/tksi/dashboard',
        },
    ]
})

</script>


<template>

    <div
        class="min-h-screen bg-slate-50 flex flex-col font-sans text-slate-800 antialiased"
    >

        <!-- ==================================================
             TOP NAVBAR
        ================================================== -->

        <header
            class="bg-white border-b border-slate-200 h-16 fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 lg:px-6"
        >

            <!-- LEFT -->

            <div class="flex items-center space-x-3">

                <!-- MOBILE MENU -->

                <button
                    type="button"
                    @click="toggleMobileSidebar"
                    class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none"
                    aria-label="Buka menu"
                >

                    <Bars3Icon
                        class="w-6 h-6"
                    />

                </button>


                <!-- LOGO -->

                <div
                    class="flex items-center space-x-3 pl-2 lg:pl-0"
                >

                    <div
                        class="bg-blue-900 p-1.5 rounded-lg flex items-center justify-center"
                    >

                        <svg
                            class="w-6 h-6 text-yellow-400"
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


                    <div
                        class="flex flex-col"
                    >

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

            <div
                class="flex items-center space-x-4"
            >

                <!-- NOTIFICATION -->
<NotificationDropdown />


                <!-- PROFILE -->

                <div
                    class="relative"
                >

                    <button
                        type="button"
                        @click="toggleProfileDropdown"
                        class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none"
                    >

                        <!-- AVATAR -->

                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center text-sm border border-blue-200"
                        >

                            {{
                                user?.name
                                    ?.charAt(0)
                                    ?.toUpperCase() || 'T'
                            }}

                        </div>


                        <!-- NAME -->

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
                            class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50"
                        >

                            <Link
                                href="/profile"
                                class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition font-medium"
                                @click="isProfileDropdownOpen = false"
                            >

                                <UserIcon
                                    class="w-4 h-4 text-slate-400"
                                />

                                <span>
                                    Kelola Profil
                                </span>

                            </Link>


                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="w-full text-left flex items-center space-x-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50/50 transition font-bold border-t border-slate-100"
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

        <div
            class="flex flex-row pt-16 flex-grow"
        >

            <!-- ==================================================
                 DESKTOP SIDEBAR
            ================================================== -->

            <aside
                class="bg-blue-950 text-white w-64 fixed top-16 bottom-0 left-0 z-20 hidden lg:flex flex-col justify-between border-r border-blue-900 shadow-xl overflow-y-auto"
            >

                <div
                    class="py-6 px-4"
                >

                    <!-- TITLE -->

                    <div
                        class="px-3 mb-6"
                    >

                        <span
                            class="text-[10px] font-bold text-blue-300 uppercase tracking-widest block"
                        >
                            Menu Navigasi
                        </span>

                        <span
                            class="text-xs text-blue-200/60 font-medium tracking-wide"
                        >
                            TKSI Panel
                        </span>

                    </div>


                    <!-- NAV -->

                    <nav
                        class="space-y-1"
                    >

                        <!-- ==================================================
                             DASHBOARD
                        ================================================== -->

                        <Link
                            href="/tksi/dashboard"
                            :class="[
                                'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/tksi/dashboard')
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >

                            <HomeIcon
                                class="w-5 h-5"
                            />

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
                                    page.url.startsWith('/tksi/panduan') ||
                                    page.url.startsWith('/tksi/input') ||
                                    page.url.startsWith('/tksi/report')
                                        ? 'text-white bg-blue-900/40'
                                        : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
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
                                    class="w-4 h-4 text-blue-300 transition-transform duration-200"
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
                                        page.url.startsWith('/tksi/panduan')
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                        page.url.startsWith('/tksi/input')
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >

                            <div
                                class="flex items-center space-x-3"
                            >

                                <BellIcon
                                    class="w-5 h-5"
                                />

                                <span>
                                    Notifikasi
                                </span>

                            </div>


                            <span
                                class="bg-rose-500 text-white text-[9px] font-bold min-w-5 h-5 px-1 flex items-center justify-center rounded-full"
                            >
                                0
                            </span>

                        </Link>

                    </nav>

                </div>


                <!-- FOOTER -->

                <div
                    class="p-4 border-t border-blue-900 text-center text-xs text-blue-200/50 font-medium"
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
                        class="relative bg-blue-950 text-white w-64 flex flex-col justify-between shadow-2xl z-50"
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
                                        class="text-xs font-bold text-blue-300 uppercase tracking-widest block"
                                    >
                                        Menu Navigasi
                                    </span>

                                    <span
                                        class="text-[10px] text-blue-200/60"
                                    >
                                        TKSI Panel
                                    </span>

                                </div>


                                <button
                                    type="button"
                                    @click="toggleMobileSidebar"
                                    class="p-1 rounded-lg hover:bg-blue-900 text-white"
                                >

                                    <XMarkIcon
                                        class="w-5 h-5"
                                    />

                                </button>

                            </div>


                            <!-- MOBILE NAV -->

                            <nav
                                class="space-y-1"
                            >

                                <!-- DASHBOARD -->

                                <Link
                                    href="/tksi/dashboard"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/tksi/dashboard')
                                            ? 'bg-blue-700 text-white'
                                            : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >

                                    <HomeIcon
                                        class="w-5 h-5"
                                    />

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
                                            page.url.startsWith('/tksi/panduan') ||
                                            page.url.startsWith('/tksi/input') ||
                                            page.url.startsWith('/tksi/report')
                                                ? 'text-white bg-blue-900/40'
                                                : 'text-blue-100 hover:bg-blue-900/50'
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
                                            class="w-4 h-4 text-blue-300 transition-transform"
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
                                                page.url.startsWith('/tksi/panduan')
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                                page.url.startsWith('/tksi/input')
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
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
                                            ? 'bg-blue-700 text-white'
                                            : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >

                                    <div
                                        class="flex items-center space-x-3"
                                    >

                                        <BellIcon
                                            class="w-5 h-5"
                                        />

                                        <span>
                                            Notifikasi
                                        </span>

                                    </div>


                                    <span
                                        class="bg-rose-500 text-white text-[9px] font-bold min-w-5 h-5 px-1 flex items-center justify-center rounded-full"
                                    >
                                        0
                                    </span>

                                </Link>

                            </nav>

                        </div>


                        <!-- FOOTER -->

                        <div
                            class="p-4 border-t border-blue-900 text-center text-xs text-blue-200/50"
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
                            :key="`${crumb.name}-${idx}`"
                        >

                            <span
                                v-if="idx > 0"
                                class="text-slate-300"
                            >
                                /
                            </span>


                            <Link
                                v-if="idx < breadcrumbs.length - 1"
                                :href="crumb.url"
                                class="hover:text-blue-600 transition"
                            >
                                {{ crumb.name }}
                            </Link>


                            <span
                                v-else
                                class="text-slate-800 font-bold"
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
                    class="bg-white border-t border-slate-200 py-4 text-center text-xs font-semibold text-slate-400"
                >

                    &copy; 2026 SMKN Jateng Semarang.
                    All rights reserved.

                </footer>

            </main>

        </div>

    </div>

</template>