<script setup>
import { computed, reactive, ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

import {
    ArrowLeftIcon,
    UserIcon,
    CalendarDaysIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon,
    InformationCircleIcon,
} from '@heroicons/vue/24/outline'

import TksiLayout from '@/Layouts/TksiLayout.vue'


/*
|--------------------------------------------------------------------------
| PROPS
|--------------------------------------------------------------------------
*/

const props = defineProps({
    periode: {
        type: Object,
        default: null,
    },

    siswa: {
        type: Array,
        default: () => [],
    },

    siswaTerpilih: {
        type: Object,
        default: null,
    },

    komponen: {
        type: Array,
        default: () => [],
    },

    hasil: {
        type: Object,
        default: () => ({}),
    },

    flash: {
        type: Object,
        default: () => ({}),
    },
})


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const errors = ref({})
const processing = ref(false)


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL
|--------------------------------------------------------------------------
*/

function formatTanggal(tanggal) {
    if (!tanggal) {
        return '-'
    }

    const date = new Date(tanggal)

    if (Number.isNaN(date.getTime())) {
        return String(tanggal)
    }

    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
}


/*
|--------------------------------------------------------------------------
| FORMAT TANGGAL INPUT
|--------------------------------------------------------------------------
*/

function formatTanggalInput(tanggal) {
    if (!tanggal) {
        return new Date().toISOString().slice(0, 10)
    }

    const value = String(tanggal)

    if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
        return value
    }

    return value.slice(0, 10)
}


/*
|--------------------------------------------------------------------------
| DATA SISWA
|--------------------------------------------------------------------------
*/

const siswa = computed(() => {
    if (props.siswaTerpilih) {
        return props.siswaTerpilih
    }

    return props.siswa[0] ?? null
})


/*
|--------------------------------------------------------------------------
| MODE EDIT
|--------------------------------------------------------------------------
*/

const isEdit = computed(() => {
    return Object.keys(props.hasil || {}).length > 0
})


/*
|--------------------------------------------------------------------------
| JENIS KELAMIN
|--------------------------------------------------------------------------
*/

const isPutera = computed(() => {
    const gender = String(
        siswa.value?.jenis_kelamin ?? ''
    )
        .trim()
        .toLowerCase()

    return [
        'laki-laki',
        'laki laki',
        'putera',
        'l',
        'male',
        'm',
    ].includes(gender)
})


const genderLabel = computed(() => {
    return isPutera.value ? 'Putera' : 'Puteri'
})


/*
|--------------------------------------------------------------------------
| TANGGAL TES
|--------------------------------------------------------------------------
*/

const tanggal = ref(
    formatTanggalInput(
        Object.values(props.hasil || {})[0]?.tanggal
    )
)


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = reactive({
    hasil: {},
})


/*
|--------------------------------------------------------------------------
| INISIALISASI FORM
|--------------------------------------------------------------------------
*/

function initForm() {
    const hasilLama = props.hasil || {}

    const data = {}

    props.komponen.forEach(item => {
        const hasilItem = hasilLama[item.key]

        data[item.key] = {
            komponen: item.key,

            nilai:
                hasilItem?.nilai !== null &&
                hasilItem?.nilai !== undefined
                    ? hasilItem.nilai
                    : '',

            level:
                hasilItem?.level !== null &&
                hasilItem?.level !== undefined
                    ? hasilItem.level
                    : '',

            balikan:
                hasilItem?.balikan !== null &&
                hasilItem?.balikan !== undefined
                    ? hasilItem.balikan
                    : '',

            catatan:
                hasilItem?.catatan !== null &&
                hasilItem?.catatan !== undefined
                    ? hasilItem.catatan
                    : '',
        }
    })

    form.hasil = data

    const tanggalLama =
        Object.values(hasilLama)[0]?.tanggal

    if (tanggalLama) {
        tanggal.value = formatTanggalInput(tanggalLama)
    }
}

initForm()


/*
|--------------------------------------------------------------------------
| NORMA TES
|--------------------------------------------------------------------------
*/

const norma = computed(() => {

    if (isPutera.value) {
        return {
            hand_eye: [
                {
                    skor: 5,
                    kategori: 'Baik Sekali',
                    range: '≥ 22 tangkapan',
                },
                {
                    skor: 4,
                    kategori: 'Baik',
                    range: '16 – 21 tangkapan',
                },
                {
                    skor: 3,
                    kategori: 'Sedang',
                    range: '10 – 15 tangkapan',
                },
                {
                    skor: 2,
                    kategori: 'Kurang',
                    range: '4 – 9 tangkapan',
                },
                {
                    skor: 1,
                    kategori: 'Kurang Sekali',
                    range: '< 4 tangkapan',
                },
            ],

            vertical_jump: [
                {
                    skor: 5,
                    kategori: 'Baik Sekali',
                    range: '≥ 63 cm',
                },
                {
                    skor: 4,
                    kategori: 'Baik',
                    range: '59 – 62 cm',
                },
                {
                    skor: 3,
                    kategori: 'Sedang',
                    range: '35 – 58 cm',
                },
                {
                    skor: 2,
                    kategori: 'Kurang',
                    range: '20 – 34 cm',
                },
                {
                    skor: 1,
                    kategori: 'Kurang Sekali',
                    range: '< 20 cm',
                },
            ],

            t_test: [
                {
                    skor: 5,
                    kategori: 'Baik Sekali',
                    range: '≤ 6,63 detik',
                },
                {
                    skor: 4,
                    kategori: 'Baik',
                    range: '6,64 – 10,15 detik',
                },
                {
                    skor: 3,
                    kategori: 'Sedang',
                    range: '10,16 – 14,16 detik',
                },
                {
                    skor: 2,
                    kategori: 'Kurang',
                    range: '14,17 – 18,17 detik',
                },
                {
                    skor: 1,
                    kategori: 'Kurang Sekali',
                    range: '> 18,17 detik',
                },
            ],

            hand_touch: [
                {
                    skor: 5,
                    kategori: 'Baik Sekali',
                    range: '≤ 0,80 detik',
                },
                {
                    skor: 4,
                    kategori: 'Baik',
                    range: '0,81 – 1,09 detik',
                },
                {
                    skor: 3,
                    kategori: 'Sedang',
                    range: '1,10 – 1,39 detik',
                },
                {
                    skor: 2,
                    kategori: 'Kurang',
                    range: '1,40 – 1,69 detik',
                },
                {
                    skor: 1,
                    kategori: 'Kurang Sekali',
                    range: '> 1,69 detik',
                },
            ],

            dipping: [
                {
                    skor: 5,
                    kategori: 'Baik Sekali',
                    range: '≥ 25 kali',
                },
                {
                    skor: 4,
                    kategori: 'Baik',
                    range: '22 – 24 kali',
                },
                {
                    skor: 3,
                    kategori: 'Sedang',
                    range: '19 – 21 kali',
                },
                {
                    skor: 2,
                    kategori: 'Kurang',
                    range: '16 – 18 kali',
                },
                {
                    skor: 1,
                    kategori: 'Kurang Sekali',
                    range: '< 16 kali',
                },
            ],
        }
    }

    return {
        hand_eye: [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≥ 15 tangkapan',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: '10 – 14 tangkapan',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: '5 – 9 tangkapan',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: '1 – 4 tangkapan',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '< 1 tangkapan',
            },
        ],

        vertical_jump: [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≥ 59 cm',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: '35 – 58 cm',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: '27 – 34 cm',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: '19 – 26 cm',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '< 19 cm',
            },
        ],

        t_test: [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≤ 7,19 detik',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: '7,20 – 11,20 detik',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: '11,21 – 15,19 detik',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: '15,20 – 19,20 detik',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '> 19,20 detik',
            },
        ],

        hand_touch: [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≤ 0,91 detik',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: '0,92 – 1,21 detik',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: '1,22 – 1,51 detik',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: '1,52 – 1,81 detik',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '> 1,81 detik',
            },
        ],

        dipping: [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≥ 19 kali',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: '16 – 18 kali',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: '13 – 15 kali',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: '10 – 12 kali',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '< 10 kali',
            },
        ],
    }
})


/*
|--------------------------------------------------------------------------
| NORMA BEEP TEST
|--------------------------------------------------------------------------
|
| PUTERA
|
| ≥ L12 B3              = 5
| L9 B10 - L12 B2       = 4
| L7 B4 - L9 B9         = 3
| L4 B8 - L7 B3         = 2
| ≤ L4 B7               = 1
|
| PUTERI
|
| ≥ L7 B10              = 5
| L6 B2 - L7 B9         = 4
| L4 B6 - L6 B1         = 3
| L1 B5 - L4 B5         = 2
| ≤ L1 B4               = 1
|
|--------------------------------------------------------------------------
*/

const normaBeepTest = computed(() => {

    if (isPutera.value) {
        return [
            {
                skor: 5,
                kategori: 'Baik Sekali',
                range: '≥ L12 B3',
            },
            {
                skor: 4,
                kategori: 'Baik',
                range: 'L9 B10 – L12 B2',
            },
            {
                skor: 3,
                kategori: 'Sedang',
                range: 'L7 B4 – L9 B9',
            },
            {
                skor: 2,
                kategori: 'Kurang',
                range: 'L4 B8 – L7 B3',
            },
            {
                skor: 1,
                kategori: 'Kurang Sekali',
                range: '≤ L4 B7',
            },
        ]
    }

    return [
        {
            skor: 5,
            kategori: 'Baik Sekali',
            range: '≥ L7 B10',
        },
        {
            skor: 4,
            kategori: 'Baik',
            range: 'L6 B2 – L7 B9',
        },
        {
            skor: 3,
            kategori: 'Sedang',
            range: 'L4 B6 – L6 B1',
        },
        {
            skor: 2,
            kategori: 'Kurang',
            range: 'L1 B5 – L4 B5',
        },
        {
            skor: 1,
            kategori: 'Kurang Sekali',
            range: '≤ L1 B4',
        },
    ]
})


/*
|--------------------------------------------------------------------------
| PLACEHOLDER NILAI
|--------------------------------------------------------------------------
*/

const placeholderNilai = (key) => {

    if (key === 'hand_eye') {
        return isPutera.value
            ? 'Contoh: 18'
            : 'Contoh: 12'
    }

    if (key === 'vertical_jump') {
        return isPutera.value
            ? 'Contoh: 60'
            : 'Contoh: 40'
    }

    if (key === 't_test') {
        return isPutera.value
            ? 'Contoh: 9.50'
            : 'Contoh: 10.50'
    }

    if (key === 'hand_touch') {
        return isPutera.value
            ? 'Contoh: 1.00'
            : 'Contoh: 1.10'
    }

    if (key === 'dipping') {
        return isPutera.value
            ? 'Contoh: 23'
            : 'Contoh: 17'
    }

    return 'Masukkan nilai'
}


/*
|--------------------------------------------------------------------------
| RANGE TEKS
|--------------------------------------------------------------------------
*/

const placeholderRange = (key) => {

    const data = norma.value[key]

    if (!data) {
        return ''
    }

    return data
        .map(item => `${item.kategori}: ${item.range}`)
        .join(' • ')
}


/*
|--------------------------------------------------------------------------
| HASIL BEEP TEST
|--------------------------------------------------------------------------
*/

const hasilBeepTest = (key) => {

    const data = form.hasil[key]

    if (!data) {
        return null
    }

    const level = Number(data.level)
    const balikan = Number(data.balikan)

    if (
        data.level === '' ||
        data.level === null ||
        data.level === undefined ||
        Number.isNaN(level)
    ) {
        return null
    }

    if (
        data.balikan === '' ||
        data.balikan === null ||
        data.balikan === undefined ||
        Number.isNaN(balikan)
    ) {
        return null
    }

    return {
        level,
        balikan,

        /*
        |----------------------------------------------------------------------
        | Nilai numerik untuk penyimpanan
        |----------------------------------------------------------------------
        |
        | Contoh:
        | L5 B8 = 5.8
        |
        */

        total: level + (balikan / 10),

        display: `${level}.${balikan}`,
    }
}


/*
|--------------------------------------------------------------------------
| KONVERSI LEVEL + BALIKAN KE NILAI URUT
|--------------------------------------------------------------------------
|
| Digunakan agar:
|
| L12 B3 > L12 B2
| L12 B2 > L9 B10
| L9 B10 > L9 B9
|
| Tidak hanya membandingkan level.
|--------------------------------------------------------------------------
*/

const nilaiBeep = (level, balikan) => {

    return (
        (Number(level) * 100) +
        Number(balikan)
    )
}


/*
|--------------------------------------------------------------------------
| KATEGORI BEEP TEST
|--------------------------------------------------------------------------
*/

const kategoriBeep = (key) => {

    const hasil = hasilBeepTest(key)

    if (!hasil) {
        return null
    }

    const level = hasil.level
    const balikan = hasil.balikan

    const nilai = nilaiBeep(
        level,
        balikan
    )


    /*
    |--------------------------------------------------------------------------
    | PUTERA
    |--------------------------------------------------------------------------
    |
    | ≥ L12 B3
    | L9 B10 - L12 B2
    | L7 B4 - L9 B9
    | L4 B8 - L7 B3
    | ≤ L4 B7
    |
    */

    if (isPutera.value) {

        /*
        |----------------------------------------------------------------------
        | SKOR 5
        | ≥ L12 B3
        |----------------------------------------------------------------------
        */

        if (
            nilai >= nilaiBeep(12, 3)
        ) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }


        /*
        |----------------------------------------------------------------------
        | SKOR 4
        | L9 B10 - L12 B2
        |----------------------------------------------------------------------
        */

        if (
            nilai >= nilaiBeep(9, 10)
        ) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }


        /*
        |----------------------------------------------------------------------
        | SKOR 3
        | L7 B4 - L9 B9
        |----------------------------------------------------------------------
        */

        if (
            nilai >= nilaiBeep(7, 4)
        ) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }


        /*
        |----------------------------------------------------------------------
        | SKOR 2
        | L4 B8 - L7 B3
        |----------------------------------------------------------------------
        */

        if (
            nilai >= nilaiBeep(4, 8)
        ) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }


        /*
        |----------------------------------------------------------------------
        | SKOR 1
        | ≤ L4 B7
        |----------------------------------------------------------------------
        */

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PUTERI
    |--------------------------------------------------------------------------
    |
    | ≥ L7 B10
    | L6 B2 - L7 B9
    | L4 B6 - L6 B1
    | L1 B5 - L4 B5
    | ≤ L1 B4
    |
    */

    /*
    |--------------------------------------------------------------------------
    | SKOR 5
    | ≥ L7 B10
    |--------------------------------------------------------------------------
    */

    if (
        nilai >= nilaiBeep(7, 10)
    ) {
        return {
            skor: 5,
            kategori: 'Baik Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SKOR 4
    | L6 B2 - L7 B9
    |--------------------------------------------------------------------------
    */

    if (
        nilai >= nilaiBeep(6, 2)
    ) {
        return {
            skor: 4,
            kategori: 'Baik',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SKOR 3
    | L4 B6 - L6 B1
    |--------------------------------------------------------------------------
    */

    if (
        nilai >= nilaiBeep(4, 6)
    ) {
        return {
            skor: 3,
            kategori: 'Sedang',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SKOR 2
    | L1 B5 - L4 B5
    |--------------------------------------------------------------------------
    */

    if (
        nilai >= nilaiBeep(1, 5)
    ) {
        return {
            skor: 2,
            kategori: 'Kurang',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SKOR 1
    | ≤ L1 B4
    |--------------------------------------------------------------------------
    */

    return {
        skor: 1,
        kategori: 'Kurang Sekali',
    }
}


/*
|--------------------------------------------------------------------------
| KATEGORI NILAI BIASA
|--------------------------------------------------------------------------
*/

const kategoriNilai = (key) => {

    const data = form.hasil[key]

    if (!data) {
        return null
    }

    if (
        data.nilai === '' ||
        data.nilai === null ||
        data.nilai === undefined
    ) {
        return null
    }

    const nilai = Number(data.nilai)

    if (Number.isNaN(nilai)) {
        return null
    }


    /*
    |--------------------------------------------------------------------------
    | HAND EYE
    |--------------------------------------------------------------------------
    */

    if (key === 'hand_eye') {

        if (isPutera.value) {

            if (nilai >= 22) {
                return {
                    skor: 5,
                    kategori: 'Baik Sekali',
                }
            }

            if (nilai >= 16) {
                return {
                    skor: 4,
                    kategori: 'Baik',
                }
            }

            if (nilai >= 10) {
                return {
                    skor: 3,
                    kategori: 'Sedang',
                }
            }

            if (nilai >= 4) {
                return {
                    skor: 2,
                    kategori: 'Kurang',
                }
            }

            return {
                skor: 1,
                kategori: 'Kurang Sekali',
            }
        }


        if (nilai >= 15) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (nilai >= 10) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (nilai >= 5) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (nilai >= 1) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VERTICAL JUMP
    |--------------------------------------------------------------------------
    */

    if (key === 'vertical_jump') {

        if (isPutera.value) {

            if (nilai >= 63) {
                return {
                    skor: 5,
                    kategori: 'Baik Sekali',
                }
            }

            if (nilai >= 59) {
                return {
                    skor: 4,
                    kategori: 'Baik',
                }
            }

            if (nilai >= 35) {
                return {
                    skor: 3,
                    kategori: 'Sedang',
                }
            }

            if (nilai >= 20) {
                return {
                    skor: 2,
                    kategori: 'Kurang',
                }
            }

            return {
                skor: 1,
                kategori: 'Kurang Sekali',
            }
        }


        if (nilai >= 59) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (nilai >= 35) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (nilai >= 27) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (nilai >= 19) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | T TEST
    |--------------------------------------------------------------------------
    */

    if (key === 't_test') {

        if (isPutera.value) {

            if (nilai <= 6.63) {
                return {
                    skor: 5,
                    kategori: 'Baik Sekali',
                }
            }

            if (nilai <= 10.15) {
                return {
                    skor: 4,
                    kategori: 'Baik',
                }
            }

            if (nilai <= 14.16) {
                return {
                    skor: 3,
                    kategori: 'Sedang',
                }
            }

            if (nilai <= 18.17) {
                return {
                    skor: 2,
                    kategori: 'Kurang',
                }
            }

            return {
                skor: 1,
                kategori: 'Kurang Sekali',
            }
        }


        if (nilai <= 7.19) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (nilai <= 11.20) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (nilai <= 15.19) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (nilai <= 19.20) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HAND TOUCH
    |--------------------------------------------------------------------------
    */

    if (key === 'hand_touch') {

        if (isPutera.value) {

            if (nilai <= 0.80) {
                return {
                    skor: 5,
                    kategori: 'Baik Sekali',
                }
            }

            if (nilai <= 1.09) {
                return {
                    skor: 4,
                    kategori: 'Baik',
                }
            }

            if (nilai <= 1.39) {
                return {
                    skor: 3,
                    kategori: 'Sedang',
                }
            }

            if (nilai <= 1.69) {
                return {
                    skor: 2,
                    kategori: 'Kurang',
                }
            }

            return {
                skor: 1,
                kategori: 'Kurang Sekali',
            }
        }


        if (nilai <= 0.91) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (nilai <= 1.21) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (nilai <= 1.51) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (nilai <= 1.81) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DIPPING
    |--------------------------------------------------------------------------
    */

    if (key === 'dipping') {

        if (isPutera.value) {

            if (nilai >= 25) {
                return {
                    skor: 5,
                    kategori: 'Baik Sekali',
                }
            }

            if (nilai >= 22) {
                return {
                    skor: 4,
                    kategori: 'Baik',
                }
            }

            if (nilai >= 19) {
                return {
                    skor: 3,
                    kategori: 'Sedang',
                }
            }

            if (nilai >= 16) {
                return {
                    skor: 2,
                    kategori: 'Kurang',
                }
            }

            return {
                skor: 1,
                kategori: 'Kurang Sekali',
            }
        }


        if (nilai >= 19) {
            return {
                skor: 5,
                kategori: 'Baik Sekali',
            }
        }

        if (nilai >= 16) {
            return {
                skor: 4,
                kategori: 'Baik',
            }
        }

        if (nilai >= 13) {
            return {
                skor: 3,
                kategori: 'Sedang',
            }
        }

        if (nilai >= 10) {
            return {
                skor: 2,
                kategori: 'Kurang',
            }
        }

        return {
            skor: 1,
            kategori: 'Kurang Sekali',
        }
    }

    return null
}


/*
|--------------------------------------------------------------------------
| GET KATEGORI
|--------------------------------------------------------------------------
*/

const getKategori = (key) => {

    if (key === 'beep_test') {
        return kategoriBeep(key)
    }

    return kategoriNilai(key)
}


/*
|--------------------------------------------------------------------------
| CLASS KATEGORI
|--------------------------------------------------------------------------
*/

const kategoriClass = (kategori) => {

    if (kategori === 'Baik Sekali') {
        return 'bg-emerald-100 text-emerald-700 border-emerald-200'
    }

    if (kategori === 'Baik') {
        return 'bg-green-100 text-green-700 border-green-200'
    }

    if (kategori === 'Sedang') {
        return 'bg-yellow-100 text-yellow-700 border-yellow-200'
    }

    if (kategori === 'Kurang') {
        return 'bg-orange-100 text-orange-700 border-orange-200'
    }

    if (kategori === 'Kurang Sekali') {
        return 'bg-red-100 text-red-700 border-red-200'
    }

    return 'bg-gray-100 text-gray-600 border-gray-200'
}


/*
|--------------------------------------------------------------------------
| VALIDASI FORM
|--------------------------------------------------------------------------
*/

function validateForm() {

    errors.value = {}

    let valid = true


    if (!tanggal.value) {
        errors.value.tanggal =
            'Tanggal tes wajib diisi.'

        valid = false
    }


    if (!siswa.value?.id) {
        errors.value.siswa_id =
            'Data siswa tidak ditemukan.'

        valid = false
    }


    props.komponen.forEach((item, index) => {

        const data = form.hasil[item.key]


        /*
        |--------------------------------------------------------------------------
        | BEEP TEST
        |--------------------------------------------------------------------------
        */

        if (item.key === 'beep_test') {

            const level = data?.level
            const balikan = data?.balikan


            if (
                level === '' ||
                level === null ||
                level === undefined
            ) {
                errors.value[
                    `hasil.${index}.level`
                ] =
                    'Level Beep Test wajib diisi.'

                valid = false
            }
            else if (
                Number.isNaN(Number(level)) ||
                Number(level) < 1
            ) {
                errors.value[
                    `hasil.${index}.level`
                ] =
                    'Level harus berupa angka minimal 1.'

                valid = false
            }


            if (
                balikan === '' ||
                balikan === null ||
                balikan === undefined
            ) {
                errors.value[
                    `hasil.${index}.balikan`
                ] =
                    'Balikan Beep Test wajib diisi.'

                valid = false
            }
            else if (
                Number.isNaN(Number(balikan)) ||
                Number(balikan) < 1
            ) {
                errors.value[
                    `hasil.${index}.balikan`
                ] =
                    'Balikan harus berupa angka minimal 1.'

                valid = false
            }


            return
        }


        /*
        |--------------------------------------------------------------------------
        | KOMPONEN BIASA
        |--------------------------------------------------------------------------
        */

        const nilai = data?.nilai

        if (
            nilai === '' ||
            nilai === null ||
            nilai === undefined
        ) {
            errors.value[
                `hasil.${index}.nilai`
            ] =
                `Nilai ${item.nama} wajib diisi.`

            valid = false

            return
        }


        if (Number.isNaN(Number(nilai))) {
            errors.value[
                `hasil.${index}.nilai`
            ] =
                `Nilai ${item.nama} harus berupa angka.`

            valid = false

            return
        }


        if (Number(nilai) < 0) {
            errors.value[
                `hasil.${index}.nilai`
            ] =
                `Nilai ${item.nama} tidak boleh negatif.`

            valid = false
        }
    })


    return valid
}


/*
|--------------------------------------------------------------------------
| STATUS KELENGKAPAN
|--------------------------------------------------------------------------
*/

const sudahLengkap = computed(() => {

    return (
        props.komponen.length > 0 &&
        props.komponen.every(item => {

            const data =
                form.hasil[item.key]


            if (item.key === 'beep_test') {

                return (
                    data?.level !== '' &&
                    data?.level !== null &&
                    data?.level !== undefined &&
                    data?.balikan !== '' &&
                    data?.balikan !== null &&
                    data?.balikan !== undefined
                )
            }


            const nilai = data?.nilai

            return (
                nilai !== '' &&
                nilai !== null &&
                nilai !== undefined
            )
        })
    )
})


/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/

function submit() {

    if (!validateForm()) {
        return
    }

    if (!siswa.value?.id) {
        return
    }

    processing.value = true


    const hasil = Object.values(
        form.hasil
    ).map(item => {

        /*
        |--------------------------------------------------------------------------
        | BEEP TEST
        |--------------------------------------------------------------------------
        */

        if (item.komponen === 'beep_test') {

            const level =
                item.level !== '' &&
                item.level !== null &&
                item.level !== undefined
                    ? Number(item.level)
                    : null

            const balikan =
                item.balikan !== '' &&
                item.balikan !== null &&
                item.balikan !== undefined
                    ? Number(item.balikan)
                    : null


            /*
            |--------------------------------------------------------------------------
            | NILAI BEEP TEST
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | L5 B8 = 5.8
            | L12 B3 = 12.3
            |
            */

            const nilai =
                level !== null &&
                balikan !== null
                    ? Number(
                        `${level}.${balikan}`
                    )
                    : null


            return {
                komponen: item.komponen,

                nilai,

                level,

                balikan,

                catatan:
                    item.catatan?.trim()
                        ? item.catatan.trim()
                        : null,
            }
        }


        /*
        |--------------------------------------------------------------------------
        | KOMPONEN BIASA
        |--------------------------------------------------------------------------
        */

        return {
            komponen: item.komponen,

            nilai:
                item.nilai !== '' &&
                item.nilai !== null &&
                item.nilai !== undefined
                    ? Number(item.nilai)
                    : null,

            level: null,

            balikan: null,

            catatan:
                item.catatan?.trim()
                    ? item.catatan.trim()
                    : null,
        }
    })


    /*
    |--------------------------------------------------------------------------
    | PAYLOAD
    |--------------------------------------------------------------------------
    */

    const payload = {
        siswa_id: siswa.value.id,

        tanggal: tanggal.value,

        hasil,
    }


    /*
    |--------------------------------------------------------------------------
    | INPUT BARU
    |--------------------------------------------------------------------------
    */

    if (!isEdit.value) {

        router.post(
            route('tksi.input.store'),
            payload,
            {
                preserveScroll: true,

                onError: err => {
                    errors.value = err
                },

                onFinish: () => {
                    processing.value = false
                },
            }
        )

        return
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    router.patch(
        route(
            'tksi.input.update',
            siswa.value.id
        ),
        payload,
        {
            preserveScroll: true,

            onError: err => {
                errors.value = err
            },

            onFinish: () => {
                processing.value = false
            },
        }
    )
}
</script>


<template>
    <TksiLayout>

        <Head
            :title="
                isEdit
                    ? 'Edit Hasil TKSI'
                    : 'Input Hasil TKSI'
            "
        />


        <div class="space-y-6">

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

                    <div class="flex items-center gap-3">

                        <Link
                            :href="
                                route(
                                    'tksi.input.index'
                                )
                            "
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 transition hover:bg-white/20"
                        >
                            <ArrowLeftIcon class="h-5 w-5" />
                        </Link>


                        <div>

                            <h1
                                class="text-2xl font-bold tracking-tight"
                            >
                                {{
                                    isEdit
                                        ? 'Edit Hasil TKSI'
                                        : 'Input Hasil TKSI'
                                }}
                            </h1>


                            <p
                                class="mt-1 text-sm font-medium text-white/80"
                            >
                                {{
                                    isEdit
                                        ? 'Perbarui hasil tes kebugaran siswa.'
                                        : 'Masukkan hasil tes kebugaran siswa.'
                                }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <!-- TIDAK ADA PERIODE -->

            <div
                v-if="!periode"
                class="rounded-2xl border border-rose-100 bg-rose-50 p-6"
            >

                <p
                    class="text-sm font-extrabold text-rose-700"
                >
                    Tidak ada periode aktif.
                </p>

                <p
                    class="mt-1 text-xs text-rose-600"
                >
                    Input TKSI belum dapat dilakukan sampai
                    admin mengaktifkan periode.
                </p>

            </div>


            <!-- KONTEN -->

            <template
                v-if="periode && siswa"
            >

                <!-- PERIODE -->

                <div
                    class="rounded-2xl border border-pink-100 bg-pink-50 p-5"
                >

                    <div
                        class="flex items-start gap-3"
                    >

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-pink-100 text-pink-700"
                        >
                            <CalendarDaysIcon
                                class="h-5 w-5"
                            />
                        </div>


                        <div class="min-w-0">

                            <p
                                class="text-[10px] font-extrabold uppercase tracking-wider text-pink-500"
                            >
                                Periode Aktif
                            </p>


                            <h2
                                class="mt-1 text-base font-extrabold text-pink-900"
                            >
                                {{ periode.nama_periode }}
                            </h2>


                            <p
                                class="mt-1 text-xs font-medium text-pink-700"
                            >
                                {{ formatTanggal(periode.tanggal_mulai) }}
                                —
                                {{ formatTanggal(periode.tanggal_selesai) }}
                            </p>

                        </div>


                        <span
                            class="ml-auto rounded-full bg-emerald-100 px-3 py-1 text-[9px] font-extrabold uppercase text-emerald-700"
                        >
                            Aktif
                        </span>

                    </div>

                </div>


                <!-- DATA SISWA -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
                >

                    <div
                        class="flex items-center gap-3"
                    >

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-pink-50 text-pink-700"
                        >
                            <UserIcon
                                class="h-5 w-5"
                            />
                        </div>


                        <div>

                            <h2
                                class="text-sm font-extrabold text-slate-800"
                            >
                                Data Peserta
                            </h2>

                            <p
                                class="text-xs text-slate-400"
                            >
                                Siswa yang mengikuti tes TKSI.
                            </p>

                        </div>

                    </div>


                    <div
                        class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-4"
                    >

                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                NISN
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.nisn || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Nama
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.nama || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Kelas
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ siswa.kelas?.nama_kelas || '-' }}
                            </p>

                        </div>


                        <div>

                            <p
                                class="text-[9px] font-bold uppercase text-slate-400"
                            >
                                Jenis Kelamin
                            </p>

                            <p
                                class="mt-1 text-xs font-extrabold text-slate-700"
                            >
                                {{ genderLabel }}
                            </p>

                        </div>

                    </div>

                </div>


                <!-- TANGGAL -->

                <div
                    class="rounded-2xl border border-pink-100 bg-white p-6 shadow-sm"
                >

                    <label
                        class="mb-2 block text-xs font-bold text-slate-600"
                    >
                        Tanggal Tes
                    </label>


                    <input
                        v-model="tanggal"
                        type="date"
                        :min="
                            formatTanggalInput(
                                periode.tanggal_mulai
                            )
                        "
                        :max="
                            formatTanggalInput(
                                periode.tanggal_selesai
                            )
                        "
                        class="w-full rounded-xl border border-pink-100 bg-white px-4 py-3 text-sm font-semibold text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100 md:w-1/2"
                    />


                    <p
                        v-if="errors.tanggal"
                        class="mt-1 text-xs font-semibold text-rose-600"
                    >
                        {{ errors.tanggal }}
                    </p>

                </div>


                <!-- INFO NORMA -->

                <div
                    class="rounded-2xl border border-pink-100 bg-pink-50 p-5"
                >

                    <div class="flex gap-3">

                        <InformationCircleIcon
                            class="mt-0.5 h-5 w-5 shrink-0 text-pink-600"
                        />

                        <div>

                            <p
                                class="text-sm font-extrabold text-pink-800"
                            >
                                Norma {{ genderLabel }}
                            </p>

                            <p
                                class="mt-1 text-xs font-medium leading-5 text-pink-700"
                            >
                                Kategori dan skor hasil tes akan
                                dihitung otomatis berdasarkan
                                jenis kelamin siswa.
                            </p>

                        </div>

                    </div>

                </div>


                <!-- HASIL -->

                <div>

                    <div class="mb-4">

                        <h2
                            class="text-base font-extrabold text-slate-800"
                        >
                            Hasil Tes TKSI
                        </h2>

                        <p
                            class="mt-1 text-xs font-medium text-slate-400"
                        >
                            Masukkan hasil setiap komponen tes.
                        </p>

                    </div>


                    <div class="space-y-5">

                        <div
                            v-for="(item, index) in komponen"
                            :key="item.key"
                            class="overflow-hidden rounded-2xl border border-pink-100 bg-white shadow-sm"
                        >

                            <!-- TITLE -->

                            <div
                                class="border-b border-pink-100 bg-pink-50 px-5 py-4"
                            >

                                <div
                                    class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
                                >

                                    <div>

                                        <div
                                            class="flex items-center gap-2"
                                        >

                                            <div
                                                class="flex h-9 w-9 items-center justify-center rounded-xl bg-pink-50 text-pink-700"
                                            >
                                                <ClipboardDocumentCheckIcon
                                                    class="h-5 w-5"
                                                />
                                            </div>

                                            <h3
                                                class="text-sm font-extrabold text-slate-800"
                                            >
                                                {{ item.nama }}
                                            </h3>

                                        </div>


                                        <p
                                            class="mt-2 text-xs leading-5 text-slate-400"
                                        >
                                            {{ item.deskripsi }}
                                        </p>

                                    </div>


                                    <span
                                        class="inline-flex w-fit rounded-full bg-pink-100 px-3 py-1 text-[10px] font-bold text-pink-700"
                                    >
                                        {{ item.satuan }}
                                    </span>

                                </div>

                            </div>


                            <!-- CONTENT -->

                            <div class="p-5">

                                <!-- BEEP TEST -->

                                <template
                                    v-if="item.key === 'beep_test'"
                                >

                                    <div
                                        class="grid grid-cols-1 gap-5 lg:grid-cols-3"
                                    >

                                        <!-- LEVEL -->

                                        <div>

                                            <label
                                                class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                            >
                                                Level
                                            </label>


                                            <input
                                                v-model="
                                                    form.hasil[item.key].level
                                                "
                                                type="number"
                                                min="1"
                                                step="1"
                                                placeholder="Contoh: 5"
                                                class="w-full rounded-xl border border-pink-100 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                                            />


                                            <p
                                                v-if="
                                                    errors[
                                                        `hasil.${index}.level`
                                                    ]
                                                "
                                                class="mt-1 text-xs font-semibold text-rose-600"
                                            >
                                                {{
                                                    errors[
                                                        `hasil.${index}.level`
                                                    ]
                                                }}
                                            </p>

                                        </div>


                                        <!-- BALIKAN -->

                                        <div>

                                            <label
                                                class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                            >
                                                Balikan
                                            </label>


                                            <input
                                                v-model="
                                                    form.hasil[item.key].balikan
                                                "
                                                type="number"
                                                min="1"
                                                step="1"
                                                placeholder="Contoh: 8"
                                                class="w-full rounded-xl border border-pink-100 px-4 py-3 text-sm font-bold text-slate-800 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                                            />


                                            <p
                                                v-if="
                                                    errors[
                                                        `hasil.${index}.balikan`
                                                    ]
                                                "
                                                class="mt-1 text-xs font-semibold text-rose-600"
                                            >
                                                {{
                                                    errors[
                                                        `hasil.${index}.balikan`
                                                    ]
                                                }}
                                            </p>

                                        </div>


                                        <!-- HASIL -->

                                        <div>

                                            <label
                                                class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                            >
                                                Hasil Beep Test
                                            </label>


                                            <div
                                                v-if="
                                                    hasilBeepTest(item.key)
                                                "
                                                class="rounded-xl border border-pink-200 bg-pink-50 p-4"
                                            >

                                                <p
                                                    class="text-[10px] font-bold uppercase text-pink-500"
                                                >
                                                    Level.Balikan
                                                </p>


                                                <p
                                                    class="mt-1 text-2xl font-extrabold text-pink-900"
                                                >
                                                    {{
                                                        hasilBeepTest(
                                                            item.key
                                                        ).display
                                                    }}
                                                </p>


                                                <p
                                                    class="mt-1 text-[10px] font-medium text-pink-600"
                                                >
                                                    Level
                                                    {{
                                                        hasilBeepTest(
                                                            item.key
                                                        ).level
                                                    }}
                                                    —
                                                    Balikan
                                                    {{
                                                        hasilBeepTest(
                                                            item.key
                                                        ).balikan
                                                    }}
                                                </p>

                                            </div>


                                            <div
                                                v-else
                                                class="flex min-h-[82px] items-center justify-center rounded-xl border border-dashed border-pink-100 bg-pink-50 text-center text-xs text-slate-400"
                                            >
                                                Masukkan level dan
                                                balikan.
                                            </div>

                                        </div>

                                    </div>


                                    <!-- INDIKATOR BEEP -->

                                    <div class="mt-5">

                                        <label
                                            class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                        >
                                            Indikator Hasil
                                        </label>


                                        <div
                                            v-if="getKategori(item.key)"
                                            class="rounded-xl border p-4"
                                            :class="
                                                kategoriClass(
                                                    getKategori(
                                                        item.key
                                                    ).kategori
                                                )
                                            "
                                        >

                                            <div
                                                class="flex items-center justify-between"
                                            >

                                                <div>

                                                    <p
                                                        class="text-[10px] font-bold uppercase opacity-70"
                                                    >
                                                        Kategori
                                                    </p>


                                                    <p
                                                        class="mt-1 text-lg font-extrabold"
                                                    >
                                                        {{
                                                            getKategori(
                                                                item.key
                                                            ).kategori
                                                        }}
                                                    </p>

                                                </div>


                                                <div
                                                    class="text-right"
                                                >

                                                    <p
                                                        class="text-[10px] font-bold uppercase opacity-70"
                                                    >
                                                        Skor
                                                    </p>


                                                    <p
                                                        class="mt-1 text-2xl font-extrabold"
                                                    >
                                                        {{
                                                            getKategori(
                                                                item.key
                                                            ).skor
                                                        }}
                                                    </p>

                                                </div>

                                            </div>

                                        </div>


                                        <div
                                            v-else
                                            class="flex min-h-[92px] items-center justify-center rounded-xl border border-dashed border-pink-100 bg-pink-50 px-4 text-center text-xs font-medium text-slate-400"
                                        >
                                            Masukkan level dan
                                            balikan untuk melihat
                                            kategori otomatis.
                                        </div>

                                    </div>


                                    <!-- NORMA BEEP -->

                                    <div
                                        class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-5"
                                    >

                                        <div
                                            v-for="norm in normaBeepTest"
                                            :key="norm.skor"
                                            class="rounded-xl border border-pink-100 bg-white p-3"
                                        >

                                            <div
                                                class="flex items-center justify-between gap-2"
                                            >

                                                <span
                                                    class="text-[10px] font-extrabold text-slate-700"
                                                >
                                                    {{ norm.kategori }}
                                                </span>


                                                <span
                                                    class="rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600"
                                                >
                                                    {{ norm.skor }}
                                                </span>

                                            </div>


                                            <p
                                                class="mt-1 text-[10px] leading-4 text-slate-500"
                                            >
                                                {{ norm.range }}
                                            </p>

                                        </div>

                                    </div>


                                    <!-- INFO -->

                                    <div
                                        class="mt-4 rounded-xl border border-amber-100 bg-amber-50 p-4"
                                    >

                                        <div
                                            class="flex gap-3"
                                        >

                                            <InformationCircleIcon
                                                class="h-5 w-5 shrink-0 text-amber-600"
                                            />


                                            <div>

                                                <p
                                                    class="text-xs font-extrabold text-amber-800"
                                                >
                                                    Cara Pengisian
                                                </p>


                                                <p
                                                    class="mt-1 text-[11px] leading-5 text-amber-700"
                                                >
                                                    Masukkan level
                                                    terakhir yang
                                                    dicapai dan jumlah
                                                    balikan pada level
                                                    tersebut.
                                                    Contoh:
                                                    Level 5 dan
                                                    Balikan 8 akan
                                                    ditampilkan sebagai
                                                    <strong>5.8</strong>.
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                </template>


                                <!-- KOMPONEN BIASA -->

                                <template v-else>

                                    <div
                                        class="grid grid-cols-1 gap-5 lg:grid-cols-3"
                                    >

                                        <div>

                                            <label
                                                class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                            >
                                                Nilai
                                            </label>


                                            <div
                                                class="relative"
                                            >

                                                <input
                                                    v-model="
                                                        form.hasil[
                                                            item.key
                                                        ].nilai
                                                    "
                                                    type="number"
                                                    min="0"
                                                    step="0.01"
                                                    :placeholder="
                                                        placeholderNilai(
                                                            item.key
                                                        )
                                                    "
                                                    class="w-full rounded-xl border border-pink-100 px-4 py-3 pr-20 text-sm font-bold text-slate-800 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                                                />


                                                <span
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400"
                                                >
                                                    {{ item.satuan }}
                                                </span>

                                            </div>


                                            <p
                                                v-if="
                                                    errors[
                                                        `hasil.${index}.nilai`
                                                    ]
                                                "
                                                class="mt-1 text-xs font-semibold text-rose-600"
                                            >
                                                {{
                                                    errors[
                                                        `hasil.${index}.nilai`
                                                    ]
                                                }}
                                            </p>


                                            <div
                                                v-if="
                                                    norma[item.key]
                                                "
                                                class="mt-3 rounded-xl bg-pink-50 p-3"
                                            >

                                                <p
                                                    class="mb-1 text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                                >
                                                    Rentang nilai
                                                    {{ genderLabel }}
                                                </p>


                                                <p
                                                    class="text-[11px] leading-5 text-slate-600"
                                                >
                                                    {{
                                                        placeholderRange(
                                                            item.key
                                                        )
                                                    }}
                                                </p>

                                            </div>

                                        </div>


                                        <div
                                            class="lg:col-span-2"
                                        >

                                            <label
                                                class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                            >
                                                Indikator Hasil
                                            </label>


                                            <div
                                                v-if="
                                                    getKategori(
                                                        item.key
                                                    )
                                                "
                                                class="rounded-xl border p-4"
                                                :class="
                                                    kategoriClass(
                                                        getKategori(
                                                            item.key
                                                        ).kategori
                                                    )
                                                "
                                            >

                                                <div
                                                    class="flex items-center justify-between"
                                                >

                                                    <div>

                                                        <p
                                                            class="text-[10px] font-bold uppercase opacity-70"
                                                        >
                                                            Kategori
                                                        </p>


                                                        <p
                                                            class="mt-1 text-lg font-extrabold"
                                                        >
                                                            {{
                                                                getKategori(
                                                                    item.key
                                                                ).kategori
                                                            }}
                                                        </p>

                                                    </div>


                                                    <div
                                                        class="text-right"
                                                    >

                                                        <p
                                                            class="text-[10px] font-bold uppercase opacity-70"
                                                        >
                                                            Skor
                                                        </p>


                                                        <p
                                                            class="mt-1 text-2xl font-extrabold"
                                                        >
                                                            {{
                                                                getKategori(
                                                                    item.key
                                                                ).skor
                                                            }}
                                                        </p>

                                                    </div>

                                                </div>

                                            </div>


                                            <div
                                                v-else
                                                class="flex min-h-[92px] items-center justify-center rounded-xl border border-dashed border-pink-100 bg-pink-50 px-4 text-center text-xs font-medium text-slate-400"
                                            >
                                                Masukkan nilai untuk
                                                melihat kategori
                                                otomatis.
                                            </div>


                                            <div
                                                v-if="
                                                    norma[item.key]
                                                "
                                                class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-5"
                                            >

                                                <div
                                                    v-for="norm in norma[
                                                        item.key
                                                    ]"
                                                    :key="
                                                        norm.skor
                                                    "
                                                    class="rounded-xl border border-pink-100 bg-white p-3"
                                                >

                                                    <div
                                                        class="flex items-center justify-between gap-2"
                                                    >

                                                        <span
                                                            class="text-[10px] font-extrabold text-slate-700"
                                                        >
                                                            {{
                                                                norm.kategori
                                                            }}
                                                        </span>


                                                        <span
                                                            class="rounded-full bg-slate-100 px-2 py-0.5 text-[9px] font-bold text-slate-600"
                                                        >
                                                            {{
                                                                norm.skor
                                                            }}
                                                        </span>

                                                    </div>


                                                    <p
                                                        class="mt-1 text-[10px] leading-4 text-slate-500"
                                                    >
                                                        {{
                                                            norm.range
                                                        }}
                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </template>


                                <!-- CATATAN -->

                                <div class="mt-5">

                                    <label
                                        class="mb-2 block text-[10px] font-extrabold uppercase tracking-wider text-slate-500"
                                    >
                                        Catatan
                                        <span
                                            class="font-normal text-slate-400"
                                        >
                                            (opsional)
                                        </span>
                                    </label>


                                    <textarea
                                        v-model="
                                            form.hasil[
                                                item.key
                                            ].catatan
                                        "
                                        rows="2"
                                        placeholder="Tambahkan catatan jika diperlukan..."
                                        class="w-full resize-none rounded-xl border border-pink-100 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-pink-500 focus:ring-2 focus:ring-pink-100"
                                    ></textarea>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- RINGKASAN -->

                <div
                    v-if="sudahLengkap"
                    class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 p-4"
                >

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"
                    >
                        <CheckCircleIcon
                            class="h-5 w-5"
                        />
                    </div>


                    <div>

                        <p
                            class="text-xs font-extrabold text-emerald-800"
                        >
                            Semua komponen sudah diisi
                        </p>


                        <p
                            class="mt-0.5 text-[10px] font-medium text-emerald-600"
                        >
                            Data siap disimpan.
                        </p>

                    </div>

                </div>


                <!-- BUTTON -->

                <div
                    class="flex flex-col-reverse gap-3 rounded-2xl border border-pink-100 bg-white p-5 shadow-sm sm:flex-row sm:justify-end"
                >

                    <Link
                        :href="
                            route(
                                'tksi.input.index'
                            )
                        "
                        class="rounded-xl border border-pink-100 px-5 py-3 text-center text-xs font-extrabold text-slate-600 transition hover:bg-pink-50"
                    >
                        Batal
                    </Link>


                    <button
                        type="button"
                        @click="submit"
                        :disabled="
                            processing ||
                            !siswa ||
                            !periode
                        "
                        class="rounded-xl bg-pink-700 px-6 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-pink-800 disabled:cursor-not-allowed disabled:opacity-50"
                    >

                        {{
                            processing
                                ? 'Menyimpan...'
                                : isEdit
                                    ? 'Perbarui Hasil TKSI'
                                    : 'Simpan Hasil TKSI'
                        }}

                    </button>

                </div>

            </template>


            <!-- SISWA TIDAK DITEMUKAN -->

            <div
                v-if="periode && !siswa"
                class="rounded-2xl border border-amber-100 bg-amber-50 p-6"
            >

                <p
                    class="text-sm font-extrabold text-amber-700"
                >
                    Siswa tidak ditemukan.
                </p>


                <p
                    class="mt-1 text-xs text-amber-600"
                >
                    Silakan kembali ke daftar peserta TKSI.
                </p>


                <Link
                    :href="
                        route(
                            'tksi.input.index'
                        )
                    "
                    class="mt-4 inline-flex rounded-xl bg-pink-700 px-4 py-2.5 text-xs font-bold text-white"
                >
                    Kembali ke Daftar Siswa
                </Link>

            </div>

        </div>

    </TksiLayout>
</template>