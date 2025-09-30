<template>
  <div class="activity-timeline">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-white">Activity History</h3>
      <div class="flex items-center space-x-2">
        <!-- Activity Type Filter -->
        <select
          v-model="filters.type"
          @change="fetchActivities"
          class="bg-gray-800/50 border border-gray-600 rounded-lg px-3 py-1 text-sm text-white focus:border-blue-500 focus:outline-none"
        >
          <option value="">All Types</option>
          <option value="created">Created</option>
          <option value="updated">Updated</option>
          <option value="deleted">Deleted</option>
          <option value="published">Published</option>
          <option value="archived">Archived</option>
        </select>

        <!-- Days Filter -->
        <select
          v-model="filters.days"
          @change="fetchActivities"
          class="bg-gray-800/50 border border-gray-600 rounded-lg px-3 py-1 text-sm text-white focus:border-blue-500 focus:outline-none"
        >
          <option value="7">Last 7 days</option>
          <option value="30">Last 30 days</option>
          <option value="90">Last 90 days</option>
          <option value="">All time</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="activities.length === 0" class="text-center py-8 text-gray-400">
      <div class="text-lg mb-2">No activity found</div>
      <div class="text-sm">No activities match your current filters.</div>
    </div>

    <!-- Activity Timeline -->
    <div v-else class="space-y-4">
      <div
        v-for="activity in activities"
        :key="activity.id"
        class="relative flex items-start space-x-3 p-4 bg-gray-800/30 backdrop-blur-sm rounded-lg border border-gray-700/50 hover:border-gray-600/50 transition-colors"
      >
        <!-- Activity Icon -->
        <div class="flex-shrink-0">
          <div
            :class="getIconClasses(activity)"
            class="w-8 h-8 rounded-full flex items-center justify-center"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(activity)"></path>
            </svg>
          </div>
        </div>

        <!-- Activity Content -->
        <div class="flex-1 min-w-0">
          <!-- Activity Description -->
          <div class="text-sm text-white mb-1">
            {{ activity.description }}
          </div>

          <!-- Activity Metadata -->
          <div class="flex items-center space-x-4 text-xs text-gray-400">
            <!-- User -->
            <span v-if="activity.user" class="flex items-center">
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPaths.user"></path>
              </svg>
              {{ activity.user.name }}
            </span>

            <!-- Time -->
            <span class="flex items-center">
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPaths.clock"></path>
              </svg>
              {{ activity.created_at_human }}
            </span>

            <!-- Model Link -->
            <span v-if="activity.trackable && showModelLinks" class="flex items-center">
              <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPaths.link"></path>
              </svg>
              <button
                @click="$emit('navigate-to-model', activity.trackable)"
                class="text-blue-400 hover:text-blue-300 underline"
              >
                {{ activity.trackable.title }}
              </button>
            </span>
          </div>

          <!-- Field Changes -->
          <div v-if="activity.old_value !== null || activity.new_value !== null" class="mt-2 text-xs">
            <div v-if="activity.old_value !== null" class="text-red-300">
              <span class="font-medium">From:</span> {{ formatValue(activity.old_value) }}
            </div>
            <div v-if="activity.new_value !== null" class="text-green-300">
              <span class="font-medium">To:</span> {{ formatValue(activity.new_value) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Load More Button -->
      <div v-if="hasMorePages" class="text-center pt-4">
        <button
          @click="loadMore"
          :disabled="loadingMore"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg text-sm transition-colors"
        >
          <div v-if="loadingMore" class="flex items-center">
            <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
            Loading...
          </div>
          <span v-else>Load More</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  modelType: String,
  modelId: [String, Number],
  userId: [String, Number],
  showModelLinks: {
    type: Boolean,
    default: true
  },
  limit: {
    type: Number,
    default: 20
  }
})

const emit = defineEmits(['navigate-to-model'])

// Reactive data
const activities = ref([])
const loading = ref(false)
const loadingMore = ref(false)
const currentPage = ref(1)
const hasMorePages = ref(false)

const filters = reactive({
  type: '',
  days: '7'
})

// Authentication check
const isAuthenticated = computed(() => {
  return !!usePage().props.auth?.user
})

// Icon path mapping
const iconPaths = {
  'plus-circle': 'M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
  'pencil': 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
  'trash': 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
  'arrow-path': 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
  'eye': 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
  'archive-box': 'M5 8a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V8zm9 4H9.5a.5.5 0 000 1H14a.5.5 0 000-1z',
  'paper-clip': 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13',
  'x-circle': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
  'tag': 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
  'minus-circle': 'M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z',
  'folder': 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z',
  'shield-check': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
  'arrow-right-circle': 'M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z',
  'information-circle': 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
  'user': 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
  'clock': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
  'link': 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1'
}

// Methods
const fetchActivities = async (page = 1) => {
  if (!isAuthenticated.value) {
    console.warn('User not authenticated - skipping activities fetch')
    return
  }

  if (page === 1) {
    loading.value = true
    activities.value = []
    currentPage.value = 1
  } else {
    loadingMore.value = true
  }

  try {
    let url = '/api/activities'

    // Build URL based on props
    if (props.modelType && props.modelId) {
      url = `/api/activities/${props.modelType}/${props.modelId}`
    } else if (props.userId) {
      url = `/api/activities/users/${props.userId}`
    }

    const params = {
      page,
      limit: props.limit,
      ...(filters.type && { type: filters.type }),
      ...(filters.days && { from_date: new Date(Date.now() - filters.days * 24 * 60 * 60 * 1000).toISOString().split('T')[0] })
    }

    const response = await axios.get(url, { params })

    if (page === 1) {
      activities.value = response.data.data
    } else {
      activities.value.push(...response.data.data)
    }

    hasMorePages.value = response.data.pagination.has_more_pages
    currentPage.value = response.data.pagination.current_page

  } catch (error) {
    console.error('Error fetching activities:', error)
  } finally {
    loading.value = false
    loadingMore.value = false
  }
}

const loadMore = () => {
  fetchActivities(currentPage.value + 1)
}

const getIconPath = (activity) => {
  return iconPaths[activity.icon] || iconPaths['information-circle']
}

const getIconClasses = (activity) => {
  const colorMap = {
    green: 'bg-green-500/20 text-green-400',
    blue: 'bg-blue-500/20 text-blue-400',
    red: 'bg-red-500/20 text-red-400',
    yellow: 'bg-yellow-500/20 text-yellow-400',
    purple: 'bg-purple-500/20 text-purple-400',
    gray: 'bg-gray-500/20 text-gray-400',
    indigo: 'bg-indigo-500/20 text-indigo-400',
    pink: 'bg-pink-500/20 text-pink-400',
    orange: 'bg-orange-500/20 text-orange-400',
    teal: 'bg-teal-500/20 text-teal-400',
    cyan: 'bg-cyan-500/20 text-cyan-400',
    lime: 'bg-lime-500/20 text-lime-400'
  }

  return colorMap[activity.color] || colorMap.gray
}

const formatValue = (value) => {
  if (value === null || value === undefined) return 'N/A'
  if (typeof value === 'object') return JSON.stringify(value)
  if (typeof value === 'string' && value.length > 100) {
    return value.substring(0, 100) + '...'
  }
  return value
}

// Lifecycle
onMounted(() => {
  // Only fetch if authenticated
  if (isAuthenticated.value) {
    fetchActivities()
  }
})
</script>

<style scoped>
.activity-timeline {
  max-height: 600px;
  overflow-y: auto;
}

.activity-timeline::-webkit-scrollbar {
  width: 6px;
}

.activity-timeline::-webkit-scrollbar-track {
  background: rgba(75, 85, 99, 0.3);
  border-radius: 3px;
}

.activity-timeline::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

.activity-timeline::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}
</style>