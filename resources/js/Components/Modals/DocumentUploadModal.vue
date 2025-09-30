<template>
  <Teleport to="body">
    <div v-show="show" class="fixed inset-0 z-50 flex items-start sm:items-center justify-center p-0 sm:p-4" @click.self="emit('close')">
      <!-- Backdrop with Dark Mode Context -->
      <div class="fixed inset-0 bg-black/70 backdrop-blur-md dark:bg-black/80"></div>
      <!-- Modal Container with Dark Mode Context -->
      <div class="relative dark bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-none sm:rounded-2xl border-0 sm:border sm:border-white/10 w-full h-full sm:h-auto sm:w-[60%] sm:max-w-2xl sm:max-h-[85vh] overflow-y-auto shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">

      <!-- Modal Header -->
      <div class="sticky top-0 bg-gray-900 border-b border-white/20 px-4 sm:px-6 py-3 sm:py-4 rounded-t-none sm:rounded-t-2xl z-10">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-semibold text-white">
              {{ isEditing ? '📝 Edit Document' : '📄 Create Research Asset' }}
            </h2>
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
      <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-red-500/10 border border-red-400/30 text-red-200 px-4 py-3 rounded-xl backdrop-blur-xl">
          {{ errors.general }}
        </div>

        <!-- Upload Method Selection -->
        <div>
          <label class="block text-sm font-medium text-white mb-3">
            {{ isEditing ? 'Update Document' : 'Create Research Asset' }}
          </label>
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

        <!-- Selected Files Display -->
        <div v-if="selectedFiles.length > 0" class="space-y-3">
          <h4 class="text-sm font-medium text-white">Selected Files:</h4>
          <div v-for="file in selectedFiles" :key="file.name"
               class="p-3 bg-white/5 border border-white/20 rounded-xl backdrop-blur-xl flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-white">{{ file.name }}</p>
              <p class="text-xs text-gray-400">{{ formatFileSize(file.size) }}</p>
            </div>
            <button @click="removeFile(file)" class="text-red-400 hover:text-red-300">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Document Title -->
        <div>
          <label class="block text-sm font-medium text-white mb-2">Research Asset Title</label>
          <input
            v-model="documentTitle"
            type="text"
            :placeholder="selectedFiles.length > 0 ? selectedFiles[0].name : 'Enter title...'"
            class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
          />
        </div>

        <!-- Form Fields -->
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-white mb-2">Category</label>
            <select
              v-model="form.category_id"
              class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            >
              <option value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              v-model="form.visibility"
              class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
            >
              <option value="private">Private</option>
              <option value="team">Team</option>
              <option value="public">Public</option>
            </select>
          </div>
        </div>

        <!-- Tags Section -->
        <div>
          <label class="block text-sm font-medium text-white mb-3">Tags</label>
          <div class="dark">
            <TagSelector
              v-model="selectedTags"
              placeholder="Search or create tags..."
              help-text="Add tags to categorize and organize your research document"
            />
          </div>
        </div>

        <!-- Error Display Section -->
        <div v-if="hasErrors || criticalError" class="mt-6 space-y-3">
          <!-- Critical Error -->
          <div v-if="criticalError" class="p-4 rounded-xl bg-red-500/20 border border-red-400/30">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></div>
                <span class="text-red-200 font-medium">Critical Error</span>
              </div>
            </div>
            <p class="text-red-100 text-sm mt-2">{{ criticalError }}</p>
          </div>

          <!-- Upload Errors -->
          <div v-for="error in uploadErrors" :key="error.id"
               :class="[
                 'p-4 rounded-xl border flex items-center justify-between',
                 error.type === 'validation' ? 'bg-yellow-500/20 border-yellow-400/30' :
                 error.type === 'upload' ? 'bg-red-500/20 border-red-400/30' :
                 error.type === 'network' ? 'bg-blue-500/20 border-blue-400/30' :
                 'bg-red-500/20 border-red-400/30'
               ]">
            <div class="flex items-center space-x-3 flex-1">
              <div :class="[
                'w-2 h-2 rounded-full',
                error.type === 'validation' ? 'bg-yellow-400' :
                error.type === 'upload' ? 'bg-red-400' :
                error.type === 'network' ? 'bg-blue-400' : 'bg-red-400'
              ]"></div>
              <div class="flex-1">
                <p :class="[
                  'text-sm font-medium',
                  error.type === 'validation' ? 'text-yellow-200' :
                  error.type === 'upload' ? 'text-red-200' :
                  error.type === 'network' ? 'text-blue-200' : 'text-red-200'
                ]">{{ error.message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ new Date(error.timestamp).toLocaleTimeString() }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-2">
              <button v-if="error.retryable" @click="retryOperation(error)"
                      class="px-3 py-1 text-xs bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors">
                Retry
              </button>
              <button @click="removeError(error.id)"
                      class="px-3 py-1 text-xs bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors">
                Dismiss
              </button>
            </div>
          </div>
        </div>

        <!-- File Upload Progress -->
        <div v-if="uploading && Object.keys(uploadProgress).length > 0" class="mt-6 space-y-3">
          <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-white">Upload Progress</h4>
            <button @click="uploadProgress = {}" class="text-xs text-gray-400 hover:text-white">Clear</button>
          </div>
          <div v-for="(progress, fileName) in uploadProgress" :key="fileName"
               class="p-3 rounded-lg bg-white/5 border border-white/10">
            <div class="flex items-center justify-between mb-2">
              <span class="text-sm text-white truncate">{{ fileName }}</span>
              <span class="text-xs text-gray-400">{{ progress.status }}</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2">
              <div :class="[
                'h-2 rounded-full transition-all duration-300',
                progress.status === 'completed' ? 'bg-green-500' :
                progress.status === 'failed' ? 'bg-red-500' :
                progress.status === 'uploading' ? 'bg-blue-500' :
                'bg-yellow-500'
              ]" :style="{ width: progress.progress + '%' }"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-1">
              <span>{{ progress.progress }}%</span>
              <span v-if="progress.status === 'completed'">✓ Complete</span>
              <span v-else-if="progress.status === 'failed'">✗ Failed</span>
              <span v-else-if="progress.status === 'uploading'">⏳ Uploading</span>
              <span v-else>⏸ {{ progress.status }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div class="sticky bottom-0 bg-gray-900 border-t border-white/20 px-6 py-4 rounded-b-2xl">
        <!-- Error Summary -->
        <div v-if="hasErrors" class="mb-4 flex items-center justify-between p-3 rounded-lg bg-red-500/10 border border-red-400/20">
          <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></div>
            <span class="text-red-200 text-sm font-medium">
              {{ uploadErrors.length }} error{{ uploadErrors.length > 1 ? 's' : '' }} occurred
            </span>
          </div>
          <div class="flex items-center space-x-2">
            <button v-if="canRetry" @click="saveDocument"
                    class="px-3 py-1 text-xs bg-red-500/20 hover:bg-red-500/30 text-red-200 rounded-lg transition-colors">
              Retry All
            </button>
            <button @click="clearErrors"
                    class="px-3 py-1 text-xs bg-white/10 hover:bg-white/20 text-white rounded-lg transition-colors">
              Clear All
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between">
          <button
            @click="emit('close')"
            class="px-6 py-3 bg-white/10 hover:bg-white/20 text-gray-300 hover:text-white font-medium rounded-xl transition-all duration-300 border border-white/20"
          >
            Cancel
          </button>
          <button
            @click="saveDocument"
            :disabled="!canSave || uploading"
            :class="[
              'px-6 py-3 font-medium rounded-xl transition-all duration-300 flex items-center space-x-2',
              canSave && !uploading
                ? hasErrors
                  ? 'bg-orange-500/20 hover:bg-orange-500/30 text-orange-200 hover:text-white border border-orange-400/30'
                  : 'bg-green-500/20 hover:bg-green-500/30 text-green-200 hover:text-white border border-green-400/30'
                : 'bg-gray-500/20 text-gray-400 border border-gray-400/30 cursor-not-allowed'
            ]"
          >
            <div v-if="uploading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            <span v-if="uploading">{{ isEditing ? 'Updating...' : 'Creating...' }}</span>
            <span v-else-if="hasErrors && canRetry">Retry Upload</span>
            <span v-else>{{ saveButtonText }}</span>
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
import TagSelector from '@/Components/TagSelector.vue'

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
  editingDocument: {
    type: Object,
    default: null
  },
  isEditing: {
    type: Boolean,
    default: false
  },
  formatFileSize: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['close', 'save', 'file-upload', 'remove-file', 'document-saved'])

// Unified upload state
const uploadMethod = ref('file')
const documentTitle = ref('')
const uploading = ref(false)
const selectedFiles = ref([])
const availableAssets = ref([])
const selectedAssets = ref([])
const selectedTags = ref([])

// Enhanced error handling
const uploadErrors = ref([])
const uploadProgress = ref({})
const retryAttempts = ref({})
const criticalError = ref(null)

// Error handling states
const hasErrors = computed(() => uploadErrors.value.length > 0)
const canRetry = computed(() => uploadErrors.value.some(error => error.retryable))

// File selection handler - simplified for direct asset creation
const handleFileSelect = async (event) => {
  selectedFiles.value = Array.from(event.target.files)
  event.target.value = ''
}

// Remove selected file
const removeFile = (fileToRemove) => {
  selectedFiles.value = selectedFiles.value.filter(file => file !== fileToRemove)
}

// Direct upload button handler
const selectDirectUpload = () => {
  uploadMethod.value = 'file'
  setTimeout(() => {
    document.getElementById('document-upload')?.click()
  }, 100)
}

// Toggle asset selection for existing assets
const toggleAssetSelection = (asset) => {
  const index = selectedAssets.value.findIndex(a => a.id === asset.id)
  if (index > -1) {
    selectedAssets.value.splice(index, 1)
  } else {
    selectedAssets.value.push(asset)
  }
}

// Fetch available assets for the company
const fetchAvailableAssets = async () => {
  if (!props.selectedCompany) return

  try {
    const response = await axios.get('/api/documents', {
      params: {
        company_id: props.selectedCompany.id,
        limit: 50
      }
    })
    availableAssets.value = response.data.data || []
  } catch (error) {
    console.error('Failed to fetch available assets:', error)
  }
}

// Error handling utilities
const clearErrors = () => {
  uploadErrors.value = []
  criticalError.value = null
}

const addError = (error) => {
  uploadErrors.value.push({
    id: Date.now(),
    message: error.message,
    type: error.type || 'error',
    retryable: error.retryable !== false,
    timestamp: new Date(),
    context: error.context || {}
  })
}

const removeError = (errorId) => {
  uploadErrors.value = uploadErrors.value.filter(error => error.id !== errorId)
}

const getFileProgress = (fileName) => {
  return uploadProgress.value[fileName] || { progress: 0, status: 'pending' }
}

const setFileProgress = (fileName, progress, status = 'uploading') => {
  uploadProgress.value[fileName] = { progress, status, timestamp: Date.now() }
}

const getRetryCount = (context) => {
  const key = typeof context === 'string' ? context : JSON.stringify(context)
  return retryAttempts.value[key] || 0
}

const incrementRetryCount = (context) => {
  const key = typeof context === 'string' ? context : JSON.stringify(context)
  retryAttempts.value[key] = (retryAttempts.value[key] || 0) + 1
}

const resetRetryCount = (context) => {
  const key = typeof context === 'string' ? context : JSON.stringify(context)
  delete retryAttempts.value[key]
}

// Robust document creation with comprehensive error handling
const saveDocument = async () => {
  console.log('saveDocument called', {
    selectedCompany: props.selectedCompany?.name,
    selectedFiles: selectedFiles.value.length,
    selectedAssets: selectedAssets.value.length,
    selectedTags: selectedTags.value.length,
    documentTitle: documentTitle.value
  })

  // Clear previous errors
  clearErrors()

  // Validation checks
  if (!props.selectedCompany) {
    addError({
      message: 'Please select a company before creating a document.',
      type: 'validation',
      retryable: false
    })
    return
  }

  if (selectedFiles.value.length === 0 && selectedAssets.value.length === 0) {
    addError({
      message: 'Please select at least one file to upload or one existing asset to attach.',
      type: 'validation',
      retryable: false
    })
    return
  }

  if (!documentTitle.value?.trim()) {
    addError({
      message: 'Document title is required.',
      type: 'validation',
      retryable: false
    })
    return
  }

  uploading.value = true
  let assetIds = []

  try {
    // Step 1: Create new assets if files are selected
    if (selectedFiles.value.length > 0) {
      try {
        console.log('Creating assets from files:', selectedFiles.value.map(f => f.name))

        // Initialize progress for each file
        selectedFiles.value.forEach(file => {
          setFileProgress(file.name, 0, 'preparing')
        })

        // Validate file sizes and types
        const maxFileSize = 10 * 1024 * 1024 // 10MB
        const allowedTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']

        for (const file of selectedFiles.value) {
          if (file.size > maxFileSize) {
            throw new Error(`File "${file.name}" is too large. Maximum size is 10MB.`)
          }

          const extension = file.name.split('.').pop()?.toLowerCase()
          if (!allowedTypes.includes(extension)) {
            throw new Error(`File "${file.name}" has an unsupported format. Allowed formats: ${allowedTypes.join(', ')}`)
          }

          setFileProgress(file.name, 25, 'validated')
        }

        const formData = new FormData()
        formData.append('title', documentTitle.value.trim())
        formData.append('description', `Research asset: ${selectedFiles.value.map(f => f.name).join(', ')}`)
        formData.append('company_id', props.selectedCompany.id)
        formData.append('visibility', props.form.visibility || 'private')

        selectedFiles.value.forEach(file => {
          formData.append('files[]', file)
          setFileProgress(file.name, 50, 'uploading')
        })

        console.log('FormData prepared for', selectedFiles.value.length, 'files')

        const assetResponse = await axios.post('/api/assets', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          timeout: 60000, // 60 second timeout
          onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
            selectedFiles.value.forEach(file => {
              setFileProgress(file.name, Math.min(50 + (percentCompleted / 2), 90), 'uploading')
            })
          }
        })

        console.log('Asset creation response:', assetResponse.data)

        // Handle partial failures
        if (assetResponse.data.errors && assetResponse.data.errors.length > 0) {
          assetResponse.data.errors.forEach(error => {
            addError({
              message: `Failed to upload "${error.filename}": ${error.error}`,
              type: 'upload',
              retryable: true,
              context: { filename: error.filename }
            })
          })
        }

        const newAssets = assetResponse.data.assets || []
        if (newAssets.length === 0 && selectedFiles.value.length > 0) {
          throw new Error('No assets were created successfully. Please check file formats and try again.')
        }

        assetIds = newAssets.map(asset => asset.id)
        console.log('Generated asset IDs:', assetIds)

        // Mark successful uploads
        selectedFiles.value.forEach(file => {
          const wasSuccessful = newAssets.some(asset =>
            asset.file_name === file.name || asset.title === documentTitle.value
          )
          if (wasSuccessful) {
            setFileProgress(file.name, 100, 'completed')
          }
        })

      } catch (fileError) {
        const retryCount = getRetryCount('asset_creation')

        if (retryCount < 3 && !fileError.message.includes('too large') && !fileError.message.includes('unsupported format')) {
          addError({
            message: `Asset creation failed: ${fileError.message}. Retry ${retryCount + 1}/3 available.`,
            type: 'upload',
            retryable: true,
            context: { operation: 'asset_creation', attempt: retryCount + 1 }
          })
          incrementRetryCount('asset_creation')
        } else {
          addError({
            message: `Asset creation failed: ${fileError.message}`,
            type: 'upload',
            retryable: false
          })
        }

        selectedFiles.value.forEach(file => {
          setFileProgress(file.name, 0, 'failed')
        })

        throw fileError
      }
    }

    // Step 2: Add selected existing assets
    assetIds = assetIds.concat(selectedAssets.value.map(asset => asset.id))

    // Step 3: Create document and link assets
    try {
      const documentData = {
        title: documentTitle.value.trim(),
        description: `Research document with ${assetIds.length} linked assets`,
        company_id: props.selectedCompany.id,
        category_id: props.form.category_id || null,
        visibility: props.form.visibility || 'private',
        asset_ids: assetIds,
        tag_ids: selectedTags.value.map(tag => tag.id)
      }

      console.log('Creating document with data:', documentData)
      const documentResponse = await axios.post('/api/documents', documentData, {
        timeout: 30000 // 30 second timeout
      })
      console.log('Document creation response:', documentResponse.data)

      // Success - emit success event with the created document
      emit('document-saved', documentResponse.data)

      // Reset form and close modal
      resetForm()
      emit('close')

    } catch (docError) {
      const retryCount = getRetryCount('document_creation')

      if (retryCount < 2) {
        addError({
          message: `Document creation failed: ${docError.response?.data?.message || docError.message}. Retry ${retryCount + 1}/2 available.`,
          type: 'creation',
          retryable: true,
          context: { operation: 'document_creation', attempt: retryCount + 1 }
        })
        incrementRetryCount('document_creation')
      } else {
        addError({
          message: `Document creation failed: ${docError.response?.data?.message || docError.message}`,
          type: 'creation',
          retryable: false
        })
      }

      throw docError
    }

  } catch (error) {
    console.error('Document creation failed:', error)

    // Handle network errors
    if (!error.response) {
      addError({
        message: 'Network error - please check your connection and try again.',
        type: 'network',
        retryable: true
      })
    }
    // Handle server errors
    else if (error.response.status >= 500) {
      addError({
        message: 'Server error - please try again in a few moments.',
        type: 'server',
        retryable: true
      })
    }
    // Handle validation errors
    else if (error.response.status === 422) {
      const validationErrors = error.response.data.errors || {}
      Object.entries(validationErrors).forEach(([field, messages]) => {
        messages.forEach(message => {
          addError({
            message: `${field}: ${message}`,
            type: 'validation',
            retryable: false
          })
        })
      })
    }
    // Handle other client errors
    else {
      addError({
        message: error.response?.data?.message || 'An unexpected error occurred.',
        type: 'error',
        retryable: false
      })
    }
  } finally {
    uploading.value = false
  }
}

// Reset form function
const resetForm = () => {
  selectedFiles.value = []
  selectedAssets.value = []
  selectedTags.value = []
  documentTitle.value = ''
  uploadMethod.value = 'file'
  clearErrors()
  uploadProgress.value = {}
  retryAttempts.value = {}
}

// Retry failed operations
const retryOperation = async (error) => {
  if (!error.retryable) return

  removeError(error.id)

  if (error.context?.operation === 'asset_creation' || error.context?.operation === 'document_creation') {
    await saveDocument()
  }
}

// Computed properties
const canSave = computed(() => {
  return props.selectedCompany &&
         (selectedFiles.value.length > 0 || selectedAssets.value.length > 0) &&
         documentTitle.value?.trim()
})

const saveButtonText = computed(() => {
  if (props.isEditing) {
    if (selectedFiles.value.length > 0 && selectedAssets.value.length > 0) {
      return `Update & Add ${selectedFiles.value.length} Assets, Link ${selectedAssets.value.length} Existing`
    } else if (selectedFiles.value.length > 0) {
      return `Update & Add ${selectedFiles.value.length} Asset${selectedFiles.value.length > 1 ? 's' : ''}`
    } else if (selectedAssets.value.length > 0) {
      return `Update & Link ${selectedAssets.value.length} Asset${selectedAssets.value.length > 1 ? 's' : ''}`
    }
    return 'Update Document'
  } else {
    if (selectedFiles.value.length > 0 && selectedAssets.value.length > 0) {
      return `Create ${selectedFiles.value.length} Assets & Link ${selectedAssets.value.length} Existing`
    } else if (selectedFiles.value.length > 0) {
      return `Create ${selectedFiles.value.length} Research Asset${selectedFiles.value.length > 1 ? 's' : ''}`
    } else if (selectedAssets.value.length > 0) {
      return `Link ${selectedAssets.value.length} Existing Asset${selectedAssets.value.length > 1 ? 's' : ''}`
    }
    return 'Create Research Asset'
  }
})

// Watch for modal show/hide and company changes
watch(() => props.show, (newValue) => {
  if (newValue) {
    fetchAvailableAssets()
  } else {
    // Reset state when modal closes
    resetForm()
  }
})

watch(() => props.selectedCompany, () => {
  if (props.show) {
    fetchAvailableAssets()
  }
})
</script>