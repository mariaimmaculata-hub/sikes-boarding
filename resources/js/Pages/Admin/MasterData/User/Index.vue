<script setup>
import { computed, ref } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';

import {
    UserPlusIcon,
    MagnifyingGlassIcon,
    FunnelIcon,
    EyeIcon,
    PencilSquareIcon,
    TrashIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
    XMarkIcon,
    ExclamationTriangleIcon,
} from '@heroicons/vue/24/outline';


// ======================================================
// PROPS
// ======================================================

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
});


// ======================================================
// STATE
// ======================================================

const search = ref('');
const statusFilter = ref('');
const roleFilter = ref('');

const showFilter = ref(false);


// ======================================================
// PAGE
// ======================================================

const page = usePage();

const flashSuccess = computed(() => {
    return page.props.flash?.success ?? null;
});

const flashError = computed(() => {
    return page.props.flash?.error ?? null;
});


// ======================================================
// FILTER DATA
// ======================================================

const filteredUsers = computed(() => {

    let data = props.users.data ?? [];


    // ==================================================
    // SEARCH
    // ==================================================

    if (search.value.trim()) {

        const keyword = search.value
            .toLowerCase()
            .trim();

        data = data.filter((user) => {

            return (
                String(user.name ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(user.email ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(user.username ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(user.role ?? '')
                    .toLowerCase()
                    .includes(keyword)

                ||

                String(user.no_hp ?? '')
                    .toLowerCase()
                    .includes(keyword)
            );

        });

    }


    // ==================================================
    // STATUS
    // ==================================================

    if (statusFilter.value) {

        data = data.filter(
            (user) =>
                String(user.status ?? '').toLowerCase() ===
                statusFilter.value
        );

    }


    // ==================================================
    // ROLE
    // ==================================================

    if (roleFilter.value) {

        data = data.filter(
            (user) =>
                String(user.role ?? '').toLowerCase() ===
                roleFilter.value
        );

    }


    return data;

});


// ======================================================
// FILTER AKTIF
// ======================================================

const hasActiveFilter = computed(() => {

    return Boolean(
        search.value ||
        statusFilter.value ||
        roleFilter.value
    );

});


// ======================================================
// JUMLAH FILTER TAMBAHAN AKTIF
// ======================================================

const activeFilterCount = computed(() => {

    let count = 0;

    if (roleFilter.value) {
        count++;
    }

    return count;

});


// ======================================================
// RESET FILTER
// ======================================================

const resetFilter = () => {

    search.value = '';
    statusFilter.value = '';
    roleFilter.value = '';

};


// ======================================================
// FORMAT ROLE
// ======================================================

const formatRole = (role) => {

    const roles = {
        admin: 'Admin',
        klinik: 'Petugas Klinik',
        tksi: 'TKSI',
    };

    return roles[String(role ?? '').toLowerCase()]
        ?? role
        ?? '-';

};


// ======================================================
// ROLE BADGE
// ======================================================

const getRoleBadge = (role) => {

    switch (String(role ?? '').toLowerCase()) {

        case 'admin':
            return 'border-blue-200 bg-blue-50 text-blue-700';

        case 'klinik':
            return 'border-emerald-200 bg-emerald-50 text-emerald-700';

        case 'tksi':
            return 'border-purple-200 bg-purple-50 text-purple-700';

        default:
            return 'border-slate-200 bg-slate-50 text-slate-600';

    }

};


// ======================================================
// STATUS BADGE
// ======================================================

const getStatusBadge = (status) => {

    return String(status ?? '').toLowerCase() === 'aktif'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
        : 'border-slate-200 bg-slate-50 text-slate-500';

};


// ======================================================
// DELETE
// ======================================================

const deleteUser = (user) => {

    const confirmed = confirm(
        `Apakah Anda yakin ingin menghapus user "${user.name}"?`
    );

    if (!confirmed) {
        return;
    }

    router.delete(
        route(
            'admin.master.user.destroy',
            user.id
        ),
        {
            preserveScroll: true,
        }
    );

};


// ======================================================
// PAGINATION
// ======================================================

const goToPage = (url) => {

    if (!url) {
        return;
    }

    router.get(
        url,
        {},
        {
            preserveScroll: true,
            preserveState: true,
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

                <h1
                    class="text-2xl font-bold text-slate-800"
                >
                    Data User
                </h1>

                <p
                    class="mt-1 text-sm text-slate-500"
                >
                    Kelola hak akses pengguna sistem.
                </p>

            </div>


            <!-- TAMBAH USER -->

            <div class="flex flex-col gap-2 sm:flex-row">

                <Link
                    :href="route('admin.master.user.create')"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-800"
                >

                    <UserPlusIcon class="h-5 w-5" />

                    Tambah User

                </Link>

            </div>

        </div>


        <!-- ==================================================
             FLASH SUCCESS
        ================================================== -->

        <div
            v-if="flashSuccess"
            class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700"
        >

            <span>
                {{ flashSuccess }}
            </span>

            <button
                type="button"
                @click="page.props.flash.success = null"
                class="rounded-lg p-1 hover:bg-emerald-100"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FLASH ERROR
        ================================================== -->

        <div
            v-if="flashError"
            class="flex items-start justify-between gap-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"
        >

            <div class="flex items-start gap-3">

                <div
                    class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-100"
                >

                    <ExclamationTriangleIcon
                        class="h-4 w-4 text-rose-600"
                    />

                </div>

                <div>

                    <p class="font-bold text-rose-800">
                        Data user tidak dapat dihapus
                    </p>

                    <p class="mt-0.5 text-xs text-rose-600">
                        {{ flashError }}
                    </p>

                </div>

            </div>

            <button
                type="button"
                @click="page.props.flash.error = null"
                class="rounded-lg p-1 text-rose-500 transition hover:bg-rose-100 hover:text-rose-700"
                title="Tutup"
            >

                <XMarkIcon class="h-4 w-4" />

            </button>

        </div>


        <!-- ==================================================
             FILTER CARD
        ================================================== -->

        <div
            class="rounded-2xl border border-slate-200 bg-white shadow-sm"
        >


            <!-- SEARCH + STATUS + FILTER -->

            <div
                class="grid grid-cols-1 gap-3 p-4 md:grid-cols-[3fr_2fr_1fr]"
            >


                <!-- SEARCH -->

                <div class="relative w-full">

                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    />

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari nama, email, username, atau role..."
                        class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
                    />

                </div>


                <!-- STATUS -->

                <div class="w-full">

                    <select
                        v-model="statusFilter"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-medium text-slate-600 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                    >

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="tidak aktif">
                            Tidak Aktif
                        </option>

                    </select>

                </div>


                <!-- FILTER + RESET -->

                <div class="flex w-full gap-2">

                    <!-- FILTER -->

                    <button
                        type="button"
                        @click="showFilter = !showFilter"
                        :class="[
                            'flex-1 inline-flex items-center justify-center gap-2 rounded-xl border px-4 py-2.5 text-sm font-semibold transition',

                            showFilter || activeFilterCount > 0
                                ? 'border-blue-200 bg-blue-50 text-blue-700'
                                : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'
                        ]"
                    >

                        <FunnelIcon class="h-4 w-4" />

                        <span>
                            Filter
                        </span>

                        <span
                            v-if="activeFilterCount > 0"
                            class="flex h-5 min-w-5 items-center justify-center rounded-full bg-blue-600 px-1.5 text-[10px] font-bold text-white"
                        >

                            {{ activeFilterCount }}

                        </span>

                    </button>


                    <!-- RESET -->

                    <button
                        v-if="hasActiveFilter"
                        type="button"
                        @click="resetFilter"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                    >

                        <XMarkIcon class="h-4 w-4" />

                        Reset

                    </button>

                </div>

            </div>


            <!-- ==================================================
                 FILTER TAMBAHAN
            ================================================== -->

            <div
                v-if="showFilter"
                class="border-t border-slate-100 bg-slate-50/70 px-4 py-4"
            >

                <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-3"
                >


                    <!-- ROLE -->

                    <div>

                        <label
                            class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-500"
                        >
                            Peran / Role
                        </label>

                        <select
                            v-model="roleFilter"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100"
                        >

                            <option value="">
                                Semua Peran
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

                    </div>


                    <!-- INFO ROLE -->

                    <div class="flex items-end">

                        <div
                            class="w-full rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"
                        >

                            <p
                                class="text-xs font-bold text-blue-700"
                            >
                                Hak Akses
                            </p>

                            <p
                                class="mt-0.5 text-[11px] leading-relaxed text-blue-600"
                            >
                                Admin, Petugas Klinik, dan TKSI
                                memiliki akses sesuai peran masing-masing.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- FILTER AKTIF -->

                <div
                    v-if="activeFilterCount > 0"
                    class="mt-4 flex flex-wrap items-center gap-2"
                >

                    <span
                        class="text-xs font-medium text-slate-400"
                    >
                        Filter aktif:
                    </span>


                    <!-- ROLE -->

                    <span
                        v-if="roleFilter"
                        :class="[
                            'rounded-full px-2.5 py-1 text-xs font-semibold',

                            roleFilter === 'admin'
                                ? 'bg-blue-100 text-blue-700'

                                : roleFilter === 'klinik'
                                    ? 'bg-emerald-100 text-emerald-700'

                                    : 'bg-purple-100 text-purple-700'
                        ]"
                    >

                        {{ formatRole(roleFilter) }}

                    </span>

                </div>

            </div>

        </div>


        <!-- ==================================================
             TABLE
        ================================================== -->

        <div
            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
        >


            <!-- TABLE HEADER -->

            <div
                class="flex items-center justify-between border-b border-slate-100 px-5 py-4"
            >

                <div>

                    <h2
                        class="text-sm font-bold text-slate-800"
                    >
                        Daftar User
                    </h2>

                    <p
                        class="mt-0.5 text-xs text-slate-400"
                    >

                        Menampilkan
                        {{ filteredUsers.length }}
                        user pada halaman ini.

                    </p>

                </div>

            </div>


            <!-- ==================================================
                 DESKTOP TABLE
            ================================================== -->

            <div
                class="hidden overflow-x-auto lg:block"
            >

                <table class="min-w-full">

                    <thead>

                        <tr
                            class="border-b border-slate-200 bg-slate-50"
                        >

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                No
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Nama User
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Email / Username
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Role
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-center text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Status
                            </th>

                            <th
                                class="whitespace-nowrap px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500"
                            >
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        class="divide-y divide-slate-100"
                    >

                        <tr
                            v-for="(user, index) in filteredUsers"
                            :key="user.id"
                            class="transition hover:bg-slate-50"
                        >


                            <!-- NO -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-sm text-slate-500"
                            >

                                {{
                                    (users.current_page - 1) *
                                    users.per_page +
                                    index +
                                    1
                                }}

                            </td>


                            <!-- NAMA -->

                            <td class="px-5 py-4">

                                <div
                                    class="flex items-center gap-3"
                                >

                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-xs font-bold text-blue-700"
                                    >

                                        {{
                                            user.name
                                                ?.charAt(0)
                                                ?.toUpperCase()
                                        }}

                                    </div>


                                    <div>

                                        <p
                                            class="whitespace-nowrap text-sm font-semibold text-slate-800"
                                        >
                                            {{ user.name }}
                                        </p>

                                        <p
                                            class="text-xs text-slate-400"
                                        >
                                            {{ user.no_hp || '-' }}
                                        </p>

                                    </div>

                                </div>

                            </td>


                            <!-- EMAIL / USERNAME -->

                            <td
                                class="px-5 py-4"
                            >

                                <p
                                    class="whitespace-nowrap text-sm font-semibold text-slate-700"
                                >
                                    {{ user.email || '-' }}
                                </p>

                                <p
                                    v-if="user.username"
                                    class="mt-0.5 text-xs text-slate-400"
                                >
                                    {{ user.username }}
                                </p>

                            </td>


                            <!-- ROLE -->

                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <span
                                    :class="[
                                        'inline-flex rounded-full border px-2.5 py-1 text-xs font-bold',
                                        getRoleBadge(user.role)
                                    ]"
                                >

                                    {{ formatRole(user.role) }}

                                </span>

                            </td>


                            <!-- STATUS -->

                            <td
                                class="whitespace-nowrap px-5 py-4 text-center"
                            >

                                <span
                                    :class="[
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-bold',

                                        String(user.status ?? '').toLowerCase() === 'aktif'
                                            ? 'bg-emerald-100 text-emerald-700'
                                            : 'bg-slate-100 text-slate-500'
                                    ]"
                                >

                                    {{
                                        String(user.status ?? '').toLowerCase() === 'aktif'
                                            ? 'Aktif'
                                            : 'Tidak Aktif'
                                    }}

                                </span>

                            </td>


                            <!-- AKSI -->

                            <td
                                class="whitespace-nowrap px-5 py-4"
                            >

                                <div
                                    class="flex items-center justify-end gap-1"
                                >


                                    <!-- DETAIL -->

                                    <Link
                                        v-if="route().has('admin.master.user.show')"
                                        :href="route(
                                            'admin.master.user.show',
                                            user.id
                                        )"
                                        title="Detail"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"
                                    >

                                        <EyeIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>


                                    <!-- EDIT -->

                                    <Link
                                        :href="route(
                                            'admin.master.user.edit',
                                            user.id
                                        )"
                                        title="Edit"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-amber-50 hover:text-amber-600"
                                    >

                                        <PencilSquareIcon
                                            class="h-4 w-4"
                                        />

                                    </Link>


                                    <!-- DELETE -->

                                    <button
                                        v-if="user.id !== 1"
                                        type="button"
                                        @click="deleteUser(user)"
                                        title="Hapus"
                                        class="rounded-lg p-2 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"
                                    >

                                        <TrashIcon
                                            class="h-4 w-4"
                                        />

                                    </button>

                                </div>

                            </td>

                        </tr>


                        <!-- EMPTY -->

                        <tr
                            v-if="filteredUsers.length === 0"
                        >

                            <td
                                colspan="6"
                                class="px-5 py-16 text-center"
                            >

                                <div
                                    class="mx-auto flex max-w-sm flex-col items-center"
                                >

                                    <div
                                        class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                    >

                                        <MagnifyingGlassIcon
                                            class="h-6 w-6 text-slate-400"
                                        />

                                    </div>

                                    <p
                                        class="text-sm font-bold text-slate-700"
                                    >
                                        Data user tidak ditemukan
                                    </p>

                                    <p
                                        class="mt-1 text-xs text-slate-400"
                                    >
                                        Coba ubah kata pencarian atau
                                        filter yang digunakan.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- ==================================================
                 MOBILE CARD
            ================================================== -->

            <div
                class="divide-y divide-slate-100 lg:hidden"
            >

                <div
                    v-for="(user, index) in filteredUsers"
                    :key="user.id"
                    class="p-4"
                >

                    <div
                        class="flex items-start justify-between gap-3"
                    >

                        <div
                            class="flex min-w-0 items-center gap-3"
                        >

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700"
                            >

                                {{
                                    user.name
                                        ?.charAt(0)
                                        ?.toUpperCase()
                                }}

                            </div>


                            <div class="min-w-0">

                                <p
                                    class="truncate text-sm font-bold text-slate-800"
                                >
                                    {{ user.name }}
                                </p>

                                <p
                                    class="truncate text-xs text-slate-400"
                                >
                                    {{ user.email || '-' }}
                                </p>

                            </div>

                        </div>


                        <!-- STATUS -->

                        <span
                            :class="[
                                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold',

                                String(user.status ?? '').toLowerCase() === 'aktif'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-slate-100 text-slate-500'
                            ]"
                        >

                            {{
                                String(user.status ?? '').toLowerCase() === 'aktif'
                                    ? 'Aktif'
                                    : 'Tidak Aktif'
                            }}

                        </span>

                    </div>


                    <!-- USER INFO -->

                    <div
                        class="mt-4 grid grid-cols-2 gap-3 text-xs"
                    >


                        <!-- USERNAME -->

                        <div>

                            <p class="text-slate-400">
                                Username
                            </p>

                            <p
                                class="mt-0.5 truncate font-semibold text-slate-700"
                            >
                                {{ user.username || '-' }}
                            </p>

                        </div>


                        <!-- ROLE -->

                        <div>

                            <p class="text-slate-400">
                                Role
                            </p>

                            <span
                                :class="[
                                    'mt-0.5 inline-flex rounded-full border px-2 py-0.5 text-[10px] font-bold',

                                    getRoleBadge(user.role)
                                ]"
                            >

                                {{ formatRole(user.role) }}

                            </span>

                        </div>


                        <!-- NO HP -->

                        <div>

                            <p class="text-slate-400">
                                No. HP
                            </p>

                            <p
                                class="mt-0.5 font-semibold text-slate-700"
                            >
                                {{ user.no_hp || '-' }}
                            </p>

                        </div>


                        <!-- EMAIL -->

                        <div>

                            <p class="text-slate-400">
                                Email
                            </p>

                            <p
                                class="mt-0.5 truncate font-semibold text-slate-700"
                            >
                                {{ user.email || '-' }}
                            </p>

                        </div>

                    </div>


                    <!-- ACTION -->

                    <div
                        class="mt-4 flex items-center justify-end gap-1 border-t border-slate-100 pt-3"
                    >


                        <!-- DETAIL -->

                        <Link
                            v-if="route().has('admin.master.user.show')"
                            :href="route(
                                'admin.master.user.show',
                                user.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-blue-600 hover:bg-blue-50"
                        >

                            <EyeIcon class="h-4 w-4" />

                            Detail

                        </Link>


                        <!-- EDIT -->

                        <Link
                            :href="route(
                                'admin.master.user.edit',
                                user.id
                            )"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                        >

                            <PencilSquareIcon
                                class="h-4 w-4"
                            />

                            Edit

                        </Link>


                        <!-- DELETE -->

                        <button
                            v-if="user.id !== 1"
                            type="button"
                            @click="deleteUser(user)"
                            class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                        >

                            <TrashIcon class="h-4 w-4" />

                            Hapus

                        </button>

                    </div>

                </div>


                <!-- MOBILE EMPTY -->

                <div
                    v-if="filteredUsers.length === 0"
                    class="px-5 py-16 text-center"
                >

                    <p
                        class="text-sm font-bold text-slate-700"
                    >
                        Data user tidak ditemukan
                    </p>

                    <p
                        class="mt-1 text-xs text-slate-400"
                    >
                        Coba ubah pencarian atau filter.
                    </p>

                </div>

            </div>


            <!-- ==================================================
                 PAGINATION
            ================================================== -->

            <div
                v-if="users.last_page > 1"
                class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <p
                    class="text-xs font-medium text-slate-500"
                >

                    Menampilkan

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ users.from ?? 0 }}
                    </span>

                    –

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ users.to ?? 0 }}
                    </span>

                    dari

                    <span
                        class="font-bold text-slate-700"
                    >
                        {{ users.total }}
                    </span>

                    user

                </p>


                <div
                    class="flex items-center gap-1"
                >


                    <!-- PREVIOUS -->

                    <button
                        type="button"
                        :disabled="!users.prev_page_url"
                        @click="goToPage(users.prev_page_url)"
                        class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >

                        <ChevronLeftIcon
                            class="h-4 w-4"
                        />

                    </button>


                    <!-- PAGE NUMBERS -->

                    <template
                        v-for="link in users.links.slice(1, -1)"
                        :key="link.label"
                    >

                        <button
                            v-if="link.url"
                            type="button"
                            @click="goToPage(link.url)"
                            :class="[

                                'min-w-9 rounded-lg border px-2.5 py-2 text-xs font-bold transition',

                                link.active
                                    ? 'border-blue-700 bg-blue-700 text-white'
                                    : 'border-slate-200 text-slate-600 hover:bg-slate-50'

                            ]"
                        >

                            <span
                                v-html="link.label"
                            ></span>

                        </button>


                        <span
                            v-else
                            class="px-1 text-slate-400"
                            v-html="link.label"
                        ></span>

                    </template>


                    <!-- NEXT -->

                    <button
                        type="button"
                        :disabled="!users.next_page_url"
                        @click="goToPage(users.next_page_url)"
                        class="rounded-lg border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    >

                        <ChevronRightIcon
                            class="h-4 w-4"
                        />

                    </button>

                </div>

            </div>

        </div>

    </div>

</AdminLayout>

</template>