<script setup>
import { ref, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { 
    CalendarDaysIcon, 
    UserIcon, 
    HeartIcon, 
    CheckCircleIcon,
    PencilIcon,
    TrashIcon
} from '@heroicons/vue/24/outline';

// Mock Classes
const classes = ['10A', 'X AKL 2', 'XI TKJ 1', 'XII RPL 1'];

// Mock Students by Class
const studentsData = {
    '10A': [
        { id: '1', name: 'Ahmad R.' },
        { id: '2', name: 'Dimas S.' }
    ],
    'X AKL 2': [
        { id: '3', name: 'Aulia Rahma' },
        { id: '4', name: 'Salma Putri' }
    ],
    'XI TKJ 1': [
        { id: '5', name: 'Nadia Putri' },
        { id: '6', name: 'Budi Santoso' }
    ],
    'XII RPL 1': [
        { id: '7', name: 'Putri Anjani' }
    ]
};

// Mock History of Last Checkups (to auto-fill if available)
const lastCheckupHistory = {
    '1': { weight: 62, height: 168, systole: 120, diastole: 80, temp: 36.5, pulse: 72, oxygen: 98, complaints: 'Sering pusing ringan saat belajar sore', history: 'Tidak Ada', notes: 'Kondisi umum normal, disarankan minum air putih cukup.' },
    '3': { weight: 52, height: 162, systole: 110, diastole: 70, temp: 36.4, pulse: 78, oxygen: 99, complaints: 'Mudah lelah', history: 'Anemia', notes: 'Diberikan tablet penambah darah.' }
};

// Mock Previous Checkups List
const previousCheckups = ref([
    { date: '15 Okt 2024', name: 'Ahmad R.', class: '10A', weight: 62, height: 168, bp: '120/80', imt: 21.9, temp: 36.5, pulse: 72, oxygen: 98, complaints: 'Sering pusing ringan saat belajar sore', history: 'Tidak Ada', notes: 'Kondisi umum normal.', staff: 'Fadli G.' },
    { date: '12 Jul 2024', name: 'Ahmad R.', class: '10A', weight: 60, height: 167, bp: '115/75', imt: 21.5, temp: 36.4, pulse: 70, oxygen: 99, complaints: 'Tidak ada', history: 'Tidak Ada', notes: 'Normal.', staff: 'Fadli G.' },
    { date: '18 Okt 2024', name: 'Aulia Rahma', class: 'X AKL 2', weight: 52, height: 162, bp: '110/70', imt: 19.8, temp: 36.4, pulse: 78, oxygen: 99, complaints: 'Mudah lelah', history: 'Anemia', notes: 'Diberikan tablet penambah darah.', staff: 'Fadli G.' }
]);

// Selection States
const selectedClass = ref('');
const selectedStudentId = ref('');
const successMessage = ref('');
const isEditing = ref(false);
const editingIdx = ref(-1);

// Form state
const form = ref({
    weight: '',
    height: '',
    systole: '',
    diastole: '',
    temp: '',
    pulse: '',
    oxygen: '',
    complaints: '',
    history: '',
    notes: ''
});

// Dropdown names computed based on class
const studentOptions = computed(() => {
    if (!selectedClass.value) return [];
    return studentsData[selectedClass.value] || [];
});

// Watch class change to reset selected student
watch(selectedClass, () => {
    if (isEditing.value) return; // Do not override during editing!
    selectedStudentId.value = '';
    resetForm();
});

// Watch selected student to pre-fill form
watch(selectedStudentId, (newId) => {
    if (isEditing.value) return; // Do not override during editing!
    if (newId && lastCheckupHistory[newId]) {
        const history = lastCheckupHistory[newId];
        form.value.weight = history.weight;
        form.value.height = history.height;
        form.value.systole = history.systole;
        form.value.diastole = history.diastole;
        form.value.temp = history.temp;
        form.value.pulse = history.pulse;
        form.value.oxygen = history.oxygen;
        form.value.complaints = history.complaints;
        form.value.history = history.history;
        form.value.notes = history.notes;
    } else {
        resetForm();
    }
});

// Calculate IMT
const imtValue = computed(() => {
    const w = parseFloat(form.value.weight);
    const h = parseFloat(form.value.height);
    if (!w || !h) return '';
    const hMeters = h / 100;
    return (w / (hMeters * hMeters)).toFixed(1);
});

const resetForm = () => {
    form.value = {
        weight: '',
        height: '',
        systole: '',
        diastole: '',
        temp: '',
        pulse: '',
        oxygen: '',
        complaints: '',
        history: '',
        notes: ''
    };
    if (isEditing.value) {
        isEditing.value = false;
        editingIdx.value = -1;
        selectedStudentId.value = '';
        selectedClass.value = '';
    }
};

const handleSave = () => {
    if (!selectedStudentId.value) {
        alert('Silakan pilih nama siswa terlebih dahulu.');
        return;
    }
    if (!form.value.weight || !form.value.height || !form.value.systole || !form.value.diastole) {
        alert('Mohon lengkapi berat badan, tinggi badan, dan tekanan darah.');
        return;
    }

    const studentObj = studentOptions.value.find(s => s.id === selectedStudentId.value);
    
    if (isEditing.value && editingIdx.value !== -1) {
        // Update existing row
        previousCheckups.value[editingIdx.value] = {
            date: previousCheckups.value[editingIdx.value].date,
            name: studentObj ? studentObj.name : 'Siswa',
            class: selectedClass.value,
            weight: form.value.weight,
            height: form.value.height,
            bp: `${form.value.systole}/${form.value.diastole}`,
            imt: parseFloat(imtValue.value),
            temp: form.value.temp,
            pulse: form.value.pulse,
            oxygen: form.value.oxygen,
            complaints: form.value.complaints,
            history: form.value.history,
            notes: form.value.notes,
            staff: 'Rina Lestari'
        };
        successMessage.value = 'Data pemeriksaan berkala berhasil diperbarui!';
    } else {
        // Add to history
        previousCheckups.value.unshift({
            date: 'Hari ini',
            name: studentObj ? studentObj.name : 'Siswa',
            class: selectedClass.value,
            weight: form.value.weight,
            height: form.value.height,
            bp: `${form.value.systole}/${form.value.diastole}`,
            imt: parseFloat(imtValue.value),
            temp: form.value.temp,
            pulse: form.value.pulse,
            oxygen: form.value.oxygen,
            complaints: form.value.complaints,
            history: form.value.history,
            notes: form.value.notes,
            staff: 'Rina Lestari'
        });
        successMessage.value = 'Data pemeriksaan berkala berhasil disimpan!';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Auto-clear notification after 4 seconds
    setTimeout(() => {
        successMessage.value = '';
    }, 4000);

    resetForm();
};

// Start editing action
const startEdit = (log, idx) => {
    isEditing.value = true;
    editingIdx.value = idx;
    
    selectedClass.value = log.class;
    
    // Defer setting selectedStudentId to allow dropdown to load
    setTimeout(() => {
        const students = studentsData[log.class] || [];
        const match = students.find(s => s.name === log.name);
        if (match) {
            selectedStudentId.value = match.id;
        }

        form.value.weight = log.weight;
        form.value.height = log.height;
        const bpParts = log.bp.split('/');
        form.value.systole = bpParts[0];
        form.value.diastole = bpParts[1];
        form.value.temp = log.temp || '36.5';
        form.value.pulse = log.pulse || '72';
        form.value.oxygen = log.oxygen || '98';
        form.value.complaints = log.complaints || '';
        form.value.history = log.history || '';
        form.value.notes = log.notes || '';
    }, 100);
};

// Delete record action
const deleteCheckup = (idx) => {
    if (confirm('Apakah Anda yakin ingin menghapus data pemeriksaan ini?')) {
        previousCheckups.value.splice(idx, 1);
    }
};
</script>

<template>
    <PetugasLayout>
        <Head title="Pemeriksaan Berkala" />

        <div class="space-y-6 max-w-7xl mx-auto p-4 md:p-6 bg-slate-50 min-h-screen">
            
            <!-- Success Notification Alert -->
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
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Pemeriksaan Berkala</h1>
                <p class="text-sm text-gray-500 font-medium mt-0.5">Input data pemeriksaan kesehatan 3 bulanan</p>
            </div>

            <!-- Student Selection Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 md:p-6 space-y-4">
                <h3 class="text-sm font-extrabold text-slate-800 tracking-tight uppercase tracking-wider">Pilih Siswa</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Dropdown Pilih Kelas -->
                    <div class="space-y-1.5">
                        <label for="select-class" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Kelas</label>
                        <select 
                            id="select-class" 
                            v-model="selectedClass"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                        >
                            <option value="">-- Pilih Kelas --</option>
                            <option v-for="c in classes" :key="c" :value="c">{{ c }}</option>
                        </select>
                    </div>

                    <!-- Dropdown Pilih Nama Siswa -->
                    <div class="space-y-1.5">
                        <label for="select-student" class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pilih Nama Siswa</label>
                        <select 
                            id="select-student" 
                            v-model="selectedStudentId"
                            :disabled="!selectedClass"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <option value="">-- Pilih Nama --</option>
                            <option v-for="s in studentOptions" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center space-x-2">
                    <HeartIcon class="w-5 h-5 text-rose-500" />
                    <h3 class="text-lg font-bold text-gray-800">Form Pemeriksaan {{ isEditing ? '(Edit Mode)' : '' }}</h3>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- BARIS 1: BB, TB, IMT -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Berat Badan (kg) <span class="text-rose-500">*</span></label>
                            <input type="number" v-model="form.weight" placeholder="62" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tinggi Badan (cm) <span class="text-rose-500">*</span></label>
                            <input type="number" v-model="form.height" placeholder="168" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">IMT (Indeks Massa Tubuh)</label>
                            <input type="text" :value="imtValue" readonly class="w-full border border-slate-200 bg-slate-50 text-slate-800 font-extrabold rounded-xl px-4 py-2.5 text-sm cursor-not-allowed" placeholder="22.5" />
                        </div>
                    </div>

                    <!-- BARIS 2: Tensi Darah (Sistole, Diastole), Suhu, Nadi -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tekanan Darah (Sistole / Diastole) <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" v-model="form.systole" placeholder="120" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-center" />
                                <input type="number" v-model="form.diastole" placeholder="80" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-center" />
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Suhu Tubuh (°C)</label>
                            <input type="number" step="0.1" v-model="form.temp" placeholder="36.5" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Nadi (x/menit)</label>
                            <input type="number" v-model="form.pulse" placeholder="72" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                    </div>

                    <!-- BARIS 3: Saturasi, Keluhan, Riwayat Penyakit -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Saturasi Oksigen (%)</label>
                            <input type="number" v-model="form.oxygen" placeholder="98" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Keluhan Kesehatan</label>
                            <textarea v-model="form.complaints" placeholder="Tulis keluhan kesehatan..." rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Riwayat Penyakit</label>
                            <textarea v-model="form.history" placeholder="Tulis riwayat penyakit..." rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                        </div>
                    </div>

                    <!-- BARIS 4: Catatan Petugas -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Catatan Petugas</label>
                        <textarea v-model="form.notes" placeholder="Tambahkan catatan pemeriksaan..." rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                    </div>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="resetForm"
                        class="px-5 py-2.5 bg-gray-300 hover:bg-gray-400 text-slate-800 text-xs font-bold rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button 
                        type="button" 
                        @click="handleSave"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                    >
                        {{ isEditing ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </div>

            <!-- History Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b border-slate-100 pb-2">Riwayat Pemeriksaan Sebelumnya</h3>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6 border-r border-slate-100">Tanggal</th>
                                <th class="py-3 px-6 border-r border-slate-100">Nama Siswa</th>
                                <th class="py-3 px-6 border-r border-slate-100">Kelas</th>
                                <th class="py-3 px-6 text-center border-r border-slate-100">BB (kg)</th>
                                <th class="py-3 px-6 text-center border-r border-slate-100">TB (cm)</th>
                                <th class="py-3 px-6 border-r border-slate-100">Tekanan Darah</th>
                                <th class="py-3 px-6 text-center border-r border-slate-100">IMT</th>
                                <th class="py-3 px-6 border-r border-slate-100">Petugas</th>
                                <th class="py-3 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(log, idx) in previousCheckups" 
                                :key="idx"
                                :class="[
                                    'border-b border-slate-100 text-xs font-medium hover:bg-slate-50/50 transition cursor-pointer',
                                    idx % 2 === 1 ? 'bg-slate-50/20' : 'bg-white'
                                ]"
                            >
                                <td class="py-3.5 px-6 font-bold text-slate-500 border-r border-slate-100">{{ log.date }}</td>
                                <td class="py-3.5 px-6 font-extrabold text-blue-900 border-r border-slate-100">{{ log.name }}</td>
                                <td class="py-3.5 px-6 font-semibold text-slate-600 border-r border-slate-100">{{ log.class }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-800 font-bold border-r border-slate-100">{{ log.weight }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-800 font-bold border-r border-slate-100">{{ log.height }}</td>
                                <td class="py-3.5 px-6 font-bold text-slate-700 border-r border-slate-100">{{ log.bp }}</td>
                                <td class="py-3.5 px-6 text-center text-slate-800 font-extrabold border-r border-slate-100">{{ log.imt }}</td>
                                <td class="py-3.5 px-6 text-slate-500 font-semibold border-r border-slate-100">{{ log.staff }}</td>
                                <td class="py-3.5 px-6 text-right space-x-2">
                                    <button @click="startEdit(log, idx)" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg inline-flex" title="Edit">
                                        <PencilIcon class="w-4 h-4" />
                                    </button>
                                    <button @click="deleteCheckup(idx)" class="text-rose-600 hover:bg-rose-50 p-1.5 rounded-lg inline-flex" title="Hapus">
                                        <TrashIcon class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </PetugasLayout>
</template>
