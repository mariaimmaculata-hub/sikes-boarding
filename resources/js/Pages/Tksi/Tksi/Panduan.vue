<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'

import {
    ClipboardDocumentCheckIcon,
    BoltIcon,
    ArrowsUpDownIcon,
    HandRaisedIcon,
    FireIcon,
    SpeakerWaveIcon,
    ArrowLeftIcon,
    CheckCircleIcon,
    BeakerIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline'

import TksiLayout from '@/Layouts/TksiLayout.vue'


/*
|--------------------------------------------------------------------------
| DATA PANDUAN TKSI
|--------------------------------------------------------------------------
*/

const panduan = [
    {
        id: 'hand-eye',
        title: 'Hand and Eye Coordination Test',
        shortTitle: 'Hand-Eye Coordination',
        description:
            'Tes untuk mengukur koordinasi mata-tangan melalui lempar dan tangkap bola secara bergantian.',
        icon: ClipboardDocumentCheckIcon,

        deskripsi:
            'Merupakan tes yang dilakukan dengan cara melempar bola ke arah tembok menggunakan tangan kanan dan menangkap bola dengan tangan kiri atau sebaliknya. Tes ini merupakan modifikasi dari Hand Wall Toss Test (Ashok, 2008).',

        tujuan:
            'Untuk mengukur koordinasi mata-tangan.',

        peralatan: [
            '5 bola tenis',
            'Stopwatch',
            'Dinding pantul yang rata',
            'Formulir tes dan pulpen',
        ],

        prosedur: [
            'Membuat batas sejauh 2 meter dari dinding pantul sebagai tanda posisi berdiri siswa saat melakukan tes.',
            'Siswa melempar bola pada saat aba-aba "mulai", bersamaan tekan tombol start pada stopwatch.',
            'Siswa melempar bola tenis ke tembok menggunakan tangan kanan dan menangkap bola menggunakan tangan kiri, kemudian melempar kembali bola tenis menggunakan tangan kiri dan menangkap menggunakan tangan kanan, dan seterusnya secara bergantian selama 30 detik.',
            'Siswa diperbolehkan melempar dan menangkap bola menggunakan teknik overhand atau underhand.',
            'Jika saat tes siswa gagal menangkap pantulan bola atau bola terpental jauh, siswa boleh mengambil bola lain yang disediakan untuk tes.',
            'Siswa diberi kesempatan untuk melakukan tes sebanyak 2 kali.',
        ],

        penskoran: [
            'Skor diperoleh berdasarkan jumlah tangkapan yang berhasil dilakukan selama 30 detik.',
            'Skor akhir adalah skor terbaik dari 2 kali kesempatan tes.',
        ],

        validitas:
            'Nilai validitas instrumen hand eye coordination test sebesar 0.751 > 0.103 (rhitung > rtabel) memiliki validitas sangat tinggi, dan reliabilitas sebesar 0.689 (reliabilitas tinggi).',

        norma: [
            { putera: '≥ 22', puteri: '≥ 15', skor: '5', kategori: 'Baik Sekali' },
            { putera: '16 - 21', puteri: '10 - 14', skor: '4', kategori: 'Baik' },
            { putera: '10 - 15', puteri: '5 - 9', skor: '3', kategori: 'Sedang' },
            { putera: '4 - 9', puteri: '1 - 4', skor: '2', kategori: 'Kurang' },
            { putera: '≤ 3', puteri: '≤ 0', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },

    {
        id: 'vertical-jump',
        title: 'Vertical Jump Test',
        shortTitle: 'Vertical Jump',
        description:
            'Tes untuk mengukur daya ledak atau power otot tungkai melalui lompatan vertikal.',
        icon: ArrowsUpDownIcon,

        deskripsi:
            'Merupakan tes yang dilakukan dengan cara melompat secara tegak lurus ke atas (vertical) menggunakan jangkauan lengan yang setinggi-tingginya. Tes ini merupakan adopsi dari vertical jump test tanpa mengubah prosedur tes (Fukuda, 2019).',

        tujuan:
            'Untuk mengukur daya ledak/power otot tungkai.',

        peralatan: [
            '1 buah meteran (pita, plastik, atau besi)',
            'Bubuk kapur sebagai penanda',
            'Dinding sedikitnya setinggi 365 cm',
            'Formulir tes dan alat tulis',
        ],

        prosedur: [
            'Siapkan dinding dengan tinggi kurang lebih 365 centimeter lalu pasang penggaris pita di dinding dengan ketinggian 150-350 cm.',
            'Siswa berdiri menyamping arah dinding, kedua kaki rapat, telapak kaki menempel penuh di lantai, ujung jari tangan yang dekat dengan dinding dibubuhi bubuk kapur.',
            'Kaki tetap menempel di lantai, satu tangan siswa yang dekat dengan dinding meraih ke atas setinggi-tingginya, lalu catat tinggi raihan pada bekas ujung jari paling tinggi.',
            'Sikap awal sebelum meloncat yaitu kaki dibuka selebar bahu, telapak kaki tetap menempel di lantai, lutut ditekuk, tangan lurus agak di belakang badan. Lalu siswa meloncat ke atas setinggi mungkin dan satu tangan siswa yang dekat dengan dinding meraih ke atas setinggi-tingginya lalu menepukkan ujung jari yang telah dibubuhi kapur ke dinding.',
            'Siswa tidak boleh melakukan awalan melangkah ketika akan meloncat ke atas.',
            'Siswa diberi kesempatan untuk melakukan tes sebanyak 2 kali.',
        ],

        penskoran: [
            'Ukur selisih antara tinggi loncatan dan tinggi raihan.',
            'Skor yang diperoleh siswa adalah selisih antara tinggi loncatan dan tinggi raihan.',
            'Skor akhir adalah skor terbaik dari 2 kali kesempatan tes.',
        ],

        validitas:
            'Nilai validitas instrumen vertical jump sebesar 0.805 > 0.103 (rhitung > rtabel) memiliki validitas tinggi, dan reliabilitas sebesar 0.683 (reliabilitas tinggi).',

        norma: [
            { putera: '≥ 63', puteri: '≥ 59', skor: '5', kategori: 'Baik Sekali' },
            { putera: '59 - 62', puteri: '35 - 58', skor: '4', kategori: 'Baik' },
            { putera: '35 - 58', puteri: '27 - 34', skor: '3', kategori: 'Sedang' },
            { putera: '20 - 34', puteri: '19 - 26', skor: '2', kategori: 'Kurang' },
            { putera: '≤ 19', puteri: '≤ 18', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },

    {
        id: 't-test',
        title: 'T Test',
        shortTitle: 'T-Test',
        description:
            'Tes untuk mengukur kelincahan siswa melalui gerakan berlari, galloping, dan berlari mundur.',
        icon: BoltIcon,

        deskripsi:
            'Tes ini dilakukan dengan cara berlari ke arah depan, galloping ke arah samping kiri dan kanan serta berlari mundur. Tes ini merupakan adopsi dari T-Test tanpa mengubah prosedur tes (Fukuda, 2019).',

        tujuan:
            'Untuk mengukur kelincahan siswa.',

        peralatan: [
            'Lapangan datar dan tidak licin, minimal panjang 12 meter dan lebar 10 meter',
            'Meteran',
            'Stopwatch',
            '4 kerucut penanda atau botol air kemasan 1 liter yang diisi pasir',
            'Formulir tes dan alat tulis',
        ],

        prosedur: [
            'Buatlah lintasan lari berbentuk "T" sesuai ukuran.',
            'Siswa berdiri di belakang garis start.',
            'Penguji memberikan aba-aba "Ya", sambil menghidupkan stopwatch.',
            'Setelah terdengar aba-aba "YA", siswa memulai tes.',
            'Siswa berlari dari penanda A ke arah penanda B dan menyentuh penanda B menggunakan tangan kanan.',
            'Kemudian berlari menyamping ke kiri (galloping) sampai menyentuh penanda C menggunakan tangan kiri.',
            'Dilanjutkan berlari menyamping ke kanan (galloping) sampai menyentuh penanda D menggunakan tangan kanan.',
            'Kemudian berlari kembali ke arah penanda B (galloping) dan menyentuh menggunakan tangan kiri.',
            'Lalu berlari mundur sampai garis finish.',
            'Siswa diberi kesempatan untuk melakukan tes sebanyak 2 kali.',
            'Untuk siswa dengan kekhususan/kidal, arah awal lari menyamping dapat dilakukan ke kanan sebagai alternatif.',
        ],

        penskoran: [
            'Waktu yang dicapai siswa dihitung dari titik start sampai finish.',
            'Waktu dicatat dalam detik/milidetik.',
            'Skor akhir adalah skor terbaik dari 2 kali kesempatan tes.',
        ],

        validitas:
            'Nilai validitas instrumen T-Test sebesar 0.714 > 0.103 (rhitung > rtabel) memiliki validitas tinggi, dan reliabilitas sebesar 0.693 (reliabilitas tinggi).',

        norma: [
            { putera: '≤ 00.06.63', puteri: '≤ 00.07.19', skor: '5', kategori: 'Baik Sekali' },
            { putera: '00.06.64 - 00.10.15', puteri: '00.07.20 - 00.11.20', skor: '4', kategori: 'Baik' },
            { putera: '00.10.16 - 00.14.16', puteri: '00.11.21 - 00.15.19', skor: '3', kategori: 'Sedang' },
            { putera: '00.14.17 - 00.18.17', puteri: '00.15.20 - 00.19.20', skor: '2', kategori: 'Kurang' },
            { putera: '≥ 00.18.18', puteri: '≥ 00.19.21', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },

    {
        id: 'hand-touch',
        title: 'Hand Touch Reaction Test',
        shortTitle: 'Hand Touch',
        description:
            'Tes untuk mengukur kecepatan reaksi melalui gerakan menyentuh cone sesuai dengan aba-aba warna.',
        icon: HandRaisedIcon,

        deskripsi:
            'Merupakan tes yang dilakukan dengan cara bergerak ke kanan atau ke kiri secepat-cepatnya menyentuh cone kerucut sesuai dengan aba-aba, yaitu menyebutkan warna "hitam" atau "hijau". Tes ini merupakan modifikasi dari berbagai latihan kecepatan reaksi.',

        tujuan:
            'Untuk mengukur kecepatan reaksi.',

        peralatan: [
            '2 buah cone kerucut 32 cm dengan warna berbeda',
            'Stopwatch',
            'Lakban',
            'Formulir tes dan alat tulis',
        ],

        prosedur: [
            'Ukur jarak 6 meter, lalu letakkan 2 cone dengan warna berbeda.',
            'Buatlah titik start di antara 2 cone dengan jarak 3 meter.',
            'Siswa berdiri rileks di atas titik start.',
            'Tester berada di depan siswa berjarak 2 meter.',
            'Tester mengucapkan secara keras "hitam" atau "hijau" sambil menekan tombol start stopwatch, bersamaan siswa berlari cepat menyentuh cone menggunakan tangan.',
            'Stopwatch dihentikan ketika siswa menyentuh cone dan catat hasilnya.',
            'Siswa melakukan 4 kali tes.',
            'Jumlah arah lari harus berimbang antara kanan dan kiri.',
            'Penyebutan warna dilakukan secara acak sehingga tidak mudah ditebak.',
        ],

        penskoran: [
            'Catat waktu yang diperoleh dari aba-aba "Hitam/Hijau" sampai tangan siswa menyentuh cone.',
            'Skor diambil dari waktu terbaik dari empat kali kesempatan melakukan tes.',
        ],

        validitas:
            'Nilai validitas instrumen hand touch reaction test sebesar 0.323 > 0.103 (rhitung > rtabel) memiliki validitas rendah, dan reliabilitas sebesar 0.732 (reliabilitas tinggi).',

        norma: [
            { putera: '≤ 0.80', puteri: '≤ 0.91', skor: '5', kategori: 'Baik Sekali' },
            { putera: '0.81 - 1.09', puteri: '0.92 - 1.21', skor: '4', kategori: 'Baik' },
            { putera: '1.10 - 1.39', puteri: '1.22 - 1.51', skor: '3', kategori: 'Sedang' },
            { putera: '1.40 - 1.69', puteri: '1.52 - 1.81', skor: '2', kategori: 'Kurang' },
            { putera: '≥ 1.70', puteri: '≥ 1.82', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },

    {
        id: 'dipping',
        title: 'Dipping Test',
        shortTitle: 'Dipping',
        description:
            'Tes untuk mengukur daya tahan otot lengan melalui gerakan dipping selama 3 menit.',
        icon: FireIcon,

        deskripsi:
            'Merupakan tes yang dilakukan dengan cara menekuk dan meluruskan lengan (mengangkat badan) selama 3 menit secara berulang mengikuti irama metronome. Tes ini merupakan modifikasi dari Dynamic Muscular Endurance Test (Miller, 2010).',

        tujuan:
            'Untuk mengukur daya tahan otot lengan.',

        peralatan: [
            'Stopwatch',
            'Metronome',
            'Kursi/bangku panjang setinggi kurang lebih 45 cm',
            'Formulir tes dan alat tulis',
        ],

        prosedur: [
            'Tester mengatur metronome ke 40 BPM (2 ketukan/beats).',
            'Siswa duduk dengan kaki rapat di ujung kursi setinggi kurang lebih 45 cm, kemudian meletakkan kedua telapak tangan di samping badan bertumpu pada ujung kursi dengan siku lurus.',
            'Kedua kaki rapat diluruskan ke depan, bertumpu pada ujung tumit dan jari kaki mengarah ke atas.',
            'Posisi punggung badan tegak dan pandangan mata ke depan.',
            'Siswa memajukan badan sedikit ke depan bergeser dari bangku.',
            'Saat metronome dioperasikan dan aba-aba "mulai", tekan tombol start stopwatch bersamaan dengan siswa menekuk kedua siku sambil menurunkan badan hingga siku membentuk sudut kurang lebih 90 derajat.',
            'Siswa kemudian mengangkat badan kembali dengan meluruskan siku ke posisi awal (dipping).',
            'Siswa melakukan gerakan dipping terus menerus secara berulang mengikuti irama ketukan metronome sebanyak mungkin selama 3 menit atau sesuai kemampuannya.',
        ],

        penskoran: [
            'Catat jumlah pengulangan penuh yang diselesaikan dengan gerakan yang benar selama 3 menit.',
            'Apabila siswa tidak dapat melakukan gerakan dengan benar sebanyak 3 kali maka tes dianggap selesai.',
        ],

        validitas:
            'Nilai validitas instrumen dipping test sebesar 0.520 > 0.103 (rhitung > rtabel) memiliki validitas sedang, dan reliabilitas sebesar 0.714 (reliabilitas tinggi).',

        norma: [
            { putera: '≥ 25', puteri: '≥ 19', skor: '5', kategori: 'Baik Sekali' },
            { putera: '22 - 24', puteri: '16 - 18', skor: '4', kategori: 'Baik' },
            { putera: '19 - 21', puteri: '13 - 15', skor: '3', kategori: 'Sedang' },
            { putera: '16 - 18', puteri: '10 - 12', skor: '2', kategori: 'Kurang' },
            { putera: '≤ 15', puteri: '≤ 9', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },

    {
        id: 'beep-test',
        title: 'Beep Test',
        shortTitle: 'Beep Test',
        description:
            'Tes daya tahan kardiorespirasi melalui aktivitas lari bolak-balik mengikuti irama beep sejauh 20 meter.',
        icon: SpeakerWaveIcon,

        deskripsi:
            'Merupakan tes yang dilakukan dengan cara berlari mengikuti isyarat audio (bunyi beep) dari titik/garis awal ke akhir yang berjarak 20 meter. Tes ini merupakan adopsi dari beep test tanpa mengubah prosedur (Wood, 2019).',

        tujuan:
            'Untuk mengukur daya tahan kardiorespirasi (VO2max).',

        peralatan: [
            'Lapangan datar dan tidak licin dengan panjang minimal 22 meter',
            'Kerucut penanda atau yang sejenis',
            'Audio MFT',
            'Pemutar audio dan alat pengeras suara',
            'Formulir tes dan alat tulis',
        ],

        prosedur: [
            'Buatlah tanda pada titik/garis awal dan akhir yang berjarak 20 meter.',
            'Siswa berdiri di belakang titik/garis awal menghadap ke titik/garis akhir dan mendengarkan aba-aba serta panduan dari pemutar audio.',
            'Siswa berlari dari satu titik/garis menuju titik/garis lainnya mengikuti bunyi penanda irama beep.',
            'Siswa harus melewati titik/garis penanda lintasan 20 meter sebelum atau bersamaan dengan penanda irama beep berbunyi.',
            'Jika siswa tiba sebelum penanda irama beep berbunyi, siswa harus menunggu penanda irama beep berbunyi untuk melanjutkan tes/berlari.',
            'Siswa berhenti secara sukarela atau dihentikan apabila sudah tidak mampu berlari mengikuti bunyi penanda irama beep.',
            'Jika siswa gagal mencapai titik/garis penanda setelah irama beep berbunyi, siswa diizinkan 2 atau 3 kali percobaan untuk mendapatkan kembali kecepatan yang diperlukan sebelum diminta berhenti.',
            'Jika setelah diberikan toleransi sebanyak 2 atau 3 kali siswa gagal menyesuaikan kecepatan larinya kembali sehingga penanda irama beep berbunyi sebelum mencapai titik/garis, siswa tersebut dihentikan dari kegiatan tes.',
        ],

        penskoran: [
            'Skor VO2max merupakan hasil konversi dari jumlah level dan balikan.',
            'Pedoman skor menggunakan tabel konversi VO2max Beep Test.',
        ],

        validitas:
            'Nilai validitas instrumen beep test sebesar 0.257 > 0.103 (rhitung > rtabel) memiliki validitas rendah, dan reliabilitas sebesar 0.738 (reliabilitas tinggi).',

        norma: [
            { putera: '≥ L12 B3', puteri: '≥ L7 B10', skor: '5', kategori: 'Baik Sekali' },
            { putera: 'L9 B10 - L12 B2', puteri: 'L6 B2 - L7 B9', skor: '4', kategori: 'Baik' },
            { putera: 'L7 B4 - L9 B9', puteri: 'L4 B6 - L6 B1', skor: '3', kategori: 'Sedang' },
            { putera: 'L4 B8 - L7 B3', puteri: 'L1 B5 - L4 B5', skor: '2', kategori: 'Kurang' },
            { putera: '≤ L4 B7', puteri: '≤ L1 B4', skor: '1', kategori: 'Kurang Sekali' },
        ],
    },
]


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const selectedId = ref(null)

const selectedTest = computed(() =>
    panduan.find(item => item.id === selectedId.value)
)


const selectTest = (id) => {

    selectedId.value = id

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })

}


const backToList = () => {

    selectedId.value = null

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })

}


/*
|--------------------------------------------------------------------------
| THEME
|--------------------------------------------------------------------------
|
| Mengikuti tema TksiLayout:
| Pink → Rose
|
*/

const theme = {
    icon: 'bg-pink-50 text-pink-700',
    iconSoft: 'bg-pink-50 text-pink-600',
    border: 'border-pink-100',
    badge: 'bg-pink-50 text-pink-700',
    accent: 'text-pink-700',
    button: 'bg-pink-700 hover:bg-pink-800',
}
</script>


<template>

    <TksiLayout>

        <Head
            :title="
                selectedTest
                    ? `Panduan ${selectedTest.shortTitle}`
                    : 'Panduan TKSI'
            "
        />


        <!-- =========================================================
             DETAIL
        ========================================================== -->

        <div
            v-if="selectedTest"
            class="space-y-6"
        >

            <!-- BACK -->

            <button
                type="button"
                @click="backToList"
                class="flex items-center gap-2 text-sm font-bold text-slate-500 transition hover:text-pink-700"
            >

                <ArrowLeftIcon class="h-4 w-4" />

                Kembali ke Panduan TKSI

            </button>


            <!-- HEADER -->

            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-700 via-pink-700 to-rose-800 p-6 text-white shadow-lg md:p-8"
            >

                <div
                    class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-16 right-20 h-48 w-48 rounded-full bg-white/5"
                ></div>


                <div
                    class="relative z-10 flex items-start gap-4"
                >

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10"
                    >

                        <component
                            :is="selectedTest.icon"
                            class="h-7 w-7"
                        />

                    </div>


                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-wider text-white/60"
                        >
                            Panduan Tes TKSI
                        </p>

                        <h1
                            class="mt-1 text-2xl font-bold tracking-tight"
                        >
                            {{ selectedTest.title }}
                        </h1>

                        <p
                            class="mt-2 max-w-3xl text-sm font-medium leading-6 text-white/80"
                        >
                            {{ selectedTest.description }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- DESKRIPSI + TUJUAN -->

            <div
                class="grid grid-cols-1 gap-6 lg:grid-cols-2"
            >

                <!-- DESKRIPSI -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="rounded-xl bg-pink-50 p-2 text-pink-700"
                        >

                            <InformationCircleIcon
                                class="h-5 w-5"
                            />

                        </div>

                        <h2
                            class="text-sm font-extrabold text-slate-800"
                        >
                            Deskripsi
                        </h2>

                    </div>


                    <p
                        class="mt-4 text-sm leading-7 text-slate-600"
                    >
                        {{ selectedTest.deskripsi }}
                    </p>

                </div>


                <!-- TUJUAN -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
                >

                    <div class="flex items-center gap-3">

                        <div
                            class="rounded-xl bg-pink-50 p-2 text-pink-700"
                        >

                            <CheckCircleIcon
                                class="h-5 w-5"
                            />

                        </div>

                        <h2
                            class="text-sm font-extrabold text-slate-800"
                        >
                            Tujuan
                        </h2>

                    </div>


                    <p
                        class="mt-4 text-sm leading-7 text-slate-600"
                    >
                        {{ selectedTest.tujuan }}
                    </p>

                </div>

            </div>


            <!-- PERALATAN -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="rounded-xl bg-pink-50 p-2 text-pink-700"
                    >

                        <BeakerIcon class="h-5 w-5" />

                    </div>


                    <div>

                        <h2
                            class="text-sm font-extrabold text-slate-800"
                        >
                            Peralatan
                        </h2>

                        <p
                            class="text-[11px] font-medium text-slate-400"
                        >
                            Peralatan yang diperlukan sebelum pelaksanaan tes.
                        </p>

                    </div>

                </div>


                <div
                    class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2"
                >

                    <div
                        v-for="(alat, index) in selectedTest.peralatan"
                        :key="index"
                        class="flex items-start gap-3 rounded-xl bg-pink-50/50 p-3"
                    >

                        <span
                            class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-extrabold text-pink-700 shadow-sm"
                        >
                            {{ index + 1 }}
                        </span>

                        <span
                            class="text-xs font-semibold leading-5 text-slate-600"
                        >
                            {{ alat }}
                        </span>

                    </div>

                </div>

            </div>


            <!-- PROSEDUR -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
            >

                <div
                    class="border-b border-pink-100 pb-4"
                >

                    <h2
                        class="text-base font-extrabold text-slate-800"
                    >
                        Prosedur Pelaksanaan Tes
                    </h2>

                    <p
                        class="mt-1 text-xs font-medium text-slate-400"
                    >
                        Ikuti tahapan berikut secara berurutan.
                    </p>

                </div>


                <div class="mt-6 space-y-4">

                    <div
                        v-for="(step, index) in selectedTest.prosedur"
                        :key="index"
                        class="flex gap-4"
                    >

                        <div
                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-pink-700 text-xs font-extrabold text-white"
                        >
                            {{ index + 1 }}
                        </div>


                        <div
                            class="rounded-xl bg-pink-50/50 p-4"
                        >

                            <p
                                class="text-sm leading-6 text-slate-600"
                            >
                                {{ step }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- PENSKORAN -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
            >

                <div class="flex items-center gap-3">

                    <div
                        class="rounded-xl bg-pink-50 p-2 text-pink-700"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5"
                        />

                    </div>

                    <h2
                        class="text-sm font-extrabold text-slate-800"
                    >
                        Penskoran
                    </h2>

                </div>


                <div class="mt-5 space-y-3">

                    <div
                        v-for="(item, index) in selectedTest.penskoran"
                        :key="index"
                        class="flex items-start gap-3"
                    >

                        <CheckCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-pink-500"
                        />

                        <p
                            class="text-sm leading-6 text-slate-600"
                        >
                            {{ item }}
                        </p>

                    </div>

                </div>

            </div>


            <!-- VALIDITAS -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
            >

                <h2
                    class="text-sm font-extrabold text-slate-800"
                >
                    Validitas dan Reliabilitas
                </h2>

                <p
                    class="mt-3 text-sm leading-7 text-slate-600"
                >
                    {{ selectedTest.validitas }}
                </p>

            </div>


            <!-- NORMA -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
            >

                <div class="mb-5">

                    <h2
                        class="text-sm font-extrabold text-slate-800"
                    >
                        Norma Tes
                    </h2>

                    <p
                        class="mt-1 text-xs font-medium text-slate-400"
                    >
                        Norma penilaian berdasarkan jenis kelamin dan skor.
                    </p>

                </div>


                <div class="overflow-x-auto">

                    <table
                        class="w-full min-w-[600px] text-left"
                    >

                        <thead>

                            <tr
                                class="border-b border-pink-100"
                            >

                                <th
                                    class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                                >
                                    Putera
                                </th>

                                <th
                                    class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                                >
                                    Puteri
                                </th>

                                <th
                                    class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                                >
                                    Skor
                                </th>

                                <th
                                    class="px-4 py-3 text-[10px] font-extrabold uppercase tracking-wider text-slate-400"
                                >
                                    Kategori
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="(row, index) in selectedTest.norma"
                                :key="index"
                                class="border-b border-pink-50 last:border-0"
                            >

                                <td
                                    class="px-4 py-3 text-xs font-semibold text-slate-600"
                                >
                                    {{ row.putera }}
                                </td>

                                <td
                                    class="px-4 py-3 text-xs font-semibold text-slate-600"
                                >
                                    {{ row.puteri }}
                                </td>

                                <td class="px-4 py-3">

                                    <span
                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-pink-50 text-xs font-extrabold text-pink-700"
                                    >
                                        {{ row.skor }}
                                    </span>

                                </td>

                                <td class="px-4 py-3">

                                    <span
                                        class="inline-flex rounded-full bg-pink-50 px-3 py-1 text-[10px] font-extrabold text-pink-700"
                                    >
                                        {{ row.kategori }}
                                    </span>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- BACK BUTTON -->

            <div
                class="flex justify-center pb-4"
            >

                <button
                    type="button"
                    @click="backToList"
                    class="flex items-center gap-2 rounded-xl bg-pink-700 px-5 py-3 text-xs font-bold text-white shadow-sm transition hover:bg-pink-800"
                >

                    <ArrowLeftIcon
                        class="h-4 w-4"
                    />

                    Kembali ke Daftar Panduan

                </button>

            </div>

        </div>


        <!-- =========================================================
             LIST
        ========================================================== -->

        <div
            v-else
            class="space-y-6"
        >

            <!-- HEADER -->

            <div
                class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-700 via-pink-700 to-rose-800 p-6 text-white shadow-lg md:p-8"
            >

                <div
                    class="pointer-events-none absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10"
                ></div>

                <div
                    class="pointer-events-none absolute -bottom-16 right-20 h-48 w-48 rounded-full bg-white/5"
                ></div>


                <div class="relative z-10">

                    <p
                        class="text-xs font-bold uppercase tracking-wider text-white/60"
                    >
                        TKSI
                    </p>

                    <h1
                        class="mt-1 text-2xl font-bold tracking-tight"
                    >
                        Panduan TKSI
                    </h1>

                    <p
                        class="mt-2 max-w-2xl text-sm font-medium leading-6 text-white/80"
                    >
                        Panduan pelaksanaan tes TKSI sebagai acuan dalam
                        melakukan pengukuran dan pencatatan hasil tes siswa.
                    </p>

                </div>

            </div>


            <!-- INFORMASI -->

            <div
                class="rounded-2xl border border-pink-100 bg-white p-5 shadow-sm"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-50 text-pink-700"
                    >

                        <ClipboardDocumentCheckIcon
                            class="h-5 w-5"
                        />

                    </div>


                    <div>

                        <h2
                            class="text-sm font-extrabold text-slate-800"
                        >
                            Tentang Panduan TKSI
                        </h2>

                        <p
                            class="mt-1 text-xs leading-5 text-slate-500"
                        >
                            Pilih salah satu jenis tes di bawah ini untuk
                            melihat detail prosedur, peralatan, penskoran,
                            validitas, reliabilitas, dan norma tes.
                        </p>

                    </div>

                </div>

            </div>


            <!-- JENIS TES -->

            <div>

                <div class="mb-4">

                    <h2
                        class="text-base font-extrabold text-slate-800"
                    >
                        Jenis Tes TKSI
                    </h2>

                    <p
                        class="mt-1 text-xs font-medium text-slate-400"
                    >
                        Tersedia 6 panduan tes.
                    </p>

                </div>


                <div
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
                >

                    <button
                        v-for="(item, index) in panduan"
                        :key="item.id"
                        type="button"
                        @click="selectTest(item.id)"
                        class="group rounded-2xl border border-pink-100 bg-white p-5 text-left shadow-sm transition duration-200 hover:-translate-y-1 hover:border-pink-200 hover:shadow-md"
                    >

                        <div
                            class="flex items-start justify-between"
                        >

                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-xl bg-pink-50 text-pink-700 transition group-hover:bg-pink-100"
                            >

                                <component
                                    :is="item.icon"
                                    class="h-6 w-6"
                                />

                            </div>


                            <span
                                class="text-[10px] font-bold text-pink-200"
                            >
                                {{ String(index + 1).padStart(2, '0') }}
                            </span>

                        </div>


                        <h3
                            class="mt-5 text-sm font-extrabold text-slate-800 transition group-hover:text-pink-700"
                        >
                            {{ item.title }}
                        </h3>


                        <p
                            class="mt-2 text-xs leading-5 text-slate-500"
                        >
                            {{ item.description }}
                        </p>


                        <div
                            class="mt-5 flex items-center gap-1 text-[10px] font-extrabold text-pink-700"
                        >

                            <span>
                                Lihat panduan
                            </span>

                            <span
                                class="transition-transform duration-200 group-hover:translate-x-1"
                            >
                                →
                            </span>

                        </div>

                    </button>

                </div>

            </div>


            <!-- FOOTER INFORMATION -->

            <div
                class="rounded-2xl border border-pink-100 bg-pink-50 p-5"
            >

                <div
                    class="flex items-start gap-3"
                >

                    <InformationCircleIcon
                        class="mt-0.5 h-5 w-5 shrink-0 text-pink-700"
                    />

                    <p
                        class="text-xs leading-6 text-pink-900"
                    >
                        Gunakan panduan ini sebagai acuan dalam pelaksanaan
                        pengukuran TKSI. Pastikan prosedur, peralatan, dan
                        ketentuan penskoran diperhatikan sebelum melakukan
                        input hasil tes siswa.
                    </p>

                </div>

            </div>

        </div>

    </TksiLayout>

</template>