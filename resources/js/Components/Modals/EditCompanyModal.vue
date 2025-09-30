<template>
  <Teleport to="body">
    <div v-show="show && company" class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-4 z-50" @click.self="$emit('close')">
      <div class="bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] flex flex-col transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">

        <!-- Modal Header -->
        <div class="sticky top-0 bg-black/10 backdrop-blur-xl border-b border-white/20 px-8 py-6 rounded-t-2xl" style="backdrop-filter: blur(20px) saturate(180%);">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-lg" style="backdrop-filter: blur(20px) saturate(150%);">
                {{ company?.name?.charAt(0)?.toUpperCase() || 'C' }}
              </div>
              <div>
                <h2 class="text-3xl font-bold text-white">Edit Company</h2>
                <p class="text-lg text-gray-300">{{ company?.ticker || 'N/A' }} • {{ company?.name || 'Unknown Company' }}</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <!-- Save Button -->
              <button
                @click="$emit('save-edit')"
                :disabled="saving"
                class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent text-green-200 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 backdrop-blur-xl font-medium disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                style="backdrop-filter: blur(20px) saturate(150%);"
              >
                <svg v-if="!saving" class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <svg v-else class="animate-spin w-4 h-4 mr-2 inline" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
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

        <!-- Edit Form Content -->
        <div class="flex-1 overflow-y-auto bg-gradient-to-br from-black/5 via-transparent to-black/5 backdrop-blur-xl p-8" style="backdrop-filter: blur(20px) saturate(180%);">
          <div class="max-w-4xl mx-auto">
            <div class="mb-6">
              <h3 class="text-2xl font-bold text-white">Edit Company Information</h3>
            </div>

            <form class="space-y-6">
              <!-- General Error Message -->
              <div v-if="editErrors.general" class="bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent backdrop-blur-xl border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4" style="backdrop-filter: blur(20px) saturate(180%);">
                {{ editErrors.general }}
              </div>

              <!-- Success Message -->
              <div v-if="editErrors.success" class="bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent backdrop-blur-xl border border-green-400/30 text-green-200 px-4 py-3 rounded-xl mb-4 flex items-center space-x-2" style="backdrop-filter: blur(20px) saturate(180%);">
                <svg class="w-5 h-5 text-green-400 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>{{ editErrors.success }}</span>
              </div>

              <!-- Two Column Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                  <!-- Ticker Symbol -->
                  <div>
                    <label for="edit_ticker" class="block text-sm font-medium text-white mb-2">Ticker Symbol</label>
                    <input
                      id="edit_ticker"
                      :value="editForm.ticker"
                      @input="handleEditTickerInput"
                      type="text"
                      required
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 uppercase"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                      placeholder="e.g., AAPL"
                      maxlength="10"
                    />
                    <div v-if="editErrors.ticker" class="text-red-400 text-sm mt-1">{{ editErrors.ticker }}</div>
                  </div>

                  <!-- Industry -->
                  <div>
                    <label for="edit_industry" class="block text-sm font-medium text-white mb-2">Industry</label>
                    <input
                      id="edit_industry"
                      :value="editForm.industry"
                      @input="$emit('update:edit-form', { ...editForm, industry: $event.target.value })"
                      type="text"
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                      placeholder="e.g., Consumer Electronics"
                    />
                    <div v-if="editErrors.industry" class="text-red-400 text-sm mt-1">{{ editErrors.industry }}</div>
                  </div>

                  <!-- Market Cap -->
                  <div>
                    <label for="edit_market_cap" class="block text-sm font-medium text-white mb-2">Market Cap (USD)</label>
                    <div class="relative">
                      <input
                        id="edit_market_cap"
                        :value="editMarketCapInput"
                        type="text"
                        :class="[
                          'w-full px-4 py-3 pr-10 rounded-xl bg-black/10 backdrop-blur-xl text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] transition-all duration-500',
                          editMarketCapValidation.state === 'valid'
                            ? 'border border-green-400/50 focus:ring-green-400/20 focus:border-green-400/70 focus:ring-2'
                            : editMarketCapValidation.state === 'invalid'
                            ? 'border border-red-400/50 focus:ring-red-400/20 focus:border-red-400/70 focus:ring-2'
                            : 'border border-white/20 focus:ring-blue-400/20 focus:border-blue-400/50 focus:ring-2'
                        ]"
                        style="backdrop-filter: blur(20px) saturate(180%);"
                        placeholder="e.g., 1.2M, 500B, 2.5T or 1500000"
                        @input="$emit('edit-market-cap-input', $event)"
                      />

                      <!-- Validation Icons -->
                      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <!-- Loading/Validating -->
                        <svg v-if="editMarketCapValidation.state === 'validating'" class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <!-- Valid -->
                        <svg v-else-if="editMarketCapValidation.state === 'valid'" class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <!-- Invalid -->
                        <svg v-else-if="editMarketCapValidation.state === 'invalid'" class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                      </div>
                    </div>
                    <p class="text-sm text-gray-400 mt-1">Enter numbers with decimals, or use shorthand: K (thousands), M (millions), B (billions), T (trillions)</p>
                    <div v-if="editErrors.market_cap" class="text-red-400 text-sm mt-1">{{ editErrors.market_cap }}</div>
                    <!-- Validation Success Message -->
                    <div v-if="editMarketCapValidation.state === 'valid' && editForm.market_cap" class="text-green-400 text-sm mt-1">
                      ✓ Valid: {{ formatMarketCap(editForm.market_cap) }}
                    </div>
                  </div>

                  <!-- Currency -->
                  <div>
                    <label for="edit_reports_financial_data_in" class="block text-sm font-medium text-white mb-2">Reports Financial Data In</label>
                    <select
                      id="edit_reports_financial_data_in"
                      :value="editForm.reports_financial_data_in"
                      @change="$emit('update:edit-form', { ...editForm, reports_financial_data_in: $event.target.value })"
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                    >
                      <option value="">Select Currency</option>
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
                    </select>
                    <div v-if="editErrors.reports_financial_data_in" class="text-red-400 text-sm mt-1">{{ editErrors.reports_financial_data_in }}</div>
                  </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                  <!-- Company Name -->
                  <div>
                    <label for="edit_name" class="block text-sm font-medium text-white mb-2">Company Name</label>
                    <input
                      id="edit_name"
                      :value="editForm.name"
                      @input="$emit('update:edit-form', { ...editForm, name: $event.target.value })"
                      type="text"
                      required
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                      placeholder="e.g., Apple Inc."
                    />
                    <div v-if="editErrors.name" class="text-red-400 text-sm mt-1">{{ editErrors.name }}</div>
                  </div>

                  <!-- Sector -->
                  <div>
                    <label for="edit_sector" class="block text-sm font-medium text-white mb-2">Sector</label>
                    <select
                      id="edit_sector"
                      :value="editForm.sector"
                      @change="$emit('update:edit-form', { ...editForm, sector: $event.target.value })"
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                    >
                      <option value="">Select Sector</option>
                      <option value="Technology">Technology</option>
                      <option value="Healthcare">Healthcare</option>
                      <option value="Financials">Financials</option>
                      <option value="Consumer Discretionary">Consumer Discretionary</option>
                      <option value="Consumer Staples">Consumer Staples</option>
                      <option value="Energy">Energy</option>
                      <option value="Industrials">Industrials</option>
                      <option value="Materials">Materials</option>
                      <option value="Real Estate">Real Estate</option>
                      <option value="Utilities">Utilities</option>
                      <option value="Communication Services">Communication Services</option>
                    </select>
                    <div v-if="editErrors.sector" class="text-red-400 text-sm mt-1">{{ editErrors.sector }}</div>
                  </div>


                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  company: {
    type: Object,
    default: null
  },
  editForm: {
    type: Object,
    default: () => ({})
  },
  editErrors: {
    type: Object,
    default: () => ({})
  },
  saving: {
    type: Boolean,
    default: false
  },
  editMarketCapInput: {
    type: String,
    default: ''
  },
  editMarketCapValidation: {
    type: Object,
    default: () => ({ state: '' })
  },
  formatMarketCap: {
    type: Function,
    default: () => () => ''
  }
})

// Define emits
const emit = defineEmits([
  'close',
  'save-edit',
  'update:edit-form',
  'edit-market-cap-input'
])

// Handle ticker input with uppercase transformation
const handleEditTickerInput = (event) => {
  const value = event.target.value.toUpperCase()
  emit('update:edit-form', { ...props.editForm, ticker: value })
}
</script>