<template>
  <!-- Responsive Navigation (Hidden on Mobile) -->
  <div class="hidden sm:flex items-center justify-between mb-4 sm:mb-6 lg:mb-8">
    <!-- Desktop Navigation (lg+): Show all tabs -->
    <div class="hidden lg:flex items-center space-x-2 sm:space-x-4 lg:space-x-8 relative">
      <button
        v-for="tab in allTabs"
        :key="tab.id"
        @click="$emit('tab-selected', tab.id)"
        :class="[
          'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-2 sm:px-3 lg:px-4 py-2 sm:py-2.5 lg:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
          'border-0 shadow-none backdrop-blur-none',
          activeTab === tab.id
            ? (tab.color === 'blue' ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]' :
               tab.color === 'green' ? 'bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 scale-105 shadow-[0_0_8px_rgba(34,197,94,0.2)]' :
               tab.color === 'purple' ? 'bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 scale-105 shadow-[0_0_8px_rgba(147,51,234,0.2)]' :
               'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]')
            : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
        ]"
        style="backdrop-filter: blur(0px);"
      >
        <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
          <div :class="[
            'p-1.5 sm:p-2 rounded-full transition-all duration-500',
            activeTab === tab.id
              ? (tab.color === 'blue' ? 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]' :
                 tab.color === 'green' ? 'bg-green-500/30 shadow-[0_0_5px_rgba(34,197,94,0.3)]' :
                 tab.color === 'purple' ? 'bg-purple-500/30 shadow-[0_0_5px_rgba(147,51,234,0.3)]' :
                 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]')
              : 'bg-white/5 group-hover:bg-white/10'
          ]">
            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              <path v-else-if="tab.id === 'companies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              <path v-else-if="tab.id === 'research'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <span class="font-semibold tracking-wide">{{ tab.label }}</span>
        </div>
        <div v-if="activeTab === tab.id" :class="tab.color === 'blue' ? 'absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full' :
                                                  tab.color === 'green' ? 'absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-green-400/5 rounded-full' :
                                                  tab.color === 'purple' ? 'absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/5 rounded-full' :
                                                  'absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full'"></div>
      </button>
    </div>

    <!-- Tablet Navigation (sm to lg): Show visible tabs + overflow menu -->
    <div class="hidden sm:flex lg:hidden items-center space-x-2 sm:space-x-3 relative">
      <button
        v-for="tab in visibleTabs"
        :key="tab.id"
        @click="$emit('tab-selected', tab.id)"
        :class="[
          'group relative flex items-center space-x-1 sm:space-x-2 px-2 sm:px-3 py-2 sm:py-2.5 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
          'border-0 shadow-none backdrop-blur-none',
          activeTab === tab.id
            ? (tab.color === 'blue' ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]' :
               tab.color === 'green' ? 'bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 scale-105 shadow-[0_0_8px_rgba(34,197,94,0.2)]' :
               tab.color === 'purple' ? 'bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 scale-105 shadow-[0_0_8px_rgba(147,51,234,0.2)]' :
               'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]')
            : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
        ]"
        style="backdrop-filter: blur(0px);"
      >
        <div class="relative z-10 flex items-center space-x-1 sm:space-x-2">
          <div :class="[
            'p-1.5 sm:p-2 rounded-full transition-all duration-500',
            activeTab === tab.id
              ? (tab.color === 'blue' ? 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]' :
                 tab.color === 'green' ? 'bg-green-500/30 shadow-[0_0_5px_rgba(34,197,94,0.3)]' :
                 tab.color === 'purple' ? 'bg-purple-500/30 shadow-[0_0_5px_rgba(147,51,234,0.3)]' :
                 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]')
              : 'bg-white/5 group-hover:bg-white/10'
          ]">
            <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="tab.id === 'overview'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              <path v-else-if="tab.id === 'companies'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              <path v-else-if="tab.id === 'research'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <span class="font-semibold tracking-wide">{{ tab.label }}</span>
        </div>
        <div v-if="activeTab === tab.id" :class="tab.color === 'blue' ? 'absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full' :
                                                  tab.color === 'green' ? 'absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-green-400/5 rounded-full' :
                                                  tab.color === 'purple' ? 'absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/5 rounded-full' :
                                                  'absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full'"></div>
      </button>

      <!-- Overflow Menu for Tablet -->
      <OverflowMenu
        :hidden-tabs="hiddenTabs"
        :active-tab="activeTab"
        @tab-selected="$emit('tab-selected', $event)"
      />
    </div>

    <!-- Mobile Navigation (<sm): Hamburger menu -->
    <div class="flex sm:hidden items-center">
      <HamburgerMenu
        :all-tabs="allTabs"
        :active-tab="activeTab"
        :user="user"
        :can-access-admin="canAccessAdmin"
        @tab-selected="$emit('tab-selected', $event)"
        @search="$emit('mobile-search', $event)"
      />
    </div>

    <!-- Search Component (Desktop and Tablet only) -->
    <SearchInterface
      ref="searchInterface"
      :is-authenticated="user !== null"
      @search-performed="$emit('search-performed', $event)"
      @result-selected="$emit('search-result-selected', $event)"
      @search-cleared="$emit('search-cleared')"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import OverflowMenu from '@/Components/Navigation/OverflowMenu.vue'
import HamburgerMenu from '@/Components/Navigation/HamburgerMenu.vue'
import SearchInterface from '@/Components/SearchInterface.vue'

const props = defineProps({
  allTabs: {
    type: Array,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  },
  windowWidth: {
    type: Number,
    required: true
  },
  user: {
    type: Object,
    default: null
  },
  canAccessAdmin: {
    type: Boolean,
    default: false
  }
})

defineEmits([
  'tab-selected',
  'mobile-search',
  'search-performed',
  'search-result-selected',
  'search-cleared'
])

const visibleTabs = computed(() => {
  // Show first 2 tabs on mobile/tablet, all tabs on desktop
  if (props.windowWidth >= 1024) {
    return props.allTabs // Show all tabs on desktop (lg+)
  }
  return props.allTabs.slice(0, 2) // Show first 2 tabs on mobile/tablet
})

const hiddenTabs = computed(() => {
  // Show remaining tabs in overflow menu on mobile/tablet
  if (props.windowWidth >= 1024) {
    return [] // No hidden tabs on desktop
  }
  return props.allTabs.slice(2) // Show remaining tabs in overflow menu on mobile/tablet
})
</script>

<style scoped>
/* Component-specific styles if needed */
</style>