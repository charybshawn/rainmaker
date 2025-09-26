<template>
  <div v-if="show" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-2 sm:p-4 z-[60]" @click.self="$emit('close')">
    <div class="bg-gradient-to-br from-white/10 via-white/15 to-white/10 backdrop-blur-xl rounded-2xl border border-white/20 w-full lg:w-[66%] max-w-6xl h-full overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] flex flex-col">
      <!-- Header -->
      <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-4 sm:px-6 lg:px-8 py-4 sm:py-6 border-b border-white/20">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3 sm:space-x-4">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center backdrop-blur-xl">
              <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
            </div>
            <div class="min-w-0 flex-1">
              <h2 class="text-lg sm:text-xl lg:text-2xl font-bold text-white truncate">{{ researchNote?.title || 'Research Note' }}</h2>
              <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-2">
                <span v-if="researchNote?.category" class="inline-flex items-center px-2 py-1 bg-blue-500/20 text-blue-300 rounded-md text-xs">
                  {{ researchNote.category.name }}
                </span>
                <span v-if="researchNote?.visibility" class="inline-flex items-center px-2 py-1 bg-purple-500/20 text-purple-300 rounded-md text-xs capitalize">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  {{ researchNote.visibility }}
                </span>
                <span v-if="researchNote?.user" class="inline-flex items-center px-2 py-1 bg-green-500/20 text-green-300 rounded-md text-xs">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  {{ researchNote.user.name }}
                </span>
              </div>
              <div class="flex flex-wrap items-center gap-2 sm:gap-4 mt-1 text-xs text-gray-400">
                <span v-if="researchNote?.created_at" class="flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Created {{ formatDate(researchNote.created_at) }}
                </span>
                <span v-if="researchNote?.updated_at && researchNote.updated_at !== researchNote.created_at" class="flex items-center">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Updated {{ formatDate(researchNote.updated_at) }}
                </span>
              </div>
            </div>
          </div>
          <button
            @click="$emit('close')"
            class="p-2 hover:bg-white/10 rounded-xl transition-colors group flex-shrink-0"
          >
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-auto p-4 sm:p-6 lg:p-8 space-y-6">
        <!-- Company Info Section -->
        <div v-if="researchNote.company" class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
          <button
            @click="toggleSection('company')"
            class="w-full flex items-center justify-between text-left group hover:bg-white/5 -m-2 p-2 rounded-lg transition-colors"
          >
            <div class="flex items-center space-x-3 sm:space-x-4">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-green-500/20 to-blue-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="text-base sm:text-lg font-semibold text-white truncate">{{ researchNote.company.name }}</h3>
                <p class="text-sm text-blue-300">${{ researchNote.company.ticker }}</p>
              </div>
            </div>
            <svg
              :class="['w-5 h-5 text-gray-400 transition-transform', collapsedSections.company ? 'rotate-0' : 'rotate-90']"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <div v-if="!collapsedSections.company" class="mt-4 pl-14 sm:pl-16 space-y-2">
            <div v-if="researchNote.company.description" class="text-sm text-gray-300">
              {{ researchNote.company.description }}
            </div>
            <div class="flex flex-wrap gap-4 text-xs text-gray-400">
              <span v-if="researchNote.company.sector">{{ researchNote.company.sector }}</span>
              <span v-if="researchNote.company.market_cap">Market Cap: {{ formatCurrency(researchNote.company.market_cap) }}</span>
            </div>
          </div>
        </div>

        <!-- Tags Section -->
        <div v-if="researchNote.tags && researchNote.tags.length > 0" class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
          <button
            @click="toggleSection('tags')"
            class="w-full flex items-center justify-between text-left group hover:bg-white/5 -m-2 p-2 rounded-lg transition-colors"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-base sm:text-lg font-semibold text-white">Tags</h3>
                <p class="text-sm text-gray-400">{{ researchNote.tags.length }} tag{{ researchNote.tags.length !== 1 ? 's' : '' }}</p>
              </div>
            </div>
            <svg
              :class="['w-5 h-5 text-gray-400 transition-transform', collapsedSections.tags ? 'rotate-0' : 'rotate-90']"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <div v-if="!collapsedSections.tags" class="mt-4 pl-14 sm:pl-16">
            <div class="flex flex-wrap gap-2">
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
          </div>
        </div>

        <!-- Content Section -->
        <div class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
          <button
            @click="toggleSection('content')"
            class="w-full flex items-center justify-between text-left group hover:bg-white/5 -m-2 p-2 rounded-lg transition-colors"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-blue-500/20 to-cyan-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-base sm:text-lg font-semibold text-white">Content</h3>
                <p class="text-sm text-gray-400">Research details and analysis</p>
              </div>
            </div>
            <svg
              :class="['w-5 h-5 text-gray-400 transition-transform', collapsedSections.content ? 'rotate-0' : 'rotate-90']"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <div v-if="!collapsedSections.content" class="mt-4">
            <div class="prose prose-invert prose-sm sm:prose-base max-w-none">
              <div v-if="researchNote.content" class="text-gray-100 leading-relaxed">
                <MdPreview
                  v-model="researchNote.content"
                  :theme="'dark'"
                  :preview-theme="'github'"
                  :code-theme="'github'"
                  class="!bg-transparent"
                />
              </div>
              <div v-else class="text-gray-400 italic text-center py-8">
                No content available for this research note.
              </div>
            </div>
          </div>
        </div>

        <!-- Research Assets Section -->
        <div v-if="researchNote.attachments && researchNote.attachments.length > 0" class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
          <button
            @click="toggleSection('attachments')"
            class="w-full flex items-center justify-between text-left group hover:bg-white/5 -m-2 p-2 rounded-lg transition-colors"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-green-500/20 to-emerald-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-base sm:text-lg font-semibold text-white">Research Assets</h3>
                <p class="text-sm text-gray-400">{{ researchNote.attachments.length }} file{{ researchNote.attachments.length !== 1 ? 's' : '' }}</p>
              </div>
            </div>
            <svg
              :class="['w-5 h-5 text-gray-400 transition-transform', collapsedSections.attachments ? 'rotate-0' : 'rotate-90']"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <div v-if="!collapsedSections.attachments" class="mt-4 pl-14 sm:pl-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
              <a
                v-for="attachment in researchNote.attachments"
                :key="attachment.id"
                :href="attachment.url"
                target="_blank"
                class="group flex items-center space-x-3 p-3 sm:p-4 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-300"
              >
                <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-purple-500/20 flex items-center justify-center">
                  <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-white font-medium group-hover:text-blue-300 transition-colors truncate text-sm sm:text-base">
                    {{ attachment.name }}
                  </p>
                  <p class="text-xs sm:text-sm text-gray-400">
                    {{ attachment.mime_type }} • {{ formatFileSize(attachment.size) }}
                  </p>
                </div>
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-400 group-hover:text-blue-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
              </a>
            </div>
          </div>
        </div>

        <!-- Activity Section -->
        <div class="bg-white/5 rounded-xl p-4 sm:p-6 border border-white/10">
          <button
            @click="toggleSection('activity')"
            class="w-full flex items-center justify-between text-left group hover:bg-white/5 -m-2 p-2 rounded-lg transition-colors"
          >
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-yellow-500/20 to-orange-500/20 flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-base sm:text-lg font-semibold text-white">Activity History</h3>
                <p class="text-sm text-gray-400">Recent changes and updates</p>
              </div>
            </div>
            <svg
              :class="['w-5 h-5 text-gray-400 transition-transform', collapsedSections.activity ? 'rotate-0' : 'rotate-90']"
              fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </button>
          <div v-if="!collapsedSections.activity" class="mt-4">
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
</template>

<script setup>
import { ref } from 'vue'
import { MdPreview } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'
import ActivityTimeline from '../ActivityTimeline.vue'

// Props
defineProps({
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
defineEmits(['close'])

// Collapsible sections management
const collapsedSections = ref({
  company: false,
  tags: false,
  content: false,
  attachments: false,
  activity: true // Start collapsed
})

const toggleSection = (section) => {
  collapsedSections.value[section] = !collapsedSections.value[section]
}

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