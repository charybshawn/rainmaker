<template>
  <Teleport to="body">
    <div v-if="show && asset" class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center p-4 z-[9999]">
      <div class="bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 backdrop-blur-2xl rounded-3xl border border-white/10 w-full max-w-6xl max-h-[95vh] overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-white/10 flex-shrink-0">
          <div class="flex items-center gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" :class="getFileTypeColor(asset.mime_type)">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path v-if="isImage(asset.mime_type)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                <path v-else-if="isPdf(asset.mime_type)" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h2 class="text-xl font-semibold text-white truncate">{{ asset.title || asset.file_name }}</h2>
              <div class="flex items-center gap-3 text-sm text-gray-400 mt-1">
                <span class="uppercase">{{ getFileExtension(asset.file_name) }}</span>
                <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                <span>{{ formatFileSize(asset.size) }}</span>
                <span class="w-1 h-1 bg-gray-500 rounded-full"></span>
                <span>{{ formatDate(asset.created_at) }}</span>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 ml-4">
            <button
              @click="downloadAsset"
              class="p-3 hover:bg-white/10 rounded-full transition-colors"
              title="Download"
            >
              <svg class="w-5 h-5 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </button>
            <button
              @click="$emit('close')"
              class="p-3 hover:bg-white/10 rounded-full transition-colors"
              title="Close"
            >
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-hidden">
          <div class="h-full flex">
            <!-- Preview Area -->
            <div class="flex-1 flex items-center justify-center p-6 overflow-hidden">
              <!-- Image Preview -->
              <div v-if="isImage(asset.mime_type)" class="max-w-full max-h-full">
                <img
                  :src="asset.url"
                  :alt="asset.title || asset.file_name"
                  class="max-w-full max-h-full object-contain rounded-lg shadow-lg"
                  @load="imageLoaded = true"
                  @error="imageError = true"
                >
                <div v-if="!imageLoaded && !imageError" class="flex items-center justify-center w-96 h-64 bg-white/5 rounded-lg">
                  <div class="flex items-center gap-2 text-gray-400">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading image...
                  </div>
                </div>
                <div v-if="imageError" class="flex items-center justify-center w-96 h-64 bg-white/5 rounded-lg">
                  <div class="text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.186-.833-2.956 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p>Failed to load image</p>
                  </div>
                </div>
              </div>

              <!-- PDF Preview -->
              <div v-else-if="isPdf(asset.mime_type)" class="w-full h-full">
                <iframe
                  :src="asset.url"
                  class="w-full h-full rounded-lg border border-white/10"
                  @load="pdfLoaded = true"
                  @error="pdfError = true"
                ></iframe>
                <div v-if="!pdfLoaded && !pdfError" class="flex items-center justify-center w-full h-full bg-white/5 rounded-lg">
                  <div class="flex items-center gap-2 text-gray-400">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Loading PDF...
                  </div>
                </div>
                <div v-if="pdfError" class="flex items-center justify-center w-full h-full bg-white/5 rounded-lg">
                  <div class="text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5l-6.928-12c-.77-.833-2.186-.833-2.956 0l-6.928 12c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p>PDF preview unavailable</p>
                    <button
                      @click="downloadAsset"
                      class="mt-2 px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg border border-blue-400/20 transition-all duration-200"
                    >
                      Download to View
                    </button>
                  </div>
                </div>
              </div>

              <!-- Generic File Preview -->
              <div v-else class="flex flex-col items-center justify-center text-center space-y-6">
                <div class="w-24 h-24 rounded-2xl flex items-center justify-center" :class="getFileTypeColor(asset.mime_type)">
                  <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-medium text-white mb-2">{{ asset.title || asset.file_name }}</h3>
                  <p class="text-gray-400 mb-4">Preview not available for this file type</p>
                  <button
                    @click="downloadAsset"
                    class="px-6 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg border border-blue-400/20 transition-all duration-200"
                  >
                    Download File
                  </button>
                </div>
              </div>
            </div>

            <!-- Sidebar -->
            <div class="w-80 border-l border-white/10 bg-white/5 p-6 overflow-y-auto">
              <div class="space-y-6">
                <!-- File Details -->
                <div>
                  <h3 class="text-lg font-semibold text-white mb-4">File Details</h3>
                  <div class="space-y-3">
                    <div>
                      <label class="block text-sm font-medium text-gray-400 mb-1">File Name</label>
                      <p class="text-white text-sm break-all">{{ asset.file_name }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-400 mb-1">File Type</label>
                      <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-white/10 text-white text-xs rounded">{{ getFileExtension(asset.file_name) }}</span>
                        <span class="text-gray-400 text-sm">{{ asset.mime_type }}</span>
                      </div>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-400 mb-1">File Size</label>
                      <p class="text-white text-sm">{{ formatFileSize(asset.size) }}</p>
                    </div>
                    <div>
                      <label class="block text-sm font-medium text-gray-400 mb-1">Upload Date</label>
                      <p class="text-white text-sm">{{ formatDate(asset.created_at) }}</p>
                    </div>
                  </div>
                </div>

                <!-- Source Info -->
                <div v-if="asset.source_type || asset.source_title">
                  <h3 class="text-lg font-semibold text-white mb-4">Source</h3>
                  <div class="space-y-3">
                    <div v-if="asset.source_type">
                      <label class="block text-sm font-medium text-gray-400 mb-1">Source Type</label>
                      <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs rounded capitalize">
                        {{ asset.source_type.replace('_', ' ') }}
                      </span>
                    </div>
                    <div v-if="asset.source_title">
                      <label class="block text-sm font-medium text-gray-400 mb-1">Linked Item</label>
                      <p class="text-white text-sm">{{ asset.source_title }}</p>
                    </div>
                  </div>
                </div>

                <!-- Description -->
                <div v-if="asset.description">
                  <h3 class="text-lg font-semibold text-white mb-4">Description</h3>
                  <p class="text-gray-300 text-sm leading-relaxed">{{ asset.description }}</p>
                </div>

                <!-- Tags -->
                <div v-if="asset.tags && asset.tags.length">
                  <h3 class="text-lg font-semibold text-white mb-4">Tags</h3>
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="tag in asset.tags"
                      :key="tag.id"
                      class="px-2 py-1 text-xs rounded"
                      :style="{ backgroundColor: tag.color + '20', color: tag.color }"
                    >
                      {{ tag.name }}
                    </span>
                  </div>
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-white/10">
                  <h3 class="text-lg font-semibold text-white mb-4">Actions</h3>
                  <div class="space-y-3">
                    <button
                      @click="downloadAsset"
                      class="w-full px-4 py-3 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg border border-blue-400/20 transition-all duration-200 flex items-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                      Download
                    </button>
                    <button
                      @click="copyLink"
                      class="w-full px-4 py-3 bg-white/5 hover:bg-white/10 text-gray-300 rounded-lg border border-white/10 transition-all duration-200 flex items-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                      </svg>
                      Copy Link
                    </button>
                    <button
                      @click="$emit('delete', asset)"
                      class="w-full px-4 py-3 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg border border-red-400/20 transition-all duration-200 flex items-center gap-2"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  show: Boolean,
  asset: Object
})

const emit = defineEmits(['close', 'delete'])

// State
const imageLoaded = ref(false)
const imageError = ref(false)
const pdfLoaded = ref(false)
const pdfError = ref(false)

// Methods
const downloadAsset = () => {
  if (props.asset?.url) {
    window.open(props.asset.url, '_blank')
  }
}

const copyLink = async () => {
  if (props.asset?.url) {
    try {
      await navigator.clipboard.writeText(props.asset.url)
      // You could show a toast notification here
      console.log('Link copied to clipboard')
    } catch (error) {
      console.error('Failed to copy link:', error)
    }
  }
}

// Utility functions
const isImage = (mimeType) => {
  return mimeType && mimeType.startsWith('image/')
}

const isPdf = (mimeType) => {
  return mimeType === 'application/pdf'
}

const getFileExtension = (filename) => {
  if (!filename) return ''
  return filename.split('.').pop()?.toUpperCase() || ''
}

const getFileTypeColor = (mimeType) => {
  if (!mimeType) return 'bg-gray-500/20 text-gray-400'

  if (mimeType.startsWith('image/')) return 'bg-green-500/20 text-green-400'
  if (mimeType === 'application/pdf') return 'bg-red-500/20 text-red-400'
  if (mimeType.includes('document') || mimeType.includes('word')) return 'bg-blue-500/20 text-blue-400'
  if (mimeType.includes('sheet') || mimeType.includes('excel')) return 'bg-emerald-500/20 text-emerald-400'
  return 'bg-gray-500/20 text-gray-400'
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>