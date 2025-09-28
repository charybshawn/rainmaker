<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        @click="handleBackdropClick"
      >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-start justify-center p-0 sm:p-6 sm:items-center">
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 sm:scale-95 translate-y-full sm:translate-y-4"
            enter-to-class="opacity-100 sm:scale-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 sm:scale-100 translate-y-0"
            leave-to-class="opacity-0 sm:scale-95 translate-y-full sm:translate-y-4"
          >
            <div
              v-if="show"
              class="relative w-full h-full sm:h-auto sm:w-[66%] sm:max-w-4xl sm:max-h-[90vh] bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl border-0 sm:border sm:border-white/20 sm:rounded-2xl shadow-2xl flex flex-col"
              role="dialog"
              :aria-labelledby="titleId"
              @click.stop
            >
              <!-- Header -->
              <div class="flex-shrink-0 flex items-center justify-between p-4 sm:p-6 border-b border-white/10">
                <!-- Title Area -->
                <div class="flex-1 min-w-0">
                  <h2 v-if="title || $slots.title" :id="titleId" class="text-xl sm:text-2xl font-semibold text-white truncate">
                    <slot name="title">{{ title }}</slot>
                  </h2>
                  <p v-if="subtitle || $slots.subtitle" class="mt-1 text-sm text-white/70">
                    <slot name="subtitle">{{ subtitle }}</slot>
                  </p>
                </div>

                <!-- Header Actions -->
                <div class="flex-shrink-0 flex items-center space-x-2 ml-4">
                  <slot name="headerActions" />

                  <!-- Close Button -->
                  <button
                    v-if="closeable"
                    @click="close"
                    class="p-2 text-white/70 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200"
                    aria-label="Close modal"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                  </button>
                </div>
              </div>

              <!-- Content Area -->
              <div class="flex-1 overflow-hidden flex flex-col">
                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6">
                  <slot name="default" />
                </div>

                <!-- Footer (if provided) -->
                <div v-if="$slots.footer" class="flex-shrink-0 border-t border-white/10 p-4 sm:p-6">
                  <slot name="footer" />
                </div>
              </div>

              <!-- Sticky Actions (Mobile) -->
              <div v-if="$slots.actions && isMobile" class="flex-shrink-0 p-4 border-t border-white/10 bg-black/20">
                <slot name="actions" />
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: ''
  },
  subtitle: {
    type: String,
    default: ''
  },
  closeable: {
    type: Boolean,
    default: true
  },
  closeOnBackdropClick: {
    type: Boolean,
    default: true
  },
  maxWidth: {
    type: String,
    default: '66%',
    validator: (value) => ['50%', '66%', '75%', '90%', 'full'].includes(value)
  }
})

const emit = defineEmits(['close'])

// Generate unique ID for accessibility
const titleId = ref(`modal-title-${Math.random().toString(36).substr(2, 9)}`)

// Mobile detection
const isMobile = ref(false)

const checkMobile = () => {
  isMobile.value = window.innerWidth < 640
}

// Handle escape key
const handleEscape = (e) => {
  if (e.key === 'Escape' && props.show && props.closeable) {
    close()
  }
}

// Handle backdrop click
const handleBackdropClick = (e) => {
  if (e.target === e.currentTarget && props.closeable && props.closeOnBackdropClick) {
    close()
  }
}

// Actions
const close = () => {
  emit('close')
}

// Lifecycle
onMounted(() => {
  checkMobile()
  window.addEventListener('resize', checkMobile)
  document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
  window.removeEventListener('resize', checkMobile)
  document.removeEventListener('keydown', handleEscape)
})

// Body scroll management
watch(() => props.show, (newShow) => {
  if (newShow) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

// Computed classes for responsive width
const modalWidthClass = computed(() => {
  const widthMap = {
    '50%': 'sm:w-1/2',
    '66%': 'sm:w-[66%]',
    '75%': 'sm:w-3/4',
    '90%': 'sm:w-[90%]',
    'full': 'sm:w-full'
  }
  return widthMap[props.maxWidth] || 'sm:w-[66%]'
})
</script>

<style scoped>
/* Custom scrollbar for content area */
.overflow-y-auto::-webkit-scrollbar {
  width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

/* Firefox scrollbar */
.overflow-y-auto {
  scrollbar-width: thin;
  scrollbar-color: rgba(255, 255, 255, 0.3) rgba(255, 255, 255, 0.1);
}
</style>