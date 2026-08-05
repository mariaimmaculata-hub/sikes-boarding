<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Selamat Datang di SiKes-Boarding" />

    <div class="relative min-h-screen bg-gradient-to-br from-blue-950 via-blue-900 to-blue-700 text-white font-sans overflow-hidden flex flex-col justify-between">
        
        <!-- Decorative Top-Right Light Glow -->
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-500/20 rounded-full blur-[120px] pointer-events-none"></div>
        <!-- Decorative Bottom-Left Light Glow -->
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-indigo-500/15 rounded-full blur-[150px] pointer-events-none"></div>
 
        <!-- Decorative Wave Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:3rem_3rem] pointer-events-none"></div>

        <!-- HEADER -->
        <header class="relative z-10 w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between font-sans">
            <!-- Left Corner: SMKN Jateng Logo & Name -->
            <div class="flex items-center space-x-3">
                <div class="bg-white/10 p-2 rounded-xl backdrop-blur-md border border-white/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="M12 6v12M8 10h8" stroke-width="1.5" opacity="0.8" />
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-white text-2xl font-bold tracking-tight leading-none">SMKN Jateng</span>
                    <span class="text-white/60 text-[9px] tracking-widest mt-0.5 uppercase hidden sm:inline-block font-medium">Semarang</span>
                </div>
            </div>

            <!-- Middle: SiKes-Boarding Text -->
            <div class="hidden md:block">
                <span class="text-white/90 font-bold text-2xl tracking-wider font-sans drop-shadow-sm">
                    SiKes-Boarding
                </span>
            </div>

            <!-- Right Corner: Login Button (TETAP PERTAHANKAN) -->
            <div>
                <Link 
                    :href="route('login')"
                    class="px-6 py-2 border border-white text-white font-medium rounded-xl transition-all duration-300 hover:bg-white hover:text-blue-900 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-white/50"
                >
                    Login
                </Link>
            </div>
        </header>

        <!-- HERO SECTION -->
        <main class="relative z-10 flex-grow flex flex-col justify-center items-center px-6 py-16 md:py-20 max-w-7xl mx-auto w-full">
            
            <!-- Left Side Wave Decor -->
            <div class="absolute left-6 bottom-1/4 hidden lg:block pointer-events-none opacity-40 select-none animate-pulse" style="animation-duration: 4s;">
                <svg class="w-16 h-auto text-blue-300" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M0,10 Q15,0 30,10 T60,10 T90,10" />
                    <path d="M0,20 Q15,10 30,20 T60,20 T90,20" />
                    <path d="M0,30 Q15,20 30,30 T60,30 T90,30" />
                </svg>
            </div>

            <!-- Right Side Wave Decor + Sparkle -->
            <div class="absolute right-6 bottom-1/4 hidden lg:block pointer-events-none opacity-40 select-none flex flex-col items-center space-y-4">
                <svg class="w-16 h-auto text-blue-300" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="3">
                    <path d="M0,10 Q15,0 30,10 T60,10 T90,10" />
                    <path d="M0,20 Q15,10 30,20 T60,20 T90,20" />
                    <path d="M0,30 Q15,20 30,30 T60,30 T90,30" />
                </svg>
                <svg class="w-6 h-6 text-yellow-300 animate-spin" style="animation-duration: 10s;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 0l3 9 9 3-9 3-3 9-3-9-9-3 9-3z" />
                </svg>
            </div>

            <!-- Hero Text Group -->
            <div class="text-center max-w-4xl mx-auto mb-16">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-tight md:leading-none drop-shadow-md">
                    Sistem Informasi Kesehatan &amp; Kebugaran Siswa Boarding
                </h1>
                <p class="text-lg sm:text-xl text-white/80 mt-6 max-w-2xl mx-auto font-medium">
                    Pantau kesehatan dan kebugaran siswa secara terintegrasi.
                </p>
            </div>

            <!-- SERVICE CARDS SECTION (DITAMBAH FUNGSI KLIK & LINK) -->
            <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
                
                <!-- CARD 1: Layanan Klinik & Kesehatan (Redirects with role: petugas) -->
                <Link 
                    :href="route('login', { role: 'petugas' })" 
                    class="bg-white rounded-2xl shadow-xl p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl flex flex-col sm:flex-row items-center sm:items-start gap-6 border border-slate-100 group cursor-pointer block"
                >
                    <!-- Icon Container -->
                    <div class="flex items-center space-x-1.5 flex-shrink-0 bg-blue-50/70 p-4 rounded-2xl border border-blue-100 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-10 h-10 text-emerald-500 fill-current drop-shadow-sm" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9 2h6v7h7v6h-7v7H9v-7H2V9h7V2z" />
                        </svg>
                        <svg class="w-12 h-12 text-sky-600 drop-shadow-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 3h2M18 3h2" />
                            <path d="M5 3v4.5C5 9.43 6.57 11 8.5 11S12 9.43 12 7.5V3" />
                            <path d="M12 3v4.5C12 9.43 13.57 11 15.5 11S19 9.43 19 7.5V3" />
                            <path d="M12 11v4c0 2.2 1.8 4 4 4" />
                            <circle cx="16" cy="19" r="2.5" fill="currentColor" fill-opacity="0.1" />
                        </svg>
                    </div>

                    <!-- Text Contents -->
                    <div class="flex flex-col text-center sm:text-left">
                        <h2 class="text-xl sm:text-2xl font-bold text-blue-950 group-hover:text-blue-900 transition-colors">
                            Layanan Klinik &amp; Kesehatan
                        </h2>
                        <p class="text-slate-600 mt-3 text-sm sm:text-base leading-relaxed font-normal">
                            Kelola pemeriksaan berkala, kunjungan klinik, dan riwayat kesehatan siswa secara terstruktur.
                        </p>
                        <!-- Teks indikasi tambahan (biru) -->
                        <p class="text-sm text-blue-500 font-bold mt-4 flex items-center justify-center sm:justify-start">
                            Login sebagai Petugas Klinik &rarr;
                        </p>
                    </div>
                </Link>

                <!-- CARD 2: Layanan Tes Kebugaran (Redirects with role: pendamping) -->
                <Link 
                    :href="route('login', { role: 'pendamping' })" 
                    class="bg-white rounded-2xl shadow-xl p-8 transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl flex flex-col sm:flex-row items-center sm:items-start gap-6 border border-slate-100 group cursor-pointer block"
                >
                    <!-- Icon Container -->
                    <div class="flex items-center space-x-1 flex-shrink-0 bg-emerald-50/70 p-4 rounded-2xl border border-emerald-100 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-12 h-12 text-emerald-500 drop-shadow-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2 6h4M1 11h5M2 16h4" stroke-width="2.2" opacity="0.8" />
                            <circle cx="15" cy="4" r="2.2" fill="currentColor" />
                            <path d="M11 9l3 1.5 3-1M9 13l3.5-3v4l4 3.5M13 14l-2.5 4H8" />
                            <circle cx="19" cy="18" r="1.8" fill="currentColor" />
                        </svg>
                    </div>

                    <!-- Text Contents -->
                    <div class="flex flex-col text-center sm:text-left">
                        <h2 class="text-xl sm:text-2xl font-bold text-blue-950 group-hover:text-blue-900 transition-colors">
                            Layanan Tes Kebugaran
                        </h2>
                        <p class="text-slate-600 mt-3 text-sm sm:text-base leading-relaxed font-normal">
                            Catat dan pantau hasil tes koordinasi mata-tangan serta kebugaran fisik siswa untuk prestasi.
                        </p>
                        <!-- Teks indikasi tambahan (hijau) -->
                        <p class="text-sm text-green-500 font-bold mt-4 flex items-center justify-center sm:justify-start">
                            Login sebagai Pendamping &rarr;
                        </p>
                    </div>
                </Link>

            </div>
        </main>

        <!-- FOOTER -->
        <footer class="relative z-10 bg-blue-950/80 backdrop-blur-md py-6 border-t border-white/5 text-white/70 text-xs sm:text-sm">
            <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center justify-between gap-4 text-center lg:text-left font-normal">
                <div>
                    &copy; 2026 SMKN Jateng Semarang. All rights reserved.
                </div>
                <div class="flex items-center space-x-1 justify-center">
                    <span class="font-semibold text-white/95">Kontak Klinik Sekolah:</span>
                    <span>(024) 7643 1488</span>
                    <span class="text-white/30 px-1">|</span>
                    <a href="mailto:klinik@smknjateng.sch.id" class="hover:text-white hover:underline transition">klinik@smknjateng.sch.id</a>
                </div>
                <div class="flex items-center space-x-1 justify-center lg:justify-end">
                    <span class="font-semibold text-white/95">Alamat:</span>
                    <span>Jl. Brotojoyo No.1, Semarang Utara, Kota Semarang.</span>
                </div>
            </div>
        </footer>

    </div>
</template>
