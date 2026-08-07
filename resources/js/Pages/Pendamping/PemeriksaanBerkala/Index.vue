<script setup>
import { ref, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import PendampingLayout from '@/Layouts/PendampingLayout.vue'

import {
    CalendarDaysIcon,
    EyeIcon,
    ClipboardDocumentCheckIcon
} from '@heroicons/vue/24/outline'


/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

const search = ref('')
const selectedJurusan = ref('')
const selectedKelas = ref('')
const selectedBatch = ref('')



/*
|--------------------------------------------------------------------------
| Dummy Batch
|--------------------------------------------------------------------------
*/

const batches = [
    {
        id:1,
        name:'Batch 1',
        periode:'Januari - Maret 2026'
    },
    {
        id:2,
        name:'Batch 2',
        periode:'April - Juni 2026'
    },
    {
        id:3,
        name:'Batch 3',
        periode:'Juli - September 2026'
    }
]



const jurusanList = [
    'Teknik Pemesinan',
    'Teknik Elektronika Industri',
    'Teknik Komputer dan Jaringan'
]


const kelasList = [
    'X',
    'XI',
    'XII'
]




/*
|--------------------------------------------------------------------------
| Data Pemeriksaan
|--------------------------------------------------------------------------
*/


const checkups = ref([


{
    id:1,

    batch:'Batch 1',

    jurusan:'Teknik Komputer dan Jaringan',

    nis:'2024001',

    name:'Ahmad Fauzi',

    kelas:'X',

    date:'10 Feb 2026',

    statusPemeriksaan:'Sudah',

    height:'172 cm',

    weight:'64 kg',

    bp:'120/80 mmHg',

    status:'Sehat'

},



{
    id:2,

    batch:'Batch 1',

    jurusan:'Teknik Komputer dan Jaringan',

    nis:'2024002',

    name:'Budi Santoso',

    kelas:'X',

    date:'-',

    statusPemeriksaan:'Belum',

    height:'-',

    weight:'-',

    bp:'-',

    status:'Belum Ada Data'

},




{
    id:3,

    batch:'Batch 1',

    jurusan:'Teknik Elektronika Industri',

    nis:'2024003',

    name:'Rina Lestari',

    kelas:'XI',

    date:'12 Feb 2026',

    statusPemeriksaan:'Sudah',

    height:'160 cm',

    weight:'48 kg',

    bp:'110/70 mmHg',

    status:'Sehat'

},



{
    id:4,

    batch:'Batch 2',

    jurusan:'Teknik Pemesinan',

    nis:'2024004',

    name:'Dimas Saputra',

    kelas:'XI',

    date:'15 Mei 2026',

    statusPemeriksaan:'Sudah',

    height:'170 cm',

    weight:'60 kg',

    bp:'120/80 mmHg',

    status:'Perlu Pemantauan'

}



])




/*
|--------------------------------------------------------------------------
| Filter Data
|--------------------------------------------------------------------------
*/


const filteredCheckups = computed(()=>{


    return checkups.value.filter(item=>{


        const matchSearch =

            item.name
            .toLowerCase()
            .includes(search.value.toLowerCase())

            ||

            item.nis
            .includes(search.value)



        const matchJurusan =

            !selectedJurusan.value

            ||

            item.jurusan === selectedJurusan.value




        const matchKelas =

            !selectedKelas.value

            ||

            item.kelas === selectedKelas.value





        const matchBatch =

            !selectedBatch.value

            ||

            item.batch === selectedBatch.value




        return (

            matchSearch

            &&

            matchJurusan

            &&

            matchKelas

            &&

            matchBatch

        )


    })


})





const statusColor = (status)=>{


    if(status === 'Sehat')

        return 'bg-emerald-50 text-emerald-700 border-emerald-200'



    if(status === 'Perlu Pemantauan')

        return 'bg-yellow-50 text-yellow-700 border-yellow-200'



    return 'bg-slate-50 text-slate-500 border-slate-200'


}


</script>



<template>


<PendampingLayout>


<Head title="Pemeriksaan Berkala"/>



<div class="max-w-7xl mx-auto p-6 space-y-6">



<!-- Header -->

<div>


<h1 class="text-2xl font-extrabold text-slate-900">
    Pemeriksaan Berkala
</h1>


<p class="text-sm text-slate-500">
    Monitoring hasil pemeriksaan kesehatan siswa boarding.
</p>


</div>






<!-- Filter -->


<div class="bg-white rounded-2xl border shadow-sm p-5">


<div class="flex items-center gap-2 mb-4">

<CalendarDaysIcon
class="w-5 h-5 text-blue-600"
/>


<h3 class="font-bold text-slate-800">
Filter Pemeriksaan
</h3>


</div>



<div class="grid md:grid-cols-4 gap-4">


<input
v-model="search"
type="text"
placeholder="Cari nama / NIS..."
class="rounded-xl border-slate-200 text-sm"
/>




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
:value="item"
>

{{item}}

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
:value="item"
>

{{item}}

</option>


</select>





<select
v-model="selectedBatch"
class="rounded-xl border-slate-200 text-sm"
>

<option value="">
Semua Periode
</option>


<option
v-for="item in batches"
:key="item.id"
:value="item.name"
>

{{item.name}} - {{item.periode}}

</option>


</select>



</div>


</div>









<!-- Table -->


<div class="bg-white rounded-2xl border shadow-sm overflow-hidden">


<div class="overflow-x-auto">


<table class="w-full text-left">


<thead>


<tr class="bg-slate-50 border-b text-xs uppercase text-slate-500">


<th class="px-6 py-4">
Siswa
</th>


<th class="px-6 py-4">
Jurusan
</th>


<th class="px-6 py-4">
Kelas
</th>


<th class="px-6 py-4">
Periode
</th>


<th class="px-6 py-4">
Tanggal
</th>


<th class="px-6 py-4">
Status
</th>


<th class="px-6 py-4 text-center">
Aksi
</th>


</tr>


</thead>




<tbody>



<tr
v-for="item in filteredCheckups"
:key="item.id"
class="border-b hover:bg-slate-50"
>



<td class="px-6 py-4">


<div class="flex items-center gap-3">


<div
class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center"
>


<ClipboardDocumentCheckIcon
class="w-5 h-5 text-blue-600"
/>


</div>



<div>

<p class="font-bold text-slate-800">
{{item.name}}
</p>


<p class="text-xs text-slate-500">
{{item.nis}}
</p>


</div>


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





<td class="px-6 py-4 text-sm">

{{item.date}}

</td>





<td class="px-6 py-4">


<span
class="px-3 py-1 rounded-full text-xs font-bold border"
:class="statusColor(item.status)"
>


{{item.status}}


</span>


</td>






<td class="px-6 py-4 text-center">


<Link
    :href="route('pendamping.pemeriksaan.show', item.id)"
    class="inline-flex p-2 hover:bg-slate-100 rounded-lg"
>

    <EyeIcon
        class="w-5 h-5 text-slate-600"
    />

</Link>



</td>





</tr>



</tbody>


</table>


</div>


</div>





</div>


</PendampingLayout>


</template>