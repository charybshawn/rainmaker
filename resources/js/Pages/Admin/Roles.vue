<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🛡️ Role Management</h1>
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
          class="px-3 py-2 rounded-md text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300"
        >
          Roles
        </Link>
        <Link 
          :href="route('admin.permissions')" 
          class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-300"
        >
          Permissions
        </Link>
      </nav>

      <!-- Success/Error Messages -->
      <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ $page.props.flash.success }}
      </div>
      <div v-if="$page.props.errors.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        {{ $page.props.errors.error }}
      </div>

      <!-- Create Role Button -->
      <div class="mb-6">
        <button 
          @click="showCreateModal = true"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        >
          <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Create New Role
        </button>
      </div>

      <!-- Roles Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="role in roles" 
          :key="role.id"
          class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ role.name }}</h3>
            <div class="flex space-x-2">
              <button 
                @click="editRole(role)"
                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button 
                @click="deleteRole(role)"
                :disabled="role.name === 'admin'"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
            </div>
          </div>
          
          <div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
              Permissions ({{ role.permissions.length }}):
            </p>
            <div class="flex flex-wrap gap-1">
              <span 
                v-for="permission in role.permissions" 
                :key="permission.id"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
              >
                {{ permission.name }}
              </span>
              <span v-if="role.permissions.length === 0" class="text-xs text-gray-500 dark:text-gray-400 italic">
                No permissions assigned
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Role Modal -->
    <div v-if="showCreateModal || editingRole" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            {{ editingRole ? 'Edit Role' : 'Create New Role' }}
          </h3>

          <form @submit.prevent="saveRole">
            <!-- Role Name -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Role Name
              </label>
              <input 
                v-model="roleForm.name"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                placeholder="Enter role name"
              >
            </div>

            <!-- Permissions -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Permissions
              </label>
              <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                <label v-for="permission in permissions" :key="permission.id" class="flex items-center">
                  <input 
                    type="checkbox" 
                    :value="permission.name"
                    v-model="roleForm.permissions"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ permission.name }}</span>
                </label>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
              <button
                type="button"
                @click="cancelRoleEdit"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                {{ editingRole ? 'Update Role' : 'Create Role' }}
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

const deleteRole = (role) => {
  if (role.name === 'admin') {
    alert('Cannot delete admin role')
    return
  }
  
  if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
    router.delete(route('admin.roles.destroy', role.id), {
      preserveScroll: true
    })
  }
}
</script>