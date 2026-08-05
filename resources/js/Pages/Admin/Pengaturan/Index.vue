<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Cog6ToothIcon, PhotoIcon } from '@heroicons/vue/24/outline';

// Configurations State
const layout = ref('gradient-bg');
const gradientFrom = ref('#1e365d'); // Default dark blue
const gradientTo = ref('#2563eb');   // Default medium blue
const filePreview = ref(null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        filePreview.value = URL.createObjectURL(file);
    }
};

const saveSettings = () => {
    alert('Pengaturan tampilan landing page berhasil disimpan!');
};
</script>

<template>
    <AdminLayout>
        <Head title="Pengaturan Tampilan" />

        <div class="space-y-6">
            <!-- Header Section -->
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Landing Page</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium">Kustomisasi tata letak, warna gradien, dan gambar latar belakang halaman depan.</p>
            </div>

            <!-- Settings Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                
                <!-- Settings Form -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-5">
                    <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2 flex items-center space-x-2">
                        <Cog6ToothIcon class="w-5 h-5 text-blue-600" />
                        <span>Kustomisasi Elemen</span>
                    </h3>

                    <!-- Layout Selector -->
                    <div class="space-y-1.5">
                        <label for="layout" class="text-xs font-bold text-slate-700 uppercase tracking-wider">Tata Letak Tampilan</label>
                        <select 
                            id="layout"
                            v-model="layout"
                            class="w-full border border-slate-300 rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 font-semibold"
                        >
                            <option value="gradient-bg">Hanya Latar Belakang Gradien</option>
                            <option value="image-bg">Latar Belakang Gambar Overlay</option>
                        </select>
                    </div>

                    <!-- Color Pickers -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Gradien Mulai</label>
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="color" 
                                    v-model="gradientFrom" 
                                    class="w-10 h-10 border border-slate-200 rounded-xl cursor-pointer p-1"
                                />
                                <span class="text-xs font-bold text-slate-600">{{ gradientFrom }}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Gradien Akhir</label>
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="color" 
                                    v-model="gradientTo" 
                                    class="w-10 h-10 border border-slate-200 rounded-xl cursor-pointer p-1"
                                />
                                <span class="text-xs font-bold text-slate-600">{{ gradientTo }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-wider block">Unggah Gambar Latar (Opsional)</label>
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-2xl p-4 cursor-pointer hover:bg-slate-50 hover:border-slate-300 transition">
                            <PhotoIcon class="w-8 h-8 text-slate-400 mb-1.5" />
                            <span class="text-[10px] font-bold text-slate-500">KLIK UNTUK UNGGAH GAMBAR</span>
                            <span class="text-[9px] text-slate-400 font-medium mt-0.5">JPG, PNG up to 2MB</span>
                            <input type="file" accept="image/*" @change="onFileChange" class="hidden" />
                        </label>
                    </div>

                    <!-- Save Button -->
                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button 
                            @click="saveSettings"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition"
                        >
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>

                <!-- Live Preview Card -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6 md:p-8 space-y-4">
                    <h3 class="text-sm font-extrabold text-slate-800 border-b border-slate-100 pb-2">
                        Pratinjau Langsung (Live Preview)
                    </h3>
                    
                    <!-- Live Preview Window -->
                    <div 
                        class="w-full h-72 rounded-2xl shadow-inner relative overflow-hidden flex flex-col justify-between p-4 text-white"
                        :style="{ background: `linear-gradient(135deg, ${gradientFrom}, ${gradientTo})` }"
                    >
                        <!-- Image background preview if loaded and layout is image-bg -->
                        <img 
                            v-if="layout === 'image-bg' && filePreview"
                            :src="filePreview" 
                            class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-30"
                        />

                        <!-- Mock Header -->
                        <div class="flex justify-between items-center relative z-10">
                            <span class="text-xs font-bold font-sans">SMKN Jateng</span>
                            <span class="text-[9px] border border-white/50 px-2 py-0.5 rounded-lg">Login</span>
                        </div>

                        <!-- Mock Hero Content -->
                        <div class="text-center space-y-1 relative z-10 my-auto">
                            <h4 class="text-sm font-black tracking-tight leading-tight px-4">Sistem Informasi Kesehatan &amp; Kebugaran</h4>
                            <p class="text-[9px] text-white/80 font-medium">Pantau kesehatan siswa secara terintegrasi.</p>
                        </div>

                        <!-- Mock Cards area -->
                        <div class="grid grid-cols-2 gap-2 relative z-10">
                            <div class="bg-white rounded-lg p-2 text-slate-800 text-[8px] font-bold text-center">
                                Klinik Kesehatan
                            </div>
                            <div class="bg-white rounded-lg p-2 text-slate-800 text-[8px] font-bold text-center">
                                Tes Kebugaran
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
