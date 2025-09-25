<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-50 space-y-2">
      <TransitionGroup
        name="toast"
        tag="div"
        class="space-y-2"
      >
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="min-w-80 max-w-md backdrop-blur-xl rounded-xl shadow-lg border transition-all duration-500"
          :class="toastClasses(toast.type)"
          style="backdrop-filter: blur(20px) saturate(180%);"
        >
          <div class="p-4">
            <div class="flex items-start">
              <!-- Icon -->
              <div class="flex-shrink-0 mr-3">
                <svg v-if="toast.type === 'success'" class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else-if="toast.type === 'error'" class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <svg v-else-if="toast.type === 'warning'" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <svg v-else class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>

              <!-- Content -->
              <div class="flex-1">
                <h4 v-if="toast.title" class="text-sm font-medium text-white mb-1">{{ toast.title }}</h4>
                <p class="text-sm text-gray-200 whitespace-pre-line">{{ toast.message }}</p>
              </div>

              <!-- Close button -->
              <button
                @click="removeToast(toast.id)"
                class="flex-shrink-0 ml-3 rounded-full p-1 hover:bg-white/10 transition-colors"
              >
                <svg class="w-4 h-4 text-gray-300 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

// Toast state
const toasts = ref([])
let nextId = 1

// Toast type classes
const toastClasses = (type) => {
  const baseClasses = 'bg-gradient-to-br border-white/20'

  switch (type) {
    case 'success':
      return `${baseClasses} from-green-500/20 via-green-400/10 to-transparent border-green-400/30`
    case 'error':
      return `${baseClasses} from-red-500/20 via-red-400/10 to-transparent border-red-400/30`
    case 'warning':
      return `${baseClasses} from-yellow-500/20 via-yellow-400/10 to-transparent border-yellow-400/30`
    default:
      return `${baseClasses} from-blue-500/20 via-blue-400/10 to-transparent border-blue-400/30`
  }
}

// Add toast function
const addToast = (message, type = 'info', title = null, duration = 5000) => {
  const toast = {
    id: nextId++,
    message,
    type,
    title,
    duration
  }

  toasts.value.push(toast)

  // Auto remove after duration
  if (duration > 0) {
    setTimeout(() => {
      removeToast(toast.id)
    }, duration)
  }

  return toast.id
}

// Remove toast function
const removeToast = (id) => {
  const index = toasts.value.findIndex(toast => toast.id === id)
  if (index > -1) {
    toasts.value.splice(index, 1)
  }
}

// Global event handlers
const handleGlobalToast = (event) => {
  addToast(event.detail.message, event.detail.type, event.detail.title, event.detail.duration)
}

onMounted(() => {
  // Listen for global toast events
  window.addEventListener('show-toast', handleGlobalToast)

  // Make functions globally available
  window.showToast = addToast
  window.removeToast = removeToast
})

onUnmounted(() => {
  window.removeEventListener('show-toast', handleGlobalToast)
  delete window.showToast
  delete window.removeToast
})

// Expose functions for parent components
defineExpose({
  addToast,
  removeToast
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.5s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.5s ease;
}
</style>