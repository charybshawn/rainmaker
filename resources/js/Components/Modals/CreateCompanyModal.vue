<template>
  <div v-show="show" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" @click.self="$emit('close')">
    <div class="bg-white dark:bg-gray-800 rounded-lg w-full max-w-md md:max-w-2xl lg:max-w-4xl xl:max-w-5xl 2xl:max-w-6xl max-h-[90vh] overflow-y-auto shadow-sm transition-all duration-200">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-8 py-6 rounded-t-2xl">
        <div class="flex items-center justify-between">
          <h2 class="text-3xl font-semibold text-gray-900 dark:text-white">Add New Company</h2>
          <div class="flex items-center space-x-2">
          <!-- Save Button -->
          <button 
            @click="$emit('save')"
            :disabled="creating"
            class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-700 hover:bg-blue-200 dark:hover:bg-blue-600 disabled:bg-blue-300 dark:disabled:bg-blue-800 flex items-center justify-center transition-colors"
            title="Save Company"
          >
            <svg v-if="!creating" class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </button>
          <!-- Close Button -->
          <button 
            @click="$emit('close')"
            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors"
            title="Close"
          >
            <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
          </div>
        </div>
      </div>

      <!-- Modal Content -->
      <div class="px-8 py-6">
      
      <form class="space-y-6">
        <!-- General Error Message -->
        <div v-if="errors.general" class="bg-red-50 dark:bg-red-900/50 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
          {{ errors.general }}
        </div>
        
        <!-- Two Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- Company Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Company Name</label>
              <input 
                id="name"
:value="form.name"
                @input="$emit('update:form', { ...form, name: $event.target.value })"
                type="text" 
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                placeholder="e.g., Apple Inc."
              />
              <div v-if="errors.name" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.name }}</div>
            </div>

            <!-- Ticker Symbol -->
            <div>
              <label for="ticker_symbol" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ticker Symbol</label>
              <input 
                id="ticker_symbol"
                :value="form.ticker_symbol"
                @input="handleTickerInput"
                type="text" 
                required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors uppercase"
                placeholder="e.g., AAPL"
                maxlength="10"
              />
              <div v-if="errors.ticker_symbol" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.ticker_symbol }}</div>
            </div>

            <!-- Sector -->
            <div>
              <label for="sector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sector</label>
              <select 
                id="sector"
:value="form.sector"
                @input="$emit('update:form', { ...form, sector: $event.target.value })"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
              >
                <option value="">Select a sector</option>
                <option value="Technology">Technology</option>
                <option value="Healthcare">Healthcare</option>
                <option value="Financials">Financials</option>
                <option value="Consumer Discretionary">Consumer Discretionary</option>
                <option value="Communication Services">Communication Services</option>
                <option value="Industrials">Industrials</option>
                <option value="Consumer Staples">Consumer Staples</option>
                <option value="Energy">Energy</option>
                <option value="Utilities">Utilities</option>
                <option value="Real Estate">Real Estate</option>
                <option value="Materials">Materials</option>
              </select>
              <div v-if="errors.sector" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.sector }}</div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Industry -->
            <div>
              <label for="industry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Industry</label>
              <input 
                id="industry"
:value="form.industry"
                @input="$emit('update:form', { ...form, industry: $event.target.value })"
                type="text" 
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                placeholder="e.g., Consumer Electronics"
              />
              <div v-if="errors.industry" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.industry }}</div>
            </div>

            <!-- Market Cap -->
            <div>
              <label for="market_cap" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market Cap (USD)</label>
              <div class="relative">
                <input 
                  id="market_cap"
                  :value="marketCapInput"
                  type="text" 
                  :class="[
                    'w-full px-4 py-3 pr-10 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 transition-colors',
                    marketCapValidation.state === 'valid' 
                      ? 'border-green-300 dark:border-green-500 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400' 
                      : marketCapValidation.state === 'invalid' 
                      ? 'border-red-300 dark:border-red-500 focus:ring-red-500 dark:focus:ring-red-400 focus:border-red-500 dark:focus:border-red-400'
                      : 'border-gray-300 dark:border-gray-600 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400'
                  ]"
                  placeholder="e.g., 1.2M, 500B, 2.5T or 1500000"
                  @input="$emit('market-cap-input', $event)"
                />
                
                <!-- Validation Icons -->
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <!-- Loading/Validating -->
                  <svg v-if="marketCapValidation.state === 'validating'" class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <!-- Valid -->
                  <svg v-else-if="marketCapValidation.state === 'valid'" class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <!-- Invalid -->
                  <svg v-else-if="marketCapValidation.state === 'invalid'" class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </div>
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enter numbers with decimals, or use shorthand: K (thousands), M (millions), B (billions), T (trillions)</p>
              <div v-if="errors.market_cap" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.market_cap }}</div>
              <!-- Validation Success Message -->
              <div v-if="marketCapValidation.state === 'valid' && form.market_cap" class="text-green-600 dark:text-green-400 text-sm mt-1">
                ✓ Valid: {{ formatMarketCap(form.market_cap) }}
              </div>
            </div>
          </div>
        </div>

        <!-- Description (Full Width) -->
        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
          <textarea 
            id="description"
:value="form.description"
            @input="$emit('update:form', { ...form, description: $event.target.value })"
            rows="3"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors resize-none"
            placeholder="Brief description of the company and its business..."
          ></textarea>
          <div v-if="errors.description" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ errors.description }}</div>
        </div>

      </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  form: {
    type: Object,
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  },
  creating: {
    type: Boolean,
    default: false
  },
  marketCapInput: {
    type: String,
    default: ''
  },
  marketCapValidation: {
    type: Object,
    default: () => ({ state: '' })
  },
  formatMarketCap: {
    type: Function,
    required: true
  }
})

const emit = defineEmits(['close', 'save', 'market-cap-input', 'update:form'])

const handleTickerInput = (event) => {
  emit('update:form', { ...props.form, ticker_symbol: event.target.value.toUpperCase() })
}
</script>