<script setup>
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

import {
    UserIcon,
    ArrowLeftIcon,
    EyeIcon,
    EyeSlashIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});


// ======================================================
// FORM
// ======================================================

const form = useForm({
    name: props.user.name ?? '',
    email: props.user.email ?? '',
    role: props.user.role ?? '',
    status: props.user.status ?? 'aktif',
    password: '',
    password_confirmation: '',
});


// ======================================================
// STATE
// ======================================================

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);


// ======================================================
// SUBMIT
// ======================================================

const submit = () => {

    form.put(
        route(
            'admin.master.user.update',
            props.user.id
        ),
        {
            preserveScroll: true,
        }
    );

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

                <div
                    class="mb-2 flex items-center gap-2 text-sm text-slate-400"
                >

                    <Link
                        :href="route('admin.master.user.index')"
                        class="transition hover:text-blue-600"
                    >
                        Data User
                    </Link>

                    <span>/</span>

                    <span class="text-slate-500">
                        Edit
                    </span>

                </div>


                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Edit User
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Perbarui informasi dan hak akses pengguna.
                </p>

            </div>


            <!-- KEMBALI -->

            <Link
                :href="route('admin.master.user.index')"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >

                <ArrowLeftIcon class="h-5 w-5" />

                Kembali

            </Link>

        </div>


        <!-- ==================================================
             FORM CARD
        ================================================== -->

        <form
            @submit.prevent="submit"
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >

            <!-- ==================================================
                 CARD HEADER
            ================================================== -->

            <div
                class="border-b border-slate-100 px-5 py-4"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100"
                    >

                        <UserIcon
                            class="h-5 w-5 text-blue-700"
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
                            Perbarui informasi dasar pengguna.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FORM CONTENT
            ================================================== -->

            <div
                class="space-y-6 px-5 py-6"
            >

                <!-- ==================================================
                     INFORMASI DASAR
                ================================================== -->

                <div>

                    <h3
                        class="mb-4 text-sm font-bold text-slate-800"
                    >
                        Informasi Dasar
                    </h3>


                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >

                        <!-- NAMA -->

                        <div>

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
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                :class="{
                                    'border-rose-400 focus:border-rose-500 focus:ring-rose-100':
                                        form.errors.name
                                }"
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
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                :class="{
                                    'border-rose-400 focus:border-rose-500 focus:ring-rose-100':
                                        form.errors.email
                                }"
                            />

                            <p
                                v-if="form.errors.email"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.email }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     AKSES USER
                ================================================== -->

                <div
                    class="border-t border-slate-100 pt-6"
                >

                    <h3
                        class="mb-4 text-sm font-bold text-slate-800"
                    >
                        Hak Akses
                    </h3>


                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >

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
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                :class="{
                                    'border-rose-400':
                                        form.errors.status
                                }"
                            >

                                <option value="aktif">
                                    Aktif
                                </option>

                                <option value="tidak aktif">
                                    Tidak Aktif
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

                        <div>

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
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                :class="{
                                    'border-rose-400':
                                        form.errors.role
                                }"
                            >

                                <option value="">
                                    Pilih Role
                                </option>

                                <option value="admin">
                                    Admin
                                </option>

                                <option value="klinik">
                                    Petugas Klinik
                                </option>

                                <option value="tksi">
                                    TKSI
                                </option>

                            </select>

                            <p
                                v-if="form.errors.role"
                                class="mt-1.5 text-xs font-medium text-rose-600"
                            >
                                {{ form.errors.role }}
                            </p>

                        </div>

                    </div>


                    <!-- ROLE INFO -->

                    <div
                        class="mt-4 rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"
                    >

                        <p
                            class="text-xs font-bold text-blue-700"
                        >
                            Hak Akses
                        </p>

                        <p
                            v-if="form.role === 'admin'"
                            class="mt-0.5 text-[11px] leading-relaxed text-blue-600"
                        >
                            Admin dapat mengelola data master, pengguna,
                            periode, dan pengaturan sistem.
                        </p>

                        <p
                            v-else-if="form.role === 'klinik'"
                            class="mt-0.5 text-[11px] leading-relaxed text-blue-600"
                        >
                            Petugas Klinik dapat mengelola pemeriksaan
                            dan data kesehatan siswa.
                        </p>

                        <p
                            v-else-if="form.role === 'tksi'"
                            class="mt-0.5 text-[11px] leading-relaxed text-blue-600"
                        >
                            TKSI dapat mengelola data dan aktivitas
                            sesuai dengan kewenangan yang diberikan.
                        </p>

                        <p
                            v-else
                            class="mt-0.5 text-[11px] leading-relaxed text-blue-600"
                        >
                            Pilih role untuk melihat informasi hak akses.
                        </p>

                    </div>

                </div>


                <!-- ==================================================
                     PASSWORD
                ================================================== -->

                <div
                    class="border-t border-slate-100 pt-6"
                >

                    <div class="mb-4">

                        <h3
                            class="text-sm font-bold text-slate-800"
                        >
                            Ubah Password
                        </h3>

                        <p
                            class="mt-1 text-xs text-slate-400"
                        >
                            Kosongkan jika password pengguna tidak ingin
                            diubah.
                        </p>

                    </div>


                    <div
                        class="grid grid-cols-1 gap-5 md:grid-cols-2"
                    >

                        <!-- PASSWORD -->

                        <div>

                            <label
                                for="password"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Password Baru
                            </label>

                            <div class="relative">

                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="
                                        showPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    placeholder="Masukkan password baru"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                    :class="{
                                        'border-rose-400 focus:border-rose-500 focus:ring-rose-100':
                                            form.errors.password
                                    }"
                                />


                                <button
                                    type="button"
                                    @click="
                                        showPassword =
                                            !showPassword
                                    "
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600"
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


                        <!-- KONFIRMASI -->

                        <div>

                            <label
                                for="password_confirmation"
                                class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Konfirmasi Password
                            </label>

                            <div class="relative">

                                <input
                                    id="password_confirmation"
                                    v-model="
                                        form.password_confirmation
                                    "
                                    :type="
                                        showPasswordConfirmation
                                            ? 'text'
                                            : 'password'
                                    "
                                    placeholder="Ulangi password baru"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                                />

                                <button
                                    type="button"
                                    @click="
                                        showPasswordConfirmation =
                                            !showPasswordConfirmation
                                    "
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-600"
                                >

                                    <EyeSlashIcon
                                        v-if="
                                            showPasswordConfirmation
                                        "
                                        class="h-5 w-5"
                                    />

                                    <EyeIcon
                                        v-else
                                        class="h-5 w-5"
                                    />

                                </button>

                            </div>

                        </div>

                    </div>


                    <!-- PASSWORD INFO -->

                    <div
                        class="mt-4 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3"
                    >

                        <p
                            class="text-xs font-bold text-amber-700"
                        >
                            Perhatian
                        </p>

                        <p
                            class="mt-0.5 text-[11px] leading-relaxed text-amber-600"
                        >
                            Password hanya perlu diisi jika ingin mengganti
                            password pengguna. Jika dikosongkan, password
                            sebelumnya tetap digunakan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 FOOTER ACTION
            ================================================== -->

            <div
                class="flex flex-col-reverse gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-end"
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
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                >

                    <span
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"
                    ></span>

                    {{
                        form.processing
                            ? 'Menyimpan...'
                            : 'Simpan Perubahan'
                    }}

                </button>

            </div>

        </form>

    </div>

</AdminLayout>

</template>