<template>
  <div>
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h3 class="text-xl font-bold text-white mb-2">Permission Management</h3>
        <p class="text-gray-300">Control system access and permissions</p>
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

    <!-- Permissions Table -->
    <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 overflow-hidden">
      <!-- Desktop Table -->
      <div class="hidden sm:block">
        <table class="min-w-full divide-y divide-white/10">
          <thead class="bg-white/5">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Permission
              </th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                Assigned Roles
              </th>
              <th class="px-4 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">
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
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-white">{{ permission.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="role in permission.roles"
                    :key="role.id"
                    class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
                  >
                    {{ role.name }}
                  </span>
                  <span v-if="permission.roles.length === 0" class="text-xs text-gray-500 italic">
                    Not assigned to any role
                  </span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-1">
                  <button
                    @click="editPermission(permission)"
                    class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                    title="Edit Permission"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                    @click="confirmDeletePermission(permission)"
                    class="p-1.5 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200"
                    title="Delete Permission"
                  >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Card View -->
      <div class="sm:hidden space-y-2 p-3">
        <div
          v-for="permission in permissions"
          :key="permission.id"
          class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-xl border border-white/10 p-3"
        >
          <div class="flex items-center justify-between mb-2">
            <div class="flex items-center space-x-2">
              <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center">
                <svg class="w-3 h-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-white/10 rounded-lg transition-all duration-200"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click="confirmDeletePermission(permission)"
                class="p-1.5 text-red-400 hover:text-red-300 hover:bg-white/10 rounded-lg transition-all duration-200"
              >
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
          <div>
            <p class="text-xs text-gray-400 mb-1">Assigned Roles:</p>
            <div class="flex flex-wrap gap-1">
              <span
                v-for="role in permission.roles"
                :key="role.id"
                class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-white/10 text-gray-300 backdrop-blur-xl border border-white/10"
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

    <!-- Create/Edit Permission Modal -->
    <BaseInfoModal
      :show="showCreateModal || !!editingPermission"
      @close="cancelPermissionEdit"
      :title="editingPermission ? 'Edit Permission' : 'Create New Permission'"
      :subtitle="editingPermission ? `Modify system permission '${permissionForm.name}'` : 'Create a new system permission'"
      max-width="50%"
    >
      <form @submit.prevent="savePermission" class="space-y-4">
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
          <p class="mt-1 text-xs text-gray-400">
            Use lowercase with spaces or hyphens (e.g., "manage users", "create-posts")
          </p>
        </div>
      </form>

      <template #footer>
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="cancelPermissionEdit"
            class="px-3 py-2 text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
          >
            Cancel
          </button>
          <button
            @click="savePermission"
            class="px-3 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-lg transition-all duration-200 shadow-lg hover:shadow-blue-500/25"
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
import { router } from '@inertiajs/vue3'
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