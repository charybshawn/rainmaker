<template>
  <div>
    <!-- Latest Additions Widgets -->
    <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Latest Companies -->
      <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            Latest Companies
          </h3>
          <button
            @click="$emit('tab-switch', 'companies')"
            class="text-blue-400 hover:text-blue-300 text-sm transition-colors"
          >
            View All
          </button>
        </div>

        <div v-if="latestCompanies.length > 0" class="space-y-3">
          <div
            v-for="company in latestCompanies"
            :key="company.id"
            class="flex items-center justify-between p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors cursor-pointer"
            @click="$emit('company-selected', company)"
          >
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-2">
                <p class="text-white font-medium truncate">{{ company.name }}</p>
                <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded font-medium">{{ company.ticker }}</span>
              </div>
              <p class="text-sm text-gray-400 truncate">{{ company.sector }}</p>
            </div>
            <div class="text-xs text-gray-500">
              {{ formatTimeAgo(company.created_at) }}
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
          </svg>
          <p class="text-sm">No companies yet</p>
        </div>
      </div>

      <!-- Latest Documents -->
      <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Latest Research Assets
          </h3>
          <button
            @click="$emit('tab-switch', 'companies')"
            class="text-green-400 hover:text-green-300 text-sm transition-colors"
          >
            View All
          </button>
        </div>

        <div v-if="latestDocuments.length > 0" class="space-y-3">
          <div
            v-for="doc in latestDocuments"
            :key="doc.id"
            class="flex items-start justify-between p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors cursor-pointer"
            @click="$emit('document-selected', doc)"
          >
            <div class="flex-1 min-w-0">
              <p class="text-white font-medium line-clamp-2 mb-1">{{ doc.title || doc.file_name }}</p>
              <div class="flex items-center space-x-2 text-xs text-gray-400">
                <span v-if="doc.user">{{ doc.user.name }}</span>
                <span v-if="doc.source_type">•</span>
                <span>{{ formatFileSize(doc.size || 0) }}</span>
                <span>•</span>
                <span>{{ formatTimeAgo(doc.created_at) }}</span>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <span v-if="doc.is_orphaned" class="px-2 py-1 bg-red-500/20 text-red-300 text-xs rounded">Orphaned</span>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="text-sm">No documents yet</p>
        </div>
      </div>

      <!-- Latest Research -->
      <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-white flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Latest Research
          </h3>
          <button
            @click="$emit('tab-switch', 'research')"
            class="text-orange-400 hover:text-orange-300 text-sm transition-colors"
          >
            View All
          </button>
        </div>

        <div v-if="latestResearch.length > 0" class="space-y-3">
          <div
            v-for="research in latestResearch"
            :key="research.id"
            class="flex items-start justify-between p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors cursor-pointer"
            @click="$emit('research-selected', research)"
          >
            <div class="flex-1 min-w-0">
              <p class="text-white font-medium line-clamp-2 mb-1">{{ research.title }}</p>
              <div class="flex items-center space-x-2 text-xs text-gray-400">
                <span v-if="research.company">{{ research.company.name }}</span>
                <span v-if="research.company">•</span>
                <span>{{ research.user?.name }}</span>
                <span>•</span>
                <span>{{ formatTimeAgo(research.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-400">
          <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <p class="text-sm">No research yet</p>
        </div>
      </div>
    </div>

    <!-- Recent Activity Widget -->
    <div class="mt-8">
      <ActivityWidget
        :show-stats="true"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import ActivityWidget from '@/Components/ActivityWidget.vue'
import axios from 'axios'

// Props
const props = defineProps({
  isAuthenticated: {
    type: Boolean,
    required: true
  },
  user: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits([
  'company-selected',
  'research-selected',
  'document-selected',
  'tab-switch',
  'widget-action'
])

// Widget data state
const latestCompanies = ref([])
const latestResearch = ref([])
const latestDocuments = ref([])
const latestInsights = ref([])
const totalDocumentsCount = ref(0)

// Loading states (optional for future use)
const isLoadingCompanies = ref(false)
const isLoadingResearch = ref(false)
const isLoadingDocuments = ref(false)
const isLoadingInsights = ref(false)

// Utility functions
const formatTimeAgo = (dateString) => {
  if (!dateString) return ''

  const date = new Date(dateString)
  const now = new Date()
  const diffInSeconds = Math.floor((now - date) / 1000)

  if (diffInSeconds < 60) return 'just now'
  if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m ago`
  if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h ago`
  if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d ago`

  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: date.getFullYear() !== now.getFullYear() ? 'numeric' : undefined
  })
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Widget data fetching functions
const fetchLatestCompanies = async () => {
  if (!props.isAuthenticated) return

  isLoadingCompanies.value = true
  try {
    const response = await axios.get('/api/activities/latest/companies?limit=5')
    latestCompanies.value = response.data || []
  } catch (error) {
    console.error('Error fetching latest companies:', error)
  } finally {
    isLoadingCompanies.value = false
  }
}

const fetchLatestResearch = async () => {
  if (!props.isAuthenticated) return

  isLoadingResearch.value = true
  try {
    const response = await axios.get('/api/activities/latest/research?limit=5')
    latestResearch.value = response.data || []
  } catch (error) {
    console.error('Error fetching latest research:', error)
  } finally {
    isLoadingResearch.value = false
  }
}

// Insights widget has been removed from dashboard
// const fetchLatestInsights = async () => {
//   if (!props.isAuthenticated) return
//
//   isLoadingInsights.value = true
//   try {
//     const response = await axios.get('/api/activities/latest/insights?limit=5')
//     latestInsights.value = response.data || []
//   } catch (error) {
//     console.error('Error fetching latest insights:', error)
//   } finally {
//     isLoadingInsights.value = false
//   }
// }

const fetchLatestDocuments = async () => {
  if (!props.isAuthenticated) return

  isLoadingDocuments.value = true
  try {
    const response = await axios.get('/api/documents?limit=5&sort=created_at&order=desc')
    latestDocuments.value = response.data.data || response.data || []

    // Also fetch total count for the widget
    const countResponse = await axios.get('/api/documents?limit=1')
    if (countResponse.data.pagination) {
      totalDocumentsCount.value = countResponse.data.pagination.total_items || 0
    }
  } catch (error) {
    console.error('Error fetching latest documents:', error)
  } finally {
    isLoadingDocuments.value = false
  }
}

// Refresh all widget data
const refreshAllWidgets = () => {
  // Only fetch if user is still authenticated
  if (!props.isAuthenticated) {
    console.warn('User not authenticated - skipping widget refresh')
    return
  }

  fetchLatestCompanies()
  fetchLatestResearch()
  fetchLatestDocuments()
  // fetchLatestInsights() - removed from dashboard
}

// Initialize widget data on mount
onMounted(() => {
  // Only initialize if authenticated - don't make any API calls otherwise
  if (props.isAuthenticated && props.user) {
    refreshAllWidgets()

    // Setup polling for real-time updates (every 45 seconds)
    const pollingInterval = setInterval(() => {
      refreshAllWidgets()
    }, 45000) // 45 seconds

    // Store polling interval for cleanup
    window.__widgetPollingInterval = pollingInterval
  }
})

// Cleanup on unmount
onUnmounted(() => {
  if (window.__widgetPollingInterval) {
    clearInterval(window.__widgetPollingInterval)
    delete window.__widgetPollingInterval
  }
})

// Expose methods for parent component if needed
defineExpose({
  refreshAllWidgets,
  fetchLatestCompanies,
  fetchLatestResearch,
  fetchLatestDocuments
})
</script>