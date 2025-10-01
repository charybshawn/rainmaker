<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-[9999]">
      <div class="bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 backdrop-blur-2xl rounded-3xl border border-white/10 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-white/10">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
            </div>
            <h2 class="text-xl font-semibold text-white">Upload Asset</h2>
          </div>
          <button
            @click="$emit('close')"
            class="p-2 hover:bg-white/10 rounded-full transition-colors"
          >
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Upload Form -->
        <div class="p-6">
          <form @submit.prevent="handleUpload" class="space-y-6">
            <!-- File Upload Area -->
            <div class="space-y-4">
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Select Files
              </label>

              <!-- Drop Zone -->
              <div
                ref="dropZone"
                @drop.prevent="handleDrop"
                @dragover.prevent
                @dragenter.prevent
                :class="[
                  'border-2 border-dashed rounded-xl p-8 text-center transition-all duration-200',
                  isDragging
                    ? 'border-blue-400 bg-blue-500/10'
                    : 'border-white/20 hover:border-blue-400/50 hover:bg-blue-500/5'
                ]"
              >
                <div v-if="!selectedFiles.length" class="space-y-4">
                  <div class="w-16 h-16 mx-auto rounded-full bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-white font-medium mb-1">Drop files here or click to browse</p>
                    <p class="text-gray-400 text-sm">Supports images, documents, PDFs, and more</p>
                  </div>
                  <input
                    ref="fileInput"
                    type="file"
                    multiple
                    @change="handleFileSelect"
                    class="hidden"
                    accept="*/*"
                  >
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg border border-blue-400/20 transition-all duration-200"
                  >
                    Choose Files
                  </button>
                </div>

                <!-- Selected Files Preview -->
                <div v-else class="space-y-3">
                  <div class="flex items-center justify-between">
                    <p class="text-white font-medium">{{ selectedFiles.length }} file(s) selected</p>
                    <button
                      type="button"
                      @click="clearFiles"
                      class="text-sm text-gray-400 hover:text-white transition-colors"
                    >
                      Clear all
                    </button>
                  </div>

                  <div class="max-h-60 overflow-y-auto space-y-3">
                    <div
                      v-for="(file, index) in selectedFiles"
                      :key="index"
                      class="p-4 bg-white/5 rounded-lg space-y-3"
                    >
                      <!-- File Info Row -->
                      <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                          <div class="w-8 h-8 rounded flex items-center justify-center" :class="getFileIconColor(file.type)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path v-if="file.type.startsWith('image/')" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                          </div>
                          <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate">{{ file.name }}</p>
                            <p class="text-gray-400 text-xs">{{ formatFileSize(file.size) }}</p>
                          </div>
                        </div>
                        <button
                          type="button"
                          @click="removeFile(index)"
                          class="p-1 hover:bg-red-500/20 rounded transition-colors"
                        >
                          <svg class="w-4 h-4 text-gray-400 hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>

                      <!-- Title Input -->
                      <div>
                        <input
                          v-model="fileTitles[index]"
                          type="text"
                          :placeholder="getDefaultTitle(file.name)"
                          class="w-full px-3 py-2 text-sm bg-white/5 border border-white/10 rounded-lg text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all"
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


            <!-- Research Item Association (Optional) -->
            <div v-if="researchItems && researchItems.length > 0">
              <label class="block text-sm font-medium text-gray-300 mb-2">
                Associate with Research Item (Optional)
              </label>
              <select
                v-model="form.research_item_id"
                class="w-full px-4 py-3 bg-white/5 border border-white/10 rounded-lg text-white focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all"
              >
                <option value="">No association</option>
                <option v-for="item in researchItems" :key="item.id" :value="item.id">
                  {{ item.title }}
                </option>
              </select>
            </div>


            <!-- Upload Progress -->
            <div v-if="uploading" class="space-y-2">
              <div class="flex items-center justify-between text-sm">
                <span class="text-gray-300">Uploading...</span>
                <span class="text-blue-400">{{ uploadProgress }}%</span>
              </div>
              <div class="w-full bg-white/10 rounded-full h-2">
                <div
                  class="bg-blue-500 h-2 rounded-full transition-all duration-300"
                  :style="{ width: uploadProgress + '%' }"
                ></div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
              <button
                type="button"
                @click="$emit('close')"
                class="flex-1 px-6 py-3 border border-white/20 text-gray-300 rounded-lg hover:bg-white/5 transition-all duration-200"
                :disabled="uploading"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="!selectedFiles.length || uploading"
                class="flex-1 px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg border border-blue-400/20 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                {{ uploading ? 'Uploading...' : 'Upload Files' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  show: Boolean,
  researchItems: Array
})

const emit = defineEmits(['close', 'uploaded'])

// State
const selectedFiles = ref([])
const fileTitles = ref([])
const isDragging = ref(false)
const uploading = ref(false)
const uploadProgress = ref(0)

// Form data
const form = ref({
  research_item_id: ''
})

// File handling
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  const currentLength = selectedFiles.value.length
  selectedFiles.value = [...selectedFiles.value, ...files]

  // Add empty titles for new files
  fileTitles.value = [...fileTitles.value, ...new Array(files.length).fill('')]
}

const handleDrop = (event) => {
  isDragging.value = false
  const files = Array.from(event.dataTransfer.files)
  const currentLength = selectedFiles.value.length
  selectedFiles.value = [...selectedFiles.value, ...files]

  // Add empty titles for new files
  fileTitles.value = [...fileTitles.value, ...new Array(files.length).fill('')]
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
  fileTitles.value.splice(index, 1)
}

const clearFiles = () => {
  selectedFiles.value = []
  fileTitles.value = []
}

// Upload handling
const handleUpload = async () => {
  if (!selectedFiles.value.length) return

  try {
    uploading.value = true
    uploadProgress.value = 0

    const formData = new FormData()

    // Add files and their individual titles
    selectedFiles.value.forEach((file, index) => {
      formData.append(`files[${index}]`, file)

      // Add individual title for each file
      const title = fileTitles.value[index] || getDefaultTitle(file.name)
      formData.append(`titles[${index}]`, title)
    })

    // Add metadata
    if (form.value.research_item_id) {
      formData.append('research_item_id', form.value.research_item_id)
    }

    // Upload with progress tracking
    const response = await axios.post('/api/assets/upload', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: (progressEvent) => {
        uploadProgress.value = Math.round(
          (progressEvent.loaded * 100) / progressEvent.total
        )
      }
    })

    emit('uploaded', response.data)
    resetForm()
    emit('close')
  } catch (error) {
    console.error('Upload error:', error)

    // Show detailed error information
    if (error.response?.status === 422) {
      const validationErrors = error.response.data.errors || error.response.data
      console.error('Validation errors:', validationErrors)

      let errorMessage = 'Upload failed due to validation errors:\n'
      if (typeof validationErrors === 'object') {
        Object.keys(validationErrors).forEach(field => {
          const messages = Array.isArray(validationErrors[field]) ? validationErrors[field] : [validationErrors[field]]
          errorMessage += `• ${field}: ${messages.join(', ')}\n`
        })
      } else {
        errorMessage += validationErrors
      }
      alert(errorMessage)
    } else {
      alert(`Upload failed: ${error.message || 'Unknown error'}`)
    }
  } finally {
    uploading.value = false
    uploadProgress.value = 0
  }
}

const resetForm = () => {
  selectedFiles.value = []
  fileTitles.value = []
  form.value = {
    research_item_id: ''
  }
}

// Utility functions
const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const getDefaultTitle = (fileName) => {
  // Remove file extension and return clean filename
  return fileName.substring(0, fileName.lastIndexOf('.')) || fileName
}

const getFileIconColor = (mimeType) => {
  if (!mimeType) return 'bg-gray-500/20 text-gray-400'

  if (mimeType.startsWith('image/')) return 'bg-green-500/20 text-green-400'
  if (mimeType === 'application/pdf') return 'bg-red-500/20 text-red-400'
  if (mimeType.includes('document') || mimeType.includes('word')) return 'bg-blue-500/20 text-blue-400'
  if (mimeType.includes('sheet') || mimeType.includes('excel')) return 'bg-emerald-500/20 text-emerald-400'
  return 'bg-gray-500/20 text-gray-400'
}

// Drag and drop event handlers
const handleDragEnter = () => {
  isDragging.value = true
}

const handleDragLeave = (event) => {
  if (!event.relatedTarget || !event.currentTarget.contains(event.relatedTarget)) {
    isDragging.value = false
  }
}

// Global drag and drop prevention
const preventGlobalDrop = (event) => {
  event.preventDefault()
}

onMounted(() => {
  document.addEventListener('dragover', preventGlobalDrop)
  document.addEventListener('drop', preventGlobalDrop)
})

onUnmounted(() => {
  document.removeEventListener('dragover', preventGlobalDrop)
  document.removeEventListener('drop', preventGlobalDrop)
})
</script>