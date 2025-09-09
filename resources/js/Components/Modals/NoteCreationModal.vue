<template>
  <div v-show="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-sm transition-all duration-200">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-8 py-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">📝 Add Note</h2>
            <p class="text-lg text-gray-600 dark:text-gray-300 mt-1" v-if="selectedCompany">
              for {{ selectedCompany.name }} ({{ selectedCompany.ticker }})
            </p>
          </div>
          <button 
            @click="$emit('close')"
            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors"
          >
            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <form @submit.prevent="$emit('save')" class="p-8 space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
          {{ errors.general }}
        </div>

        <!-- Title -->
        <div>
          <label for="note_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Title</label>
          <input 
            id="note_title"
            v-model="form.title"
            type="text" 
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            placeholder="e.g., Q3 2024 Earnings Analysis"
          />
          <div v-if="errors.title" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.title }}</div>
        </div>

        <!-- Content -->
        <div>
          <label for="note_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Content</label>
          <textarea 
            id="note_content"
            v-model="form.content"
            rows="12"
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors resize-none"
            placeholder="Enter your analysis, thoughts, observations, and research notes..."
          ></textarea>
          <div v-if="errors.content" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.content }}</div>
        </div>

        <!-- Category and Visibility -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="note_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <select 
              id="note_category"
              v-model="form.category_id"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            >
              <option value="">Select category (optional)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <div>
            <label for="note_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
            <select 
              id="note_visibility"
              v-model="form.visibility"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
            >
              <option value="private">Private (Only me)</option>
              <option value="team">Team</option>
              <option value="public">Public</option>
            </select>
            <div v-if="errors.visibility" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.visibility }}</div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="flex gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
          <button 
            type="button"
            @click="$emit('close')"
            class="flex-1 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-md transition-all duration-200"
          >
            Cancel
          </button>
          <button 
            type="submit"
            :disabled="creatingNote"
            class="flex-1 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 disabled:bg-blue-400 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 shadow-sm"
          >
            {{ creatingNote ? 'Saving...' : 'Save Note' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  selectedCompany: {
    type: Object,
    default: null
  },
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  creatingNote: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close', 'save'])
</script>