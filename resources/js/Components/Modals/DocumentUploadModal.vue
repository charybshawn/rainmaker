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
      <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
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
      </div>

      <!-- Modal Footer -->
      <div class="sticky bottom-0 bg-gray-900 border-t border-white/20 px-6 py-4 rounded-b-2xl">
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
              'px-6 py-3 font-medium rounded-xl transition-all duration-300',
              canSave && !uploading
                ? 'bg-green-500/20 hover:bg-green-500/30 text-green-200 hover:text-white border border-green-400/30'
                : 'bg-gray-500/20 text-gray-400 border border-gray-400/30 cursor-not-allowed'
            ]"
          >
            <span v-if="uploading">Creating...</span>
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

// Unified upload state
const uploadMethod = ref('file')
const documentTitle = ref('')
const uploading = ref(false)
const selectedFiles = ref([])
const availableAssets = ref([])
const selectedAssets = ref([])

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

// Create assets directly and then create document
const saveDocument = async () => {
  if (!props.selectedCompany || (selectedFiles.value.length === 0 && selectedAssets.value.length === 0)) {
    return
  }

  uploading.value = true

  try {
    let assetIds = []

    // Step 1: Create new assets if files are selected
    if (selectedFiles.value.length > 0) {
      const formData = new FormData()
      formData.append('title', documentTitle.value || selectedFiles.value[0].name)
      formData.append('description', `Research asset: ${selectedFiles.value.map(f => f.name).join(', ')}`)
      formData.append('company_id', props.selectedCompany.id)
      formData.append('visibility', props.form.visibility || 'private')

      selectedFiles.value.forEach(file => {
        formData.append('files[]', file)
      })

      const assetResponse = await axios.post('/api/assets', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })

      const newAssets = assetResponse.data.assets || []
      assetIds = newAssets.map(asset => asset.id)
    }

    // Step 2: Add selected existing assets
    assetIds = assetIds.concat(selectedAssets.value.map(asset => asset.id))

    // Step 3: Create document and link assets
    const documentData = {
      title: documentTitle.value || 'Research Document',
      description: `Research document with ${assetIds.length} linked assets`,
      company_id: props.selectedCompany.id,
      category_id: props.form.category_id || null,
      visibility: props.form.visibility || 'private',
      asset_ids: assetIds
    }

    const documentResponse = await axios.post('/api/documents', documentData)

    // Reset form
    selectedFiles.value = []
    selectedAssets.value = []
    documentTitle.value = ''
    uploadMethod.value = 'file'

    // Emit success
    emit('document-saved', documentResponse.data)
    emit('close')

  } catch (error) {
    console.error('Failed to create research asset:', error)
    // Handle error display
  } finally {
    uploading.value = false
  }
}

// Computed properties
const canSave = computed(() => {
  return props.selectedCompany && (selectedFiles.value.length > 0 || selectedAssets.value.length > 0)
})

const saveButtonText = computed(() => {
  if (selectedFiles.value.length > 0 && selectedAssets.value.length > 0) {
    return `Create ${selectedFiles.value.length} Assets & Link ${selectedAssets.value.length} Existing`
  } else if (selectedFiles.value.length > 0) {
    return `Create ${selectedFiles.value.length} Research Asset${selectedFiles.value.length > 1 ? 's' : ''}`
  } else if (selectedAssets.value.length > 0) {
    return `Link ${selectedAssets.value.length} Existing Asset${selectedAssets.value.length > 1 ? 's' : ''}`
  }
  return 'Create Research Asset'
})

// Watch for modal show/hide and company changes
watch(() => props.show, (newValue) => {
  if (newValue) {
    fetchAvailableAssets()
  } else {
    // Reset state when modal closes
    selectedFiles.value = []
    selectedAssets.value = []
    documentTitle.value = ''
    uploadMethod.value = 'file'
  }
})

watch(() => props.selectedCompany, () => {
  if (props.show) {
    fetchAvailableAssets()
  }
})
</script>