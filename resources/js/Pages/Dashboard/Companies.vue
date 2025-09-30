<template>
  <DashboardLayout active-tab="companies">
    <template #title>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
            <svg class="w-10 h-10 mr-4 text-blue-400 drop-shadow-[0_0_8px_rgba(59,130,246,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Companies
          </h2>
          <p class="text-gray-300 mt-2 ml-14">{{ filteredCompanies.length }} companies with {{ totalResearchItems }} research items</p>
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
    </template>

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

      <!-- View Toggle -->
      <div class="flex items-center gap-1">
        <button
          @click="viewMode = 'cards'"
          :class="[
            'p-3 rounded-lg transition-all duration-200',
            viewMode === 'cards'
              ? 'text-white bg-white/10'
              : 'text-gray-500 hover:text-gray-300'
          ]"
          title="Card View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
          </svg>
        </button>
        <button
          @click="viewMode = 'table'"
          :class="[
            'p-3 rounded-lg transition-all duration-200',
            viewMode === 'table'
              ? 'text-white bg-white/10'
              : 'text-gray-500 hover:text-gray-300'
          ]"
          title="Table View"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Bulk Actions Toolbar -->
    <Transition name="slide-down">
      <div v-if="showBulkActions" class="mb-6 bg-gradient-to-r from-blue-500/20 via-purple-500/20 to-blue-500/20 backdrop-blur-xl rounded-xl border border-blue-400/30 p-4">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            <span class="text-white font-medium">
              {{ selectedCount }} {{ selectedCount === 1 ? 'company' : 'companies' }} selected
            </span>
            <button
              @click="clearSelection"
              class="text-gray-300 hover:text-white text-sm underline"
            >
              Clear selection
            </button>
          </div>

          <div class="flex items-center gap-3">
            <button
              @click="bulkDelete"
              class="px-4 py-2 bg-red-500/30 hover:bg-red-500/50 text-red-300 hover:text-white border border-red-400/30 rounded-lg transition-all duration-200 flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              Delete Selected
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Select All Controls -->
    <div v-if="filteredCompanies.length > 0" class="mb-4 flex items-center gap-3">
      <label class="flex items-center gap-2 cursor-pointer">
        <input
          type="checkbox"
          :checked="isAllSelected"
          :indeterminate="isIndeterminate"
          @change="toggleSelectAll"
          class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
        />
        <span class="text-gray-300 text-sm">
          {{ isAllSelected ? 'Deselect all' : 'Select all' }}
        </span>
      </label>
    </div>

    <!-- Companies List -->
    <div v-if="loading" class="text-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto"></div>
      <p class="text-gray-300 mt-3">Loading companies...</p>
    </div>

    <div v-else-if="filteredCompanies.length === 0" class="text-center py-16 bg-white/5 rounded-2xl border border-white/10">
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

    <!-- Card View -->
    <div v-else-if="viewMode === 'cards'" class="space-y-4">
      <!-- Company Card -->
      <div
        v-for="company in filteredCompanies"
        :key="company.id"
        class="group relative bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl p-6 border border-white/10 hover:border-blue-400/30 transition-all duration-300 cursor-pointer hover:scale-[1.02] hover:shadow-[0_8px_32px_0_rgba(59,130,246,0.15)]"
        style="backdrop-filter: blur(20px) saturate(180%);"
        @click="viewCompanyDetails(company)"
      >
        <div class="flex items-start justify-between">
          <!-- Checkbox -->
          <div class="flex items-start gap-4 flex-1">
            <div class="mt-2">
              <input
                type="checkbox"
                :checked="selectedCompanies.has(company.id)"
                @change="toggleCompanySelection(company.id)"
                @click.stop
                class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
              />
            </div>

            <!-- Company Info -->
            <div class="flex items-start space-x-4 flex-1">
            <!-- Company Icon -->
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-xl flex items-center justify-center shadow-[0_0_10px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] transition-all duration-500">
              <span class="text-blue-300 font-bold text-lg">{{ (company.symbol || company.ticker || 'N/A').substring(0, 2) }}</span>
            </div>

            <!-- Company Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="text-xl font-semibold text-white group-hover:text-blue-200 transition-colors">{{ company.name }}</h3>
                <span class="px-3 py-1 bg-blue-500/20 text-blue-300 text-sm font-medium rounded-full border border-blue-400/20">{{ company.symbol || company.ticker || 'N/A' }}</span>
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

    <!-- Table View -->
    <div v-else class="space-y-4">
      <!-- Desktop Table -->
      <div class="hidden md:block">
        <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-b border-gray-700/30" style="backdrop-filter: blur(20px) saturate(180%);">
          <!-- Table Header -->
          <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
            <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-300">
              <div class="col-span-1 flex items-center">
                <input
                  type="checkbox"
                  :checked="isAllSelected"
                  :indeterminate="isIndeterminate"
                  @change="toggleSelectAll"
                  class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                />
              </div>
              <div class="col-span-3">Company</div>
              <div class="col-span-1 text-center">Symbol</div>
              <div class="col-span-2">Sector</div>
              <div class="col-span-2">Market Cap</div>
              <div class="col-span-1 text-center">Research</div>
              <div class="col-span-1 text-center">Assets</div>
              <div class="col-span-1 text-center">Actions</div>
            </div>
          </div>

          <!-- Table Body -->
          <div class="divide-y divide-gray-700/30">
            <div
              v-for="company in filteredCompanies"
              :key="company.id"
              class="group px-6 py-4 hover:bg-gray-800/20 transition-all duration-200 cursor-pointer"
              @click="viewCompanyDetails(company)"
            >
              <div class="grid grid-cols-12 gap-4 items-center">
                <!-- Checkbox -->
                <div class="col-span-1">
                  <input
                    type="checkbox"
                    :checked="selectedCompanies.has(company.id)"
                    @change="toggleCompanySelection(company.id)"
                    @click.stop
                    class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                  />
                </div>

                <!-- Company Name -->
                <div class="col-span-3 flex items-center gap-3">
                  <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-lg flex items-center justify-center">
                    <span class="text-blue-300 font-bold text-sm">{{ (company.symbol || company.ticker || 'N/A').substring(0, 2) }}</span>
                  </div>
                  <div class="min-w-0 flex-1">
                    <p class="text-white font-medium truncate group-hover:text-blue-200 transition-colors">{{ company.name }}</p>
                    <p class="text-gray-400 text-sm truncate">{{ company.industry || 'N/A' }}</p>
                  </div>
                </div>

                <!-- Symbol -->
                <div class="col-span-1 text-center">
                  <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs font-medium rounded border border-blue-400/20">
                    {{ company.symbol || company.ticker || 'N/A' }}
                  </span>
                </div>

                <!-- Sector -->
                <div class="col-span-2">
                  <p class="text-white text-sm truncate">{{ company.sector || 'N/A' }}</p>
                </div>

                <!-- Market Cap -->
                <div class="col-span-2">
                  <p class="text-white text-sm font-medium">{{ formatMarketCap(company.marketCap) }}</p>
                </div>

                <!-- Research Count -->
                <div class="col-span-1 text-center">
                  <span class="text-green-300 font-bold">{{ company.research_items_count || 0 }}</span>
                </div>

                <!-- Assets Count -->
                <div class="col-span-1 text-center">
                  <span class="text-purple-300 font-bold">{{ company.documents_count || 0 }}</span>
                </div>

                <!-- Actions -->
                <div class="col-span-1 text-center">
                  <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                      @click.stop="viewCompanyDetails(company)"
                      class="w-8 h-8 rounded bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                      title="View Details"
                    >
                      <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>

                    <button
                      v-if="$page.props.auth.user"
                      @click.stop="openQuickBlogWithCompany(company)"
                      class="w-8 h-8 rounded bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
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
        </div>
      </div>

      <!-- Mobile List (for table mode on mobile) -->
      <div class="block md:hidden space-y-3">
        <div
          v-for="company in filteredCompanies"
          :key="company.id"
          class="group bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10 hover:border-blue-400/30 transition-all duration-300 cursor-pointer"
          style="backdrop-filter: blur(20px) saturate(180%);"
          @click="viewCompanyDetails(company)"
        >
          <div class="flex items-start gap-3">
            <!-- Checkbox -->
            <input
              type="checkbox"
              :checked="selectedCompanies.has(company.id)"
              @change="toggleCompanySelection(company.id)"
              @click.stop
              class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2 mt-1"
            />

            <!-- Company Icon -->
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-lg flex items-center justify-center">
              <span class="text-blue-300 font-bold text-sm">{{ (company.symbol || company.ticker || 'N/A').substring(0, 2) }}</span>
            </div>

            <!-- Company Info -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h3 class="text-white font-medium truncate group-hover:text-blue-200 transition-colors">{{ company.name }}</h3>
                <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs font-medium rounded border border-blue-400/20 shrink-0">
                  {{ company.symbol || company.ticker || 'N/A' }}
                </span>
              </div>

              <div class="grid grid-cols-2 gap-2 text-xs text-gray-400 mb-2">
                <div>{{ company.sector || 'N/A' }}</div>
                <div>{{ formatMarketCap(company.marketCap) }}</div>
              </div>

              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4 text-xs">
                  <span class="text-green-300 font-medium">{{ company.research_items_count || 0 }} Research</span>
                  <span class="text-purple-300 font-medium">{{ company.documents_count || 0 }} Assets</span>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    @click.stop="viewCompanyDetails(company)"
                    class="w-8 h-8 rounded bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                    title="View Details"
                  >
                    <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                  </button>

                  <button
                    v-if="$page.props.auth.user"
                    @click.stop="openQuickBlogWithCompany(company)"
                    class="w-8 h-8 rounded bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
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
      </div>
    </div>

    <!-- Results Summary -->
    <div class="text-center text-gray-400 text-sm mt-8 pt-6 border-t border-white/10">
      {{ filteredCompanies.length }} {{ filteredCompanies.length === 1 ? 'company' : 'companies' }} found
    </div>

    <!-- Create Company Modal -->
    <CreateCompanyModal
      :show="showCreateCompanyModal"
      :form="companyForm"
      :format-market-cap="formatMarketCap"
      :errors="formErrors"
      :creating="creating"
      :market-cap-input="marketCapInput"
      :market-cap-validation="marketCapValidation"
      @close="showCreateCompanyModal = false"
      @created="handleCompanyCreated"
      @save="handleCreateCompany"
      @market-cap-input="handleMarketCapInput"
      @update:form="updateForm"
    />

    <!-- Quick Blog Modal -->
    <QuickBlogModal
      :show="showQuickBlogModal"
      :preselected-company="quickBlogCompany"
      @close="closeQuickBlogModal"
      @posted="handleBlogPosted"
    />

    <!-- Confirmation Modal -->
    <ConfirmationModal
      :show="showConfirmationModal"
      :title="confirmationTitle"
      :message="confirmationMessage"
      :type="confirmationType"
      :confirm-text="confirmText"
      :cancel-text="cancelText"
      @confirm="handleConfirmation"
      @cancel="cancelConfirmation"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import ConfirmationModal from '@/Components/Modals/ConfirmationModal.vue'
import axios from 'axios'

defineProps({
  auth: Object
})

// Data
const companies = ref([])
const loading = ref(true)

// Search and filtering
const searchQuery = ref('')
const selectedSector = ref('')
const sortBy = ref('name')

// View mode with persistence
const viewMode = ref(localStorage.getItem('companies-view-mode') || 'cards')

// Modals
const showCreateCompanyModal = ref(false)
const showQuickBlogModal = ref(false)
const quickBlogCompany = ref(null)

// Company form data
const companyForm = ref({
  name: '',
  ticker: '',
  sector: '',
  industry: '',
  market_cap: '',
  reports_financial_data_in: 'USD',
  description: ''
})

const formErrors = ref({})
const creating = ref(false)
const marketCapInput = ref('')
const marketCapValidation = ref({ state: '' })

// Multi-select functionality
const selectedCompanies = ref(new Set())
const showBulkActions = ref(false)

// Confirmation modal data
const showConfirmationModal = ref(false)
const confirmationTitle = ref('')
const confirmationMessage = ref('')
const confirmationType = ref('warning')
const confirmText = ref('Delete')
const cancelText = ref('Cancel')
const confirmationCallback = ref(null)

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
      (company.symbol && company.symbol.toLowerCase().includes(query)) ||
      (company.ticker && company.ticker.toLowerCase().includes(query)) ||
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
        return (a.symbol || a.ticker || '').localeCompare(b.symbol || b.ticker || '')
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

const isAllSelected = computed(() => {
  return filteredCompanies.value.length > 0 &&
         filteredCompanies.value.every(company => selectedCompanies.value.has(company.id))
})

const isIndeterminate = computed(() => {
  return selectedCompanies.value.size > 0 && !isAllSelected.value
})

const selectedCount = computed(() => selectedCompanies.value.size)

// Methods
const fetchCompanies = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/companies', {
      params: {
        include_counts: true,
        limit: 1000
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

const viewCompanyDetails = (company) => {
  if (company.ticker) {
    router.visit(route('company.profile', { ticker: company.ticker }))
  }
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
  showCreateCompanyModal.value = false
  resetCompanyForm()
}

const handleBlogPosted = () => {
  fetchCompanies()
}

const resetCompanyForm = () => {
  companyForm.value = {
    name: '',
    ticker: '',
    sector: '',
    industry: '',
    market_cap: '',
    reports_financial_data_in: 'USD',
    description: ''
  }
  formErrors.value = {}
  marketCapInput.value = ''
  marketCapValidation.value = { state: '' }
}

const updateForm = (updatedForm) => {
  companyForm.value = updatedForm
}

const handleMarketCapInput = (event) => {
  const value = event.target.value
  marketCapInput.value = value

  if (!value.trim()) {
    companyForm.value.market_cap = ''
    marketCapValidation.value = { state: '' }
    return
  }

  // Basic validation and parsing
  const parseMarketCap = (input) => {
    const cleanInput = input.replace(/[$,\s]/g, '').toUpperCase()

    if (!cleanInput) return null

    const lastChar = cleanInput.slice(-1)
    const number = parseFloat(cleanInput.slice(0, -1))

    if (isNaN(number)) {
      const directNumber = parseFloat(cleanInput)
      return isNaN(directNumber) ? null : directNumber
    }

    switch (lastChar) {
      case 'K':
        return number * 1000
      case 'M':
        return number * 1000000
      case 'B':
        return number * 1000000000
      case 'T':
        return number * 1000000000000
      default:
        return parseFloat(cleanInput)
    }
  }

  const parsedValue = parseMarketCap(value)

  if (parsedValue === null) {
    marketCapValidation.value = { state: 'invalid' }
    companyForm.value.market_cap = ''
  } else {
    marketCapValidation.value = { state: 'valid' }
    companyForm.value.market_cap = parsedValue
  }
}

const handleCreateCompany = async () => {
  try {
    creating.value = true
    formErrors.value = {}

    const response = await axios.post('/api/companies', companyForm.value)

    if (response.data.success || response.data.data) {
      const newCompany = response.data.data || response.data
      handleCompanyCreated(newCompany)
    }
  } catch (error) {
    if (error.response?.data?.errors) {
      formErrors.value = error.response.data.errors
    } else {
      formErrors.value = { general: 'An error occurred while creating the company.' }
    }
  } finally {
    creating.value = false
  }
}

// Multi-select methods
const toggleCompanySelection = (companyId) => {
  if (selectedCompanies.value.has(companyId)) {
    selectedCompanies.value.delete(companyId)
  } else {
    selectedCompanies.value.add(companyId)
  }
  showBulkActions.value = selectedCompanies.value.size > 0
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedCompanies.value.clear()
  } else {
    filteredCompanies.value.forEach(company => {
      selectedCompanies.value.add(company.id)
    })
  }
  showBulkActions.value = selectedCompanies.value.size > 0
}

const clearSelection = () => {
  selectedCompanies.value.clear()
  showBulkActions.value = false
}

const showConfirmation = (title, message, callback, type = 'warning', confirmTextValue = 'Delete') => {
  confirmationTitle.value = title
  confirmationMessage.value = message
  confirmationType.value = type
  confirmText.value = confirmTextValue
  confirmationCallback.value = callback
  showConfirmationModal.value = true
}

const handleConfirmation = () => {
  if (confirmationCallback.value) {
    confirmationCallback.value()
  }
  showConfirmationModal.value = false
  confirmationCallback.value = null
}

const cancelConfirmation = () => {
  showConfirmationModal.value = false
  confirmationCallback.value = null
}

const bulkDelete = () => {
  const count = selectedCount.value
  const companyText = count === 1 ? 'company' : 'companies'

  showConfirmation(
    'Delete Companies',
    `Are you sure you want to delete ${count} ${companyText}?<br><br>This action will permanently remove the selected companies and all associated data.`,
    async () => {
      try {
        const companyIds = Array.from(selectedCompanies.value)
        console.log('Attempting to delete company IDs:', companyIds)

        const response = await axios.delete('/api/companies/bulk', {
          data: { company_ids: companyIds }
        })

        console.log('Bulk delete response:', response.data)

        // Check if the response indicates success
        if (response.data && response.data.deleted_count !== undefined) {
          // Remove deleted companies from the local state
          companies.value = companies.value.filter(company => !selectedCompanies.value.has(company.id))
          clearSelection()

          // Show appropriate success message
          if (response.data.errors && response.data.errors.length > 0) {
            showConfirmation(
              'Partial Success',
              `Successfully deleted ${response.data.deleted_count} companies, but ${response.data.errors.length} failed to delete.`,
              null,
              'question',
              'OK'
            )
          } else {
            showConfirmation(
              'Success',
              `Successfully deleted ${response.data.deleted_count} ${companyText}.`,
              null,
              'question',
              'OK'
            )
          }
        } else {
          throw new Error('Unexpected response format')
        }
      } catch (error) {
        console.error('Error deleting companies:', error)

        let errorMessage = 'An error occurred while deleting the companies. Please try again.'

        if (error.response) {
          // Server responded with error status
          console.error('Response data:', error.response.data)
          console.error('Response status:', error.response.status)

          if (error.response.status === 403) {
            errorMessage = 'You do not have permission to delete these companies.'
          } else if (error.response.status === 422) {
            errorMessage = 'Invalid company selection. Please refresh the page and try again.'
          } else if (error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message
          }
        } else if (error.request) {
          // Network error
          errorMessage = 'Network error. Please check your connection and try again.'
        }

        showConfirmation(
          'Delete Failed',
          errorMessage,
          null,
          'question',
          'OK'
        )
      }
    },
    'warning',
    'Delete Companies'
  )
}

// Watch for view mode changes to persist
watch(viewMode, (newMode) => {
  localStorage.setItem('companies-view-mode', newMode)
})

// Lifecycle
onMounted(() => {
  fetchCompanies()
})
</script>

<style scoped>
/* Transitions for bulk actions */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-20px);
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>