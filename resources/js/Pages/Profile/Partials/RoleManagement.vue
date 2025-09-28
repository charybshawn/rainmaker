<template>
  <div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h3 class="text-xl font-bold text-white mb-2">Role Management</h3>
        <p class="text-gray-300">Create and configure user roles with permissions</p>
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
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
      <div
        v-for="role in roles"
        :key="role.id"
        class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-4 hover:border-white/20 transition-all duration-300 group"
      >
        <!-- Role Header -->
        <div class="flex items-center justify-between mb-3">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center">
              <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
            <div>
              <h4 class="text-md font-semibold text-white">{{ role.name }}</h4>
              <p class="text-xs text-gray-400">{{ role.permissions.length }} permissions</p>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <button
              @click="editRole(role)"
              class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
              title="Edit Role"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button
              @click="confirmDeleteRole(role)"
              :disabled="role.name === 'admin'"
              class="p-1.5 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              title="Delete Role"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Permissions Display -->
        <div>
          <div class="flex flex-wrap gap-1 min-h-[1.5rem]">
            <span
              v-for="permission in role.permissions.slice(0, 3)"
              :key="permission.id"
              class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
            >
              {{ permission.name }}
            </span>
            <span
              v-if="role.permissions.length > 3"
              class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-500/20 text-blue-300 backdrop-blur-xl border border-blue-400/30"
            >
              +{{ role.permissions.length - 3 }} more
            </span>
            <span v-if="role.permissions.length === 0" class="text-xs text-gray-500 italic py-0.5">
              No permissions assigned
            </span>
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
      <form @submit.prevent="saveRole" class="space-y-4">
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
          <label class="block text-sm font-medium text-white mb-2">
            Permissions ({{ roleForm.permissions.length }} selected)
          </label>
          <div class="backdrop-blur-3xl bg-white/5 border border-white/20 rounded-lg p-3 max-h-48 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
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
                <span class="ml-2 text-sm text-white">{{ permission.name }}</span>
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
            class="px-3 py-2 text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
          >
            Cancel
          </button>
          <button
            @click="saveRole"
            class="px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25"
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
import { router } from '@inertiajs/vue3'
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