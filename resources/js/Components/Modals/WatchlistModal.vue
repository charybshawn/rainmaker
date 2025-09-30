<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto" @click.self="$emit('close')">
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
            <h3 class="text-lg font-semibold text-white flex items-center">
              <svg class="w-6 h-6 mr-3 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              {{ isEditing ? 'Edit Watchlist' : 'Create New Watchlist' }}
            </h3>
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

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
          <!-- Name Field -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
              Watchlist Name *
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              maxlength="255"
              placeholder="e.g., Tech Stocks, Dividend Growth, etc."
              class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)] focus:border-orange-400/50 focus:ring-2 focus:ring-orange-400/20 transition-all duration-200"
              style="backdrop-filter: blur(20px) saturate(150%);"
            />
            <div class="mt-1 text-right">
              <span class="text-xs text-gray-500">{{ form.name.length }}/255</span>
            </div>
          </div>

          <!-- Description Field -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">
              Description
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              maxlength="1000"
              placeholder="Optional description of this watchlist's purpose or criteria..."
              class="w-full px-4 py-3 rounded-xl bg-white/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(249,115,22,0.2)] focus:border-orange-400/50 focus:ring-2 focus:ring-orange-400/20 transition-all duration-200 resize-none"
              style="backdrop-filter: blur(20px) saturate(150%);"
            ></textarea>
            <div class="mt-1 text-right">
              <span class="text-xs text-gray-500">{{ (form.description || '').length }}/1000</span>
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="p-4 rounded-xl bg-red-500/20 border border-red-500/50">
            <p class="text-red-300 text-sm">{{ error }}</p>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 pt-4">
            <button
              type="button"
              @click="$emit('close')"
              class="px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium transition-all duration-200 border border-white/20"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="!form.name.trim() || loading"
              class="px-6 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white font-medium shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <svg v-if="loading" class="animate-spin w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ isEditing ? 'Update Watchlist' : 'Create Watchlist' }}
            </button>
          </div>
        </form>
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
  watchlist: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

// Reactive state
const form = ref({
  name: '',
  description: ''
})
const loading = ref(false)
const error = ref('')

// Computed properties
const isEditing = computed(() => !!props.watchlist)

// Methods
const resetForm = () => {
  form.value = {
    name: '',
    description: ''
  }
}

// Watch for prop changes to populate form
watch(() => props.watchlist, (newWatchlist) => {
  if (newWatchlist) {
    form.value = {
      name: newWatchlist.name || '',
      description: newWatchlist.description || ''
    }
  } else {
    resetForm()
  }
}, { immediate: true })

// Watch for modal show changes to reset form
watch(() => props.show, (show) => {
  if (show) {
    error.value = ''
    if (!props.watchlist) {
      resetForm()
    }
  }
})

const handleSubmit = async () => {
  if (!form.value.name.trim()) {
    error.value = 'Watchlist name is required'
    return
  }

  try {
    loading.value = true
    error.value = ''

    const data = {
      name: form.value.name.trim(),
      description: form.value.description?.trim() || null
    }

    let response
    if (isEditing.value) {
      response = await axios.put(`/api/watchlists/${props.watchlist.id}`, data)
    } else {
      response = await axios.post('/api/watchlists', data)
    }

    emit('saved', response.data.watchlist)
  } catch (err) {
    console.error('Error saving watchlist:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else if (err.response?.data?.errors) {
      const errors = Object.values(err.response.data.errors).flat()
      error.value = errors.join(', ')
    } else {
      error.value = 'An error occurred while saving the watchlist. Please try again.'
    }
  } finally {
    loading.value = false
  }
}
</script>