<template>
  <!-- Search Results Content -->
  <div v-if="searchState.showResults" class="space-y-8">
    <!-- Search Results View Mode Toggle -->
    <div class="flex justify-between items-center">
      <div class="text-sm text-gray-400">
        {{ searchResults.companies.length + searchResults.blogPosts.length + searchResults.researchItems.length + (searchResults.documents?.length || 0) }} total results
      </div>
      <div class="flex backdrop-blur-sm bg-white/8 rounded-2xl p-2 border border-white/15 shadow-[0_4px_16px_0_rgba(31,38,135,0.08)]">
        <button
          @click="$emit('update-view-mode', 'grid')"
          :class="[
            'p-3 rounded-xl transition-all duration-200 transform-gpu',
            viewMode === 'grid'
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
          @click="$emit('update-view-mode', 'list')"
          :class="[
            'p-3 rounded-xl transition-all duration-200 transform-gpu',
            viewMode === 'list'
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
          @click="$emit('update-view-mode', 'tree')"
          :class="[
            'p-3 rounded-xl transition-all duration-200 transform-gpu',
            viewMode === 'tree'
              ? 'bg-gradient-to-br from-purple-500/50 to-purple-600/50 text-white shadow-[0_2px_4px_rgba(147,51,234,0.2)] scale-105'
              : 'text-gray-400 hover:text-white hover:bg-white/10 hover:scale-105'
          ]"
          title="Tree View"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4m0 0V4m0 4l4 4 4-4m0 0V8a2 2 0 00-2-2h-2a2 2 0 00-2 2v0"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Grid View -->
    <div v-if="viewMode === 'grid'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
            @click="$emit('company-selected', company)"
            class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-white group-hover:text-blue-200">{{ company.name }}</p>
                <p class="text-sm text-gray-400">{{ company.ticker }}</p>
              </div>
              <div class="text-xs text-gray-500">{{ company.sector }}</div>
            </div>
          </div>
        </div>

        <!-- Pagination Controls -->
        <div v-if="totalPages.companies > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-blue-400/20">
          <button
            @click="$emit('change-page', 'companies', pagination.companies.currentPage - 1)"
            :disabled="pagination.companies.currentPage === 1"
            class="px-3 py-1 rounded-lg bg-blue-500/20 text-blue-200 hover:bg-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            ←
          </button>
          <span class="text-sm text-blue-300/70 px-2">
            {{ pagination.companies.currentPage }} / {{ totalPages.companies }}
          </span>
          <button
            @click="$emit('change-page', 'companies', pagination.companies.currentPage + 1)"
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
            @click="$emit('blog-post-selected', post)"
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
            @click="$emit('change-page', 'blogPosts', pagination.blogPosts.currentPage - 1)"
            :disabled="pagination.blogPosts.currentPage === 1"
            class="px-3 py-1 rounded-lg bg-green-500/20 text-green-200 hover:bg-green-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            ←
          </button>
          <span class="text-sm text-green-300/70 px-2">
            {{ pagination.blogPosts.currentPage }} / {{ totalPages.blogPosts }}
          </span>
          <button
            @click="$emit('change-page', 'blogPosts', pagination.blogPosts.currentPage + 1)"
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
            @click="$emit('research-item-selected', item)"
            class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
          >
            <p class="font-medium text-white group-hover:text-purple-200 line-clamp-2">{{ item.title || item.name }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ item.category?.name || 'Uncategorized' }}</p>
            <div class="flex items-center justify-between text-xs mt-1">
              <span class="text-gray-500">Created: {{ formatDate(item.created_at) }}</span>
              <span v-if="item.source_date" class="text-blue-400">Source: {{ formatDate(item.source_date) }}</span>
            </div>
          </div>
        </div>

        <!-- Pagination Controls -->
        <div v-if="totalPages.researchItems > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-purple-400/20">
          <button
            @click="$emit('change-page', 'researchItems', pagination.researchItems.currentPage - 1)"
            :disabled="pagination.researchItems.currentPage === 1"
            class="px-3 py-1 rounded-lg bg-purple-500/20 text-purple-200 hover:bg-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            ←
          </button>
          <span class="text-sm text-purple-300/70 px-2">
            {{ pagination.researchItems.currentPage }} / {{ totalPages.researchItems }}
          </span>
          <button
            @click="$emit('change-page', 'researchItems', pagination.researchItems.currentPage + 1)"
            :disabled="pagination.researchItems.currentPage === totalPages.researchItems"
            class="px-3 py-1 rounded-lg bg-purple-500/20 text-purple-200 hover:bg-purple-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            →
          </button>
        </div>
      </div>

      <!-- Documents Results -->
      <div v-if="searchResults.documents?.length > 0" class="backdrop-blur-3xl bg-gradient-to-br from-orange-500/10 via-white/5 to-orange-400/8 rounded-2xl border border-orange-400/20 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
        <h3 class="text-lg font-semibold text-orange-200 mb-4 flex items-center justify-between">
          <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Documents ({{ searchResults.documents.length }})
          </div>
          <div class="text-sm text-orange-300/70" v-if="totalPages.documents > 1">
            Page {{ pagination.documents.currentPage }} of {{ totalPages.documents }}
          </div>
        </h3>
        <div class="space-y-3 mb-4">
          <div
            v-for="document in paginatedDocuments"
            :key="'document-' + document.id"
            @click="$emit('document-selected', document)"
            class="p-3 rounded-xl bg-white/5 hover:bg-white/10 cursor-pointer transition-all duration-300 group"
          >
            <p class="font-medium text-white group-hover:text-orange-200 line-clamp-2">{{ document.name || document.title }}</p>
            <p class="text-sm text-gray-400 mt-1">{{ document.company?.name || 'Unknown Company' }}</p>
            <div class="flex items-center justify-between text-xs mt-1">
              <span class="text-gray-500">{{ document.type || 'Document' }}</span>
              <span class="text-gray-500">{{ formatDate(document.created_at) }}</span>
            </div>
          </div>
        </div>

        <!-- Pagination Controls -->
        <div v-if="totalPages.documents > 1" class="flex items-center justify-center space-x-2 pt-4 border-t border-orange-400/20">
          <button
            @click="$emit('change-page', 'documents', pagination.documents.currentPage - 1)"
            :disabled="pagination.documents.currentPage === 1"
            class="px-3 py-1 rounded-lg bg-orange-500/20 text-orange-200 hover:bg-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            ←
          </button>
          <span class="text-sm text-orange-300/70 px-2">
            {{ pagination.documents.currentPage }} / {{ totalPages.documents }}
          </span>
          <button
            @click="$emit('change-page', 'documents', pagination.documents.currentPage + 1)"
            :disabled="pagination.documents.currentPage === totalPages.documents"
            class="px-3 py-1 rounded-lg bg-orange-500/20 text-orange-200 hover:bg-orange-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all text-sm"
          >
            →
          </button>
        </div>
      </div>
    </div>

    <!-- List View -->
    <div v-if="viewMode === 'list'" class="space-y-4">
      <!-- Combined Results List -->
      <div v-for="(group, index) in combinedResults" :key="'group-' + index" class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/3 rounded-2xl border border-white/10 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
        <!-- Companies -->
        <div v-if="group.type === 'companies'" class="space-y-3">
          <h4 class="text-blue-200 font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Companies
          </h4>
          <div v-for="company in group.items" :key="'list-company-' + company.id" @click="$emit('company-selected', company)" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 cursor-pointer transition-all">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-medium text-white">{{ company.name }}</p>
                <p class="text-sm text-gray-400">{{ company.ticker }} • {{ company.sector }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Blog Posts -->
        <div v-if="group.type === 'blogPosts'" class="space-y-3">
          <h4 class="text-green-200 font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            Blog Posts
          </h4>
          <div v-for="post in group.items" :key="'list-post-' + post.id" @click="$emit('blog-post-selected', post)" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 cursor-pointer transition-all">
            <p class="font-medium text-white line-clamp-2">{{ post.title }}</p>
            <p class="text-sm text-gray-400">by {{ post.user?.name || post.author }} • {{ formatDate(post.published_at || post.created_at) }}</p>
          </div>
        </div>

        <!-- Research Items -->
        <div v-if="group.type === 'researchItems'" class="space-y-3">
          <h4 class="text-purple-200 font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research Items
          </h4>
          <div v-for="item in group.items" :key="'list-research-' + item.id" @click="$emit('research-item-selected', item)" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 cursor-pointer transition-all">
            <p class="font-medium text-white line-clamp-2">{{ item.title || item.name }}</p>
            <p class="text-sm text-gray-400">{{ item.category?.name || 'Uncategorized' }} • {{ formatDate(item.created_at) }}</p>
          </div>
        </div>

        <!-- Documents -->
        <div v-if="group.type === 'documents'" class="space-y-3">
          <h4 class="text-orange-200 font-semibold flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Documents
          </h4>
          <div v-for="document in group.items" :key="'list-document-' + document.id" @click="$emit('document-selected', document)" class="p-3 rounded-lg bg-white/5 hover:bg-white/10 cursor-pointer transition-all">
            <p class="font-medium text-white line-clamp-2">{{ document.name || document.title }}</p>
            <p class="text-sm text-gray-400">{{ document.company?.name || 'Unknown Company' }} • {{ formatDate(document.created_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tree View -->
    <div v-if="viewMode === 'tree'" class="space-y-6">
      <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/3 rounded-2xl border border-white/10 p-6" style="backdrop-filter: blur(20px) saturate(180%);">
        <SearchResultsTree :results="searchResults" @node-click="$emit('tree-node-click', $event)" />
      </div>
    </div>

    <!-- No Results -->
    <div v-if="!isSearching && !searchResults.companies.length && !searchResults.blogPosts.length && !searchResults.researchItems.length && !(searchResults.documents?.length) && searchQuery.length >= 2" class="text-center py-12">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
      <h3 class="text-xl font-semibold text-gray-300 mb-2">No Results Found</h3>
      <p class="text-gray-400 mb-4">Try different keywords or check your spelling</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import SearchResultsTree from '@/Components/SearchResultsTree.vue'

const props = defineProps({
  searchState: {
    type: Object,
    required: true
  },
  searchResults: {
    type: Object,
    required: true
  },
  viewMode: {
    type: String,
    default: 'grid'
  },
  pagination: {
    type: Object,
    required: true
  },
  totalPages: {
    type: Object,
    required: true
  },
  paginatedCompanies: {
    type: Array,
    default: () => []
  },
  paginatedBlogPosts: {
    type: Array,
    default: () => []
  },
  paginatedResearchItems: {
    type: Array,
    default: () => []
  },
  paginatedDocuments: {
    type: Array,
    default: () => []
  },
  isSearching: {
    type: Boolean,
    default: false
  },
  searchQuery: {
    type: String,
    default: ''
  }
})

defineEmits([
  'update-view-mode',
  'company-selected',
  'blog-post-selected',
  'research-item-selected',
  'document-selected',
  'change-page',
  'tree-node-click'
])

const combinedResults = computed(() => {
  const results = []

  if (props.searchResults.companies.length > 0) {
    results.push({
      type: 'companies',
      items: props.paginatedCompanies
    })
  }

  if (props.searchResults.blogPosts.length > 0) {
    results.push({
      type: 'blogPosts',
      items: props.paginatedBlogPosts
    })
  }

  if (props.searchResults.researchItems.length > 0) {
    results.push({
      type: 'researchItems',
      items: props.paginatedResearchItems
    })
  }

  if (props.searchResults.documents?.length > 0) {
    results.push({
      type: 'documents',
      items: props.paginatedDocuments
    })
  }

  return results
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>