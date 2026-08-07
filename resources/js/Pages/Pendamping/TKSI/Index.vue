<script setup>

import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    BoltIcon,
    PlusIcon,
    EyeIcon,
    ClipboardDocumentCheckIcon
} from '@heroicons/vue/24/outline'



/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

const search = ref('')
const selectedKelas = ref('')
const selectedJurusan = ref('')
const selectedKategori = ref('')
const selectedBatch = ref('')



/*
|--------------------------------------------------------------------------
| Dummy Batch TKSI
|--------------------------------------------------------------------------
*/

const batches = ref([

{
    id:1,
    nama:'Batch 1',
    periode:'Agustus 2026'
},

{
    id:2,
    nama:'Batch 2',
    periode:'November 2026'
}

])




/*
|--------------------------------------------------------------------------
| Data Siswa TKSI
|--------------------------------------------------------------------------
*/


const siswa = ref([


{
id:1,

batch:'Batch 1',

nis:'2024001',

nama:'Ahmad Fauzi',

kelas:'X',

jurusan:'Teknik Komputer dan Jaringan',

status:'Sudah',

kategori:'Baik'


},



{
id:2,

batch:'Batch 1',

nis:'2024002',

nama:'Budi Santoso',

kelas:'X',

jurusan:'Teknik Komputer dan Jaringan',

status:'Belum',

kategori:'-'

},



{
id:3,

batch:'Batch 1',

nis:'2024003',

nama:'Rina Lestari',

kelas:'XI',

jurusan:'Teknik Elektronika Industri',

status:'Sudah',

kategori:'Baik Sekali'

},



{
id:4,

batch:'Batch 2',

nis:'2024004',

nama:'Dimas Saputra',

kelas:'XI',

jurusan:'Teknik Pemesinan',

status:'Belum',

kategori:'-'

}


])




/*
|--------------------------------------------------------------------------
| List Filter
|--------------------------------------------------------------------------
*/


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


const kategoriList=[

'Baik Sekali',

'Baik',

'Sedang',

'Kurang',

'Kurang Sekali'

]





/*
|--------------------------------------------------------------------------
| Filter Data
|--------------------------------------------------------------------------
*/


const filteredSiswa = computed(()=>{


return siswa.value.filter(item=>{


const cari =

item.nama
.toLowerCase()
.includes(search.value.toLowerCase())

||

item.nis.includes(search.value)



const kelas =

!selectedKelas.value

||

item.kelas === selectedKelas.value



const jurusan =

!selectedJurusan.value

||

item.jurusan === selectedJurusan.value




const kategori =

!selectedKategori.value

||

item.kategori === selectedKategori.value




const batch =

!selectedBatch.value

||

item.batch === selectedBatch.value



return cari && kelas && jurusan && kategori && batch


})


})





const kategoriColor=(kategori)=>{


if(kategori==='Baik Sekali')

return 'bg-emerald-100 text-emerald-700'


if(kategori==='Baik')

return 'bg-green-100 text-green-700'


if(kategori==='Sedang')

return 'bg-yellow-100 text-yellow-700'


if(kategori==='Kurang')

return 'bg-orange-100 text-orange-700'


if(kategori==='Kurang Sekali')

return 'bg-red-100 text-red-700'


return 'bg-slate-100 text-slate-500'


}



</script>




<template>

<PendampingLayout>


<Head title="Daftar Tes TKSI"/>



<div class="max-w-7xl mx-auto p-6 space-y-6">





<!-- Header -->


<div class="flex justify-between items-center">


<div>

<h1 class="text-2xl font-bold text-slate-800">

Daftar Tes TKSI

</h1>


<p class="text-sm text-slate-500">

Monitoring hasil Tes Kebugaran Siswa Indonesia

</p>

</div>




<Link

:href="route('pendamping.tksi.create')"

class="
flex items-center gap-2
bg-blue-700
text-white
px-4 py-2
rounded-xl
font-semibold
"

>


<PlusIcon class="w-5 h-5"/>

Tambah Tes

</Link>



</div>








<!-- Filter -->


<div class="bg-white border rounded-2xl shadow-sm p-5">


<h3 class="font-bold mb-4 flex gap-2">

<BoltIcon class="w-5 h-5 text-yellow-500"/>

Filter TKSI

</h3>




<div class="grid md:grid-cols-5 gap-4">



<input

v-model="search"

placeholder="Cari nama / NIS"

class="rounded-xl border-slate-200 text-sm"

/>



<select
v-model="selectedBatch"
class="rounded-xl border-slate-200 text-sm"
>

<option value="">
Semua Batch
</option>

<option
v-for="item in batches"
:value="item.nama"
:key="item.id"
>

{{item.nama}}

</option>


</select>





<select
v-model="selectedKelas"
class="rounded-xl border-slate-200 text-sm"
>

<option value="">
Semua Kelas
</option>

<option
v-for="item in kelasList"
:key="item"
>

{{item}}

</option>

</select>





<select
v-model="selectedJurusan"
class="rounded-xl border-slate-200 text-sm"
>

<option value="">
Semua Jurusan
</option>


<option
v-for="item in jurusanList"
:key="item"
>

{{item}}

</option>


</select>





<select
v-model="selectedKategori"
class="rounded-xl border-slate-200 text-sm"
>

<option value="">
Semua Kategori
</option>


<option
v-for="item in kategoriList"
:key="item"
>

{{item}}

</option>


</select>




</div>


</div>









<!-- Table -->


<div
class="
bg-white
border
rounded-2xl
shadow-sm
overflow-hidden
"
>



<table class="w-full">


<thead
class="bg-slate-50 text-xs uppercase text-slate-500"
>


<tr>


<th class="px-6 py-4 text-left">
Siswa
</th>


<th class="px-6 py-4">
Jurusan
</th>


<th class="px-6 py-4">
Kelas
</th>


<th class="px-6 py-4">
Batch
</th>


<th class="px-6 py-4">
Kategori
</th>


<th class="px-6 py-4">
Aksi
</th>


</tr>


</thead>




<tbody>


<tr
v-for="item in filteredSiswa"
:key="item.id"
class="border-t"
>


<td class="px-6 py-4">


<div>


<p class="font-bold">

{{item.nama}}

</p>


<p class="text-xs text-slate-500">

{{item.nis}}

</p>


</div>


</td>




<td class="px-6 py-4 text-sm">

{{item.jurusan}}

</td>




<td class="px-6 py-4 text-sm">

{{item.kelas}}

</td>




<td class="px-6 py-4 text-sm">

{{item.batch}}

</td>




<td class="px-6 py-4">


<span
class="px-3 py-1 rounded-full text-xs font-bold"
:class="kategoriColor(item.kategori)"
>

{{item.kategori}}

</span>


</td>





<td class="px-6 py-4 text-center">


<Link

v-if="item.status==='Belum'"

:href="route('pendamping.tksi.isi',item.id)"

class="
px-3 py-2
bg-blue-600
text-white
rounded-lg
text-xs
font-bold
"

>

Isi Tes

</Link>




<Link

v-else

:href="route('pendamping.tksi.show',item.id)"

class="p-2 inline-flex hover:bg-slate-100 rounded-lg"

>


<EyeIcon class="w-5 h-5"/>


</Link>



</td>




</tr>


</tbody>



</table>



</div>



</div>


</PendampingLayout>


</template>