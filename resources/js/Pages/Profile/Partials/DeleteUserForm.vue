<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => {
        passwordInput.value?.focus();
    });
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section>
        <p class="text-sm leading-6 text-slate-600">
            Setelah akun dihapus, seluruh data dan akses akun akan
            dihapus secara permanen. Pastikan Anda benar-benar ingin
            melakukan tindakan ini.
        </p>

        <div class="mt-5">
            <button
                type="button"
                @click="confirmUserDeletion"
                class="rounded-xl bg-pink-600 px-6 py-3 text-sm font-bold text-white shadow-md transition hover:bg-pink-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
            >
                HAPUS AKUN
            </button>
        </div>

        <Modal
            :show="confirmingUserDeletion"
            @close="closeModal"
        >
            <div class="p-6">

                <div
                    class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-pink-100 text-pink-600"
                >
                    <svg
                        class="h-7 w-7"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 7h12M9 7V4h6v3m-8 0l1 13h6l1-13"
                        />
                    </svg>
                </div>

                <h2
                    class="mt-4 text-center text-lg font-bold text-slate-800"
                >
                    Hapus Akun?
                </h2>

                <p class="mt-2 text-center text-sm text-slate-500">
                    Tindakan ini tidak dapat dibatalkan. Masukkan password
                    untuk mengonfirmasi penghapusan akun.
                </p>

                <div class="mt-6">
                    <InputLabel
                        for="delete-password"
                        value="Password"
                        class="font-semibold text-slate-700"
                    />

                    <TextInput
                        id="delete-password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-2 block w-full rounded-xl border-pink-200 focus:border-pink-500 focus:ring-pink-500"
                        placeholder="Masukkan password"
                        @keyup.enter="deleteUser"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password"
                    />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton
                        type="button"
                        @click="closeModal"
                    >
                        Batal
                    </SecondaryButton>

                    <DangerButton
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="bg-pink-600 hover:bg-pink-700 focus:ring-pink-500"
                    >
                        Hapus Akun
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>