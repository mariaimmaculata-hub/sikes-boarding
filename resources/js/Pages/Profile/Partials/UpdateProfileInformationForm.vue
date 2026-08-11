<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">

        <!-- NAMA -->
        <div>
            <InputLabel
                for="name"
                value="Nama Lengkap"
                class="font-semibold text-slate-700"
            />

            <TextInput
                id="name"
                type="text"
                class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-slate-800 focus:border-blue-500 focus:ring-blue-500"
                v-model="form.name"
                required
                autocomplete="name"
            />

            <InputError
                class="mt-2"
                :message="form.errors.name"
            />
        </div>

        <!-- EMAIL -->
        <div>
            <InputLabel
                for="email"
                value="Alamat Email"
                class="font-semibold text-slate-700"
            />

            <TextInput
                id="email"
                type="email"
                class="mt-2 block w-full rounded-xl border-slate-300 px-4 py-3 text-slate-800 focus:border-blue-500 focus:ring-blue-500"
                v-model="form.email"
                required
                autocomplete="username"
            />

            <InputError
                class="mt-2"
                :message="form.errors.email"
            />

            <!-- VERIFIKASI -->
            <div
                v-if="mustVerifyEmail && user.email_verified_at === null"
                class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4"
            >
                <p class="text-sm text-amber-800">
                    Alamat email Anda belum diverifikasi.
                </p>

                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="mt-2 text-sm font-semibold text-blue-700 underline hover:text-blue-900"
                >
                    Kirim ulang email verifikasi
                </Link>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-semibold text-green-700"
                >
                    Link verifikasi baru telah dikirim ke email Anda.
                </div>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="flex items-center gap-4">
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:from-blue-700 hover:to-blue-800 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                SIMPAN PERUBAHAN
            </button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm font-semibold text-green-600"
                >
                    Tersimpan.
                </p>
            </Transition>
        </div>
    </form>
</template>