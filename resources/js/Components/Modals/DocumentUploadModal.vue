<template>
  <Teleport to="body">
    <div v-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
      <!-- Backdrop with Dark Mode Context -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-md dark:bg-black/80"></div>
      <!-- Modal Container with Dark Mode Context -->
      <div class="relative dark bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 w-[66%] max-h-[80vh] overflow-y-auto shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">

      <!-- Modal Header -->
      <div class="sticky top-0 bg-gray-900 border-b border-white/20 px-8 py-6 rounded-t-2xl z-10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-3xl font-semibold text-white">📄 Upload Document</h2>
            <p class="text-lg text-gray-300 mt-1" v-if="selectedCompany">
              for {{ selectedCompany.name }} ({{ selectedCompany.ticker }})
            </p>
          </div>
          <div class="flex items-center space-x-3">
          <!-- Save Button -->
          <button
            @click="$emit('save')"
            :disabled="uploading || !hasValidUpload"
            class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
            style="backdrop-filter: blur(20px) saturate(150%);"
            title="Upload Document"
          >
            <svg v-if="!uploading" class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ uploading ? 'Uploading...' : getUploadButtonText }}
          </button>
          <!-- Close Button -->
          <button
            @click="$emit('close')"
            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 flex items-center justify-center transition-all duration-300 hover:scale-105 backdrop-blur-xl"
            style="backdrop-filter: blur(20px) saturate(150%);"
            title="Close"
          >
            <svg class="w-5 h-5 text-white/70 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          </div>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-6 sm:p-8 space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-red-500/10 border border-red-400/30 text-red-200 px-4 py-3 rounded-xl backdrop-blur-xl">
          {{ errors.general }}
        </div>

        <!-- Title -->
        <div>
          <label for="document_title" class="block text-sm font-medium text-white mb-2">Document Title <span class="text-red-400">*</span></label>
          <input
            id="document_title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            placeholder="e.g., Q3 2024 Financial Report"
          />
          <div v-if="errors.title" class="text-red-400 text-sm mt-1">{{ errors.title }}</div>
        </div>

        <!-- Description -->
        <div>
          <label for="document_description" class="block text-sm font-medium text-white mb-2">Description</label>
          <textarea
            id="document_description"
            v-model="form.description"
            rows="3"
            class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300 resize-none"
            placeholder="Brief description of the document..."
          ></textarea>
          <div v-if="errors.description" class="text-red-400 text-sm mt-1">{{ errors.description }}</div>
        </div>

        <!-- Upload Method Selection -->
        <div>
          <label class="block text-sm font-medium text-white mb-3">Upload Method</label>
          <div class="flex space-x-2 mb-4">
            <button
              type="button"
              @click="form.uploadType = 'file'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                (!form.uploadType || form.uploadType === 'file')
                  ? 'bg-green-500/20 text-green-300 border border-green-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              📎 Direct Upload
            </button>
            <button
              type="button"
              @click="form.uploadType = 'url'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                form.uploadType === 'url'
                  ? 'bg-green-500/20 text-green-300 border border-green-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🔗 From URL
            </button>
            <button
              type="button"
              @click="form.uploadType = 'existing'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                form.uploadType === 'existing'
                  ? 'bg-green-500/20 text-green-300 border border-green-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              📁 Existing Files
            </button>
          </div>
        </div>

        <!-- File Upload Area -->
        <div v-if="!form.uploadType || form.uploadType === 'file'"
             class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-white/30 transition-colors backdrop-blur-xl bg-white/5">
          <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <div class="mt-4">
            <label for="document-upload" class="cursor-pointer">
              <span class="mt-2 block text-lg font-medium text-gray-300">
                Click to upload documents or drag and drop
              </span>
              <span class="mt-1 block text-sm text-gray-400">
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

          <!-- File List -->
          <div v-if="form.files.length > 0" class="mt-6 space-y-3">
            <div v-for="(file, index) in form.files" :key="index" class="flex items-center justify-between p-4 bg-green-500/10 border border-green-400/30 rounded-xl backdrop-blur-xl">
              <div class="flex items-center">
                <svg class="w-8 h-8 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <div>
                  <span class="text-sm font-medium text-white">{{ file.name }}</span>
                  <span class="text-xs text-gray-400 block">{{ formatFileSize(file.size) }}</span>
                </div>
              </div>
              <button
                type="button"
                @click="$emit('remove-file', index)"
                class="text-red-400 hover:text-red-300 transition-colors p-1"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="errors.files" class="text-red-400 text-sm mt-1">{{ errors.files }}</div>
        </div>

        <!-- URL Download Area -->
        <div v-if="form.uploadType === 'url'" class="space-y-4">
          <div class="flex items-center space-x-2">
            <input
              v-model="form.newUrl"
              type="url"
              placeholder="https://example.com/document.pdf"
              class="flex-1 px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            />
            <button
              type="button"
              @click="$emit('add-url')"
              :disabled="!form.newUrl"
              class="px-4 py-3 bg-green-500/20 hover:bg-green-500/30 disabled:bg-green-500/10 text-green-200 disabled:text-green-400/50 font-medium rounded-xl transition-all duration-300 border border-green-400/30"
            >
              Add URL
            </button>
          </div>

          <!-- Added URLs List -->
          <div v-if="form.urls && form.urls.length > 0" class="mt-4 space-y-2">
            <h4 class="text-sm font-medium text-white">URLs to Download:</h4>
            <div v-for="(url, index) in form.urls" :key="index"
                 class="flex items-center justify-between p-3 bg-green-500/10 border border-green-400/30 rounded-xl backdrop-blur-xl">
              <div class="flex items-center flex-1 min-w-0">
                <svg class="w-5 h-5 text-green-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                </svg>
                <p class="text-sm text-white truncate">{{ url }}</p>
              </div>
              <button
                type="button"
                @click="$emit('remove-url', index)"
                class="ml-2 text-red-400 hover:text-red-300 transition-colors p-1"
              >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Existing Files Selection Area -->
        <div v-if="form.uploadType === 'existing'" class="space-y-4">
          <div class="flex items-center space-x-2">
            <input
              v-model="existingFilesSearch"
              type="text"
              placeholder="Search existing files..."
              class="flex-1 px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            />
            <button
              type="button"
              @click="$emit('load-existing-files')"
              class="px-4 py-3 bg-green-500/20 hover:bg-green-500/30 text-green-200 font-medium rounded-xl transition-all duration-300 border border-green-400/30"
            >
              Load Files
            </button>
          </div>

          <!-- Existing Files List -->
          <div v-if="availableFiles && availableFiles.length > 0" class="max-h-48 overflow-y-auto space-y-2 border border-white/20 rounded-xl p-4 bg-white/5 backdrop-blur-xl">
            <div
              v-for="file in filteredExistingFiles"
              :key="file.id"
              @click="$emit('toggle-file-selection', file)"
              class="flex items-center space-x-3 p-3 rounded-lg cursor-pointer transition-all duration-300 hover:bg-white/10 border border-white/10"
              :class="{
                'bg-green-500/20 border-green-400/30': form.selectedExistingFiles?.some(f => f.id === file.id),
                'bg-white/5': !form.selectedExistingFiles?.some(f => f.id === file.id)
              }"
            >
              <div class="flex-shrink-0">
                <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a1 1 0 01.707.293l4 4A1 1 0 0119 7v9a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-white font-medium truncate">{{ file.name || file.original_name }}</p>
                <p class="text-sm text-gray-400">{{ formatFileSize(file.size) }}</p>
              </div>
              <div v-if="form.selectedExistingFiles?.some(f => f.id === file.id)"
                   class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>
          </div>

          <!-- Selected files count -->
          <div v-if="form.selectedExistingFiles?.length > 0" class="text-sm text-green-400">
            {{ form.selectedExistingFiles.length }} file(s) selected
          </div>
        </div>

        <!-- Category and Visibility -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="document_category" class="block text-sm font-medium text-white mb-2">Category</label>
            <select
              id="document_category"
              v-model="form.category_id"
              class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            >
              <option value="" class="bg-gray-800">Select category (optional)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id" class="bg-gray-800">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <div>
            <label for="document_visibility" class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              id="document_visibility"
              v-model="form.visibility"
              class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            >
              <option value="private" class="bg-gray-800">Private (Only me)</option>
              <option value="team" class="bg-gray-800">Team</option>
              <option value="public" class="bg-gray-800">Public</option>
            </select>
            <div v-if="errors.visibility" class="text-red-400 text-sm mt-1">{{ errors.visibility }}</div>
          </div>
        </div>

      </div>
    </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
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
  },
  availableFiles: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close', 'save', 'file-upload', 'remove-file', 'add-url', 'remove-url', 'load-existing-files', 'toggle-file-selection'])

// Local state for existing files search
const existingFilesSearch = ref('')

// Computed properties
const filteredExistingFiles = computed(() => {
  if (!props.availableFiles || !existingFilesSearch.value) {
    return props.availableFiles || []
  }

  const searchTerm = existingFilesSearch.value.toLowerCase()
  return props.availableFiles.filter(file =>
    (file.name || file.original_name || '').toLowerCase().includes(searchTerm)
  )
})

const hasValidUpload = computed(() => {
  const uploadType = props.form.uploadType || 'file'

  if (uploadType === 'file') {
    return props.form.files && props.form.files.length > 0
  } else if (uploadType === 'url') {
    return props.form.urls && props.form.urls.length > 0
  } else if (uploadType === 'existing') {
    return props.form.selectedExistingFiles && props.form.selectedExistingFiles.length > 0
  }

  return false
})

const getUploadButtonText = computed(() => {
  const uploadType = props.form.uploadType || 'file'

  if (uploadType === 'file') {
    return 'Upload Documents'
  } else if (uploadType === 'url') {
    return 'Download & Save'
  } else if (uploadType === 'existing') {
    return 'Link Documents'
  }

  return 'Save Documents'
})
</script>