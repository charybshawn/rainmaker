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
        <div class="flex items-center justify-between mb-2 sm:mb-3 lg:mb-4">
          <div class="flex items-center gap-4">
            <!-- Rainmaker Logo Image -->
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
              <button
                @click="showLoginModal = true"
                class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 backdrop-blur-xl"
                style="backdrop-filter: blur(20px) saturate(150%);"
              >
                Login
              </button>
              <button
                @click="showRegisterModal = true"
                class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl"
                style="backdrop-filter: blur(20px) saturate(150%);"
              >
                Register
              </button>
            </div>
          </div>
        </div>

        <!-- Animated Quotes Section - Crown above content -->
        <div class="mb-0">
          <AnimatedQuotes />
        </div>


      </div>
    </div>

    <!-- Glass Container for Lower Content -->
    <div class="w-[95%] sm:w-[90%] lg:w-[80%] mx-auto px-4 sm:px-6 py-3 sm:py-4 lg:py-6 -mt-12">
      <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl sm:rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 p-4 sm:p-6 lg:p-8 relative" style="backdrop-filter: blur(20px) saturate(180%);">

        <!-- Navigation Tabs with Search (top position) -->
        <div class="flex items-center justify-between mb-4 sm:mb-6 lg:mb-8">
          <div class="flex items-center space-x-2 sm:space-x-4 lg:space-x-8 relative">
            <button
                @click="switchTab('overview')"
                :class="[
                  'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
                  'border-0 shadow-none backdrop-blur-none',
                  activeTab === 'overview'
                    ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]'
                    : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                ]"
                style="backdrop-filter: blur(0px);"
              >
                <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
                  <div :class="[
                    'p-2 rounded-full transition-all duration-500',
                    activeTab === 'overview'
                      ? 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]'
                      : 'bg-white/5 group-hover:bg-white/10'
                  ]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                  </div>
                  <span class="font-semibold tracking-wide">Overview</span>
                </div>
                <div v-if="activeTab === 'overview'" class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full"></div>
              </button>

              <button
                @click="switchTab('companies')"
                :class="[
                  'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
                  'border-0 shadow-none backdrop-blur-none',
                  activeTab === 'companies'
                    ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]'
                    : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                ]"
                style="backdrop-filter: blur(0px);"
              >
                <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
                  <div :class="[
                    'p-2 rounded-full transition-all duration-500',
                    activeTab === 'companies'
                      ? 'bg-blue-500/30 shadow-[0_0_5px_rgba(59,130,246,0.3)]'
                      : 'bg-white/5 group-hover:bg-white/10'
                  ]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <span class="font-semibold tracking-wide">Companies</span>
                </div>
                <div v-if="activeTab === 'companies'" class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/5 rounded-full"></div>
              </button>

              <button
                @click="switchTab('research')"
                :class="[
                  'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
                  'border-0 shadow-none backdrop-blur-none',
                  activeTab === 'research'
                    ? 'bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 scale-105 shadow-[0_0_8px_rgba(34,197,94,0.2)]'
                    : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                ]"
                style="backdrop-filter: blur(0px);"
              >
                <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
                  <div :class="[
                    'p-2 rounded-full transition-all duration-500',
                    activeTab === 'research'
                      ? 'bg-green-500/30 shadow-[0_0_5px_rgba(34,197,94,0.3)]'
                      : 'bg-white/5 group-hover:bg-white/10'
                  ]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                  <span class="font-semibold tracking-wide">Research</span>
                </div>
                <div v-if="activeTab === 'research'" class="absolute inset-0 bg-gradient-to-br from-green-500/5 via-transparent to-green-400/5 rounded-full"></div>
              </button>

              <button
                @click="switchTab('insights')"
                :class="[
                  'group relative flex items-center space-x-1 sm:space-x-2 lg:space-x-3 px-3 sm:px-4 lg:px-6 py-2 sm:py-3 rounded-full font-medium transition-all duration-500 transform-gpu text-xs sm:text-sm',
                  'border-0 shadow-none backdrop-blur-none',
                  activeTab === 'insights'
                    ? 'bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 scale-105 shadow-[0_0_8px_rgba(147,51,234,0.2)]'
                    : 'text-gray-300 hover:text-white hover:scale-105 hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                ]"
                style="backdrop-filter: blur(0px);"
              >
                <div class="relative z-10 flex items-center space-x-1 sm:space-x-2 lg:space-x-3">
                  <div :class="[
                    'p-2 rounded-full transition-all duration-500',
                    activeTab === 'insights'
                      ? 'bg-purple-500/30 shadow-[0_0_5px_rgba(147,51,234,0.3)]'
                      : 'bg-white/5 group-hover:bg-white/10'
                  ]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                  </div>
                  <span class="font-semibold tracking-wide">Insights</span>
                </div>
                <div v-if="activeTab === 'insights'" class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/5 rounded-full"></div>
              </button>
          </div>
          <!-- Search Component aligned with navigation tabs -->
          <div class="flex items-center">
            <!-- Single Morphing Container -->
            <div
              :class="[
                'relative flex items-center bg-gradient-to-r rounded-full shadow-inner border cursor-pointer transition-all duration-700 ease-out transform-gpu',
                showHeaderSearch
                  ? 'w-72 sm:w-96 h-12 from-white/8 to-white/12 border-white/10 shadow-[0_2px_8px_0_rgba(139,69,197,0.15)]'
                  : 'w-10 h-10 from-white/5 to-white/5 border-white/5 hover:from-white/15 hover:to-white/20 hover:border-white/20 justify-center shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] hover:scale-105'
              ]"
              style="backdrop-filter: blur(20px) saturate(180%); transform-origin: right center;"
              @click="!showHeaderSearch ? toggleHeaderSearch() : null"
              @mouseenter="!showHeaderSearch ? toggleHeaderSearch() : null"
              title="Search"
            >
              <!-- Magnifying Glass Icon -->
              <div
                :class="[
                  'flex items-center justify-center shrink-0 transition-all duration-700 ease-out transform-gpu',
                  showHeaderSearch
                    ? 'ml-4 w-4 h-4 text-white/70'
                    : 'w-full h-full text-gray-300 hover:text-purple-200 hover:drop-shadow-[0_0_3px_rgba(139,69,197,0.4)]'
                ]"
              >
                <svg class="w-4 h-4 transition-all duration-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>

              <!-- Search Input (only visible when expanded) -->
              <input
                v-if="showHeaderSearch"
                id="header-search-input"
                v-model="searchQuery"
                type="text"
                placeholder="Search companies, tickers..."
                class="flex-1 h-full ml-3 mr-4 sm:mr-6 bg-transparent border-0 text-white placeholder-white/60 focus:outline-none focus:ring-0 focus:border-transparent focus:placeholder-white/80 transition-opacity duration-300 delay-200 text-xs sm:text-sm"
                style="box-shadow: none !important;"
                ref="searchInput"
              />
            </div>
          </div>
        </div>

        <!-- Header Title (moved below tabs) -->
        <div class="flex items-start mb-12">
          <h2 class="text-4xl font-bold text-white flex items-center transition-all duration-500 ease-out">
            <svg v-if="!showSearchResults" class="w-10 h-10 mr-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <svg v-else class="w-10 h-10 mr-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            {{ showSearchResults ? 'Search Results' : 'Investment Research Platform' }}
            <div v-if="showSearchResults && !isSearching" class="ml-6 text-sm text-gray-300 font-normal">
              for "{{ searchQuery }}"
            </div>
            <div v-if="showSearchResults && isSearching" class="ml-6 flex items-center space-x-3 text-blue-300">
              <div class="w-4 h-4 border-2 border-blue-300/30 border-t-blue-300 rounded-full animate-spin"></div>
              <span class="text-sm font-normal">Searching...</span>
            </div>
          </h2>
        </div>

        <!-- Tab Content -->
        <div class="tab-content">
          <!-- Search Results Content -->
          <div v-if="showSearchResults" class="space-y-8">
            <!-- Search Results Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Companies Results -->
              <div v-if="searchResults.companies.length > 0" class="backdrop-blur-3xl bg-gradient-to-br from-blue-500/10 via-white/5 to-blue-400/8 rounded-2xl border border-blue-400/20 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-lg font-semibold text-blue-200 mb-4 flex items-center justify-between">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Companies ({{ searchResults.companies.length }})
                  </div>
                  <div class="text-sm text-blue-300/70" v-if="totalPages.companies > 1">
                    Page {{ pagination.companies.currentPage }} of {{ totalPages.companies }}
                  </div>
                </h3>
                <div class="space-y-3 mb-4">
                  <div
                    v-for="company in paginatedCompanies"
                    :key="'company-' + company.id"
                    @click="openCompanyDetails(company)"
                    class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
                  >
                    <div class="flex items-center justify-between">
                      <div>
                        <p class="font-medium text-white group-hover:text-blue-200">{{ company.name }}</p>
                        <p class="text-sm text-gray-400">{{ company.ticker_symbol || company.ticker }}</p>
                      </div>
                      <div class="text-xs text-gray-500">{{ company.sector }}</div>
                    </div>
                  </div>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPages.companies > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-blue-400/20">
                  <button
                    @click="changePage('companies', pagination.companies.currentPage - 1)"
                    :disabled="pagination.companies.currentPage === 1"
                    class="px-3 py-1 rounded-lg bg-blue-500/20 text-blue-200 hover:bg-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    ←
                  </button>
                  <span class="text-sm text-blue-300/70 px-2">
                    {{ pagination.companies.currentPage }} / {{ totalPages.companies }}
                  </span>
                  <button
                    @click="changePage('companies', pagination.companies.currentPage + 1)"
                    :disabled="pagination.companies.currentPage === totalPages.companies"
                    class="px-3 py-1 rounded-lg bg-blue-500/20 text-blue-200 hover:bg-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    →
                  </button>
                </div>
              </div>

              <!-- Blog Posts Results -->
              <div v-if="searchResults.blogPosts.length > 0" class="backdrop-blur-3xl bg-gradient-to-br from-green-500/10 via-white/5 to-green-400/8 rounded-2xl border border-green-400/20 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-lg font-semibold text-green-200 mb-4 flex items-center justify-between">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    Blog Posts ({{ searchResults.blogPosts.length }})
                  </div>
                  <div class="text-sm text-green-300/70" v-if="totalPages.blogPosts > 1">
                    Page {{ pagination.blogPosts.currentPage }} of {{ totalPages.blogPosts }}
                  </div>
                </h3>
                <div class="space-y-3 mb-4">
                  <div
                    v-for="post in paginatedBlogPosts"
                    :key="'post-' + post.id"
                    @click="openBlogPost(post)"
                    class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
                  >
                    <p class="font-medium text-white group-hover:text-green-200 line-clamp-2">{{ post.title }}</p>
                    <p class="text-sm text-gray-400 mt-1">by {{ post.user?.name || post.author }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDate(post.published_at || post.created_at) }}</p>
                  </div>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPages.blogPosts > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-green-400/20">
                  <button
                    @click="changePage('blogPosts', pagination.blogPosts.currentPage - 1)"
                    :disabled="pagination.blogPosts.currentPage === 1"
                    class="px-3 py-1 rounded-lg bg-green-500/20 text-green-200 hover:bg-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    ←
                  </button>
                  <span class="text-sm text-green-300/70 px-2">
                    {{ pagination.blogPosts.currentPage }} / {{ totalPages.blogPosts }}
                  </span>
                  <button
                    @click="changePage('blogPosts', pagination.blogPosts.currentPage + 1)"
                    :disabled="pagination.blogPosts.currentPage === totalPages.blogPosts"
                    class="px-3 py-1 rounded-lg bg-green-500/20 text-green-200 hover:bg-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    →
                  </button>
                </div>
              </div>

              <!-- Research Items Results -->
              <div v-if="searchResults.researchItems.length > 0" class="backdrop-blur-3xl bg-gradient-to-br from-purple-500/10 via-white/5 to-purple-400/8 rounded-2xl border border-purple-400/20 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-lg font-semibold text-purple-200 mb-4 flex items-center justify-between">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Research Items ({{ searchResults.researchItems.length }})
                  </div>
                  <div class="text-sm text-purple-300/70" v-if="totalPages.researchItems > 1">
                    Page {{ pagination.researchItems.currentPage }} of {{ totalPages.researchItems }}
                  </div>
                </h3>
                <div class="space-y-3 mb-4">
                  <div
                    v-for="item in paginatedResearchItems"
                    :key="'research-' + item.id"
                    @click="openResearchItem(item)"
                    class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
                  >
                    <p class="font-medium text-white group-hover:text-purple-200 line-clamp-2">{{ item.title || item.name }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ item.category?.name || 'Uncategorized' }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ formatDate(item.created_at) }}</p>
                  </div>
                </div>

                <!-- Pagination Controls -->
                <div v-if="totalPages.researchItems > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-purple-400/20">
                  <button
                    @click="changePage('researchItems', pagination.researchItems.currentPage - 1)"
                    :disabled="pagination.researchItems.currentPage === 1"
                    class="px-3 py-1 rounded-lg bg-purple-500/20 text-purple-200 hover:bg-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    ←
                  </button>
                  <span class="text-sm text-purple-300/70 px-2">
                    {{ pagination.researchItems.currentPage }} / {{ totalPages.researchItems }}
                  </span>
                  <button
                    @click="changePage('researchItems', pagination.researchItems.currentPage + 1)"
                    :disabled="pagination.researchItems.currentPage === totalPages.researchItems"
                    class="px-3 py-1 rounded-lg bg-purple-500/20 text-purple-200 hover:bg-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
                  >
                    →
                  </button>
                </div>
              </div>

            </div>

            <!-- Login Required for Search -->
            <div v-if="!$page.props.auth.user && searchQuery.length >= 2" class="text-center py-12">
              <svg class="w-16 h-16 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
              </svg>
              <h3 class="text-xl font-medium text-white mb-2">Login Required</h3>
              <p class="text-gray-300 mb-6">Please log in to search and view investment research content.</p>
              <div class="flex items-center justify-center space-x-4">
                <button
                  @click="showLoginModal = true"
                  class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10 backdrop-blur-xl font-medium"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                >
                  Log In
                </button>
                <button
                  @click="showRegisterModal = true"
                  class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                >
                  Register
                </button>
              </div>
            </div>

            <!-- No Results -->
            <div v-else-if="!isSearching && !searchResults.companies.length && !searchResults.blogPosts.length && !searchResults.researchItems.length && searchQuery.length >= 2" class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <h3 class="text-xl font-medium text-gray-300 mb-2">No results found</h3>
              <p class="text-gray-500">Try adjusting your search terms or browse our content.</p>
            </div>
          </div>

          <!-- Overview Tab -->
          <div v-else-if="!showSearchResults && activeTab === 'overview'" class="space-y-4">
            <div class="grid grid-cols-3 gap-8 mb-4">
              <!-- Total Companies -->
              <div
                @click="switchTab('companies')"
                class="group relative p-6 transition-all duration-500 hover:scale-105 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/10 rounded-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                  <div>
                    <p class="text-xs text-blue-300/80 font-medium tracking-wider uppercase mb-2">Companies</p>
                    <p class="text-3xl font-light text-white/90">{{ companies.length }}</p>
                  </div>
                  <div class="w-12 h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-full flex items-center justify-center shadow-[0_0_10px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] transition-all duration-500">
                    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                </div>
              </div>


              <!-- Insights Published -->
              <div class="group relative p-6 transition-all duration-500 hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/10 rounded-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                  <div>
                    <p class="text-xs text-purple-300/80 font-medium tracking-wider uppercase mb-2">Insights</p>
                    <p class="text-3xl font-light text-white/90">{{ $page.props.recentBlogPosts?.length || 0 }}</p>
                  </div>
                  <div class="w-12 h-12 bg-gradient-to-br from-purple-500/20 to-purple-600/30 rounded-full flex items-center justify-center shadow-[0_0_10px_rgba(147,51,234,0.15)] group-hover:shadow-[0_0_15px_rgba(147,51,234,0.25)] transition-all duration-500">
                    <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- Active Research -->
              <div class="group relative p-6 transition-all duration-500 hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 via-transparent to-orange-400/10 rounded-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                  <div>
                    <p class="text-xs text-orange-300/80 font-medium tracking-wider uppercase mb-2">Research</p>
                    <p class="text-3xl font-light text-white/90">{{ getTotalResearchItems() }}</p>
                  </div>
                  <div class="w-12 h-12 bg-gradient-to-br from-orange-500/20 to-orange-600/30 rounded-full flex items-center justify-center shadow-[0_0_10px_rgba(249,115,22,0.15)] group-hover:shadow-[0_0_15px_rgba(249,115,22,0.25)] transition-all duration-500">
                    <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <!-- Insights Tab -->
          <div v-if="!showSearchResults && activeTab === 'insights'" class="space-y-6">

            <!-- Blog Post Content View -->
            <div v-if="showBlogPostContent && selectedBlogPost" class="space-y-6">
              <!-- Breadcrumb -->
              <div class="flex items-center text-sm text-gray-300 mb-6">
                <button @click="backToInsights" class="text-purple-400 hover:text-purple-300 transition-colors flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                  </svg>
                  Back to Insights
                </button>
                <span class="mx-2 text-gray-500">/</span>
                <span class="text-gray-400">{{ selectedBlogPost.title }}</span>
              </div>

              <!-- Blog Post Content -->
              <div class="backdrop-blur-3xl bg-gradient-to-br from-white/8 via-white/4 to-transparent rounded-2xl shadow-[0_8px_24px_0_rgba(31,38,135,0.15)] border border-white/15 p-8" style="backdrop-filter: blur(20px) saturate(180%);">
                <h1 class="text-3xl font-bold text-white mb-6">{{ selectedBlogPost.title }}</h1>

                <div class="flex items-center gap-4 mb-8 text-sm text-gray-300">
                  <span class="text-purple-400 font-medium">{{ selectedBlogPost.user?.name }}</span>
                  <span>{{ formatDate(selectedBlogPost.published_at) }}</span>
                </div>

                <div class="prose prose-lg prose-invert max-w-none">
                  <div class="text-gray-200 leading-relaxed whitespace-pre-line">
                    {{ selectedBlogPost.content }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Insights List View -->
            <div v-else>
              <!-- Header with Title and New Insight Button -->
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
                  New Insight
                </button>
              </div>

              <!-- Filters and Controls -->
              <div class="mb-10">
                <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center justify-between">
                  <!-- Search and Category Filter -->
                  <div class="flex flex-col sm:flex-row gap-6 flex-1">
                    <!-- Search Input -->
                    <div class="relative">
                      <input
                        v-model="insightsSearch"
                        type="text"
                        placeholder="Search insights..."
                        class="w-full sm:w-64 backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-400/50 transition-all duration-200 hover:bg-white/15"
                      />
                      <svg class="absolute right-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                      </svg>
                    </div>

                    <!-- Category Filter -->
                    <select
                      v-model="insightsFilter"
                      class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-400/50 transition-all duration-200 hover:bg-white/15"
                    >
                      <option
                        v-for="option in insightsCategoryOptions"
                        :key="option.value"
                        :value="option.value"
                        class="bg-slate-800 text-white"
                      >
                        {{ option.label }} ({{ option.count }})
                      </option>
                    </select>
                  </div>

                  <!-- View Mode and Items Per Page -->
                  <div class="flex items-center gap-6">
                    <!-- Items Per Page -->
                    <select
                      v-model="insightsPerPage"
                      class="backdrop-blur-sm bg-white/10 border border-white/20 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-400/50 transition-all duration-200 hover:bg-white/15"
                    >
                      <option value="6" class="bg-slate-800">6 per page</option>
                      <option value="9" class="bg-slate-800">9 per page</option>
                      <option value="12" class="bg-slate-800">12 per page</option>
                      <option value="18" class="bg-slate-800">18 per page</option>
                    </select>

                    <!-- View Mode Toggle -->
                    <div class="flex backdrop-blur-sm bg-white/8 rounded-2xl p-2 border border-white/15 shadow-[0_4px_16px_0_rgba(31,38,135,0.08)]">
                      <button
                        @click="insightsViewMode = 'cards'"
                        :class="[
                          'p-3 rounded-xl transition-all duration-200 transform-gpu',
                          insightsViewMode === 'cards'
                            ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
                            : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
                        ]"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                      </button>
                      <button
                        @click="insightsViewMode = 'list'"
                        :class="[
                          'p-3 rounded-xl transition-all duration-200 transform-gpu',
                          insightsViewMode === 'list'
                            ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
                            : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
                        ]"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Results Summary -->
                <div class="mt-4 text-sm text-gray-400">
                  Showing {{ paginatedInsights.length }} of {{ filteredInsights.length }} insights
                  <span v-if="insightsSearch || insightsFilter !== 'all'">
                    (filtered from {{ (usePage().props.recentBlogPosts || []).length }} total)
                  </span>
                </div>
              </div>

              <!-- Insights Content -->
              <div v-if="paginatedInsights.length > 0">
                <!-- Card View -->
                <div v-if="insightsViewMode === 'cards'" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
                  <div
                    v-for="post in paginatedInsights"
                    :key="post.id"
                    class="group relative p-6 transition-all duration-500 hover:scale-105 cursor-pointer"
                    @click="viewBlogPost(post)"
                  >
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 via-transparent to-purple-400/10 rounded-2xl border border-purple-400/20"></div>
                    <div class="relative z-10">
                      <div class="flex items-center justify-between mb-3">
                        <p class="font-medium text-white group-hover:text-purple-200 line-clamp-2 flex-1 pr-3">{{ post.title }}</p>
                        <div class="flex items-center gap-2">
                          <span v-if="post.category" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-400/20 whitespace-nowrap">
                            {{ post.category }}
                          </span>
                          <button
                            v-if="$page.props.auth.user && post.user_id === $page.props.auth.user.id"
                            @click.stop="editBlogPost(post)"
                            class="opacity-0 group-hover:opacity-100 p-1.5 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-200 hover:text-white transition-all duration-200"
                            title="Edit post"
                          >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                      <p class="text-sm text-gray-400 mt-1 line-clamp-2 group-hover:text-gray-300">{{ getExcerpt(post.content) }}</p>
                      <div class="flex items-center justify-between mt-3">
                        <p class="text-sm text-gray-400 group-hover:text-gray-300">by {{ post.user.name }}</p>
                        <p class="text-xs text-gray-500 group-hover:text-gray-400">{{ formatDate(post.published_at) }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- List View -->
                <div v-else class="space-y-1 mb-6">
                  <div
                    v-for="post in paginatedInsights"
                    :key="post.id"
                    class="py-4 px-2 hover:bg-white/5 transition-colors duration-200 group"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <div class="flex items-center gap-4 mb-2">
                          <h4 class="text-lg font-semibold text-white group-hover:text-purple-300 transition-colors flex-1">
                            <button
                              @click="viewBlogPost(post)"
                              class="hover:text-purple-300 transition-all duration-300 text-left"
                            >
                              {{ post.title }}
                            </button>
                          </h4>
                          <span v-if="post.category" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-400/20">
                            {{ post.category }}
                          </span>
                          <span class="text-sm text-purple-400 font-medium whitespace-nowrap">
                            {{ post.user.name }}
                          </span>
                          <span class="text-sm text-gray-400 whitespace-nowrap">{{ formatDate(post.published_at) }}</span>
                          <button
                            v-if="$page.props.auth.user && post.user_id === $page.props.auth.user.id"
                            @click.stop="editBlogPost(post)"
                            class="opacity-0 group-hover:opacity-100 p-1.5 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-200 hover:text-white transition-all duration-200 ml-2"
                            title="Edit post"
                          >
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                        </div>
                        <p class="text-gray-300 text-sm line-clamp-1 pr-4">
                          {{ getExcerpt(post.content) }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Pagination -->
                <div v-if="totalInsightsPages > 1" class="flex items-center justify-center space-x-3 mt-8">
                  <button
                    @click="insightsCurrentPage = Math.max(1, insightsCurrentPage - 1)"
                    :disabled="insightsCurrentPage === 1"
                    class="px-4 py-2 rounded-xl backdrop-blur-sm bg-white/10 text-white border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/20 hover:border-purple-400/30 hover:scale-105 transition-all duration-200 transform-gpu"
                  >
                    Previous
                  </button>

                  <button
                    v-for="page in Math.min(totalInsightsPages, 5)"
                    :key="page"
                    @click="insightsCurrentPage = page"
                    :class="[
                      'px-4 py-2 rounded-xl backdrop-blur-sm border transition-all duration-200 transform-gpu hover:scale-105',
                      insightsCurrentPage === page
                        ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white border-purple-400/30 shadow-[0_2px_8px_rgba(147,51,234,0.2)]'
                        : 'bg-white/10 text-gray-300 border-white/20 hover:bg-white/20 hover:border-purple-400/30'
                    ]"
                  >
                    {{ page }}
                  </button>

                  <button
                    @click="insightsCurrentPage = Math.min(totalInsightsPages, insightsCurrentPage + 1)"
                    :disabled="insightsCurrentPage === totalInsightsPages"
                    class="px-4 py-2 rounded-xl backdrop-blur-sm bg-white/10 text-white border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/20 hover:border-purple-400/30 hover:scale-105 transition-all duration-200 transform-gpu"
                  >
                    Next
                  </button>
                </div>
              </div>

              <!-- Empty State for Insights -->
              <div v-else class="text-center py-16">
                <div class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-white mb-2">
                  {{ insightsSearch || insightsFilter !== 'all' ? 'No matching insights found' : 'No Insights Yet' }}
                </h4>
                <p class="text-sm text-gray-300 mb-4">
                  {{ insightsSearch || insightsFilter !== 'all' ? 'Try adjusting your search or filters' : 'Be the first to share market insights with the community' }}
                </p>
                <button
                  v-if="$page.props.auth.user && !insightsSearch && insightsFilter === 'all'"
                  @click="showQuickBlogModal = true"
                  class="px-6 py-3 bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium rounded-lg transition-colors border border-purple-400/30"
                >
                  Share Your First Insight
                </button>
              </div>
            </div>
          </div>

          <!-- Companies Tab -->
          <div v-if="!showSearchResults && activeTab === 'companies'" class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
              <div>
                <h3 class="text-2xl font-bold text-white flex items-center">
                  <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  Company Research Database
                </h3>
                <p class="text-gray-300 mt-2">{{ companiesFiltered.length }} companies with {{ totalCompanyResearchItems }} research items</p>
              </div>

              <!-- Add Company Button -->
              <button
                v-if="$page.props.auth.user"
                @click="showCreateModal = true"
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
                    v-model="companiesSearchQuery"
                    type="text"
                    placeholder="Search companies by name, symbol, or sector..."
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-white/20 text-white placeholder-gray-400 transition-all duration-500 bg-black/10"
                    style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
                  />
                </div>
              </div>

              <!-- Sector Filter -->
              <div class="w-full lg:w-48">
                <select
                  v-model="companiesSelectedSector"
                  class="w-full py-3 px-4 rounded-xl border border-white/20 text-white transition-all duration-500 bg-black/10"
                  style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
                >
                  <option value="">All Sectors</option>
                  <option v-for="sector in companiesSectors" :key="sector" :value="sector">{{ sector }}</option>
                </select>
              </div>

              <!-- Sort By -->
              <div class="w-full lg:w-48">
                <select
                  v-model="companiesSortBy"
                  class="w-full py-3 px-4 rounded-xl border border-white/20 text-white transition-all duration-500 bg-black/10"
                  style="backdrop-filter: blur(20px) saturate(180%) !important; box-shadow: 0 4px 12px 0 rgba(31, 38, 135, 0.15) !important;"
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
            <div v-if="companiesLoading" class="text-center py-12">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400 mx-auto"></div>
              <p class="text-gray-300 mt-3">Loading companies...</p>
            </div>

            <div v-else-if="companiesPaginated.length === 0" class="text-center py-16">
              <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
              </div>
              <h4 class="text-xl font-semibold text-white mb-2">No Companies Found</h4>
              <p class="text-gray-300 mb-6">{{ companiesSearchQuery || companiesSelectedSector ? 'Try adjusting your filters' : 'Start by adding your first company' }}</p>
              <button
                v-if="$page.props.auth.user && !companiesSearchQuery && !companiesSelectedSector"
                @click="showCreateModal = true"
                class="px-6 py-3 bg-blue-500/30 hover:bg-blue-500/50 text-blue-300 font-medium rounded-lg transition-colors border border-blue-400/30"
              >
                Add Your First Company
              </button>
            </div>

            <!-- Companies Table -->
            <div v-else class="overflow-hidden rounded-2xl border border-white/20 bg-gradient-to-br from-black/20 via-black/10 to-transparent backdrop-blur-xl" style="backdrop-filter: blur(20px) saturate(180%);">
              <!-- Table Header -->
              <div class="px-6 py-4 border-b border-white/20 bg-black/10">
                <div class="grid grid-cols-12 gap-4 items-center text-sm font-medium text-gray-300">
                  <div class="col-span-4">Company</div>
                  <div class="col-span-2">Sector</div>
                  <div class="col-span-2">Market Cap</div>
                  <div class="col-span-1 text-center">Research</div>
                  <div class="col-span-1 text-center">Insights</div>
                  <div class="col-span-2 text-center">Actions</div>
                </div>
              </div>

              <!-- Table Body -->
              <div class="divide-y divide-white/20">
                <div
                  v-for="company in companiesPaginated"
                  :key="company.id"
                  class="group px-6 py-4 hover:bg-black/10 transition-all duration-300 cursor-pointer border-b border-white/10 hover:border-white/20 last:border-b-0"
                  @click="viewCompanyDetails(company)"
                >
                  <div class="grid grid-cols-12 gap-4 items-center">
                    <!-- Company Info -->
                    <div class="col-span-4 flex items-center space-x-3">
                      <!-- Stock Ticker -->
                      <div class="w-16 h-12 bg-gradient-to-br from-blue-500/20 to-blue-600/30 rounded-lg flex items-center justify-center shadow-[0_0_8px_rgba(59,130,246,0.15)] group-hover:shadow-[0_0_12px_rgba(59,130,246,0.25)] transition-all duration-300">
                        <span class="text-blue-300 font-bold text-sm">{{ company.ticker || 'N/A' }}</span>
                      </div>

                      <!-- Company Details -->
                      <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                          <h4 class="text-lg font-semibold text-white group-hover:text-blue-200 transition-colors truncate">{{ company.name }}</h4>
                        </div>
                        <div class="flex items-center gap-2">
                          <span class="text-gray-400 text-sm truncate">{{ company.industry || 'N/A' }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Sector -->
                    <div class="col-span-2">
                      <span class="text-white font-medium truncate">{{ company.sector || 'N/A' }}</span>
                    </div>

                    <!-- Market Cap -->
                    <div class="col-span-2">
                      <span class="text-white font-medium">{{ formatMarketCap(company.market_cap) }}</span>
                    </div>

                    <!-- Research Count -->
                    <div class="col-span-1 text-center">
                      <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-500/20 border border-green-400/20">
                        <span class="text-green-300 font-bold text-sm">{{ company.research_items_count || 0 }}</span>
                      </div>
                    </div>

                    <!-- Insights Count -->
                    <div class="col-span-1 text-center">
                      <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-500/20 border border-purple-400/20">
                        <span class="text-purple-300 font-bold text-sm">{{ company.blog_posts_count || 0 }}</span>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="col-span-2 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
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

            <!-- Pagination -->
            <div v-if="companiesTotalPages > 1" class="flex items-center justify-center space-x-2 mt-8 pt-6 border-t border-white/10">
              <button
                @click="companiesCurrentPage = Math.max(1, companiesCurrentPage - 1)"
                :disabled="companiesCurrentPage === 1"
                class="px-4 py-2 rounded-xl backdrop-blur-sm bg-white/10 text-white border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/20 hover:border-blue-400/30 hover:scale-105 transition-all duration-200 transform-gpu"
              >
                Previous
              </button>

              <!-- Page Numbers -->
              <div class="flex space-x-1">
                <button
                  v-for="page in companiesPageNumbers"
                  :key="page"
                  @click="companiesCurrentPage = page"
                  :class="[
                    'px-3 py-2 rounded-lg transition-all duration-200',
                    page === companiesCurrentPage
                      ? 'bg-blue-500/30 text-blue-200 border border-blue-400/30'
                      : 'bg-white/10 text-gray-300 border border-white/20 hover:bg-white/20 hover:text-white'
                  ]"
                >
                  {{ page }}
                </button>
              </div>

              <button
                @click="companiesCurrentPage = Math.min(companiesTotalPages, companiesCurrentPage + 1)"
                :disabled="companiesCurrentPage === companiesTotalPages"
                class="px-4 py-2 rounded-xl backdrop-blur-sm bg-white/10 text-white border border-white/20 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-white/20 hover:border-blue-400/30 hover:scale-105 transition-all duration-200 transform-gpu"
              >
                Next
              </button>
            </div>

            <!-- Results Summary -->
            <div class="text-center text-gray-400 text-sm mt-4">
              Showing {{ ((companiesCurrentPage - 1) * companiesPerPage) + 1 }} to {{ Math.min(companiesCurrentPage * companiesPerPage, companiesFiltered.length) }} of {{ companiesFiltered.length }} companies
            </div>
          </div>

          <!-- Analytics Tab -->
          <div v-if="!showSearchResults && activeTab === 'analytics'" class="space-y-6">
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
          <div v-if="!showSearchResults && activeTab === 'actions'" class="space-y-6">
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
                  @click="switchTab('analytics')"
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
                  @click="switchTab('insights')"
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
      :editing-post="editingBlogPost"
      @close="closeQuickBlogModal"
      @posted="handleBlogPosted"
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

  </div>
</template>

<style scoped>
/* Custom styling for smoother transitions */
</style>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import AnimatedQuotes from '@/Components/AnimatedQuotes.vue'
import CompanyDetailsModal from '@/Components/Modals/CompanyDetailsModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import LoginModal from '@/Components/Modals/LoginModal.vue'
import RegisterModal from '@/Components/Modals/RegisterModal.vue'
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
const editingBlogPost = ref(null)
const showLoginModal = ref(false)
const showRegisterModal = ref(false)
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

// Blog post viewing state
const selectedBlogPost = ref(null)
const showBlogPostContent = ref(false)

// Insights filtering and display state
const insightsViewMode = ref('cards') // 'cards' or 'list'
const insightsFilter = ref('all') // 'all', 'analysis', 'strategy', 'insights', 'quotes', 'news'
const insightsSearch = ref('')
const insightsCurrentPage = ref(1)
const insightsPerPage = ref(9)

// Universal Search State
const searchResults = ref({
  companies: [],
  blogPosts: [],
  researchItems: []
})
const isSearching = ref(false)
const showSearchResults = ref(false)
const showHeaderSearch = ref(false)

// Companies Tab State
const companiesLoading = ref(false)
const companiesSearchQuery = ref('')
const companiesSelectedSector = ref('')
const companiesSortBy = ref('name')
const companiesCurrentPage = ref(1)
const companiesPerPage = ref(10)

// Pagination State
const pagination = ref({
  companies: { currentPage: 1, perPage: 5 },
  blogPosts: { currentPage: 1, perPage: 5 },
  researchItems: { currentPage: 1, perPage: 5 }
})

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

// Pagination computed properties
const paginatedCompanies = computed(() => {
  const start = (pagination.value.companies.currentPage - 1) * pagination.value.companies.perPage
  const end = start + pagination.value.companies.perPage
  return searchResults.value.companies.slice(start, end)
})

const paginatedBlogPosts = computed(() => {
  const start = (pagination.value.blogPosts.currentPage - 1) * pagination.value.blogPosts.perPage
  const end = start + pagination.value.blogPosts.perPage
  return searchResults.value.blogPosts.slice(start, end)
})

const paginatedResearchItems = computed(() => {
  const start = (pagination.value.researchItems.currentPage - 1) * pagination.value.researchItems.perPage
  const end = start + pagination.value.researchItems.perPage
  return searchResults.value.researchItems.slice(start, end)
})

const totalPages = computed(() => ({
  companies: Math.ceil(searchResults.value.companies.length / pagination.value.companies.perPage),
  blogPosts: Math.ceil(searchResults.value.blogPosts.length / pagination.value.blogPosts.perPage),
  researchItems: Math.ceil(searchResults.value.researchItems.length / pagination.value.researchItems.perPage)
}))

// Companies Tab Computed Properties
const companiesSectors = computed(() => {
  const sectors = new Set()
  companies.value.forEach(company => {
    if (company.sector) sectors.add(company.sector)
  })
  return Array.from(sectors).sort()
})

const companiesFiltered = computed(() => {
  let filtered = companies.value

  // Search filter
  if (companiesSearchQuery.value.trim()) {
    const query = companiesSearchQuery.value.toLowerCase()
    filtered = filtered.filter(company =>
      company.name.toLowerCase().includes(query) ||
      company.ticker?.toLowerCase().includes(query) ||
      company.sector?.toLowerCase().includes(query) ||
      company.industry?.toLowerCase().includes(query)
    )
  }

  // Sector filter
  if (companiesSelectedSector.value) {
    filtered = filtered.filter(company => company.sector === companiesSelectedSector.value)
  }

  // Sort
  if (companiesSortBy.value === 'name') {
    filtered.sort((a, b) => a.name.localeCompare(b.name))
  } else if (companiesSortBy.value === 'ticker') {
    filtered.sort((a, b) => (a.ticker || '').localeCompare(b.ticker || ''))
  } else if (companiesSortBy.value === 'sector') {
    filtered.sort((a, b) => (a.sector || '').localeCompare(b.sector || ''))
  } else if (companiesSortBy.value === 'research_count') {
    filtered.sort((a, b) => (b.researchItemsCount || 0) - (a.researchItemsCount || 0))
  }

  return filtered
})

const companiesTotalPages = computed(() =>
  Math.ceil(companiesFiltered.value.length / companiesPerPage.value)
)

const companiesPaginated = computed(() => {
  const start = (companiesCurrentPage.value - 1) * companiesPerPage.value
  const end = start + companiesPerPage.value
  return companiesFiltered.value.slice(start, end)
})

const companiesPageNumbers = computed(() => {
  const pages = []
  const total = companiesTotalPages.value
  const current = companiesCurrentPage.value

  if (total <= 7) {
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    if (current <= 3) {
      pages.push(1, 2, 3, 4, '...', total)
    } else if (current >= total - 2) {
      pages.push(1, '...', total - 3, total - 2, total - 1, total)
    } else {
      pages.push(1, '...', current - 1, current, current + 1, '...', total)
    }
  }

  return pages
})

const totalCompanyResearchItems = computed(() =>
  companies.value.reduce((total, company) => total + (company.researchItemsCount || 0), 0)
)

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

// Universal Search Function
const performUniversalSearch = async (query) => {
  if (query.length < 2) {
    showSearchResults.value = false
    return
  }

  // Check if user is logged in
  if (!$page.props.auth.user) {
    showSearchResults.value = true
    isSearching.value = false
    searchResults.value = {
      companies: [],
      blogPosts: [],
      researchItems: []
    }
    return
  }

  try {
    isSearching.value = true
    showSearchResults.value = true

    // API calls for actual content
    const [companiesRes, blogPostsRes, researchItemsRes] = await Promise.all([
      // Companies search
      axios.get('/api/companies', { params: { search: query } }),
      // Blog posts search - search by title, content, AND related companies
      axios.get('/api/blog-posts/search', {
        params: {
          q: query,
          include_companies: true // Include posts linked to companies that match
        }
      }).catch((error) => {
        console.error('Blog posts search error:', error)
        return { data: [] }
      }),
      // Research items search - search by title, content, AND related companies
      axios.get('/api/research-items', {
        params: {
          search: query,
          include_companies: true // Include research linked to companies that match
        }
      }).catch(() => ({ data: [] }))
    ])

    console.log('Search API responses:', {
      companies: companiesRes.data,
      blogPosts: blogPostsRes.data,
      researchItems: researchItemsRes.data
    })

    searchResults.value = {
      companies: companiesRes.data, // Remove slicing - pagination will handle display
      blogPosts: blogPostsRes.data,
      researchItems: researchItemsRes.data
    }

    // Reset pagination when new search results arrive
    resetPagination()
  } catch (error) {
    console.error('Universal search error:', error)
  } finally {
    isSearching.value = false
  }
}

// Pagination functions
const changePage = (section, page) => {
  if (page >= 1 && page <= totalPages.value[section]) {
    pagination.value[section].currentPage = page
  }
}

const resetPagination = () => {
  pagination.value.companies.currentPage = 1
  pagination.value.blogPosts.currentPage = 1
  pagination.value.researchItems.currentPage = 1
}

// Debounced universal search
let searchTimeout = null
watch(searchQuery, (newValue) => {
  clearTimeout(searchTimeout)

  if (newValue.length === 0) {
    showSearchResults.value = false
    return
  }

  searchTimeout = setTimeout(() => {
    performUniversalSearch(newValue)
  }, 300)
})

const closeSearch = () => {
  showSearchResults.value = false
  searchQuery.value = ''
  showHeaderSearch.value = false
}

const switchTab = (tabName) => {
  activeTab.value = tabName
  closeSearch() // Clear search when switching tabs
}

const toggleHeaderSearch = () => {
  if (!showHeaderSearch.value) {
    showHeaderSearch.value = true
    // Focus on the input after Vue updates the DOM
    nextTick(() => {
      const input = document.querySelector('#header-search-input')
      if (input) input.focus()
    })
  }
}

const closeHeaderSearch = () => {
  showHeaderSearch.value = false
  closeSearch()
}

// Search result click handlers
const openCompanyDetails = (company) => {
  selectedCompany.value = company
  showDetailsModal.value = true
  closeSearch()
}

const openBlogPost = (post) => {
  viewBlogPost(post)
  closeSearch()
}

const viewBlogPost = (post) => {
  selectedBlogPost.value = post
  showBlogPostContent.value = true
}

const backToInsights = () => {
  selectedBlogPost.value = null
  showBlogPostContent.value = false
}

// Insights computed properties
const filteredInsights = computed(() => {
  const posts = usePage().props.recentBlogPosts || []

  // Filter by category
  let filtered = posts
  if (insightsFilter.value !== 'all') {
    filtered = filtered.filter(post => post.category === insightsFilter.value)
  }

  // Filter by search
  if (insightsSearch.value) {
    const search = insightsSearch.value.toLowerCase()
    filtered = filtered.filter(post =>
      post.title.toLowerCase().includes(search) ||
      post.content.toLowerCase().includes(search) ||
      post.user.name.toLowerCase().includes(search)
    )
  }

  return filtered
})

const paginatedInsights = computed(() => {
  const start = (insightsCurrentPage.value - 1) * insightsPerPage.value
  const end = start + insightsPerPage.value
  return filteredInsights.value.slice(start, end)
})

const totalInsightsPages = computed(() => {
  return Math.ceil(filteredInsights.value.length / insightsPerPage.value)
})

const insightsCategoryOptions = computed(() => {
  const posts = usePage().props.recentBlogPosts || []
  return [
    { value: 'all', label: 'All Categories', count: posts.length },
    { value: 'analysis', label: 'Market Analysis', count: posts.filter(p => p.category === 'analysis').length },
    { value: 'strategy', label: 'Investment Strategy', count: posts.filter(p => p.category === 'strategy').length },
    { value: 'insights', label: 'Company Insights', count: posts.filter(p => p.category === 'insights').length },
    { value: 'quotes', label: 'Investment Quotes', count: posts.filter(p => p.category === 'quotes').length },
    { value: 'news', label: 'Market News', count: posts.filter(p => p.category === 'news').length }
  ]
})

const openResearchItem = (item) => {
  // Navigate to research item or open modal - implement based on your research item structure
  console.log('Opening research item:', item)
  closeSearch()
}



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
  editingBlogPost.value = null
}

const handleBlogPosted = () => {
  // Refresh the page to show the new blog post in the widget
  window.location.reload()
}

const openQuickBlogWithCompany = (company) => {
  quickBlogCompany.value = company
  showQuickBlogModal.value = true
}
const editBlogPost = (post) => {
  editingBlogPost.value = post
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

// Companies Tab Methods
const fetchCompaniesWithResearch = async () => {
  try {
    companiesLoading.value = true
    const response = await axios.get('/api/companies')
    companies.value = response.data
  } catch (error) {
    console.error('Error fetching companies with research:', error)
  } finally {
    companiesLoading.value = false
  }
}

const clearCompaniesFilters = () => {
  companiesSearchQuery.value = ''
  companiesSelectedSector.value = ''
  companiesSortBy.value = 'name'
  companiesCurrentPage.value = 1
}

const goToCompaniesPage = (page) => {
  if (page >= 1 && page <= companiesTotalPages.value) {
    companiesCurrentPage.value = page
  }
}

const previousCompaniesPage = () => {
  if (companiesCurrentPage.value > 1) {
    companiesCurrentPage.value--
  }
}

const nextCompaniesPage = () => {
  if (companiesCurrentPage.value < companiesTotalPages.value) {
    companiesCurrentPage.value++
  }
}

const viewCompanyResearch = (company) => {
  // This could open a modal or navigate to research items for this company
  console.log('Viewing research for company:', company.name)
  // Future implementation: Could switch to research tab with company filter
}

// Watch for companies tab state changes
watch([companiesSearchQuery, companiesSelectedSector, companiesSortBy], () => {
  companiesCurrentPage.value = 1 // Reset to first page when filters change
})

onMounted(() => {
  fetchCompanies()
  fetchCompaniesWithResearch() // Fetch companies with research counts for companies tab
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
      // Create subtle diagonal Milky Way band
      const gradient = ctx.createLinearGradient(0, canvas.height * 0.3, canvas.width, canvas.height * 0.7)
      gradient.addColorStop(0, 'rgba(30, 30, 40, 0.02)')
      gradient.addColorStop(0.2, 'rgba(40, 40, 50, 0.05)')
      gradient.addColorStop(0.4, 'rgba(50, 50, 70, 0.08)')
      gradient.addColorStop(0.6, 'rgba(45, 45, 60, 0.06)')
      gradient.addColorStop(0.8, 'rgba(35, 35, 45, 0.04)')
      gradient.addColorStop(1, 'rgba(25, 25, 35, 0.02)')

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