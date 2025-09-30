<template>
  <Teleport to="body">
    <div
      v-if="show"
      class="fixed inset-0 bg-black/75 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4 lg:p-6 z-[9999]"
      @click.self="$emit('close')"
    >
      <div class="bg-gradient-to-br from-white/10 via-white/15 to-white/10 backdrop-blur-xl rounded-2xl border border-white/20 w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.25)]">

        <!-- Header -->
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-4 border-b border-white/20">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-white">📝 Edit Document</h2>
            <button
              @click="$emit('close')"
              class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 transition-colors flex items-center justify-center"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Form Content -->
        <div class="p-6 space-y-6 max-h-[calc(90vh-8rem)] overflow-y-auto">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-white mb-2">Document Title</label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              class="w-full px-4 py-3 rounded-xl bg-black/20 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-300"
              placeholder="Enter document title..."
              :class="{ 'border-red-400/50 focus:border-red-400/50 focus:ring-red-400/20': errors.title }"
            />
            <div v-if="errors.title" class="text-red-400 text-sm mt-1">{{ errors.title }}</div>
          </div>

          <!-- Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-white mb-2">Description (Optional)</label>
            <textarea
              id="description"
              v-model="form.description"
              rows="3"
              class="w-full px-4 py-3 rounded-xl bg-black/20 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-300 resize-vertical"
              placeholder="Enter document description..."
              :class="{ 'border-red-400/50 focus:border-red-400/50 focus:ring-red-400/20': errors.description }"
            ></textarea>
            <div v-if="errors.description" class="text-red-400 text-sm mt-1">{{ errors.description }}</div>
          </div>

          <!-- Company -->
          <div>
            <label for="company" class="block text-sm font-medium text-white mb-2">Company</label>
            <select
              id="company"
              v-model="form.company_id"
              class="w-full px-4 py-3 rounded-xl bg-black/20 backdrop-blur-xl border border-white/20 text-white focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-300"
              :class="{ 'border-red-400/50 focus:border-red-400/50 focus:ring-red-400/20': errors.company_id }"
            >
              <option value="">No Company</option>
              <option v-for="company in companies" :key="company.id" :value="company.id">
                {{ company.name }} ({{ company.ticker }})
              </option>
            </select>
            <div v-if="errors.company_id" class="text-red-400 text-sm mt-1">{{ errors.company_id }}</div>
          </div>

          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-white mb-2">Category</label>
            <select
              id="category"
              v-model="form.category_id"
              class="w-full px-4 py-3 rounded-xl bg-black/20 backdrop-blur-xl border border-white/20 text-white focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-300"
              :class="{ 'border-red-400/50 focus:border-red-400/50 focus:ring-red-400/20': errors.category_id }"
            >
              <option value="">No Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
            <div v-if="errors.category_id" class="text-red-400 text-sm mt-1">{{ errors.category_id }}</div>
          </div>

          <!-- Visibility -->
          <div>
            <label for="visibility" class="block text-sm font-medium text-white mb-2">Visibility</label>
            <select
              id="visibility"
              v-model="form.visibility"
              class="w-full px-4 py-3 rounded-xl bg-black/20 backdrop-blur-xl border border-white/20 text-white focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-300"
              :class="{ 'border-red-400/50 focus:border-red-400/50 focus:ring-red-400/20': errors.visibility }"
            >
              <option value="private">Private</option>
              <option value="team">Team</option>
              <option value="public">Public</option>
            </select>
            <div v-if="errors.visibility" class="text-red-400 text-sm mt-1">{{ errors.visibility }}</div>
          </div>

          <!-- Tags -->
          <div>
            <label class="block text-sm font-medium text-white mb-2">Tags ({{ availableTags.length }} available)</label>
            <div v-if="availableTags.length > 0" class="flex flex-wrap gap-2 max-h-32 overflow-y-auto p-3 bg-black/10 rounded-xl border border-white/10">
              <button
                v-for="tag in availableTags"
                :key="tag.id"
                @click="toggleTag(tag)"
                :class="[
                  'px-3 py-1 rounded-full text-sm font-medium transition-all duration-200 border cursor-pointer',
                  isTagSelected(tag.id)
                    ? 'border-white/40 text-white'
                    : 'border-white/20 text-gray-300 hover:border-white/30 hover:text-white'
                ]"
                :style="{
                  backgroundColor: isTagSelected(tag.id) ? tag.color + '40' : tag.color + '10',
                  color: isTagSelected(tag.id) ? '#ffffff' : tag.color
                }"
              >
                {{ tag.name }} {{ isTagSelected(tag.id) ? '✓' : '' }}
              </button>
            </div>
            <div v-else class="text-gray-400 text-sm">No tags available (tags prop: {{ props.tags?.length || 0 }})</div>
            <div class="text-xs text-gray-500 mt-1">Selected: {{ selectedTagIds.length }} tags</div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gradient-to-r from-gray-900/50 to-gray-800/50 px-6 py-4 border-t border-white/20">
          <div class="flex items-center justify-end space-x-3">
            <button
              @click="$emit('close')"
              class="px-4 py-2 bg-gray-600/20 text-gray-300 border border-gray-500/30 rounded-lg hover:bg-gray-600/30 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="saveDocument"
              :disabled="saving || !form.title?.trim()"
              :class="[
                'px-6 py-2 font-medium rounded-lg transition-colors flex items-center space-x-2',
                saving || !form.title?.trim()
                  ? 'bg-gray-500/20 text-gray-400 border border-gray-400/30 cursor-not-allowed'
                  : 'bg-blue-600/20 text-blue-300 border border-blue-500/30 hover:bg-blue-600/30'
              ]"
            >
              <div v-if="saving" class="w-4 h-4 border-2 border-blue-300/30 border-t-blue-300 rounded-full animate-spin"></div>
              <span>{{ saving ? 'Updating...' : 'Update Document' }}</span>
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
  document: {
    type: Object,
    default: null
  },
  categories: {
    type: Array,
    default: () => []
  },
  companies: {
    type: Array,
    default: () => []
  },
  tags: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'updated'])

// Form state
const form = ref({
  title: '',
  description: '',
  company_id: '',
  category_id: '',
  visibility: 'private'
})

const selectedTagIds = ref([])
const errors = ref({})
const saving = ref(false)

// Available tags computed
const availableTags = computed(() => {
  console.log('Available tags:', props.tags)
  return props.tags || []
})

// Helper functions for tags
const isTagSelected = (tagId) => selectedTagIds.value.includes(tagId)

const toggleTag = (tag) => {
  console.log('Toggling tag:', tag.name, 'Current selected:', selectedTagIds.value)
  if (isTagSelected(tag.id)) {
    selectedTagIds.value = selectedTagIds.value.filter(id => id !== tag.id)
    console.log('Removed tag, new selected:', selectedTagIds.value)
  } else {
    selectedTagIds.value.push(tag.id)
    console.log('Added tag, new selected:', selectedTagIds.value)
  }
}

// Watch for document changes to populate form
watch(() => props.document, (newDocument) => {
  if (newDocument) {
    console.log('Document changed:', newDocument)
    console.log('Document tags:', newDocument.tags)
    form.value = {
      title: newDocument.title || '',
      description: newDocument.description || '',
      company_id: newDocument.company?.id || '',
      category_id: newDocument.category?.id || '',
      visibility: newDocument.visibility || 'private'
    }
    selectedTagIds.value = newDocument.tags?.map(tag => tag.id) || []
    console.log('Selected tag IDs after document load:', selectedTagIds.value)
  }
}, { immediate: true })

// Reset form when modal closes
watch(() => props.show, (show) => {
  if (!show) {
    errors.value = {}
    saving.value = false
  }
})

// Save document
const saveDocument = async () => {
  if (!form.value.title?.trim()) return

  saving.value = true
  errors.value = {}

  try {
    const payload = {
      ...form.value,
      tag_ids: selectedTagIds.value
    }

    const response = await axios.put(`/api/documents/${props.document.id}`, payload)

    emit('updated', response.data)
    emit('close')
  } catch (error) {
    console.error('Error updating document:', error)

    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value = {
        general: error.response?.data?.message || 'An error occurred while updating the document.'
      }
    }
  } finally {
    saving.value = false
  }
}
</script>