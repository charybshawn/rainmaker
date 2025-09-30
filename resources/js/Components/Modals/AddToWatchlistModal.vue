<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click="$emit('close')">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
      <!-- Background overlay -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

      <!-- Modal panel -->
      <div
        class="relative inline-block align-bottom bg-gradient-to-br from-gray-900/95 via-gray-800/95 to-gray-900/95 backdrop-blur-xl rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20"
        style="backdrop-filter: blur(20px) saturate(150%);"
        @click.stop
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-700/50">
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <svg class="w-6 h-6 mr-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              <div>
                <h3 class="text-lg font-semibold text-white">Add to Watchlist</h3>
                <p class="text-sm text-gray-400 mt-1">
                  {{ company?.name }} ({{ company?.ticker }})
                </p>
              </div>
            </div>
            <button
              @click="$emit('close')"
              class="p-2 rounded-lg bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white transition-all duration-200"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loadingWatchlists" class="p-6">
          <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-400"></div>
          </div>
        </div>

        <!-- Content -->
        <div v-else class="p-6">
          <!-- Existing Watchlists -->
          <div v-if="watchlists.length > 0" class="mb-6">
            <h4 class="text-sm font-medium text-gray-300 mb-4">Select existing watchlists:</h4>
            <div class="space-y-3 max-h-60 overflow-y-auto">
              <label
                v-for="watchlist in watchlists"
                :key="watchlist.id"
                class="flex items-center p-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 cursor-pointer transition-all duration-200"
              >
                <input
                  v-model="selectedWatchlistIds"
                  :value="watchlist.id"
                  type="checkbox"
                  :disabled="watchlist.has_company"
                  class="w-4 h-4 text-orange-500 bg-transparent border-2 border-gray-400 rounded focus:ring-orange-500 focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed"
                />
                <div class="ml-3 flex-1">
                  <div class="flex items-center justify-between">
                    <span class="text-white font-medium">{{ watchlist.name }}</span>
                    <div class="flex items-center gap-2">
                      <span v-if="watchlist.has_company" class="text-xs text-green-400 bg-green-500/20 px-2 py-1 rounded-full">
                        Already added
                      </span>
                      <span class="text-xs text-gray-400 bg-gray-500/20 px-2 py-1 rounded-full">
                        {{ watchlist.companies_count }} {{ watchlist.companies_count === 1 ? 'company' : 'companies' }}
                      </span>
                    </div>
                  </div>
                  <p v-if="watchlist.description" class="text-sm text-gray-400 mt-1 truncate">
                    {{ watchlist.description }}
                  </p>
                </div>
              </label>
            </div>
          </div>

          <!-- No Existing Watchlists -->
          <div v-else class="mb-6 text-center py-8">
            <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
            </svg>
            <p class="text-gray-400 text-sm">You don't have any watchlists yet.</p>
            <p class="text-gray-500 text-xs mt-1">Create one below to get started.</p>
          </div>

          <!-- Create New Watchlist Section -->
          <div class="border-t border-gray-700/50 pt-6">
            <h4 class="text-sm font-medium text-gray-300 mb-4">Or create a new watchlist:</h4>
            <div class="space-y-4">
              <div>
                <input
                  v-model="newWatchlist.name"
                  type="text"
                  placeholder="Watchlist name"
                  maxlength="255"
                  class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)] focus:border-orange-400/50 focus:ring-2 focus:ring-orange-400/20 transition-all duration-200"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                />
              </div>
              <div>
                <textarea
                  v-model="newWatchlist.description"
                  placeholder="Optional description"
                  rows="2"
                  maxlength="1000"
                  class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)] focus:border-orange-400/50 focus:ring-2 focus:ring-orange-400/20 transition-all duration-200 resize-none"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mt-4 p-4 rounded-xl bg-red-500/20 border border-red-500/50">
            <p class="text-red-300 text-sm">{{ error }}</p>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="mt-4 p-4 rounded-xl bg-green-500/20 border border-green-500/50">
            <p class="text-green-300 text-sm">{{ successMessage }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-6">
            <button
              type="button"
              @click="$emit('close')"
              class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium transition-all duration-200 border border-white/20"
            >
              Cancel
            </button>
            <button
              @click="handleSubmit"
              :disabled="!canSubmit || loading"
              class="px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add to Watchlist{{ selectedWatchlistIds.length > 1 ? 's' : '' }}
            </button>
          </div>
        </div>
      </div>
    </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  company: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'added'])

// Reactive state
const watchlists = ref([])
const selectedWatchlistIds = ref([])
const newWatchlist = ref({
  name: '',
  description: ''
})
const loadingWatchlists = ref(false)
const loading = ref(false)
const error = ref('')
const successMessage = ref('')

// Computed properties
const canSubmit = computed(() => {
  return selectedWatchlistIds.value.length > 0 || newWatchlist.value.name.trim()
})

// Watch for modal show changes
watch(() => props.show, (show) => {
  if (show) {
    fetchWatchlists()
    resetForm()
  }
})

// Methods
const resetForm = () => {
  selectedWatchlistIds.value = []
  newWatchlist.value = {
    name: '',
    description: ''
  }
  error.value = ''
  successMessage.value = ''
}

const fetchWatchlists = async () => {
  if (!props.company?.id) return

  try {
    loadingWatchlists.value = true
    const response = await axios.get('/api/watchlists')

    // Check which watchlists already contain this company
    watchlists.value = response.data.watchlists.map(watchlist => {
      const hasCompany = watchlist.companies?.some(company => company.id === props.company.id) || false
      return {
        ...watchlist,
        has_company: hasCompany
      }
    })
  } catch (err) {
    console.error('Error fetching watchlists:', err)
    error.value = 'Failed to load watchlists. Please try again.'
  } finally {
    loadingWatchlists.value = false
  }
}

const handleSubmit = async () => {
  if (!canSubmit.value) return

  try {
    loading.value = true
    error.value = ''
    successMessage.value = ''

    const data = {
      company_id: props.company.id,
      watchlist_ids: selectedWatchlistIds.value,
      new_watchlist: newWatchlist.value.name.trim() ? {
        name: newWatchlist.value.name.trim(),
        description: newWatchlist.value.description?.trim() || null
      } : null
    }

    const response = await axios.post('/api/watchlists/add-company', data)

    successMessage.value = response.data.message
    emit('added', {
      company: props.company,
      addedTo: response.data.added_to,
      alreadyIn: response.data.already_in
    })

    // Close modal after a short delay to show success message
    setTimeout(() => {
      emit('close')
    }, 1500)

  } catch (err) {
    console.error('Error adding company to watchlists:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    } else {
      error.value = 'An error occurred while adding the company to watchlists. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>