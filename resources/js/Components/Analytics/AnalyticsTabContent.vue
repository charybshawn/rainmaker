<template>
  <!-- Analytics Tab -->
  <div v-if="!showSearchResults && activeTab === 'analytics'" class="space-y-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white flex items-center">
        <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
        </svg>
        Portfolio Analytics
      </h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Sector Distribution -->
      <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
        <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
          </svg>
          Sector Distribution
        </h4>
        <div class="space-y-3">
          <div
            v-for="sector in topSectors"
            :key="sector.name"
            class="flex items-center justify-between p-3 bg-white/10 rounded-lg"
          >
            <div>
              <p class="text-sm font-medium text-white">{{ sector.name || 'Other' }}</p>
              <p class="text-xs text-gray-400">{{ sector.count }} companies</p>
            </div>
            <div class="text-right">
              <div class="text-sm font-medium text-orange-400">{{ Math.round((sector.count / totalCompanies) * 100) }}%</div>
              <div class="w-16 bg-gray-700 rounded-full h-2 mt-1">
                <div class="bg-orange-400 h-2 rounded-full" :style="`width: ${(sector.count / totalCompanies) * 100}%`"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Market Cap Analysis -->
      <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
        <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
          </svg>
          Market Cap Breakdown
        </h4>
        <div class="space-y-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-white mb-2">{{ formattedTotalMarketCap }}</div>
            <div class="text-sm text-gray-400">Total Portfolio Value</div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-center">
            <div class="p-3 bg-white/10 rounded-lg">
              <div class="text-lg font-semibold text-green-400">{{ largeCapCount }}</div>
              <div class="text-xs text-gray-400">Large Cap (>$10B)</div>
            </div>
            <div class="p-3 bg-white/10 rounded-lg">
              <div class="text-lg font-semibold text-blue-400">{{ midCapCount }}</div>
              <div class="text-xs text-gray-400">Mid Cap ($2B-$10B)</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions Tab -->
  <div v-if="!showSearchResults && activeTab === 'actions'" class="space-y-6">
    <div class="mb-6">
      <h3 class="text-xl font-bold text-white flex items-center">
        <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
        </svg>
        Quick Actions Center
      </h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Primary Actions -->
      <div class="space-y-4">
        <h4 class="text-lg font-semibold text-white mb-4">Primary Actions</h4>
        <button
          v-if="user"
          @click="$emit('show-quick-blog-modal')"
          class="w-full bg-green-500/30 hover:bg-green-500/50 text-green-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-green-500/30 hover:shadow-lg hover:scale-105"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          Share Market Insight
        </button>

        <button
          v-if="user"
          @click="$emit('open-create-modal')"
          class="w-full bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-blue-500/30 hover:shadow-lg hover:scale-105"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Add New Company
        </button>
      </div>

      <!-- Navigation Actions -->
      <div class="space-y-4">
        <h4 class="text-lg font-semibold text-white mb-4">Navigation</h4>
        <Link
          :href="route('blog.index')"
          class="w-full bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-purple-500/30 hover:shadow-lg hover:scale-105"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          My Blog Dashboard
        </Link>

        <button
          @click="$emit('switch-tab', 'analytics')"
          class="w-full bg-orange-500/30 hover:bg-orange-500/50 text-orange-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-orange-500/30 hover:shadow-lg hover:scale-105"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
          </svg>
          View Analytics
        </button>
      </div>

      <!-- Quick Switches -->
      <div class="space-y-4">
        <h4 class="text-lg font-semibold text-white mb-4">Quick Switch</h4>

        <button
          @click="$emit('switch-tab', 'insights')"
          class="w-full bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-purple-500/30 hover:shadow-lg hover:scale-105"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
          </svg>
          Browse Insights
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  // Tab state
  showSearchResults: {
    type: Boolean,
    default: false
  },
  activeTab: {
    type: String,
    required: true
  },

  // Analytics data
  topSectors: {
    type: Array,
    default: () => []
  },
  totalCompanies: {
    type: Number,
    default: 0
  },
  formattedTotalMarketCap: {
    type: String,
    default: '$0'
  },
  largeCapCount: {
    type: Number,
    default: 0
  },
  midCapCount: {
    type: Number,
    default: 0
  },

  // User data
  user: {
    type: Object,
    default: null
  },

  // Utility functions
  route: {
    type: Function,
    required: true
  }
})

defineEmits([
  'show-quick-blog-modal',
  'open-create-modal',
  'switch-tab'
])
</script>

<style scoped>
/* Component-specific styles if needed */
</style>