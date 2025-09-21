<template>
  <div class="relative">
    <!-- Hamburger Button -->
    <button
      @click="toggleMenu"
      class="group relative p-3 transition-all duration-500 hover:scale-105 active:scale-95 backdrop-blur-3xl rounded-2xl shadow-[0_8px_32px_0_rgba(147,51,234,0.3)] hover:shadow-[0_12px_40px_0_rgba(147,51,234,0.5)] border border-white/30 hover:border-purple-300/50"
      style="backdrop-filter: blur(20px) saturate(180%); background: linear-gradient(135deg, rgba(147,51,234,0.15), rgba(59,130,246,0.15), rgba(16,185,129,0.1));"
      :title="showMenu ? 'Close menu' : 'Open menu'"
    >
      <!-- Animated Hamburger Icon -->
      <div class="relative w-5 h-5">
        <!-- Top line -->
        <span
          :class="[
            'absolute top-1 left-0 w-5 h-0.5 rounded-full shadow-[0_2px_8px_rgba(147,51,234,0.4)] group-hover:shadow-[0_2px_12px_rgba(147,51,234,0.6)] transition-all duration-300 ease-in-out',
            showMenu ? 'rotate-45 translate-y-1.5' : ''
          ]"
          style="background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(147,51,234,0.7)); backdrop-filter: blur(4px);"
        ></span>
        <!-- Middle line -->
        <span
          :class="[
            'absolute top-2 left-0 w-5 h-0.5 rounded-full shadow-[0_2px_8px_rgba(59,130,246,0.4)] group-hover:shadow-[0_2px_12px_rgba(59,130,246,0.6)] transition-all duration-300 ease-in-out',
            showMenu ? 'opacity-0' : ''
          ]"
          style="background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(59,130,246,0.7)); backdrop-filter: blur(4px);"
        ></span>
        <!-- Bottom line -->
        <span
          :class="[
            'absolute top-3 left-0 w-5 h-0.5 rounded-full shadow-[0_2px_8px_rgba(16,185,129,0.4)] group-hover:shadow-[0_2px_12px_rgba(16,185,129,0.6)] transition-all duration-300 ease-in-out',
            showMenu ? '-rotate-45 -translate-y-1.5' : ''
          ]"
          style="background: linear-gradient(90deg, rgba(255,255,255,0.9), rgba(16,185,129,0.7)); backdrop-filter: blur(4px);"
        ></span>
      </div>
    </button>

    <!-- Mobile Dropdown Menu -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="transform -translate-y-4 opacity-0"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      leave-to-class="transform -translate-y-4 opacity-0"
    >
      <div
        v-if="showMenu"
        :class="[
          isFullScreen
            ? 'fixed inset-0 w-full h-full z-50 flex flex-col'
            : 'absolute top-full mt-2 right-0 w-64 rounded-2xl shadow-[0_20px_25px_-5px_rgba(0,0,0,0.6)] border border-white/20 py-3 z-50'
        ]"
        :style="isFullScreen
          ? 'background: linear-gradient(135deg, rgb(15,23,42), rgb(30,41,59));'
          : 'background: linear-gradient(135deg, rgba(15,23,42,0.95), rgba(30,41,59,0.95)); backdrop-filter: blur(20px); transform: translateX(-100%); transform-origin: top right;'"
        @click.stop
      >
        <!-- Full-Screen Modal Header -->
        <div v-if="isFullScreen" class="flex items-center justify-between p-6 border-b border-white/20 bg-gradient-to-r from-slate-800/50 to-slate-700/50">
          <h2 class="text-2xl font-bold text-white">Navigation</h2>
          <button
            @click="closeMenu"
            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 flex items-center justify-center transition-all duration-300 hover:scale-105 backdrop-blur-xl"
            style="backdrop-filter: blur(20px) saturate(150%);"
          >
            <svg class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Full-Screen Modal Content -->
        <div v-if="isFullScreen" class="flex-1 flex flex-col justify-center items-center px-6 py-8">
          <!-- Logo -->
          <div class="mb-8">
            <img
              src="/images/rainmaker-logo.png"
              alt="Rainmaker Logo"
              class="h-16 w-auto drop-shadow-sm"
              style="filter: brightness(1.2) contrast(1.2) saturate(1.2);"
            />
          </div>

          <!-- Search Section -->
          <div class="w-full max-w-md mb-8">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search companies, tickers..."
                class="w-full h-12 pl-12 pr-4 bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400/20 focus:border-blue-400/50 transition-all duration-200 text-base"
                style="backdrop-filter: blur(20px) saturate(150%);"
              />
              <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Navigation Tabs -->
          <div class="w-full max-w-sm space-y-3 mb-8">
            <button
              v-for="tab in allTabs"
              :key="tab.id"
              @click="selectTab(tab.id)"
              :class="[
                'group relative w-full flex items-center justify-center space-x-3 px-6 py-4 text-lg font-medium transition-all duration-500 hover:scale-105 rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] border border-white/10 backdrop-blur-xl',
                activeTab === tab.id
                  ? (tab.color === 'blue' ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:text-white hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]' :
                     tab.color === 'green' ? 'bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)]' :
                     tab.color === 'purple' ? 'bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 hover:text-white hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)]' :
                     'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:text-white hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)]')
                  : 'bg-gradient-to-br from-white/5 via-white/10 to-white/5 text-gray-300 hover:text-white hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)]'
              ]"
            >
              <div :class="[
                'p-2 rounded-full transition-all duration-200',
                activeTab === tab.id
                  ? (tab.color === 'blue' ? 'bg-blue-500/30' :
                     tab.color === 'green' ? 'bg-green-500/30' :
                     tab.color === 'purple' ? 'bg-purple-500/30' :
                     'bg-blue-500/30')
                  : 'bg-white/5'
              ]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  <path v-else-if="tab.id === 'companies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  <path v-else-if="tab.id === 'research'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  <path v-else-if="tab.id === 'insights'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <span>{{ tab.label }}</span>
            </button>
          </div>

          <!-- User Actions (if authenticated) -->
          <div v-if="user" class="w-full max-w-sm space-y-3">
            <button
              v-if="canAccessAdmin"
              @click="navigateToAdmin"
              class="group relative w-full flex items-center justify-center space-x-3 px-6 py-3 text-base font-medium transition-all duration-500 hover:scale-105 bg-gradient-to-br from-orange-500/20 via-orange-400/10 to-transparent text-orange-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(234,88,12,0.2)] border border-white/10 backdrop-blur-xl"
            >
              <div class="p-1.5 rounded-full bg-white/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <span>Admin Panel</span>
            </button>
            <button
              @click="logout"
              class="group relative w-full flex items-center justify-center space-x-3 px-6 py-3 text-base font-medium transition-all duration-500 hover:scale-105 bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent text-red-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(239,68,68,0.2)] border border-white/10 backdrop-blur-xl"
            >
              <div class="p-1.5 rounded-full bg-white/5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
              </div>
              <span>Sign Out</span>
            </button>
          </div>
        </div>

        <!-- Regular Dropdown Content (when not full-screen) -->
        <div v-if="!isFullScreen">
          <!-- Search Section -->
          <div class="px-4 pb-3 border-b border-white/10">
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search companies, tickers..."
                class="w-full h-10 pl-10 pr-4 bg-white/5 backdrop-blur-xl border border-white/20 text-white placeholder-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400/20 focus:border-blue-400/50 transition-all duration-200 text-sm"
                style="backdrop-filter: blur(20px) saturate(150%);"
              />
              <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Navigation Tabs -->
          <div class="py-2">
            <button
              v-for="tab in allTabs"
              :key="tab.id"
              @click="selectTab(tab.id)"
              :class="[
              'w-full flex items-center space-x-3 px-4 py-3 text-left text-sm font-medium transition-all duration-200',
              activeTab === tab.id
                ? (tab.color === 'blue' ? 'text-blue-200 bg-blue-500/20' :
                   tab.color === 'green' ? 'text-green-200 bg-green-500/20' :
                   tab.color === 'purple' ? 'text-purple-200 bg-purple-500/20' :
                   'text-blue-200 bg-blue-500/20')
                : 'text-gray-300 hover:text-white hover:bg-white/10'
            ]"
          >
            <div :class="[
              'p-1.5 rounded-full transition-all duration-200',
              activeTab === tab.id
                ? (tab.color === 'blue' ? 'bg-blue-500/30' :
                   tab.color === 'green' ? 'bg-green-500/30' :
                   tab.color === 'purple' ? 'bg-purple-500/30' :
                   'bg-blue-500/30')
                : 'bg-white/5 group-hover:bg-white/10'
            ]">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                <path v-else-if="tab.id === 'companies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                <path v-else-if="tab.id === 'research'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                <path v-else-if="tab.id === 'insights'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
            </div>
            <span>{{ tab.label }}</span>
            </button>
          </div>

        <!-- User Actions (if authenticated) -->
        <div v-if="user" class="pt-3 border-t border-white/10">
          <div class="px-4 pb-2">
            <div class="text-xs text-white/60 font-medium">Account</div>
          </div>
          <button
            v-if="canAccessAdmin"
            @click="navigateToAdmin"
            class="w-full flex items-center space-x-3 px-4 py-2 text-left text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200"
          >
            <div class="p-1.5 rounded-full bg-white/5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
            </div>
            <span>Admin Panel</span>
          </button>
          <button
            @click="logout"
            class="w-full flex items-center space-x-3 px-4 py-2 text-left text-sm font-medium text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-200"
          >
            <div class="p-1.5 rounded-full bg-white/5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
            </div>
            <span>Sign Out</span>
          </button>
        </div>
        </div>
      </div>
    </Transition>

    <!-- Click outside to close -->
    <div
      v-if="showMenu"
      class="fixed inset-0 z-40"
      @click="closeMenu"
    ></div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  allTabs: {
    type: Array,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  },
  user: {
    type: Object,
    default: null
  },
  canAccessAdmin: {
    type: Boolean,
    default: false
  },
  isFullScreen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['tab-selected', 'search'])

const showMenu = ref(false)
const searchQuery = ref('')

const toggleMenu = () => {
  showMenu.value = !showMenu.value
}

const closeMenu = () => {
  showMenu.value = false
}

const selectTab = (tabId) => {
  emit('tab-selected', tabId)
  closeMenu()
}

const navigateToAdmin = () => {
  router.visit(route('admin.dashboard'))
  closeMenu()
}

const logout = () => {
  router.post(route('logout'))
  closeMenu()
}

// Watch search query and emit search events
const handleSearch = () => {
  emit('search', searchQuery.value)
}

// Close menu on escape key
const handleEscape = (event) => {
  if (event.key === 'Escape') {
    closeMenu()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
})
</script>