<template>
  <div v-if="!searchState.showResults && activeTab === 'companies'" class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h3 class="text-2xl font-bold text-white flex items-center">
          <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          Company Research Database
        </h3>
        <p class="text-gray-300 mt-2">{{ companiesFiltered.length }} companies with {{ totalCompanyResearchItems }} research items</p>
      </div>

      <!-- Add Company Button -->
      <button
        v-if="user"
        @click="$emit('create-company')"
        class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium"
        style="backdrop-filter: blur(20px) saturate(150%);"
      >
        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Add Company
      </button>
    </div>

    <!-- Filters and Search -->
    <div class="flex flex-col lg:flex-row gap-4 mb-8">
      <!-- Search -->
      <div class="flex-1">
        <div class="relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input
            :value="searchQuery"
            @input="$emit('update-search-query', $event.target.value)"
            type="text"
            placeholder="Search companies by name, symbol, or sector..."
            class="w-full pl-10 pr-4 py-3 rounded-xl border border-white/20 text-white placeholder-gray-400 transition-all duration-500 bg-black/10"
            style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
          />
        </div>
      </div>

      <!-- Sector Filter -->
      <div class="w-full lg:w-48">
        <select
          :value="selectedSector"
          @change="$emit('update-selected-sector', $event.target.value)"
          class="w-full py-3 px-4 rounded-xl border border-white/20 text-white transition-all duration-500 bg-black/10"
          style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
        >
          <option value="">All Sectors</option>
          <option v-for="sector in sectors" :key="sector" :value="sector">{{ sector }}</option>
        </select>
      </div>

      <!-- Sort By -->
      <div class="w-full lg:w-48">
        <select
          :value="sortBy"
          @change="$emit('update-sort-by', $event.target.value)"
          class="w-full py-3 px-4 rounded-xl border border-white/20 text-white transition-all duration-500 bg-black/10"
          style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
        >
          <option value="name">Name (A-Z)</option>
          <option value="symbol">Symbol (A-Z)</option>
          <option value="market_cap">Market Cap (High-Low)</option>
          <option value="research_count">Research Count (High-Low)</option>
          <option value="created_at">Recently Added</option>
        </select>
      </div>
    </div>

    <!-- Companies List -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto"></div>
      <p class="text-gray-300 mt-3">Loading companies...</p>
    </div>

    <div v-else-if="paginatedCompanies.length === 0" class="text-center py-16">
      <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
      <h4 class="text-xl font-semibold text-white mb-2">No Companies Found</h4>
      <p class="text-gray-300 mb-6">{{ searchQuery || selectedSector ? 'Try adjusting your filters' : 'Start by adding your first company' }}</p>
      <button
        v-if="user && !searchQuery && !selectedSector"
        @click="$emit('create-company')"
        class="px-6 py-3 bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium rounded-lg transition-colors border border-blue-400/30"
      >
        Add Your First Company
      </button>
    </div>

    <!-- Companies Table -->
    <div v-else data-infinite-companies>
      <!-- Mobile Simple List View -->
      <div class="sm:hidden space-y-3">
        <div
          v-for="company in infiniteCompanies"
          :key="company.id"
          class="p-3 rounded-xl border border-white/20 bg-gradient-to-br from-black/20 via-black/10 to-transparent backdrop-blur-xl cursor-pointer hover:bg-black/20 transition-all duration-300"
          style="backdrop-filter: blur(20px) saturate(180%);"
          @click="$emit('navigate-to-company', company)"
        >
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2 min-w-0 flex-1">
              <div class="w-10 h-8 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded flex items-center justify-center flex-shrink-0">
                <span class="text-blue-300 font-bold text-xs">{{ (company.ticker || '').substring(0, 3) }}</span>
              </div>
              <div class="min-w-0 flex-1">
                <h4 class="text-white font-medium truncate text-sm">{{ company.name }}</h4>
              </div>
            </div>
            <div class="text-xs text-gray-400 flex-shrink-0 ml-2">
              {{ company.researchItemsCount || 0 }}
            </div>
          </div>
        </div>
      </div>

      <!-- Bulk Actions Toolbar -->
      <div v-if="selectedCompanies.length > 0" class="hidden sm:flex items-center justify-between p-4 mb-4 rounded-xl bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-400/30 backdrop-blur-xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <div class="flex items-center space-x-3">
          <span class="text-white font-medium">{{ selectedCompanies.length }} companies selected</span>
          <button @click="$emit('clear-selection')" class="text-blue-300 hover:text-white transition-colors text-sm">
            Clear selection
          </button>
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="$emit('bulk-delete-companies')"
            class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-300 hover:text-white rounded-lg transition-all duration-300 flex items-center space-x-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            <span>Delete Selected</span>
          </button>
        </div>
      </div>

      <!-- Desktop Table View -->
      <div class="hidden sm:block overflow-hidden rounded-2xl border border-white/20 bg-gradient-to-br from-black/20 via-black/10 to-transparent backdrop-blur-xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-white/20 bg-black/10">
          <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium text-gray-300">
            <div class="col-span-1 flex items-center justify-center">
              <input
                type="checkbox"
                :checked="isAllSelected"
                :indeterminate="isIndeterminate"
                @change="$emit('toggle-select-all')"
                class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-2"
              />
            </div>
            <div class="col-span-3">Company</div>
            <div class="col-span-2">Sector</div>
            <div class="col-span-2">Market Cap</div>
            <div class="col-span-1 text-center">Research</div>
            <div class="col-span-1 text-center">Assets</div>
            <div class="col-span-2 text-center">Actions</div>
          </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-white/20">
          <div
            v-for="company in infiniteCompanies"
            :key="company.id"
            class="group px-6 py-4 hover:bg-black/10 transition-all duration-300 border-b border-white/10 hover:border-white/20 last:border-b-0"
            :class="{ 'bg-blue-500/10': selectedCompanies.includes(company.id) }"
          >
            <div class="grid grid-cols-12 gap-4 items-center">
              <!-- Checkbox -->
              <div class="col-span-1 flex items-center justify-center">
                <input
                  type="checkbox"
                  :checked="selectedCompanies.includes(company.id)"
                  @change="$emit('toggle-company-selection', company.id)"
                  @click.stop
                  class="w-4 h-4 rounded border-gray-600 bg-gray-700 text-blue-500 focus:ring-blue-500 focus:ring-2"
                />
              </div>

              <!-- Company Info -->
              <div class="col-span-3 flex items-center space-x-3 cursor-pointer" @click="$emit('navigate-to-company', company)">
                <!-- Stock Ticker -->
                <div class="w-16 h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-lg flex items-center justify-center shadow-[0_0_8px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_12px_rgba(59,130,246,0.25)] transition-all duration-300">
                  <span class="text-blue-300 font-bold text-sm">{{ company.ticker || 'N/A' }}</span>
                </div>

                <!-- Company Details -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <h4 class="text-lg font-semibold text-white group-hover:text-blue-200 transition-colors truncate">{{ company.name }}</h4>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-gray-400 text-sm truncate">{{ company.industry || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Sector -->
              <div class="col-span-2">
                <span class="text-white font-medium truncate">{{ company.sector || 'N/A' }}</span>
              </div>

              <!-- Market Cap -->
              <div class="col-span-2">
                <span class="text-white font-medium">{{ formatMarketCap(company.marketCap) }}</span>
              </div>

              <!-- Research Count -->
              <div class="col-span-1 text-center">
                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500/20 border border-green-400/20">
                  <span class="text-green-300 font-bold text-sm">{{ company.research_items_count || 0 }}</span>
                </div>
              </div>

              <!-- Assets Count -->
              <div class="col-span-1 text-center">
                <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-500/20 border border-purple-400/20">
                  <span class="text-purple-300 font-bold text-sm">{{ company.documents_count || 0 }}</span>
                </div>
              </div>

              <!-- Actions -->
              <div class="col-span-2 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                  @click.stop="$emit('navigate-to-company', company)"
                  class="w-8 h-8 rounded-lg bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                  title="View Details"
                >
                  <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>

                <button
                  v-if="user"
                  @click.stop="$emit('view-company-research', company)"
                  class="w-8 h-8 rounded-lg bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                  title="Add Research"
                >
                  <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Infinite Scroll Loading Indicator -->
      <div v-if="showLoading" class="flex items-center justify-center space-x-3 mt-8 py-6">
        <div class="w-6 h-6 border-2 border-blue-300/30 border-t-blue-300 rounded-full animate-spin"></div>
        <span class="text-blue-300 text-sm font-medium">Loading more companies...</span>
      </div>

      <!-- Initial Loading State -->
      <div v-if="initialLoading" class="flex items-center justify-center space-x-3 mt-8 py-12">
        <div class="w-8 h-8 border-2 border-blue-300/30 border-t-blue-300 rounded-full animate-spin"></div>
        <span class="text-blue-300 text-base font-medium">Loading companies...</span>
      </div>

      <!-- Results Summary -->
      <div v-if="!initialLoading && infiniteCompanies.length > 0" class="text-center text-gray-400 text-sm mt-4">
        Showing {{ infiniteCompanies.length }} companies
        <span v-if="hasMore">(scroll for more)</span>
      </div>

      <!-- End of Results -->
      <div v-if="!hasMore && infiniteCompanies.length > 0" class="text-center text-gray-500 text-sm mt-6 py-4">
        <span class="inline-block px-4 py-2 rounded-full bg-gray-800/50 border border-gray-600/30">
          ✨ You've reached the end
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  searchState: {
    type: Object,
    required: true
  },
  activeTab: {
    type: String,
    required: true
  },
  companiesFiltered: {
    type: Array,
    default: () => []
  },
  totalCompanyResearchItems: {
    type: Number,
    default: 0
  },
  user: {
    type: Object,
    default: null
  },
  searchQuery: {
    type: String,
    default: ''
  },
  selectedSector: {
    type: String,
    default: ''
  },
  sortBy: {
    type: String,
    default: 'name'
  },
  sectors: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  },
  paginatedCompanies: {
    type: Array,
    default: () => []
  },
  infiniteCompanies: {
    type: Array,
    default: () => []
  },
  selectedCompanies: {
    type: Array,
    default: () => []
  },
  showLoading: {
    type: Boolean,
    default: false
  },
  initialLoading: {
    type: Boolean,
    default: false
  },
  hasMore: {
    type: Boolean,
    default: false
  }
})

defineEmits([
  'create-company',
  'update-search-query',
  'update-selected-sector',
  'update-sort-by',
  'navigate-to-company',
  'clear-selection',
  'bulk-delete-companies',
  'toggle-select-all',
  'toggle-company-selection',
  'view-company-research'
])

const isAllSelected = computed(() => {
  return props.companiesFiltered.length > 0 &&
         props.selectedCompanies.length === props.companiesFiltered.length
})

const isIndeterminate = computed(() => {
  return props.selectedCompanies.length > 0 &&
         props.selectedCompanies.length < props.companiesFiltered.length
})

const formatMarketCap = (value) => {
  if (!value || value === 0) {
    return 'N/A'
  }

  const numericValue = typeof value === 'string' ? parseFloat(value) : value

  if (numericValue >= 1000000000000) {
    return `$${(numericValue / 1000000000000).toFixed(1)}T`
  } else if (numericValue >= 1000000000) {
    return `$${(numericValue / 1000000000).toFixed(1)}B`
  } else if (numericValue >= 1000000) {
    return `$${(numericValue / 1000000).toFixed(1)}M`
  }
}
</script>

<style scoped>
/* Component-specific styles if needed */
</style>