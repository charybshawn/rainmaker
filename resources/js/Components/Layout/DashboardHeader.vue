<template>
  <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
    <div class="flex items-center gap-4">
      <!-- Rainmaker Logo Image -->
      <img
        src="/images/rainmaker-logo.png"
        alt="Rainmaker Logo"
        class="drop-shadow-sm h-20 sm:h-24 lg:h-28 xl:h-32 w-auto"
        style="opacity: 1 !important; filter: brightness(1.2) contrast(1.2) saturate(1.2);"
      />
    </div>

    <div class="flex items-center gap-4">
      <!-- Dark Mode Toggle -->
      <button
        @click="toggleDarkMode"
        class="group relative p-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)] border border-white/10 backdrop-blur-xl"
        style="backdrop-filter: blur(20px) saturate(150%);"
        :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
      >
        <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
        <svg v-else class="w-5 h-5 text-purple-200" fill="currentColor" viewBox="0 0 20 20">
          <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
      </button>

      <!-- User Authentication Section -->
      <span v-if="$page.props.auth.user" class="text-sm text-gray-600 dark:text-gray-300">
        Welcome, {{ $page.props.auth.user.name }}
      </span>

      <div v-if="$page.props.auth.user" class="relative">
        <Dropdown align="right" width="48">
          <template #trigger>
            <button class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
              {{ $page.props.auth.user.name }}
              <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
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

      <div v-else class="flex items-center gap-3">
        <button
          @click="$emit('show-login')"
          class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 backdrop-blur-xl"
          style="backdrop-filter: blur(20px) saturate(150%);"
        >
          Login
        </button>
        <button
          @click="$emit('show-register')"
          class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl"
          style="backdrop-filter: blur(20px) saturate(150%);"
        >
          Register
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Props
defineProps({
  canAccessAdmin: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['show-login', 'show-register'])

// Composables
const { isDarkMode, toggleDarkMode } = useDarkMode()
const page = usePage()
</script>