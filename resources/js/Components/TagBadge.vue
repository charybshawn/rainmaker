<template>
  <span
    :class="[
      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
      removable ? 'pr-1' : '',
      sizeClass
    ]"
    :style="{ backgroundColor: tag.color + '20', color: tag.color, borderColor: tag.color }"
    class="border"
  >
    {{ tag.name }}
    <button
      v-if="removable"
      @click="$emit('remove', tag.id)"
      class="ml-1 inline-flex items-center justify-center w-4 h-4 rounded-full hover:bg-black hover:bg-opacity-10 focus:outline-none focus:bg-black focus:bg-opacity-10"
      type="button"
    >
      <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
      </svg>
    </button>
  </span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  tag: {
    type: Object,
    required: true,
    validator: (tag) => tag && tag.id && tag.name && tag.color
  },
  removable: {
    type: Boolean,
    default: false
  },
  size: {
    type: String,
    default: 'sm',
    validator: (value) => ['xs', 'sm', 'md'].includes(value)
  }
})

const emit = defineEmits(['remove'])

const sizeClass = computed(() => {
  switch (props.size) {
    case 'xs':
      return 'px-2 py-0.5 text-xs'
    case 'md':
      return 'px-3 py-1 text-sm'
    default:
      return 'px-2.5 py-0.5 text-xs'
  }
})
</script>