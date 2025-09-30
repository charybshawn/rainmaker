<template>
  <!-- Full-Screen Mobile Menu (TOPMOST ELEMENT) -->
  <div
    v-if="mobileMenuOpen"
    class="fixed inset-0 z-[9999] bg-slate-900 md:hidden"
    @click="mobileMenuOpen = false"
  >
    <div class="flex flex-col h-full">
      <!-- Header with Close Button -->
      <div class="flex items-center justify-between p-6">
        <h2 class="text-2xl font-bold text-white">Navigation</h2>
        <button
          @click="mobileMenuOpen = false"
          class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all duration-200"
          aria-label="Close navigation menu"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Navigation Items -->
      <div class="flex-1 flex flex-col justify-center px-6 space-y-6">
        <!-- Content Section -->
        <button
          @click="setActiveSection('content')"
          :class="[
            'w-full flex items-center justify-between py-6 px-6 text-xl font-semibold rounded-2xl transition-all duration-200 backdrop-blur-xl border border-white/10',
            activeSection === 'content'
              ? 'bg-blue-500/20 text-blue-200 border-blue-400/30'
              : 'bg-white/10 hover:bg-white/20 text-white'
          ]"
        >
          <div class="flex items-center">
            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research Content
          </div>
          <svg v-if="activeSection === 'content'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </button>

        <!-- Assets Section (only show if there are attachments) -->
        <button
          v-if="researchNote.attachments && researchNote.attachments.length > 0"
          @click="setActiveSection('assets')"
          :class="[
            'w-full flex items-center justify-between py-6 px-6 text-xl font-semibold rounded-2xl transition-all duration-200 backdrop-blur-xl border border-white/10',
            activeSection === 'assets'
              ? 'bg-emerald-500/20 text-emerald-200 border-emerald-400/30'
              : 'bg-white/10 hover:bg-white/20 text-white'
          ]"
        >
          <div class="flex items-center">
            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
            <span>Assets</span>
            <span class="ml-2 bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full text-sm">
              {{ researchNote.attachments.length }}
            </span>
          </div>
          <svg v-if="activeSection === 'assets'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </button>

        <!-- Activity Section -->
        <button
          @click="setActiveSection('activity')"
          :class="[
            'w-full flex items-center justify-between py-6 px-6 text-xl font-semibold rounded-2xl transition-all duration-200 backdrop-blur-xl border border-white/10',
            activeSection === 'activity'
              ? 'bg-amber-500/20 text-amber-200 border-amber-400/30'
              : 'bg-white/10 hover:bg-white/20 text-white'
          ]"
        >
          <div class="flex items-center">
            <svg class="w-8 h-8 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Activity History
          </div>
          <svg v-if="activeSection === 'activity'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Main Modal -->
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-[9999] overflow-y-auto" @click.self="$emit('close')">
      <div class="bg-gradient-to-br from-slate-800/95 via-slate-700/95 to-slate-800/95 backdrop-blur-2xl rounded-3xl w-full h-auto max-h-[90vh] sm:w-[85%] sm:max-w-6xl overflow-hidden shadow-[0_20px_60px_-12px_rgba(0,0,0,0.8)] border border-white/20 flex flex-col">
        <!-- Vertical Layout with Header -->
        <div class="flex flex-col h-full max-h-full">
          <!-- Top Header Section -->
          <div class="bg-slate-800/50 backdrop-blur-sm border-b border-white/10">
            <!-- Edit button -->
            <button
              @click="$emit('edit', researchNote)"
              class="absolute top-4 right-16 z-30 p-2 hover:bg-blue-500/20 bg-slate-800/60 rounded-full transition-all group border border-white/20"
              aria-label="Edit research note"
            >
              <svg class="w-5 h-5 text-white/70 group-hover:text-blue-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>

            <!-- Close button -->
            <button
              @click="$emit('close')"
              class="absolute top-4 right-4 z-30 p-2 hover:bg-white/20 bg-slate-800/60 rounded-full transition-all group border border-white/20"
              aria-label="Close modal"
            >
              <svg class="w-5 h-5 text-white/70 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>

            <div class="p-4 pb-0">
            <!-- Hamburger Menu Button (Mobile Only) -->
            <div class="md:hidden mb-4">
              <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="p-2 bg-white/5 backdrop-blur-xl rounded-lg border border-white/10 transition-all duration-300 hover:bg-white/10"
              >
                <svg
                  :class="['w-6 h-6 text-white/80 transition-all duration-300']"
                  fill="none" stroke="currentColor" viewBox="0 0 24 24"
                >
                  <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                  <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>

            <!-- Title Row -->
            <div class="mb-3">
              <h1 class="text-xl font-bold text-white mb-1 leading-tight">{{ researchNote?.title || 'Research Note' }}</h1>
            </div>

            <!-- Meta Row -->
            <div class="mb-3">
              <div class="flex-1 min-w-0">

                <!-- Meta info row -->
                <div class="flex flex-wrap items-center gap-4 mb-3">
                  <div v-if="researchNote?.user" class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-green-400/20 to-emerald-400/20 flex items-center justify-center">
                      <svg class="w-3 h-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <span class="text-sm text-white font-medium">{{ researchNote.user.name }}</span>
                  </div>

                  <div v-if="researchNote?.category" class="bg-blue-400/15 text-blue-300 px-2 py-1 rounded text-xs font-medium">
                    {{ researchNote.category.name }}
                  </div>

                  <div v-if="researchNote?.visibility" class="bg-purple-400/15 text-purple-300 px-2 py-1 rounded text-xs font-medium capitalize">
                    {{ researchNote.visibility }}
                  </div>
                </div>

                <!-- Tags Row -->
                <div v-if="researchNote?.tags && researchNote.tags.length > 0" class="flex flex-wrap gap-2 mb-3">
                  <span
                    v-for="tag in researchNote.tags"
                    :key="tag.id"
                    class="inline-block px-2 py-1 rounded text-xs font-medium"
                    :style="{
                      backgroundColor: tag.color + '20',
                      color: tag.color
                    }"
                  >
                    {{ tag.name }}
                  </span>
                </div>

                <!-- Timestamps -->
                <div class="text-xs text-white/50 space-x-4 mb-6">
                  <span v-if="researchNote?.created_at">Created {{ formatDate(researchNote.created_at) }}</span>
                  <span v-if="researchNote?.updated_at && researchNote.updated_at !== researchNote.created_at">
                    Updated {{ formatDate(researchNote.updated_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Navigation Section -->
          <div class="relative">
            <!-- Desktop Tab Navigation (hidden on mobile) -->
            <div class="hidden md:block">
              <!-- Tab buttons with bottom border integration -->
              <div class="flex px-6 space-x-1">
                <!-- Content Tab -->
                <button
                  @click="activeSection = 'content'"
                  :class="[
                    'flex items-center gap-2 px-6 py-3 text-sm font-medium transition-all duration-300 relative',
                    activeSection === 'content'
                      ? 'text-blue-200'
                      : 'text-white/60 hover:text-white/90'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  Research Content
                  <!-- Active indicator -->
                  <div
                    v-if="activeSection === 'content'"
                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400"
                  ></div>
                </button>

                <!-- Assets Tab (only show if there are attachments) -->
                <button
                  v-if="researchNote.attachments && researchNote.attachments.length > 0"
                  @click="activeSection = 'assets'"
                  :class="[
                    'flex items-center gap-2 px-6 py-3 text-sm font-medium transition-all duration-300 relative',
                    activeSection === 'assets'
                      ? 'text-emerald-200'
                      : 'text-white/60 hover:text-white/90'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                  </svg>
                  Assets
                  <span class="bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full text-xs font-medium min-w-[20px] text-center">
                    {{ researchNote.attachments.length }}
                  </span>
                  <!-- Active indicator -->
                  <div
                    v-if="activeSection === 'assets'"
                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-emerald-400 to-green-400"
                  ></div>
                </button>

                <!-- Activity Tab -->
                <button
                  @click="activeSection = 'activity'"
                  :class="[
                    'flex items-center gap-2 px-6 py-3 text-sm font-medium transition-all duration-300 relative',
                    activeSection === 'activity'
                      ? 'text-amber-200'
                      : 'text-white/60 hover:text-white/90'
                  ]"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Activity History
                  <!-- Active indicator -->
                  <div
                    v-if="activeSection === 'activity'"
                    class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-amber-400 to-orange-400"
                  ></div>
                </button>
              </div>
            </div>



            <!-- Bottom border for the entire navigation area -->
            <div class="border-b border-white/10"></div>
          </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto bg-slate-900/30 min-h-0">
          <div class="p-6">
            <!-- Content Section -->
            <div v-if="activeSection === 'content'">
              <div v-if="researchNote.content" class="prose prose-invert max-w-none">
                <div
                  class="research-content-display blog-style-content text-gray-100"
                  v-html="researchNote.content"
                ></div>
              </div>
              <div v-else class="text-center py-20 opacity-60">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-400/10 to-indigo-500/10 flex items-center justify-center">
                  <svg class="w-10 h-10 text-blue-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <h3 class="text-xl font-medium text-white mb-3">No Content Available</h3>
                <p class="text-gray-400">This research note doesn't have any content yet.</p>
              </div>
            </div>

            <!-- Assets Section -->
            <div v-if="activeSection === 'assets'" class="space-y-4">
              <div
                v-for="attachment in researchNote.attachments"
                :key="attachment.id"
                class="group"
              >
                <a
                  :href="attachment.url"
                  target="_blank"
                  class="flex items-center gap-4 p-4 rounded-xl bg-white/3 hover:bg-white/8 border border-white/10 hover:border-emerald-400/30 transition-all duration-300 hover:scale-[1.01]"
                >
                  <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400/15 to-green-500/15 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-white font-medium group-hover:text-emerald-200 transition-colors truncate text-lg">
                      {{ attachment.name }}
                    </p>
                    <p class="text-sm text-gray-400 group-hover:text-gray-300 transition-colors">
                      {{ attachment.mime_type }} • {{ formatFileSize(attachment.size) }}
                    </p>
                  </div>
                  <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-emerald-400/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400 group-hover:text-emerald-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                  </div>
                </a>
              </div>
            </div>

            <!-- Activity Section -->
            <div v-if="activeSection === 'activity'">
              <ActivityTimeline
                model-type="ResearchItem"
                :model-id="researchNote.id"
                :show-model-links="false"
              />
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
import ActivityTimeline from '../ActivityTimeline.vue'

// Props
const props = defineProps({
  researchNote: {
    type: [Object, null],
    default: null
  },
  show: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['close', 'viewCompany', 'edit'])

// Active section management for split layout
const activeSection = ref('content')

// Mobile menu state
const mobileMenuOpen = ref(false)

// Helper function to set active section and close mobile menu
const setActiveSection = (section) => {
  activeSection.value = section
  mobileMenuOpen.value = false
}

// Legacy support for old collapsible sections (not used in new layout)
const collapsedSections = ref({
  company: false,
  tags: false,
  content: false,
  attachments: false,
  activity: true
})

const toggleSection = (section) => {
  collapsedSections.value[section] = !collapsedSections.value[section]
}

// Clean tab management without complex positioning

// Format date helper
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Format file size helper
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

// Format currency helper function
const formatCurrency = (amount) => {
  if (!amount) return 'N/A'
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    notation: 'compact',
    maximumFractionDigits: 1
  }).format(amount)
}
</script>

<style scoped>
/* Professional blog-style content display */
:deep(.blog-style-content) {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  line-height: 1.8;
  color: #ffffff;
}

/* Enhanced typography for blog content */
:deep(.blog-style-content h1) {
  font-size: 2.25rem;
  font-weight: 700;
  color: #ffffff;
  margin: 2rem 0 1rem 0;
  border-bottom: 2px solid rgba(59, 130, 246, 0.3);
  padding-bottom: 0.5rem;
}

:deep(.blog-style-content h2) {
  font-size: 1.875rem;
  font-weight: 600;
  color: #ffffff;
  margin: 1.75rem 0 0.75rem 0;
  position: relative;
}

:deep(.blog-style-content h2::before) {
  content: '';
  position: absolute;
  left: -1rem;
  top: 50%;
  transform: translateY(-50%);
  width: 4px;
  height: 1.5rem;
  background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
  border-radius: 2px;
}

:deep(.blog-style-content h3) {
  font-size: 1.5rem;
  font-weight: 600;
  color: #e5e7eb;
  margin: 1.5rem 0 0.5rem 0;
}

:deep(.blog-style-content h4) {
  font-size: 1.25rem;
  font-weight: 500;
  color: #d1d5db;
  margin: 1.25rem 0 0.5rem 0;
}

:deep(.blog-style-content p) {
  color: rgba(255, 255, 255, 0.9);
  margin: 1rem 0;
  line-height: 1.8;
  font-size: 1rem;
}

:deep(.blog-style-content strong) {
  color: #ffffff;
  font-weight: 600;
}

:deep(.blog-style-content em) {
  color: #e5e7eb;
  font-style: italic;
}

/* Enhanced lists */
:deep(.blog-style-content ul) {
  margin: 1rem 0;
  padding-left: 1.5rem;
}

:deep(.blog-style-content li) {
  color: rgba(255, 255, 255, 0.9);
  margin: 0.5rem 0;
  position: relative;
}

:deep(.blog-style-content ul li::marker) {
  color: #3b82f6;
}

:deep(.blog-style-content ol li::marker) {
  color: #3b82f6;
  font-weight: 600;
}

/* Enhanced code blocks */
:deep(.blog-style-content code) {
  background: rgba(59, 130, 246, 0.2);
  color: #60a5fa;
  padding: 0.25rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

:deep(.blog-style-content pre) {
  background: rgba(0, 0, 0, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 0.75rem;
  padding: 1.5rem;
  margin: 1.5rem 0;
  overflow-x: auto;
  backdrop-filter: blur(10px);
}

:deep(.blog-style-content pre code) {
  background: transparent;
  border: none;
  padding: 0;
  color: #e5e7eb;
}

/* Enhanced blockquotes */
:deep(.blog-style-content blockquote) {
  border-left: 4px solid #3b82f6;
  background: rgba(59, 130, 246, 0.1);
  margin: 1.5rem 0;
  padding: 1.25rem 1.5rem;
  border-radius: 0 0.75rem 0.75rem 0;
  position: relative;
  backdrop-filter: blur(10px);
}

:deep(.blog-style-content blockquote::before) {
  content: '"';
  position: absolute;
  top: -0.5rem;
  left: 1rem;
  font-size: 3rem;
  color: #3b82f6;
  font-weight: 700;
  line-height: 1;
}

:deep(.blog-style-content blockquote p) {
  color: #e5e7eb;
  font-style: italic;
  margin: 0;
  font-size: 1.125rem;
}

/* Enhanced tables */
:deep(.blog-style-content table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1.5rem 0;
  background: rgba(0, 0, 0, 0.2);
  border-radius: 0.75rem;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

:deep(.blog-style-content th) {
  background: rgba(59, 130, 246, 0.2);
  color: #ffffff;
  font-weight: 600;
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

:deep(.blog-style-content td) {
  padding: 0.875rem 1rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  color: rgba(255, 255, 255, 0.9);
}

:deep(.blog-style-content tr:last-child td) {
  border-bottom: none;
}

:deep(.blog-style-content tr:hover) {
  background: rgba(255, 255, 255, 0.05);
}

/* Enhanced links */
:deep(.blog-style-content a) {
  color: #60a5fa;
  text-decoration: none;
  border-bottom: 1px solid rgba(96, 165, 250, 0.3);
  transition: all 0.3s ease;
}

:deep(.blog-style-content a:hover) {
  color: #93c5fd;
  border-bottom-color: #93c5fd;
}

/* Horizontal rules */
:deep(.blog-style-content hr) {
  border: none;
  height: 2px;
  background: linear-gradient(to right, transparent, rgba(59, 130, 246, 0.5), transparent);
  margin: 2rem 0;
}

/* Image styling */
:deep(.blog-style-content img) {
  border-radius: 0.75rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
  margin: 1.5rem 0;
}

/* Task lists */
:deep(.blog-style-content input[type="checkbox"]) {
  accent-color: #3b82f6;
  margin-right: 0.5rem;
}

/* Scroll styling for content */
.research-content-display {
  max-height: 70vh;
  overflow-y: auto;
  padding-right: 0.5rem;
}

.research-content-display::-webkit-scrollbar {
  width: 8px;
}

.research-content-display::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

.research-content-display::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}

.research-content-display::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>