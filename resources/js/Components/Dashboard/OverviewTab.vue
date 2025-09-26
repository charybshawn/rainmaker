<template>
  <div class="space-y-6">
    <!-- Overview Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
        </svg>
        Company Overview
      </h3>

      <div class="flex items-center space-x-3">
        <!-- View Mode Toggle -->
        <div class="flex bg-white/10 rounded-lg p-1">
          <button
            @click="viewMode = 'cards'"
            :class="[
              'px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200',
              viewMode === 'cards'
                ? 'bg-blue-500/30 text-blue-200 shadow-sm'
                : 'text-gray-300 hover:text-white hover:bg-white/10'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v6a2 2 0 002 2"></path>
            </svg>
          </button>
          <button
            @click="viewMode = 'list'"
            :class="[
              'px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200',
              viewMode === 'list'
                ? 'bg-blue-500/30 text-blue-200 shadow-sm'
                : 'text-gray-300 hover:text-white hover:bg-white/10'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
          </button>
        </div>

        <!-- Add Company Button -->
        <button
          v-if="$page.props.auth.user"
          @click="$emit('show-create-modal')"
          class="group relative px-4 py-2 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 rounded-lg shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl text-sm font-medium"
          style="backdrop-filter: blur(20px) saturate(150%);"
        >
          <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Add Company
        </button>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white/5 rounded-lg p-4 border border-white/10">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-gray-400">Total Companies</p>
            <p class="text-lg font-semibold text-white">{{ companies.length }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/5 rounded-lg p-4 border border-white/10">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-gray-400">Total Market Cap</p>
            <p class="text-lg font-semibold text-white">{{ formatTotalMarketCap() }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white/5 rounded-lg p-4 border border-white/10">
        <div class="flex items-center">
          <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm text-gray-400">Top Sectors</p>
            <p class="text-lg font-semibold text-white">{{ topSectors.length }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Company Grid/List -->
    <div v-if="companies.length > 0">
      <!-- Card View -->
      <div v-if="viewMode === 'cards'" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          v-for="company in companies"
          :key="company.id"
          class="group relative p-6 transition-all duration-500 hover:scale-105 cursor-pointer"
          @click="$emit('view-company', company)"
        >
          <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/10 rounded-2xl border border-blue-400/20"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-3">
              <h4 class="text-lg font-semibold text-white group-hover:text-blue-200 transition-colors">
                {{ company.name }}
              </h4>
              <span class="text-sm font-mono text-blue-400 bg-blue-500/20 px-2 py-1 rounded">
                {{ company.ticker }}
              </span>
            </div>

            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-400">Sector:</span>
                <span class="text-gray-300">{{ company.sector || 'N/A' }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Market Cap:</span>
                <span class="text-gray-300">{{ formatMarketCap(company.market_cap) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-400">Industry:</span>
                <span class="text-gray-300">{{ company.industry || 'N/A' }}</span>
              </div>
            </div>

            <div class="mt-4 flex items-center justify-between">
              <span class="text-xs text-gray-500">
                Added {{ formatDate(company.created_at) }}
              </span>
              <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                <button
                  @click.stop="$emit('quick-note', company)"
                  class="p-2 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 text-blue-200 hover:text-white transition-all duration-200 mr-2"
                  title="Quick note"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button
                  @click.stop="$emit('quick-blog', company)"
                  class="p-2 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-200 hover:text-white transition-all duration-200"
                  title="Quick blog post"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else class="space-y-1">
        <div class="grid grid-cols-12 gap-4 px-4 py-3 text-sm font-medium text-gray-400 border-b border-white/10">
          <div class="col-span-3">Company</div>
          <div class="col-span-2">Ticker</div>
          <div class="col-span-2">Sector</div>
          <div class="col-span-2">Market Cap</div>
          <div class="col-span-2">Industry</div>
          <div class="col-span-1">Actions</div>
        </div>

        <div
          v-for="company in companies"
          :key="company.id"
          class="grid grid-cols-12 gap-4 px-4 py-3 text-sm hover:bg-white/5 transition-colors duration-200 group cursor-pointer"
          @click="$emit('view-company', company)"
        >
          <div class="col-span-3 font-medium text-white group-hover:text-blue-200">
            {{ company.name }}
          </div>
          <div class="col-span-2 font-mono text-blue-400">
            {{ company.ticker }}
          </div>
          <div class="col-span-2 text-gray-300">
            {{ company.sector || 'N/A' }}
          </div>
          <div class="col-span-2 text-gray-300">
            {{ formatMarketCap(company.market_cap) }}
          </div>
          <div class="col-span-2 text-gray-300">
            {{ company.industry || 'N/A' }}
          </div>
          <div class="col-span-1">
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-1">
              <button
                @click.stop="$emit('quick-note', company)"
                class="p-1.5 rounded bg-blue-500/20 hover:bg-blue-500/30 text-blue-200 hover:text-white transition-all duration-200"
                title="Quick note"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click.stop="$emit('quick-blog', company)"
                class="p-1.5 rounded bg-purple-500/20 hover:bg-purple-500/30 text-purple-200 hover:text-white transition-all duration-200"
                title="Quick blog post"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16">
      <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
      </div>
      <h4 class="text-lg font-semibold text-white mb-2">No Companies Yet</h4>
      <p class="text-sm text-gray-300 mb-4">Start building your investment research by adding your first company</p>
      <button
        v-if="$page.props.auth.user"
        @click="$emit('show-create-modal')"
        class="px-6 py-3 bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium rounded-lg transition-colors border border-blue-400/30"
      >
        Add Your First Company
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Props
const props = defineProps({
  companies: {
    type: Array,
    default: () => []
  }
})

// Emits
defineEmits([
  'view-company',
  'show-create-modal',
  'quick-note',
  'quick-blog'
])

// Local state
const viewMode = ref('cards')

// Computed properties
const topSectors = computed(() => {
  const sectorCounts = {}
  props.companies.forEach(company => {
    const sector = company.sector || 'Other'
    sectorCounts[sector] = (sectorCounts[sector] || 0) + 1
  })

  return Object.entries(sectorCounts)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
})

// Methods
const formatMarketCap = (value) => {
  if (!value) return 'N/A'

  if (value >= 1000000000000) {
    return `$${(value / 1000000000000).toFixed(1)}T`
  } else if (value >= 1000000000) {
    return `$${(value / 1000000000).toFixed(1)}B`
  } else {
    return `$${(value / 1000000).toFixed(1)}M`
  }
}

const formatTotalMarketCap = () => {
  const total = props.companies.reduce((sum, company) => {
    return sum + (company.market_cap || 0)
  }, 0)
  return formatMarketCap(total)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>