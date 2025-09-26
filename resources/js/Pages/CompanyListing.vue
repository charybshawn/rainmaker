<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 transition-colors relative">
    <!-- Canvas Shooting Stars Background -->
    <canvas
      ref="starsCanvas"
      class="fixed inset-0 w-full h-full pointer-events-none"
      style="z-index: 1;"
    ></canvas>

    <!-- Header Section -->
    <div class="relative z-10">
      <div class="w-[95%] sm:w-[90%] lg:w-[80%] mx-auto px-4 sm:px-6 py-3 sm:py-4 lg:py-6">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-4 sm:mb-6 lg:mb-8">
          <div class="flex items-center gap-4">
            <!-- Back Button -->
            <button
              @click="goBack"
              class="group relative p-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 backdrop-blur-xl"
              style="backdrop-filter: blur(20px) saturate(150%);"
              title="Back to Dashboard"
            >
              <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
            </button>

            <!-- Rainmaker Logo -->
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

            <!-- User Menu -->
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
                  <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                </template>
              </Dropdown>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="w-[95%] sm:w-[90%] lg:w-[80%] mx-auto px-4 sm:px-6 py-3 sm:py-4 lg:py-6 -mt-12">
      <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl sm:rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 p-4 sm:p-6 lg:p-8 relative" style="backdrop-filter: blur(20px) saturate(180%);">

        <!-- Page Header -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <h1 class="text-3xl font-bold text-white flex items-center">
              <svg class="w-8 h-8 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              Company Research Database
            </h1>
            <p class="text-gray-300 mt-2">{{ filteredCompanies.length }} companies with {{ totalResearchItems }} research items</p>
          </div>

          <!-- Add Company Button -->
          <button
            v-if="$page.props.auth.user"
            @click="showCreateCompanyModal = true"
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
                v-model="searchQuery"
                type="text"
                placeholder="Search companies by name, symbol, or sector..."
                class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/10 dark:bg-white/5 backdrop-blur-xl border border-white/20 dark:border-white/10 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-200"
                style="backdrop-filter: blur(20px) saturate(150%);"
              />
            </div>
          </div>

          <!-- Sector Filter -->
          <div class="w-full lg:w-48">
            <select
              v-model="selectedSector"
              class="w-full py-3 px-4 rounded-xl bg-white/10 dark:bg-white/5 backdrop-blur-xl border border-white/20 dark:border-white/10 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-200"
              style="backdrop-filter: blur(20px) saturate(150%);"
            >
              <option value="">All Sectors</option>
              <option v-for="sector in sectors" :key="sector" :value="sector">{{ sector }}</option>
            </select>
          </div>

          <!-- Sort By -->
          <div class="w-full lg:w-48">
            <select
              v-model="sortBy"
              class="w-full py-3 px-4 rounded-xl bg-white/10 dark:bg-white/5 backdrop-blur-xl border border-white/20 dark:border-white/10 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-200"
              style="backdrop-filter: blur(20px) saturate(150%);"
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
          <h3 class="text-xl font-semibold text-white mb-2">No Companies Found</h3>
          <p class="text-gray-300 mb-6">{{ searchQuery || selectedSector ? 'Try adjusting your filters' : 'Start by adding your first company' }}</p>
          <button
            v-if="$page.props.auth.user && !searchQuery && !selectedSector"
            @click="showCreateCompanyModal = true"
            class="px-6 py-3 bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium rounded-lg transition-colors border border-blue-400/30"
          >
            Add Your First Company
          </button>
        </div>

        <div v-else class="space-y-4">
          <!-- Company Card -->
          <div
            v-for="company in filteredCompanies"
            :key="company.id"
            class="group relative bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:border-blue-400/30 transition-all duration-300 cursor-pointer hover:scale-[1.02] hover:shadow-[0_8px_32px_0_rgba(59,130,246,0.15)]"
            style="backdrop-filter: blur(20px) saturate(180%);"
            @click="viewCompanyDetails(company)"
          >
            <div class="flex items-start justify-between">
              <!-- Company Info -->
              <div class="flex items-start space-x-4 flex-1">
                <!-- Company Icon -->
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-xl flex items-center justify-center shadow-[0_0_10px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] transition-all duration-500">
                  <span class="text-blue-300 font-bold text-lg">{{ company.symbol.substring(0, 2) }}</span>
                </div>

                <!-- Company Details -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-3 mb-2">
                    <h3 class="text-xl font-semibold text-white group-hover:text-blue-200 transition-colors">{{ company.name }}</h3>
                    <span class="px-3 py-1 bg-blue-500/20 text-blue-300 text-sm font-medium rounded-full border border-blue-400/20">{{ company.symbol }}</span>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                      <p class="text-gray-400">Sector</p>
                      <p class="text-white font-medium">{{ company.sector || 'N/A' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-400">Industry</p>
                      <p class="text-white font-medium truncate">{{ company.industry || 'N/A' }}</p>
                    </div>
                    <div>
                      <p class="text-gray-400">Market Cap</p>
                      <p class="text-white font-medium">{{ formatMarketCap(company.marketCap) }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Research Breakdown -->
              <div class="flex items-center space-x-6 text-center">
                <!-- Research Items Count -->
                <div class="text-center">
                  <div class="text-2xl font-bold text-green-300">{{ company.research_items_count || 0 }}</div>
                  <div class="text-xs text-gray-400">Research Items</div>
                </div>

                <!-- Research Assets Count -->
                <div class="text-center">
                  <div class="text-2xl font-bold text-purple-300">{{ company.documents_count || 0 }}</div>
                  <div class="text-xs text-gray-400">Assets</div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    @click.stop="viewCompanyDetails(company)"
                    class="w-10 h-10 rounded-lg bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                    title="View Details"
                  >
                    <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>

                  <button
                    v-if="$page.props.auth.user"
                    @click.stop="openQuickBlogWithCompany(company)"
                    class="w-10 h-10 rounded-lg bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                    title="Add Research"
                  >
                    <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Results Summary -->
        <div class="text-center text-gray-400 text-sm mt-8 pt-6 border-t border-white/10">
          {{ filteredCompanies.length }} {{ filteredCompanies.length === 1 ? 'company' : 'companies' }} found
        </div>
      </div>
    </div>


    <!-- Create Company Modal -->
    <CreateCompanyModal
      :show="showCreateCompanyModal"
      @close="showCreateCompanyModal = false"
      @created="handleCompanyCreated"
    />

    <!-- Quick Blog Modal -->
    <QuickBlogModal
      :show="showQuickBlogModal"
      :preselected-company="quickBlogCompany"
      @close="closeQuickBlogModal"
      @posted="handleBlogPosted"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import axios from 'axios'

// Dark mode
const { isDarkMode, toggleDarkMode, initializeDarkMode } = useDarkMode()

// Data
const companies = ref([])
const loading = ref(true)
const starsCanvas = ref(null)

// Search and filtering
const searchQuery = ref('')
const selectedSector = ref('')
const sortBy = ref('name')


// Modals
const showCreateCompanyModal = ref(false)
const showQuickBlogModal = ref(false)
const quickBlogCompany = ref(null)

// Computed properties
const sectors = computed(() => {
  const uniqueSectors = [...new Set(companies.value.map(c => c.sector).filter(Boolean))]
  return uniqueSectors.sort()
})

const filteredCompanies = computed(() => {
  let filtered = companies.value

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(company =>
      company.name.toLowerCase().includes(query) ||
      company.symbol.toLowerCase().includes(query) ||
      (company.sector && company.sector.toLowerCase().includes(query)) ||
      (company.industry && company.industry.toLowerCase().includes(query))
    )
  }

  // Sector filter
  if (selectedSector.value) {
    filtered = filtered.filter(company => company.sector === selectedSector.value)
  }

  // Sort
  filtered.sort((a, b) => {
    switch (sortBy.value) {
      case 'name':
        return a.name.localeCompare(b.name)
      case 'symbol':
        return a.symbol.localeCompare(b.symbol)
      case 'market_cap':
        return (parseFloat(b.marketCap) || 0) - (parseFloat(a.marketCap) || 0)
      case 'research_count':
        return (b.research_items_count || 0) - (a.research_items_count || 0)
      case 'created_at':
        return new Date(b.created_at) - new Date(a.created_at)
      default:
        return 0
    }
  })

  return filtered
})


const totalResearchItems = computed(() => {
  return companies.value.reduce((sum, company) => {
    return sum + (company.research_items_count || 0) + (company.documents_count || 0)
  }, 0)
})

// Methods
const fetchCompanies = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/companies', {
      params: {
        include_counts: true,
        limit: 1000 // High limit to get all companies for infinite scroll
      }
    })
    companies.value = response.data.data
  } catch (error) {
    console.error('Error fetching companies:', error)
  } finally {
    loading.value = false
  }
}

const formatMarketCap = (marketCap) => {
  if (!marketCap) return 'N/A'

  // Convert string to number
  const numericMarketCap = parseFloat(marketCap)
  if (!numericMarketCap) return 'N/A'

  if (numericMarketCap >= 1e12) {
    return `$${(numericMarketCap / 1e12).toFixed(1)}T`
  } else if (numericMarketCap >= 1e9) {
    return `$${(numericMarketCap / 1e9).toFixed(1)}B`
  } else if (numericMarketCap >= 1e6) {
    return `$${(numericMarketCap / 1e6).toFixed(1)}M`
  } else {
    return `$${numericMarketCap.toLocaleString()}`
  }
}

const navigateToCompany = (company) => {
  if (company.ticker) {
    router.visit(route('company.profile', { ticker: company.ticker }))
  } else {
    console.error('Company ticker not available for navigation')
  }
}

const viewCompanyDetails = (company) => {
  // Redirect to new page instead of modal
  navigateToCompany(company)
}


const openQuickBlogWithCompany = (company) => {
  quickBlogCompany.value = company
  showQuickBlogModal.value = true
}

const closeQuickBlogModal = () => {
  showQuickBlogModal.value = false
  quickBlogCompany.value = null
}

const handleCompanyCreated = (newCompany) => {
  companies.value.unshift(newCompany)
}

const handleBlogPosted = () => {
  // Refresh companies to update research counts
  fetchCompanies()
}

const goBack = () => {
  router.visit('/')
}

// Initialize shooting stars animation
const initializeStars = () => {
  if (!starsCanvas.value) return

  const canvas = starsCanvas.value
  const ctx = canvas.getContext('2d')

  const resizeCanvas = () => {
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
  }

  resizeCanvas()
  window.addEventListener('resize', resizeCanvas)

  const stars = []
  for (let i = 0; i < 150; i++) {
    stars.push({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      radius: Math.random() * 2,
      vx: (Math.random() - 0.5) * 0.5,
      vy: (Math.random() - 0.5) * 0.5,
      alpha: Math.random() * 0.8 + 0.2
    })
  }

  const animate = () => {
    ctx.clearRect(0, 0, canvas.width, canvas.height)

    stars.forEach(star => {
      ctx.save()
      ctx.globalAlpha = star.alpha
      ctx.beginPath()
      ctx.arc(star.x, star.y, star.radius, 0, Math.PI * 2)
      ctx.fillStyle = '#ffffff'
      ctx.fill()
      ctx.restore()

      star.x += star.vx
      star.y += star.vy

      if (star.x < 0) star.x = canvas.width
      if (star.x > canvas.width) star.x = 0
      if (star.y < 0) star.y = canvas.height
      if (star.y > canvas.height) star.y = 0
    })

    requestAnimationFrame(animate)
  }

  animate()
}


// Lifecycle
onMounted(() => {
  initializeDarkMode()
  fetchCompanies()
  initializeStars()
})
</script>