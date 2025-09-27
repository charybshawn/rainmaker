<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    @click="$emit('close')"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            Company Details
          </h2>
          <button
            @click="$emit('close')"
            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div v-if="company" class="space-y-4">
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ company.name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ company.ticker }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sector</label>
              <p class="text-sm text-gray-900 dark:text-white">{{ company.sector || 'N/A' }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Industry</label>
              <p class="text-sm text-gray-900 dark:text-white">{{ company.industry || 'N/A' }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Market Cap</label>
            <p class="text-sm text-gray-900 dark:text-white">{{ company.marketCapFormatted || 'N/A' }}</p>
          </div>

          <div v-if="company.description">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <p class="text-sm text-gray-900 dark:text-white">{{ company.description }}</p>
          </div>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            @click="$emit('close')"
            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-700"
          >
            Close
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
  company: {
    type: Object,
    default: null
  }
})

defineEmits(['close'])
</script>