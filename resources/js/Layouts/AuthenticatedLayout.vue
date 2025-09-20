<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';

const showingNavigationDropdown = ref(false);
const { isDarkMode, toggleDarkMode, initializeDarkMode } = useDarkMode();

onMounted(() => {
  initializeDarkMode();
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
            <!-- Floating Navigation Bar with Glassmorphism -->
            <nav class="relative z-50">
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-4">
                    <div class="backdrop-blur-3xl bg-gradient-to-r from-white/5 via-white/8 to-white/5 rounded-2xl border border-white/10 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] p-4" style="backdrop-filter: blur(20px) saturate(180%);">
                        <div class="flex h-12 justify-between items-center">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="group">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-white group-hover:text-blue-200 transition-colors duration-300"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-4 sm:ms-8 sm:flex items-center"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('blog.index')"
                                    :active="route().current('blog.*')"
                                >
                                    My Blog
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center space-x-4">
                            <!-- Dark Mode Toggle -->
                            <button
                                @click="toggleDarkMode"
                                class="p-3 rounded-full bg-white/10 hover:bg-white/20 backdrop-blur-xl border border-white/10 transition-all duration-300 hover:scale-105 shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)]"
                                style="backdrop-filter: blur(20px) saturate(180%);"
                                :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
                            >
                                <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-400 drop-shadow-[0_0_3px_rgba(251,191,36,0.6)]" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                                </svg>
                                <svg v-else class="w-5 h-5 text-gray-300 hover:text-purple-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                            </button>
                            
                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-full">
                                            <button
                                                type="button"
                                                class="group relative inline-flex items-center px-4 py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-sm bg-white/10 hover:bg-white/20 backdrop-blur-xl border border-white/10 text-white hover:text-blue-200 hover:scale-105 shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] focus:outline-none"
                                                style="backdrop-filter: blur(20px) saturate(180%);"
                                            >
                                                <!-- User Avatar -->
                                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-full flex items-center justify-center mr-3 shadow-[0_0_5px_rgba(59,130,246,0.3)]">
                                                    <span class="text-white font-semibold text-sm">{{ $page.props.auth.user.name.charAt(0).toUpperCase() }}</span>
                                                </div>

                                                <span class="font-semibold tracking-wide">{{ $page.props.auth.user.name }}</span>

                                                <svg
                                                    class="ml-2 h-4 w-4 transition-transform duration-200 group-hover:rotate-180"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-full p-3 bg-white/10 hover:bg-white/20 backdrop-blur-xl border border-white/10 text-white hover:text-blue-200 transition-all duration-300 hover:scale-105 shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] focus:outline-none"
                                style="backdrop-filter: blur(20px) saturate(180%);"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('blog.index')"
                            :active="route().current('blog.*')"
                        >
                            My Blog
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 dark:border-gray-600 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800 dark:text-gray-100"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="mt-4"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="backdrop-blur-3xl bg-gradient-to-r from-white/5 via-white/8 to-white/5 rounded-2xl border border-white/10 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] p-6" style="backdrop-filter: blur(20px) saturate(180%);">
                        <slot name="header" />
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
