<script setup>

import { Head, Link, useForm } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    ArrowLeftIcon,
    BoltIcon
} from '@heroicons/vue/24/outline'


const form = useForm({

    nama_tes:'',

    tanggal:'',

    periode:'',

    kelas:[],

    jurusan:[],

    komponen:[
    'Hand and Eye Coordination Test',
    'Vertical Jump Test',
    'T Test',
    'Hand Touch Reaction Test',
    'Dipping Test',
    'Beep Test'
],

    keterangan:''

})



const kelasList=[

    'X',
    'XI',
    'XII'

]


const jurusanList=[

    'Teknik Pemesinan',

    'Teknik Elektronika Industri',

    'Teknik Komputer dan Jaringan'

]



const simpanTes=()=>{


    form.post(
        route('pendamping.tksi.store')
    )


}


</script>



<template>

<PendampingLayout>


<Head title="Tambah Tes TKSI"/>


<div class="max-w-5xl mx-auto p-6 space-y-6">



<!-- Header -->

<div class="flex items-center gap-3">


<Link
:href="route('pendamping.tksi.index')"
class="p-2 rounded-xl hover:bg-slate-100"
>

<ArrowLeftIcon class="w-5 h-5"/>

</Link>



<div>

<h1 class="text-2xl font-bold text-slate-800">
Tambah Tes TKSI
</h1>


<p class="text-sm text-slate-500">
Membuat periode tes kebugaran siswa baru
</p>


</div>


</div>







<form
@submit.prevent="simpanTes"
class="space-y-6"
>



<!-- Informasi Tes -->


<div class="bg-white rounded-2xl border shadow-sm p-6">


<div class="flex items-center gap-2 mb-5">


<BoltIcon
class="w-5 h-5 text-yellow-500"
/>


<h2 class="font-bold">
Informasi Tes
</h2>


</div>




<div class="grid md:grid-cols-2 gap-5">


<div>

<label class="text-sm font-semibold">
Nama Tes
</label>


<input

v-model="form.nama_tes"

placeholder="Contoh : TKSI Semester Ganjil 2026"

class="mt-2 w-full rounded-xl border-slate-200"

>


</div>




<div>

<label class="text-sm font-semibold">
Tanggal Pelaksanaan
</label>


<input

type="date"

v-model="form.tanggal"

class="mt-2 w-full rounded-xl border-slate-200"

>


</div>




<div>

<label class="text-sm font-semibold">
Periode / Batch
</label>


<input

v-model="form.periode"

placeholder="Contoh : Batch 1"

class="mt-2 w-full rounded-xl border-slate-200"

>


</div>


</div>


</div>









<!-- Peserta -->


<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="font-bold mb-5">
Peserta Tes
</h2>



<div class="grid md:grid-cols-2 gap-6">


<div>


<p class="text-sm font-semibold mb-3">
Kelas
</p>


<div
v-for="item in kelasList"
:key="item"
class="flex items-center gap-2"
>


<input

type="checkbox"

:value="item"

v-model="form.kelas"

>


<label>
{{item}}
</label>


</div>


</div>







<div>


<p class="text-sm font-semibold mb-3">
Jurusan
</p>


<div
v-for="item in jurusanList"
:key="item"
class="flex items-center gap-2"
>


<input

type="checkbox"

:value="item"

v-model="form.jurusan"

>


<label>
{{item}}
</label>


</div>


</div>


</div>


</div>









<!-- Komponen TKSI -->


<div class="bg-white rounded-2xl border shadow-sm p-6">


<h2 class="font-bold mb-4">
Komponen Tes TKSI
</h2>


<div class="grid md:grid-cols-2 gap-3">


<div

v-for="item in form.komponen"

:key="item"

class="
bg-slate-50
rounded-xl
p-3
text-sm
font-medium
"

>

✓ {{item}}


</div>


</div>


</div>








<!-- Keterangan -->


<div class="bg-white rounded-2xl border shadow-sm p-6">


<label class="font-bold">
Keterangan
</label>


<textarea

v-model="form.keterangan"

rows="4"

placeholder="Tambahkan informasi tes..."

class="
mt-3
w-full
rounded-xl
border-slate-200
"

></textarea>


</div>









<!-- Button -->


<div class="flex justify-end gap-3">


<Link

:href="route('pendamping.tksi.index')"

class="
px-5 py-3
rounded-xl
border
font-semibold
"

>

Batal

</Link>




<button

type="submit"

class="
px-5 py-3
rounded-xl
bg-blue-700
text-white
font-semibold
"

>

Simpan Tes

</button>



</div>





</form>


</div>


</PendampingLayout>


</template>