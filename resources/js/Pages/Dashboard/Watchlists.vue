<template>
  <DashboardLayout active-tab="watchlists">
    <template #title>
      <div class="flex items-center justify-between w-full">
        <div>
          <h2 class="text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
            <svg class="w-10 h-10 mr-4 text-orange-400 drop-shadow-[0_0_8px_rgba(249,115,22,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            Watchlists
          </h2>
          <p class="text-gray-300 mt-2 ml-14">{{ watchlists.length }} watchlists with {{ totalCompanies }} companies</p>
        </div>

        <!-- Add Watchlist Button -->
        <button
          v-if="$page.props.auth.user"
          @click="openCreateWatchlistModal"
          class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-orange-500/20 via-orange-400/10 to-transparent text-orange-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)] border border-white/10 backdrop-blur-xl font-medium"
          style="backdrop-filter: blur(20px) saturate(150%);"
        >
          <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Create Watchlist
        </button>
      </div>
    </template>

    <!-- Main Content Layout with Sidebar -->
    <div class="flex gap-6 h-[calc(100vh-200px)]">
      <!-- Left Sidebar - Watchlists List -->
      <div
        class="bg-gradient-to-br from-gray-900/60 via-gray-800/40 to-gray-900/60 backdrop-blur-xl rounded-2xl border border-gray-700/30 transition-all duration-300 flex flex-col"
        :class="sidebarCollapsed ? 'w-16' : 'w-80'"
        style="backdrop-filter: blur(20px) saturate(150%);"
      >
        <!-- Sidebar Header -->
        <div class="p-4 border-b border-gray-700/30 flex items-center justify-between">
          <h3
            v-if="!sidebarCollapsed"
            class="text-lg font-semibold text-white"
          >
            My Watchlists
          </h3>
          <button
            @click="toggleSidebar"
            class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
          >
            <svg
              class="w-5 h-5 transition-transform duration-200"
              :class="{ 'rotate-180': sidebarCollapsed }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
            </svg>
          </button>
        </div>

        <!-- Search (when expanded) -->
        <div v-if="!sidebarCollapsed" class="p-4 border-b border-gray-700/30">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search watchlists..."
              class="w-full pl-9 pr-3 py-2 text-sm rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-400 focus:border-orange-400/50 focus:ring-1 focus:ring-orange-400/20 transition-all duration-200"
            />
          </div>
        </div>

        <!-- Watchlists List -->
        <div class="flex-1 overflow-y-auto">
          <!-- Loading State -->
          <div v-if="loading" class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-400"></div>
          </div>

          <!-- Empty State -->
          <div v-else-if="watchlists.length === 0" class="p-4 text-center text-gray-400">
            <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <p v-if="!sidebarCollapsed" class="text-sm">No watchlists yet</p>
          </div>

          <!-- Watchlist Items -->
          <div v-else class="p-2">
            <div
              v-for="watchlist in filteredWatchlists"
              :key="watchlist.id"
              class="mb-2 p-3 rounded-lg hover:bg-white/5 transition-all duration-200 cursor-pointer border"
              :class="selectedWatchlist?.id === watchlist.id ? 'bg-orange-500/20 border-orange-400/50' : 'border-transparent hover:border-white/10'"
              @click="selectWatchlist(watchlist)"
              :title="sidebarCollapsed ? watchlist.name : ''"
            >
              <div v-if="sidebarCollapsed" class="flex justify-center">
                <div class="w-8 h-8 bg-gradient-to-br from-orange-500/20 to-orange-600/20 rounded-lg flex items-center justify-center">
                  <span class="text-orange-400 font-mono text-xs font-bold">{{ watchlist.name.charAt(0).toUpperCase() }}</span>
                </div>
              </div>
              <div v-else>
                <div class="flex items-center justify-between mb-1">
                  <h4 class="text-white font-medium text-sm truncate">{{ watchlist.name }}</h4>
                  <div class="flex items-center gap-1 ml-2">
                    <button
                      @click.stop="editWatchlist(watchlist)"
                      class="p-1 rounded bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click.stop="deleteWatchlist(watchlist)"
                      class="p-1 rounded bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="flex items-center justify-between text-xs">
                  <span class="text-orange-400">{{ watchlist.companies_count || 0 }} companies</span>
                  <span class="text-gray-500">{{ formatDate(watchlist.created_at) }}</span>
                </div>
                <p v-if="watchlist.description" class="text-gray-400 text-xs mt-1 line-clamp-2">{{ watchlist.description }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Panel - Selected Watchlist Details -->
      <div class="flex-1 bg-gradient-to-br from-gray-900/60 via-gray-800/40 to-gray-900/60 backdrop-blur-xl rounded-2xl border border-gray-700/30 flex flex-col overflow-hidden" style="backdrop-filter: blur(20px) saturate(150%);">
        <!-- Welcome State -->
        <div v-if="!selectedWatchlist" class="flex-1 flex items-center justify-center text-center p-8">
          <div>
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <h3 class="text-xl font-medium text-white mb-2">Select a Watchlist</h3>
            <p class="text-gray-400 mb-6">Choose a watchlist from the sidebar to view its companies and details.</p>
            <button
              @click="openCreateWatchlistModal"
              class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Create Your First Watchlist
            </button>
          </div>
        </div>

        <!-- Selected Watchlist Content -->
        <div v-else class="flex-1 flex flex-col overflow-hidden">
          <!-- Watchlist Header -->
          <div class="p-6 border-b border-gray-700/30">
            <div class="flex items-center justify-between">
              <div>
                <h2 class="text-2xl font-bold text-white">{{ selectedWatchlist.name }}</h2>
                <div class="flex items-center gap-4 mt-1 text-sm text-gray-400">
                  <span>{{ watchlistCompanies.length }} companies</span>
                  <span>•</span>
                  <span>Created {{ formatDate(selectedWatchlist.created_at) }}</span>
                </div>
                <p v-if="selectedWatchlist.description" class="text-gray-400 mt-2">{{ selectedWatchlist.description }}</p>
              </div>
              <div class="flex items-center gap-2">
                <button
                  @click="editWatchlist(selectedWatchlist)"
                  class="px-4 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium transition-all duration-200 border border-white/20"
                >
                  Edit
                </button>
                <button
                  @click="deleteWatchlist(selectedWatchlist)"
                  class="px-4 py-2 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-300 hover:text-red-200 font-medium transition-all duration-200 border border-red-500/30"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Companies Content -->
          <div class="flex-1 overflow-y-auto p-6">
            <!-- Loading Companies -->
            <div v-if="loadingCompanies" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-400"></div>
            </div>

            <!-- Empty Companies State -->
            <div v-else-if="watchlistCompanies.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-300 mb-2">No companies yet</h3>
              <p class="text-gray-400 mb-6">Start building your watchlist by adding companies from the Companies page.</p>
              <button
                @click="router.visit('/dashboard/companies')"
                class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-lg transition-colors duration-200"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Browse Companies
              </button>
            </div>

            <!-- Companies Treeview -->
            <div v-else class="space-y-4">
              <div
                v-for="company in watchlistCompanies"
                :key="company.id"
                class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl border border-gray-700/30 overflow-hidden"
              >
                <!-- Company Header -->
                <div class="p-6 border-b border-gray-700/30">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                      <button
                        @click="toggleCompanyExpansion(company.id)"
                        class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
                      >
                        <svg
                          class="w-5 h-5 transition-transform duration-200"
                          :class="{ 'rotate-90': expandedCompanies.has(company.id) }"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                      </button>
                      <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                          <div class="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/20 rounded-xl flex items-center justify-center">
                            <span class="text-blue-400 font-mono text-lg font-bold">{{ company.ticker }}</span>
                          </div>
                        </div>
                        <div>
                          <h3 class="text-xl font-semibold text-white">{{ company.name }}</h3>
                          <div class="flex items-center gap-4 mt-1 text-sm text-gray-400">
                            <span>{{ company.sector || 'Unknown' }}</span>
                            <span>•</span>
                            <span>{{ formatMarketCap(company.market_cap) }}</span>
                            <span>•</span>
                            <span>Added {{ formatDate(company.pivot?.added_at) }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <!-- Summary badges -->
                      <div class="flex items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-500/20 text-green-300">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          {{ company.research_items_count || 0 }} Research
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-300">
                          <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a2 2 0 00-2.828-2.828z"></path>
                          </svg>
                          {{ company.assets_count || 0 }} Assets
                        </span>
                      </div>
                      <button
                        @click="navigateToCompany(company)"
                        class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
                        title="View company profile"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                      </button>
                      <button
                        @click="removeCompanyFromWatchlist(company)"
                        class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition-all duration-200"
                        title="Remove from watchlist"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Expanded Content -->
                <div v-if="expandedCompanies.has(company.id)" class="p-6 bg-gray-800/20">
                  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Research Items -->
                    <div>
                      <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-white flex items-center">
                          <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          Research Items ({{ company.research_items_count || 0 }})
                        </h4>
                        <button
                          class="text-sm text-green-400 hover:text-green-300 transition-colors duration-200"
                          @click="router.visit(`/companies/${company.ticker}?tab=research`)"
                        >
                          View All →
                        </button>
                      </div>
                      <div v-if="company.research_items && company.research_items.length > 0" class="space-y-3">
                        <div
                          v-for="item in company.research_items"
                          :key="item.id"
                          class="p-3 bg-gray-900/30 rounded-lg border border-gray-700/30 hover:bg-gray-900/50 transition-all duration-200 cursor-pointer"
                          @click="openResearchItem(item)"
                        >
                          <div class="flex items-start justify-between">
                            <div class="flex-1">
                              <h5 class="text-sm font-medium text-white mb-1">{{ item.title }}</h5>
                              <p class="text-xs text-gray-400 line-clamp-2">{{ item.content || 'No content preview' }}</p>
                              <div class="flex items-center gap-2 mt-2">
                                <span
                                  v-for="tag in item.tags"
                                  :key="tag.id"
                                  class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                  :style="{ backgroundColor: `${tag.color}20`, color: tag.color }"
                                >
                                  {{ tag.name }}
                                </span>
                              </div>
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap ml-3">{{ formatDate(item.created_at) }}</span>
                          </div>
                        </div>
                      </div>
                      <div v-else class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm">No research items yet</p>
                      </div>
                    </div>

                    <!-- Assets -->
                    <div>
                      <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-white flex items-center">
                          <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a2 2 0 00-2.828-2.828z"></path>
                          </svg>
                          Assets ({{ company.assets_count || 0 }})
                        </h4>
                        <button
                          class="text-sm text-purple-400 hover:text-purple-300 transition-colors duration-200"
                          @click="router.visit(`/companies/${company.ticker}?tab=assets`)"
                        >
                          View All →
                        </button>
                      </div>
                      <div v-if="company.assets && company.assets.length > 0" class="space-y-3">
                        <div
                          v-for="asset in company.assets"
                          :key="asset.id"
                          class="p-3 bg-gray-900/30 rounded-lg border border-gray-700/30 hover:bg-gray-900/50 transition-all duration-200 cursor-pointer"
                          @click="openAsset(asset)"
                        >
                          <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3 flex-1">
                              <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                                  <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                  </svg>
                                </div>
                              </div>
                              <div class="flex-1 min-w-0">
                                <h5 class="text-sm font-medium text-white truncate">{{ asset.title || asset.file_name }}</h5>
                                <p class="text-xs text-gray-400">{{ asset.description || formatFileSize(asset.size) }}</p>
                              </div>
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap ml-3">{{ formatDate(asset.created_at) }}</span>
                          </div>
                        </div>
                      </div>
                      <div v-else class="text-center py-8 text-gray-400">
                        <svg class="mx-auto h-8 w-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.586-6.586a2 2 0 00-2.828-2.828z"></path>
                        </svg>
                        <p class="text-sm">No assets yet</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Watchlist Modal (Create/Edit) -->
    <WatchlistFormModal
      key="watchlist-form-modal"
      :show="showCreateWatchlistModal || showEditWatchlistModal"
      :watchlist="editingWatchlist"
      @close="closeModal"
      @saved="handleWatchlistSaved"
    />

  </DashboardLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import WatchlistFormModal from '@/Components/Modals/WatchlistFormModal.vue'
import axios from 'axios'

// Props from route
const props = defineProps({
  selectedWatchlistSlug: {
    type: String,
    default: null
  }
})

// Reactive state
const watchlists = ref([])
const loading = ref(true)
const searchQuery = ref('')

// Sidebar state
const sidebarCollapsed = ref(false)

// View state
const selectedWatchlist = ref(null)
const watchlistCompanies = ref([])
const loadingCompanies = ref(false)

// Modal state
const showCreateWatchlistModal = ref(false)
const showEditWatchlistModal = ref(false)
const editingWatchlist = ref(null)

// Treeview state
const expandedCompanies = ref(new Set())

// Computed properties
const filteredWatchlists = computed(() => {
  let filtered = watchlists.value

  // Apply search filter
  if (searchQuery.value.trim()) {
    const search = searchQuery.value.toLowerCase()
    filtered = filtered.filter(watchlist =>
      watchlist.name.toLowerCase().includes(search) ||
      (watchlist.description && watchlist.description.toLowerCase().includes(search))
    )
  }

  // Sort by name by default
  return [...filtered].sort((a, b) => a.name.localeCompare(b.name))
})

const totalCompanies = computed(() => {
  return watchlists.value.reduce((total, watchlist) => total + (watchlist.companies_count || 0), 0)
})

// Methods
const fetchWatchlists = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/watchlists')
    watchlists.value = response.data.watchlists

    // If we have a selected slug from the route, find and select it
    if (props.selectedWatchlistSlug) {
      const watchlist = watchlists.value.find(w => w.slug === props.selectedWatchlistSlug)
      if (watchlist) {
        await selectWatchlist(watchlist)
      }
    }
  } catch (error) {
    console.error('Error fetching watchlists:', error)
  } finally {
    loading.value = false
  }
}

const selectWatchlist = async (watchlist) => {
  selectedWatchlist.value = watchlist

  // Update URL without page reload
  const url = `/dashboard/watchlists/${watchlist.slug}`
  if (window.location.pathname !== url) {
    window.history.pushState({}, '', url)
  }

  await fetchWatchlistCompanies(watchlist.id)
}

const fetchWatchlistCompanies = async (watchlistId) => {
  try {
    loadingCompanies.value = true
    const response = await axios.get(`/api/watchlists/${watchlistId}`)
    watchlistCompanies.value = response.data.watchlist.companies || []
  } catch (error) {
    console.error('Error fetching watchlist companies:', error)
    watchlistCompanies.value = []
  } finally {
    loadingCompanies.value = false
  }
}

const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const removeCompanyFromWatchlist = async (company) => {
  if (!confirm(`Remove ${company.name} (${company.ticker}) from this watchlist?`)) {
    return
  }

  try {
    await axios.delete(`/api/watchlists/${selectedWatchlist.value.id}/companies/${company.id}`)

    // Remove from local state
    watchlistCompanies.value = watchlistCompanies.value.filter(c => c.id !== company.id)

    // Update the count in the sidebar
    const watchlist = watchlists.value.find(w => w.id === selectedWatchlist.value.id)
    if (watchlist) {
      watchlist.companies_count = Math.max(0, (watchlist.companies_count || 1) - 1)
      selectedWatchlist.value.companies_count = watchlist.companies_count
    }
  } catch (error) {
    console.error('Error removing company from watchlist:', error)
    alert('Failed to remove company from watchlist. Please try again.')
  }
}

const navigateToCompany = (company) => {
  router.visit(`/companies/${company.ticker}`)
}

// Treeview methods
const toggleCompanyExpansion = (companyId) => {
  if (expandedCompanies.value.has(companyId)) {
    expandedCompanies.value.delete(companyId)
  } else {
    expandedCompanies.value.add(companyId)
  }
}

const openResearchItem = (item) => {
  // Navigate to research tab and highlight the item
  router.visit(`/dashboard/research?item=${item.id}`)
}

const openAsset = (asset) => {
  // Open asset in new tab or modal
  if (asset.url) {
    window.open(asset.url, '_blank')
  }
}

const formatFileSize = (bytes) => {
  if (!bytes) return 'Unknown size'
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const editWatchlist = (watchlist) => {
  editingWatchlist.value = watchlist
  showEditWatchlistModal.value = true
}

const deleteWatchlist = async (watchlist) => {
  if (!confirm(`Are you sure you want to delete "${watchlist.name}"? This action cannot be undone.`)) {
    return
  }

  try {
    await axios.delete(`/api/watchlists/${watchlist.id}`)

    // Remove from local state
    watchlists.value = watchlists.value.filter(w => w.id !== watchlist.id)

    // If this was the selected watchlist, clear selection
    if (selectedWatchlist.value && selectedWatchlist.value.id === watchlist.id) {
      selectedWatchlist.value = null
      watchlistCompanies.value = []
      // Update URL to remove the slug
      window.history.pushState({}, '', '/dashboard/watchlists')
    }
  } catch (error) {
    console.error('Error deleting watchlist:', error)
    alert('Failed to delete watchlist. Please try again.')
  }
}

const openCreateWatchlistModal = () => {
  editingWatchlist.value = null
  showCreateWatchlistModal.value = true
}

const closeModal = () => {
  showCreateWatchlistModal.value = false
  showEditWatchlistModal.value = false
  editingWatchlist.value = null
}

const handleWatchlistSaved = (watchlist) => {
  closeModal()

  if (editingWatchlist.value) {
    // Update existing watchlist
    const index = watchlists.value.findIndex(w => w.id === watchlist.id)
    if (index !== -1) {
      watchlists.value[index] = watchlist
      if (selectedWatchlist.value && selectedWatchlist.value.id === watchlist.id) {
        selectedWatchlist.value = watchlist
      }
    }
  } else {
    // Add new watchlist
    watchlists.value.push(watchlist)
  }
}

// Utility functions
const formatDate = (dateString) => {
  if (!dateString) return 'Unknown'
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return 'Invalid date'
  }
}

const formatMarketCap = (marketCap) => {
  if (!marketCap) return 'N/A'

  const value = parseFloat(marketCap)
  if (value >= 1000000000000) {
    return '$' + (value / 1000000000000).toFixed(1) + 'T'
  } else if (value >= 1000000000) {
    return '$' + (value / 1000000000).toFixed(1) + 'B'
  } else if (value >= 1000000) {
    return '$' + (value / 1000000).toFixed(1) + 'M'
  } else {
    return '$' + value.toLocaleString()
  }
}

// Lifecycle
onMounted(() => {
  fetchWatchlists()
})
</script>