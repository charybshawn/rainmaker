<template>
  <Teleport to="body">
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
            @click="handleTabClick(tab.id)"
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
                      :key="`currency-${editForm.reports_financial_data_in || 'empty'}`"
                      v-model="currencyModel"
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

            <div class="flex items-center justify-between mb-6">
              <h3 class="text-2xl font-bold text-white/90">📚 Research & Notes</h3>
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
            
            <!-- Research Items Data Table -->
            <div v-if="company?.researchItems && company.researchItems.length > 0">
              <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">

                <!-- Table Header -->
                <div class="bg-gradient-to-r from-white/5 to-white/2 px-6 py-4 border-b border-white/10">
                  <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white/80">
                    <div class="col-span-1 text-center">#</div>
                    <div class="col-span-4">Research Title</div>
                    <div class="col-span-2">Category</div>
                    <div class="col-span-2">Created</div>
                    <div class="col-span-1 text-center">Files</div>
                    <div class="col-span-1 text-center">Status</div>
                    <div class="col-span-1 text-center">Actions</div>
                  </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-white/5">
                  <div
                    v-for="(item, index) in company.researchItems"
                    :key="item.id"
                    class="group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300 cursor-pointer"
                    @click="viewResearchItem(item)"
                  >
                    <div class="grid grid-cols-12 gap-4 items-center text-sm">

                      <!-- Index -->
                      <div class="col-span-1 text-center">
                        <span class="text-white/60 font-medium">{{ index + 1 }}</span>
                      </div>

                      <!-- Title -->
                      <div class="col-span-4">
                        <h3 class="text-base font-medium text-white/90 line-clamp-1 mb-1">{{ item.title }}</h3>
                        <p class="text-xs text-blue-200/60 line-clamp-1">{{ item.content ? item.content.substring(0, 80) + '...' : 'No content preview' }}</p>
                      </div>

                      <!-- Category -->
                      <div class="col-span-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-200 border border-blue-500/30">
                          {{ item.category?.name || 'Uncategorized' }}
                        </span>
                      </div>

                      <!-- Created Date -->
                      <div class="col-span-2">
                        <div class="text-white/70">
                          <div class="font-medium">{{ formatDate(item.created_at) }}</div>
                          <div class="text-xs text-white/50">{{ new Date(item.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                        </div>
                      </div>

                      <!-- Files Count -->
                      <div class="col-span-1 text-center">
                        <div class="flex items-center justify-center">
                          <svg class="w-4 h-4 mr-1 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          <span class="text-white/70 font-medium">{{ item.attachments?.length || 0 }}</span>
                        </div>
                      </div>

                      <!-- Status -->
                      <div class="col-span-1 text-center">
                        <div class="flex items-center justify-center">
                          <div v-if="item.visibility === 'public'" class="flex items-center text-green-400">
                            <svg class="w-4 h-4 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                          </div>
                          <div v-else class="flex items-center text-white/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                          </div>
                        </div>
                      </div>

                      <!-- Actions -->
                      <div class="col-span-1">
                        <div class="flex items-center justify-center space-x-2">
                          <button
                            @click.stop="$emit('edit-research', item)"
                            class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                            title="Edit Research Note"
                          >
                            <svg class="w-3.5 h-3.5 text-blue-200 group-hover:text-white shadow-[0_0_5px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                          <button
                            @click.stop="$emit('delete-research', item)"
                            class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-red-500/20 to-red-600/30 hover:from-red-500/30 hover:to-red-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(239,68,68,0.15)] hover:shadow-[0_0_15px_rgba(239,68,68,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                            title="Delete Research Note"
                          >
                            <svg class="w-3.5 h-3.5 text-red-200 group-hover:text-white shadow-[0_0_5px_rgba(239,68,68,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
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
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                  {{ isEditingResearchItem ? '✏️ Edit Research Note' : '📝 Add Research Note' }}
                </h3>
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
                  <span>{{ creatingNote ? 'Saving...' : (isEditingResearchItem ? 'Update Note' : 'Save Note') }}</span>
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
              <div class="flex items-center justify-between">
                <h3 class="text-2xl font-bold text-white/90">Documents & Files</h3>
              </div>
              
              <button
                @click="activeTab = 'documents-upload'"
                class="backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent hover:from-green-500/30 hover:via-green-400/15 hover:to-green-300/5 text-white font-medium py-3 px-6 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10 flex items-center space-x-2"
                style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <svg class="w-4 h-4 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span class="shadow-[0_0_5px_rgba(34,197,94,0.3)]">Upload Document</span>
              </button>
            </div>
            
            <!-- Documents Data Table -->
            <div v-if="documentsWithAttachments && documentsWithAttachments.length > 0">
              <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 overflow-hidden" style="backdrop-filter: blur(20px) saturate(180%);">

                <!-- Table Header -->
                <div class="bg-gradient-to-r from-white/5 to-white/2 px-6 py-4 border-b border-white/10">
                  <div class="grid grid-cols-12 gap-4 text-sm font-semibold text-white/80">
                    <div class="col-span-1 text-center">#</div>
                    <div class="col-span-4">Document Title</div>
                    <div class="col-span-2">Type</div>
                    <div class="col-span-2">Created</div>
                    <div class="col-span-1 text-center">Files</div>
                    <div class="col-span-1 text-center">Status</div>
                    <div class="col-span-1 text-center">Actions</div>
                  </div>
                </div>

                <!-- Table Body -->
                <div class="divide-y divide-white/5">
                  <div
                    v-for="(document, index) in documentsWithAttachments"
                    :key="document.id"
                    class="group px-6 py-4 hover:bg-gradient-to-br hover:from-blue-500/5 hover:via-transparent hover:to-blue-400/5 transition-all duration-300 cursor-pointer"
                    @click="viewDocument(document)"
                  >
                    <div class="grid grid-cols-12 gap-4 items-center text-sm">

                      <!-- Index -->
                      <div class="col-span-1 text-center">
                        <span class="text-white/60 font-medium">{{ index + 1 }}</span>
                      </div>

                      <!-- Title -->
                      <div class="col-span-4">
                        <h3 class="text-base font-medium text-white/90 line-clamp-1 mb-1 hover:text-blue-300 transition-colors">{{ document.title }}</h3>
                        <p class="text-xs text-blue-200/60 line-clamp-1">{{ document.description ? document.description.substring(0, 80) + '...' : 'No description available' }}</p>
                      </div>

                      <!-- Type -->
                      <div class="col-span-2">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-500/20 text-purple-200 border border-purple-500/30">
                          {{ document.type === 'document' ? 'Document' : 'Research Item' }}
                        </span>
                      </div>

                      <!-- Created Date -->
                      <div class="col-span-2">
                        <div class="text-white/70">
                          <div class="font-medium">{{ formatDate(document.created_at) }}</div>
                          <div class="text-xs text-white/50">{{ new Date(document.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                        </div>
                      </div>

                      <!-- Files Count -->
                      <div class="col-span-1 text-center">
                        <div class="flex items-center justify-center">
                          <svg class="w-4 h-4 mr-1 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                          </svg>
                          <span class="text-white/70 font-medium">{{ document.attachments?.length || 0 }}</span>
                        </div>
                      </div>

                      <!-- Status -->
                      <div class="col-span-1 text-center">
                        <div class="flex items-center justify-center">
                          <div v-if="document.visibility === 'public'" class="flex items-center text-green-400">
                            <svg class="w-4 h-4 shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                          </div>
                          <div v-else class="flex items-center text-white/40">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                            </svg>
                          </div>
                        </div>
                      </div>

                      <!-- Actions -->
                      <div class="col-span-1">
                        <div class="flex items-center justify-center space-x-2" @click.stop>
                          <button
                            class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-green-500/20 to-green-600/30 hover:from-green-500/30 hover:to-green-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(34,197,94,0.15)] hover:shadow-[0_0_15px_rgba(34,197,94,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                            title="Download Document"
                          >
                            <svg class="w-3.5 h-3.5 text-green-200 group-hover:text-white shadow-[0_0_5px_rgba(34,197,94,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                          </button>
                          <button
                            class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 hover:from-blue-500/30 hover:to-blue-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(59,130,246,0.15)] hover:shadow-[0_0_15px_rgba(59,130,246,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                            title="Edit Document"
                          >
                            <svg class="w-3.5 h-3.5 text-blue-200 group-hover:text-white shadow-[0_0_5px_rgba(59,130,246,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                          <button
                            @click.stop="$emit('delete-document', document)"
                            class="group w-8 h-8 rounded-lg backdrop-blur-3xl bg-gradient-to-br from-red-500/20 to-red-600/30 hover:from-red-500/30 hover:to-red-600/40 flex items-center justify-center transition-all duration-300 hover:scale-110 shadow-[0_0_10px_rgba(239,68,68,0.15)] hover:shadow-[0_0_15px_rgba(239,68,68,0.25)] border border-white/10 cursor-pointer pointer-events-auto"
                            title="Delete Document"
                          >
                            <svg class="w-3.5 h-3.5 text-red-200 group-hover:text-white shadow-[0_0_5px_rgba(239,68,68,0.3)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- No Documents -->
            <div v-else class="relative text-center py-16">
              <!-- Floating decorative elements -->
              <div class="absolute top-8 left-12 w-2 h-2 bg-green-400/40 rounded-full blur-sm animate-pulse delay-500"></div>
              <div class="absolute bottom-12 right-16 w-1.5 h-1.5 bg-purple-400/30 rounded-full blur-sm animate-pulse delay-1000"></div>

              <div class="backdrop-blur-3xl bg-gradient-to-br from-white/5 via-transparent to-white/5 rounded-3xl p-12 shadow-[0_5px_16px_0_rgba(31,38,135,0.2)] border border-white/10 max-w-2xl mx-auto" style="backdrop-filter: blur(20px) saturate(180%);">
                <svg class="w-20 h-20 text-white/40 mx-auto mb-6 shadow-[0_0_10px_rgba(255,255,255,0.1)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <h3 class="text-2xl font-semibold text-white/90 mb-3">📄 No Documents Yet</h3>
                <p class="text-white/70 mb-8 leading-relaxed">Upload documents and files related to this company to build a comprehensive resource library.</p>
                <button
                  @click="activeTab = 'documents-upload'"
                  class="group backdrop-blur-3xl bg-gradient-to-br from-green-500/20 via-green-400/10 to-transparent hover:from-green-500/30 hover:via-green-400/15 hover:to-green-300/5 text-white font-medium py-4 px-8 rounded-2xl transition-all duration-500 hover:scale-105 shadow-[0_4px_12px_0_rgba(31,38,135,0.15)] hover:shadow-[0_4px_16px_0_rgba(34,197,94,0.2)] border border-white/10"
                  style="backdrop-filter: blur(20px) saturate(180%);"
              >
                <span class="shadow-[0_0_5px_rgba(34,197,94,0.3)] group-hover:shadow-[0_0_8px_rgba(34,197,94,0.4)]">📄 Upload First Document</span>
              </button>
              </div>
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
                  :disabled="uploading || (documentForm.uploadType === 'file' && (!documentForm.files || documentForm.files.length === 0)) || (documentForm.uploadType === 'url' && (!documentForm.urls || documentForm.urls.length === 0))"
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

              <!-- AI Synopsis -->
              <div>
                <label for="upload_ai_synopsis" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Provide AI Synopsis (optional)</label>
                <textarea
                  id="upload_ai_synopsis"
                  :value="documentForm.ai_synopsis"
                  @input="$emit('update:document-form', { ...documentForm, ai_synopsis: $event.target.value })"
                  rows="4"
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors resize-none"
                  placeholder="AI-generated synopsis or key insights from the document..."
                ></textarea>
                <div v-if="documentErrors.ai_synopsis" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.ai_synopsis }}</div>
              </div>

              <!-- File Upload Area -->
              <div>
                <div class="flex items-center justify-between mb-4">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Documents</label>
                  <div class="flex items-center space-x-4">
                    <button
                      type="button"
                      @click="documentForm.uploadType = 'file'"
                      :class="[
                        'px-3 py-1 text-sm rounded-lg transition-colors',
                        documentForm.uploadType === 'file'
                          ? 'bg-green-500 text-white'
                          : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500'
                      ]"
                    >
                      Upload Files
                    </button>
                    <button
                      type="button"
                      @click="documentForm.uploadType = 'url'"
                      :class="[
                        'px-3 py-1 text-sm rounded-lg transition-colors',
                        documentForm.uploadType === 'url'
                          ? 'bg-green-500 text-white'
                          : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500'
                      ]"
                    >
                      Add URL
                    </button>
                  </div>
                </div>

                <!-- File Upload Area -->
                <div v-if="documentForm.uploadType === 'file'" class="border-2 border-dashed border-green-300 dark:border-green-600 rounded-lg p-8 text-center hover:border-green-400 dark:hover:border-green-500 transition-colors">
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

                <!-- URL Input Area -->
                <div v-if="documentForm.uploadType === 'url'" class="space-y-4">
                  <div class="border-2 border-dashed border-green-300 dark:border-green-600 rounded-lg p-6">
                    <div class="text-center mb-4">
                      <svg class="mx-auto h-12 w-12 text-green-400 dark:text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                      </svg>
                      <p class="text-lg font-medium text-gray-900 dark:text-white">Add Document from URL</p>
                      <p class="text-sm text-gray-500 dark:text-gray-400">The document will be downloaded and attached</p>
                    </div>

                    <div class="space-y-3">
                      <div>
                        <input
                          type="url"
                          placeholder="https://example.com/document.pdf"
                          :value="documentForm.documentUrl"
                          @input="$emit('update:document-form', { ...documentForm, documentUrl: $event.target.value })"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                        />
                      </div>
                      <div>
                        <input
                          type="text"
                          placeholder="Document name (optional)"
                          :value="documentForm.documentName"
                          @input="$emit('update:document-form', { ...documentForm, documentName: $event.target.value })"
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 dark:focus:border-green-400 transition-colors"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If not provided, the filename will be extracted from the URL</p>
                      </div>
                      <div class="flex justify-end">
                        <button
                          type="button"
                          @click="addUrlToList"
                          :disabled="!documentForm.documentUrl"
                          class="px-4 py-2 bg-green-500 hover:bg-green-600 disabled:bg-green-300 text-white rounded-lg transition-colors flex items-center space-x-2"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                          </svg>
                          <span>Add URL</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <!-- URL List -->
                  <div v-if="documentForm.urls && documentForm.urls.length > 0" class="space-y-3">
                    <div v-for="(urlItem, index) in documentForm.urls" :key="index" class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                      <div class="flex items-center">
                        <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <div>
                          <span class="text-sm font-medium text-gray-900 dark:text-white">{{ urlItem.name || 'Document from URL' }}</span>
                          <span class="text-xs text-gray-500 dark:text-gray-400 block">{{ urlItem.url }}</span>
                        </div>
                      </div>
                      <button
                        type="button"
                        @click="$emit('remove-url', index)"
                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200 p-1 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/20 transition-colors"
                      >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div v-if="documentErrors.files" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.files }}</div>
                <div v-if="documentErrors.urls" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ documentErrors.urls }}</div>
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

    <!-- Document Viewer Modal -->
    <div
      v-if="showDocumentViewer && selectedDocument"
      class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-[60]"
      @click.self="closeDocumentViewer"
    >
      <div class="bg-gradient-to-br from-white/10 via-white/15 to-white/10 backdrop-blur-xl rounded-2xl border border-white/20 w-full h-full overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.25)] flex flex-col transition-all duration-500" style="backdrop-filter: blur(20px) saturate(180%);">

        <!-- Modal Header -->
        <div class="sticky top-0 bg-black/20 backdrop-blur-xl border-b border-white/30 px-6 py-4 rounded-t-2xl" style="backdrop-filter: blur(20px) saturate(180%);">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <h2 class="text-2xl font-bold text-white truncate">{{ selectedDocument.title }}</h2>
              <p v-if="selectedDocument.type" class="text-sm text-gray-300 mt-1">
                {{ selectedDocument.type === 'document' ? 'Document' : 'Research Item' }}
              </p>
            </div>
            <button
              @click="closeDocumentViewer"
              class="ml-4 p-2 rounded-full bg-white/10 hover:bg-white/20 transition-colors"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-hidden flex flex-col">

          <!-- Description (if available) -->
          <div v-if="selectedDocument.description" class="bg-gradient-to-r from-green-500/10 to-blue-500/10 border-b border-white/10">
            <button
              @click="toggleSection('description')"
              class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-colors"
            >
              <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Description
              </h3>
              <svg
                class="w-5 h-5 text-gray-400 transition-transform"
                :class="{ 'transform rotate-180': collapsedSections.description }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="!collapsedSections.description" class="px-6 pb-4">
              <p class="text-gray-200 leading-relaxed">{{ selectedDocument.description }}</p>
            </div>
          </div>

          <!-- AI Synopsis (if available) -->
          <div v-if="selectedDocument.ai_synopsis" class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border-b border-white/10">
            <button
              @click="toggleSection('aiSynopsis')"
              class="w-full px-6 py-4 flex items-center justify-between text-left hover:bg-white/5 transition-colors"
            >
              <h3 class="text-lg font-semibold text-white flex items-center">
                <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                AI Synopsis
              </h3>
              <svg
                class="w-5 h-5 text-gray-400 transition-transform"
                :class="{ 'transform rotate-180': collapsedSections.aiSynopsis }"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-show="!collapsedSections.aiSynopsis" class="px-6 pb-4">
              <p class="text-gray-200 leading-relaxed">{{ selectedDocument.ai_synopsis }}</p>
            </div>
          </div>

          <!-- File Attachments -->
          <div v-if="selectedDocument.attachments && selectedDocument.attachments.length > 0" class="flex-1 overflow-hidden flex flex-col">

            <!-- File Display Area -->
            <div class="flex-1 overflow-auto p-6">
              <div v-for="(attachment, index) in selectedDocument.attachments" :key="attachment.id" class="relative h-full">

                <!-- File Content Preview -->
                <div class="relative h-full">

                  <!-- PDF Preview -->
                  <div v-if="attachment.mime_type === 'application/pdf'" class="w-full h-full bg-white rounded-lg overflow-hidden">
                    <iframe
                      :src="attachment.url + '#toolbar=1&navpanes=1&scrollbar=1'"
                      class="w-full h-full border-0"
                      frameborder="0"
                    ></iframe>
                  </div>

                  <!-- Image Preview -->
                  <div v-else-if="attachment.mime_type.startsWith('image/')" class="relative flex justify-center">
                    <img
                      :src="attachment.url"
                      :alt="attachment.name"
                      class="max-w-full max-h-96 rounded-lg shadow-lg object-contain"
                    />
                    <a :href="attachment.url" target="_blank" class="absolute top-2 right-2 px-3 py-1 bg-black/70 hover:bg-black/85 text-white rounded-lg text-sm transition-colors backdrop-blur-sm">
                      📥 Download Image
                    </a>
                  </div>

                  <!-- Text File Preview -->
                  <div v-else-if="attachment.mime_type === 'text/plain' || attachment.mime_type === 'text/csv'" class="relative bg-gray-900 rounded-lg p-4 max-h-96 overflow-auto">
                    <iframe
                      :src="attachment.url"
                      class="w-full h-80 bg-white rounded border-0"
                      frameborder="0"
                    ></iframe>
                    <a :href="attachment.url" target="_blank" class="absolute top-2 right-2 px-3 py-1 bg-black/70 hover:bg-black/85 text-white rounded-lg text-sm transition-colors backdrop-blur-sm">
                      📥 Download Text
                    </a>
                  </div>

                  <!-- Office Documents (Word, Excel, PowerPoint) -->
                  <div v-else-if="attachment.mime_type.includes('word') || attachment.mime_type.includes('excel') || attachment.mime_type.includes('powerpoint') || attachment.mime_type.includes('document') || attachment.mime_type.includes('spreadsheet') || attachment.mime_type.includes('presentation')" class="relative w-full h-96 bg-white rounded-lg overflow-hidden">
                    <iframe
                      :src="`https://view.officeapps.live.com/op/embed.aspx?src=${encodeURIComponent(attachment.url)}`"
                      class="w-full h-full border-0"
                      frameborder="0"
                    ></iframe>
                    <a :href="attachment.url" target="_blank" class="absolute top-2 right-2 px-3 py-1 bg-black/70 hover:bg-black/85 text-white rounded-lg text-sm transition-colors backdrop-blur-sm">
                      📥 Download Document
                    </a>
                  </div>

                  <!-- Generic File Preview -->
                  <div v-else class="text-center py-8">
                    <div class="w-16 h-16 mx-auto bg-gray-500/20 rounded-full flex items-center justify-center mb-4">
                      <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                      </svg>
                    </div>
                    <p class="text-gray-300 mb-2">{{ attachment.name || attachment.file_name }}</p>
                    <p class="text-sm text-gray-400 mb-4">Preview not available for this file type</p>
                    <a :href="attachment.url" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 rounded-lg transition-colors">
                      📥 Download File
                    </a>
                  </div>

                </div>
              </div>
            </div>
          </div>

          <!-- No Attachments -->
          <div v-else class="flex-1 flex items-center justify-center py-12">
            <div class="text-center">
              <div class="w-16 h-16 mx-auto bg-gray-500/20 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <p class="text-gray-300 text-lg">No attachments found</p>
              <p class="text-sm text-gray-400 mt-1">This document has no file attachments</p>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Research Item View Modal -->
    <div v-show="showResearchModal && selectedResearchItem" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 z-[60]" @click.self="closeResearchModal">
      <div class="bg-gradient-to-br from-white/5 via-white/10 to-white/5 backdrop-blur-xl rounded-2xl border border-white/10 max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-[0_8px_32px_0_rgba(31,38,135,0.15)]" style="backdrop-filter: blur(20px) saturate(180%);">

        <!-- Header matching TreeNode style -->
        <div class="flex items-center py-4 px-6 border-b border-white/10 bg-gradient-to-r from-purple-500/10 to-transparent">
          <div class="w-5 h-5 mr-3 text-purple-200">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-purple-200 font-medium text-lg">{{ selectedResearchItem?.title }}</h3>
            <p v-if="selectedResearchItem?.category" class="text-purple-300/60 text-sm mt-1">{{ selectedResearchItem.category }}</p>
          </div>
          <button @click="closeResearchModal" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center transition-all duration-300">
            <svg class="w-4 h-4 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Content -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
          <div class="prose prose-sm max-w-none text-white/90">
            <div v-if="selectedResearchItem?.content" v-html="renderMarkdown(selectedResearchItem.content)"></div>
            <div v-else class="text-white/60 italic">No content available</div>
          </div>

          <!-- Metadata -->
          <div class="mt-6 pt-4 border-t border-white/10 grid grid-cols-2 gap-4 text-sm text-white/60">
            <div>
              <span class="font-medium">Created:</span> {{ selectedResearchItem?.created_at ? new Date(selectedResearchItem.created_at).toLocaleDateString() : 'Unknown' }}
            </div>
            <div>
              <span class="font-medium">Visibility:</span> {{ selectedResearchItem?.visibility || 'Private' }}
            </div>
          </div>

          <!-- Attachments if any -->
          <div v-if="selectedResearchItem?.attachments && selectedResearchItem.attachments.length > 0" class="mt-4 pt-4 border-t border-white/10">
            <h4 class="text-white/80 font-medium mb-2">Attachments</h4>
            <div class="space-y-2">
              <div v-for="attachment in selectedResearchItem.attachments" :key="attachment.id"
                   class="flex items-center space-x-2 text-sm text-white/70 hover:text-white cursor-pointer hover:bg-white/5 rounded-lg p-2 transition-all duration-200"
                   @click="viewAttachment(attachment)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span>{{ attachment.name }}</span>
                <svg class="w-3 h-3 ml-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
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
  isEditingResearchItem: {
    type: Boolean,
    default: false
  },
  editingResearchItemId: {
    type: [String, Number],
    default: null
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
  initialTab: {
    type: String,
    default: 'overview'
  }
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
  'add-url',
  'remove-url',
  'create-insight',
  'start-edit',
  'edit-research',
  'delete-research',
  'delete-document'
])

// Computed property for currency v-model
const currencyModel = computed({
  get() {
    return props.editForm.reports_financial_data_in ?? ''
  },
  set(value) {
    emit('update:edit-form', { ...props.editForm, reports_financial_data_in: value })
  }
})

// Handle tab click and emit edit event when edit tab is clicked
const handleTabClick = (tabId) => {
  activeTab.value = tabId
  if (tabId === 'edit') {
    emit('start-edit')
  }
}

// Add URL to the list
const addUrlToList = () => {
  if (props.documentForm.documentUrl) {
    emit('add-url', {
      url: props.documentForm.documentUrl,
      name: props.documentForm.documentName || ''
    })
  }
}

// Computed property to get documents (includes both standalone documents and research items with attachments)
const documentsWithAttachments = computed(() => {
  if (!props.company) {
    return []
  }

  const allDocuments = []

  // Add standalone documents
  if (props.company.documents) {
    allDocuments.push(...props.company.documents.map(doc => ({
      ...doc,
      type: 'document'
    })))
  }

  // Add research items with attachments
  if (props.company.researchItems) {
    const researchWithFiles = props.company.researchItems.filter(item =>
      item.attachments && item.attachments.length > 0
    )
    allDocuments.push(...researchWithFiles.map(item => ({
      id: `research-${item.id}`,
      title: item.title,
      description: item.content ? item.content.substring(0, 200) + '...' : 'Research item with attachments',
      ai_synopsis: item.ai_synopsis,
      visibility: item.visibility,
      attachments: item.attachments,
      created_at: item.created_at,
      type: 'research_item'
    })))
  }

  return allDocuments
})

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

// Document viewing functions
const viewDocument = (document) => {
  selectedDocument.value = document
  showDocumentViewer.value = true
}

const closeDocumentViewer = () => {
  showDocumentViewer.value = false
  selectedDocument.value = null
}

const viewResearchItem = (item) => {
  selectedResearchItem.value = item
  showResearchModal.value = true
}

const closeResearchModal = () => {
  showResearchModal.value = false
  selectedResearchItem.value = null
}

const viewAttachment = (attachment) => {
  // Create a document object that matches the expected format for the document viewer
  const documentForViewer = {
    id: attachment.id,
    title: attachment.name || attachment.file_name,
    description: `Attachment from research item: ${selectedResearchItem.value?.title}`,
    url: attachment.url,
    mime_type: attachment.mime_type,
    size: attachment.size,
    attachments: [attachment] // Include the attachment itself
  }

  // Close the research modal and open the document viewer
  closeResearchModal()
  viewDocument(documentForViewer)
}

const toggleSection = (section) => {
  collapsedSections.value[section] = !collapsedSections.value[section]
}

const activeTab = ref(props.initialTab)

// Watch for edit mode changes and switch to research-new tab
watch(() => props.isEditingResearchItem, (isEditing) => {
  if (isEditing) {
    activeTab.value = 'research-new'
  }
})

// Document viewer state
const showDocumentViewer = ref(false)
const selectedDocument = ref(null)
const collapsedSections = ref({
  description: false,
  aiSynopsis: false
})

// Insights-related refs
const companyInsights = ref([])
const loadingInsights = ref(false)

// Research item modal
const showResearchModal = ref(false)
const selectedResearchItem = ref(null)

// Watch for insights prop changes
watch(() => props.insights, (newInsights) => {
  companyInsights.value = newInsights || []
}, { immediate: true })

// Watch for initialTab changes to reset activeTab
watch(() => props.initialTab, (newTab) => {
  activeTab.value = newTab
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