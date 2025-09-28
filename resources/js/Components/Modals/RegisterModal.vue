<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'switch-to-login']);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        onSuccess: () => {
            emit('close');
        },
    });
};

const closeModal = () => {
    form.reset();
    emit('close');
};

const switchToLogin = () => {
    form.reset();
    emit('switch-to-login');
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-[10000] overflow-y-auto"
        @click="closeModal"
    >
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Background overlay -->
            <div
                class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
                aria-hidden="true"
            ></div>

            <!-- Modal panel -->
            <div
                class="relative bg-black/20 backdrop-blur-3xl rounded-2xl p-6 text-left overflow-hidden shadow-[0_20px_25px_-5px_rgba(0,0,0,0.4),0_10px_10px_-5px_rgba(0,0,0,0.3)] transform transition-all max-w-lg w-full border border-white/20"
                style="backdrop-filter: blur(20px) saturate(180%); background: linear-gradient(135deg, rgba(255,255,255,0.1), rgba(255,255,255,0.05));"
                @click.stop
            >
                <!-- Close button -->
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        type="button"
                        class="bg-white/10 rounded-md p-2 inline-flex items-center justify-center text-white/60 hover:text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-green-500 transition-all duration-200"
                        @click="closeModal"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="w-full">
                    <div class="text-center w-full">
                        <div class="flex flex-col items-center mb-6">
                            <div class="mb-4">
                                <img
                                    src="https://rainmaker.test/images/rainmaker-logo.png"
                                    alt="Rainmaker Logo"
                                    class="h-16 w-auto drop-shadow-sm"
                                    style="filter: brightness(1.2) contrast(1.2) saturate(1.2);"
                                />
                            </div>
                            <h3 class="text-2xl font-bold leading-6 text-white">
                                Join Rainmaker
                            </h3>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Name" />
                                <TextInput
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <div>
                                <InputLabel for="password" value="Password" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div>
                                <InputLabel for="password_confirmation" value="Confirm Password" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <div class="flex items-center justify-center">
                                <button
                                    type="button"
                                    @click="switchToLogin"
                                    class="text-sm text-white/60 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                                >
                                    Already have an account?
                                </button>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <button
                                    type="button"
                                    @click="closeModal"
                                    class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-white/5 via-white/10 to-white/5 text-gray-300 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)] border border-white/10 backdrop-blur-xl font-medium"
                                    style="backdrop-filter: blur(20px) saturate(150%);"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium"
                                    style="backdrop-filter: blur(20px) saturate(150%);"
                                >
                                    Create Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>