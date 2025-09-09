<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-950 transition-colors relative">
    
    <!-- Canvas Shooting Stars Background -->
    <canvas
      ref="starsCanvas"
      class="fixed inset-0 w-full h-full pointer-events-none"
      style="z-index: 1;"
    ></canvas>
    <!-- Header Section -->
    <div class="bg-gray-100 dark:bg-gray-950 shadow-sm">
      <div class="w-[80%] mx-auto px-6 py-8">
        <!-- Navigation Bar -->
        <div class="flex items-center justify-between mb-8">
          <div class="flex items-center gap-4">
            <!-- Rainmaker Logo Image -->
            <img 
              src="/images/rainmaker-logo.png" 
              alt="Rainmaker Logo" 
              class="drop-shadow-sm"
              style="height: 120px; width: auto; opacity: 1 !important; filter: brightness(1.2) contrast(1.2) saturate(1.2);"
            />
          </div>
          <div class="flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button 
              @click="toggleDarkMode"
              class="p-2 rounded-lg bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
              :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
            >
              <svg v-if="isDarkMode" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
              </svg>
              <svg v-else class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
              </svg>
            </button>
            <span v-if="$page.props.auth.user" class="text-sm text-gray-600 dark:text-gray-300">
              Welcome, {{ $page.props.auth.user.name }}
            </span>
            <div v-if="$page.props.auth.user" class="relative">
              <Dropdown align="right" width="48">
                <template #trigger>
                  <button class="flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                    {{ $page.props.auth.user.name }}
                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </template>
                <template #content>
                  <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                  <DropdownLink 
                    v-if="canAccessAdmin" 
                    :href="route('admin.dashboard')"
                    class="border-t border-gray-100 dark:border-gray-600"
                  >
                    🔧 Admin Panel
                  </DropdownLink>
                  <DropdownLink :href="route('logout')" method="post" as="button">Log Out</DropdownLink>
                </template>
              </Dropdown>
            </div>
            <div v-else class="flex items-center gap-3">
              <Link :href="route('login')" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">
                Login
              </Link>
              <Link :href="route('register')" class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Register
              </Link>
            </div>
          </div>
        </div>
        
        <!-- Title and Action Button -->
        <div class="flex items-center justify-between mb-8">
          <div>
            <h2 class="text-4xl font-semibold text-gray-900 dark:text-white mb-2">Investment Research</h2>
            <p class="text-xl text-gray-600 dark:text-gray-300">Search and discover companies in your portfolio</p>
          </div>
          <button 
            v-if="$page.props.auth.user"
            @click="openCreateModal"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-sm transition-all duration-200 transform hover:scale-105"
          >
            + Add Company
          </button>
          <Link 
            v-else
            :href="route('login')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow-sm hover:shadow-sm transition-all duration-200 transform hover:scale-105"
          >
            Login to Add Companies
          </Link>
        </div>
        
        <!-- Search Input -->
        <div class="w-full max-w-2xl">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Search companies, tickers, or sectors..."
            class="w-full text-xl py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 shadow-sm focus:outline-none focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-200"
          />
        </div>
      </div>
    </div>

    <!-- Glass Container for Lower Content -->
    <div class="w-[80%] mx-auto px-6 py-8">
      <div class="backdrop-blur-xl bg-white/10 dark:bg-white/5 rounded-2xl shadow-2xl p-6 pl-8">
    
    <!-- View Controls -->
    <div>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          
          <!-- Bulk Actions (only in list mode) -->
          <div v-if="viewMode === 'list' && selectedCompanies.length > 0" class="flex items-center space-x-3">
            <span class="text-sm text-gray-600 dark:text-gray-300">
              {{ selectedCompanies.length }} selected
            </span>
            <button 
              @click="bulkDeleteCompanies"
              :disabled="bulkDeleting"
              class="bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-medium py-2 px-4 rounded-lg transition-all duration-200 flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              <span>{{ bulkDeleting ? 'Deleting...' : 'Delete Selected' }}</span>
            </button>
          </div>
        </div>
        
        <!-- View Toggle -->
        <div class="flex items-center rounded-md p-1">
          <button 
            @click="viewMode = 'cards'"
            :class="[
              'flex items-center space-x-2 px-3 py-2 transition-all duration-200',
              viewMode === 'cards' 
                ? 'text-blue-600 dark:text-blue-400' 
                : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span>Cards</span>
          </button>
          <button 
            @click="viewMode = 'list'"
            :class="[
              'flex items-center space-x-2 px-3 py-2 transition-all duration-200',
              viewMode === 'list' 
                ? 'text-blue-600 dark:text-blue-400' 
                : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white'
            ]"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
            </svg>
            <span>List</span>
          </button>
        </div>
      </div>
    </div> <!-- End View Controls -->

    <!-- Results -->
    <div>
      <div v-if="loading" class="text-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 dark:border-blue-400 mx-auto"></div>
        <p class="text-gray-600 dark:text-gray-300 mt-4">Loading companies...</p>
      </div>
      
      <!-- Cards View -->
      <div v-else-if="filteredCompanies.length > 0 && viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 3xl:grid-cols-5 gap-8">
        <div 
          v-for="company in filteredCompanies" 
          :key="company.id"
          class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-600 transition-all duration-200"
        >
          <!-- Company Header -->
          <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-blue-500 dark:bg-blue-600 rounded-md flex items-center justify-center text-white font-semibold text-lg">
              {{ company.ticker.charAt(0) }}
            </div>
            <div class="ml-4 flex-1">
              <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-1">{{ company.name }}</h3>
              <p class="text-lg text-blue-600 dark:text-blue-400 font-medium">{{ company.ticker }}</p>
            </div>
          </div>
          
          <!-- Company Details -->
          <div class="space-y-3 mb-6">
            <div v-if="company.sector" class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
              <span class="text-gray-500 dark:text-gray-400 font-medium">Sector</span>
              <span class="text-gray-900 dark:text-white font-medium">{{ company.sector }}</span>
            </div>
            <div v-if="company.marketCap" class="flex justify-between items-center py-2">
              <span class="text-gray-500 dark:text-gray-400 font-medium">Market Cap</span>
              <span class="text-gray-900 dark:text-white font-medium text-lg">{{ formatMarketCap(company.marketCap) }}</span>
            </div>
          </div>
          
          <!-- Action Buttons -->
          <div class="flex gap-3">
            <button 
              @click.stop="() => { console.log('CARDS VIEW: Clicking view details for company:', company); viewCompanyDetails(company); }"
              class="flex-1 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-200"
            >
              View Details
            </button>
            <button 
              v-if="$page.props.auth.user"
              @click="editCompany(company)"
              class="bg-gray-500 hover:bg-gray-600 dark:bg-gray-600 dark:hover:bg-gray-700 text-white font-medium py-2 px-3 rounded-md transition-colors duration-200"
              title="Edit Company"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- List View -->
      <div v-else-if="filteredCompanies.length > 0 && viewMode === 'list'" class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
          <div class="flex items-center">
            <div class="flex items-center mr-4">
              <input 
                type="checkbox" 
                :checked="isSelectAll" 
                @change="toggleSelectAll"
                class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2"
              />
              <label class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Select All</label>
            </div>
          </div>
        </div>
        
        <!-- Table Body -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          <div 
            v-for="company in filteredCompanies" 
            :key="company.id"
            class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
          >
            <div class="flex items-center">
              <!-- Checkbox -->
              <div class="flex items-center mr-4">
                <input 
                  type="checkbox" 
                  :checked="selectedCompanies.includes(company.id)" 
                  @change="toggleCompanySelection(company.id)"
                  class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 dark:focus:ring-blue-600 focus:ring-2"
                />
              </div>
              
              <!-- Company Logo -->
              <div class="w-10 h-10 bg-blue-500 dark:bg-blue-600 rounded-md flex items-center justify-center text-white font-semibold text-sm mr-3">
                {{ company.ticker.charAt(0) }}
              </div>
              
              <!-- Company Info -->
              <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-4 items-center">
                <div class="lg:col-span-2">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ company.name }}</h3>
                  <p class="text-blue-600 dark:text-blue-400 font-medium">{{ company.ticker }}</p>
                </div>
                
                <div class="text-center lg:text-left">
                  <p class="text-sm text-gray-500 dark:text-gray-400">Sector</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ company.sector || 'N/A' }}</p>
                </div>
                
                <div class="text-center lg:text-left">
                  <p class="text-sm text-gray-500 dark:text-gray-400">Market Cap</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ formatMarketCap(company.marketCap) }}</p>
                </div>
                
                <div class="text-center lg:text-right">
                  <p class="text-sm text-gray-500 dark:text-gray-400">Research Items</p>
                  <p class="font-medium text-gray-900 dark:text-white">{{ company.researchItemsCount || 0 }}</p>
                </div>
              </div>
              
              <!-- Actions -->
              <div class="ml-6 flex items-center space-x-2">
                <!-- View Button -->
                <button 
                  @click.stop="viewCompanyDetails(company)"
                  class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-700 hover:bg-blue-200 dark:hover:bg-blue-600 flex items-center justify-center transition-colors"
                  title="View Company Details"
                >
                  <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                
                <!-- Edit Button -->
                <button 
                  v-if="$page.props.auth.user"
                  @click.stop="editCompany(company)"
                  class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition-colors"
                  title="Edit Company"
                >
                  <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <!-- Delete Button -->
                <button 
                  v-if="$page.props.auth.user"
                  @click.stop="deleteCompany(company)"
                  :disabled="deleting"
                  class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-700 hover:bg-red-200 dark:hover:bg-red-600 disabled:bg-red-300 dark:disabled:bg-red-800 flex items-center justify-center transition-colors"
                  title="Delete Company"
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
      
      <!-- Empty State -->
      <div v-else class="text-center py-20">
        <div class="max-w-md mx-auto">
          <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
          <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-3">No companies found</h3>
          <p class="text-lg text-gray-600 dark:text-gray-300">Try adjusting your search terms or add a new company</p>
        </div>
      </div>
    </div>
    
      </div> <!-- End Glass Container -->
    </div> <!-- End Lower Content Container -->

    <!-- Create Company Modal -->
    <CreateCompanyModal
      :show="showCreateModal"
      :form="companyForm"
      :errors="errors"
      :creating="creating"
      :marketCapInput="marketCapInput"
      :marketCapValidation="marketCapValidation"
      :formatMarketCap="formatMarketCap"
      @close="closeCreateModal"
      @save="createCompany"
      @market-cap-input="handleMarketCapInput"
      @update:form="companyForm = $event"
    />

    <!-- Company Details Modal -->
    <CompanyDetailsModal 
      :show="showDetailsModal" 
      :company="selectedCompany"
      :editForm="companyForm"
      :editErrors="errors"
      :saving="editingCompany"
      :editMarketCapInput="editMarketCapInput"
      :editMarketCapValidation="editMarketCapValidation"
      :formatMarketCap="formatMarketCap"
      :noteForm="noteForm"
      :noteErrors="errors"
      :creatingNote="creatingNote"
      :categories="categories"
      :documentForm="uploadForm"
      :documentErrors="errors"
      :uploading="uploading"
      :formatFileSize="formatFileSize"
      @close="closeDetailsModal"
      @save-edit="saveCompanyEdits"
      @update:edit-form="companyForm = $event"
      @edit-market-cap-input="handleEditMarketCapInput"
      @save-note="createNote"
      @update:note-form="noteForm = $event"
      @note-file-upload="handleNoteFileUpload"
      @remove-note-file="removeNoteFile"
      @save-document="uploadDocuments"
      @update:document-form="uploadForm = $event"
      @file-upload="handleDocumentUpload"
      @remove-file="removeUploadFile"
    />

    <!-- Note Creation Modal -->
    <NoteCreationModal
      :show="showNoteModal"
      :selectedCompany="selectedCompany"
      :form="noteForm"
      :errors="errors"
      :creatingNote="creatingNote"
      :categories="categories"
      @close="showNoteModal = false"
      @save="createNote"
    />

    <!-- Document Upload Modal -->
    <DocumentUploadModal
      :show="showUploadModal"
      :selectedCompany="selectedCompany"
      :form="uploadForm"
      :errors="errors"
      :uploading="uploading"
      :categories="categories"
      :formatFileSize="formatFileSize"
      @close="showUploadModal = false"
      @save="uploadDocuments"
      @file-upload="handleDocumentUpload"
      @remove-file="removeUploadFile"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import CompanyDetailsModal from '@/Components/Modals/CompanyDetailsModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import { useDarkMode } from '@/composables/useDarkMode'
import axios from 'axios'

const companies = ref([])
const searchQuery = ref('')
const showCreateModal = ref(false)
const showDetailsModal = ref(false)
const debugModalVisible = ref(false)
const showNoteModal = ref(false)
const showUploadModal = ref(false)
const selectedCompany = ref(null)
const loading = ref(false)
const creating = ref(false)
const deleting = ref(false)
const creatingNote = ref(false)
const uploading = ref(false)
const errors = ref({})
const { isDarkMode, toggleDarkMode, initializeDarkMode } = useDarkMode()
const categories = ref([])
const tags = ref([])
const viewMode = ref('cards') // 'cards' or 'list'
const selectedCompanies = ref([])
const isSelectAll = ref(false)
const bulkDeleting = ref(false)
const isEditingCompany = ref(false)
const editingCompany = ref(false)
const starsCanvas = ref(null)

const companyForm = ref({
  name: '',
  ticker_symbol: '',
  sector: '',
  industry: '',
  market_cap: '',
  description: ''
})

const noteForm = ref({
  title: '',
  content: '',
  company_id: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const uploadForm = ref({
  title: '',
  description: '',
  company_id: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const marketCapInput = ref('')
const marketCapValidation = ref({ state: '', timer: null })

const filteredCompanies = computed(() => {
  if (!searchQuery.value.trim()) {
    return companies.value
  }
  
  const query = searchQuery.value.toLowerCase().trim()
  return companies.value.filter(company => 
    company.name.toLowerCase().includes(query) ||
    company.ticker.toLowerCase().includes(query) ||
    (company.sector && company.sector.toLowerCase().includes(query)) ||
    (company.industry && company.industry.toLowerCase().includes(query))
  )
})

const canAccessAdmin = computed(() => {
  const user = usePage().props.auth?.user
  return user && (user.roles?.some(role => role.name === 'admin') || user.permissions?.some(permission => ['manage users', 'manage roles', 'manage permissions'].includes(permission.name)))
})

const fetchCompanies = async (search = '') => {
  try {
    loading.value = true
    const params = search ? { search } : {}
    const response = await axios.get('/api/companies', { params })
    companies.value = response.data
  } catch (error) {
    console.error('Error fetching companies:', error)
  } finally {
    loading.value = false
  }
}

// Debounced search
let searchTimeout = null
watch(searchQuery, (newValue) => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchCompanies(newValue)
  }, 300)
})



const formatMarketCap = (value) => {
  if (!value) return 'N/A'
  
  if (value >= 1000000000000) {
    return `$${(value / 1000000000000).toFixed(1)}T`
  } else if (value >= 1000000000) {
    return `$${(value / 1000000000).toFixed(1)}B`
  } else {
    return `$${(value / 1000000).toFixed(1)}M`
  }
}

const formatMarketCapAsUserTypes = (value) => {
  if (!value) return ''
  
  // Remove all non-numeric characters except decimal points
  const numericValue = parseFloat(value.toString().replace(/[^\d.]/g, ''))
  if (isNaN(numericValue)) return ''
  
  if (numericValue >= 1000000000000) {
    return `${(numericValue / 1000000000000).toFixed(1)} Trillion`
  } else if (numericValue >= 1000000000) {
    return `${(numericValue / 1000000000).toFixed(1)} Billion`
  } else if (numericValue >= 1000000) {
    return `${(numericValue / 1000000).toFixed(1)} Million`
  } else if (numericValue >= 1000) {
    // Format with commas for thousands
    return numericValue.toLocaleString()
  } else {
    return numericValue.toString()
  }
}

const parseMarketCapForStorage = (displayValue) => {
  if (!displayValue) return ''
  
  // Extract the numeric value and multiplier
  const cleanValue = displayValue.toString().replace(/[,$]/g, '')
  
  if (cleanValue.includes('Trillion')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000000000
  } else if (cleanValue.includes('Billion')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000000
  } else if (cleanValue.includes('Million')) {
    const num = parseFloat(cleanValue.replace(/[^\d.]/g, ''))
    return num * 1000000
  } else {
    // Remove commas and parse as regular number
    return parseFloat(cleanValue.replace(/,/g, '')) || ''
  }
}

const parseMarketCap = (input) => {
  if (!input) return 0
  
  const value = input.toString().trim().toUpperCase()
  
  // Handle comma-separated numbers and shorthand notation
  const match = value.match(/^([\d,]+\.?\d*)([KMBT])?$/)
  
  if (!match) return 0
  
  // Remove commas and parse number
  const number = parseFloat(match[1].replace(/,/g, ''))
  if (isNaN(number)) return 0
  
  const multiplier = match[2]
  switch (multiplier) {
    case 'K': return number * 1000
    case 'M': return number * 1000000
    case 'B': return number * 1000000000
    case 'T': return number * 1000000000000
    default: return number
  }
}

const formatMarketCapInput = (value) => {
  if (!value) return ''
  
  if (value >= 1000000000000) {
    return `${(value / 1000000000000).toFixed(1)}T`
  } else if (value >= 1000000000) {
    return `${(value / 1000000000).toFixed(1)}B`
  } else if (value >= 1000000) {
    return `${(value / 1000000).toFixed(1)}M`
  } else if (value >= 1000) {
    return `${(value / 1000).toFixed(1)}K`
  } else {
    return value.toString()
  }
}

const handleMarketCapInput = (event) => {
  const input = event.target.value
  marketCapInput.value = input
  
  // Clear existing timer
  if (marketCapValidation.value.timer) {
    clearTimeout(marketCapValidation.value.timer)
  }
  
  if (!input) {
    companyForm.value.market_cap = ''
    marketCapValidation.value.state = ''
    return
  }
  
  // Parse and store numeric value
  const numericValue = parseMarketCap(input)
  companyForm.value.market_cap = numericValue
  
  // Set validation state with debounce
  marketCapValidation.value.state = 'validating'
  marketCapValidation.value.timer = setTimeout(() => {
    if (numericValue > 0) {
      marketCapValidation.value.state = 'valid'
    } else {
      marketCapValidation.value.state = 'invalid'
    }
  }, 500)
}

const editMarketCapInput = ref('')
const editMarketCapValidation = ref({ state: '', timer: null })

const handleEditMarketCapInput = (event) => {
  const input = event.target.value
  editMarketCapInput.value = input
  
  // Clear existing timer
  if (editMarketCapValidation.value.timer) {
    clearTimeout(editMarketCapValidation.value.timer)
  }
  
  if (!input) {
    companyForm.value.market_cap = ''
    editMarketCapValidation.value.state = ''
    return
  }
  
  // Parse and store numeric value
  const numericValue = parseMarketCap(input)
  companyForm.value.market_cap = numericValue
  
  // Set validation state with debounce
  editMarketCapValidation.value.state = 'validating'
  editMarketCapValidation.value.timer = setTimeout(() => {
    if (numericValue > 0) {
      editMarketCapValidation.value.state = 'valid'
    } else {
      editMarketCapValidation.value.state = 'invalid'
    }
  }, 500)
}

const openCreateModal = () => {
  // First close any open modals and clear state
  showDetailsModal.value = false
  selectedCompany.value = null
  isEditingCompany.value = false
  
  // Then open create modal
  showCreateModal.value = true
}

const closeCreateModal = () => {
  companyForm.value = {
    name: '',
    ticker_symbol: '',
    sector: '',
    industry: '',
    market_cap: '',
    description: ''
  }
  marketCapInput.value = ''
  if (marketCapValidation.value.timer) {
    clearTimeout(marketCapValidation.value.timer)
  }
  marketCapValidation.value.state = ''
  errors.value = {}
  showCreateModal.value = false
}

const createCompany = async () => {
  try {
    creating.value = true
    errors.value = {}
    
    // Convert market_cap to number if provided
    const formData = { ...companyForm.value }
    if (formData.market_cap) {
      formData.market_cap = parseFloat(formData.market_cap)
    }
    
    const response = await axios.post('/api/companies', formData)
    companies.value.unshift(response.data)
    
    // Reset form and close modal
    companyForm.value = {
      name: '',
      ticker_symbol: '',
      sector: '',
      industry: '',
      market_cap: '',
      description: ''
    }
    marketCapInput.value = ''
    if (marketCapValidation.value.timer) {
      clearTimeout(marketCapValidation.value.timer)
    }
    marketCapValidation.value.state = ''
    showCreateModal.value = false
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error creating company:', error)
      errors.value = { general: 'An error occurred while creating the company. Please try again.' }
    }
  } finally {
    creating.value = false
  }
}

const viewCompanyDetails = async (company) => {
  console.log('viewCompanyDetails called with:', company)
  try {
    // Close create modal if open
    showCreateModal.value = false
    
    // Fetch detailed company information from API
    console.log('Fetching company details for ID:', company.id)
    const response = await axios.get(`/api/companies/${company.id}`)
    
    // Set both values in sequence
    selectedCompany.value = { ...response.data }
    
    // Initialize edit form with company data for the tabbed interface
    companyForm.value = {
      name: selectedCompany.value.name || '',
      ticker_symbol: selectedCompany.value.ticker || '',
      sector: selectedCompany.value.sector || '',
      industry: selectedCompany.value.industry || '',
      market_cap: selectedCompany.value.marketCap || '',
      description: selectedCompany.value.description || ''
    }
    
    // Initialize edit market cap input
    if (selectedCompany.value.marketCap) {
      editMarketCapInput.value = formatMarketCapInput(selectedCompany.value.marketCap)
      editMarketCapValidation.value.state = 'valid'
    } else {
      editMarketCapInput.value = ''
      editMarketCapValidation.value.state = ''
    }
    
    // Initialize note form with company ID
    noteForm.value.company_id = selectedCompany.value.id
    noteForm.value.title = ''
    noteForm.value.content = ''
    noteForm.value.category_id = ''
    noteForm.value.visibility = 'public'
    noteForm.value.files = []
    
    // Initialize upload form with company ID
    uploadForm.value.company_id = selectedCompany.value.id
    uploadForm.value.title = ''
    uploadForm.value.description = ''
    uploadForm.value.category_id = ''
    uploadForm.value.visibility = 'private'
    uploadForm.value.files = []
    
    showDetailsModal.value = true
    
    console.log('selectedCompany set to:', selectedCompany.value)
    console.log('showDetailsModal set to:', showDetailsModal.value)
    console.log('Both values:', !!selectedCompany.value, showDetailsModal.value)
    
    // Force Vue to detect changes
    await nextTick()
    console.log('After nextTick - values:', !!selectedCompany.value, showDetailsModal.value)
  } catch (error) {
    console.error('Error fetching company details:', error)
    // Fallback to basic company data if API call fails
    selectedCompany.value = { ...company }
    
    // Initialize forms with fallback data
    companyForm.value = {
      name: company.name || '',
      ticker_symbol: company.ticker || '',
      sector: company.sector || '',
      industry: company.industry || '',
      market_cap: company.marketCap || '',
      description: company.description || ''
    }
    
    editMarketCapInput.value = company.marketCap ? formatMarketCapInput(company.marketCap) : ''
    editMarketCapValidation.value.state = company.marketCap ? 'valid' : ''
    
    noteForm.value.company_id = company.id
    uploadForm.value.company_id = company.id
    
    showDetailsModal.value = true
  }
}

const deleteCompany = async (company) => {
  if (!confirm(`Are you sure you want to delete ${company.name}? This action cannot be undone.`)) {
    return
  }
  
  try {
    deleting.value = true
    await axios.delete(`/api/companies/${company.id}`)
    
    // Remove company from the list
    const index = companies.value.findIndex(c => c.id === company.id)
    if (index !== -1) {
      companies.value.splice(index, 1)
    }
    
    // Close modal
    showDetailsModal.value = false
    selectedCompany.value = null
    
  } catch (error) {
    console.error('Error deleting company:', error)
    alert('Failed to delete company. Please try again.')
  } finally {
    deleting.value = false
  }
}

const fetchCategoriesAndTags = async () => {
  try {
    const [categoriesResponse, tagsResponse] = await Promise.all([
      axios.get('/api/categories'),
      axios.get('/api/tags')
    ])
    categories.value = categoriesResponse.data
    tags.value = tagsResponse.data
  } catch (error) {
    console.error('Error fetching categories and tags:', error)
  }
}

const openResearchModal = (company) => {
  selectedCompany.value = company
  researchForm.value.company_id = company.id
  showResearchModal.value = true
}

const openNoteModal = (company) => {
  selectedCompany.value = company
  noteForm.value.company_id = company.id
  noteForm.value.title = ''
  noteForm.value.content = ''
  noteForm.value.category_id = ''
  noteForm.value.visibility = 'private'
  noteForm.value.files = []
  errors.value = {}
  showNoteModal.value = true
}

const openUploadModal = (company) => {
  selectedCompany.value = company
  uploadForm.value.company_id = company.id
  uploadForm.value.title = ''
  uploadForm.value.description = ''
  uploadForm.value.category_id = ''
  uploadForm.value.visibility = 'private'
  uploadForm.value.files = []
  errors.value = {}
  showUploadModal.value = true
}

const createNote = async () => {
  try {
    creatingNote.value = true
    errors.value = {}
    
    // Use FormData if files are attached, otherwise use regular JSON
    let requestData
    let requestConfig = {}
    
    if (noteForm.value.files && noteForm.value.files.length > 0) {
      // Use FormData for file uploads
      const formData = new FormData()
      formData.append('title', noteForm.value.title)
      formData.append('content', noteForm.value.content)
      formData.append('company_id', noteForm.value.company_id)
      formData.append('visibility', noteForm.value.visibility)
      
      if (noteForm.value.category_id) {
        formData.append('category_id', noteForm.value.category_id)
      }
      
      // Append files
      noteForm.value.files.forEach((file, index) => {
        formData.append(`attachments[${index}]`, file)
      })
      
      requestData = formData
      requestConfig = {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    } else {
      // Use regular JSON for notes without files
      requestData = {
        title: noteForm.value.title,
        content: noteForm.value.content,
        company_id: noteForm.value.company_id,
        category_id: noteForm.value.category_id,
        visibility: noteForm.value.visibility
      }
    }
    
    const response = await axios.post('/api/research-items', requestData, requestConfig)
    
    // Reset form and close modal
    noteForm.value = {
      title: '',
      content: '',
      company_id: '',
      category_id: '',
      visibility: 'private',
      files: []
    }
    showNoteModal.value = false
    
    // Refresh company details if modal is open
    if (showDetailsModal.value && selectedCompany.value) {
      viewCompanyDetails(selectedCompany.value)
    }
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error creating note:', error)
      errors.value = { general: 'An error occurred while creating the note. Please try again.' }
    }
  } finally {
    creatingNote.value = false
  }
}

const uploadDocuments = async () => {
  try {
    uploading.value = true
    errors.value = {}
    
    const formData = new FormData()
    formData.append('title', uploadForm.value.title)
    formData.append('content', uploadForm.value.description)
    formData.append('company_id', uploadForm.value.company_id)
    formData.append('visibility', uploadForm.value.visibility)
    
    if (uploadForm.value.category_id) {
      formData.append('category_id', uploadForm.value.category_id)
    }
    
    // Append files
    uploadForm.value.files.forEach((file, index) => {
      formData.append(`attachments[${index}]`, file)
    })
    
    const response = await axios.post('/api/research-items', formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    
    // Reset form and close modal
    uploadForm.value = {
      title: '',
      description: '',
      company_id: '',
      category_id: '',
      visibility: 'private',
      files: []
    }
    showUploadModal.value = false
    
    // Refresh company details if modal is open
    if (showDetailsModal.value && selectedCompany.value) {
      viewCompanyDetails(selectedCompany.value)
    }
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error uploading documents:', error)
      errors.value = { general: 'An error occurred while uploading documents. Please try again.' }
    }
  } finally {
    uploading.value = false
  }
}

const handleDocumentUpload = (event) => {
  const files = Array.from(event.target.files)
  uploadForm.value.files = [...uploadForm.value.files, ...files]
}

const removeUploadFile = (index) => {
  uploadForm.value.files.splice(index, 1)
}

const handleNoteFileUpload = (event) => {
  const files = Array.from(event.target.files)
  noteForm.value.files = [...noteForm.value.files, ...files]
}

const removeNoteFile = (index) => {
  noteForm.value.files.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const toggleViewMode = () => {
  viewMode.value = viewMode.value === 'cards' ? 'list' : 'cards'
  selectedCompanies.value = []
  isSelectAll.value = false
}

const toggleSelectAll = () => {
  if (isSelectAll.value) {
    selectedCompanies.value = []
    isSelectAll.value = false
  } else {
    selectedCompanies.value = filteredCompanies.value.map(c => c.id)
    isSelectAll.value = true
  }
}

const toggleCompanySelection = (companyId) => {
  const index = selectedCompanies.value.indexOf(companyId)
  if (index > -1) {
    selectedCompanies.value.splice(index, 1)
  } else {
    selectedCompanies.value.push(companyId)
  }
  
  // Update select all checkbox state
  isSelectAll.value = selectedCompanies.value.length === filteredCompanies.value.length
}

const bulkDeleteCompanies = async () => {
  if (selectedCompanies.value.length === 0) return
  
  if (!confirm(`Are you sure you want to delete ${selectedCompanies.value.length} selected companies? This action cannot be undone.`)) {
    return
  }
  
  try {
    bulkDeleting.value = true
    
    // Delete companies one by one
    const deletePromises = selectedCompanies.value.map(companyId => 
      axios.delete(`/api/companies/${companyId}`)
    )
    
    await Promise.all(deletePromises)
    
    // Remove deleted companies from local state
    companies.value = companies.value.filter(company => 
      !selectedCompanies.value.includes(company.id)
    )
    
    selectedCompanies.value = []
    isSelectAll.value = false
    
  } catch (error) {
    console.error('Error deleting companies:', error)
    alert('An error occurred while deleting companies. Please try again.')
  } finally {
    bulkDeleting.value = false
  }
}

// Company Edit Functions
const editCompany = (company) => {
  // Open details modal and start editing
  viewCompanyDetails(company)
  nextTick(() => {
    startEditingCompany()
  })
}

const startEditingCompany = () => {
  if (!selectedCompany.value) return
  
  // Populate form with current company data
  companyForm.value = {
    name: selectedCompany.value.name || '',
    ticker_symbol: selectedCompany.value.ticker || '',
    sector: selectedCompany.value.sector || '',
    industry: selectedCompany.value.industry || '',
    market_cap: selectedCompany.value.marketCap || '',
    description: selectedCompany.value.description || ''
  }
  
  // Populate edit market cap input if there's a value
  if (selectedCompany.value.marketCap) {
    editMarketCapInput.value = formatMarketCapInput(selectedCompany.value.marketCap)
    editMarketCapValidation.value.state = 'valid'
  } else {
    editMarketCapInput.value = ''
    editMarketCapValidation.value.state = ''
  }
  
  isEditingCompany.value = true
}

const saveCompanyEdits = async () => {
  if (!selectedCompany.value) return
  
  try {
    editingCompany.value = true
    errors.value = {}
    
    const response = await axios.put(`/api/companies/${selectedCompany.value.id}`, {
      name: companyForm.value.name,
      ticker_symbol: companyForm.value.ticker_symbol,
      sector: companyForm.value.sector,
      industry: companyForm.value.industry,
      market_cap: parseMarketCap(companyForm.value.market_cap),
      description: companyForm.value.description
    })
    
    // Update the company in the list
    const index = companies.value.findIndex(c => c.id === selectedCompany.value.id)
    if (index !== -1) {
      companies.value[index] = { ...companies.value[index], ...response.data }
    }
    
    // Update selectedCompany
    selectedCompany.value = { ...selectedCompany.value, ...response.data }
    
    // Reset form and exit edit mode
    companyForm.value = {
      name: '',
      ticker_symbol: '',
      sector: '',
      industry: '',
      market_cap: '',
      description: ''
    }
    isEditingCompany.value = false
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else {
      console.error('Error updating company:', error)
      errors.value = { general: 'An error occurred while updating the company. Please try again.' }
    }
  } finally {
    editingCompany.value = false
  }
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  isEditingCompany.value = false
  selectedCompany.value = null
  errors.value = {}
}

onMounted(() => {
  fetchCompanies()
  fetchCategoriesAndTags()
  
  // Initialize dark mode using the composable
  initializeDarkMode()
  
  // Initialize shooting stars animation  
  initStarsAnimation()
})

// Canvas stars animation

const initStarsAnimation = () => {
  setTimeout(() => {
    const canvas = starsCanvas.value
    if (!canvas) return
    
    const ctx = canvas.getContext('2d')
    if (!ctx) return
    
    // Set canvas size
    canvas.width = window.innerWidth
    canvas.height = window.innerHeight
    
    // Shooting stars array
    const shootingStars = []
    const staticStars = []
    const satellites = []
    
    // Create static twinkling stars with depth
    for (let i = 0; i < 50; i++) {
      const size = Math.random() * 2 + 0.5
      const y = Math.random() * canvas.height
      
      // Calculate distance factor (smaller stars higher up appear more distant)
      const distanceFactor = (size / 2.5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.05 + (distanceFactor * 0.35) // 0.05 to 0.4 range
      
      staticStars.push({
        x: Math.random() * canvas.width,
        y: y,
        size: size,
        baseOpacity: baseOpacity,
        opacity: baseOpacity,
        twinkleSpeed: Math.random() * 0.02 + 0.01,
        twinkleDirection: Math.random() < 0.5 ? 1 : -1,
        distanceFactor: distanceFactor
      })
    }
    
    // Create shooting star with depth
    const createShootingStar = () => {
      const size = Math.random() * 3 + 2
      const y = Math.random() * canvas.height * 0.6
      
      // Calculate distance factor for shooting stars
      const distanceFactor = (size / 5) * ((canvas.height - y) / canvas.height)
      const baseOpacity = 0.1 + (distanceFactor * 0.3) // 0.1 to 0.4 range
      
      return {
        x: canvas.width + 100,
        y: y,
        size: size,
        speed: Math.random() * 5 + 3,
        opacity: baseOpacity,
        baseOpacity: baseOpacity,
        tail: [],
        tailLength: Math.floor(15 + (distanceFactor * 10)), // 15-25 tail length based on distance
        distanceFactor: distanceFactor
      }
    }
    
    // Add initial shooting stars
    for (let i = 0; i < 3; i++) {
      shootingStars.push(createShootingStar())
    }
    
    // Create satellite function
    const createSatellite = () => {
      const size = Math.random() * 1.5 + 1
      const speed = Math.random() * 1.5 + 0.8
      
      // Start from left side, travel to random point on right side
      const startX = -50
      const startY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1 // Avoid extreme edges
      const endX = canvas.width + 50
      const endY = Math.random() * canvas.height * 0.8 + canvas.height * 0.1
      
      // Calculate direction vector
      const dx = endX - startX
      const dy = endY - startY
      const distance = Math.sqrt(dx * dx + dy * dy)
      const vx = (dx / distance) * speed
      const vy = (dy / distance) * speed
      
      return {
        x: startX,
        y: startY,
        vx: vx,
        vy: vy,
        size: size,
        opacity: 0.3 + Math.random() * 0.3, // 0.3 to 0.6 opacity
        blinkTimer: 0,
        blinkInterval: Math.random() * 90 + 60, // More frequent blinking
        isBlinking: false
      }
    }
    
    // Satellite spawn timer
    let satelliteSpawnTimer = 0
    const satelliteSpawnInterval = Math.random() * 600 + 300 // Every 5-15 seconds (at 60fps)
    
    // Create Milky Way background gradient
    const createMilkyWayBackground = () => {
      // Create diagonal Milky Way band
      const gradient = ctx.createLinearGradient(0, canvas.height * 0.3, canvas.width, canvas.height * 0.7)
      gradient.addColorStop(0, 'rgba(40, 30, 60, 0.05)')
      gradient.addColorStop(0.2, 'rgba(80, 60, 120, 0.15)')
      gradient.addColorStop(0.4, 'rgba(120, 90, 160, 0.25)')
      gradient.addColorStop(0.6, 'rgba(100, 80, 140, 0.2)')
      gradient.addColorStop(0.8, 'rgba(60, 50, 100, 0.12)')
      gradient.addColorStop(1, 'rgba(30, 25, 50, 0.05)')
      
      ctx.fillStyle = gradient
      ctx.fillRect(0, 0, canvas.width, canvas.height)
      
      
      
      // Reset alpha for other elements
      ctx.globalAlpha = 1
    }
    
    // Animation loop
    const animate = () => {
      // Clear canvas
      ctx.clearRect(0, 0, canvas.width, canvas.height)
      
      // Draw Milky Way background
      createMilkyWayBackground()
      
      // Draw static stars with depth-based opacity
      staticStars.forEach(star => {
        // Use distance factor for opacity calculation
        const finalOpacity = star.opacity * star.distanceFactor
        ctx.globalAlpha = finalOpacity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()
        
        // Twinkle effect with distance-aware limits
        const minOpacity = star.baseOpacity * 0.3
        const maxOpacity = star.baseOpacity * 1.2
        
        star.opacity += star.twinkleSpeed * star.twinkleDirection
        if (star.opacity <= minOpacity || star.opacity >= maxOpacity) {
          star.twinkleDirection *= -1
        }
      })
      
      // Draw shooting stars
      shootingStars.forEach((star, index) => {
        // Update position
        star.x -= star.speed
        star.y += star.speed * 0.5
        
        // Add to tail
        star.tail.unshift({ x: star.x, y: star.y })
        if (star.tail.length > star.tailLength) {
          star.tail.pop()
        }
        
        // Draw tail with distance-based opacity
        star.tail.forEach((point, i) => {
          const tailOpacity = (1 - i / star.tailLength) * star.opacity * star.distanceFactor * 0.5
          ctx.globalAlpha = tailOpacity
          ctx.fillStyle = '#ffffff'
          const tailSize = star.size * (1 - i / star.tailLength)
          ctx.beginPath()
          ctx.arc(point.x, point.y, tailSize, 0, Math.PI * 2)
          ctx.fill()
        })
        
        // Draw main star with distance-based opacity
        ctx.globalAlpha = star.opacity * star.distanceFactor
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size, 0, Math.PI * 2)
        ctx.fill()
        
        // Add glow effect with distance-based intensity
        const glowIntensity = star.distanceFactor > 0.7 ? 0.15 : 0.05 // Brighter glow for closer stars
        ctx.globalAlpha = star.opacity * glowIntensity
        ctx.fillStyle = '#ffffff'
        ctx.beginPath()
        ctx.arc(star.x, star.y, star.size * (2 + star.distanceFactor), 0, Math.PI * 2)
        ctx.fill()
        
        // Remove if off screen
        if (star.x < -100) {
          shootingStars.splice(index, 1)
          shootingStars.push(createShootingStar())
        }
      })
      
      // Handle satellite spawning
      satelliteSpawnTimer++
      if (satelliteSpawnTimer >= satelliteSpawnInterval) {
        satellites.push(createSatellite())
        satelliteSpawnTimer = 0
      }
      
      // Draw satellites
      satellites.forEach((satellite, index) => {
        // Update position with linear movement
        satellite.x += satellite.vx
        satellite.y += satellite.vy
        
        // Handle blinking
        satellite.blinkTimer++
        if (satellite.blinkTimer >= satellite.blinkInterval) {
          satellite.isBlinking = !satellite.isBlinking
          satellite.blinkTimer = 0
          if (!satellite.isBlinking) {
            // Set next blink interval
            satellite.blinkInterval = Math.random() * 60 + 120
          } else {
            // Short blink duration
            satellite.blinkInterval = 3
          }
        }
        
        // Draw satellite
        if (!satellite.isBlinking) {
          ctx.globalAlpha = satellite.opacity
          ctx.fillStyle = '#ffffff'
          ctx.beginPath()
          ctx.arc(satellite.x, satellite.y, satellite.size, 0, Math.PI * 2)
          ctx.fill()
          
          // Add subtle red/green navigation lights occasionally
          if (Math.random() < 0.2) { // 20% chance for colored light
            ctx.globalAlpha = satellite.opacity * 0.6
            ctx.fillStyle = Math.random() < 0.5 ? '#ff4444' : '#44ff44'
            ctx.beginPath()
            ctx.arc(satellite.x + satellite.size * 1.5, satellite.y, satellite.size * 0.3, 0, Math.PI * 2)
            ctx.fill()
          }
        }
        
        // Remove if off screen (with buffer)
        if (satellite.x < -100 || satellite.x > canvas.width + 100 || 
            satellite.y < -100 || satellite.y > canvas.height + 100) {
          satellites.splice(index, 1)
        }
      })
      
      ctx.globalAlpha = 1
      requestAnimationFrame(animate)
    }
    
    animate()
  }, 200)
}
</script>