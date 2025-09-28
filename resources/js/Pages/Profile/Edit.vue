<script setup>
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AdminManagement from './Partials/AdminManagement.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    adminStats: {
        type: Object,
        default: () => ({})
    }
});

const activeTab = ref('profile')

const canAccessAdmin = computed(() => {
    const user = usePage().props.auth?.user
    return user && (user.roles?.some(role => role.name === 'admin') ||
            user.permissions?.some(permission => ['manage users', 'manage roles', 'manage permissions'].includes(permission.name)))
})
</script>

<template>
    <Head title="Profile Settings" />

    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <!-- Header Section -->
        <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
                <!-- Top Navigation Bar -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <Link
                            :href="route('dashboard')"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Back to Dashboard
                        </Link>
                    </div>

                    <!-- User Menu -->
                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 hover:text-white focus:outline-none transition ease-in-out duration-150">
                                    {{ $page.props.auth.user.name }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink
                                    v-if="$page.props.auth.user?.roles?.some(role => role.name === 'admin')"
                                    :href="route('admin.dashboard')"
                                >
                                    Admin Panel
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-white mb-2">⚙️ Profile Settings</h1>
                    <p class="text-gray-300">Manage your account information and preferences</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
            <!-- Success Message -->
            <div v-if="status" class="mb-6 backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent rounded-2xl border border-green-500/30 p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-green-300 font-medium">{{ status }}</span>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="border-b border-white/20 mb-8">
                <nav class="flex space-x-8" aria-label="Profile Tabs">
                    <button
                        @click="activeTab = 'profile'"
                        :class="[
                            activeTab === 'profile'
                                ? 'border-blue-400 text-blue-300'
                                : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
                            'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300'
                        ]"
                    >
                        📋 Profile Information
                    </button>
                    <button
                        @click="activeTab = 'security'"
                        :class="[
                            activeTab === 'security'
                                ? 'border-blue-400 text-blue-300'
                                : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
                            'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300'
                        ]"
                    >
                        🔒 Security
                    </button>
                    <button
                        v-if="canAccessAdmin"
                        @click="activeTab = 'admin'"
                        :class="[
                            activeTab === 'admin'
                                ? 'border-blue-400 text-blue-300'
                                : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
                            'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300'
                        ]"
                    >
                        🛡️ Admin Panel
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="space-y-6">
                <!-- Profile Information Tab -->
                <div v-show="activeTab === 'profile'" class="space-y-6">
                    <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6 sm:p-8 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Security Tab -->
                <div v-show="activeTab === 'security'" class="space-y-6">
                    <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6 sm:p-8 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>

                    <div class="backdrop-blur-3xl bg-gradient-to-br from-red-500/10 via-transparent to-white/5 rounded-2xl border border-red-500/20 p-6 sm:p-8 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Admin Panel Tab -->
                <div v-if="canAccessAdmin" v-show="activeTab === 'admin'" class="space-y-6">
                    <div class="backdrop-blur-3xl bg-gradient-to-br from-blue-500/10 via-transparent to-white/5 rounded-2xl border border-blue-500/20 p-6 sm:p-8 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]">
                        <AdminManagement :admin-stats="adminStats" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
