<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import TksiLayout from '@/Layouts/TksiLayout.vue'

import {
    BellIcon,
    CheckCircleIcon,
    ExclamationTriangleIcon,
    InformationCircleIcon,
    XCircleIcon,
    ArrowLeftIcon,
    CheckIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'


// ============================================================
// PROPS
// ============================================================

const props = defineProps({
    notifications: {
        type: Array,
        default: () => [],
    },

    unreadCount: {
        type: Number,
        default: 0,
    },

    role: {
        type: String,
        default: '',
    },
})


// ============================================================
// STATE
// ============================================================

// Mode hapus
const isDeleteMode = ref(false)

// ID notifikasi yang dipilih
const selectedIds = ref([])


// ============================================================
// ROLE
// ============================================================

const roleName = computed(() => {
    const roles = {
        admin: 'Admin',
        klinik: 'Klinik',
        tksi: 'TKSI',
    }

    return roles[props.role] ?? props.role
})


// ============================================================
// LAYOUT
// ============================================================

const currentLayout = computed(() => {
    const layouts = {
        admin: AdminLayout,
        klinik: KlinikLayout,
        tksi: TksiLayout,
    }

    return layouts[props.role] ?? KlinikLayout
})


// ============================================================
// STYLE TIPE NOTIFIKASI
// ============================================================

const typeClass = (type) => {
    return {
        success: 'bg-emerald-100 text-emerald-600',
        warning: 'bg-amber-100 text-amber-600',
        danger: 'bg-red-100 text-red-600',
        info: 'bg-blue-100 text-blue-600',
    }[type] ?? 'bg-blue-100 text-blue-600'
}


// ============================================================
// ICON TIPE NOTIFIKASI
// ============================================================

const typeIcon = (type) => {
    return {
        success: CheckCircleIcon,
        warning: ExclamationTriangleIcon,
        danger: XCircleIcon,
        info: InformationCircleIcon,
    }[type] ?? InformationCircleIcon
}


// ============================================================
// CEK READ
// ============================================================

const isRead = (notification) => {
    if (typeof notification.read === 'boolean') {
        return notification.read
    }

    return notification.read_at !== null &&
           notification.read_at !== undefined
}


// ============================================================
// BUKA NOTIFIKASI
// ============================================================

const openNotification = (notification) => {

    // Kalau sedang mode hapus,
    // klik card hanya memilih checkbox
    if (isDeleteMode.value) {
        toggleSelection(notification.id)
        return
    }

    router.visit(
        route('notifikasi.show', notification.id)
    )
}


// ============================================================
// MASUK MODE HAPUS
// ============================================================

const startDeleteMode = () => {
    isDeleteMode.value = true
    selectedIds.value = []
}


// ============================================================
// BATAL MODE HAPUS
// ============================================================

const cancelDeleteMode = () => {
    isDeleteMode.value = false
    selectedIds.value = []
}


// ============================================================
// PILIH / BATAL PILIH SATU
// ============================================================

const toggleSelection = (id) => {

    if (selectedIds.value.includes(id)) {

        selectedIds.value =
            selectedIds.value.filter(
                selectedId => selectedId !== id
            )

        return
    }

    selectedIds.value.push(id)
}


// ============================================================
// CEK APAKAH SEMUA TERPILIH
// ============================================================

const isAllSelected = computed(() => {

    if (!props.notifications.length) {
        return false
    }

    return selectedIds.value.length ===
        props.notifications.length
})


// ============================================================
// PILIH SEMUA
// ============================================================

const toggleSelectAll = () => {

    if (isAllSelected.value) {

        selectedIds.value = []

        return
    }

    selectedIds.value =
        props.notifications.map(
            notification => notification.id
        )
}


// ============================================================
// HAPUS NOTIFIKASI YANG DIPILIH
// ============================================================

const deleteSelected = () => {

    if (selectedIds.value.length === 0) {
        return
    }

    const jumlah = selectedIds.value.length

    const confirmed = window.confirm(
        `Hapus ${jumlah} notifikasi yang dipilih?`
    )

    if (!confirmed) {
        return
    }

    router.delete(
        route('notifikasi.destroy-multiple'),
        {
            data: {
                ids: selectedIds.value,
            },

            preserveScroll: true,

            onSuccess: () => {
                isDeleteMode.value = false
                selectedIds.value = []
            },
        }
    )
}


// ============================================================
// TANDAI SEMUA DIBACA
// ============================================================

const markAllRead = () => {

    if (props.unreadCount <= 0) {
        return
    }

    router.patch(
        route('notifikasi.read-all'),
        {},
        {
            preserveScroll: true,
            preserveState: true,
        }
    )
}
</script>


<template>

    <Head :title="`Notifikasi ${roleName}`" />

    <component :is="currentLayout">

        <div class="space-y-6">

            <!-- ================================================= -->
            <!-- HEADER -->
            <!-- ================================================= -->

            <div
                class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
            >

                <!-- LEFT -->

                <div class="flex items-center gap-3">

                    <Link
                        :href="route('dashboard')"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:bg-slate-50"
                    >

                        <ArrowLeftIcon class="h-4 w-4" />

                    </Link>


                    <div>

                        <h1 class="text-xl font-bold text-slate-800">
                            Notifikasi
                        </h1>

                        <p class="mt-1 text-sm text-slate-500">
                            Pemberitahuan khusus untuk akun
                            {{ roleName }}.
                        </p>

                    </div>

                </div>


                <!-- ================================================= -->
                <!-- ACTION -->
                <!-- ================================================= -->

                <div class="flex flex-wrap items-center gap-2">

                    <!-- NORMAL MODE -->

                    <template v-if="!isDeleteMode">

                        <!-- TANDAI SEMUA -->

                        <button
                            v-if="unreadCount > 0"
                            type="button"
                            @click="markAllRead"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                        >

                            <CheckIcon class="h-4 w-4" />

                            Tandai semua dibaca

                        </button>


                        <!-- HAPUS -->

                        <button
                            v-if="notifications.length > 0"
                            type="button"
                            @click="startDeleteMode"
                            class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-50"
                        >

                            <TrashIcon class="h-4 w-4" />

                            Hapus

                        </button>

                    </template>


                    <!-- ================================================= -->
                    <!-- DELETE MODE -->
                    <!-- ================================================= -->

                    <template v-else>

                        <!-- PILIH SEMUA -->

                        <button
                            type="button"
                            @click="toggleSelectAll"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                        >

                            <span
                                class="flex h-4 w-4 items-center justify-center rounded border"
                                :class="
                                    isAllSelected
                                        ? 'border-blue-600 bg-blue-600 text-white'
                                        : 'border-slate-300 bg-white'
                                "
                            >

                                <CheckIcon
                                    v-if="isAllSelected"
                                    class="h-3 w-3"
                                />

                            </span>

                            Pilih semua

                        </button>


                        <!-- HAPUS YANG DIPILIH -->

                        <button
                            type="button"
                            @click="deleteSelected"
                            :disabled="selectedIds.length === 0"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-semibold transition"
                            :class="
                                selectedIds.length > 0
                                    ? 'bg-red-600 text-white hover:bg-red-700'
                                    : 'cursor-not-allowed bg-slate-100 text-slate-400'
                            "
                        >

                            <TrashIcon class="h-4 w-4" />

                            Hapus
                            <span v-if="selectedIds.length > 0">
                                ({{ selectedIds.length }})
                            </span>

                        </button>


                        <!-- BATAL -->

                        <button
                            type="button"
                            @click="cancelDeleteMode"
                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                        >

                            <XMarkIcon class="h-4 w-4" />

                            Batal

                        </button>

                    </template>

                </div>

            </div>

            <!-- ================================================= -->
            <!-- MODE HAPUS INFO -->
            <!-- ================================================= -->

            <div
                v-if="isDeleteMode"
                class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3"
            >

                <p class="text-xs font-semibold text-amber-700">

                    Pilih notifikasi yang ingin dihapus.
                    {{ selectedIds.length }}
                    notifikasi dipilih.

                </p>

            </div>


            <!-- ================================================= -->
            <!-- KOSONG -->
            <!-- ================================================= -->

            <div
                v-if="!notifications.length"
                class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center"
            >

                <BellIcon
                    class="mx-auto h-10 w-10 text-slate-300"
                />

                <h3
                    class="mt-3 text-sm font-bold text-slate-600"
                >
                    Tidak ada notifikasi
                </h3>

                <p
                    class="mt-1 text-xs text-slate-400"
                >
                    Belum ada pemberitahuan untuk akun Anda.
                </p>

            </div>


            <!-- ================================================= -->
            <!-- LIST NOTIFIKASI -->
            <!-- ================================================= -->

            <div
                v-else
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white"
            >

                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="flex w-full gap-4 border-b border-slate-100 px-5 py-5 text-left transition last:border-b-0 hover:bg-slate-50"
                    :class="{
                        'bg-blue-50/40': !isRead(notification),
                        'cursor-pointer': isDeleteMode,
                    }"
                    @click="openNotification(notification)"
                >

                    <!-- ================================================= -->
                    <!-- CHECKBOX - HANYA MUNCUL SAAT MODE HAPUS -->
                    <!-- ================================================= -->

                    <div
                        v-if="isDeleteMode"
                        class="flex shrink-0 items-start pt-1"
                    >

                        <input
                            type="checkbox"
                            :checked="
                                selectedIds.includes(
                                    notification.id
                                )
                            "
                            @click.stop
                            @change="
                                toggleSelection(
                                    notification.id
                                )
                            "
                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                        />

                    </div>


                    <!-- ================================================= -->
                    <!-- ICON -->
                    <!-- ================================================= -->

                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                        :class="typeClass(notification.type)"
                    >

                        <component
                            :is="typeIcon(notification.type)"
                            class="h-5 w-5"
                        />

                    </div>


                    <!-- ================================================= -->
                    <!-- CONTENT -->
                    <!-- ================================================= -->

                    <div class="min-w-0 flex-1">

                        <div
                            class="flex items-start justify-between gap-3"
                        >

                            <div class="min-w-0">

                                <h3
                                    class="truncate text-sm font-bold text-slate-700"
                                >

                                    {{ notification.title }}

                                </h3>


                                <p
                                    class="mt-1 line-clamp-2 text-sm leading-6 text-slate-500"
                                >

                                    {{ notification.message }}

                                </p>

                            </div>


                            <!-- UNREAD DOT -->

                            <span
                                v-if="!isRead(notification)"
                                class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"
                                title="Belum dibaca"
                            />

                        </div>


                        <!-- ================================================= -->
                        <!-- FOOTER -->
                        <!-- ================================================= -->

                        <div
                            class="mt-2 flex flex-wrap items-center gap-2"
                        >

                            <p
                                class="text-[11px] font-medium text-slate-400"
                            >

                                {{ notification.created_at }}

                            </p>


                            <span
                                v-if="notification.type"
                                class="rounded-full px-2 py-0.5 text-[10px] font-semibold capitalize"
                                :class="typeClass(notification.type)"
                            >

                                {{ notification.type }}

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </component>

</template>