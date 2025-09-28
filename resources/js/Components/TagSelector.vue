<template>
  <div class="space-y-3">
    <!-- Selected Tags Display -->
    <div v-if="selectedTags.length > 0" class="flex flex-wrap gap-2">
      <TagBadge
        v-for="tag in selectedTags"
        :key="tag.id"
        :tag="tag"
        :removable="true"
        @remove="removeTag"
      />
    </div>

    <!-- Tag Input/Selector -->
    <div class="relative">
      <div class="relative">
        <input
          ref="input"
          v-model="searchQuery"
          @focus="showDropdown = true"
          @keydown.enter.prevent="handleEnter"
          @keydown.escape="showDropdown = false"
          @keydown.arrow-down.prevent="navigateDown"
          @keydown.arrow-up.prevent="navigateUp"
          type="text"
          class="w-full px-4 py-3 rounded-xl backdrop-blur-xl bg-white/10 border border-white/20 text-white placeholder-white/50 focus:bg-white/15 focus:border-white/40 focus:ring-2 focus:ring-white/25 transition-all duration-300"
          :placeholder="placeholder"
        />
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
          <svg class="w-4 h-4 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
      </div>

      <!-- Dropdown -->
      <div
        v-show="showDropdown"
        v-click-outside="closeDropdown"
        class="absolute z-50 w-full mt-1 backdrop-blur-xl bg-gray-800 border border-white/30 rounded-xl shadow-xl max-h-60 overflow-y-auto"
      >
        <!-- Loading State -->
        <div v-if="loading" class="px-3 py-2 text-sm text-gray-300">
          Loading tags...
        </div>

        <!-- No Results -->
        <div v-else-if="filteredTags.length === 0 && searchQuery" class="px-3 py-2 text-sm text-gray-300">
          No tags found. Press Enter to create "{{ searchQuery }}"
        </div>

        <!-- Tag Options -->
        <template v-else>
          <!-- Create New Tag Option -->
          <div
            v-if="searchQuery && !exactMatch && canCreateNew"
            :class="[
              'px-3 py-2 cursor-pointer flex items-center text-sm',
              highlightedIndex === -1 ? 'bg-blue-500/20 text-blue-300' : 'text-gray-300 hover:bg-white/10'
            ]"
            @click="createNewTag"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create "{{ searchQuery }}"
          </div>

          <!-- Existing Tags -->
          <div
            v-for="(tag, index) in filteredTags"
            :key="tag.id"
            :class="[
              'px-3 py-2 cursor-pointer flex items-center justify-between text-sm',
              highlightedIndex === index ? 'bg-blue-500/20 text-blue-300' : 'text-gray-300 hover:bg-white/10',
              isSelected(tag) ? 'opacity-50' : ''
            ]"
            @click="selectTag(tag)"
          >
            <div class="flex items-center">
              <div
                class="w-3 h-3 rounded-full mr-2"
                :style="{ backgroundColor: tag.color }"
              ></div>
              <span>{{ tag.name }}</span>
            </div>
            <svg v-if="isSelected(tag)" class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </template>
      </div>
    </div>

    <!-- Help Text -->
    <p v-if="helpText" class="text-sm text-gray-300">{{ helpText }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import TagBadge from './TagBadge.vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Search or create tags...'
  },
  helpText: {
    type: String,
    default: null
  },
  canCreateNew: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['update:modelValue'])

// Reactive data
const searchQuery = ref('')
const showDropdown = ref(false)
const loading = ref(false)
const availableTags = ref([])
const selectedTags = ref([])
const highlightedIndex = ref(-1)
const input = ref(null)

// Computed properties
const filteredTags = computed(() => {
  if (!searchQuery.value) return availableTags.value

  return availableTags.value.filter(tag =>
    tag.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const exactMatch = computed(() => {
  return filteredTags.value.some(tag =>
    tag.name.toLowerCase() === searchQuery.value.toLowerCase()
  )
})

// Watchers
watch(() => props.modelValue, (newValue) => {
  selectedTags.value = newValue || []
}, { immediate: true })

watch(selectedTags, (newValue) => {
  emit('update:modelValue', newValue)
}, { deep: true })

// Methods
const fetchTags = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/tags')
    const data = await response.json()
    availableTags.value = data
  } catch (error) {
    console.error('Error fetching tags:', error)
  } finally {
    loading.value = false
  }
}

const selectTag = (tag) => {
  if (isSelected(tag)) return

  selectedTags.value.push(tag)
  searchQuery.value = ''
  showDropdown.value = false
  highlightedIndex.value = -1

  // Re-focus the input after a short delay to allow for another tag selection
  nextTick(() => {
    if (input.value) {
      input.value.focus()
    }
  })
}

const removeTag = (tagId) => {
  selectedTags.value = selectedTags.value.filter(tag => tag.id !== tagId)
}

const isSelected = (tag) => {
  return selectedTags.value.some(selected => selected.id === tag.id)
}

const createNewTag = async () => {
  if (!searchQuery.value.trim()) return

  loading.value = true
  try {
    const response = await fetch('/api/tags', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({
        name: searchQuery.value.trim()
      })
    })

    if (response.ok) {
      const newTag = await response.json()
      availableTags.value.push(newTag)

      // Add the new tag to selected tags
      selectedTags.value.push(newTag)
      searchQuery.value = ''
      showDropdown.value = false
      highlightedIndex.value = -1

      // Re-focus the input for another tag selection
      nextTick(() => {
        if (input.value) {
          input.value.focus()
        }
      })
    } else {
      console.error('Error creating tag:', await response.text())
    }
  } catch (error) {
    console.error('Error creating tag:', error)
  } finally {
    loading.value = false
  }
}

const handleEnter = () => {
  if (highlightedIndex.value === -1 && searchQuery.value && !exactMatch.value && props.canCreateNew) {
    createNewTag()
  } else if (highlightedIndex.value >= 0) {
    selectTag(filteredTags.value[highlightedIndex.value])
  }
}

const navigateDown = () => {
  const maxIndex = filteredTags.value.length - 1
  if (highlightedIndex.value < maxIndex) {
    highlightedIndex.value++
  }
}

const navigateUp = () => {
  if (highlightedIndex.value > -1) {
    highlightedIndex.value--
  }
}

const closeDropdown = () => {
  showDropdown.value = false
  highlightedIndex.value = -1
}

// Click outside directive
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutside = (event) => {
      // Check if the click is outside the dropdown AND outside the input field
      const inputElement = el.parentElement?.querySelector('input')
      const isClickOnInput = inputElement && (inputElement === event.target || inputElement.contains(event.target))
      const isClickOutside = !(el === event.target || el.contains(event.target))

      if (isClickOutside && !isClickOnInput) {
        binding.value()
      }
    }
    document.addEventListener('mousedown', el._clickOutside)
  },
  unmounted(el) {
    document.removeEventListener('mousedown', el._clickOutside)
  }
}

// Lifecycle
onMounted(() => {
  fetchTags()
})
</script>