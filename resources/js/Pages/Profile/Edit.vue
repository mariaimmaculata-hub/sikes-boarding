<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Kelola Profil - SiKes-Boarding" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-2xl font-extrabold text-slate-800">
                    Kelola Profil
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Kelola informasi akun dan keamanan profil Anda.
                </p>
            </div>
        </template>

        <div
            class="min-h-screen bg-gradient-to-br from-blue-50 via-slate-50 to-teal-50 py-8"
        >
            <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- PROFILE HEADER -->
                <div
                    class="overflow-hidden rounded-2xl bg-gradient-to-r from-blue-900 via-blue-700 to-teal-600 shadow-xl"
                >
                    <div class="px-6 py-8 sm:px-8">
                        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                            <!-- Avatar -->
                            <div
                                class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/20 text-3xl font-extrabold text-white shadow-lg ring-1 ring-white/30 backdrop-blur"
                            >
                                {{ $page.props.auth.user.name?.charAt(0)?.toUpperCase() }}
                            </div>

                            <div class="text-white">
                                <p
                                    class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-100"
                                >
                                    Akun Pengguna
                                </p>

                                <h1 class="mt-1 text-2xl font-extrabold">
                                    {{ $page.props.auth.user.name }}
                                </h1>

                                <p class="mt-1 text-sm text-blue-100">
                                    {{ $page.props.auth.user.email }}
                                </p>

                                <div class="mt-3">
                                    <span
                                        class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white ring-1 ring-white/20"
                                    >
                                        {{ $page.props.auth.user.role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INFORMASI PROFIL -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8"
                >
                    <div class="mb-6 border-b border-slate-100 pb-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M4.5 20.25a8.25 8.25 0 0115 0"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3
                                    class="text-lg font-bold text-slate-800"
                                >
                                    Informasi Profil
                                </h3>

                                <p class="text-sm text-slate-500">
                                    Perbarui nama dan alamat email akun Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <!-- PASSWORD -->
                <div
                    class="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg sm:p-8"
                >
                    <div class="mb-6 border-b border-slate-100 pb-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-teal-100 text-teal-700"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5.25 10.5h13.5v9H5.25v-9z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3
                                    class="text-lg font-bold text-slate-800"
                                >
                                    Keamanan Password
                                </h3>

                                <p class="text-sm text-slate-500">
                                    Pastikan akun Anda menggunakan password
                                    yang kuat.
                                </p>
                            </div>
                        </div>
                    </div>

                    <UpdatePasswordForm />
                </div>

                <!-- HAPUS AKUN -->
                <div
                    class="rounded-2xl border border-red-200 bg-white p-6 shadow-lg sm:p-8"
                >
                    <div class="mb-6 border-b border-red-100 pb-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M6 7h12M9 7V4h6v3m-8 0l1 13h6l1-13"
                                    />
                                </svg>
                            </div>

                            <div>
                                <h3
                                    class="text-lg font-bold text-slate-800"
                                >
                                    Hapus Akun
                                </h3>

                                <p class="text-sm text-slate-500">
                                    Hapus akun secara permanen dari sistem.
                                </p>
                            </div>
                        </div>
                    </div>

                    <DeleteUserForm />
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>