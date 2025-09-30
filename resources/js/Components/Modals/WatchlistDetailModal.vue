<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="$emit('close')">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

      <!-- Modal panel -->
      <div
        class="relative inline-block align-bottom bg-gradient-to-br from-gray-900/95 via-gray-800/95 to-gray-900/95 backdrop-blur-xl rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-white/20"
        style="backdrop-filter: blur(20px) saturate(150%);"
        @click.stop
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-700/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-6 h-6 mr-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              <div>
                <h3 class="text-lg font-semibold text-white">{{ watchlist?.name }}</h3>
                <p v-if="watchlist?.description" class="text-sm text-gray-400 mt-1">{{ watchlist.description }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="$emit('edit', watchlist)"
                class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
                title="Edit Watchlist"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click="$emit('delete', watchlist)"
                class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                title="Delete Watchlist"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
              <button
                @click="$emit('close')"
                class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Content -->
        <div class="p-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-500/20 text-orange-300 border border-orange-500/30">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ companies?.length || 0 }} {{ (companies?.length === 1) ? 'company' : 'companies' }}
              </span>
              <span class="text-sm text-gray-400">
                Created {{ formatDate(watchlist?.created_at) }}
              </span>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-400"></div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!companies || companies.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-300 mb-2">No companies yet</h3>
            <p class="text-gray-400 mb-6">Start building your watchlist by adding companies from the Companies page.</p>
            <button
              @click="navigateToCompanies"
              class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Browse Companies
            </button>
          </div>

          <!-- Companies List -->
          <div v-else class="space-y-3">
            <div
              v-for="company in companies"
              :key="company.id"
              class="flex items-center justify-between p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-200 cursor-pointer group"
              @click="viewCompany(company)"
            >
              <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-lg flex items-center justify-center">
                    <span class="text-blue-400 font-mono text-sm font-bold">{{ company.ticker }}</span>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <h4 class="text-white font-medium truncate group-hover:text-blue-300 transition-colors duration-200">
                    {{ company.name }}
                  </h4>
                  <div class="flex items-center gap-4 mt-1">
                    <span class="text-sm text-gray-400">{{ company.sector || 'Unknown Sector' }}</span>
                    <span v-if="company.market_cap" class="text-sm text-gray-400">
                      {{ formatMarketCap(company.market_cap) }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-2">
                <span v-if="company.pivot?.added_at" class="text-xs text-gray-500">
                  Added {{ formatDate(company.pivot.added_at) }}
                </span>
                <button
                  @click.stop="removeCompany(company)"
                  class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200 opacity-0 group-hover:opacity-100"
                  title="Remove from watchlist"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-700/50">
          <div class="flex items-center justify-between">
            <button
              @click="navigateToCompanies"
              class="inline-flex items-center px-4 py-2 bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium rounded-lg transition-all duration-200 border border-white/20"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add More Companies
            </button>
            <button
              @click="$emit('close')"
              class="px-6 py-2 bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium rounded-lg transition-all duration-200"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  watchlist: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'edit', 'delete'])

// Reactive state
const companies = ref([])
const loading = ref(false)

// Watch for watchlist changes to fetch companies
watch(() => props.watchlist, async (newWatchlist) => {
  if (newWatchlist && props.show) {
    await fetchWatchlistDetails()
  }
}, { immediate: true })

watch(() => props.show, async (show) => {
  if (show && props.watchlist) {
    await fetchWatchlistDetails()
  }
})

// Methods
const fetchWatchlistDetails = async () => {
  if (!props.watchlist?.id) return

  try {
    loading.value = true
    const response = await axios.get(`/api/watchlists/${props.watchlist.id}`)
    companies.value = response.data.watchlist.companies || []
  } catch (error) {
    console.error('Error fetching watchlist details:', error)
    companies.value = []
  } finally {
    loading.value = false
  }
}

const removeCompany = async (company) => {
  if (!confirm(`Remove ${company.name} (${company.ticker}) from this watchlist?`)) {
    return
  }

  try {
    await axios.delete(`/api/watchlists/${props.watchlist.id}/companies/${company.id}`)
    companies.value = companies.value.filter(c => c.id !== company.id)

    // Update the watchlist object if it has a companies_count property
    if (props.watchlist.companies_count !== undefined) {
      props.watchlist.companies_count = Math.max(0, props.watchlist.companies_count - 1)
    }
  } catch (error) {
    console.error('Error removing company from watchlist:', error)
    alert('Failed to remove company from watchlist. Please try again.')
  }
}

const viewCompany = (company) => {
  router.visit(`/companies/${company.ticker}`)
}

const navigateToCompanies = () => {
  router.visit('/dashboard/companies')
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatMarketCap = (marketCap) => {
  if (!marketCap) return 'N/A'

  const value = parseFloat(marketCap)
  if (value >= 1000000000000) {
    return '$' + (value / 1000000000000).toFixed(1) + 'T'
  } else if (value >= 1000000000) {
    return '$' + (value / 1000000000).toFixed(1) + 'B'
  } else {
    return '$' + (value / 1000000).toFixed(1) + 'M'
  }
}
</script>