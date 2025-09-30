<template>
  <Head :title="company?.name || 'Company Profile'" />

  <DashboardLayout active-tab="companies">
    <template #title>
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-6">
          <h2 class="text-3xl lg:text-4xl font-bold text-white drop-shadow-lg">
            {{ company?.name || 'Loading...' }}
          </h2>
          <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-blue-500/20 text-blue-300 text-lg font-bold rounded-lg border border-blue-400/20">
              {{ company?.ticker || 'N/A' }}
            </span>
            <span class="text-gray-300 text-lg">
              {{ company?.sector || 'Unknown Sector' }}
            </span>
          </div>
        </div>
      </div>
    </template>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-400"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="text-center py-12">
      <div class="bg-red-500/10 border border-red-500/20 rounded-xl p-8 max-w-md mx-auto">
        <h3 class="text-xl font-semibold text-red-400 mb-2">Company Not Found</h3>
        <p class="text-gray-300 mb-6">{{ error }}</p>
        <Link :href="route('dashboard.companies')" class="inline-flex items-center px-4 py-2 bg-blue-500/20 text-white rounded-lg hover:bg-blue-500/30 transition-colors">
          Return to Companies
        </Link>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="company" class="space-y-8">
      <!-- Company Overview Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Key Metrics -->
        <div class="bg-gradient-to-br from-blue-500/10 via-indigo-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Key Metrics
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Market Cap</span>
              <span class="text-white font-semibold">{{ company?.marketCapFormatted || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Sector</span>
              <span class="text-white font-semibold">{{ company?.sector || 'N/A' }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Industry</span>
              <span class="text-white font-semibold">{{ company?.industry || 'N/A' }}</span>
            </div>
          </div>
        </div>

        <!-- Research Stats -->
        <div class="bg-gradient-to-br from-green-500/10 via-emerald-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Research
          </h3>
          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Research Notes</span>
              <span class="text-white font-semibold">{{ company?.research_items?.length || 0 }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-gray-300">Documents</span>
              <span class="text-white font-semibold">{{ company?.documents?.length || 0 }}</span>
            </div>
            <button
              v-if="$page.props.auth.user"
              @click="openNoteCreationModal"
              class="w-full mt-4 bg-green-500/20 hover:bg-green-500/30 text-green-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-green-400/20"
            >
              Add Research Note
            </button>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-purple-500/10 via-violet-500/5 to-transparent backdrop-blur-xl rounded-xl p-6 border border-white/10">
          <h3 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="w-5 h-5 text-purple-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            Quick Actions
          </h3>
          <div class="space-y-3">
            <button
              v-if="$page.props.auth.user"
              @click="openEditCompanyModal"
              class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-blue-400/20"
            >
              Edit Company
            </button>
            <button
              v-if="$page.props.auth.user"
              @click="openDocumentUploadModal"
              class="w-full bg-orange-500/20 hover:bg-orange-500/30 text-orange-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-orange-400/20"
            >
              Upload Document
            </button>
            <button
              v-if="$page.props.auth.user"
              @click="addToWatchlist"
              class="w-full bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 border border-purple-400/20 flex items-center justify-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"></path>
              </svg>
              Add to Watchlist
            </button>
          </div>
        </div>
      </div>

      <!-- Company Description -->
      <div v-if="company?.description" class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
        <h3 class="text-xl font-semibold text-white mb-4">About {{ company.name }}</h3>
        <p class="text-gray-300 leading-relaxed">{{ company.description }}</p>
      </div>

      <!-- Recent Research Notes -->
      <div v-if="company?.research_items?.length > 0" class="bg-gradient-to-br from-white/5 via-transparent to-white/5 backdrop-blur-xl rounded-xl p-6 border border-white/10">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-semibold text-white">Recent Research Notes</h3>
          <Link :href="`/companies/${company.ticker}?tab=research`" class="text-blue-400 hover:text-blue-300 text-sm font-medium">
            View All →
          </Link>
        </div>
        <div class="space-y-4">
          <div
            v-for="item in company.research_items.slice(0, 3)"
            :key="item.id"
            class="p-4 bg-white/5 rounded-lg border border-white/10 hover:bg-white/10 transition-colors cursor-pointer"
            @click="openResearchNoteModal(item)"
          >
            <h4 class="text-white font-medium mb-2">{{ item.title }}</h4>
            <p class="text-gray-400 text-sm mb-2 line-clamp-2">{{ getContentPreview(item.content) }}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
              <span>{{ formatDate(item.created_at) }}</span>
              <span v-if="item.category" class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded">{{ item.category.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <NoteCreationModal
      :show="showNoteCreationModal"
      :selected-company="company"
      :form="noteForm"
      :errors="noteErrors"
      :creating-note="creatingNote"
      :categories="categories"
      :tags="tags"
      :user="$page.props.auth.user"
      :is-editing="isEditingResearchItem"
      @close="handleNoteModalClose"
      @save="handleNoteSave"
      @category-created="handleCategoryCreated"
    />

    <EditCompanyModal
      :show="showEditCompanyModal"
      :company="company"
      :edit-form="editForm"
      :edit-errors="editErrors"
      :saving="savingCompany"
      @close="showEditCompanyModal = false"
      @save-edit="handleCompanySave"
    />

    <DocumentUploadModal
      :show="showDocumentUploadModal"
      :selected-company="company"
      :form="documentForm"
      :errors="documentErrors"
      :uploading="uploadingDocument"
      :format-file-size="formatFileSize"
      @close="showDocumentUploadModal = false"
      @save="handleDocumentSave"
    />

    <ResearchNoteModal
      :show="showResearchNoteModal"
      :research-note="selectedResearchItem"
      @close="showResearchNoteModal = false"
      @edit="handleEditResearchNote"
    />

    <AddToWatchlistModal
      :show="showAddToWatchlistModal"
      :company="company"
      @close="closeAddToWatchlistModal"
      @added="handleAddedToWatchlist"
    />

    <ToastNotification />

  </DashboardLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import EditCompanyModal from '@/Components/Modals/EditCompanyModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import ResearchNoteModal from '@/Components/Modals/ResearchNoteModal.vue'
import AddToWatchlistModal from '@/Components/Modals/AddToWatchlistModal.vue'
import ToastNotification from '@/Components/ToastNotification.vue'

const props = defineProps({
  ticker: String,
  tab: String
})

const page = usePage()

// State
const company = ref(null)
const loading = ref(true)
const error = ref(null)
const showNoteCreationModal = ref(false)
const showEditCompanyModal = ref(false)
const showDocumentUploadModal = ref(false)
const showResearchNoteModal = ref(false)
const showAddToWatchlistModal = ref(false)
const selectedResearchItem = ref(null)
const editingResearchItem = ref(null)
const isEditingResearchItem = computed(() => !!editingResearchItem.value)

// Modal states
const creatingNote = ref(false)
const savingCompany = ref(false)
const uploadingDocument = ref(false)

// Form objects
const noteForm = ref({
  title: '',
  content: '',
  category_id: '',
  source_date: '',
  selectedTags: [],
  visibility: 'private'
})

const noteErrors = ref({})

const editForm = ref({
  name: '',
  ticker: '',
  sector: '',
  industry: '',
  description: '',
  market_cap: ''
})

const editErrors = ref({})

const documentForm = ref({
  title: '',
  description: '',
  category_id: '',
  visibility: 'private',
  files: []
})

const documentErrors = ref({})

// Categories and other data
const categories = ref([])
const tags = ref([])

// Fetch company data and other required data
onMounted(async () => {
  try {
    loading.value = true
    console.log('Fetching company with ticker:', props.ticker)

    // Fetch categories and tags in parallel with company data
    const [companiesResponse, categoriesResponse, tagsResponse] = await Promise.all([
      axios.get('/api/companies', { params: { search: props.ticker, limit: 100 } }),
      axios.get('/api/categories'),
      axios.get('/api/tags')
    ])

    // Load categories and tags
    categories.value = categoriesResponse.data
    tags.value = tagsResponse.data

    console.log('Companies API response:', companiesResponse.data)

    if (companiesResponse.data.data && companiesResponse.data.data.length > 0) {
      // Find exact ticker match (case insensitive)
      const exactMatch = companiesResponse.data.data.find(c =>
        c.ticker && c.ticker.toUpperCase() === props.ticker.toUpperCase()
      )

      if (exactMatch) {
        console.log('Found exact match:', exactMatch)
        // Now get the full company details using the ID
        const detailResponse = await axios.get(`/api/companies/${exactMatch.id}`)
        company.value = detailResponse.data
        console.log('Company details loaded:', company.value)
      } else {
        console.log('Available tickers:', companiesResponse.data.data.map(c => c.ticker))
        error.value = `Company with ticker "${props.ticker}" not found. Available companies: ${companiesResponse.data.data.map(c => c.ticker).join(', ')}`
      }
    } else {
      error.value = `No companies found in database`
    }
  } catch (err) {
    console.error('Error fetching data:', err)
    if (err.response?.status === 401) {
      error.value = 'Please log in to view company details'
    } else {
      error.value = err.response?.data?.message || 'Failed to load data'
    }
  } finally {
    loading.value = false
  }
})

// Modal handlers
const openNoteCreationModal = () => {
  // Reset form
  noteForm.value = {
    title: '',
    content: '',
    category_id: '',
    source_date: '',
    selectedTags: [],
    visibility: 'private'
  }
  noteErrors.value = {}
  showNoteCreationModal.value = true
}

const openEditCompanyModal = () => {
  // Populate form with current company data
  if (company.value) {
    editForm.value = {
      name: company.value.name || '',
      ticker: company.value.ticker || '',
      sector: company.value.sector || '',
      industry: company.value.industry || '',
      description: company.value.description || '',
      market_cap: company.value.market_cap || ''
    }
  }
  editErrors.value = {}
  showEditCompanyModal.value = true
}

const openDocumentUploadModal = () => {
  // Reset form
  documentForm.value = {
    title: '',
    description: '',
    category_id: '',
    visibility: 'private',
    files: []
  }
  documentErrors.value = {}
  showDocumentUploadModal.value = true
}

const openResearchNoteModal = (item) => {
  selectedResearchItem.value = item
  showResearchNoteModal.value = true
}

const handleEditResearchNote = (item) => {
  // Close the view modal
  showResearchNoteModal.value = false

  // Open the edit modal (NoteCreationModal) with the item data
  noteForm.value = {
    title: item.title || '',
    content: item.content || '',
    company_id: company.value?.id || '',
    category_id: item.category?.id || '',
    tag_ids: item.tags?.map(tag => tag.id) || [],
    visibility: item.visibility || 'private',
    ai_synopsis: item.ai_synopsis || '',
    source_date: item.source_date || ''
  }

  // Set the item being edited
  editingResearchItem.value = item

  // Show the creation/edit modal
  showNoteCreationModal.value = true
}

const handleNoteModalClose = () => {
  showNoteCreationModal.value = false
  editingResearchItem.value = null
  // Reset form if needed
  noteForm.value = {
    title: '',
    content: '',
    category_id: '',
    source_date: '',
    visibility: 'private',
    tag_ids: [],
    ai_synopsis: ''
  }
}

// Handle new category creation
const handleCategoryCreated = (newCategory) => {
  categories.value.push(newCategory)
  // Auto-select the new category
  noteForm.value.category_id = newCategory.id
}

// Save handlers for modals
const handleNoteSave = async () => {
  try {
    creatingNote.value = true
    const response = await axios.post('/api/research-items', {
      ...noteForm.value,
      company_id: company.value.id
    })

    if (!company.value.research_items) {
      company.value.research_items = []
    }
    company.value.research_items.unshift(response.data)
    showNoteCreationModal.value = false
    noteErrors.value = {}
  } catch (error) {
    noteErrors.value = error.response?.data?.errors || {}
  } finally {
    creatingNote.value = false
  }
}

const handleCompanySave = async () => {
  try {
    savingCompany.value = true
    const response = await axios.put(`/api/companies/${company.value.id}`, editForm.value)
    company.value = { ...company.value, ...response.data }
    showEditCompanyModal.value = false
    editErrors.value = {}
  } catch (error) {
    editErrors.value = error.response?.data?.errors || {}
  } finally {
    savingCompany.value = false
  }
}

const handleDocumentSave = async () => {
  try {
    uploadingDocument.value = true
    const formData = new FormData()
    formData.append('title', documentForm.value.title)
    formData.append('description', documentForm.value.description)
    formData.append('category_id', documentForm.value.category_id)
    formData.append('visibility', documentForm.value.visibility)
    formData.append('company_id', company.value.id)

    documentForm.value.files.forEach((file, index) => {
      formData.append(`files[${index}]`, file)
    })

    const response = await axios.post('/api/documents', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })

    if (!company.value.documents) {
      company.value.documents = []
    }
    company.value.documents.unshift(response.data)
    showDocumentUploadModal.value = false
    documentErrors.value = {}
  } catch (error) {
    documentErrors.value = error.response?.data?.errors || {}
  } finally {
    uploadingDocument.value = false
  }
}

// Watchlist methods
const addToWatchlist = () => {
  showAddToWatchlistModal.value = true
}

const closeAddToWatchlistModal = () => {
  showAddToWatchlistModal.value = false
}

const handleAddedToWatchlist = (data) => {
  // Optional: Show a toast notification or update UI to indicate success
  console.log('Company added to watchlist(s):', data)
  // You could add a toast notification here if you have a toast system
}

// Utilities
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getContentPreview = (content) => {
  if (!content) return 'No content preview'

  // Strip HTML tags
  const stripped = content.replace(/<[^>]*>/g, '')

  // Decode HTML entities
  const textarea = document.createElement('textarea')
  textarea.innerHTML = stripped
  const decoded = textarea.value

  // Truncate to a reasonable length for preview
  const maxLength = 150
  if (decoded.length <= maxLength) {
    return decoded
  }

  return decoded.substring(0, maxLength).trim() + '...'
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>