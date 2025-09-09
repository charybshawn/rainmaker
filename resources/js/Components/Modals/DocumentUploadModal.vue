<template>
  <div v-show="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-sm transition-all duration-200">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-8 py-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">📎 Upload Document</h2>
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
          <label for="upload_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title</label>
          <input 
            id="upload_title"
            v-model="form.title"
            type="text" 
            required
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
            placeholder="e.g., Q3 2024 Financial Report"
          />
          <div v-if="errors.title" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.title }}</div>
        </div>

        <!-- Description -->
        <div>
          <label for="upload_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (optional)</label>
          <textarea 
            id="upload_description"
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors resize-none"
            placeholder="Brief description of the document(s)..."
          ></textarea>
          <div v-if="errors.description" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.description }}</div>
        </div>

        <!-- File Upload Area -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Documents</label>
          <div class="border-2 border-dashed border-green-300 dark:border-green-600 rounded-lg p-8 text-center hover:border-green-400 dark:hover:border-green-500 transition-colors">
            <svg class="mx-auto h-16 w-16 text-green-400 dark:text-green-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="mt-4">
              <label for="document-upload" class="cursor-pointer">
                <span class="mt-2 block text-lg font-medium text-gray-900 dark:text-white">
                  Click to upload documents or drag and drop
                </span>
                <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">
                  PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)
                </span>
              </label>
              <input 
                id="document-upload" 
                name="document-upload" 
                type="file" 
                multiple 
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
                @change="$emit('file-upload', $event)"
                class="sr-only" 
              />
            </div>
          </div>
          
          <!-- File List -->
          <div v-if="form.files.length > 0" class="mt-6 space-y-3">
            <div v-for="(file, index) in form.files" :key="index" class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
              <div class="flex items-center">
                <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <div>
                  <span class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</span>
                  <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ formatFileSize(file.size) }}</span>
                </div>
              </div>
              <button 
                type="button" 
                @click="$emit('remove-file', index)"
                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors p-1"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="errors.files" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.files }}</div>
        </div>

        <!-- Category and Visibility -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="upload_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <select 
              id="upload_category"
              v-model="form.category_id"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
            >
              <option value="">Select category (optional)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <div>
            <label for="upload_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
            <select 
              id="upload_visibility"
              v-model="form.visibility"
              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
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
            :disabled="uploading || form.files.length === 0"
            class="flex-1 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 disabled:bg-green-400 text-white font-medium py-2 px-4 rounded-md transition-all duration-200 shadow-sm"
          >
            {{ uploading ? 'Uploading...' : 'Upload Documents' }}
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
  uploading: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  },
  formatFileSize: {
    type: Function,
    required: true
  }
})

defineEmits(['close', 'save', 'file-upload', 'remove-file'])
</script>