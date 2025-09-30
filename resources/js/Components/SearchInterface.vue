<template>
  <div>
    <!-- Desktop/Tablet Search Component -->
    <div class="hidden sm:flex items-center">
      <div
        :class="[
          'relative flex items-center bg-gradient-to-r rounded-full shadow-inner border cursor-pointer transition-all duration-700 ease-out transform-gpu',
          showHeaderSearch
            ? 'w-64 sm:w-72 lg:w-96 h-10 sm:h-12 from-white/8 to-white/12 border-white/10 shadow-[0_2px_8px_0_rgba(139,69,197,0.15)]'
            : 'w-8 sm:w-10 h-8 sm:h-10 from-white/5 to-white/5 border-white/5 hover:from-white/15 hover:to-white/20 hover:border-white/20 justify-center shadow-[0_2px_8px_0_rgba(31,38,135,0.1)] hover:shadow-[0_2px_8px_0_rgba(139,69,197,0.2)] hover:scale-105'
        ]"
        style="backdrop-filter: blur(20px) saturate(180%); transform-origin: right center;"
        @click="!showHeaderSearch ? toggleHeaderSearch() : null"
        @mouseenter="!showHeaderSearch ? toggleHeaderSearch() : null"
        title="Search"
      >
        <div
          :class="[
            'flex items-center justify-center shrink-0 transition-all duration-700 ease-out transform-gpu',
            showHeaderSearch
              ? 'ml-3 sm:ml-4 w-3 h-3 sm:w-4 sm:h-4 text-white/70'
              : 'w-full h-full text-gray-300 hover:text-purple-200 hover:drop-shadow-[0_0_3px_rgba(139,69,197,0.4)]'
          ]"
        >
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <input
          v-if="showHeaderSearch"
          id="header-search-input"
          v-model="searchQuery"
          type="text"
          placeholder="Search companies, tickers..."
          class="flex-1 h-full ml-2 sm:ml-3 mr-3 sm:mr-4 lg:mr-6 bg-transparent border-0 text-white placeholder-white/60 focus:outline-none focus:ring-0 focus:border-transparent focus:placeholder-white/80 transition-opacity duration-300 delay-200 text-xs sm:text-sm"
          style="box-shadow: none !important;"
          ref="searchInput"
        />
      </div>
    </div>

    <!-- Search Results Content -->
    <div v-if="showSearchResults" class="space-y-8">
      <!-- Search Results View Mode Toggle -->
      <div class="flex justify-between items-center">
        <div class="text-sm text-gray-400">
          {{ searchResults.companies.length + searchResults.blogPosts.length + searchResults.researchItems.length + (searchResults.documents?.length || 0) }} total results
        </div>
        <div class="flex backdrop-blur-sm bg-white/8 rounded-2xl p-2 border border-white/15 shadow-[0_4px_16px_0_rgba(31,38,135,0.08)]">
          <button
            @click="searchResultsViewMode = 'grid'"
            :class="[
              'p-3 rounded-xl transition-all duration-200 transform-gpu',
              searchResultsViewMode === 'grid'
                ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
                : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
            ]"
            title="Grid View"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
          </button>
          <button
            @click="searchResultsViewMode = 'list'"
            :class="[
              'p-3 rounded-xl transition-all duration-200 transform-gpu',
              searchResultsViewMode === 'list'
                ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
                : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
            ]"
            title="List View"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
          </button>
          <button
            @click="searchResultsViewMode = 'tree'"
            :class="[
              'p-3 rounded-xl transition-all duration-200 transform-gpu',
              searchResultsViewMode === 'tree'
                ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
                : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
            ]"
            title="Tree View"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5m14 14H5"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Grid View -->
      <div v-if="searchResultsViewMode === 'grid'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
              @click="$emit('result-selected', { type: 'company', data: company })"
              class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
            >
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-medium text-white group-hover:text-blue-200 line-clamp-1">{{ company.name }}</p>
                  <p class="text-blue-400 text-sm">{{ company.ticker }}</p>
                  <p v-if="company.sector" class="text-gray-400 text-xs mt-1">{{ company.sector }}</p>
                </div>
                <div class="text-right">
                  <p v-if="company.marketCap" class="text-blue-300 text-sm">{{ formatMarketCap(company.marketCap) }}</p>
                </div>
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
              :key="'blog-' + post.id"
              @click="$emit('result-selected', { type: 'blog', data: post })"
              class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
            >
              <p class="font-medium text-white group-hover:text-green-200 line-clamp-2">{{ post.title }}</p>
              <p v-if="post.published_at" class="text-green-400 text-xs mt-1">{{ formatDate(post.published_at) }}</p>
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
              @click="$emit('result-selected', { type: 'research', data: item })"
              class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
            >
              <p class="font-medium text-white group-hover:text-purple-200 line-clamp-2">{{ item.title || item.name }}</p>
              <p v-if="item.company && item.company.ticker" class="text-purple-400 text-sm">{{ item.company.ticker }} - {{ item.company.name }}</p>
              <p v-if="item.source_date" class="text-blue-400 text-xs mt-1">Source: {{ formatDate(item.source_date) }}</p>
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

        <!-- Documents Results -->
        <div v-if="searchResults.documents && searchResults.documents.length > 0" class="backdrop-blur-3xl bg-gradient-to-br from-orange-500/10 via-white/5 to-orange-400/8 rounded-2xl border border-orange-400/20 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
          <h3 class="text-lg font-semibold text-orange-200 mb-4 flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              Documents ({{ searchResults.documents?.length || 0 }})
            </div>
            <div class="text-sm text-orange-300/70" v-if="totalPages.documents > 1">
              Page {{ pagination.documents.currentPage }} of {{ totalPages.documents }}
            </div>
          </h3>
          <div class="space-y-3 mb-4">
            <div
              v-for="document in paginatedDocuments"
              :key="'document-' + document.id"
              @click="$emit('result-selected', { type: 'document', data: document })"
              class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
            >
              <p class="font-medium text-white group-hover:text-orange-200 line-clamp-2">{{ document.title || document.name }}</p>
              <p v-if="document.company && document.company.ticker" class="text-orange-400 text-sm">{{ document.company.ticker }} - {{ document.company.name }}</p>
              <p v-if="document.created_at" class="text-gray-400 text-xs mt-1">{{ formatDate(document.created_at) }}</p>
            </div>
          </div>
          <!-- Pagination Controls -->
          <div v-if="totalPages.documents > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-orange-400/20">
            <button
              @click="changePage('documents', pagination.documents.currentPage - 1)"
              :disabled="pagination.documents.currentPage === 1"
              class="px-3 py-1 rounded-lg bg-orange-500/20 text-orange-200 hover:bg-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
            >
              ←
            </button>
            <span class="text-sm text-orange-300/70 px-2">
              {{ pagination.documents.currentPage }} / {{ totalPages.documents }}
            </span>
            <button
              @click="changePage('documents', pagination.documents.currentPage + 1)"
              :disabled="pagination.documents.currentPage === totalPages.documents"
              class="px-3 py-1 rounded-lg bg-orange-500/20 text-orange-200 hover:bg-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
            >
              →
            </button>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else-if="searchResultsViewMode === 'list'" class="space-y-6">
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
          <!-- Combined Results List -->
          <div class="space-y-4">
            <!-- Companies -->
            <div v-for="company in searchResults.companies" :key="'list-company-' + company.id"
                 @click="$emit('result-selected', { type: 'company', data: company })"
                 class="flex items-center justify-between p-4 rounded-lg bg-blue-500/10 border border-blue-400/20 hover:bg-blue-500/20 transition-colors cursor-pointer">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                </div>
                <div>
                  <h4 class="font-medium text-white">{{ company.name }}</h4>
                  <p class="text-blue-400 text-sm">{{ company.ticker }}</p>
                </div>
              </div>
              <div class="text-blue-300 text-sm">Company</div>
            </div>

            <!-- Blog Posts -->
            <div v-for="post in searchResults.blogPosts" :key="'list-blog-' + post.id"
                 @click="$emit('result-selected', { type: 'blog', data: post })"
                 class="flex items-center justify-between p-4 rounded-lg bg-purple-500/10 border border-purple-400/20 hover:bg-purple-500/20 transition-colors cursor-pointer">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                  </svg>
                </div>
                <div>
                  <h4 class="font-medium text-white">{{ post.title }}</h4>
                  <p v-if="post.published_at" class="text-purple-400 text-sm">{{ formatDate(post.published_at) }}</p>
                </div>
              </div>
              <div class="text-purple-300 text-sm">Blog Post</div>
            </div>

            <!-- Research Items -->
            <div v-for="item in searchResults.researchItems" :key="'list-research-' + item.id"
                 @click="$emit('result-selected', { type: 'research', data: item })"
                 class="flex items-center justify-between p-4 rounded-lg bg-green-500/10 border border-green-400/20 hover:bg-green-500/20 transition-colors cursor-pointer">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h4 class="font-medium text-white">{{ item.title || item.name }}</h4>
                  <p v-if="item.company && item.company.ticker" class="text-green-400 text-sm">{{ item.company.ticker }} - {{ item.company.name }}</p>
                  <p v-if="item.source_date" class="text-blue-400 text-xs mt-1">Source: {{ formatDate(item.source_date) }}</p>
                </div>
              </div>
              <div class="text-green-300 text-sm">Research</div>
            </div>

            <!-- Documents -->
            <div v-for="document in (searchResults.documents || [])" :key="'list-document-' + document.id"
                 @click="$emit('result-selected', { type: 'document', data: document })"
                 class="flex items-center justify-between p-4 rounded-lg bg-orange-500/10 border border-orange-400/20 hover:bg-orange-500/20 transition-colors cursor-pointer">
              <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center">
                  <svg class="w-5 h-5 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div>
                  <h4 class="font-medium text-white">{{ document.title || document.name }}</h4>
                  <p v-if="document.company && document.company.ticker" class="text-orange-400 text-sm">{{ document.company.ticker }} - {{ document.company.name }}</p>
                  <p v-if="document.created_at" class="text-gray-400 text-xs mt-1">{{ formatDate(document.created_at) }}</p>
                </div>
              </div>
              <div class="text-orange-300 text-sm">Document</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tree View -->
      <div v-else-if="searchResultsViewMode === 'tree'" class="space-y-6">
        <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-2xl border border-white/10 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
          <SearchResultsTree :searchResults="treeData" @node-click="handleTreeNodeClick" />
        </div>
      </div>

      <!-- No Results -->
      <div v-else-if="!isSearching && !searchResults.companies.length && !searchResults.blogPosts.length && !searchResults.researchItems.length && !(searchResults.documents?.length) && searchQuery.length >= 2" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <h3 class="text-xl font-medium text-gray-300 mb-2">No results found</h3>
        <p class="text-gray-400">Try a different search term or check your spelling.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, nextTick } from 'vue'
import SearchResultsTree from '@/Components/SearchResultsTree.vue'
import axios from 'axios'

// Props
const props = defineProps({
  isAuthenticated: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['search-performed', 'result-selected', 'search-cleared'])

// Search State
const searchQuery = ref('')
const searchResults = ref({
  companies: [],
  blogPosts: [],
  researchItems: [],
  documents: []
})
const isSearching = ref(false)
const showSearchResults = ref(false)
const showHeaderSearch = ref(false)
const searchResultsViewMode = ref('grid') // 'grid', 'list', 'tree'

// Pagination state
const pagination = ref({
  companies: { currentPage: 1, perPage: 6 },
  blogPosts: { currentPage: 1, perPage: 6 },
  researchItems: { currentPage: 1, perPage: 6 },
  documents: { currentPage: 1, perPage: 6 }
})

// Computed Properties
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

const paginatedDocuments = computed(() => {
  const start = (pagination.value.documents.currentPage - 1) * pagination.value.documents.perPage
  const end = start + pagination.value.documents.perPage
  return searchResults.value.documents.slice(start, end)
})

const totalPages = computed(() => ({
  companies: Math.ceil(searchResults.value.companies.length / pagination.value.companies.perPage),
  blogPosts: Math.ceil(searchResults.value.blogPosts.length / pagination.value.blogPosts.perPage),
  researchItems: Math.ceil(searchResults.value.researchItems.length / pagination.value.researchItems.perPage),
  documents: Math.ceil(searchResults.value.documents.length / pagination.value.documents.perPage)
}))

// Tree data for tree view
const treeData = computed(() => {
  const sectors = new Map()

  // Process companies
  searchResults.value.companies.forEach(company => {
    const sector = company.sector || 'Uncategorized'
    if (!sectors.has(sector)) {
      sectors.set(sector, {
        id: `sector-${sector}`,
        name: sector,
        type: 'sector',
        children: []
      })
    }
    sectors.get(sector).children.push({
      id: `company-${company.id}`,
      name: `${company.name} (${company.ticker})`,
      type: 'company',
      data: company,
      children: []
    })
  })

  // Add research items to their respective companies
  searchResults.value.researchItems.forEach(item => {
    if (item.company_id) {
      // Find the company in the tree
      for (const [sectorName, sectorData] of sectors) {
        const company = sectorData.children.find(c => c.data && c.data.id === item.company_id)
        if (company) {
          company.children.push({
            id: `research-${item.id}`,
            name: item.title || item.name,
            type: 'research',
            data: item
          })
          break
        }
      }
    }
  })

  // Add blog posts as their own section
  if (searchResults.value.blogPosts.length > 0) {
    const blogSection = {
      id: 'sector-blog',
      name: 'Blog Posts',
      type: 'sector',
      children: searchResults.value.blogPosts.map(post => ({
        id: `blog-${post.id}`,
        name: post.title,
        type: 'blog',
        data: post
      }))
    }
    sectors.set('Blog Posts', blogSection)
  }

  return Array.from(sectors.values())
})

// Search Methods
const performUniversalSearch = async (query) => {
  if (query.length < 2) {
    showSearchResults.value = false
    return
  }

  try {
    isSearching.value = true
    showSearchResults.value = true

    const response = await axios.get('/api/search', {
      params: { q: query }
    })

    console.log('Universal search API response:', response.data)
    searchResults.value = {
      companies: response.data.companies || [],
      blogPosts: response.data.blogPosts || [],
      researchItems: response.data.researchItems || [],
      documents: response.data.documents || []
    }

    // Reset pagination to first page
    Object.keys(pagination.value).forEach(key => {
      pagination.value[key].currentPage = 1
    })

    emit('search-performed', {
      query,
      results: searchResults.value,
      totalCount: searchResults.value.companies.length + searchResults.value.blogPosts.length +
                 searchResults.value.researchItems.length + (searchResults.value.documents?.length || 0)
    })

  } catch (error) {
    console.error('Universal search error:', error)
    // Set empty results on error but maintain the structure
    searchResults.value = {
      companies: [],
      blogPosts: [],
      researchItems: [],
      documents: []
    }
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

// Search interface controls
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

const closeSearch = () => {
  showSearchResults.value = false
  searchQuery.value = ''
  showHeaderSearch.value = false
  emit('search-cleared')
}

// Tree view node click handler
const handleTreeNodeClick = (node) => {
  if (node.data) {
    emit('result-selected', { type: node.type, data: node.data })
  }
}

// Utility methods
const formatMarketCap = (marketCap) => {
  if (!marketCap) return ''

  const num = parseFloat(marketCap)
  if (num >= 1000000000) {
    return `$${(num / 1000000000).toFixed(1)}B`
  } else if (num >= 1000000) {
    return `$${(num / 1000000).toFixed(1)}M`
  } else if (num >= 1000) {
    return `$${(num / 1000).toFixed(1)}K`
  } else {
    return `$${num.toFixed(0)}`
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''

  try {
    const date = new Date(dateString)
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return dateString
  }
}

// Debounced search watcher
let searchTimeout = null
watch(searchQuery, (newValue) => {
  clearTimeout(searchTimeout)
  if (newValue.length === 0) {
    showSearchResults.value = false
    emit('search-cleared')
    return
  }
  searchTimeout = setTimeout(() => {
    performUniversalSearch(newValue)
  }, 300)
})

// Expose methods for external calls (like mobile search)
const handleExternalSearch = (query) => {
  searchQuery.value = query
  if (query.trim()) {
    performUniversalSearch(query)
  } else {
    closeSearch()
  }
}

defineExpose({
  handleExternalSearch,
  closeSearch,
  searchQuery,
  showSearchResults
})
</script>