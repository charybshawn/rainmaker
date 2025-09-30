<template>
  <div class="bg-gray-800/30 backdrop-blur-sm rounded-xl border border-gray-700/50 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold text-white flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Recent Activity
      </h3>
      <button
        @click="$emit('view-all')"
        class="text-blue-400 hover:text-blue-300 text-sm transition-colors"
      >
        View All
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-400"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="activities.length === 0" class="text-center py-8 text-gray-400">
      <div class="text-sm">No recent activity</div>
    </div>

    <!-- Activity List -->
    <div v-else class="space-y-3">
      <div
        v-for="activity in activities"
        :key="activity.id"
        class="flex items-start space-x-3 p-3 bg-gray-800/50 rounded-lg hover:bg-gray-700/50 transition-colors"
      >
        <!-- Activity Icon -->
        <div class="flex-shrink-0">
          <div
            :class="getIconClasses(activity)"
            class="w-6 h-6 rounded-full flex items-center justify-center"
          >
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getIconPath(activity)"></path>
            </svg>
          </div>
        </div>

        <!-- Activity Content -->
        <div class="flex-1 min-w-0">
          <!-- Description -->
          <div class="text-sm text-white mb-1 line-clamp-2">
            {{ activity.description }}
          </div>

          <!-- Metadata -->
          <div class="flex items-center space-x-2 text-xs text-gray-400">
            <span v-if="activity.user">{{ activity.user.name }}</span>
            <span>•</span>
            <span>{{ activity.created_at_human }}</span>
            <span v-if="activity.trackable && showModelInfo">•</span>
            <span v-if="activity.trackable && showModelInfo" class="text-blue-400">
              {{ activity.trackable.title }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Footer (if enabled) -->
    <div v-if="showStats && stats" class="mt-4 pt-4 border-t border-gray-700/50">
      <div class="grid grid-cols-3 gap-4 text-center">
        <div>
          <div class="text-lg font-semibold text-white">{{ stats.total_activities }}</div>
          <div class="text-xs text-gray-400">Total</div>
        </div>
        <div>
          <div class="text-lg font-semibold text-green-400">{{ stats.activities_by_type?.created || 0 }}</div>
          <div class="text-xs text-gray-400">Created</div>
        </div>
        <div>
          <div class="text-lg font-semibold text-blue-400">{{ stats.activities_by_type?.updated || 0 }}</div>
          <div class="text-xs text-gray-400">Updated</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
  limit: {
    type: Number,
    default: 5
  },
  days: {
    type: Number,
    default: 7
  },
  showStats: {
    type: Boolean,
    default: false
  },
  showModelInfo: {
    type: Boolean,
    default: true
  },
  modelType: String,
  modelId: [String, Number],
  userId: [String, Number]
})

const emit = defineEmits(['view-all', 'auth-required'])

// Reactive data
const activities = ref([])
const stats = ref(null)
const loading = ref(false)

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
  'clock': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
}

// Methods
const fetchActivities = async () => {
  if (!isAuthenticated.value) return

  loading.value = true

  try {
    let url = '/api/activities'

    // Build URL based on props
    if (props.modelType && props.modelId) {
      url = `/api/activities/${props.modelType}/${props.modelId}`
    } else if (props.userId) {
      url = `/api/activities/users/${props.userId}`
    }

    const params = {
      limit: props.limit,
      from_date: new Date(Date.now() - props.days * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
    }

    const response = await axios.get(url, { params })
    activities.value = response.data.data

    // Fetch stats if enabled
    if (props.showStats) {
      const statsResponse = await axios.get('/api/activities/stats', {
        params: { days: props.days }
      })
      stats.value = statsResponse.data
    }

  } catch (error) {
    console.error('Error fetching activities:', error)
  } finally {
    loading.value = false
  }
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

// Lifecycle
onMounted(() => {
  // Only fetch if authenticated
  if (isAuthenticated.value) {
    fetchActivities()
  }
})

// Expose refresh method for parent components
defineExpose({
  refresh: fetchActivities
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>