<template>
  <div class="space-y-3">
    <!-- Filter Header -->
    <div class="flex items-center justify-between">
      <label class="text-sm font-medium text-gray-300">Filter by Tags</label>
      <button
        v-if="selectedTags.length > 0"
        @click="clearFilters"
        class="text-xs text-blue-400 hover:text-blue-300 transition-colors"
      >
        Clear all
      </button>
    </div>

    <!-- Selected Tags Display -->
    <div v-if="selectedTags.length > 0" class="flex flex-wrap gap-2">
      <TagBadge
        v-for="tag in selectedTags"
        :key="tag.id"
        :tag="tag"
        :removable="true"
        size="sm"
        @remove="removeTag"
      />
    </div>

    <!-- Tag Search and Selection -->
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
          class="w-full px-3 py-2 pl-9 border border-gray-600 bg-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 text-sm"
          :placeholder="placeholder"
        />
        <div class="absolute inset-y-0 left-0 flex items-center pl-3">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </div>
      </div>

      <!-- Dropdown -->
      <div
        v-show="showDropdown"
        v-click-outside="closeDropdown"
        class="absolute z-50 w-full mt-1 bg-gray-800 border border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto"
      >
        <!-- Loading State -->
        <div v-if="loading" class="px-3 py-2 text-sm text-gray-400">
          Loading tags...
        </div>

        <!-- No Results -->
        <div v-else-if="filteredTags.length === 0" class="px-3 py-2 text-sm text-gray-400">
          No tags found
        </div>

        <!-- Tag Options -->
        <template v-else>
          <div
            v-for="(tag, index) in filteredTags"
            :key="tag.id"
            :class="[
              'px-3 py-2 cursor-pointer flex items-center justify-between text-sm',
              highlightedIndex === index ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700',
              isSelected(tag) ? 'opacity-50' : ''
            ]"
            @click="toggleTag(tag)"
          >
            <div class="flex items-center">
              <div
                class="w-3 h-3 rounded-full mr-2"
                :style="{ backgroundColor: tag.color }"
              ></div>
              <span>{{ tag.name }}</span>
            </div>
            <svg v-if="isSelected(tag)" class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
          </div>
        </template>
      </div>
    </div>

    <!-- Quick Filter Buttons for Popular Tags -->
    <div v-if="popularTags.length > 0 && !showDropdown" class="flex flex-wrap gap-2">
      <button
        v-for="tag in popularTags"
        :key="tag.id"
        @click="toggleTag(tag)"
        :class="[
          'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-all',
          isSelected(tag)
            ? 'border-blue-500 bg-blue-500/20 text-blue-300'
            : 'border-gray-600 bg-gray-700 text-gray-300 hover:border-gray-500 hover:bg-gray-600'
        ]"
        :style="!isSelected(tag) ? { borderColor: tag.color + '40' } : {}"
      >
        <div
          class="w-2 h-2 rounded-full mr-1.5"
          :style="{ backgroundColor: tag.color }"
        ></div>
        {{ tag.name }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import TagBadge from './TagBadge.vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  },
  placeholder: {
    type: String,
    default: 'Search tags to filter...'
  },
  showPopularTags: {
    type: Boolean,
    default: true
  },
  maxPopularTags: {
    type: Number,
    default: 6
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

const popularTags = computed(() => {
  if (!props.showPopularTags) return []

  // Return the first few available tags as "popular" (you could enhance this with actual usage stats)
  return availableTags.value.slice(0, props.maxPopularTags)
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

const toggleTag = (tag) => {
  if (isSelected(tag)) {
    removeTag(tag.id)
  } else {
    selectedTags.value.push(tag)
  }
  searchQuery.value = ''
  showDropdown.value = false
  highlightedIndex.value = -1
}

const removeTag = (tagId) => {
  selectedTags.value = selectedTags.value.filter(tag => tag.id !== tagId)
}

const isSelected = (tag) => {
  return selectedTags.value.some(selected => selected.id === tag.id)
}

const clearFilters = () => {
  selectedTags.value = []
}

const handleEnter = () => {
  if (highlightedIndex.value >= 0) {
    toggleTag(filteredTags.value[highlightedIndex.value])
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
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
      }
    }
    document.addEventListener('click', el._clickOutside)
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutside)
  }
}

// Lifecycle
onMounted(() => {
  fetchTags()
})
</script>