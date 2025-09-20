<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950 transition-colors relative">
    
    <!-- Canvas Shooting Stars Background -->
    <canvas
      ref="starsCanvas"
      class="fixed inset-0 w-full h-full pointer-events-none"
      style="z-index: 1;"
    ></canvas>
    <!-- Header Section -->
    <div class="bg-gray-100 dark:bg-gray-950 shadow-sm">
      <div class="w-[80%] mx-auto px-6 py-8">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-4">
            <!-- Rainmaker Logo Image -->
            <img 
              src="/images/rainmaker-logo.png" 
              alt="Rainmaker Logo" 
              class="drop-shadow-sm"
              style="height: 120px; width: auto; opacity: 1 !important; filter: brightness(1.2) contrast(1.2) saturate(1.2);"
            />
          </div>
          <div class="flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
              :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
              </svg>
            </button>
            <span v-if="$page.props.auth.user" class="text-sm text-gray-600 dark:text-gray-300">
              Welcome, {{ $page.props.auth.user.name }}
            </span>
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
                  <DropdownLink 
                    v-if="canAccessAdmin" 
                    :href="route('admin.dashboard')"
                    class="border-t border-gray-100 dark:border-gray-600"
                  >
                    🔧 Admin Panel
                  </DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                </template>
              </Dropdown>
            </div>
            <div v-else class="flex items-center gap-3">
              <Link :href="route('login')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Login
              </Link>
              <Link :href="route('register')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Register
              </Link>
            </div>
          </div>
        </div>
        
        <!-- Title and Action Button -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-4xl font-semibold text-gray-900 dark:text-white mb-2">Investment Research</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">Search and discover companies in your portfolio</p>
          </div>
          <div v-if="$page.props.auth.user" class="flex space-x-3">
            <button
              @click="showQuickBlogModal = true"
              class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-sm transition-all duration-200 transform hover:scale-105 flex items-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              Quick Insight
            </button>
            <button
              @click="openCreateModal"
              class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-sm transition-all duration-200 transform hover:scale-105"
            >
              + Add Company
            </button>
          </div>
          <Link 
            v-else
            :href="route('login')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-sm transition-all duration-200 transform hover:scale-105"
          >
            Login to Add Companies
          </Link>
        </div>
        
        <!-- Search Input -->
        <div class="w-full max-w-2xl">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search companies, tickers, or sectors..."
            class="w-full text-xl py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 shadow-sm focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-200"
          />
        </div>
      </div>
    </div>

    <!-- Glass Container for Lower Content -->
    <div class="w-[80%] mx-auto px-6 py-8">
      <div class="backdrop-blur-xl bg-white/10 dark:bg-white/5 rounded-2xl shadow-2xl p-6">

        <!-- Tab Navigation -->
        <div class="mb-8">
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white flex items-center">
              <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              Investment Research Platform
            </h2>
          </div>

          <!-- Tabbed Navigation -->
          <div class="flex items-center space-x-2 mb-6 p-1 bg-white/10 backdrop-blur-sm rounded-xl border border-white/20">
            <button
              @click="activeTab = 'overview'"
              :class="[
                'flex items-center space-x-2 px-4 py-3 rounded-lg font-medium transition-all duration-300 relative overflow-hidden',
                activeTab === 'overview'
                  ? 'bg-blue-500/40 text-blue-200 shadow-lg backdrop-blur-md border border-blue-400/30'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
              <span>Overview</span>
              <div v-if="activeTab === 'overview'" class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-blue-600/20 rounded-lg"></div>
            </button>

            <button
              @click="activeTab = 'portfolio'"
              :class="[
                'flex items-center space-x-2 px-4 py-3 rounded-lg font-medium transition-all duration-300 relative overflow-hidden',
                activeTab === 'portfolio'
                  ? 'bg-green-500/40 text-green-200 shadow-lg backdrop-blur-md border border-green-400/30'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span>Portfolio</span>
              <div v-if="activeTab === 'portfolio'" class="absolute inset-0 bg-gradient-to-r from-green-500/20 to-green-600/20 rounded-lg"></div>
            </button>

            <button
              @click="activeTab = 'insights'"
              :class="[
                'flex items-center space-x-2 px-4 py-3 rounded-lg font-medium transition-all duration-300 relative overflow-hidden',
                activeTab === 'insights'
                  ? 'bg-purple-500/40 text-purple-200 shadow-lg backdrop-blur-md border border-purple-400/30'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
              </svg>
              <span>Insights</span>
              <div v-if="activeTab === 'insights'" class="absolute inset-0 bg-gradient-to-r from-purple-500/20 to-purple-600/20 rounded-lg"></div>
            </button>

            <button
              @click="activeTab = 'analytics'"
              :class="[
                'flex items-center space-x-2 px-4 py-3 rounded-lg font-medium transition-all duration-300 relative overflow-hidden',
                activeTab === 'analytics'
                  ? 'bg-orange-500/40 text-orange-200 shadow-lg backdrop-blur-md border border-orange-400/30'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
              </svg>
              <span>Analytics</span>
              <div v-if="activeTab === 'analytics'" class="absolute inset-0 bg-gradient-to-r from-orange-500/20 to-orange-600/20 rounded-lg"></div>
            </button>

            <button
              @click="activeTab = 'actions'"
              :class="[
                'flex items-center space-x-2 px-4 py-3 rounded-lg font-medium transition-all duration-300 relative overflow-hidden',
                activeTab === 'actions'
                  ? 'bg-yellow-500/40 text-yellow-200 shadow-lg backdrop-blur-md border border-yellow-400/30'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
              ]"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
              </svg>
              <span>Quick Actions</span>
              <div v-if="activeTab === 'actions'" class="absolute inset-0 bg-gradient-to-r from-yellow-500/20 to-yellow-600/20 rounded-lg"></div>
            </button>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">

          <!-- Overview Tab -->
          <div v-if="activeTab === 'overview'" class="space-y-8">
            <!-- Executive Overview Section -->

          <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Companies -->
            <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-300 dark:text-gray-400">Total Companies</p>
                  <p class="text-2xl font-bold text-white">{{ companies.length }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Total Market Cap -->
            <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-300 dark:text-gray-400">Portfolio Value</p>
                  <p class="text-2xl font-bold text-white">{{ formatTotalMarketCap() }}</p>
                </div>
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Insights Published -->
            <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-300 dark:text-gray-400">Published Insights</p>
                  <p class="text-2xl font-bold text-white">{{ $page.props.recentBlogPosts?.length || 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
              </div>
            </div>

            <!-- Active Research -->
            <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm text-gray-300 dark:text-gray-400">Research Items</p>
                  <p class="text-2xl font-bold text-white">{{ getTotalResearchItems() }}</p>
                </div>
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
          </div>

          <!-- Portfolio Tab -->
          <div v-if="activeTab === 'portfolio'" class="space-y-6">
            <!-- Portfolio Header -->
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Company Portfolio Management
              </h3>
              <!-- View Toggle -->
              <div class="flex items-center rounded-lg p-1 bg-white/10">
                <button
                  @click="viewMode = 'cards'"
                  :class="[
                    'flex items-center space-x-2 px-3 py-2 rounded-md transition-all duration-200',
                    viewMode === 'cards'
                      ? 'bg-green-500/30 text-green-300'
                      : 'text-gray-300 hover:text-white hover:bg-white/10'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                  </svg>
                  <span class="text-sm">Cards</span>
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="[
                    'flex items-center space-x-2 px-3 py-2 rounded-md transition-all duration-200',
                    viewMode === 'list'
                      ? 'bg-green-500/30 text-green-300'
                      : 'text-gray-300 hover:text-white hover:bg-white/10'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                  </svg>
                  <span class="text-sm">List</span>
                </button>
              </div>
            </div>

            <!-- Bulk Actions (only in list mode) -->
            <div v-if="viewMode === 'list' && selectedCompanies.length > 0" class="mb-4 flex items-center space-x-3 p-3 bg-red-500/20 rounded-lg border border-red-500/30">
              <span class="text-sm text-red-300">
                {{ selectedCompanies.length }} companies selected
              </span>
              <button
                @click="bulkDeleteCompanies"
                :disabled="bulkDeleting"
                class="bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-medium py-1 px-3 rounded-md transition-all duration-200 flex items-center space-x-2 text-sm"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>{{ bulkDeleting ? 'Deleting...' : 'Delete Selected' }}</span>
              </button>
            </div>

            <!-- Portfolio Content -->
            <div v-if="loading" class="text-center py-12">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-400 mx-auto"></div>
              <p class="text-gray-300 mt-3">Loading companies...</p>
            </div>

            <!-- Cards View -->
            <div v-else-if="filteredCompanies.length > 0 && viewMode === 'cards'" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
              <div
                v-for="company in filteredCompanies"
                :key="company.id"
                class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20 hover:bg-white/30 dark:hover:bg-white/15 transition-all duration-200 group"
              >
                <!-- Company Header -->
                <div class="flex items-center mb-4">
                  <div class="w-10 h-10 bg-blue-500/30 rounded-lg flex items-center justify-center text-blue-300 font-semibold text-sm">
                    {{ company.ticker.charAt(0) }}
                  </div>
                  <div class="ml-3 flex-1">
                    <h4 class="text-lg font-semibold text-white mb-1 group-hover:text-blue-300 transition-colors">{{ company.name }}</h4>
                    <p class="text-sm text-blue-400 font-medium">{{ company.ticker }}</p>
                  </div>

                  <!-- Quick Actions -->
                  <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button
                      @click.stop="viewCompanyDetails(company)"
                      class="w-8 h-8 rounded-lg bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
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
                      class="w-8 h-8 rounded-lg bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                      title="Share Insight"
                    >
                      <svg class="w-4 h-4 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Company Details -->
                <div class="grid grid-cols-2 gap-3 text-sm">
                  <div>
                    <p class="text-gray-400">Sector</p>
                    <p class="text-white font-medium">{{ company.sector || 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-gray-400">Market Cap</p>
                    <p class="text-white font-medium">{{ formatMarketCap(company.marketCap) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- List View -->
            <div v-else-if="filteredCompanies.length > 0 && viewMode === 'list'" class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg border border-white/20">
              <!-- Table Header -->
              <div class="px-4 py-3 border-b border-white/20">
                <div class="flex items-center">
                  <div class="flex items-center mr-4">
                    <input
                      type="checkbox"
                      :checked="isSelectAll"
                      @change="toggleSelectAll"
                      class="w-4 h-4 text-blue-600 bg-white/20 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                    />
                    <label class="ml-2 text-sm font-medium text-gray-300">Select All</label>
                  </div>
                </div>
              </div>

              <!-- Table Body -->
              <div class="divide-y divide-white/10">
                <div
                  v-for="company in filteredCompanies"
                  :key="company.id"
                  class="px-4 py-3 hover:bg-white/20 transition-colors duration-150"
                >
                  <div class="flex items-center">
                    <!-- Checkbox -->
                    <div class="flex items-center mr-3">
                      <input
                        type="checkbox"
                        :checked="selectedCompanies.includes(company.id)"
                        @change="toggleCompanySelection(company.id)"
                        class="w-4 h-4 text-blue-600 bg-white/20 border-white/30 rounded focus:ring-blue-500 focus:ring-2"
                      />
                    </div>

                    <!-- Company Logo -->
                    <div class="w-8 h-8 bg-blue-500/30 rounded-lg flex items-center justify-center text-blue-300 font-semibold text-sm mr-3">
                      {{ company.ticker.charAt(0) }}
                    </div>

                    <!-- Company Info -->
                    <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-3 items-center">
                      <div class="lg:col-span-1">
                        <h4 class="text-sm font-medium text-white">{{ company.name }}</h4>
                        <p class="text-xs text-blue-400 font-medium">{{ company.ticker }}</p>
                      </div>

                      <div class="text-center lg:text-left">
                        <p class="text-xs text-gray-400">Sector</p>
                        <p class="text-sm font-medium text-white">{{ company.sector || 'N/A' }}</p>
                      </div>

                      <div class="text-center lg:text-left">
                        <p class="text-xs text-gray-400">Market Cap</p>
                        <p class="text-sm font-medium text-white">{{ formatMarketCap(company.marketCap) }}</p>
                      </div>

                      <div class="text-center lg:text-right">
                        <p class="text-xs text-gray-400">Research Items</p>
                        <p class="text-sm font-medium text-white">{{ company.researchItemsCount || 0 }}</p>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="ml-4 flex items-center space-x-1">
                      <!-- View Button -->
                      <button
                        @click.stop="viewCompanyDetails(company)"
                        class="w-7 h-7 rounded-lg bg-blue-500/30 hover:bg-blue-500/50 flex items-center justify-center transition-colors"
                        title="View Details"
                      >
                        <svg class="w-3 h-3 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                      </button>

                      <!-- Share Insight Button -->
                      <button
                        v-if="$page.props.auth.user"
                        @click.stop="openQuickBlogWithCompany(company)"
                        class="w-7 h-7 rounded-lg bg-green-500/30 hover:bg-green-500/50 flex items-center justify-center transition-colors"
                        title="Share Insight"
                      >
                        <svg class="w-3 h-3 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                      </button>

                      <!-- Edit Button -->
                      <button
                        v-if="$page.props.auth.user"
                        @click.stop="editCompany(company)"
                        class="w-7 h-7 rounded-lg bg-gray-500/30 hover:bg-gray-500/50 flex items-center justify-center transition-colors"
                        title="Edit Company"
                      >
                        <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>

                      <!-- Delete Button -->
                      <button
                        v-if="$page.props.auth.user"
                        @click.stop="deleteCompany(company)"
                        :disabled="deleting"
                        class="w-7 h-7 rounded-lg bg-red-500/30 hover:bg-red-500/50 disabled:bg-red-300/30 flex items-center justify-center transition-colors"
                        title="Delete Company"
                      >
                        <svg class="w-3 h-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">No companies found</h4>
                <p class="text-sm text-gray-300">Try adjusting your search terms or add a new company</p>
              </div>
            </div>
          </div>

          </div>

          <!-- Insights Tab -->
          <div v-if="activeTab === 'insights'" class="space-y-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Community Insights & Research
              </h3>
              <button
                v-if="$page.props.auth.user"
                @click="showQuickBlogModal = true"
                class="px-4 py-2 bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium rounded-lg transition-colors border border-purple-400/30"
              >
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Insight
              </button>
            </div>

            <!-- Community Insights Grid -->
            <div v-if="$page.props.recentBlogPosts && $page.props.recentBlogPosts.length > 0" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
              <div
                v-for="post in $page.props.recentBlogPosts"
                :key="post.id"
                class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20 hover:bg-white/30 dark:hover:bg-white/15 transition-all duration-200 group"
              >
                <h4 class="text-lg font-semibold text-white mb-3 line-clamp-2 group-hover:text-purple-300 transition-colors">
                  <Link
                    :href="route('blog.show', post.slug)"
                    class="hover:text-purple-400 transition-colors"
                  >
                    {{ post.title }}
                  </Link>
                </h4>
                <p class="text-sm text-gray-300 mb-4 line-clamp-3">
                  {{ getExcerpt(post.content) }}
                </p>
                <div class="flex items-center justify-between text-sm">
                  <Link
                    :href="route('user.blog', post.user.name)"
                    class="text-purple-400 hover:text-purple-300 transition-colors font-medium"
                  >
                    {{ post.user.name }}
                  </Link>
                  <span class="text-gray-400">{{ formatDate(post.published_at) }}</span>
                </div>
              </div>
            </div>

            <!-- Empty State for Insights -->
            <div v-else class="text-center py-16">
              <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <h4 class="text-lg font-semibold text-white mb-2">No Insights Yet</h4>
              <p class="text-sm text-gray-300 mb-4">Be the first to share market insights with the community</p>
              <button
                v-if="$page.props.auth.user"
                @click="showQuickBlogModal = true"
                class="px-6 py-3 bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium rounded-lg transition-colors border border-purple-400/30"
              >
                Share Your First Insight
              </button>
            </div>
          </div>

          <!-- Analytics Tab -->
          <div v-if="activeTab === 'analytics'" class="space-y-6">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Portfolio Analytics
              </h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
              <!-- Sector Distribution -->
              <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                  </svg>
                  Sector Distribution
                </h4>
                <div class="space-y-3">
                  <div
                    v-for="sector in getTopSectors()"
                    :key="sector.name"
                    class="flex items-center justify-between p-3 bg-white/10 rounded-lg"
                  >
                    <div>
                      <p class="text-sm font-medium text-white">{{ sector.name || 'Other' }}</p>
                      <p class="text-xs text-gray-400">{{ sector.count }} companies</p>
                    </div>
                    <div class="text-right">
                      <div class="text-sm font-medium text-orange-400">{{ Math.round((sector.count / companies.length) * 100) }}%</div>
                      <div class="w-16 bg-gray-700 rounded-full h-2 mt-1">
                        <div class="bg-orange-400 h-2 rounded-full" :style="`width: ${(sector.count / companies.length) * 100}%`"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Market Cap Analysis -->
              <div class="bg-white/20 dark:bg-white/10 backdrop-blur-sm rounded-lg p-6 border border-white/20">
                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                  </svg>
                  Market Cap Breakdown
                </h4>
                <div class="space-y-4">
                  <div class="text-center">
                    <div class="text-2xl font-bold text-white mb-2">{{ formatTotalMarketCap() }}</div>
                    <div class="text-sm text-gray-400">Total Portfolio Value</div>
                  </div>
                  <div class="grid grid-cols-2 gap-4 text-center">
                    <div class="p-3 bg-white/10 rounded-lg">
                      <div class="text-lg font-semibold text-green-400">{{ getLargeCapCount() }}</div>
                      <div class="text-xs text-gray-400">Large Cap (>$10B)</div>
                    </div>
                    <div class="p-3 bg-white/10 rounded-lg">
                      <div class="text-lg font-semibold text-blue-400">{{ getMidCapCount() }}</div>
                      <div class="text-xs text-gray-400">Mid Cap ($2B-$10B)</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions Tab -->
          <div v-if="activeTab === 'actions'" class="space-y-6">
            <div class="mb-6">
              <h3 class="text-xl font-bold text-white flex items-center">
                <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                Quick Actions Center
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Primary Actions -->
              <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white mb-4">Primary Actions</h4>
                <button
                  v-if="$page.props.auth.user"
                  @click="showQuickBlogModal = true"
                  class="w-full bg-green-500/30 hover:bg-green-500/50 text-green-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-green-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                  Share Market Insight
                </button>

                <button
                  v-if="$page.props.auth.user"
                  @click="openCreateModal"
                  class="w-full bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-blue-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                  </svg>
                  Add New Company
                </button>
              </div>

              <!-- Navigation Actions -->
              <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white mb-4">Navigation</h4>
                <Link
                  :href="route('blog.index')"
                  class="w-full bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-purple-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                  My Blog Dashboard
                </Link>

                <button
                  @click="activeTab = 'analytics'"
                  class="w-full bg-orange-500/30 hover:bg-orange-500/50 text-orange-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-orange-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                  </svg>
                  View Analytics
                </button>
              </div>

              <!-- Quick Switches -->
              <div class="space-y-4">
                <h4 class="text-lg font-semibold text-white mb-4">Quick Switch</h4>
                <button
                  @click="activeTab = 'portfolio'"
                  class="w-full bg-green-500/30 hover:bg-green-500/50 text-green-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-green-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  Manage Portfolio
                </button>

                <button
                  @click="activeTab = 'insights'"
                  class="w-full bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium py-4 px-6 rounded-lg transition-all duration-300 flex items-center justify-center border border-purple-500/30 hover:shadow-lg hover:scale-105"
                >
                  <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                  Browse Insights
                </button>
              </div>
            </div>
          </div>

        </div> <!-- End Tab Content -->
      </div> <!-- End Glass Container -->
    </div> <!-- End Lower Content Container -->


    <!-- Create Company Modal -->
    <CreateCompanyModal
      :show="showCreateModal"
      :form="companyForm"
      :errors="errors"
      :creating="creating"
      :marketCapInput="marketCapInput"
      :marketCapValidation="marketCapValidation"
      :formatMarketCap="formatMarketCap"
      @close="closeCreateModal"
      @save="createCompany"
      @market-cap-input="handleMarketCapInput"
      @update:form="companyForm = $event"
    />

    <!-- Company Details Modal -->
    <CompanyDetailsModal 
      :show="showDetailsModal" 
      :company="selectedCompany"
      :editForm="companyForm"
      :editErrors="errors"
      :saving="editingCompany"
      :editMarketCapInput="editMarketCapInput"
      :editMarketCapValidation="editMarketCapValidation"
      :formatMarketCap="formatMarketCap"
      :noteForm="noteForm"
      :noteErrors="errors"
      :creatingNote="creatingNote"
      :categories="categories"
      :documentForm="uploadForm"
      :documentErrors="errors"
      :uploading="uploading"
      :formatFileSize="formatFileSize"
      :insights="companyInsights"
      @close="closeDetailsModal"
      @create-insight="handleCreateInsight"
      @save-edit="saveCompanyEdits"
      @update:edit-form="companyForm = $event"
      @edit-market-cap-input="handleEditMarketCapInput"
      @save-note="createNote"
      @update:note-form="noteForm = $event"
      @note-file-upload="handleNoteFileUpload"
      @remove-note-file="removeNoteFile"
      @save-document="uploadDocuments"
      @update:document-form="uploadForm = $event"
      @file-upload="handleDocumentUpload"
      @remove-file="removeUploadFile"
    />

    <!-- Note Creation Modal -->
    <NoteCreationModal
      :show="showNoteModal"
      :selectedCompany="selectedCompany"
      :form="noteForm"
      :errors="errors"
      :creatingNote="creatingNote"
      :categories="categories"
      @close="showNoteModal = false"
      @save="createNote"
    />

    <!-- Document Upload Modal -->
    <DocumentUploadModal
      :show="showUploadModal"
      :selectedCompany="selectedCompany"
      :form="uploadForm"
      :errors="errors"
      :uploading="uploading"
      :categories="categories"
      :formatFileSize="formatFileSize"
      @close="showUploadModal = false"
      @save="uploadDocuments"
      @file-upload="handleDocumentUpload"
      @remove-file="removeUploadFile"
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
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import CompanyDetailsModal from '@/Components/Modals/CompanyDetailsModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import axios from 'axios'

const companies = ref([])
const searchQuery = ref('')
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const debugModalVisible = ref(false)
const showNoteModal = ref(false)
const showUploadModal = ref(false)
const showQuickBlogModal = ref(false)
const quickBlogCompany = ref(null)
const selectedCompany = ref(null)
const companyInsights = ref([])
const loading = ref(false)
const creating = ref(false)
const deleting = ref(false)
const creatingNote = ref(false)
const uploading = ref(false)
const errors = ref({})
const { isDarkMode, toggleDarkMode, initializeDarkMode } = useDarkMode()
const categories = ref([])
const tags = ref([])
const viewMode = ref('cards') // 'cards' or 'list'
const selectedCompanies = ref([])
const isSelectAll = ref(false)
const bulkDeleting = ref(false)
const isEditingCompany = ref(false)
const editingCompany = ref(false)
const starsCanvas = ref(null)
const activeTab = ref('overview') // Tab navigation state

const companyForm = ref({
  name: '',
  ticker_symbol: '',
  sector: '',
  industry: '',
  market_cap: '',
  description: ''
})

const noteForm = ref({
  title: '',
  content: '',
  company_id: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const uploadForm = ref({
  title: '',
  description: '',
  company_id: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const marketCapInput = ref('')
const marketCapValidation = ref({ state: '', timer: null })

const filteredCompanies = computed(() => {
  if (!searchQuery.value.trim()) {
    return companies.value
  }
  
  const query = searchQuery.value.toLowerCase().trim()
  return companies.value.filter(company => 
    company.name.toLowerCase().includes(query) ||
    company.ticker.toLowerCase().includes(query) ||
    (company.sector && company.sector.toLowerCase().includes(query)) ||
    (company.industry && company.industry.toLowerCase().includes(query))
  )
})

const canAccessAdmin = computed(() => {
  const user = usePage().props.auth?.user
  return user && (user.roles?.some(role => role.name === 'admin') || user.permissions?.some(permission => ['manage users', 'manage roles', 'manage permissions'].includes(permission.name)))
})

const fetchCompanies = async (search = '') => {
  try {
    loading.value = true
    const params = search ? { search } : {}
    const response = await axios.get('/api/companies', { params })
    companies.value = response.data
  } catch (error) {
    console.error('Error fetching companies:', error)
  } finally {
    loading.value = false
  }
}

// Debounced search
let searchTimeout = null
watch(searchQuery, (newValue) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchCompanies(newValue)
  }, 300)
})



const formatMarketCap = (value) => {
  if (!value) return 'N/A'
  
  if (value >= 1000000000000) {
    return `$${(value / 1000000000000).toFixed(1)}T`
  } else if (value >= 1000000000) {
    return `$${(value / 1000000000).toFixed(1)}B`
  } else {
    return `$${(value / 1000000).toFixed(1)}M`
  }
}

const formatMarketCapAsUserTypes = (value) => {
  if (!value) return ''
  
  // Remove all non-numeric characters except decimal points
  const numericValue = parseFloat(value.toString().replace(/[^\d.]/g, ''))
  if (isNaN(numericValue)) return ''
  
  if (numericValue >= 1000000000000) {
    return `${(numericValue / 1000000000000).toFixed(1)} Trillion`
  } else if (numericValue >= 1000000000) {
    return `${(numericValue / 1000000000).toFixed(1)} Billion`
  } else if (numericValue >= 1000000) {
    return `${(numericValue / 1000000).toFixed(1)} Million`
  } else if (numericValue >= 1000) {
    // Format with commas for thousands
    return numericValue.toLocaleString()
  } else {
    return numericValue.toString()
  }
}

const parseMarketCapForStorage = (displayValue) => {
  if (!displayValue) return ''
  
  // Extract the numeric value and multiplier
  const cleanValue = displayValue.toString().replace(/[,$]/g, '')
  
  if (cleanValue.includes('Trillion')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000000000
  } else if (cleanValue.includes('Billion')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000000
  } else if (cleanValue.includes('Million')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000
  } else {
    // Remove commas and parse as regular number
    return parseFloat(cleanValue.replace(/,/g, '')) || ''
  }
}

const parseMarketCap = (input) => {
  if (!input) return 0
  
  const value = input.toString().trim().toUpperCase()
  
  // Handle comma-separated numbers and shorthand notation
  const match = value.match(/^([\d,]+\.?\d*)([KMBT])?$/)
  
  if (!match) return 0
  
  // Remove commas and parse number
  const number = parseFloat(match[1].replace(/,/g, ''))
  if (isNaN(number)) return 0
  
  const multiplier = match[2]
  switch (multiplier) {
    case 'K': return number * 1000
    case 'M': return number * 1000000
    case 'B': return number * 1000000000
    case 'T': return number * 1000000000000
    default: return number
  }
}

const formatMarketCapInput = (value) => {
  if (!value) return ''
  
  if (value >= 1000000000000) {
    return `${(value / 1000000000000).toFixed(1)}T`
  } else if (value >= 1000000000) {
    return `${(value / 1000000000).toFixed(1)}B`
  } else if (value >= 1000000) {
    return `${(value / 1000000).toFixed(1)}M`
  } else if (value >= 1000) {
    return `${(value / 1000).toFixed(1)}K`
  } else {
    return value.toString()
  }
}

const handleMarketCapInput = (event) => {
  const input = event.target.value
  marketCapInput.value = input
  
  // Clear existing timer
  if (marketCapValidation.value.timer) {
    clearTimeout(marketCapValidation.value.timer)
  }
  
  if (!input) {
    companyForm.value.market_cap = ''
    marketCapValidation.value.state = ''
    return
  }
  
  // Parse and store numeric value
  const numericValue = parseMarketCap(input)
  companyForm.value.market_cap = numericValue
  
  // Set validation state with debounce
  marketCapValidation.value.state = 'validating'
  marketCapValidation.value.timer = setTimeout(() => {
    if (numericValue > 0) {
      marketCapValidation.value.state = 'valid'
    } else {
      marketCapValidation.value.state = 'invalid'
    }
  }, 500)
}

const editMarketCapInput = ref('')
const editMarketCapValidation = ref({ state: '', timer: null })

const handleEditMarketCapInput = (event) => {
  const input = event.target.value
  editMarketCapInput.value = input
  
  // Clear existing timer
  if (editMarketCapValidation.value.timer) {
    clearTimeout(editMarketCapValidation.value.timer)
  }
  
  if (!input) {
    companyForm.value.market_cap = ''
    editMarketCapValidation.value.state = ''
    return
  }
  
  // Parse and store numeric value
  const numericValue = parseMarketCap(input)
  companyForm.value.market_cap = numericValue
  
  // Set validation state with debounce
  editMarketCapValidation.value.state = 'validating'
  editMarketCapValidation.value.timer = setTimeout(() => {
    if (numericValue > 0) {
      editMarketCapValidation.value.state = 'valid'
    } else {
      editMarketCapValidation.value.state = 'invalid'
    }
  }, 500)
}

const openCreateModal = () => {
  // First close any open modals and clear state
  showDetailsModal.value = false
  selectedCompany.value = null
  isEditingCompany.value = false
  
  // Then open create modal
  showCreateModal.value = true
}

const closeCreateModal = () => {
  companyForm.value = {
    name: '',
    ticker_symbol: '',
    sector: '',
    industry: '',
    market_cap: '',
    description: ''
  }
  marketCapInput.value = ''
  if (marketCapValidation.value.timer) {
    clearTimeout(marketCapValidation.value.timer)
  }
  marketCapValidation.value.state = ''
  errors.value = {}
  showCreateModal.value = false
}

// Quick Blog Modal Functions
const closeQuickBlogModal = () => {
  showQuickBlogModal.value = false
  quickBlogCompany.value = null
}

const handleBlogPosted = () => {
  // Refresh the page to show the new blog post in the widget
  window.location.reload()
}

const openQuickBlogWithCompany = (company) => {
  quickBlogCompany.value = company
  showQuickBlogModal.value = true
}

const handleCreateInsight = (company) => {
  // Open the quick blog modal with the selected company
  openQuickBlogWithCompany(company)
}

const loadCompanyInsights = async (companyId) => {
  if (!companyId) {
    companyInsights.value = []
    return
  }

  try {
    const response = await axios.get(`/api/companies/${companyId}/blog-posts`)
    companyInsights.value = response.data
  } catch (error) {
    console.error('Failed to load company insights:', error)
    companyInsights.value = []
  }
}

const createCompany = async () => {
  try {
    creating.value = true
    errors.value = {}
    
    // Convert market_cap to number if provided
    const formData = { ...companyForm.value }
    if (formData.market_cap) {
      formData.market_cap = parseFloat(formData.market_cap)
    }
    
    const response = await axios.post('/api/companies', formData)
    companies.value.unshift(response.data)
    
    // Reset form and close modal
    companyForm.value = {
      name: '',
      ticker_symbol: '',
      sector: '',
      industry: '',
      market_cap: '',
      description: ''
    }
    marketCapInput.value = ''
    if (marketCapValidation.value.timer) {
      clearTimeout(marketCapValidation.value.timer)
    }
    marketCapValidation.value.state = ''
    showCreateModal.value = false
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error creating company:', error)
      errors.value = { general: 'An error occurred while creating the company. Please try again.' }
    }
  } finally {
    creating.value = false
  }
}

const viewCompanyDetails = async (company) => {
  console.log('viewCompanyDetails called with:', company)
  try {
    // Close create modal if open
    showCreateModal.value = false
    
    // Fetch detailed company information from API
    console.log('Fetching company details for ID:', company.id)
    const response = await axios.get(`/api/companies/${company.id}`)
    
    // Set both values in sequence
    selectedCompany.value = { ...response.data }
    
    // Initialize edit form with company data for the tabbed interface
    companyForm.value = {
      name: selectedCompany.value.name || '',
      ticker_symbol: selectedCompany.value.ticker || '',
      sector: selectedCompany.value.sector || '',
      industry: selectedCompany.value.industry || '',
      market_cap: selectedCompany.value.marketCap || '',
      description: selectedCompany.value.description || ''
    }
    
    // Initialize edit market cap input
    if (selectedCompany.value.marketCap) {
      editMarketCapInput.value = formatMarketCapInput(selectedCompany.value.marketCap)
      editMarketCapValidation.value.state = 'valid'
    } else {
      editMarketCapInput.value = ''
      editMarketCapValidation.value.state = ''
    }
    
    // Initialize note form with company ID
    noteForm.value.company_id = selectedCompany.value.id
    noteForm.value.title = ''
    noteForm.value.content = ''
    noteForm.value.category_id = ''
    noteForm.value.visibility = 'public'
    noteForm.value.files = []
    
    // Initialize upload form with company ID
    uploadForm.value.company_id = selectedCompany.value.id
    uploadForm.value.title = ''
    uploadForm.value.description = ''
    uploadForm.value.category_id = ''
    uploadForm.value.visibility = 'private'
    uploadForm.value.files = []
    
    // Load company insights
    await loadCompanyInsights(selectedCompany.value.id)

    showDetailsModal.value = true

    console.log('selectedCompany set to:', selectedCompany.value)
    console.log('showDetailsModal set to:', showDetailsModal.value)
    console.log('Both values:', !!selectedCompany.value, showDetailsModal.value)

    // Force Vue to detect changes
    await nextTick()
    console.log('After nextTick - values:', !!selectedCompany.value, showDetailsModal.value)
  } catch (error) {
    console.error('Error fetching company details:', error)
    // Fallback to basic company data if API call fails
    selectedCompany.value = { ...company }
    
    // Initialize forms with fallback data
    companyForm.value = {
      name: company.name || '',
      ticker_symbol: company.ticker || '',
      sector: company.sector || '',
      industry: company.industry || '',
      market_cap: company.marketCap || '',
      description: company.description || ''
    }
    
    editMarketCapInput.value = company.marketCap ? formatMarketCapInput(company.marketCap) : ''
    editMarketCapValidation.value.state = company.marketCap ? 'valid' : ''
    
    noteForm.value.company_id = company.id
    uploadForm.value.company_id = company.id
    
    showDetailsModal.value = true
  }
}

const deleteCompany = async (company) => {
  if (!confirm(`Are you sure you want to delete ${company.name}? This action cannot be undone.`)) {
    return
  }
  
  try {
    deleting.value = true
    await axios.delete(`/api/companies/${company.id}`)
    
    // Remove company from the list
    const index = companies.value.findIndex(c => c.id === company.id)
    if (index !== -1) {
      companies.value.splice(index, 1)
    }
    
    // Close modal
    showDetailsModal.value = false
    selectedCompany.value = null
    
  } catch (error) {
    console.error('Error deleting company:', error)
    alert('Failed to delete company. Please try again.')
  } finally {
    deleting.value = false
  }
}

const fetchCategoriesAndTags = async () => {
  try {
    const [categoriesResponse, tagsResponse] = await Promise.all([
      axios.get('/api/categories'),
      axios.get('/api/tags')
    ])
    categories.value = categoriesResponse.data
    tags.value = tagsResponse.data
  } catch (error) {
    console.error('Error fetching categories and tags:', error)
  }
}

const openResearchModal = (company) => {
  selectedCompany.value = company
  researchForm.value.company_id = company.id
  showResearchModal.value = true
}

const openNoteModal = (company) => {
  selectedCompany.value = company
  noteForm.value.company_id = company.id
  noteForm.value.title = ''
  noteForm.value.content = ''
  noteForm.value.category_id = ''
  noteForm.value.visibility = 'private'
  noteForm.value.files = []
  errors.value = {}
  showNoteModal.value = true
}

const openUploadModal = (company) => {
  selectedCompany.value = company
  uploadForm.value.company_id = company.id
  uploadForm.value.title = ''
  uploadForm.value.description = ''
  uploadForm.value.category_id = ''
  uploadForm.value.visibility = 'private'
  uploadForm.value.files = []
  errors.value = {}
  showUploadModal.value = true
}

const createNote = async () => {
  try {
    creatingNote.value = true
    errors.value = {}
    
    // Use FormData if files are attached, otherwise use regular JSON
    let requestData
    let requestConfig = {}
    
    if (noteForm.value.files && noteForm.value.files.length > 0) {
      // Use FormData for file uploads
      const formData = new FormData()
      formData.append('title', noteForm.value.title)
      formData.append('content', noteForm.value.content)
      formData.append('company_id', noteForm.value.company_id)
      formData.append('visibility', noteForm.value.visibility)
      
      if (noteForm.value.category_id) {
        formData.append('category_id', noteForm.value.category_id)
      }
      
      // Append files
      noteForm.value.files.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file)
      })
      
      requestData = formData
      requestConfig = {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    } else {
      // Use regular JSON for notes without files
      requestData = {
        title: noteForm.value.title,
        content: noteForm.value.content,
        company_id: noteForm.value.company_id,
        category_id: noteForm.value.category_id,
        visibility: noteForm.value.visibility
      }
    }
    
    const response = await axios.post('/api/research-items', requestData, requestConfig)
    
    // Reset form and close modal
    noteForm.value = {
      title: '',
      content: '',
      company_id: '',
      category_id: '',
      visibility: 'private',
      files: []
    }
    showNoteModal.value = false
    
    // Refresh company details if modal is open
    if (showDetailsModal.value && selectedCompany.value) {
      viewCompanyDetails(selectedCompany.value)
    }
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error creating note:', error)
      errors.value = { general: 'An error occurred while creating the note. Please try again.' }
    }
  } finally {
    creatingNote.value = false
  }
}

const uploadDocuments = async () => {
  try {
    uploading.value = true
    errors.value = {}
    
    const formData = new FormData()
    formData.append('title', uploadForm.value.title)
    formData.append('content', uploadForm.value.description)
    formData.append('company_id', uploadForm.value.company_id)
    formData.append('visibility', uploadForm.value.visibility)
    
    if (uploadForm.value.category_id) {
      formData.append('category_id', uploadForm.value.category_id)
    }
    
    // Append files
    uploadForm.value.files.forEach((file, index) => {
      formData.append(`attachments[${index}]`, file)
    })
    
    const response = await axios.post('/api/research-items', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Reset form and close modal
    uploadForm.value = {
      title: '',
      description: '',
      company_id: '',
      category_id: '',
      visibility: 'private',
      files: []
    }
    showUploadModal.value = false
    
    // Refresh company details if modal is open
    if (showDetailsModal.value && selectedCompany.value) {
      viewCompanyDetails(selectedCompany.value)
    }
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error uploading documents:', error)
      errors.value = { general: 'An error occurred while uploading documents. Please try again.' }
    }
  } finally {
    uploading.value = false
  }
}

const handleDocumentUpload = (event) => {
  const files = Array.from(event.target.files)
  uploadForm.value.files = [...uploadForm.value.files, ...files]
}

const removeUploadFile = (index) => {
  uploadForm.value.files.splice(index, 1)
}

const handleNoteFileUpload = (event) => {
  const files = Array.from(event.target.files)
  noteForm.value.files = [...noteForm.value.files, ...files]
}

const removeNoteFile = (index) => {
  noteForm.value.files.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getExcerpt = (content) => {
  // Remove markdown syntax for a clean excerpt
  const text = content.replace(/[#*_`>-]/g, '').replace(/\n/g, ' ')
  return text.length > 120 ? text.substring(0, 120) + '...' : text
}

// New dashboard utility functions
const formatTotalMarketCap = () => {
  const total = companies.value.reduce((sum, company) => {
    return sum + (company.marketCap || 0)
  }, 0)
  return formatMarketCap(total)
}

const getTotalResearchItems = () => {
  return companies.value.reduce((sum, company) => {
    return sum + (company.researchItemsCount || 0)
  }, 0)
}

const getTopSectors = () => {
  const sectorCounts = {}

  companies.value.forEach(company => {
    const sector = company.sector || 'Other'
    sectorCounts[sector] = (sectorCounts[sector] || 0) + 1
  })

  return Object.entries(sectorCounts)
    .map(([name, count]) => ({ name, count }))
    .sort((a, b) => b.count - a.count)
    .slice(0, 5)
}

const getLargeCapCount = () => {
  return companies.value.filter(company => (company.marketCap || 0) > 10000000000).length
}

const getMidCapCount = () => {
  return companies.value.filter(company => {
    const marketCap = company.marketCap || 0
    return marketCap >= 2000000000 && marketCap <= 10000000000
  }).length
}

const toggleViewMode = () => {
  viewMode.value = viewMode.value === 'cards' ? 'list' : 'cards'
  selectedCompanies.value = []
  isSelectAll.value = false
}

const toggleSelectAll = () => {
  if (isSelectAll.value) {
    selectedCompanies.value = []
    isSelectAll.value = false
  } else {
    selectedCompanies.value = filteredCompanies.value.map(c => c.id)
    isSelectAll.value = true
  }
}

const toggleCompanySelection = (companyId) => {
  const index = selectedCompanies.value.indexOf(companyId)
  if (index > -1) {
    selectedCompanies.value.splice(index, 1)
  } else {
    selectedCompanies.value.push(companyId)
  }
  
  // Update select all checkbox state
  isSelectAll.value = selectedCompanies.value.length === filteredCompanies.value.length
}

const bulkDeleteCompanies = async () => {
  if (selectedCompanies.value.length === 0) return
  
  if (!confirm(`Are you sure you want to delete ${selectedCompanies.value.length} selected companies? This action cannot be undone.`)) {
    return
  }
  
  try {
    bulkDeleting.value = true
    
    // Delete companies one by one
    const deletePromises = selectedCompanies.value.map(companyId => 
      axios.delete(`/api/companies/${companyId}`)
    )
    
    await Promise.all(deletePromises)
    
    // Remove deleted companies from local state
    companies.value = companies.value.filter(company => 
      !selectedCompanies.value.includes(company.id)
    )
    
    selectedCompanies.value = []
    isSelectAll.value = false
    
  } catch (error) {
    console.error('Error deleting companies:', error)
    alert('An error occurred while deleting companies. Please try again.')
  } finally {
    bulkDeleting.value = false
  }
}

// Company Edit Functions
const editCompany = (company) => {
  // Open details modal and start editing
  viewCompanyDetails(company)
  nextTick(() => {
    startEditingCompany()
  })
}

const startEditingCompany = () => {
  if (!selectedCompany.value) return
  
  // Populate form with current company data
  companyForm.value = {
    name: selectedCompany.value.name || '',
    ticker_symbol: selectedCompany.value.ticker || '',
    sector: selectedCompany.value.sector || '',
    industry: selectedCompany.value.industry || '',
    market_cap: selectedCompany.value.marketCap || '',
    description: selectedCompany.value.description || ''
  }
  
  // Populate edit market cap input if there's a value
  if (selectedCompany.value.marketCap) {
    editMarketCapInput.value = formatMarketCapInput(selectedCompany.value.marketCap)
    editMarketCapValidation.value.state = 'valid'
  } else {
    editMarketCapInput.value = ''
    editMarketCapValidation.value.state = ''
  }
  
  isEditingCompany.value = true
}

const saveCompanyEdits = async () => {
  if (!selectedCompany.value) return
  
  try {
    editingCompany.value = true
    errors.value = {}
    
    const response = await axios.put(`/api/companies/${selectedCompany.value.id}`, {
      name: companyForm.value.name,
      ticker_symbol: companyForm.value.ticker_symbol,
      sector: companyForm.value.sector,
      industry: companyForm.value.industry,
      market_cap: parseMarketCap(companyForm.value.market_cap),
      description: companyForm.value.description
    })
    
    // Update the company in the list
    const index = companies.value.findIndex(c => c.id === selectedCompany.value.id)
    if (index !== -1) {
      companies.value[index] = { ...companies.value[index], ...response.data }
    }
    
    // Update selectedCompany
    selectedCompany.value = { ...selectedCompany.value, ...response.data }
    
    // Reset form and exit edit mode
    companyForm.value = {
      name: '',
      ticker_symbol: '',
      sector: '',
      industry: '',
      market_cap: '',
      description: ''
    }
    isEditingCompany.value = false
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error updating company:', error)
      errors.value = { general: 'An error occurred while updating the company. Please try again.' }
    }
  } finally {
    editingCompany.value = false
  }
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  isEditingCompany.value = false
  selectedCompany.value = null
  errors.value = {}
}

onMounted(() => {
  fetchCompanies()
  fetchCategoriesAndTags()
  
  // Initialize dark mode using the composable
  initializeDarkMode()
  
  // Initialize shooting stars animation  
  initStarsAnimation()
})

// Canvas stars animation

const initStarsAnimation = () => {
  setTimeout(() => {
    const canvas = starsCanvas.value
    if (!canvas) return
    
    const ctx = canvas.getContext('2d')
    if (!ctx) return
    
    // Set canvas size
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
    
    // Shooting stars array
    const shootingStars = []
    const staticStars = []
    const satellites = []
    
    // Create static twinkling stars with depth
    for (let i = 0; i < 50; i++) {
      const size = Math.random() * 2 + 0.5
      const y = Math.random() * canvas.height
      
      // Calculate distance factor (smaller stars higher up appear more distant)
      const distanceFactor = (size / 2.5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.05 + (distanceFactor * 0.35) // 0.05 to 0.4 range
      
      staticStars.push({
        x: Math.random() * canvas.width,
        y: y,
        size: size,
        baseOpacity: baseOpacity,
        opacity: baseOpacity,
        twinkleSpeed: Math.random() * 0.02 + 0.01,
        twinkleDirection: Math.random() < 0.5 ? 1 : -1,
        distanceFactor: distanceFactor
      })
    }
    
    // Create shooting star with depth
    const createShootingStar = () => {
      const size = Math.random() * 3 + 2
      const y = Math.random() * canvas.height * 0.6
      
      // Calculate distance factor for shooting stars
      const distanceFactor = (size / 5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.1 + (distanceFactor * 0.3) // 0.1 to 0.4 range
      
      return {
        x: canvas.width + 100,
        y: y,
        size: size,
        speed: Math.random() * 5 + 3,
        opacity: baseOpacity,
        baseOpacity: baseOpacity,
        tail: [],
        tailLength: Math.floor(15 + (distanceFactor * 10)), // 15-25 tail length based on distance
        distanceFactor: distanceFactor
      }
    }
    
    // Add initial shooting stars
    for (let i = 0; i < 3; i++) {
      shootingStars.push(createShootingStar())
    }
    
    // Create satellite function
    const createSatellite = () => {
      const size = Math.random() * 1.5 + 1
      const speed = Math.random() * 1.5 + 0.8
      
      // Start from left side, travel to random point on right side
      const startX = -50
      const startY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1 // Avoid extreme edges
      const endX = canvas.width + 50
      const endY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1
      
      // Calculate direction vector
      const dx = endX - startX
      const dy = endY - startY
      const distance = Math.sqrt(dx * dx + dy * dy)
      const vx = (dx / distance) * speed
      const vy = (dy / distance) * speed
      
      return {
        x: startX,
        y: startY,
        vx: vx,
        vy: vy,
        size: size,
        opacity: 0.3 + Math.random() * 0.3, // 0.3 to 0.6 opacity
        blinkTimer: 0,
        blinkInterval: Math.random() * 90 + 60, // More frequent blinking
        isBlinking: false
      }
    }
    
    // Satellite spawn timer
    let satelliteSpawnTimer = 0
    const satelliteSpawnInterval = Math.random() * 600 + 300 // Every 5-15 seconds (at 60fps)
    
    // Create Milky Way background gradient
    const createMilkyWayBackground = () => {
      // Create diagonal Milky Way band
      const gradient = ctx.createLinearGradient(0, canvas.height * 0.3, canvas.width, canvas.height * 0.7)
      gradient.addColorStop(0, 'rgba(40, 30, 60, 0.05)')
      gradient.addColorStop(0.2, 'rgba(80, 60, 120, 0.15)')
      gradient.addColorStop(0.4, 'rgba(120, 90, 160, 0.25)')
      gradient.addColorStop(0.6, 'rgba(100, 80, 140, 0.2)')
      gradient.addColorStop(0.8, 'rgba(60, 50, 100, 0.12)')
      gradient.addColorStop(1, 'rgba(30, 25, 50, 0.05)')
      
      ctx.fillStyle = gradient
      ctx.fillRect(0, 0, canvas.width, canvas.height)
      
      
      
      // Reset alpha for other elements
      ctx.globalAlpha = 1
    }
    
    // Animation loop
    const animate = () => {
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      
      // Draw Milky Way background
      createMilkyWayBackground()
      
      // Draw static stars with depth-based opacity
      staticStars.forEach(star => {
        // Use distance factor for opacity calculation
        const finalOpacity = star.opacity * star.distanceFactor
        ctx.globalAlpha = finalOpacity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()
        
        // Twinkle effect with distance-aware limits
        const minOpacity = star.baseOpacity * 0.3
        const maxOpacity = star.baseOpacity * 1.2
        
        star.opacity += star.twinkleSpeed * star.twinkleDirection
        if (star.opacity <= minOpacity || star.opacity >= maxOpacity) {
          star.twinkleDirection *= -1
        }
      })
      
      // Draw shooting stars
      shootingStars.forEach((star, index) => {
        // Update position
        star.x -= star.speed
        star.y += star.speed * 0.5
        
        // Add to tail
        star.tail.unshift({ x: star.x, y: star.y })
        if (star.tail.length > star.tailLength) {
          star.tail.pop()
        }
        
        // Draw tail with distance-based opacity
        star.tail.forEach((point, i) => {
          const tailOpacity = (1 - i / star.tailLength) * star.opacity * star.distanceFactor * 0.5
          ctx.globalAlpha = tailOpacity
          ctx.fillStyle = '#ffffff'
          const tailSize = star.size * (1 - i / star.tailLength)
          ctx.beginPath()
          ctx.arc(point.x, point.y, tailSize, 0, Math.PI * 2)
          ctx.fill()
        })
        
        // Draw main star with distance-based opacity
        ctx.globalAlpha = star.opacity * star.distanceFactor
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()
        
        // Add glow effect with distance-based intensity
        const glowIntensity = star.distanceFactor > 0.7 ? 0.15 : 0.05 // Brighter glow for closer stars
        ctx.globalAlpha = star.opacity * glowIntensity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size * (2 + star.distanceFactor), 0, Math.PI * 2)
        ctx.fill()
        
        // Remove if off screen
        if (star.x < -100) {
          shootingStars.splice(index, 1)
          shootingStars.push(createShootingStar())
        }
      })
      
      // Handle satellite spawning
      satelliteSpawnTimer++
      if (satelliteSpawnTimer >= satelliteSpawnInterval) {
        satellites.push(createSatellite())
        satelliteSpawnTimer = 0
      }
      
      // Draw satellites
      satellites.forEach((satellite, index) => {
        // Update position with linear movement
        satellite.x += satellite.vx
        satellite.y += satellite.vy
        
        // Handle blinking
        satellite.blinkTimer++
        if (satellite.blinkTimer >= satellite.blinkInterval) {
          satellite.isBlinking = !satellite.isBlinking
          satellite.blinkTimer = 0
          if (!satellite.isBlinking) {
            // Set next blink interval
            satellite.blinkInterval = Math.random() * 60 + 120
          } else {
            // Short blink duration
            satellite.blinkInterval = 3
          }
        }
        
        // Draw satellite
        if (!satellite.isBlinking) {
          ctx.globalAlpha = satellite.opacity
          ctx.fillStyle = '#ffffff'
          ctx.beginPath()
          ctx.arc(satellite.x, satellite.y, satellite.size, 0, Math.PI * 2)
          ctx.fill()
          
          // Add subtle red/green navigation lights occasionally
          if (Math.random() < 0.2) { // 20% chance for colored light
            ctx.globalAlpha = satellite.opacity * 0.6
            ctx.fillStyle = Math.random() < 0.5 ? '#ff4444' : '#44ff44'
            ctx.beginPath()
            ctx.arc(satellite.x + satellite.size * 1.5, satellite.y, satellite.size * 0.3, 0, Math.PI * 2)
            ctx.fill()
          }
        }
        
        // Remove if off screen (with buffer)
        if (satellite.x < -100 || satellite.x > canvas.width + 100 || 
            satellite.y < -100 || satellite.y > canvas.height + 100) {
          satellites.splice(index, 1)
        }
      })
      
      ctx.globalAlpha = 1
      requestAnimationFrame(animate)
    }
    
    animate()
  }, 200)
}
</script>