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
            <h2 class="text-3xl font-semibold text-white">{{ props.isEditing ? '✏️ Edit Research' : '📝 Add Research' }}</h2>
            <p class="text-lg text-gray-300 mt-1" v-if="selectedCompany">
              for {{ selectedCompany.name }} ({{ selectedCompany.ticker_symbol }})
            </p>
          </div>
          <div class="flex items-center space-x-3">
          <!-- Save Button -->
          <button
            @click="$emit('save')"
            :disabled="creatingNote"
            class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
            style="backdrop-filter: blur(20px) saturate(150%);"
            :title="props.isEditing ? 'Update Research' : 'Save Research'"
          >
            <svg v-if="!creatingNote" class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ creatingNote ? (props.isEditing ? 'Updating...' : 'Saving...') : (props.isEditing ? 'Update Research' : 'Save Research') }}
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
      <div class="px-8 py-6">

      <form class="space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent backdrop-blur-xl border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4" style="backdrop-filter: blur(20px) saturate(180%);">
          {{ errors.general }}
        </div>

        <!-- Title -->
        <div>
          <label for="note_title" class="block text-sm font-medium text-white mb-2">Note Title</label>
          <input
            id="note_title"
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
            style="backdrop-filter: blur(20px) saturate(180%);"
            placeholder="e.g., Q3 2024 Earnings Analysis"
          />
          <div v-if="errors.title" class="text-red-400 text-sm mt-1">{{ errors.title }}</div>
        </div>

        <!-- Content -->
        <div>
          <label for="note_content" class="block text-sm font-medium text-white mb-2">Note Content</label>
          <textarea
            id="note_content"
            v-model="form.content"
            rows="12"
            required
            class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 resize-none"
            style="backdrop-filter: blur(20px) saturate(180%);"
            placeholder="Enter your analysis, thoughts, observations, and research notes..."
          ></textarea>
          <div v-if="errors.content" class="text-red-400 text-sm mt-1">{{ errors.content }}</div>
        </div>

        <!-- Category and Visibility -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label for="note_category" class="block text-sm font-medium text-white mb-2">Category</label>
            <select
              id="note_category"
              v-model="form.category_id"
              class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <option value="">Select category (optional)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <div>
            <label for="note_visibility" class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              id="note_visibility"
              v-model="form.visibility"
              class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <option value="private">Private (Only me)</option>
              <option value="team">Team</option>
              <option value="public">Public</option>
            </select>
            <div v-if="errors.visibility" class="text-red-400 text-sm mt-1">{{ errors.visibility }}</div>
          </div>
        </div>

        <!-- File Attachments Section -->
        <div class="space-y-4">
          <label class="block text-sm font-medium text-white mb-3">Attachments (optional)</label>

          <!-- Upload Type Toggle -->
          <div class="flex space-x-2 mb-4">
            <button
              type="button"
              @click="form.uploadType = 'file'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                (!form.uploadType || form.uploadType === 'file')
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              📁 Upload Files
            </button>
            <button
              type="button"
              @click="form.uploadType = 'url'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                form.uploadType === 'url'
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🔗 Download from URL
            </button>
            <button
              type="button"
              @click="form.uploadType = 'existing'; $emit('load-existing-files')"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                form.uploadType === 'existing'
                  ? 'bg-blue-500/20 text-blue-300 border border-blue-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🗂️ Select Existing Files
            </button>
          </div>

          <!-- File Upload Area -->
          <div v-if="!form.uploadType || form.uploadType === 'file'"
               class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-white/30 transition-colors backdrop-blur-xl bg-white/5">
            <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
              <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="mt-4">
              <label for="research-file-upload" class="cursor-pointer">
                <span class="mt-2 block text-sm font-medium text-white">
                  Click to upload files or drag and drop
                </span>
                <span class="mt-1 block text-xs text-gray-400">
                  PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)
                </span>
              </label>
              <input
                id="research-file-upload"
                name="research-file-upload"
                type="file"
                multiple
                accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
                @change="$emit('file-upload', $event)"
                class="sr-only"
              />
            </div>
          </div>

          <!-- URL Download Area -->
          <div v-if="form.uploadType === 'url'" class="space-y-4">
            <div class="flex items-center space-x-2">
              <input
                v-model="form.newUrl"
                type="url"
                placeholder="https://example.com/document.pdf"
                class="flex-1 px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              />
              <button
                type="button"
                @click="$emit('add-url', form.newUrl)"
                :disabled="!form.newUrl"
                class="px-4 py-3 bg-blue-500/20 text-blue-300 border border-blue-400/30 rounded-xl hover:bg-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 font-medium"
              >
                Add URL
              </button>
            </div>

            <!-- Added URLs List -->
            <div v-if="form.urls && form.urls.length > 0" class="mt-4 space-y-2">
              <h4 class="text-sm font-medium text-white">URLs to Download:</h4>
              <div v-for="(url, index) in form.urls" :key="index"
                   class="flex items-center justify-between p-3 bg-blue-500/10 border border-blue-400/30 rounded-xl backdrop-blur-xl">
                <div class="flex items-center flex-1 min-w-0">
                  <svg class="w-5 h-5 text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                  </svg>
                  <span class="text-blue-300 truncate">{{ url }}</span>
                </div>
                <button
                  type="button"
                  @click="$emit('remove-url', index)"
                  class="text-red-400 hover:text-red-300 p-1 transition-colors ml-2 flex-shrink-0"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
                @input="$emit('search-existing-files', $event.target.value)"
                type="text"
                placeholder="Search your existing files..."
                class="flex-1 px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              />
            </div>

            <!-- Loading existing files -->
            <div v-if="loadingExistingFiles" class="text-center py-8">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-400 mx-auto"></div>
              <p class="text-sm text-gray-400 mt-2">Loading your files...</p>
            </div>

            <!-- Existing files list -->
            <div v-else-if="availableFiles.length > 0"
                 class="max-h-64 overflow-y-auto border border-white/10 rounded-xl bg-white/5 backdrop-blur-xl">
              <table class="min-w-full">
                <thead class="border-b border-white/10">
                  <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                      <input
                        type="checkbox"
                        @change="toggleAllFiles($event)"
                        class="rounded bg-white/10 border-white/20 text-blue-400 focus:ring-blue-400/20"
                      />
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Size</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Source</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                  <tr
                    v-for="file in availableFiles"
                    :key="file.id"
                    @click="$emit('toggle-file-selection', file)"
                    class="cursor-pointer transition-all duration-300 hover:bg-white/10"
                    :class="{
                      'bg-blue-500/20': form.selectedExistingFiles?.some(f => f.id === file.id),
                      'bg-transparent': !form.selectedExistingFiles?.some(f => f.id === file.id)
                    }"
                  >
                    <td class="px-4 py-3">
                      <input
                        type="checkbox"
                        :checked="form.selectedExistingFiles?.some(f => f.id === file.id)"
                        @click.stop
                        @change="$emit('toggle-file-selection', file)"
                        class="rounded bg-white/10 border-white/20 text-blue-400 focus:ring-blue-400/20"
                      />
                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-white text-sm truncate">{{ file.name || file.original_name }}</span>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-400">{{ formatFileSize(file.size) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-400 capitalize">{{ file.source_type?.replace('_', ' ') }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- No files state -->
            <div v-else-if="!loadingExistingFiles" class="text-center py-8 text-gray-400">
              <svg class="mx-auto h-12 w-12 text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
              </svg>
              <p class="mt-2 text-sm">No existing files found</p>
              <p class="text-xs text-gray-500">Upload some files first to use this feature</p>
            </div>

            <!-- Selected files count -->
            <div v-if="form.selectedExistingFiles?.length > 0" class="text-sm text-blue-400">
              {{ form.selectedExistingFiles.length }} file(s) selected
            </div>
          </div>

          <!-- File List -->
          <div v-if="form.files && form.files.length > 0" class="mt-4 space-y-2">
            <div v-for="(file, index) in form.files" :key="index"
                 class="flex items-center justify-between p-3 bg-white/5 border border-white/10 rounded-xl backdrop-blur-xl">
              <div class="flex items-center">
                <svg class="w-5 h-5 text-gray-300 mr-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <div>
                  <p class="text-white font-medium">{{ file.name }}</p>
                  <p class="text-sm text-gray-400">{{ formatFileSize(file.size) }}</p>
                </div>
              </div>
              <button
                type="button"
                @click="$emit('remove-file', index)"
                class="text-red-400 hover:text-red-300 p-1 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="errors.files" class="text-red-400 text-sm mt-1">{{ errors.files }}</div>
        </div>

      </form>
      </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

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
  creatingNote: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  },
  availableFiles: {
    type: Array,
    default: () => []
  },
  loadingExistingFiles: {
    type: Boolean,
    default: false
  },
  isEditing: {
    type: Boolean,
    default: false
  }
})

defineEmits([
  'close',
  'save',
  'file-upload',
  'add-url',
  'remove-url',
  'remove-file',
  'search-existing-files',
  'load-existing-files',
  'toggle-file-selection'
])

// Reactive data
const existingFilesSearch = ref('')

// Utility functions
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const toggleAllFiles = (event) => {
  if (event.target.checked) {
    // Select all files
    props.form.selectedExistingFiles = [...props.availableFiles]
  } else {
    // Deselect all files
    props.form.selectedExistingFiles = []
  }
}
</script>