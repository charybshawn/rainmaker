<template>
  <div class="space-y-6">
    <!-- Insights Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
        </svg>
        Market Insights
      </h3>

      <button
        v-if="$page.props.auth.user"
        @click="$emit('show-blog-modal')"
        class="group relative px-4 py-2 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-purple-500/20 via-purple-400/10 to-transparent text-purple-200 rounded-lg shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(147,51,234,0.2)] border border-white/10 backdrop-blur-xl text-sm font-medium"
        style="backdrop-filter: blur(20px) saturate(150%);"
      >
        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Share Insight
      </button>
    </div>

    <!-- Filters and Search -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div class="flex items-center space-x-4">
        <!-- Category Filter -->
        <select
          v-model="insightsFilter"
          class="bg-white/10 border border-white/20 rounded-lg px-3 py-2 text-white text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
        >
          <option
            v-for="category in categoryOptions"
            :key="category.value"
            :value="category.value"
            class="bg-gray-800 text-white"
          >
            {{ category.label }} ({{ category.count }})
          </option>
        </select>

        <!-- View Mode Toggle -->
        <div class="flex bg-white/10 rounded-lg p-1">
          <button
            @click="insightsViewMode = 'cards'"
            :class="[
              'px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200',
              insightsViewMode === 'cards'
                ? 'bg-purple-500/30 text-purple-200 shadow-sm'
                : 'text-gray-300 hover:text-white hover:bg-white/10'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 00-2 2v6a2 2 0 002 2"></path>
            </svg>
          </button>
          <button
            @click="insightsViewMode = 'list'"
            :class="[
              'px-3 py-1.5 rounded-md text-xs font-medium transition-all duration-200',
              insightsViewMode === 'list'
                ? 'bg-purple-500/30 text-purple-200 shadow-sm'
                : 'text-gray-300 hover:text-white hover:bg-white/10'
            ]"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Search -->
      <div class="relative">
        <input
          v-model="insightsSearch"
          type="text"
          placeholder="Search insights..."
          class="w-64 px-4 py-2 pl-10 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/60 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
        />
        <svg class="w-4 h-4 text-white/60 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Results Summary -->
    <div class="text-sm text-gray-400">
      Showing {{ paginatedInsights.length }} of {{ filteredInsights.length }} insights
      <span v-if="insightsSearch || insightsFilter !== 'all'">
        (filtered from {{ blogPosts.length }} total)
      </span>
    </div>

    <!-- Insights Content -->
    <div v-if="paginatedInsights.length > 0">
      <!-- Card View -->
      <div v-if="insightsViewMode === 'cards'" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">
        <div
          v-for="post in paginatedInsights"
          :key="post.id"
          class="group relative p-6 transition-all duration-500 hover:scale-105 cursor-pointer"
          @click="$emit('view-post', post)"
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
                  v-if="canEditPost(post)"
                  @click.stop="$emit('edit-post', post)"
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
          class="py-4 px-2 hover:bg-white/5 transition-colors duration-200 group cursor-pointer"
          @click="$emit('view-post', post)"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-4 mb-2">
                <h4 class="text-lg font-semibold text-white group-hover:text-purple-300 transition-colors flex-1">
                  {{ post.title }}
                </h4>
                <span v-if="post.category" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-400/20">
                  {{ post.category }}
                </span>
                <span class="text-sm text-purple-400 font-medium whitespace-nowrap">
                  {{ post.user.name }}
                </span>
                <span class="text-sm text-gray-400 whitespace-nowrap">{{ formatDate(post.published_at) }}</span>
                <button
                  v-if="canEditPost(post)"
                  @click.stop="$emit('edit-post', post)"
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

    <!-- Empty State -->
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
        @click="$emit('show-blog-modal')"
        class="px-6 py-3 bg-purple-500/30 hover:bg-purple-500/50 text-purple-300 font-medium rounded-lg transition-colors border border-purple-400/30"
      >
        Share Your First Insight
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

// Props
const props = defineProps({
  blogPosts: {
    type: Array,
    default: () => []
  }
})

// Emits
defineEmits([
  'view-post',
  'edit-post',
  'show-blog-modal'
])

// Local state
const insightsViewMode = ref('cards')
const insightsFilter = ref('all')
const insightsSearch = ref('')
const insightsCurrentPage = ref(1)
const insightsPerPage = ref(9)

// Computed properties
const filteredInsights = computed(() => {
  let filtered = props.blogPosts

  // Filter by category
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

const categoryOptions = computed(() => {
  const posts = props.blogPosts
  return [
    { value: 'all', label: 'All Categories', count: posts.length },
    { value: 'analysis', label: 'Market Analysis', count: posts.filter(p => p.category === 'analysis').length },
    { value: 'strategy', label: 'Investment Strategy', count: posts.filter(p => p.category === 'strategy').length },
    { value: 'insights', label: 'Company Insights', count: posts.filter(p => p.category === 'insights').length },
    { value: 'quotes', label: 'Investment Quotes', count: posts.filter(p => p.category === 'quotes').length },
    { value: 'news', label: 'Market News', count: posts.filter(p => p.category === 'news').length }
  ]
})

// Methods
const canEditPost = (post) => {
  const user = usePage().props.auth.user
  return user && post.user_id === user.id
}

const getExcerpt = (content) => {
  if (!content) return ''
  const stripped = content.replace(/<[^>]*>/g, '')
  return stripped.length > 150 ? stripped.substring(0, 150) + '...' : stripped
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>