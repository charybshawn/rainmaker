<template>
  <Head title="Admin Dashboard" />

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

          <!-- User Info -->
          <div class="flex items-center space-x-4">
            <span class="text-white/80">{{ $page.props.auth.user?.name }}</span>
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-sm">
              {{ $page.props.auth.user?.name?.charAt(0)?.toUpperCase() || 'U' }}
            </div>
          </div>
        </div>

        <!-- Admin Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold text-white mb-2">🛡️ Admin Panel</h1>
          <p class="text-gray-300">Manage users, roles, and permissions</p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
      <!-- Tab Navigation -->
      <div class="border-b border-white/20 mb-8">
        <nav class="flex space-x-8" aria-label="Admin Tabs">
          <Link
            :href="route('admin.dashboard')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-blue-400 text-blue-300"
          >
            Dashboard
          </Link>
          <Link
            :href="route('admin.users')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            <div class="flex items-center space-x-2">
              <span>Users</span>
              <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
                {{ users_count }}
              </span>
            </div>
          </Link>
          <Link
            :href="route('admin.roles')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            <div class="flex items-center space-x-2">
              <span>Roles</span>
              <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
                {{ roles_count }}
              </span>
            </div>
          </Link>
          <Link
            :href="route('admin.permissions')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            Permissions
          </Link>
        </nav>
      </div>

      <!-- Dashboard Content -->
      <div class="space-y-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Users Card -->
          <div class="backdrop-blur-3xl bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-sm font-medium">Total Users</p>
                <p class="text-3xl font-bold text-white mt-1">{{ users_count }}</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-blue-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Roles Card -->
          <div class="backdrop-blur-3xl bg-gradient-to-br from-green-500/10 via-emerald-500/5 to-transparent rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-sm font-medium">Total Roles</p>
                <p class="text-3xl font-bold text-white mt-1">{{ roles_count }}</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-green-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Quick Action Card -->
          <div class="backdrop-blur-3xl bg-gradient-to-br from-purple-500/10 via-violet-500/5 to-transparent rounded-2xl border border-white/10 p-6">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-gray-300 text-sm font-medium">Admin Actions</p>
                <p class="text-white text-sm mt-1">Manage system access</p>
              </div>
              <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6">
          <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
            <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Quick Actions
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Manage Users -->
            <Link
              :href="route('admin.users')"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-blue-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-blue-300 transition-colors">Manage Users</p>
                  <p class="text-sm text-gray-400">Add, edit, and assign roles</p>
                </div>
              </div>
            </Link>

            <!-- Manage Roles -->
            <Link
              :href="route('admin.roles')"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-green-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                  <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-green-300 transition-colors">Manage Roles</p>
                  <p class="text-sm text-gray-400">Create and configure roles</p>
                </div>
              </div>
            </Link>

            <!-- Manage Permissions -->
            <Link
              :href="route('admin.permissions')"
              class="group p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-400/50 transition-all duration-300 hover:scale-105"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                  <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <div>
                  <p class="font-semibold text-white group-hover:text-purple-300 transition-colors">Manage Permissions</p>
                  <p class="text-sm text-gray-400">Control system access</p>
                </div>
              </div>
            </Link>
          </div>
        </div>

        <!-- System Overview -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6">
          <h2 class="text-2xl font-bold text-white mb-6 flex items-center">
            <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            System Status
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">Authentication System</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-300 rounded-full">Active</span>
              </div>
              <div class="flex items-center justify-between p-3 rounded-lg bg-white/5">
                <span class="text-gray-300">Permission System</span>
                <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-300 rounded-full">Active</span>
              </div>
            </div>
            <div class="space-y-4">
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
    </div>
  </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
  users_count: {
    type: Number,
    required: true
  },
  roles_count: {
    type: Number,
    required: true
  }
})
</script>