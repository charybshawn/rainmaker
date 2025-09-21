<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';
import { onMounted } from 'vue';

const { isDarkMode, toggleDarkMode, initializeDarkMode } = useDarkMode();

onMounted(() => {
  initializeDarkMode();
});
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-gray-100 dark:bg-gray-900 pt-6 sm:justify-center sm:pt-0 transition-colors duration-200"
    >
        <!-- Dark Mode Toggle -->
        <div class="absolute top-4 right-4">
            <button
                @click="toggleDarkMode"
                class="group relative p-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)] border border-white/10 backdrop-blur-xl"
                style="backdrop-filter: blur(20px) saturate(150%);"
                :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-purple-200" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </button>
        </div>

        <div>
            <Link href="/">
                <ApplicationLogo class="h-20 w-20 fill-current text-gray-500 dark:text-gray-400" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-gradient-to-br from-white/10 via-white/5 to-white/10 dark:from-white/5 dark:via-transparent dark:to-white/5 backdrop-blur-xl px-6 py-8 shadow-[0_8px_32px_0_rgba(31,38,135,0.37)] border border-white/20 dark:border-white/10 sm:max-w-md sm:rounded-3xl transition-all duration-200"
            style="backdrop-filter: blur(20px) saturate(180%);"
        >
            <slot />
        </div>
    </div>
</template>
