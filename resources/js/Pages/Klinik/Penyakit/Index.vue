<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import KlinikLayout from '@/Layouts/KlinikLayout.vue'
import {
    PlusIcon,
    MagnifyingGlassIcon,
    ExclamationTriangleIcon,
    EyeIcon,
} from '@heroicons/vue/24/outline'


// ============================================================
// PROPS
// ============================================================

const props = defineProps({
    penyakits: {
        type: Array,
        default: () => [],
    },
})


// ============================================================
// STATE
// ============================================================

const search = ref('')


// ============================================================
// FILTER DATA
// ============================================================

const filteredPenyakits = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    if (!keyword) {
        return props.penyakits
    }

    return props.penyakits.filter((penyakit) => {
        return (
            penyakit.nama_penyakit
                ?.toLowerCase()
                .includes(keyword) ||

            penyakit.kategori
                ?.toLowerCase()
                .includes(keyword) ||

            penyakit.keterangan
                ?.toLowerCase()
                .includes(keyword)
        )
    })
})


// ============================================================
// FORMAT KETERANGAN
// ============================================================

const formatKeterangan = (keterangan) => {
    if (!keterangan) {
        return '-'
    }

    if (keterangan.length <= 60) {
        return keterangan
    }

    return `${keterangan.substring(0, 60)}...`
}
</script>


<template>

    <KlinikLayout>

        <div class="space-y-6">

            <!-- ==================================================
                 HEADER
            ================================================== -->

            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"
                        >

                            <ExclamationTriangleIcon
                                class="w-6 h-6"
                            />

                        </div>

                        <div>

                            <h1
                                class="text-xl lg:text-2xl font-bold text-slate-900"
                            >
                                Data Penyakit
                            </h1>

                            <p
                                class="text-sm text-slate-500 mt-0.5"
                            >
                                Kelola data penyakit yang digunakan dalam pelayanan klinik.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TAMBAH -->

                <Link
                    href="/klinik/penyakit/create"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold shadow-sm transition"
                >

                    <PlusIcon
                        class="w-5 h-5"
                    />

                    <span>
                        Tambah Penyakit
                    </span>

                </Link>

            </div>


            <!-- ==================================================
                 STATISTIK
            ================================================== -->

            <div
                class="grid grid-cols-1 sm:grid-cols-2 gap-4"
            >

                <div
                    class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400 uppercase tracking-wider"
                            >
                                Total Penyakit
                            </p>

                            <p
                                class="text-2xl font-bold text-slate-900 mt-1"
                            >
                                {{ props.penyakits.length }}
                            </p>

                        </div>


                        <div
                            class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center"
                        >

                            <ExclamationTriangleIcon
                                class="w-5 h-5"
                            />

                        </div>

                    </div>

                </div>


                <div
                    class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p
                                class="text-xs font-semibold text-slate-400 uppercase tracking-wider"
                            >
                                Hasil Pencarian
                            </p>

                            <p
                                class="text-2xl font-bold text-slate-900 mt-1"
                            >
                                {{ filteredPenyakits.length }}
                            </p>

                        </div>


                        <div
                            class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center"
                        >

                            <MagnifyingGlassIcon
                                class="w-5 h-5"
                            />

                        </div>

                    </div>

                </div>

            </div>


            <!-- ==================================================
                 TABLE CARD
            ================================================== -->

            <div
                class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
            >

                <!-- TOOLBAR -->

                <div
                    class="p-4 lg:p-5 border-b border-slate-200"
                >

                    <div
                        class="relative max-w-md"
                    >

                        <MagnifyingGlassIcon
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"
                        />

                        <input
                            v-model="search"
                            type="text"
                            placeholder="Cari nama penyakit atau kategori..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"
                        />

                    </div>

                </div>


                <!-- TABLE -->

                <div class="overflow-x-auto">

                    <table
                        class="w-full text-sm"
                    >

                        <thead
                            class="bg-slate-50 border-b border-slate-200"
                        >

                            <tr>

                                <th
                                    class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap"
                                >
                                    No
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap"
                                >
                                    Nama Penyakit
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider whitespace-nowrap"
                                >
                                    Kategori
                                </th>

                                <th
                                    class="px-5 py-3.5 text-left text-xs font-bold text-slate-500 uppercase tracking-wider"
                                >
                                    Keterangan
                                </th>

                                <th
                                    class="px-5 py-3.5 text-center text-xs font-bold text-slate-500 uppercase tracking-wider"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody
                            class="divide-y divide-slate-100"
                        >

                            <!-- DATA -->

                            <tr
                                v-for="(penyakit, index) in filteredPenyakits"
                                :key="penyakit.id"
                                class="hover:bg-slate-50/70 transition"
                            >

                                <td
                                    class="px-5 py-4 text-slate-500 font-medium whitespace-nowrap"
                                >
                                    {{ index + 1 }}
                                </td>


                                <td
                                    class="px-5 py-4 whitespace-nowrap"
                                >

                                    <div
                                        class="flex items-center gap-3"
                                    >

                                        <div
                                            class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0"
                                        >

                                            <ExclamationTriangleIcon
                                                class="w-5 h-5"
                                            />

                                        </div>


                                        <div>

                                            <p
                                                class="font-semibold text-slate-800"
                                            >
                                                {{ penyakit.nama_penyakit }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <td
                                    class="px-5 py-4 whitespace-nowrap"
                                >

                                    <span
                                        v-if="penyakit.kategori"
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold"
                                    >
                                        {{ penyakit.kategori }}
                                    </span>

                                    <span
                                        v-else
                                        class="text-slate-400"
                                    >
                                        -
                                    </span>

                                </td>


                                <td
                                    class="px-5 py-4 text-slate-500 max-w-md"
                                >
                                    {{ formatKeterangan(penyakit.keterangan) }}
                                </td>


                                <td
                                    class="px-5 py-4"
                                >

                                    <div
                                        class="flex items-center justify-center"
                                    >

                                        <Link
                                            :href="`/klinik/penyakit/${penyakit.id}`"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 transition"
                                        >

                                            <EyeIcon
                                                class="w-4 h-4"
                                            />

                                            Detail

                                        </Link>

                                    </div>

                                </td>

                            </tr>


                            <!-- EMPTY -->

                            <tr
                                v-if="filteredPenyakits.length === 0"
                            >

                                <td
                                    colspan="5"
                                    class="px-5 py-14 text-center"
                                >

                                    <div
                                        class="flex flex-col items-center"
                                    >

                                        <div
                                            class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3"
                                        >

                                            <ExclamationTriangleIcon
                                                class="w-7 h-7"
                                            />

                                        </div>


                                        <p
                                            class="font-semibold text-slate-700"
                                        >
                                            {{
                                                search
                                                    ? 'Data penyakit tidak ditemukan'
                                                    : 'Belum ada data penyakit'
                                            }}
                                        </p>


                                        <p
                                            class="text-sm text-slate-400 mt-1"
                                        >
                                            {{
                                                search
                                                    ? 'Coba gunakan kata kunci pencarian lainnya.'
                                                    : 'Silakan tambahkan data penyakit terlebih dahulu.'
                                            }}
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </KlinikLayout>

</template>
