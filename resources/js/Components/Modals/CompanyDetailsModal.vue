<template>
  <div v-show="show && company" class="fixed inset-0 bg-black/70 backdrop-blur-md flex items-center justify-center p-4 z-50" @click.self="$emit('close')">
    <div class="bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 max-w-6xl w-full max-h-[90vh] overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.15)] flex flex-col transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">
      <!-- Modal Header -->
      <div class="sticky top-0 bg-black/10 backdrop-blur-xl border-b border-white/20 px-8 py-6 rounded-t-2xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent backdrop-blur-xl border border-white/10 flex items-center justify-center text-white font-bold text-lg" style="backdrop-filter: blur(20px) saturate(150%);">
              {{ company?.name?.charAt(0)?.toUpperCase() || 'C' }}
            </div>
            <div>
              <h2 class="text-3xl font-bold text-white">{{ company?.name || 'Company Details' }}</h2>
              <p class="text-lg text-gray-300">{{ company?.ticker || 'N/A' }} • {{ company?.sector || 'Unknown Sector' }}</p>
            </div>
          </div>
          <div class="flex items-center space-x-3">
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

      <!-- Tab Navigation -->
      <div class="border-b border-white/20 px-8 bg-black/5 backdrop-blur-xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <nav class="flex space-x-8" aria-label="Tabs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300',
              activeTab === tab.id
                ? 'border-blue-400/70 text-blue-300'
                : 'border-transparent text-gray-400 hover:text-white hover:border-white/30'
            ]"
          >
            <div class="flex items-center space-x-2">
              <component :is="tab.icon" class="w-5 h-5" />
              <span>{{ tab.name }}</span>
              <span v-if="tab.count" class="bg-white/10 text-gray-300 py-1 px-2 rounded-full text-xs backdrop-blur-xl border border-white/10" style="backdrop-filter: blur(20px) saturate(150%);">
                {{ tab.count }}
              </span>
            </div>
          </button>
        </nav>
      </div>
      
      <!-- Tab Content -->
      <div class="flex-1 overflow-y-auto bg-gradient-to-br from-black/5 via-transparent to-black/5 backdrop-blur-xl" style="backdrop-filter: blur(20px) saturate(180%);">
        <!-- Overview Tab -->
        <div v-show="activeTab === 'overview'" class="p-6">
          <!-- Company Overview Grid -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Left Column - Key Metrics -->
            <div class="space-y-4">
              <div class="bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent backdrop-blur-xl rounded-xl p-4 border border-white/10 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                  </svg>
                  Key Metrics
                </h3>
                <div class="space-y-4">
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Ticker Symbol</span>
                    <span class="text-white font-bold text-lg">{{ company?.ticker || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Market Cap</span>
                    <span class="text-white font-bold text-lg">{{ company?.marketCapFormatted || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Sector</span>
                    <span class="text-white">{{ company?.sector || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Industry</span>
                    <span class="text-white">{{ company?.industry || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Headquarters</span>
                    <span class="text-white">{{ company?.headquarters || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Employees</span>
                    <span class="text-white">{{ company?.employees ? company.employees.toLocaleString() : 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-gray-300 font-medium">Founded</span>
                    <span class="text-white">{{ company?.foundedDate || 'N/A' }}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Right Column - Description & Details -->
            <div class="space-y-4">
              <!-- Company Description -->
              <div class="bg-gradient-to-br from-purple-500/10 via-pink-500/5 to-transparent backdrop-blur-xl rounded-xl p-4 border border-white/10 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                  </svg>
                  Company Overview
                </h3>
                <div class="text-gray-300 leading-relaxed">
                  {{ company?.description || 'No description available for this company.' }}
                </div>
              </div>

              <!-- Company Timeline/Stats -->
              <div class="bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-xl p-4 border border-white/10 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)]" style="backdrop-filter: blur(20px) saturate(180%);">
                <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
                  <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Company Timeline
                </h3>
                <div class="text-sm text-gray-300">
                  <p><strong class="text-white">Added to system:</strong> {{ company?.createdAt || 'Unknown' }}</p>
                  <p v-if="company?.foundedDate"><strong class="text-white">Company founded:</strong> {{ company.foundedDate }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Edit Tab -->
        <div v-show="activeTab === 'edit'" class="p-6">
          <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-2xl font-bold text-white">Edit Company Information</h3>
              <div class="flex items-center space-x-2">
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
                <!-- Cancel Button -->
                <button
                  @click="activeTab = 'overview'"
                  class="group relative px-6 py-3 transition-all duration-500 hover:scale-105 bg-gradient-to-br from-white/10 via-white/5 to-transparent text-gray-300 hover:text-white rounded-full shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(255,255,255,0.1)] border border-white/20 backdrop-blur-xl font-medium"
                  style="backdrop-filter: blur(20px) saturate(150%);"
                >
                  Cancel
                </button>
              </div>
            </div>

            <form class="space-y-6">
              <!-- General Error Message -->
              <div v-if="editErrors.general" class="bg-gradient-to-br from-red-500/20 via-red-400/10 to-transparent backdrop-blur-xl border border-red-400/30 text-red-200 px-4 py-3 rounded-xl mb-4" style="backdrop-filter: blur(20px) saturate(180%);">
                {{ editErrors.general }}
              </div>
              
              <!-- Two Column Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                  <!-- Ticker Symbol -->
                  <div>
                    <label for="edit_ticker_symbol" class="block text-sm font-medium text-white mb-2">Ticker Symbol</label>
                    <input
                      id="edit_ticker_symbol"
                      :value="editForm.ticker_symbol"
                      @input="handleEditTickerInput"
                      type="text"
                      required
                      class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 uppercase"
                      style="backdrop-filter: blur(20px) saturate(180%);"
                      placeholder="e.g., AAPL"
                      maxlength="10"
                    />
                    <div v-if="editErrors.ticker_symbol" class="text-red-400 text-sm mt-1">{{ editErrors.ticker_symbol }}</div>
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
                      @input="$emit('update:edit-form', { ...editForm, sector: $event.target.value })"
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
                    <div v-if="editErrors.sector" class="text-red-400 text-sm mt-1">{{ editErrors.sector }}</div>
                  </div>

                  <!-- Currency -->
                  <div>
                    <label for="edit_reports_financial_data_in" class="block text-sm font-medium text-white mb-2">Reports Financial Data In</label>
                    <select
                      id="edit_reports_financial_data_in"
                      :value="editForm.reports_financial_data_in"
                      @input="$emit('update:edit-form', { ...editForm, reports_financial_data_in: $event.target.value })"
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
                    <div v-if="editErrors.reports_financial_data_in" class="text-red-400 text-sm mt-1">{{ editErrors.reports_financial_data_in }}</div>
                  </div>
                </div>
              </div>

              <!-- Description (Full Width) -->
              <div>
                <label for="edit_description" class="block text-sm font-medium text-white mb-2">Description</label>
                <textarea
                  id="edit_description"
                  :value="editForm.description"
                  @input="$emit('update:edit-form', { ...editForm, description: $event.target.value })"
                  rows="4"
                  class="w-full px-4 py-3 rounded-xl bg-black/10 backdrop-blur-xl border border-white/20 text-white placeholder-gray-400 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] focus:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] focus:border-blue-400/50 focus:ring-2 focus:ring-blue-400/20 transition-all duration-500 resize-none"
                  style="backdrop-filter: blur(20px) saturate(180%);"
                  placeholder="Brief description of the company and its business..."
                ></textarea>
                <div v-if="editErrors.description" class="text-red-400 text-sm mt-1">{{ editErrors.description }}</div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Research Tab -->
        <div v-show="activeTab === 'research'" class="p-6">
          <div class="max-w-6xl mx-auto">
            <!-- Floating decorative elements -->
            <div class="absolute -top-4 -left-4 w-3 h-3 bg-blue-400/30 rounded-full blur-sm animate-pulse"></div>
            <div class="absolute -top-8 right-12 w-2 h-2 bg-purple-400/40 rounded-full blur-sm animate-pulse delay-1000"></div>
            <div class="absolute -bottom-6 left-8 w-1.5 h-1.5 bg-green-400/30 rounded-full blur-sm animate-pulse delay-500"></div>

            <div class="flex items-center justify-between mb-8">
              <div class="flex items-center space-x-6">
                <h3 class="text-2xl font-bold text-white/90">📚 Research & Notes</h3>

                <!-- View Toggle -->
                <div class="backdrop-blur-3xl bg-gradient-to-r from-white/5 via-white/10 to-white/5 rounded-full p-1 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] border border-white/10">
                  <button
                    @click="researchViewMode = 'cards'"
                    :class="[
                      'flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-500 hover:scale-105',
                      researchViewMode === 'cards'
                        ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]'
                        : 'text-white/60 hover:text-white hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                    ]"
                  >
                    <svg class="w-4 h-4" :class="researchViewMode === 'cards' ? 'shadow-[0_0_5px_rgba(59,130,246,0.3)]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-sm font-medium">Cards</span>
                  </button>
                  <button
                    @click="researchViewMode = 'list'"
                    :class="[
                      'flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-500 hover:scale-105',
                      researchViewMode === 'list'
                        ? 'bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent text-blue-200 scale-105 shadow-[0_0_8px_rgba(59,130,246,0.2)]'
                        : 'text-white/60 hover:text-white hover:shadow-[0_0_6px_rgba(255,255,255,0.1)]'
                    ]"
                  >
                    <svg class="w-4 h-4" :class="researchViewMode === 'list' ? 'shadow-[0_0_5px_rgba(59,130,246,0.3)]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span class="text-sm font-medium">List</span>
                  </button>
                </div>
              </div>

              <button
                @click="activeTab = 'research-new'"
                class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-3 px-6 rounded-2xl transition-all duration-500 hover:scale-105 flex items-center space-x-2 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <svg class="w-5 h-5 shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Add Research Note</span>
              </button>
            </div>
            
            <!-- Research Items Display -->
            <div v-if="company?.researchItems && company.researchItems.length > 0">
              
              <!-- List View -->
              <div v-if="researchViewMode === 'list'" class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10" style="backdrop-filter: blur(20px) saturate(180%);">
                <div class="divide-y divide-white/10">
                  <div
                    v-for="item in company.researchItems"
                    :key="item.id"
                    class="group px-6 py-6 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/10 transition-all duration-500 hover:scale-[1.02] rounded-3xl"
                  >
                    <div class="flex items-center">
                      <!-- Research Item Info -->
                      <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-4 items-center">
                        <div class="lg:col-span-2">
                          <h3 class="text-lg font-medium text-white/90">{{ item.title }}</h3>
                          <p class="text-sm text-blue-200/70">{{ item.category?.name || 'Uncategorized' }}</p>
                        </div>

                        <div class="text-center lg:text-left">
                          <p class="text-sm text-white/60">Visibility</p>
                          <div class="flex items-center justify-center lg:justify-start">
                            <svg v-if="item.visibility === 'public'" class="w-4 h-4 mr-2 text-green-400 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-2 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                            <span class="font-medium text-white/90">
                              {{ item.visibility === 'public' ? 'Visible' : 'Hidden' }}
                            </span>
                          </div>
                        </div>

                        <div class="text-center lg:text-left">
                          <p class="text-sm text-white/60">Created</p>
                          <p class="font-medium text-white/90">{{ item.created_at }}</p>
                        </div>

                        <div class="text-center lg:text-right">
                          <p class="text-sm text-white/60">Attachments</p>
                          <p class="font-medium text-white/90">{{ item.attachments?.length || 0 }}</p>
                        </div>
                      </div>
                      
                      <!-- Actions -->
                      <div class="ml-6 flex items-center space-x-3">
                        <button
                          class="group w-10 h-10 rounded-full backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-500 hover:scale-105 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10"
                          title="Edit Research Note"
                        >
                          <svg class="w-4 h-4 text-blue-200 group-hover:text-white shadow-[0_0_5px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button
                          class="group w-10 h-10 rounded-full backdrop-blur-3xl bg-gradient-to-br from-red-500/20 to-red-600/30 hover:from-red-500/30 hover:to-red-600/40 flex items-center justify-center transition-all duration-500 hover:scale-105 shadow-[0_0_10px_rgba(239,68,68,0.15)] hover:shadow-[0_0_15px_rgba(239,68,68,0.25)] border border-white/10"
                          title="Delete Research Note"
                        >
                          <svg class="w-4 h-4 text-red-200 group-hover:text-white shadow-[0_0_5px_rgba(239,68,68,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cards View -->
              <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                  v-for="item in company.researchItems"
                  :key="item.id"
                  class="group relative p-6 transition-all duration-500 hover:scale-105 backdrop-blur-3xl bg-gradient-to-br from-blue-500/5 via-transparent to-blue-400/10 rounded-2xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 hover:shadow-[0_8px_20px_0_rgba(31,38,135,0.3)]"
                  style="backdrop-filter: blur(20px) saturate(180%);"
                >
                  <!-- Floating particle decoration -->
                  <div class="absolute top-2 right-2 w-2 h-2 bg-blue-400/30 rounded-full blur-sm animate-pulse"></div>

                  <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white/90 line-clamp-2">{{ item.title }}</h3>
                    <div class="ml-4 flex items-center space-x-2 flex-shrink-0">
                      <button
                        class="group w-8 h-8 rounded-full backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-500 hover:scale-105 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10"
                        title="Edit Research Note"
                      >
                        <svg class="w-3 h-3 text-blue-200 group-hover:text-white shadow-[0_0_5px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                      <button
                        class="group w-8 h-8 rounded-full backdrop-blur-3xl bg-gradient-to-br from-red-500/20 to-red-600/30 hover:from-red-500/30 hover:to-red-600/40 flex items-center justify-center transition-all duration-500 hover:scale-105 shadow-[0_0_10px_rgba(239,68,68,0.15)] hover:shadow-[0_0_15px_rgba(239,68,68,0.25)] border border-white/10"
                        title="Delete Research Note"
                      >
                        <svg class="w-3 h-3 text-red-200 group-hover:text-white shadow-[0_0_5px_rgba(239,68,68,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                    
                  <div class="space-y-4">
                    <div class="flex items-center justify-between">
                      <span class="text-sm text-white/60">Category</span>
                      <span class="text-sm font-medium text-blue-200">{{ item.category?.name || 'Uncategorized' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                      <span class="text-sm text-white/60">Visibility</span>
                      <div class="flex items-center">
                        <svg v-if="item.visibility === 'public'" class="w-4 h-4 mr-1 text-green-400 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg v-else class="w-4 h-4 mr-1 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                        </svg>
                        <span class="text-sm font-medium text-white/90">
                          {{ item.visibility === 'public' ? 'Visible' : 'Hidden' }}
                        </span>
                      </div>
                    </div>

                    <div class="flex items-center justify-between">
                      <span class="text-sm text-white/60">Created</span>
                      <span class="text-sm font-medium text-white/90">{{ item.created_at }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                      <span class="text-sm text-white/60">Attachments</span>
                      <span class="text-sm font-medium text-white/90">{{ item.attachments?.length || 0 }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No Research Items -->
            <div v-else class="relative text-center py-16">
              <!-- Floating decorative elements -->
              <div class="absolute top-8 left-12 w-2 h-2 bg-purple-400/40 rounded-full blur-sm animate-pulse delay-500"></div>
              <div class="absolute bottom-12 right-16 w-1.5 h-1.5 bg-green-400/30 rounded-full blur-sm animate-pulse delay-1000"></div>

              <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl p-12 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 max-w-2xl mx-auto" style="backdrop-filter: blur(20px) saturate(180%);">
                <svg class="w-20 h-20 text-white/40 mx-auto mb-6 shadow-[0_0_10px_rgba(255,255,255,0.1)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-2xl font-semibold text-white/90 mb-3">📝 No Research Items Yet</h3>
                <p class="text-white/70 mb-8 leading-relaxed">Start documenting your research and analysis for this company to build a comprehensive investment profile.</p>
                <button
                  @click="activeTab = 'research-new'"
                  class="group backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 via-blue-400/10 to-transparent hover:from-blue-500/30 hover:via-blue-400/15 hover:to-blue-300/5 text-white font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(59,130,246,0.2)] border border-white/10"
                  style="backdrop-filter: blur(20px) saturate(180%);"
                >
                  <span class="shadow-[0_0_5px_rgba(59,130,246,0.3)] group-hover:shadow-[0_0_8px_rgba(59,130,246,0.4)]">🚀 Create First Research Note</span>
                </button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Research New Tab -->
        <div v-show="activeTab === 'research-new'" class="p-6">
          <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Add Research Note</h3>
                <p class="text-lg text-gray-600 dark:text-gray-300 mt-1" v-if="company">
                  for {{ company.name }} ({{ company.ticker }})
                </p>
              </div>
              <div class="flex items-center space-x-2">
                <!-- Save Button -->
                <button 
                  @click="$emit('save-note')"
                  :disabled="creatingNote"
                  class="bg-blue-500 hover:bg-blue-600 disabled:bg-blue-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center space-x-2"
                >
                  <svg v-if="!creatingNote" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                  <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ creatingNote ? 'Saving...' : 'Save Note' }}</span>
                </button>
                <!-- Cancel Button -->
                <button 
                  @click="activeTab = 'research'"
                  class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>

            <form class="space-y-6">
              <!-- General Error Message -->
              <div v-if="noteErrors.general" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
                {{ noteErrors.general }}
              </div>

              <!-- Title -->
              <div>
                <label for="note_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Title</label>
                <input 
                  id="note_title"
                  :value="noteForm.title"
                  @input="$emit('update:note-form', { ...noteForm, title: $event.target.value })"
                  type="text" 
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                  placeholder="e.g., Q3 2024 Earnings Analysis"
                />
                <div v-if="noteErrors.title" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ noteErrors.title }}</div>
              </div>

              <!-- Category and Visibility -->
              <div class="flex items-start gap-6">
                <div class="flex-1">
                  <label for="note_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                  <select 
                    id="note_category"
                    :value="noteForm.category_id"
                    @input="$emit('update:note-form', { ...noteForm, category_id: $event.target.value })"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors"
                  >
                    <option value="">Select category (optional)</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                  <div v-if="noteErrors.category_id" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ noteErrors.category_id }}</div>
                </div>

                <div class="flex flex-col items-center">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
                  <button 
                    @click="toggleVisibility"
                    type="button"
                    class="w-12 h-12 rounded-lg border-2 transition-all duration-200 flex items-center justify-center"
                    :class="noteForm.visibility === 'public' 
                      ? 'border-green-300 dark:border-green-500 bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:border-green-400 dark:hover:border-green-400' 
                      : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-400 dark:hover:border-gray-500'"
                    :title="noteForm.visibility === 'public' ? 'Public - Click to make private' : 'Private - Click to make public'"
                  >
                    <!-- Eye Open Icon (Public) -->
                    <svg v-if="noteForm.visibility === 'public'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <!-- Eye Closed Icon (Private) -->
                    <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                    </svg>
                  </button>
                  <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">
                    {{ noteForm.visibility === 'public' ? 'Public' : 'Private' }}
                  </span>
                </div>
              </div>

              <!-- Content -->
              <div>
                <label for="note_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note Content</label>
                <div class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                  <MdEditor
                    :modelValue="noteForm.content"
                    @update:modelValue="handleMarkdownChange"
                    language="en-US"
                    :preview="true"
                    :toolbars="markdownToolbars"
                    :footers="[]"
                    style="height: 400px;"
                    placeholder="Enter your analysis, thoughts, observations, and research notes..."
                  />
                </div>
                <div v-if="noteErrors.content" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ noteErrors.content }}</div>
              </div>

              <!-- File Attachments -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments (optional)</label>
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-gray-400 dark:hover:border-gray-500 transition-colors">
                  <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="mt-4">
                    <label for="note-file-upload" class="cursor-pointer">
                      <span class="mt-2 block text-sm font-medium text-gray-900 dark:text-white">
                        Click to upload files or drag and drop
                      </span>
                      <span class="mt-1 block text-xs text-gray-500 dark:text-gray-400">
                        PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)
                      </span>
                    </label>
                    <input 
                      id="note-file-upload" 
                      name="note-file-upload" 
                      type="file" 
                      multiple 
                      accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
                      @change="$emit('note-file-upload', $event)"
                      class="sr-only" 
                    />
                  </div>
                </div>
                
                <!-- File List -->
                <div v-if="noteForm.files && noteForm.files.length > 0" class="mt-4 space-y-2">
                  <div v-for="(file, index) in noteForm.files" :key="index" class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <div class="flex items-center">
                      <svg class="w-6 h-6 text-gray-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                      <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ formatFileSize(file.size) }}</span>
                      </div>
                    </div>
                    <button 
                      type="button" 
                      @click="$emit('remove-note-file', index)"
                      class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors p-1"
                    >
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div v-if="noteErrors.files" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ noteErrors.files }}</div>
              </div>

            </form>
          </div>
        </div>
        
        <!-- Documents Tab -->
        <div v-show="activeTab === 'documents'" class="p-6">
          <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-6">
              <div class="flex items-center space-x-6">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Documents & Files</h3>
                
                <!-- View Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                  <button
                    @click="documentsViewMode = 'cards'"
                    :class="[
                      'flex items-center space-x-2 px-3 py-2 rounded-md transition-all duration-200',
                      documentsViewMode === 'cards' 
                        ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm' 
                        : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
                    ]"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span class="text-sm font-medium">Cards</span>
                  </button>
                  <button
                    @click="documentsViewMode = 'list'"
                    :class="[
                      'flex items-center space-x-2 px-3 py-2 rounded-md transition-all duration-200',
                      documentsViewMode === 'list' 
                        ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm' 
                        : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
                    ]"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    <span class="text-sm font-medium">List</span>
                  </button>
                </div>
              </div>
              
              <button 
                @click="activeTab = 'documents-upload'"
                class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center space-x-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span>Upload Document</span>
              </button>
            </div>
            
            <!-- Documents Content -->
            <div v-if="company && company.documents && company.documents.length > 0">
              <!-- List View -->
              <div v-if="documentsViewMode === 'list'" class="space-y-3">
                <div 
                  v-for="document in company.documents" 
                  :key="document.id"
                  class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-200 overflow-hidden"
                >
                  <div class="p-6">
                    <div class="flex items-center justify-between">
                      <div class="flex-1 min-w-0">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 truncate">{{ document.title }}</h4>
                        <p v-if="document.description" class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">{{ document.description }}</p>
                      </div>
                      
                      <!-- Document metadata in a grid -->
                      <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-sm ml-8">
                        <div class="text-center lg:text-left">
                          <p class="text-sm text-gray-500 dark:text-gray-400">Category</p>
                          <p class="font-medium text-gray-900 dark:text-white">{{ document.category?.name || 'Uncategorized' }}</p>
                        </div>
                        
                        <div class="text-center lg:text-left">
                          <p class="text-sm text-gray-500 dark:text-gray-400">Visibility</p>
                          <div class="flex items-center justify-center lg:justify-start">
                            <svg v-if="document.visibility === 'public'" class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg v-else class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                            <span class="font-medium text-gray-900 dark:text-white">
                              {{ document.visibility === 'public' ? 'Visible' : 'Hidden' }}
                            </span>
                          </div>
                        </div>
                        
                        <div class="text-center lg:text-left">
                          <p class="text-sm text-gray-500 dark:text-gray-400">Created</p>
                          <p class="font-medium text-gray-900 dark:text-white">{{ document.created_at }}</p>
                        </div>
                        
                        <div class="text-center lg:text-right">
                          <p class="text-sm text-gray-500 dark:text-gray-400">Files</p>
                          <p class="font-medium text-gray-900 dark:text-white">{{ document.files?.length || 0 }}</p>
                        </div>
                      </div>
                      
                      <!-- Actions -->
                      <div class="ml-6 flex items-center space-x-2">
                        <button 
                          class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-700 hover:bg-green-200 dark:hover:bg-green-600 flex items-center justify-center transition-colors"
                          title="Download Document"
                        >
                          <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </button>
                        <button 
                          class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-700 hover:bg-blue-200 dark:hover:bg-blue-600 flex items-center justify-center transition-colors"
                          title="Edit Document"
                        >
                          <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button 
                          class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-700 hover:bg-red-200 dark:hover:bg-red-600 flex items-center justify-center transition-colors"
                          title="Delete Document"
                        >
                          <svg class="w-4 h-4 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Cards View -->
              <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div 
                  v-for="document in company.documents" 
                  :key="document.id"
                  class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-600 hover:shadow-lg transition-all duration-200"
                >
                  <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                      <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">{{ document.title }}</h3>
                      <div class="ml-4 flex items-center space-x-2 flex-shrink-0">
                        <button 
                          class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-700 hover:bg-green-200 dark:hover:bg-green-600 flex items-center justify-center transition-colors"
                          title="Download Document"
                        >
                          <svg class="w-4 h-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                        </button>
                        <button 
                          class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-700 hover:bg-blue-200 dark:hover:bg-blue-600 flex items-center justify-center transition-colors"
                          title="Edit Document"
                        >
                          <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                          </svg>
                        </button>
                        <button 
                          class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-700 hover:bg-red-200 dark:hover:bg-red-600 flex items-center justify-center transition-colors"
                          title="Delete Document"
                        >
                          <svg class="w-4 h-4 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                          </svg>
                        </button>
                      </div>
                    </div>
                    
                    <div v-if="document.description" class="mb-4">
                      <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3">{{ document.description }}</p>
                    </div>
                    
                    <div class="space-y-3">
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Category</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ document.category?.name || 'Uncategorized' }}</span>
                      </div>
                      
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Visibility</span>
                        <div class="flex items-center">
                          <svg v-if="document.visibility === 'public'" class="w-4 h-4 mr-1 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                          </svg>
                          <svg v-else class="w-4 h-4 mr-1 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                          </svg>
                          <span class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ document.visibility === 'public' ? 'Visible' : 'Hidden' }}
                          </span>
                        </div>
                      </div>
                      
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ document.created_at }}</span>
                      </div>
                      
                      <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Files</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ document.files?.length || 0 }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No Documents -->
            <div v-else class="text-center py-12">
              <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No Documents Yet</h3>
              <p class="text-gray-600 dark:text-gray-300 mb-6">Upload documents and files related to this company.</p>
              <button 
                @click="activeTab = 'documents-upload'"
                class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-6 rounded-lg transition-colors"
              >
                Upload First Document
              </button>
            </div>
          </div>
        </div>
        
        <!-- Documents Upload Tab -->
        <div v-show="activeTab === 'documents-upload'" class="p-6">
          <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">📎 Upload Document</h3>
                <p class="text-lg text-gray-600 dark:text-gray-300 mt-1" v-if="company">
                  for {{ company.name }} ({{ company.ticker }})
                </p>
              </div>
              <div class="flex items-center space-x-2">
                <!-- Save Button -->
                <button 
                  @click="$emit('save-document')"
                  :disabled="uploading || !documentForm.files || documentForm.files.length === 0"
                  class="bg-green-500 hover:bg-green-600 disabled:bg-green-400 text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center space-x-2"
                >
                  <svg v-if="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                  </svg>
                  <svg v-else class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ uploading ? 'Uploading...' : 'Upload Documents' }}</span>
                </button>
                <!-- Cancel Button -->
                <button 
                  @click="activeTab = 'documents'"
                  class="bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white font-medium py-2 px-4 rounded-lg transition-colors"
                >
                  Cancel
                </button>
              </div>
            </div>

            <form class="space-y-6">
              <!-- General Error Message -->
              <div v-if="documentErrors.general" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-4">
                {{ documentErrors.general }}
              </div>

              <!-- Title -->
              <div>
                <label for="upload_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Title</label>
                <input 
                  id="upload_title"
                  :value="documentForm.title"
                  @input="$emit('update:document-form', { ...documentForm, title: $event.target.value })"
                  type="text" 
                  required
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                  placeholder="e.g., Q3 2024 Financial Report"
                />
                <div v-if="documentErrors.title" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.title }}</div>
              </div>

              <!-- Description -->
              <div>
                <label for="upload_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description (optional)</label>
                <textarea 
                  id="upload_description"
                  :value="documentForm.description"
                  @input="$emit('update:document-form', { ...documentForm, description: $event.target.value })"
                  rows="3"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors resize-none"
                  placeholder="Brief description of the document(s)..."
                ></textarea>
                <div v-if="documentErrors.description" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.description }}</div>
              </div>

              <!-- File Upload Area -->
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Documents</label>
                <div class="border-2 border-dashed border-green-300 dark:border-green-600 rounded-lg p-8 text-center hover:border-green-400 dark:hover:border-green-500 transition-colors">
                  <svg class="mx-auto h-16 w-16 text-green-400 dark:text-green-500" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="mt-4">
                    <label for="document-upload" class="cursor-pointer">
                      <span class="mt-2 block text-lg font-medium text-gray-900 dark:text-white">
                        Click to upload documents or drag and drop
                      </span>
                      <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">
                        PDF, DOC, XLS, PPT, Images, TXT, CSV (max 10MB each)
                      </span>
                    </label>
                    <input 
                      id="document-upload" 
                      name="document-upload" 
                      type="file" 
                      multiple 
                      accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.jpg,.jpeg,.png,.gif,.webp,.svg"
                      @change="$emit('file-upload', $event)"
                      class="sr-only" 
                    />
                  </div>
                </div>
                
                <!-- File List -->
                <div v-if="documentForm.files && documentForm.files.length > 0" class="mt-6 space-y-3">
                  <div v-for="(file, index) in documentForm.files" :key="index" class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                    <div class="flex items-center">
                      <svg class="w-8 h-8 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                      <div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ file.name }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ formatFileSize ? formatFileSize(file.size) : file.size + ' bytes' }}</span>
                      </div>
                    </div>
                    <button 
                      type="button" 
                      @click="$emit('remove-file', index)"
                      class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 transition-colors p-1"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                      </svg>
                    </button>
                  </div>
                </div>
                <div v-if="documentErrors.files" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.files }}</div>
              </div>

              <!-- Category and Visibility -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="upload_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                  <select 
                    id="upload_category"
                    :value="documentForm.category_id"
                    @input="$emit('update:document-form', { ...documentForm, category_id: $event.target.value })"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                  >
                    <option value="">Select category (optional)</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                      {{ category.name }}
                    </option>
                  </select>
                  <div v-if="documentErrors.category_id" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.category_id }}</div>
                </div>

                <div>
                  <label for="upload_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
                  <select 
                    id="upload_visibility"
                    :value="documentForm.visibility"
                    @input="$emit('update:document-form', { ...documentForm, visibility: $event.target.value })"
                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                  >
                    <option value="private">Private (Only me)</option>
                    <option value="team">Team</option>
                    <option value="public">Public</option>
                  </select>
                  <div v-if="documentErrors.visibility" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.visibility }}</div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Community Insights Tab -->
        <div v-show="activeTab === 'insights'" class="p-6">
          <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
              <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Community Insights</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                  Investment insights and analysis from the community about {{ company?.name }}
                </p>
              </div>
              <!-- Create New Insight Button -->
              <button
                @click="$emit('create-insight', company)"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Share Insight
              </button>
            </div>

            <!-- Loading State -->
            <div v-if="loadingInsights" class="text-center py-8">
              <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <p class="text-gray-500 dark:text-gray-400 mt-2">Loading insights...</p>
            </div>

            <!-- Insights List -->
            <div v-else-if="companyInsights?.length > 0" class="space-y-4">
              <div
                v-for="insight in companyInsights"
                :key="insight.id"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow"
              >
                <!-- Post Header -->
                <div class="flex justify-between items-start mb-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0">
                      <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium text-sm">
                          {{ insight.user?.name?.charAt(0).toUpperCase() }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-3">
                      <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ insight.user?.name }}
                      </p>
                      <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ formatDate(insight.published_at || insight.created_at) }}
                        <span
                          v-if="insight.status === 'draft'"
                          class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full"
                        >
                          Draft
                        </span>
                      </p>
                    </div>
                  </div>
                  <!-- Actions dropdown could go here -->
                </div>

                <!-- Post Content -->
                <div class="mb-4">
                  <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                    {{ insight.title }}
                  </h4>
                  <div class="prose prose-sm max-w-none text-gray-700 dark:text-gray-300">
                    <div v-html="renderMarkdown(insight.content)"></div>
                  </div>
                </div>

                <!-- Post Footer -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                  <div class="flex items-center space-x-4">
                    <!-- Read More Link -->
                    <a
                      :href="route('blog.show', insight.slug)"
                      class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium transition-colors"
                    >
                      Read full post →
                    </a>
                  </div>

                  <!-- Additional actions could go here -->
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ insight.content?.length > 200 ? Math.ceil(insight.content.length / 1000) + ' min read' : '< 1 min read' }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-12">
              <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No insights yet</h3>
              <p class="text-gray-500 dark:text-gray-400 mb-6">
                Be the first to share an investment insight about {{ company?.name }}
              </p>
              <button
                @click="$emit('create-insight', company)"
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors"
              >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Write First Insight
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { MdEditor, MdPreview } from 'md-editor-v3'
import 'md-editor-v3/lib/style.css'

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
  },
  noteForm: {
    type: Object,
    default: () => ({})
  },
  noteErrors: {
    type: Object,
    default: () => ({})
  },
  creatingNote: {
    type: Boolean,
    default: false
  },
  categories: {
    type: Array,
    default: () => []
  },
  documentForm: {
    type: Object,
    default: () => ({})
  },
  documentErrors: {
    type: Object,
    default: () => ({})
  },
  uploading: {
    type: Boolean,
    default: false
  },
  formatFileSize: {
    type: Function,
    default: null
  },
  insights: {
    type: Array,
    default: () => []
  },
})

const emit = defineEmits([
  'close',
  'edit',
  'add-note',
  'upload-doc',
  'save-edit',
  'update:edit-form',
  'edit-market-cap-input',
  'save-note',
  'update:note-form',
  'note-file-upload',
  'remove-note-file',
  'save-document',
  'update:document-form',
  'file-upload',
  'remove-file',
  'create-insight'
])

// Markdown editor configuration
const markdownToolbars = [
  'bold',
  'italic',
  'strikeThrough',
  '-',
  'title',
  'sub',
  'sup',
  'quote',
  'unorderedList',
  'orderedList',
  '-',
  'codeRow',
  'code',
  'link',
  'image',
  'table',
  '-',
  'revoke',
  'next',
  '=',
  'prettier',
  'pageFullscreen',
  'fullscreen',
  'preview',
  'htmlPreview',
  'catalog'
]

const handleMarkdownChange = (content) => {
  emit('update:note-form', { ...props.noteForm, content })
}


const handleEditTickerInput = (event) => {
  emit('update:edit-form', { 
    ...props.editForm, 
    ticker_symbol: event.target.value.toUpperCase() 
  })
}

const toggleVisibility = () => {
  const newVisibility = props.noteForm.visibility === 'public' ? 'private' : 'public'
  emit('update:note-form', { ...props.noteForm, visibility: newVisibility })
}

const activeTab = ref('overview')
const researchViewMode = ref('list') // 'cards' or 'list'
const documentsViewMode = ref('list') // 'cards' or 'list'

// Insights-related refs
const companyInsights = ref([])
const loadingInsights = ref(false)

// Watch for insights prop changes
watch(() => props.insights, (newInsights) => {
  companyInsights.value = newInsights || []
}, { immediate: true })

const tabs = [
  {
    id: 'overview',
    name: 'Overview',
    icon: 'svg',
    count: null
  },
  {
    id: 'edit', 
    name: 'Edit',
    icon: 'svg',
    count: null
  },
  {
    id: 'research',
    name: 'Research',
    icon: 'svg', 
    count: null
  },
  {
    id: 'documents',
    name: 'Documents',
    icon: 'svg',
    count: null
  },
  {
    id: 'insights',
    name: 'Community Insights',
    icon: 'svg',
    count: null
  }
]

// Utility functions for insights
const renderMarkdown = (content) => {
  if (!content) return ''
  // Simple markdown rendering - you might want to use a proper markdown library
  return content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/`(.*?)`/g, '<code>$1</code>')
    .replace(/\n/g, '<br>')
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>

<style scoped>
/* Custom styles for markdown editor integration */
:deep(.md-editor) {
  --md-color: theme('colors.gray.900');
  --md-hover-color: theme('colors.gray.700');
  --md-bk-color: theme('colors.white');
  --md-bk-hover-color: theme('colors.gray.50');
  --md-border-color: theme('colors.gray.300');
  --md-border-hover-color: theme('colors.gray.400');
  --md-border-focus-color: theme('colors.blue.500');
  --md-scrollbar-bg-color: theme('colors.gray.100');
  --md-scrollbar-thumb-color: theme('colors.gray.400');
  --md-scrollbar-thumb-hover-color: theme('colors.gray.500');
  --md-scrollbar-thumb-active-color: theme('colors.gray.600');
}

.dark :deep(.md-editor) {
  --md-color: theme('colors.white');
  --md-hover-color: theme('colors.gray.300');
  --md-bk-color: theme('colors.gray.800');
  --md-bk-hover-color: theme('colors.gray.700');
  --md-border-color: theme('colors.gray.600');
  --md-border-hover-color: theme('colors.gray.500');
  --md-border-focus-color: theme('colors.blue.400');
  --md-scrollbar-bg-color: theme('colors.gray.700');
  --md-scrollbar-thumb-color: theme('colors.gray.500');
  --md-scrollbar-thumb-hover-color: theme('colors.gray.400');
  --md-scrollbar-thumb-active-color: theme('colors.gray.300');
}

:deep(.md-editor .md-editor-toolbar) {
  border-bottom: 1px solid var(--md-border-color);
  padding: 8px 12px;
}

:deep(.md-editor .md-editor-input-wrapper),
:deep(.md-editor .md-editor-preview-wrapper) {
  font-size: 14px;
  line-height: 1.6;
}

:deep(.md-editor .md-editor-input) {
  padding: 12px;
  font-family: ui-monospace, SFMono-Regular, 'SF Mono', Consolas, 'Liberation Mono', Menlo, monospace;
}

:deep(.md-editor .md-editor-preview) {
  padding: 12px;
}
</style>