<template>
  <DashboardLayout active-tab="research">
    <template #title>
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center">
          <h2 class="text-3xl lg:text-4xl font-bold text-white flex items-center drop-shadow-lg">
            <svg class="w-10 h-10 mr-4 text-green-400 drop-shadow-[0_0_8px_rgba(34,197,94,0.5)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research
          </h2>
        </div>
        <div class="flex items-center gap-3">
          <button
            @click="openUploadModal"
            class="px-3 py-2 sm:px-4 sm:py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl border border-blue-400/20 transition-all duration-200 flex items-center gap-2 backdrop-blur-xl text-sm sm:text-base"
          >
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <span class="hidden sm:inline">Upload File</span>
            <span class="sm:hidden">Upload</span>
          </button>

          <button
            @click="openCreateModal"
            class="px-3 py-2 sm:px-6 sm:py-3 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-xl border border-green-400/20 transition-all duration-200 flex items-center gap-2 backdrop-blur-xl text-sm sm:text-base"
          >
            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span class="hidden sm:inline">New Research</span>
            <span class="sm:hidden">New</span>
          </button>
        </div>
      </div>
    </template>

    <!-- Main Content Layout: Left Sidebar + Right Data Table -->
    <div class="flex gap-6 h-full">
      <!-- Left Sidebar: Search & Filters -->
      <div class="w-80 flex-shrink-0">
        <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl border-t border-b border-gray-700/30 p-6 sticky top-6" style="backdrop-filter: blur(20px) saturate(180%);">

          <!-- Search Section -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
              <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              Search & Filter
            </h3>

            <!-- Search Bar -->
            <div class="relative">
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search research items and documents..."
                class="w-full px-4 py-3 pl-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-400 focus:border-green-400/50 focus:ring-2 focus:ring-green-400/20 transition-all"
                @input="debouncedSearch"
              >
              <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>

          <!-- Filters -->
          <div class="space-y-4">
            <h4 class="text-md font-medium text-gray-300 border-b border-gray-700/30 pb-2">Filters</h4>

            <!-- Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-2">Type</label>
              <select
                v-model="selectedType"
                @change="loadCombinedData"
                class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
              >
                <option value="">All Types</option>
                <option value="research">Research Items</option>
                <option value="document">Documents</option>
              </select>
            </div>

            <!-- Company Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-2">Company</label>
              <select
                v-model="selectedCompany"
                @change="loadCombinedData"
                class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
              >
                <option value="">All Companies</option>
                <option v-for="company in companies" :key="company.id" :value="company.id">
                  {{ company.name }} ({{ company.ticker }})
                </option>
              </select>
            </div>

            <!-- Category Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-2">Category</label>
              <select
                v-model="selectedCategory"
                @change="loadCombinedData"
                class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
              >
                <option value="">All Categories</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Sort Options -->
            <div>
              <label class="block text-sm font-medium text-gray-400 mb-2">Sort By</label>
              <select
                v-model="sortBy"
                @change="loadCombinedData"
                class="w-full px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
              >
                <option value="created_at_desc">Newest First</option>
                <option value="created_at_asc">Oldest First</option>
                <option value="title_asc">Title A-Z</option>
                <option value="title_desc">Title Z-A</option>
              </select>
            </div>
          </div>

          <!-- Clear Filters -->
          <div class="mt-6 pt-4 border-t border-gray-700/30">
            <button
              @click="clearFilters"
              class="w-full px-4 py-2 bg-gray-600/20 hover:bg-gray-600/30 text-gray-300 hover:text-white rounded-lg transition-all duration-200 text-sm"
            >
              Clear All Filters
            </button>
          </div>
        </div>
      </div>

      <!-- Right Panel: Unified Data Table -->
      <div class="flex-1 min-w-0">
        <!-- Unified Research & Documents Table -->
        <div>
          <!-- Bulk Actions Bar -->
          <div v-if="hasSelectedItems" class="mb-4 p-4 bg-gradient-to-br from-red-900/20 via-red-800/30 to-red-900/20 backdrop-blur-xl rounded-xl border-t border-b border-red-700/30" style="backdrop-filter: blur(20px) saturate(180%);">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-white">
                  {{ selectedCount }} item{{ selectedCount !== 1 ? 's' : '' }} selected
                </span>
                <button
                  @click="clearSelection"
                  class="text-xs text-gray-400 hover:text-white transition-colors"
                >
                  Clear selection
                </button>
              </div>
              <button
                @click="bulkDeleteItems"
                class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg border border-red-400/20 transition-all duration-200 flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete Selected
              </button>
            </div>
          </div>

          <!-- Data Table with CLAUDE.md Styling -->
          <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-b border-gray-700/30" style="backdrop-filter: blur(20px) saturate(180%);">

            <!-- Table Header -->
            <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
              <div class="flex items-center gap-4 text-xs font-medium text-gray-300 uppercase tracking-wider">
                <div class="w-8 flex items-center justify-center">
                  <input
                    type="checkbox"
                    :checked="isAllSelected"
                    @change="toggleSelectAll"
                    class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2"
                  >
                </div>
                <div class="w-16">Type</div>
                <div class="flex-1 min-w-0">Title</div>
                <div class="w-32">Company</div>
                <div class="w-24">Category</div>
                <div class="w-32">Tags</div>
                <div class="w-24">Created</div>
                <div class="w-24 text-center">Actions</div>
              </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading || loadingAssets" class="px-6 py-12">
              <div class="text-center">
                <div class="inline-flex items-center gap-2 text-gray-400">
                  <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Loading data...
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="combinedItems.length === 0" class="px-6 py-12">
              <div class="text-center">
                <div class="max-w-md mx-auto">
                  <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-400/10 to-emerald-500/10 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <h3 class="text-xl font-semibold text-white mb-2">No Items Found</h3>
                  <p class="text-gray-400 mb-6">{{ searchQuery || assetSearchQuery ? 'No items match your search criteria.' : 'Start by creating your first research item.' }}</p>
                  <button
                    v-if="!searchQuery && !assetSearchQuery"
                    @click="openCreateModal"
                    class="px-6 py-3 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-xl border border-green-400/20 transition-all duration-200"
                  >
                    Create Research Item
                  </button>
                </div>
              </div>
            </div>

            <!-- Table Body -->
            <div v-else class="divide-y divide-gray-700/30">
              <div
                v-for="item in combinedItems"
                :key="`${item.type}-${item.id}`"
                class="group px-6 py-4 hover:bg-gray-800/20 transition-all duration-200"
              >
                <div class="flex items-center gap-4">
                  <!-- Checkbox -->
                  <div class="w-8 flex items-center justify-center">
                    <input
                      type="checkbox"
                      :checked="selectedItems.has(`${item.type}-${item.id}`)"
                      @change="toggleItemSelection(item)"
                      @click.stop
                      class="w-4 h-4 text-green-600 bg-gray-700 border-gray-600 rounded focus:ring-green-500 focus:ring-2"
                    >
                  </div>

                  <!-- Type -->
                  <div class="w-16">
                    <span class="px-2 py-1 text-xs rounded" :class="{
                      'bg-green-500/20 text-green-400': item.type === 'research',
                      'bg-blue-500/20 text-blue-400': item.type === 'document'
                    }">
                      {{ item.type === 'research' ? 'Research' : 'Document' }}
                    </span>
                  </div>

                  <!-- Title -->
                  <div class="flex-1 min-w-0 cursor-pointer" @click="handleItemClick(item)">
                    <div class="flex flex-col">
                      <div class="text-sm font-medium text-white line-clamp-1 group-hover:text-green-200 transition-colors">
                        {{ item.title }}
                        <span v-if="item.type === 'document' && item.size" class="text-xs text-gray-400 font-normal">
                          ({{ formatFileSize(item.size) }})
                        </span>
                      </div>
                      <div v-if="item.content" class="text-sm text-gray-400 line-clamp-2 mt-1">{{ getContentPreview(item.content) }}</div>
                      <div v-else-if="item.file_name" class="text-sm text-gray-400 mt-1">{{ item.file_name }}</div>
                    </div>
                  </div>

                  <!-- Company -->
                  <div class="w-32">
                    <div v-if="item.company" class="flex items-center gap-2">
                      <span class="text-sm text-white truncate">{{ item.company.name }}</span>
                      <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded shrink-0">{{ item.company.ticker }}</span>
                    </div>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </div>

                  <!-- Category -->
                  <div class="w-24">
                    <span v-if="item.category" class="px-2 py-1 text-xs rounded" :style="{ backgroundColor: item.category.color + '20', color: item.category.color }">
                      {{ item.category.name }}
                    </span>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </div>

                  <!-- Tags -->
                  <div class="w-32">
                    <div v-if="item.tags && item.tags.length > 0" class="flex flex-wrap gap-1">
                      <span
                        v-for="tag in item.tags.slice(0, 2)"
                        :key="tag.id"
                        class="px-2 py-1 text-xs rounded"
                        :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                      >
                        {{ tag.name }}
                      </span>
                      <span v-if="item.tags.length > 2" class="px-2 py-1 bg-gray-500/20 text-gray-400 text-xs rounded">
                        +{{ item.tags.length - 2 }}
                      </span>
                    </div>
                    <div v-else-if="item.type === 'document'" class="flex items-center gap-2">
                      <span class="px-2 py-1 text-xs rounded bg-gray-500/20 text-gray-400">
                        {{ getFileExtension(item.file_name) }}
                      </span>
                    </div>
                    <span v-else class="text-sm text-gray-400">—</span>
                  </div>

                  <!-- Created -->
                  <div class="w-24">
                    <div class="text-sm text-gray-400">{{ formatDate(item.created_at) }}</div>
                  </div>

                  <!-- Actions -->
                  <div class="w-24 text-center">
                    <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button
                        v-if="item.type === 'research'"
                        @click.stop="openEditModal(item)"
                        class="w-8 h-8 rounded bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                        title="Edit"
                      >
                        <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        v-if="item.type === 'document'"
                        @click.stop="downloadAsset(item)"
                        class="w-8 h-8 rounded bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                        title="Download"
                      >
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                      </button>
                      <button
                        @click.stop="handleDeleteItem(item)"
                        class="w-8 h-8 rounded bg-red-500/30 hover:bg-red-500/50 flex items-center justify-center transition-colors"
                        title="Delete"
                      >
                        <svg class="w-4 h-4 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <div v-if="pagination && pagination.total_pages > 1" class="mt-8 flex justify-center">
            <div class="flex items-center gap-2">
              <button
                @click="loadPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/10 transition-colors"
              >
                Previous
              </button>

              <div class="flex gap-1">
                <button
                  v-for="page in visiblePages"
                  :key="page"
                  @click="loadPage(page)"
                  :class="[
                    'px-3 py-2 rounded-lg transition-colors',
                    page === pagination.current_page
                      ? 'bg-green-500/20 text-green-300 border border-green-400/20'
                      : 'bg-white/5 border border-white/10 text-white hover:bg-white/10'
                  ]"
                >
                  {{ page }}
                </button>
              </div>

              <button
                @click="loadPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.total_pages"
                class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/10 transition-colors"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <NoteCreationModal
      :show="showCreateModal"
      :selected-company="null"
      :form="createForm"
      :errors="createErrors"
      :creating-note="creating"
      :categories="categories"
      :tags="tags"
      :companies="companies"
      :user="$page.props.auth.user"
      :is-editing="isEditing"
      :available-files="availableFiles"
      :loading-existing-files="loadingExistingFiles"
      @close="handleCreateModalClose"
      @save="handleSave"
      @category-created="handleCategoryCreated"
      @file-upload="handleFileUpload"
      @add-url="handleAddUrl"
      @remove-url="handleRemoveUrl"
      @remove-file="handleRemoveFile"
      @search-existing-files="handleSearchExistingFiles"
      @load-existing-files="handleLoadExistingFiles"
      @toggle-file-selection="handleToggleFileSelection"
      @update:form="handleFormUpdate"
    />

    <ResearchNoteModal
      :show="showViewModal"
      :research-note="selectedItem"
      @close="showViewModal = false"
      @edit="handleEdit"
    />

    <AssetUploadModal
      :show="showUploadModal"
      :research-items="researchItems"
      @close="showUploadModal = false"
      @uploaded="handleAssetUploaded"
    />

    <AssetViewModal
      :show="showAssetModal"
      :asset="selectedAsset"
      @close="showAssetModal = false"
      @delete="confirmDeleteAsset"
    />
  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import AssetUploadModal from '@/Components/Modals/AssetUploadModal.vue'
import AssetViewModal from '@/Components/Modals/AssetViewModal.vue'
import axios from 'axios'

defineProps({
  auth: Object
})

// No longer needed - unified view

// Reactive data - Research Items
const loading = ref(true)
const researchItems = ref([])
const companies = ref([])
const categories = ref([])
const tags = ref([])
const pagination = ref(null)

// Reactive data - Assets
const loadingAssets = ref(false)
const assets = ref([])
const documents = ref([])
const assetPagination = ref(null)

// Computed counts
const researchItemsCount = computed(() => pagination.value?.total_items || researchItems.value.length)
const assetsCount = computed(() => assets.value.length + documents.value.length)

// Search and filters - Unified
const searchQuery = ref('')
const selectedType = ref('')
const selectedCompany = ref('')
const selectedCategory = ref('')
const sortBy = ref('created_at_desc')

// Search and filters - Assets
const assetSearchQuery = ref('')
const selectedFileType = ref('')
const selectedAssetSource = ref('')
const selectedSizeFilter = ref('')
const assetSortBy = ref('created_at_desc')

// Modal states - Research Items
const showCreateModal = ref(false)
const showViewModal = ref(false)
const selectedItem = ref(null)
const editingItem = ref(null)
const isEditing = computed(() => !!editingItem.value)
const creating = ref(false)

// Modal states - Assets
const showUploadModal = ref(false)
const showAssetModal = ref(false)
const selectedAsset = ref(null)

// Selection states for bulk actions
const selectedItems = ref(new Set())
const isAllSelected = ref(false)

// Form data
const createForm = ref({
  title: '',
  content: '',
  company_id: '',
  category_id: '',
  tag_ids: [],
  visibility: 'private',
  ai_synopsis: '',
  source_date: '',
  files: [],
  urls: [],
  selectedTags: [],
  selectedExistingFiles: [],
  uploadType: 'file',
  newUrl: ''
})
const createErrors = ref({})

// File upload related data
const availableFiles = ref([])
const loadingExistingFiles = ref(false)

// Computed properties
const visiblePages = computed(() => {
  if (!pagination.value) return []

  const current = pagination.value.current_page
  const total = pagination.value.total_pages
  const delta = 2

  const range = []
  const rangeWithDots = []

  for (let i = Math.max(2, current - delta); i <= Math.min(total - 1, current + delta); i++) {
    range.push(i)
  }

  if (current - delta > 2) {
    rangeWithDots.push(1, '...')
  } else {
    rangeWithDots.push(1)
  }

  rangeWithDots.push(...range)

  if (current + delta < total - 1) {
    rangeWithDots.push('...', total)
  } else {
    rangeWithDots.push(total)
  }

  return rangeWithDots.filter((v, i, a) => a.indexOf(v) === i)
})

const filteredAssets = computed(() => {
  let filtered = [...assets.value]

  // Apply search filter
  if (assetSearchQuery.value) {
    const search = assetSearchQuery.value.toLowerCase()
    filtered = filtered.filter(asset =>
      (asset.title && asset.title.toLowerCase().includes(search)) ||
      (asset.file_name && asset.file_name.toLowerCase().includes(search)) ||
      (asset.source_title && asset.source_title.toLowerCase().includes(search))
    )
  }

  // Apply file type filter
  if (selectedFileType.value) {
    filtered = filtered.filter(asset => {
      const type = getFileTypeCategory(asset.mime_type)
      return type === selectedFileType.value
    })
  }

  // Apply source filter
  if (selectedAssetSource.value) {
    filtered = filtered.filter(asset => asset.source_type === selectedAssetSource.value)
  }

  // Apply size filter
  if (selectedSizeFilter.value) {
    filtered = filtered.filter(asset => {
      const size = asset.size || 0
      switch (selectedSizeFilter.value) {
        case 'small': return size < 1024 * 1024 // < 1MB
        case 'medium': return size >= 1024 * 1024 && size <= 10 * 1024 * 1024 // 1-10MB
        case 'large': return size > 10 * 1024 * 1024 // > 10MB
        default: return true
      }
    })
  }

  return filtered
})

const combinedItems = computed(() => {
  let items = []

  // Add research items with type identifier
  const researchWithType = researchItems.value.map(item => ({
    ...item,
    type: 'research'
  }))

  // Add assets with type identifier
  const assetsWithType = assets.value.map(asset => ({
    ...asset,
    type: 'document',
    title: asset.title || asset.file_name
  }))

  items = [...researchWithType, ...assetsWithType]

  // Apply filters
  if (selectedType.value) {
    items = items.filter(item => item.type === selectedType.value)
  }

  if (selectedCompany.value) {
    items = items.filter(item => item.company?.id == selectedCompany.value)
  }

  if (selectedCategory.value) {
    items = items.filter(item => item.category?.id == selectedCategory.value)
  }

  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase()
    items = items.filter(item => {
      const title = (item.title || '').toLowerCase()
      const content = (item.content || '').toLowerCase()
      const fileName = (item.file_name || '').toLowerCase()
      const companyName = (item.company?.name || '').toLowerCase()

      return title.includes(search) ||
             content.includes(search) ||
             fileName.includes(search) ||
             companyName.includes(search)
    })
  }

  // Apply sorting
  const [field, direction] = sortBy.value.split('_')
  if (field && direction) {
    items.sort((a, b) => {
      let aValue, bValue

      if (field === 'title') {
        aValue = (a.title || a.file_name || '').toLowerCase()
        bValue = (b.title || b.file_name || '').toLowerCase()
      } else if (field === 'created_at') {
        aValue = new Date(a.created_at)
        bValue = new Date(b.created_at)
      } else {
        aValue = a[field] || ''
        bValue = b[field] || ''
      }

      if (direction === 'desc') {
        return aValue > bValue ? -1 : aValue < bValue ? 1 : 0
      } else {
        return aValue < bValue ? -1 : aValue > bValue ? 1 : 0
      }
    })
  }

  return items
})

// Selection computed properties
const selectedCount = computed(() => selectedItems.value.size)
const hasSelectedItems = computed(() => selectedCount.value > 0)
const currentPageItems = computed(() => combinedItems.value.map(item => `${item.type}-${item.id}`))

// Methods
const loadCombinedData = async () => {
  await Promise.all([
    loadResearchItems(),
    loadAssets()
  ])
}

const handleItemClick = (item) => {
  if (item.type === 'research') {
    openViewModal(item)
  } else if (item.type === 'document') {
    openAssetModal(item)
  }
}

const handleDeleteItem = async (item) => {
  if (item.type === 'research') {
    await deleteItem(item)
  } else if (item.type === 'document') {
    await deleteAsset(item)
  }
}

const loadResearchItems = async (page = 1) => {
  try {
    loading.value = true

    const params = {
      page,
      limit: 12,
      search: searchQuery.value,
      company_id: selectedCompany.value,
      category_id: selectedCategory.value
    }

    // Handle sorting
    const [field, direction] = sortBy.value.split('_')
    if (field && direction) {
      params.sort_by = field
      params.sort_direction = direction === 'desc' ? 'desc' : 'asc'
    }

    const response = await axios.get('/api/research-items', { params })

    researchItems.value = response.data.data
    pagination.value = response.data.pagination
  } catch (error) {
    console.error('Error loading research items:', error)
  } finally {
    loading.value = false
  }
}

const loadInitialData = async () => {
  try {
    // Load companies, categories, and tags in parallel
    const [companiesResponse, categoriesResponse, tagsResponse] = await Promise.all([
      axios.get('/api/companies'),
      axios.get('/api/categories'),
      axios.get('/api/tags')
    ])

    companies.value = companiesResponse.data.data || companiesResponse.data
    categories.value = categoriesResponse.data
    tags.value = tagsResponse.data

    // Load research items
    await loadResearchItems()
  } catch (error) {
    console.error('Error loading initial data:', error)
  }
}

const loadPage = (page) => {
  if (page && page !== '...') {
    loadResearchItems(page)
  }
}

// Debounced search
let searchTimeout = null
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    // No need to call loadCombinedData since combinedItems is computed and will update automatically
  }, 300)
}

// Modal handlers
const openCreateModal = () => {
  editingItem.value = null
  createForm.value = {
    title: '',
    content: '',
    company_id: '',
    category_id: '',
    tag_ids: [],
    visibility: 'private',
    ai_synopsis: '',
    source_date: ''
  }
  createErrors.value = {}
  showCreateModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  createForm.value = {
    title: item.title || '',
    content: item.content || '',
    company_id: item.company?.id || '',
    category_id: item.category?.id || '',
    tag_ids: item.tags?.map(tag => tag.id) || [],
    visibility: item.visibility || 'private',
    ai_synopsis: item.ai_synopsis || '',
    source_date: item.source_date || ''
  }
  createErrors.value = {}
  showCreateModal.value = true
}

const openViewModal = (item) => {
  selectedItem.value = item
  showViewModal.value = true
}

const handleCreateModalClose = () => {
  showCreateModal.value = false
  editingItem.value = null
  createForm.value = {
    title: '',
    content: '',
    company_id: '',
    category_id: '',
    tag_ids: [],
    visibility: 'private',
    ai_synopsis: '',
    source_date: '',
    files: [],
    urls: [],
    selectedTags: [],
    selectedExistingFiles: [],
    uploadType: 'file',
    newUrl: ''
  }
  createErrors.value = {}
}

const handleSave = async () => {
  try {
    creating.value = true
    createErrors.value = {}

    const url = editingItem.value
      ? `/api/research-items/${editingItem.value.id}`
      : '/api/research-items'

    const method = editingItem.value ? 'put' : 'post'

    // Prepare data for submission
    let submitData
    const hasFiles = createForm.value.files && createForm.value.files.length > 0
    const hasUrls = createForm.value.urls && createForm.value.urls.length > 0
    const hasSelectedFiles = createForm.value.selectedExistingFiles && createForm.value.selectedExistingFiles.length > 0

    if (hasFiles || hasUrls || hasSelectedFiles) {
      // Use FormData for file uploads
      submitData = new FormData()

      // Add text fields
      Object.keys(createForm.value).forEach(key => {
        if (key !== 'files' && key !== 'urls' && key !== 'selectedExistingFiles' && key !== 'selectedTags') {
          submitData.append(key, createForm.value[key] || '')
        }
      })

      // Add selected tags
      if (createForm.value.selectedTags && createForm.value.selectedTags.length > 0) {
        createForm.value.selectedTags.forEach(tag => {
          submitData.append('tag_ids[]', tag.id)
        })
      }

      // Add files
      if (hasFiles) {
        createForm.value.files.forEach(file => {
          submitData.append('files[]', file)
        })
      }

      // Add URLs
      if (hasUrls) {
        createForm.value.urls.forEach(url => {
          submitData.append('urls[]', url)
        })
      }

      // Add selected existing files
      if (hasSelectedFiles) {
        createForm.value.selectedExistingFiles.forEach(file => {
          submitData.append('existing_file_ids[]', file.id)
        })
      }
    } else {
      // Regular JSON submission
      submitData = {
        ...createForm.value,
        tag_ids: createForm.value.selectedTags?.map(tag => tag.id) || []
      }
    }

    const response = await axios[method](url, submitData, {
      headers: hasFiles || hasUrls || hasSelectedFiles ? {
        'Content-Type': 'multipart/form-data'
      } : {}
    })

    // Refresh the list
    await loadResearchItems(pagination.value?.current_page || 1)

    // Close modal
    handleCreateModalClose()

    console.log('Research item saved successfully')
  } catch (error) {
    if (error.response?.status === 422) {
      createErrors.value = error.response.data.errors || {}
    }
    console.error('Error saving research item:', error)
  } finally {
    creating.value = false
  }
}

const handleEdit = (item) => {
  showViewModal.value = false
  openEditModal(item)
}

const handleCategoryCreated = (newCategory) => {
  categories.value.push(newCategory)
  createForm.value.category_id = newCategory.id
}

// File upload event handlers
const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  if (files.length > 0) {
    createForm.value.files = [...(createForm.value.files || []), ...files]
  }
}

const handleAddUrl = (url) => {
  if (url && !createForm.value.urls.includes(url)) {
    createForm.value.urls = [...(createForm.value.urls || []), url]
    createForm.value.newUrl = ''
  }
}

const handleRemoveUrl = (index) => {
  createForm.value.urls = createForm.value.urls.filter((_, i) => i !== index)
}

const handleRemoveFile = (index) => {
  createForm.value.files = createForm.value.files.filter((_, i) => i !== index)
}

const handleSearchExistingFiles = async (searchTerm) => {
  try {
    loadingExistingFiles.value = true
    const response = await axios.get('/api/assets', {
      params: {
        search: searchTerm,
        per_page: 50
      }
    })
    availableFiles.value = response.data.data || []
  } catch (error) {
    console.error('Error searching existing files:', error)
    availableFiles.value = []
  } finally {
    loadingExistingFiles.value = false
  }
}

const handleLoadExistingFiles = async () => {
  try {
    loadingExistingFiles.value = true
    const response = await axios.get('/api/assets', {
      params: {
        per_page: 50
      }
    })
    availableFiles.value = response.data.data || []
  } catch (error) {
    console.error('Error loading existing files:', error)
    availableFiles.value = []
  } finally {
    loadingExistingFiles.value = false
  }
}

const handleToggleFileSelection = (file) => {
  const currentSelection = createForm.value.selectedExistingFiles || []
  const isSelected = currentSelection.some(f => f.id === file.id)

  if (isSelected) {
    createForm.value.selectedExistingFiles = currentSelection.filter(f => f.id !== file.id)
  } else {
    createForm.value.selectedExistingFiles = [...currentSelection, file]
  }
}

const handleFormUpdate = (updatedForm) => {
  createForm.value = updatedForm
}

// Selection methods
const toggleItemSelection = (item) => {
  const itemId = `${item.type}-${item.id}`
  if (selectedItems.value.has(itemId)) {
    selectedItems.value.delete(itemId)
  } else {
    selectedItems.value.add(itemId)
  }

  // Update select all state
  isAllSelected.value = currentPageItems.value.length > 0 &&
    currentPageItems.value.every(id => selectedItems.value.has(id))
}

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    // Deselect all current page items
    currentPageItems.value.forEach(id => selectedItems.value.delete(id))
    isAllSelected.value = false
  } else {
    // Select all current page items
    currentPageItems.value.forEach(id => selectedItems.value.add(id))
    isAllSelected.value = true
  }
}

const clearSelection = () => {
  selectedItems.value.clear()
  isAllSelected.value = false
}

const deleteItem = async (item) => {
  if (!confirm(`Are you sure you want to delete "${item.title}"?`)) {
    return
  }

  try {
    await axios.delete(`/api/research-items/${item.id}`)
    await loadResearchItems(pagination.value?.current_page || 1)
    console.log('Research item deleted successfully')
  } catch (error) {
    console.error('Error deleting research item:', error)
  }
}

const bulkDeleteItems = async () => {
  if (!hasSelectedItems.value) return

  const selectedItemsList = Array.from(selectedItems.value).map(id => {
    const [type, itemId] = id.split('-')
    return { type, id: parseInt(itemId) }
  })

  const message = `Are you sure you want to delete ${selectedCount.value} selected item${selectedCount.value !== 1 ? 's' : ''}?`
  if (!confirm(message)) {
    return
  }

  try {
    // Group items by type for efficient deletion
    const researchItems = selectedItemsList.filter(item => item.type === 'research')
    const documentItems = selectedItemsList.filter(item => item.type === 'document')

    // Delete research items
    if (researchItems.length > 0) {
      await Promise.all(researchItems.map(item =>
        axios.delete(`/api/research-items/${item.id}`)
      ))
    }

    // Delete document items
    if (documentItems.length > 0) {
      await Promise.all(documentItems.map(item =>
        axios.delete(`/api/assets/${item.id}`)
      ))
    }

    // Clear selection and refresh data
    clearSelection()
    await loadCombinedData()

    console.log(`Successfully deleted ${selectedCount.value} items`)
  } catch (error) {
    console.error('Error deleting selected items:', error)
    alert('Some items could not be deleted. Please try again.')
  }
}

// Asset Management Functions
const loadAssets = async (page = 1) => {
  try {
    loadingAssets.value = true

    const params = {
      page,
      limit: 16,
      search: assetSearchQuery.value,
      file_type: selectedFileType.value,
      source_type: selectedAssetSource.value,
      size_filter: selectedSizeFilter.value
    }

    // Handle sorting
    const [field, direction] = assetSortBy.value.split('_')
    if (field && direction) {
      params.sort_by = field
      params.sort_direction = direction === 'desc' ? 'desc' : 'asc'
    }

    const response = await axios.get('/api/assets', { params })

    assets.value = response.data.data || response.data
    assetPagination.value = response.data.pagination
  } catch (error) {
    console.error('Error loading assets:', error)
  } finally {
    loadingAssets.value = false
  }
}

const loadAssetPage = (page) => {
  if (page && page !== '...') {
    loadAssets(page)
  }
}

// Debounced asset search
let assetSearchTimeout = null
const debouncedAssetSearch = () => {
  clearTimeout(assetSearchTimeout)
  assetSearchTimeout = setTimeout(() => {
    loadAssets(1)
  }, 300)
}

const openUploadModal = () => {
  showUploadModal.value = true
}

const openAssetModal = (asset) => {
  selectedAsset.value = asset
  showAssetModal.value = true
}

const downloadAsset = (asset) => {
  window.open(asset.url, '_blank')
}

const deleteAsset = async (asset) => {
  if (!confirm(`Are you sure you want to delete "${asset.title || asset.file_name}"?`)) {
    return
  }

  try {
    await axios.delete(`/api/assets/${asset.id}`)
    await loadAssets(assetPagination.value?.current_page || 1)
    console.log('Asset deleted successfully')
  } catch (error) {
    console.error('Error deleting asset:', error)
  }
}

const handleAssetUploaded = (uploadedAssets) => {
  // Refresh the assets list
  loadAssets(assetPagination.value?.current_page || 1)
  console.log('Assets uploaded successfully:', uploadedAssets)
}

const confirmDeleteAsset = async (asset) => {
  if (!confirm(`Are you sure you want to delete "${asset.title || asset.file_name}"?`)) {
    return
  }

  try {
    await axios.delete(`/api/assets/${asset.id}`)
    showAssetModal.value = false
    await loadAssets(assetPagination.value?.current_page || 1)
    console.log('Asset deleted successfully')
  } catch (error) {
    console.error('Error deleting asset:', error)
  }
}

// Asset utility functions
const isImage = (mimeType) => {
  return mimeType && mimeType.startsWith('image/')
}

const isPdf = (mimeType) => {
  return mimeType === 'application/pdf'
}

const getFileExtension = (filename) => {
  if (!filename) return ''
  return filename.split('.').pop()?.toUpperCase() || ''
}

const getFileTypeCategory = (mimeType) => {
  if (!mimeType) return 'other'

  if (mimeType.startsWith('image/')) return 'image'
  if (mimeType === 'application/pdf') return 'pdf'
  if (mimeType.includes('document') || mimeType.includes('word')) return 'document'
  if (mimeType.includes('sheet') || mimeType.includes('excel')) return 'spreadsheet'
  return 'other'
}

const getFileTypeColor = (mimeType) => {
  const category = getFileTypeCategory(mimeType)
  switch (category) {
    case 'image': return 'bg-green-500/20 text-green-400'
    case 'pdf': return 'bg-red-500/20 text-red-400'
    case 'document': return 'bg-blue-500/20 text-blue-400'
    case 'spreadsheet': return 'bg-emerald-500/20 text-emerald-400'
    default: return 'bg-gray-500/20 text-gray-400'
  }
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Utility functions
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getContentPreview = (content) => {
  if (!content) return 'No content available'

  // Strip HTML tags
  const stripped = content.replace(/<[^>]*>/g, '')

  // Decode HTML entities
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  const decoded = textarea.value

  // Truncate to a reasonable length for preview
  const maxLength = 120
  if (decoded.length <= maxLength) {
    return decoded
  }

  return decoded.substring(0, maxLength).trim() + '...'
}

// Clear all filters method
const clearFilters = () => {
  searchQuery.value = ''
  selectedType.value = ''
  selectedCompany.value = ''
  selectedCategory.value = ''
  sortBy.value = 'created_at_desc'
  // No need to reload data since combinedItems will update automatically
}

// Note: viewMode removed since we now use unified table view

// Initialize on mount
onMounted(() => {
  loadInitialData()
  loadAssets() // Load assets initially
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>