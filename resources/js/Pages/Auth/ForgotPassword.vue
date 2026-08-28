<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Lupa Password - SiKes-Boarding" />

        <div
            class="min-h-screen bg-gradient-to-br from-pink-50 via-pink-100 to-rose-200 flex items-center justify-center p-6 font-sans select-none"
        >
            <!-- LOGIN CARD -->
            <div
                class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border border-pink-100"
            >

                <!-- HEADER -->
                <div class="text-center mb-7">

                    <!-- LOGO -->
                    <div
                        class="w-14 h-14 mx-auto mb-4 bg-pink-600 rounded-xl flex items-center justify-center shadow-md"
                    >
                        <svg
                            class="w-8 h-8 text-white"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                            />
                        </svg>
                    </div>

                    <span
                        class="text-pink-500 text-xs font-semibold uppercase tracking-widest block mb-1"
                    >
                        SIKES BOARDING
                    </span>

                    <h1
                        class="text-2xl font-extrabold text-slate-800 tracking-tight"
                    >
                        LUPA PASSWORD?
                    </h1>

                    <p
                        class="mt-2 text-sm text-slate-500 leading-relaxed"
                    >
                        Jangan khawatir. Masukkan alamat email Anda dan kami
                        akan mengirimkan tautan untuk mengatur ulang password.
                    </p>

                </div>


                <!-- STATUS MESSAGE -->
                <div
                    v-if="status"
                    class="mb-5 text-center text-sm font-semibold text-green-600 bg-green-50 py-3 px-4 rounded-xl border border-green-200"
                >
                    {{ status }}
                </div>


                <!-- FORM -->
                <form
                    @submit.prevent="submit"
                    class="space-y-5"
                >

                    <!-- EMAIL -->
                    <div class="space-y-1">

                        <InputLabel
                            for="email"
                            value="Email"
                            class="text-sm font-semibold text-slate-700"
                        />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full border border-slate-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-slate-800 placeholder-slate-400 transition"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="maria@sekolah.id"
                        />

                        <InputError
                            class="mt-1"
                            :message="form.errors.email"
                        />

                    </div>


                    <!-- BUTTON -->
                    <div>

                        <PrimaryButton
                            :class="[
                                'w-full justify-center bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold py-3 rounded-lg transition-all duration-300 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 active:scale-[0.98] uppercase',
                                { 'opacity-50': form.processing }
                            ]"
                            :disabled="form.processing"
                        >
                            KIRIM LINK RESET PASSWORD
                        </PrimaryButton>

                    </div>

                </form>


                <!-- FOOTER -->
                <div
                    class="mt-6 pt-5 border-t border-pink-100 text-center"
                >
                    <a
                        href="/login"
                        class="text-sm text-pink-600 hover:text-pink-700 hover:underline font-semibold transition"
                    >
                        ← Kembali ke Halaman Login
                    </a>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>