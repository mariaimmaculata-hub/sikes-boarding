<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    FolderIcon, 
    AcademicCapIcon, 
    ClipboardDocumentListIcon, 
    UserGroupIcon, 
    UserIcon, 
    CalendarDaysIcon, 
    ClipboardDocumentCheckIcon, 
    BoltIcon, 
    ChartBarIcon, 
    Cog6ToothIcon, 
    BellIcon, 
    ChevronDownIcon, 
    ChevronRightIcon,
    Bars3Icon, 
    XMarkIcon 
} from '@heroicons/vue/24/outline';

const { url, props } = usePage();

// States
const isMobileSidebarOpen = ref(false);
const isProfileDropdownOpen = ref(false);

// Auto-expand Master Data accordion if current route is part of master data
const isMasterAccordionOpen = ref(url.includes('/admin/master'));

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const toggleProfileDropdown = () => {
    isProfileDropdownOpen.value = !isProfileDropdownOpen.value;
};

const toggleMasterAccordion = () => {
    isMasterAccordionOpen.value = !isMasterAccordionOpen.value;
};

// Check if a link is currently active
const isActive = (path) => {
    return url.startsWith(path);
};

// Breadcrumb computation based on URL
const breadcrumbs = computed(() => {
    const segments = url.split('/').filter(Boolean);
    const list = [{ name: 'Home', url: '/' }];
    let currentUrl = '';
    
    segments.forEach((seg, idx) => {
        currentUrl += `/${seg}`;
        // Map raw segments to user-friendly titles
        let name = seg.charAt(0).toUpperCase() + seg.slice(1);
        if (seg === 'admin') name = 'Admin';
        else if (seg === 'master') name = 'Master Data';
        else if (seg === 'jurusan') name = 'Jurusan';
        else if (seg === 'kelas') name = 'Kelas';
        else if (seg === 'siswa') name = 'Siswa';
        else if (seg === 'user') name = 'User';
        else if (seg === 'pemeriksaan') name = 'Pemeriksaan Berkala';
        else if (seg === 'kunjungan') name = 'Kunjungan Klinik';
        else if (seg === 'tksi') name = 'TKSI & Panduan';
        else if (seg === 'laporan') name = 'Laporan';
        else if (seg === 'pengaturan') name = 'Pengaturan';
        else if (seg === 'create') name = 'Tambah';
        else if (seg === 'edit') name = 'Ubah';
        else if (!isNaN(seg)) name = `Detail #${seg}`; // ID representation

        list.push({ name, url: currentUrl });
    });
    
    return list;
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col font-sans text-slate-800 antialiased select-none">
        
        <!-- TOP NAVBAR (From Image layout) -->
        <header class="bg-white border-b border-slate-200 h-16 fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 lg:px-6">
            <div class="flex items-center space-x-3">
                <!-- Hamburger menu button (Visible on Mobile) -->
                <button @click="toggleMobileSidebar" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <Bars3Icon class="w-6 h-6" />
                </button>
                
                <!-- School Emblem logo & header title -->
                <div class="flex items-center space-x-3 pl-2 lg:pl-0">
                    <div class="bg-blue-900 p-1.5 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight leading-tight">Sistem Pencatatan Kesehatan &amp; Kebugaran Siswa Boarding</span>
                        <span class="text-[9px] sm:text-xs text-slate-500 font-medium">SMKN Jateng di Semarang</span>
                    </div>
                </div>
            </div>

            <!-- Navbar Right Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notification Bell with count -->
                <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition">
                    <BellIcon class="w-6 h-6" />
                    <span class="absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white">
                        3
                    </span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button @click="toggleProfileDropdown" class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center text-sm border border-blue-200">
                            A
                        </div>
                        <span class="text-sm font-semibold text-slate-700 hidden sm:inline-block">Admin</span>
                        <ChevronDownIcon class="w-4 h-4 text-slate-400 transition duration-200" :class="{ 'transform rotate-180': isProfileDropdownOpen }" />
                    </button>
                    
                    <!-- Dropdown Options -->
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div v-if="isProfileDropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-55">
                            <Link :href="route('profile.edit')" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition font-medium">
                                <UserIcon class="w-4 h-4 text-slate-400" />
                                <span>Kelola Profil</span>
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" class="w-full text-left flex items-center space-x-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50/50 transition font-bold border-t border-slate-100">
                                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Logout</span>
                            </Link>
                        </div>
                    </transition>
                </div>
            </div>
        </header>

        <div class="flex flex-row pt-16 flex-grow">

            <!-- SIDEBAR (Desktop: 256px wide, visible, absolute-position relative to flex-row) -->
            <aside class="bg-blue-950 text-white w-64 fixed top-16 bottom-0 left-0 z-20 hidden lg:flex flex-col justify-between border-r border-blue-900 shadow-xl overflow-y-auto">
                <div class="py-6 px-4 space-y-6">
                    <!-- Sidebar Label Info -->
                    <div class="px-3">
                        <span class="text-[10px] font-bold text-blue-300 uppercase tracking-widest block">Menu Navigasi</span>
                        <span class="text-xs text-blue-200/60 font-medium tracking-wide">Admin Panel</span>
                    </div>

                    <!-- Sidebar Navigation Link Menu Items -->
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <Link 
                            :href="route('admin.dashboard')" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/dashboard') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <HomeIcon class="w-5 h-5" />
                                <span>Dashboard</span>
                            </div>
                        </Link>

                        <!-- Master Data (Accordion Toggle) -->
                        <div>
                            <button 
                                @click="toggleMasterAccordion"
                                :class="[
                                    'w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                    isActive('/admin/master') 
                                        ? 'text-white' 
                                        : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                                ]"
                            >
                                <div class="flex items-center space-x-3">
                                    <FolderIcon class="w-5 h-5" />
                                    <span>Master Data</span>
                                </div>
                                <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isMasterAccordionOpen }" />
                            </button>
                            
                            <!-- Master Data Sub-menu -->
                            <div v-if="isMasterAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                <Link 
                                    :href="route('admin.master.siswa.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/siswa') 
                                            ? 'text-white bg-blue-900/70' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >
                                    <UserGroupIcon class="w-4 h-4" />
                                    <span>Data Siswa</span>
                                </Link>
                                <Link 
                                    :href="route('admin.master.kelas.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/kelas') 
                                            ? 'text-white bg-blue-900/70' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >
                                    <ClipboardDocumentListIcon class="w-4 h-4" />
                                    <span>Data Kelas</span>
                                </Link>
                                <Link 
                                    :href="route('admin.master.jurusan.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/jurusan') 
                                            ? 'text-white bg-blue-900/70' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >
                                    <AcademicCapIcon class="w-4 h-4" />
                                    <span>Data Jurusan</span>
                                </Link>
                                <Link 
                                    :href="route('admin.master.user.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/admin/master/user') 
                                            ? 'text-white bg-blue-900/70' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-900/30'
                                    ]"
                                >
                                    <UserIcon class="w-4 h-4" />
                                    <span>Data User</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Pemeriksaan Berkala -->
                        <Link 
                            :href="route('admin.pemeriksaan.index')"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/pemeriksaan') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <CalendarDaysIcon class="w-5 h-5" />
                                <span>Pemeriksaan Berkala</span>
                            </div>
                        </Link>

                        <!-- Kunjungan Klinik -->
                        <Link 
                            :href="route('admin.kunjungan.index')"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/kunjungan') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <ClipboardDocumentCheckIcon class="w-5 h-5" />
                                <span>Kunjungan Klinik</span>
                            </div>
                        </Link>

                        <!-- TKSI & Panduan -->
                        <Link 
                            :href="route('admin.tksi.index')"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/tksi') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <BoltIcon class="w-5 h-5" />
                                <span>TKSI &amp; Panduan</span>
                            </div>
                        </Link>

                        <!-- Laporan -->
                        <Link 
                            :href="route('admin.laporan.index')"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/laporan') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <ChartBarIcon class="w-5 h-5" />
                                <span>Laporan</span>
                            </div>
                        </Link>

                        <!-- Pengaturan -->
                        <Link 
                            :href="route('admin.pengaturan.index')"
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/admin/pengaturan') 
                                    ? 'bg-blue-700 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-900/50 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <Cog6ToothIcon class="w-5 h-5" />
                                <span>Pengaturan</span>
                            </div>
                        </Link>
                    </nav>
                </div>
                
                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-blue-900 text-center text-xs text-blue-200/50 font-medium">
                    &copy; 2026 SMKN Jateng Semarang
                </div>
            </aside>

            <!-- MOBILE OFFCANVAS SIDEBAR (Visible on Mobile overlay) -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-x-full"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-full"
            >
                <div v-if="isMobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden flex">
                    <!-- Overlay backdrop -->
                    <div @click="toggleMobileSidebar" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
                    
                    <!-- Sidebar Drawer contents -->
                    <aside class="relative bg-blue-950 text-white w-64 flex flex-col justify-between shadow-2xl z-50">
                        <div class="py-6 px-4 space-y-6">
                            <!-- Mobile Header close button -->
                            <div class="flex items-center justify-between px-2">
                                <span class="text-xs font-bold text-blue-300 uppercase tracking-widest">Menu Navigasi</span>
                                <button @click="toggleMobileSidebar" class="p-1 rounded-lg hover:bg-blue-900 text-white">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Mobile Navigation list -->
                            <nav class="space-y-1">
                                <Link 
                                    :href="route('admin.dashboard')" 
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/dashboard') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <HomeIcon class="w-5 h-5" />
                                    <span>Dashboard</span>
                                </Link>

                                <div>
                                    <button 
                                        @click="toggleMasterAccordion"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm text-blue-100 hover:bg-blue-900/50"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <FolderIcon class="w-5 h-5" />
                                            <span>Master Data</span>
                                        </div>
                                        <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isMasterAccordionOpen }" />
                                    </button>
                                    
                                    <div v-if="isMasterAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                        <Link 
                                            :href="route('admin.master.siswa.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold text-blue-200/80 hover:text-white"
                                        >
                                            <UserGroupIcon class="w-4 h-4" />
                                            <span>Data Siswa</span>
                                        </Link>
                                        <Link 
                                            :href="route('admin.master.kelas.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold text-blue-200/80 hover:text-white"
                                        >
                                            <ClipboardDocumentListIcon class="w-4 h-4" />
                                            <span>Data Kelas</span>
                                        </Link>
                                        <Link 
                                            :href="route('admin.master.jurusan.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold text-blue-200/80 hover:text-white"
                                        >
                                            <AcademicCapIcon class="w-4 h-4" />
                                            <span>Data Jurusan</span>
                                        </Link>
                                        <Link 
                                            :href="route('admin.master.user.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold text-blue-200/80 hover:text-white"
                                        >
                                            <UserIcon class="w-4 h-4" />
                                            <span>Data User</span>
                                        </Link>
                                    </div>
                                </div>

                                <Link 
                                    :href="route('admin.pemeriksaan.index')"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/pemeriksaan') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <CalendarDaysIcon class="w-5 h-5" />
                                    <span>Pemeriksaan Berkala</span>
                                </Link>

                                <Link 
                                    :href="route('admin.kunjungan.index')"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/kunjungan') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <ClipboardDocumentCheckIcon class="w-5 h-5" />
                                    <span>Kunjungan Klinik</span>
                                </Link>

                                <Link 
                                    :href="route('admin.tksi.index')"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/tksi') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <BoltIcon class="w-5 h-5" />
                                    <span>TKSI &amp; Panduan</span>
                                </Link>

                                <Link 
                                    :href="route('admin.laporan.index')"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/laporan') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <ChartBarIcon class="w-5 h-5" />
                                    <span>Laporan</span>
                                </Link>

                                <Link 
                                    :href="route('admin.pengaturan.index')"
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/admin/pengaturan') ? 'bg-blue-700 text-white shadow-md' : 'text-blue-100 hover:bg-blue-900/50'
                                    ]"
                                >
                                    <Cog6ToothIcon class="w-5 h-5" />
                                    <span>Pengaturan</span>
                                </Link>
                            </nav>
                        </div>
                        <div class="p-4 border-t border-blue-900 text-center text-xs text-blue-200/50">
                            &copy; 2026 SMKN Jateng Semarang
                        </div>
                    </aside>
                </div>
            </transition>

            <!-- MAIN CONTENT AREA (Pushed left margin by 256px on desktop) -->
            <main class="flex-grow lg:pl-64 flex flex-col justify-between min-h-[calc(100vh-4rem)] min-w-0 overflow-x-auto">
                <div class="p-4 lg:p-6 space-y-6">
                    
                    <!-- BREADCRUMBS -->
                    <nav class="flex text-xs font-semibold text-slate-500 space-x-2" aria-label="Breadcrumb">
                        <template v-for="(crumb, idx) in breadcrumbs" :key="idx">
                            <span v-if="idx > 0" class="text-slate-300">/</span>
                            <Link 
                                v-if="idx < breadcrumbs.length - 1" 
                                :href="crumb.url" 
                                class="hover:text-blue-600 transition"
                            >
                                {{ crumb.name }}
                            </Link>
                            <span v-else class="text-slate-800 font-bold">
                                {{ crumb.name }}
                            </span>
                        </template>
                    </nav>

                    <!-- INNER PAGES CONTENT SLOT -->
                    <slot />

                </div>

                <!-- FOOTER -->
                <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs font-semibold text-slate-400">
                    &copy; 2026 SMKN Jateng Semarang. All rights reserved.
                </footer>
            </main>

        </div>
    </div>
</template>
