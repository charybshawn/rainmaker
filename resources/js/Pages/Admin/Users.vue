<template>
  <Head title="User Management" />

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

        <!-- Page Header -->
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold text-white mb-2">👥 User Management</h1>
          <p class="text-gray-300">Manage user accounts and role assignments</p>
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
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            Dashboard
          </Link>
          <Link
            :href="route('admin.users')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-blue-400 text-blue-300"
          >
            <div class="flex items-center space-x-2">
              <span>Users</span>
              <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
                {{ users.length }}
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
                {{ roles.length }}
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

      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="mb-6 backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent rounded-2xl border border-green-500/30 p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <span class="text-green-300 font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
          <h2 class="text-2xl font-bold text-white mb-2">System Users</h2>
          <p class="text-gray-300">Manage user roles. Users inherit permissions through their assigned roles.</p>
        </div>
        <button
          @click="openCreateUserModal"
          class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-2 sm:py-3 px-4 sm:px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center justify-center sm:justify-start space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 w-full sm:w-auto"
          style="backdrop-filter: blur(20px) saturate(180%);"
        >
          <svg class="w-4 h-4 sm:w-5 sm:h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <span class="text-sm sm:text-base">Add User</span>
        </button>
      </div>

      <!-- Users Table -->
      <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.15)]">
        <!-- Desktop Table -->
        <div class="hidden sm:block">
          <!-- Table Header -->
          <div class="bg-gradient-to-r from-white/5 to-white/2 px-6 py-4 border-b border-white/10">
            <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white/80">
              <div class="col-span-4">User</div>
              <div class="col-span-3">Email</div>
              <div class="col-span-3">Roles</div>
              <div class="col-span-2 text-center">Actions</div>
            </div>
          </div>

          <!-- Table Body -->
          <div class="divide-y divide-white/5">
            <div
              v-for="user in users"
              :key="user.id"
              class="group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300"
            >
              <div class="grid grid-cols-12 gap-4 items-center text-sm">
                <!-- User Info -->
                <div class="col-span-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-sm">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                      <p class="font-medium text-white">{{ user.name }}</p>
                    </div>
                  </div>
                </div>

                <!-- Email -->
                <div class="col-span-3">
                  <p class="text-white/70">{{ user.email }}</p>
                </div>

                <!-- Roles -->
                <div class="col-span-3">
                  <div class="flex flex-wrap gap-1">
                    <span
                      v-for="role in user.roles"
                      :key="role.id"
                      class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30"
                    >
                      {{ role.name }}
                    </span>
                    <span v-if="user.roles.length === 0" class="text-gray-400 text-xs italic">No roles assigned</span>
                  </div>
                </div>

                <!-- Actions -->
                <div class="col-span-2">
                  <div class="flex items-center justify-center">
                    <button
                      @click="editUser(user)"
                      class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10"
                      title="Edit User"
                    >
                      <svg class="w-3.5 h-3.5 text-blue-200 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Card Layout -->
        <div class="sm:hidden space-y-3 p-4">
          <div
            v-for="user in users"
            :key="user.id"
            class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-4"
          >
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-3">
              <div class="flex items-center space-x-3 flex-1">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-sm">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="font-medium text-white">{{ user.name }}</p>
                  <p class="text-sm text-white/70 truncate">{{ user.email }}</p>
                </div>
              </div>
              <button
                @click="editUser(user)"
                class="p-1.5 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 text-blue-200 hover:text-white transition-colors"
                title="Edit User"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
            </div>

            <!-- Roles -->
            <div class="flex flex-wrap gap-1">
              <span
                v-for="role in user.roles"
                :key="role.id"
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30"
              >
                {{ role.name }}
              </span>
              <span v-if="user.roles.length === 0" class="text-gray-400 text-xs italic">No roles assigned</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <BaseInfoModal
      :show="!!editingUser"
      :title="`Edit User: ${editingUser?.name || ''}`"
      @close="cancelEdit"
    >
      <form @submit.prevent="updateUser" class="space-y-6">
        <!-- Roles Section -->
        <div>
          <label class="block text-sm font-medium text-white/80 mb-3">
            Assigned Roles
          </label>
          <div class="space-y-3 max-h-48 overflow-y-auto backdrop-blur-xl bg-white/5 border border-white/20 rounded-lg p-4">
            <label
              v-for="role in roles"
              :key="role.id"
              class="flex items-center space-x-3 cursor-pointer group"
            >
              <input
                type="checkbox"
                :value="role.name"
                v-model="userForm.roles"
                class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
              >
              <span class="text-white group-hover:text-blue-300 transition-colors">{{ role.name }}</span>
              <span class="text-xs text-gray-400">({{ role.permissions?.length || 0 }} permissions)</span>
            </label>
          </div>
          <p class="text-xs text-gray-400 mt-2">
            Users inherit all permissions from their assigned roles.
          </p>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
          <button
            type="button"
            @click="cancelEdit"
            class="order-2 sm:order-1 px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="order-1 sm:order-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 shadow-lg"
          >
            Save Changes
          </button>
        </div>
      </form>
    </BaseInfoModal>

    <!-- Create User Modal -->
    <BaseInfoModal
      :show="showCreateUserModal"
      title="Create New User"
      @close="closeCreateUserModal"
    >
      <form @submit.prevent="createUser" class="space-y-6">
        <!-- User Info -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">
              Full Name
            </label>
            <input
              v-model="createUserForm.name"
              type="text"
              required
              class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors"
              placeholder="Enter full name"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">
              Email Address
            </label>
            <input
              v-model="createUserForm.email"
              type="email"
              required
              class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors"
              placeholder="Enter email address"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-white/80 mb-2">
              Password
            </label>
            <input
              v-model="createUserForm.password"
              type="password"
              required
              class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors"
              placeholder="Enter password"
            />
          </div>
        </div>

        <!-- Roles Section -->
        <div>
          <label class="block text-sm font-medium text-white/80 mb-3">
            Assign Roles
          </label>
          <div class="space-y-3 max-h-48 overflow-y-auto backdrop-blur-xl bg-white/5 border border-white/20 rounded-lg p-4">
            <label
              v-for="role in roles"
              :key="role.id"
              class="flex items-center space-x-3 cursor-pointer group"
            >
              <input
                type="checkbox"
                :value="role.name"
                v-model="createUserForm.roles"
                class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
              >
              <span class="text-white group-hover:text-blue-300 transition-colors">{{ role.name }}</span>
              <span class="text-xs text-gray-400">({{ role.permissions?.length || 0 }} permissions)</span>
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
          <button
            type="button"
            @click="closeCreateUserModal"
            class="order-2 sm:order-1 px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="order-1 sm:order-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-lg transition-all duration-200 shadow-lg"
          >
            Create User
          </button>
        </div>
      </form>
    </BaseInfoModal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BaseInfoModal from '@/Components/Modals/BaseInfoModal.vue'

const props = defineProps({
  users: {
    type: Array,
    required: true
  },
  roles: {
    type: Array,
    required: true
  }
})

const editingUser = ref(null)
const userForm = ref({
  roles: []
})

const showCreateUserModal = ref(false)
const createUserForm = ref({
  name: '',
  email: '',
  password: '',
  roles: []
})

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    roles: user.roles.map(role => role.name)
  }
}

const cancelEdit = () => {
  editingUser.value = null
  userForm.value = { roles: [] }
}

const updateUser = () => {
  router.put(route('admin.users.roles', editingUser.value.id), {
    roles: userForm.value.roles
  }, {
    preserveScroll: true,
    onSuccess: () => {
      cancelEdit()
    }
  })
}

const openCreateUserModal = () => {
  showCreateUserModal.value = true
}

const closeCreateUserModal = () => {
  showCreateUserModal.value = false
  createUserForm.value = {
    name: '',
    email: '',
    password: '',
    roles: []
  }
}

const createUser = () => {
  router.post(route('admin.users.store'), createUserForm.value, {
    preserveScroll: true,
    onSuccess: () => {
      closeCreateUserModal()
    }
  })
}
</script>