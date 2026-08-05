<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import PendampingLayout from '@/Layouts/PendampingLayout.vue';
import { 
    BoltIcon, 
    UserIcon, 
    CheckCircleIcon,
    ArrowPathIcon
} from '@heroicons/vue/24/outline';

// Mock Class List
const classes = ['10A', 'X AKL 2', 'XI TKJ 1', 'XII RPL 1'];

// Mock Students with Genders
const studentsData = {
    '10A': [
        { id: '1', name: 'Ahmad R.', gender: 'Putera' },
        { id: '2', name: 'Dimas S.', gender: 'Putera' }
    ],
    'X AKL 2': [
        { id: '3', name: 'Aulia Rahma', gender: 'Puteri' },
        { id: '4', name: 'Salma Putri', gender: 'Puteri' }
    ],
    'XI TKJ 1': [
        { id: '5', name: 'Nadia Putri', gender: 'Puteri' },
        { id: '6', name: 'Budi Santoso', gender: 'Putera' }
    ],
    'XII RPL 1': [
        { id: '7', name: 'Putri Anjani', gender: 'Puteri' }
    ]
};

// Form States
const selectedClass = ref('');
const selectedStudentId = ref('');
const selectedGender = ref('');
const attempt1 = ref('');
const attempt2 = ref('');
const successMessage = ref('');

// Previous Test Logs
const previousTests = ref([
    { date: '20 Okt 2024', score: 22, category: 'Baik', badge: 'bg-green-400 text-white' },
    { date: '18 Okt 2024', score: 16, category: 'Sedang', badge: 'bg-yellow-400 text-yellow-800' }
]);

// Computed list of students based on class selection
const studentOptions = computed(() => {
    if (!selectedClass.value) return [];
    return studentsData[selectedClass.value] || [];
});

// Watch class changes to clear student and gender fields
watch(selectedClass, () => {
    selectedStudentId.value = '';
    selectedGender.value = '';
    resetAttempts();
});

// Watch student selection to auto-populate gender
watch(selectedStudentId, (newId) => {
    if (newId) {
        const student = studentOptions.value.find(s => s.id === newId);
        selectedGender.value = student ? student.gender : '';
    } else {
        selectedGender.value = '';
    }
});

// Calculate Best Score
const bestScore = computed(() => {
    const a1 = parseInt(attempt1.value);
    const a2 = parseInt(attempt2.value);
    
    const validA1 = isNaN(a1) ? 0 : a1;
    const validA2 = isNaN(a2) ? 0 : a2;

    if (attempt1.value === '' && attempt2.value === '') return '';
    return Math.max(validA1, validA2);
});

// Calculate Category based on Gender and Best Score
const categoryValue = computed(() => {
    if (bestScore.value === '') return '';
    const score = bestScore.value;
    const gender = selectedGender.value;

    if (gender === 'Putera') {
        if (score >= 22) return 'Baik Sekali';
        if (score >= 16) return 'Baik';
        if (score >= 10) return 'Sedang';
        if (score >= 4) return 'Kurang';
        return 'Kurang Sekali';
    } else if (gender === 'Puteri') {
        if (score >= 15) return 'Baik Sekali';
        if (score >= 10) return 'Baik';
        if (score >= 5) return 'Sedang';
        if (score >= 1) return 'Kurang';
        return 'Kurang Sekali';
    }
    return '';
});

// Get Badge colors for table categories
const getBadgeClass = (categoryName) => {
    if (categoryName === 'Baik Sekali') return 'bg-green-600 text-white';
    if (categoryName === 'Baik') return 'bg-green-400 text-white';
    if (categoryName === 'Sedang') return 'bg-yellow-400 text-yellow-800';
    if (categoryName === 'Kurang') return 'bg-orange-400 text-white';
    return 'bg-red-500 text-white'; // Kurang Sekali
};

const resetAttempts = () => {
    attempt1.value = '';
    attempt2.value = '';
};

const handleSave = () => {
    if (!selectedStudentId.value) {
        alert('Silakan pilih siswa terlebih dahulu.');
        return;
    }
    if (attempt1.value === '' || attempt2.value === '') {
        alert('Mohon isi hasil Percobaan ke-1 dan Percobaan ke-2.');
        return;
    }

    // Add to history log
    previousTests.value.unshift({
        date: 'Hari ini',
        score: bestScore.value,
        category: categoryValue.value,
        badge: getBadgeClass(categoryValue.value)
    });

    successMessage.value = 'Data hasil tes koordinasi mata-tangan berhasil disimpan!';
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Auto-clear notification
    setTimeout(() => {
        successMessage.value = '';
    }, 4500);

    resetAttempts();
    selectedStudentId.value = '';
    selectedClass.value = '';
    selectedGender.value = '';
};
</script>

<template>
    <PendampingLayout>
        <Head title="Tes Koordinasi Mata-Tangan" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">
            
            <!-- Success Alert Notification Banner -->
            <transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="transform opacity-0 -translate-y-2"
                enter-to-class="transform opacity-100 translate-y-0"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="transform opacity-100 translate-y-0"
                leave-to-class="transform opacity-0 -translate-y-2"
            >
                <div v-if="successMessage" class="flex items-center space-x-2.5 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl shadow-sm text-sm font-semibold">
                    <CheckCircleIcon class="w-5 h-5 text-emerald-600 flex-shrink-0" />
                    <span>{{ successMessage }}</span>
                </div>
            </transition>

            <!-- Header Section -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Tes Koordinasi Mata-Tangan</h1>
                <p class="text-sm text-gray-500 font-medium mt-0.5">Hand and Eye Coordination Test</p>
            </div>

            <!-- Student Selection Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 md:p-6 space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Pilih Siswa</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Dropdown Pilih Kelas -->
                    <div class="space-y-1.5">
                        <label for="class-select" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kelas</label>
                        <select 
                            id="class-select" 
                            v-model="selectedClass"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                        >
                            <option value="">-- Pilih Kelas --</option>
                            <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>

                    <!-- Dropdown Pilih Nama Siswa -->
                    <div class="space-y-1.5">
                        <label for="student-select" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Nama Siswa</label>
                        <select 
                            id="student-select" 
                            v-model="selectedStudentId"
                            :disabled="!selectedClass"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <option value="">-- Pilih Nama --</option>
                            <option v-for="s in studentOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <!-- Readonly Gender -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                        <input 
                            type="text" 
                            :value="selectedGender || '-'" 
                            readonly 
                            class="w-full border border-slate-200 bg-slate-50 text-slate-800 font-extrabold rounded-xl px-4 py-2 text-sm cursor-not-allowed" 
                        />
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center space-x-2">
                    <BoltIcon class="w-5 h-5 text-emerald-500 animate-pulse" />
                    <h3 class="text-lg font-bold text-gray-800">Form Input Hasil Tes Koordinasi</h3>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- BARIS 1: Attempt 1, Attempt 2, Best Score -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Percobaan ke-1 (Jumlah Tangkapan) <span class="text-rose-500">*</span></label>
                            <input type="number" min="0" v-model="attempt1" placeholder="0" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Percobaan ke-2 (Jumlah Tangkapan) <span class="text-rose-500">*</span></label>
                            <input type="number" min="0" v-model="attempt2" placeholder="0" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Skor Terbaik</label>
                            <input type="text" :value="bestScore" readonly class="w-full border border-slate-200 bg-slate-50 text-slate-800 font-extrabold rounded-xl px-4 py-2.5 text-sm cursor-not-allowed" placeholder="0" />
                        </div>
                    </div>

                    <!-- BARIS 2: Kategori Norma -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Penilaian</label>
                        <input type="text" :value="categoryValue" readonly class="w-full border border-slate-200 bg-slate-50 text-slate-800 font-extrabold rounded-xl px-4 py-2.5 text-sm cursor-not-allowed" placeholder="Kategori otomatis terhitung" />
                    </div>

                    <!-- Norma Evaluation table -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3">
                        <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider block">Norma Penilaian</span>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                                        <th class="py-2.5 px-4 border-r border-slate-200">Kategori</th>
                                        <th class="py-2.5 px-4 border-r border-slate-200 text-center">Putera</th>
                                        <th class="py-2.5 px-4 text-center">Puteri</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                                    <tr>
                                        <td class="py-2 px-4 border-r border-slate-200 text-emerald-700">Baik Sekali</td>
                                        <td class="py-2 px-4 border-r border-slate-200 text-center">&ge; 22</td>
                                        <td class="py-2 px-4 text-center">&ge; 15</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4 border-r border-slate-200 text-emerald-600">Baik</td>
                                        <td class="py-2 px-4 border-r border-slate-200 text-center">16 - 21</td>
                                        <td class="py-2 px-4 text-center">10 - 14</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4 border-r border-slate-200 text-yellow-600">Sedang</td>
                                        <td class="py-2 px-4 border-r border-slate-200 text-center">10 - 15</td>
                                        <td class="py-2 px-4 text-center">5 - 9</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4 border-r border-slate-200 text-orange-600">Kurang</td>
                                        <td class="py-2 px-4 border-r border-slate-200 text-center">4 - 9</td>
                                        <td class="py-2 px-4 text-center">1 - 4</td>
                                    </tr>
                                    <tr>
                                        <td class="py-2 px-4 border-r border-slate-200 text-rose-600">Kurang Sekali</td>
                                        <td class="py-2 px-4 border-r border-slate-200 text-center">&le; 3</td>
                                        <td class="py-2 px-4 text-center">&le; 0</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="resetAttempts"
                        class="px-5 py-2.5 bg-gray-300 hover:bg-gray-400 text-slate-800 text-xs font-bold rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="handleSave"
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                    >
                        Simpan Hasil Tes
                    </button>
                </div>
            </div>

            <!-- History Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b border-slate-100 pb-2">Riwayat Tes Siswa Sebelumnya</h3>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6 border-r border-slate-100">Tanggal</th>
                                <th class="py-3 px-6 text-center border-r border-slate-100">Skor Terbaik</th>
                                <th class="py-3 px-6 text-center">Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(log, idx) in previousTests" 
                                :key="idx"
                                :class="[
                                    'border-b border-slate-100 text-xs font-medium hover:bg-slate-50/50 transition cursor-pointer',
                                    idx % 2 === 1 ? 'bg-slate-50/20' : 'bg-white'
                                ]"
                            >
                                <td class="py-3.5 px-6 font-bold text-slate-500 border-r border-slate-100">{{ log.date }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-800 font-extrabold border-r border-slate-100">{{ log.score }}</td>
                                <td class="py-3.5 px-6 text-center">
                                    <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider" :class="log.badge">
                                        {{ log.category }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </PendampingLayout>
</template>
