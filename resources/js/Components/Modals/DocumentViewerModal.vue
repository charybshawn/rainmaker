<template>
  <div
    v-show="show"
    class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 lg:p-6 z-[9999]"
    @click.self="$emit('close')"
  >
    <div class="bg-gradient-to-br from-white/10 via-white/15 to-white/10 backdrop-blur-xl rounded-2xl border border-white/20 w-full lg:w-[66%] max-w-6xl h-full overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] flex flex-col">

      <!-- Header -->
      <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-white/20">
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-2">
              📄 {{ document?.title || 'Document Title' }}
            </h1>
            <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
              <span v-if="document?.category" class="inline-flex items-center px-2 py-1 bg-blue-500/20 text-blue-300 rounded-md text-xs">
                {{ document.category.name }}
              </span>
              <span v-if="document?.visibility" class="inline-flex items-center px-2 py-1 bg-purple-500/20 text-purple-300 rounded-md text-xs capitalize">
                {{ document.visibility }}
              </span>
              <span v-if="document?.user" class="inline-flex items-center px-2 py-1 bg-green-500/20 text-green-300 rounded-md text-xs">
                👤 {{ document.user.name }}
              </span>
              <span v-if="document?.created_at" class="inline-flex items-center px-2 py-1 bg-gray-500/20 text-gray-300 rounded-md text-xs">
                {{ formatDate(document.created_at) }}
              </span>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="ml-4 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center"
          >
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-y-auto">
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

          <!-- Description -->
          <div v-if="document?.description" class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
            <button
              @click="toggleSection('description')"
              class="w-full flex items-center justify-between text-left"
            >
              <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
                Description
              </h2>
              <svg
                class="w-5 h-5 text-white/70 transition-transform"
                :class="{ 'rotate-180': openSections.description }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div v-if="openSections.description" class="mt-4 prose prose-invert max-w-none">
              <p class="text-gray-300 whitespace-pre-wrap">{{ document.description }}</p>
            </div>
          </div>


          <!-- Tags -->
          <div v-if="document?.tags && document.tags.length > 0" class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
            <button
              @click="toggleSection('tags')"
              class="w-full flex items-center justify-between text-left"
            >
              <h2 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Tags
              </h2>
              <svg
                class="w-5 h-5 text-white/70 transition-transform"
                :class="{ 'rotate-180': openSections.tags }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
              </svg>
            </button>
            <div v-if="openSections.tags" class="mt-4 flex flex-wrap gap-2">
              <span
                v-for="tag in document.tags"
                :key="tag.id"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :style="{ backgroundColor: tag.color + '20', color: tag.color, borderColor: tag.color + '40' }"
                :class="'border'"
              >
                {{ tag.name }}
              </span>
            </div>
          </div>

          <!-- Document Content -->
          <div v-if="document?.attachments && document.attachments.length > 0" class="space-y-4">
            <div
              v-for="attachment in document.attachments"
              :key="attachment.id"
              class="bg-white/5 rounded-xl border border-white/10"
            >
              <!-- File Header -->
              <div class="flex items-center justify-between p-4 border-b border-white/10">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0">
                    <svg v-if="getFileIcon(attachment.mime_type)" class="w-6 h-6 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                      <path v-html="getFileIcon(attachment.mime_type)"/>
                    </svg>
                  </div>
                  <div>
                    <p class="text-white font-medium text-sm">{{ attachment.name || attachment.file_name }}</p>
                    <p class="text-gray-400 text-xs">{{ formatFileSize(attachment.size) }} • {{ getFileType(attachment.mime_type) }}</p>
                  </div>
                </div>
                <div class="flex space-x-2">
                  <button
                    @click="downloadAttachment(attachment)"
                    class="p-1 text-blue-400 hover:text-blue-300 hover:bg-blue-500/20 rounded transition-colors"
                    title="Download"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </button>
                  <button
                    @click="openInNewTab(attachment)"
                    class="p-1 text-green-400 hover:text-green-300 hover:bg-green-500/20 rounded transition-colors"
                    title="Open in new tab"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Inline Content Display -->
              <div class="p-4">
                <!-- PDF Viewer -->
                <div v-if="isPDF(attachment.mime_type)" class="w-full">
                  <iframe
                    :src="attachment.url"
                    class="w-full h-96 rounded border border-white/10"
                    frameborder="0"
                    title="PDF Viewer"
                  ></iframe>
                </div>

                <!-- Image Viewer -->
                <div v-else-if="isImage(attachment.mime_type)" class="flex justify-center">
                  <img
                    :src="attachment.url"
                    :alt="attachment.name || attachment.file_name"
                    class="max-w-full h-auto rounded border border-white/10 shadow-lg"
                    style="max-height: 500px;"
                  />
                </div>

                <!-- Text File Viewer -->
                <div v-else-if="isTextFile(attachment.mime_type)" class="bg-gray-900/50 rounded p-4 font-mono text-sm">
                  <div class="text-gray-300">
                    <p class="italic text-gray-400 mb-2">Text content preview:</p>
                    <button
                      @click="loadTextContent(attachment)"
                      v-if="!attachment.textContent"
                      class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded text-xs hover:bg-blue-500/30 transition-colors"
                    >
                      Load Content
                    </button>
                    <pre v-else class="whitespace-pre-wrap text-gray-300">{{ attachment.textContent }}</pre>
                  </div>
                </div>

                <!-- Other File Types -->
                <div v-else class="text-center p-8 bg-gray-900/50 rounded">
                  <div class="flex flex-col items-center space-y-3">
                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path v-html="getFileIcon(attachment.mime_type)"/>
                    </svg>
                    <p class="text-gray-300">{{ getFileType(attachment.mime_type) }} file</p>
                    <p class="text-gray-400 text-sm">Click "Open in new tab" or "Download" to view this file</p>
                    <div class="flex space-x-2 mt-4">
                      <button
                        @click="openInNewTab(attachment)"
                        class="px-4 py-2 bg-green-500/20 text-green-300 rounded hover:bg-green-500/30 transition-colors text-sm"
                      >
                        Open in New Tab
                      </button>
                      <button
                        @click="downloadAttachment(attachment)"
                        class="px-4 py-2 bg-blue-500/20 text-blue-300 rounded hover:bg-blue-500/30 transition-colors text-sm"
                      >
                        Download
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- Footer Actions -->
      <div class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 px-4 sm:px-6 lg:px-8 py-4 border-t border-white/20">
        <div class="flex flex-wrap items-center justify-between gap-4">
          <div class="text-sm text-gray-400">
            <span v-if="document?.company">📈 {{ document.company.name }} ({{ document.company.ticker }})</span>
          </div>
          <div class="flex items-center space-x-3">
            <button
              v-if="canEdit"
              @click="$emit('edit', document)"
              class="px-4 py-2 bg-blue-600/20 text-blue-300 border border-blue-500/30 rounded-lg hover:bg-blue-600/30 transition-colors flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
              </svg>
              <span>Edit</span>
            </button>
            <button
              v-if="canDelete"
              @click="$emit('delete', document)"
              class="px-4 py-2 bg-red-600/20 text-red-300 border border-red-500/30 rounded-lg hover:bg-red-600/30 transition-colors flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
              <span>Delete</span>
            </button>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  document: {
    type: Object,
    default: null
  },
  canEdit: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  }
})

defineEmits(['close', 'edit', 'delete'])

const openSections = ref({
  description: false,
  tags: false,
  content: false
})

const toggleSection = (section) => {
  openSections.value[section] = !openSections.value[section]
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

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const getFileType = (mimeType) => {
  const types = {
    'application/pdf': 'PDF',
    'application/msword': 'DOC',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
    'application/vnd.ms-excel': 'XLS',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
    'application/vnd.ms-powerpoint': 'PPT',
    'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
    'text/plain': 'TXT',
    'text/csv': 'CSV',
    'image/jpeg': 'JPG',
    'image/png': 'PNG',
    'image/gif': 'GIF',
    'image/webp': 'WEBP',
    'image/svg+xml': 'SVG'
  }
  return types[mimeType] || 'FILE'
}

const getFileIcon = (mimeType) => {
  // Return appropriate icon path based on mime type
  if (mimeType?.startsWith('image/')) {
    return '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>'
  } else if (mimeType?.includes('pdf')) {
    return '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a1 1 0 01.707.293l4 4A1 1 0 0119 7v9a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>'
  } else {
    return '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a1 1 0 01.707.293l4 4A1 1 0 0119 7v9a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>'
  }
}

const downloadAttachment = (attachment) => {
  if (attachment.url) {
    const a = document.createElement('a')
    a.href = attachment.url
    a.download = attachment.name || attachment.file_name
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
  }
}

const viewAttachment = (attachment) => {
  if (attachment.url) {
    window.open(attachment.url, '_blank')
  }
}

// Utility functions for inline content display
const isPDF = (mimeType) => mimeType === 'application/pdf'

const isImage = (mimeType) => mimeType?.startsWith('image/')

const isTextFile = (mimeType) => mimeType === 'text/plain'

const loadTextContent = async (attachment) => {
  try {
    const response = await fetch(attachment.url)
    const text = await response.text()
    // Store text content in attachment object for display
    attachment.textContent = text
  } catch (error) {
    console.error('Failed to load text content:', error)
    attachment.textContent = 'Failed to load content'
  }
}

const openInNewTab = (attachment) => {
  if (attachment.url) {
    window.open(attachment.url, '_blank')
  }
}

</script>