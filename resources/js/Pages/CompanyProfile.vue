<template>
  <Head :title="company?.name || 'Company Profile'" />

  <div :class="[
    'min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900',
    { 'blur-sm pointer-events-none': !isAuthenticated }
  ]">
    <!-- Fixed Hamburger Menu -->
    <div class="fixed top-4 left-4 z-40">
      <button
        @click="showFullScreenMenu = true"
        class="p-3 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all duration-200 backdrop-blur-xl border border-white/10 shadow-lg"
        aria-label="Open navigation menu"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
    </div>

    <!-- Header Section -->
    <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">
        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-end mb-6">

          <!-- User Menu (Hidden on mobile) -->
          <div v-if="$page.props.auth.user" class="relative hidden sm:block">
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
          <div v-else class="hidden sm:flex items-center gap-3">
            <button
              @click="showLoginModal = true"
              class="px-4 py-2 bg-blue-500/20 text-blue-200 rounded-lg hover:bg-blue-500/30 transition-colors"
            >
              Login
            </button>
          </div>
        </div>

        <!-- Company Header -->
        <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
          <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-xl sm:text-2xl mx-auto sm:mx-0">
            {{ company?.name?.charAt(0)?.toUpperCase() || 'C' }}
          </div>
          <div class="text-center sm:text-left">
            <div class="flex items-center justify-center sm:justify-start space-x-2 mb-2">
              <h1 class="text-2xl sm:text-4xl font-bold text-white">{{ company?.name || 'Loading...' }}</h1>
              <button
                v-if="$page.props.auth.user"
                @click="openEditCompanyModal"
                class="group p-2 rounded-lg bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white transition-all duration-200 border border-white/10"
                title="Edit Company"
              >
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
            </div>
            <p class="text-lg sm:text-xl text-gray-300">{{ company?.ticker || 'N/A' }} • {{ company?.sector || 'Unknown Sector' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
    </div>

    <!-- Full-Screen Navigation Menu -->
    <Teleport to="body">
      <Transition
        enter-active-class="transition-all duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-all duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="showFullScreenMenu"
          class="fixed inset-0 z-50 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900"
          @click="showFullScreenMenu = false"
        >
          <div class="flex flex-col h-full">
            <!-- Header with Close Button -->
            <div class="flex items-center justify-between p-6">
              <h2 class="text-2xl font-bold text-white">Navigation</h2>
              <button
                @click="showFullScreenMenu = false"
                class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all duration-200"
                aria-label="Close navigation menu"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col justify-center px-6 space-y-6">
              <!-- Back to Dashboard -->
              <Link
                :href="route('dashboard')"
                class="flex items-center justify-center py-6 text-2xl font-semibold text-white bg-white/10 hover:bg-white/20 rounded-2xl transition-all duration-200 backdrop-blur-xl border border-white/10"
                @click="showFullScreenMenu = false"
              >
                <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Dashboard
              </Link>

              <!-- Tab Navigation Items -->
              <div class="space-y-4">
                <button
                  v-for="tab in tabs"
                  :key="tab.id"
                  @click="handleFullScreenTabClick(tab.id)"
                  :class="[
                    'w-full flex items-center justify-between py-6 px-6 text-xl font-semibold rounded-2xl transition-all duration-200 backdrop-blur-xl border border-white/10',
                    activeTab === tab.id
                      ? 'bg-blue-500/20 text-blue-300 border-blue-500/30'
                      : 'text-white bg-white/10 hover:bg-white/20'
                  ]"
                >
                  <span>{{ tab.name }}</span>
                  <span v-if="tab.count" class="bg-white/10 text-gray-300 py-2 px-3 rounded-full text-sm">
                    {{ tab.count }}
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>

    <!-- Main Content -->
    <div v-if="company" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-8">

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
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">Research Notes</h2>
              <p class="text-gray-300" v-if="company">for {{ company.name }} ({{ company.ticker }})</p>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="openCreateResearchModal"
              class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-2 sm:py-3 px-4 sm:px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center justify-center sm:justify-start space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 w-full sm:w-auto"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span class="text-sm sm:text-base">Add Research Note</span>
            </button>
          </div>

          <!-- Research Items Data Table -->
          <div v-if="company?.researchItems && company.researchItems.length > 0">
            <!-- Filters and Column Controls -->
            <div class="mb-4 backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-3 sm:p-4">
              <div class="flex flex-col gap-3 sm:gap-4">
                <!-- Filters Row -->
                <div class="flex flex-col gap-3">
                  <!-- Search Filter -->
                  <div class="w-full">
                    <input
                      v-model="researchFilters.search"
                      type="text"
                      placeholder="Search research items..."
                      class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors text-sm"
                    />
                  </div>

                  <!-- Mobile filters row -->
                  <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Category Filter -->
                    <div class="w-full sm:w-40">
                      <select
                        v-model="researchFilters.category"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors text-sm"
                      >
                        <option value="">All Categories</option>
                        <option v-for="category in availableCategories" :key="category.id" :value="category.id.toString()">
                          {{ category.name }}
                        </option>
                      </select>
                    </div>

                    <!-- Visibility Filter -->
                    <div class="w-full sm:w-32">
                      <select
                        v-model="researchFilters.visibility"
                        class="w-full px-3 py-2 rounded-lg bg-white/5 border border-white/20 text-white focus:border-blue-400/50 focus:ring-1 focus:ring-blue-400/20 transition-colors text-sm"
                      >
                        <option value="">All Visibility</option>
                        <option value="public">Public</option>
                        <option value="team">Team</option>
                        <option value="private">Private</option>
                      </select>
                    </div>
                  </div>
                </div>

                <!-- Controls Row -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                  <!-- Reset Filters -->
                  <button
                    @click="resetFilters"
                    class="px-3 py-2 text-xs text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/20 rounded-lg transition-colors w-full sm:w-auto"
                  >
                    Reset Filters
                  </button>

                  <!-- Column Controls - Hidden on mobile -->
                  <div class="relative hidden sm:block">
                    <button
                      @click="showColumnControls = !showColumnControls"
                      class="px-3 py-2 text-xs text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/20 rounded-lg transition-colors flex items-center space-x-1"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                      </svg>
                      <span>Columns</span>
                    </button>

                    <!-- Column Dropdown -->
                    <div v-if="showColumnControls" class="absolute right-0 top-full mt-2 w-48 backdrop-blur-3xl bg-gray-900/95 border border-white/20 rounded-lg shadow-xl z-50">
                      <div class="p-3">
                        <div class="flex items-center justify-between mb-3">
                          <span class="text-sm font-medium text-white">Show Columns</span>
                          <button
                            @click="resetColumns"
                            class="text-xs text-blue-400 hover:text-blue-300"
                          >
                            Reset
                          </button>
                        </div>
                        <div class="space-y-2">
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.title"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Title</span>
                          </label>
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.category"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Category</span>
                          </label>
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.created"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Created</span>
                          </label>
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.sourceDate"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Source Date</span>
                          </label>
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.files"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Files</span>
                          </label>
                          <label class="flex items-center space-x-2 text-sm text-gray-300">
                            <input
                              type="checkbox"
                              v-model="visibleColumns.actions"
                              class="w-4 h-4 text-blue-500 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                            />
                            <span>Actions</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

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

            <!-- Desktop Table -->
            <div class="hidden sm:block backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">
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
                  <div v-if="visibleColumns.title" class="col-span-5">
                    <button @click="sortBy('title')" class="flex items-center space-x-1 hover:text-white transition-colors">
                      <span>Research Title</span>
                      <svg v-if="sortConfig.field === 'title'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sortConfig.direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-if="visibleColumns.category" class="col-span-2">
                    <button @click="sortBy('category')" class="flex items-center space-x-1 hover:text-white transition-colors">
                      <span>Category</span>
                      <svg v-if="sortConfig.field === 'category'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sortConfig.direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-if="visibleColumns.created" class="col-span-1">
                    <button @click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-white transition-colors">
                      <span>Created</span>
                      <svg v-if="sortConfig.field === 'created_at'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sortConfig.direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-if="visibleColumns.sourceDate" class="col-span-1">
                    <button @click="sortBy('source_date')" class="flex items-center space-x-1 hover:text-white transition-colors">
                      <span>Source Date</span>
                      <svg v-if="sortConfig.field === 'source_date'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sortConfig.direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-if="visibleColumns.files" class="col-span-1 text-center">
                    <button @click="sortBy('files')" class="flex items-center space-x-1 hover:text-white transition-colors">
                      <span>Files</span>
                      <svg v-if="sortConfig.field === 'files'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="sortConfig.direction === 'asc'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7l4-4m0 0l4 4m-4-4v18"></path>
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 17l-4 4m0 0l-4-4m4 4V3"></path>
                      </svg>
                    </button>
                  </div>
                  <div v-if="visibleColumns.actions" class="col-span-1 text-center">Actions</div>
                </div>
              </div>

              <!-- Table Body -->
              <div class="divide-y divide-white/5">
                <div
                  v-for="(item, index) in filteredResearchItems"
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
                    <div v-if="visibleColumns.title" class="col-span-5">
                      <h3 class="text-base font-medium text-white/90 line-clamp-1" :title="item.title">{{ item.title }}</h3>
                    </div>

                    <!-- Category -->
                    <div v-if="visibleColumns.category" class="col-span-2">
                      <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30">
                        {{ item.category?.name || 'Uncategorized' }}
                      </span>
                    </div>

                    <!-- Created Date -->
                    <div v-if="visibleColumns.created" class="col-span-1">
                      <div class="text-white/70">
                        <div class="font-medium text-xs">{{ formatDate(item.created_at) }}</div>
                      </div>
                    </div>

                    <!-- Source Date -->
                    <div v-if="visibleColumns.sourceDate" class="col-span-1">
                      <div class="text-white/70">
                        <div v-if="item.source_date" class="font-medium text-xs">{{ formatDate(item.source_date) }}</div>
                        <div v-else class="text-xs text-white/40 italic">Not set</div>
                      </div>
                    </div>

                    <!-- Files Count -->
                    <div v-if="visibleColumns.files" class="col-span-1 text-center">
                      <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-white/70 font-medium">{{ item.attachments?.length || 0 }}</span>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div v-if="visibleColumns.actions" class="col-span-1">
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

            <!-- Mobile Card Layout -->
            <div class="sm:hidden space-y-3">
              <div
                v-for="(item, index) in filteredResearchItems"
                :key="item.id"
                :class="[
                  'backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-4 transition-all duration-300',
                  selectedResearchItems.has(item.id) ? 'bg-blue-500/10 border-blue-500/50' : ''
                ]"
                @click="viewResearchItem(item)"
              >
                <!-- Card Header -->
                <div class="flex items-start justify-between mb-3">
                  <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-medium text-white/90 line-clamp-2 mb-1" :title="item.title">{{ item.title }}</h3>
                    <div class="flex items-center space-x-2">
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30">
                        {{ item.category?.name || 'Uncategorized' }}
                      </span>
                      <div class="flex items-center text-xs text-white/50">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ item.attachments?.length || 0 }}
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center space-x-2 ml-2">
                    <input
                      type="checkbox"
                      :checked="selectedResearchItems.has(item.id)"
                      @change="toggleItemSelection(item.id)"
                      @click.stop
                      class="w-4 h-4 text-blue-600 bg-white/10 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                    />
                  </div>
                </div>

                <!-- Card Footer -->
                <div class="flex items-center justify-between text-xs text-white/60">
                  <div class="flex items-center space-x-4">
                    <span>{{ formatDate(item.created_at) }}</span>
                    <span v-if="item.source_date">Source: {{ formatDate(item.source_date) }}</span>
                  </div>
                  <div class="flex items-center space-x-1">
                    <button
                      @click.stop="editResearchItem(item)"
                      class="p-1.5 rounded-lg bg-blue-500/20 hover:bg-blue-500/30 text-blue-200 hover:text-white transition-colors"
                      title="Edit Research Note"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button
                      @click.stop="deleteResearchItem(item)"
                      class="p-1.5 rounded-lg bg-red-500/20 hover:bg-red-500/30 text-red-200 hover:text-white transition-colors"
                      title="Delete Research Note"
                    >
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1v3M4 7h16"></path>
                      </svg>
                    </button>
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


        <!-- Research Assets Tab -->
        <div v-show="activeTab === 'documents'" class="space-y-6">
          <!-- Header with Upload Button -->
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">Research Assets</h2>
              <p class="text-gray-300" v-if="company">for {{ company.name }} ({{ company.ticker }})</p>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="showUploadDocumentModal = true"
              class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-2 sm:py-3 px-4 sm:px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center justify-center sm:justify-start space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 w-full sm:w-auto"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span class="text-sm sm:text-base">Add Research Asset</span>
            </button>
          </div>

          <!-- Research Assets Data Table -->
          <div v-if="company?.documents && company.documents.length > 0">
            <!-- Bulk Actions Toolbar -->
            <div v-if="selectedDocuments.size > 0" class="mb-4 backdrop-blur-3xl bg-gradient-to-r from-red-500/10 to-red-600/20 rounded-2xl px-6 py-4 border border-red-500/20">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <span class="text-white font-medium">{{ selectedDocuments.size }} asset{{ selectedDocuments.size !== 1 ? 's' : '' }} selected</span>
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
                  <div class="col-span-2">Uploaded by</div>
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

                    <!-- Uploaded by -->
                    <div class="col-span-2">
                      <div class="text-white font-medium">{{ doc.user?.name || 'Unknown' }}</div>
                      <div class="text-gray-400 text-xs">{{ formatDate(doc.created_at).split(' ')[0] }}</div>
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
              <h3 class="text-xl font-medium text-white mb-2">No research assets available</h3>
              <p class="text-gray-400 mb-6">Create research assets or add research notes with files to see them here</p>
              <button
                v-if="$page.props.auth.user"
                @click="showUploadDocumentModal = true"
                class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center justify-center space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <svg class="w-5 h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Create First Research Asset</span>
              </button>
            </div>
          </div>
        </div>

        <!-- Documents Tab -->
        <div v-show="activeTab === 'insights'" class="space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
              <h2 class="text-2xl font-bold text-white mb-2">Research Assets</h2>
              <p class="text-gray-300" v-if="company">for {{ company.name }} ({{ company.ticker }})</p>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="showUploadDocumentModal = true"
              class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-2 sm:py-3 px-4 sm:px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center justify-center sm:justify-start space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 w-full sm:w-auto"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <svg class="w-4 h-4 sm:w-5 sm:h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span class="text-sm sm:text-base">Add Research Asset</span>
            </button>
          </div>

          <div class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
            <p class="text-gray-400">Company research assets will be displayed here.</p>
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

    <!-- Research Asset Creation Modal -->
    <DocumentUploadModal
      v-if="company"
      :show="showUploadDocumentModal"
      :selectedCompany="company"
      :form="documentForm"
      :errors="documentErrors"
      :categories="categories"
      :formatFileSize="formatFileSize"
      @close="showUploadDocumentModal = false"
      @document-saved="handleDocumentSaved"
    />

    <!-- Document Viewer Modal -->
    <DocumentViewerModal
      :show="showDocumentViewer"
      :document="selectedDocument"
      :categories="categories"
      :canEdit="$page.props.auth.user?.id === selectedDocument?.user?.id"
      :canDelete="$page.props.auth.user?.id === selectedDocument?.user?.id"
      @close="showDocumentViewer = false"
      @update="updateDocumentFromModal"
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

    <!-- Auth Modals -->
    <LoginModal
      :show="showLoginModal"
      :can-reset-password="true"
      @close="showLoginModal = false"
      @switch-to-register="showLoginModal = false; showRegisterModal = true"
    />

    <RegisterModal
      :show="showRegisterModal"
      @close="showRegisterModal = false"
      @switch-to-login="showRegisterModal = false; showLoginModal = true"
    />

    <!-- Toast Notifications -->
    <ToastNotification />

  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import LoginModal from '@/Components/Modals/LoginModal.vue'
import RegisterModal from '@/Components/Modals/RegisterModal.vue'
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
const showRegisterModal = ref(false)
const activeTab = ref(props.tab)
const showMobileMenu = ref(false)
const showFullScreenMenu = ref(false)

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

// Column visibility and filtering
const showColumnControls = ref(false)
const visibleColumns = ref({
  title: true,
  category: true,
  created: false,
  sourceDate: true,
  files: true,
  actions: true
})

// Research filtering
const researchFilters = ref({
  search: '',
  category: '',
  visibility: '',
  dateRange: ''
})

// Research sorting
const sortConfig = ref({
  field: 'created_at',
  direction: 'desc'
})

// Forms
const researchForm = ref({
  title: '',
  content: '',
  company_id: null,
  category_id: '',
  source_date: '',
  visibility: 'private',
  uploadType: 'file',
  files: [],
  urls: [],
  newUrl: '',
  selectedExistingFiles: []
})

const documentForm = ref({
  category_id: null,
  visibility: 'private'
})

const editCompanyForm = ref({
  name: '',
  ticker: '',
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
      name: 'Research Assets',
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

// Authentication check
const page = usePage()
const isAuthenticated = computed(() => {
  return !!page.props.auth.user
})

// Filtered research items
const filteredResearchItems = computed(() => {
  if (!company.value?.researchItems) return []

  let filtered = company.value.researchItems.filter(item => {
    // Search filter
    if (researchFilters.value.search) {
      const searchLower = researchFilters.value.search.toLowerCase()
      const matchesSearch = item.title?.toLowerCase().includes(searchLower) ||
                           item.content?.toLowerCase().includes(searchLower)
      if (!matchesSearch) return false
    }

    // Category filter
    if (researchFilters.value.category) {
      const itemCategoryId = item.category?.id?.toString()
      if (itemCategoryId !== researchFilters.value.category) return false
    }

    // Visibility filter
    if (researchFilters.value.visibility) {
      if (item.visibility !== researchFilters.value.visibility) return false
    }

    return true
  })

  // Apply sorting
  if (sortConfig.value.field) {
    filtered.sort((a, b) => {
      let aValue, bValue

      switch (sortConfig.value.field) {
        case 'title':
          aValue = a.title?.toLowerCase() || ''
          bValue = b.title?.toLowerCase() || ''
          break
        case 'category':
          aValue = a.category?.name?.toLowerCase() || ''
          bValue = b.category?.name?.toLowerCase() || ''
          break
        case 'created_at':
          aValue = new Date(a.created_at)
          bValue = new Date(b.created_at)
          break
        case 'source_date':
          aValue = a.source_date ? new Date(a.source_date) : new Date(0)
          bValue = b.source_date ? new Date(b.source_date) : new Date(0)
          break
        case 'files':
          aValue = a.attachments?.length || 0
          bValue = b.attachments?.length || 0
          break
        default:
          return 0
      }

      if (aValue < bValue) {
        return sortConfig.value.direction === 'asc' ? -1 : 1
      }
      if (aValue > bValue) {
        return sortConfig.value.direction === 'asc' ? 1 : -1
      }
      return 0
    })
  }

  return filtered
})

// Available categories for filter dropdown
const availableCategories = computed(() => {
  if (!company.value?.researchItems) return []

  const categories = new Map()
  company.value.researchItems.forEach(item => {
    if (item.category) {
      categories.set(item.category.id, item.category)
    }
  })

  return Array.from(categories.values()).sort((a, b) => a.name.localeCompare(b.name))
})

// Methods
const handleTabClick = (tabId) => {
  activeTab.value = tabId
  showMobileMenu.value = false // Close mobile menu after selection
}

const handleFullScreenTabClick = (tabId) => {
  activeTab.value = tabId
  showFullScreenMenu.value = false // Close full-screen menu after selection
}

const fetchCompanyData = async () => {
  try {
    loading.value = true
    error.value = null

    // First try to find company by ticker symbol with higher limit to ensure we get it
    const companiesResponse = await axios.get('/api/companies', {
      params: {
        search: props.ticker,
        limit: 1000  // High limit to ensure we get all companies
      }
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

// Filter and column methods
const resetFilters = () => {
  researchFilters.value = {
    search: '',
    category: '',
    visibility: '',
    dateRange: ''
  }
}

const toggleColumn = (columnName) => {
  visibleColumns.value[columnName] = !visibleColumns.value[columnName]
}

const resetColumns = () => {
  visibleColumns.value = {
    title: true,
    category: true,
    created: false,
    sourceDate: true,
    files: true,
    actions: true
  }
}

// Click outside handler for column dropdown
const handleClickOutside = (event) => {
  if (showColumnControls.value && !event.target.closest('.relative')) {
    showColumnControls.value = false
  }
}

// Sort function
const sortBy = (field) => {
  if (sortConfig.value.field === field) {
    // Toggle direction if same field
    sortConfig.value.direction = sortConfig.value.direction === 'asc' ? 'desc' : 'asc'
  } else {
    // Set new field with default direction
    sortConfig.value.field = field
    sortConfig.value.direction = 'asc'
  }
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
  // For now, just open the modal to upload new documents
  // Document editing can be handled separately
  documentForm.value = {
    category_id: doc.category_id || null,
    visibility: doc.visibility || 'private'
  }
  showUploadDocumentModal.value = true
}

const updateDocumentFromModal = async (updateData) => {
  try {
    let response
    let endpoint

    // Check if this is a research item (has research_item_type) or a document
    if (selectedDocument.value?.research_item_type || selectedDocument.value?.content !== undefined) {
      // This is a research item
      endpoint = `/api/research-items/${updateData.id}`
    } else {
      // This is a document
      endpoint = `/api/documents/${updateData.id}`
    }

    response = await axios.put(endpoint, updateData.data)

    // Update the document/research item in the local state
    if (selectedDocument.value && selectedDocument.value.id === updateData.id) {
      selectedDocument.value = { ...selectedDocument.value, ...response.data }
    }

    // Update in documents list if present
    const docIndex = documents.value.findIndex(doc => doc.id === updateData.id)
    if (docIndex !== -1) {
      documents.value[docIndex] = { ...documents.value[docIndex], ...response.data }
    }

    // Update in research items list if present
    const researchIndex = researchItems.value.findIndex(item => item.id === updateData.id)
    if (researchIndex !== -1) {
      researchItems.value[researchIndex] = { ...researchItems.value[researchIndex], ...response.data }
    }

    // Show success toast
    window.showToast(`${updateData.field.charAt(0).toUpperCase() + updateData.field.slice(1)} updated successfully`, 'success')
  } catch (error) {
    console.error('Error updating item:', error)
    console.error('Update data:', updateData)
    console.error('Selected document:', selectedDocument.value)

    if (error.response?.status === 404) {
      window.showToast(`Item not found. It may have been deleted.`, 'error')
    } else {
      window.showToast(`Failed to update ${updateData.field}`, 'error')
    }
  }
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

// Simplified handler for background upload completion
const handleDocumentSaved = (document) => {
  // Add the new document to the company documents
  if (!company.value.documents) {
    company.value.documents = []
  }
  company.value.documents.unshift(document)

  // Reset form state
  documentForm.value = {
    category_id: null,
    visibility: 'private'
  }

  // Close modal
  showUploadDocumentModal.value = false

  // Show success notification
  console.log('Document saved successfully:', document.title)
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
    ticker: company.value?.ticker || '',
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
    source_date: item.source_date || '',
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
    source_date: '',
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

    // Always send source_date, even if empty (to allow clearing)
    formData.append('source_date', researchForm.value.source_date || '')

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


// Watch for authentication status and show login modal if not authenticated
watch(isAuthenticated, (newValue) => {
  if (!newValue) {
    showLoginModal.value = true
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  fetchCompanyData()
  fetchCategories()
  // Add click outside listener for column dropdown
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  // Remove click outside listener
  document.removeEventListener('click', handleClickOutside)
})
</script>