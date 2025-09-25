<template>
  <Head :title="company?.name || 'Company Profile'" />

  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button and Company Header -->
        <div class="flex items-center justify-between mb-6">
          <Link
            :href="route('dashboard')"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-white/10 hover:bg-white/20 rounded-lg transition-all duration-200 backdrop-blur-xl border border-white/10"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Dashboard
          </Link>

          <!-- User Menu -->
          <div v-if="$page.props.auth.user" class="relative">
            <Dropdown align="right" width="48">
              <template #trigger>
                <button class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-300 hover:text-white focus:outline-none transition ease-in-out duration-150">
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
          <div v-else class="flex items-center gap-3">
            <button
              @click="showLoginModal = true"
              class="px-4 py-2 bg-blue-500/20 text-blue-200 rounded-lg hover:bg-blue-500/30 transition-colors"
            >
              Login
            </button>
          </div>
        </div>

        <!-- Company Header -->
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-6">
            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-2xl">
              {{ company?.name?.charAt(0)?.toUpperCase() || 'C' }}
            </div>
            <div>
              <h1 class="text-4xl font-bold text-white mb-2">{{ company?.name || 'Loading...' }}</h1>
              <p class="text-xl text-gray-300">{{ company?.ticker_symbol || 'N/A' }} • {{ company?.sector || 'Unknown Sector' }}</p>
            </div>
          </div>

          <!-- Edit Company Button -->
          <button
            v-if="$page.props.auth.user"
            @click="openEditCompanyModal"
            class="group backdrop-blur-3xl bg-gradient-to-br from-yellow-500/20 via-yellow-400/10 to-transparent hover:from-yellow-500/30 hover:via-yellow-400/15 hover:to-yellow-300/5 text-yellow-200 hover:text-white font-medium py-3 px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(245,158,11,0.2)] border border-white/10"
            style="backdrop-filter: blur(20px) saturate(180%);"
          >
            <svg class="w-5 h-5 shadow-[0_0_5px_rgba(245,158,11,0.3)] group-hover:shadow-[0_0_8px_rgba(245,158,11,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <span>Edit Company</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
    </div>

    <!-- Main Content -->
    <div v-else-if="company" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Tab Navigation -->
      <div class="border-b border-white/20 mb-8">
        <nav class="flex space-x-8" aria-label="Tabs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="handleTabClick(tab.id)"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300',
              activeTab === tab.id
                ? 'border-blue-400 text-blue-300'
                : 'border-transparent text-gray-400 hover:text-white hover:border-white/30'
            ]"
          >
            <div class="flex items-center space-x-2">
              <span>{{ tab.name }}</span>
              <span v-if="tab.count" class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10">
                {{ tab.count }}
              </span>
            </div>
          </button>
        </nav>
      </div>

      <!-- Tab Content -->
      <div class="min-h-[600px]">
        <!-- Overview Tab -->
        <div v-show="activeTab === 'overview'" class="space-y-8">
          <!-- Company Overview Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column - Key Metrics -->
            <div class="bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
              <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Key Metrics
              </h2>
              <div class="space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-gray-300 font-medium">Ticker Symbol</span>
                  <span class="text-white font-bold text-lg">{{ company?.ticker_symbol || 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-300 font-medium">Market Cap</span>
                  <span class="text-white font-bold text-lg">{{ company?.marketCapFormatted || 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-300 font-medium">Sector</span>
                  <span class="text-white">{{ company?.sector || 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                  <span class="text-gray-300 font-medium">Industry</span>
                  <span class="text-white">{{ company?.industry || 'N/A' }}</span>
                </div>
              </div>
            </div>

            <!-- Right Column - Description -->
            <div class="bg-gradient-to-br from-green-500/10 via-emerald-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
              <h2 class="text-2xl font-semibold text-white mb-6 flex items-center">
                <svg class="w-6 h-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Company Description
              </h2>
              <p class="text-gray-300 leading-relaxed">
                {{ company?.description || 'No description available.' }}
              </p>
            </div>
          </div>
        </div>


        <!-- Research Tab -->
        <div v-show="activeTab === 'research'" class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">Research Notes</h2>
              <p class="text-gray-300" v-if="company">for {{ company.name }} ({{ company.ticker_symbol }})</p>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="openCreateResearchModal"
              class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-3 px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <svg class="w-5 h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Add Research Note</span>
            </button>
          </div>

          <!-- Research Items Data Table -->
          <div v-if="company?.researchItems && company.researchItems.length > 0">
            <!-- Bulk Actions Toolbar -->
            <div v-if="selectedResearchItems.size > 0" class="mb-4 backdrop-blur-3xl bg-gradient-to-r from-red-500/10 to-red-600/20 rounded-2xl px-6 py-4 border border-red-500/20">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <span class="text-white font-medium">{{ selectedResearchItems.size }} item{{ selectedResearchItems.size !== 1 ? 's' : '' }} selected</span>
                  <button
                    @click="clearSelection"
                    class="text-gray-300 hover:text-white text-sm underline"
                  >
                    Clear selection
                  </button>
                </div>
                <button
                  @click="deleteSelectedResearchItems"
                  class="flex items-center space-x-2 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-white rounded-lg border border-red-500/30 transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                  <span>Delete Selected</span>
                </button>
              </div>
            </div>

            <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">
              <!-- Table Header -->
              <div class="bg-gradient-to-r from-white/5 to-white/2 px-6 py-4 border-b border-white/10">
                <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white/80">
                  <div class="col-span-1 flex items-center justify-center">
                    <input
                      type="checkbox"
                      :checked="selectAll"
                      @change="toggleSelectAll"
                      class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                    />
                  </div>
                  <div class="col-span-4">Research Title</div>
                  <div class="col-span-2">Category</div>
                  <div class="col-span-2">Created</div>
                  <div class="col-span-1 text-center">Files</div>
                  <div class="col-span-1 text-center">Status</div>
                  <div class="col-span-1 text-center">Actions</div>
                </div>
              </div>

              <!-- Table Body -->
              <div class="divide-y divide-white/5">
                <div
                  v-for="(item, index) in company.researchItems"
                  :key="item.id"
                  :class="[
                    'group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300',
                    selectedResearchItems.has(item.id) ? 'bg-blue-500/10 border-l-4 border-blue-500/50' : 'cursor-pointer'
                  ]"
                  @click="!selectedResearchItems.has(item.id) ? viewResearchItem(item) : null"
                >
                  <div class="grid grid-cols-12 gap-4 items-center text-sm">

                    <!-- Checkbox -->
                    <div class="col-span-1 flex items-center justify-center">
                      <input
                        type="checkbox"
                        :checked="selectedResearchItems.has(item.id)"
                        @change="toggleItemSelection(item.id)"
                        @click.stop
                        class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                      />
                    </div>

                    <!-- Title -->
                    <div class="col-span-4">
                      <h3 class="text-base font-medium text-white/90 line-clamp-1 mb-1">{{ item.title }}</h3>
                      <p class="text-xs text-blue-200/60 line-clamp-1">{{ item.content ? item.content.substring(0, 80) + '...' : 'No content preview' }}</p>
                    </div>

                    <!-- Category -->
                    <div class="col-span-2">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30">
                        {{ item.category?.name || 'Uncategorized' }}
                      </span>
                    </div>

                    <!-- Created Date -->
                    <div class="col-span-2">
                      <div class="text-white/70">
                        <div class="font-medium">{{ formatDate(item.created_at) }}</div>
                        <div class="text-xs text-white/50">{{ new Date(item.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                      </div>
                    </div>

                    <!-- Files Count -->
                    <div class="col-span-1 text-center">
                      <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-white/70 font-medium">{{ item.attachments?.length || 0 }}</span>
                      </div>
                    </div>

                    <!-- Status -->
                    <div class="col-span-1 text-center">
                      <div class="flex items-center justify-center">
                        <div v-if="item.visibility === 'public'" class="flex items-center text-green-400">
                          <svg class="w-4 h-4 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                        </div>
                        <div v-else class="flex items-center text-white/40">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                          </svg>
                        </div>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-1">
                      <div class="flex items-center justify-center space-x-2">
                        <button
                          @click.stop="editResearchItem(item)"
                          class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                          title="Edit Research Note"
                        >
                          <svg class="w-3.5 h-3.5 text-blue-200 group-hover:text-white shadow-[0_0_5px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button
                          @click.stop="deleteResearchItem(item)"
                          class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-red-500/20 to-red-600/30 hover:from-red-500/30 hover:to-red-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(239,68,68,0.15)] hover:shadow-[0_0_15px_rgba(239,68,68,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                          title="Delete Research Note"
                        >
                          <svg class="w-3.5 h-3.5 text-red-200 group-hover:text-white shadow-[0_0_5px_rgba(239,68,68,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/20 mb-6 mx-auto backdrop-blur-xl border border-white/10" style="backdrop-filter: blur(20px) saturate(180%);">
              <svg class="w-10 h-10 text-blue-300 shadow-[0_0_10px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white/90 mb-3">No Research Notes Yet</h3>
            <p class="text-gray-300 mb-6 max-w-md mx-auto">Start documenting your research, analysis, and insights for {{ company?.name || 'this company' }}.</p>
            <button
              v-if="$page.props.auth.user"
              @click="openCreateResearchModal"
              class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <span class="shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]">🚀 Create First Research Note</span>
            </button>
          </div>
        </div>


        <!-- Documents Tab -->
        <div v-show="activeTab === 'documents'" class="space-y-6">
          <!-- Header with Upload Button -->
          <div class="flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-white">Documents</h2>
            <button
              v-if="$page.props.auth.user"
              @click="showUploadDocumentModal = true"
              class="group backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent hover:from-green-500/30 hover:via-green-400/15 hover:to-green-300/5 text-green-200 font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <svg class="w-5 h-5 shadow-[0_0_5px_rgba(34,197,94,0.3)] group-hover:shadow-[0_0_8px_rgba(34,197,94,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Upload Document</span>
            </button>
          </div>

          <!-- Documents Data Table -->
          <div v-if="company?.documents && company.documents.length > 0">
            <!-- Bulk Actions Toolbar -->
            <div v-if="selectedDocuments.size > 0" class="mb-4 backdrop-blur-3xl bg-gradient-to-r from-red-500/10 to-red-600/20 rounded-2xl px-6 py-4 border border-red-500/20">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <span class="text-white font-medium">{{ selectedDocuments.size }} item{{ selectedDocuments.size !== 1 ? 's' : '' }} selected</span>
                  <button
                    @click="clearDocumentSelection"
                    class="text-gray-300 hover:text-white text-sm underline"
                  >
                    Clear selection
                  </button>
                </div>
                <button
                  @click="bulkDeleteDocuments"
                  class="px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-200 rounded-lg transition-colors border border-red-500/30 hover:border-red-500/50 font-medium"
                >
                  Delete Selected
                </button>
              </div>
            </div>

            <!-- Table Container -->
            <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.15)]">
              <!-- Table Header -->
              <div class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 px-6 py-4 border-b border-white/10">
                <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium text-gray-300 uppercase tracking-wider">
                  <div class="col-span-1">
                    <input
                      type="checkbox"
                      :checked="selectAllDocuments"
                      @change="toggleSelectAllDocuments"
                      class="rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-500/25"
                    />
                  </div>
                  <div class="col-span-4">Title & Description</div>
                  <div class="col-span-2">Company</div>
                  <div class="col-span-2">Created</div>
                  <div class="col-span-1 text-center">Files</div>
                  <div class="col-span-1 text-center">Source</div>
                  <div class="col-span-1 text-center">Actions</div>
                </div>
              </div>
              <!-- Table Body -->
              <div class="divide-y divide-white/5">
                <div
                  v-for="(doc, index) in company.documents"
                  :key="doc.id"
                  :class="[
                    'group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300',
                    selectedDocuments.has(doc.id) ? 'bg-blue-500/10 border-l-4 border-blue-500/50' : 'cursor-pointer'
                  ]"
                  @click="!selectedDocuments.has(doc.id) ? viewDocument(doc) : null"
                >
                  <div class="grid grid-cols-12 gap-4 items-center text-sm">
                    <!-- Checkbox -->
                    <div class="col-span-1">
                      <input
                        type="checkbox"
                        :checked="selectedDocuments.has(doc.id)"
                        @change="toggleDocumentSelection(doc.id)"
                        @click.stop
                        class="rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-500/25"
                      />
                    </div>

                    <!-- Title & Description -->
                    <div class="col-span-4">
                      <div class="font-medium text-white group-hover:text-blue-300 transition-colors mb-1 cursor-pointer" @click.stop="viewDocument(doc)">
                        {{ doc.title }}
                      </div>
                      <div class="text-gray-400 text-xs line-clamp-2">
                        {{ doc.description || 'No description provided' }}
                      </div>
                    </div>

                    <!-- Company -->
                    <div class="col-span-2">
                      <div class="text-white font-medium">{{ doc.company.name }}</div>
                      <div class="text-gray-400 text-xs">{{ doc.company.ticker_symbol }}</div>
                    </div>

                    <!-- Created Date -->
                    <div class="col-span-2 text-gray-300">
                      <div>{{ formatDate(doc.created_at).split(' ')[0] }}</div>
                      <div class="text-xs text-gray-400">{{ formatDate(doc.created_at).split(' ')[1] }}</div>
                    </div>

                    <!-- Files Count -->
                    <div class="col-span-1 text-center">
                      <span class="inline-flex items-center justify-center w-8 h-6 text-xs font-medium bg-blue-500/20 text-blue-300 rounded-full">
                        {{ doc.attachments?.length || 0 }}
                      </span>
                    </div>

                    <!-- Source -->
                    <div class="col-span-1 text-center">
                      <span
                        :class="[
                          'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                          doc.source_type === 'document' ? 'bg-green-500/20 text-green-300' : 'bg-purple-500/20 text-purple-300'
                        ]"
                      >
                        {{ doc.source_type === 'document' ? 'Direct' : 'Research' }}
                      </span>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-1">
                      <div class="flex items-center justify-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button
                          @click.stop="viewDocument(doc)"
                          class="p-1.5 text-blue-400 hover:text-blue-300 hover:bg-blue-500/20 rounded transition-colors"
                          title="View"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                          </svg>
                        </button>
                        <div v-if="(doc.source_type === 'document' || doc.is_orphaned) && $page.props.auth.user && doc.user_id === $page.props.auth.user.id" class="flex space-x-1">
                          <button
                            @click.stop="editDocument(doc)"
                            class="p-1.5 text-yellow-400 hover:text-yellow-300 hover:bg-yellow-500/20 rounded transition-colors"
                            title="Edit"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                          </button>
                          <button
                            @click.stop="showDeleteConfirmation(doc, 'Document')"
                            class="p-1.5 text-red-400 hover:text-red-300 hover:bg-red-500/20 rounded transition-colors"
                            title="Delete"
                          >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16"/>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-12">
            <div class="text-center text-gray-400">
              <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              <h3 class="text-xl font-medium text-white mb-2">No documents available</h3>
              <p class="text-gray-400 mb-6">Upload documents or add research notes with attachments to see them here</p>
              <button
                v-if="$page.props.auth.user"
                @click="showUploadDocumentModal = true"
                class="group backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent hover:from-green-500/30 hover:via-green-400/15 hover:to-green-300/5 text-green-200 font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <span class="shadow-[0_0_5px_rgba(34,197,94,0.3)] group-hover:shadow-[0_0_8px_rgba(34,197,94,0.4)]">🚀 Upload First Document</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Insights Tab -->
        <div v-show="activeTab === 'insights'" class="space-y-6">
          <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
            <h2 class="text-2xl font-semibold text-white mb-6">Insights</h2>
            <p class="text-gray-400">Company insights will be displayed here.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="text-center">
        <h2 class="text-2xl font-semibold text-white mb-4">Company Not Found</h2>
        <p class="text-gray-300 mb-6">The company you're looking for could not be found.</p>
        <Link
          :href="route('dashboard')"
          class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors"
        >
          Return to Dashboard
        </Link>
      </div>
    </div>

    <!-- Modals -->
    <LoginModal
      :show="showLoginModal"
      :can-reset-password="true"
      @close="showLoginModal = false"
    />

    <!-- Delete Confirmation Modal -->
    <DeleteConfirmationModal
      :show="showDeleteModal"
      :item-name="bulkDeleteOperation ? 'selected items' : (deleteItem?.title || deleteItem?.name || '')"
      :item-type="deleteItemType"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />

    <!-- Research Creation Modal -->
    <NoteCreationModal
      v-if="company"
      :show="showCreateResearchModal"
      :selectedCompany="company"
      :form="researchForm"
      :errors="researchErrors"
      :creatingNote="creatingResearch"
      :categories="categories"
      :availableFiles="Array.isArray(availableFiles) ? availableFiles : []"
      :loadingExistingFiles="loadingExistingFiles"
      :isEditing="isEditingResearch"
      @close="showCreateResearchModal = false"
      @save="handleResearchSave"
      @file-upload="handleResearchFileUpload"
      @add-url="handleResearchAddUrl"
      @remove-url="handleResearchRemoveUrl"
      @remove-file="handleResearchRemoveFile"
      @search-existing-files="handleSearchExistingFiles"
      @load-existing-files="handleLoadExistingFiles"
      @toggle-file-selection="handleToggleFileSelection"
    />

    <!-- Document Upload Modal -->
    <DocumentUploadModal
      v-if="company"
      :show="showUploadDocumentModal"
      :selectedCompany="company"
      :form="documentForm"
      :errors="documentErrors"
      :uploading="uploadingDocument"
      :categories="categories"
      :formatFileSize="formatFileSize"
      :availableFiles="availableFiles"
      @close="showUploadDocumentModal = false"
      @save="handleDocumentSave"
      @file-upload="handleDocumentFileUpload"
      @remove-file="handleDocumentRemoveFile"
      @add-url="handleDocumentAddUrl"
      @remove-url="handleDocumentRemoveUrl"
      @load-existing-files="handleDocumentLoadExistingFiles"
      @toggle-file-selection="handleDocumentToggleFileSelection"
    />

    <!-- Document Viewer Modal -->
    <DocumentViewerModal
      :show="showDocumentViewer"
      :document="selectedDocument"
      :canEdit="$page.props.auth.user?.id === selectedDocument?.user?.id"
      :canDelete="$page.props.auth.user?.id === selectedDocument?.user?.id"
      @close="showDocumentViewer = false"
      @edit="editDocumentFromModal"
      @delete="deleteDocumentFromModal"
    />

    <!-- Company Edit Modal -->
    <EditCompanyModal
      v-if="company"
      :show="showEditCompanyModal"
      :company="company"
      :editForm="editCompanyForm"
      :editErrors="editCompanyErrors"
      :saving="updatingCompany"
      :editMarketCapInput="editCompanyForm.marketCapInput"
      :editMarketCapValidation="editCompanyForm.marketCapValidation"
      :formatMarketCap="formatMarketCap"
      @close="showEditCompanyModal = false"
      @save-edit="handleEditCompanySave"
      @update:edit-form="updateEditCompanyForm"
      @edit-market-cap-input="handleMarketCapInput"
    />

    <!-- Research Item Viewer Modal -->
    <ResearchNoteModal
      v-if="selectedResearchItem"
      :show="showResearchViewer"
      :researchNote="selectedResearchItem"
      @close="closeResearchViewer"
    />

    <!-- Toast Notifications -->
    <ToastNotification />

  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import LoginModal from '@/Components/Modals/LoginModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import DocumentViewerModal from '@/Components/Modals/DocumentViewerModal.vue'
import EditCompanyModal from '@/Components/Modals/EditCompanyModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import DeleteConfirmationModal from '@/Components/Modals/DeleteConfirmationModal.vue'
import ToastNotification from '@/Components/ToastNotification.vue'

const props = defineProps({
  ticker: {
    type: String,
    required: true
  },
  tab: {
    type: String,
    default: 'overview'
  }
})

// Reactive data
const company = ref(null)
const loading = ref(true)
const error = ref(null)
const showLoginModal = ref(false)
const activeTab = ref(props.tab)

// Modal states
const showCreateResearchModal = ref(false)
const showUploadDocumentModal = ref(false)
const showDocumentViewer = ref(false)
const showEditCompanyModal = ref(false)
const showResearchViewer = ref(false)
const selectedResearchItem = ref(null)

// Delete confirmation modal state
const showDeleteModal = ref(false)
const deleteItem = ref(null)
const deleteItemType = ref('')
const selectedDocument = ref(null)

// Bulk delete state
const bulkDeleteOperation = ref('')
const bulkDeleteCount = ref(0)

// Multiselect state
const selectedResearchItems = ref(new Set())
const selectAll = ref(false)

// Document multiselect state
const selectedDocuments = ref(new Set())
const selectAllDocuments = ref(false)

// Edit state
const isEditingResearch = ref(false)
const editingResearchId = ref(null)

// Forms
const researchForm = ref({
  title: '',
  content: '',
  company_id: null,
  category_id: '',
  visibility: 'private',
  uploadType: 'file',
  files: [],
  urls: [],
  newUrl: '',
  selectedExistingFiles: []
})

const documentForm = ref({
  title: '',
  description: '',
  company_id: null,
  uploadType: 'file',
  files: [],
  urls: [],
  newUrl: '',
  selectedExistingFiles: []
})

const editCompanyForm = ref({
  name: '',
  ticker_symbol: '',
  sector: '',
  industry: '',
  market_cap: '',
  description: '',
  headquarters: '',
  employees: null,
  founded_date: '',
  website: '',
  reports_financial_data_in: '',
  marketCapInput: '',
  marketCapValidation: { state: '', timer: null }
})

// Loading states
const creatingResearch = ref(false)
const uploadingDocument = ref(false)
const updatingCompany = ref(false)

// Error states
const researchErrors = ref({})
const documentErrors = ref({})
const editCompanyErrors = ref({})

// Additional data
const categories = ref([])
const availableFiles = ref([])
const loadingExistingFiles = ref(false)

// Tab configuration
const tabs = computed(() => {
  const baseTabs = [
    {
      id: 'overview',
      name: 'Overview',
      count: null
    },
    {
      id: 'research',
      name: 'Research',
      count: company.value?.researchItems?.length || 0
    },
    {
      id: 'documents',
      name: 'Documents',
      count: company.value?.documents?.length || 0
    },
    {
      id: 'insights',
      name: 'Insights',
      count: null
    }
  ]

  return baseTabs
})

// Methods
const handleTabClick = (tabId) => {
  activeTab.value = tabId
}

const fetchCompanyData = async () => {
  try {
    loading.value = true
    error.value = null

    // First try to find company by ticker symbol
    const companiesResponse = await axios.get('/api/companies', {
      params: { search: props.ticker }
    })

    const foundCompany = companiesResponse.data.data.find(
      c => c.ticker_symbol?.toLowerCase() === props.ticker.toLowerCase()
    )

    if (!foundCompany) {
      error.value = 'Company not found'
      return
    }

    // Get detailed company data
    const response = await axios.get(`/api/companies/${foundCompany.id}`)
    company.value = response.data

    // Load unified documents list (includes both direct documents and research note attachments)
    const documentsResponse = await axios.get('/api/documents', {
      params: { company_id: foundCompany.id }
    })
    company.value.documents = documentsResponse.data.data

  } catch (err) {
    console.error('Error fetching company data:', err)
    if (err.response?.status === 401) {
      showLoginModal.value = true
    } else {
      error.value = 'Failed to load company data'
    }
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleDateString()
}

const formatFileSize = (bytes) => {
  if (!bytes) return ''
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i]
}

const formatMarketCap = (value) => {
  if (!value) return ''

  // Remove any non-numeric characters except decimal points
  const num = parseFloat(value.toString().replace(/[^\d.-]/g, ''))
  if (isNaN(num)) return ''

  if (num >= 1e12) {
    return '$' + (num / 1e12).toFixed(1) + 'T'
  } else if (num >= 1e9) {
    return '$' + (num / 1e9).toFixed(1) + 'B'
  } else if (num >= 1e6) {
    return '$' + (num / 1e6).toFixed(1) + 'M'
  } else if (num >= 1e3) {
    return '$' + (num / 1e3).toFixed(1) + 'K'
  } else {
    return '$' + num.toString()
  }
}

const parseMarketCapInput = (input) => {
  if (!input || input.trim() === '') return null

  // Remove currency symbols and whitespace, keep only numbers, decimals, and K/M/B/T
  const cleaned = input.toString().replace(/[\$,\s]/g, '').replace(/[^\d.kmbtKMBT]/gi, '')
  if (!cleaned) return null

  // Check if it ends with a valid shorthand letter
  const lastChar = cleaned.slice(-1).toLowerCase()
  const isShorthand = ['k', 'm', 'b', 't'].includes(lastChar)

  let numPart
  if (isShorthand) {
    // Extract the numeric part before the shorthand letter
    numPart = parseFloat(cleaned.slice(0, -1))
  } else {
    // No shorthand, treat entire string as number
    numPart = parseFloat(cleaned)
  }

  // Validate the numeric part
  if (isNaN(numPart) || numPart < 0) return null

  // Apply multipliers for shorthand notation
  if (isShorthand) {
    switch (lastChar) {
      case 'k': return numPart * 1e3
      case 'm': return numPart * 1e6
      case 'b': return numPart * 1e9
      case 't': return numPart * 1e12
      default: return null
    }
  } else {
    // Direct number input - must be positive
    return numPart
  }
}

// Research Item Methods
const viewResearchItem = (item) => {
  selectedResearchItem.value = item
  showResearchViewer.value = true
}

const closeResearchViewer = () => {
  showResearchViewer.value = false
  selectedResearchItem.value = null
}


const deleteResearchItem = async (item) => {
  showDeleteConfirmation(item, 'Research Note')
}

const performDeleteResearchItem = async (item) => {
  try {
    await axios.delete(`/api/research-items/${item.id}`)
    // Remove from company data
    if (company.value.researchItems) {
      const index = company.value.researchItems.findIndex(r => r.id === item.id)
      if (index !== -1) {
        company.value.researchItems.splice(index, 1)
      }
    }
    // Remove from selection if it was selected
    selectedResearchItems.value.delete(item.id)
  } catch (error) {
    console.error('Error deleting research item:', error)
    window.showToast('Failed to delete research item', 'error')
  }
}

// Multiselect methods
const toggleItemSelection = (itemId) => {
  if (selectedResearchItems.value.has(itemId)) {
    selectedResearchItems.value.delete(itemId)
  } else {
    selectedResearchItems.value.add(itemId)
  }
  updateSelectAllState()
}

const toggleSelectAll = () => {
  if (selectAll.value) {
    // Deselect all
    selectedResearchItems.value.clear()
  } else {
    // Select all
    if (company.value?.researchItems) {
      company.value.researchItems.forEach(item => {
        selectedResearchItems.value.add(item.id)
      })
    }
  }
  updateSelectAllState()
}

const updateSelectAllState = () => {
  const totalItems = company.value?.researchItems?.length || 0
  selectAll.value = totalItems > 0 && selectedResearchItems.value.size === totalItems
}

const clearSelection = () => {
  selectedResearchItems.value.clear()
  selectAll.value = false
}

const deleteSelectedResearchItems = () => {
  const selectedCount = selectedResearchItems.value.size
  if (selectedCount === 0) return

  showBulkDeleteConfirmation('research', selectedCount)
}

const performBulkDeleteResearchItems = async () => {
  const selectedIds = Array.from(selectedResearchItems.value)
  let deletedCount = 0
  let failedCount = 0

  try {
    // Delete items one by one to handle individual failures
    for (const itemId of selectedIds) {
      try {
        await axios.delete(`/api/research-items/${itemId}`)

        // Remove from company data
        if (company.value.researchItems) {
          const index = company.value.researchItems.findIndex(r => r.id === itemId)
          if (index !== -1) {
            company.value.researchItems.splice(index, 1)
          }
        }

        selectedResearchItems.value.delete(itemId)
        deletedCount++
      } catch (error) {
        console.error(`Error deleting research item ${itemId}:`, error)
        failedCount++
      }
    }

    // Show result message
    if (failedCount === 0) {
      window.showToast(`Successfully deleted ${deletedCount} research item${deletedCount !== 1 ? 's' : ''}`, 'success')
    } else {
      window.showToast(`Deleted ${deletedCount} item${deletedCount !== 1 ? 's' : ''} successfully.\n${failedCount} item${failedCount !== 1 ? 's' : ''} could not be deleted.`, 'warning')
    }

    updateSelectAllState()
  } catch (error) {
    console.error('Error in bulk delete operation:', error)
    window.showToast('An error occurred during the delete operation. Some items may not have been deleted.', 'error')
  }
}


// Document Methods
const viewDocument = (doc) => {
  selectedDocument.value = doc
  showDocumentViewer.value = true
}

const downloadDocument = (doc) => {
  if (doc.file_url) {
    const a = document.createElement('a')
    a.href = doc.file_url
    a.download = doc.title
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
  }
}

const editDocument = (doc) => {
  // Initialize form with document data
  documentForm.value = {
    title: doc.title || '',
    description: doc.description || '',
    company_id: doc.company_id,
    files: []
  }
  showUploadDocumentModal.value = true
}

const editDocumentFromModal = (doc) => {
  showDocumentViewer.value = false
  editDocument(doc)
}

const deleteDocumentFromModal = async (doc) => {
  showDocumentViewer.value = false
  await deleteDocument(doc)
}

// Delete confirmation helpers
const showDeleteConfirmation = (item, type) => {
  deleteItem.value = item
  deleteItemType.value = type
  bulkDeleteOperation.value = ''
  bulkDeleteCount.value = 0
  showDeleteModal.value = true
}

const showBulkDeleteConfirmation = (operation, count) => {
  bulkDeleteOperation.value = operation
  bulkDeleteCount.value = count
  deleteItem.value = null

  if (operation === 'research') {
    deleteItemType.value = `${count} Research Note${count !== 1 ? 's' : ''}`
  } else if (operation === 'document') {
    deleteItemType.value = `${count} Document${count !== 1 ? 's' : ''}`
  }

  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  deleteItem.value = null
  deleteItemType.value = ''
  bulkDeleteOperation.value = ''
  bulkDeleteCount.value = 0
}

const confirmDelete = () => {
  const item = deleteItem.value
  const type = deleteItemType.value
  const bulkOp = bulkDeleteOperation.value

  closeDeleteModal()

  // Route to appropriate delete function
  if (bulkOp === 'research') {
    performBulkDeleteResearchItems()
  } else if (bulkOp === 'document') {
    performBulkDeleteDocuments()
  } else if (type === 'Document') {
    performDeleteDocument(item)
  } else if (type === 'Research Note') {
    performDeleteResearchItem(item)
  }
}

const deleteDocument = async (doc) => {
  showDeleteConfirmation(doc, 'Document')
}

const performDeleteDocument = async (doc) => {
  try {
    // Use the appropriate endpoint based on whether it's orphaned or not
    const endpoint = doc.is_orphaned
      ? `/api/assets/${doc.id}`             // Direct asset deletion for orphaned files
      : `/api/documents/${doc.source_id}`   // Original document endpoint for non-orphaned

    await axios.delete(endpoint)
    // Remove from company data
    if (company.value.documents) {
      const index = company.value.documents.findIndex(d => d.id === doc.id)
      if (index !== -1) {
        company.value.documents.splice(index, 1)
      }
    }
  } catch (error) {
    console.error('Error deleting document:', error)
    window.showToast('Failed to delete document', 'error')
  }
}


const handleDocumentAddUrl = () => {
  if (documentForm.value.newUrl && documentForm.value.newUrl.trim()) {
    documentForm.value.urls.push(documentForm.value.newUrl.trim())
    documentForm.value.newUrl = ''
  }
}

const handleDocumentRemoveUrl = (index) => {
  documentForm.value.urls.splice(index, 1)
}

const handleDocumentLoadExistingFiles = async () => {
  try {
    const response = await axios.get('/api/media/available')
    availableFiles.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('Error loading existing files:', error)
    availableFiles.value = []
  }
}

const handleDocumentToggleFileSelection = (file) => {
  if (!documentForm.value.selectedExistingFiles) {
    documentForm.value.selectedExistingFiles = []
  }

  const index = documentForm.value.selectedExistingFiles.findIndex(f => f.id === file.id)
  if (index > -1) {
    documentForm.value.selectedExistingFiles.splice(index, 1)
  } else {
    documentForm.value.selectedExistingFiles.push(file)
  }
}

const handleDocumentFileUpload = (event) => {
  const files = Array.from(event.target.files)
  documentForm.value.files.push(...files)
}

const handleDocumentRemoveFile = (index) => {
  documentForm.value.files.splice(index, 1)
}

const handleDocumentSave = async () => {
  if (!company.value) return

  uploadingDocument.value = true
  documentErrors.value = {}

  try {
    const formData = new FormData()

    // Add basic fields
    formData.append('title', documentForm.value.title)
    formData.append('description', documentForm.value.description || '')
    formData.append('company_id', company.value.id)
    formData.append('visibility', documentForm.value.visibility || 'private')

    const uploadType = documentForm.value.uploadType || 'file'

    if (uploadType === 'file') {
      // Handle file uploads
      if (documentForm.value.files) {
        documentForm.value.files.forEach((file, index) => {
          formData.append(`attachments[${index}]`, file)
        })
      }
    } else if (uploadType === 'url') {
      // Handle URL downloads
      if (documentForm.value.urls && documentForm.value.urls.length > 0) {
        documentForm.value.urls.forEach((url, index) => {
          formData.append(`document_urls[${index}]`, url)
          formData.append(`document_names[${index}]`, `Document from ${new URL(url).hostname}`)
        })
      }
    } else if (uploadType === 'existing') {
      // Handle existing file selection
      if (documentForm.value.selectedExistingFiles && documentForm.value.selectedExistingFiles.length > 0) {
        documentForm.value.selectedExistingFiles.forEach((file, index) => {
          formData.append(`existing_files[${index}]`, file.id)
        })
      }
    }

    const response = await axios.post('/api/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Add the new document to the company documents
    if (!company.value.documents) {
      company.value.documents = []
    }
    company.value.documents.unshift(response.data)

    // Reset form
    documentForm.value = {
      title: '',
      description: '',
      company_id: company.value.id,
      uploadType: 'file',
      files: [],
      urls: [],
      newUrl: '',
      selectedExistingFiles: []
    }

    showUploadDocumentModal.value = false
  } catch (error) {
    if (error.response?.status === 422) {
      documentErrors.value = error.response.data.errors || {}
    } else if (error.response?.status === 401) {
      showLoginModal.value = true
    } else {
      documentErrors.value = { general: 'Failed to upload document. Please try again.' }
    }
    console.error('Error uploading document:', error)
  } finally {
    uploadingDocument.value = false
  }
}

const handleDocumentSuccess = () => {
  showUploadDocumentModal.value = false
  // Refresh company data
  fetchCompanyData()
}

// Document multiselect methods
const toggleDocumentSelection = (docId) => {
  if (selectedDocuments.value.has(docId)) {
    selectedDocuments.value.delete(docId)
  } else {
    selectedDocuments.value.add(docId)
  }
  updateDocumentSelectAllState()
}

const toggleSelectAllDocuments = () => {
  if (selectAllDocuments.value) {
    selectedDocuments.value.clear()
  } else {
    if (company.value?.documents) {
      company.value.documents.forEach(doc => {
        selectedDocuments.value.add(doc.id)
      })
    }
  }
  updateDocumentSelectAllState()
}

const updateDocumentSelectAllState = () => {
  const totalDocs = company.value?.documents?.length || 0
  selectAllDocuments.value = totalDocs > 0 && selectedDocuments.value.size === totalDocs
}

const clearDocumentSelection = () => {
  selectedDocuments.value.clear()
  selectAllDocuments.value = false
}

const bulkDeleteDocuments = () => {
  const selectedCount = selectedDocuments.value.size
  if (selectedCount === 0) return

  showBulkDeleteConfirmation('document', selectedCount)
}

const performBulkDeleteDocuments = async () => {
  const selectedIds = Array.from(selectedDocuments.value)
  let deletedCount = 0
  let skippedCount = 0

  try {
    // Process each selected document
    for (const docId of selectedIds) {
      const doc = company.value.documents.find(d => d.id === docId)

      if (doc && (doc.source_type === 'document' || doc.is_orphaned)) {
        try {
          // Use the appropriate endpoint based on whether it's orphaned or not
          const endpoint = doc.is_orphaned
            ? `/api/assets/${doc.id}`            // Direct asset deletion for orphaned files
            : `/api/documents/${doc.source_id}`  // Original document endpoint for non-orphaned

          await axios.delete(endpoint)
          deletedCount++

          // Remove from company data
          if (company.value.documents) {
            const index = company.value.documents.findIndex(d => d.id === docId)
            if (index !== -1) {
              company.value.documents.splice(index, 1)
            }
          }
        } catch (error) {
          console.error(`Error deleting document ${docId}:`, error)
        }
      } else {
        // Skip non-orphaned research note attachments
        skippedCount++
      }

      selectedDocuments.value.delete(docId)
    }

    // Show result message
    if (skippedCount > 0) {
      window.showToast(`Deleted ${deletedCount} document${deletedCount !== 1 ? 's' : ''} successfully.\n${skippedCount} research note attachment${skippedCount !== 1 ? 's' : ''} were skipped (cannot be deleted from here).`, 'info')
    } else if (deletedCount > 0) {
      console.log(`Successfully deleted ${deletedCount} document${deletedCount !== 1 ? 's' : ''}`)
    }

    updateDocumentSelectAllState()
  } catch (error) {
    console.error('Error in bulk delete operation:', error)
    if (error.response?.status === 401) {
      showLoginModal.value = true
    } else {
      window.showToast('An error occurred during the delete operation.', 'error')
    }
  }
}

// Company edit methods
const openEditCompanyModal = () => {
  // Populate the form with current company data
  editCompanyForm.value = {
    name: company.value?.name || '',
    ticker_symbol: company.value?.ticker_symbol || company.value?.ticker || '',
    sector: company.value?.sector || '',
    industry: company.value?.industry || '',
    market_cap: company.value?.market_cap || company.value?.marketCap || '',
    description: company.value?.description || '',
    headquarters: company.value?.headquarters || '',
    employees: company.value?.employees || null,
    founded_date: company.value?.founded_date || company.value?.foundedDate || '',
    website: company.value?.website || company.value?.website_url || '',
    reports_financial_data_in: company.value?.reports_financial_data_in || '',
    marketCapInput: formatMarketCap(company.value?.market_cap || company.value?.marketCap) || '',
    marketCapValidation: { state: '', timer: null }
  }

  editCompanyErrors.value = {}
  showEditCompanyModal.value = true
}

const handleEditCompanySave = async () => {
  if (!company.value) return

  updatingCompany.value = true
  editCompanyErrors.value = {}

  try {
    // Map frontend form fields to backend expected fields
    const requestData = {
      ...editCompanyForm.value,
      website_url: editCompanyForm.value.website // Map website to website_url
    }

    // Remove the frontend-only fields
    delete requestData.website
    delete requestData.marketCapInput
    delete requestData.marketCapValidation

    // Ensure market_cap is a number, not string
    if (requestData.market_cap) {
      requestData.market_cap = parseFloat(requestData.market_cap)
      if (isNaN(requestData.market_cap)) {
        requestData.market_cap = null
      }
    }

    console.log('Updating company with data:', requestData)

    const response = await axios.put(`/api/companies/${company.value.id}`, requestData)

    console.log('Company update response:', response.data)

    // Update the local company data
    company.value = { ...company.value, ...response.data.company }

    showEditCompanyModal.value = false

    // Show success message
    editCompanyErrors.value = { success: 'Company updated successfully!' }

    // Clear success message after 3 seconds
    setTimeout(() => {
      if (editCompanyErrors.value.success) {
        delete editCompanyErrors.value.success
      }
    }, 3000)

    console.log('Company updated successfully!')

  } catch (error) {
    console.error('Company update error:', error)
    console.error('Error response:', error.response?.data)
    console.error('Error status:', error.response?.status)

    if (error.response?.status === 422 && error.response?.data?.errors) {
      // Validation errors
      editCompanyErrors.value = error.response.data.errors
      console.log('Validation errors:', error.response.data.errors)
    } else if (error.response?.status === 401) {
      // Authentication error
      showLoginModal.value = true
      editCompanyErrors.value = { general: 'Please log in to update companies.' }
    } else if (error.response?.data?.message) {
      // Server error message
      editCompanyErrors.value = { general: error.response.data.message }
    } else if (error.response?.data?.error) {
      // Alternative error format
      editCompanyErrors.value = { general: error.response.data.error }
    } else if (error.message) {
      // Network or other errors
      editCompanyErrors.value = { general: `Network error: ${error.message}` }
    } else {
      // Generic fallback
      editCompanyErrors.value = { general: 'Failed to update company. Please try again.' }
    }
  } finally {
    updatingCompany.value = false
  }
}

// Edit form handlers
const updateEditCompanyForm = (newForm) => {
  editCompanyForm.value = { ...editCompanyForm.value, ...newForm }
}

const handleMarketCapInput = (event) => {
  const input = event.target.value
  editCompanyForm.value.marketCapInput = input

  // Clear existing timer
  if (editCompanyForm.value.marketCapValidation.timer) {
    clearTimeout(editCompanyForm.value.marketCapValidation.timer)
  }

  if (!input) {
    editCompanyForm.value.market_cap = ''
    editCompanyForm.value.marketCapValidation.state = ''
    return
  }

  // Parse and store numeric value
  const numericValue = parseMarketCapInput(input)
  editCompanyForm.value.market_cap = numericValue

  // Set validation state with debounce
  editCompanyForm.value.marketCapValidation.state = 'validating'
  editCompanyForm.value.marketCapValidation.timer = setTimeout(() => {
    if (numericValue > 0) {
      editCompanyForm.value.marketCapValidation.state = 'valid'
    } else {
      editCompanyForm.value.marketCapValidation.state = 'invalid'
    }
  }, 500)
}

// Modal control methods

const openCreateResearchModal = () => {
  resetResearchForm()
  isEditingResearch.value = false
  editingResearchId.value = null
  showCreateResearchModal.value = true
}

const editResearchItem = (item) => {
  // Populate form with existing data
  researchForm.value = {
    title: item.title || '',
    content: item.content || '',
    company_id: item.company_id || company.value?.id,
    category_id: item.category_id || '',
    visibility: item.visibility || 'private',
    uploadType: 'file',
    files: [],
    urls: [],
    newUrl: '',
    selectedExistingFiles: []
  }

  // Set edit state
  isEditingResearch.value = true
  editingResearchId.value = item.id

  // Open modal
  showCreateResearchModal.value = true
}

const resetResearchForm = () => {
  researchForm.value = {
    title: '',
    content: '',
    company_id: company.value?.id || null,
    category_id: '',
    visibility: 'private',
    uploadType: 'file',
    files: [],
    urls: [],
    newUrl: '',
    selectedExistingFiles: []
  }
  researchErrors.value = {}

  // Reset file management state
  availableFiles.value = []
  loadingExistingFiles.value = false
}

const handleResearchSave = async () => {
  try {
    // Frontend validation
    if (!researchForm.value.title || !researchForm.value.title.trim()) {
      window.showToast('Please enter a title for the research note.', 'warning')
      return
    }

    if (!researchForm.value.content || !researchForm.value.content.trim()) {
      window.showToast('Please enter content for the research note.', 'warning')
      return
    }

    if (!researchForm.value.visibility) {
      window.showToast('Please select a visibility setting.', 'warning')
      return
    }

    if (!company.value?.id) {
      window.showToast('Company information is missing. Please refresh the page and try again.', 'error')
      return
    }

    creatingResearch.value = true
    researchErrors.value = {}

    const formData = new FormData()
    formData.append('title', researchForm.value.title)
    formData.append('content', researchForm.value.content)
    formData.append('company_id', researchForm.value.company_id)
    formData.append('category_id', researchForm.value.category_id || '')
    formData.append('visibility', researchForm.value.visibility)

    // Add uploaded files
    if (researchForm.value.files && researchForm.value.files.length > 0) {
      researchForm.value.files.forEach((file, index) => {
        formData.append(`files[${index}]`, file)
      })
    }

    // Add URLs for download
    if (researchForm.value.urls && researchForm.value.urls.length > 0) {
      console.log('Adding URLs to FormData:', researchForm.value.urls)
      researchForm.value.urls.forEach((url, index) => {
        formData.append(`urls[${index}]`, url)
      })
    }

    // Add selected existing files
    if (researchForm.value.selectedExistingFiles && researchForm.value.selectedExistingFiles.length > 0) {
      researchForm.value.selectedExistingFiles.forEach((file, index) => {
        formData.append(`existing_files[${index}]`, file.id)
      })
    }

    // Debug: Log the form data being sent
    console.log('Form data before sending:')
    for (let [key, value] of formData.entries()) {
      console.log(`${key}:`, value)
    }

    // Create or update research item
    console.log(`${isEditingResearch.value ? 'Updating' : 'Creating'} research item...`)

    let response
    if (isEditingResearch.value && editingResearchId.value) {
      // Update existing item
      formData.append('_method', 'PUT')
      response = await axios.post(`/api/research-items/${editingResearchId.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    } else {
      // Create new item
      response = await axios.post('/api/research-items', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
    }

    console.log('Server response:', response.data)

    // Check if URL downloads failed (only show warning for URL download failures)
    const expectedUrlDownloads = researchForm.value.urls?.length || 0
    const expectedLocalFiles = (researchForm.value.files?.length || 0) + (researchForm.value.selectedExistingFiles?.length || 0)
    const expectedTotalAttachments = expectedUrlDownloads + expectedLocalFiles
    const actualAttachments = response.data.attachments ? response.data.attachments.length : 0

    console.log(`Expected URL downloads: ${expectedUrlDownloads}, Expected local files: ${expectedLocalFiles}, Total expected: ${expectedTotalAttachments}, Actual: ${actualAttachments}`)

    // Show appropriate success/warning message
    const action = isEditingResearch.value ? 'updated' : 'created'

    // Success - refresh company data and close modal
    await fetchCompanyData()
    resetResearchForm()
    isEditingResearch.value = false
    editingResearchId.value = null
    showCreateResearchModal.value = false

    // Only show download warning if there were URLs that failed to download
    if (expectedUrlDownloads > 0 && actualAttachments < expectedTotalAttachments) {
      // Calculate how many URLs failed (assuming local files always succeed)
      const successfulAttachments = actualAttachments
      const successfulUrls = Math.max(0, successfulAttachments - expectedLocalFiles)
      const failedUrls = expectedUrlDownloads - successfulUrls

      if (failedUrls > 0) {
        console.warn(`Warning: ${failedUrls} out of ${expectedUrlDownloads} URL downloads failed`)
        window.showToast(`Research item ${action} successfully!\n\nWarning: ${failedUrls} out of ${expectedUrlDownloads} file(s) could not be downloaded automatically from URLs.\n\nPlease manually download the files from the provided URLs and upload them to this research item.`, 'warning')
      } else {
        console.log(`Research item ${action} successfully with all attachments`)
      }
    } else {
      console.log(`Research item ${action} successfully with all attachments`)
    }

  } catch (error) {
    console.error('Error saving research note:', error)
    console.error('Error response data:', error.response?.data)
    console.error('Error status:', error.response?.status)

    if (error.response?.data?.errors) {
      console.error('Validation errors:', error.response.data.errors)
      researchErrors.value = error.response.data.errors

      // Show validation errors to user
      const errorMessages = Object.entries(error.response.data.errors)
        .map(([field, messages]) => `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`)
        .join('\n')
      window.showToast(`Validation Error:\n\n${errorMessages}`, 'error')
    } else if (error.response?.data?.message) {
      console.error('Server message:', error.response.data.message)
      window.showToast(`Error: ${error.response.data.message}`, 'error')
      researchErrors.value = { general: error.response.data.message }
    } else {
      window.showToast('Failed to save research note. Please try again.', 'error')
      researchErrors.value = { general: 'Failed to save research note. Please try again.' }
    }
  } finally {
    creatingResearch.value = false
  }
}

// File upload handlers for research
const handleResearchFileUpload = (event) => {
  const files = Array.from(event.target.files)

  // Validate file size (10MB max)
  const maxSize = 10 * 1024 * 1024 // 10MB
  const oversizedFiles = files.filter(file => file.size > maxSize)

  if (oversizedFiles.length > 0) {
    researchErrors.value.files = `Files too large: ${oversizedFiles.map(f => f.name).join(', ')}. Max size is 10MB per file.`
    return
  }

  // Add files to form
  researchForm.value.files = [...(researchForm.value.files || []), ...files]

  // Clear any previous errors
  if (researchErrors.value.files) {
    delete researchErrors.value.files
  }
}

const handleResearchAddUrl = async (url) => {
  if (!url || !url.trim()) {
    researchErrors.value.files = 'Please enter a valid URL.'
    return
  }

  // Basic URL validation
  try {
    new URL(url.trim())
  } catch (e) {
    researchErrors.value.files = 'Please enter a valid URL (must start with http:// or https://).'
    return
  }

  try {
    // Initialize urls array if it doesn't exist
    if (!researchForm.value.urls) {
      researchForm.value.urls = []
    }

    // Check for duplicate URLs
    if (researchForm.value.urls.includes(url.trim())) {
      researchErrors.value.files = 'This URL has already been added.'
      return
    }

    // Add URL to the array
    researchForm.value.urls.push(url.trim())
    researchForm.value.newUrl = '' // Clear input

    // Clear any previous errors
    if (researchErrors.value.files) {
      delete researchErrors.value.files
    }

    console.log('URL added successfully:', url.trim())
    console.log('Current URLs:', researchForm.value.urls)
  } catch (error) {
    console.error('Error adding URL:', error)
    researchErrors.value.files = 'Failed to add URL. Please try again.'
  }
}

const handleResearchRemoveUrl = (index) => {
  if (researchForm.value.urls && researchForm.value.urls.length > index) {
    researchForm.value.urls.splice(index, 1)
  }
}

const handleResearchRemoveFile = (index) => {
  if (researchForm.value.files && researchForm.value.files.length > index) {
    researchForm.value.files.splice(index, 1)
  }
}

const handleSearchExistingFiles = async (searchTerm) => {
  if (!searchTerm || searchTerm.length < 2) {
    availableFiles.value = []
    return
  }

  try {
    loadingExistingFiles.value = true
    const response = await axios.get('/api/research-items/files/available', {
      params: { search: searchTerm }
    })
    availableFiles.value = Array.isArray(response.data?.data) ? response.data.data : []
  } catch (error) {
    console.error('Error searching files:', error)
    availableFiles.value = []
  } finally {
    loadingExistingFiles.value = false
  }
}

const handleLoadExistingFiles = async () => {
  try {
    loadingExistingFiles.value = true
    const response = await axios.get('/api/research-items/files/available')
    availableFiles.value = Array.isArray(response.data?.data) ? response.data.data : []
  } catch (error) {
    console.error('Error loading files:', error)
    availableFiles.value = []
    researchErrors.value.files = 'Failed to load existing files. Please try again.'
  } finally {
    loadingExistingFiles.value = false
  }
}

const handleToggleFileSelection = (file) => {
  if (!researchForm.value.selectedExistingFiles) {
    researchForm.value.selectedExistingFiles = []
  }

  const existingIndex = researchForm.value.selectedExistingFiles.findIndex(f => f.id === file.id)

  if (existingIndex >= 0) {
    // Remove if already selected
    researchForm.value.selectedExistingFiles.splice(existingIndex, 1)
  } else {
    // Add if not selected
    researchForm.value.selectedExistingFiles.push(file)
  }
}

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/categories')
    categories.value = response.data || []
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

// Initialize forms when company is loaded
watch(company, (newCompany) => {
  if (newCompany) {
    researchForm.value.company_id = newCompany.id
    documentForm.value.company_id = newCompany.id
  }
})


// Lifecycle
onMounted(() => {
  fetchCompanyData()
  fetchCategories()
})
</script>