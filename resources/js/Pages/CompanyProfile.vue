<template>
  <Head :title="company?.name || 'Company Profile'" />

  <DashboardLayout active-tab="companies">
    <template #title>
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-6">
          <h2 class="text-3xl lg:text-4xl font-bold text-white drop-shadow-lg">
            {{ company?.name || 'Loading...' }}
          </h2>
          <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-500/20 text-blue-300 text-lg font-bold rounded-lg border border-blue-400/20">
              {{ company?.ticker || 'N/A' }}
            </span>
            <span class="text-gray-300 text-lg">
              {{ company?.sector || 'Unknown Sector' }}
            </span>
          </div>
        </div>
      </div>
    </template>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-8 max-w-md mx-auto">
        <h3 class="text-xl font-semibold text-red-400 mb-2">Company Not Found</h3>
        <p class="text-gray-300 mb-6">{{ error }}</p>
        <Link :href="route('dashboard.companies')" class="inline-flex items-center px-4 py-2 bg-blue-500/20 text-white rounded-lg hover:bg-blue-500/30 transition-colors">
          Return to Companies
        </Link>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="company" class="space-y-8">
      <!-- Company Overview Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Key Metrics -->
        <div class="bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Key Metrics
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Market Cap</span>
              <span class="text-white font-semibold">{{ company?.marketCapFormatted || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Sector</span>
              <span class="text-white font-semibold">{{ company?.sector || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Industry</span>
              <span class="text-white font-semibold">{{ company?.industry || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Research Stats -->
        <div class="bg-gradient-to-br from-green-500/10 via-emerald-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Research Notes</span>
              <span class="text-white font-semibold">{{ company?.research_items?.length || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Documents</span>
              <span class="text-white font-semibold">{{ company?.documents?.length || 0 }}</span>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="openNoteCreationModal"
              class="w-full mt-4 bg-green-500/20 hover:bg-green-500/30 text-green-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-green-400/20"
            >
              Add Research Note
            </button>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-purple-500/10 via-violet-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Quick Actions
          </h3>
          <div class="space-y-3">
            <button
              v-if="$page.props.auth.user"
              @click="openEditCompanyModal"
              class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-blue-400/20"
            >
              Edit Company
            </button>
            <button
              v-if="$page.props.auth.user"
              @click="addToWatchlist"
              class="w-full bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-purple-400/20 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              Add to Watchlist
            </button>
          </div>
        </div>
      </div>

      <!-- Company Description -->
      <div v-if="company?.description" class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
        <h3 class="text-xl font-semibold text-white mb-4">About {{ company.name }}</h3>
        <p class="text-gray-300 leading-relaxed">{{ company.description }}</p>
      </div>

      <!-- Main Content Layout with Sidebar -->
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Toggle Button (Mobile) -->
        <button
          @click="toggleSidebar"
          class="lg:hidden flex items-center justify-between w-full px-4 py-3 bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-xl border border-gray-700/30 text-white font-medium"
        >
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            Navigation
          </span>
          <svg
            :class="['w-5 h-5 transition-transform duration-200', sidebarOpen ? 'rotate-180' : '']"
            fill="none" stroke="currentColor" viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <!-- Collapsible Sidebar -->
        <div :class="[
          'transition-all duration-300 ease-in-out lg:w-80 lg:flex-shrink-0',
          sidebarOpen ? 'block' : 'hidden lg:block'
        ]">
          <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl border-t border-b border-gray-700/30 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">
            <!-- Sidebar Header -->
            <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
              <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Company Sections
              </h3>
            </div>

            <!-- Navigation Links -->
            <div class="p-4 space-y-2">
              <Link
                :href="`/companies/${company.ticker}?tab=research`"
                :class="[
                  'flex items-center w-full px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200',
                  isResearchTab
                    ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                    : 'text-gray-300 hover:text-white hover:bg-gray-800/30'
                ]"
                @click="sidebarOpen = false"
              >
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1 text-left">
                  <div class="font-medium">Research Notes</div>
                  <div class="text-xs text-gray-400">{{ company?.research_items?.length || 0 }} notes available</div>
                </div>
                <span v-if="company?.research_items?.length > 0" class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
                  {{ company.research_items.length }}
                </span>
              </Link>

              <Link
                :href="`/companies/${company.ticker}?tab=documents`"
                :class="[
                  'flex items-center w-full px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200',
                  isDocumentsTab
                    ? 'bg-orange-500/20 text-orange-300 border border-orange-400/30'
                    : 'text-gray-300 hover:text-white hover:bg-gray-800/30'
                ]"
                @click="sidebarOpen = false"
              >
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1 text-left">
                  <div class="font-medium">Documents</div>
                  <div class="text-xs text-gray-400">{{ company?.documents?.length || 0 }} documents uploaded</div>
                </div>
                <span v-if="company?.documents?.length > 0" class="bg-orange-500/20 text-orange-300 px-2 py-1 rounded-full text-xs font-medium">
                  {{ company.documents.length }}
                </span>
              </Link>
            </div>

          </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0 space-y-8">


      <!-- Research Tab Content -->
      <div v-if="isResearchTab">
        <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-b border-gray-700/30" style="backdrop-filter: blur(20px) saturate(180%);">
          <!-- Header -->
          <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                All Research Notes ({{ company?.research_items?.length || 0 }})
              </h3>
              <button
                v-if="$page.props.auth.user"
                @click="openNoteCreationModal"
                class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 font-medium rounded-lg transition-all duration-200 border border-blue-400/20 flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Research Note
              </button>
            </div>
          </div>

          <!-- All Research Notes List -->
          <div v-if="company?.research_items?.length > 0" class="divide-y divide-gray-700/30">
            <div
              v-for="item in company.research_items"
              :key="item.id"
              class="group px-6 py-4 hover:bg-gray-800/20 transition-all duration-200 cursor-pointer"
              @click="openResearchNoteModal(item)"
            >
              <!-- Note Header -->
              <div class="flex items-start justify-between mb-2">
                <h4 class="text-white font-medium text-lg group-hover:text-blue-200 transition-colors">{{ item.title }}</h4>
                <span v-if="item.category" class="text-xs px-2 py-1 bg-blue-500/20 text-blue-300 rounded-md">{{ item.category.name }}</span>
              </div>

              <!-- Content Preview -->
              <p class="text-gray-300 text-sm mb-3 line-clamp-2 leading-relaxed">{{ getContentPreview(item.content) }}</p>

              <!-- Tags -->
              <div v-if="item.tags && item.tags.length > 0" class="flex flex-wrap gap-2 mb-3">
                <div
                  v-for="tag in item.tags.slice(0, 5)"
                  :key="tag.id"
                  class="px-2 py-1 rounded-lg text-xs"
                  :style="{
                    backgroundColor: tag.color + '20',
                    borderColor: tag.color + '40',
                    color: tag.color
                  }"
                  style="border-width: 1px;"
                >
                  {{ tag.name }}
                </div>
                <span v-if="item.tags.length > 5" class="text-xs text-gray-400 px-2 py-1 bg-gray-700/20 rounded-lg">
                  +{{ item.tags.length - 5 }} more
                </span>
              </div>

              <!-- Footer -->
              <div class="flex items-center justify-between text-xs text-gray-400">
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ formatDate(item.created_at) }}
                </span>
                <span v-if="item.user" class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  {{ item.user.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="px-6 py-12 text-center">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-gray-300 mb-2">No Research Notes</h4>
            <p class="text-gray-400 mb-4">Start documenting your research and analysis for {{ company.name }}.</p>
            <button
              v-if="$page.props.auth.user"
              @click="openNoteCreationModal"
              class="px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 font-medium rounded-lg transition-all duration-200 border border-blue-400/20"
            >
              Create First Research Note
            </button>
          </div>
        </div>
      </div>

      <!-- Documents Tab Content -->
      <div v-if="isDocumentsTab">
        <div class="bg-gradient-to-br from-gray-900/20 via-gray-800/30 to-gray-900/20 backdrop-blur-xl rounded-2xl overflow-hidden border-t border-b border-gray-700/30" style="backdrop-filter: blur(20px) saturate(180%);">
          <!-- Header -->
          <div class="bg-gray-800/20 border-b border-gray-700/30 px-6 py-4">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-5 h-5 text-orange-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                All Documents ({{ company?.documents?.length || 0 }})
              </h3>
              <button
                v-if="$page.props.auth.user"
                @click="openDocumentUploadModal"
                class="px-4 py-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-300 font-medium rounded-lg transition-all duration-200 border border-orange-400/20 flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Upload Document
              </button>
            </div>
          </div>

          <!-- All Documents List -->
          <div v-if="company?.documents?.length > 0" class="divide-y divide-gray-700/30">
            <div
              v-for="document in company.documents"
              :key="document.id"
              class="group px-6 py-4 hover:bg-gray-800/20 transition-all duration-200 cursor-pointer"
              @click="openDocumentModal(document)"
            >
              <!-- Document Header -->
              <div class="flex items-start justify-between mb-2">
                <h4 class="text-white font-medium text-lg group-hover:text-orange-200 transition-colors">{{ document.title }}</h4>
                <span class="text-xs text-gray-400 capitalize px-2 py-1 bg-gray-700/30 rounded-md">{{ document.visibility }}</span>
              </div>

              <!-- Description -->
              <p v-if="document.description" class="text-gray-300 text-sm mb-3 line-clamp-2 leading-relaxed">{{ document.description }}</p>

              <!-- Attachments -->
              <div v-if="document.attachments && document.attachments.length > 0" class="flex flex-wrap gap-2 mb-3">
                <div
                  v-for="attachment in document.attachments.slice(0, 3)"
                  :key="attachment.id"
                  class="flex items-center gap-2 px-3 py-1.5 bg-orange-500/10 border border-orange-400/20 rounded-lg text-xs hover:bg-orange-500/15 transition-colors"
                >
                  <svg class="w-3.5 h-3.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                  </svg>
                  <span class="text-orange-300 font-medium">{{ attachment.file_name }}</span>
                  <span class="text-gray-400">({{ formatFileSize(attachment.size) }})</span>
                </div>
                <span v-if="document.attachments.length > 3" class="text-xs text-gray-400 px-3 py-1.5 bg-gray-700/20 rounded-lg">
                  +{{ document.attachments.length - 3 }} more
                </span>
              </div>

              <!-- Footer -->
              <div class="flex items-center justify-between text-xs text-gray-400">
                <span class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  {{ formatDate(document.created_at) }}
                </span>
                <span v-if="document.user" class="flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  {{ document.user.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="px-6 py-12 text-center">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h4 class="text-lg font-medium text-gray-300 mb-2">No Documents</h4>
            <p class="text-gray-400 mb-4">Upload research documents, reports, and files for {{ company.name }}.</p>
            <button
              v-if="$page.props.auth.user"
              @click="openDocumentUploadModal"
              class="px-4 py-2 bg-orange-500/20 hover:bg-orange-500/30 text-orange-300 font-medium rounded-lg transition-all duration-200 border border-orange-400/20"
            >
              Upload First Document
            </button>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Modals -->
    <NoteCreationModal
      :show="showNoteCreationModal"
      :selected-company="company"
      :form="noteForm"
      :errors="noteErrors"
      :creating-note="creatingNote"
      :categories="categories"
      :tags="tags"
      :user="$page.props.auth.user"
      :is-editing="isEditingResearchItem"
      @close="handleNoteModalClose"
      @save="handleNoteSave"
      @category-created="handleCategoryCreated"
    />

    <EditCompanyModal
      :show="showEditCompanyModal"
      :company="company"
      :edit-form="editForm"
      :edit-errors="editErrors"
      :saving="savingCompany"
      :edit-market-cap-input="editMarketCapInput"
      :edit-market-cap-validation="editMarketCapValidation"
      @close="showEditCompanyModal = false"
      @save-edit="handleCompanySave"
    />

    <DocumentUploadModal
      :show="showDocumentUploadModal"
      :selected-company="company"
      :form="documentForm"
      :errors="documentErrors"
      :uploading="uploadingDocument"
      :categories="categories"
      :format-file-size="formatFileSize"
      @close="showDocumentUploadModal = false"
      @save="handleDocumentSave"
    />

    <DocumentViewerModal
      :show="showDocumentViewerModal"
      :document="selectedDocument"
      :categories="categories"
      :can-edit="$page.props.auth.user && (selectedDocument?.user?.id === $page.props.auth.user.id || $page.props.auth.user.roles?.some(role => role.name === 'admin'))"
      :can-delete="$page.props.auth.user && (selectedDocument?.user?.id === $page.props.auth.user.id || $page.props.auth.user.roles?.some(role => role.name === 'admin'))"
      @close="showDocumentViewerModal = false"
    />

    <ResearchNoteModal
      :show="showResearchNoteModal"
      :research-note="selectedResearchItem"
      @close="showResearchNoteModal = false"
      @edit="handleEditResearchNote"
    />

    <AddToWatchlistModal
      :show="showAddToWatchlistModal"
      :company="company"
      @close="closeAddToWatchlistModal"
      @added="handleAddedToWatchlist"
    />

    <ToastNotification />

  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import EditCompanyModal from '@/Components/Modals/EditCompanyModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import DocumentViewerModal from '@/Components/Modals/DocumentViewerModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import AddToWatchlistModal from '@/Components/Modals/AddToWatchlistModal.vue'
import ToastNotification from '@/Components/ToastNotification.vue'

const props = defineProps({
  ticker: String,
  tab: String
})

const page = usePage()

// State
const company = ref(null)
const loading = ref(true)
const error = ref(null)
const showNoteCreationModal = ref(false)
const showEditCompanyModal = ref(false)
const showDocumentUploadModal = ref(false)
const showDocumentViewerModal = ref(false)
const showResearchNoteModal = ref(false)
const showAddToWatchlistModal = ref(false)
const selectedResearchItem = ref(null)
const selectedDocument = ref(null)
const editingResearchItem = ref(null)
const isEditingResearchItem = computed(() => !!editingResearchItem.value)

// Tab management
const activeTab = computed(() => props.tab || 'research')
const isResearchTab = computed(() => activeTab.value === 'research')
const isDocumentsTab = computed(() => activeTab.value === 'documents')

// Sidebar management
const sidebarOpen = ref(false)
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

// Modal states
const creatingNote = ref(false)
const savingCompany = ref(false)
const uploadingDocument = ref(false)

// Form objects
const noteForm = ref({
  title: '',
  content: '',
  category_id: '',
  source_date: '',
  selectedTags: [],
  visibility: 'private'
})

const noteErrors = ref({})

const editForm = ref({
  name: '',
  ticker: '',
  sector: '',
  industry: '',
  description: '',
  market_cap: ''
})

const editErrors = ref({})

// Market cap input handling for edit modal
const editMarketCapInput = ref('')
const editMarketCapValidation = ref({ state: 'neutral' })

const documentForm = ref({
  title: '',
  description: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const documentErrors = ref({})

// Categories and other data
const categories = ref([])
const tags = ref([])

// Fetch company data and other required data
onMounted(async () => {
  try {
    loading.value = true
    console.log('Fetching company with ticker:', props.ticker)

    // Fetch categories and tags in parallel with company data
    const [companiesResponse, categoriesResponse, tagsResponse] = await Promise.all([
      axios.get('/api/companies', { params: { search: props.ticker, limit: 100 } }),
      axios.get('/api/categories'),
      axios.get('/api/tags')
    ])

    // Load categories and tags
    categories.value = categoriesResponse.data
    tags.value = tagsResponse.data

    console.log('Companies API response:', companiesResponse.data)

    if (companiesResponse.data.data && companiesResponse.data.data.length > 0) {
      // Find exact ticker match (case insensitive)
      const exactMatch = companiesResponse.data.data.find(c =>
        c.ticker && c.ticker.toUpperCase() === props.ticker.toUpperCase()
      )

      if (exactMatch) {
        console.log('Found exact match:', exactMatch)
        // Now get the full company details using the ID
        const detailResponse = await axios.get(`/api/companies/${exactMatch.id}`)
        company.value = detailResponse.data
        console.log('Company details loaded:', company.value)
      } else {
        console.log('Available tickers:', companiesResponse.data.data.map(c => c.ticker))
        error.value = `Company with ticker "${props.ticker}" not found. Available companies: ${companiesResponse.data.data.map(c => c.ticker).join(', ')}`
      }
    } else {
      error.value = `No companies found in database`
    }
  } catch (err) {
    console.error('Error fetching data:', err)
    if (err.response?.status === 401) {
      error.value = 'Please log in to view company details'
    } else {
      error.value = err.response?.data?.message || 'Failed to load data'
    }
  } finally {
    loading.value = false
  }
})

// Modal handlers
const openNoteCreationModal = () => {
  // Reset form
  noteForm.value = {
    title: '',
    content: '',
    category_id: '',
    source_date: '',
    selectedTags: [],
    visibility: 'private'
  }
  noteErrors.value = {}
  showNoteCreationModal.value = true
}

const openEditCompanyModal = () => {
  // Populate form with current company data
  if (company.value) {
    // Convert market cap to a user-friendly format for editing
    let marketCapDisplayValue = company.value.marketCap || company.value.market_cap || ''

    // If we have a numeric market cap, convert it to a more readable format
    if (marketCapDisplayValue && !isNaN(marketCapDisplayValue)) {
      const numValue = parseFloat(marketCapDisplayValue)
      if (numValue >= 1000000000000) {
        marketCapDisplayValue = (numValue / 1000000000000).toFixed(1) + 'T'
      } else if (numValue >= 1000000000) {
        marketCapDisplayValue = (numValue / 1000000000).toFixed(1) + 'B'
      } else if (numValue >= 1000000) {
        marketCapDisplayValue = (numValue / 1000000).toFixed(1) + 'M'
      } else if (numValue >= 1000) {
        marketCapDisplayValue = (numValue / 1000).toFixed(1) + 'K'
      }
    }

    editForm.value = {
      name: company.value.name || '',
      ticker: company.value.ticker || '',
      sector: company.value.sector || '',
      industry: company.value.industry || '',
      market_cap: company.value.marketCap || company.value.market_cap || '',
      reports_financial_data_in: company.value.reports_financial_data_in || ''
    }

    // Set the display value for the market cap input
    editMarketCapInput.value = marketCapDisplayValue
    editMarketCapValidation.value = { state: 'neutral' }
  }
  editErrors.value = {}
  showEditCompanyModal.value = true
}

const openDocumentUploadModal = () => {
  // Reset form
  documentForm.value = {
    title: '',
    description: '',
    category_id: '',
    visibility: 'private',
    files: []
  }
  documentErrors.value = {}
  showDocumentUploadModal.value = true
}

const openResearchNoteModal = (item) => {
  selectedResearchItem.value = item
  showResearchNoteModal.value = true
}

const openDocumentModal = (document) => {
  selectedDocument.value = document
  showDocumentViewerModal.value = true
}

const handleEditResearchNote = (item) => {
  // Close the view modal
  showResearchNoteModal.value = false

  // Open the edit modal (NoteCreationModal) with the item data
  noteForm.value = {
    title: item.title || '',
    content: item.content || '',
    company_id: company.value?.id || '',
    category_id: item.category?.id || '',
    tag_ids: item.tags?.map(tag => tag.id) || [],
    visibility: item.visibility || 'private',
    ai_synopsis: item.ai_synopsis || '',
    source_date: item.source_date || ''
  }

  // Set the item being edited
  editingResearchItem.value = item

  // Show the creation/edit modal
  showNoteCreationModal.value = true
}

const handleNoteModalClose = () => {
  showNoteCreationModal.value = false
  editingResearchItem.value = null
  // Reset form if needed
  noteForm.value = {
    title: '',
    content: '',
    category_id: '',
    source_date: '',
    visibility: 'private',
    tag_ids: [],
    ai_synopsis: ''
  }
}

// Handle new category creation
const handleCategoryCreated = (newCategory) => {
  categories.value.push(newCategory)
  // Auto-select the new category
  noteForm.value.category_id = newCategory.id
}

// Save handlers for modals
const handleNoteSave = async () => {
  try {
    creatingNote.value = true
    const response = await axios.post('/api/research-items', {
      ...noteForm.value,
      company_id: company.value.id
    })

    if (!company.value.research_items) {
      company.value.research_items = []
    }
    company.value.research_items.unshift(response.data)
    showNoteCreationModal.value = false
    noteErrors.value = {}
  } catch (error) {
    noteErrors.value = error.response?.data?.errors || {}
  } finally {
    creatingNote.value = false
  }
}

const handleCompanySave = async () => {
  try {
    savingCompany.value = true
    const response = await axios.put(`/api/companies/${company.value.id}`, editForm.value)
    company.value = { ...company.value, ...response.data }
    showEditCompanyModal.value = false
    editErrors.value = {}
  } catch (error) {
    editErrors.value = error.response?.data?.errors || {}
  } finally {
    savingCompany.value = false
  }
}

const handleDocumentSave = async () => {
  try {
    uploadingDocument.value = true
    const formData = new FormData()
    formData.append('title', documentForm.value.title)
    formData.append('description', documentForm.value.description)
    formData.append('category_id', documentForm.value.category_id)
    formData.append('visibility', documentForm.value.visibility)
    formData.append('company_id', company.value.id)

    documentForm.value.files.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })

    const response = await axios.post('/api/documents', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (!company.value.documents) {
      company.value.documents = []
    }
    company.value.documents.unshift(response.data)
    showDocumentUploadModal.value = false
    documentErrors.value = {}
  } catch (error) {
    documentErrors.value = error.response?.data?.errors || {}
  } finally {
    uploadingDocument.value = false
  }
}

// Watchlist methods
const addToWatchlist = () => {
  showAddToWatchlistModal.value = true
}

const closeAddToWatchlistModal = () => {
  showAddToWatchlistModal.value = false
}

const handleAddedToWatchlist = (data) => {
  // Optional: Show a toast notification or update UI to indicate success
  console.log('Company added to watchlist(s):', data)
  // You could add a toast notification here if you have a toast system
}

// Utilities
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getContentPreview = (content) => {
  if (!content) return 'No content preview'

  // Strip HTML tags
  const stripped = content.replace(/<[^>]*>/g, '')

  // Decode HTML entities
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  const decoded = textarea.value

  // Truncate to a reasonable length for preview
  const maxLength = 150
  if (decoded.length <= maxLength) {
    return decoded
  }

  return decoded.substring(0, maxLength).trim() + '...'
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>