<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-hidden" @click.self="close">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black/75 backdrop-blur-sm transition-all duration-300"></div>

    <!-- Modal container -->
    <div class="fixed inset-0 flex items-center justify-center p-4">
      <div class="relative w-full max-w-5xl xl:max-w-6xl max-h-[90vh] mx-auto">
        <!-- Glass morphism modal -->
        <div class="backdrop-blur-3xl bg-gradient-to-br from-gray-900/95 via-gray-800/90 to-gray-900/95 rounded-3xl shadow-[0_20px_40px_0_rgba(0,0,0,0.3)] border border-white/20 overflow-hidden">

          <!-- Header -->
          <div class="flex items-center justify-between p-6 border-b border-white/10">
            <div class="flex-1">
              <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <h2 class="text-2xl font-bold text-white">{{ researchNote?.title || 'Research Note' }}</h2>
                  <div class="flex items-center space-x-4 text-sm text-gray-300 mt-1">
                    <span v-if="researchNote?.company">
                      <span class="text-blue-400">{{ researchNote.company.name }}</span>
                      <span class="text-gray-500 ml-1">({{ researchNote.company.ticker }})</span>
                    </span>
                    <span v-if="researchNote?.user" class="flex items-center space-x-1">
                      <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                      <span class="text-green-400">{{ researchNote.user.name }}</span>
                    </span>
                    <span v-if="researchNote?.category" class="flex items-center space-x-1">
                      <div class="w-2 h-2 rounded-full bg-purple-400"></div>
                      <span>{{ researchNote.category.name }}</span>
                    </span>
                    <span v-if="researchNote?.created_at">{{ formatDate(researchNote.created_at) }}</span>
                    <span v-if="researchNote?.updated_at && researchNote.updated_at !== researchNote.created_at" class="flex items-center space-x-1">
                      <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                      <span class="text-yellow-400">Edited {{ formatDate(researchNote.updated_at) }}</span>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Close button -->
            <button
              @click="close"
              class="group relative p-2 transition-all duration-300 hover:scale-110 bg-gradient-to-br from-white/10 via-white/5 to-transparent text-gray-300 hover:text-white rounded-xl shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)] border border-white/20 backdrop-blur-xl"
              style="backdrop-filter: blur(20px) saturate(150%);"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Tabs -->
          <div class="border-b border-white/10 px-6">
            <nav class="flex space-x-8">
              <button
                @click="activeTab = 'content'"
                :class="[
                  'py-3 px-1 border-b-2 font-medium text-sm transition-colors',
                  activeTab === 'content'
                    ? 'border-blue-500 text-blue-400'
                    : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'
                ]"
              >
                Content
              </button>
              <button
                @click="activeTab = 'activity'"
                :class="[
                  'py-3 px-1 border-b-2 font-medium text-sm transition-colors',
                  activeTab === 'activity'
                    ? 'border-blue-500 text-blue-400'
                    : 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300'
                ]"
              >
                Activity History
              </button>
            </nav>
          </div>

          <!-- Content -->
          <div class="flex-1 overflow-y-auto p-6 max-h-[calc(90vh-260px)]">
            <!-- Content Tab -->
            <div v-if="researchNote && activeTab === 'content'" class="space-y-6">

              <!-- Tags -->
              <div v-if="researchNote.tags && researchNote.tags.length > 0" class="flex flex-wrap gap-2">
                <span
                  v-for="tag in researchNote.tags"
                  :key="tag.id"
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium backdrop-blur-xl border border-white/20"
                  :style="{
                    backgroundColor: tag.color + '20',
                    color: tag.color,
                    borderColor: tag.color + '40'
                  }"
                >
                  {{ tag.name }}
                </span>
              </div>

              <!-- Main Content -->
              <div class="prose prose-invert prose-lg max-w-none">
                <div v-if="researchNote.content" class="text-gray-100 leading-relaxed">
                  <!-- Render markdown content -->
                  <MdPreview
                    v-model="researchNote.content"
                    :theme="'dark'"
                    :preview-theme="'github'"
                    :code-theme="'github'"
                    class="!bg-transparent"
                  />
                </div>
                <div v-else class="text-gray-400 italic">
                  No content available for this research note.
                </div>
              </div>

              <!-- Attachments -->
              <div v-if="researchNote.attachments && researchNote.attachments.length > 0" class="space-y-4">
                <h3 class="text-lg font-semibold text-white flex items-center space-x-2">
                  <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                  </svg>
                  <span>Attachments</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <a
                    v-for="attachment in researchNote.attachments"
                    :key="attachment.id"
                    :href="attachment.url"
                    target="_blank"
                    class="group flex items-center space-x-3 p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300"
                  >
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-white font-medium group-hover:text-blue-300 transition-colors truncate">
                        {{ attachment.name }}
                      </p>
                      <p class="text-sm text-gray-400">
                        {{ attachment.mime_type }} • {{ formatFileSize(attachment.size) }}
                      </p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                  </a>
                </div>
              </div>

              <!-- Metadata -->
              <div class="border-t border-white/10 pt-6 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                  <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span class="text-gray-400">Visibility:</span>
                    <span class="text-white capitalize">{{ researchNote.visibility }}</span>
                  </div>
                  <div v-if="researchNote.updated_at" class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span class="text-gray-400">Updated:</span>
                    <span class="text-white">{{ formatDate(researchNote.updated_at) }}</span>
                  </div>
                  <div v-if="researchNote.user" class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-gray-400">Author:</span>
                    <span class="text-white">{{ researchNote.user.name }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Activity Tab -->
            <div v-else-if="researchNote && activeTab === 'activity'">
              <ActivityTimeline
                model-type="ResearchItem"
                :model-id="researchNote.id"
                :show-model-links="false"
              />
            </div>

            <!-- Loading state -->
            <div v-else-if="loading" class="flex items-center justify-center py-12">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
            </div>

            <!-- Error state -->
            <div v-else class="text-center py-12">
              <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-medium text-white mb-2">Research Note Not Found</h3>
              <p class="text-gray-400">The research note could not be loaded.</p>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="border-t border-white/10 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center space-x-3">
                <button
                  v-if="researchNote?.company"
                  @click="viewCompany"
                  class="group relative px-4 py-2 transition-all duration-300 hover:scale-105 bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 hover:text-white rounded-xl shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/20 backdrop-blur-xl font-medium"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                >
                  View Company Details
                </button>
              </div>

              <div class="flex items-center space-x-3">
                <button
                  @click="close"
                  class="group relative px-6 py-2 transition-all duration-300 hover:scale-105 bg-gradient-to-br from-white/10 via-white/5 to-transparent text-gray-300 hover:text-white rounded-xl shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)] border border-white/20 backdrop-blur-xl font-medium"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { MdPreview } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'
import ActivityTimeline from '../ActivityTimeline.vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  researchNoteId: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['close', 'view-company'])

const researchNote = ref(null)
const loading = ref(false)
const activeTab = ref('content')

// Watch for changes to show/researchNoteId to load data
watch([() => props.show, () => props.researchNoteId], async ([show, noteId]) => {
  if (show && noteId) {
    await loadResearchNote(noteId)
  }
}, { immediate: true })

const loadResearchNote = async (noteId) => {
  if (!noteId) return

  loading.value = true
  try {
    const response = await axios.get(`/api/research-items/${noteId}`)
    researchNote.value = response.data
  } catch (error) {
    console.error('Error loading research note:', error)
    researchNote.value = null
  } finally {
    loading.value = false
  }
}

const close = () => {
  emit('close')
  // Reset state when closing
  researchNote.value = null
  activeTab.value = 'content'
}

const viewCompany = () => {
  if (researchNote.value?.company) {
    emit('view-company', researchNote.value.company)
  }
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
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
</script>

<style scoped>
/* Custom styles for markdown content */
:deep(.md-editor-preview) {
  background: transparent !important;
  color: #f3f4f6 !important;
}

:deep(.md-editor-preview h1),
:deep(.md-editor-preview h2),
:deep(.md-editor-preview h3),
:deep(.md-editor-preview h4),
:deep(.md-editor-preview h5),
:deep(.md-editor-preview h6) {
  color: #ffffff !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
}

:deep(.md-editor-preview blockquote) {
  border-left: 4px solid #3b82f6 !important;
  background: rgba(59, 130, 246, 0.1) !important;
}

:deep(.md-editor-preview code) {
  background: rgba(255, 255, 255, 0.1) !important;
  color: #e5e7eb !important;
}

:deep(.md-editor-preview pre) {
  background: rgba(0, 0, 0, 0.3) !important;
  border: 1px solid rgba(255, 255, 255, 0.1) !important;
}
</style>