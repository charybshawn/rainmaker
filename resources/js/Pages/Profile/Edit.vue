<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import AdminManagement from './Partials/AdminManagement.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100"
            >
                Profile Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Tab Navigation -->
                <div class="mb-6">
                    <div class="border-b border-gray-200 dark:border-gray-600">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button
                                @click="activeTab = 'profile'"
                                :class="[
                                    activeTab === 'profile'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                    'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                                ]"
                            >
                                📋 Profile Information
                            </button>
                            <button
                                @click="activeTab = 'security'"
                                :class="[
                                    activeTab === 'security'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                    'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                                ]"
                            >
                                🔒 Security
                            </button>
                            <button
                                v-if="canAccessAdmin"
                                @click="activeTab = 'admin'"
                                :class="[
                                    activeTab === 'admin'
                                        ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500',
                                    'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm'
                                ]"
                            >
                                🛡️ Admin Panel
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="space-y-6">
                    <!-- Profile Information Tab -->
                    <div v-show="activeTab === 'profile'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                                class="max-w-xl"
                            />
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div v-show="activeTab === 'security'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8">
                            <UpdatePasswordForm class="max-w-xl" />
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8">
                            <DeleteUserForm class="max-w-xl" />
                        </div>
                    </div>

                    <!-- Admin Panel Tab -->
                    <div v-if="canAccessAdmin" v-show="activeTab === 'admin'" class="space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8">
                            <AdminManagement :admin-stats="adminStats" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
