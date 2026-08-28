<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { UserGroupIcon, ShieldCheckIcon } from '@heroicons/vue/24/outline';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    role: '', // 'admin', 'klinik', 'tksi'
    remember: false,
});

onMounted(() => {
    const params = new URLSearchParams(window.location.search);
    const roleParam = params.get('role');
    if (roleParam && ['admin', 'klinik', 'tksi'].includes(roleParam)) {
        form.role = roleParam;
    }
});

const clientErrors = ref({});

const selectRole = (roleName) => {
    form.role = roleName;
    clientErrors.value.role = null;
};

const submit = () => {
    clientErrors.value = {};
    let valid = true;

    if (!form.role) {
        alert('Silakan pilih peran Anda terlebih dahulu.');
        clientErrors.value.role = 'Silakan pilih peran Anda terlebih dahulu.';
        valid = false;
    }
    if (!form.email) {
        clientErrors.value.email = 'Username / Email wajib diisi.';
        valid = false;
    }
    if (!form.password) {
        clientErrors.value.password = 'Password wajib diisi.';
        valid = false;
    }

    if (!valid) return;

    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk ke Sistem - SiKes-Boarding" />

    <div class="min-h-screen bg-gradient-to-br from-pink-50 via-pink-100 to-rose-200 flex items-center justify-center p-6 font-sans select-none relative">
        
        <!-- LOGIN CARD CONTAINER -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border border-pink-100 flex flex-col justify-between">
            
            <div class="text-center mb-6">
                <span class="text-pink-500 text-xs font-semibold uppercase tracking-widest block mb-1">
                    Selamat Datang
                </span>

                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                    MASUK KE SISTEM
                </h1>
            </div>

            <!-- STATUS MESSAGE FROM LARAVEL -->
            <div
                v-if="status"
                class="mb-4 text-center text-sm font-semibold text-green-600 bg-green-50 py-2 px-4 rounded-xl border border-green-200"
            >
                {{ status }}
            </div>

            <!-- FORM START -->
            <form @submit.prevent="submit" class="space-y-5">
                
                <!-- SELECT ROLE AREA -->
                <div class="space-y-2">

                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">
                        PILIH PERAN ANDA:
                    </span>
                    
                    <div class="grid grid-cols-3 gap-3">
                        
                        <!-- Role 1: Petugas Klinik -->
                        <div 
                            @click="selectRole('klinik')"
                            :class="[
                                'flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition text-center select-none bg-gray-50',
                                form.role === 'klinik'
                                    ? 'border-pink-500 bg-pink-50/50 text-pink-950'
                                    : 'border-slate-200 text-slate-600 hover:border-pink-300'
                            ]"
                        >

                            <!-- Custom Stethoscope SVG Icon -->
                            <svg
                                class="w-7 h-7 mb-1"
                                :class="form.role === 'klinik' ? 'text-pink-600' : 'text-slate-400'"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M4.8 2.3A.3.3 0 1 0 5 3a2.5 2.5 0 0 0 4.5-.5.3.3 0 1 0-.6-.2A1.5 1.5 0 0 1 7.4 3.5a1.5 1.5 0 0 1-1.5-1.1Z"/>
                                <path d="M13.2 2.3A.3.3 0 1 1 13 3a2.5 2.5 0 0 1-4.5-.5.3.3 0 1 1 .6-.2A1.5 1.5 0 0 0 10.6 3.5a1.5 1.5 0 0 0 1.5-1.1Z"/>
                                <path d="M9 3v7a5 5 0 0 0 10 0V3"/>
                                <circle cx="14" cy="11" r="1"/>
                                <path d="M14 12v3a3 3 0 0 0 6 0v-3"/>
                                <path d="M17 15v5a2 2 0 0 1-4 0v-2"/>
                                <circle cx="13" cy="18" r="2"/>
                            </svg>

                            <span class="font-semibold text-xs mt-1 block">
                                Petugas Klinik
                            </span>

                        </div>


                        <!-- Role 2: Pendamping -->
                        <div 
                            @click="selectRole('tksi')"
                            :class="[
                                'flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition text-center select-none bg-gray-50',
                                form.role === 'tksi'
                                    ? 'border-pink-500 bg-pink-50/50 text-pink-950'
                                    : 'border-slate-200 text-slate-600 hover:border-pink-300'
                            ]"
                        >

                            <!-- UserGroupIcon -->
                            <UserGroupIcon
                                class="w-7 h-7 mb-1"
                                :class="form.role === 'tksi' ? 'text-pink-600' : 'text-slate-400'"
                            />

                            <span class="font-semibold text-xs mt-1 block">
                                TKSI
                            </span>

                        </div>


                        <!-- Role 3: Admin -->
                        <div 
                            @click="selectRole('admin')"
                            :class="[
                                'flex flex-col items-center justify-center p-3 border-2 rounded-xl cursor-pointer transition text-center select-none bg-gray-50',
                                form.role === 'admin'
                                    ? 'border-pink-500 bg-pink-50/50 text-pink-950'
                                    : 'border-slate-200 text-slate-600 hover:border-pink-300'
                            ]"
                        >

                            <!-- ShieldCheckIcon -->
                            <ShieldCheckIcon
                                class="w-7 h-7 mb-1"
                                :class="form.role === 'admin' ? 'text-pink-600' : 'text-slate-400'"
                            />

                            <span class="font-semibold text-xs mt-1 block">
                                Admin
                            </span>

                        </div>

                    </div>

                    <InputError
                        class="mt-1"
                        :message="clientErrors.role || form.errors.role"
                    />

                </div>


                <!-- USERNAME / EMAIL FIELD -->
                <div class="space-y-1">

                    <label
                        for="email"
                        class="text-sm font-semibold text-slate-700"
                    >
                        Username / Email
                    </label>

                    <input 
                        id="email"
                        type="email"
                        placeholder="maria@sekolah.id"
                        v-model="form.email"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-slate-800 placeholder-slate-400 transition"
                        autocomplete="username"
                    />

                    <!-- Direct validation error below field -->
                    <InputError
                        :message="clientErrors.email || form.errors.email"
                    />

                </div>


                <!-- PASSWORD FIELD -->
                <div class="space-y-1">

                    <label
                        for="password"
                        class="text-sm font-semibold text-slate-700"
                    >
                        Password
                    </label>

                    <input 
                        id="password"
                        type="password"
                        placeholder="Masukkan kata sandi"
                        v-model="form.password"
                        class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-slate-800 placeholder-slate-400 transition"
                        autocomplete="current-password"
                    />

                    <InputError
                        :message="clientErrors.password || form.errors.password"
                    />

                </div>


                <!-- CHECKBOX & FORGOT PASSWORD LINK -->
                <div class="flex items-center justify-between select-none">

                    <label class="flex items-center cursor-pointer">

                        <input 
                            type="checkbox"
                            v-model="form.remember"
                            class="rounded border-slate-300 text-pink-600 shadow-sm focus:ring-pink-500 w-4 h-4 cursor-pointer"
                        />

                        <span class="ml-2 text-sm text-slate-600 font-medium">
                            Ingat Saya
                        </span>

                    </label>


                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-pink-600 hover:text-pink-700 hover:underline font-semibold transition"
                    >
                        Lupa Password?
                    </Link>

                </div>


                <!-- SUBMIT BUTTON -->
                <div>

                    <button 
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold py-3 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 active:scale-[0.98] disabled:opacity-50 uppercase"
                    >
                        MASUK SEKARANG
                    </button>

                </div>

            </form>
            <!-- FORM END -->

        </div>

    </div>
</template>