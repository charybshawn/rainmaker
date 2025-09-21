<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 transition-colors relative">
    <Head title="Investment Research Platform" />

    <!-- Canvas Shooting Stars Background -->
    <StarFieldBackground />

    <!-- Header Section -->
    <div class="relative z-10">
      <div class="w-[95%] sm:w-[90%] lg:w-[80%] mx-auto px-4 sm:px-6 py-3 sm:py-4 lg:py-6">
        <!-- Navigation Bar -->
        <DashboardHeader
          :can-access-admin="canAccessAdmin"
          @show-login="showLoginModal = true"
          @show-register="showRegisterModal = true"
        />

        <!-- Animated Quotes Section -->
        <div class="mb-0">
          <AnimatedQuotes />
        </div>
      </div>
    </div>

    <!-- Glass Container for Lower Content -->
    <div class="w-[95%] sm:w-[90%] lg:w-[80%] mx-auto px-4 sm:px-6 py-3 sm:py-4 lg:py-6 -mt-12">
      <GlassContainer>
        <!-- Navigation Tabs with Search -->
        <div class="flex items-center justify-between mb-4 sm:mb-6 lg:mb-8">
          <NavigationTabs
            :active-tab="activeTab"
            @tab-change="switchTab"
          />

          <!-- Universal Search -->
          <UniversalSearch ref="searchComponent" />
        </div>

        <!-- Header Title -->
        <div class="flex items-start mb-12">
          <h2 class="text-4xl font-bold text-white flex items-center transition-all duration-500 ease-out">
            <svg v-if="!showSearchResults" class="w-10 h-10 mr-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <svg v-else class="w-10 h-10 mr-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            {{ showSearchResults ? 'Search Results' : 'Investment Research Platform' }}
            <div v-if="showSearchResults && searchQuery" class="ml-6 text-sm text-gray-300 font-normal">
              for "{{ searchQuery }}"
            </div>
          </h2>
        </div>

        <!-- Search Results -->
        <div v-if="showSearchResults" class="space-y-8">
          <SearchResults
            :search-results="searchResults"
            @open-company="openCompanyDetails"
            @open-blog-post="openBlogPost"
            @open-research-item="openResearchItem"
          />
        </div>

        <!-- Tab Content -->
        <div v-else>
          <!-- Overview Tab -->
          <OverviewTab
            v-if="activeTab === 'overview'"
            :companies="companies"
            @view-company="viewCompanyDetails"
            @show-create-modal="showCreateModal = true"
            @quick-note="openNoteModal"
            @quick-blog="openQuickBlogModal"
          />

          <!-- Portfolio Tab -->
          <PortfolioTab
            v-else-if="activeTab === 'portfolio'"
            :companies="companies"
            @view-company="viewCompanyDetails"
          />

          <!-- Insights Tab -->
          <InsightsTab
            v-else-if="activeTab === 'insights'"
            :blog-posts="recentBlogPosts"
            @view-post="viewBlogPost"
            @edit-post="editBlogPost"
            @show-blog-modal="showQuickBlogModal = true"
          />

          <!-- Analytics Tab -->
          <AnalyticsTab
            v-else-if="activeTab === 'analytics'"
            :companies="companies"
          />

          <!-- Actions Tab -->
          <ActionsTab
            v-else-if="activeTab === 'actions'"
            @show-create-modal="showCreateModal = true"
            @show-note-modal="showNoteModal = true"
            @show-upload-modal="showUploadModal = true"
            @show-blog-modal="showQuickBlogModal = true"
          />
        </div>
      </GlassContainer>
    </div>

    <!-- Modals -->
    <CompanyDetailsModal
      v-if="showDetailsModal && selectedCompany"
      :company="selectedCompany"
      :company-insights="companyInsights"
      @close="closeDetailsModal"
      @edit-company="editCompany"
      @delete-company="deleteCompany"
    />

    <CreateCompanyModal
      v-if="showCreateModal"
      :creating="creating"
      :errors="errors"
      @close="closeCreateModal"
      @create="createCompany"
    />

    <NoteCreationModal
      v-if="showNoteModal"
      :note-form="noteForm"
      :creating="creatingNote"
      :companies="companies"
      :categories="categories"
      :errors="errors"
      @close="closeNoteModal"
      @create="createNote"
    />

    <DocumentUploadModal
      v-if="showUploadModal"
      :upload-form="uploadForm"
      :uploading="uploading"
      :companies="companies"
      :categories="categories"
      :errors="errors"
      @close="closeUploadModal"
      @upload="uploadDocuments"
    />

    <QuickBlogModal
      v-if="showQuickBlogModal"
      :company="quickBlogCompany"
      :editing-post="editingBlogPost"
      @close="closeQuickBlogModal"
      @save="saveBlogPost"
    />

    <LoginModal
      v-if="showLoginModal"
      @close="showLoginModal = false"
    />

    <RegisterModal
      v-if="showRegisterModal"
      @close="showRegisterModal = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'

// Layout Components
import StarFieldBackground from '@/Components/Background/StarFieldBackground.vue'
import DashboardHeader from '@/Components/Layout/DashboardHeader.vue'
import NavigationTabs from '@/Components/Layout/NavigationTabs.vue'
import UniversalSearch from '@/Components/Layout/UniversalSearch.vue'
import GlassContainer from '@/Components/Layout/GlassContainer.vue'

// Feature Components - Eager Loaded
import OverviewTab from '@/Components/Dashboard/OverviewTab.vue'
import InsightsTab from '@/Components/Dashboard/InsightsTab.vue'

// Placeholder components for other tabs
const PortfolioTab = {
  template: '<div class="text-center py-16"><h3 class="text-xl text-white">Portfolio Tab - Coming Soon</h3></div>'
}

const AnalyticsTab = {
  template: '<div class="text-center py-16"><h3 class="text-xl text-white">Analytics Tab - Coming Soon</h3></div>'
}

const ActionsTab = {
  template: '<div class="text-center py-16"><h3 class="text-xl text-white">Actions Tab - Coming Soon</h3></div>'
}

const SearchResults = {
  template: '<div class="text-center py-16"><h3 class="text-xl text-white">Search Results - Coming Soon</h3></div>'
}

// Modal Components
import AnimatedQuotes from '@/Components/AnimatedQuotes.vue'
import CompanyDetailsModal from '@/Components/Modals/CompanyDetailsModal.vue'
import CreateCompanyModal from '@/Components/Modals/CreateCompanyModal.vue'
import NoteCreationModal from '@/Components/Modals/NoteCreationModal.vue'
import DocumentUploadModal from '@/Components/Modals/DocumentUploadModal.vue'
import QuickBlogModal from '@/Components/Modals/QuickBlogModal.vue'
import LoginModal from '@/Components/Modals/LoginModal.vue'
import RegisterModal from '@/Components/Modals/RegisterModal.vue'

// Props from Laravel
defineProps({
  companies: {
    type: Array,
    default: () => []
  },
  recentBlogPosts: {
    type: Array,
    default: () => []
  },
  categories: {
    type: Array,
    default: () => []
  },
  tags: {
    type: Array,
    default: () => []
  }
})

// State Management
const activeTab = ref('overview')
const searchComponent = ref(null)

// Modal states
const showDetailsModal = ref(false)
const showCreateModal = ref(false)
const showNoteModal = ref(false)
const showUploadModal = ref(false)
const showQuickBlogModal = ref(false)
const showLoginModal = ref(false)
const showRegisterModal = ref(false)

// Selected items
const selectedCompany = ref(null)
const quickBlogCompany = ref(null)
const editingBlogPost = ref(null)

// Loading states
const creating = ref(false)
const creatingNote = ref(false)
const uploading = ref(false)
const errors = ref({})

// Company insights and forms
const companyInsights = ref([])
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

// Search state from UniversalSearch component
const showSearchResults = computed(() => {
  return searchComponent.value?.showSearchResults || false
})

const searchQuery = computed(() => {
  return searchComponent.value?.searchQuery || ''
})

const searchResults = computed(() => {
  return searchComponent.value?.searchResults || { companies: [], blogPosts: [], researchItems: [] }
})

// User permissions
const canAccessAdmin = computed(() => {
  const user = usePage().props.auth.user
  return user?.roles?.some(role => ['admin', 'super-admin'].includes(role.name)) || false
})

// Initialize dark mode
const { initializeDarkMode } = useDarkMode()

// Methods
const switchTab = (tabId) => {
  activeTab.value = tabId
  if (searchComponent.value) {
    searchComponent.value.closeHeaderSearch()
  }
}

// Company methods
const viewCompanyDetails = (company) => {
  selectedCompany.value = company
  showDetailsModal.value = true
  // TODO: Load company insights
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedCompany.value = null
  companyInsights.value = []
}

const editCompany = (company) => {
  // TODO: Implement edit company
  console.log('Edit company:', company)
}

const deleteCompany = (company) => {
  // TODO: Implement delete company
  console.log('Delete company:', company)
}

const createCompany = (companyData) => {
  // TODO: Implement create company
  console.log('Create company:', companyData)
}

const closeCreateModal = () => {
  showCreateModal.value = false
  errors.value = {}
}

// Note methods
const openNoteModal = (company) => {
  noteForm.value.company_id = company?.id || ''
  showNoteModal.value = true
}

const closeNoteModal = () => {
  showNoteModal.value = false
  noteForm.value = {
    title: '',
    content: '',
    company_id: '',
    category_id: '',
    visibility: 'private',
    files: []
  }
  errors.value = {}
}

const createNote = (noteData) => {
  // TODO: Implement create note
  console.log('Create note:', noteData)
}

// Upload methods
const closeUploadModal = () => {
  showUploadModal.value = false
  uploadForm.value = {
    title: '',
    description: '',
    company_id: '',
    category_id: '',
    visibility: 'private',
    files: []
  }
  errors.value = {}
}

const uploadDocuments = (uploadData) => {
  // TODO: Implement upload documents
  console.log('Upload documents:', uploadData)
}

// Blog methods
const openQuickBlogModal = (company) => {
  quickBlogCompany.value = company
  editingBlogPost.value = null
  showQuickBlogModal.value = true
}

const viewBlogPost = (post) => {
  // TODO: Implement view blog post
  console.log('View blog post:', post)
}

const editBlogPost = (post) => {
  editingBlogPost.value = post
  quickBlogCompany.value = null
  showQuickBlogModal.value = true
}

const closeQuickBlogModal = () => {
  showQuickBlogModal.value = false
  quickBlogCompany.value = null
  editingBlogPost.value = null
}

const saveBlogPost = (postData) => {
  // TODO: Implement save blog post
  console.log('Save blog post:', postData)
}

// Search methods
const openCompanyDetails = (company) => {
  viewCompanyDetails(company)
}

const openBlogPost = (post) => {
  viewBlogPost(post)
}

const openResearchItem = (item) => {
  // TODO: Implement open research item
  console.log('Open research item:', item)
}

// Lifecycle
onMounted(() => {
  initializeDarkMode()
})
</script>