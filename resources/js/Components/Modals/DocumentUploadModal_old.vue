<template>
  <Teleport to="body">
    <div v-show="show" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click.self="emit('close')">
      <!-- Backdrop with Dark Mode Context -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-md dark:bg-black/80"></div>
      <!-- Modal Container with Dark Mode Context -->
      <div class="relative dark bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 w-[60%] max-w-2xl max-h-[85vh] overflow-y-auto shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">

      <!-- Modal Header -->
      <div class="sticky top-0 bg-gray-900 border-b border-white/20 px-6 py-4 rounded-t-2xl z-10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-white">📄 Create Research Asset</h2>
            <p class="text-sm text-gray-300 mt-1" v-if="selectedCompany">
              for {{ selectedCompany.name }} ({{ selectedCompany.ticker }})
            </p>
          </div>
          <button
            @click="emit('close')"
            class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 flex items-center justify-center transition-all duration-300 backdrop-blur-xl"
            title="Close"
          >
            <svg class="w-4 h-4 text-white/70 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="p-6 space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-red-500/10 border border-red-400/30 text-red-200 px-4 py-3 rounded-xl backdrop-blur-xl">
          {{ errors.general }}
        </div>

        <!-- Upload Method Selection -->
        <div>
          <label class="block text-sm font-medium text-white mb-3">Create Research Asset</label>
          <div class="flex space-x-2 mb-4">
            <button
              type="button"
              @click="selectDirectUpload"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                (!uploadMethod || uploadMethod === 'file')
                  ? 'bg-green-500/20 text-green-300 border border-green-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              📎 Upload Files
            </button>
            <button
              type="button"
              @click="uploadMethod = 'existing'"
              :class="[
                'px-4 py-2 text-sm rounded-lg font-medium transition-all duration-300',
                uploadMethod === 'existing'
                  ? 'bg-green-500/20 text-green-300 border border-green-400/30'
                  : 'bg-white/5 text-gray-300 border border-white/10 hover:bg-white/10'
              ]"
            >
              🔗 Link Existing
            </button>
          </div>
        </div>

        <!-- File Upload Area -->
        <div v-if="!uploadMethod || uploadMethod === 'file'"
             class="border-2 border-dashed border-white/20 rounded-xl p-6 text-center hover:border-white/30 transition-colors backdrop-blur-xl bg-white/5">
          <svg class="mx-auto h-12 w-12 text-gray-300" stroke="currentColor" fill="none" viewBox="0 0 48 48">
            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <div class="mt-4">
            <label for="document-upload" class="cursor-pointer">
              <span class="mt-2 block text-lg font-medium text-gray-300">
                Drop research files here or click to upload
              </span>
              <span class="mt-1 block text-sm text-gray-400">
                PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)<br>
                <span class="text-green-400">Files will be stored as reusable research assets</span>
              </span>
            </label>
            <input
              id="document-upload"
              name="document-upload"
              type="file"
              multiple
              accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
              @change="handleFileSelect"
              class="sr-only"
            />
          </div>
        </div>

        <!-- Existing Assets Selection -->
        <div v-if="uploadMethod === 'existing'" class="space-y-4">
          <div class="bg-white/5 border border-white/20 rounded-xl p-4 backdrop-blur-xl">
            <h4 class="text-sm font-medium text-white mb-3">Select Existing Research Assets</h4>
            <div v-if="availableAssets.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
              <div v-for="asset in availableAssets" :key="asset.id"
                   @click="toggleAssetSelection(asset)"
                   :class="[
                     'p-3 rounded-lg cursor-pointer transition-all duration-200',
                     selectedAssets.some(a => a.id === asset.id)
                       ? 'bg-green-500/20 border border-green-400/30'
                       : 'bg-white/5 border border-white/10 hover:bg-white/10'
                   ]">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-white">{{ asset.title }}</p>
                    <p class="text-xs text-gray-400">{{ asset.file_name }} • {{ formatFileSize(asset.size) }}</p>
                  </div>
                  <div v-if="selectedAssets.some(a => a.id === asset.id)"
                       class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-4 text-gray-400">
              <p class="text-sm">No existing assets found for this company.</p>
            </div>
          </div>
        </div>

        <!-- Background Upload Progress -->
        <div v-if="uploadProgress.length > 0" class="space-y-3">
          <h4 class="text-sm font-medium text-white">Uploading Files:</h4>
          <div v-for="upload in uploadProgress" :key="upload.token"
               class="p-4 bg-white/5 border border-white/20 rounded-xl backdrop-blur-xl">
            <div class="flex items-center justify-between mb-2">
              <div class="flex items-center">
                <svg v-if="upload.status === 'uploading'" class="animate-spin w-5 h-5 text-blue-400 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else-if="upload.status === 'complete'" class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else-if="upload.status === 'error'" class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <div>
                  <p class="text-sm font-medium text-white">{{ upload.cleaned_title || upload.original_name }}</p>
                  <p class="text-xs text-gray-400">{{ upload.size || '' }}</p>
                </div>
              </div>
              <button v-if="upload.status === 'uploading'" @click="cancelUpload(upload.token)"
                      class="text-red-400 hover:text-red-300 text-xs">Cancel</button>
            </div>
            <div v-if="upload.status === 'uploading'" class="w-full bg-white/10 rounded-full h-2">
              <div class="bg-blue-400 h-2 rounded-full transition-all duration-300"
                   :style="`width: ${upload.progress || 0}%`"></div>
            </div>
            <p v-if="upload.status === 'error'" class="text-red-400 text-xs mt-1">{{ upload.error }}</p>
          </div>
        </div>

        <!-- Document Title (Auto-parsed from filename) -->
        <div v-if="completedUploads.length > 0">
          <label for="document_title" class="block text-sm font-medium text-white mb-2">Document Title</label>
          <input
            id="document_title"
            v-model="documentTitle"
            type="text"
            required
            class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            placeholder="Document title will auto-populate from filename"
          />
          <div v-if="errors.title" class="text-red-400 text-sm mt-1">{{ errors.title }}</div>
        </div>

        <!-- Category and Visibility -->
        <div v-if="completedUploads.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="document_category" class="block text-sm font-medium text-white mb-2">Category</label>
            <select
              id="document_category"
              v-model="form.category_id"
              class="w-full px-3 py-2 rounded-lg backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-1 focus:ring-white/25 transition-all duration-300"
            >
              <option value="" class="bg-gray-800">Select category (optional)</option>
              <option v-for="category in categories" :key="category.id" :value="category.id" class="bg-gray-800">
                {{ category.name }}
              </option>
            </select>
          </div>

          <div>
            <label for="document_visibility" class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              id="document_visibility"
              v-model="form.visibility"
              class="w-full px-3 py-2 rounded-lg backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-1 focus:ring-white/25 transition-all duration-300"
            >
              <option value="private" class="bg-gray-800">Private</option>
              <option value="team" class="bg-gray-800">Team</option>
              <option value="public" class="bg-gray-800">Public</option>
            </select>
          </div>
        </div>

        <!-- Save Button -->
        <div v-if="completedUploads.length > 0" class="flex justify-end pt-4">
          <button
            @click="saveDocument"
            :disabled="uploading"
            class="px-6 py-2 bg-gradient-to-r from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-lg shadow-lg hover:shadow-xl border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 hover:scale-105"
          >
            <svg v-if="!uploading" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ uploading ? 'Saving...' : 'Save Document' }}
          </button>
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

const emit = defineEmits(['close', 'save', 'file-upload', 'remove-file', 'document-saved'])

// Background upload state
const uploadProgress = ref([])
const uploading = ref(false)
const uploadMethod = ref('file')
const urlInput = ref('')
const documentTitle = ref('')

// Helper function to generate unique tokens
const generateToken = () => 'upload_' + Date.now() + '_' + Math.random().toString(36).substring(7)

// Helper function to clean filename like backend service
const cleanFilename = (filename) => {
  const name = filename.substring(0, filename.lastIndexOf('.')) || filename
  return name
    .replace(/[-_.]/g, ' ')
    .replace(/\s+/g, ' ')
    .trim()
    .split(' ')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(' ')
}

// Computed properties
const completedUploads = computed(() => {
  return uploadProgress.value.filter(upload => upload.status === 'complete')
})

const activeUploads = computed(() => {
  return uploadProgress.value.filter(upload => upload.status === 'uploading')
})

// Auto-populate document title when uploads complete
watch(completedUploads, (newUploads) => {
  if (newUploads.length > 0 && !documentTitle.value) {
    documentTitle.value = newUploads[0].cleaned_title
  }
}, { deep: true })

// Direct upload button handler
const selectDirectUpload = () => {
  uploadMethod.value = 'file'
  // Trigger the file input dialog immediately
  setTimeout(() => {
    document.getElementById('document-upload')?.click()
  }, 100)
}

// File selection handler
const handleFileSelect = async (event) => {
  const files = Array.from(event.target.files)

  for (const file of files) {
    const token = generateToken()
    const uploadData = {
      token,
      status: 'uploading',
      progress: 0,
      original_name: file.name,
      cleaned_title: cleanFilename(file.name),
      size: props.formatFileSize(file.size),
      file
    }

    uploadProgress.value.push(uploadData)

    // Start background upload
    await startFileUpload(file, token)
  }

  // Clear file input
  event.target.value = ''
}

// URL upload handler
const handleUrlUpload = async () => {
  if (!urlInput.value) return

  const token = generateToken()
  const documentName = `Document from ${new URL(urlInput.value).hostname}`

  const uploadData = {
    token,
    status: 'uploading',
    progress: 0,
    original_name: documentName,
    cleaned_title: cleanFilename(documentName),
    size: 'Unknown size',
    url: urlInput.value
  }

  uploadProgress.value.push(uploadData)

  // Start background URL upload
  await startUrlUpload(urlInput.value, documentName, token)

  // Clear input
  urlInput.value = ''
}

// Background URL upload function
const startUrlUpload = async (url, documentName, token) => {
  try {
    // Start URL download
    await axios.post(`/api/upload/url/${token}`, {
      url: url,
      name: documentName
    })

    // Poll for progress
    pollUploadProgress(token)

  } catch (error) {
    console.error('URL upload failed:', error)
    updateUploadStatus(token, {
      status: 'error',
      error: error.response?.data?.error || 'URL download failed'
    })
  }
}

// Background file upload function
const startFileUpload = async (file, token) => {
  const formData = new FormData()
  formData.append('file', file)

  try {
    // Start upload
    await axios.post(`/api/upload/file/${token}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Poll for progress
    pollUploadProgress(token)

  } catch (error) {
    console.error('Upload failed:', error)
    updateUploadStatus(token, {
      status: 'error',
      error: error.response?.data?.error || 'Upload failed'
    })
  }
}

// Poll upload progress
const pollUploadProgress = async (token) => {
  const maxAttempts = 60 // 1 minute max polling
  let attempts = 0

  const poll = async () => {
    try {
      const response = await axios.get(`/api/upload/progress/${token}`)
      const progress = response.data

      updateUploadStatus(token, progress)

      if (progress.status === 'complete' || progress.status === 'error') {
        return
      }

      if (attempts < maxAttempts && progress.status === 'uploading') {
        attempts++
        setTimeout(poll, 1000) // Poll every second
      } else {
        updateUploadStatus(token, {
          status: 'error',
          error: 'Upload timeout'
        })
      }

    } catch (error) {
      console.error('Progress polling failed:', error)
      updateUploadStatus(token, {
        status: 'error',
        error: 'Failed to check progress'
      })
    }
  }

  poll()
}

// Update upload status
const updateUploadStatus = (token, updates) => {
  const uploadIndex = uploadProgress.value.findIndex(upload => upload.token === token)
  if (uploadIndex !== -1) {
    Object.assign(uploadProgress.value[uploadIndex], updates)

    // Update cleaned info from backend
    if (updates.file_info) {
      uploadProgress.value[uploadIndex].cleaned_title = updates.file_info.cleaned_title
      uploadProgress.value[uploadIndex].size = updates.file_info.size
      uploadProgress.value[uploadIndex].original_name = updates.file_info.original_name
    }
  }
}

// Cancel upload
const cancelUpload = async (token) => {
  try {
    await axios.delete(`/api/upload/cancel/${token}`)
    uploadProgress.value = uploadProgress.value.filter(upload => upload.token !== token)
  } catch (error) {
    console.error('Failed to cancel upload:', error)
  }
}

// Save document with background uploads
const saveDocument = async () => {
  if (completedUploads.value.length === 0 || !props.selectedCompany) return

  uploading.value = true

  try {
    const formData = new FormData()

    // Use custom title or fallback to cleaned title
    formData.append('title', documentTitle.value || completedUploads.value[0].cleaned_title)
    formData.append('description', `Uploaded: ${completedUploads.value.map(u => u.original_name).join(', ')}`)
    formData.append('company_id', props.selectedCompany.id)
    formData.append('category_id', props.form.category_id || '')
    formData.append('visibility', props.form.visibility || 'private')

    // Add temp upload tokens
    completedUploads.value.forEach((upload, index) => {
      formData.append(`temp_upload_tokens[${index}]`, upload.token)
    })

    const response = await axios.post('/api/documents', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })

    // Clear completed uploads
    uploadProgress.value = uploadProgress.value.filter(upload => upload.status !== 'complete')

    // Reset form
    documentTitle.value = ''
    uploadMethod.value = 'file'
    urlInput.value = ''

    // Emit success
    emit('document-saved', response.data)
    emit('close')

  } catch (error) {
    console.error('Failed to save document:', error)
    // Handle error display
  } finally {
    uploading.value = false
  }
}

// Reset uploads when modal closes
watch(() => props.show, (newShow) => {
  if (!newShow) {
    // Cancel any active uploads when modal closes
    activeUploads.value.forEach(upload => {
      cancelUpload(upload.token)
    })
    uploadProgress.value = []
    documentTitle.value = ''
    uploadMethod.value = 'file'
    urlInput.value = ''
  }
})
</script>