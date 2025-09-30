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
    </template>

    <!-- Tabs -->
    <div class="mb-6">
      <div class="flex space-x-1 bg-white/5 rounded-xl p-1 border border-white/10">
        <button
          @click="activeTab = 'research'"
          :class="[
            'flex-1 px-6 py-3 rounded-lg font-medium transition-all duration-200',
            activeTab === 'research'
              ? 'bg-green-500/20 text-green-300 border border-green-400/20'
              : 'text-gray-400 hover:text-white hover:bg-white/5'
          ]"
        >
          <div class="flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research Items
            <span v-if="researchItemsCount > 0" class="px-2 py-1 bg-green-400/20 text-green-300 text-xs rounded-full">
              {{ researchItemsCount }}
            </span>
          </div>
        </button>
        <button
          @click="activeTab = 'assets'"
          :class="[
            'flex-1 px-6 py-3 rounded-lg font-medium transition-all duration-200',
            activeTab === 'assets'
              ? 'bg-blue-500/20 text-blue-300 border border-blue-400/20'
              : 'text-gray-400 hover:text-white hover:bg-white/5'
          ]"
        >
          <div class="flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
            Assets & Documents
            <span v-if="assetsCount > 0" class="px-2 py-1 bg-blue-400/20 text-blue-300 text-xs rounded-full">
              {{ assetsCount }}
            </span>
          </div>
        </button>
      </div>
    </div>

    <!-- Research Items Tab -->
    <div v-if="activeTab === 'research'">
      <!-- Search and Filters -->
      <div class="mb-6 space-y-4">
      <!-- Search Bar -->
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search research items..."
          class="w-full px-4 py-3 pl-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-400 focus:border-green-400/50 focus:ring-2 focus:ring-green-400/20 transition-all"
          @input="debouncedSearch"
        >
        <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>

      <!-- Filters -->
      <div class="space-y-4">
        <!-- First Row: Filters -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <!-- Company Filter -->
          <select
            v-model="selectedCompany"
            @change="loadResearchItems"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
          >
            <option value="">All Companies</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">
              {{ company.name }} ({{ company.ticker }})
            </option>
          </select>

          <!-- Category Filter -->
          <select
            v-model="selectedCategory"
            @change="loadResearchItems"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>

          <!-- Visibility Filter -->
          <select
            v-model="selectedVisibility"
            @change="loadResearchItems"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
          >
            <option value="">All Visibility</option>
            <option value="public">Public</option>
            <option value="team">Team</option>
            <option value="private">Private</option>
          </select>

          <!-- Sort Options -->
          <select
            v-model="sortBy"
            @change="loadResearchItems"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-green-400/50 text-sm"
          >
            <option value="created_at_desc">Newest First</option>
            <option value="created_at_asc">Oldest First</option>
            <option value="title_asc">Title A-Z</option>
            <option value="title_desc">Title Z-A</option>
          </select>
        </div>

        <!-- Second Row: View Toggle -->
        <div class="flex justify-center sm:justify-end">
          <div class="flex items-center gap-1">
            <button
              @click="viewMode = 'cards'"
              :class="[
                'p-2 rounded transition-all duration-200',
                viewMode === 'cards'
                  ? 'text-white bg-white/10'
                  : 'text-gray-500 hover:text-gray-300'
              ]"
              title="Card View"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-4H5m14 8H5m14 4H5"></path>
              </svg>
            </button>
            <button
              @click="viewMode = 'table'"
              :class="[
                'p-2 rounded transition-all duration-200',
                viewMode === 'table'
                  ? 'text-white bg-white/10'
                  : 'text-gray-500 hover:text-gray-300'
              ]"
              title="Table View"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Research Items Display -->
    <div v-if="loading" class="text-center py-12">
      <div class="inline-flex items-center gap-2 text-gray-400">
        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Loading research items...
      </div>
    </div>

    <div v-else-if="researchItems.length === 0" class="text-center py-12">
      <div class="max-w-md mx-auto">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-400/10 to-emerald-500/10 flex items-center justify-center">
          <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">No Research Items</h3>
        <p class="text-gray-400 mb-6">{{ searchQuery ? 'No items match your search criteria.' : 'Start by creating your first research item.' }}</p>
        <button
          v-if="!searchQuery"
          @click="openCreateModal"
          class="px-6 py-3 bg-green-500/20 hover:bg-green-500/30 text-green-300 rounded-xl border border-green-400/20 transition-all duration-200"
        >
          Create Research Item
        </button>
      </div>
    </div>

    <!-- Card View -->
    <div v-else-if="viewMode === 'cards'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
      <div
        v-for="item in researchItems"
        :key="item.id"
        class="bg-gradient-to-br from-white/5 via-white/8 to-white/5 backdrop-blur-xl rounded-xl p-4 sm:p-6 border border-white/10 hover:border-green-400/30 transition-all duration-300 cursor-pointer group"
        @click="openViewModal(item)"
      >
        <!-- Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-white mb-1 line-clamp-2 group-hover:text-green-300 transition-colors">
              {{ item.title }}
            </h3>
            <div class="flex items-center gap-2 text-sm text-gray-400">
              <span>{{ formatDate(item.created_at) }}</span>
              <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
              <span class="capitalize">{{ item.visibility }}</span>
            </div>
          </div>
          <div class="flex gap-2 ml-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              @click.stop="openEditModal(item)"
              class="p-2 hover:bg-white/10 rounded-lg transition-colors"
              title="Edit"
            >
              <svg class="w-4 h-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button
              @click.stop="deleteItem(item)"
              class="p-2 hover:bg-red-500/20 rounded-lg transition-colors"
              title="Delete"
            >
              <svg class="w-4 h-4 text-gray-400 hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content Preview -->
        <p class="text-gray-300 text-sm mb-4 line-clamp-3">
          {{ getContentPreview(item.content) }}
        </p>

        <!-- Company Info -->
        <div v-if="item.company" class="flex items-center gap-2 mb-3">
          <div class="w-6 h-6 rounded bg-blue-500/20 flex items-center justify-center">
            <svg class="w-3 h-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 8h1m-1-4h1m4 4h1m-1-4h1"></path>
            </svg>
          </div>
          <span class="text-sm text-gray-400">{{ item.company.name }}</span>
          <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded">{{ item.company.ticker }}</span>
        </div>

        <!-- Category and Tags -->
        <div class="flex flex-wrap gap-2 mb-4">
          <span v-if="item.category" class="px-2 py-1 text-xs rounded" :style="{ backgroundColor: item.category.color + '20', color: item.category.color }">
            {{ item.category.name }}
          </span>
          <span
            v-for="tag in item.tags.slice(0, 3)"
            :key="tag.id"
            class="px-2 py-1 text-xs rounded"
            :style="{ backgroundColor: tag.color + '20', color: tag.color }"
          >
            {{ tag.name }}
          </span>
          <span v-if="item.tags.length > 3" class="px-2 py-1 bg-gray-500/20 text-gray-400 text-xs rounded">
            +{{ item.tags.length - 3 }}
          </span>
        </div>

        <!-- Attachments -->
        <div v-if="item.attachments && item.attachments.length > 0" class="flex items-center gap-2 text-xs text-gray-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
          </svg>
          <span>{{ item.attachments.length }} attachment{{ item.attachments.length !== 1 ? 's' : '' }}</span>
        </div>
      </div>
    </div>

    <!-- Table View -->
    <div v-else-if="viewMode === 'table'">
      <!-- Desktop Table -->
      <div class="hidden lg:block">
        <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-b border-gray-700/30" style="backdrop-filter: blur(20px) saturate(180%);">
          <!-- Table Header -->
          <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
            <div class="grid grid-cols-12 gap-4 text-xs font-medium text-gray-300 uppercase tracking-wider">
              <div class="col-span-3">Title</div>
              <div class="col-span-2">Company</div>
              <div class="col-span-1">Category</div>
              <div class="col-span-2">Tags</div>
              <div class="col-span-1">Visibility</div>
              <div class="col-span-2">Created</div>
              <div class="col-span-1 text-center">Actions</div>
            </div>
          </div>

          <!-- Table Body -->
          <div class="divide-y divide-gray-700/30">
            <div
              v-for="item in researchItems"
              :key="item.id"
              class="group px-6 py-4 hover:bg-gray-800/20 transition-all duration-200 cursor-pointer"
              @click="openViewModal(item)"
            >
              <div class="grid grid-cols-12 gap-4 items-center">
                <!-- Title -->
                <div class="col-span-3">
                  <div class="flex flex-col">
                    <div class="text-sm font-medium text-white line-clamp-1 group-hover:text-green-200 transition-colors">{{ item.title }}</div>
                    <div class="text-sm text-gray-400 line-clamp-2 mt-1">{{ getContentPreview(item.content) }}</div>
                  </div>
                </div>

                <!-- Company -->
                <div class="col-span-2">
                  <div v-if="item.company" class="flex items-center gap-2">
                    <span class="text-sm text-white truncate">{{ item.company.name }}</span>
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded shrink-0">{{ item.company.ticker }}</span>
                  </div>
                  <span v-else class="text-sm text-gray-400">—</span>
                </div>

                <!-- Category -->
                <div class="col-span-1">
                  <span v-if="item.category" class="px-2 py-1 text-xs rounded" :style="{ backgroundColor: item.category.color + '20', color: item.category.color }">
                    {{ item.category.name }}
                  </span>
                  <span v-else class="text-sm text-gray-400">—</span>
                </div>

                <!-- Tags -->
                <div class="col-span-2">
                  <div class="flex flex-wrap gap-1">
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
                </div>

                <!-- Visibility -->
                <div class="col-span-1">
                  <span class="px-2 py-1 text-xs rounded capitalize" :class="{
                    'bg-green-500/20 text-green-400': item.visibility === 'public',
                    'bg-yellow-500/20 text-yellow-400': item.visibility === 'team',
                    'bg-red-500/20 text-red-400': item.visibility === 'private'
                  }">
                    {{ item.visibility }}
                  </span>
                </div>

                <!-- Created -->
                <div class="col-span-2">
                  <div class="text-sm text-gray-400">{{ formatDate(item.created_at) }}</div>
                </div>

                <!-- Actions -->
                <div class="col-span-1 text-center">
                  <div class="flex items-center justify-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                      @click.stop="openEditModal(item)"
                      class="w-8 h-8 rounded bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                      title="Edit"
                    >
                      <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click.stop="deleteItem(item)"
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
      </div>

      <!-- Mobile List View -->
      <div class="lg:hidden space-y-3">
        <div
          v-for="item in researchItems"
          :key="item.id"
          class="bg-gradient-to-br from-white/5 via-white/8 to-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10 hover:border-green-400/30 transition-all duration-300 cursor-pointer"
          @click="openViewModal(item)"
        >
          <!-- Title and Actions -->
          <div class="flex items-start justify-between mb-3">
            <div class="flex-1 min-w-0">
              <h3 class="text-base font-semibold text-white line-clamp-2 mb-1">{{ item.title }}</h3>
              <p class="text-sm text-gray-400 line-clamp-2">{{ getContentPreview(item.content) }}</p>
            </div>
            <div class="flex gap-1 ml-3">
              <button
                @click.stop="openEditModal(item)"
                class="p-1.5 hover:bg-white/10 rounded transition-colors"
                title="Edit"
              >
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click.stop="deleteItem(item)"
                class="p-1.5 hover:bg-red-500/20 rounded transition-colors"
                title="Delete"
              >
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Meta Info -->
          <div class="space-y-2">
            <!-- Company -->
            <div v-if="item.company" class="flex items-center gap-2">
              <span class="text-xs text-gray-500">Company:</span>
              <span class="text-sm text-white">{{ item.company.name }}</span>
              <span class="px-1.5 py-0.5 bg-blue-500/20 text-blue-300 text-xs rounded">{{ item.company.ticker }}</span>
            </div>

            <!-- Category and Visibility -->
            <div class="flex items-center gap-4">
              <div v-if="item.category" class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Category:</span>
                <span class="px-2 py-1 text-xs rounded" :style="{ backgroundColor: item.category.color + '20', color: item.category.color }">
                  {{ item.category.name }}
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Visibility:</span>
                <span class="px-2 py-1 text-xs rounded capitalize" :class="{
                  'bg-green-500/20 text-green-400': item.visibility === 'public',
                  'bg-yellow-500/20 text-yellow-400': item.visibility === 'team',
                  'bg-red-500/20 text-red-400': item.visibility === 'private'
                }">
                  {{ item.visibility }}
                </span>
              </div>
            </div>

            <!-- Tags -->
            <div v-if="item.tags.length > 0" class="flex items-center gap-2">
              <span class="text-xs text-gray-500">Tags:</span>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="tag in item.tags.slice(0, 3)"
                  :key="tag.id"
                  class="px-2 py-1 text-xs rounded"
                  :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                >
                  {{ tag.name }}
                </span>
                <span v-if="item.tags.length > 3" class="px-2 py-1 bg-gray-500/20 text-gray-400 text-xs rounded">
                  +{{ item.tags.length - 3 }}
                </span>
              </div>
            </div>

            <!-- Date -->
            <div class="flex justify-between items-center text-xs text-gray-500 pt-1">
              <span>{{ formatDate(item.created_at) }}</span>
              <div v-if="item.attachments && item.attachments.length > 0" class="flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span>{{ item.attachments.length }}</span>
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

    <!-- Assets & Documents Tab -->
    <div v-if="activeTab === 'assets'">
      <!-- Assets Header -->
      <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
          <h3 class="text-xl font-semibold text-white">Assets & Documents</h3>
          <div class="flex gap-2 text-sm">
            <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-lg">
              {{ assets.length }} Total Assets
            </span>
            <span class="px-3 py-1 bg-purple-500/20 text-purple-300 rounded-lg">
              {{ documents.length }} Documents
            </span>
          </div>
        </div>
        <button
          @click="openUploadModal"
          class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl border border-blue-400/20 transition-all duration-200 flex items-center gap-2 backdrop-blur-xl"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
          </svg>
          Upload Asset
        </button>
      </div>

      <!-- Assets Search and Filters -->
      <div class="mb-6 space-y-4">
        <!-- Search Bar -->
        <div class="relative">
          <input
            v-model="assetSearchQuery"
            type="text"
            placeholder="Search assets and documents..."
            class="w-full px-4 py-3 pl-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all"
            @input="debouncedAssetSearch"
          >
          <svg class="absolute left-4 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>

        <!-- Asset Filters -->
        <div class="flex flex-wrap gap-4">
          <!-- File Type Filter -->
          <select
            v-model="selectedFileType"
            @change="loadAssets"
            class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-400/50"
          >
            <option value="">All File Types</option>
            <option value="image">Images</option>
            <option value="document">Documents</option>
            <option value="spreadsheet">Spreadsheets</option>
            <option value="pdf">PDFs</option>
            <option value="other">Other</option>
          </select>

          <!-- Source Filter -->
          <select
            v-model="selectedAssetSource"
            @change="loadAssets"
            class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-400/50"
          >
            <option value="">All Sources</option>
            <option value="research_item">Research Items</option>
            <option value="document">Documents</option>
            <option value="direct_upload">Direct Upload</option>
          </select>

          <!-- Size Filter -->
          <select
            v-model="selectedSizeFilter"
            @change="loadAssets"
            class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-400/50"
          >
            <option value="">All Sizes</option>
            <option value="small">Small (< 1MB)</option>
            <option value="medium">Medium (1-10MB)</option>
            <option value="large">Large (> 10MB)</option>
          </select>

          <!-- Asset Sort -->
          <select
            v-model="assetSortBy"
            @change="loadAssets"
            class="px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-400/50"
          >
            <option value="created_at_desc">Newest First</option>
            <option value="created_at_asc">Oldest First</option>
            <option value="size_desc">Largest First</option>
            <option value="size_asc">Smallest First</option>
            <option value="name_asc">Name A-Z</option>
            <option value="name_desc">Name Z-A</option>
          </select>
        </div>
      </div>

      <!-- Assets Grid -->
      <div v-if="loadingAssets" class="text-center py-12">
        <div class="inline-flex items-center gap-2 text-gray-400">
          <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Loading assets...
        </div>
      </div>

      <div v-else-if="filteredAssets.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
          <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-400/10 to-indigo-500/10 flex items-center justify-center">
            <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-white mb-2">No Assets Found</h3>
          <p class="text-gray-400 mb-6">{{ assetSearchQuery ? 'No assets match your search criteria.' : 'Start by uploading your first asset.' }}</p>
          <button
            v-if="!assetSearchQuery"
            @click="openUploadModal"
            class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-xl border border-blue-400/20 transition-all duration-200"
          >
            Upload Asset
          </button>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="asset in filteredAssets"
          :key="asset.id"
          class="bg-gradient-to-br from-white/5 via-white/8 to-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10 hover:border-blue-400/30 transition-all duration-300 cursor-pointer group"
          @click="openAssetModal(asset)"
        >
          <!-- Asset Preview -->
          <div class="aspect-square mb-4 rounded-lg overflow-hidden bg-white/5 flex items-center justify-center">
            <img
              v-if="isImage(asset.mime_type)"
              :src="asset.url"
              :alt="asset.title"
              class="w-full h-full object-cover"
              loading="lazy"
            >
            <div v-else class="w-16 h-16 rounded-lg flex items-center justify-center" :class="getFileTypeColor(asset.mime_type)">
              <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="isPdf(asset.mime_type)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
            </div>
          </div>

          <!-- Asset Info -->
          <div class="space-y-2">
            <h4 class="text-white font-medium text-sm line-clamp-2 group-hover:text-blue-300 transition-colors">
              {{ asset.title || asset.file_name }}
            </h4>

            <div class="flex items-center justify-between text-xs text-gray-400">
              <span class="uppercase">{{ getFileExtension(asset.file_name) }}</span>
              <span>{{ formatFileSize(asset.size) }}</span>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-500">
              <span>{{ formatDate(asset.created_at) }}</span>
              <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
              <span class="capitalize">{{ asset.source_type || 'Direct' }}</span>
            </div>

            <!-- Source Info -->
            <div v-if="asset.source_title" class="text-xs text-gray-400 line-clamp-1">
              From: {{ asset.source_title }}
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-2 mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              @click.stop="downloadAsset(asset)"
              class="flex-1 px-3 py-2 bg-white/5 hover:bg-white/10 rounded-lg transition-colors text-xs text-gray-300 hover:text-white"
            >
              Download
            </button>
            <button
              @click.stop="deleteAsset(asset)"
              class="px-3 py-2 bg-red-500/20 hover:bg-red-500/30 rounded-lg transition-colors text-xs text-red-400"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Assets Pagination -->
      <div v-if="assetPagination && assetPagination.total_pages > 1" class="mt-8 flex justify-center">
        <div class="flex items-center gap-2">
          <button
            @click="loadAssetPage(assetPagination.current_page - 1)"
            :disabled="assetPagination.current_page === 1"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/10 transition-colors"
          >
            Previous
          </button>

          <div class="flex gap-1">
            <button
              v-for="page in visibleAssetPages"
              :key="page"
              @click="loadAssetPage(page)"
              :class="[
                'px-3 py-2 rounded-lg transition-colors',
                page === assetPagination.current_page
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/20'
                  : 'bg-white/5 border border-white/10 text-white hover:bg-white/10'
              ]"
            >
              {{ page }}
            </button>
          </div>

          <button
            @click="loadAssetPage(assetPagination.current_page + 1)"
            :disabled="assetPagination.current_page === assetPagination.total_pages"
            class="px-3 py-2 bg-white/5 border border-white/10 rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/10 transition-colors"
          >
            Next
          </button>
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
      :user="$page.props.auth.user"
      :is-editing="isEditing"
      @close="handleCreateModalClose"
      @save="handleSave"
      @category-created="handleCategoryCreated"
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

// Tab state
const activeTab = ref('research')

// View mode for research items with persistence
const viewMode = ref(localStorage.getItem('research-view-mode') || 'cards')

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

// Search and filters - Research Items
const searchQuery = ref('')
const selectedCompany = ref('')
const selectedCategory = ref('')
const selectedVisibility = ref('')
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

// Form data
const createForm = ref({
  title: '',
  content: '',
  company_id: '',
  category_id: '',
  tag_ids: [],
  visibility: 'private',
  ai_synopsis: '',
  source_date: ''
})
const createErrors = ref({})

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

const visibleAssetPages = computed(() => {
  if (!assetPagination.value) return []

  const current = assetPagination.value.current_page
  const total = assetPagination.value.total_pages
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

// Methods
const loadResearchItems = async (page = 1) => {
  try {
    loading.value = true

    const params = {
      page,
      limit: 12,
      search: searchQuery.value,
      company_id: selectedCompany.value,
      category_id: selectedCategory.value,
      visibility: selectedVisibility.value
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
    loadResearchItems(1)
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
    source_date: ''
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

    const response = await axios[method](url, createForm.value)

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

// Watch view mode changes and persist to localStorage
watch(viewMode, (newMode) => {
  localStorage.setItem('research-view-mode', newMode)
})

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