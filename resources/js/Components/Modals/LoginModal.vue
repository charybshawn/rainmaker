<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    canResetPassword: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close', 'switch-to-register']);

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            emit('close');
        },
    });
};

const closeModal = () => {
    form.reset();
    emit('close');
};

const switchToRegister = () => {
    form.reset();
    emit('switch-to-register');
};
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="closeModal"
    >
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div
                class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity"
                aria-hidden="true"
            ></div>

            <!-- Modal panel -->
            <div
                class="relative inline-block align-bottom bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-[0_20px_25px_-5px_rgba(0,0,0,0.1),0_10px_10px_-5px_rgba(0,0,0,0.04)] dark:shadow-[0_20px_25px_-5px_rgba(0,0,0,0.25),0_10px_10px_-5px_rgba(0,0,0,0.1)] transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 border border-white/20 dark:border-white/10"
                style="backdrop-filter: blur(20px) saturate(150%);"
                @click.stop
            >
                <!-- Close button -->
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button
                        type="button"
                        class="bg-white/10 dark:bg-white/5 rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 hover:bg-white/20 dark:hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-all duration-200"
                        @click="closeModal"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal content -->
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                        <div class="flex flex-col items-center mb-6">
                            <div class="mb-4">
                                <img
                                    src="https://rainmaker.test/images/rainmaker-logo.png"
                                    alt="Rainmaker Logo"
                                    class="h-16 w-auto drop-shadow-sm"
                                    style="filter: brightness(1.2) contrast(1.2) saturate(1.2);"
                                />
                            </div>
                            <h3 class="text-2xl font-bold leading-6 text-gray-900 dark:text-white">
                                Sign In to Rainmaker
                            </h3>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="email" value="Email" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autofocus
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
                                    autocomplete="current-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <Checkbox name="remember" v-model:checked="form.remember" />
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-between">
                                <button
                                    v-if="canResetPassword"
                                    type="button"
                                    class="text-sm text-gray-600 dark:text-gray-300 underline hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                                >
                                    Forgot your password?
                                </button>

                                <button
                                    type="button"
                                    @click="switchToRegister"
                                    class="text-sm text-gray-600 dark:text-gray-300 underline hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                                >
                                    Need an account?
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
                                    class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 backdrop-blur-xl font-medium"
                                    style="backdrop-filter: blur(20px) saturate(150%);"
                                >
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>