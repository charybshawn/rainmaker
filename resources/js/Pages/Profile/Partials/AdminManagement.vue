<template>
  <section>
    <header class="mb-6">
      <h2 class="text-lg font-medium text-white">
        🛡️ Admin Management
      </h2>
      <p class="mt-1 text-sm text-gray-300">
        Manage users, roles, and permissions for the system.
      </p>
    </header>

    <!-- Admin Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
      <!-- Users Card -->
      <div class="backdrop-blur-3xl bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent rounded-xl border border-white/10 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm font-medium">Total Users</p>
            <p class="text-2xl font-bold text-white mt-1">{{ adminStats.users_count || 0 }}</p>
          </div>
          <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Roles Card -->
      <div class="backdrop-blur-3xl bg-gradient-to-br from-green-500/10 via-emerald-500/5 to-transparent rounded-xl border border-white/10 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm font-medium">Total Roles</p>
            <p class="text-2xl font-bold text-white mt-1">{{ adminStats.roles_count || 0 }}</p>
          </div>
          <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Permissions Card -->
      <div class="backdrop-blur-3xl bg-gradient-to-br from-purple-500/10 via-violet-500/5 to-transparent rounded-xl border border-white/10 p-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-300 text-sm font-medium">Permissions</p>
            <p class="text-2xl font-bold text-white mt-1">{{ adminStats.permissions_count || 0 }}</p>
          </div>
          <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Admin Tab Navigation -->
    <div class="border-b border-white/20 mb-6">
      <nav class="flex space-x-8" aria-label="Admin Tabs">
        <button
          @click="activeAdminTab = 'overview'"
          :class="[
            activeAdminTab === 'overview'
              ? 'border-blue-400 text-blue-300'
              : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
            'py-3 px-1 border-b-2 font-medium text-sm transition-all duration-300'
          ]"
        >
          Overview
        </button>
        <button
          @click="activeAdminTab = 'users'"
          :class="[
            activeAdminTab === 'users'
              ? 'border-blue-400 text-blue-300'
              : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
            'py-3 px-1 border-b-2 font-medium text-sm transition-all duration-300'
          ]"
        >
          <div class="flex items-center space-x-2">
            <span>Users</span>
            <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
              {{ adminStats.users_count || 0 }}
            </span>
          </div>
        </button>
        <button
          @click="activeAdminTab = 'roles'"
          :class="[
            activeAdminTab === 'roles'
              ? 'border-blue-400 text-blue-300'
              : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
            'py-3 px-1 border-b-2 font-medium text-sm transition-all duration-300'
          ]"
        >
          <div class="flex items-center space-x-2">
            <span>Roles</span>
            <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
              {{ adminStats.roles_count || 0 }}
            </span>
          </div>
        </button>
        <button
          @click="activeAdminTab = 'permissions'"
          :class="[
            activeAdminTab === 'permissions'
              ? 'border-blue-400 text-blue-300'
              : 'border-transparent text-gray-400 hover:text-white hover:border-white/30',
            'py-3 px-1 border-b-2 font-medium text-sm transition-all duration-300'
          ]"
        >
          Permissions
        </button>
      </nav>
    </div>

    <!-- Tab Content -->
    <div class="space-y-6">
      <!-- Overview Tab -->
      <div v-show="activeAdminTab === 'overview'" class="space-y-6">
        <!-- Quick Actions -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-bold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Quick Actions
          </h3>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Open Full Admin Panel -->
            <Link
              :href="route('admin.dashboard')"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-blue-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-blue-300 transition-colors">Full Admin Panel</p>
                  <p class="text-sm text-gray-400">Open dedicated admin interface</p>
                </div>
              </div>
            </Link>

            <!-- Quick User Management -->
            <button
              @click="activeAdminTab = 'users'"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-green-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                  <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-green-300 transition-colors">Manage Users</p>
                  <p class="text-sm text-gray-400">Add, edit, and assign roles</p>
                </div>
              </div>
            </button>

            <!-- Quick Role Management -->
            <button
              @click="activeAdminTab = 'roles'"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                  <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-purple-300 transition-colors">Manage Roles</p>
                  <p class="text-sm text-gray-400">Create and configure roles</p>
                </div>
              </div>
            </button>
          </div>
        </div>

        <!-- System Status -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-6">
          <h3 class="text-lg font-bold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            System Status
          </h3>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">Authentication System</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-300 rounded-full">Active</span>
              </div>
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">Permission System</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-300 rounded-full">Active</span>
              </div>
            </div>
            <div class="space-y-3">
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">User Registration</span>
                <span class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-300 rounded-full">Enabled</span>
              </div>
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">Email Verification</span>
                <span class="px-2 py-1 text-xs font-medium bg-blue-500/20 text-blue-300 rounded-full">Enabled</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Tab -->
      <div v-show="activeAdminTab === 'users'" class="space-y-6">
        <UserManagement :users="adminStats.users || []" :roles="adminStats.roles || []" />
      </div>

      <!-- Roles Tab -->
      <div v-show="activeAdminTab === 'roles'" class="space-y-6">
        <RoleManagement :roles="adminStats.roles || []" :permissions="adminStats.permissions || []" />
      </div>

      <!-- Permissions Tab -->
      <div v-show="activeAdminTab === 'permissions'" class="space-y-6">
        <PermissionManagement :permissions="adminStats.permissions || []" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import UserManagement from './UserManagement.vue'
import RoleManagement from './RoleManagement.vue'
import PermissionManagement from './PermissionManagement.vue'

defineProps({
  adminStats: {
    type: Object,
    required: true
  }
})

const activeAdminTab = ref('overview')
</script>