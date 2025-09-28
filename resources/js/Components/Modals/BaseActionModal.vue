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
        <div class="flex h-full sm:min-h-full items-start sm:items-center justify-center p-0 sm:p-6">
          <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 scale-95 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
              v-if="show"
              class="relative w-full h-full sm:h-auto sm:max-w-md sm:mx-auto bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl border-0 sm:border sm:border-white/20 rounded-none sm:rounded-2xl shadow-2xl flex flex-col sm:block"
              role="dialog"
              :aria-labelledby="titleId"
              :aria-describedby="messageId"
              @click.stop
            >
              <!-- Close Button (Mobile) -->
              <button
                v-if="closeable"
                @click="close"
                class="absolute top-4 right-4 p-2 text-white hover:text-white/70 transition-colors sm:hidden"
                aria-label="Close modal"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>

              <!-- Modal Content -->
              <div class="flex-1 sm:flex-none flex flex-col justify-center p-6 sm:p-8">
                <!-- Icon Slot -->
                <div v-if="$slots.icon" class="flex justify-center mb-4">
                  <div class="flex items-center justify-center w-12 h-12 rounded-full bg-white/10">
                    <slot name="icon" />
                  </div>
                </div>

                <!-- Title -->
                <div v-if="title || $slots.title" class="text-center mb-3">
                  <h3 :id="titleId" class="text-lg sm:text-xl font-semibold text-white">
                    <slot name="title">{{ title }}</slot>
                  </h3>
                </div>

                <!-- Message/Content -->
                <div v-if="message || $slots.default" class="text-center mb-6">
                  <p :id="messageId" class="text-sm sm:text-base text-white/80 leading-relaxed">
                    <slot name="default">{{ message }}</slot>
                  </p>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 sm:justify-center">
                  <slot name="actions">
                    <!-- Default Cancel Button -->
                    <button
                      v-if="showCancel"
                      @click="close"
                      class="order-2 sm:order-1 px-4 py-2 text-sm font-medium text-white/80 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg transition-all duration-200"
                    >
                      {{ cancelText }}
                    </button>

                    <!-- Default Confirm Button -->
                    <button
                      v-if="showConfirm"
                      @click="confirm"
                      class="order-1 sm:order-2 px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-lg transition-all duration-200 shadow-lg"
                      :class="confirmButtonClass"
                    >
                      {{ confirmText }}
                    </button>
                  </slot>
                </div>
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
  message: {
    type: String,
    default: ''
  },
  closeable: {
    type: Boolean,
    default: true
  },
  showCancel: {
    type: Boolean,
    default: true
  },
  showConfirm: {
    type: Boolean,
    default: true
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  confirmButtonClass: {
    type: String,
    default: ''
  },
  dangerMode: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'confirm'])

// Generate unique IDs for accessibility
const titleId = ref(`modal-title-${Math.random().toString(36).substr(2, 9)}`)
const messageId = ref(`modal-message-${Math.random().toString(36).substr(2, 9)}`)

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
  if (e.target === e.currentTarget && props.closeable) {
    close()
  }
}

// Actions
const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm')
}

// Computed confirm button class with danger mode
const computedConfirmClass = computed(() => {
  if (props.dangerMode) {
    return 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700'
  }
  return props.confirmButtonClass || 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700'
})

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
</script>