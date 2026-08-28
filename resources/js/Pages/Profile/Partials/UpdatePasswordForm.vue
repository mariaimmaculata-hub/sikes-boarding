<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,

        onSuccess: () => {
            form.reset();
        },

        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
            }

            if (form.errors.current_password) {
                form.reset('current_password');
            }
        },
    });
};
</script>

<template>
    <form @submit.prevent="updatePassword" class="space-y-6">

        <!-- PASSWORD LAMA -->
        <div>
            <InputLabel
                for="current_password"
                value="Password Saat Ini"
                class="font-semibold text-slate-700"
            />

            <input
                id="current_password"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
                required
                class="mt-2 block w-full rounded-xl border-pink-200 px-4 py-3 text-slate-800 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            />

            <InputError
                class="mt-2"
                :message="form.errors.current_password"
            />
        </div>

        <!-- PASSWORD BARU -->
        <div>
            <InputLabel
                for="password"
                value="Password Baru"
                class="font-semibold text-slate-700"
            />

            <input
                id="password"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                required
                class="mt-2 block w-full rounded-xl border-pink-200 px-4 py-3 text-slate-800 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            />

            <InputError
                class="mt-2"
                :message="form.errors.password"
            />
        </div>

        <!-- KONFIRMASI -->
        <div>
            <InputLabel
                for="password_confirmation"
                value="Konfirmasi Password Baru"
                class="font-semibold text-slate-700"
            />

            <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="mt-2 block w-full rounded-xl border-pink-200 px-4 py-3 text-slate-800 shadow-sm focus:border-pink-500 focus:ring-pink-500"
            />

            <InputError
                class="mt-2"
                :message="form.errors.password_confirmation"
            />
        </div>

        <!-- BUTTON -->
        <div class="flex items-center gap-4">
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-pink-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >
                PERBARUI PASSWORD
            </button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm font-semibold text-pink-600"
                >
                    Password berhasil diperbarui.
                </p>
            </Transition>
        </div>
    </form>
</template>