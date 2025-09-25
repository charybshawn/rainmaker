<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
      @click="$emit('cancel')"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div
        class="relative dark transform overflow-hidden rounded-2xl bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl border border-white/20 shadow-2xl transition-all duration-300 w-full max-w-md"
        style="backdrop-filter: blur(20px) saturate(180%);"
        @click.stop
      >
        <!-- Header with icon -->
        <div class="px-6 pt-6 pb-4">
          <div class="flex items-center justify-center mb-4">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full backdrop-blur-xl border"
              :class="iconClasses"
              style="backdrop-filter: blur(20px) saturate(180%);"
            >
              <!-- Question mark icon for general confirmations -->
              <svg v-if="type === 'question'" class="h-8 w-8 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c.498-1.02 1.52-1.519 2.121-1.519s1.623.499 2.121 1.519c.281.577.231 1.24-.112 1.748l-.363.618A2.25 2.25 0 0 0 13.5 12h.007M12 15.75h.007v.008H12v-.008Z" />
              </svg>
              <!-- Warning icon for destructive actions -->
              <svg v-else class="h-8 w-8 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
            </div>
          </div>

          <h3 class="text-lg font-semibold text-white text-center mb-2">
            {{ title }}
          </h3>

          <div class="text-sm text-gray-300 text-center">
            <p v-html="message"></p>
            <span v-if="type === 'warning'" class="block mt-3 text-yellow-400 font-medium">
              This action cannot be undone.
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button
            @click="$emit('cancel')"
            class="px-4 py-2.5 text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 rounded-lg backdrop-blur-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-white/20"
            style="backdrop-filter: blur(20px) saturate(180%);"
          >
            {{ cancelText }}
          </button>

          <button
            @click="$emit('confirm')"
            class="px-4 py-2.5 text-sm font-medium text-white rounded-lg backdrop-blur-xl transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2"
            :class="confirmButtonClasses"
            style="backdrop-filter: blur(20px) saturate(180%);"
          >
            {{ confirmText }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirm Action'
  },
  message: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'question',
    validator: (value) => ['question', 'warning'].includes(value)
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  }
})

defineEmits(['confirm', 'cancel'])

const iconClasses = computed(() => {
  if (props.type === 'warning') {
    return 'bg-gradient-to-br from-yellow-500/20 via-yellow-400/10 to-transparent border-yellow-400/30'
  }
  return 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent border-blue-400/30'
})

const confirmButtonClasses = computed(() => {
  if (props.type === 'warning') {
    return 'bg-gradient-to-r from-yellow-500/20 via-yellow-400/10 to-transparent hover:from-yellow-500/30 hover:via-yellow-400/15 hover:to-yellow-300/5 border border-yellow-400/30 focus:ring-yellow-400/20'
  }
  return 'bg-gradient-to-r from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 border border-blue-400/30 focus:ring-blue-400/20'
})
</script>