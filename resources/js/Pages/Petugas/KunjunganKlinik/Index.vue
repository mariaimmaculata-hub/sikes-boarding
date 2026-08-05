<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import PetugasLayout from '@/Layouts/PetugasLayout.vue';
import { 
    MagnifyingGlassIcon, 
    PlusIcon, 
    XMarkIcon,
    CheckCircleIcon,
    ClipboardDocumentCheckIcon
} from '@heroicons/vue/24/outline';

// Mock Student Database
const mockStudents = [
    { nis: '2024001', name: 'Ahmad Fauzi', class: 'X TKJ 1', dorm: 'Asrama Putra' },
    { nis: '123456789', name: 'Ahmad R.', class: '10A', dorm: 'Asrama Putra' },
    { nis: '2024002', name: 'Aulia Rahma', class: 'X AKL 2', dorm: 'Asrama Putri' }
];

// Dropdown Dummy Data
const diseases = ['Demam Ringan', 'ISPA', 'Sakit Kepala TEGANG', 'Diare', 'Migrain', 'Gastroenteritis'];
const medicinesList = ['Paracetamol', 'Bodrex', 'OBH', 'Amoxicillin', 'Oralit', 'Ibuprofen'];
const staffList = ['Fadli G.', 'Rina L.', 'Budi S.'];

// State
const searchQuery = ref('');
const searchedStudent = ref(null);
const searchPerformed = ref(false);
const successMessage = ref('');

// Prescription Basket State
const prescriptionBasket = ref([]);
const selectedMedicine = ref('');
const medicineQty = ref('');

// Form Fields
const form = ref({
    date: new Date().toISOString().substring(0, 10), // Default: Today
    complaints: '',
    examination_result: '',
    diagnosis: '',
    action: '',
    resting_days: '',
    referral: '',
    attending_staff: ''
});

// Search Student
const handleSearch = () => {
    searchPerformed.value = true;
    if (!searchQuery.value) {
        searchedStudent.value = null;
        return;
    }
    const query = searchQuery.value.toLowerCase();
    const found = mockStudents.find(s => s.name.toLowerCase().includes(query) || s.nis.includes(query));
    if (found) {
        searchedStudent.value = found;
    } else {
        searchedStudent.value = null;
    }
};

// Add Medicine to Basket
const addMedicine = () => {
    if (!selectedMedicine.value) {
        alert('Silakan pilih nama obat terlebih dahulu.');
        return;
    }
    const qty = parseInt(medicineQty.value);
    if (isNaN(qty) || qty <= 0) {
        alert('Silakan masukkan jumlah obat yang valid.');
        return;
    }

    // Check duplicate
    const exists = prescriptionBasket.value.find(item => item.name === selectedMedicine.value);
    if (exists) {
        exists.qty += qty;
    } else {
        prescriptionBasket.value.push({
            name: selectedMedicine.value,
            qty: qty
        });
    }

    selectedMedicine.value = '';
    medicineQty.value = '';
};

// Remove Medicine from Basket
const removeMedicine = (idx) => {
    prescriptionBasket.value.splice(idx, 1);
};

// Mock History of Clinical Visits
const previousVisits = ref([
    { date: '20 Okt 2024', complaints: 'Demam', diagnosis: 'Demam Ringan', therapy: 'Kompres', meds: ['Paracetamol (3)'], referral: '-', staff: 'Fadli G.' },
    { date: '18 Okt 2024', complaints: 'Sakit Kepala', diagnosis: 'Sakit Kepala TEGANG', therapy: 'Analgesik', meds: ['Bodrex (2)'], referral: '-', staff: 'Fadli G.' },
    { date: '15 Okt 2024', complaints: 'Batuk', diagnosis: 'ISPA', therapy: 'Obat Batuk', meds: ['OBH (2)'], referral: '-', staff: 'Fadli G.' }
]);

const resetForm = () => {
    form.value = {
        date: new Date().toISOString().substring(0, 10),
        complaints: '',
        examination_result: '',
        diagnosis: '',
        action: '',
        resting_days: '',
        referral: '',
        attending_staff: ''
    };
    prescriptionBasket.value = [];
    searchedStudent.value = null;
    searchQuery.value = '';
    searchPerformed.value = false;
};

const handleSave = () => {
    if (!searchedStudent.value) {
        alert('Silakan cari dan pilih siswa terlebih dahulu.');
        return;
    }
    if (!form.value.complaints || !form.value.diagnosis || !form.value.attending_staff) {
        alert('Mohon lengkapi data Keluhan, Diagnosa, dan Petugas Pemeriksa.');
        return;
    }

    // Format medicines list as array of strings
    const medsArray = prescriptionBasket.value.length > 0 
        ? prescriptionBasket.value.map(item => `${item.name} (${item.qty})`) 
        : ['-'];

    // Add to history
    previousVisits.value.unshift({
        date: form.value.date,
        complaints: form.value.complaints,
        diagnosis: form.value.diagnosis,
        therapy: form.value.action || '-',
        meds: medsArray,
        referral: form.value.referral || '-',
        staff: form.value.attending_staff
    });

    successMessage.value = 'Data kunjungan klinik berhasil dicatat!';
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Auto-clear notification
    setTimeout(() => {
        successMessage.value = '';
    }, 4000);

    resetForm();
};
</script>

<template>
    <PetugasLayout>
        <Head title="Kunjungan Klinik" />

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
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Kunjungan Klinik</h1>
                <p class="text-sm text-gray-500 font-medium mt-0.5">Catat kunjungan siswa yang sakit</p>
            </div>

            <!-- Student Selection Search Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 md:p-6 space-y-4">
                <h3 class="text-sm font-bold text-slate-800">Pilih Siswa</h3>
                
                <div class="flex items-center space-x-2.5 max-w-md">
                    <!-- Search input -->
                    <div class="relative w-full">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                            <MagnifyingGlassIcon class="w-4.5 h-4.5" />
                        </span>
                        <input 
                            type="text" 
                            placeholder="Cari NIS / Nama Siswa" 
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            class="pl-10 pr-4 py-2.5 w-full border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800"
                        />
                    </div>
                    <!-- Search button -->
                    <button 
                        @click="handleSearch"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-md transition text-sm"
                    >
                        Cari
                    </button>
                </div>

                <!-- Search Results panel -->
                <div v-if="searchPerformed" class="mt-4 pt-4 border-t border-slate-100">
                    <div v-if="searchedStudent" class="bg-blue-50/50 border border-blue-100 rounded-2xl p-4 flex items-center space-x-4 max-w-xl text-xs">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 font-extrabold text-base flex items-center justify-center flex-shrink-0">
                            {{ searchedStudent.name.charAt(0) }}
                        </div>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 w-full font-medium">
                            <div>
                                <span class="text-slate-400 font-semibold block">Nama</span>
                                <span class="text-blue-900 font-bold text-sm">{{ searchedStudent.name }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block">NIS</span>
                                <span class="text-slate-800 font-bold">{{ searchedStudent.nis }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block">Kelas</span>
                                <span class="text-slate-800 font-bold">{{ searchedStudent.class }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 font-semibold block">Asrama</span>
                                <span class="text-slate-800 font-bold">{{ searchedStudent.dorm }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-100 px-4 py-3 rounded-2xl w-fit">
                        Siswa tidak ditemukan
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center space-x-2">
                    <ClipboardDocumentCheckIcon class="w-5 h-5 text-green-600" />
                    <h3 class="text-lg font-bold text-gray-800">Form Kunjungan Klinik</h3>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- BARIS 1 (3 kolom): Tanggal Kunjungan, Keluhan Utama, Hasil Pemeriksaan -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tanggal Kunjungan</label>
                            <input type="date" v-model="form.date" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Keluhan Utama <span class="text-rose-500">*</span></label>
                            <textarea v-model="form.complaints" placeholder="Demam tinggi, batuk berbahak" rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Hasil Pemeriksaan</label>
                            <textarea v-model="form.examination_result" placeholder="Suhu 38.5°C, Nadi 90 bpm" rows="3" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                        </div>
                    </div>

                    <!-- BARIS 2 (3 kolom): Diagnosis, Tindakan, Obat yang Diberikan -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Diagnosis <span class="text-rose-500">*</span></label>
                            <select v-model="form.diagnosis" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold">
                                <option value="">-- Pilih Penyakit --</option>
                                <option v-for="d in diseases" :key="d" :value="d">{{ d }}</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tindakan</label>
                            <textarea v-model="form.action" placeholder="Pemberian kompres, istirahat" rows="2" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm"></textarea>
                        </div>
                        <div class="space-y-1.5 bg-slate-50/50 border border-slate-100 rounded-2xl p-4 flex flex-col justify-between">
                            <div>
                                <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-1">Obat yang Diberikan</label>
                                <div class="flex items-center space-x-2">
                                    <select v-model="selectedMedicine" class="w-full border border-slate-300 rounded-xl px-2.5 py-1.5 text-xs text-slate-800 font-medium">
                                        <option value="">-- Pilih Obat --</option>
                                        <option v-for="m in medicinesList" :key="m" :value="m">{{ m }}</option>
                                    </select>
                                    <input type="number" v-model="medicineQty" min="1" placeholder="3" class="w-16 border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-center font-bold" />
                                    <button 
                                        type="button" 
                                        @click="addMedicine"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold p-2 rounded-xl transition flex-shrink-0"
                                        title="Tambah Obat"
                                    >
                                        <PlusIcon class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- Inline Medication Badges inside Column 3 -->
                            <div v-if="prescriptionBasket.length > 0" class="flex flex-wrap gap-1.5 mt-3 pt-2 border-t border-slate-200">
                                <span 
                                    v-for="(item, idx) in prescriptionBasket" 
                                    :key="idx" 
                                    class="inline-flex items-center space-x-1.5 bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-extrabold px-2.5 py-0.5 rounded-lg shadow-sm"
                                >
                                    <span>{{ item.name }} ({{ item.qty }})</span>
                                    <button 
                                        type="button" 
                                        @click="removeMedicine(idx)"
                                        class="text-blue-500 hover:text-rose-600 transition focus:outline-none"
                                        title="Hapus"
                                    >
                                        <XMarkIcon class="w-3.5 h-3.5" />
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- BARIS 3 (2 kolom): Istirahat yang Disarankan, Rujukan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Istirahat yang Disarankan</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" v-model="form.resting_days" min="0" placeholder="2" class="w-24 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-center" />
                                <span class="text-xs font-bold text-slate-600">hari</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Rujukan</label>
                            <input type="text" v-model="form.referral" placeholder="Klinik Sehat Utama" class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm" />
                        </div>
                    </div>

                    <!-- BARIS 4 (1 kolom): Petugas Pemeriksa -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider">Petugas Pemeriksa <span class="text-rose-500">*</span></label>
                        <select v-model="form.attending_staff" class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold">
                            <option value="">-- Pilih Petugas --</option>
                            <option v-for="s in staffList" :key="s" :value="s">{{ s }}</option>
                        </select>
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
                        class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                    >
                        Simpan Kunjungan
                    </button>
                </div>
            </div>

            <!-- History Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b border-slate-100 pb-2">Riwayat Kunjungan Siswa Sebelumnya</h3>
                
                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                <th class="py-3 px-6 border-r border-slate-100">Tanggal</th>
                                <th class="py-3 px-6 border-r border-slate-100">Keluhan Utama</th>
                                <th class="py-3 px-6 border-r border-slate-100">Diagnosis</th>
                                <th class="py-3 px-6 border-r border-slate-100">Tindakan</th>
                                <th class="py-3 px-6 border-r border-slate-100">Obat Diberikan</th>
                                <th class="py-3 px-6 border-r border-slate-100">Rujukan</th>
                                <th class="py-3 px-6">Petugas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr 
                                v-for="(log, idx) in previousVisits" 
                                :key="idx"
                                :class="[
                                    'border-b border-slate-100 text-xs font-medium hover:bg-slate-50/50 transition cursor-pointer',
                                    idx % 2 === 1 ? 'bg-slate-50/20' : 'bg-white'
                                ]"
                            >
                                <td class="py-3.5 px-6 font-bold text-slate-500 border-r border-slate-100">{{ log.date }}</td>
                                <td class="py-3.5 px-6 text-slate-800 font-semibold border-r border-slate-100">{{ log.complaints }}</td>
                                <td class="py-3.5 px-6 border-r border-slate-100 font-extrabold text-blue-900">{{ log.diagnosis }}</td>
                                <td class="py-3.5 px-6 text-slate-600 border-r border-slate-100">{{ log.therapy }}</td>
                                <td class="py-3.5 px-6 border-r border-slate-100">
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="(medBadge, mIdx) in log.meds" 
                                            :key="mIdx"
                                            class="bg-blue-100 text-blue-800 border border-blue-200 text-[10px] font-bold px-2 py-0.5 rounded-md"
                                        >
                                            {{ medBadge }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6 text-slate-500 font-medium border-r border-slate-100">{{ log.referral }}</td>
                                <td class="py-3.5 px-6 text-slate-500 font-semibold">{{ log.staff }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </PetugasLayout>
</template>
