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
              <p class="text-xl text-gray-300">{{ company?.ticker || 'N/A' }} • {{ company?.sector || 'Unknown Sector' }}</p>
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
                  <span class="text-white font-bold text-lg">{{ company?.ticker || 'N/A' }}</span>
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
              <p class="text-gray-300" v-if="company">for {{ company.name }} ({{ company.ticker }})</p>
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
            <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">
              <!-- Table Header -->
              <div class="bg-gradient-to-r from-white/5 to-white/2 px-6 py-4 border-b border-white/10">
                <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white/80">
                  <div class="col-span-1 text-center">#</div>
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
                  class="group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300 cursor-pointer"
                  @click="viewResearchItem(item)"
                >
                  <div class="grid grid-cols-12 gap-4 items-center text-sm">

                    <!-- Index -->
                    <div class="col-span-1 text-center">
                      <span class="text-white/60 font-medium">{{ index + 1 }}</span>
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
              class="px-4 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-200 rounded-lg transition-colors border border-green-500/30 hover:border-green-500/50"
            >
              <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              Upload Document
            </button>
          </div>

          <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
            <div v-if="company?.documents?.length > 0" class="space-y-4">
              <div
                v-for="doc in company.documents"
                :key="doc.id"
                class="bg-gradient-to-r from-white/5 to-white/2 rounded-lg p-4 border border-white/10 hover:bg-white/10 transition-colors cursor-pointer"
                @click="viewDocument(doc)"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-lg font-medium text-white mb-2">{{ doc.title }}</h3>
                    <p class="text-gray-300 text-sm mb-3">{{ doc.description }}</p>
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                      <span>{{ formatDate(doc.created_at) }}</span>
                      <span v-if="doc.file_type" class="px-2 py-1 bg-gray-500/20 text-gray-200 rounded text-xs">
                        {{ doc.file_type }}
                      </span>
                      <span v-if="doc.file_size" class="text-xs">
                        {{ formatFileSize(doc.file_size) }}
                      </span>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 ml-4">
                    <button
                      @click.stop="downloadDocument(doc)"
                      class="p-2 text-blue-300 hover:text-blue-200 hover:bg-blue-500/20 rounded transition-colors"
                      title="Download"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </button>
                    <div v-if="$page.props.auth.user && doc.user_id === $page.props.auth.user.id" class="flex space-x-1">
                      <button
                        @click.stop="editDocument(doc)"
                        class="p-2 text-yellow-300 hover:text-yellow-200 hover:bg-yellow-500/20 rounded transition-colors"
                        title="Edit"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        @click.stop="deleteDocument(doc)"
                        class="p-2 text-red-300 hover:text-red-200 hover:bg-red-500/20 rounded transition-colors"
                        title="Delete"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-12 text-gray-400">
              <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-lg mb-2">No documents yet</p>
              <p class="text-sm mb-4">Upload documents related to this company</p>
              <button
                v-if="$page.props.auth.user"
                @click="showUploadDocumentModal = true"
                class="px-6 py-2 bg-green-500/20 hover:bg-green-500/30 text-green-200 rounded-lg transition-colors border border-green-500/30"
              >
                Upload First Document
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
      :formatFileSize="formatFileSize"
      @close="showUploadDocumentModal = false"
      @success="handleDocumentSuccess"
    />

    <!-- Company Edit Modal -->
    <CreateCompanyModal
      v-if="company"
      :show="showEditCompanyModal"
      :form="editCompanyForm"
      :errors="editCompanyErrors"
      :creating="updatingCompany"
      :formatMarketCap="formatMarketCap"
      @close="showEditCompanyModal = false"
      @save="handleEditCompanySave"
    />

    <!-- Research Item Viewer Modal -->
    <div v-if="showResearchViewer && selectedResearchItem" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
      <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden border border-white/20">
        <div class="p-6 border-b border-white/20">
          <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-white">{{ selectedResearchItem.title }}</h2>
            <button @click="closeResearchViewer" class="text-white/60 hover:text-white">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[70vh]">
          <div class="prose prose-invert max-w-none">
            <div v-html="formatResearchContent(selectedResearchItem.content)"></div>
          </div>
          <div v-if="selectedResearchItem.attachments?.length" class="mt-6 pt-6 border-t border-white/20">
            <h3 class="text-lg font-semibold text-white mb-4">Attachments</h3>
            <div class="space-y-2">
              <div v-for="attachment in selectedResearchItem.attachments" :key="attachment.id"
                   class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                <span class="text-white">{{ attachment.name }}</span>
                <button @click="downloadAttachment(attachment)"
                        class="text-blue-300 hover:text-blue-200">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 7h10a2 2 0 012 2v9a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

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
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'

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
const showEditCompanyModal = ref(false)
const showResearchViewer = ref(false)
const selectedResearchItem = ref(null)

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
  files: []
})

const editCompanyForm = ref({
  name: '',
  ticker: '',
  sector: '',
  industry: '',
  market_cap: '',
  description: ''
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

// File management data
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
      c => c.ticker?.toLowerCase() === props.ticker.toLowerCase()
    )

    if (!foundCompany) {
      error.value = 'Company not found'
      return
    }

    // Get detailed company data
    const response = await axios.get(`/api/companies/${foundCompany.id}`)
    company.value = response.data

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

// Research Item Methods
const viewResearchItem = (item) => {
  selectedResearchItem.value = item
  showResearchViewer.value = true
}

const closeResearchViewer = () => {
  showResearchViewer.value = false
  selectedResearchItem.value = null
}

const formatResearchContent = (content) => {
  if (!content) return ''
  // Simple markdown-like formatting
  return content
    .replace(/\n/g, '<br>')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
}


const deleteResearchItem = async (item) => {
  if (!confirm('Are you sure you want to delete this research item?')) return

  try {
    await axios.delete(`/api/research-items/${item.id}`)
    // Remove from company data
    if (company.value.researchItems) {
      const index = company.value.researchItems.findIndex(r => r.id === item.id)
      if (index !== -1) {
        company.value.researchItems.splice(index, 1)
      }
    }
  } catch (error) {
    console.error('Error deleting research item:', error)
    alert('Failed to delete research item')
  }
}


// Document Methods
const viewDocument = (doc) => {
  // Open document in new tab or download
  if (doc.file_url) {
    window.open(doc.file_url, '_blank')
  }
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

const deleteDocument = async (doc) => {
  if (!confirm('Are you sure you want to delete this document?')) return

  try {
    await axios.delete(`/api/documents/${doc.id}`)
    // Remove from company data
    if (company.value.documents) {
      const index = company.value.documents.findIndex(d => d.id === doc.id)
      if (index !== -1) {
        company.value.documents.splice(index, 1)
      }
    }
  } catch (error) {
    console.error('Error deleting document:', error)
    alert('Failed to delete document')
  }
}

const downloadAttachment = (attachment) => {
  if (attachment.url) {
    const a = document.createElement('a')
    a.href = attachment.url
    a.download = attachment.name
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
  }
}

const handleDocumentSuccess = () => {
  showUploadDocumentModal.value = false
  // Refresh company data
  fetchCompanyData()
}

// Company edit methods
const openEditCompanyModal = () => {
  // Populate the form with current company data
  editCompanyForm.value = {
    name: company.value?.name || '',
    ticker: company.value?.ticker || '',
    sector: company.value?.sector || '',
    industry: company.value?.industry || '',
    market_cap: company.value?.market_cap || '',
    description: company.value?.description || ''
  }

  editCompanyErrors.value = {}
  showEditCompanyModal.value = true
}

const handleEditCompanySave = async () => {
  if (!company.value) return

  updatingCompany.value = true
  editCompanyErrors.value = {}

  try {
    const response = await axios.put(`/api/companies/${company.value.id}`, editCompanyForm.value)

    // Update the local company data
    company.value = { ...company.value, ...response.data.company }

    showEditCompanyModal.value = false

    // Optional: Show success message (you could emit a toast here)
    console.log('Company updated successfully!')

  } catch (error) {
    if (error.response && error.response.data && error.response.data.errors) {
      editCompanyErrors.value = error.response.data.errors
    } else {
      editCompanyErrors.value = { general: 'Failed to update company. Please try again.' }
    }
  } finally {
    updatingCompany.value = false
  }
}

// Modal control methods

const openCreateResearchModal = () => {
  resetResearchForm()
  showCreateResearchModal.value = true
}

const editResearchItem = (item) => {
  // TODO: Implement edit functionality via modal
  // For now, just open the create modal
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

    // Create new research item
    console.log('Sending FormData to server...')
    const response = await axios.post('/api/research-items', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    console.log('Server response:', response.data)

    // Success - refresh company data and close modal
    await fetchCompanyData()
    resetResearchForm()
    showCreateResearchModal.value = false

    console.log('Research item created successfully')

  } catch (error) {
    console.error('Error saving research note:', error)
    if (error.response?.data?.errors) {
      researchErrors.value = error.response.data.errors
    } else {
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
    availableFiles.value = Array.isArray(response.data) ? response.data : []
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
    availableFiles.value = Array.isArray(response.data) ? response.data : []
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