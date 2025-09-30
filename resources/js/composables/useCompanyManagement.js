import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

export function useCompanyManagement() {
  // State
  const selectedCompany = ref(null)
  const companyForm = ref({
    name: '',
    ticker: '',
    sector: '',
    industry: '',
    market_cap: '',
    description: '',
    reports_financial_data_in: ''
  })

  // Modal and editing states
  const showCreateModal = ref(false)
  const showDetailsModal = ref(false)
  const isEditingCompany = ref(false)
  const editingCompany = ref(false)
  const creating = ref(false)

  // Market cap validation states
  const marketCapInput = ref('')
  const editMarketCapInput = ref('')
  const marketCapValidation = ref({
    state: '',
    timer: null
  })
  const editMarketCapValidation = ref({
    state: '',
    timer: null
  })

  // Companies data and pagination
  const companiesInfinite = ref([])
  const companiesLoading = ref(false)
  const companiesSearchQuery = ref('')
  const companiesSelectedSector = ref('')
  const companiesSortBy = ref('name')
  const companiesCurrentPage = ref(1)
  const companiesPerPage = ref(10)
  const companiesShowLoading = ref(false)
  const companiesInitialLoading = ref(true)
  const companiesHasMore = ref(true)

  // Bulk operations
  const selectedCompanies = ref([])
  const showDeleteConfirmModal = ref(false)
  const loadingDeletionImpact = ref(false)
  const bulkDeleting = ref(false)
  const deletionImpact = ref(null)

  // Error handling
  const errors = ref({})

  // Computed properties
  const companiesSectors = computed(() => {
    const sectors = [...new Set(companiesInfinite.value.map(company => company.sector).filter(Boolean))]
    return sectors.sort()
  })

  const companiesFiltered = computed(() => {
    let filtered = [...companiesInfinite.value]

    // Filter by search query
    if (companiesSearchQuery.value) {
      const query = companiesSearchQuery.value.toLowerCase()
      filtered = filtered.filter(company =>
        company.name?.toLowerCase().includes(query) ||
        company.ticker?.toLowerCase().includes(query) ||
        company.sector?.toLowerCase().includes(query) ||
        company.industry?.toLowerCase().includes(query)
      )
    }

    // Filter by sector
    if (companiesSelectedSector.value) {
      filtered = filtered.filter(company =>
        company.sector === companiesSelectedSector.value
      )
    }

    // Sort companies
    filtered.sort((a, b) => {
      switch (companiesSortBy.value) {
        case 'name':
          return (a.name || '').localeCompare(b.name || '')
        case 'symbol':
          return (a.ticker || '').localeCompare(b.ticker || '')
        case 'market_cap':
          return (b.marketCap || 0) - (a.marketCap || 0)
        case 'research_count':
          return (b.research_items_count || 0) - (a.research_items_count || 0)
        case 'created_at':
          return new Date(b.created_at || 0) - new Date(a.created_at || 0)
        default:
          return 0
      }
    })

    return filtered
  })

  const companiesTotalPages = computed(() =>
    Math.ceil(companiesFiltered.value.length / companiesPerPage.value)
  )

  const companiesPaginated = computed(() => {
    const start = (companiesCurrentPage.value - 1) * companiesPerPage.value
    const end = start + companiesPerPage.value
    return companiesFiltered.value.slice(start, end)
  })

  const totalCompanyResearchItems = computed(() =>
    companiesInfinite.value.reduce((total, company) => total + (company.research_items_count || 0), 0)
  )

  const isAllSelected = computed(() =>
    companiesFiltered.value.length > 0 &&
    companiesFiltered.value.every(company => selectedCompanies.value.includes(company.id))
  )

  const isIndeterminate = computed(() =>
    selectedCompanies.value.length > 0 &&
    selectedCompanies.value.length < companiesFiltered.value.length
  )

  // Utility functions
  const formatMarketCap = (value) => {
    if (!value || value === 0) return 'N/A'

    const num = parseFloat(value)
    if (isNaN(num)) return 'N/A'

    if (num >= 1e12) {
      return '$' + (num / 1e12).toFixed(1) + 'T'
    } else if (num >= 1e9) {
      return '$' + (num / 1e9).toFixed(1) + 'B'
    } else if (num >= 1e6) {
      return '$' + (num / 1e6).toFixed(1) + 'M'
    } else if (num >= 1e3) {
      return '$' + (num / 1e3).toFixed(1) + 'K'
    } else {
      return '$' + num.toLocaleString()
    }
  }

  const formatMarketCapInput = (value) => {
    if (!value) return ''
    const num = parseFloat(value)
    if (isNaN(num)) return value
    return num.toLocaleString()
  }

  const parseMarketCap = (value) => {
    if (!value) return null
    const cleanValue = value.toString().replace(/[,$]/g, '')
    const num = parseFloat(cleanValue)
    return isNaN(num) ? null : num
  }

  const formatMarketCapAsUserTypes = (value) => {
    const numericValue = value.replace(/[^0-9.]/g, '')
    const num = parseFloat(numericValue)

    if (isNaN(num)) return ''
    return num.toLocaleString()
  }

  // Company CRUD operations
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
      companiesInfinite.value.unshift(response.data)

      // Reset form and close modal
      resetCompanyForm()
      showCreateModal.value = false

    } catch (error) {
      handleApiError(error)
    } finally {
      creating.value = false
    }
  }

  const saveCompanyEdits = async () => {
    if (!selectedCompany.value) return

    try {
      editingCompany.value = true
      errors.value = {}

      const response = await axios.put(`/api/companies/${selectedCompany.value.id}`, {
        name: companyForm.value.name,
        ticker: companyForm.value.ticker,
        sector: companyForm.value.sector,
        industry: companyForm.value.industry,
        market_cap: parseMarketCap(companyForm.value.market_cap),
        description: companyForm.value.description,
        reports_financial_data_in: companyForm.value.reports_financial_data_in
      })

      // Update the company in the list
      const index = companiesInfinite.value.findIndex(c => c.id === selectedCompany.value.id)
      if (index !== -1) {
        companiesInfinite.value[index] = { ...companiesInfinite.value[index], ...response.data }
      }

      // Update selectedCompany with the saved data
      selectedCompany.value = { ...selectedCompany.value, ...response.data }

      // Update the form with the saved data
      updateCompanyForm(response.data)

      // Show success message
      errors.value = { success: 'Company information updated successfully!' }

      // Update edit market cap input with saved value
      if (response.data.marketCap) {
        editMarketCapInput.value = formatMarketCapInput(response.data.marketCap)
        editMarketCapValidation.value.state = 'valid'
      } else {
        editMarketCapInput.value = ''
        editMarketCapValidation.value.state = ''
      }

      // Exit edit mode
      isEditingCompany.value = false

      // Clear success message after 3 seconds
      setTimeout(() => {
        if (errors.value.success) {
          delete errors.value.success
        }
      }, 3000)

    } catch (error) {
      handleApiError(error)
    } finally {
      editingCompany.value = false
    }
  }

  const viewCompanyDetails = async (company) => {
    try {
      showCreateModal.value = false

      const response = await axios.get(`/api/companies/${company.id}`)
      selectedCompany.value = { ...response.data }

      // Initialize edit form with company data
      updateCompanyForm(selectedCompany.value)

      // Initialize edit market cap input
      const marketCapValue = selectedCompany.value.marketCap || selectedCompany.value.market_cap
      if (marketCapValue) {
        editMarketCapInput.value = formatMarketCapInput(marketCapValue)
        editMarketCapValidation.value.state = 'valid'
      } else {
        editMarketCapInput.value = ''
        editMarketCapValidation.value.state = ''
      }

      showDetailsModal.value = true

    } catch (error) {
      console.error('Error loading company details:', error)
    }
  }

  const navigateToCompany = (company) => {
    const ticker = company.ticker
    if (ticker) {
      router.visit(route('company.profile', { ticker: ticker }))
    } else {
      console.error('Company ticker not available for navigation', company)
    }
  }

  const viewCompanyResearch = (company) => {
    navigateToCompany(company)
  }

  // Modal management
  const openCreateModal = () => {
    resetCompanyForm()
    showCreateModal.value = true
  }

  const closeCreateModal = () => {
    showCreateModal.value = false
    resetCompanyForm()
  }

  const closeDetailsModal = () => {
    showDetailsModal.value = false
    isEditingCompany.value = false
    selectedCompany.value = null
    errors.value = {}
  }

  // Selection management
  const toggleCompanySelection = (companyId) => {
    const index = selectedCompanies.value.indexOf(companyId)
    if (index > -1) {
      selectedCompanies.value.splice(index, 1)
    } else {
      selectedCompanies.value.push(companyId)
    }
  }

  const toggleSelectAll = () => {
    if (isAllSelected.value) {
      selectedCompanies.value = []
    } else {
      selectedCompanies.value = companiesFiltered.value.map(company => company.id)
    }
  }

  const clearSelection = () => {
    selectedCompanies.value = []
  }

  // Bulk operations
  const bulkDeleteCompanies = async () => {
    if (selectedCompanies.value.length === 0) return

    try {
      loadingDeletionImpact.value = true
      const response = await axios.post('/api/companies/bulk-delete-impact', {
        company_ids: selectedCompanies.value
      })
      deletionImpact.value = response.data
      showDeleteConfirmModal.value = true
    } catch (error) {
      console.error('Error loading deletion impact:', error)
    } finally {
      loadingDeletionImpact.value = false
    }
  }

  const confirmBulkDelete = async () => {
    try {
      bulkDeleting.value = true
      await axios.delete('/api/companies/bulk-delete', {
        data: { company_ids: selectedCompanies.value }
      })

      // Remove deleted companies from the list
      companiesInfinite.value = companiesInfinite.value.filter(
        company => !selectedCompanies.value.includes(company.id)
      )

      // Clear selection and close modal
      selectedCompanies.value = []
      cancelBulkDelete()

    } catch (error) {
      console.error('Error deleting companies:', error)
    } finally {
      bulkDeleting.value = false
    }
  }

  const cancelBulkDelete = () => {
    showDeleteConfirmModal.value = false
    deletionImpact.value = null
    loadingDeletionImpact.value = false
  }

  // Market cap input handlers
  const handleMarketCapInput = (value) => {
    marketCapInput.value = formatMarketCapAsUserTypes(value)
    companyForm.value.market_cap = parseMarketCap(marketCapInput.value)

    // Clear existing timer
    if (marketCapValidation.value.timer) {
      clearTimeout(marketCapValidation.value.timer)
    }

    // Set validation state
    if (!value.trim()) {
      marketCapValidation.value.state = ''
    } else {
      marketCapValidation.value.state = 'typing'

      // Validate after delay
      marketCapValidation.value.timer = setTimeout(() => {
        const num = parseMarketCap(marketCapInput.value)
        marketCapValidation.value.state = num && num > 0 ? 'valid' : 'invalid'
      }, 500)
    }
  }

  const handleEditMarketCapInput = (value) => {
    editMarketCapInput.value = formatMarketCapAsUserTypes(value)
    companyForm.value.market_cap = parseMarketCap(editMarketCapInput.value)

    // Clear existing timer
    if (editMarketCapValidation.value.timer) {
      clearTimeout(editMarketCapValidation.value.timer)
    }

    // Set validation state
    if (!value.trim()) {
      editMarketCapValidation.value.state = ''
    } else {
      editMarketCapValidation.value.state = 'typing'

      // Validate after delay
      editMarketCapValidation.value.timer = setTimeout(() => {
        const num = parseMarketCap(editMarketCapInput.value)
        editMarketCapValidation.value.state = num && num > 0 ? 'valid' : 'invalid'
      }, 500)
    }
  }

  // Helper functions
  const resetCompanyForm = () => {
    companyForm.value = {
      name: '',
      ticker: '',
      sector: '',
      industry: '',
      market_cap: '',
      description: '',
      reports_financial_data_in: ''
    }
    marketCapInput.value = ''
    if (marketCapValidation.value.timer) {
      clearTimeout(marketCapValidation.value.timer)
    }
    marketCapValidation.value.state = ''
    errors.value = {}
  }

  const updateCompanyForm = (company) => {
    companyForm.value = {
      name: company.name || '',
      ticker: company.ticker || '',
      sector: company.sector || '',
      industry: company.industry || '',
      market_cap: company.marketCap || company.market_cap || '',
      description: company.description || '',
      reports_financial_data_in: company.reports_financial_data_in || ''
    }
  }

  const handleApiError = (error) => {
    console.error('API Error:', error)

    if (error.response && error.response.status === 422) {
      errors.value = error.response.data.errors || {}
    } else if (error.response && error.response.data) {
      const serverMessage = error.response.data.message || error.response.data.error || 'Unknown server error'
      errors.value = { general: `Server Error (${error.response.status}): ${serverMessage}` }
    } else if (error.message) {
      errors.value = { general: `Network Error: ${error.message}` }
    } else {
      errors.value = { general: 'An unknown error occurred. Please try again.' }
    }
  }

  return {
    // State
    selectedCompany,
    companyForm,
    showCreateModal,
    showDetailsModal,
    isEditingCompany,
    editingCompany,
    creating,
    marketCapInput,
    editMarketCapInput,
    marketCapValidation,
    editMarketCapValidation,
    companiesInfinite,
    companiesLoading,
    companiesSearchQuery,
    companiesSelectedSector,
    companiesSortBy,
    companiesCurrentPage,
    companiesPerPage,
    companiesShowLoading,
    companiesInitialLoading,
    companiesHasMore,
    selectedCompanies,
    showDeleteConfirmModal,
    loadingDeletionImpact,
    bulkDeleting,
    deletionImpact,
    errors,

    // Computed
    companiesSectors,
    companiesFiltered,
    companiesTotalPages,
    companiesPaginated,
    totalCompanyResearchItems,
    isAllSelected,
    isIndeterminate,

    // Methods
    formatMarketCap,
    formatMarketCapInput,
    parseMarketCap,
    createCompany,
    saveCompanyEdits,
    viewCompanyDetails,
    navigateToCompany,
    viewCompanyResearch,
    openCreateModal,
    closeCreateModal,
    closeDetailsModal,
    toggleCompanySelection,
    toggleSelectAll,
    clearSelection,
    bulkDeleteCompanies,
    confirmBulkDelete,
    cancelBulkDelete,
    handleMarketCapInput,
    handleEditMarketCapInput,
    resetCompanyForm,
    updateCompanyForm
  }
}