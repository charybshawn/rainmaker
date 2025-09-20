<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🔐 Permission Management</h1>
          </div>
          <div class="flex items-center space-x-4">
            <Link href="/" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200">
              ← Back to Dashboard
            </Link>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Navigation -->
      <nav class="flex space-x-8 mb-8">
        <Link 
          :href="route('admin.dashboard')" 
          class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-300"
        >
          Dashboard
        </Link>
        <Link 
          :href="route('admin.users')" 
          class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-300"
        >
          Users
        </Link>
        <Link 
          :href="route('admin.roles')" 
          class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-300"
        >
          Roles
        </Link>
        <Link 
          :href="route('admin.permissions')" 
          class="px-3 py-2 rounded-md text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300"
        >
          Permissions
        </Link>
      </nav>

      <!-- Success Message -->
      <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ $page.props.flash.success }}
      </div>

      <!-- Create Permission Button -->
      <div class="mb-6">
        <button 
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Create New Permission
        </button>
      </div>

      <!-- Permissions Table -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            System Permissions
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Manage system permissions and their assignments to roles.
          </p>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-600">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Permission Name
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Assigned to Roles
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                <tr v-for="permission in permissions" :key="permission.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div class="h-8 w-8 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                          <svg class="h-4 w-4 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                          </svg>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ permission.name }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-wrap gap-1">
                      <span 
                        v-for="role in permission.roles" 
                        :key="role.id"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                      >
                        {{ role.name }}
                      </span>
                      <span v-if="permission.roles.length === 0" class="text-xs text-gray-500 dark:text-gray-400 italic">
                        Not assigned to any role
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="editPermission(permission)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-4"
                    >
                      Edit
                    </button>
                    <button 
                      @click="deletePermission(permission)"
                      class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Permission Modal -->
    <div v-if="showCreateModal || editingPermission" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ editingPermission ? 'Edit Permission' : 'Create New Permission' }}
          </h3>

          <form @submit.prevent="savePermission">
            <!-- Permission Name -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Permission Name
              </label>
              <input 
                v-model="permissionForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="e.g., manage users, create posts, etc."
              >
              <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Use lowercase with spaces or hyphens (e.g., "manage users", "create-posts")
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
              <button
                type="button"
                @click="cancelPermissionEdit"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                {{ editingPermission ? 'Update Permission' : 'Create Permission' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  permissions: {
    type: Array,
    required: true
  }
})

const showCreateModal = ref(false)
const editingPermission = ref(null)
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

const deletePermission = (permission) => {
  if (confirm(`Are you sure you want to delete the permission "${permission.name}"?`)) {
    router.delete(route('admin.permissions.destroy', permission.id), {
      preserveScroll: true
    })
  }
}
</script>