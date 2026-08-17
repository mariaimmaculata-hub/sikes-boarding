<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    HomeIcon,
    FolderIcon,
    UserGroupIcon,
    UserIcon,
    CalendarDaysIcon,
    ClipboardDocumentCheckIcon,
    ChartBarIcon,
    BellIcon,
    ChevronDownIcon,
    ChevronRightIcon,
    Bars3Icon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


// ======================================================
// PAGE
// ======================================================

const page = usePage()

const currentUrl = computed(() => page.url)


// ======================================================
// STATE
// ======================================================

const isMobileSidebarOpen = ref(false)

const isProfileDropdownOpen = ref(false)

const isMasterAccordionOpen = ref(
    currentUrl.value.startsWith('/admin/master')
)

const isPeriodeAccordionOpen = ref(
    currentUrl.value.startsWith('/admin/periode')
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

const toggleMasterAccordion = () => {
    isMasterAccordionOpen.value =
        !isMasterAccordionOpen.value
}

const togglePeriodeAccordion = () => {
    isPeriodeAccordionOpen.value =
        !isPeriodeAccordionOpen.value
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
// BREADCRUMB MAP
// ======================================================
//
// MASTER DATA TIDAK DITAMPILKAN
//
// /admin/dashboard
// Dashboard
//
// /admin/master/siswa
// Dashboard / Data Siswa
//
// /admin/master/user
// Dashboard / Data User
//
// /admin/periode
// Dashboard / Periode
//
// /admin/periode/siswa-aktif
// Dashboard / Periode / Siswa Periode Aktif
//
// /admin/periode/report
// Dashboard / Periode / Report Periode
//
// /admin/kunjungan
// Dashboard / Kunjungan Klinik
//
// /notifikasi
// Dashboard / Notifikasi
// ======================================================

const breadcrumbMap = {

    // MASTER DATA
    siswa: 'Data Siswa',
    user: 'Data User',
    'siswa-import': 'Import Siswa',

    // PERIODE
    periode: 'Periode',
    'siswa-aktif': 'Siswa Periode Aktif',
    report: 'Report Periode',

    // LAINNYA
    kunjungan: 'Kunjungan Klinik',
    notifikasi: 'Notifikasi',

    // PEMERIKSAAN
    pemeriksaan: 'Pemeriksaan Berkala',
    'report-berkala': 'Report Berkala',

    // TKSI
    tksi: 'TKSI',
    input: 'Input TKSI',

    // ACTION
    create: 'Tambah',
    edit: 'Ubah',
    show: 'Detail',
}


// ======================================================
// BUILD BREADCRUMB
// ======================================================

const breadcrumbs = computed(() => {

    const segments = currentUrl.value
        .split('/')
        .filter(Boolean)

    // ==================================================
    // DASHBOARD SELALU MENJADI ROOT
    // ==================================================

    const list = [
        {
            name: 'Dashboard',
            url: '/admin/dashboard',
        },
    ]

    let currentPath = ''


    segments.forEach((segment) => {

        currentPath += `/${segment}`


        // ==================================================
        // JANGAN TAMPILKAN "admin"
        // ==================================================

        if (segment === 'admin') {
            return
        }


        // ==================================================
        // JANGAN TAMPILKAN "dashboard"
        // Karena Dashboard sudah menjadi root
        // ==================================================

        if (segment === 'dashboard') {
            return
        }


        // ==================================================
        // JANGAN TAMPILKAN "master"
        //
        // /admin/master/siswa
        // menjadi:
        //
        // Dashboard / Data Siswa
        //
        // bukan:
        //
        // Dashboard / Master / Data Siswa
        // ==================================================

        if (segment === 'master') {
            return
        }


        // ==================================================
        // NAMA BREADCRUMB
        // ==================================================

        let name =
            breadcrumbMap[segment] ??
            segment.charAt(0).toUpperCase() +
            segment.slice(1)


        // ==================================================
        // JIKA SEGMENT BERUPA ID
        // ==================================================

        if (!isNaN(segment)) {
            name = `Detail #${segment}`
        }


        // ==================================================
        // TAMBAHKAN BREADCRUMB
        // ==================================================

        list.push({
            name,
            url: currentPath,
        })

    })

    return list
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
                >

                    <Bars3Icon class="w-6 h-6" />

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

                <Link
                    href="/notifikasi"
                    class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition"
                >

                    <BellIcon class="w-6 h-6" />

                    <span
                        class="absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white"
                    >
                        3
                    </span>

                </Link>


                <!-- PROFILE -->

                <div class="relative">

                    <button
                        type="button"
                        @click="toggleProfileDropdown"
                        class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none"
                    >

                        <div
                            class="w-8 h-8 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center text-sm border border-blue-200"
                        >
                            A
                        </div>


                        <span
                            class="text-sm font-semibold text-slate-700 hidden sm:inline-block"
                        >
                            Admin
                        </span>


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
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
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
                class="bg-blue-950 text-white w-64 fixed top-16 bottom-0 left-0 z-20 hidden lg:flex flex-col justify-between border-r border-blue-900 shadow-xl overflow-y-auto"
            >

                <div class="py-6 px-4">

                    <!-- TITLE -->

                    <div class="px-3 mb-6">

                        <span
                            class="text-[10px] font-bold text-blue-300 uppercase tracking-widest block"
                        >
                            Menu Navigasi
                        </span>

                        <span
                            class="text-xs text-blue-200/60 font-medium tracking-wide"
                        >
                            Admin Panel
                        </span>

                    </div>


                    <!-- NAV -->

                    <nav class="space-y-1">


                        <!-- ==================================================
                             DASHBOARD
                        ================================================== -->

                        <Link
                            href="/admin/dashboard"
                            :class="[
                                'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/dashboard')
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >

                            <HomeIcon class="w-5 h-5" />

                            <span>
                                Dashboard
                            </span>

                        </Link>


                        <!-- ==================================================
                             MASTER DATA
                        ================================================== -->

                        <div>

                            <button
                                type="button"
                                @click="toggleMasterAccordion"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                    isActive('/admin/master')
                                        ? 'text-white bg-blue-900/40'
                                        : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                                ]"
                            >

                                <div
                                    class="flex items-center space-x-3"
                                >

                                    <FolderIcon
                                        class="w-5 h-5"
                                    />

                                    <span>
                                        Master Data
                                    </span>

                                </div>


                                <ChevronRightIcon
                                    class="w-4 h-4 text-blue-300 transition-transform duration-200"
                                    :class="{
                                        'rotate-90':
                                            isMasterAccordionOpen
                                    }"
                                />

                            </button>


                            <div
                                v-show="isMasterAccordionOpen"
                                class="mt-1 pl-10 pr-2 space-y-1"
                            >

                                <!-- DATA SISWA -->

                                <Link
                                    href="/admin/master/siswa"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/siswa')
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >

                                    <UserGroupIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Data Siswa
                                    </span>

                                </Link>


                                <!-- DATA USER -->

                                <Link
                                    href="/admin/master/user"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/user')
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >

                                    <UserIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Data User
                                    </span>

                                </Link>

                            </div>

                        </div>


                        <!-- ==================================================
                             PERIODE
                        ================================================== -->

                        <div>

                            <button
                                type="button"
                                @click="togglePeriodeAccordion"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                    isActive('/admin/periode')
                                        ? 'text-white bg-blue-900/40'
                                        : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                                ]"
                            >

                                <div
                                    class="flex items-center space-x-3"
                                >

                                    <CalendarDaysIcon
                                        class="w-5 h-5"
                                    />

                                    <span>
                                        Periode
                                    </span>

                                </div>


                                <ChevronRightIcon
                                    class="w-4 h-4 text-blue-300 transition-transform duration-200"
                                    :class="{
                                        'rotate-90':
                                            isPeriodeAccordionOpen
                                    }"
                                />

                            </button>


                            <div
                                v-show="isPeriodeAccordionOpen"
                                class="mt-1 pl-10 pr-2 space-y-1"
                            >

                                <!-- DATA PERIODE -->

                                <Link
                                    href="/admin/periode"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        currentUrl === '/admin/periode'
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >

                                    <CalendarDaysIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Data Periode
                                    </span>

                                </Link>


                                <!-- SISWA PERIODE AKTIF -->

                                <Link
                                    href="/admin/periode/siswa-aktif"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/periode/siswa-aktif')
                                            ? 'text-white bg-blue-900/70'
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >

                                    <UserGroupIcon
                                        class="w-4 h-4"
                                    />

                                    <span>
                                        Siswa Periode Aktif
                                    </span>

                                </Link>


                                <!-- REPORT PERIODE -->

                                <Link
                                    href="/admin/periode/report"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/periode/report')
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
                             KUNJUNGAN KLINIK
                        ================================================== -->

                        <Link
                            href="/admin/kunjungan"
                            :class="[
                                'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/kunjungan')
                                    ? 'bg-blue-700 text-white shadow-md'
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >

                            <ClipboardDocumentCheckIcon
                                class="w-5 h-5"
                            />

                            <span>
                                Kunjungan Klinik
                            </span>

                        </Link>


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
                                3
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
                                        Admin Panel
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

                            <nav class="space-y-1">


                                <!-- DASHBOARD -->

                                <Link
                                    href="/admin/dashboard"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/dashboard')
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


                                <!-- MASTER DATA -->

                                <div>

                                    <button
                                        type="button"
                                        @click="toggleMasterAccordion"
                                        :class="[
                                            'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                            isActive('/admin/master')
                                                ? 'text-white bg-blue-900/40'
                                                : 'text-blue-100 hover:bg-blue-900/50'
                                        ]"
                                    >

                                        <div
                                            class="flex items-center space-x-3"
                                        >

                                            <FolderIcon
                                                class="w-5 h-5"
                                            />

                                            <span>
                                                Master Data
                                            </span>

                                        </div>


                                        <ChevronRightIcon
                                            class="w-4 h-4 text-blue-300 transition-transform"
                                            :class="{
                                                'rotate-90':
                                                    isMasterAccordionOpen
                                            }"
                                        />

                                    </button>


                                    <div
                                        v-show="isMasterAccordionOpen"
                                        class="mt-1 pl-10 pr-2 space-y-1"
                                    >

                                        <!-- DATA SISWA -->

                                        <Link
                                            href="/admin/master/siswa"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/admin/master/siswa')
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                            ]"
                                        >

                                            <UserGroupIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Data Siswa
                                            </span>

                                        </Link>


                                        <!-- DATA USER -->

                                        <Link
                                            href="/admin/master/user"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/admin/master/user')
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                            ]"
                                        >

                                            <UserIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Data User
                                            </span>

                                        </Link>

                                    </div>

                                </div>


                                <!-- PERIODE -->

                                <div>

                                    <button
                                        type="button"
                                        @click="togglePeriodeAccordion"
                                        :class="[
                                            'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                            isActive('/admin/periode')
                                                ? 'text-white bg-blue-900/40'
                                                : 'text-blue-100 hover:bg-blue-900/50'
                                        ]"
                                    >

                                        <div
                                            class="flex items-center space-x-3"
                                        >

                                            <CalendarDaysIcon
                                                class="w-5 h-5"
                                            />

                                            <span>
                                                Periode
                                            </span>

                                        </div>


                                        <ChevronRightIcon
                                            class="w-4 h-4 text-blue-300 transition-transform"
                                            :class="{
                                                'rotate-90':
                                                    isPeriodeAccordionOpen
                                            }"
                                        />

                                    </button>


                                    <div
                                        v-show="isPeriodeAccordionOpen"
                                        class="mt-1 pl-10 pr-2 space-y-1"
                                    >

                                        <!-- DATA PERIODE -->

                                        <Link
                                            href="/admin/periode"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                currentUrl === '/admin/periode'
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                            ]"
                                        >

                                            <CalendarDaysIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Data Periode
                                            </span>

                                        </Link>


                                        <!-- SISWA PERIODE AKTIF -->

                                        <Link
                                            href="/admin/periode/siswa-aktif"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/admin/periode/siswa-aktif')
                                                    ? 'text-white bg-blue-900/70'
                                                    : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                            ]"
                                        >

                                            <UserGroupIcon
                                                class="w-4 h-4"
                                            />

                                            <span>
                                                Siswa Periode Aktif
                                            </span>

                                        </Link>


                                        <!-- REPORT PERIODE -->

                                        <Link
                                            href="/admin/periode/report"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/admin/periode/report')
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


                                <!-- KUNJUNGAN KLINIK -->

                                <Link
                                    href="/admin/kunjungan"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/kunjungan')
                                            ? 'bg-blue-700 text-white'
                                            : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >

                                    <ClipboardDocumentCheckIcon
                                        class="w-5 h-5"
                                    />

                                    <span>
                                        Kunjungan Klinik
                                    </span>

                                </Link>


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
                                        3
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

                    <!-- ==================================================
                         BREADCRUMB
                    ================================================== -->

                    <nav
                        class="flex flex-wrap items-center text-xs font-semibold text-slate-500 gap-x-2 gap-y-1"
                        aria-label="Breadcrumb"
                    >

                        <template
                            v-for="(crumb, idx) in breadcrumbs"
                            :key="idx"
                        >

                            <!-- SEPARATOR -->

                            <span
                                v-if="idx > 0"
                                class="text-slate-300"
                            >
                                /
                            </span>


                            <!-- CLICKABLE BREADCRUMB -->

                            <Link
                                v-if="idx < breadcrumbs.length - 1"
                                :href="crumb.url"
                                class="hover:text-blue-600 transition"
                            >

                                {{ crumb.name }}

                            </Link>


                            <!-- CURRENT PAGE -->

                            <span
                                v-else
                                class="text-slate-800 font-bold"
                            >

                                {{ crumb.name }}

                            </span>

                        </template>

                    </nav>


                    <!-- ==================================================
                         PAGE CONTENT
                    ================================================== -->

                    <slot />

                </div>


                <!-- ==================================================
                     FOOTER
                ================================================== -->

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