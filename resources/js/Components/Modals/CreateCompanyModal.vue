<template>
  <Teleport to="body">
    <div v-show="show" class="fixed inset-0 bg-black/70 backdrop-blur-md z-50 flex items-center justify-center p-4" @click.self="$emit('close')">
    <div class="bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 w-full max-w-2xl lg:max-w-3xl max-h-[80vh] overflow-y-auto shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-black/10 backdrop-blur-xl border-b border-white/20 px-8 py-6 rounded-t-2xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <div class="flex items-center justify-between">
          <h2 class="text-3xl font-semibold text-white">Add New Company</h2>
          <div class="flex items-center space-x-3">
          <!-- Save Button -->
          <button
            @click="$emit('save')"
            :disabled="creating"
            class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
            style="backdrop-filter: blur(20px) saturate(150%);"
            title="Save Company"
          >
            <svg v-if="!creating" class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <svg v-else class="animate-spin w-5 h-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ creating ? 'Saving...' : 'Save Company' }}
          </button>
          <!-- Close Button -->
          <button
            @click="$emit('close')"
            class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 hover:border-white/30 flex items-center justify-center transition-all duration-300 hover:scale-105 backdrop-blur-xl"
            style="backdrop-filter: blur(20px) saturate(150%);"
            title="Close"
          >
            <svg class="w-5 h-5 text-white/70 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <div v-if="errors.general" class="bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent backdrop-blur-xl border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4" style="backdrop-filter: blur(20px) saturate(180%);">
          {{ errors.general }}
        </div>
        
        <!-- Two Column Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column -->
          <div class="space-y-6">
            <!-- Company Name -->
            <div>
              <label for="name" class="block text-sm font-medium text-white mb-2">Company Name</label>
              <input
                id="name"
:value="form.name"
                @input="$emit('update:form', { ...form, name: $event.target.value })"
                type="text"
                required
                class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
                placeholder="e.g., Apple Inc."
              />
              <div v-if="errors.name" class="text-red-400 text-sm mt-1">{{ errors.name }}</div>
            </div>

            <!-- Ticker Symbol -->
            <div>
              <label for="ticker_symbol" class="block text-sm font-medium text-white mb-2">Ticker Symbol</label>
              <input
                id="ticker_symbol"
                :value="form.ticker_symbol"
                @input="handleTickerInput"
                type="text"
                required
                class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 uppercase"
                style="backdrop-filter: blur(20px) saturate(180%);"
                placeholder="e.g., AAPL"
                maxlength="10"
              />
              <div v-if="errors.ticker_symbol" class="text-red-400 text-sm mt-1">{{ errors.ticker_symbol }}</div>
            </div>

            <!-- Sector -->
            <div>
              <label for="sector" class="block text-sm font-medium text-white mb-2">Sector</label>
              <select
                id="sector"
:value="form.sector"
                @input="$emit('update:form', { ...form, sector: $event.target.value })"
                class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
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
              <div v-if="errors.sector" class="text-red-400 text-sm mt-1">{{ errors.sector }}</div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="space-y-6">
            <!-- Industry -->
            <div>
              <label for="industry" class="block text-sm font-medium text-white mb-2">Industry</label>
              <input
                id="industry"
:value="form.industry"
                @input="$emit('update:form', { ...form, industry: $event.target.value })"
                type="text"
                class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
                placeholder="e.g., Consumer Electronics"
              />
              <div v-if="errors.industry" class="text-red-400 text-sm mt-1">{{ errors.industry }}</div>
            </div>

            <!-- Market Cap -->
            <div>
              <label for="market_cap" class="block text-sm font-medium text-white mb-2">Market Cap (USD)</label>
              <div class="relative">
                <input
                  id="market_cap"
                  :value="marketCapInput"
                  type="text"
                  :class="[
                    'w-full px-4 py-3 pr-10 rounded-xl bg-black/10 backdrop-blur-xl text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] transition-all duration-500',
                    marketCapValidation.state === 'valid'
                      ? 'border border-green-400/50 focus:ring-green-400/20 focus:border-green-400/70 focus:ring-2'
                      : marketCapValidation.state === 'invalid'
                      ? 'border border-red-400/50 focus:ring-red-400/20 focus:border-red-400/70 focus:ring-2'
                      : 'border border-white/20 focus:ring-blue-400/20 focus:border-blue-400/50 focus:ring-2'
                  ]"
                  style="backdrop-filter: blur(20px) saturate(180%);"
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
              <p class="text-sm text-gray-400 mt-1">Enter numbers with decimals, or use shorthand: K (thousands), M (millions), B (billions), T (trillions)</p>
              <div v-if="errors.market_cap" class="text-red-400 text-sm mt-1">{{ errors.market_cap }}</div>
              <!-- Validation Success Message -->
              <div v-if="marketCapValidation.state === 'valid' && form.market_cap" class="text-green-400 text-sm mt-1">
                ✓ Valid: {{ formatMarketCap(form.market_cap) }}
              </div>
            </div>

            <!-- Currency -->
            <div>
              <label for="reports_financial_data_in" class="block text-sm font-medium text-white mb-2">Reports Financial Data In</label>
              <select
                id="reports_financial_data_in"
                :value="form.reports_financial_data_in"
                @input="$emit('update:form', { ...form, reports_financial_data_in: $event.target.value })"
                class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <option value="">Select currency...</option>
                <option value="USD">USD - US Dollar</option>
                <option value="EUR">EUR - Euro</option>
                <option value="GBP">GBP - British Pound</option>
                <option value="JPY">JPY - Japanese Yen</option>
                <option value="CAD">CAD - Canadian Dollar</option>
                <option value="AUD">AUD - Australian Dollar</option>
                <option value="CHF">CHF - Swiss Franc</option>
                <option value="CNY">CNY - Chinese Yuan</option>
                <option value="SEK">SEK - Swedish Krona</option>
                <option value="NOK">NOK - Norwegian Krone</option>
                <option value="DKK">DKK - Danish Krone</option>
                <option value="PLN">PLN - Polish Zloty</option>
                <option value="CZK">CZK - Czech Koruna</option>
                <option value="HUF">HUF - Hungarian Forint</option>
                <option value="RUB">RUB - Russian Ruble</option>
                <option value="BRL">BRL - Brazilian Real</option>
                <option value="MXN">MXN - Mexican Peso</option>
                <option value="INR">INR - Indian Rupee</option>
                <option value="KRW">KRW - South Korean Won</option>
                <option value="SGD">SGD - Singapore Dollar</option>
                <option value="HKD">HKD - Hong Kong Dollar</option>
                <option value="NZD">NZD - New Zealand Dollar</option>
                <option value="ZAR">ZAR - South African Rand</option>
                <option value="TRY">TRY - Turkish Lira</option>
                <option value="ILS">ILS - Israeli Shekel</option>
              </select>
              <div v-if="errors.reports_financial_data_in" class="text-red-400 text-sm mt-1">{{ errors.reports_financial_data_in }}</div>
            </div>
          </div>
        </div>

        <!-- Description (Full Width) -->
        <div>
          <label for="description" class="block text-sm font-medium text-white mb-2">Description</label>
          <textarea
            id="description"
:value="form.description"
            @input="$emit('update:form', { ...form, description: $event.target.value })"
            rows="3"
            class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 resize-none"
            style="backdrop-filter: blur(20px) saturate(180%);"
            placeholder="Brief description of the company and its business..."
          ></textarea>
          <div v-if="errors.description" class="text-red-400 text-sm mt-1">{{ errors.description }}</div>
        </div>

      </form>
      </div>
    </div>
    </div>
  </Teleport>
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