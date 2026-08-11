<script setup>
import KlinikLayout from '@/Layouts/KlinikLayout.vue'

const props = defineProps({
    kunjungan: Object,
    periodeList: Array,
    siswaList: Array,
    statistik: Object,
    filter: Object,
})
</script>

<template>
    <KlinikLayout>

        <div class="space-y-6">

            <h1 class="text-2xl font-bold text-slate-800">
                Riwayat Kunjungan
            </h1>

            <div class="rounded-2xl border bg-white p-5">

                <p>
                    Total Kunjungan:
                    {{ statistik.total }}
                </p>

                <p>
                    Jumlah Siswa:
                    {{ statistik.siswa }}
                </p>

                <p>
                    Pemeriksaan Selesai:
                    {{ statistik.selesai }}
                </p>

            </div>

            <div
                v-for="item in kunjungan.data"
                :key="item.id"
                class="rounded-2xl border bg-white p-5"
            >

                <p class="font-bold">
                    {{ item.siswa?.nama }}
                </p>

                <p>
                    {{ item.tanggal_kunjungan }}
                </p>

                <p>
                    Keluhan:
                    {{ item.keluhan ?? '-' }}
                </p>

                <p>
                    Diagnosis:
                    {{ item.diagnosis ?? '-' }}
                </p>

                <p>
                    Pemeriksa:
                    {{ item.pemeriksa?.name ?? '-' }}
                </p>

                <div class="mt-3">

                    <p class="font-semibold">
                        Obat
                    </p>

                    <div
                        v-for="obat in item.obat"
                        :key="obat.id"
                    >
                        {{ obat.nama_obat }}
                        -
                        {{ obat.jumlah }}
                        {{ obat.satuan }}
                    </div>

                </div>

            </div>

        </div>

    </KlinikLayout>
</template>