<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    HomeIcon, 
    CalendarDaysIcon, 
    ClipboardDocumentCheckIcon, 
    BeakerIcon, 
    ChartBarIcon, 
    Cog6ToothIcon,
    BellIcon,
    ChevronDownIcon, 
    Bars3Icon, 
    XMarkIcon,
    UserIcon,
    FolderIcon,
    UserGroupIcon,
    ChevronRightIcon
} from '@heroicons/vue/24/outline';

const { url } = usePage();

// States
const isMobileSidebarOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const isMasterAccordionOpen = ref(true); // Default open for Master Data
const isLayananAccordionOpen = ref(true); // Default open for Layanan Kesehatan

const toggleMobileSidebar = () => {
    isMobileSidebarOpen.value = !isMobileSidebarOpen.value;
};

const toggleProfileDropdown = () => {
    isProfileDropdownOpen.value = !isProfileDropdownOpen.value;
};

const toggleMasterAccordion = () => {
    isMasterAccordionOpen.value = !isMasterAccordionOpen.value;
};

const toggleLayananAccordion = () => {
    isLayananAccordionOpen.value = !isLayananAccordionOpen.value;
};

// Check if a link is active
const isActive = (path) => {
    return url.startsWith(path);
};

// Dynamic Breadcrumbs
const breadcrumbs = computed(() => {
    const segments = url.split('/').filter(Boolean);
    const list = [{ name: 'Home', url: '/' }];
    let currentUrl = '';
    
    segments.forEach((seg) => {
        currentUrl += `/${seg}`;
        let name = seg.charAt(0).toUpperCase() + seg.slice(1);
        if (seg === 'petugas') name = 'Petugas Klinik Panel';
        else if (seg === 'dashboard') name = 'Dashboard';
        else if (seg === 'pemeriksaan') name = 'Pemeriksaan Berkala';
        else if (seg === 'kunjungan') name = 'Kunjungan Klinik';
        else if (seg === 'master') name = 'Master Data';
        else if (seg === 'siswa') name = 'Data Siswa';
        else if (seg === 'kelas') name = 'Data Kelas/Asrama';
        else if (seg === 'jurusan') name = 'Jurusan';
        else if (seg === 'obat') name = 'Data Obat & Penyakit';
        else if (seg === 'laporan') name = 'Laporan & Statistik';
        else if (seg === 'pengaturan') name = 'Pengaturan';
        else if (!isNaN(seg)) name = `Detail #${seg}`;

        list.push({ name, url: currentUrl });
    });
    
    return list;
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex flex-col font-sans text-slate-800 antialiased select-none">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-slate-200 h-16 fixed top-0 right-0 left-0 z-30 flex items-center justify-between px-4 lg:px-6">
            <div class="flex items-center space-x-3">
                <!-- Hamburger menu button -->
                <button @click="toggleMobileSidebar" class="lg:hidden p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none">
                    <Bars3Icon class="w-6 h-6" />
                </button>
                
                <!-- Logo & header title -->
                <div class="flex items-center space-x-3 pl-2 lg:pl-0">
                    <div class="bg-blue-900 p-1.5 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight leading-tight">Sistem Pencatatan Kesehatan &amp; Kebugaran Siswa Boarding</span>
                        <span class="text-[9px] sm:text-xs text-slate-500 font-medium">SMKN Jateng di Semarang</span>
                    </div>
                </div>
            </div>

            <!-- Navbar Right Actions -->
            <div class="flex items-center space-x-4">
                <!-- Notification Bell -->
                <button class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition">
                    <BellIcon class="w-5.5 h-5.5" />
                    <span class="absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white">
                        4
                    </span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button @click="toggleProfileDropdown" class="flex items-center space-x-2.5 p-1.5 rounded-xl hover:bg-slate-100 transition focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-900 font-bold flex items-center justify-center text-sm border border-blue-200">
                            PK
                        </div>
                        <span class="text-sm font-bold text-slate-700 hidden sm:inline-block">Rina Lestari</span>
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

            <!-- DESKTOP SIDEBAR -->
            <aside class="bg-blue-900 text-white w-64 fixed top-16 bottom-0 left-0 z-20 hidden lg:flex flex-col justify-between border-r border-blue-800 shadow-xl overflow-y-auto">
                <div class="py-6 px-4 space-y-6">
                    <div class="px-3">
                        <span class="text-base font-bold tracking-tight block">SiKes-Boarding</span>
                        <span class="text-[10px] text-blue-200/70 font-semibold tracking-wide uppercase">Petugas Klinik Panel</span>
                    </div>

                    <!-- Sidebar Navigation Link Menu Items -->
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <Link 
                            :href="route('petugas.dashboard')" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/petugas/dashboard') 
                                    ? 'bg-blue-800 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-955/40 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <HomeIcon class="w-5 h-5" />
                                <span>Dashboard</span>
                            </div>
                        </Link>

                        <!-- Master Data (Accordion) -->
                        <div>
                            <button 
                                @click="toggleMasterAccordion"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition text-blue-100 hover:bg-blue-955/40 hover:text-white"
                            >
                                <div class="flex items-center space-x-3">
                                    <FolderIcon class="w-5 h-5" />
                                    <span>Master Data</span>
                                </div>
                                <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isMasterAccordionOpen }" />
                            </button>
                            
                            <div v-if="isMasterAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                <Link 
                                    :href="route('petugas.master.siswa.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/petugas/master/siswa') 
                                            ? 'text-white bg-blue-955/50 shadow-sm' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-955/30'
                                    ]"
                                >
                                    <UserGroupIcon class="w-4 h-4" />
                                    <span>Data Siswa</span>
                                </Link>
                                <Link 
                                    :href="route('petugas.master.kelas.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/petugas/master/kelas') 
                                            ? 'text-white bg-blue-955/50 shadow-sm' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-955/30'
                                    ]"
                                >
                                    <HomeIcon class="w-4 h-4" />
                                    <span>Data Kelas/Asrama</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Layanan Kesehatan (Accordion) -->
                        <div>
                            <button 
                                @click="toggleLayananAccordion"
                                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition text-blue-100 hover:bg-blue-955/40 hover:text-white"
                            >
                                <div class="flex items-center space-x-3">
                                    <ClipboardDocumentCheckIcon class="w-5 h-5" />
                                    <span>Layanan Kesehatan</span>
                                </div>
                                <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isLayananAccordionOpen }" />
                            </button>
                            
                            <div v-if="isLayananAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                <Link 
                                    :href="route('petugas.pemeriksaan.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/petugas/pemeriksaan') 
                                            ? 'text-white bg-blue-955/50 shadow-sm' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-955/30'
                                    ]"
                                >
                                    <CalendarDaysIcon class="w-4 h-4" />
                                    <span>Pemeriksaan Berkala</span>
                                </Link>
                                <Link 
                                    :href="route('petugas.kunjungan.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/petugas/kunjungan') 
                                            ? 'text-white bg-blue-955/50 shadow-sm' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-955/30'
                                    ]"
                                >
                                    <ClipboardDocumentCheckIcon class="w-4 h-4" />
                                    <span>Kunjungan Klinik</span>
                                </Link>
                                <Link 
                                    :href="route('petugas.obat.index')"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                        isActive('/petugas/obat') || isActive('/petugas/penyakit')
                                            ? 'text-white bg-blue-955/50 shadow-sm' 
                                            : 'text-blue-200/80 hover:text-white hover:bg-blue-955/30'
                                    ]"
                                >
                                    <BeakerIcon class="w-4 h-4" />
                                    <span>Data Obat &amp; Penyakit</span>
                                </Link>
                            </div>
                        </div>

                        <!-- Laporan & Statistik -->
                        <Link 
                            :href="route('petugas.laporan')" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/petugas/laporan') 
                                    ? 'bg-blue-800 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-955/40 hover:text-white'
                            ]"
                        >
                            <div class="flex items-center space-x-3">
                                <ChartBarIcon class="w-5 h-5" />
                                <span>Laporan &amp; Statistik</span>
                            </div>
                        </Link>

                        <!-- Pengaturan -->
                        <Link 
                            :href="route('petugas.pengaturan')" 
                            :class="[
                                'flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                isActive('/petugas/pengaturan') 
                                    ? 'bg-blue-800 text-white shadow-md' 
                                    : 'text-blue-100 hover:bg-blue-955/40 hover:text-white'
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
                <div class="p-4 border-t border-blue-800 text-center text-[10px] text-blue-200/50 font-semibold uppercase tracking-wider">
                    &copy; 2026 SMKN Jateng Semarang
                </div>
            </aside>

            <!-- MOBILE SIDEBAR DRAWER -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0 -translate-x-full"
                enter-to-class="opacity-100 translate-x-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-full"
            >
                <div v-if="isMobileSidebarOpen" class="fixed inset-0 z-40 lg:hidden flex">
                    <div @click="toggleMobileSidebar" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
                    <aside class="relative bg-blue-900 text-white w-64 flex flex-col justify-between shadow-2xl z-50">
                        <div class="py-6 px-4 space-y-6">
                            <div class="flex items-center justify-between px-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold tracking-tight block">SiKes-Boarding</span>
                                    <span class="text-[9px] text-blue-200/70 font-semibold tracking-wide uppercase">Petugas Panel</span>
                                </div>
                                <button @click="toggleMobileSidebar" class="p-1 rounded-lg hover:bg-blue-800 text-white">
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <nav class="space-y-1">
                                <Link 
                                    :href="route('petugas.dashboard')" 
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/petugas/dashboard') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-100 hover:bg-blue-955/40'
                                    ]"
                                >
                                    <HomeIcon class="w-5 h-5" />
                                    <span>Dashboard</span>
                                </Link>

                                <!-- Master Data Accordion (Mobile) -->
                                <div>
                                    <button 
                                        @click="toggleMasterAccordion"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition text-blue-100 hover:bg-blue-955/40"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <FolderIcon class="w-5 h-5" />
                                            <span>Master Data</span>
                                        </div>
                                        <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isMasterAccordionOpen }" />
                                    </button>
                                    
                                    <div v-if="isMasterAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                        <Link 
                                            :href="route('petugas.master.siswa.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition text-blue-200/80 hover:text-white hover:bg-blue-955/30"
                                        >
                                            <UserGroupIcon class="w-4 h-4" />
                                            <span>Data Siswa</span>
                                        </Link>
                                        <Link 
                                            :href="route('petugas.master.kelas.index')"
                                            @click="toggleMobileSidebar"
                                            class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition text-blue-200/80 hover:text-white hover:bg-blue-955/30"
                                        >
                                            <HomeIcon class="w-4 h-4" />
                                            <span>Data Kelas/Asrama</span>
                                        </Link>
                                    </div>
                                </div>

                                <!-- Layanan Kesehatan Accordion (Mobile) -->
                                <div>
                                    <button 
                                        @click="toggleLayananAccordion"
                                        class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl font-medium text-sm transition text-blue-100 hover:bg-blue-955/40"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <ClipboardDocumentCheckIcon class="w-5 h-5" />
                                            <span>Layanan Kesehatan</span>
                                        </div>
                                        <ChevronRightIcon class="w-4 h-4 text-blue-300 transition duration-200" :class="{ 'transform rotate-90': isLayananAccordionOpen }" />
                                    </button>
                                    
                                    <div v-if="isLayananAccordionOpen" class="mt-1 pl-10 pr-2 space-y-1">
                                        <Link 
                                            :href="route('petugas.pemeriksaan.index')"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/petugas/pemeriksaan') ? 'text-white bg-blue-955/50' : 'text-blue-200/80 hover:text-white'
                                            ]"
                                        >
                                            <CalendarDaysIcon class="w-4 h-4" />
                                            <span>Pemeriksaan Berkala</span>
                                        </Link>
                                        <Link 
                                            :href="route('petugas.kunjungan.index')"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/petugas/kunjungan') ? 'text-white bg-blue-955/50' : 'text-blue-200/80 hover:text-white'
                                            ]"
                                        >
                                            <ClipboardDocumentCheckIcon class="w-4 h-4" />
                                            <span>Kunjungan Klinik</span>
                                        </Link>
                                        <Link 
                                            :href="route('petugas.obat.index')"
                                            @click="toggleMobileSidebar"
                                            :class="[
                                                'flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-semibold transition',
                                                isActive('/petugas/obat') || isActive('/petugas/penyakit') ? 'text-white bg-blue-955/50' : 'text-blue-200/80 hover:text-white'
                                            ]"
                                        >
                                            <BeakerIcon class="w-4 h-4" />
                                            <span>Data Obat &amp; Penyakit</span>
                                        </Link>
                                    </div>
                                </div>

                                <Link 
                                    :href="route('petugas.laporan')" 
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/petugas/laporan') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-100 hover:bg-blue-955/40'
                                    ]"
                                >
                                    <ChartBarIcon class="w-5 h-5" />
                                    <span>Laporan &amp; Statistik</span>
                                </Link>

                                <Link 
                                    :href="route('petugas.pengaturan')" 
                                    @click="toggleMobileSidebar"
                                    :class="[
                                        'flex items-center space-x-3 px-3 py-2.5 rounded-xl font-medium text-sm transition',
                                        isActive('/petugas/pengaturan') ? 'bg-blue-800 text-white shadow-md' : 'text-blue-100 hover:bg-blue-955/40'
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

            <!-- MAIN CONTENT AREA -->
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
