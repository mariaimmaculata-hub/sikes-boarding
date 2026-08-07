<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    ArrowLeftIcon,
    HeartIcon,
    ClipboardDocumentCheckIcon,
    UserCircleIcon
} from '@heroicons/vue/24/outline'


const props = defineProps({
    id:{
        type:String,
        required:true
    }
})


// Dummy data sementara
const pemeriksaan = {
    id: props.id,

    tanggal:'10 Februari 2026',

    nis:'2024001',

    nama:'Ahmad Fauzi',

    kelas:'X TKJ 1',

    jurusan:'Teknik Komputer dan Jaringan',

    batch:'Batch 1',

    pemeriksa:'Klinik Sekolah',

    tinggi:'172 cm',

    berat:'64 kg',

    tekanan:'120/80 mmHg',

    golongan:'O',

    alergi:'Tidak Ada',

    penyakit:'Tidak Ada',

    kondisi:'Sehat',

    catatan:
    'Kondisi siswa normal, berat badan ideal, tidak terdapat keluhan kesehatan.',


    riwayat:[
        {
            tanggal:'10 Februari 2026',
            kegiatan:'Pemeriksaan Berkala',
            hasil:'Tekanan darah normal, kondisi fisik baik.'
        },

        {
            tanggal:'15 Maret 2026',
            kegiatan:'Kunjungan Klinik',
            hasil:'Keluhan demam ringan dan diberikan obat.'
        }
    ]

}


const statusColor = {

    Sehat:
    'bg-emerald-50 text-emerald-700 border-emerald-200',

    'Perlu Pemantauan':
    'bg-yellow-50 text-yellow-700 border-yellow-200',

    Sakit:
    'bg-red-50 text-red-700 border-red-200'

}

</script>


<template>

<PendampingLayout>

<Head title="Detail Pemeriksaan Berkala"/>


<div class="max-w-6xl mx-auto p-6 space-y-6">


<!-- Header -->

<div class="flex items-center gap-3">

<Link
    :href="route('pendamping.pemeriksaan.index')"
    class="p-2 rounded-xl hover:bg-slate-100"
>

<ArrowLeftIcon class="w-5 h-5"/>

</Link>


<div>

<h1 class="text-2xl font-bold text-slate-800">
    Detail Pemeriksaan Berkala
</h1>

<p class="text-sm text-slate-500">
    Informasi hasil pemeriksaan kesehatan siswa
</p>

</div>

</div>





<div class="grid lg:grid-cols-3 gap-6">



<!-- Profil kiri -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<div class="flex flex-col items-center">


<div
class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center"
>

<UserCircleIcon
class="w-14 h-14 text-blue-600"
/>

</div>



<h2 class="mt-4 text-lg font-bold">

{{ pemeriksaan.nama }}

</h2>


<p class="text-sm text-slate-500">

NIS : {{ pemeriksaan.nis }}

</p>



<span
class="mt-3 px-4 py-1 rounded-full text-xs font-bold border"
:class="statusColor[pemeriksaan.kondisi]"
>

{{ pemeriksaan.kondisi }}

</span>


</div>




<div class="border-t mt-6 pt-5 space-y-3 text-sm">


<div class="flex justify-between">

<span class="text-slate-500">
Kelas
</span>

<span class="font-semibold">
{{ pemeriksaan.kelas }}
</span>

</div>



<div class="flex justify-between">

<span class="text-slate-500">
Jurusan
</span>

<span class="font-semibold text-right">
{{ pemeriksaan.jurusan }}
</span>

</div>




<div class="flex justify-between">

<span class="text-slate-500">
Batch
</span>

<span class="font-semibold">
{{ pemeriksaan.batch }}
</span>

</div>



<div class="flex justify-between">

<span class="text-slate-500">
Tanggal
</span>

<span class="font-semibold">
{{ pemeriksaan.tanggal }}
</span>

</div>


</div>


</div>








<!-- Detail kanan -->

<div class="lg:col-span-2 space-y-6">



<!-- Kondisi kesehatan -->

<div class="bg-white rounded-2xl border shadow-sm">


<div class="border-b p-5 flex items-center gap-2">


<HeartIcon
class="w-5 h-5 text-red-500"
/>


<h3 class="font-bold">
Hasil Pemeriksaan Fisik
</h3>


</div>



<div class="grid md:grid-cols-2 gap-6 p-6">


<div>

<p class="text-xs text-slate-500">
Tinggi Badan
</p>

<p class="font-bold">
{{ pemeriksaan.tinggi }}
</p>

</div>




<div>

<p class="text-xs text-slate-500">
Berat Badan
</p>

<p class="font-bold">
{{ pemeriksaan.berat }}
</p>

</div>




<div>

<p class="text-xs text-slate-500">
Tekanan Darah
</p>

<p class="font-bold">
{{ pemeriksaan.tekanan }}
</p>

</div>




<div>

<p class="text-xs text-slate-500">
Golongan Darah
</p>

<p class="font-bold">
{{ pemeriksaan.golongan }}
</p>

</div>




<div>

<p class="text-xs text-slate-500">
Alergi
</p>

<p class="font-bold">
{{ pemeriksaan.alergi }}
</p>

</div>



<div>

<p class="text-xs text-slate-500">
Riwayat Penyakit
</p>

<p class="font-bold">
{{ pemeriksaan.penyakit }}
</p>

</div>


</div>


</div>







<!-- Catatan -->

<div class="bg-white rounded-2xl border shadow-sm p-6">


<div class="flex gap-2 items-center mb-3">


<ClipboardDocumentCheckIcon
class="w-5 h-5 text-green-600"
/>


<h3 class="font-bold">
Catatan Klinik
</h3>


</div>



<p class="text-sm text-slate-600">

{{ pemeriksaan.catatan }}

</p>


<p class="text-xs text-slate-400 mt-4">

Diperiksa oleh:
{{ pemeriksaan.pemeriksa }}

</p>


</div>





</div>


</div>



</div>


</PendampingLayout>

</template>