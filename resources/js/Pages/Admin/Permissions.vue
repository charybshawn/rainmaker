<template>
  <Head title="Permission Management" />

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
          <h1 class="text-4xl font-bold text-white mb-2">🔐 Permission Management</h1>
          <p class="text-gray-300">Control system access and permissions</p>
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
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-transparent text-gray-400 hover:text-white hover:border-white/30"
          >
            <div class="flex items-center space-x-2">
              <span>Roles</span>
            </div>
          </Link>
          <Link
            :href="route('admin.permissions')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 border-blue-400 text-blue-300"
          >
            <div class="flex items-center space-x-2">
              <span>Permissions</span>
              <span class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
                {{ permissions.length }}
              </span>
            </div>
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
        <!-- Permissions Management Section -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">System Permissions</h2>
              <p class="text-gray-300">Manage system permissions and their assignments to roles</p>
            </div>
            <button
              @click="showCreateModal = true"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25 hover:scale-105"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Create New Permission
            </button>
          </div>

          <!-- Desktop Table View -->
          <div class="hidden sm:block">
            <div class="backdrop-blur-3xl bg-white/5 rounded-xl border border-white/10 overflow-hidden">
              <table class="min-w-full divide-y divide-white/10">
                <thead class="bg-white/5">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                      Permission
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                      Assigned Roles
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                  <tr
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="hover:bg-white/5 transition-colors"
                  >
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center mr-4">
                          <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                          </svg>
                        </div>
                        <div>
                          <div class="text-sm font-medium text-white">{{ permission.name }}</div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <div class="flex flex-wrap gap-1">
                        <span
                          v-for="role in permission.roles"
                          :key="role.id"
                          class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
                        >
                          {{ role.name }}
                        </span>
                        <span v-if="permission.roles.length === 0" class="text-xs text-gray-500 italic">
                          Not assigned to any role
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div class="flex items-center justify-end space-x-2">
                        <button
                          @click="editPermission(permission)"
                          class="p-2 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                          title="Edit Permission"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button
                          @click="confirmDeletePermission(permission)"
                          class="p-2 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                          title="Delete Permission"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Mobile Card View -->
          <div class="sm:hidden space-y-3">
            <div
              v-for="permission in permissions"
              :key="permission.id"
              class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-4"
            >
              <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-white">{{ permission.name }}</div>
                  </div>
                </div>
                <div class="flex space-x-1">
                  <button
                    @click="editPermission(permission)"
                    class="p-2 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="confirmDeletePermission(permission)"
                    class="p-2 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </div>
              <div>
                <p class="text-xs text-gray-400 mb-2">Assigned Roles:</p>
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="role in permission.roles"
                    :key="role.id"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
                  >
                    {{ role.name }}
                  </span>
                  <span v-if="permission.roles.length === 0" class="text-xs text-gray-500 italic">
                    Not assigned to any role
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Permission Modal -->
    <BaseInfoModal
      :show="showCreateModal || !!editingPermission"
      @close="cancelPermissionEdit"
      :title="editingPermission ? 'Edit Permission' : 'Create New Permission'"
      :subtitle="editingPermission ? `Modify system permission '${permissionForm.name}'` : 'Create a new system permission'"
      max-width="50%"
    >
      <form @submit.prevent="savePermission" class="space-y-6">
        <!-- Permission Name -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">
            Permission Name
          </label>
          <input
            v-model="permissionForm.name"
            type="text"
            required
            class="w-full px-3 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 backdrop-blur-xl"
            placeholder="e.g., manage users, create posts, etc."
          >
          <p class="mt-2 text-xs text-gray-400">
            Use lowercase with spaces or hyphens (e.g., "manage users", "create-posts")
          </p>
        </div>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="cancelPermissionEdit"
            class="px-4 py-2 text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
          >
            Cancel
          </button>
          <button
            @click="savePermission"
            class="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25"
          >
            {{ editingPermission ? 'Update Permission' : 'Create Permission' }}
          </button>
        </div>
      </template>
    </BaseInfoModal>

    <!-- Delete Confirmation Modal -->
    <BaseActionModal
      :show="!!permissionToDelete"
      @close="permissionToDelete = null"
      @confirm="deletePermission"
      title="Delete Permission"
      :message="`Are you sure you want to delete the permission '${permissionToDelete?.name}'? This action cannot be undone.`"
      danger-mode
      confirm-text="Delete Permission"
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
  permissions: {
    type: Array,
    required: true
  }
})

const showCreateModal = ref(false)
const editingPermission = ref(null)
const permissionToDelete = ref(null)
const permissionForm = ref({
  name: ''
})

const editPermission = (permission) => {
  editingPermission.value = permission
  permissionForm.value = {
    name: permission.name
  }
}

const cancelPermissionEdit = () => {
  showCreateModal.value = false
  editingPermission.value = null
  permissionForm.value = { name: '' }
}

const savePermission = () => {
  if (editingPermission.value) {
    // Update existing permission
    router.put(route('admin.permissions.update', editingPermission.value.id), permissionForm.value, {
      preserveScroll: true,
      onSuccess: () => cancelPermissionEdit()
    })
  } else {
    // Create new permission
    router.post(route('admin.permissions.store'), permissionForm.value, {
      preserveScroll: true,
      onSuccess: () => cancelPermissionEdit()
    })
  }
}

const confirmDeletePermission = (permission) => {
  permissionToDelete.value = permission
}

const deletePermission = () => {
  if (permissionToDelete.value) {
    router.delete(route('admin.permissions.destroy', permissionToDelete.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        permissionToDelete.value = null
      }
    })
  }
}
</script>