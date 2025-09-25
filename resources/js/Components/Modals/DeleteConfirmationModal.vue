<template>
  <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
      @click="$emit('close')"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
      <div
        class="relative transform overflow-hidden rounded-2xl bg-gradient-to-br from-white/95 to-gray-50/95 backdrop-blur-xl border border-white/20 shadow-2xl transition-all duration-300 w-full max-w-md"
        @click.stop
      >
        <!-- Header with icon -->
        <div class="px-6 pt-6 pb-4">
          <div class="flex items-center justify-center mb-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-100 to-red-200">
              <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
              </svg>
            </div>
          </div>

          <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">
            Delete {{ itemType }}
          </h3>

          <p class="text-sm text-gray-600 text-center">
            Are you sure you want to delete
            <span class="font-medium text-gray-900">"{{ itemName }}"</span>?
            <br><br>
            <span class="text-red-600 font-medium">This action cannot be undone.</span>
          </p>
        </div>

        <!-- Actions -->
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button
            @click="$emit('close')"
            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
          >
            Cancel
          </button>

          <button
            @click="$emit('confirm')"
            class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-lg shadow-sm transition-all duration-200 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
          >
            Delete {{ itemType }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    default: false
  },
  itemName: {
    type: String,
    required: true
  },
  itemType: {
    type: String,
    default: 'Item'
  }
})

defineEmits(['close', 'confirm'])
</script>