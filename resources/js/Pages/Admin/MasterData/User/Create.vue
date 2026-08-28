<script setup>
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

import {
    ArrowLeftIcon,
    UserPlusIcon,
    EyeIcon,
    EyeSlashIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PAGE
// ======================================================

const page = usePage();


// ======================================================
// FORM
// ======================================================

const form = useForm({
    name: '',
    email: '',
    role: '',
    status: 'aktif',
    password: '',
    password_confirmation: '',
});


// ======================================================
// PASSWORD VISIBILITY
// ======================================================

const showPassword = ref(false);


// ======================================================
// ROLE OPTIONS
// ======================================================

const roleOptions = [
    {
        value: 'admin',
        label: 'Admin',
        description: 'Mengelola seluruh data dan pengguna sistem.',
    },
    {
        value: 'klinik',
        label: 'Petugas Klinik',
        description: 'Mengelola pemeriksaan dan data kesehatan siswa.',
    },
    {
        value: 'tksi',
        label: 'TKSI',
        description: 'Mengelola data sesuai kewenangan TKSI.',
    },
];


// ======================================================
// STATUS OPTIONS
// ======================================================

const statusOptions = [
    {
        value: 'aktif',
        label: 'Aktif',
    },
    {
        value: 'tidak aktif',
        label: 'Tidak Aktif',
    },
];


// ======================================================
// SUBMIT
// ======================================================

const submit = () => {

    form.post(
        route('admin.master.user.store'),
        {
            preserveScroll: true,
        }
    );

};


// ======================================================
// ERROR HELPER
// ======================================================

const hasError = (field) => {
    return Boolean(form.errors[field]);
};

</script>


<template>

<AdminLayout>

    <div class="space-y-6">


        <!-- ==================================================
             HEADER
        ================================================== -->

        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >

            <div>

                <div class="flex items-center gap-3">

                    <Link
                        :href="route('admin.master.user.index')"
                        class="rounded-xl border border-slate-200 bg-white p-2 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                        title="Kembali"
                    >

                        <ArrowLeftIcon class="h-5 w-5" />

                    </Link>


                    <div>

                        <h1
                            class="text-2xl font-bold text-slate-800"
                        >
                            Tambah User
                        </h1>

                        <p
                            class="mt-1 text-sm text-slate-500"
                        >
                            Tambahkan pengguna baru ke dalam sistem.
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- ==================================================
             FORM
        ================================================== -->

        <form
            @submit.prevent="submit"
            class="space-y-6"
        >


            <!-- ==================================================
                 DATA USER
            ================================================== -->

            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            >


                <!-- CARD HEADER -->

                <div
                    class="border-b border-slate-100 px-5 py-4"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-100"
                        >

                            <UserPlusIcon
                                class="h-5 w-5 text-pink-700"
                            />

                        </div>


                        <div>

                            <h2
                                class="text-sm font-bold text-slate-800"
                            >
                                Informasi User
                            </h2>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Masukkan informasi dasar pengguna.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- FORM CONTENT -->

                <div class="p-5">

                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >


                        <!-- NAMA -->

                        <div class="md:col-span-2">

                            <label
                                for="name"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama Lengkap
                                <span class="text-rose-500">*</span>
                            </label>


                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    hasError('name')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />


                            <p
                                v-if="form.errors.name"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.name }}
                            </p>

                        </div>


                        <!-- EMAIL -->

                        <div>

                            <label
                                for="email"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Email
                                <span class="text-rose-500">*</span>
                            </label>


                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="contoh@email.com"
                                autocomplete="email"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                                    hasError('email')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            />


                            <p
                                v-if="form.errors.email"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.email }}
                            </p>

                        </div>


                        <!-- STATUS -->

                        <div>

                            <label
                                for="status"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status
                                <span class="text-rose-500">*</span>
                            </label>


                            <select
                                id="status"
                                v-model="form.status"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:ring-2',
                                    hasError('status')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            >

                                <option
                                    v-for="status in statusOptions"
                                    :key="status.value"
                                    :value="status.value"
                                >
                                    {{ status.label }}
                                </option>

                            </select>


                            <p
                                v-if="form.errors.status"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.status }}
                            </p>

                        </div>


                        <!-- ROLE -->

                        <div class="md:col-span-2">

                            <label
                                for="role"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Peran / Role
                                <span class="text-rose-500">*</span>
                            </label>


                            <select
                                id="role"
                                v-model="form.role"
                                :class="[
                                    'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:ring-2',
                                    hasError('role')
                                        ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                                        : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
                                ]"
                            >

                                <option
                                    value=""
                                    disabled
                                >
                                    Pilih peran user
                                </option>


                                <option
                                    v-for="role in roleOptions"
                                    :key="role.value"
                                    :value="role.value"
                                >
                                    {{ role.label }}
                                </option>

                            </select>


                            <p
                                v-if="form.errors.role"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.role }}
                            </p>


                            <!-- ROLE INFO -->

                            <div
                                v-if="form.role"
                                class="mt-3 rounded-xl border border-pink-100 bg-pink-50 px-4 py-3"
                            >

                                <p
                                    class="text-xs font-bold text-pink-700"
                                >
                                    {{
                                        roleOptions.find(
                                            role => role.value === form.role
                                        )?.label
                                    }}
                                </p>

                                <p
                                    class="mt-0.5 text-[11px] leading-relaxed text-pink-600"
                                >
                                    {{
                                        roleOptions.find(
                                            role => role.value === form.role
                                        )?.description
                                    }}
                                </p>

                            </div>

                        </div>


                        
<!-- ==================================================
     PASSWORD
================================================== -->

<div>

    <label
        for="password"
        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
    >
        Password
        <span class="text-rose-500">*</span>
    </label>

    <div class="relative">

        <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Masukkan password"
            autocomplete="new-password"
            :class="[
                'w-full rounded-xl border bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                form.errors.password
                    ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                    : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
            ]"
        />

        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
        >

            <EyeSlashIcon
                v-if="showPassword"
                class="h-5 w-5"
            />

            <EyeIcon
                v-else
                class="h-5 w-5"
            />

        </button>

    </div>

    <p
        v-if="form.errors.password"
        class="mt-1.5 text-xs font-medium text-rose-600"
    >
        {{ form.errors.password }}
    </p>

</div>


<!-- ==================================================
     KONFIRMASI PASSWORD
================================================== -->

<div>

    <label
        for="password_confirmation"
        class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
    >
        Konfirmasi Password
        <span class="text-rose-500">*</span>
    </label>

    <div class="relative">

        <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Ulangi password"
            autocomplete="new-password"
            :class="[
                'w-full rounded-xl border bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-2',
                form.errors.password_confirmation
                    ? 'border-rose-300 focus:border-rose-500 focus:ring-rose-100'
                    : 'border-slate-200 focus:border-pink-500 focus:ring-pink-100'
            ]"
        />

        <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
        >

            <EyeSlashIcon
                v-if="showPassword"
                class="h-5 w-5"
            />

            <EyeIcon
                v-else
                class="h-5 w-5"
            />

        </button>

    </div>

    <p
        v-if="form.errors.password_confirmation"
        class="mt-1.5 text-xs font-medium text-rose-600"
    >
        {{ form.errors.password_confirmation }}
    </p>

</div>


                    </div>

                </div>

            </div>


            <!-- ==================================================
                 INFORMASI HAK AKSES
            ================================================== -->

            <div
                class="rounded-2xl border border-pink-100 bg-pink-50 px-5 py-4"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-pink-100"
                    >

                        <UserPlusIcon
                            class="h-4 w-4 text-pink-700"
                        />

                    </div>


                    <div>

                        <p
                            class="text-sm font-bold text-pink-800"
                        >
                            Perhatian Hak Akses
                        </p>

                        <p
                            class="mt-1 text-xs leading-relaxed text-pink-600"
                        >
                            Pastikan role yang dipilih sesuai dengan tugas
                            dan tanggung jawab pengguna. Hak akses pengguna
                            akan mengikuti role yang diberikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 ACTION
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
            >

                <Link
                    :href="route('admin.master.user.index')"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </Link>


                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-pink-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-pink-800 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <UserPlusIcon
                        class="h-5 w-5"
                    />

                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan User'
                    }}

                </button>

            </div>

        </form>

    </div>

</AdminLayout>

</template>
