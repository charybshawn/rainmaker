<template>
  <Head title="Role Management" />

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
          <h1 class="text-4xl font-bold text-white mb-2">🛡️ Role Management</h1>
          <p class="text-gray-300">Create and configure user roles with permissions</p>
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
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            <div class="flex items-center space-x-2">
              <span>Users</span>
            </div>
          </Link>
          <Link
            :href="route('admin.roles')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-blue-400 text-blue-300"
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
      <div v-if="$page.props.flash?.success" class="mb-6 backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent rounded-2xl border border-green-400/30 p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="text-green-300 font-medium">{{ $page.props.flash.success }}</span>
        </div>
      </div>

      <!-- Content Section -->
      <div class="space-y-8">
        <!-- Create Role Section -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">Role Configuration</h2>
              <p class="text-gray-300">Create and manage user roles with specific permissions</p>
            </div>
            <button
              @click="showCreateModal = true"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25 hover:scale-105"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Create New Role
            </button>
          </div>

          <!-- Roles Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
              v-for="role in roles"
              :key="role.id"
              class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-6 hover:border-white/20 transition-all duration-300 group"
            >
              <!-- Role Header -->
              <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-white">{{ role.name }}</h3>
                    <p class="text-xs text-gray-400">{{ role.permissions.length }} permissions</p>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                  <button
                    @click="editRole(role)"
                    class="p-2 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                    title="Edit Role"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="confirmDeleteRole(role)"
                    :disabled="role.name === 'admin'"
                    class="p-2 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Delete Role"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Permissions Display -->
              <div>
                <div class="flex flex-wrap gap-1 min-h-[2rem]">
                  <span
                    v-for="permission in role.permissions.slice(0, 4)"
                    :key="permission.id"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
                  >
                    {{ permission.name }}
                  </span>
                  <span
                    v-if="role.permissions.length > 4"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-500/20 text-blue-300 backdrop-blur-xl border border-blue-400/30"
                  >
                    +{{ role.permissions.length - 4 }} more
                  </span>
                  <span v-if="role.permissions.length === 0" class="text-xs text-gray-500 italic py-1">
                    No permissions assigned
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <BaseInfoModal
      :show="showCreateModal || !!editingRole"
      @close="cancelRoleEdit"
      :title="editingRole ? 'Edit Role' : 'Create New Role'"
      :subtitle="editingRole ? `Modify permissions for ${roleForm.name}` : 'Configure a new role with specific permissions'"
      max-width="66%"
    >
      <form @submit.prevent="saveRole" class="space-y-6">
        <!-- Role Name -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">
            Role Name
          </label>
          <input
            v-model="roleForm.name"
            type="text"
            required
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 backdrop-blur-xl"
            placeholder="Enter role name"
          >
        </div>

        <!-- Permissions -->
        <div>
          <label class="block text-sm font-medium text-white mb-3">
            Permissions ({{ roleForm.permissions.length }} selected)
          </label>
          <div class="backdrop-blur-3xl bg-white/5 border border-white/20 rounded-lg p-4 max-h-64 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label
                v-for="permission in permissions"
                :key="permission.id"
                class="flex items-center p-2 rounded-lg hover:bg-white/5 transition-colors cursor-pointer"
              >
                <input
                  type="checkbox"
                  :value="permission.name"
                  v-model="roleForm.permissions"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-white/30 rounded bg-white/10 backdrop-blur-xl"
                >
                <span class="ml-3 text-sm text-white">{{ permission.name }}</span>
              </label>
            </div>
          </div>
        </div>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="cancelRoleEdit"
            class="px-4 py-2 text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
          >
            Cancel
          </button>
          <button
            @click="saveRole"
            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25"
          >
            {{ editingRole ? 'Update Role' : 'Create Role' }}
          </button>
        </div>
      </template>
    </BaseInfoModal>

    <!-- Delete Confirmation Modal -->
    <BaseActionModal
      :show="!!roleToDelete"
      @close="roleToDelete = null"
      @confirm="deleteRole"
      title="Delete Role"
      :message="`Are you sure you want to delete the role '${roleToDelete?.name}'? This action cannot be undone.`"
      danger-mode
      confirm-text="Delete Role"
    >
      <template #icon>
        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
        </svg>
      </template>
    </BaseActionModal>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import BaseInfoModal from '@/Components/Modals/BaseInfoModal.vue'
import BaseActionModal from '@/Components/Modals/BaseActionModal.vue'

const props = defineProps({
  roles: {
    type: Array,
    required: true
  },
  permissions: {
    type: Array,
    required: true
  }
})

const showCreateModal = ref(false)
const editingRole = ref(null)
const roleToDelete = ref(null)
const roleForm = ref({
  name: '',
  permissions: []
})

const editRole = (role) => {
  editingRole.value = role
  roleForm.value = {
    name: role.name,
    permissions: role.permissions.map(permission => permission.name)
  }
}

const cancelRoleEdit = () => {
  showCreateModal.value = false
  editingRole.value = null
  roleForm.value = { name: '', permissions: [] }
}

const saveRole = () => {
  if (editingRole.value) {
    // Update existing role
    router.put(route('admin.roles.update', editingRole.value.id), roleForm.value, {
      preserveScroll: true,
      onSuccess: () => cancelRoleEdit()
    })
  } else {
    // Create new role
    router.post(route('admin.roles.store'), roleForm.value, {
      preserveScroll: true,
      onSuccess: () => cancelRoleEdit()
    })
  }
}

const confirmDeleteRole = (role) => {
  if (role.name === 'admin') {
    return
  }
  roleToDelete.value = role
}

const deleteRole = () => {
  if (roleToDelete.value) {
    router.delete(route('admin.roles.destroy', roleToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        roleToDelete.value = null
      }
    })
  }
}
</script>