
<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import {
    BellIcon,
    ChevronDownIcon,
    UserIcon,
} from '@heroicons/vue/24/outline'


// ============================================================
// PAGE
// ============================================================

const page = usePage()

const user = computed(() => page.props.auth?.user)


// ============================================================
// PROFILE DROPDOWN
// ============================================================

const isProfileDropdownOpen = ref(false)

const toggleProfileDropdown = () => {
    isProfileDropdownOpen.value =
        !isProfileDropdownOpen.value
}


// ============================================================
// ROLE
// ============================================================

const roleLabel = computed(() => {
    const role = user.value?.role

    if (!role) {
        return ''
    }

    return role.charAt(0).toUpperCase() + role.slice(1)
})


// ============================================================
// BREADCRUMB
// ============================================================

const breadcrumbs = computed(() => {

    const path = window.location.pathname

    // Hilangkan slash di awal dan akhir
    const segments = path
        .split('/')
        .filter(Boolean)

    // Tidak ada path
    if (segments.length === 0) {
        return []
    }

    return segments.map((segment, index) => {

        const name = segment
            .replace(/-/g, ' ')
            .replace(/\b\w/g, char => char.toUpperCase())

        const url =
            '/' +
            segments
                .slice(0, index + 1)
                .join('/')

        return {
            name,
            url,
        }
    })
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

            <!-- ==================================================
                 LOGO
            ================================================== -->

            <div class="flex items-center space-x-3">

                <div class="flex items-center space-x-3">

                    <!-- LOGO ICON -->

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


                    <!-- APPLICATION NAME -->

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


            <!-- ==================================================
                 RIGHT NAVBAR
            ================================================== -->

            <div class="flex items-center space-x-4">

                <!-- ==================================================
                     NOTIFICATION
                ================================================== -->

                <Link
                    href="/notifikasi"
                    class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition"
                    aria-label="Notifikasi"
                >

                    <BellIcon class="w-6 h-6" />

                    <span
                        class="absolute top-1 right-1 bg-rose-500 text-white text-[9px] font-bold w-4 h-4 flex items-center justify-center rounded-full border border-white"
                    >
                        0
                    </span>

                </Link>


                <!-- ==================================================
                     PROFILE
                ================================================== -->

                <div class="relative">

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
                                    ?.toUpperCase() || 'U'
                            }}

                        </div>


                        <!-- USER INFO -->

                        <div
                            class="hidden sm:flex flex-col items-start"
                        >

                            <span
                                class="text-sm font-semibold text-slate-700"
                            >
                                {{ user?.name || 'User' }}
                            </span>

                            <span
                                class="text-[10px] font-medium text-slate-400"
                            >
                                {{ roleLabel }}
                            </span>

                        </div>


                        <!-- CHEVRON -->

                        <ChevronDownIcon
                            class="w-4 h-4 text-slate-400 transition-transform duration-200"
                            :class="{
                                'rotate-180':
                                    isProfileDropdownOpen
                            }"
                        />

                    </button>


                    <!-- ==================================================
                         PROFILE DROPDOWN
                    ================================================== -->

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

                            <!-- PROFILE -->

                            <Link
                                href="/profile"
                                class="flex items-center space-x-2 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition"
                                @click="
                                    isProfileDropdownOpen = false
                                "
                            >

                                <UserIcon
                                    class="w-4 h-4 text-slate-400"
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
                                class="w-full text-left flex items-center space-x-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 border-t border-slate-100 transition"
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
             PAGE CONTENT
        ================================================== -->

        <main
            class="flex-grow pt-16 flex flex-col justify-between min-h-screen min-w-0"
        >

            <div
                class="p-4 lg:p-6 space-y-6"
            >

                <!-- ==================================================
                     BREADCRUMB
                ================================================== -->

                <nav
                    v-if="breadcrumbs.length > 0"
                    class="flex flex-wrap items-center text-xs font-semibold text-slate-500 gap-x-2 gap-y-1"
                    aria-label="Breadcrumb"
                >

                    <template
                        v-for="(crumb, idx) in breadcrumbs"
                        :key="`${crumb.name}-${idx}`"
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
                            v-if="
                                idx <
                                breadcrumbs.length - 1
                            "
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
                     CONTENT
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

</template>