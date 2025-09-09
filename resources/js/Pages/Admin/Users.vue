<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">👥 User Management</h1>
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
          class="px-3 py-2 rounded-md text-sm font-medium text-blue-600 bg-blue-100 dark:bg-blue-900 dark:text-blue-300"
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
          class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-300"
        >
          Permissions
        </Link>
      </nav>

      <!-- Success Message -->
      <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
        {{ $page.props.flash.success }}
      </div>

      <!-- Users Table -->
      <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            System Users
          </h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Manage user roles and permissions.
          </p>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-600">
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    User
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Email
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Roles
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Direct Permissions
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                <tr v-for="user in users" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                          <span class="text-gray-600 dark:text-gray-300 font-medium">
                            {{ user.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ user.name }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ user.email }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-wrap gap-1">
                      <span 
                        v-for="role in user.roles" 
                        :key="role.id"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                      >
                        {{ role.name }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-wrap gap-1">
                      <span 
                        v-for="permission in user.permissions" 
                        :key="permission.id"
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200"
                      >
                        {{ permission.name }}
                      </span>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button 
                      @click="editUser(user)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 mr-4"
                    >
                      Edit
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="editingUser" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Edit User: {{ editingUser.name }}
          </h3>

          <form @submit.prevent="updateUser">
            <!-- Roles Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Roles
              </label>
              <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                <label v-for="role in roles" :key="role.id" class="flex items-center">
                  <input 
                    type="checkbox" 
                    :value="role.name"
                    v-model="userForm.roles"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ role.name }}</span>
                </label>
              </div>
            </div>

            <!-- Direct Permissions Section -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Direct Permissions
              </label>
              <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md p-3">
                <label v-for="permission in permissions" :key="permission.id" class="flex items-center">
                  <input 
                    type="checkbox" 
                    :value="permission.name"
                    v-model="userForm.permissions"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                  >
                  <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ permission.name }}</span>
                </label>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-3">
              <button
                type="button"
                @click="cancelEdit"
                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              >
                Save Changes
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
  users: {
    type: Array,
    required: true
  },
  roles: {
    type: Array,
    required: true
  },
  permissions: {
    type: Array,
    required: true
  }
})

const editingUser = ref(null)
const userForm = ref({
  roles: [],
  permissions: []
})

const editUser = (user) => {
  editingUser.value = user
  userForm.value = {
    roles: user.roles.map(role => role.name),
    permissions: user.permissions.map(permission => permission.name)
  }
}

const cancelEdit = () => {
  editingUser.value = null
  userForm.value = { roles: [], permissions: [] }
}

const updateUser = () => {
  // Update roles
  router.put(route('admin.users.roles', editingUser.value.id), {
    roles: userForm.value.roles
  }, {
    preserveScroll: true,
    onSuccess: () => {
      // Update permissions
      router.put(route('admin.users.permissions', editingUser.value.id), {
        permissions: userForm.value.permissions
      }, {
        preserveScroll: true,
        onSuccess: () => {
          cancelEdit()
        }
      })
    }
  })
}
</script>