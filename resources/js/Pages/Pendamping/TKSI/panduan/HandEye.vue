<script setup>
import PendampingLayout from '@/Layouts/PendampingLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const equipments = [
    '5 buah bola tenis',
    'Stopwatch',
    'Dinding pantul yang rata',
    'Formulir tes dan pulpen'
]
</script>

<template>
    <Head title="Panduan Hand and Eye Coordination Test" />

    <PendampingLayout>
        <div class="max-w-6xl mx-auto space-y-6">

            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-700 to-blue-500 rounded-2xl text-white p-8">

                <p class="uppercase tracking-widest text-blue-100 text-sm">
                    TKSI Fase EF
                </p>

                <h1 class="text-3xl font-bold mt-2">
                    Hand and Eye Coordination Test
                </h1>

                <p class="mt-3 text-blue-100 leading-relaxed">
                    Tes koordinasi mata dan tangan yang dilakukan dengan melempar bola
                    ke arah dinding menggunakan satu tangan dan menangkap pantulannya
                    menggunakan tangan yang berlawanan secara bergantian selama
                    30 detik.
                </p>

                <div class="mt-6">
                    <Link
                        :href="route('pendamping.tksi.panduan')"
                        class="inline-flex items-center px-4 py-2 bg-white text-blue-700 rounded-xl font-semibold hover:bg-slate-100"
                    >
                        ← Kembali ke Panduan
                    </Link>
                </div>

            </div>

            <!-- Deskripsi -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h2 class="text-xl font-bold text-slate-800 mb-4">
                    Deskripsi
                </h2>

                <p class="text-slate-600 leading-8">
                    Hand and Eye Coordination Test merupakan tes yang dilakukan
                    dengan cara melempar bola tenis ke arah dinding menggunakan
                    tangan kanan kemudian menangkap pantulan bola menggunakan
                    tangan kiri, lalu dilakukan bergantian menggunakan tangan kiri
                    dan menangkap dengan tangan kanan secara terus-menerus selama
                    30 detik.

                    Tes ini merupakan modifikasi dari
                    <span class="font-semibold">
                        Hand Wall Toss Test (Ashok, 2008).
                    </span>
                </p>

            </div>

            <!-- Tujuan -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h2 class="text-xl font-bold text-slate-800 mb-4">
                    Tujuan Tes
                </h2>

                <div class="bg-blue-50 border-l-4 border-blue-600 rounded-xl p-5">
                    <p class="text-blue-900 font-semibold">
                        Mengukur kemampuan koordinasi antara mata dan tangan siswa.
                    </p>
                </div>

            </div>

            <!-- Peralatan -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h2 class="text-xl font-bold mb-5">
                    Peralatan
                </h2>

                <div class="grid md:grid-cols-2 gap-4">

                    <div
                        v-for="(item,index) in equipments"
                        :key="index"
                        class="flex items-center gap-4 border rounded-xl p-4"
                    >

                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold"
                        >
                            {{ index + 1 }}
                        </div>

                        <div class="font-medium text-slate-700">
                            {{ item }}
                        </div>

                    </div>

                </div>

            </div>

            <!-- Penskoran -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h2 class="text-xl font-bold text-slate-800 mb-6">
                    Penskoran
                </h2>

                <div class="grid md:grid-cols-2 gap-6">

                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-6">

                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xl mb-4">
                            📝
                        </div>

                        <h3 class="font-bold text-blue-900 mb-3">
                            Cara Penilaian
                        </h3>

                        <ul class="space-y-3 text-slate-700 leading-7 list-disc pl-5">
                            <li>Hitung jumlah tangkapan bola yang berhasil dilakukan selama <b>30 detik</b>.</li>
                            <li>Siswa memperoleh kesempatan <b>2 kali</b> melakukan tes.</li>
                            <li>Nilai akhir adalah <b>hasil terbaik</b> dari dua percobaan.</li>
                        </ul>

                    </div>

                    <div class="rounded-2xl border border-green-200 bg-green-50 p-6">

                        <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center text-xl mb-4">
                            ✔
                        </div>

                        <h3 class="font-bold text-green-900 mb-3">
                            Hasil Tes
                        </h3>

                        <p class="text-slate-700 leading-8">
                            Semakin banyak jumlah tangkapan bola yang berhasil dilakukan
                            dalam waktu 30 detik, maka semakin baik kemampuan
                            koordinasi mata dan tangan siswa.
                        </p>

                    </div>

                </div>

            </div>

            <!-- Validitas -->
            <div class="bg-white rounded-2xl shadow-sm border p-6">

                <h2 class="text-xl font-bold text-slate-800 mb-6">
                    Validitas dan Reliabilitas
                </h2>

                <div class="grid md:grid-cols-2 gap-6">

                    <div class="border rounded-2xl p-6">

                        <p class="text-sm uppercase text-slate-500 font-semibold">
                            Validitas
                        </p>

                        <div class="mt-3 flex items-end gap-2">
                            <span class="text-4xl font-bold text-blue-700">
                                0.751
                            </span>

                            <span class="text-slate-500">
                                (Sangat Tinggi)
                            </span>
                        </div>

                        <div class="mt-4 bg-slate-200 rounded-full h-3">
                            <div class="bg-blue-600 h-3 rounded-full w-3/4"></div>
                        </div>

                        <p class="text-sm text-slate-600 mt-3">
                            Nilai validitas instrumen lebih besar daripada r tabel
                            (0.751 > 0.103) sehingga memiliki
                            <b>validitas sangat tinggi.</b>
                        </p>

                    </div>

                    <div class="border rounded-2xl p-6">

                        <p class="text-sm uppercase text-slate-500 font-semibold">
                            Reliabilitas
                        </p>

                        <div class="mt-3 flex items-end gap-2">

                            <span class="text-4xl font-bold text-emerald-700">
                                0.689
                            </span>

                            <span class="text-slate-500">
                                (Tinggi)
                            </span>

                        </div>

                        <div class="mt-4 bg-slate-200 rounded-full h-3">
                            <div class="bg-emerald-600 h-3 rounded-full" style="width:69%"></div>
                        </div>

                        <p class="text-sm text-slate-600 mt-3">
                            Instrumen memiliki reliabilitas tinggi sehingga hasil
                            pengukuran konsisten ketika tes dilakukan kembali.
                        </p>

                    </div>

                </div>

            </div>

        </div>
    </PendampingLayout>
</template>