<template>
  <!-- Header Section -->
  <div class="relative z-10">
    <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-3 md:py-4 lg:py-6">
      <!-- Navigation Bar -->
      <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
        <div class="flex items-center gap-2 sm:gap-4">
          <!-- Rainmaker Logo Image -->
          <img
            src="/images/rainmaker-logo.png"
            alt="Rainmaker Logo"
            class="drop-shadow-sm h-8 sm:h-12 md:h-16 lg:h-20 xl:h-24 w-auto"
            style="opacity: 1 !important; filter: brightness(1.2) contrast(1.2) saturate(1.2);"
          />
        </div>
        <div class="flex items-center gap-2 sm:gap-4">
          <!-- Dark Mode Toggle -->
          <button
            @click="handleToggleDarkMode"
            class="group relative p-2 sm:p-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)] border border-white/10 backdrop-blur-xl"
            style="backdrop-filter: blur(20px) saturate(150%);"
            :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <svg v-if="isDarkMode" class="w-4 h-4 sm:w-5 sm:h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
            </svg>
            <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 text-purple-200" fill="currentColor" viewBox="0 0 20 20">
              <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
          </button>
          <span v-if="user" class="hidden sm:block text-sm text-gray-600 dark:text-gray-300">
            Welcome, {{ user.name }}
          </span>
          <div v-if="user" class="relative">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center px-2 sm:px-3 py-1 sm:py-2 border border-transparent text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                  {{ user.name }}
                  <svg class="ml-1 sm:ml-2 -mr-0.5 h-3 w-3 sm:h-4 sm:w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </template>
              <template #content>
                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                <DropdownLink
                  v-if="canAccessAdmin"
                  :href="route('admin.dashboard')"
                  class="border-t border-gray-100 dark:border-gray-600"
                >
                  🔧 Admin Panel
                </DropdownLink>
                <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
              </template>
            </Dropdown>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { useDarkMode } from '@/composables/useDarkMode'

// Props
defineProps({
  user: {
    type: Object,
    default: null
  },
  gitInfo: {
    type: Object,
    default: null
  }
})

// Events
const emit = defineEmits(['toggle-dark-mode', 'logout'])

// Composables
const { isDarkMode, toggleDarkMode } = useDarkMode()

// Computed
const canAccessAdmin = computed(() => {
  const user = usePage().props.auth?.user
  return user && (user.roles?.some(role => role.name === 'admin') || user.permissions?.some(permission => ['manage users', 'manage roles', 'manage permissions'].includes(permission.name)))
})

// Methods
const handleToggleDarkMode = () => {
  toggleDarkMode()
  emit('toggle-dark-mode')
}
</script>